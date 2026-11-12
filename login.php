<?php
session_start();
require_once 'koneksi.php';

$pesan_error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($email) || empty($password)) {
        $pesan_error = "Email dan password wajib diisi!";
    } else {
        try {
            // Ambil data user berdasarkan email dan password
            $stmt = $pdo->prepare("SELECT * FROM Pengguna WHERE email = ? AND password = ?");
            $stmt->execute([$email, $password]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                // Simpan sesi login
                $_SESSION['user_id'] = $user['id_pengguna'];
                $_SESSION['nama_user'] = $user['nama_ormawa'];
                $_SESSION['role'] = $user['role'] ?? 'mahasiswa'; // Default mahasiswa jika kosong

                // RBAC Redirect Berdasarkan Role
                if ($_SESSION['role'] === 'admin') {
                    header("Location: admin/index.php");
                    exit;
                } else {
                    header("Location: index.php");
                    exit;
                }
            } else {
                $pesan_error = "Email atau password salah!";
            }
        } catch (PDOException $e) {
            $pesan_error = "Terjadi kesalahan sistem: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Sistem — Horizon University</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #e4e8f0 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        :root {
            --horizon-maroon: #800000;
            --horizon-dark-maroon: #5a0000;
        }

        .login-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            background: #ffffff;
            border-top: 5px solid var(--horizon-maroon);
        }

        .form-control {
            border-radius: 8px;
            padding: 11px 15px;
        }

        .form-control:focus {
            border-color: var(--horizon-maroon);
            box-shadow: 0 0 0 0.2rem rgba(128, 0, 0, 0.15);
        }

        .btn-login {
            background-color: var(--horizon-maroon);
            color: white;
            padding: 12px;
            border-radius: 30px;
            font-weight: 600;
            border: none;
            width: 100%;
            transition: all 0.3s ease;
        }

        .btn-login:hover {
            background-color: var(--horizon-dark-maroon);
            color: white;
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="login-card p-4 p-md-5">
                    <div class="text-center mb-4">
                        <i class="fa-solid fa-graduation-cap fa-3x" style="color: var(--horizon-maroon);"></i>
                        <h4 class="fw-bold mt-3">Login Akun</h4>
                        <p class="text-muted small">Sistem Peminjaman Fasilitas Kampus</p>
                    </div>

                    <?php if (!empty($pesan_error)): ?>
                        <div class="alert alert-danger"><?= $pesan_error ?></div>
                    <?php endif; ?>

                    <form method="POST" action="login.php">
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Email Kampus</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i
                                        class="fa-solid fa-envelope text-muted"></i></span>
                                <!-- PASTIKAN ADA name="email" -->
                                <input type="email" name="email" class="form-control border-start-0"
                                    placeholder="nama@kampus.ac.id" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold small">Password</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i
                                        class="fa-solid fa-lock text-muted"></i></span>
                                <!-- PASTIKAN ADA name="password" -->
                                <input type="password" name="password" class="form-control border-start-0"
                                    placeholder="••••••••" required>
                            </div>
                        </div>

                        <button type="submit" class="btn-login mb-3">Masuk</button>
                    </form>

                    <div class="text-center">
                        <small class="text-muted">Belum punya akun ormawa? <a href="register.php"
                                style="color: var(--horizon-maroon); text-decoration: none; font-weight: 600;">Daftar di
                                sini</a></small>
                    </div>
                    <div class="text-center mt-2">
                        <a href="index.php" class="text-muted small" style="text-decoration: none;"><i
                                class="fa-solid fa-arrow-left me-1"></i> Kembali ke Beranda</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>