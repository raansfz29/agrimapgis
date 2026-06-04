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
            padding: 40px 0;
        }

        .register-card {
            background: white;
            border-radius: 24px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.05);
            width: 100%;
            max-width: 500px;
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

        .form-control, .form-select {
            border-radius: 12px;
            padding: 12px 16px;
            border: 1px solid #e2e8f0;
            font-size: 15px;
        }

        .form-control:focus {
            border-color: #1e7e34;
            box-shadow: 0 0 0 4px rgba(30, 126, 52, 0.1);
        }

        .btn-register {
            background: #1e7e34;
            color: white;
            border-radius: 12px;
            padding: 14px;
            font-weight: 700;
            border: none;
            width: 100%;
            margin-top: 20px;
            transition: all 0.3s;
        }

        .btn-register:hover {
            background: #155724;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(30, 126, 52, 0.3);
        }

        .login-link {
            text-align: center;
            margin-top: 25px;
            color: #64748b;
            font-size: 14px;
        }

        .login-link a {
            color: #1e7e34;
            font-weight: 700;
            text-decoration: none;
        }
    </style>
</head>
<body>

    <div class="register-card">
        <a href="/" class="brand-logo" style="color: #166534; font-weight: 800;">
            <i class="fas fa-seedling me-2"></i> AgriMapGIS
        </a>
        
        <h4 class="fw-bold text-center mb-1">Daftar Akun Baru</h4>
        <p class="text-muted text-center mb-4 small">Mendaftar sebagai Petugas Penyuluh Lapangan (PPL)</p>

        <?php if (session()->getFlashdata('error')) : ?>
            <div class="alert alert-danger py-2 small"><?= session()->getFlashdata('error') ?></div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('errors')) : ?>
            <div class="alert alert-danger py-2 small">
                <ul class="mb-0">
                <?php foreach (session()->getFlashdata('errors') as $error) : ?>
                    <li><?= $error ?></li>
                <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="/register" method="POST">
            <?= csrf_field() ?>
            <div class="mb-3">
                <label class="form-label">Nama Lengkap</label>
                <input type="text" name="nama" class="form-control" placeholder="Masukkan nama lengkap" value="<?= old('nama') ?>" required>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" placeholder="nama@email.com" value="<?= old('email') ?>" required>
            </div>



            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Password</label>
                    <div class="position-relative">
                        <input type="password" name="password" id="password" class="form-control" placeholder="••••••••" required>
                        <button type="button" class="btn position-absolute end-0 top-50 translate-middle-y border-0 bg-transparent text-muted" onclick="togglePassword('password')">
                            <i class="fas fa-eye" id="eye-password"></i>
                        </button>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Konfirmasi</label>
                    <div class="position-relative">
                        <input type="password" name="password_confirm" id="password_confirm" class="form-control" placeholder="••••••••" required>
                        <button type="button" class="btn position-absolute end-0 top-50 translate-middle-y border-0 bg-transparent text-muted" onclick="togglePassword('password_confirm')">
                            <i class="fas fa-eye" id="eye-password_confirm"></i>
                        </button>
                    </div>
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
            </script>



            <button type="submit" class="btn btn-register">Daftar Sekarang</button>
        </form>

        <div class="login-link">
            Sudah punya akun? <a href="/login">Masuk di sini</a>
        </div>
    </div>

</body>
</html>
