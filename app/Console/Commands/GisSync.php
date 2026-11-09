<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

/**
 * Import the public Medical Cannabis GIS data into the local database.
 *   php artisan gis:sync
 */
class GisSync extends Command
{
    protected $signature = 'gis:sync {--source= : Base URL of the GIS API (default: services.cannabis_gis.base)}';

    protected $description = 'Download health regions, provinces and map markers from the Medical Cannabis GIS API';

    public function handle(): int
    {
        $base = rtrim($this->option('source') ?: config('services.cannabis_gis.base'), '/');
        $http = Http::timeout(180)->withHeaders(['User-Agent' => config('app.name').' map sync'])->acceptJson();
        $now = now();

        $this->info('Fetching from '.$base);

        $regions = $http->get($base.'/api/geo/health-regions-dev')->throw()->json();
        $provinces = $http->get($base.'/api/geo/provinces-dev')->throw()->json();
        $markers = $http->get($base.'/api/geo/markers-dev')->throw()->json();

        if (! is_array($markers) || ! count($markers)) {
            $this->error('No markers returned, keeping the existing data.');

            return self::FAILURE;
        }

        DB::transaction(function () use ($regions, $provinces, $markers, $now) {
            DB::table('gis_health_regions')->upsert(array_map(fn ($r) => [
                'id' => $r['id'], 'code' => $r['code'] ?? null, 'name_th' => $r['name_th'],
                'created_at' => $now, 'updated_at' => $now,
            ], $regions), ['id'], ['code', 'name_th', 'updated_at']);

            DB::table('gis_provinces')->upsert(array_map(fn ($p) => [
                'id' => $p['id'], 'code' => $p['code'] ?? null, 'health_region_id' => $p['health_region_id'] ?? null,
                'name_th' => $p['name_th'], 'created_at' => $now, 'updated_at' => $now,
            ], $provinces), ['id'], ['code', 'health_region_id', 'name_th', 'updated_at']);

            $rows = array_map(fn ($m) => [
                'source_id' => $m['id'],
                'marker_type' => $m['marker_type'] === 'farm_plot' ? 'farm_plot' : 'establishment',
                'name' => isset($m['name']) ? trim($m['name']) : null,
                'category' => $m['category'] ?? null,
                'status' => $m['status'] ?? null,
                'license_no' => $m['license_no'] ?? null,
                'license_expiry_date' => ! empty($m['license_expiry_date']) ? substr($m['license_expiry_date'], 0, 10) : null,
                'health_region_id' => $m['health_region_id'] ?? null,
                'province_id' => $m['province_id'] ?? null,
                'province_name' => $m['province_name'] ?? null,
                'lat' => $m['lat'] ?? null,
                'lng' => $m['lng'] ?? null,
                'certified_standards' => isset($m['certified_standards']) ? json_encode($m['certified_standards'], JSON_UNESCAPED_UNICODE) : null,
                'is_indoor' => (bool) ($m['is_indoor'] ?? false),
                'is_greenhouse' => (bool) ($m['is_greenhouse'] ?? false),
                'is_outdoor' => (bool) ($m['is_outdoor'] ?? false),
                'area_indoor' => $m['area_indoor'] ?? null,
                'area_greenhouse' => $m['area_greenhouse'] ?? null,
                'area_outdoor' => $m['area_outdoor'] ?? null,
                'avg_yield_kg_per_cycle' => $m['avg_yield_kg_per_cycle'] ?? null,
                'plants_per_cycle' => $m['plants_per_cycle'] ?? null,
                'created_at' => $now,
                'updated_at' => $now,
            ], $markers);

            foreach (array_chunk($rows, 500) as $chunk) {
                DB::table('gis_markers')->upsert($chunk, ['marker_type', 'source_id'], array_keys(array_diff_key($chunk[0], ['source_id' => 1, 'marker_type' => 1, 'created_at' => 1])));
            }

            // remove records that no longer exist at the source
            DB::table('gis_markers')->where('updated_at', '<', $now)->delete();
        });

        Cache::flush(); // cached marker responses

        $this->info(sprintf('Imported %d regions, %d provinces, %d markers.', count($regions), count($provinces), count($markers)));

        return self::SUCCESS;
    }
}
