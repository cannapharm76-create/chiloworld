<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Medical cannabis GIS map data (health regions, provinces, establishments + farm plots).
 * Filled by: php artisan gis:sync
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gis_health_regions', function (Blueprint $table) {
            $table->unsignedInteger('id')->primary();
            $table->string('code', 10)->nullable();
            $table->string('name_th');
            $table->timestamps();
        });

        Schema::create('gis_provinces', function (Blueprint $table) {
            $table->unsignedInteger('id')->primary();
            $table->string('code', 10)->nullable();
            $table->unsignedInteger('health_region_id')->nullable()->index();
            $table->string('name_th');
            $table->timestamps();
        });

        Schema::create('gis_markers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('source_id');
            $table->enum('marker_type', ['establishment', 'farm_plot'])->index();
            $table->string('name')->nullable()->index();
            $table->string('category')->nullable();
            $table->string('status', 20)->nullable()->index();
            $table->string('license_no')->nullable();
            $table->date('license_expiry_date')->nullable();
            $table->unsignedInteger('health_region_id')->nullable()->index();
            $table->unsignedInteger('province_id')->nullable()->index();
            $table->string('province_name')->nullable();
            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('lng', 10, 7)->nullable();
            // farm plot details
            $table->json('certified_standards')->nullable();
            $table->boolean('is_indoor')->default(false);
            $table->boolean('is_greenhouse')->default(false);
            $table->boolean('is_outdoor')->default(false);
            $table->decimal('area_indoor', 12, 2)->nullable();
            $table->decimal('area_greenhouse', 12, 2)->nullable();
            $table->decimal('area_outdoor', 12, 2)->nullable();
            $table->decimal('avg_yield_kg_per_cycle', 12, 2)->nullable();
            $table->unsignedInteger('plants_per_cycle')->nullable();
            $table->timestamps();

            $table->unique(['marker_type', 'source_id']);
        });

        Schema::create('gis_dashboard_views', function (Blueprint $table) {
            $table->id();
            $table->string('ip', 45)->nullable();
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gis_dashboard_views');
        Schema::dropIfExists('gis_markers');
        Schema::dropIfExists('gis_provinces');
        Schema::dropIfExists('gis_health_regions');
    }
};
