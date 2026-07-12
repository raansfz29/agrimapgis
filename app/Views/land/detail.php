<?= $this->extend('layouts/premium') ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    .detail-hero {
        background: linear-gradient(135deg, #1e3a1f 0%, #2d5a2e 100%);
        border-radius: 24px;
        padding: 30px 35px;
        color: white;
        margin-bottom: 30px;
        position: relative;
        overflow: hidden;
    }
    .detail-hero::after {
        content: '';
        position: absolute;
        right: -40px; top: -40px;
        width: 200px; height: 200px;
        border-radius: 50%;
        background: rgba(255,255,255,0.04);
        pointer-events: none;
    }
    #landMap { height: 350px; border-radius: 0 0 16px 16px; }
    .info-block { margin-bottom: 22px; }
    .info-block .label { font-size: 11px; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px; display: block; margin-bottom: 6px; }
    .info-block .value { font-size: 15px; font-weight: 700; color: #1e293b; }
    .edit-detail-modal .modal-header { background: linear-gradient(135deg, #1e3a1f 0%, #2d5a2e 100%); color: white; border-radius: 16px 16px 0 0; }
    .edit-detail-modal .modal-content { border-radius: 16px; border: none; box-shadow: 0 25px 50px rgba(0,0,0,0.15); }
    .edit-detail-modal .form-label { font-size: 11px; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px; }
    .edit-detail-modal .form-control, .edit-detail-modal .form-select { border-radius: 10px; border: 2px solid #e2e8f0; font-weight: 600; font-size: 14px; padding: 10px 14px; transition: border-color .2s; }
    .edit-detail-modal .form-control:focus, .edit-detail-modal .form-select:focus { border-color: #22c55e; box-shadow: 0 0 0 3px rgba(34,197,94,.15); }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="detail-hero">
    <div class="d-flex justify-content-between align-items-start">
        <div>
            <div style="font-size:11px; font-weight:800; opacity:0.6; letter-spacing:1px; text-transform:uppercase; margin-bottom:8px;">
                Informasi Lahan Pertanian · #<?= esc($land['id_lahan']) ?>
            </div>
            <h2 class="fw-800 mb-2" style="font-size:28px;"><?= esc($land['nama_lahan']) ?></h2>
            <div class="d-flex align-items-center gap-3 mt-2 opacity-80 small fw-700">
                <span><i class="fas fa-seedling me-1"></i><?= esc($land['komoditas']) ?></span>
                <span>·</span>
                <span><i class="fas fa-layer-group me-1"></i>Luas: <?= number_format($land['luas'], 2) ?> Ha</span>
            </div>
        </div>
        <div class="d-flex flex-column align-items-end gap-3">
            <span class="badge bg-white text-success fw-bold px-3 py-2 rounded-pill"><i class="fas fa-check-circle me-1"></i> <?= esc($land['status_fase'] ?? 'Aktif') ?></span>
            <a href="<?= base_url('land') ?>" class="btn btn-sm fw-800 rounded-3 px-3 py-2" style="background: rgba(255,255,255,0.15); color: white; border: 1px solid rgba(255,255,255,0.2);">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>
</div>

<?php if(session()->getFlashdata('success')): ?>
<div class="alert alert-success alert-dismissible fade show rounded-3 mb-3 fw-bold" role="alert">
    <i class="fas fa-check-circle me-2"></i><?= session()->getFlashdata('success') ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>
<?php if(session()->getFlashdata('error')): ?>
<div class="alert alert-danger alert-dismissible fade show rounded-3 mb-3 fw-bold" role="alert">
    <i class="fas fa-exclamation-triangle me-2"></i><?= session()->getFlashdata('error') ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="row g-4">
    <div class="col-lg-5">
        <div class="premium-card border-0 shadow-sm mb-4">
            <h6 class="fw-800 mb-4"><i class="fas fa-info-circle text-success me-2"></i>Detail Lahan</h6>
            
            <div class="info-block">
                <span class="label">Nama Lahan</span>
                <span class="value"><?= esc($land['nama_lahan']) ?></span>
            </div>
            
            <div class="info-block">
                <span class="label">Komoditas Utama</span>
                <span class="value"><?= esc(ucwords($land['komoditas'])) ?></span>
            </div>
            
            <div class="info-block">
                <span class="label">Luas Area</span>
                <span class="value"><?= number_format($land['luas'], 2) ?> Hektar</span>
            </div>
            
            <div class="info-block">
                <span class="label">Fase Lahan</span>
                <span class="value"><?= esc(ucwords(str_replace('_', ' ', $land['status_fase'] ?? 'Aktif'))) ?></span>
            </div>
            
            <div class="info-block">
                <span class="label">Alamat Lahan</span>
                <span class="value">Kec. <?= esc($land['kecamatan']) ?></span>
            </div>
            
            <div class="info-block">
                <span class="label">Koordinat Pusat</span>
                <span class="value"><?= esc($land['koordinat_tengah']) ?></span>
            </div>
            
            <div class="info-block">
                <span class="label">Kelompok Tani Pengelola</span>
                <span class="value"><?= esc($land['nama_kelompok']) ?></span>
            </div>
            
            <div class="info-block">
                <span class="label">Tanggal Registrasi</span>
                <span class="value"><?= date('d F Y', strtotime($land['created_at'])) ?></span>
            </div>

            <div class="info-block mt-4 pt-3 border-top">
                <span class="label">Aksi & Traceability</span>
                <div class="mt-2 d-flex flex-wrap gap-2">
                    <?php if(session()->get('role') !== 'petani'): ?>
                    <button type="button" class="btn btn-success btn-sm fw-bold rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#editDetailModal">
                        <i class="fas fa-pen me-1"></i> Edit Detail
                    </button>
                    <?php endif; ?>
                    <a href="<?= base_url('peta-gis') ?>?edit=<?= $land['id_lahan'] ?>" class="btn btn-outline-success btn-sm fw-bold rounded-pill px-4"><i class="fas fa-draw-polygon me-1"></i> Edit Polygon</a>
                    <a href="<?= base_url('trace/'.$land['id_lahan']) ?>" target="_blank" class="btn btn-outline-dark btn-sm fw-bold rounded-pill px-4"><i class="fas fa-qrcode me-1"></i> QR Traceability</a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-7">
        <div class="premium-card border-0 shadow-sm p-0 overflow-hidden">
            <div class="p-4 border-bottom">
                <h6 class="fw-800 mb-0"><i class="fas fa-map-marked-alt text-primary me-2"></i>Pemetaan Geografis</h6>
            </div>
            <div id="landMap"></div>
        </div>

        <!-- Activity History -->
        <div class="premium-card border-0 shadow-sm mt-4">
            <h6 class="fw-800 mb-4"><i class="fas fa-history text-warning me-2"></i>Riwayat Aktivitas Lahan</h6>
            
            <?php if(empty($activities)): ?>
                <div class="text-center p-4 bg-light rounded-3">
                    <i class="fas fa-clipboard-list fs-3 text-muted opacity-50 mb-3"></i>
                    <p class="text-muted small fw-bold mb-0">Belum ada aktivitas yang dicatat pada lahan ini.</p>
                </div>
            <?php else: ?>
                <div class="activity-timeline">
                    <?php foreach($activities as $act): ?>
                        <div class="d-flex gap-3 mb-3 pb-3 border-bottom position-relative">
                            <div class="text-center" style="width: 50px;">
                                <div class="bg-<?= $act['status'] === 'approved' ? 'success' : ($act['status'] === 'rejected' ? 'danger' : 'warning') ?> bg-opacity-10 text-<?= $act['status'] === 'approved' ? 'success' : ($act['status'] === 'rejected' ? 'danger' : 'warning') ?> rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <?php
                                        $icon = 'seedling';
                                        if($act['jenis_aktivitas'] == 'panen') $icon = 'tractor';
                                        elseif($act['jenis_aktivitas'] == 'pemupukan') $icon = 'leaf';
                                    ?>
                                    <i class="fas fa-<?= $icon ?>"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start">
                                    <h6 class="fw-bold mb-1"><?= esc(ucwords(str_replace('_', ' ', $act['jenis_aktivitas']))) ?></h6>
                                    <span class="small fw-bold text-muted"><?= date('d M Y', strtotime($act['tanggal'])) ?></span>
                                </div>
                                <p class="small text-muted mb-2"><?= esc($act['deskripsi'] ?? 'Tidak ada deskripsi') ?></p>
                                
                                <?php if($act['jenis_aktivitas'] === 'panen' && !empty($act['hasil_panen'])): ?>
                                    <div class="bg-light p-2 rounded-2 d-inline-block small fw-bold mb-2 text-success">
                                        <i class="fas fa-box-open me-1"></i> Hasil: <?= esc($act['hasil_panen']) ?> <?= esc($act['satuan']) ?>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="d-flex gap-2 align-items-center">
                                    <?php if($act['status'] === 'approved'): ?>
                                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2 py-1" style="font-size:10px;"><i class="fas fa-check me-1"></i>Disetujui</span>
                                    <?php elseif($act['status'] === 'rejected'): ?>
                                        <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-2 py-1" style="font-size:10px;"><i class="fas fa-times me-1"></i>Ditolak</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-2 py-1" style="font-size:10px;"><i class="fas fa-clock me-1"></i>Menunggu</span>
                                    <?php endif; ?>
                                    
                                    <a href="<?= base_url('activity/detail/'.$act['id_aktivitas']) ?>" class="btn btn-link text-primary p-0 m-0 small fw-bold text-decoration-none" style="font-size: 11px;">Lihat Detail <i class="fas fa-chevron-right ms-1" style="font-size: 9px;"></i></a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php if(session()->get('role') !== 'petani'): ?>
<!-- Edit Detail Modal — placed outside all rows/cols for correct Bootstrap behavior -->
<div class="modal fade edit-detail-modal" id="editDetailModal" tabindex="-1" aria-labelledby="editDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <div style="font-size:10px; font-weight:800; opacity:0.7; letter-spacing:1px; text-transform:uppercase;">Ubah Informasi</div>
                    <h5 class="modal-title fw-800 mb-0" id="editDetailModalLabel">Edit Detail Lahan</h5>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form id="editDetailForm" action="<?= base_url('land/update-detail/'.$land['id_lahan']) ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label for="edit_nama_lahan" class="form-label">Nama Lahan</label>
                        <input type="text" id="edit_nama_lahan" name="nama_lahan" class="form-control" value="<?= esc($land['nama_lahan']) ?>">
                    </div>
                    <div class="mb-3">
                        <label for="edit_komoditas" class="form-label">Komoditas Utama</label>
                        <select id="edit_komoditas" name="komoditas" class="form-select">
                            <option value="padi" <?= ($land['komoditas'] === 'padi') ? 'selected' : '' ?>>Padi</option>
                            <option value="jagung" <?= ($land['komoditas'] === 'jagung') ? 'selected' : '' ?>>Jagung</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_alamat" class="form-label">Alamat / Detail Lokasi</label>
                        <input type="text" id="edit_alamat" name="alamat" class="form-control" value="<?= esc($land['alamat'] ?? '') ?>" placeholder="Contoh: Kec. Sukarame, RT 02">
                    </div>
                    <div class="mb-4">
                        <label for="edit_luas" class="form-label">Luas Lahan (Ha) <small class="text-success fw-bold ms-1">Opsional — kosongkan jika tidak berubah</small></label>
                        <input type="text" id="edit_luas" name="luas" class="form-control" value="<?= esc($land['luas']) ?>" placeholder="Contoh: 1.5 atau 1,5">
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-success fw-bold rounded-pill px-4 flex-fill">
                            <i class="fas fa-save me-1"></i> Simpan Perubahan
                        </button>
                        <button type="button" class="btn btn-light fw-bold rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var map = L.map('landMap', { zoomControl: false }).setView([-5.3971, 105.2668], 15);
    
    L.tileLayer('https://{s}.google.com/vt/lyrs=s&x={x}&y={y}&z={z}', {
        maxZoom: 20,
        subdomains: ['mt0','mt1','mt2','mt3']
    }).addTo(map);
    
    L.control.zoom({ position: 'topright' }).addTo(map);

    var statusBencana = "<?= $land['status_bencana'] ?>";
    var statusFase = "<?= $land['status_fase'] ?>";
    
    var fillColor = '#22c55e'; // Default Hijau (Tumbuh/Tanam)
    
    if (statusBencana === 'darurat') {
        fillColor = '#ef4444'; // Merah (Bencana)
    } else {
        if (statusFase === 'persiapan') fillColor = '#94a3b8'; // Abu-abu
        else if (statusFase === 'tanam') fillColor = '#3b82f6'; // Biru
        else if (statusFase === 'tumbuh') fillColor = '#10b981'; // Hijau
        else if (statusFase === 'panen') fillColor = '#f59e0b'; // Emas
        else if (statusFase === 'bera') fillColor = '#94a3b8'; // Abu-abu
    }

    var geojsonStr = `<?= $land['geojson'] ?>`;
    if(geojsonStr) {
        try {
            var geojsonData = JSON.parse(geojsonStr);
            var landLayer = L.geoJSON(geojsonData, {
                style: {
                    color: '#ffffff', // White stroke
                    weight: 2,
                    fillColor: fillColor,
                    fillOpacity: 0.5
                }
            }).addTo(map);
            
            map.fitBounds(landLayer.getBounds(), { padding: [20, 20] });
        } catch(e) {
            console.error("Invalid GeoJSON:", e);
        }
    }
});
</script>
<?= $this->endSection() ?>
