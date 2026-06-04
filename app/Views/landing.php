<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AgriMapGIS - Transformasi Pertanian Presisi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="/favicon.png">
    <style>
        :root {
            --primary-green: #166534;
            --secondary-green: #15803d;
            --accent-green: #dcfce7;
            --dark-bg: #052e16;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --font-main: 'Outfit', sans-serif;
            --nav-height: 90px;
        }

        body {
            font-family: var(--font-main);
            color: var(--text-main);
            overflow-x: hidden;
            background-color: #ffffff;
            -webkit-font-smoothing: antialiased;
        }

        /* Navbar Styling */
        .navbar {
            min-height: var(--nav-height);
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(0,0,0,0.05);
            transition: all 0.4s ease;
            display: flex;
            align-items: center;
        }
        .navbar.scrolled {
            min-height: 75px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.04);
        }
        .navbar-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 800;
            font-size: 26px;
            color: var(--primary-green) !important;
            letter-spacing: -1px;
        }
        .nav-link {
            font-weight: 600;
            color: #475569 !important;
            font-size: 15px;
            margin: 0 18px;
            transition: color 0.3s;
        }
        .nav-link:hover { color: var(--primary-green) !important; }
        
        .btn-auth-login {
            font-weight: 700;
            color: #475569;
            text-decoration: none;
            margin-right: 30px;
            font-size: 15px;
            transition: color 0.3s;
        }
        .btn-auth-login:hover { color: var(--primary-green); }
        
        .btn-auth-register {
            background: var(--dark-bg);
            color: white;
            padding: 14px 32px;
            border-radius: 14px;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.3s;
            box-shadow: 0 10px 30px rgba(5, 46, 22, 0.2);
            font-size: 15px;
        }
        .btn-auth-register:hover {
            transform: translateY(-3px);
            box-shadow: 0 20px 40px rgba(5, 46, 22, 0.3);
            color: white;
            background: var(--primary-green);
        }

        /* Hero Section */
        .hero-section {
            padding: calc(var(--nav-height) + 80px) 0 140px;
            background: radial-gradient(circle at top right, #f0fdf4, transparent 40%),
                        radial-gradient(circle at bottom left, #f8fafc, transparent 40%);
        }
        .hero-badge {
            background: var(--accent-green);
            color: var(--primary-green);
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 10px 20px;
            border-radius: 100px;
            font-weight: 800;
            font-size: 12px;
            margin-bottom: 30px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            border: 1px solid rgba(22, 101, 52, 0.1);
        }
        .hero-title {
            font-size: 72px;
            font-weight: 900;
            line-height: 1.05;
            color: var(--dark-bg);
            margin-bottom: 30px;
            letter-spacing: -2px;
        }
        .hero-subtitle {
            font-size: 19px;
            color: var(--text-muted);
            margin-bottom: 50px;
            line-height: 1.7;
            max-width: 580px;
        }
        .hero-btns { display: flex; gap: 24px; }
        .btn-hero-primary {
            background: var(--dark-bg);
            color: white;
            padding: 18px 40px;
            border-radius: 16px;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.3s;
            font-size: 16px;
        }
        .btn-hero-secondary {
            background: white;
            color: var(--primary-green);
            border: 2px solid var(--primary-green);
            padding: 18px 40px;
            border-radius: 16px;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.3s;
            font-size: 16px;
        }
        .btn-hero-primary:hover { transform: translateY(-4px); box-shadow: 0 20px 40px rgba(0,0,0,0.15); color: white; }
        .btn-hero-secondary:hover { background: var(--accent-green); }

        .hero-image-wrapper {
            position: relative;
            padding-left: 40px;
        }
        .hero-main-img {
            width: 100%;
            border-radius: 40px;
            box-shadow: 0 60px 120px rgba(0,0,0,0.15);
            transform: perspective(1000px) rotateY(-5deg) rotateX(2deg);
            transition: all 0.6s ease;
        }
        .hero-main-img:hover {
            transform: perspective(1000px) rotateY(0deg) rotateX(0deg);
        }

        /* Features Section */
        .features-premium { padding: 120px 0; background: #ffffff; }
        .section-tag {
            text-align: center;
            font-weight: 900;
            font-size: 42px;
            color: var(--dark-bg);
            margin-bottom: 15px;
            letter-spacing: -1px;
        }
        .section-desc {
            text-align: center;
            color: var(--text-muted);
            max-width: 650px;
            margin: 0 auto 80px;
            font-size: 17px;
        }

        .feature-grid { 
            display: grid; 
            grid-template-columns: repeat(2, 1fr); 
            gap: 40px; 
        }
        .feature-card-premium {
            background: #f8fafc;
            border-radius: 32px;
            overflow: hidden;
            border: 1px solid rgba(0,0,0,0.03);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .feature-card-premium:hover {
            transform: translateY(-12px);
            box-shadow: 0 30px 60px rgba(0,0,0,0.06);
            border-color: var(--accent-green);
        }
        .feature-card-premium.dark { 
            background: var(--dark-bg); 
            color: white; 
            padding-bottom: 40px;
        }
        
        .feature-card-content { padding: 50px; }
        .feature-card-premium h3 { font-weight: 800; font-size: 28px; margin-bottom: 18px; letter-spacing: -0.5px; }
        .feature-card-premium p { font-size: 16px; opacity: 0.8; line-height: 1.7; margin-bottom: 0; }
        
        .feature-card-img { 
            width: 100%; 
            height: 350px; 
            object-fit: cover;
            border-radius: 20px;
            margin-top: 10px;
        }
        
        .feature-icon-box {
            width: 64px;
            height: 64px;
            background: white;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary-green);
            font-size: 26px;
            margin-bottom: 30px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.05);
        }

        /* Dashboard Section */
        .dashboard-promo { padding: 140px 0; background: #fafafa; position: relative; }
        .promo-list { list-style: none; padding: 0; }
        .promo-list li { 
            display: flex; 
            gap: 20px; 
            margin-bottom: 35px; 
            padding: 25px;
            background: white;
            border-radius: 24px;
            border: 1px solid rgba(0,0,0,0.02);
            transition: all 0.3s;
        }
        .promo-list li:hover { transform: translateX(10px); box-shadow: 0 10px 30px rgba(0,0,0,0.03); }
        .promo-list li i { 
            width: 45px;
            height: 45px;
            background: var(--accent-green);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary-green);
            font-size: 18px;
            flex-shrink: 0;
        }
        .promo-list h6 { font-weight: 800; margin-bottom: 8px; font-size: 18px; }
        .promo-list p { font-size: 15px; color: var(--text-muted); margin: 0; line-height: 1.6; }

        .mockup-img {
            width: 110%;
            margin-left: -10%;
            border-radius: 30px;
            box-shadow: 0 40px 80px rgba(0,0,0,0.1);
        }

        /* CTA Section */
        .cta-final {
            margin: 120px 0;
            background: linear-gradient(135deg, var(--dark-bg) 0%, var(--primary-green) 100%);
            border-radius: 50px;
            padding: 100px 50px;
            text-align: center;
            color: white;
            position: relative;
            overflow: hidden;
            box-shadow: 0 30px 60px rgba(5, 46, 22, 0.3);
        }
        .cta-final h2 { font-size: 48px; font-weight: 900; margin-bottom: 25px; letter-spacing: -1.5px; }
        .cta-final p { font-size: 19px; opacity: 0.8; max-width: 650px; margin: 0 auto 50px; line-height: 1.7; }
        .btn-cta-white {
            background: white;
            color: var(--primary-green);
            padding: 20px 45px;
            border-radius: 18px;
            font-weight: 800;
            text-decoration: none;
            transition: all 0.3s;
            display: inline-block;
        }
        .btn-cta-white:hover { transform: scale(1.05); box-shadow: 0 15px 30px rgba(255,255,255,0.2); }

        /* Footer */
        footer { padding: 120px 0 60px; background: #ffffff; border-top: 1px solid #f1f5f9; }
        .footer-brand { font-weight: 800; font-size: 28px; color: var(--primary-green); margin-bottom: 25px; display: block; text-decoration: none; letter-spacing: -1px; }
        .footer-links h5 { font-weight: 800; font-size: 17px; margin-bottom: 30px; color: var(--dark-bg); }
        .footer-links ul { list-style: none; padding: 0; }
        .footer-links li { margin-bottom: 15px; }
        .footer-links a { text-decoration: none; color: var(--text-muted); font-size: 15px; font-weight: 600; transition: color 0.3s; }
        .footer-links a:hover { color: var(--primary-green); }

        @media (max-width: 1200px) {
            .mockup-img { width: 100%; margin-left: 0; }
        }
        @media (max-width: 992px) {
            .hero-title { font-size: 52px; }
            .feature-grid { grid-template-columns: 1fr; }
            .hero-btns { flex-direction: column; }
            .hero-image-wrapper { padding-left: 0; margin-top: 60px; }
            .navbar { min-height: 80px; height: auto; }
            .navbar-collapse {
                background: white;
                margin-top: 15px;
                padding: 20px;
                border-radius: 24px;
                box-shadow: 0 20px 40px rgba(0,0,0,0.1);
                border: 1px solid rgba(0,0,0,0.05);
            }
            .navbar-nav { padding: 10px 0; }
            .nav-link { margin: 10px 0; padding: 5px 0; }
            .btn-auth-login { margin-right: 0; margin-bottom: 15px; display: block; }
            .navbar-collapse .d-flex { flex-direction: column; align-items: stretch !important; gap: 10px; }
            .btn-auth-register { text-align: center; }
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg fixed-top" id="mainNav">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-seedling"></i> AgriMapGIS
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <i class="fas fa-bars-staggered"></i>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item"><a class="nav-link" href="#fitur">Features</a></li>
                    <li class="nav-item"><a class="nav-link" href="#dashboard">Solutions</a></li>

                    <li class="nav-item"><a class="nav-link" href="mailto:support@agrimapgis.test">Contact Us</a></li>
                </ul>
                <div class="d-flex align-items-center">
                    <a href="/login" class="btn-auth-login">Masuk</a>
                    <a href="/register" class="btn-auth-register shadow">Register</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="hero-badge shadow-sm">
                        <i class="fas fa-bolt-lightning text-warning"></i> Solusi Pertanian 4.0 Terdepan
                    </div>
                    <h1 class="hero-title">Transformasi Pertanian dengan Data Presisi</h1>
                    <p class="hero-subtitle">
                        Kelola lahan Anda dengan teknologi GIS modern. Pantau kesehatan tanaman, catat aktivitas harian, dan analisis produktivitas dalam satu platform terpadu.
                    </p>
                    <div class="hero-btns">
                        <a href="/register" class="btn-hero-primary shadow-lg">Mulai Sekarang <i class="fas fa-arrow-right ms-2"></i></a>
                        <a href="#fitur" class="btn-hero-secondary">Eksplorasi Fitur</a>
                    </div>
                </div>
                <div class="col-lg-6 hero-image-wrapper">
                    <img src="/assets/images/landing/hero.png" alt="Precision Agriculture" class="hero-main-img">
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features-premium" id="fitur">
        <div class="container">
            <h2 class="section-tag">Fitur Utama AgriMapGIS</h2>
            <p class="section-desc">Platform cerdas yang dirancang khusus untuk meningkatkan efisiensi operasional dan hasil panen Anda secara berkelanjutan.</p>
            
            <div class="feature-grid">
                <!-- Feature 1: Mapping -->
                <div class="feature-card-premium">
                    <div class="feature-card-content">
                        <div class="feature-icon-box"><i class="fas fa-map-location-dot"></i></div>
                        <h3>Pemetaan Lahan Interaktif</h3>
                        <p>Visualisasikan batas lahan dan zonasi komoditas dengan akurasi tinggi menggunakan integrasi teknologi PostGIS dan Leaflet.</p>
                    </div>
                    <div class="px-5 pb-5">
                        <img src="/assets/images/landing/mapping.png" alt="Mapping" class="feature-card-img shadow-sm">
                    </div>
                </div>

                <!-- Feature 2: NDVI -->
                <div class="feature-card-premium dark">
                    <div class="feature-card-content">
                        <div class="feature-icon-box" style="background: rgba(255,255,255,0.1); color: #fff;"><i class="fas fa-microscope"></i></div>
                        <h3>Monitoring Kesehatan (NDVI)</h3>
                        <p>Deteksi dini anomali tanaman melalui indeks vegetasi. Identifikasi area yang membutuhkan perhatian khusus sebelum masalah meluas.</p>
                        <div class="mt-5">
                            <img src="/assets/images/landing/ndvi.png" alt="NDVI Health" class="img-fluid rounded-4 shadow-sm opacity-75">
                        </div>
                    </div>
                </div>

                <!-- Feature 3: History -->
                <div class="feature-card-premium">
                    <div class="feature-card-content">
                        <div class="feature-icon-box"><i class="fas fa-clipboard-list"></i></div>
                        <h3>Digitalisasi Riwayat Lahan</h3>
                        <p>Dokumentasikan setiap tahap penanaman, pemupukan, hingga panen secara digital. Bangun basis data pengetahuan untuk musim mendatang.</p>
                    </div>
                </div>

                <!-- Feature 4: Alert -->
                <div class="feature-card-premium">
                    <div class="feature-card-content">
                        <div class="feature-icon-box"><i class="fas fa-triangle-exclamation"></i></div>
                        <h3>Smart Warning System</h3>
                        <p>Dapatkan peringatan otomatis terkait anomali pertumbuhan atau potensi gangguan di wilayah Rajabasa secara real-time.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Dashboard Preview -->
    <section class="dashboard-promo" id="dashboard">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-7">
                    <img src="/assets/images/landing/dashboard.png" alt="Dashboard View" class="mockup-img">
                </div>
                <div class="col-lg-5 ps-lg-5 mt-5 mt-lg-0">
                    <div class="hero-badge mb-3">Manajemen Modern</div>
                    <h2 class="fw-900 mb-4" style="font-size: 44px; color: var(--dark-bg); letter-spacing: -1.5px;">Kendali Penuh di Ujung Jari Anda</h2>
                    <ul class="promo-list">
                        <li>
                            <i class="fas fa-layer-group"></i>
                            <div>
                                <h6>Multi-Layer Data</h6>
                                <p>Gabungkan data spasial, cuaca, dan kondisi tanah dalam satu tampilan peta yang komprehensif.</p>
                            </div>
                        </li>
                        <li>
                            <i class="fas fa-clock-rotate-left"></i>
                            <div>
                                <h6>Timeline Aktivitas</h6>
                                <p>Pantau perkembangan lahan dari hari ke hari dengan laporan aktivitas yang terverifikasi lokasi (Geofencing).</p>
                            </div>
                        </li>
                        <li>
                            <i class="fas fa-chart-pie"></i>
                            <div>
                                <h6>Analisis Produktivitas</h6>
                                <p>Dapatkan estimasi hasil panen dan analisis KPI untuk mengoptimalkan keuntungan usaha tani Anda.</p>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Values -->
    <section class="values-section py-5">
        <div class="container py-5">
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="value-card">
                        <div class="value-icon"><i class="fas fa-leaf"></i></div>
                        <h4>Keberlanjutan</h4>
                        <p class="small text-muted">Meningkatkan hasil produksi tanpa merusak ekosistem tanah melalui input yang presisi.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="value-card">
                        <div class="value-icon"><i class="fas fa-brain"></i></div>
                        <h4>Kecerdasan Buatan</h4>
                        <p class="small text-muted">Memanfaatkan data historis dan sensor untuk memberikan rekomendasi penanaman terbaik.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="value-card">
                        <div class="value-icon"><i class="fas fa-users-gear"></i></div>
                        <h4>Kolaborasi PPL</h4>
                        <p class="small text-muted">Memudahkan koordinasi antara petani dan petugas penyuluh lapangan dalam satu ekosistem.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <div class="container mb-5">
        <section class="cta-final">
            <h2 class="mb-3">Mulai Optimalkan Lahan Anda Sekarang</h2>
            <p class="mb-5">Bergabunglah dengan ratusan petani lainnya yang telah bertransformasi ke arah digital. Daftar gratis untuk wilayah Rajabasa.</p>
            <div class="d-flex justify-content-center gap-3 flex-wrap">
                <a href="/register" class="btn-cta-white">Register Now</a>
                <a href="mailto:support@agrimapgis.test" class="btn btn-outline-light rounded-pill px-5 py-3 fw-800">Hubungi Kami</a>
            </div>
        </section>
    </div>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-5 mb-lg-0 pe-lg-5">
                    <a href="#" class="footer-brand"><i class="fas fa-seedling" style="color: #166534;"></i> AgriMapGIS</a>
                    <p class="text-muted small lh-lg">Platform sistem informasi geografis pertanian terpadu. Memberdayakan petani dengan teknologi presisi untuk masa depan kedaulatan pangan Indonesia.</p>
                    <div class="d-flex gap-4 mt-4">
                        <a href="#" class="text-muted fs-5"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-muted fs-5"><i class="fab fa-linkedin"></i></a>
                        <a href="#" class="text-muted fs-5"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
                <div class="col-6 col-lg-2 footer-links">
                    <h5>Navigasi</h5>
                    <ul>
                        <li><a href="#fitur">Fitur Utama</a></li>
                        <li><a href="/peta-gis">Peta Spasial</a></li>
                        <li><a href="/dashboard">Dashboard</a></li>
                        <li><a href="/">Tentang Kami</a></li>
                    </ul>
                </div>
                <div class="col-6 col-lg-2 footer-links">
                    <h5>Resource</h5>
                    <ul>
                        <li><a href="/">Panduan Petani</a></li>
                        <li><a href="/">Dokumentasi PPL</a></li>
                        <li><a href="/">Kebijakan Privasi</a></li>
                        <li><a href="/">Terms of Use</a></li>
                    </ul>
                </div>
                <div class="col-lg-4 footer-links">
                    <h5>Kantor Pusat</h5>
                    <p class="small text-muted mb-4 lh-lg">Kecamatan Rajabasa, Kabupaten Lampung Selatan, Lampung, Indonesia. 35144.</p>
                    <a href="mailto:support@agrimapgis.test" class="text-decoration-none fw-800 text-success fs-5">support@agrimapgis.test</a>
                </div>
            </div>
            <div class="border-top mt-5 pt-5 text-center">
                <p class="text-muted mb-0" style="font-size: 13px; font-weight: 500;">&copy; 2026 AgriMapGIS Intelligence. Precision of the Earth. Developed for Agricultural Excellence.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        window.addEventListener('scroll', function() {
            const nav = document.getElementById('mainNav');
            if (window.scrollY > 80) {
                nav.classList.add('scrolled');
            } else {
                nav.classList.remove('scrolled');
            }
        });
    </script>
</body>
</html>
