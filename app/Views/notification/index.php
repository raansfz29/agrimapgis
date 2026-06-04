<?= $this->extend('layouts/premium') ?>

<?= $this->section('styles') ?>
<style>
    .notif-hero {
        background: linear-gradient(135deg, #155724 0%, #1e7e34 100%);
        border-radius: 24px;
        padding: 36px 40px;
        color: white;
        margin-bottom: 30px;
        position: relative;
        overflow: hidden;
    }
    .notif-hero::after {
        content: '';
        position: absolute;
        right: -50px; bottom: -50px;
        width: 220px; height: 220px;
        border-radius: 50%;
        background: rgba(255,255,255,0.05);
        pointer-events: none;
    }
    .filter-btn {
        border-radius: 100px;
        padding: 7px 20px;
        font-size: 12px;
        font-weight: 800;
        cursor: pointer;
        border: 1.5px solid #e2e8f0;
        background: white;
        color: #64748b;
        transition: all 0.2s;
    }
    .filter-btn.active {
        background: #1e293b;
        color: white;
        border-color: #1e293b;
        box-shadow: 0 4px 12px rgba(0,0,0,0.12);
    }
    .notif-item {
        border-radius: 16px;
        padding: 18px 20px;
        margin-bottom: 10px;
        display: flex;
        gap: 18px;
        align-items: flex-start;
        border: 1.5px solid #f1f5f9;
        background: white;
        cursor: pointer;
        transition: all 0.2s;
    }
    .notif-item:hover { transform: translateY(-1px); box-shadow: 0 8px 24px rgba(0,0,0,0.07); border-color: #cbd5e1; }
    .notif-item.unread { background: #f0fdf4; border-color: #bbf7d0; }
    .notif-item.read { background: #f8fafc; opacity: 0.8; }
    .notif-icon-wrap {
        width: 52px; height: 52px;
        border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
        font-size: 20px;
        flex-shrink: 0;
    }
    .notif-unread-dot {
        width: 10px; height: 10px;
        border-radius: 50%;
        background: #16a34a;
        flex-shrink: 0;
        margin-top: 6px;
        box-shadow: 0 0 0 3px rgba(22,163,74,0.15);
    }
    .section-label {
        font-size: 10px;
        font-weight: 800;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        margin: 20px 0 10px;
    }
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #94a3b8;
    }
    .empty-state i { font-size: 52px; margin-bottom: 16px; opacity: 0.25; }
    .action-bar {
        display: flex;
        gap: 10px;
        justify-content: flex-end;
    }
    .action-bar button {
        border-radius: 100px;
        padding: 8px 20px;
        font-size: 12px;
        font-weight: 800;
        cursor: pointer;
        border: 1.5px solid;
        transition: all 0.2s;
    }
    .btn-mark-all {
        background: #166534;
        color: white;
        border-color: #166534;
    }
    .btn-mark-all:hover { background: #15803d; }
    .btn-clear {
        background: white;
        color: #64748b;
        border-color: #e2e8f0;
    }
    .btn-clear:hover { background: #f1f5f9; }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Hero -->
<div class="notif-hero shadow-sm">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h2 class="fw-800 mb-1" style="font-size:26px;"><i class="fas fa-bell me-3" style="opacity:0.8;"></i>Pusat Notifikasi</h2>
            <p class="mb-0 fw-600" style="opacity:0.8; font-size:14px;">
                Kelola dan pantau semua aktivitas sistem Anda.
                <?php if ($unreadCount > 0): ?>
                    <span class="ms-2 badge rounded-pill" style="background:rgba(255,255,255,0.25); font-size:12px;">
                        <?= $unreadCount ?> belum dibaca
                    </span>
                <?php endif; ?>
            </p>
        </div>
        <div class="col-md-4 text-md-end mt-3 mt-md-0">
            <div class="action-bar justify-content-md-end">
                <button class="btn-mark-all" id="btnMarkAll" onclick="markAllRead()">
                    <i class="fas fa-check-double me-1"></i>Tandai Semua Dibaca
                </button>
                <button class="btn-clear" id="btnClear" onclick="clearRead()">
                    <i class="fas fa-trash-alt me-1"></i>Hapus Yang Sudah Dibaca
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Filter Tabs -->
<div class="d-flex gap-2 mb-4" id="filterBar">
    <button class="filter-btn active" data-filter="semua">Semua <span class="ms-1 badge rounded-pill bg-dark" style="font-size:10px;"><?= count($notifications) ?></span></button>
    <button class="filter-btn" data-filter="laporan">Laporan</button>
    <button class="filter-btn" data-filter="peringatan">Peringatan</button>
    <button class="filter-btn" data-filter="sistem">Sistem</button>
</div>

<!-- Notification List -->
<div class="premium-card p-4" id="notifContainer">

    <?php if (empty($notifications)): ?>
        <div class="empty-state">
            <i class="fas fa-bell-slash d-block"></i>
            <h6 class="fw-800 text-muted">Tidak Ada Notifikasi</h6>
            <p class="small text-muted">Semua notifikasi akan muncul di sini.</p>
        </div>
    <?php else: ?>

        <!-- Unread Section -->
        <?php $unreads = array_filter($notifications, fn($n) => !$n['is_read']); ?>
        <?php if (!empty($unreads)): ?>
            <div class="section-label" data-section="unread">Belum Dibaca (<?= count($unreads) ?>)</div>
            <?php foreach ($unreads as $notif): ?>
            <div class="notif-item unread" data-category="<?= esc($notif['category']) ?>" data-id="<?= $notif['id'] ?>"
                 onclick="readAndMark(<?= $notif['id'] ?>, this)">
                <div class="notif-icon-wrap bg-<?= esc($notif['color']) ?> bg-opacity-10 text-<?= esc($notif['color']) ?>">
                    <i class="<?= esc($notif['icon']) ?>"></i>
                </div>
                <div class="flex-grow-1 min-w-0">
                    <div class="d-flex justify-content-between align-items-start mb-1">
                        <h6 class="fw-800 mb-0" style="font-size:14px; color:#0f172a;"><?= esc($notif['title']) ?></h6>
                        <span class="text-muted fw-700 ms-3 text-nowrap" style="font-size:10px; text-transform:uppercase;"><?= esc($notif['time']) ?></span>
                    </div>
                    <p class="text-muted mb-0 fw-600" style="font-size:13px; line-height:1.5;"><?= esc($notif['message']) ?></p>
                </div>
                <div class="notif-unread-dot"></div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <!-- Read Section -->
        <?php $reads = array_filter($notifications, fn($n) => $n['is_read']); ?>
        <?php if (!empty($reads)): ?>
            <div class="section-label mt-4" data-section="read">Sudah Dibaca (<?= count($reads) ?>)</div>
            <?php foreach ($reads as $notif): ?>
            <div class="notif-item read" data-category="<?= esc($notif['category']) ?>" data-id="<?= $notif['id'] ?>">
                <div class="notif-icon-wrap bg-<?= esc($notif['color']) ?> bg-opacity-10 text-<?= esc($notif['color']) ?>" style="opacity:0.6;">
                    <i class="<?= esc($notif['icon']) ?>"></i>
                </div>
                <div class="flex-grow-1 min-w-0">
                    <div class="d-flex justify-content-between align-items-start mb-1">
                        <h6 class="fw-700 mb-0 text-muted" style="font-size:14px;"><?= esc($notif['title']) ?></h6>
                        <span class="text-muted fw-700 ms-3 text-nowrap" style="font-size:10px; text-transform:uppercase;"><?= esc($notif['time']) ?></span>
                    </div>
                    <p class="text-muted mb-0" style="font-size:13px; font-weight:500; line-height:1.5;"><?= esc($notif['message']) ?></p>
                </div>
                <i class="fas fa-check text-success mt-1 flex-shrink-0" style="font-size:12px;"></i>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>

    <?php endif; ?>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    // Filter tabs
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            const filter = this.dataset.filter;
            document.querySelectorAll('.notif-item').forEach(item => {
                const match = filter === 'semua' || item.dataset.category === filter;
                item.style.display = match ? 'flex' : 'none';
            });
            // Section labels
            document.querySelectorAll('.section-label').forEach(label => {
                const sibling = label.nextElementSibling;
                label.style.display = sibling && sibling.style.display !== 'none' ? 'block' : 'none';
            });
        });
    });

    // Mark single as read
    function readAndMark(id, el) {
        if (el.classList.contains('read')) return;
        fetch(`<?= base_url('notification/mark-read/') ?>${id}`)
            .then(() => {
                window.location.reload();
            });
    }

    // Mark all as read
    function markAllRead() {
        fetch('<?= base_url('notification/mark-all-read') ?>', {
            method: 'POST',
            headers: { '<?= csrf_token() ?>': '<?= csrf_hash() ?>' }
        }).then(() => {
            window.location.reload();
        });
    }

    // Clear read notifications
    function clearRead() {
        if (!confirm('Hapus semua notifikasi yang sudah dibaca?')) return;
        fetch('<?= base_url('notification/clear-all') ?>', {
            method: 'POST',
            headers: { '<?= csrf_token() ?>': '<?= csrf_hash() ?>' }
        }).then(() => {
            window.location.reload();
        });
    }
</script>
<?= $this->endSection() ?>
