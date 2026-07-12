<?= $this->extend('layouts/premium') ?>

<?= $this->section('styles') ?>
<style>
    /* Standard Interactive Screen Styles */
    .premium-card {
        background: white;
        border-radius: 20px;
        padding: 25px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 2px 10px rgba(0,0,0,0.02);
        transition: all 0.3s;
        margin-bottom: 25px;
    }
    .premium-card:hover { box-shadow: 0 10px 30px rgba(0,0,0,0.04); }

    /* Hidden Print Report Styles (Off-screen for Chart.js to render) */
    .stat-report-container { 
        position: absolute; 
        left: -9999px; 
        top: 0; 
        width: 1000px; /* Large enough for high-res charts */
        visibility: hidden;
    }

    @media print {
        @page { size: A4 portrait; margin: 15mm; }
        body { 
            background: white !important; 
            font-family: 'Outfit', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif !important; 
            font-size: 11pt !important;
            color: #000 !important;
        }
        .sidebar, .top-nav, .footer-mockup, .d-print-none, .btn, .interactive-section { display: none !important; }
        .main-content { margin: 0 !important; padding: 0 !important; width: 100% !important; }
        
        .stat-report-container { 
            position: static !important;
            visibility: visible !important;
            display: block !important; 
            width: 100% !important;
            background: white;
            padding: 0;
            left: 0;
        }

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
        .stat-title p { font-size: 10pt; font-weight: 700; color: #334155 !important; margin: 0; }

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

        /* KPI & Charts specific */
        .print-layout-table { width: 100%; border-collapse: separate; border-spacing: 15px 0; margin-bottom: 20px; table-layout: fixed; }
        .print-kpi-box { border: 1px solid #e2e8f0; padding: 15px; text-align: center; border-radius: 12px; }
        .print-kpi-label { font-size: 8pt; font-weight: 800; text-transform: uppercase; color: #64748b; margin-bottom: 4px; }
        .print-kpi-val { font-size: 16pt; font-weight: 900; color: #0f172a !important; }

        .charts-layout-table { width: 100%; border-collapse: separate; border-spacing: 20px 0; margin-bottom: 30px; table-layout: fixed; page-break-inside: avoid; }
        .chart-print-box { border: 1px solid #e2e8f0; padding: 15px; text-align: center; border-radius: 10px; }
        .chart-print-title { font-size: 9pt; font-weight: 800; margin-bottom: 10px; text-transform: uppercase; border-bottom: 1px solid #f1f5f9; padding-bottom: 5px; color: #334155 !important; }

        /* Signature Area */
        .stat-sig-table { width: 100% !important; margin-top: 20px; page-break-inside: avoid; border-collapse: collapse; table-layout: fixed; }
        .stat-sig-table td { width: 33.33%; text-align: center; color: #1e293b; vertical-align: top; padding: 0; font-size: 9pt; }
        .sig-name { font-weight: 800; text-decoration: underline; margin-top: 40px; font-size: 9.5pt; }
        .sig-nip { font-size: 8.5pt; margin-top: 2px; }
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Interactive System UI (Screen Only) -->
<div class="interactive-section d-print-none">
    <div class="d-flex justify-content-between align-items-center mb-4 page-title-area">
        <div>
            <h3 class="fw-800 mb-1">Pusat Laporan & KPI</h3>
            <p class="text-muted small mb-0 fw-500">Monitor performa wilayah kerja Anda melalui metrik statistik.</p>
        </div>
        <button type="button" onclick="window.print()" class="btn btn-success rounded-pill px-4 fw-bold shadow-sm">
            <i class="fas fa-print me-2"></i> Cetak Laporan PDF
        </button>
    </div>

    <!-- Quick Metrics Row -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="premium-card">
                <h6 class="text-muted x-small fw-800 text-uppercase mb-3">Produktivitas Luas</h6>
                <div class="d-flex align-items-end gap-2">
                    <h2 class="fw-900 mb-0"><?= number_format($summary['total_luas'], 1) ?></h2>
                    <span class="text-muted small fw-bold mb-1">Ha</span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="premium-card">
                <h6 class="text-muted x-small fw-800 text-uppercase mb-3">Intensitas Aktivitas</h6>
                <div class="d-flex align-items-end gap-2">
                    <h2 class="fw-900 mb-0"><?= number_format($totalActivities) ?></h2>
                    <span class="text-muted small fw-bold mb-1">Aktivitas</span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="premium-card">
                <h6 class="text-muted x-small fw-800 text-uppercase mb-3">Hasil Panen Estimasi</h6>
                <?php 
                    // Calculate estimated yield based on current avg productivity and total area
                    $estYield = ($summary['total_luas'] ?? 0) * ($avgProd > 0 ? $avgProd : 5.8); 
                ?>
                <div class="d-flex align-items-end gap-2">
                    <h2 class="fw-900 mb-0"><?= number_format($estYield, 1) ?></h2>
                    <span class="text-muted small fw-bold mb-1">Ton GKP</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row g-4 mb-4">
        <div class="col-md-8">
            <div class="premium-card h-100">
                <h6 class="fw-800 mb-4">Tren Produktivitas Bulanan</h6>
                <div style="height: 300px;">
                    <canvas id="trendsChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="premium-card h-100">
                <h6 class="fw-800 mb-4">Proporsi Komoditas</h6>
                <div style="height: 200px;">
                    <canvas id="commodityChart"></canvas>
                </div>
                <div class="mt-4">
                    <?php if(!empty($commodityStats)): foreach ($commodityStats as $name => $stat): ?>
                    <div class="d-flex flex-column mb-3 border-bottom pb-2">
                        <div class="d-flex justify-content-between mb-1 small fw-bold">
                            <span class="text-uppercase" style="color: <?= $name == 'padi' ? '#10b981' : '#f59e0b' ?>"><i class="fas fa-seedling me-1"></i><?= esc(ucfirst($name)) ?></span>
                            <span class="text-dark fw-900"><?= number_format($stat['prod'], 2) ?> <small class="text-muted">ton/ha</small></span>
                        </div>
                        <div class="d-flex justify-content-between x-small fw-600 text-muted">
                            <span>Luas: <?= number_format($stat['luas'], 1) ?> Ha</span>
                            <span>Panen: <?= number_format($stat['panen'], 1) ?> Ton</span>
                        </div>
                    </div>
                    <?php endforeach; else: ?>
                    <?php foreach ($summary['commodities'] as $name => $val): ?>
                    <div class="d-flex justify-content-between mb-2 small fw-bold">
                        <span><?= esc(ucfirst($name)) ?></span>
                        <span class="text-muted"><?= number_format($val, 1) ?> Ha</span>
                    </div>
                    <?php endforeach; endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Performers Table (Only for PPL) -->
    <?php if (session()->get('role') === 'ppl'): ?>
    <div class="premium-card p-0 overflow-hidden mb-4">
        <div class="p-4 border-bottom d-flex justify-content-between align-items-center">
            <h6 class="fw-800 mb-0">Kelompok Tani Terbaik</h6>
            <span class="badge bg-dark rounded-pill px-3 py-2 fw-800" style="font-size: 10px;">TOP 5 KELOMPOK</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr class="x-small fw-800 text-muted">
                        <th class="ps-4">RANK</th>
                        <th>KELOMPOK TANI</th>
                        <th>LUAS</th>
                        <th>PRODUKTIVITAS</th>
                        <th>TREN</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($topGroups as $group): ?>
                    <tr class="fw-700">
                        <td class="ps-4 fw-900 fs-5"><?= $group['rank'] ?></td>
                        <td><?= esc($group['nama']) ?></td>
                        <td class="text-muted"><?= number_format($group['luas'], 1) ?> ha</td>
                        <td class="fs-5 fw-900"><?= number_format($group['prod'], 2) ?> <small class="text-muted fs-6">ton/ha</small></td>
                        <td>
                            <?php if ($group['prod'] > 0 && $group['trend'] !== 0.0): ?>
                                <?php 
                                    $isPositive = $group['trend'] > 0;
                                    $trendColor = $isPositive ? 'text-success' : 'text-danger';
                                    $trendIcon = $isPositive ? 'fa-arrow-up' : 'fa-arrow-down';
                                ?>
                                <span class="<?= $trendColor ?> small fw-700">
                                    <i class="fas <?= $trendIcon ?> me-1"></i> <?= number_format(abs($group['trend']), 1) ?>%
                                </span>
                            <?php else: ?>
                                <span class="text-muted small">-</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($topGroups)): ?>
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted small fw-bold">
                            <i class="fas fa-info-circle me-2"></i> Belum ada data panen yang tercatat di database.
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>

    <!-- Performa Setiap Lahan Table -->
    <div class="row g-4 mb-4">
        <div class="col-md-8">
            <div class="premium-card p-0 overflow-hidden h-100">
                <div class="p-4 border-bottom d-flex justify-content-between align-items-center bg-light">
                    <h6 class="fw-800 mb-0">Performa Produktivitas Setiap Lahan</h6>
                    <span class="badge bg-primary rounded-pill px-3 py-2 fw-800" style="font-size: 10px;">DATA INDIVIDU LAHAN</span>
                </div>
                <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-white sticky-top">
                            <tr class="x-small fw-800 text-muted">
                                <th class="ps-4">NAMA LAHAN</th>
                                <th>KOMODITAS</th>
                                <th>LUAS</th>
                                <th>TOTAL PANEN</th>
                                <th>PRODUKTIVITAS</th>
                                <th>STATUS FASE</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($topLands)): foreach ($topLands as $land): ?>
                            <tr class="fw-700">
                                <td class="ps-4"><span class="fw-bold"><?= esc($land['nama']) ?></span></td>
                                <td><span class="badge bg-dark bg-opacity-10 text-dark rounded-pill" style="font-size: 10px;"><?= strtoupper(esc($land['komoditas'])) ?></span></td>
                                <td class="text-muted small"><?= number_format($land['luas'], 1) ?> ha</td>
                                <td class="text-dark small fw-bold"><?= number_format($land['panen'], 1) ?> Ton</td>
                                <td class="text-primary fw-900"><?= number_format($land['prod'], 2) ?> <small class="text-muted">ton/ha</small></td>
                                <td>
                                    <?php
                                        $faseBg = '#fbbf24'; // Persiapan lahan default
                                        if ($land['fase'] == 'panen') $faseBg = '#f59e0b';
                                        if ($land['fase'] == 'tanam') $faseBg = '#22c55e';
                                        if ($land['fase'] == 'pemeliharaan') $faseBg = '#3b82f6';
                                        if ($land['fase'] == 'bera') $faseBg = '#94a3b8';
                                        if ($land['fase'] == 'darurat') $faseBg = '#ef4444';
                                    ?>
                                    <span class="badge rounded-pill" style="background-color: <?= $faseBg ?>20; color: <?= $faseBg ?>; font-size: 10px;"><?= strtoupper(esc($land['fase'])) ?></span>
                                </td>
                            </tr>
                            <?php endforeach; else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted small fw-bold">
                                    <i class="fas fa-info-circle me-2"></i> Belum ada data lahan tercatat.
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Disaster Risk Assessment -->
            <div class="premium-card">
                <h6 class="fw-800 mb-4"><i class="fas fa-shield-alt text-danger me-2"></i> Risiko Bencana & Kerawanan</h6>
                
                <?php 
                    $darurat = $disasterStats['darurat_count'] ?? 0;
                    $total = $disasterStats['total_count'] ?? 0;
                    $persentase = ($total > 0) ? ($darurat / $total * 100) : 0;
                    $statusRisiko = ($persentase > 20) ? 'Kritis' : (($persentase > 0) ? 'Waspada' : 'Aman');
                    $colorClass = ($persentase > 20) ? 'danger' : (($persentase > 0) ? 'warning' : 'success');
                ?>
                
                <div class="text-center mb-4">
                    <div class="display-4 fw-900 text-<?= $colorClass ?> mb-2"><?= number_format($persentase, 1) ?>%</div>
                    <span class="text-muted fw-bold small text-uppercase letter-spacing-1">Lahan Terdampak Bencana</span>
                </div>
                
                <div class="progress mb-3" style="height: 10px; border-radius: 5px; background-color: #f1f5f9;">
                    <div class="progress-bar bg-<?= $colorClass ?>" role="progressbar" style="width: <?= $persentase ?>%" aria-valuenow="<?= $persentase ?>" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                
                <div class="d-flex justify-content-between x-small fw-800 text-uppercase mb-4">
                    <span class="text-muted"><i class="fas fa-check-circle text-success me-1"></i> Normal: <?= $total - $darurat ?></span>
                    <span class="text-muted"><i class="fas fa-exclamation-triangle text-danger me-1"></i> Darurat: <?= $darurat ?></span>
                </div>

                <div class="p-3 bg-<?= $colorClass ?> bg-opacity-10 rounded-3 border border-<?= $colorClass ?> border-opacity-25">
                    <div class="d-flex gap-3 align-items-center">
                        <div class="text-<?= $colorClass ?> fs-3"><i class="fas <?= $persentase > 0 ? 'fa-exclamation-circle' : 'fa-check-circle' ?>"></i></div>
                        <div>
                            <h6 class="fw-800 text-<?= $colorClass ?> mb-1">Status: <?= $statusRisiko ?></h6>
                            <p class="x-small mb-0 opacity-75 fw-bold text-dark">
                                <?= $persentase > 0 ? 'Terdapat lahan yang membutuhkan mitigasi segera.' : 'Semua lahan dalam kondisi aman dari bencana.' ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Formal Print Report (Hidden on Screen, Shown on Print) -->
<div class="stat-report-container">
    <!-- Formal Print Header (New Premium Style) -->
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

    <!-- Title & Meta Info -->
    <div class="stat-title">
        <h2>LAPORAN STATISTIK & KPI PERTANIAN</h2>
        <p>Wilayah Kerja Rajabasa • Periode: <?= date('F Y') ?></p>
    </div>
    
    <table class="stat-meta-table">
        <tr>
            <td style="text-align: left;">ID Dokumen: KPI/STAT/<?= date('Ymd') ?></td>
            <td style="text-align: right;">Dicetak: <?= date('d/m/Y H:i') ?> WIB</td>
        </tr>
    </table>

    <!-- Summary KPI Cards -->
    <table class="print-layout-table">
        <tr>
            <td>
                <div class="print-kpi-box">
                    <div class="print-kpi-label">Produktivitas Luas</div>
                    <div class="print-kpi-val"><?= number_format($summary['total_luas'], 1) ?> Ha</div>
                </div>
            </td>
            <td>
                <div class="print-kpi-box">
                    <div class="print-kpi-label">Intensitas Aktivitas</div>
                    <div class="print-kpi-val"><?= array_sum(array_column($trends, 'count')) ?> Akt.</div>
                </div>
            </td>
            <td>
                <div class="print-kpi-box">
                    <div class="print-kpi-label">Hasil Panen Estimasi</div>
                    <div class="print-kpi-val"><?= number_format($estYield, 1) ?> Ton</div>
                </div>
            </td>
        </tr>
    </table>

    <!-- Main Data Table -->
    <table class="table-formal-report">
        <thead>
            <tr>
                <th class="text-center" style="width: 8%;">RANK</th>
                <th style="width: 42%; text-align: left;">KELOMPOK TANI / UNIT KERJA</th>
                <th class="text-center" style="width: 15%;">LUAS (HA)</th>
                <th class="text-center" style="width: 20%;">PRODUKTIVITAS</th>
                <th class="text-center" style="width: 15%;">TREN</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($topGroups as $group): ?>
            <tr>
                <td class="text-center fw-bold"><?= $group['rank'] ?></td>
                <td class="fw-bold"><?= esc($group['nama']) ?></td>
                <td class="text-center"><?= number_format($group['luas'], 1) ?></td>
                <td class="text-center fw-bold"><?= number_format($group['prod'], 2) ?> Ton/Ha</td>
                <td class="text-center">
                    <?php if ($group['prod'] > 0 && $group['trend'] !== 0.0): ?>
                        <?= $group['trend'] > 0 ? '+' : '-' ?><?= number_format(abs($group['trend']), 1) ?>%
                    <?php else: ?>
                        -
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($topGroups)): ?>
            <tr>
                <td colspan="5" class="text-center" style="padding: 40px; font-style: italic; font-weight: 600;">Data aktivitas panen belum tersedia untuk periode ini.</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Charts Section -->
    <table class="charts-layout-table">
        <tr>
            <td>
                <div class="chart-print-box">
                    <div class="chart-print-title">Distribusi Komoditas</div>
                    <div style="height: 220px;">
                        <canvas id="commodityChartPrint"></canvas>
                    </div>
                </div>
            </td>
            <td>
                <div class="chart-print-box">
                    <div class="chart-print-title">Tren Aktivitas Monitoring</div>
                    <div style="height: 220px;">
                        <canvas id="trendsChartPrint"></canvas>
                    </div>
                </div>
            </td>
        </tr>
    </table>

    <!-- Signature Table -->
    <table class="stat-sig-table">
        <tr>
            <td>
                Mengetahui,<br>Petugas Lapangan (PPL)
                <div class="sig-space"></div>
                <div class="sig-name"><?= esc(session()->get('nama')) ?></div>
                <p class="sig-nip">NIP. 19850215 201201 1 005</p>
            </td>
            <td>
                <br>Kabid Statistik & Data
                <div class="sig-space"></div>
                <div class="sig-name">Indra Pratama, S.T.</div>
                <p class="sig-nip">NIP. 19781020 200601 1 008</p>
            </td>
            <td>
                <br>Kepala Dinas Pertanian
                <div class="sig-space"></div>
                <div class="sig-name">Dr. Ir. Heru Santoso</div>
                <p class="sig-nip">NIP. 19680320 199403 1 002</p>
            </td>
        </tr>
    </table>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
<script>
    // Commodity Chart (Interactive Screen)
    var ctxComm = document.getElementById('commodityChart').getContext('2d');
    new Chart(ctxComm, {
        type: 'doughnut',
        data: {
            labels: <?= json_encode(array_keys($summary['commodities'])) ?>,
            datasets: [{
                data: <?= json_encode(array_values($summary['commodities'])) ?>,
                backgroundColor: ['#1e7e34', '#f59e0b', '#3b82f6', '#ef4444'],
                borderWidth: 0,
                cutout: '75%'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } }
        }
    });

    // Trends Chart (Interactive Screen) — same style as Dashboard
    var ctxTrends = document.getElementById('trendsChart').getContext('2d');
    var trendsGradient = ctxTrends.createLinearGradient(0, 0, 0, 300);
    trendsGradient.addColorStop(0, 'rgba(30, 126, 52, 0.3)');
    trendsGradient.addColorStop(1, 'rgba(30, 126, 52, 0.0)');
    new Chart(ctxTrends, {
        type: 'line',
        data: {
            labels: <?= json_encode(array_column($trends, 'month')) ?>,
            datasets: [{
                label: 'Tren Produktivitas',
                data: <?= json_encode(array_column($trends, 'count')) ?>,
                borderColor: '#1e7e34',
                borderWidth: 2.5,
                fill: true,
                backgroundColor: trendsGradient,
                tension: 0.45,
                pointRadius: 4,
                pointBackgroundColor: '#fff',
                pointBorderColor: '#1e7e34',
                pointBorderWidth: 2.5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(ctx) { return ' ' + ctx.parsed.y + ' ton/ha'; }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(0,0,0,0.04)', drawBorder: false },
                    ticks: { font: { size: 11 }, color: '#94a3b8' }
                },
                x: {
                    grid: { display: false },
                    ticks: { font: { size: 11 }, color: '#94a3b8' }
                }
            }
        }
    });

    // --- Print Only Charts ---
    // Commodity Chart Print
    new Chart(document.getElementById('commodityChartPrint').getContext('2d'), {
        type: 'doughnut',
        data: {
            labels: <?= json_encode(array_keys($summary['commodities'])) ?>,
            datasets: [{
                data: <?= json_encode(array_values($summary['commodities'])) ?>,
                backgroundColor: ['#1e3a5f', '#a3d977', '#3b82f6', '#ef4444'],
                borderWidth: 1,
                borderColor: '#000'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: true, position: 'bottom', labels: { boxWidth: 10, font: { size: 10, family: 'Outfit' } } } },
            animation: false
        }
    });

    // Trends Chart Print
    new Chart(document.getElementById('trendsChartPrint').getContext('2d'), {
        type: 'line',
        data: {
            labels: <?= json_encode(array_column($trends, 'month')) ?>,
            datasets: [{
                label: 'Aktivitas',
                data: <?= json_encode(array_column($trends, 'count')) ?>,
                borderColor: '#1e3a5f',
                borderWidth: 2,
                fill: false,
                tension: 0,
                pointRadius: 3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, grid: { color: '#eee' }, ticks: { font: { size: 8, family: 'Outfit' } } },
                x: { grid: { display: false }, ticks: { font: { size: 8, family: 'Outfit' } } }
            },
            animation: false
        }
    });
</script>
<?= $this->endSection() ?>
