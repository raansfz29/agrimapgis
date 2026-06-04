<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'AgriMapGIS' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="/favicon.png">
    
    <!-- PWA Setup -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#1e7e34">
    <link rel="apple-touch-icon" href="/images/dummy-icon-192x192.png">

    <!-- CSRF Token for AJAX -->
    <meta name="<?= csrf_token() ?>" content="<?= csrf_hash() ?>" id="csrf-token">

    <?= $this->renderSection('styles') ?>
    <style>
        :root {
            --primary-green: #1e7e34;
            --dark-green: #155724;
            --bg-gray: #f8fafc;
            --border-color: #e2e8f0;
            --text-dark: #0f172a;
            --text-muted: #64748b;
            --sidebar-width: 280px;
        }

        html, body {
            height: 100%;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background-color: var(--bg-gray);
            color: var(--text-dark);
            margin: 0;
            display: flex;
            flex-direction: column;
        }

        /* Modern Floating Sidebar */
        .sidebar {
            position: fixed;
            left: 20px;
            top: 20px;
            bottom: 20px;
            width: var(--sidebar-width);
            background: white;
            border-radius: 24px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.02);
            display: flex;
            flex-direction: column;
            z-index: 1100;
            padding: 30px;
            border: 1px solid var(--border-color);
        }

        .sidebar-brand-box { display: flex; align-items: center; gap: 15px; margin-bottom: 40px; }
        .brand-icon { width: 45px; height: 45px; background: var(--dark-green); border-radius: 14px; display: flex; align-items: center; justify-content: center; color: white; font-size: 22px; }
        .brand-info h5 { margin: 0; font-weight: 800; font-size: 18px; color: var(--text-dark); }
        .brand-info span { font-size: 12px; color: var(--text-muted); font-weight: 600; }
        
        .sidebar-menu { list-style: none; padding: 0; margin: 0; flex-grow: 1; }
        .sidebar-menu li { margin-bottom: 8px; }
        .sidebar-menu a { 
            display: flex; 
            align-items: center; 
            gap: 15px; 
            padding: 14px 20px; 
            color: #475569; 
            text-decoration: none; 
            font-size: 14px; 
            font-weight: 600; 
            border-radius: 12px; 
            transition: all 0.2s; 
        }
        .sidebar-menu a:hover { background: #f1f5f9; color: var(--text-dark); }
        .sidebar-menu a.active { background: var(--dark-green); color: white; box-shadow: 0 8px 20px rgba(21, 87, 36, 0.2); }
        .sidebar-menu a i { font-size: 18px; width: 24px; text-align: center; }

        .btn-logout { background: #f1f5f9; color: #475569; border: none; padding: 15px; border-radius: 16px; font-weight: 700; font-size: 14px; display: flex; align-items: center; justify-content: center; gap: 12px; text-decoration: none; transition: all 0.2s; }
        .btn-logout:hover { background: #e2e8f0; color: var(--text-dark); }

        /* Modern Top Navigation */
        .top-nav {
            margin-left: var(--sidebar-width);
            padding: 20px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: rgba(248, 250, 252, 0.9);
            backdrop-filter: blur(15px);
            flex-shrink: 0;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .search-box {
            background: white;
            border-radius: 16px;
            padding: 10px 20px;
            width: 450px;
            display: flex;
            align-items: center;
            gap: 12px;
            border: 1px solid var(--border-color);
            box-shadow: 0 2px 10px rgba(0,0,0,0.02);
        }
        .search-box input { border: none; outline: none; width: 100%; font-size: 14px; font-weight: 500; }
        .search-box i { color: var(--text-muted); }

        .nav-actions { display: flex; align-items: center; gap: 25px; }
        .notif-btn { position: relative; color: #475569; font-size: 20px; cursor: pointer; padding: 5px; transition: all 0.2s; }
        .notif-btn:hover { color: var(--primary-green); transform: scale(1.1); }
        .notif-badge { position: absolute; top: 2px; right: 2px; width: 8px; height: 8px; background: #ef4444; border-radius: 50%; border: 2px solid white; }

        .notif-dropdown { width: 380px; border-radius: 20px; border: 1px solid var(--border-color); box-shadow: 0 15px 50px rgba(0,0,0,0.1) !important; overflow: hidden; margin-top: 15px !important; }
        .notif-header { padding: 18px 25px; border-bottom: 1px solid #f1f5f9; background: #f8fafc; }
        .notif-item-mini { padding: 15px 25px; border-bottom: 1px solid #f8fafc; transition: all 0.2s; cursor: pointer; text-decoration: none; display: flex; gap: 15px; }
        .notif-item-mini:hover { background: #f8fafc; }
        .notif-item-mini.unread { background: rgba(30, 126, 52, 0.03); }
        .notif-icon-box { width: 40px; height: 40px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 16px; flex-shrink: 0; }
        .notif-content-box { flex-grow: 1; min-width: 0; }
        .notif-title-mini { display: block; font-weight: 700; color: var(--text-dark); font-size: 13px; margin-bottom: 2px; }
        .notif-msg-mini { display: block; font-size: 12px; color: var(--text-muted); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .notif-time-mini { display: block; font-size: 10px; color: #94a3b8; margin-top: 4px; font-weight: 600; text-transform: uppercase; }
        .notif-footer { padding: 12px; background: #f8fafc; text-align: center; }
        .notif-footer a { font-size: 12px; font-weight: 700; color: var(--primary-green); text-decoration: none; }
        .notif-footer a:hover { text-decoration: underline; }

        .user-profile { display: flex; align-items: center; gap: 15px; padding-left: 25px; border-left: 1px solid var(--border-color); }
        .user-info { text-align: right; }
        .user-info .name { display: block; font-weight: 800; font-size: 14px; color: var(--text-dark); }
        .user-info .role { display: block; font-size: 11px; color: var(--text-muted); font-weight: 700; text-transform: uppercase; }
        .user-avatar { width: 42px; height: 42px; border-radius: 50%; object-fit: cover; border: 2px solid white; box-shadow: 0 4px 12px rgba(0,0,0,0.08); }

        .nav-link-premium {
            text-decoration: none;
            font-weight: 700;
            font-size: 15px; /* Larger than 'small' */
            transition: all 0.2s;
            padding: 5px 10px;
        }
        .nav-link-premium.active { color: var(--primary-green) !important; }
        .nav-link-premium.inactive { color: var(--text-muted); }
        .nav-link-premium:hover { color: var(--primary-green); }

        /* Content Area */
        .main-content {
            margin-left: calc(var(--sidebar-width) + 40px);
            padding: 0 40px 40px 40px;
            display: flex;
            flex-direction: column;
            flex: 1 0 auto;
        }

        .content-wrapper { flex: 1 0 auto; }

        .main-content.no-padding { margin-left: calc(var(--sidebar-width) + 40px); padding: 0 20px 20px 0; }

        .premium-card {
            background: white;
            border-radius: 20px;
            padding: 25px;
            border: 1px solid var(--border-color);
            transition: all 0.3s;
        }
        .premium-card:hover { box-shadow: 0 10px 30px rgba(0,0,0,0.04); }

        .footer-mockup { margin-top: 50px; padding: 25px 0; border-top: 1px solid var(--border-color); display: flex; justify-content: space-between; color: var(--dark-green); font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; opacity: 0.7; flex-shrink: 0; }

        /* Mobile Overlay */
        .sidebar-overlay {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0,0,0,0.5);
            z-index: 1090;
            display: none;
            backdrop-filter: blur(4px);
        }
        .sidebar-overlay.show { display: block; }

        /* Mobile Header */
        .mobile-header {
            display: none;
            justify-content: space-between;
            align-items: center;
            padding: 15px 20px;
            background: white;
            border-bottom: 1px solid var(--border-color);
            position: sticky;
            top: 0;
            z-index: 1050;
            box-shadow: 0 4px 20px rgba(0,0,0,0.02);
        }
        .mobile-header .brand-title { font-weight: 800; font-size: 20px; color: var(--dark-green); letter-spacing: -0.5px; }
        .mobile-header .menu-toggle { background: none; border: none; font-size: 24px; color: var(--text-dark); cursor: pointer; padding: 5px; }

        /* Responsive Breakpoints */
        @media (max-width: 991px) {
            .sidebar {
                transform: translateX(-150%);
                transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                border-radius: 0 24px 24px 0;
                top: 0; bottom: 0; left: 0;
                box-shadow: 10px 0 40px rgba(0,0,0,0.1);
            }
            .sidebar.open { transform: translateX(0); }
            
            .mobile-header { display: flex; }
            
            .top-nav {
                margin-left: 0;
                padding: 10px 20px;
                flex-direction: row; /* Keep elements on one row if possible, or stack smartly */
                flex-wrap: wrap;
                gap: 10px;
                background: white;
                position: relative;
            }
            
            .top-nav > .d-flex:first-child {
                order: 2;
                width: 100%;
                gap: 10px !important;
            }
            
            .search-box { width: 100%; padding: 8px 15px; }
            
            .d-flex.gap-4.ms-4 {
                display: none !important; /* Hide secondary links on mobile to save space, they are in sidebar */
            }
            
            .nav-actions { 
                order: 1;
                width: 100%; 
                justify-content: space-between; 
                padding-bottom: 10px; 
                border-bottom: 1px solid var(--border-color);
                border-top: none;
            }

            .user-info { display: none; } /* Hide name/role on mobile */
            .user-profile { padding-left: 0; border-left: none; }
            
            .main-content { margin-left: 0; padding: 15px; }
            .main-content.no-padding { margin-left: 0; padding: 0; }
            .notif-dropdown { 
                width: calc(100vw - 40px); 
                max-width: 380px;
                position: absolute !important; 
                top: 45px !important; 
                left: 0 !important; 
                right: auto !important; 
                margin: 0;
                transform: none !important;
                z-index: 2000;
            }
            .footer-mockup { flex-direction: column; gap: 10px; text-align: center; }
        }

        /* Global Print Styles - New Government Theme */
        @media print {
            @page { size: A4 portrait; margin: 0; }
            .sidebar, .top-nav, .mobile-header, .sidebar-overlay, .footer-mockup, .btn, .d-print-none, .pagination-area, .page-title-area {
                display: none !important;
            }
            .d-print-block { display: block !important; }
            .d-print-none-custom { display: none !important; }
            .print-only-container { position: static !important; left: auto !important; width: 100% !important; height: auto !important; visibility: visible !important; }
            body { 
                background: white !important; 
                padding: 0 !important;
                margin: 0 !important;
                font-family: 'Outfit', sans-serif !important; /* Use Outfit for modern look as per image */
            }
            .main-content {
                margin: 0 !important;
                padding: 0 !important;
                width: 100% !important;
            }
            
            /* Theme Colors */
            .print-bg-blue { background-color: #1e3a5f !important; color: white !important; -webkit-print-color-adjust: exact; }
            .print-bg-green { background-color: #a3d977 !important; color: #000 !important; -webkit-print-color-adjust: exact; }
            .print-border { border: 1px solid #000 !important; }

            /* New Kop Surat - Simplified for Print Compatibility */
            .kop-surat-new {
                display: flex;
                align-items: center;
                background-color: #1e3a5f !important;
                padding: 15px 30px;
                color: white !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                margin-bottom: 25px;
                border-bottom: 5px solid #4ade80;
            }
            .kop-logo {
                width: 60px;
                height: 60px;
                background: white;
                border-radius: 12px;
                display: flex;
                align-items: center;
                justify-content: center;
                margin-right: 20px;
                padding: 5px;
            }
            .kop-text { text-align: left !important; }
            .kop-text h4 { margin: 0; font-weight: 800; font-size: 15pt; color: white !important; }
            .kop-text p { margin: 0; font-size: 9pt; opacity: 1; color: white !important; }

            .report-title {
                text-align: center;
                margin-bottom: 25px;
                padding: 0 40px;
            }
            .report-title h2 { font-size: 18pt; font-weight: 800; margin-bottom: 5px; text-transform: uppercase; color: #000 !important; }
            
            .meta-info-grid {
                display: flex;
                justify-content: space-between;
                padding: 0 40px;
                margin-bottom: 15px;
                font-weight: 700;
                font-size: 10pt;
            }

            .section-header-blue {
                background-color: #1e3a5f !important;
                color: white !important;
                padding: 10px 20px;
                font-weight: 800;
                font-size: 11pt;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                margin: 0 40px !important;
            }
            
            .section-subheader-green {
                background-color: #a3d977 !important;
                color: black !important;
                font-weight: 800;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .print-table {
                width: calc(100% - 80px) !important;
                margin: 0 40px 25px 40px !important;
                border-collapse: collapse !important;
                border: 1px solid #000 !important;
            }
            .print-table th, .print-table td {
                border: 1px solid #000 !important;
                padding: 8px 12px !important;
                font-size: 9.5pt !important;
                color: #000 !important;
            }

            .map-placeholder {
                width: calc(100% - 82px);
                height: 300px;
                background-color: #f8fafc !important;
                margin: 0 40px 25px 40px;
                display: flex;
                align-items: center;
                justify-content: center;
                border: 1px solid #000;
                font-weight: 800;
                color: #000;
                -webkit-print-color-adjust: exact;
            }

            .kpi-grid {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 20px;
                padding: 15px 30px;
                background-color: #fff !important;
                margin: 0 40px 20px 40px;
                border: 1px solid #000;
                -webkit-print-color-adjust: exact;
                font-size: 10pt;
            }

            .print-chart-container {
                width: calc(100% - 80px);
                margin: 0 40px 25px 40px;
                padding: 15px;
                border: 1px solid #000;
                background: #fff !important;
                -webkit-print-color-adjust: exact;
                break-inside: avoid;
            }
            .print-chart-title {
                font-weight: 800;
                font-size: 10pt;
                margin-bottom: 10px;
                text-align: center;
                text-transform: uppercase;
                border-bottom: 1px solid #000;
                padding-bottom: 5px;
            }

            .signature-area {
                display: flex;
                justify-content: space-between;
                padding: 0 40px;
                margin-top: 30px;
                text-align: center;
                page-break-inside: avoid;
            }
            .sig-box { width: 30%; }
        }
    </style>
    <style>
        /* CSS to hide print-only containers from screen but keep them for Chart.js rendering */
        @media screen {
            .print-only-container {
                position: absolute;
                left: -9999px;
                top: -9999px;
                width: 800px; /* Give it a fixed width for Chart.js to calculate */
            }
        }
    </style>
    <?= $this->renderSection('styles') ?>
</head>
<body>
    <!-- Mobile Header -->
    <div class="mobile-header">
        <div class="d-flex align-items-center gap-2">
            <i class="fas fa-seedling" style="color: #166534; font-size: 24px;"></i>
            <span class="brand-title">AgriMapGIS</span>
        </div>
        <button class="menu-toggle" onclick="toggleSidebar()">
            <i class="fas fa-bars"></i>
        </button>
    </div>

    <!-- Sidebar Overlay -->
    <div class="sidebar-overlay" onclick="toggleSidebar()"></div>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-brand-box" style="margin-bottom: 50px;">
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-seedling" style="color: #166534; font-size: 32px;"></i>
                <span style="font-weight: 800; font-size: 26px; color: #166534; letter-spacing: -1px;">AgriMapGIS</span>
            </div>
        </div>

        <ul class="sidebar-menu">
            <?php $uri = service('uri'); $segment = $uri->getSegment(1); ?>
            <li>
                <a href="<?= base_url('dashboard') ?>" class="<?= $segment == 'dashboard' ? 'active' : '' ?>">
                    <i class="fas fa-th-large"></i> <span>Dashboard</span>
                </a>
            </li>
            <?php if (session()->get('role') === 'ppl'): ?>
            <li>
                <a href="<?= base_url('activity/verification') ?>" class="<?= $segment == 'activity' && strpos(current_url(), 'verification') !== false ? 'active' : '' ?>">
                    <i class="fas fa-check-double"></i> <span>Verifikasi Lahan</span>
                </a>
            </li>
            <?php endif; ?>

            <?php if (session()->get('role') === 'petani'): ?>
            <li>
                <a href="<?= base_url('activity') ?>" class="<?= $segment == 'activity' ? 'active' : '' ?>">
                    <i class="fas fa-history"></i> <span>Aktivitas Kelompok</span>
                </a>
            </li>
            <?php endif; ?>

            <?php if (session()->get('role') === 'petani'): ?>
            <li>
                <a href="<?= base_url('peta-gis') ?>" class="<?= $segment == 'peta-gis' ? 'active' : '' ?>">
                    <i class="fas fa-map-marked-alt"></i> <span>Peta & Lahan Kelompok</span>
                </a>
            </li>
            <?php else: ?>
            <li>
                <a href="<?= base_url('land') ?>" class="<?= $segment == 'land' ? 'active' : '' ?>">
                    <i class="fas fa-map-marked-alt"></i> <span>Kelola Lahan</span>
                </a>
            </li>
            <li>
                <a href="<?= base_url('peta-gis') ?>" class="<?= $segment == 'peta-gis' ? 'active' : '' ?>">
                    <i class="fas fa-layer-group"></i> <span>Peta GIS</span>
                </a>
            </li>
            <?php endif; ?>

            <?php if (session()->get('role') === 'ppl'): ?>
            <li>
                <a href="<?= base_url('reports') ?>" class="<?= $segment == 'reports' ? 'active' : '' ?>">
                    <i class="fas fa-chart-line"></i> <span>Statistik & KPI</span>
                </a>
            </li>
            <li>
                <a href="<?= base_url('farmer-groups') ?>" class="<?= $segment == 'farmer-groups' ? 'active' : '' ?>">
                    <i class="fas fa-users-viewfinder"></i> <span>Kelompok Tani</span>
                </a>
            </li>
            <?php endif; ?>

            <li>
                <a href="<?= base_url('disaster') ?>" class="<?= $segment == 'disaster' ? 'active' : '' ?>">
                    <i class="fas fa-triangle-exclamation"></i> <span>Mitigasi Bencana</span>
                </a>
            </li>

            <li>
                <a href="<?= base_url('message') ?>" class="<?= $segment == 'message' ? 'active' : '' ?>">
                    <i class="fas fa-comments"></i> <span>Pesan</span>
                </a>
            </li>
            
            <li>
                <a href="<?= base_url('notifications') ?>" class="<?= $segment == 'notifications' ? 'active' : '' ?>">
                    <i class="fas fa-bell"></i> <span>Notifikasi</span>
                </a>
            </li>

            <li>
                <a href="<?= base_url('profile') ?>" class="<?= $segment == 'profile' ? 'active' : '' ?>">
                    <i class="fas fa-user-gear"></i> <span>Update Profil</span>
                </a>
            </li>
        </ul>

        <a href="<?= base_url('logout') ?>" class="btn-logout">
            <i class="fas fa-arrow-right-from-bracket"></i> <span>Logout</span>
        </a>
    </div>

    <!-- Top Nav -->
    <?php if (!($no_header ?? false)): ?>
    <div class="top-nav">
        <div class="d-flex align-items-center gap-4">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" id="global-search" placeholder="Cari lahan, aktivitas, atau anggota...">
            </div>
            <div class="d-flex gap-4 ms-4">
                <a href="<?= base_url('peta-gis') ?>" class="nav-link-premium <?= $segment == 'peta-gis' ? 'active' : 'inactive' ?>">Peta Utama</a>
                <a href="<?= base_url('dashboard') ?>" class="nav-link-premium <?= $segment == 'dashboard' ? 'active' : 'inactive' ?>">Dashboard</a>
            </div>
        </div>
        <div class="nav-actions">
            <div class="dropdown">
                <div class="notif-btn" data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside">
                    <i class="far fa-bell"></i>
                    <div class="notif-badge"></div>
                </div>
                    <div class="dropdown-menu dropdown-menu-end notif-dropdown p-0 shadow-lg border-0">
                        <?php 
                            $notifModel = model('NotificationModel');
                            $userId = session()->get('id_user');
                            $unreadNotifs = $notifModel->getUnreadByUser($userId) ?? [];
                        ?>
                        <div class="notif-header d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 fw-800">Notifikasi</h6>
                            <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2" style="font-size: 10px;"><?= count($unreadNotifs) ?> BARU</span>
                        </div>
                        <div class="notif-list-scroll" style="max-height: 380px; overflow-y: auto;">
                            <?php if (empty($unreadNotifs)): ?>
                                <div class="p-4 text-center text-muted small">Tidak ada notifikasi baru.</div>
                            <?php else: ?>
                            <?php foreach ($unreadNotifs as $n): ?>
                                <?php 
                                    $icon = $n['tipe'] === 'danger' ? 'fa-exclamation-triangle' : ($n['tipe'] === 'warning' ? 'fa-exclamation-circle' : 'fa-info-circle');
                                    $color = $n['tipe'] ?: 'info';
                                ?>
                                <a href="<?= base_url('notifications') ?>" class="notif-item-mini unread" onclick="markRead(<?= $n['id_notif'] ?>)">
                                    <div class="notif-icon-box bg-<?= $color ?> bg-opacity-10 text-<?= $color ?>">
                                        <i class="fas <?= $icon ?>"></i>
                                    </div>
                                    <div class="notif-content-box">
                                        <span class="notif-title-mini"><?= esc($n['judul']) ?></span>
                                        <span class="notif-msg-mini"><?= esc($n['pesan']) ?></span>
                                        <span class="notif-time-mini">BARU</span>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    <div class="notif-footer">
                        <a href="<?= base_url('notifications') ?>">Lihat Semua Notifikasi</a>
                    </div>
                </div>
            </div>
            <a href="<?= base_url('profile') ?>" class="user-profile text-decoration-none">
                <div class="user-info">
                    <?php
                        // Fetch latest name directly from DB to prevent session sync delays
                        $dbUser = \Config\Database::connect()->table('users')->select('nama')->where('id_user', session()->get('id_user'))->get()->getRowArray();
                        $currentName = $dbUser ? $dbUser['nama'] : session()->get('nama');
                    ?>
                    <span class="name"><?= esc($currentName) ?></span>
                    <?php
                        $rawRole = session()->get('role');
                        $displayRole = 'Pengguna';
                        if ($rawRole === 'ppl') $displayRole = 'Petugas Lapangan';
                        elseif ($rawRole === 'petani') $displayRole = 'Petani';
                    ?>
                    <span class="role"><?= esc($displayRole) ?></span>
                </div>
                <img src="https://ui-avatars.com/api/?name=<?= urlencode($currentName) ?>&background=155724&color=fff" class="user-avatar" alt="Avatar">
            </a>
        </div>
    </div>
    <?php endif; ?>

    <!-- Main Content -->
    <div class="main-content <?= $no_padding ?? false ? 'no-padding' : '' ?>">
        <div class="content-wrapper">
            <?= $this->renderSection('content') ?>
        </div>
        
        <?php if (!($no_padding ?? false)): ?>
        <div class="footer-mockup">
            <span>AgriMapGIS • High-Fidelity Mockup</span>
            <span>Agriculture Modern • Green / Earth / Gold Palette</span>
        </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@turf/turf@6/turf.min.js"></script>
    <script>
        // Global Fetch Interceptor for CSRF
        const originalFetch = window.fetch;
        window.fetch = async (...args) => {
            const [resource, config] = args;
            const csrfToken = document.querySelector('meta#csrf-token');
            
            if (csrfToken && config && (config.method === 'POST' || config.method === 'PUT' || config.method === 'DELETE')) {
                if (!config.headers) config.headers = {};
                config.headers[csrfToken.name] = csrfToken.content;
            }
            
            const response = await originalFetch(resource, config);
            
            // Optional: Update CSRF token if returned in header (for regenerate = true)
            const newCsrfToken = response.headers.get('X-CSRF-TOKEN');
            if (newCsrfToken && csrfToken) {
                csrfToken.content = newCsrfToken;
                // Update all forms as well if needed
            }
            
            return response;
        };
    </script>
    <script>
        async function fetchNotifications() {
            try {
                const res = await fetch('<?= base_url('notification/api-get') ?>');
                if (!res.ok) return;
                const data = await res.json();
                
                const badge = document.querySelector('.notif-badge');
                const badgeCount = document.querySelector('.notif-header .badge');
                const list = document.querySelector('.notif-list-scroll');
                
                if (data.unread_count > 0) {
                    badge.style.display = 'block';
                    badgeCount.innerText = `${data.unread_count} BARU`;
                } else {
                    badge.style.display = 'none';
                    badgeCount.innerText = `0 BARU`;
                }
                
                if (data.notifications.length > 0) {
                    let html = '';
                    data.notifications.forEach(n => {
                        const icon = n.tipe === 'danger' ? 'fa-exclamation-triangle' : (n.tipe === 'warning' ? 'fa-exclamation-circle' : 'fa-info-circle');
                        const color = n.tipe || 'info';
                        html += `
                            <a href="<?= base_url('notifications') ?>" class="notif-item-mini unread" onclick="markRead(${n.id_notif})">
                                <div class="notif-icon-box bg-${color} bg-opacity-10 text-${color}">
                                    <i class="fas ${icon}"></i>
                                </div>
                                <div class="notif-content-box">
                                    <span class="notif-title-mini">${n.judul}</span>
                                    <span class="notif-msg-mini">${n.pesan}</span>
                                    <span class="notif-time-mini">BARU</span>
                                </div>
                            </a>
                        `;
                    });
                    list.innerHTML = html;
                } else {
                    list.innerHTML = '<div class="p-4 text-center text-muted small">Tidak ada notifikasi baru.</div>';
                }
            } catch (e) { console.error(e); }
        }

        async function markRead(id) {
            fetch(`<?= base_url('notification/mark-read/') ?>${id}`);
        }

        // Fetch every 30 seconds
        fetchNotifications();
        setInterval(fetchNotifications, 30000);

        function toggleSidebar() {
            document.querySelector('.sidebar').classList.toggle('open');
            document.querySelector('.sidebar-overlay').classList.toggle('show');
        }

        // PWA Service Worker Registration
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then(reg => console.log('Service Worker Registered'))
                    .catch(err => console.log('Service Worker Registration Failed:', err));
            });
        }
    </script>
    <?= $this->renderSection('scripts') ?>
</body>
</html>
