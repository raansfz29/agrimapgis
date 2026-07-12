<?= $this->extend('layouts/premium') ?>

<?php 
    $no_padding = true; 
    $title = 'Peta Tematik';
?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://unpkg.com/@geoman-io/leaflet-geoman-free@latest/dist/leaflet-geoman.css" />
<!-- Leaflet.heat for Productivity Heatmap -->
<script src="https://unpkg.com/leaflet.heat@0.2.0/dist/leaflet-heat.js"></script>
<style>
    .map-full-wrapper {
        position: relative;
        height: 700px;
        width: 100%;
        overflow: hidden;
        border-radius: 28px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        z-index: 1;
        margin-bottom: 30px;
    }

    #thematicMap {
        width: 100%;
        height: 100%;
    }

    .left-panels-container {
        position: absolute;
        top: 20px;
        left: 60px;
        width: 270px;
        display: flex;
        flex-direction: column;
        gap: 12px;
        z-index: 1000;
        max-height: calc(100% - 120px);
        overflow-y: auto;
        scrollbar-width: none;
    }

    .left-panels-container::-webkit-scrollbar {
        display: none;
    }

    .floating-card {
        background: rgba(255, 255, 255, 0.92);
        backdrop-filter: blur(10px);
        border-radius: 16px;
        padding: 15px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        border: 1px solid rgba(255,255,255,0.4);
    }

    /* Detail Card Refinement */
    .detail-card { 
        position: absolute;
        top: 20px; 
        right: 20px; 
        width: 340px; 
        padding: 0; 
        overflow: hidden; 
        z-index: 1000;
        background: white;
        border-radius: 18px;
        box-shadow: 0 15px 40px rgba(0,0,0,0.15);
        border: 1px solid rgba(0,0,0,0.05);
    }

    .panel-title {
        font-size: 10px; font-weight: 800; text-transform: uppercase;
        color: #64748b; letter-spacing: 1px; margin-bottom: 15px; display: block;
    }

    .layer-item {
        display: flex; align-items: center; justify-content: space-between;
        padding: 14px 18px; border-radius: 14px; margin-bottom: 10px;
        cursor: pointer; transition: all 0.2s; background: rgba(0,0,0,0.02);
    }

    .layer-item.active { background: #dcfce7; border: 1px solid rgba(34, 197, 94, 0.2); }
    .layer-label-box { display: flex; align-items: center; gap: 14px; }
    .layer-icon { font-size: 16px; color: #64748b; width: 20px; text-align: center; }
    .layer-item.active .layer-icon { color: #166534; }
    .layer-name { font-size: 13px; font-weight: 700; color: #1e293b; }

    .radio-circle { width: 18px; height: 18px; border: 2px solid #cbd5e1; border-radius: 50%; position: relative; }
    .layer-item.active .radio-circle { border-color: #166534; }
    .layer-item.active .radio-circle::after { 
        content: ''; position: absolute; top: 3px; left: 3px; width: 8px; height: 8px; 
        background: #166534; border-radius: 50%; 
    }

    .legend-item { display: flex; align-items: center; gap: 12px; margin-bottom: 10px; }
    .legend-color { width: 18px; height: 18px; border-radius: 4px; }
    .legend-text { font-size: 12px; font-weight: 700; color: #475569; }

    .detail-card { top: 20px; right: 20px; width: 320px; padding: 0; overflow: hidden; display: block; z-index: 1000; }
    .detail-head {
        background: #1e3a1f; color: white; padding: 18px 22px;
        display: flex; justify-content: space-between; align-items: center;
    }

    .detail-content { padding: 22px; }
    .detail-stats { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 22px; }
    .stat-box { background: #f8fafc; padding: 14px; border-radius: 14px; border: 1px solid #f1f5f9; }
    .stat-label { font-size: 10px; font-weight: 800; color: #94a3b8; text-transform: uppercase; margin-bottom: 4px; display: block; }
    .stat-value { font-size: 15px; font-weight: 800; color: #1e293b; }

    .health-status-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; }
    .badge-optimal { background: #dcfce7; color: #166534; padding: 4px 10px; border-radius: 8px; font-size: 10px; font-weight: 800; }
    .progress-bar-flat { height: 8px; background: #e2e8f0; border-radius: 4px; overflow: hidden; }
    .progress-bar-fill { height: 100%; background: #166534; width: 82%; }

    .btn-brown-detail {
        width: 100%; background: #7c5e53; color: white; border: none; padding: 14px;
        border-radius: 14px; font-weight: 800; font-size: 14px; margin-top: 25px; transition: all 0.2s;
    }

    .action-bar-center {
        position: absolute; bottom: 60px; left: 50%; transform: translateX(-50%);
        background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(20px);
        padding: 8px 10px; border-radius: 50px; box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        display: flex; align-items: center; gap: 10px; z-index: 1000; border: 1px solid rgba(255,255,255,0.5);
    }

    .btn-new-analysis {
        background: #1e3a1f; color: white; border: none; padding: 10px 20px;
        border-radius: 50px; font-weight: 700; font-size: 13px; display: flex; align-items: center; gap: 8px;
        white-space: nowrap; flex-shrink: 0;
    }

    .btn-buffer {
        background: #fee2e2; color: #991b1b; border: none; padding: 10px 18px;
        border-radius: 50px; font-weight: 700; font-size: 13px; display: flex; align-items: center; gap: 8px;
        white-space: nowrap; flex-shrink: 0;
    }
    
    .btn-buffer.active { background: #fecaca; }

    .action-circle-btn {
        width: 40px; height: 40px; border-radius: 50%; border: none; background: transparent;
        color: #475569; display: flex; align-items: center; justify-content: center;
        font-size: 18px; transition: all 0.2s; flex-shrink: 0;
    }

    .zoom-controls { position: absolute; bottom: 120px; right: 20px; display: flex; flex-direction: column; gap: 8px; z-index: 999; }
    .zoom-btn {
        width: 44px; height: 44px; background: white; border-radius: 12px; border: none;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1); color: #475569; font-size: 16px;
        display: flex; align-items: center; justify-content: center;
    }

    .mockup-footer {
        position: absolute; bottom: 0; left: 0; right: 0;
        height: 35px; background: #1e3a1f; color: rgba(255,255,255,0.7);
        display: flex; align-items: center; justify-content: space-between;
        padding: 0 20px; font-size: 9px; font-weight: 800; letter-spacing: 1px; z-index: 2000; text-transform: uppercase;
    }

    .legend-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 8px;
    }
    .legend-item-small {
        display: flex; align-items: center; gap: 8px; font-size: 10px; font-weight: 700; color: #475569;
    }
    .dot { width: 10px; height: 10px; border-radius: 50%; }

    /* Add Land Panel */
    .add-land-card {
        top: 20px; right: 20px; width: 320px;
        display: none;
        max-height: calc(100% - 60px);
        overflow-y: auto;
        z-index: 2000;
        scrollbar-width: thin;
    }
    .add-land-card.active { display: block; }
    
    .form-label-premium {
        font-size: 10px; font-weight: 800; color: #94a3b8; text-transform: uppercase; margin-bottom: 6px; display: block;
    }
    .input-premium {
        background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 10px 15px; font-size: 13px; font-weight: 600; width: 100%;
    }
    .input-premium:focus { border-color: #22c55e; outline: none; box-shadow: 0 0 0 4px rgba(34, 197, 94, 0.1); }

    /* Global Search Results */
    #search-results-dropdown {
        position: absolute;
        top: 65px;
        left: 320px; /* Aligned with sidebar and search bar */
        width: 450px;
        background: white;
        border-radius: 16px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        z-index: 2100;
        display: none;
        max-height: 400px;
        overflow-y: auto;
        border: 1px solid #e2e8f0;
    }
    .search-result-item {
        padding: 12px 20px;
        cursor: pointer;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .search-result-item:hover { background: #f8fafc; }
    .search-result-item:last-child { border-bottom: none; }
    .search-result-item .name { font-weight: 700; color: #1e293b; font-size: 13px; }
    .search-result-item .sub { font-size: 11px; color: #64748b; }

    /* Land List Panel */
    .land-list-card {
        top: 20px; left: 360px; width: 340px;
        display: none;
        max-height: calc(100vh - 120px);
        overflow-y: auto;
    }
    .land-list-card.active { display: block; }
    
    .land-item-row {
        padding: 15px 20px; border-bottom: 1px solid #f1f5f9; cursor: pointer; transition: all 0.2s;
        background: white;
    }
    .land-item-row:hover { background: #f8fafc; }
    .land-item-row:last-child { border-bottom: none; }

    /* Bottom Detail Section (Below Map) */
    .bottom-detail-section {
        background: white;
        padding: 30px;
        border-radius: 0 0 24px 24px;
        border-top: 1px solid #f1f5f9;
        display: none; /* Hidden until land selected */
    }
    .bottom-detail-section.active { display: block; }
    .detail-grid { display: grid; grid-template-columns: 1fr 1.5fr 1fr; gap: 30px; }
    .detail-col { display: flex; flex-direction: column; gap: 5px; }
    .detail-label { font-size: 10px; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px; }
    .detail-value { font-size: 18px; font-weight: 800; color: #1e293b; }
    .status-badge-premium { padding: 8px 18px; border-radius: 50px; font-size: 12px; font-weight: 800; display: inline-flex; align-items: center; gap: 10px; }

    /* Data Table Section (Stronger Visibility) */
    .table-section-premium {
        background: #fdfdfd;
        margin-top: 50px;
        margin-bottom: 50px;
        padding: 40px;
        border-radius: 30px;
        box-shadow: 0 20px 60px rgba(0,0,0,0.1);
        border: 2px solid #e2e8f0;
        position: relative;
        z-index: 100;
        display: block !important;
        min-height: 400px;
    }
    .table-section-premium h4 { color: #166534; font-size: 22px; }
    .table-section-premium p { font-size: 14px; margin-bottom: 30px; }
    .table-premium { width: 100%; border-collapse: separate; border-spacing: 0 10px; }
    .table-premium th { padding: 15px 20px; font-size: 10px; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px; border: none; }
    .table-premium tr { transition: all 0.2s; }
    .table-premium td { padding: 18px 20px; background: #f8fafc; border: none; font-size: 13px; font-weight: 700; color: #1e293b; }
    .table-premium td:first-child { border-radius: 15px 0 0 15px; }
    .table-premium td:last-child { border-radius: 0 15px 15px 0; }
    .table-premium tr:hover td { background: #f1f5f9; transform: scale(1.005); }

    /* Responsive Adjustments for Map View */
    @media (max-width: 991px) {
        .map-full-wrapper { height: calc(100vh - 120px); border-radius: 16px; margin-bottom: 20px; }
        
        .left-panels-container { 
            top: 10px; left: 55px; width: 220px; 
            max-height: calc(100% - 100px); 
            overflow-y: auto; 
            pointer-events: auto;
            scrollbar-width: thin;
            display: none; /* Hide by default on mobile to prevent blocking map */
        }
        .left-panels-container.active-mobile {
            display: flex; /* Show when toggled */
            animation: slideInRight 0.3s ease;
        }
        .left-panels-container::-webkit-scrollbar { display: block; width: 4px; }
        .left-panels-container::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.2); border-radius: 4px; }
        
        /* Shrink map panels heavily to prevent clipping */
        .floating-card { padding: 12px !important; }
        .panel-title { margin-bottom: 10px !important; font-size: 9px !important; }
        .layer-item { padding: 8px 12px !important; margin-bottom: 6px !important; }
        .layer-name { font-size: 11px !important; }
        .legend-item { margin-bottom: 6px !important; }
        .legend-text { font-size: 10px !important; }

        .detail-card { width: calc(100% - 20px); right: 10px; top: 10px; max-height: 80vh; overflow-y: auto; }
        .add-land-card { width: calc(100% - 20px); right: 10px; top: 10px; }
        .land-list-card { width: calc(100% - 20px); left: 10px; top: 10px; }
        #search-results-dropdown { left: 10px; width: calc(100% - 20px); top: 50px; }
        .action-bar-center { 
            width: calc(100% - 20px); bottom: 10px; 
            flex-wrap: wrap; 
            justify-content: center; padding: 10px 15px; border-radius: 20px; 
            background: rgba(255, 255, 255, 0.95);
            gap: 10px;
        }
        .zoom-controls { bottom: 130px; right: 10px; }
        .table-section-premium { padding: 20px; margin-top: 20px; margin-bottom: 20px; border-radius: 20px; }
        .table-section-premium .d-flex.justify-content-between { flex-direction: column; align-items: flex-start !important; gap: 15px; }
        .table-section-premium .input-group { width: 100% !important; }
        .detail-grid { grid-template-columns: 1fr; gap: 20px; }
        .bottom-detail-section { padding: 20px; }
        .bottom-detail-section .d-flex.justify-content-between { flex-direction: column; align-items: flex-start !important; gap: 15px; }
    }
    /* Print Styles (Laporan Premium) */
    @media print {
        @page { size: A4 portrait; margin: 10mm; }
        html, body { height: auto !important; min-height: auto !important; background: white !important; font-family: 'Outfit', sans-serif !important; color: black !important; margin: 0; padding: 0; }
        
        .sidebar, .top-nav, .footer-mockup, .btn, .d-print-none, .page-title-area, .pagination-area, .map-full-wrapper, .alert {
            display: none !important;
        }
        
        .main-content { margin: 0 !important; padding: 0 !important; width: 100% !important; max-width: 100% !important; }
        .table-section-premium { border: none !important; box-shadow: none !important; padding: 0 !important; margin: 0 !important; }

        /* Blue Header */
        .stat-header {
            background: #1e293b !important;
            color: white !important;
            padding: 15px 25px;
            display: flex !important;
            align-items: center;
            gap: 20px;
            -webkit-print-color-adjust: exact;
            margin-bottom: 20px;
            border-radius: 12px;
        }
        .stat-logo-box {
            width: 50px; height: 50px;
            background: white;
            border-radius: 10px;
            display: flex !important; align-items: center; justify-content: center;
        }
        .stat-logo-box i { font-size: 24px; color: #1e293b; }
        .stat-header-text h4 { margin: 0; font-weight: 800; font-size: 13pt; letter-spacing: 0.5px; }
        .stat-header-text p { margin: 0; font-size: 8pt; opacity: 0.9; }

        .stat-title { text-align: center; margin-bottom: 15px; }
        .stat-title h2 { font-weight: 900; font-size: 14pt; text-transform: uppercase; margin-bottom: 5px; color: #0f172a; }

        .stat-meta-table { width: 100% !important; font-weight: 800; font-size: 9pt; margin-bottom: 15px; border-bottom: 2px solid #e2e8f0; padding-bottom: 8px; color: #1e293b; border-collapse: collapse; }
        .stat-meta-table td { padding: 0 8px; padding-bottom: 8px; }

        /* Professional Table Style */
        .table-formal-report { width: 100% !important; border-collapse: collapse !important; margin-bottom: 15px; }
        .table-formal-report thead { background-color: #f1f5f9 !important; -webkit-print-color-adjust: exact; border-bottom: 2px solid #cbd5e1 !important; border-top: 2px solid #cbd5e1 !important; }
        .table-formal-report th {
            color: #334155 !important;
            padding: 8px 6px !important;
            font-weight: 800 !important;
            text-align: left;
            font-size: 8.5pt;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border: none !important;
        }
        .table-formal-report th.text-center, .table-formal-report th.text-end { text-align: center !important; }
        .table-formal-report td { 
            padding: 6px 6px !important; 
            border-bottom: 1px solid #e2e8f0 !important; 
            border-left: none !important;
            border-right: none !important;
            border-top: none !important;
            background: white !important;
            font-size: 8.5pt; 
            color: #1e293b;
            vertical-align: middle;
        }
        .table-formal-report tbody tr:last-child td { border-bottom: 2px solid #cbd5e1 !important; }

        /* Signature Area */
        .stat-sig-table { width: 100% !important; margin-top: 20px; page-break-inside: avoid; border-collapse: collapse; table-layout: fixed; }
        .stat-sig-table td { width: 33.33%; text-align: center; color: #1e293b; vertical-align: top; padding: 0; }
        .sig-name { font-weight: 800; text-decoration: underline; margin-top: 40px; font-size: 10pt; }
        .sig-nip { font-size: 9pt; margin-top: 2px; }
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Flash Messages -->
<div class="container-fluid mb-3">
    <?php if (session()->getFlashdata('success')) : ?>
        <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm" role="alert">
            <div class="d-flex align-items-center">
                <i class="fas fa-check-circle me-3 fs-4"></i>
                <div>
                    <strong>Berhasil!</strong> <?= session()->getFlashdata('success') ?>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')) : ?>
        <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm" role="alert">
            <div class="d-flex align-items-center">
                <i class="fas fa-exclamation-triangle me-3 fs-4"></i>
                <div>
                    <strong>Gagal!</strong> <?= session()->getFlashdata('error') ?>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
</div>

<!-- Formal Print Header (New Premium Style) -->
<div class="d-none d-print-block">
    <div class="stat-header">
        <div class="stat-logo-box">
            <i class="fas fa-seedling"></i>
        </div>
        <div class="stat-header-text">
            <h4>PEMERINTAH KOTA BANDAR LAMPUNG</h4>
            <p>DINAS PERTANIAN DAN KETAHANAN PANGAN</p>
            <p>Jl. Dr. Susilo No.1, Bandar Lampung, Lampung. Telp: (0721) 252300</p>
        </div>
    </div>

    <div class="stat-title">
        <h2>LAPORAN INVENTARISASI SPASIAL LAHAN PERTANIAN</h2>
        <p class="fw-bold">Database Lahan Terdaftar • Wilayah Binaan Rajabasa</p>
    </div>
    
    <table class="stat-meta-table">
        <tr>
            <td style="text-align: left;">ID Dokumen: GIS/INV/<?= date('Ymd') ?></td>
            <td style="text-align: right;">Dicetak: <?= date('d/m/Y H:i') ?> WIB</td>
        </tr>
    </table>
</div>

<!-- Table Section (Moved to Top for Visibility) -->
<div class="table-section-premium" style="display: block !important; margin-top: 0; margin-bottom: 40px;">
    <div class="d-flex justify-content-between align-items-center mb-5 d-print-none">
        <div>
            <h4 class="fw-800 mb-2">📋 Tabel Informasi Detail Lahan</h4>
            <p class="text-muted mb-0">Database lengkap mencakup status fase dan detail geografis lahan Anda.</p>
        </div>
        <div class="d-flex gap-3">
            <div class="input-group" style="width: 320px;">
                <span class="input-group-text bg-white border-1"><i class="fas fa-search text-muted"></i></span>
                <input type="text" class="form-control border-1" placeholder="Cari lahan atau komoditas..." onkeyup="filterDataTable(this.value)">
            </div>
            <button class="btn btn-success rounded-pill px-4 fw-800 shadow-sm" onclick="window.print()" style="font-size: 13px;">
                <i class="fas fa-print me-2"></i> Ekspor PDF
            </button>
        </div>
    </div>

    <div class="table-responsive" style="min-height: 200px;">
        <table class="table table-premium table-formal-report">
            <thead style="background: #f8fafc;">
                <tr>
                    <th>Identitas Lahan</th>
                    <th>Komoditas</th>
                    <th>Luas (Ha)</th>
                    <th>Status Fase</th>
                    <th>Kondisi</th>
                    <th class="text-end d-print-none">Tindakan</th>
                </tr>
            </thead>
            <tbody id="landDataTableBody">
                <tr><td colspan="6" class="text-center p-5 text-muted">Memuat database lahan...</td></tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Print-only Signature Area -->
<div class="d-none d-print-block">
    <table class="stat-sig-table">
        <tr>
            <td>
                <p>Mengetahui,<br>Analis Pertanian</p>
                <div class="sig-name">Analis Pertanian Utama</div>
                <p class="sig-nip">NIP. 19850215 201201 1 005</p>
            </td>
            <td>
                <p><br>Kabid Perencanaan Lahan</p>
                <div class="sig-name">Sari Wijaya, S.P.</div>
                <p class="sig-nip">NIP. 19750512 200501 2 003</p>
            </td>
            <td>
                <p><br>Kepala Dinas Pertanian</p>
                <div class="sig-name">Dr. Ir. Heru Santoso</div>
                <p class="sig-nip">NIP. 19680320 199403 1 002</p>
            </td>
        </tr>
    </table>
</div>

<div class="map-full-wrapper">
    <!-- Global Search Dropdown (Outside map but inside wrapper) -->
    <div id="search-results-dropdown"></div>

    <div id="thematicMap">
        <!-- Left Panels Container (Combined) -->
        <div class="left-panels-container">
            <div class="floating-card">
                <span class="panel-title">Lapisan Peta Tematik</span>
                <div class="layer-item active"><div class="layer-label-box"><i class="fas fa-calendar-day layer-icon"></i><span class="layer-name">Fase Lahan</span></div><div class="radio-circle"></div></div>
                <div class="layer-item"><div class="layer-label-box"><i class="fas fa-bug layer-icon text-danger"></i><span class="layer-name">Kerawanan Hama (Heatmap)</span></div><div class="radio-circle"></div></div>
            </div>


            <div class="floating-card" id="legend-fase" style="display: block;">
                <span class="panel-title">Legenda Status Fase</span>
                <div class="d-flex flex-column gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <div class="dot" style="background: #fbbf24; width: 12px; height: 12px; border: 2px solid white; box-shadow: 0 0 0 1px #fbbf24;"></div>
                        <span class="fw-800 text-dark" style="font-size: 11px;">Persiapan Lahan</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <div class="dot" style="background: #22c55e; width: 12px; height: 12px; border: 2px solid white; box-shadow: 0 0 0 1px #22c55e;"></div>
                        <span class="fw-800 text-dark" style="font-size: 11px;">Fase Tanam</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <div class="dot" style="background: #3b82f6; width: 12px; height: 12px; border: 2px solid white; box-shadow: 0 0 0 1px #3b82f6;"></div>
                        <span class="fw-800 text-dark" style="font-size: 11px;">Pemeliharaan</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <div class="dot" style="background: #f59e0b; width: 12px; height: 12px; border: 2px solid white; box-shadow: 0 0 0 1px #f59e0b;"></div>
                        <span class="fw-800 text-dark" style="font-size: 11px;">Siap Panen</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <div class="dot" style="background: #94a3b8; width: 12px; height: 12px; border: 2px solid white; box-shadow: 0 0 0 1px #94a3b8;"></div>
                        <span class="fw-800 text-dark" style="font-size: 11px;">Lahan Bera</span>
                    </div>
                    <div class="d-flex align-items-center gap-2 mt-1 pt-2 border-top">
                        <div class="dot" style="background: #ef4444; width: 12px; height: 12px; border: 2px solid white; box-shadow: 0 0 0 1px #ef4444;"></div>
                        <span class="fw-800 text-danger" style="font-size: 11px;">Status Darurat/Bencana</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="floating-panel detail-card" id="landCard" style="display: none;">
            <div class="detail-head">
                <div><span class="x-small fw-800 opacity-60">Lahan #RJ-204</span><h5 class="fw-800 mb-0">Budi Santoso</h5></div>
                <i class="fas fa-times cursor-pointer opacity-50" onclick="hideCard()"></i>
            </div>
            <div class="detail-content">
                <div class="detail-stats">
                    <div class="stat-box"><span class="stat-label">Luas Area</span><span class="stat-value" id="card-area">2.1 Ha</span></div>
                    <div class="stat-box"><span class="stat-label">Est. Panen</span><span class="stat-value text-success" id="card-harvest" style="font-size: 13px;">--</span></div>
                </div>
                <div class="health-status-row"><span class="text-muted small fw-800">Kesehatan Tanaman</span><span class="badge-optimal">OPTIMAL</span></div>
                <div class="progress-bar-flat"><div class="progress-bar-fill"></div></div>
                <p class="x-small text-muted mt-2 fw-700 italic mb-0">Skor NDVI: 0.82 (Update 2 jam yang lalu)</p>
                <button class="btn-brown-detail shadow-sm">Detail Analisis Lahan</button>
            </div>
        </div>

        <!-- Floating Add Land Form -->
        <div class="floating-panel detail-card add-land-card" id="addLandCard">
            <div class="detail-head" style="background: #166534; padding: 12px 20px;">
                <div><span class="x-small fw-800 opacity-60" id="form-land-subtitle">Pendaftaran</span><h5 class="fw-800 mb-0" id="form-land-title" style="font-size: 15px;">Tambah Lahan Baru</h5></div>
                <i class="fas fa-times cursor-pointer opacity-50" onclick="hideAddLand()"></i>
            </div>
            <div class="detail-content" style="padding: 15px 20px;">
                <form id="form-land" action="<?= base_url('farmer-groups/store-land') ?>" method="post">
                    <input type="hidden" name="id_lahan" id="form-id-lahan" value="">
                    <?= csrf_field() ?>
                    <?php if (session()->get('role') === 'petani'): ?>
                    <div class="mb-2">
                        <span class="form-label-premium" style="margin-bottom: 4px;">Kelompok Tani</span>
                        <input type="hidden" name="id_kelompok" id="form-group-id" value="<?= session()->get('id_kelompok') ?>">
                        <?php
                            $myGroup = null;
                            if(isset($groups)) {
                                foreach($groups as $g) {
                                    if ($g['id_kelompok'] == session()->get('id_kelompok')) { $myGroup = $g; break; }
                                }
                            }
                        ?>
                        <input type="text" class="input-premium bg-light" readonly value="<?= esc($myGroup['nama_kelompok'] ?? 'Kelompok Saya') ?>" style="padding: 8px 12px; font-size: 12px;">
                    </div>
                    <?php else: ?>
                    <div class="mb-2">
                        <span class="form-label-premium" style="margin-bottom: 4px;">Pilih Kelompok Tani</span>
                        <select name="id_kelompok" id="form-group-id" class="input-premium" required style="appearance: auto; padding: 8px 12px; font-size: 12px;">
                            <option value="">-- Pilih Kelompok --</option>
                            <?php if(isset($groups)): ?>
                                <?php foreach($groups as $g): ?>
                                    <option value="<?= $g['id_kelompok'] ?>" <?= (session()->get('id_kelompok') == $g['id_kelompok']) ? 'selected' : '' ?>><?= esc($g['nama_kelompok']) ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <?php endif; ?>

                    <div class="mb-2">
                        <span class="form-label-premium" style="margin-bottom: 4px;">Nama Lahan</span>
                        <input type="text" name="nama_lahan" id="form-nama-lahan" class="input-premium" required placeholder="Contoh: Sawah Blok B" style="padding: 8px 12px; font-size: 12px;">
                    </div>

                    <div class="mb-2">
                        <span class="form-label-premium" style="margin-bottom: 4px;">Komoditas</span>
                        <select name="komoditas" id="form-komoditas" class="input-premium" required style="appearance: auto; padding: 8px 12px; font-size: 12px;">
                            <option value="">-- Pilih Komoditas --</option>
                            <option value="padi">Padi</option>
                            <option value="jagung">Jagung</option>
                        </select>
                    </div>

                    <div class="mb-2">
                        <span class="form-label-premium" style="margin-bottom: 4px;">Alamat / Detail Lokasi</span>
                        <input type="text" name="alamat" id="form-alamat" class="input-premium" required placeholder="Lokasi spesifik" style="padding: 8px 12px; font-size: 12px;">
                    </div>

                    <div class="row g-2 mb-2">
                        <div class="col-6">
                            <span class="form-label-premium" style="margin-bottom: 4px;">LATITUDE</span>
                            <input type="number" step="any" name="lat" id="map-lat" class="input-premium" placeholder="-5.xxxx" style="padding: 8px 12px; font-size: 12px;">
                        </div>
                        <div class="col-6">
                            <span class="form-label-premium" style="margin-bottom: 4px;">LONGITUDE</span>
                            <input type="number" step="any" name="lng" id="map-lng" class="input-premium" placeholder="105.xxxx" style="padding: 8px 12px; font-size: 12px;">
                        </div>
                    </div>

                    <div class="mb-3">
                        <button type="button" class="btn btn-outline-primary w-100 rounded-pill fw-bold" id="btn-map-location" style="padding: 6px; font-size: 11px;">
                            <i class="fas fa-location-crosshairs me-1"></i>Gunakan Lokasi Saat Ini
                        </button>
                    </div>

                    <div class="mb-3">
                        <span class="form-label-premium" style="margin-bottom: 4px;">Luas Lahan (Hektar) <small class="text-success fw-bold">(Otomatis/Manual)</small></span>
                        <input type="number" step="any" name="luas" id="map-luas" class="input-premium bg-light" required placeholder="0.00" style="padding: 8px 12px; font-size: 12px;">
                    </div>

                    <input type="hidden" name="id_user" value="<?= session()->get('id_user') ?>">
                    <input type="hidden" name="geojson" id="map-geojson">
                    <button type="submit" id="btn-form-submit" class="btn-brown-detail shadow-sm w-100" style="background: #166534; margin-top: 5px; padding: 10px; font-size: 13px;">Simpan Data Lahan</button>
                </form>
            </div>
        </div>

        <!-- Land List Card -->
        <div class="floating-panel detail-card land-list-card" id="landListCard">
            <div class="detail-head" style="background: #334155;">
                <div><span class="x-small fw-800 opacity-60">Database GIS</span><h5 class="fw-800 mb-0"><?= session()->get('role') === 'petani' ? 'Lahan Saya' : 'Daftar Lahan Terdaftar' ?></h5></div>
                <i class="fas fa-times cursor-pointer opacity-50" onclick="hideLandList()"></i>
            </div>
            <div class="p-3 bg-light border-bottom">
                <?php if (session()->get('role') === 'admin'): ?>
                    <select id="filterGroup" class="input-premium mb-2" onchange="loadMapData()">
                        <option value="">Semua Kelompok</option>
                        <?php foreach($groups as $g): ?>
                            <option value="<?= $g['id_kelompok'] ?>"><?= esc($g['nama_kelompok']) ?></option>
                        <?php endforeach; ?>
                    </select>
                <?php endif; ?>
                <input type="text" id="searchLand" class="input-premium" placeholder="Cari nama lahan..." onkeyup="filterLandList()">
            </div>
            <div class="detail-content p-0" id="landListContent">
                <div class="p-4 text-center text-muted small">Memuat data lahan...</div>
            </div>
        </div>

        <div class="action-bar-center">
            <?php if (session()->get('role') !== 'petani'): ?>
            <button class="btn-new-analysis shadow-lg" onclick="event.stopPropagation(); showAddLand(event)"><i class="fas fa-plus-circle"></i> Tambah Lahan Baru</button>
            <div class="vr opacity-10 mx-1" style="height: 24px;"></div>
            <?php endif; ?>
            <button class="btn-buffer" id="btnBuffer" onclick="event.stopPropagation(); toggleBuffer()"><i class="fas fa-bullseye"></i> Buffer</button>
            <div class="vr opacity-10 mx-1" style="height: 24px;"></div>
            <button class="action-circle-btn" onclick="event.stopPropagation(); toggleLeftPanels()" title="Lapisan Peta"><i class="fas fa-layer-group"></i></button>
            <button class="action-circle-btn" onclick="event.stopPropagation(); locateUser()" title="Lokasi Saya"><i class="fas fa-location-arrow"></i></button>
            <button class="action-circle-btn" onclick="event.stopPropagation(); toggleLandList()" title="Daftar Lahan"><i class="fas fa-table-list"></i></button>
        </div>

        <div class="zoom-controls">
            <button class="zoom-btn" onclick="map.zoomIn()"><i class="fas fa-plus"></i></button>
            <button class="zoom-btn" onclick="map.zoomOut()"><i class="fas fa-minus"></i></button>
            <button class="zoom-btn" onclick="locateUser()"><i class="fas fa-crosshairs"></i></button>
        </div>

        <div class="mt-4 pb-2 text-center opacity-50 small fw-bold" style="color: white; z-index: 1000; position: relative;">
            &copy; <?= date('Y') ?> AgriMapGIS. Hak Cipta Dilindungi.
        </div>
    </div>

    <!-- Bottom Detail Section (Static) -->
    <div class="bottom-detail-section" id="landBottomPanel">
        <div class="d-flex justify-content-between align-items-center mb-5">
            <div>
                <span class="x-small fw-800 text-success opacity-50 uppercase mb-1 d-block">Detail Analisis Lahan Terpilih</span>
                <h2 class="fw-800 mb-1" id="bp-name">Sawah Blok A</h2>
                <p class="text-muted mb-0"><i class="fas fa-map-marker-alt me-2"></i><span id="bp-address">Rajabasa, Lampung Selatan</span></p>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-outline-secondary rounded-pill px-4 fw-800" onclick="hideBottomPanel()" style="font-size: 12px;">Tutup</button>
                <?php if (session()->get('role') !== 'petani'): ?>
                    <button class="btn btn-outline-danger rounded-pill px-4 fw-800" id="btn-delete-land" style="font-size: 12px;">
                        <i class="fas fa-trash-alt me-2"></i>Hapus Lahan
                    </button>
                <?php endif; ?>
                <button class="btn btn-success rounded-pill px-4 fw-800 shadow-sm" style="font-size: 12px;">Cetak Laporan GIS</button>
            </div>
        </div>
        
        <div class="detail-grid">
            <div class="detail-col">
                <span class="detail-label">Parameter Lahan</span>
                <div class="d-flex align-items-center gap-3 mt-3">
                    <div class="p-3 rounded-3 bg-success bg-opacity-10 text-success" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;"><i class="fas fa-seedling fa-lg"></i></div>
                    <div>
                        <div class="detail-value" id="bp-commodity">Padi</div>
                        <div class="text-muted" style="font-size: 11px;">Komoditas Utama</div>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-3 mt-4">
                    <div class="p-3 rounded-3 bg-primary bg-opacity-10 text-primary" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;"><i class="fas fa-expand-arrows-alt fa-lg"></i></div>
                    <div>
                        <div class="detail-value" id="bp-area">1.23 Ha</div>
                        <div class="text-muted" style="font-size: 11px;">Luas Total Lahan</div>
                    </div>
                </div>
            </div>

            <div class="detail-col">
                <span class="detail-label">Estimasi Panen</span>
                <div class="mt-3">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="fw-800 text-dark small">Prediksi Tanggal Panen</span>
                    </div>
                    <div class="d-flex justify-content-between mt-2">
                        <span class="text-muted" style="font-size: 10px;">Panen: <strong id="bp-harvest-date" class="text-dark">--</strong></span>
                    </div>
                </div>
            </div>

            <div class="detail-col">
                <span class="detail-label">Status & Verifikasi</span>
                <div class="mt-3 d-flex flex-column gap-3">
                    <div id="bp-fase-badge" class="status-badge-premium bg-warning bg-opacity-10 text-warning">
                        <i class="fas fa-clock"></i> FASE PERSIAPAN
                    </div>
                    <div id="bp-status-badge" class="status-badge-premium bg-success bg-opacity-10 text-success">
                        <i class="fas fa-check-circle"></i> KONDISI OPTIMAL
                    </div>
                </div>
                <button class="btn btn-dark w-100 mt-4 fw-800 rounded-3 py-3" style="font-size: 13px;">
                    <i class="fas fa-history me-2"></i> RIWAYAT AKTIVITAS LENGKAP
                </button>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/@geoman-io/leaflet-geoman-free@latest/dist/leaflet-geoman.min.js"></script>

<script>
    var map;
    var landLayer;
    var bufferLayer;
    var heatmapLayer;
    var currentMode = 'fase';

    document.addEventListener('DOMContentLoaded', function() {
        map = L.map('thematicMap', { zoomControl: false, attributionControl: false }).setView([-5.37, 105.25], 14);
        L.tileLayer('https://{s}.google.com/vt/lyrs=s,h&x={x}&y={y}&z={z}', { 
            maxZoom: 20, 
            subdomains:['mt0','mt1','mt2','mt3'],
            attribution: 'Google Satellite'
        }).addTo(map);

        loadMapData();
            
        // Geoman Map Setup
        map.pm.addControls({
            position: 'topleft',
            drawMarker: false,
            drawCircleMarker: false,
            drawPolyline: false,
            drawRectangle: false,
            drawPolygon: true,
            drawCircle: false,
            drawText: false,
            editMode: true,
            dragMode: true,
            cutPolygon: false,
            removalMode: true,
        });

        map.on('click', (e) => {
            if (!document.getElementById('addLandCard').classList.contains('active')) {
                hideCard();
            }
        });

        // Listen for drawn polygons
        map.on('pm:create', (e) => {
            if (!document.getElementById('addLandCard').classList.contains('active')) {
                alert("Harap buka form 'Tambah Lahan Baru' terlebih dahulu sebelum menggambar!");
                e.layer.remove();
                return;
            }
            
            const layer = e.layer;
            const geojson = layer.toGeoJSON();
            
            if(geojson.geometry.type !== 'Polygon') {
                alert('Tolong gambar area lahan berupa Polygon tertutup.');
                layer.remove();
                return;
            }

            try {
                // turf.js is included globally in premium.php
                const areaSqm = turf.area(geojson);
                const areaHa = areaSqm / 10000;
                
                document.getElementById('map-luas').value = areaHa.toFixed(4);
                document.getElementById('map-geojson').value = JSON.stringify(geojson.geometry);
                
                // If lat/lng are empty, populate them with the centroid of the polygon
                if (!document.getElementById('map-lat').value || !document.getElementById('map-lng').value) {
                    const center = layer.getBounds().getCenter();
                    document.getElementById('map-lat').value = center.lat.toFixed(6);
                    document.getElementById('map-lng').value = center.lng.toFixed(6);
                }
                
                alert(`Luas lahan terhitung otomatis: ${areaHa.toFixed(2)} Ha. Poligon tersimpan. Titik lokasi juga telah disesuaikan.`);
            } catch (err) {
                console.error("Error calculating area:", err);
            }
        });

        // Check for add_land param
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('add_land') && urlParams.get('id_kelompok')) {
            showAddLand(urlParams.get('id_kelompok'));
        }
    });

    function initFarmerView() {
        <?php if (session()->get('role') === 'petani'): ?>
            if (typeof toggleLandList === 'function') toggleLandList();
        <?php endif; ?>
    }

    document.addEventListener('DOMContentLoaded', function() {
        const btnMapLoc = document.getElementById('btn-map-location');
        const mapLat = document.getElementById('map-lat');
        const mapLng = document.getElementById('map-lng');
        const mapGeojson = document.getElementById('map-geojson');

        let manualMarker = null;

        function updateManualGeojson() {
            const lat = parseFloat(mapLat.value);
            const lng = parseFloat(mapLng.value);
            if (!isNaN(lat) && !isNaN(lng) && lat !== 0 && lng !== 0) {
                // Only overwrite geojson if it's empty or NOT a polygon
                if (!mapGeojson.value || !mapGeojson.value.includes('Polygon')) {
                    mapGeojson.value = JSON.stringify({"type": "Point", "coordinates": [lng, lat]});
                }
                
                // Automatically move map to the coordinate
                map.setView([lat, lng], 18);
                
                // Update or create a marker to show the location
                if (manualMarker) {
                    manualMarker.setLatLng([lat, lng]);
                } else {
                    manualMarker = L.marker([lat, lng]).addTo(map).bindPopup("Titik Koordinat Manual").openPopup();
                }
            }
        }

        if (mapLat && mapLng) {
            mapLat.addEventListener('input', updateManualGeojson);
            mapLng.addEventListener('input', updateManualGeojson);
        }

        if (btnMapLoc) {
            btnMapLoc.addEventListener('click', function() {
                // Check if already has polygon
                if (mapGeojson.value && mapGeojson.value.includes('Polygon')) {
                    if (!confirm('Anda sudah menggambar poligon lahan. Menggunakan lokasi saat ini akan menghapus poligon tersebut. Lanjutkan?')) {
                        return;
                    }
                    // Remove drawn layers if any
                    map.eachLayer(l => { if (l.pm && l !== map) map.removeLayer(l); });
                }

                const originalText = this.innerHTML;
                this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Mendeteksi...';
                this.disabled = true;

                if ("geolocation" in navigator) {
                    navigator.geolocation.getCurrentPosition((position) => {
                        const lat = position.coords.latitude;
                        const lng = position.coords.longitude;
                        
                        mapLat.value = lat.toFixed(6);
                        mapLng.value = lng.toFixed(6);
                        
                        // ONLY set geojson to point if NO polygon is already drawn
                        if (!mapGeojson.value || !mapGeojson.value.includes('Polygon')) {
                            mapGeojson.value = JSON.stringify({"type": "Point", "coordinates": [lng, lat]});
                        }
                        
                        // Add marker to map to show user
                        L.marker([lat, lng]).addTo(map).bindPopup("Lokasi GPS Anda").openPopup();
                        map.setView([lat, lng], 18);
                        
                        this.innerHTML = '<i class="fas fa-check me-2"></i>Titik Lahan Ditandai!';
                        this.classList.replace('btn-outline-primary', 'btn-success');
                        
                        setTimeout(() => {
                            this.innerHTML = originalText;
                            this.classList.replace('btn-success', 'btn-outline-primary');
                            this.disabled = false;
                        }, 3000);
                        
                        alert("Titik lahan berhasil didapatkan dari lokasi saat ini. Silakan isi Luas Lahan secara manual.");
                    }, (error) => {
                        alert("Gagal mengambil lokasi: " + error.message + " (Pastikan izin lokasi di browser diaktifkan atau gunakan localhost/HTTPS).");
                        this.innerHTML = originalText;
                        this.disabled = false;
                    }, { enableHighAccuracy: true, timeout: 10000 });
                } else {
                    alert("Browser Anda tidak mendukung fitur lokasi, atau fitur ini diblokir karena Anda tidak menggunakan koneksi aman (HTTPS / localhost).");
                    this.innerHTML = originalText;
                    this.disabled = false;
                }
            });
        }
    });

    function showAddLand(evtOrGroupId = null) {
        // Prevent event bubbling if first arg is an event object
        if (evtOrGroupId && typeof evtOrGroupId === 'object' && evtOrGroupId.stopPropagation) {
            evtOrGroupId.stopPropagation();
            evtOrGroupId = null; // reset so no groupId override
        }
        
        const groupId = evtOrGroupId; // will be a string groupId if passed from URL, null otherwise
        
        const card = document.getElementById('addLandCard');
        card.classList.add('active');
        
        // Only override group select if a specific groupId is provided AND role is not petani
        // (for petani, the group is fixed via hidden input)
        const groupInput = document.getElementById('form-group-id');
        if (groupId && groupInput && groupInput.tagName === 'SELECT') {
            groupInput.value = groupId;
        }
        
        document.getElementById('landCard').style.display = 'none';
        
        // Enable Geoman polygon drawing
        if (map && map.pm) {
            map.pm.enableDraw('Polygon', {
                snappable: true,
                snapDistance: 20,
            });
        }
        
        // Small timeout to let alert appear after draw mode is active
        setTimeout(() => {
            alert("Mode Menggambar Poligon Aktif.\n\nKlik pada peta untuk menentukan titik-titik batas lahan Anda. Klik pada titik pertama untuk menutup area dan menyelesaikan gambar.");
        }, 100);
    }

    function hideAddLand() {
        document.getElementById('addLandCard').classList.remove('active');
        if (map.pm) map.pm.disableDraw();
        resetFormToAddMode();
        // Clear URL params without refresh
        window.history.replaceState({}, document.title, window.location.pathname);
    }

    function loadMapData() {
        if (landLayer) map.removeLayer(landLayer);
        if (heatmapLayer) map.removeLayer(heatmapLayer);
        
        // Clear all markers that might have been added by add markers loop
        map.eachLayer(l => { if (l instanceof L.Marker && l !== map) map.removeLayer(l); });

        let url = '<?= base_url('map/api-lands') ?>?t=' + new Date().getTime();
        const filterGroup = document.getElementById('filterGroup');
        if (filterGroup && filterGroup.value) {
            url += '&id_kelompok=' + filterGroup.value;
        }

        fetch(url)
            .then(res => res.json())
            .then(data => {
                if (currentMode === 'heatmap') {
                    // Fetch real heatmap data
                    let heatUrl = '<?= base_url('map/api-heatmap') ?>?days=30&t=' + new Date().getTime();
                    if (filterGroup && filterGroup.value) heatUrl += '&id_kelompok=' + filterGroup.value;
                    
                    fetch(heatUrl)
                        .then(r => r.json())
                        .then(pts => {
                            if (pts.length > 0) {
                                heatmapLayer = L.heatLayer(pts, { 
                                    radius: 35, 
                                    blur: 25, 
                                    max: 10,
                                    gradient: {0.2: 'blue', 0.4: 'lime', 0.6: 'yellow', 0.8: 'orange', 1.0: 'red'}
                                }).addTo(map);
                            } else {
                                alert("Belum ada laporan hama dalam 30 hari terakhir.");
                            }
                        });
                } else {
                    landLayer = L.geoJSON(data, {
                        style: styleFeature,
                        onEachFeature: function(f, l) {
                            l.on({
                                mouseover: highlightFeature,
                                mouseout: resetHighlight,
                                click: function(e) {
                                    L.DomEvent.stopPropagation(e); 
                                    showLand(f.properties); 
                                    if (e.target.getBounds) {
                                        map.fitBounds(e.target.getBounds(), { padding: [50, 50] });
                                    } else if (e.target.getLatLng) {
                                        map.setView(e.target.getLatLng(), 18);
                                    }
                                }
                            });
                            l.bindTooltip(`<b>${f.properties.nama_lahan}</b><br><span style="font-size:10px">${f.properties.status_fase.toUpperCase()}</span>`, { 
                                sticky: true, 
                                direction: 'top', 
                                className: 'custom-tooltip' 
                            });
                        }
                    }).addTo(map);

                    // Auto-zoom to show all polygons
                    try {
                        const bounds = landLayer.getBounds();
                        if (bounds.isValid()) {
                            map.fitBounds(bounds, { padding: [40, 40] });
                        }
                    } catch(e) {}

                    // Add reference markers for each land if they have lat/lng
                    data.features.forEach(f => {
                        if (f.properties.latitude && f.properties.longitude) {
                            const marker = L.marker([f.properties.latitude, f.properties.longitude], {
                                icon: L.divIcon({
                                    className: 'custom-div-icon',
                                    html: `<div style="background-color:#155724; width:12px; height:12px; border:2px solid white; border-radius:50%; box-shadow:0 0 10px rgba(0,0,0,0.3);"></div>`,
                                    iconSize: [12, 12],
                                    iconAnchor: [6, 6]
                                })
                            }).addTo(map);
                            
                            marker.on('click', (e) => {
                                L.DomEvent.stopPropagation(e);
                                showLand(f.properties);
                            });
                        }
                    });
                }
            })
            .catch(err => {
                console.error('[AgriMapGIS] Gagal memuat data peta:', err);
            });
    }

    function styleFeature(feature) {
        const props = feature.properties;
        let color = '#22c55e';
        let fillOpacity = 0.5;

        // Color based on phase or disaster
        if (props.status_bencana === 'darurat') {
            color = '#ef4444'; // Red for disaster
        } else {
            switch(currentMode) {
                case 'fase':
                    const fase = props.status_fase;
                    if (fase === 'persiapan') color = '#fbbf24'; // Kuning
                    else if (fase === 'tanam') color = '#22c55e'; // Hijau
                    else if (fase === 'pemeliharaan') color = '#3b82f6'; // Biru
                    else if (fase === 'panen') color = '#f59e0b'; // Emas
                    else if (fase === 'bera') color = '#94a3b8'; // Abu-abu
                    else color = '#22c55e';
                    break;
                default:
                    color = '#22c55e';
            }
        }

        return {
            fillColor: color,
            weight: 2,
            opacity: 1,
            color: 'white',
            fillOpacity: fillOpacity
        };
    }

    function highlightFeature(e) {
        var layer = e.target;
        layer.setStyle({
            weight: 4,
            color: '#fff',
            fillOpacity: 0.8
        });
        layer.bringToFront();
    }

    function resetHighlight(e) {
        landLayer.resetStyle(e.target);
    }

    function showLand(p) {
        const panel = document.getElementById('landBottomPanel');
        panel.classList.add('active');

        document.getElementById('bp-name').innerText = p.nama_lahan;
        document.getElementById('bp-address').innerText = p.alamat || 'Rajabasa, Lampung Selatan';
        document.getElementById('bp-commodity').innerText = p.komoditas.charAt(0).toUpperCase() + p.komoditas.slice(1);
        document.getElementById('bp-area').innerText = parseFloat(p.luas) + ' Ha';
        
        // Fase Logic
        const faseBadge = document.getElementById('bp-fase-badge');
        faseBadge.innerHTML = `<i class="fas fa-clock"></i> FASE ${p.status_fase.toUpperCase()}`;
        let faseBgClass = 'bg-success text-success';
        if (p.status_fase === 'persiapan') faseBgClass = 'bg-warning text-warning';
        else if (p.status_fase === 'tanam') faseBgClass = 'bg-success text-success';
        else if (p.status_fase === 'pemeliharaan') faseBgClass = 'bg-primary text-primary';
        else if (p.status_fase === 'panen') faseBgClass = 'bg-warning text-warning';
        else if (p.status_fase === 'bera') faseBgClass = 'bg-secondary text-secondary';
        faseBadge.className = 'status-badge-premium bg-opacity-10 ' + faseBgClass;

        // Status Logic
        const statusBadge = document.getElementById('bp-status-badge');
        const isBencana = p.status_bencana !== 'normal';
        statusBadge.innerHTML = isBencana ? `<i class="fas fa-exclamation-triangle"></i> TERKENA BENCANA` : `<i class="fas fa-check-circle"></i> KONDISI OPTIMAL`;
        statusBadge.className = 'status-badge-premium ' + (isBencana ? 'bg-danger bg-opacity-10 text-danger' : 'bg-success bg-opacity-10 text-success');

        // Harvest Date
        const harvestDate = p.estimasi_panen ? new Date(p.estimasi_panen).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) : 'Belum Ada Data Tanam';
        document.getElementById('bp-harvest-date').innerText = harvestDate;
        
        // Update Delete Button
        const delBtn = document.getElementById('btn-delete-land');
        if (delBtn) {
            delBtn.onclick = function() {
                if (confirm('Apakah Anda yakin ingin menghapus lahan "' + p.nama_lahan + '" ini? Semua data aktivitas terkait juga akan hilang.')) {
                    window.location.href = '<?= base_url('farmer-groups/delete-land') ?>/' + p.id_lahan;
                }
            };
        }

        // Update Card as well
        document.getElementById('card-area').innerText = parseFloat(p.luas) + ' Ha';
        document.getElementById('card-harvest').innerText = p.estimasi_panen ? new Date(p.estimasi_panen).toLocaleDateString('id-ID', { day: 'numeric', month: 'short' }) : '--';
    }

    function hideCard() {
        hideBottomPanel();
    }

    function hideBottomPanel() {
        const panel = document.getElementById('landBottomPanel');
        if (panel) panel.classList.remove('active');
    }

    // Layer Switching Logic
    document.querySelectorAll('.layer-item').forEach(item => {
        item.onclick = function() {
            document.querySelectorAll('.layer-item').forEach(li => li.classList.remove('active'));
            this.classList.add('active');
            
            const layerName = this.querySelector('.layer-name').innerText;
            const legFase = document.getElementById('legend-fase');
            
            if (legFase) legFase.style.display = 'none';

            if (layerName.includes('Fase')) {
                currentMode = 'fase';
                if (legFase) legFase.style.display = 'block';
            }
            else if (layerName.includes('Heatmap')) {
                currentMode = 'heatmap';
            }

            loadMapData(); // Refresh map styling
        };
    });

    function toggleLeftPanels() {
        const containers = document.querySelectorAll('.left-panels-container, .bottom-left-panels');
        containers.forEach(container => {
            container.style.display = (container.style.display === 'none' || container.style.display === '') ? 'flex' : 'none';
        });
    }

    function locateUser() {
        if (!map) return;
        map.locate({setView: true, maxZoom: 16});
        map.once('locationfound', function(e) {
            L.marker(e.latlng).addTo(map).bindPopup("Lokasi Anda").openPopup();
        });
        map.once('locationerror', function(e) {
            alert("Gagal mendapatkan lokasi: " + e.message);
        });
    }

    function toggleBuffer() {
        const btn = document.getElementById('btnBuffer');
        const isActive = btn.classList.toggle('active');
        
        if (isActive) {
            if (bufferLayer) map.removeLayer(bufferLayer);
            
            const buffers = [];
            landLayer.eachLayer(layer => {
                try {
                    const buffered = turf.buffer(layer.feature, 0.05, { units: 'kilometers' }); // 50m buffer
                    buffers.push(buffered);
                } catch (e) { console.error(e); }
            });
            
            bufferLayer = L.geoJSON(buffers, {
                style: {
                    color: '#ef4444',
                    weight: 1,
                    dashArray: '5, 5',
                    fillColor: '#ef4444',
                    fillOpacity: 0.1
                }
            }).addTo(map);
            
            alert("Mode Buffer Aktif (Radius 50m). Garis putus-putus merah menunjukkan jangkauan irigasi/kendali.");
        } else {
            if (bufferLayer) map.removeLayer(bufferLayer);
        }
    }

    function resetView() {
        map.setView([-5.37, 105.25], 14);
    }

    // Land List Functionality
    let allLands = [];
    function toggleLandList() {
        const card = document.getElementById('landListCard');
        if (!card) return;
        
        const isActive = card.classList.toggle('active');
        if (isActive) {
            populateLandList();
            // Scroll to table if on mobile or if needed
            // document.querySelector('.table-section-premium').scrollIntoView({ behavior: 'smooth' });
        }
    }

    function hideLandList() {
        const card = document.getElementById('landListCard');
        if (card) card.classList.remove('active');
    }

    function populateLandList() {
        const content = document.getElementById('landListContent');
        if (allLands.length === 0) {
            fetch('<?= base_url('map/api-lands') ?>')
                .then(res => res.json())
                .then(data => {
                    allLands = data.features;
                    renderLandRows(allLands);
                });
        } else {
            renderLandRows(allLands);
        }
    }

    function renderLandRows(features) {
        const content = document.getElementById('landListContent');
        const tableBody = document.getElementById('landDataTableBody');
        
        if (features.length === 0) {
            content.innerHTML = '<div class="p-4 text-center text-muted small">Tidak ada lahan ditemukan.</div>';
            tableBody.innerHTML = '<tr><td colspan="7" class="text-center p-5 text-muted">Tidak ada data lahan tersedia.</td></tr>';
            return;
        }

        let listHtml = '';
        let tableHtml = '';

        features.forEach(f => {
            const p = f.properties;
            let faseColor = '#22c55e'; // Default
            if (p.status_fase === 'persiapan') faseColor = '#fbbf24';
            else if (p.status_fase === 'tanam') faseColor = '#22c55e';
            else if (p.status_fase === 'pemeliharaan') faseColor = '#3b82f6';
            else if (p.status_fase === 'panen') faseColor = '#f59e0b';
            else if (p.status_fase === 'bera') faseColor = '#94a3b8';
            
            const isBencana = p.status_bencana !== 'normal';
            
            // List Row
            listHtml += `
                <div class="land-item-row" onclick="zoomToLand('${p.id_lahan}')">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="fw-800 text-dark" style="font-size: 13px;">${p.nama_lahan}</div>
                            <div class="text-muted" style="font-size: 11px;">${p.komoditas.toUpperCase()} • ${parseFloat(p.luas)} Ha</div>
                        </div>
                        <span class="badge rounded-pill" style="background: ${faseColor}20; color: ${faseColor}; font-size: 9px; font-weight: 800;">
                            ${p.status_fase.toUpperCase()}
                        </span>
                    </div>
                </div>
            `;

            // Table Row
            tableHtml += `
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-success bg-opacity-10 p-2 rounded-3 text-success"><i class="fas fa-map-location-dot"></i></div>
                            <div>${p.nama_lahan}</div>
                        </div>
                    </td>
                    <td>
                        <span class="badge bg-primary bg-opacity-10 text-primary px-3 rounded-pill d-print-none">${p.komoditas.toUpperCase()}</span>
                        <span class="d-none d-print-block">${p.komoditas.toUpperCase()}</span>
                    </td>
                    <td>${parseFloat(p.luas)} Ha</td>
                    <td>
                        <span class="badge rounded-pill d-print-none" style="background: ${faseColor}20; color: ${faseColor}; font-size: 10px;">
                            ${p.status_fase.toUpperCase()}
                        </span>
                        <span class="d-none d-print-block">${p.status_fase.toUpperCase()}</span>
                    </td>
                    <td>
                        <span class="badge rounded-pill d-print-none ${isBencana ? 'bg-danger bg-opacity-10 text-danger' : 'bg-success bg-opacity-10 text-success'}" style="font-size: 10px;">
                            ${isBencana ? 'BENCANA' : 'OPTIMAL'}
                        </span>
                        <span class="d-none d-print-block">${isBencana ? 'BENCANA' : 'OPTIMAL'}</span>
                    </td>
                    <td class="text-end d-print-none">
                        <button class="btn btn-sm btn-light rounded-pill px-3 fw-800" onclick="zoomToLand('${p.id_lahan}')" style="font-size: 10px;">
                            <i class="fas fa-eye me-1"></i> Lihat Peta
                        </button>
                    </td>
                </tr>
            `;
        });

        content.innerHTML = listHtml;
        tableBody.innerHTML = tableHtml;
    }

    function filterDataTable(val) {
        const q = val.toLowerCase();
        const filtered = allLands.filter(f => 
            f.properties.nama_lahan.toLowerCase().includes(q) || 
            f.properties.komoditas.toLowerCase().includes(q)
        );
        renderLandRows(filtered);
    }

    function exportToCSV() {
        if (allLands.length === 0) {
            alert("Tidak ada data untuk diekspor.");
            return;
        }

        const headers = ["Nama Lahan", "Komoditas", "Luas (Ha)", "Status Fase", "Status Bencana"];
        const rows = allLands.map(f => {
            const p = f.properties;
            return [
                p.nama_lahan,
                p.komoditas,
                p.luas,
                p.status_fase,
                p.status_bencana
            ];
        });

        let csvContent = "data:text/csv;charset=utf-8," 
            + headers.join(",") + "\n"
            + rows.map(e => e.join(",")).join("\n");

        const encodedUri = encodeURI(csvContent);
        const link = document.createElement("a");
        link.setAttribute("href", encodedUri);
        link.setAttribute("download", "data_lahan_agrimap.csv");
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }

    function filterLandList() {
        const q = document.getElementById('searchLand').value.toLowerCase();
        const filtered = allLands.filter(f => f.properties.nama_lahan.toLowerCase().includes(q));
        renderLandRows(filtered);
    }

    function zoomToLand(id) {
        landLayer.eachLayer(layer => {
            if (layer.feature.properties.id_lahan == id) {
                const bounds = layer.getBounds ? layer.getBounds() : layer.getLatLng();
                if (layer.getBounds) map.fitBounds(bounds);
                else map.setView(bounds, 18);
                
                showLand(layer.feature.properties);
                if (window.innerWidth < 768) hideLandList();
                
                // Close search dropdown
                document.getElementById('search-results-dropdown').style.display = 'none';
                document.getElementById('global-search').value = '';
            }
        });
    }

    // Global Search Logic
    const globalSearch = document.getElementById('global-search');
    const searchDropdown = document.getElementById('search-results-dropdown');

    if (globalSearch) {
        globalSearch.addEventListener('input', function() {
            const q = this.value.toLowerCase().trim();
            if (q.length < 2) {
                searchDropdown.style.display = 'none';
                return;
            }

            const results = allLands.filter(f => 
                f.properties.nama_lahan.toLowerCase().includes(q) || 
                f.properties.komoditas.toLowerCase().includes(q)
            );

            if (results.length > 0) {
                let html = '';
                results.forEach(f => {
                    html += `
                        <div class="search-result-item" onclick="zoomToLand('${f.properties.id_lahan}')">
                            <div>
                                <div class="name">${f.properties.nama_lahan}</div>
                                <div class="sub">${f.properties.komoditas.toUpperCase()} • ${parseFloat(f.properties.luas)} Ha</div>
                            </div>
                            <i class="fas fa-chevron-right text-muted opacity-30 small"></i>
                        </div>
                    `;
                });
                searchDropdown.innerHTML = html;
                searchDropdown.style.display = 'block';
            } else {
                searchDropdown.innerHTML = '<div class="p-4 text-center text-muted small">Lahan tidak ditemukan.</div>';
                searchDropdown.style.display = 'block';
            }
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (!globalSearch.contains(e.target) && !searchDropdown.contains(e.target)) {
                searchDropdown.style.display = 'none';
            }
        });
    }

    // Final Initialization
    document.addEventListener('DOMContentLoaded', function() {
        // initFarmerView(); // Removed auto-open list to prevent clutter
        populateLandList(); // Ensure table and lists are populated on load
        
        // Fix for map tiles not fully loading on right side
        if (typeof ResizeObserver !== 'undefined') {
            new ResizeObserver(() => {
                if (typeof map !== 'undefined') map.invalidateSize();
            }).observe(document.getElementById('thematicMap'));
        } else {
            setTimeout(() => { if (typeof map !== 'undefined') map.invalidateSize(); }, 1000);
        }
        
        // Handle URL params if any
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('add_land') && urlParams.get('id_kelompok')) {
            showAddLand(urlParams.get('id_kelompok'));
        }
        // Handle ?edit=ID param to load existing land for editing
        if (urlParams.has('edit')) {
            const editId = parseInt(urlParams.get('edit'));
            if (editId > 0) {
                activateEditMode(editId);
            }
        }
    });

    // ─── EDIT MODE LOGIC ────────────────────────────────────────────────────
    let editLayerRef = null; // hold the editable layer so we can clean up

    async function activateEditMode(id_lahan) {
        try {
            // 1. Fetch GeoJSON for all lands to find our target
            const resp = await fetch('<?= base_url('map/api-lands') ?>');
            const fc   = await resp.json();

            const feature = fc.features.find(f => f.properties.id_lahan == id_lahan);
            if (!feature) {
                alert('Data lahan tidak ditemukan pada peta. Pastikan lahan sudah memiliki poligon.');
                return;
            }

            const props = feature.properties;

            // 2. Draw the existing polygon on the map in edit-mode color
            editLayerRef = L.geoJSON(feature.geometry, {
                style: { color: '#f59e0b', weight: 3, fillOpacity: 0.3, fillColor: '#fcd34d' }
            }).addTo(map);

            // Enable editing on the layer via Leaflet-Geoman
            editLayerRef.eachLayer(layer => {
                layer.pm.enable({ allowSelfIntersection: false });
                // Listen to edits and update the hidden GeoJSON field
                layer.on('pm:edit', (ev) => {
                    document.getElementById('map-geojson').value = JSON.stringify(ev.layer.toGeoJSON().geometry);
                });
                // Set initial GeoJSON
                document.getElementById('map-geojson').value = JSON.stringify(layer.toGeoJSON().geometry);
            });

            // Fit map to the polygon
            map.fitBounds(editLayerRef.getBounds(), { padding: [60, 60] });

            // 3. Show the Add/Edit form panel
            const addCard = document.getElementById('addLandCard');
            addCard.classList.add('active');

            // 4. Switch form to EDIT mode
            const form = document.getElementById('form-land');
            form.action = '<?= base_url('farmer-groups/update-land') ?>';

            document.getElementById('form-id-lahan').value = id_lahan;
            document.getElementById('form-land-subtitle').textContent = 'Edit Data Lahan';
            document.getElementById('form-land-title').textContent = '✏️ Perbarui Data Lahan';
            document.getElementById('btn-form-submit').textContent = '💾 Simpan Perubahan';
            document.getElementById('btn-form-submit').style.background = '#b45309'; // amber color

            // 5. Pre-fill form fields
            document.getElementById('form-nama-lahan').value = props.nama_lahan || '';
            document.getElementById('form-komoditas').value  = props.komoditas  || '';
            document.getElementById('form-alamat').value     = props.alamat     || '';
            document.getElementById('map-luas').value        = props.luas       || '';

            // Set group select
            const groupSelect = document.getElementById('form-group-id');
            if (groupSelect && groupSelect.tagName === 'SELECT') {
                groupSelect.value = props.id_kelompok || '';
            }

        } catch(err) {
            console.error('Edit mode error:', err);
            alert('Gagal memuat data lahan untuk diedit: ' + err.message);
        }
    }

    function resetFormToAddMode() {
        const form = document.getElementById('form-land');
        if (!form) return;

        form.action = '<?= base_url('farmer-groups/store-land') ?>';
        document.getElementById('form-id-lahan').value = '';
        document.getElementById('form-land-subtitle').textContent = 'Pendaftaran';
        document.getElementById('form-land-title').textContent = 'Tambah Lahan Baru';
        document.getElementById('btn-form-submit').textContent = 'Simpan Data Lahan';
        document.getElementById('btn-form-submit').style.background = '#166534';

        // Remove editable layer if in edit mode
        if (editLayerRef) {
            map.removeLayer(editLayerRef);
            editLayerRef = null;
        }

        // Clear form inputs
        ['form-nama-lahan','form-komoditas','form-alamat','map-luas','map-geojson','map-lat','map-lng','form-id-lahan'].forEach(id => {
            const el = document.getElementById(id);
            if (el) el.value = '';
        });
    }

    function toggleLeftPanels() {
        const panels = document.querySelector('.left-panels-container');
        if (panels) {
            panels.classList.toggle('active-mobile');
        }
    }
</script>
<?= $this->endSection() ?>