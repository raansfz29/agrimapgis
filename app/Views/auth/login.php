<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f0fdf4 0%, #ffffff 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-card {
            background: white;
            border-radius: 24px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.05);
            width: 100%;
            max-width: 420px;
            padding: 40px;
            border: 1px solid #f1f5f9;
        }

        .brand-logo {
            font-weight: 800;
            color: #1e7e34;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 24px;
            justify-content: center;
            margin-bottom: 30px;
        }

        .form-label {
            font-weight: 600;
            color: #475569;
            font-size: 14px;
        }

        .form-control {
            border-radius: 12px;
            padding: 12px 16px;
            border: 1px solid #e2e8f0;
            font-size: 15px;
        }

        .form-control:focus {
            border-color: #1e7e34;
            box-shadow: 0 0 0 4px rgba(30, 126, 52, 0.1);
        }

        .btn-login {
            background: #1e7e34;
            color: white;
            border-radius: 12px;
            padding: 14px;
            font-weight: 700;
            border: none;
            width: 100%;
            margin-top: 10px;
            transition: all 0.3s;
        }

        .btn-login:hover {
            background: #155724;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(30, 126, 52, 0.3);
        }

        .register-link {
            text-align: center;
            margin-top: 25px;
            color: #64748b;
            font-size: 14px;
        }

        .register-link a {
            color: #1e7e34;
            font-weight: 700;
            text-decoration: none;
        }

        .alert {
            border-radius: 12px;
            font-size: 14px;
        }
    </style>
</head>
<body>

    <div class="login-card">
        <a href="/" class="brand-logo" style="color: #166534; font-weight: 800;">
            <i class="fas fa-seedling me-2"></i> AgriMapGIS
        </a>
        
        <h4 class="fw-bold text-center mb-1">Selamat Datang</h4>
        <p class="text-muted text-center mb-4 small">Silakan masuk ke akun Anda</p>

        <?php if (session()->getFlashdata('error')) : ?>
            <div class="alert alert-danger py-2"><?= session()->getFlashdata('error') ?></div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('success')) : ?>
            <div class="alert alert-success py-2"><?= session()->getFlashdata('success') ?></div>
        <?php endif; ?>

        <div id="login-section">
            <form action="/login" method="POST">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label class="form-label">Alamat Email</label>
                    <input type="email" name="email" class="form-control" placeholder="nama@email.com" required>
                </div>
                
                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label class="form-label mb-0">Password</label>
                        <a href="javascript:void(0)" onclick="toggleSection('forgot')" class="small text-muted text-decoration-none">Lupa Password?</a>
                    </div>
                    <div class="position-relative">
                        <input type="password" name="password" id="password" class="form-control" placeholder="••••••••" required>
                        <button type="button" class="btn position-absolute end-0 top-50 translate-middle-y border-0 bg-transparent text-muted" onclick="togglePassword('password')">
                            <i class="fas fa-eye" id="eye-password"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn btn-login shadow-sm">Masuk Sekarang</button>
            </form>

            <div class="register-link">
                Belum punya akun? <a href="/register">Register</a>
            </div>
        </div>

        <div id="forgot-section" style="display: none;">
            <h5 class="fw-bold mb-2">Lupa Password?</h5>
            <p class="text-muted small mb-4">Masukkan email Anda untuk menerima instruksi pemulihan kata sandi.</p>
            
            <form action="/forgot-password" method="POST">
                <?= csrf_field() ?>
                <div class="mb-4">
                    <label class="form-label">Email Terdaftar</label>
                    <input type="email" name="email" class="form-control" placeholder="nama@email.com" required>
                </div>
                <button type="submit" class="btn btn-login shadow-sm mb-3">Kirim Link Reset</button>
            </form>
            
            <div class="text-center">
                <a href="javascript:void(0)" onclick="toggleSection('login')" class="small text-muted text-decoration-none fw-bold">Kembali ke Login</a>
            </div>
        </div>

        <script>
            function togglePassword(id) {
                const input = document.getElementById(id);
                const eye = document.getElementById('eye-' + id);
                if (input.type === 'password') {
                    input.type = 'text';
                    eye.classList.replace('fa-eye', 'fa-eye-slash');
                } else {
                    input.type = 'password';
                    eye.classList.replace('fa-eye-slash', 'fa-eye');
                }
            }

            function toggleSection(section) {
                const loginSection = document.getElementById('login-section');
                const forgotSection = document.getElementById('forgot-section');
                
                if (section === 'forgot') {
                    loginSection.style.display = 'none';
                    forgotSection.style.display = 'block';
                } else {
                    loginSection.style.display = 'block';
                    forgotSection.style.display = 'none';
                }
            }
        </script>
    </div>

</body>
</html>