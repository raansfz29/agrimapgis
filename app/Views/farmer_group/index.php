<?= $this->extend('layouts/premium') ?>

<?= $this->section('content') ?>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success border-0 shadow-sm rounded-3 fw-bold"><i class="fas fa-check-circle me-2"></i><?= session()->getFlashdata('success') ?></div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger border-0 shadow-sm rounded-3 fw-bold"><i class="fas fa-exclamation-circle me-2"></i><?= session()->getFlashdata('error') ?></div>
<?php endif; ?>

<!-- Formal Print Header (Kop Surat - hidden on screen) -->
<div class="d-none d-print-block">
    <div class="print-header">
        <div class="print-logo-box">
            <i class="fas fa-seedling"></i>
        </div>
        <div class="print-header-text">
            <h4>PEMERINTAH KOTA BANDAR LAMPUNG</h4>
            <p>DINAS PERTANIAN DAN KETAHANAN PANGAN</p>
            <p>Jl. Dr. Susilo No.1, Bandar Lampung, Lampung. Telp: (0721) 252300</p>
        </div>
    </div>

    <div class="print-title">
        <h2>LAPORAN DATA KELOMPOK TANI</h2>
        <p>Wilayah Kerja Rajabasa &bull; Periode: <?= date('F Y') ?></p>
    </div>

    <table class="print-meta-table">
        <tr>
            <td style="text-align: left;">Nomor : <?= rand(100, 999) ?>/KT-MAP/<?= date('Y') ?></td>
            <td style="text-align: right;">Dicetak: <?= date('d/m/Y H:i') ?> WIB</td>
        </tr>
    </table>
</div>

<?php if (!isset($group)): ?>
<!-- TAMPILAN DAFTAR KELOMPOK TANI -->
<div class="premium-card mb-4">
    <div class="d-flex justify-content-between align-items-center mb-4 page-title-area">
        <div>
            <h5 class="fw-800 mb-0">Daftar Kelompok Tani Terdaftar</h5>
            <p class="text-muted small mb-0">Kelola master data kelompok tani wilayah Rajabasa.</p>
        </div>
        <div class="d-flex gap-2 d-print-none">
            <button class="btn btn-outline-success rounded-pill px-4 fw-bold shadow-sm" onclick="window.print()">
                <i class="fas fa-print me-2"></i> Ekspor PDF
            </button>
            <button class="btn btn-success rounded-pill px-4 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#addgroupModal">
                <i class="fas fa-plus me-2"></i> Tambah Kelompok Tani
            </button>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
                <tr>
                    <th class="border-0 small fw-800 text-muted ps-3">NAMA KELOMPOK</th>
                    <th class="border-0 small fw-800 text-muted">KETUA</th>
                    <th class="border-0 small fw-800 text-muted">WILAYAH</th>
                    <th class="border-0 small fw-800 text-muted">GAPOKTAN</th>
                    <th class="border-0 small fw-800 text-muted">KOMODITAS</th>
                    <th class="border-0 small fw-800 text-muted text-center">LAHAN</th>
                    <th class="border-0 small fw-800 text-muted text-center">ANGGOTA</th>
                    <th class="border-0 small fw-800 text-muted text-center d-print-none">AKSI</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($all_groups)): ?>
                    <tr><td colspan="7" class="text-center py-5 text-muted small">Belum ada kelompok tani.</td></tr>
                <?php else: ?>
                    <?php foreach ($all_groups as $g): ?>
                        <tr>
                            <td class="ps-3 fw-bold"><?= esc($g['nama_kelompok']) ?></td>
                            <td><?= esc($g['ketua']) ?></td>
                            <td>Kec. <?= esc($g['kecamatan']) ?></td>
                            <td><?= esc($g['gapoktan'] ?? '-') ?></td>
                            <td>
                                <?php if (!empty($g['komoditas_aktual'])): ?>
                                    <div class="d-flex flex-wrap gap-1">
                                    <?php 
                                        $komoditasList = array_map('trim', explode(',', $g['komoditas_aktual']));
                                        foreach ($komoditasList as $komoditasItem):
                                            $lowerItem = strtolower($komoditasItem);
                                            if (strpos($lowerItem, 'padi') !== false) {
                                                $color = ['text' => 'text-primary', 'bg' => 'bg-primary'];
                                            } elseif (strpos($lowerItem, 'jagung') !== false) {
                                                $color = ['text' => 'text-success', 'bg' => 'bg-success'];
                                            } else {
                                                $color = ['text' => 'text-info', 'bg' => 'bg-info'];
                                            }
                                    ?>
                                        <span class="badge <?= $color['bg'] ?> bg-opacity-10 <?= $color['text'] ?> rounded-pill" style="font-size: 10px;">
                                            <?= esc(ucwords($komoditasItem)) ?>
                                        </span>
                                    <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <span class="text-muted small">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <div class="fw-900 text-dark" style="font-size: 16px; line-height: 1.2;"><?= $g['total_lahan'] ?> <small class="fw-700 text-muted" style="font-size: 11px;">Plot</small></div>
                                <div class="text-success fw-800" style="font-size: 12px;"><?= number_format($g['total_luas'], 1) ?> Ha</div>
                            </td>
                            <td class="text-center"><span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3"><?= $g['total_anggota'] ?> Petani</span></td>
                            <td class="text-center d-print-none">
                                <a href="<?= base_url('farmer-groups?id='.$g['id_kelompok']) ?>" class="btn btn-light btn-sm border rounded-pill px-3 fw-bold">Detail</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Print-only Signature Area -->
<div class="d-none d-print-block">
    <table class="print-sig-table">
        <tr>
            <td>
                <div class="sig-title-block">Mengetahui,<br>Analis Pertanian</div>
                <div class="sig-name">Analis Pertanian Utama</div>
                <div class="sig-nip">NIP. 19850215 201201 1 005</div>
            </td>
            <td>
                <div class="sig-title-block"><br>Kabid Kelompok Tani</div>
                <div class="sig-name">Sari Wijaya, S.P.</div>
                <div class="sig-nip">NIP. 19750512 200501 2 003</div>
            </td>
            <td>
                <div class="sig-title-block"><br>Kepala Dinas Pertanian</div>
                <div class="sig-name">Dr. Ir. Heru Santoso</div>
                <div class="sig-nip">NIP. 19680320 199403 1 002</div>
            </td>
        </tr>
    </table>
</div>

<!-- Modal Tambah Kelompok -->
<div class="modal fade" id="addgroupModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-800"><i class="fas fa-users text-success me-2"></i>Registrasi Kelompok Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form action="<?= base_url('farmer-groups/store') ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">NAMA KELOMPOK TANI</label>
                        <input type="text" name="nama_kelompok" class="form-control rounded-3" required placeholder="Contoh: Maju Jaya">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">NAMA KETUA</label>
                        <input type="text" name="ketua" class="form-control rounded-3" required placeholder="Nama lengkap ketua">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">WILAYAH (KECAMATAN)</label>
                        <input type="text" name="kecamatan" class="form-control rounded-3" required value="Rajabasa">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">GAPOKTAN</label>
                        <input type="text" name="gapoktan" class="form-control rounded-3" placeholder="Nama Gabungan Kelompok Tani">
                    </div>
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-muted">KOMODITAS UTAMA</label>
                        <input type="text" name="komoditas" class="form-control rounded-3" placeholder="Contoh: Padi, Jagung">
                    </div>
                    <div class="mt-4 pt-3 border-top">
                        <button type="submit" class="btn btn-success w-100 rounded-pill fw-bold py-2 shadow-sm">Simpan Kelompok</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php if (isset($group)): ?>
<!-- TAMPILAN DETAIL KELOMPOK (UNTUK PPL ATAU ADMIN YANG MELIHAT DETAIL) -->
<div class="d-flex align-items-center mb-4">
    <a href="<?= session()->get('role') === 'petani' ? base_url('dashboard') : base_url('farmer-groups') ?>" class="btn btn-light rounded-pill shadow-sm fw-bold me-3"><i class="fas fa-arrow-left me-2"></i>Kembali</a>
    <h5 class="fw-800 mb-0">Detail Manajemen Kelompok</h5>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-4">
        <!-- Group Info Card -->
        <div class="premium-card h-100">
            <div class="bg-success bg-opacity-10 p-3 rounded-circle mx-auto mb-4 d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                <i class="fas fa-users text-success fs-2"></i>
            </div>
            <div class="text-center position-relative">
                <?php if (session()->get('role') !== 'petani'): ?>
                <button type="button" class="btn btn-light text-primary btn-sm position-absolute top-0 end-0 rounded-circle" style="width: 35px; height: 35px; margin-top: -10px; margin-right: -10px;" data-bs-toggle="modal" data-bs-target="#editGroupModal" title="Edit Kelompok"><i class="fas fa-edit"></i></button>
                <?php endif; ?>
                <h4 class="fw-800 mb-1"><?= esc($group['nama_kelompok']) ?></h4>
                <p class="text-muted small mb-4">Wilayah Kerja: <?= esc($group['kecamatan']) ?></p>
            </div>
            
            <hr class="opacity-25">
            
            <div class="mt-4">
                <div class="d-flex justify-content-between mb-3">
                    <span class="text-muted small fw-bold">Ketua Kelompok</span>
                    <span class="small fw-800"><?= esc($group['ketua'] ?? '-') ?></span>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <span class="text-muted small fw-bold">Total Anggota</span>
                    <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3"><?= count($members) ?> Petani</span>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <span class="text-muted small fw-bold">Total Lahan</span>
                    <span class="small fw-800 text-dark"><?= esc($land_summary['total_lands']) ?> Lahan</span>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <span class="text-muted small fw-bold">Total Luas Lahan</span>
                    <span class="small fw-800 text-dark"><?= number_format($land_summary['total_luas'], 2) ?> Ha</span>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <span class="text-muted small fw-bold">Lokasi Lahan</span>
                    <span class="small fw-800 text-dark text-end" style="max-width: 60%;">
                        <?php 
                        if(!empty($lands)) {
                            $lokasi = array_column($lands, 'nama_lahan');
                            echo esc(implode(', ', $lokasi));
                        } else {
                            echo '-';
                        }
                        ?>
                    </span>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <span class="text-muted small fw-bold">Gapoktan</span>
                    <span class="small fw-800 text-dark text-end"><?= !empty($group['gapoktan']) ? esc($group['gapoktan']) : '-' ?></span>
                </div>
                <div class="d-flex justify-content-between mb-3 align-items-center">
                    <span class="text-muted small fw-bold">Komoditas Utama</span>
                    <div class="text-end">
                        <?php if(!empty($group['komoditas'])): ?>
                            <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-3"><?= esc(ucwords($group['komoditas'])) ?></span>
                        <?php else: ?>
                            <span class="small fw-800 text-dark">-</span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-muted small fw-bold">Status Binaan</span>
                    <span class="text-success small fw-800"><i class="fas fa-check-circle me-1"></i> Aktif</span>
                </div>

                <?php if (session()->get('role') !== 'petani'): ?>
                <a href="<?= base_url('peta-gis?add_land=true&id_kelompok=' . $group['id_kelompok']) ?>" class="btn btn-outline-success w-100 rounded-pill fw-bold mt-4 shadow-sm py-2">
                    <i class="fas fa-map-location-dot me-2"></i> Tambah Lahan Baru
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <!-- Members List Card -->
        <div class="premium-card h-100">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h6 class="fw-800 mb-0">Data Petani Lengkap</h6>
                <?php 
                $canManageMembers = (session()->get('role') === 'admin' || session()->get('role') === 'ppl' || (isset($is_ketua) && $is_ketua)); 
                if ($canManageMembers): 
                ?>
                <button class="btn btn-primary btn-sm border-0 shadow-sm rounded-pill px-4 fw-bold py-2" data-bs-toggle="modal" data-bs-target="#addfarmerModal"><i class="fas fa-user-plus me-1"></i> Tambah Anggota</button>
                <?php endif; ?>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="border-0 small fw-800 text-muted ps-3">IDENTITAS PETANI</th>
                            <th class="border-0 small fw-800 text-muted">TGL GABUNG</th>
                            <th class="border-0 small fw-800 text-muted text-center">AKTIVITAS</th>
                            <th class="border-0 small fw-800 text-muted text-center">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($members)): ?>
                            <tr><td colspan="4" class="text-center py-5 text-muted small">Belum ada anggota terdaftar.</td></tr>
                        <?php else: ?>
                            <?php foreach ($members as $farmer): ?>
                                <tr>
                                    <td class="ps-3">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 38px; height: 38px; font-size: 13px;">
                                                <?= strtoupper(substr($farmer['nama'], 0, 1)) ?>
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark" style="font-size: 14px;"><?= esc($farmer['nama']) ?></div>
                                                <div class="text-muted mt-1" style="font-size: 12px;"><i class="fas fa-envelope text-muted me-1 opacity-50"></i><?= esc($farmer['email']) ?></div>
                                                <div class="text-muted" style="font-size: 12px;"><i class="fab fa-whatsapp text-success me-1 opacity-75"></i><?= esc($farmer['telepon'] ?? '-') ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="small fw-bold text-muted"><?= date('d M Y', strtotime($farmer['created_at'])) ?></span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-3"><?= $farmer['total_aktivitas'] ?> Laporan</span>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group gap-1">
                                            <a href="<?= base_url('message/chat/'.$farmer['id_user']) ?>" class="btn btn-light btn-xs border rounded-circle p-2" title="Kirim Pesan"><i class="fas fa-comment-alt text-primary"></i></a>
                                            <?php if ($canManageMembers): ?>
                                            <button type="button" class="btn btn-light btn-xs border rounded-circle p-2 text-warning" title="Edit Anggota" data-bs-toggle="modal" data-bs-target="#editfarmerModal<?= $farmer['id_user'] ?>"><i class="fas fa-edit"></i></button>
                                            <a href="<?= base_url('farmer-groups/delete-farmer/'.$farmer['id_user']) ?>" class="btn btn-light btn-xs border rounded-circle p-2 text-danger" title="Hapus Anggota" onclick="return confirm('Yakin ingin menghapus anggota ini?');"><i class="fas fa-trash-alt"></i></a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>

                                <?php if ($canManageMembers): ?>
                                <!-- Edit Farmer Modal -->
                                <div class="modal fade" id="editfarmerModal<?= $farmer['id_user'] ?>" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
                                            <div class="modal-header border-0 bg-light rounded-top-4 p-4 pb-3">
                                                <div>
                                                    <h5 class="modal-title fw-800">Edit Anggota Petani</h5>
                                                    <p class="text-muted small mb-0 mt-1">Perbarui informasi anggota kelompok</p>
                                                </div>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form action="<?= base_url('farmer-groups/update-farmer/'.$farmer['id_user']) ?>" method="post">
                                                <?= csrf_field() ?>
                                                <div class="modal-body p-4">
                                                    <div class="mb-3">
                                                        <label class="form-label small fw-bold text-muted">NAMA LENGKAP</label>
                                                        <input type="text" name="nama" class="form-control rounded-3" value="<?= esc($farmer['nama']) ?>" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label small fw-bold text-muted">EMAIL</label>
                                                        <input type="email" name="email" class="form-control rounded-3" value="<?= esc($farmer['email']) ?>" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label small fw-bold text-muted">PASSWORD BARU (Opsional)</label>
                                                        <input type="password" name="password" class="form-control rounded-3" placeholder="Kosongkan jika tidak ingin mengubah password">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label small fw-bold text-muted">NO. TELEPON</label>
                                                        <input type="text" name="telepon" class="form-control rounded-3" value="<?= esc($farmer['telepon']) ?>">
                                                    </div>
                                                </div>
                                                <div class="modal-footer border-0 p-4 pt-0">
                                                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">Simpan Perubahan</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Petani -->
<div class="modal fade" id="addfarmerModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-800"><i class="fas fa-user-plus text-primary me-2"></i>Registrasi Petani Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form action="<?= base_url('farmer-groups/store-farmer') ?>" method="post">
                    <?= csrf_field() ?>
                    <input type="hidden" name="id_kelompok" value="<?= $group['id_kelompok'] ?>">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">KELOMPOK TANI</label>
                        <input type="text" class="form-control rounded-3 bg-light" readonly value="<?= esc($group['nama_kelompok']) ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">NAMA LENGKAP PETANI</label>
                        <input type="text" name="nama" class="form-control rounded-3" required placeholder="Contoh: Anton Subagyo">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">EMAIL</label>
                        <input type="email" name="email" class="form-control rounded-3" required placeholder="email@contoh.com" autocomplete="off">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">PASSWORD</label>
                        <div class="position-relative">
                            <input type="password" name="password" id="farmer-password" class="form-control rounded-3" required placeholder="Minimal 6 karakter">
                            <button type="button" class="btn position-absolute end-0 top-50 translate-middle-y border-0 bg-transparent text-muted pe-3" onclick="toggleFarmerPassword()">
                                <i class="fas fa-eye" id="eye-farmer-password"></i>
                            </button>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-muted">NOMOR TELEPON / WHATSAPP</label>
                        <input type="text" name="telepon" class="form-control rounded-3" required placeholder="08xxxxxxxxxx">
                    </div>
                    <button type="submit" class="btn btn-primary w-100 rounded-pill fw-bold py-2">Daftarkan Petani</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php if (isset($group)): ?>
<!-- Modal Edit Kelompok -->
<div class="modal fade" id="editGroupModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-800"><i class="fas fa-edit text-primary me-2"></i>Edit Kelompok Tani</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form action="<?= base_url('farmer-groups/update/'.$group['id_kelompok']) ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">NAMA KELOMPOK</label>
                        <input type="text" name="nama_kelompok" class="form-control rounded-3" required value="<?= esc($group['nama_kelompok']) ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">NAMA KETUA</label>
                        <input type="text" name="ketua" class="form-control rounded-3" required value="<?= esc($group['ketua']) ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">KECAMATAN / WILAYAH KERJA</label>
                        <input type="text" name="kecamatan" class="form-control rounded-3" required value="<?= esc($group['kecamatan']) ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">GAPOKTAN</label>
                        <input type="text" name="gapoktan" class="form-control rounded-3" value="<?= esc($group['gapoktan'] ?? '') ?>" placeholder="Nama Gabungan Kelompok Tani">
                    </div>
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-muted">KOMODITAS UTAMA</label>
                        <input type="text" name="komoditas" class="form-control rounded-3" value="<?= esc($group['komoditas'] ?? '') ?>" placeholder="Contoh: Padi, Jagung">
                    </div>
                    <button type="submit" class="btn btn-primary w-100 rounded-pill fw-bold py-2">Simpan Perubahan</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<style>
    .btn-xs { padding: 0.2rem 0.4rem; font-size: 0.8rem; }
    .table td { vertical-align: middle; }
    .leaflet-draw-toolbar a { background-color: white !important; }
    
    @media print {
        @page { size: A4 portrait; margin: 10mm; }
        html, body { height: auto !important; min-height: auto !important; background: white !important; font-family: 'Outfit', sans-serif !important; color: black !important; margin: 0; padding: 0; }

        .sidebar, .top-nav, .footer-mockup, .btn, .d-print-none, .alert, .modal { display: none !important; }
        .main-content { margin: 0 !important; padding: 0 !important; width: 100% !important; max-width: 100% !important; }
        .premium-card { border: none !important; box-shadow: none !important; padding: 0 !important; margin: 0 !important; }

        /* Kop Header */
        .print-header {
            display: flex !important;
            align-items: center;
            gap: 20px;
            background: #1e293b !important;
            color: white !important;
            padding: 15px 25px;
            border-radius: 12px;
            margin-bottom: 20px;
            -webkit-print-color-adjust: exact;
        }
        .print-logo-box {
            width: 50px; height: 50px;
            background: white;
            border-radius: 10px;
            display: flex !important; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .print-logo-box i { font-size: 24px; color: #1e293b; }
        .print-header-text h4 { margin: 0; font-weight: 800; font-size: 13pt; letter-spacing: 0.5px; }
        .print-header-text p { margin: 0; font-size: 8pt; opacity: 0.9; }

        .print-title { text-align: center; margin-bottom: 15px; }
        .print-title h2 { font-weight: 900; font-size: 14pt; text-transform: uppercase; margin-bottom: 5px; color: #0f172a; }
        .print-title p { font-size: 10pt; font-weight: 700; color: #334155; margin: 0; }

        .print-meta-table { width: 100% !important; font-weight: 800; font-size: 9pt; margin-bottom: 15px; border-bottom: 2px solid #e2e8f0; padding-bottom: 8px; color: #1e293b; border-collapse: collapse; }
        .print-meta-table td { padding: 0 8px; padding-bottom: 8px; }

        /* Table */
        .table { width: 100% !important; border-collapse: collapse !important; margin-bottom: 15px; }
        .table thead { background-color: #f1f5f9 !important; -webkit-print-color-adjust: exact; border-bottom: 2px solid #cbd5e1 !important; border-top: 2px solid #cbd5e1 !important; }
        .table th { color: #334155 !important; padding: 8px 6px !important; font-weight: 800 !important; font-size: 8.5pt; text-transform: uppercase; letter-spacing: 0.5px; border: none !important; }
        .table td { padding: 6px !important; border-bottom: 1px solid #e2e8f0 !important; border-left: none !important; border-right: none !important; border-top: none !important; font-size: 8.5pt; color: #1e293b; vertical-align: middle; }
        .table tbody tr:last-child td { border-bottom: 2px solid #cbd5e1 !important; }
        .badge { border: 1px solid #94a3b8 !important; color: #1e293b !important; background: transparent !important; font-size: 7.5pt !important; padding: 2px 6px !important; }

        /* Signature */
        .print-sig-table { width: 100% !important; margin-top: 30px; page-break-inside: avoid; border-collapse: collapse; table-layout: fixed; }
        .print-sig-table td { width: 33.33%; text-align: center; color: #1e293b; vertical-align: top; padding: 0; }
        .sig-title-block { height: 40px; margin-bottom: 50px; font-size: 10pt; line-height: 1.5; }
        .sig-name { font-weight: 800; text-decoration: underline; font-size: 10pt; }
        .sig-nip { font-size: 9pt; margin-top: 4px; }
    }
</style>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.css" />
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    function toggleFarmerPassword() {
        const input = document.getElementById('farmer-password');
        const eye = document.getElementById('eye-farmer-password');
        if (input.type === 'password') {
            input.type = 'text';
            eye.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            input.type = 'password';
            eye.classList.replace('fa-eye-slash', 'fa-eye');
        }
    }

    // Clear form fields when modal opens to prevent browser autofill
    const addfarmerModal = document.getElementById('addfarmerModal');
    if (addfarmerModal) {
        addfarmerModal.addEventListener('show.bs.modal', function() {
            const form = this.querySelector('form');
            if (form) {
                form.querySelectorAll('input:not([type="hidden"]):not([readonly])').forEach(function(input) {
                    input.value = '';
                });
                // Reset password field type
                const pwd = document.getElementById('farmer-password');
                if (pwd) pwd.type = 'password';
                const eye = document.getElementById('eye-farmer-password');
                if (eye) { eye.classList.remove('fa-eye-slash'); eye.classList.add('fa-eye'); }
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        const modalLat = document.getElementById('modal-lat');
        const modalLng = document.getElementById('modal-lng');
        const modalGeojson = document.getElementById('modal-geojson');
        const btnModalLoc = document.getElementById('btn-modal-location');

        function updateModalGeoJSON() {
            const lat = parseFloat(modalLat.value);
            const lng = parseFloat(modalLng.value);
            if (!isNaN(lat) && !isNaN(lng)) {
                modalGeojson.value = JSON.stringify({"type": "Point", "coordinates": [lng, lat]});
            }
        }

        if (modalLat) {
            modalLat.addEventListener('input', updateModalGeoJSON);
            modalLng.addEventListener('input', updateModalGeoJSON);
        }

        function handleGeoLocation(btn, latInput, lngInput, updateFn) {
            if (btn) {
                btn.addEventListener('click', function() {
                    const originalText = this.innerHTML;
                    this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Mendeteksi...';
                    this.disabled = true;

                    if ("geolocation" in navigator) {
                        navigator.geolocation.getCurrentPosition((position) => {
                            latInput.value = position.coords.latitude.toFixed(6);
                            lngInput.value = position.coords.longitude.toFixed(6);
                            updateFn();
                            this.innerHTML = '<i class="fas fa-check me-2"></i>Berhasil';
                            this.classList.replace('btn-outline-primary', 'btn-success');
                            setTimeout(() => {
                                this.innerHTML = originalText;
                                this.classList.replace('btn-success', 'btn-outline-primary');
                                this.disabled = false;
                            }, 2000);
                        }, (error) => {
                            alert("Gagal: " + error.message);
                            this.innerHTML = originalText;
                            this.disabled = false;
                        }, { enableHighAccuracy: true });
                    }
                });
            }
        }

        handleGeoLocation(btnModalLoc, modalLat, modalLng, updateModalGeoJSON);
    });
</script>
<?= $this->endSection() ?>
<?= $this->endSection() ?>
