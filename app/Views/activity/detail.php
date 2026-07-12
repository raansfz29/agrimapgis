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
    .info-block { margin-bottom: 22px; }
    .info-block .label {
        font-size: 10px; font-weight: 800;
        color: #94a3b8; text-transform: uppercase;
        letter-spacing: 1px; display: block; margin-bottom: 6px;
    }
    .info-block .value { font-size: 15px; font-weight: 700; color: #1e293b; }
    .status-pill {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 6px 16px; border-radius: 100px;
        font-size: 12px; font-weight: 800;
    }
    .status-approved { background: #dcfce7; color: #166534; }
    .status-pending  { background: #fef9c3; color: #854d0e; }
    .status-rejected { background: #fee2e2; color: #991b1b; }

    .action-btn {
        border: none; border-radius: 14px;
        padding: 14px 24px; font-weight: 800;
        font-size: 14px; cursor: pointer;
        transition: all 0.2s; width: 100%;
        display: flex; align-items: center; justify-content: center; gap: 8px;
    }
    .action-approve { background: #16a34a; color: white; }
    .action-approve:hover { background: #15803d; transform: translateY(-1px); }
    .action-reject  { background: #fee2e2; color: #991b1b; }
    .action-reject:hover  { background: #fecaca; transform: translateY(-1px); }
    #activityMap { height: 260px; border-radius: 0 0 16px 16px; }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Hero Header -->
<div class="detail-hero">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start gap-4">
        <div>
            <div style="font-size:11px; font-weight:800; opacity:0.6; letter-spacing:1px; text-transform:uppercase; margin-bottom:8px;">
                Detail Aktivitas · #<?= $activity['id_aktivitas'] ?>
            </div>
            <h2 class="fw-800 mb-2" style="font-size:28px; word-break: break-word;"><?= esc(ucwords($activity['jenis_aktivitas'])) ?></h2>
            <div class="d-flex flex-wrap align-items-center gap-3 mt-2 opacity-80 small fw-700">
                <span><i class="far fa-calendar me-1"></i><?= date('d F Y', strtotime($activity['tanggal'])) ?></span>
                <span class="d-none d-sm-inline">·</span>
                <span><i class="fas fa-map-marked-alt me-1"></i><?= esc($land['nama_lahan'] ?? 'N/A') ?></span>
            </div>
        </div>
        <div class="d-flex flex-row flex-md-column align-items-center align-items-md-end flex-wrap gap-2 gap-md-3">
            <?php if ($activity['status'] === 'approved'): ?>
                <span class="status-pill status-approved"><i class="fas fa-check-circle"></i> Disetujui</span>
            <?php elseif ($activity['status'] === 'rejected'): ?>
                <span class="status-pill status-rejected"><i class="fas fa-times-circle"></i> Ditolak</span>
            <?php else: ?>
                <span class="status-pill status-pending"><i class="fas fa-clock"></i> Menunggu Approval</span>
            <?php endif; ?>
            <a href="<?= base_url('activity/verification') ?>" class="btn btn-sm fw-800 rounded-3 px-3 py-2"
               style="background: rgba(255,255,255,0.15); color: white; border: 1px solid rgba(255,255,255,0.2);">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="row g-4">

    <!-- Left: Detail Info -->
    <div class="col-lg-8">

        <!-- Info Card -->
        <div class="premium-card border-0 shadow-sm mb-4">
            <div class="d-flex align-items-center gap-3 mb-4">
                <div class="bg-success bg-opacity-10 text-success rounded-3 p-3">
                    <i class="fas fa-clipboard-list"></i>
                </div>
                <h6 class="fw-800 mb-0">Informasi Aktivitas</h6>
            </div>
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="info-block">
                        <span class="label">Jenis Aktivitas</span>
                        <span class="value"><?= esc(ucwords($activity['jenis_aktivitas'])) ?></span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-block">
                        <span class="label">Tanggal Pelaksanaan</span>
                        <span class="value"><?= date('d F Y', strtotime($activity['tanggal'])) ?></span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-block">
                        <span class="label">Lahan Pertanian</span>
                        <div class="d-flex align-items-center gap-2 mt-1">
                            <div class="bg-success bg-opacity-10 text-success rounded-2 p-2">
                                <i class="fas fa-map-marked-alt small"></i>
                            </div>
                            <div>
                                <div class="fw-800 small"><?= esc($land['nama_lahan'] ?? 'N/A') ?></div>
                                <div class="text-muted" style="font-size:11px; font-weight:700;">
                                    Komoditas: <?= esc($land['komoditas'] ?? 'N/A') ?>
                                    <?php if (!empty($land['luas'])): ?> · <?= number_format($land['luas'], 2) ?> Ha<?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-block">
                        <span class="label">Pelapor / Petani</span>
                        <div class="d-flex align-items-center gap-2 mt-1">
                            <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center fw-800"
                                 style="width:40px; height:40px; font-size:14px;">
                                <?= strtoupper(substr($user['nama'] ?? 'P', 0, 1)) ?>
                            </div>
                            <div>
                                <div class="fw-800 small"><?= esc($user['nama'] ?? 'N/A') ?></div>
                                <div class="text-muted" style="font-size:11px; font-weight:700;">
                                    <?= esc($user['role'] ?? '') ?> · ID #<?= $user['id_user'] ?? 'N/A' ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="info-block">
                        <span class="label">Deskripsi Kegiatan</span>
                        <div class="bg-light rounded-3 p-3 mt-1" style="font-size:14px; line-height:1.7; color:#334155;">
                            <?php
                                $deskripsi = $activity['deskripsi'] ?: 'Tidak ada deskripsi tambahan.';
                                $parts = explode('[CATATAN PENOLAKAN]:', $deskripsi, 2);
                                echo nl2br(esc(trim($parts[0])));
                            ?>
                        </div>
                    </div>
                </div>
                <?php if ($activity['status'] === 'rejected' && !empty($activity['deskripsi']) && str_contains($activity['deskripsi'], '[CATATAN PENOLAKAN]:')):?>
                <?php $note = explode('[CATATAN PENOLAKAN]:', $activity['deskripsi'], 2); ?>
                <div class="col-12">
                    <div class="rounded-4 p-4" style="background:#fff1f2; border:1.5px solid #fecdd3;">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <i class="fas fa-exclamation-circle text-danger"></i>
                            <span class="fw-800 text-danger" style="font-size:12px; text-transform:uppercase; letter-spacing:1px;">Catatan Penolakan</span>
                        </div>
                        <p class="mb-0 fw-700" style="color:#991b1b; font-size:14px; line-height:1.7;">
                            <?= nl2br(esc(trim($note[1]))) ?>
                        </p>
                    </div>
                </div>
                <?php endif; ?>
                <?php if (!empty($activity['hasil_panen'])): ?>
                <div class="col-12">
                    <div class="rounded-4 p-4 d-flex align-items-center gap-4" style="background: linear-gradient(135deg, #dcfce7, #bbf7d0); border: 1.5px solid #86efac;">
                        <div class="d-flex align-items-center justify-content-center rounded-3" style="width:56px; height:56px; background:rgba(22,163,74,0.15); flex-shrink:0;">
                            <i class="fas fa-box-open" style="color:#16a34a; font-size:22px;"></i>
                        </div>
                        <div>
                            <div style="font-size:10px; font-weight:800; color:#15803d; text-transform:uppercase; letter-spacing:1px; margin-bottom:4px;">Hasil Panen Tercatat</div>
                            <div style="font-size:28px; font-weight:900; color:#14532d; line-height:1;">
                                <?= number_format($activity['hasil_panen'], 2) ?>
                                <span style="font-size:16px; font-weight:700; color:#15803d;"><?= esc($activity['satuan'] ?? 'Ton') ?></span>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                <div class="col-md-6">
                    <div class="info-block">
                        <span class="label">Waktu Input Sistem</span>
                        <span class="value" style="font-size:13px;"><?= date('d F Y, H:i', strtotime($activity['created_at'])) ?> WIB</span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-block">
                        <span class="label">Koordinat Lokasi</span>
                        <span class="value" style="font-size:13px;">
                            <?php if (!empty($activity['koordinat_wkt'])): ?>
                                <i class="fas fa-map-pin text-success me-1"></i>
                                <?php
                                    $coords = explode(' ', str_replace(['POINT(', ')'], '', $activity['koordinat_wkt']));
                                    echo round($coords[1] ?? 0, 6) . ', ' . round($coords[0] ?? 0, 6);
                                ?>
                            <?php else: ?>
                                <span class="text-muted">Tidak tersedia</span>
                            <?php endif; ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- PPL Action Card -->
        <?php if (session()->get('role') === 'ppl' && $activity['status'] === 'pending'): ?>
        <div class="premium-card border-0 shadow-sm" style="border-left: 4px solid #f59e0b !important; border-left-style: solid;">
            <div class="d-flex align-items-center gap-3 mb-4">
                <div class="bg-warning bg-opacity-10 text-warning rounded-3 p-3">
                    <i class="fas fa-gavel"></i>
                </div>
                <div>
                    <h6 class="fw-800 mb-0">Tindakan Verifikasi</h6>
                    <p class="text-muted small mb-0 fw-600">Aktivitas ini menunggu keputusan Anda</p>
                </div>
            </div>
            <div class="row g-3">
                <div class="col-md-6">
                    <form method="post" action="<?= base_url('activity/approve/' . $activity['id_aktivitas']) ?>">
                        <?= csrf_field() ?>
                        <button type="submit" class="action-btn action-approve"
                                onclick="return confirm('Setujui aktivitas ini?')">
                            <i class="fas fa-check-circle"></i> Setujui Aktivitas
                        </button>
                    </form>
                </div>
                <div class="col-md-6">
                    <form method="post" id="rejectFormPpl" action="<?= base_url('activity/reject/' . $activity['id_aktivitas']) ?>">
                        <?= csrf_field() ?>
                        <input type="hidden" name="catatan_penolakan" id="rejectNotePpl">
                        <button type="button" class="action-btn action-reject" onclick="openRejectModal('rejectFormPpl','rejectNotePpl')">
                            <i class="fas fa-times-circle"></i> Tolak Aktivitas
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Admin Action Card -->
        <?php if (session()->get('role') === 'admin' && $activity['status'] === 'pending'): ?>
        <div class="premium-card border-0 shadow-sm">
            <h6 class="fw-800 mb-3"><i class="fas fa-shield-alt text-success me-2"></i>Tindakan Admin</h6>
            <div class="row g-3">
                <div class="col-md-6">
                    <form method="post" action="<?= base_url('activity/approve/' . $activity['id_aktivitas']) ?>">
                        <?= csrf_field() ?>
                        <button type="submit" class="action-btn action-approve"
                                onclick="return confirm('Setujui aktivitas ini?')">
                            <i class="fas fa-check-circle"></i> Setujui
                        </button>
                    </form>
                </div>
                <div class="col-md-6">
                    <form method="post" id="rejectFormAdmin" action="<?= base_url('activity/reject/' . $activity['id_aktivitas']) ?>">
                        <?= csrf_field() ?>
                        <input type="hidden" name="catatan_penolakan" id="rejectNoteAdmin">
                        <button type="button" class="action-btn action-reject" onclick="openRejectModal('rejectFormAdmin','rejectNoteAdmin')">
                            <i class="fas fa-times-circle"></i> Tolak
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Rejected: Aksi Ubah Keputusan (PPL & Admin) -->
        <?php if (in_array(session()->get('role'), ['ppl','admin']) && $activity['status'] === 'rejected'): ?>
        <div class="premium-card border-0 shadow-sm" style="border-left:4px solid #dc2626;">
            <div class="d-flex align-items-center gap-3 mb-4">
                <div class="rounded-3 p-3" style="background:#fff1f2;">
                    <i class="fas fa-undo" style="color:#dc2626;"></i>
                </div>
                <div>
                    <h6 class="fw-800 mb-0">Ubah Keputusan</h6>
                    <p class="text-muted small mb-0 fw-600">Aktivitas ini sebelumnya telah ditolak</p>
                </div>
            </div>
            <div class="row g-3">
                <div class="col-md-6">
                    <form method="post" action="<?= base_url('activity/approve/' . $activity['id_aktivitas']) ?>">
                        <?= csrf_field() ?>
                        <button type="submit" class="action-btn action-approve"
                                onclick="return confirm('Setujui aktivitas yang telah ditolak ini?')">
                            <i class="fas fa-check-circle"></i> Setujui Sekarang
                        </button>
                    </form>
                </div>
                <div class="col-md-6">
                    <form method="post" action="<?= base_url('activity/reopen/' . $activity['id_aktivitas']) ?>">
                        <?= csrf_field() ?>
                        <button type="submit" class="action-btn"
                                style="background:#fef3c7; color:#92400e;"
                                onclick="return confirm('Kembalikan status ke Menunggu Verifikasi?')">
                            <i class="fas fa-rotate-left"></i> Kembalikan ke Pending
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Rejected: Petani Revise & Resubmit -->
        <?php if (session()->get('role') === 'petani' && $activity['status'] === 'rejected'): ?>
        <div class="premium-card border-0 shadow-sm" style="border-left:4px solid #f59e0b; background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);">
            <div class="d-flex align-items-center gap-3 mb-3">
                <div class="rounded-3 p-3" style="background:rgba(245,158,11,0.15);">
                    <i class="fas fa-pen-to-square" style="color:#d97706; font-size:18px;"></i>
                </div>
                <div>
                    <h6 class="fw-800 mb-1" style="color:#92400e;">Aktivitas Ini Ditolak</h6>
                    <p class="mb-0 fw-600" style="font-size:12px; color:#b45309;">Baca catatan penolakan, perbaiki data, lalu kirim ulang.</p>
                </div>
            </div>
            <a href="<?= base_url('activity/edit/' . $activity['id_aktivitas']) ?>"
               class="btn fw-800 w-100 rounded-3 d-flex align-items-center justify-content-center gap-2"
               style="background:#d97706; color:white; padding:14px; font-size:14px; border:none; transition:all 0.2s;"
               onmouseover="this.style.background='#b45309'" onmouseout="this.style.background='#d97706'">
                <i class="fas fa-pen-to-square"></i> Perbaiki &amp; Submit Ulang
            </a>
        </div>
        <?php endif; ?>


        <!-- Approved: Batalkan Persetujuan (Admin only) -->
        <?php if (session()->get('role') === 'admin' && $activity['status'] === 'approved'): ?>
        <div class="premium-card border-0 shadow-sm" style="border-left:4px solid #16a34a;">
            <div class="d-flex align-items-center gap-3 mb-4">
                <div class="rounded-3 p-3" style="background:#dcfce7;">
                    <i class="fas fa-shield-check" style="color:#16a34a;"></i>
                </div>
                <div>
                    <h6 class="fw-800 mb-0">Tindakan Admin</h6>
                    <p class="text-muted small mb-0 fw-600">Aktivitas ini sudah disetujui</p>
                </div>
            </div>
            <div class="row g-3">
                <div class="col-md-6">
                    <form method="post" action="<?= base_url('activity/reopen/' . $activity['id_aktivitas']) ?>">
                        <?= csrf_field() ?>
                        <button type="submit" class="action-btn"
                                style="background:#fef3c7; color:#92400e;"
                                onclick="return confirm('Batalkan persetujuan dan kembalikan ke pending?')">
                            <i class="fas fa-rotate-left"></i> Batalkan Persetujuan
                        </button>
                    </form>
                </div>
                <div class="col-md-6">
                    <form method="post" id="rejectFormApproved" action="<?= base_url('activity/reject/' . $activity['id_aktivitas']) ?>">
                        <?= csrf_field() ?>
                        <input type="hidden" name="catatan_penolakan" id="rejectNoteApproved">
                        <button type="button" class="action-btn action-reject"
                                onclick="openRejectModal('rejectFormApproved','rejectNoteApproved')">
                            <i class="fas fa-times-circle"></i> Tolak
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Right: Photo + Map -->
    <div class="col-lg-4">

        <!-- Flash Messages -->
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert border-0 rounded-4 mb-4 fw-700" style="background:#dcfce7; color:#166534;">
                <i class="fas fa-check-circle me-2"></i><?= session()->getFlashdata('success') ?>
            </div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert border-0 rounded-4 mb-4 fw-700" style="background:#fee2e2; color:#991b1b;">
                <i class="fas fa-exclamation-circle me-2"></i><?= session()->getFlashdata('error') ?>
            </div>
        <?php endif; ?>

        <!-- Photo Card -->
        <div class="premium-card border-0 shadow-sm p-0 overflow-hidden mb-4">
            <div class="p-4 border-bottom d-flex justify-content-between align-items-center">
                <h6 class="fw-800 mb-0"><i class="fas fa-image text-muted me-2"></i>Lampiran Foto</h6>
                <?php if (!empty($activity['foto'])): ?>
                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill fw-800" style="font-size:10px;">TERSEDIA</span>
                <?php endif; ?>
            </div>
            <?php if (!empty($activity['foto'])): ?>
                <div style="position: relative; padding-bottom: 75%; overflow: hidden; background: #f1f5f9;">
                    <img src="<?= base_url('uploads/' . $activity['foto']) ?>"
                         alt="Foto Aktivitas" 
                         style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: contain;"
                         onerror="this.src='https://placehold.co/600x400/f8fafc/64748b?text=Gagal+Memuat+Foto'">
                </div>
                <div class="p-3 bg-light">
                    <a href="<?= base_url('uploads/' . $activity['foto']) ?>" target="_blank"
                       class="btn btn-white btn-sm fw-800 rounded-3 w-100 border shadow-sm" style="font-size:12px;">
                        <i class="fas fa-expand-alt me-1 text-primary"></i> Lihat Foto Penuh
                    </a>
                </div>
            <?php else: ?>
                <div class="d-flex flex-column align-items-center justify-content-center p-5 text-center"
                     style="height:250px; background:#f8fafc;">
                    <i class="fas fa-image fs-1 text-muted opacity-25 mb-3"></i>
                    <p class="text-muted small fw-800 mb-0">Tidak Ada Lampiran Foto</p>
                    <p class="text-muted mt-1" style="font-size:11px;">Aktivitas ini dilaporkan tanpa menyertakan foto bukti.</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Map Card -->
        <div class="premium-card border-0 shadow-sm p-0 overflow-hidden">
            <div class="p-4 border-bottom">
                <h6 class="fw-800 mb-0"><i class="fas fa-map-pin text-success me-2"></i>Lokasi Geografis</h6>
            </div>
            <?php if (!empty($activity['koordinat_wkt'])): ?>
                <div id="activityMap"></div>
            <?php else: ?>
                <div class="d-flex flex-column align-items-center justify-content-center p-5 text-center"
                     style="height:200px; background:#f8fafc;">
                    <i class="fas fa-map-marker-alt fs-1 text-muted opacity-25 mb-2"></i>
                    <p class="text-muted small fw-700 mb-0">Data lokasi tidak tersedia</p>
                    <p class="text-muted" style="font-size:11px;">Petani tidak mencantumkan GPS saat input</p>
                </div>
            <?php endif; ?>
        </div>

    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<?php if (!empty($activity['koordinat_wkt'])): ?>
<script>
document.addEventListener("DOMContentLoaded", function() {
    var wkt = "<?= $activity['koordinat_wkt'] ?>";
    var coords = wkt.replace('POINT(', '').replace(')', '').split(' ');
    // WKT POINT format is POINT(longitude latitude)
    var lng = parseFloat(coords[0]);
    var lat = parseFloat(coords[1]);

    if (!isNaN(lat) && !isNaN(lng)) {
        var map = L.map('activityMap', { 
            zoomControl: false,
            attributionControl: false 
        }).setView([lat, lng], 16);
        
        L.tileLayer('https://{s}.google.com/vt/lyrs=s,h&x={x}&y={y}&z={z}', {
            maxZoom: 20, 
            subdomains: ['mt0','mt1','mt2','mt3']
        }).addTo(map);
        L.control.zoom({ position: 'topright' }).addTo(map);

        var icon = L.divIcon({
            html: '<div style="background:#16a34a; width:16px; height:16px; border-radius:50%; border:3px solid white; box-shadow:0 2px 8px rgba(0,0,0,0.3);"></div>',
            iconSize: [16, 16], iconAnchor: [8, 8]
        });
        L.marker([lat, lng], { icon: icon }).addTo(map)
            .bindPopup('<b><?= esc($activity['jenis_aktivitas']) ?></b><br><?= esc($land['nama_lahan'] ?? '') ?>')
            .openPopup();
    }
});
</script>
<?php endif; ?>

<!-- Rejection Modal -->
<div id="rejectModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.55); z-index:9999; align-items:center; justify-content:center;">
    <div style="background:white; border-radius:24px; width:100%; max-width:480px; margin:20px; box-shadow:0 25px 60px rgba(0,0,0,0.25); overflow:hidden;">
        <div style="background:linear-gradient(135deg,#991b1b,#dc2626); padding:24px 28px; color:white;">
            <div class="d-flex align-items-center gap-3">
                <div style="background:rgba(255,255,255,0.2); border-radius:12px; padding:10px 12px;">
                    <i class="fas fa-times-circle fs-5"></i>
                </div>
                <div>
                    <h5 class="fw-800 mb-0">Konfirmasi Penolakan</h5>
                    <p class="small mb-0" style="opacity:0.8;">Berikan alasan yang jelas agar petani dapat memperbaiki laporan</p>
                </div>
            </div>
        </div>
        <div class="p-4">
            <label class="fw-800 mb-2 d-block" style="font-size:11px; text-transform:uppercase; letter-spacing:1px; color:#64748b;">
                Catatan Penolakan <span class="text-danger">*</span>
            </label>
            <textarea id="rejectNoteInput" rows="4"
                placeholder="Contoh: Foto tidak sesuai lokasi lahan, aktivitas dilakukan di luar area yang terdaftar, data tidak lengkap..."
                style="width:100%; border:1.5px solid #e2e8f0; border-radius:12px; padding:14px 16px; font-size:14px; font-weight:600; resize:none; outline:none; transition:border 0.2s; color:#1e293b;"
                onfocus="this.style.borderColor='#dc2626'" onblur="this.style.borderColor='#e2e8f0'"></textarea>
            <p id="rejectNoteError" style="color:#dc2626; font-size:12px; font-weight:700; display:none; margin-top:6px;">
                <i class="fas fa-exclamation-circle me-1"></i>Catatan penolakan wajib diisi.
            </p>
            <div class="d-flex gap-3 mt-4">
                <button onclick="closeRejectModal()"
                    class="btn fw-800 border rounded-3 flex-grow-1" style="padding:13px; font-size:14px;">
                    <i class="fas fa-arrow-left me-2"></i>Batal
                </button>
                <button onclick="submitReject()"
                    class="btn fw-800 rounded-3 flex-grow-1 text-white" style="padding:13px; background:#dc2626; font-size:14px;">
                    <i class="fas fa-times-circle me-2"></i>Konfirmasi Tolak
                </button>
            </div>
        </div>
    </div>
</div>

<script>
var _rejectFormId, _rejectNoteId;
function openRejectModal(formId, noteId) {
    _rejectFormId = formId;
    _rejectNoteId = noteId;
    document.getElementById('rejectNoteInput').value = '';
    document.getElementById('rejectNoteError').style.display = 'none';
    document.getElementById('rejectModal').style.display = 'flex';
    setTimeout(() => document.getElementById('rejectNoteInput').focus(), 100);
}
function closeRejectModal() {
    document.getElementById('rejectModal').style.display = 'none';
}
function submitReject() {
    var note = document.getElementById('rejectNoteInput').value.trim();
    if (!note) {
        document.getElementById('rejectNoteError').style.display = 'block';
        document.getElementById('rejectNoteInput').style.borderColor = '#dc2626';
        return;
    }
    document.getElementById(_rejectNoteId).value = note;
    document.getElementById(_rejectFormId).submit();
}
document.getElementById('rejectModal').addEventListener('click', function(e) {
    if (e.target === this) closeRejectModal();
});
</script>
<?= $this->endSection() ?>