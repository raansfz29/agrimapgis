<?= $this->extend('layouts/premium') ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #dashboardMap { background: #f8fafc; cursor: crosshair; border-radius: 20px; overflow: hidden; }
    .leaflet-control-zoom, .leaflet-control-attribution { display: none; }
    .custom-tooltip {
        background: rgba(15, 23, 42, 0.9);
        border: none;
        border-radius: 8px;
        color: white;
        font-weight: 700;
        padding: 5px 10px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    }
    .custom-tooltip::before { border-top-color: rgba(15, 23, 42, 0.9); }
    .map-container-dash { height: 500px; }
    .map-legend-dash { width: 220px; }
    @media (max-width: 991px) {
        .map-container-dash { height: 280px !important; }
        .map-legend-dash { width: auto; max-width: 160px; padding: 8px !important; margin: 8px !important; }
        .map-legend-dash .panel-title { font-size: 8px !important; margin-bottom: 8px !important; }
        .map-legend-dash span { font-size: 8px !important; }
        .map-legend-dash div[style*="width: 12px"] { width: 8px !important; height: 8px !important; }
        .kpi-cards-row .premium-card { padding: 12px !important; }
        .kpi-cards-row .x-small { font-size: 9px !important; letter-spacing: 0 !important; }
        .kpi-cards-row h3 { font-size: 1.25rem !important; }
        .kpi-cards-row .gap-3 { gap: 8px !important; }
        .kpi-cards-row .bg-opacity-10 { width: 32px !important; height: 32px !important; }
        .kpi-cards-row .bg-opacity-10 i { font-size: 14px !important; }
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
var map;
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Map with Hybrid Satellite (Same as Main Map)
    map = L.map('dashboardMap', {
        zoomControl: false,
        attributionControl: false
    }).setView([-5.37, 105.25], 14);

    L.tileLayer('https://{s}.google.com/vt/lyrs=s,h&x={x}&y={y}&z={z}', {
        maxZoom: 20,
        subdomains:['mt0','mt1','mt2','mt3']
    }).addTo(map);

    const plotLayer = L.layerGroup().addTo(map);

    // Styling Functions (Exact Copy from Main Map)
    function getFeatureColor(props) {
        if (props.status_bencana === 'darurat') return '#ef4444';
        const fase = props.status_fase;
        if (fase === 'persiapan') return '#fbbf24';
        if (fase === 'tanam') return '#22c55e';
        if (fase === 'pemeliharaan') return '#3b82f6';
        if (fase === 'panen') return '#f59e0b';
        if (fase === 'bera') return '#94a3b8';
        return '#22c55e';
    }

    const url = '<?= base_url('map/api-lands') ?>?t=' + new Date().getTime();

    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data && data.type === 'FeatureCollection') {
                const geojson = L.geoJSON(data, {
                    style: function(feature) {
                        return {
                            fillColor: getFeatureColor(feature.properties),
                            weight: 2,
                            opacity: 1,
                            color: 'white',
                            fillOpacity: 0.5
                        };
                    },
                    onEachFeature: function(feature, layer) {
                        const props = feature.properties;
                        
                        // Exact same tooltip as Main Map
                        layer.bindTooltip(`<b>${props.nama_lahan}</b><br><span style="font-size:10px">${props.status_fase.toUpperCase()}</span>`, { 
                            sticky: true, 
                            direction: 'top', 
                            className: 'custom-tooltip' 
                        });

                        const popupContent = `
                            <div class="p-2" style="min-width: 150px;">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="x-small fw-800 text-muted text-uppercase">${props.komoditas}</span>
                                    <span class="badge ${props.status_bencana === 'darurat' ? 'bg-danger' : 'bg-success'} rounded-pill" style="font-size: 8px;">${props.status_bencana.toUpperCase()}</span>
                                </div>
                                <h6 class="fw-800 mb-1">${props.nama_lahan}</h6>
                                <div class="d-flex justify-content-between x-small fw-700">
                                    <span class="text-muted">Luas:</span>
                                    <span>${parseFloat(props.luas)} Ha</span>
                                </div>
                                <hr class="my-2 opacity-10">
                                <a href="<?= base_url('land/detail') ?>/${props.id_lahan}" class="btn btn-dark btn-sm w-100 rounded-pill py-1 fw-800" style="font-size: 10px;">Detail Analisis</a>
                            </div>
                        `;
                        layer.bindPopup(popupContent);

                        // Add circle marker
                        if (props.latitude && props.longitude) {
                            const marker = L.marker([props.latitude, props.longitude], {
                                icon: L.divIcon({
                                    className: 'custom-div-icon',
                                    html: `<div style="background-color:#155724; width:12px; height:12px; border:2px solid white; border-radius:50%; box-shadow:0 0 10px rgba(0,0,0,0.3);"></div>`,
                                    iconSize: [12, 12],
                                    iconAnchor: [6, 6]
                                })
                            }).addTo(plotLayer);
                            marker.bindPopup(popupContent);
                        }
                    }
                }).addTo(plotLayer);
                
                if (data.features.length > 0) {
                    map.fitBounds(geojson.getBounds(), { padding: [40, 40], maxZoom: 17 });
                }
            }
        });
    
    // Fix map layout issues robustly
    if (typeof ResizeObserver !== 'undefined') {
        new ResizeObserver(() => {
            if (typeof map !== 'undefined') map.invalidateSize();
        }).observe(document.getElementById('dashboardMap'));
    } else {
        setTimeout(() => { if (typeof map !== 'undefined') map.invalidateSize(); }, 1000);
    }
    
    // Productivity Chart
    const ctxProd = document.getElementById('productivityChart').getContext('2d');
    new Chart(ctxProd, {
        type: 'line',
        data: {
            labels: <?= json_encode($monthlyLabels) ?>,
            datasets: [{
                label: 'Produktivitas (Ton/Ha)',
                data: <?= json_encode($monthlyProdData) ?>,
                borderColor: '#1e7e34',
                backgroundColor: 'rgba(30, 126, 52, 0.05)',
                fill: true,
                tension: 0.4,
                borderWidth: 3,
                pointRadius: 4,
                pointBackgroundColor: '#fff',
                pointBorderColor: '#1e7e34',
                pointBorderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: false, grid: { display: false } },
                x: { grid: { display: false } }
            }
        }
    });

    <?php 
        $commLabels = array_keys($summary['commodities'] ?? []);
        $commValues = array_values($summary['commodities'] ?? []);
        if (empty($commLabels)) {
            $commLabels = ['Belum Ada Data'];
            $commValues = [1];
        }
    ?>
    const ctxComm = document.getElementById('commodityChart').getContext('2d');
    new Chart(ctxComm, {
        type: 'doughnut',
        data: {
            labels: <?= json_encode($commLabels) ?>,
            datasets: [{
                data: <?= json_encode($commValues) ?>,
                backgroundColor: ['#1e7e34', '#f59e0b', '#3b82f6', '#ef4444', '#8b5cf6'],
                borderWidth: 0,
                hoverOffset: 10
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom', labels: { usePointStyle: true, font: { weight: '800', size: 11 } } }
            },
            cutout: '70%'
        }
    });

    // Initialize Print Charts (Hidden)
    const commPrint = document.getElementById('commodityChartPrint');
    if (commPrint) {
        new Chart(commPrint.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: <?= json_encode($commLabels) ?>,
                datasets: [{
                    data: <?= json_encode($commValues) ?>,
                    backgroundColor: ['#1e3a5f', '#a3d9a5', '#3b82f6', '#ef4444', '#8b5cf6'],
                    borderWidth: 1
                }]
            },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } }, animation: false }
        });
    }

    const actPrint = document.getElementById('activityChartPrint');
    if (actPrint) {
        new Chart(actPrint.getContext('2d'), {
            type: 'line',
            data: {
                labels: <?= json_encode($monthlyLabels) ?>,
                datasets: [{
                    data: <?= json_encode($monthlyProdData) ?>,
                    borderColor: '#1e3a5f',
                    backgroundColor: 'rgba(30, 58, 95, 0.05)',
                    fill: true,
                    tension: 0.1,
                    pointRadius: 4,
                    pointBackgroundColor: '#1e3a5f'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { color: '#eee' }, ticks: { font: { size: 9 } } },
                    x: { grid: { display: false }, ticks: { font: { size: 9 } } }
                },
                animation: false
            }
        });
    }

    // Layer Toggles Removed
});

function printStatistics() {
    window.print();
}
</script>

<style>
    /* Professional Statistics Report Print Styles */
    @media print {
        @page { size: A4 portrait; margin: 1cm; }
        .sidebar, .top-nav, .footer-mockup, .btn, .d-print-none, .page-title-area, .kpi-cards-row, .map-row, .timeline-card, .btn-dark, .ranking-card {
            display: none !important;
        }
        .main-content { margin: 0 !important; padding: 0 !important; width: 100% !important; }
        
        .stat-report-container { display: block !important; }
        
        /* Blue Header */
        .stat-header {
            background: #1e3a5f !important;
            color: white !important;
            padding: 20px 30px;
            display: flex !important;
            align-items: center;
            gap: 25px;
            -webkit-print-color-adjust: exact;
            border-radius: 0;
            margin-bottom: 30px;
        }
        .stat-logo-box {
            width: 70px; height: 70px;
            background: white;
            border-radius: 50%;
            display: flex !important; align-items: center; justify-content: center;
        }
        .stat-logo-box i { font-size: 35px; color: #1e3a5f; }
        .stat-header-text h4 { margin: 0; font-weight: 800; font-size: 16pt; letter-spacing: 0.5px; }
        .stat-header-text p { margin: 0; font-size: 9pt; opacity: 0.9; }

        .stat-title { text-align: center; margin-bottom: 25px; }
        .stat-title h2 { font-weight: 900; font-size: 18pt; text-transform: uppercase; margin-bottom: 5px; }

        .stat-meta { display: flex; justify-content: space-between; font-weight: 800; font-size: 10pt; margin-bottom: 15px; }

        /* Green Table */
        .stat-table { width: 100% !important; border-collapse: collapse !important; border: none !important; }
        .stat-table th {
            background-color: #a3d9a5 !important;
            color: #1e293b !important;
            padding: 12px !important;
            font-weight: 800 !important;
            text-align: left;
            -webkit-print-color-adjust: exact;
        }
        .stat-table td { padding: 12px !important; border-bottom: 1px solid #eee !important; font-size: 10pt; font-weight: 500; }
        .stat-table .rank-col { width: 60px; text-align: center; font-weight: 800; }

        /* Charts Layout */
        .stat-charts-row { display: flex !important; justify-content: space-between; margin-top: 40px; gap: 30px; }
        .stat-chart-box { width: 48%; text-align: center; }
        .stat-chart-title { font-weight: 800; font-size: 11pt; margin-bottom: 15px; text-transform: uppercase; }

        /* Signature Area */
        .stat-sig-area { display: flex !important; justify-content: space-between; margin-top: 60px; page-break-inside: avoid; }
        .sig-box { width: 30%; text-align: center; }
        .sig-name { font-weight: 800; text-decoration: underline; margin-top: 60px; font-size: 10pt; }
        .sig-nip { font-size: 9pt; margin-top: 2px; }
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<!-- Premium Emergency Alert -->
<?php if (!empty($disasterLands)): ?>
<div class="disaster-alert-premium mb-5">
    <div class="row align-items-center">
        <div class="col-lg-9">
            <div class="d-flex align-items-center gap-4">
                <div class="alert-icon-box">
                    <i class="fas fa-triangle-exclamation pulse-alert"></i>
                </div>
                <div class="text-white">
                    <h5 class="fw-800 mb-1" style="letter-spacing: -0.5px;">Darurat Bencana: <?= esc($disasterLands[0]['nama_lahan']) ?><?= !empty($disasterLands[0]['deskripsi_bencana']) ? ' (' . esc($disasterLands[0]['deskripsi_bencana']) . ')' : '' ?></h5>
                    <p class="small mb-0 opacity-80 fw-500"><?= esc($disasterLands[0]['deskripsi_bencana']) ?></p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 text-lg-end mt-3 mt-lg-0">
            <a href="<?= base_url('disaster') ?>" class="btn-alert-action">
                TINDAK LANJUT <i class="fas fa-arrow-right ms-2"></i>
            </a>
        </div>
    </div>
</div>

<style>
    .disaster-alert-premium {
        background: linear-gradient(135deg, #be123c 0%, #9f1239 100%);
        border-radius: 24px;
        padding: 25px 35px;
        box-shadow: 0 15px 35px rgba(190, 18, 60, 0.2);
        position: relative;
        overflow: hidden;
    }
    .disaster-alert-premium::after {
        content: '\f071';
        font-family: 'Font Awesome 6 Free';
        font-weight: 900;
        position: absolute;
        right: -10px;
        top: -10px;
        font-size: 100px;
        opacity: 0.05;
        transform: rotate(-15deg);
    }
    .alert-icon-box {
        width: 54px;
        height: 54px;
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(10px);
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        color: white;
        border: 1px solid rgba(255,255,255,0.2);
    }
    .btn-alert-action {
        background: white;
        color: #be123c !important;
        font-weight: 800;
        font-size: 11px;
        padding: 12px 24px;
        border-radius: 12px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        transition: all 0.2s;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .btn-alert-action:hover {
        transform: scale(1.05);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    .pulse-alert {
        animation: pulse-white 2s infinite;
    }
    @keyframes pulse-white {
        0% { opacity: 1; }
        50% { opacity: 0.5; }
        100% { opacity: 1; }
    }
</style>
<?php endif; ?>



<!-- Header Section -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-end gap-3 mb-4">
    <div>
        <h3 class="fw-800 mb-1">Dashboard Utama</h3>
        <p class="text-muted small mb-0 fw-500">Monitoring real-time aktivitas pertanian wilayah Rajabasa.</p>
    </div>
    <div class="d-flex gap-3">
        <div class="bg-white border rounded-3 px-3 py-2 d-flex align-items-center gap-3 cursor-pointer">
            <i class="far fa-calendar text-muted"></i>
            <span class="small fw-800"><?= date('M Y') ?></span>
        </div>
    </div>
</div>

<!-- KPI Cards -->
<div class="row g-3 mb-5 kpi-cards-row">
    <div class="col-6 col-sm-6 col-xl">
        <div class="premium-card p-3 p-xl-4 h-100 border-0 shadow-sm">
            <div class="d-flex align-items-center gap-3 mb-3">
                <div class="bg-success bg-opacity-10 text-success p-2 rounded-3" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-map-marked-alt"></i>
                </div>
                <span class="text-muted x-small fw-800 text-uppercase letter-spacing-1">Total Managed Area</span>
            </div>
            <h3 class="fw-800 mb-1"><?= number_format($summary['total_luas'] ?? 0, 2) ?></h3>
            <div class="d-flex align-items-center gap-2">
                <span class="text-muted small fw-600">ha</span>
                <span class="text-success x-small fw-800"><i class="fas fa-layer-group me-1"></i> <?= count($lands) ?> Plot Lahan</span>
            </div>
        </div>
    </div>
    <div class="col-6 col-sm-6 col-xl">
        <div class="premium-card p-3 p-xl-4 h-100 border-0 shadow-sm">
            <div class="d-flex align-items-center gap-3 mb-3">
                <div class="bg-warning bg-opacity-10 text-warning p-2 rounded-3" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-users"></i>
                </div>
                <span class="text-muted x-small fw-800 text-uppercase letter-spacing-1">Active Farmers</span>
            </div>
            <h3 class="fw-800 mb-1"><?= $totalFarmers ?></h3>
            <div class="d-flex align-items-center gap-2">
                <span class="text-muted small fw-600">orang</span>
                <span class="text-dark x-small fw-800"><i class="fas fa-check-circle text-success me-1"></i> Data Terpadu</span>
            </div>
        </div>
    </div>
    <div class="col-6 col-sm-6 col-xl">
        <div class="premium-card p-3 p-xl-4 h-100 border-0 shadow-sm">
            <div class="d-flex align-items-center gap-3 mb-3">
                <div class="bg-danger bg-opacity-10 text-danger p-2 rounded-3" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-chart-line"></i>
                </div>
                <span class="text-muted x-small fw-800 text-uppercase letter-spacing-1">Avg Productivity</span>
            </div>
            <h3 class="fw-800 mb-1"><?= number_format($avgProd, 2) ?></h3>
            <div class="d-flex align-items-center gap-2">
                <span class="text-muted small fw-600">ton/ha</span>
                <span class="text-muted x-small fw-800">→ Real-time Data</span>
            </div>
        </div>
    </div>
    <div class="col-6 col-sm-6 col-xl">
        <div class="premium-card p-3 p-xl-4 h-100 border-0 shadow-sm">
            <div class="d-flex align-items-center gap-3 mb-3">
                <div class="bg-primary bg-opacity-10 text-primary p-2 rounded-3" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-file-invoice"></i>
                </div>
                <span class="text-muted x-small fw-800 text-uppercase letter-spacing-1">Pending Verifications</span>
            </div>
            <h3 class="fw-800 mb-1"><?= $verificationStats['menunggu'] ?></h3>
            <div class="d-flex align-items-center gap-2">
                <span class="text-muted small fw-600">berkas</span>
                <span class="text-danger x-small fw-800"><i class="fas fa-exclamation-circle me-1"></i> Perlu perhatian</span>
            </div>
        </div>
    </div>
    <div class="col-6 col-sm-6 col-xl">
        <div class="premium-card p-3 p-xl-4 h-100 border-0 shadow-sm" style="background: linear-gradient(135deg, #fff 0%, #fefce8 100%);">
            <div class="d-flex align-items-center gap-3 mb-3">
                <div class="bg-dark text-white p-2 rounded-3" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-tractor"></i>
                </div>
                <span class="text-muted x-small fw-800 text-uppercase letter-spacing-1">Prediksi Panen</span>
            </div>
            <?php
                $upcoming = array_filter($lands, function($l) {
                    $p = $l['estimasi_panen'] ?? null;
                    if (!$p) return false;
                    return is_array($p) ? !empty($p['tanggal_panen']) : true;
                });
                usort($upcoming, function($a, $b) {
                    $ta = is_array($a['estimasi_panen']) ? $a['estimasi_panen']['tanggal_panen'] : $a['estimasi_panen'];
                    $tb = is_array($b['estimasi_panen']) ? $b['estimasi_panen']['tanggal_panen'] : $b['estimasi_panen'];
                    return strtotime($ta) <=> strtotime($tb);
                });
                $first     = !empty($upcoming) ? array_values($upcoming)[0] : null;
                $firstDate = null;
                if ($first) {
                    $p = $first['estimasi_panen'];
                    $firstDate = is_array($p) ? ($p['tanggal_panen'] ?? null) : $p;
                }
            ?>
            <h3 class="fw-800 mb-1"><?= $firstDate ? date('d', strtotime($firstDate)) : '--' ?></h3>
            <div class="d-flex align-items-center gap-2">
                <span class="text-muted small fw-600"><?= $firstDate ? date('M Y', strtotime($firstDate)) : 'Belum Ada Data' ?></span>
                <span class="text-dark x-small fw-800"><i class="far fa-calendar-check me-1"></i> <?= count($upcoming) ?> Plot Utama</span>
            </div>

        </div>
    </div>
</div>

<!-- Main Section: Map & Activity -->
<div class="row g-4 mb-5">
    <div class="col-md-8">
        <div class="premium-card p-0 overflow-hidden border-0 shadow-sm position-relative map-container-dash">
            <!-- Real Leaflet Map Container -->
            <div id="dashboardMap" style="width: 100%; height: 100%; z-index: 1; background-color: #f8fafc !important;"></div>
            
            <!-- Map Layers Filter Removed -->

            <!-- Map Controls (Same Style as Main Map) -->
            <div class="position-absolute bottom-0 end-0 m-4 d-flex flex-column gap-2" style="z-index: 1000; bottom: 80px !important;">
                <button onclick="map.zoomIn()" class="btn btn-white p-0 rounded-3 border shadow-sm d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background: white;"><i class="fas fa-plus"></i></button>
                <button onclick="map.zoomOut()" class="btn btn-white p-0 rounded-3 border shadow-sm d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background: white;"><i class="fas fa-minus"></i></button>
                <button onclick="locateUser(map)" class="btn btn-white p-0 rounded-3 border shadow-sm d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background: white;"><i class="fas fa-crosshairs"></i></button>
            </div>

            <!-- Map Legend (Exact Copy from Main Map) -->
            <div class="position-absolute top-0 start-0 m-4 p-3 bg-white rounded-4 shadow-lg border map-legend-dash" style="z-index: 1000; background: rgba(255,255,255,0.92); backdrop-filter: blur(10px);">
                <span class="panel-title" style="font-size: 10px; font-weight: 800; text-transform: uppercase; color: #64748b; letter-spacing: 1px; margin-bottom: 15px; display: block;">Legenda Status Fase</span>
                <div class="d-flex flex-column gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <div style="background: #fbbf24; width: 12px; height: 12px; border: 2px solid white; border-radius: 50%; box-shadow: 0 0 0 1px #fbbf24;"></div>
                        <span class="fw-800 text-dark" style="font-size: 11px;">Persiapan Lahan</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <div style="background: #22c55e; width: 12px; height: 12px; border: 2px solid white; border-radius: 50%; box-shadow: 0 0 0 1px #22c55e;"></div>
                        <span class="fw-800 text-dark" style="font-size: 11px;">Fase Tanam</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <div style="background: #3b82f6; width: 12px; height: 12px; border: 2px solid white; border-radius: 50%; box-shadow: 0 0 0 1px #3b82f6;"></div>
                        <span class="fw-800 text-dark" style="font-size: 11px;">Pemeliharaan</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <div style="background: #f59e0b; width: 12px; height: 12px; border: 2px solid white; border-radius: 50%; box-shadow: 0 0 0 1px #f59e0b;"></div>
                        <span class="fw-800 text-dark" style="font-size: 11px;">Siap Panen</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <div style="background: #94a3b8; width: 12px; height: 12px; border: 2px solid white; border-radius: 50%; box-shadow: 0 0 0 1px #94a3b8;"></div>
                        <span class="fw-800 text-dark" style="font-size: 11px;">Lahan Bera</span>
                    </div>
                    <div class="d-flex align-items-center gap-2 mt-1 pt-2 border-top">
                        <div style="background: #ef4444; width: 12px; height: 12px; border: 2px solid white; border-radius: 50%; box-shadow: 0 0 0 1px #ef4444;"></div>
                        <span class="fw-800 text-danger" style="font-size: 11px;">Status Darurat/Bencana</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="premium-card h-100 border-0 shadow-sm d-flex flex-column">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-800 mb-0">Aktivitas Terbaru</h6>
                <a href="<?= base_url('activity/verification') ?>" class="text-primary x-small fw-800 text-decoration-none">LIHAT SEMUA</a>
            </div>

            <div class="activity-timeline">
                <?php if (empty($recentActivities)): ?>
                    <div class="text-center py-4">
                        <i class="fas fa-history text-muted fs-1 mb-3 opacity-20"></i>
                        <p class="text-muted small">Belum ada aktivitas tercatat.</p>
                    </div>
                <?php else: ?>
                    <?php $displayActivities = array_slice($recentActivities, 0, 5); ?>
                    <ul class="list-unstyled mb-0">
                        <?php foreach ($displayActivities as $index => $activity): ?>
                            <?php
                                $statusColor = 'primary';
                                if ($activity['status'] === 'approved') $statusColor = 'success';
                                if ($activity['status'] === 'rejected') $statusColor = 'danger';

                                $timeDiff = time() - strtotime($activity['created_at']);
                                if ($timeDiff < 60) $timeStr = 'Baru saja';
                                elseif ($timeDiff < 3600) $timeStr = floor($timeDiff/60) . ' menit lalu';
                                elseif ($timeDiff < 86400) $timeStr = floor($timeDiff/3600) . ' jam lalu';
                                else $timeStr = floor($timeDiff/86400) . ' hari lalu';
                            ?>
                            <li class="mb-3 position-relative" style="padding-left: 30px;">
                                <?php if ($index < count($displayActivities) - 1): ?>
                                    <div class="position-absolute border-start border-2 opacity-10" style="left:6px; top:20px; bottom:-12px; z-index:1;"></div>
                                <?php endif; ?>
                                <div class="position-absolute rounded-circle bg-<?= $statusColor ?>" style="width:12px; height:12px; left:0; top:4px; z-index:2; border:2px solid white;"></div>
                                <div class="mb-1 lh-sm">
                                    <span class="fw-800 small text-dark"><?= esc($activity['nama_petani']) ?></span>
                                    <span class="text-muted small"> <?= esc($activity['jenis_aktivitas']) ?> · </span>
                                    <span class="fw-700 small"><?= esc($activity['nama_lahan']) ?></span>
                                </div>
                                <div class="d-flex align-items-center gap-2 x-small text-muted fw-700">
                                    <span><?= $timeStr ?></span>
                                    <span>•</span>
                                    <span class="text-<?= $statusColor ?>"><?= ucfirst($activity['status']) ?></span>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <?php if (count($recentActivities) > 5): ?>
                    <a href="<?= base_url('activity/verification') ?>"
                       class="d-flex align-items-center justify-content-center gap-2 text-decoration-none mt-1 py-2 rounded-3 fw-800"
                       style="font-size:11px; color:#475569; background:#f8fafc; border:1px solid #e2e8f0;">
                        <i class="fas fa-list-ul text-success" style="font-size:10px;"></i>
                        Lihat <?= count($recentActivities) - 5 ?> aktivitas lainnya
                        <i class="fas fa-arrow-right" style="font-size:10px;"></i>
                    </a>
                    <?php endif; ?>
                <?php endif; ?>
            </div>

            <a href="<?= base_url('peta-gis') ?>" class="btn btn-dark w-100 rounded-4 py-3 mt-auto fw-800 d-flex align-items-center justify-content-center gap-3 shadow-lg">
                <div class="bg-white rounded-circle d-flex align-items-center justify-content-center" style="width:24px; height:24px;">
                    <i class="fas fa-plus text-dark small"></i>
                </div>
                TAMBAH PLOT BARU
            </a>
        </div>
    </div>

</div>

<!-- Bottom Charts Section -->
<div class="row g-4 mb-5">
    <div class="col-md-8">
        <div class="premium-card border-0 shadow-sm">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h6 class="fw-800 mb-0">Tren Produktivitas Bulanan</h6>
                <span class="text-muted x-small fw-800">WILAYAH RAJABASA</span>
            </div>
            <div style="height: 300px;">
                <canvas id="productivityChart"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="premium-card border-0 shadow-sm">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h6 class="fw-800 mb-0">Komposisi Lahan</h6>
                <i class="fas fa-chart-pie text-muted"></i>
            </div>
            <div style="height: 300px;">
                <canvas id="commodityChart"></canvas>
            </div>
        </div>
    </div>
</div>

<style>
    .trend-toggle-wrapper {
        border: 1px solid #f1f5f9;
    }
    .btn-trend-toggle {
        border: none;
        background: transparent;
        font-size: 11px;
        font-weight: 800;
        padding: 6px 20px;
        border-radius: 50px;
        color: #64748b;
        transition: all 0.2s;
    }
    .btn-trend-toggle.active {
        background: white;
        color: #1e293b;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }
    .btn-trend-toggle:hover:not(.active) {
        color: #1e293b;
    }
</style>

<script>
function locateUser(mapObj) {
    if (!mapObj) return;
    mapObj.locate({setView: true, maxZoom: 16});
    mapObj.once('locationfound', function(e) {
        L.marker(e.latlng).addTo(mapObj).bindPopup("Lokasi Anda").openPopup();
    });
    mapObj.once('locationerror', function(e) {
        alert("Gagal mendapatkan lokasi: " + e.message + "\n\nPastikan GPS aktif dan browser diizinkan mengakses lokasi.");
    });
}
</script>

<!-- Ranking Section -->
<div class="premium-card border-0 shadow-sm p-0 overflow-hidden">
    <div class="p-4 d-flex justify-content-between align-items-center border-bottom">
        <h6 class="fw-800 mb-0">Kelompok Tani Terbaik Berdasarkan Produktivitas</h6>
        <span class="badge bg-dark rounded-pill px-3 py-2 x-small fw-800">Top 5 Wilayah</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
                <tr class="x-small fw-800 text-muted text-uppercase letter-spacing-1">
                    <th class="ps-4 py-3 border-0">Rank</th>
                    <th class="py-3 border-0">Nama Kelompok Tani</th>
                    <th class="py-3 border-0">Desa</th>
                    <th class="py-3 border-0">Total Luas</th>
                    <th class="py-3 border-0">Produktivitas</th>
                    <th class="py-3 border-0">Tren</th>
                </tr>
            </thead>
            <tbody class="fw-700">
                <?php foreach ($leaderboard as $index => $group): ?>
                <tr>
                    <td class="ps-4 fw-800 fs-5 <?= $index > 2 ? 'text-muted' : '' ?>"><?= $index + 1 ?></td>
                    <td><?= esc($group['nama']) ?></td>
                    <td class="text-muted small"><?= esc($group['kecamatan']) ?></td>
                    <td class="text-muted small"><?= number_format($group['total_luas'], 1) ?> ha</td>
                    <td class="fs-6 fw-800 text-dark"><?= number_format($group['prod'], 2) ?> <span class="text-muted x-small fw-700">ton/ha</span></td>
                    <td>
                        <?php if ($group['prod'] > 0 && $group['trend'] !== 0.0): ?>
                            <?php 
                                $isPositive = $group['trend'] > 0;
                                $trendColor = $isPositive ? 'text-success' : 'text-danger';
                                $trendIcon = $isPositive ? 'fa-arrow-up' : 'fa-arrow-down';
                            ?>
                            <span class="<?= $trendColor ?> small fw-700">
                                <i class="fas <?= $trendIcon ?> me-1"></i> <?= number_format(abs($group['trend']), 1) ?>%
                            </span>
                        <?php else: ?>
                            <span class="text-muted small">-</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($leaderboard)): ?>
                <tr>
                    <td colspan="6" class="text-center py-4 text-muted small">Belum ada data kelompok tani.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>