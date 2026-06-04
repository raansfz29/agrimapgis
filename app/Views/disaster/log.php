<?= $this->extend('layouts/premium') ?>

<?= $this->section('styles') ?>
<style>
    .log-hero {
        background: linear-gradient(135deg, #be123c 0%, #881337 100%);
        border-radius: 24px;
        padding: 35px;
        color: white;
        position: relative;
        overflow: hidden;
        margin-bottom: 30px;
    }
    .log-hero.resolved {
        background: linear-gradient(135deg, #1e7e34 0%, #14532d 100%);
    }
    .timeline-container {
        position: relative;
        padding-left: 30px;
    }
    .timeline-container::before {
        content: '';
        position: absolute;
        left: 10px;
        top: 10px;
        bottom: 10px;
        width: 2px;
        background: #e2e8f0;
    }
    .timeline-item {
        position: relative;
        margin-bottom: 25px;
    }
    .timeline-dot {
        position: absolute;
        left: -29px;
        top: 0;
        width: 18px;
        height: 18px;
        border-radius: 50%;
        background: #be123c;
        border: 4px solid white;
        box-shadow: 0 0 0 2px #f1f5f9;
    }
    .timeline-dot.resolved { background: #10b981; }
    .timeline-dot.system { background: #3b82f6; }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <a href="<?= base_url('disaster') ?>" class="btn btn-light btn-sm fw-800 rounded-pill px-3 mb-2"><i class="fas fa-arrow-left me-1"></i> Kembali</a>
        <h3 class="fw-800 mb-1">Jurnal Log Mitigasi</h3>
        <p class="text-muted small fw-600 mb-0">Catatan kronologis penanganan bencana lahan.</p>
    </div>
</div>

<div class="log-hero <?= $land['status_bencana'] === 'aman' ? 'resolved' : '' ?>">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h4 class="fw-800 mb-2"><?= esc($land['nama_lahan']) ?></h4>
            <div class="d-flex gap-3 text-white-50 small fw-bold mb-3">
                <span><i class="fas fa-seedling me-1"></i> <?= esc($land['komoditas']) ?></span>
                <span><i class="fas fa-layer-group me-1"></i> <?= $land['luas'] ?> Ha</span>
            </div>
            <p class="mb-0 opacity-75 small">Deskripsi Kejadian Awal: <br> <span class="text-white fw-600"><?= esc($land['deskripsi_bencana']) ?></span></p>
        </div>
        <div class="col-md-4 text-md-end mt-3 mt-md-0">
            <?php if ($land['status_bencana'] === 'darurat'): ?>
                <span class="badge bg-white text-danger px-3 py-2 rounded-pill fw-800"><i class="fas fa-exclamation-circle me-1"></i> STATUS: DARURAT</span>
            <?php else: ?>
                <span class="badge bg-white text-success px-3 py-2 rounded-pill fw-800"><i class="fas fa-check-circle me-1"></i> STATUS: SELESAI (AMAN)</span>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="premium-card border-0 shadow-sm">
            <h6 class="fw-800 mb-4 border-bottom pb-3"><i class="fas fa-history text-muted me-2"></i> Rekam Jejak (Timeline)</h6>
            
            <?php if (empty($logs)): ?>
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-clipboard-list fs-1 opacity-25 mb-3"></i>
                    <p class="small fw-bold">Belum ada log kejadian yang dicatat.</p>
                </div>
            <?php else: ?>
                <div class="timeline-container">
                    <?php foreach ($logs as $log): ?>
                        <div class="timeline-item">
                            <div class="timeline-dot <?= (isset($log['status_penanganan']) && ($log['status_penanganan'] === 'Selesai' || $log['status_penanganan'] === 'Selesai Ditangani')) ? 'resolved' : '' ?>"></div>
                            <div class="bg-light rounded-4 p-3 border">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <span class="fw-800 text-dark small"><?= esc($log['nama_user'] ?? 'User') ?></span>
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary border px-2 py-1 ms-2" style="font-size:9px;"><?= strtoupper($log['role_user'] ?? 'N/A') ?></span>
                                    </div>
                                    <span class="text-muted" style="font-size:10px; font-weight:700;"><i class="far fa-clock me-1"></i> <?= isset($log['created_at']) ? date('d M Y, H:i', strtotime($log['created_at'])) : date('d M Y, H:i') ?></span>
                                </div>
                                <?php if (!empty($log['judul_kejadian'])): ?>
                                    <h6 class="fw-bold text-dark small mb-1"><?= esc($log['judul_kejadian']) ?></h6>
                                <?php endif; ?>
                                <p class="mb-0 text-muted small fw-600 mb-2" style="line-height:1.5;">
                                    <?= nl2br(esc($log['deskripsi_kejadian'] ?? $log['tindakan_diambil'] ?? '')) ?>
                                </p>
                                <?php if (!empty($log['foto'])): ?>
                                    <div class="mt-2">
                                        <a href="<?= base_url('uploads/' . $log['foto']) ?>" target="_blank">
                                            <img src="<?= base_url('uploads/' . $log['foto']) ?>" alt="Foto Kejadian" class="img-fluid rounded-3 border" style="max-height: 200px; object-fit: cover; max-width: 100%;">
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="col-lg-4">
        <?php if ($land['status_bencana'] === 'darurat'): ?>
            <div class="premium-card border-0 shadow-sm mb-4">
                <h6 class="fw-800 mb-3"><i class="fas fa-plus-circle text-primary me-2"></i> Tambah Log Baru</h6>
                <form action="<?= base_url('disaster/submitLog/'.$land['id_lahan']) ?>" method="POST" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label x-small fw-800 text-muted">CATATAN PERKEMBANGAN</label>
                        <textarea name="catatan" class="form-control bg-light border-0" rows="4" placeholder="Contoh: Air mulai surut, penyemprotan dilakukan..." required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label x-small fw-800 text-muted">FOTO KEJADIAN <?= $role === 'petani' ? '<span class="text-danger">* (Wajib)</span>' : '(Opsional)' ?></label>
                        <input type="file" name="foto_kejadian" class="form-control bg-light border-0" accept="image/*" <?= $role === 'petani' ? 'required' : '' ?>>
                        <div class="form-text text-muted" style="font-size: 10px;">Format: JPG, JPEG, PNG, WEBP, HEIC</div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 fw-800 rounded-pill py-2">SIMPAN LOG</button>
                </form>
            </div>
            
            <?php if (session()->get('role') !== 'petani'): ?>
                <div class="premium-card border-0 shadow-sm bg-danger bg-opacity-10 border border-danger border-opacity-25">
                    <h6 class="fw-800 mb-2 text-danger"><i class="fas fa-exclamation-triangle me-2"></i> Zona Resolusi</h6>
                    <p class="small text-danger opacity-75 fw-600 mb-3">Tandai jika lahan sudah kembali normal dan aman. Ini akan mengunci panel log.</p>
                    <form action="<?= base_url('disaster/deactivate/'.$land['id_lahan']) ?>" method="POST">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-danger w-100 fw-800 rounded-pill py-2" onclick="return confirm('Anda yakin ingin menandai lahan ini telah aman?')"><i class="fas fa-check-circle me-1"></i> TANDAI BENCANA SELESAI</button>
                    </form>
                </div>
            <?php endif; ?>
            
        <?php else: ?>
            <div class="premium-card border-0 shadow-sm bg-light text-center py-5">
                <div class="bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width:50px; height:50px;">
                    <i class="fas fa-lock"></i>
                </div>
                <h6 class="fw-800 text-dark">Log Diarsipkan</h6>
                <p class="small text-muted mb-0 fw-600 px-3">Bencana telah dinyatakan selesai. Panel log kejadian ini telah dikunci secara permanen sebagai arsip.</p>
            </div>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>
