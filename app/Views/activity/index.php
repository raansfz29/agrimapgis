<?= $this->extend('layouts/premium') ?>

<?= $this->section('styles') ?>
<style>
    .activity-hero {
        background: linear-gradient(135deg, #15803d 0%, #166534 100%);
        border-radius: 24px;
        padding: 32px 40px;
        color: white;
        margin-bottom: 24px;
        position: relative;
        overflow: hidden;
    }
    .activity-hero::after {
        content: ''; position: absolute;
        right: -50px; bottom: -50px;
        width: 220px; height: 220px;
        border-radius: 50%;
        background: rgba(255,255,255,0.05);
        pointer-events: none;
    }
    .btn-hero {
        background: #f0fdf4;
        color: #15803d;
        border: none;
        border-radius: 14px;
        padding: 12px 24px;
        font-weight: 800;
        font-size: 14px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
    }
    .btn-hero:hover { background: #ffffff; transform: translateY(-2px); box-shadow: 0 8px 20px rgba(0,0,0,0.12); color: #15803d; }

    .stat-mini {
        background: white; border-radius: 16px;
        padding: 16px 20px; border: 1px solid #e2e8f0;
        display: flex; align-items: center; gap: 14px;
    }
    .stat-icon {
        width: 40px; height: 40px; border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 18px; flex-shrink: 0;
    }

    /* Table */
    .list-card {
        background: white; border-radius: 20px;
        border: 1px solid #e2e8f0; overflow: hidden;
    }
    .activity-row { border-bottom: 1px solid #f1f5f9; transition: background 0.12s; }
    .activity-row:hover { background: #f8fafc; }
    .activity-row:last-child { border-bottom: none; }
    .activity-row td { vertical-align: middle; padding: 11px 10px; }
    .activity-row td:first-child { padding-left: 20px; }
    .activity-row td:last-child  { padding-right: 16px; }

    /* Status badges */
    .status-badge {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 5px 12px; border-radius: 100px;
        font-size: 10px; font-weight: 800;
        text-transform: uppercase; letter-spacing: 0.5px;
        white-space: nowrap;
    }
    .status-approved { background: #dcfce7; color: #166534; }
    .status-pending  { background: #fef9c3; color: #854d0e; }
    .status-rejected { background: #fee2e2; color: #991b1b; }

    /* Action buttons — fixed 2-slot layout */
    .aksi-wrap {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        flex-wrap: nowrap;
    }
    /* Each slot is a fixed width so buttons never shift */
    .aksi-slot {
        width: 90px;
        display: flex;
        justify-content: center;
    }
    .btn-detail {
        display: inline-flex; align-items: center; justify-content: center; gap: 5px;
        width: 100%; padding: 6px 10px; border-radius: 10px;
        font-size: 12px; font-weight: 800;
        background: #f8fafc; color: #475569;
        border: 1.5px solid #e2e8f0;
        text-decoration: none; white-space: nowrap;
        transition: all 0.15s;
    }
    .btn-detail:hover { background: #f1f5f9; color: #0f172a; border-color: #cbd5e1; }
    .btn-perbaiki {
        display: inline-flex; align-items: center; justify-content: center; gap: 5px;
        width: 100%; padding: 6px 10px; border-radius: 10px;
        font-size: 12px; font-weight: 800;
        background: #fef3c7; color: #92400e;
        border: 1.5px solid #fde68a;
        text-decoration: none; white-space: nowrap;
        transition: all 0.15s;
    }
    .btn-perbaiki:hover { background: #fde68a; color: #78350f; }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Hero -->
<div class="activity-hero shadow-sm">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h2 class="fw-800 mb-1" style="font-size:24px;">Aktivitas Pertanian</h2>
            <p class="mb-0 fw-600" style="opacity:0.8; font-size:13px;">Riwayat pengerjaan lahan &amp; status verifikasi dari petugas lapangan.</p>
        </div>
        <a href="<?= base_url('activity/input') ?>" class="btn-hero">
            <i class="fas fa-plus-circle"></i> Input Aktivitas Baru
        </a>
    </div>
</div>

<!-- Stats -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="stat-mini shadow-sm">
            <div class="stat-icon bg-success bg-opacity-10 text-success"><i class="fas fa-check-circle"></i></div>
            <div>
                <div class="text-muted fw-800" style="font-size:9px; text-transform:uppercase; letter-spacing:1px;">Total Aktivitas</div>
                <div class="fw-800" style="font-size:22px; line-height:1.2;"><?= $totalActivities ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-mini shadow-sm">
            <div class="stat-icon bg-warning bg-opacity-10 text-warning"><i class="fas fa-clock"></i></div>
            <div>
                <div class="text-muted fw-800" style="font-size:9px; text-transform:uppercase; letter-spacing:1px;">Menunggu Verifikasi</div>
                <div class="fw-800" style="font-size:22px; line-height:1.2;"><?= $pendingCount ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-mini shadow-sm">
            <div class="stat-icon bg-danger bg-opacity-10 text-danger"><i class="fas fa-exclamation-circle"></i></div>
            <div>
                <div class="text-muted fw-800" style="font-size:9px; text-transform:uppercase; letter-spacing:1px;">Butuh Perbaikan</div>
                <div class="fw-800" style="font-size:22px; line-height:1.2;"><?= $flaggedCount ?></div>
            </div>
        </div>
    </div>
</div>

<!-- Activity Table -->
<div class="list-card shadow-sm">
    <!-- Card Header -->
    <div class="px-4 py-3 border-bottom d-flex justify-content-between align-items-center">
        <h6 class="fw-800 mb-0">Riwayat Aktivitas</h6>
        <div class="dropdown">
            <button class="btn btn-light btn-sm fw-800 rounded-3 border px-3" type="button" data-bs-toggle="dropdown">
                <i class="fas fa-filter me-2 text-muted"></i>
                <?= $filterStatus ? ucfirst($filterStatus) : 'Semua Status' ?>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3">
                <li><a class="dropdown-item fw-700" href="<?= base_url('activity') ?>">Semua Status</a></li>
                <li><a class="dropdown-item fw-700" href="<?= base_url('activity?status=approved') ?>">Disetujui</a></li>
                <li><a class="dropdown-item fw-700" href="<?= base_url('activity?status=pending') ?>">Menunggu</a></li>
                <li><a class="dropdown-item fw-700" href="<?= base_url('activity?status=rejected') ?>">Ditolak</a></li>
            </ul>
        </div>
    </div>

    <!-- Table -->
    <div class="table-responsive">
        <table class="table table-borderless mb-0" style="table-layout: fixed; width: 100%;">
            <colgroup>
                <col style="width: 28%;">  <!-- JENIS AKTIVITAS -->
                <col style="width: 14%;">  <!-- PETANI -->
                <col style="width: 13%;">  <!-- TANGGAL -->
                <col style="width: 17%;">  <!-- LAHAN -->
                <col style="width: 12%;">  <!-- STATUS -->
                <col style="width: 16%;">  <!-- AKSI -->
            </colgroup>
            <thead style="background:#f8fafc;">
                <tr>
                    <th class="ps-4 py-3 fw-800 text-muted" style="font-size:10px; letter-spacing:1px;">JENIS AKTIVITAS</th>
                    <th class="py-3 fw-800 text-muted" style="font-size:10px; letter-spacing:1px;">PETANI</th>
                    <th class="py-3 fw-800 text-muted" style="font-size:10px; letter-spacing:1px;">TANGGAL</th>
                    <th class="py-3 fw-800 text-muted" style="font-size:10px; letter-spacing:1px;">LAHAN</th>
                    <th class="py-3 fw-800 text-muted" style="font-size:10px; letter-spacing:1px;">STATUS</th>
                    <th class="py-3 pe-4 fw-800 text-muted text-center" style="font-size:10px; letter-spacing:1px;">AKSI</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($activities)): ?>
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <div class="opacity-20 mb-3"><i class="fas fa-folder-open" style="font-size:40px;"></i></div>
                            <h6 class="fw-700 text-muted">Belum ada aktivitas tercatat</h6>
                            <p class="small text-muted mb-0">Mulai dengan menambahkan aktivitas baru pada lahan Anda.</p>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($activities as $activity): ?>
                    <?php
                        // Parse deskripsi — only show Catatan part
                        $desc = $activity['deskripsi'] ?? '';
                        if (preg_match('/Catatan:\s*(.*)/s', $desc, $dm)) $desc = trim($dm[1]);
                        $desc = trim(preg_replace('/\[.*?\]/s', '', $desc));
                    ?>
                    <tr class="activity-row">
                        <!-- Jenis Aktivitas -->
                        <td class="ps-4">
                            <div class="d-flex align-items-center gap-2">
                                <div class="bg-success bg-opacity-10 text-success rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                                     style="width:34px; height:34px; font-size:13px;">
                                    <i class="fas fa-clipboard-check"></i>
                                </div>
                                <div style="overflow:hidden; min-width:0;">
                                    <div class="fw-800 text-dark text-truncate" style="font-size:13px;"><?= esc(ucwords($activity['jenis_aktivitas'])) ?></div>
                                    <div class="text-muted fw-600 text-truncate" style="font-size:11px;"><?= esc(mb_strimwidth($desc, 0, 40, '...')) ?></div>
                                </div>
                            </div>
                        </td>

                        <!-- Petani -->
                        <td>
                            <div class="fw-700 text-dark" style="font-size:13px;"><?= esc($activity['nama_petani'] ?? 'Anggota') ?></div>
                        </td>

                        <!-- Tanggal -->
                        <td>
                            <div class="fw-700 text-dark" style="font-size:12px;"><?= date('d M Y', strtotime($activity['tanggal'])) ?></div>
                            <div class="text-muted fw-600" style="font-size:10px;"><?= date('H:i', strtotime($activity['created_at'])) ?> WIB</div>
                        </td>

                        <!-- Lahan -->
                        <td>
                            <span class="badge bg-light text-dark border fw-700 px-2 py-1" style="font-size:11px; white-space:nowrap;">
                                <i class="fas fa-map-marker-alt me-1 text-success"></i><?= esc($activity['nama_lahan']) ?>
                            </span>
                        </td>

                        <!-- Status -->
                        <td>
                            <?php if ($activity['status'] === 'approved'): ?>
                                <span class="status-badge status-approved"><i class="fas fa-check-circle"></i>Disetujui</span>
                            <?php elseif ($activity['status'] === 'rejected'): ?>
                                <span class="status-badge status-rejected"><i class="fas fa-times-circle"></i>Ditolak</span>
                            <?php else: ?>
                                <span class="status-badge status-pending"><i class="fas fa-clock"></i>Menunggu</span>
                            <?php endif; ?>
                        </td>

                        <!-- Aksi -->
                        <td class="pe-4 text-center">
                            <div class="aksi-wrap">
                                <!-- Slot kiri: selalu Detail -->
                                <div class="aksi-slot">
                                    <a href="<?= base_url('activity/detail/' . $activity['id_aktivitas']) ?>" class="btn-detail">
                                        Detail <i class="fas fa-arrow-right"></i>
                                    </a>
                                </div>
                                <!-- Slot kanan: Perbaiki jika ditolak, kosong jika tidak -->
                                <div class="aksi-slot">
                                    <?php if ($activity['status'] === 'rejected' && session()->get('role') === 'petani'): ?>
                                    <a href="<?= base_url('activity/edit/' . $activity['id_aktivitas']) ?>" class="btn-perbaiki">
                                        <i class="fas fa-pen-to-square"></i>Perbaiki
                                    </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
    <div class="px-4 py-3 border-top d-flex justify-content-between align-items-center" style="background:#f8fafc;">
        <div class="text-muted small fw-700">Halaman <?= $currentPage ?> / <?= $totalPages ?></div>
        <nav>
            <ul class="pagination pagination-sm mb-0 gap-1">
                <li class="page-item <?= $currentPage <= 1 ? 'disabled' : '' ?>">
                    <a class="page-link border-0 rounded-2 fw-800"
                       href="<?= base_url('activity?page=' . ($currentPage - 1) . ($filterStatus ? '&status='.$filterStatus : '')) ?>">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                </li>
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?= $i === $currentPage ? 'active' : '' ?>">
                        <a class="page-link border-0 rounded-2 fw-800 <?= $i === $currentPage ? 'bg-success text-white' : '' ?>"
                           href="<?= base_url('activity?page=' . $i . ($filterStatus ? '&status='.$filterStatus : '')) ?>">
                           <?= $i ?>
                        </a>
                    </li>
                <?php endfor; ?>
                <li class="page-item <?= $currentPage >= $totalPages ? 'disabled' : '' ?>">
                    <a class="page-link border-0 rounded-2 fw-800"
                       href="<?= base_url('activity?page=' . ($currentPage + 1) . ($filterStatus ? '&status='.$filterStatus : '')) ?>">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>