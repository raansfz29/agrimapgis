<?= $this->extend('layouts/premium') ?>

<?php 
    $no_padding = true; 
    $title = 'Peta Tematik';
?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    .map-full-wrapper {
        position: fixed;
        top: 80px;
        left: calc(var(--sidebar-width) + 40px);
        right: 0;
        bottom: 0;
        background: #000;
        z-index: 1;
    }

    #thematicMap {
        width: 100%;
        height: 100%;
    }

    .floating-panel {
        position: absolute;
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(15px);
        border-radius: 20px;
        padding: 22px;
        box-shadow: 0 15px 45px rgba(0,0,0,0.2);
        z-index: 1000;
        border: 1px solid rgba(255,255,255,0.4);
    }

    .layer-panel { top: 20px; left: 20px; width: 290px; }
    .legend-panel { top: 310px; left: 20px; width: 290px; }

    .panel-title {
        font-size: 11px; font-weight: 800; text-transform: uppercase;
        color: #475569; letter-spacing: 1px; margin-bottom: 18px; display: block;
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

    .detail-card { top: 20px; right: 20px; width: 320px; padding: 0; overflow: hidden; display: block; }
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
        padding: 10px 12px; border-radius: 24px; box-shadow: 0 10px 40px rgba(0,0,0,0.3);
        display: flex; align-items: center; gap: 12px; z-index: 1000; border: 1px solid rgba(255,255,255,0.5);
    }

    .btn-new-analysis {
        background: #1e3a1f; color: white; border: none; padding: 12px 24px;
        border-radius: 16px; font-weight: 800; font-size: 13px; display: flex; align-items: center; gap: 12px;
    }

    .action-circle-btn {
        width: 44px; height: 44px; border-radius: 14px; border: none; background: transparent;
        color: #475569; display: flex; align-items: center; justify-content: center;
        font-size: 20px; transition: all 0.2s;
    }

    .zoom-controls { position: absolute; bottom: 120px; right: 20px; display: flex; flex-direction: column; gap: 10px; z-index: 1000; }
    .zoom-btn {
        width: 48px; height: 48px; background: white; border-radius: 14px; border: none;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1); color: #475569; font-size: 18px;
        display: flex; align-items: center; justify-content: center;
    }

    .mockup-footer {
        position: fixed; bottom: 0; left: calc(var(--sidebar-width) + 40px); right: 0;
        height: 45px; background: #1e3a1f; color: rgba(255,255,255,0.7);
        display: flex; align-items: center; justify-content: space-between;
        padding: 0 30px; font-size: 10px; font-weight: 800; letter-spacing: 1.5px; z-index: 2000; text-transform: uppercase;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="map-full-wrapper">
    <div id="thematicMap"></div>

    <div class="floating-panel layer-panel">
        <span class="panel-title">Lapisan Peta Tematik</span>
        <div class="layer-item active"><div class="layer-label-box"><i class="fas fa-calendar-day layer-icon"></i><span class="layer-name">Fase Lahan</span></div><div class="radio-circle"></div></div>
        <div class="layer-item"><div class="layer-label-box"><i class="fas fa-tint layer-icon"></i><span class="layer-name">Soil Moisture</span></div><div class="radio-circle"></div></div>
        <div class="layer-item"><div class="layer-label-box"><i class="fas fa-cloud-rain layer-icon"></i><span class="layer-name">Rainfall Distribution</span></div><div class="radio-circle"></div></div>
        <div class="layer-item"><div class="layer-label-box"><i class="fas fa-mountain layer-icon"></i><span class="layer-name">Land Use</span></div><div class="radio-circle"></div></div>
    </div>

    <div class="floating-panel legend-panel">
        <span class="panel-title">Legend: Status Fase</span>
        <div class="legend-item"><div class="legend-color" style="background: #fbbf24;"></div><span class="legend-text">Persiapan Lahan</span></div>
        <div class="legend-item"><div class="legend-color" style="background: #22c55e;"></div><span class="legend-text">Fase Tanam</span></div>
        <div class="legend-item"><div class="legend-color" style="background: #3b82f6;"></div><span class="legend-text">Pemeliharaan</span></div>
        <div class="legend-item"><div class="legend-color" style="background: #f59e0b;"></div><span class="legend-text">Siap Panen</span></div>
        <div class="legend-item"><div class="legend-color" style="background: #94a3b8;"></div><span class="legend-text">Bera / Istirahat</span></div>
    </div>

    <div class="floating-panel detail-card" id="landCard">
        <div class="detail-head">
            <div><span class="x-small fw-800 opacity-60">Lahan #RJ-204</span><h5 class="fw-800 mb-0">Budi Santoso</h5></div>
            <i class="fas fa-times cursor-pointer opacity-50" onclick="hideCard()"></i>
        </div>
        <div class="detail-content">
            <div class="detail-stats">
                <div class="stat-box"><span class="stat-label">Luas Area</span><span class="stat-value">2.1 Ha</span></div>
                <div class="stat-box"><span class="stat-label">Komoditas</span><span class="stat-value">Padi Gogo</span></div>
            </div>
            <div class="health-status-row"><span class="text-muted small fw-800">Status Fase</span><span id="thematic-status-badge" class="badge-optimal">OPTIMAL</span></div>
            <button class="btn-brown-detail shadow-sm">Detail Analisis Lahan</button>
        </div>
    </div>

    <div class="action-bar-center">
        <button class="btn-new-analysis shadow-lg"><i class="fas fa-plus-circle"></i> Analisis Baru</button>
        <div class="vr opacity-10 mx-1" style="height: 24px;"></div>
        <button class="action-circle-btn"><i class="fas fa-layer-group"></i></button>
        <button class="action-circle-btn"><i class="fas fa-location-arrow"></i></button>
        <button class="action-circle-btn"><i class="fas fa-table-list"></i></button>
    </div>

    <div class="zoom-controls">
        <button class="zoom-btn" onclick="map.zoomIn()"><i class="fas fa-plus"></i></button>
        <button class="zoom-btn" onclick="map.zoomOut()"><i class="fas fa-minus"></i></button>
        <button class="zoom-btn"><i class="fas fa-crosshairs"></i></button>
    </div>
</div>

<div class="mockup-footer">
    <div>AGRIMAPGIS • HIGH-FIDELITY MOCKUP • V2.4</div>
    <div>RAJABASA, LAMPUNG SELATAN • SATELIT: SENTINEL-2 L2A • CLOUD COVERAGE: 2.4%</div>
    <div>MODERN AGRICULTURE • GREEN / EARTH / GOLD</div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    var map;
    document.addEventListener('DOMContentLoaded', function() {
        map = L.map('thematicMap', { zoomControl: false, attributionControl: false }).setView([-5.37, 105.25], 14);
        L.tileLayer('https://{s}.google.com/vt/lyrs=s&x={x}&y={y}&z={z}', { maxZoom: 20, subdomains:['mt0','mt1','mt2','mt3'] }).addTo(map);

        fetch('<?= base_url('map/api-lands') ?>')
            .then(res => res.json())
            .then(data => {
                L.geoJSON(data, {
                    style: { fillColor: '#22c55e', weight: 2, opacity: 1, color: 'white', fillOpacity: 0.3 },
                    onEachFeature: function(f, l) {
                        l.on('click', e => { L.DomEvent.stopPropagation(e); showLand(f.properties); });
                    }
                }).addTo(map);
            });
            
        map.on('click', () => hideCard());
    });

    function showLand(p) {
        const card = document.getElementById('landCard');
        card.style.display = 'block';
        card.querySelector('h5').innerText = p.nama_lahan || 'N/A';
        card.querySelector('.stat-value').innerText = (p.luas || '0') + ' Ha';
        card.querySelectorAll('.stat-value')[1].innerText = p.komoditas || 'N/A';
    }

    function hideCard() {
        const card = document.getElementById('landCard');
        if (card) card.style.display = 'none';
    }

    document.querySelectorAll('.layer-item').forEach(item => {
        item.onclick = function() {
            document.querySelectorAll('.layer-item').forEach(li => li.classList.remove('active'));
            this.classList.add('active');
        };
    });
</script>
<?= $this->endSection() ?>