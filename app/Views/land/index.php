<?= $this->extend('layouts/premium') ?>

<?= $this->section('styles') ?>
<style>
    /* Screen Styles */
    .premium-card {
        background: white;
        border-radius: 24px;
        padding: 30px;
        border: 1px solid var(--border-color);
        box-shadow: 0 4px 20px rgba(0,0,0,0.03);
    }

    /* Print Styles (Laporan Premium) */
    @media print {
        @page { size: A4 portrait; margin: 10mm; }
        html, body { height: auto !important; min-height: auto !important; background: white !important; font-family: 'Outfit', sans-serif !important; color: black !important; margin: 0; padding: 0; }
        
        .sidebar, .top-nav, .footer-mockup, .btn, .d-print-none, .page-title-area, .pagination-area {
            display: none !important;
        }
        
        .main-content { margin: 0 !important; padding: 0 !important; width: 100% !important; max-width: 100% !important; }
        .premium-card { border: none !important; box-shadow: none !important; padding: 0 !important; margin-bottom: 20px !important; }

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
        .table-formal-report th.text-center { text-align: center !important; }
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
        .stat-sig-table { width: 100% !important; margin-top: 20px; page-break-inside: avoid; border-collapse: collapse; table-layout: fixed; }
        .stat-sig-table td { width: 33.33%; text-align: center; color: #1e293b; vertical-align: top; padding: 0; }
        .sig-name { font-weight: 800; text-decoration: underline; margin-top: 40px; font-size: 10pt; }
        .sig-nip { font-size: 9pt; margin-top: 2px; }
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
        <h2>LAPORAN INVENTARISASI LAHAN BINAAN</h2>
        <p class="fw-bold">Wilayah Kerja Rajabasa • Periode: <?= date('F Y') ?></p>
    </div>
    
    <table class="stat-meta-table">
        <tr>
            <td style="text-align: left;">ID Dokumen: LAND/INV/<?= date('Ymd') ?></td>
            <td style="text-align: right;">Dicetak: <?= date('d/m/Y H:i') ?> WIB</td>
        </tr>
    </table>
</div>

<div class="premium-card">
    <div class="d-flex justify-content-between align-items-center mb-4 page-title-area">
        <div>
            <h5 class="fw-800 mb-0">Inventarisasi Lahan Binaan</h5>
            <p class="text-muted small mb-0">Total <?= count($lands) ?> plot lahan aktif dalam wilayah binaan.</p>
        </div>
        <div class="d-flex gap-3">
            <button type="button" onclick="window.print()" class="btn btn-light rounded-pill px-4 fw-bold small border d-print-none"><i class="fas fa-print me-2"></i>Cetak Laporan</button>
            <a href="<?= base_url('peta-gis') ?>" class="btn btn-success rounded-pill px-4 fw-bold d-print-none">
                <i class="fas fa-plus me-2"></i> Tambah Lahan Baru
            </a>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 table-formal-report">
            <thead>
                <tr>
                    <th>NAMA LAHAN</th>
                    <th>KOMODITAS</th>
                    <th class="text-center">LUAS (HA)</th>
                    <th class="text-center">FASE TANAM</th>
                    <th class="text-center">TANGGAL DAFTAR</th>
                    <th class="text-center d-print-none">AKSI</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($lands)): ?>
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">Belum ada lahan terdaftar.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($lands as $land): ?>
                        <tr>
                            <td>
                                <div class="fw-bold"><?= esc($land['nama_lahan']) ?></div>
                                <div class="small text-muted" style="font-size: 8pt;">ID: #<?= $land['id_lahan'] ?></div>
                            </td>
                            <td>
                                <span class="badge bg-primary bg-opacity-10 text-primary px-3 rounded-pill d-print-none"><?= esc(ucwords($land['komoditas'])) ?></span>
                                <span class="d-none d-print-block"><?= esc(ucwords($land['komoditas'])) ?></span>
                            </td>
                            <td class="text-center fw-bold"><?= number_format($land['luas'] ?? 0, 2) ?> ha</td>
                            <td class="text-center">
                                <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 d-print-none">
                                    <?= esc(ucwords(str_replace('_', ' ', $land['status_fase'] ?? 'Aktif'))) ?>
                                </span>
                                <span class="d-none d-print-block"><?= esc(ucwords(str_replace('_', ' ', $land['status_fase'] ?? 'Aktif'))) ?></span>
                            </td>
                            <td class="text-center"><?= date('d M Y', strtotime($land['created_at'])) ?></td>
                            <td class="text-center d-print-none">
                                <div class="btn-group">
                                    <a href="<?= base_url('land/detail/' . $land['id_lahan']) ?>" class="btn btn-light btn-sm border rounded-pill px-3 fw-bold me-1">Detail</a>
                                    <a href="<?= base_url('peta-gis') ?>?edit=<?= $land['id_lahan'] ?>" class="btn btn-light btn-sm border rounded-pill px-3 fw-bold me-1">Edit</a>
                                    <a href="javascript:void(0)" onclick="if(confirm('Hapus lahan ini?')) window.location.href='<?= base_url('farmer-groups/delete-land/' . $land['id_lahan']) ?>'" class="btn btn-outline-danger btn-sm rounded-pill px-3 fw-bold">Hapus</a>
                                </div>
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
    <table class="stat-sig-table">
        <tr>
            <td>
                <p>Mengetahui,<br>Analis Pertanian</p>
                <div class="sig-name">Analis Pertanian Utama</div>
                <p class="sig-nip">NIP. 19850215 201201 1 005</p>
            </td>
            <td>
                <p><br>Kabid Perencanaan Lahan</p>
                <div class="sig-name">Sari Wijaya, S.P.</div>
                <p class="sig-nip">NIP. 19750512 200501 2 003</p>
            </td>
            <td>
                <p><br>Kepala Dinas Pertanian</p>
                <div class="sig-name">Dr. Ir. Heru Santoso</div>
                <p class="sig-nip">NIP. 19680320 199403 1 002</p>
            </td>
        </tr>
    </table>
</div>
<?= $this->endSection() ?>
