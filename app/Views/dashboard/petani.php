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

<!-- Weather Widget -->
<div id="weatherWidget" class="premium-card mb-4 p-4 position-relative overflow-hidden" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); border-radius: 24px; color: white; border: none; display: none; box-shadow: 0 15px 30px rgba(30, 60, 114, 0.2);">
    <!-- Decorative background elements -->
    <div style="position: absolute; right: -50px; top: -50px; width: 200px; height: 200px; border-radius: 50%; background: rgba(255,255,255,0.05); pointer-events: none;"></div>
    <div style="position: absolute; right: 100px; bottom: -80px; width: 150px; height: 150px; border-radius: 50%; background: rgba(255,255,255,0.03); pointer-events: none;"></div>

    <div class="row align-items-center position-relative z-1">
        <!-- Current Weather -->
        <div class="col-lg-5 col-md-12 mb-3 mb-lg-0">
            <div class="d-flex align-items-center gap-4">
                <div id="weatherIcon" class="d-flex justify-content-center align-items-center" style="font-size: 55px; line-height: 1; width: 90px; height: 90px; background: rgba(255,255,255,0.1); border-radius: 24px; backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.2);">⛅</div>
                <div>
                    <div class="d-flex align-items-start gap-1">
                        <span id="weatherTemp" class="fw-900" style="font-size: 48px; line-height: 0.9; letter-spacing: -2px;">--</span>
                        <span style="font-size: 20px; font-weight: 800; margin-top: 4px;">°C</span>
                    </div>
                    <div id="weatherDesc" class="fw-800 text-uppercase mt-1" style="font-size: 14px; letter-spacing: 1px; color: #a5d8ff;">Memuat...</div>
                    <div id="weatherLoc" class="opacity-75 mt-1" style="font-size: 12px; font-weight: 600;">
                        <i class="fas fa-map-marker-alt me-1 text-warning"></i> Lokasi Lahan
                    </div>
                </div>
            </div>
        </div>

        <!-- Weather Stats -->
        <div class="col-lg-3 col-md-6 mb-3 mb-lg-0">
            <div class="d-flex flex-column justify-content-center h-100 px-lg-3" style="border-left: 1px solid rgba(255,255,255,0.1);">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="small fw-700 opacity-75"><i class="fas fa-tint me-2" style="width: 15px;"></i>Kelembaban</span>
                    <span class="fw-800" id="weatherHumidity">--%</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="small fw-700 opacity-75"><i class="fas fa-wind me-2" style="width: 15px;"></i>Angin</span>
                    <span class="fw-800"><span id="weatherWind">--</span> <span style="font-size: 10px;">km/h</span></span>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="small fw-700 opacity-75"><i class="fas fa-cloud-rain me-2" style="width: 15px;"></i>Curah Hujan</span>
                    <span class="fw-800"><span id="weatherRain">--</span> <span style="font-size: 10px;">mm</span></span>
                </div>
            </div>
        </div>

        <!-- 3 Day Forecast -->
        <div class="col-lg-4 col-md-6">
            <div class="d-flex justify-content-lg-end gap-2 h-100">
                <?php for ($d = 1; $d <= 3; $d++): ?>
                <div class="forecast-day text-center p-2 rounded-4" data-day="<?= $d ?>" style="background: rgba(255,255,255,0.1); backdrop-filter: blur(5px); flex: 1; border: 1px solid rgba(255,255,255,0.1); transition: transform 0.3s;">
                    <div class="mb-2" style="font-size: 10px; font-weight: 800; text-transform: uppercase; color: #a5d8ff; letter-spacing: 1px;"><?= date('D', strtotime("+{$d} days")) ?></div>
                    <div class="forecast-icon mb-1" style="font-size: 26px;">⛅</div>
                    <div class="d-flex justify-content-center gap-1 align-items-baseline mt-2" title="Suhu Maksimum / Minimum">
                        <span class="fw-800 fc-max text-white" style="font-size: 15px;">--°</span>
                        <span class="opacity-50 mx-1" style="font-size: 10px;">/</span>
                        <span class="opacity-75 fw-600 fc-min" style="font-size: 12px; color: #a5d8ff;">--°</span>
                    </div>
                </div>
                <?php endfor; ?>
            </div>
        </div>
    </div>
    
    <!-- Agricultural Advisory -->
    <div id="weatherAdvisory" class="mt-4 pt-3 position-relative z-1" style="display: none; border-top: 1px solid rgba(255,255,255,0.1);">
        <div class="d-flex align-items-center gap-3 bg-white bg-opacity-10 rounded-pill px-4 py-2 d-inline-flex">
            <div id="advisoryIconBox" class="rounded-circle d-flex align-items-center justify-content-center" style="width: 28px; height: 28px; background: rgba(255, 215, 0, 0.2);">
                <i id="advisoryIcon" class="fas fa-lightbulb" style="color: #ffd700; font-size: 14px;"></i>
            </div>
            <span id="advisoryText" class="fw-700" style="font-size: 13px; letter-spacing: 0.3px;">Saran pertanian akan muncul di sini...</span>
        </div>
        <div class="float-end pt-2" style="font-size: 9px; opacity: 0.4; font-weight: 800; letter-spacing: 1px; text-transform: uppercase;">
            Data by Open-Meteo
        </div>
    </div>
</div>

<style>
    .forecast-day:hover {
        transform: translateY(-5px);
        background: rgba(255,255,255,0.15) !important;
    }
</style>

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
                    // DEBUG SEMENTARA - hapus setelah fix
                    // foreach ($lands as $dl) { echo "<pre style='font-size:9px'>" . $dl['nama_lahan'] . " | est: " . print_r($dl['estimasi_panen'], true) . "</pre>"; }
                    
                    $nextHarvest     = null;
                    $nextHarvestDays = null;
                    $nextYield       = null;
                    $nextSource      = '';
                    foreach ($lands as $l) {
                        $pred = $l['estimasi_panen'] ?? null;
                        if (!$pred || !is_array($pred)) continue;
                        $tgl = $pred['tanggal_panen'] ?? null;
                        if (!$tgl) continue;
                        // Prioritaskan yang paling dekat ke masa depan, atau yang paling baru lewat jika semua sudah lewat
                        if (!$nextHarvest || $tgl < $nextHarvest) {
                            $nextHarvest     = $tgl;
                            $nextHarvestDays = $pred['hari_tersisa'] ?? null;
                            $nextYield       = $pred['total_yield'] ?? null;
                            $nextSource      = $pred['source'] ?? '';
                        }
                    }
                    
                    // Jika semua lewat, tetap tampilkan yang terdekat dengan keterangan "siap panen"
                    if ($nextHarvest && $nextHarvestDays === null) {
                        $today = new DateTime();
                        $hd    = new DateTime($nextHarvest);
                        $diff  = $today->diff($hd);
                        $nextHarvestDays = ($hd >= $today) ? (int)$diff->days : -(int)$diff->days;
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
        <div class="premium-card bg-light border-0 d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
            <div class="d-flex align-items-center gap-3">
                <div id="syncIcon" class="bg-white p-3 rounded-circle shadow-sm">
                    <i class="fas fa-sync text-primary" id="syncSpinner"></i>
                </div>
                <div>
                    <h6 class="fw-800 mb-1">Status Sinkronisasi PWA</h6>
                    <p class="text-muted small mb-0" id="syncStatusText">Mengecek data offline...</p>
                </div>
            </div>
            <button onclick="manualSync()" class="btn btn-white btn-sm rounded-pill px-4 border fw-bold shadow-sm w-100 w-md-auto">Sinkronkan Sekarang</button>
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
<script>
/* ===== INLINE PWA SYNC (bypass SW cache) ===== */
const db = new Dexie("AgriMapDatabase");
db.version(1).stores({
    offline_activities: "++id, id_lahan, jenis_aktivitas, tanggal, deskripsi, latitude, longitude, foto, synced"
});

async function saveActivityOffline(data) {
    try {
        await db.offline_activities.add({ ...data, synced: 0, created_at: new Date().toISOString() });
        return true;
    } catch (e) { console.error("Dexie:", e); return false; }
}

async function syncActivities() {
    if (!navigator.onLine) return { success: false, message: "Anda masih offline.", count: 0 };

    const offlineData = await db.offline_activities.where("synced").equals(0).toArray();
    if (offlineData.length === 0) return { success: true, message: "Semua data sudah sinkron dengan server.", count: 0 };

    let successCount = 0, failCount = 0, errors = [];

    for (const item of offlineData) {
        try {
            const formData = new FormData();
            // Fields that are safe to send (skip internal IndexedDB fields only)
            const SYNC_SKIP = ['id', 'synced', 'foto', 'created_at'];
            Object.keys(item).forEach(key => {
                if (!SYNC_SKIP.includes(key) && !key.startsWith('csrf') &&
                    item[key] !== undefined && item[key] !== null && item[key] !== '') {
                    formData.append(key, item[key]);  // includes foto_base64, foto_mime, foto_name
                }
            });

            const response = await fetch('/activity/sync-offline', {
                method: 'POST',
                body: formData,
                credentials: 'same-origin'
            });

            let json = {};
            try { json = await response.json(); } catch(e) {
                json = { success: false, message: 'Respons server tidak valid (HTTP ' + response.status + ')' };
            }

            if (json.success) {
                await db.offline_activities.update(item.id, { synced: 1 });
                successCount++;
            } else {
                failCount++;
                errors.push(json.message || 'Gagal');
                console.warn("[Sync] Gagal item", item.id, json);
            }
        } catch (e) {
            failCount++;
            errors.push(e.message);
            console.error("[Sync] Error:", e);
        }
    }

    let msg = `${successCount} dari ${offlineData.length} aktivitas berhasil disinkronkan.`;
    if (failCount > 0) msg += ` ${failCount} gagal: ${errors.join('; ')}`;
    return { success: failCount === 0, message: msg, count: successCount };
}

window.addEventListener('online', async () => {
    const result = await syncActivities();
    if (result.count > 0 || !result.success) alert("📡 " + result.message);
    if (typeof updateSyncStatus === 'function') updateSyncStatus();
});
/* ===== END INLINE PWA SYNC ===== */
</script>
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

        // Show raw offline data in console for debugging
        const all = await db.offline_activities.toArray();
        console.log("[PWA Sync] All offline items:", JSON.stringify(all));
        
        const pending = await db.offline_activities.where("synced").equals(0).toArray();
        console.log("[PWA Sync] Pending items:", JSON.stringify(pending));
        
        const result = await syncActivities();
        console.log("[PWA Sync] Result:", result);
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

    // ============================================================
    // WEATHER WIDGET — Open-Meteo API (free, no key needed)
    // ============================================================
    const weatherWidget = document.getElementById('weatherWidget');
    const WMO_CODES = {
        0: ['☀️', 'Cerah Sempurna'], 1: ['🌤️', 'Sebagian Berawan'], 2: ['⛅', 'Berawan'], 3: ['☁️', 'Mendung'],
        45: ['🌫️', 'Berkabut'], 48: ['🌫️', 'Kabut Beku'],
        51: ['🌦️', 'Gerimis Ringan'], 53: ['🌦️', 'Gerimis Sedang'], 55: ['🌧️', 'Gerimis Lebat'],
        61: ['🌧️', 'Hujan Ringan'], 63: ['🌧️', 'Hujan Sedang'], 65: ['🌧️', 'Hujan Lebat'],
        71: ['🌨️', 'Salju Ringan'], 73: ['🌨️', 'Salju Sedang'], 75: ['❄️', 'Salju Lebat'],
        80: ['🌦️', 'Hujan Lebat Lokal'], 81: ['🌧️', 'Hujan Badai'], 95: ['⛈️', 'Badai Petir'], 99: ['⛈️', 'Badai Petir Besar']
    };

    function getAgriculturalAdvisory(wmo, rain, wind) {
        if ([61,63,65,80,81].includes(wmo)) return ['fas fa-cloud-rain', '⚠️ Curah hujan tinggi. Tunda pemupukan & pengendalian hama. Periksa drainase lahan!'];
        if ([95,99].includes(wmo)) return ['fas fa-bolt', '🚨 Bahaya Badai! Jangan bekerja di lahan terbuka. Amankan peralatan!'];
        if (wmo === 0 || wmo === 1) {
            if (parseFloat(wind) > 30) return ['fas fa-wind', '💨 Angin Kencang. Hindari penyemprotan pestisida karena bisa melayang.'];
            return ['fas fa-sun', '✅ Cuaca ideal untuk bekerja di lahan, tanam, & penyemprotan.'];
        }
        if ([51,53,55].includes(wmo)) return ['fas fa-tint', '🌧️ Gerimis. Waktu baik untuk penyiraman alami, tidak perlu irigasi hari ini.'];
        return ['fas fa-seedling', '🌱 Kondisi cuaca cukup baik untuk aktivitas pertanian.'];
    }

    function loadWeather(lat, lng, locationName) {
        const url = `https://api.open-meteo.com/v1/forecast?latitude=${lat}&longitude=${lng}&current=temperature_2m,relative_humidity_2m,weather_code,wind_speed_10m,precipitation&daily=weather_code,temperature_2m_max,temperature_2m_min&timezone=Asia%2FJakarta&forecast_days=4`;
        fetch(url)
            .then(r => r.json())
            .then(d => {
                const c = d.current;
                const wmo = c.weather_code;
                const [icon, desc] = WMO_CODES[wmo] || ['🌤️', 'Tidak Diketahui'];

                document.getElementById('weatherTemp').textContent = Math.round(c.temperature_2m);
                document.getElementById('weatherDesc').textContent = desc;
                document.getElementById('weatherLoc').innerHTML = `<i class="fas fa-map-marker-alt me-1"></i> ${locationName}`;
                document.getElementById('weatherIcon').textContent = icon;
                document.getElementById('weatherHumidity').textContent = c.relative_humidity_2m;
                document.getElementById('weatherWind').textContent = Math.round(c.wind_speed_10m);
                document.getElementById('weatherRain').textContent = (c.precipitation || 0).toFixed(1);

                // 3-day forecast
                const forecasts = document.querySelectorAll('.forecast-day');
                forecasts.forEach((el, i) => {
                    const dayIdx = i + 1;
                    const fWmo = d.daily.weather_code[dayIdx];
                    const [fIcon] = WMO_CODES[fWmo] || ['⛅'];
                    el.querySelector('.forecast-icon').textContent = fIcon;
                    el.querySelector('.fc-max').textContent = Math.round(d.daily.temperature_2m_max[dayIdx]) + '°';
                    el.querySelector('.fc-min').textContent = Math.round(d.daily.temperature_2m_min[dayIdx]) + '°';
                });

                // Agricultural Advisory
                const [advIcon, advText] = getAgriculturalAdvisory(wmo, c.precipitation, c.wind_speed_10m);
                document.getElementById('advisoryIcon').className = `${advIcon}`;
                document.getElementById('advisoryIcon').style.color = '#ffd700';
                document.getElementById('advisoryText').textContent = advText;
                document.getElementById('weatherAdvisory').style.display = 'block';
                
                weatherWidget.style.display = 'block';
                weatherWidget.style.animation = 'fadeInUp 0.5s ease';
            })
            .catch(() => { /* Silently fail if offline */ });
    }

    // Load weather using first land's coordinates, or fallback to geolocation
    <?php 
    $firstLat = null; $firstLng = null; $firstNama = '';
    foreach ($landsGeoJSON as $lg) {
        if (!empty($lg['latitude']) && !empty($lg['longitude'])) {
            $firstLat = $lg['latitude'];
            $firstLng = $lg['longitude'];
            $firstNama = $lg['nama_lahan'] ?? '';
            break;
        }
    }
    ?>
    <?php if ($firstLat && $firstLng): ?>
    loadWeather(<?= $firstLat ?>, <?= $firstLng ?>, '<?= esc($firstNama) ?>');
    <?php else: ?>
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(pos => {
            loadWeather(pos.coords.latitude, pos.coords.longitude, 'Lokasi Anda');
        }, () => { /* no geolocation */ });
    }
    <?php endif; ?>

</script>
<?= $this->endSection() ?>
