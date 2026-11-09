<?php

namespace App\Http\Controllers;

use App\Models\GisMarker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Medical cannabis GIS map: page + JSON API (same response shape as the original dashboard).
 */
class GisController extends Controller
{
    public function page()
    {
        return view('front.cannabis-map');
    }

    public function markers(Request $request)
    {
        $key = 'gis.markers.'.md5(json_encode($request->only(['q', 'type', 'status', 'health_region_id', 'province_id'])));

        $rows = Cache::remember($key, 600, function () use ($request) {
            return GisMarker::query()->filter($request)
                ->whereNotNull('lat')->whereNotNull('lng')
                ->orderBy('id')
                ->get()
                ->map(fn (GisMarker $m) => $this->present($m))
                ->all();
        });

        return response()->json($rows);
    }

    public function healthRegions()
    {
        return response()->json(DB::table('gis_health_regions')->orderBy('id')->get(['id', 'code', 'name_th']));
    }

    public function provinces(Request $request)
    {
        return response()->json(
            DB::table('gis_provinces')
                ->when($request->filled('health_region_id'), fn ($q) => $q->where('health_region_id', (int) $request->query('health_region_id')))
                ->orderBy('name_th')
                ->get(['id', 'code', 'health_region_id', 'name_th'])
        );
    }

    public function summary(Request $request)
    {
        $base = GisMarker::query()->filter($request);

        $byType = (clone $base)->select('marker_type', DB::raw('count(*) as c'))->groupBy('marker_type')->pluck('c', 'marker_type');
        $byStatus = (clone $base)->select('status', DB::raw('count(*) as c'))->groupBy('status')->pluck('c', 'status');

        return response()->json([
            'total' => (int) $byType->sum(),
            'establishments' => (int) ($byType['establishment'] ?? 0),
            'farm_plots' => (int) ($byType['farm_plot'] ?? 0),
            'active' => (int) ($byStatus['active'] ?? 0),
            'closed' => (int) ($byStatus['closed'] ?? 0),
            'suspended' => (int) ($byStatus['suspended'] ?? 0),
            'revoked' => (int) ($byStatus['revoked'] ?? 0),
            'dashboard_views' => DB::table('gis_dashboard_views')->count(),
        ]);
    }

    public function autocomplete(Request $request)
    {
        if (mb_strlen(trim((string) $request->query('q', ''))) < 2) {
            return response()->json([]);
        }

        return response()->json(
            GisMarker::query()->filter($request)
                ->orderBy('name')
                ->limit(20)
                ->get(['id', 'marker_type', 'name'])
        );
    }

    public function trackView(Request $request)
    {
        DB::table('gis_dashboard_views')->insert(['ip' => $request->ip(), 'created_at' => now()]);

        return response()->json(['ok' => true]);
    }

    private function present(GisMarker $m): array
    {
        $row = [
            'id' => $m->id,
            'marker_type' => $m->marker_type,
            'name' => $m->name,
            'category' => $m->category,
            'status' => $m->status,
            'license_no' => $m->license_no,
            'health_region_id' => $m->health_region_id,
            'province_id' => $m->province_id,
            'province_name' => $m->province_name,
            'lat' => $m->lat,
            'lng' => $m->lng,
        ];

        if ($m->marker_type === 'establishment') {
            $row['license_expiry_date'] = $m->license_expiry_date?->format('Y-m-d');
        } else {
            $row += [
                'certified_standards' => $m->certified_standards ?? [],
                'is_indoor' => $m->is_indoor,
                'is_greenhouse' => $m->is_greenhouse,
                'is_outdoor' => $m->is_outdoor,
                'area_indoor' => $m->area_indoor,
                'area_greenhouse' => $m->area_greenhouse,
                'area_outdoor' => $m->area_outdoor,
                'avg_yield_kg_per_cycle' => $m->avg_yield_kg_per_cycle,
                'plants_per_cycle' => $m->plants_per_cycle,
            ];
        }

        return $row;
    }
}
