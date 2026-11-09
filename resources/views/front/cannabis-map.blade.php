@extends('front.layout.app')
@section('title', 'Medical Cannabis GIS Map | CHILO EXOTICS®')
@section('description', 'Map of licensed medical cannabis establishments and farm plots in Thailand, with search, status and region filters.')

@push('styles')
@include('front.layout.header-styles')
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@400;500;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
<link rel="stylesheet" href="{{ asset('public/front/gis/gis.css') }}">
@endpush

@section('content')
<main class="gis-page">

    <div class="gis-head">
        <div>
            <h1><span>🌿</span>Medical Cannabis GIS</h1>
            <p>แผนที่สถานประกอบการและแปลงปลูกกัญชาทางการแพทย์ · Map of licensed cannabis establishments &amp; farm plots</p>
        </div>
        <div class="gis-source">
            ข้อมูล / Data source:
            <a href="https://cannabis-gis.dtam.moph.go.th/" target="_blank" rel="noopener noreferrer">กรมการแพทย์แผนไทยและการแพทย์ทางเลือก (DTAM) ↗</a>
        </div>
    </div>

    <div class="gis-stats">
        <div class="glass-card gis-stat"><div class="label">ทั้งหมด</div><div class="value" id="stat_total">0</div></div>
        <div class="glass-card gis-stat"><div class="label">สถานประกอบการ</div><div class="value" id="stat_establishments">0</div></div>
        <div class="glass-card gis-stat"><div class="label">แปลงปลูก</div><div class="value" id="stat_farm_plots">0</div></div>
        <div class="glass-card gis-stat"><div class="label">ดำเนินกิจการอยู่</div><div class="value" id="stat_active">0</div></div>
        <div class="glass-card gis-stat"><div class="label">พักใช้ใบอนุญาต</div><div class="value" id="stat_suspended">0</div></div>
        <div class="glass-card gis-stat"><div class="label">เพิกถอนใบอนุญาต</div><div class="value" id="stat_revoked">0</div></div>
    </div>

    <div class="glass-card search-panel">
        <form id="search-form" class="gis-form" data-local>
            <div>
                <label for="q">ค้นหา</label>
                <div class="autocomplete-wrap">
                    <input id="q" name="q" class="form-control" placeholder="ชื่อสถานประกอบการ หรือชื่อฟาร์ม" autocomplete="off">
                    <div id="autocomplete-list" class="autocomplete-list"></div>
                </div>
            </div>

            <div>
                <label for="type">ประเภทข้อมูล</label>
                <select id="type" name="type" class="form-select">
                    <option value="all">ทั้งหมด</option>
                    <option value="establishment">สถานประกอบการ</option>
                    <option value="farm_plot">แปลงปลูก</option>
                </select>
            </div>

            <div>
                <label for="status">สถานะ</label>
                <select id="status" name="status" class="form-select">
                    <option value="all">ทั้งหมด</option>
                    <option value="active">ดำเนินกิจการอยู่</option>
                    <option value="closed">ปิดกิจการ</option>
                    <option value="suspended">พักใช้ใบอนุญาต</option>
                    <option value="revoked">เพิกถอนใบอนุญาต</option>
                </select>
            </div>

            <div>
                <label for="health_region_id">เขตสุขภาพ</label>
                <select id="health_region_id" name="health_region_id" class="form-select">
                    <option value="">ทุกเขตสุขภาพ</option>
                </select>
            </div>

            <div>
                <label for="province_id">จังหวัด</label>
                <select id="province_id" name="province_id" class="form-select">
                    <option value="">ทุกจังหวัด</option>
                </select>
            </div>

            <div>
                <button class="btn-brand" type="submit">ค้นหา</button>
            </div>
        </form>
    </div>

    <div class="glass-card map-panel">
        <div id="gis-loading" class="gis-loading">กำลังโหลดข้อมูล...</div>
        <div id="map"></div>
    </div>

    <div id="dashboard-views-badge" class="dashboard-views-badge">
        <span class="dashboard-views-label">สถิติผู้ใช้งาน</span>
        <span class="dashboard-views-value" id="stat_dashboard_views">0</span>
    </div>

</main>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script src="{{ asset('public/front/gis/gis.js') }}"></script>
@endpush
