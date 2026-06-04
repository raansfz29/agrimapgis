<?= $this->extend('layouts/premium') ?>

<?= $this->section('content') ?>
<div class="row g-4">
    <div class="col-md-4">
        <!-- Profile Overview -->
        <div class="premium-card text-center">
            <div class="avatar-box mx-auto mb-4" style="width: 120px; height: 120px; background: var(--primary-green); border-radius: 30px; display: flex; align-items: center; justify-content: center; color: white; font-size: 48px; font-weight: 800; box-shadow: 0 10px 25px rgba(30, 126, 52, 0.2);">
                <?= strtoupper(substr($user['nama'], 0, 1)) ?>
            </div>
            <h4 class="fw-800 mb-1"><?= esc($user['nama']) ?></h4>
            <p class="text-muted small mb-4"><?= esc(ucfirst($user['role'])) ?> • <?= esc($group['nama_kelompok'] ?? 'Semua Kelompok') ?></p>
            
            <div class="d-flex justify-content-center gap-2 mb-4">
                <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill fw-bold">Active</span>
            </div>

            <a href="#edit-form" class="btn btn-light w-100 rounded-pill fw-bold mb-4 border shadow-sm"><i class="fas fa-edit me-2"></i>Edit Profil</a>

            <hr class="text-muted opacity-25 mb-4">

            <div class="text-start">
                <h6 class="fw-800 mb-3 small text-muted text-uppercase">Informasi Kontak</h6>
                <div class="mb-3 d-flex align-items-center gap-3">
                    <div class="bg-light p-2 rounded-3 text-muted"><i class="fas fa-envelope small"></i></div>
                    <span class="small fw-bold"><?= esc($user['email']) ?></span>
                </div>
                <div class="mb-3 d-flex align-items-center gap-3">
                    <div class="bg-light p-2 rounded-3 text-muted"><i class="fas fa-phone small"></i></div>
                    <span class="small fw-bold"><?= esc($user['telepon'] ?? '-') ?></span>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-light p-2 rounded-3 text-muted"><i class="fas fa-calendar small"></i></div>
                    <span class="small fw-bold">Bergabung <?= date('M Y', strtotime($user['created_at'])) ?></span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-8 d-none" id="edit-form">
        <!-- Edit Profile Form -->
        <div class="premium-card mb-4 shadow-sm">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-800 mb-0">Pengaturan Profil</h5>
                <span class="badge bg-primary bg-opacity-10 text-primary fw-bold px-3">Form Update</span>
            </div>
            
            <?php if (session()->getFlashdata('success')) : ?>
                <div class="alert alert-success border-0 shadow-sm rounded-3 mb-4 small fw-bold">
                    <i class="fas fa-check-circle me-2"></i> <?= session()->getFlashdata('success') ?>
                </div>
                <script>document.addEventListener('DOMContentLoaded', () => document.getElementById('edit-form').classList.remove('d-none'));</script>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')) : ?>
                <div class="alert alert-danger border-0 shadow-sm rounded-3 mb-4 small fw-bold">
                    <i class="fas fa-exclamation-circle me-2"></i> <?= session()->getFlashdata('error') ?>
                </div>
                <script>document.addEventListener('DOMContentLoaded', () => document.getElementById('edit-form').classList.remove('d-none'));</script>
            <?php endif; ?>

            <form action="<?= base_url('profile/update') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-800 text-muted">NAMA LENGKAP</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0"><i class="fas fa-user text-muted"></i></span>
                            <input type="text" name="nama" class="form-control border-start-0 ps-0 py-2 fw-bold" value="<?= esc($user['nama']) ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-800 text-muted">ALAMAT EMAIL</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0"><i class="fas fa-envelope text-muted"></i></span>
                            <input type="email" name="email" class="form-control border-start-0 ps-0 py-2 fw-bold" value="<?= esc($user['email']) ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-800 text-muted">NOMOR TELEPON / WA</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0"><i class="fas fa-phone text-muted"></i></span>
                            <input type="text" name="telepon" class="form-control border-start-0 ps-0 py-2 fw-bold" value="<?= esc($user['telepon'] ?? '') ?>" placeholder="08xx...">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-800 text-muted">PASSWORD BARU</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0"><i class="fas fa-lock text-muted"></i></span>
                            <input type="password" name="password" class="form-control border-start-0 ps-0 py-2" placeholder="Kosongkan jika tidak diubah">
                        </div>
                    </div>
                    <div class="col-12 mt-4 pt-2 border-top">
                        <button type="submit" class="btn btn-success fw-800 rounded-pill px-5 py-2">Update Profil</button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Security Info -->
        <div class="premium-card bg-light border-0">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-white p-3 rounded-circle shadow-sm">
                    <i class="fas fa-shield-alt text-success fs-4"></i>
                </div>
                <div>
                    <h6 class="fw-800 mb-1">Keamanan Akun</h6>
                    <p class="text-muted small mb-0">Pastikan Anda menggunakan kata sandi yang kuat untuk menjaga data pertanian tetap aman.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->section('scripts') ?>
<script>
    document.querySelector('a[href="#edit-form"]').addEventListener('click', function(e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        
        if (target.classList.contains('d-none')) {
            target.classList.remove('d-none');
            target.style.opacity = '0';
            target.style.transform = 'translateY(20px)';
            
            // Trigger animation
            setTimeout(() => {
                target.style.transition = 'all 0.5s ease';
                target.style.opacity = '1';
                target.style.transform = 'translateY(0)';
            }, 50);
        }
        
        target.scrollIntoView({ behavior: 'smooth' });
    });
</script>
<?= $this->endSection() ?>
<?= $this->endSection() ?>
