<?= $this->extend('layouts/premium') ?>

<?php $title = 'Monitoring Aktivitas'; ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
    /* Screen Styles */
    .filter-card { background: white; border-radius: 20px; padding: 25px; border: 1px solid var(--border-color); margin-bottom: 30px; box-shadow: 0 4px 15px rgba(0,0,0,0.02); }
    .filter-label { font-size: 11px; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px; display: block; }
    .premium-table-card { background: white; border-radius: 24px; border: 1px solid var(--border-color); overflow: hidden; margin-bottom: 30px; }
    .premium-table th { background: #f8fafc; padding: 20px; font-size: 11px; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 1px; border-bottom: 1px solid var(--border-color); }
    .premium-table td { padding: 20px; vertical-align: middle; border-bottom: 1px solid #f1f5f9; }
    .badge-status { font-size: 10px; font-weight: 800; padding: 6px 12px; border-radius: 8px; text-transform: uppercase; }
    .badge-verified { background: #dcfce7; color: #166534; }
    .badge-pending { background: #fef9c3; color: #854d0e; }
    .badge-flagged { background: #fee2e2; color: #991b1b; }
    .chart-container { background: white; border-radius: 24px; padding: 30px; border: 1px solid var(--border-color); height: 100%; }
    .attention-card { background: #1e3a1f; border-radius: 24px; padding: 30px; color: white; height: 100%; position: relative; overflow: hidden; }
    .btn-attention { background: #fde68a; color: #1e293b; border: none; width: 100%; padding: 15px; border-radius: 12px; font-weight: 800; margin-top: 20px; display: block; text-align: center; text-decoration: none; }

    /* Off-screen Print Chart Container (so ChartJS can render it before printing) */
    .print-chart-container-wrapper {
        position: absolute;
        left: -9999px;
        top: 0;
        visibility: hidden;
        width: 800px;
        height: 300px;
    }

    /* Print Styles (Laporan Premium) */
    @media print {
        @page { size: A4 portrait; margin: 10mm; }
        html, body { height: auto !important; min-height: auto !important; background: white !important; font-family: 'Outfit', sans-serif !important; color: black !important; margin: 0; padding: 0; }
        
        .sidebar, .top-nav, .footer-mockup, .btn, .d-print-none, .page-title-area, .filter-card, .pagination-area { display: none !important; }
        .main-content { margin: 0 !important; padding: 0 !important; width: 100% !important; max-width: 100% !important; }
        .premium-table-card { border: none !important; box-shadow: none !important; padding: 0 !important; margin-bottom: 20px !important; }

        /* Blue Header */
        .stat-header {
            background: #1e293b !important;
            color: white !important;
            padding: 15px 25px;
            display: flex !important;
            align-items: center;
            gap: 20px;
            -webkit-print-color-adjust: exact;
            margin-bottom: 20px;
            border-radius: 12px;
        }
        .stat-logo-box {
            width: 50px; height: 50px;
            background: white;
            border-radius: 10px;
            display: flex !important; align-items: center; justify-content: center;
        }
        .stat-logo-box i { font-size: 24px; color: #1e293b; }
        .stat-header-text h4 { margin: 0; font-weight: 800; font-size: 13pt; letter-spacing: 0.5px; }
        .stat-header-text p { margin: 0; font-size: 8pt; opacity: 0.9; }

        .stat-title { text-align: center; margin-bottom: 15px; }
        .stat-title h2 { font-weight: 900; font-size: 14pt; text-transform: uppercase; margin-bottom: 5px; color: #0f172a; }

        .stat-meta-table { width: 100% !important; font-weight: 800; font-size: 9pt; margin-bottom: 15px; border-bottom: 2px solid #e2e8f0; padding-bottom: 8px; color: #1e293b; border-collapse: collapse; }
        .stat-meta-table td { padding: 0 8px; padding-bottom: 8px; }

        /* Professional Table Style */
        .table-formal-report { width: 100% !important; border-collapse: collapse !important; margin-bottom: 15px; }
        .table-formal-report thead { background-color: #f1f5f9 !important; -webkit-print-color-adjust: exact; border-bottom: 2px solid #cbd5e1 !important; border-top: 2px solid #cbd5e1 !important; }
        .table-formal-report th {
            color: #334155 !important;
            padding: 8px 6px !important;
            font-weight: 800 !important;
            text-align: left;
            font-size: 8.5pt;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border: none !important;
        }
        .table-formal-report th.text-center, .table-formal-report td.text-center { text-align: center !important; }
        .table-formal-report td { 
            padding: 6px 6px !important; 
            border-bottom: 1px solid #e2e8f0 !important; 
            border-left: none !important;
            border-right: none !important;
            border-top: none !important;
            font-size: 8.5pt; 
            color: #1e293b;
            vertical-align: middle;
        }
        .table-formal-report tbody tr:last-child td { border-bottom: 2px solid #cbd5e1 !important; }

        /* Signature Area */
        .stat-sig-table { width: 100% !important; margin-top: 30px; page-break-inside: avoid; border-collapse: collapse; table-layout: fixed; }
        .stat-sig-table td { width: 33.33%; text-align: center; color: #1e293b; vertical-align: top; padding: 0; }
        .sig-title { height: 40px; margin-bottom: 50px; font-size: 10pt; line-height: 1.3; }
        .sig-name { font-weight: 800; text-decoration: underline; font-size: 10pt; }
        .sig-nip { font-size: 9pt; margin-top: 4px; }
        
        .print-chart-container-wrapper {
            position: static !important;
            visibility: visible !important;
            width: 100% !important;
            height: auto !important;
        }
        
        .print-chart-container { margin: 15px 0; page-break-inside: avoid; text-align: center; border: 1px solid #e2e8f0; padding: 15px; border-radius: 12px; }
        .print-chart-title { font-weight: 800; font-size: 10pt; margin-bottom: 10px; text-transform: uppercase; color: #334155; letter-spacing: 0.5px; }
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Formal Print Header (New Premium Style) -->
<div class="d-none d-print-block">
    <div class="stat-header">
        <div class="stat-logo-box">
            <i class="fas fa-seedling"></i>
        </div>
        <div class="stat-header-text">
            <h4>PEMERINTAH KOTA BANDAR LAMPUNG</h4>
            <p>DINAS PERTANIAN DAN KETAHANAN PANGAN</p>
            <p>Jl. Dr. Susilo No.1, Bandar Lampung, Lampung. Telp: (0721) 252300</p>
        </div>
    </div>

    <div class="stat-title">
        <h2>LAPORAN MONITORING AKTIVITAS PERTANIAN</h2>
        <p class="fw-bold">Wilayah Kerja Rajabasa • Periode: <?= date('F Y') ?></p>
    </div>
    
    <table class="stat-meta-table">
        <tr>
            <td style="text-align: left;">Nomor : <?= rand(100, 999) ?>/LP-MAP/<?= date('Y') ?></td>
            <td style="text-align: right;">Dicetak: <?= date('d/m/Y H:i') ?> WIB</td>
        </tr>
    </table>
</div>

<div class="d-print-none">
    <div class="d-flex justify-content-between align-items-end mb-4 page-title-area">
        <div>
            <h3 class="fw-800 mb-1">Monitoring Aktivitas</h3>
            <p class="text-muted small mb-0">Pantau progress pengerjaan lahan di wilayah Rajabasa secara real-time.</p>
        </div>
        <div class="d-flex gap-3">
            <a href="<?= base_url('activity/verification') ?>" class="btn btn-white rounded-3 px-4 fw-bold border"><i class="fas fa-sync-alt me-2"></i> Reset</a>
            <button type="button" onclick="window.print()" class="btn btn-dark rounded-3 px-4 fw-bold"><i class="fas fa-print me-2"></i> Cetak Laporan</button>
        </div>
    </div>

    <form method="GET" action="<?= base_url('activity/verification') ?>" class="filter-card">
        <div class="row g-4">
            <div class="col-md-4">
                <span class="filter-label">Rentang Tanggal</span>
                <input type="date" name="date" class="form-control bg-light border-0 py-2 fw-bold" value="<?= esc(service('request')->getGet('date') ?? '') ?>" onchange="this.form.submit()">
            </div>
            <div class="col-md-4">
                <span class="filter-label">Jenis Aktivitas</span>
                <select name="type" class="form-select bg-light border-0 py-2 fw-bold" onchange="this.form.submit()">
                    <option value="">Semua Aktivitas</option>
                    <option value="penanaman" <?= service('request')->getGet('type') == 'penanaman' ? 'selected' : '' ?>>Penanaman</option>
                    <option value="pemupukan" <?= service('request')->getGet('type') == 'pemupukan' ? 'selected' : '' ?>>Pemupukan</option>
                    <option value="panen" <?= service('request')->getGet('type') == 'panen' ? 'selected' : '' ?>>Panen</option>
                </select>
            </div>
            <div class="col-md-4">
                <span class="filter-label">Status Verifikasi</span>
                <select name="status" class="form-select bg-light border-0 py-2 fw-bold" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    <option value="approved" <?= isset($filterStatus) && $filterStatus == 'approved' ? 'selected' : '' ?>>Verified</option>
                    <option value="pending" <?= isset($filterStatus) && $filterStatus == 'pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="rejected" <?= isset($filterStatus) && $filterStatus == 'rejected' ? 'selected' : '' ?>>Flagged</option>
                </select>
            </div>
        </div>
    </form>
</div>

<div class="premium-table-card shadow-sm">
    <div class="p-4 border-bottom d-flex justify-content-between align-items-center d-print-none">
        <h5 class="fw-800 mb-0">Daftar Aktivitas Terkini</h5>
        <span class="text-muted small fw-700">
            Menampilkan <?= (($currentPage - 1) * $perPage) + 1 ?>–<?= min($currentPage * $perPage, $totalActivities) ?> dari <?= $totalActivities ?> entri
        </span>
    </div>
    <div class="table-responsive">
        <table class="table premium-table mb-0 table-formal-report">
            <thead>
                <tr>
                    <th>TANGGAL</th>
                    <th>PETANI / KELOMPOK</th>
                    <th>JENIS AKTIVITAS</th>
                    <th>LOKASI / PLOT</th>
                    <th class="text-center">STATUS</th>
                    <th class="text-center d-print-none">AKSI</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($activities as $act): ?>
                <tr>
                    <td class="text-center"><?= date('d/m/y', strtotime($act['created_at'])) ?></td>
                    <td>
                        <div class="fw-bold"><?= esc($act['nama_petani']) ?></div>
                        <div class="small text-muted" style="font-size: 8pt;">Klp: <?= esc($act['nama_lahan']) ?></div>
                    </td>
                    <td><?= esc(ucfirst($act['jenis_aktivitas'])) ?></td>
                    <td>
                        <div class="fw-bold"><?= esc($act['nama_lahan']) ?></div>
                        <div class="small text-muted" style="font-size: 8pt;">Plot #L-<?= str_pad($act['id_lahan'], 3, '0', STR_PAD_LEFT) ?></div>
                    </td>
                    <td class="text-center">
                        <span class="badge-status d-print-none <?= $act['status'] == 'approved' ? 'badge-verified' : ($act['status'] == 'rejected' ? 'badge-flagged' : 'badge-pending') ?>">
                            <?= strtoupper($act['status'] == 'approved' ? 'VERIFIED' : ($act['status'] == 'rejected' ? 'FLAGGED' : 'PENDING')) ?>
                        </span>
                        <span class="d-none d-print-block fw-bold"><?= strtoupper($act['status']) ?></span>
                    </td>
                    <td class="text-center d-print-none">
                        <a href="<?= base_url('activity/detail/' . $act['id_aktivitas']) ?>" class="btn btn-light btn-sm rounded-pill px-3 border fw-800 x-small">DETAIL</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="p-4 bg-light bg-opacity-50 border-top d-flex justify-content-between align-items-center pagination-area d-print-none">
        <div class="btn-group">
            <?php if ($currentPage > 1): ?>
                <a href="?page=<?= $currentPage - 1 ?>" class="btn btn-white btn-sm border fw-bold px-3">Sebelumnya</a>
            <?php else: ?>
                <button class="btn btn-white btn-sm border fw-bold px-3" disabled>Sebelumnya</button>
            <?php endif; ?>
        </div>
        <div class="d-flex gap-2">
            <?php
            $start = max(1, $currentPage - 2);
            $end   = min($totalPages, $currentPage + 2);
            for ($p = $start; $p <= $end; $p++):
            ?>
                <a href="?page=<?= $p ?>" class="btn btn-sm rounded-2 fw-bold px-3 <?= $p == $currentPage ? 'btn-dark' : 'btn-white border' ?>"><?= $p ?></a>
            <?php endfor; ?>
        </div>
        <div class="btn-group">
            <?php if ($currentPage < $totalPages): ?>
                <a href="?page=<?= $currentPage + 1 ?>" class="btn btn-white btn-sm border fw-bold px-3">Berikutnya</a>
            <?php else: ?>
                <button class="btn btn-white btn-sm border fw-bold px-3" disabled>Berikutnya</button>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Print-only Chart -->
<div class="print-chart-container-wrapper">
    <div class="print-chart-container">
        <div class="print-chart-title">Distribusi Aktivitas per Wilayah</div>
        <div style="text-align: center; margin: 0 auto;">
            <canvas id="activityChartPrint" width="700" height="220" style="max-width: 100%; height: auto;"></canvas>
        </div>
    </div>
</div>

<!-- Print-only Signature Area -->
<div class="d-none d-print-block">
    <table class="stat-sig-table">
        <tr>
            <td>
                <div class="sig-title">Mengetahui,<br>Analis Pertanian</div>
                <div class="sig-name">Analis Pertanian Utama</div>
                <div class="sig-nip">NIP. 19850215 201201 1 005</div>
            </td>
            <td>
                <div class="sig-title"><br>Kabid Monitoring Pertanian</div>
                <div class="sig-name">Sari Wijaya, S.P.</div>
                <div class="sig-nip">NIP. 19750512 200501 2 003</div>
            </td>
            <td>
                <div class="sig-title"><br>Kepala Dinas Pertanian</div>
                <div class="sig-name">Dr. Ir. Heru Santoso</div>
                <div class="sig-nip">NIP. 19680320 199403 1 002</div>
            </td>
        </tr>
    </table>
</div>

<div class="row g-4 d-print-none mt-2">
    <div class="col-md-8">
        <div class="chart-container shadow-sm">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h6 class="fw-800 mb-0">Distribusi Aktivitas</h6>
                <span class="text-muted small fw-800">Per Kelompok Tani</span>
            </div>
            <div style="height: 220px;">
                <canvas id="activityChart"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="attention-card shadow-sm">
            <h5 class="fw-800 mb-2">Butuh Perhatian</h5>
            <p class="opacity-70 small mb-4"><?= $flaggedCount ?> Aktivitas ditandai (Flagged)</p>
            <div class="bg-white bg-opacity-10 p-3 rounded-4 mb-3 d-flex justify-content-between align-items-center border border-white border-opacity-10">
                <span class="small fw-800">Verifikasi Menunggu</span>
                <span class="h3 fw-800 mb-0"><?= $pendingCount ?></span>
            </div>
            <div class="bg-white bg-opacity-10 p-3 rounded-4 mb-4 d-flex justify-content-between align-items-center border border-white border-opacity-10">
                <span class="small fw-800">Ditolak / Flagged</span>
                <span class="h3 fw-800 mb-0 text-warning"><?= $flaggedCount ?></span>
            </div>
            <a href="<?= base_url('activity/verification') ?>?status=pending" class="btn-attention shadow-lg">Cek Sekarang</a>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Activity Chart (Screen)
    const ctx = document.getElementById('activityChart').getContext('2d');
    const gradient = ctx.createLinearGradient(0, 0, 0, 200);
    gradient.addColorStop(0, '#1e3a1f');
    gradient.addColorStop(1, '#4ade80');

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?= json_encode($distLabels) ?>,
            datasets: [{
                label: 'Jumlah Aktivitas',
                data: <?= json_encode($distData) ?>,
                backgroundColor: gradient,
                borderRadius: 8,
                barPercentage: 0.6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { font: { size: 11 } } },
                x: { grid: { display: false }, ticks: { font: { size: 11, weight: 'bold' } } }
            }
        }
    });

    // Activity Chart (Print)
    new Chart(document.getElementById('activityChartPrint').getContext('2d'), {
        type: 'bar',
        data: {
            labels: <?= json_encode($distLabels) ?>,
            datasets: [{
                data: <?= json_encode($distData) ?>,
                backgroundColor: '#1e3a5f',
                borderRadius: 4
            }]
        },
        options: {
            responsive: false,
            maintainAspectRatio: true,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, grid: { color: '#eee' }, ticks: { font: { size: 9 } } },
                x: { grid: { display: false }, ticks: { font: { size: 9 } } }
            },
            animation: false
        }
    });
});
</script>
<?= $this->endSection() ?>
