<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class GisMarker extends Model
{
    protected $table = 'gis_markers';

    protected $guarded = ['id'];

    protected $casts = [
        'certified_standards' => 'array',
        'is_indoor' => 'boolean',
        'is_greenhouse' => 'boolean',
        'is_outdoor' => 'boolean',
        'lat' => 'float',
        'lng' => 'float',
        'area_indoor' => 'float',
        'area_greenhouse' => 'float',
        'area_outdoor' => 'float',
        'avg_yield_kg_per_cycle' => 'float',
        'license_expiry_date' => 'date:Y-m-d',
    ];

    /** Filters shared by markers / summary / autocomplete: q, type, status, health_region_id, province_id */
    public function scopeFilter(Builder $query, Request $request): Builder
    {
        $q = trim((string) $request->query('q', ''));
        $type = $request->query('type', 'all');
        $status = $request->query('status', 'all');

        return $query
            ->when($q !== '', fn ($w) => $w->where(function ($w) use ($q) {
                $w->where('name', 'like', '%'.$q.'%')->orWhere('license_no', 'like', '%'.$q.'%');
            }))
            ->when(in_array($type, ['establishment', 'farm_plot'], true), fn ($w) => $w->where('marker_type', $type))
            ->when(in_array($status, ['active', 'closed', 'suspended', 'revoked'], true), fn ($w) => $w->where('status', $status))
            ->when($request->filled('health_region_id'), fn ($w) => $w->where('health_region_id', (int) $request->query('health_region_id')))
            ->when($request->filled('province_id'), fn ($w) => $w->where('province_id', (int) $request->query('province_id')));
    }
}
