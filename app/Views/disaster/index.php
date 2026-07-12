<?= $this->extend('layouts/premium') ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    .emergency-pulse {
        animation: pulse-red 2s infinite;
        border: 2px solid #ef4444 !important;
    }
    @keyframes pulse-red {
        0% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.4); }
        70% { box-shadow: 0 0 0 15px rgba(239, 68, 68, 0); }
        100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
    }

    .disaster-hero-card {
        background: linear-gradient(135deg, #be123c 0%, #881337 100%);
        border-radius: 30px;
        padding: 40px;
        color: white;
        position: relative;
        overflow: hidden;
        margin-bottom: 40px;
        box-shadow: 0 20px 40px rgba(159, 18, 57, 0.3);
        border: 1px solid rgba(255,255,255,0.1);
    }

    .disaster-hero-card::after {
        content: '\f071';
        font-family: 'Font Awesome 6 Free';
        font-weight: 900;
        position: absolute;
        right: -30px;
        bottom: -30px;
        font-size: 200px;
        opacity: 0.05;
        transform: rotate(-15deg);
    }

    .hero-icon-box {
        width: 65px;
        height: 65px;
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(10px);
        border-radius: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        color: white;
        border: 1px solid rgba(255,255,255,0.3);
    }

    .mitigation-step-card {
        background: white;
        border-radius: 20px;
        padding: 25px;
        border: 1px solid var(--border-color);
        height: 100%;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .mitigation-step-card:hover {
        transform: translateY(-8px);
        border-color: #be123c;
        box-shadow: 0 15px 35px rgba(159, 18, 57, 0.08);
    }

    .step-number {
        width: 45px;
        height: 45px;
        background: #fff1f2;
        color: #be123c;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 900;
        font-size: 20px;
        margin-bottom: 20px;
    }

    .land-status-item {
        border-radius: 20px;
        padding: 15px 20px;
        background: white;
        border: 1px solid var(--border-color);
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        gap: 15px;
        transition: all 0.2s;
    }
    .land-status-item:hover { border-color: var(--primary-green); background: #f8fafc; }
    .land-status-item.darurat { border-left: 6px solid #ef4444; background: #fff1f2; }
    
    #disasterMap {
        height: 550px;
        border-radius: 30px;
        border: 1px solid var(--border-color);
    }

    .btn-hero-white {
        background: white;
        color: #be123c !important;
        font-weight: 800;
        border-radius: 14px;
        padding: 12px 25px;
        border: none;
        transition: all 0.2s;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 10px;
    }
    .btn-hero-white:hover {
        background: #f8fafc;
        transform: scale(1.05);
        box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    }

    .btn-hero-outline {
        background: transparent;
        color: white !important;
        font-weight: 800;
        border-radius: 14px;
        padding: 12px 25px;
        border: 2px solid rgba(255,255,255,0.4);
        transition: all 0.2s;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 10px;
    }
    .btn-hero-outline:hover {
        background: rgba(255,255,255,0.1);
        border-color: white;
    }

    .solution-title {
        color: #be123c;
        font-weight: 900;
        font-size: 18px;
        margin-bottom: 25px;
        display: flex;
        align-items: center;
        gap: 12px;
        letter-spacing: -0.5px;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
    <div>
        <h3 class="fw-800 mb-1">Pusat Mitigasi & Krisis</h3>
        <p class="text-muted small mb-0 fw-600">Pemantauan lahan pertanian berbasis risiko wilayah Rajabasa.</p>
    </div>
    <div>
        <div class="bg-white border rounded-4 px-3 py-2 d-flex align-items-center gap-2 shadow-sm border-0">
            <div class="bg-danger bg-opacity-10 text-danger p-2 rounded-3">
                <i class="fas fa-satellite-dish"></i>
            </div>
            <div>
                <div class="text-muted" style="font-size: 9px; font-weight: 800; letter-spacing: 1px;">SISTEM MONITORING</div>
                <div class="small fw-800 text-danger"><?= count($disasterLands) ?> TITIK KRITIS</div>
            </div>
        </div>
    </div>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert border-0 rounded-4 p-4 mb-4 d-flex align-items-center gap-3" style="background:#f0fdf4;">
        <i class="fas fa-check-circle text-success fs-5"></i>
        <span class="fw-700 small text-success"><?= session()->getFlashdata('success') ?></span>
    </div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
    <div class="alert border-0 rounded-4 p-4 mb-4 d-flex align-items-center gap-3" style="background:#fff1f2;">
        <i class="fas fa-circle-xmark text-danger fs-5"></i>
        <span class="fw-700 small text-danger"><?= session()->getFlashdata('error') ?></span>
    </div>
<?php endif; ?>

<?php if (!empty($disasterLands)): ?>
    <!-- Re-styled Hero Card -->
    <div class="disaster-hero-card">
        <div class="row align-items-center">
            <div class="col-lg-9">
                <div class="d-flex align-items-center gap-4 mb-4">
                    <div class="hero-icon-box">
                        <i class="fas fa-triangle-exclamation"></i>
                    </div>
                    <div>
                        <h2 class="fw-800 mb-1" style="letter-spacing: -1px;">Respons Darurat Diperlukan</h2>
                        <div class="d-flex align-items-center gap-2 opacity-80 fw-600">
                            <i class="fas fa-clock small"></i>
                            <span>Update Terakhir: <?= esc($disasterLands[0]['nama_lahan']) ?> - Terkena Banjir</span>
                        </div>
                    </div>
                </div>
                <p class="fs-5 opacity-90 mb-4 fw-500 pe-lg-5" style="line-height: 1.6;">Sistem mendeteksi anomali kritis pada <strong><?= count($disasterLands) ?> lokasi</strong>. Segera aktifkan protokol mitigasi sesuai standar operasional untuk meminimalkan kerugian hasil panen petani.</p>
                <div class="d-flex flex-wrap gap-3 mt-4">
                    <?php if (session()->get('role') !== 'petani'): ?>
                    <form method="POST" action="<?= base_url('disaster/broadcast-alert') ?>" onsubmit="return confirmBroadcast()">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn-hero-white">
                            <i class="fas fa-paper-plane"></i> KIRIM PERINGATAN KE PETANI
                        </button>
                    </form>
                    <?php endif; ?>
                    <a href="<?= base_url('disaster/log/' . $disasterLands[0]['id_lahan']) ?>" class="btn-hero-outline">
                        <i class="fas fa-file-signature"></i> LOG KEJADIAN
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Mitigation Solutions Section -->
    <div class="mb-5">
        <div class="solution-title">
            <i class="fas fa-lightbulb"></i> PROSEDUR OPERASIONAL MITIGASI
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="mitigation-step-card">
                    <div class="step-number">01</div>
                    <h6 class="fw-800 mb-3 text-dark">Drainase Darurat</h6>
                    <p class="text-muted small mb-0 fw-500" style="line-height: 1.6;">Buat sodetan atau aktifkan pompa air mobile untuk mengeluarkan genangan air dari petakan sawah guna mencegah busuk akar.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="mitigation-step-card">
                    <div class="step-number">02</div>
                    <h6 class="fw-800 mb-3 text-dark">Proteksi Patogen</h6>
                    <p class="text-muted small mb-0 fw-500" style="line-height: 1.6;">Setelah air surut, segera semprotkan fungisida sistemik untuk menekan pertumbuhan jamur akibat kelembapan tinggi pasca-banjir.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="mitigation-step-card">
                    <div class="step-number">03</div>
                    <h6 class="fw-800 mb-3 text-dark">Nutrisi Pemulihan</h6>
                    <p class="text-muted small mb-0 fw-500" style="line-height: 1.6;">Berikan pupuk daun (foliar) untuk mempercepat pemulihan tanaman karena sistem perakaran biasanya terganggu setelah terendam.</p>
                </div>
            </div>
        </div>
    </div>
<?php else: ?>
    <!-- Safe Status -->
    <div class="alert alert-success border-0 rounded-4 p-5 text-center mb-5 shadow-sm bg-white">
        <div class="bg-success bg-opacity-10 text-success rounded-circle d-inline-flex p-4 mb-4">
            <i class="fas fa-shield-check fs-1"></i>
        </div>
        <h4 class="fw-800">Kondisi Wilayah Terkendali</h4>
        <p class="text-muted fw-500 mb-0">Tidak ada indikasi ancaman bencana pada seluruh lahan binaan saat ini.</p>
    </div>
<?php endif; ?>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="premium-card p-0 overflow-hidden border-0 shadow-sm position-relative">
            <div id="disasterMap"></div>
            <div class="position-absolute bottom-0 start-0 m-4 p-3 bg-white rounded-4 shadow-lg border-0" style="z-index: 1000; width: 220px; border: 1px solid var(--border-color) !important;">
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
                        <div class="emergency-pulse" style="background: #ef4444; width: 12px; height: 12px; border: 2px solid white; border-radius: 50%; box-shadow: 0 0 0 1px #ef4444;"></div>
                        <span class="fw-800 text-danger" style="font-size: 11px;">Status Darurat/Bencana</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="premium-card h-100 border-0 shadow-sm d-flex flex-column" style="max-height: 550px;">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h6 class="fw-800 mb-0">Daftar Status Lahan</h6>
                <span class="badge bg-light text-dark rounded-pill px-3 py-2 fw-800" style="font-size: 10px;">TOTAL: <?= count($lands) ?></span>
            </div>
            
            <div class="flex-grow-1 overflow-auto pe-2" style="max-height: 450px;">
                <?php foreach ($lands as $land): ?>
                    <div class="land-status-item <?= $land['status_bencana'] === 'darurat' ? 'darurat' : '' ?>">
                        <div class="bg-<?= $land['status_bencana'] === 'darurat' ? 'danger' : 'success' ?> bg-opacity-10 text-<?= $land['status_bencana'] === 'darurat' ? 'danger' : 'success' ?> p-2 rounded-3" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <i class="fas <?= $land['status_bencana'] === 'darurat' ? 'fa-triangle-exclamation' : 'fa-seedling' ?>"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="fw-800 mb-1 small text-dark"><?= esc($land['nama_lahan']) ?></h6>
                            <div class="d-flex gap-2 align-items-center">
                                <span class="text-muted fw-800" style="font-size: 9px; text-transform: uppercase;"><?= esc($land['komoditas']) ?></span>
                                <span class="text-muted" style="font-size: 9px;">•</span>
                                <span class="text-muted fw-800" style="font-size: 9px;"><?= esc($land['luas']) ?> HA</span>
                            </div>
                        </div>
                        <div class="col-auto d-flex flex-column gap-1 align-items-end">
                            <?php if ($land['status_bencana'] === 'darurat'): ?>
                                <a href="<?= base_url('disaster/log/' . $land['id_lahan']) ?>"
                                   class="btn btn-sm fw-800 w-100"
                                   style="background:#fff1f2; color:#e11d48; border:1px solid #fecdd3; border-radius:8px; font-size:10px; white-space:nowrap;">
                                    <i class="fas fa-circle-info me-1"></i> Detail Bencana
                                </a>
                                <form method="post" action="<?= base_url('disaster/deactivate/' . $land['id_lahan']) ?>" class="w-100">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-sm fw-800 w-100"
                                            onclick="return confirm('Selesaikan status darurat? Sistem akan mencatat log penyelesaian dan mengirim notifikasi ke seluruh petani.')"
                                            style="background:#f0fdf4; color:#16a34a; border:1px solid #bbf7d0; border-radius:8px; font-size:10px; white-space:nowrap;">
                                        <i class="fas fa-check me-1"></i> Selesaikan
                                    </button>
                                </form>
                            <?php else: ?>
                                <a href="<?= base_url('disaster/activate/' . $land['id_lahan']) ?>"
                                   class="btn btn-sm fw-800 w-100"
                                   style="background:#fff7ed; color:#ea580c; border:1px solid #fed7aa; border-radius:8px; font-size:10px; white-space:nowrap;">
                                    <i class="fas fa-triangle-exclamation me-1"></i> Laporkan
                                </a>
                                <a href="<?= base_url('land/detail/' . $land['id_lahan']) ?>"
                                   class="btn btn-sm fw-800 w-100"
                                   style="background:#f8fafc; color:#64748b; border:1px solid #e2e8f0; border-radius:8px; font-size:10px; white-space:nowrap;">
                                    <i class="fas fa-eye me-1"></i> Lihat Lahan
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<style>
    .btn-action-disaster { transition: all 0.2s; }
    .btn-action-disaster:hover { transform: scale(1.1); filter: brightness(0.9); }
    .overflow-auto::-webkit-scrollbar { width: 5px; }
    .overflow-auto::-webkit-scrollbar-track { background: transparent; }
    .overflow-auto::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
</style>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var map = L.map('disasterMap', { zoomControl: false }).setView([-5.37, 105.25], 14);
        L.tileLayer('https://{s}.google.com/vt/lyrs=s,h&x={x}&y={y}&z={z}', {
            maxZoom: 20,
            subdomains:['mt0','mt1','mt2','mt3'],
            attribution: 'Google Satellite'
        }).addTo(map);
        L.control.zoom({ position: 'topright' }).addTo(map);
        
        fetch('<?= base_url('map/api-lands') ?>?t=' + new Date().getTime())
            .then(res => res.json())
            .then(data => {
                var geojson = L.geoJSON(data, {
                    style: function(feature) {
                        var props = feature.properties;
                        var isDarurat = props.status_bencana === 'darurat';
                        var fillColor = '#22c55e';
                        
                        if (isDarurat) {
                            fillColor = '#ef4444';
                        } else {
                            var fase = props.status_fase;
                            if (fase === 'persiapan') fillColor = '#fbbf24';
                            else if (fase === 'tanam') fillColor = '#22c55e';
                            else if (fase === 'pemeliharaan') fillColor = '#3b82f6';
                            else if (fase === 'panen') fillColor = '#f59e0b';
                            else if (fase === 'bera') fillColor = '#94a3b8';
                        }

                        return {
                            fillColor: fillColor,
                            weight: 2,
                            opacity: 1,
                            color: 'white',
                            fillOpacity: 0.6,
                            className: isDarurat ? 'emergency-pulse' : ''
                        };
                    },
                    onEachFeature: function(feature, layer) {
                        layer.bindPopup('<strong>' + feature.properties.nama_lahan + '</strong><br><span style="font-size:10px;">FASE ' + feature.properties.status_fase.toUpperCase() + '</span>');
                    }
                }).addTo(map);
                if (data.features.length > 0) map.fitBounds(geojson.getBounds(), { padding: [50, 50] });
            });
    });

    function confirmBroadcast() {
        return confirm(
            '⚠️ KONFIRMASI SIARAN DARURAT\n\n' +
            'Sistem akan mengirimkan pesan peringatan darurat terstruktur ke SEMUA petani dalam kelompok tani ini.\n\n' +
            'Setiap petani juga akan menerima notifikasi darurat di panel mereka.\n\n' +
            'Lanjutkan?'
        );
    }
</script>
<?= $this->endSection() ?>