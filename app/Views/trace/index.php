<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?> — AgriMapGIS Traceability</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background: #f0f4f8; }
        .trace-header {
            background: linear-gradient(135deg, #166534 0%, #15803d 50%, #16a34a 100%);
            color: white;
            padding: 40px 24px;
            position: relative;
            overflow: hidden;
        }
        .trace-header::after {
            content: '🌾';
            position: absolute;
            right: -20px;
            top: -20px;
            font-size: 120px;
            opacity: 0.08;
            transform: rotate(15deg);
        }
        .verified-badge {
            background: rgba(255,255,255,0.2);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.3);
            border-radius: 50px;
            padding: 6px 16px;
            font-size: 11px;
            font-weight: 800;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .timeline { position: relative; padding-left: 30px; }
        .timeline::before {
            content: '';
            position: absolute;
            left: 10px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: linear-gradient(to bottom, #16a34a, #e2e8f0);
        }
        .timeline-item { position: relative; margin-bottom: 24px; }
        .timeline-dot {
            position: absolute;
            left: -25px;
            top: 4px;
            width: 14px;
            height: 14px;
            border-radius: 50%;
            border: 2px solid white;
            box-shadow: 0 0 0 2px;
        }
        .stat-card {
            background: white;
            border-radius: 16px;
            padding: 20px;
            border: 1px solid #e2e8f0;
            text-align: center;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
        .activity-badge {
            font-size: 10px;
            font-weight: 800;
            padding: 4px 10px;
            border-radius: 50px;
            text-transform: uppercase;
        }
        .ab-tanam { background: #dcfce7; color: #166534; }
        .ab-pemupukan { background: #fef3c7; color: #92400e; }
        .ab-pengairan { background: #dbeafe; color: #1e40af; }
        .ab-pengendalian { background: #fce7f3; color: #9d174d; }
        .ab-panen { background: #f59e0b20; color: #b45309; }
        .ab-other { background: #f1f5f9; color: #475569; }
        .ab-riwayat { background: #fee2e2; color: #991b1b; } /* Riwayat Bencana */
        .qr-section {
            background: white;
            border-radius: 16px;
            padding: 24px;
            text-align: center;
            border: 2px dashed #e2e8f0;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        @media print {
            .no-print { display: none !important; }
            body { background: white; }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="trace-header">
        <div class="container">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                <div>
                    <div class="verified-badge mb-3">
                        <i class="fas fa-shield-check"></i> TERVERIFIKASI AGRIMAPGIS
                    </div>
                    <h2 class="fw-900 mb-1" style="font-size: 28px; letter-spacing: -0.5px;"><?= esc($land['nama_lahan']) ?></h2>
                    <p class="opacity-80 mb-0 fw-600 small">
                        <i class="fas fa-map-marker-alt me-2"></i><?= esc($land['alamat'] ?? 'Alamat tidak tersedia') ?>
                    </p>
                    <p class="opacity-70 mb-0 small mt-1">
                        Komoditas: <strong><?= ucfirst(esc($land['komoditas'])) ?></strong> &nbsp;·&nbsp; 
                        Luas: <strong><?= number_format($land['luas'], 2) ?> Ha</strong>
                    </p>
                </div>
                <div class="text-end no-print">
                    <a href="<?= base_url('/') ?>" class="btn btn-light btn-sm rounded-pill fw-700 mb-2">
                        <i class="fas fa-home me-1"></i> Beranda
                    </a><br>
                    <button onclick="window.print()" class="btn btn-outline-light btn-sm rounded-pill fw-700">
                        <i class="fas fa-print me-1"></i> Cetak
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="container py-4">
        <!-- Stats Row -->
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="stat-card">
                    <div class="fw-900 fs-3 text-success"><?= count($activities) ?></div>
                    <div class="small fw-700 text-muted">Total Aktivitas</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-card">
                    <div class="fw-900 fs-3 text-warning"><?= $panenCount ?></div>
                    <div class="small fw-700 text-muted">Kali Panen</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-card">
                    <div class="fw-900 fs-3 text-primary"><?= number_format($totalPanen, 1) ?></div>
                    <div class="small fw-700 text-muted">Total Ton (GKP)</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-card">
                    <?php $prodHa = ($land['luas'] > 0 && $totalPanen > 0) ? $totalPanen / $land['luas'] : 0; ?>
                    <div class="fw-900 fs-3 text-danger"><?= number_format($prodHa, 2) ?></div>
                    <div class="small fw-700 text-muted">Ton/Ha (Avg)</div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Timeline -->
            <div class="col-md-8">
                <div class="bg-white rounded-4 p-4 border border-light-subtle shadow-sm">
                    <h6 class="fw-800 mb-4"><i class="fas fa-timeline text-success me-2"></i>Riwayat Aktivitas Terverifikasi</h6>
                    <?php if (empty($activities)): ?>
                    <div class="text-center py-5 text-muted">
                        <i class="fas fa-seedling fs-1 opacity-25 mb-3 d-block"></i>
                        <p class="fw-700">Belum ada aktivitas terverifikasi pada lahan ini.</p>
                    </div>
                    <?php else: ?>
                    <div class="timeline">
                        <?php 
                        $colors = ['tanam'=>'#16a34a','pemupukan'=>'#f59e0b','pengairan'=>'#3b82f6','pengendalian_hama'=>'#ec4899','panen'=>'#f97316', 'riwayat_bencana'=>'#dc2626'];
                        $icons = ['tanam'=>'seedling','pemupukan'=>'leaf','pengairan'=>'water','pengendalian_hama'=>'bug','panen'=>'tractor', 'riwayat_bencana'=>'exclamation-triangle'];
                        
                        foreach ($activities as $act): 
                            $color = $colors[$act['jenis_aktivitas']] ?? '#94a3b8';
                            $icon  = $icons[$act['jenis_aktivitas']] ?? 'check';
                            
                            $badgeClass = 'ab-other';
                            if ($act['jenis_aktivitas'] === 'pengendalian_hama') $badgeClass = 'ab-pengendalian';
                            else if (strpos($act['jenis_aktivitas'], 'riwayat') !== false) $badgeClass = 'ab-riwayat';
                            else if (in_array($act['jenis_aktivitas'], ['tanam','pemupukan','pengairan','panen'])) $badgeClass = 'ab-' . $act['jenis_aktivitas'];
                        ?>
                        <div class="timeline-item">
                            <div class="timeline-dot d-flex align-items-center justify-content-center" style="background: <?= $color ?>; box-shadow: 0 0 0 4px <?= $color ?>30; width: 32px; height: 32px; left: -34px; top: 0; color: white; font-size: 12px;">
                                <i class="fas fa-<?= $icon ?>"></i>
                            </div>
                            <div class="bg-light p-3 rounded-4 border border-light-subtle shadow-sm position-relative timeline-card" style="transition: transform 0.2s, box-shadow 0.2s; cursor: default;">
                                <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                                    <div>
                                        <div class="d-flex align-items-center gap-2 mb-2">
                                            <span class="activity-badge <?= $badgeClass ?> shadow-sm">
                                                <?= ucwords(str_replace('_', ' ', $act['jenis_aktivitas'])) ?>
                                            </span>
                                            <span class="text-muted small fw-700" style="font-size: 11px;">
                                                <i class="fas fa-calendar-alt me-1"></i><?= date('d M Y', strtotime($act['tanggal'])) ?>
                                            </span>
                                        </div>
                                        <div class="fw-800 text-dark mb-1" style="font-size: 15px;"><?= esc($act['deskripsi'] ?? 'Tidak ada deskripsi') ?></div>
                                        <div class="text-muted small fw-600">
                                            <i class="fas fa-user-circle me-1"></i>Dilaporkan oleh: <?= esc($act['nama_petani'] ?? 'Petani') ?>
                                        </div>
                                    </div>
                                    
                                    <?php if ($act['hasil_panen'] > 0): ?>
                                    <div class="text-end">
                                        <div class="bg-success bg-opacity-10 text-success rounded-3 px-3 py-2 border border-success border-opacity-25 text-center mt-2 mt-md-0">
                                            <div class="small fw-800 text-uppercase" style="font-size: 9px; letter-spacing: 1px;">Hasil Panen</div>
                                            <div class="fw-900 fs-5">🌾 <?= number_format($act['hasil_panen'], 1) ?> <span class="fs-6">Ton</span></div>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <style>
                        .timeline-card:hover { transform: translateX(5px); box-shadow: 0 10px 25px rgba(0,0,0,0.05) !important; }
                    </style>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Info Sidebar -->
            <div class="col-md-4">
                <!-- Kelompok Tani Info -->
                <div class="bg-white rounded-4 p-4 border border-light-subtle shadow-sm mb-4">
                    <h6 class="fw-800 mb-3"><i class="fas fa-users text-success me-2"></i>Kelompok Tani</h6>
                    <div class="fw-800 mb-1"><?= esc($group['nama_kelompok'] ?? '-') ?></div>
                    <div class="text-muted small fw-600 mb-1"><i class="fas fa-user-tie me-1"></i> Ketua: <?= esc($group['ketua'] ?? '-') ?></div>
                    <div class="text-muted small fw-600"><i class="fas fa-map-pin me-1"></i> <?= esc($group['kecamatan'] ?? '-') ?></div>
                </div>

                <!-- Phase Status -->
                <div class="bg-white rounded-4 p-4 border border-light-subtle shadow-sm mb-4">
                    <h6 class="fw-800 mb-3"><i class="fas fa-seedling text-success me-2"></i>Status Fase Saat Ini</h6>
                    <?php
                        $faseColors = ['persiapan'=>['#fbbf24','Persiapan Lahan'],'tanam'=>['#22c55e','Fase Tanam'],'pemeliharaan'=>['#3b82f6','Pemeliharaan'],'panen'=>['#f59e0b','Siap Panen'],'bera'=>['#94a3b8','Lahan Bera']];
                        [$fc, $fl] = $faseColors[$land['status_fase']] ?? ['#94a3b8', ucfirst($land['status_fase'])];
                    ?>
                    <div class="d-flex align-items-center gap-3">
                        <div style="width: 14px; height: 14px; background: <?= $fc ?>; border-radius: 50%;"></div>
                        <span class="fw-800" style="color: <?= $fc ?>; font-size: 15px;"><?= $fl ?></span>
                    </div>
                </div>

                <!-- QR Code -->
                <div class="qr-section no-print">
                    <div id="qrcode" class="mb-3"></div>
                    <p class="small fw-700 text-muted mb-2">Scan untuk berbagi riwayat lahan ini</p>
                    <a href="<?= current_url() ?>" class="small text-primary fw-600" style="word-break: break-all;"><?= current_url() ?></a>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center mt-5 text-muted small fw-600">
            <i class="fas fa-seedling text-success me-1"></i> 
            Dokumen ini diterbitkan oleh sistem <strong>AgriMapGIS</strong> · Seluruh aktivitas telah diverifikasi oleh PPL.
            <br>Dicetak pada <?= date('d M Y H:i') ?> WIB
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
    <script>
        new QRCode(document.getElementById("qrcode"), {
            text: "<?= current_url() ?>",
            width: 140,
            height: 140,
            colorDark: "#166534",
            colorLight: "#ffffff",
            correctLevel: QRCode.CorrectLevel.H
        });
    </script>
</body>
</html>
