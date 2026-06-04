<?= $this->extend('layouts/premium') ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #miniMap { width: 100%; height: 100%; z-index: 1; }
    .premium-card { transition: transform 0.2s; }
    .premium-card:hover { transform: translateY(-5px); }
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
                    <h5 class="fw-800 mb-1" style="letter-spacing: -0.5px;">Darurat Bencana: <?= esc($disasterLands[0]['nama_lahan']) ?></h5>
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

<!-- Quick Stats for Petani -->
<div class="row g-4 mb-4" style="align-items: stretch;">
    <div class="col-md-4">
        <div class="premium-card d-flex justify-content-between align-items-start h-100" style="min-height:130px;">
            <div>
                <span class="text-muted small fw-bold text-uppercase">Lahan Kelompok</span>
                <div class="mt-2">
                    <span class="h2 fw-800 mb-0"><?= count($lands) ?></span>
                    <span class="text-muted small ms-1">plot</span>
                </div>
                <span class="text-success small fw-bold d-block mt-2">Terdaftar di GIS</span>
            </div>
            <div class="bg-success bg-opacity-10 p-3 rounded-3"><i class="fas fa-seedling text-success"></i></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="premium-card d-flex justify-content-between align-items-start h-100" style="min-height:130px;">
            <div>
                <span class="text-muted small fw-bold text-uppercase">Aktivitas Pending</span>
                <div class="mt-2">
                    <span class="h2 fw-800 mb-0"><?= $verificationStats['menunggu'] ?></span>
                    <span class="text-muted small ms-1">menunggu</span>
                </div>
                <span class="text-warning small fw-bold d-block mt-2">Menunggu validasi PPL</span>
            </div>
            <div class="bg-warning bg-opacity-10 p-3 rounded-3"><i class="fas fa-hourglass-half text-warning"></i></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="premium-card d-flex justify-content-between align-items-start h-100" style="min-height:130px;">
            <div>
                <span class="text-muted small fw-bold text-uppercase">Panen Terdekat</span>
                <?php
                    $nextHarvest     = null;
                    $nextHarvestDays = null;
                    $nextYield       = null;
                    $nextSource      = '';
                    foreach ($lands as $l) {
                        $pred = $l['estimasi_panen'] ?? null;
                        if (!$pred) continue;
                        $tgl = is_array($pred) ? ($pred['tanggal_panen'] ?? null) : $pred;
                        if (!$tgl) continue;
                        if (!$nextHarvest || $tgl < $nextHarvest) {
                            $nextHarvest     = $tgl;
                            $nextHarvestDays = is_array($pred) ? ($pred['hari_tersisa'] ?? null) : null;
                            $nextYield       = is_array($pred) ? ($pred['total_yield'] ?? null) : null;
                            $nextSource      = is_array($pred) ? ($pred['source'] ?? '') : '';
                        }
                    }
                ?>
                <div class="mt-2">
                    <span class="h2 fw-800 mb-0"><?= $nextHarvest ? date('d M', strtotime($nextHarvest)) : '--' ?></span>
                    <span class="text-muted small ms-1"><?= $nextHarvest ? date('Y', strtotime($nextHarvest)) : '' ?></span>
                </div>
                <!-- Compact info row -->
                <div class="d-flex align-items-center gap-2 mt-2 flex-wrap" style="font-size:11px; font-weight:700;">
                    <?php if ($nextHarvestDays !== null): ?>
                        <?php if ($nextHarvestDays > 0): ?>
                            <span class="text-warning"><i class="fas fa-clock me-1"></i><?= $nextHarvestDays ?> hari lagi</span>
                        <?php elseif ($nextHarvestDays == 0): ?>
                            <span class="text-success"><i class="fas fa-star me-1"></i>Hari ini!</span>
                        <?php else: ?>
                            <span class="text-danger"><i class="fas fa-exclamation-circle me-1"></i>Terlambat <?= abs($nextHarvestDays) ?> hari</span>
                        <?php endif; ?>
                        <?php if ($nextYield): ?>
                            <span class="text-muted">· <?= number_format($nextYield, 1) ?> ton</span>
                        <?php endif; ?>
                    <?php else: ?>
                        <span class="text-muted fw-bold small">
                            <?= $nextHarvest ? ($nextSource === 'status_fase' ? 'Berdasarkan fase lahan' : 'Berdasarkan data tanam') : 'Belum ada data tanam' ?>
                        </span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="bg-primary bg-opacity-10 p-3 rounded-3"><i class="fas fa-chart-line text-primary"></i></div>
        </div>
    </div>
</div>


<div class="row g-4 mb-4">
    <!-- Map Quick View -->
    <div class="col-md-8">
        <div class="premium-card p-0 overflow-hidden border-0 shadow-sm position-relative" style="height: 500px;">
            <!-- Real Leaflet Map Container -->
            <div id="miniMap" style="width: 100%; height: 100%; z-index: 1;"></div>
            
            <!-- Map Controls (Same Style as Main Map) -->
            <div class="position-absolute bottom-0 end-0 m-4 d-flex flex-column gap-2" style="z-index: 1000; bottom: 80px !important;">
                <button onclick="miniMap.zoomIn()" class="btn btn-white p-0 rounded-3 border shadow-sm d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background: white;"><i class="fas fa-plus"></i></button>
                <button onclick="miniMap.zoomOut()" class="btn btn-white p-0 rounded-3 border shadow-sm d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background: white;"><i class="fas fa-minus"></i></button>
                <button onclick="locateUser(miniMap)" class="btn btn-white p-0 rounded-3 border shadow-sm d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background: white;"><i class="fas fa-crosshairs"></i></button>
            </div>

            <!-- Map Legend (Exact Copy from Main Map) -->
            <div class="position-absolute top-0 start-0 m-4 p-3 bg-white rounded-4 shadow-lg border" style="z-index: 1000; width: 220px; background: rgba(255,255,255,0.92); backdrop-filter: blur(10px);">
                <span class="panel-title" style="font-size: 10px; font-weight: 800; text-transform: uppercase; color: #64748b; letter-spacing: 1px; margin-bottom: 15px; display: block;">Legenda Status Lahan</span>
                <div class="d-flex flex-column gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <div style="background: #22c55e; width: 12px; height: 12px; border: 2px solid white; border-radius: 50%; box-shadow: 0 0 0 1px #22c55e;"></div>
                        <span class="fw-800 text-dark" style="font-size: 11px;">Lahan Kelompok (Aktif)</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <div style="background: #f59e0b; width: 12px; height: 12px; border: 2px solid white; border-radius: 50%; box-shadow: 0 0 0 1px #f59e0b;"></div>
                        <span class="fw-800 text-dark" style="font-size: 11px;">Persiapan / Bera</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <div style="background: #ef4444; width: 12px; height: 12px; border: 2px solid white; border-radius: 50%; box-shadow: 0 0 0 1px #ef4444;"></div>
                        <span class="fw-800 text-dark" style="font-size: 11px;">Terkena Bencana</span>
                    </div>
                </div>
            </div>
            
            <!-- Floating Map Actions -->
            <div class="position-absolute top-0 end-0 m-4" style="z-index: 1000;">
                <a href="<?= base_url('peta-gis') ?>" class="btn btn-dark btn-sm rounded-pill px-4 fw-800 py-2 shadow-lg"><i class="fas fa-expand me-2"></i> Buka Peta Full</a>
            </div>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="col-md-4">
        <div class="premium-card h-100 bg-success bg-opacity-10 border-0 text-center d-flex flex-column justify-content-center p-4">
            <div class="bg-white w-50 h-50 rounded-circle mx-auto mb-4 shadow-sm d-flex align-items-center justify-content-center" style="width: 80px !important; height: 80px !important;">
                <i class="fas fa-plus text-success fs-2"></i>
            </div>
            <h5 class="fw-800">Input Aktivitas</h5>
            <p class="text-muted small mb-4">Laporkan kegiatan tanam, pemupukan, atau panen Anda hari ini.</p>
            <a href="<?= base_url('activity/input') ?>" class="btn btn-success fw-bold rounded-pill py-2 shadow-sm">Mulai Lapor Sekarang</a>
        </div>
    </div>
</div>

<!-- Sync Status Panel -->
<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="premium-card bg-light border-0 d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-3">
                <div id="syncIcon" class="bg-white p-3 rounded-circle shadow-sm">
                    <i class="fas fa-sync text-primary" id="syncSpinner"></i>
                </div>
                <div>
                    <h6 class="fw-800 mb-1">Status Sinkronisasi PWA</h6>
                    <p class="text-muted small mb-0" id="syncStatusText">Mengecek data offline...</p>
                </div>
            </div>
            <button onclick="manualSync()" class="btn btn-white btn-sm rounded-pill px-4 border fw-bold shadow-sm">Sinkronkan Sekarang</button>
        </div>
    </div>
</div>

<!-- My Recent Activities -->
<div class="premium-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h6 class="fw-800 mb-0">Riwayat Aktivitas Terakhir</h6>
        <a href="<?= base_url('activity') ?>" class="text-success fw-bold small text-decoration-none">Lihat Semua <i class="fas fa-chevron-right ms-1"></i></a>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
                <tr>
                    <th class="border-0 small fw-800 text-muted">AKTIVITAS</th>
                    <th class="border-0 small fw-800 text-muted">PETANI</th>
                    <th class="border-0 small fw-800 text-muted">LAHAN</th>
                    <th class="border-0 small fw-800 text-muted">TANGGAL</th>
                    <th class="border-0 small fw-800 text-muted text-center">STATUS</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($allActivities)): ?>
                    <tr><td colspan="5" class="text-center py-5 text-muted small">Belum ada aktivitas yang dilaporkan.</td></tr>
                <?php else: ?>
                    <?php foreach (array_slice($allActivities, 0, 5) as $act): ?>
                        <tr>
                            <td><span class="fw-bold"><?= esc($act['jenis_aktivitas']) ?></span></td>
                            <td><span class="text-dark fw-bold" style="font-size: 13px;"><?= esc($act['nama_petani'] ?? 'Anggota') ?></span></td>
                            <td><span class="text-muted small fw-bold">#<?= esc($act['nama_lahan'] ?? 'Lahan') ?></span></td>
                            <td><span class="text-muted small"><?= date('d M Y', strtotime($act['tanggal'])) ?></span></td>
                            <td class="text-center">
                                <?php if($act['status'] == 'approved'): ?>
                                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3">Disetujui</span>
                                <?php elseif($act['status'] == 'rejected'): ?>
                                    <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3">Ditolak</span>
                                <?php else: ?>
                                    <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-3">Pending</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://unpkg.com/dexie/dist/dexie.js"></script>
<script src="<?= base_url('js/pwa-sync.js') ?>"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
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

    async function updateSyncStatus() {
        const offlineCount = await db.offline_activities.where("synced").equals(0).count();
        const statusText = document.getElementById('syncStatusText');
        const spinner = document.getElementById('syncSpinner');

        if (offlineCount > 0) {
            statusText.innerText = `Ada ${offlineCount} data aktivitas tersimpan secara offline.`;
            statusText.classList.add('text-warning');
            spinner.classList.add('fa-spin');
        } else {
            statusText.innerText = "Semua data telah sinkron dengan server.";
            statusText.classList.remove('text-warning');
            spinner.classList.remove('fa-spin');
        }
    }

    async function manualSync() {
        const btn = event.target;
        btn.disabled = true;
        btn.innerText = "Mensinkronkan...";
        
        const result = await syncActivities();
        alert(result.message);
        
        btn.disabled = false;
        btn.innerText = "Sinkronkan Sekarang";
        updateSyncStatus();
    }

    // Initial check
    updateSyncStatus();

    // Leaflet Mini Map Initialization (Perfectly Synced with Main Map and Admin Dashboard)
    const landsData = <?= json_encode($landsGeoJSON) ?>;
    const miniMap = L.map('miniMap', { zoomControl: false }).setView([-5.37, 105.25], 14);
    
    L.tileLayer('https://{s}.google.com/vt/lyrs=s,h&x={x}&y={y}&z={z}', {
        maxZoom: 20,
        subdomains:['mt0','mt1','mt2','mt3']
    }).addTo(miniMap);

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

    if (landsData.length > 0) {
        const layers = [];
        landsData.forEach(land => {
            const geojson = JSON.parse(land.geojson);
            
            const layer = L.geoJSON(geojson, {
                style: function(feature) {
                    return {
                        fillColor: getFeatureColor(land),
                        weight: 2,
                        opacity: 1,
                        color: 'white',
                        fillOpacity: 0.5
                    };
                }
            }).addTo(miniMap);
            
            // Exact same hover tooltip as main map
            layer.bindTooltip(`<b>${land.nama_lahan}</b><br><span style="font-size:10px">${land.status_fase.toUpperCase()}</span>`, { 
                sticky: true, 
                direction: 'top', 
                className: 'custom-tooltip' 
            });

            // Exact same premium popup
            const popupContent = `
                <div class="p-2" style="min-width: 150px;">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="x-small fw-800 text-muted text-uppercase">${land.komoditas}</span>
                        <span class="badge ${land.status_bencana === 'darurat' ? 'bg-danger' : 'bg-success'} rounded-pill" style="font-size: 8px;">${land.status_bencana.toUpperCase()}</span>
                    </div>
                    <h6 class="fw-800 mb-1">${land.nama_lahan}</h6>
                    <div class="d-flex justify-content-between x-small fw-700">
                        <span class="text-muted">Luas:</span>
                        <span>${land.luas} Ha</span>
                    </div>
                    <hr class="my-2 opacity-10">
                    <a href="<?= base_url('land/detail') ?>/${land.id_lahan}" class="btn btn-dark btn-sm w-100 rounded-pill py-1 fw-800" style="font-size: 10px;">Detail Analisis</a>
                </div>
            `;
            layer.bindPopup(popupContent);
            layers.push(layer);

            // Add professional circle marker at centroid
            if (land.latitude && land.longitude) {
                const marker = L.marker([land.latitude, land.longitude], {
                    icon: L.divIcon({
                        className: 'custom-div-icon',
                        html: `<div style="background-color:#155724; width:12px; height:12px; border:2px solid white; border-radius:50%; box-shadow:0 0 10px rgba(0,0,0,0.3);"></div>`,
                        iconSize: [12, 12],
                        iconAnchor: [6, 6]
                    })
                }).addTo(miniMap);
                marker.bindPopup(popupContent);
            }
        });

        const group = new L.featureGroup(layers);
        miniMap.fitBounds(group.getBounds().pad(0.5));
    }

    // Fix map layout issues on load
    setTimeout(() => {
        miniMap.invalidateSize();
    }, 800);
</script>
<?= $this->endSection() ?>
