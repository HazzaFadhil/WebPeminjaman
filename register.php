<?php
require_once 'koneksi.php';

$pesan_sukses = '';
$pesan_error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Tangkap data dari form
    $id_pengguna  = trim($_POST['id_pengguna']);
    $nama_ormawa  = trim($_POST['nama_ormawa']);
    $email        = trim($_POST['email']);
    $password     = trim($_POST['password']);
    $kontak_pj    = trim($_POST['kontak_pj']);
    $role         = 'mahasiswa'; // Default role untuk pendaftar baru

    if (empty($id_pengguna) || empty($nama_ormawa) || empty($email) || empty($password) || empty($kontak_pj)) {
        $pesan_error = "Semua kolom wajib diisi!";
    } else {
        try {
            // Cek apakah email atau ID sudah terdaftar
            $cek = $pdo->prepare("SELECT * FROM Pengguna WHERE email = :email OR id_pengguna = :id_pengguna");
            $cek->execute([
                'email' => $email,
                'id_pengguna' => $id_pengguna
            ]);
            
            if ($cek->rowCount() > 0) {
                $pesan_error = "ID Pengguna atau Email sudah terdaftar!";
            } else {
                // Masukkan data baru menggunakan Named Parameters (Aman dari error HY093)
                $sql = "INSERT INTO Pengguna (id_pengguna, nama_ormawa, email, password, kontak_pj, role) 
                        VALUES (:id_pengguna, :nama_ormawa, :email, :password, :kontak_pj, :role)";
                
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    'id_pengguna' => $id_pengguna,
                    'nama_ormawa' => $nama_ormawa,
                    'email'       => $email,
                    'password'    => $password,
                    'kontak_pj'   => $kontak_pj,
                    'role'        => $role
                ]);
                
                $pesan_sukses = "Registrasi berhasil! Silakan <a href='login.php'>Login di sini</a>.";
            }
        } catch (PDOException $e) {
            $pesan_error = "Terjadi kesalahan database: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Akun — Horizon University</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #e4e8f0 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 30px 0;
        }
        :root {
            --horizon-maroon: #800000;
            --horizon-dark-maroon: #5a0000;
        }
        .register-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            background: #ffffff;
            border-top: 5px solid var(--horizon-maroon);
        }
        .form-control {
            border-radius: 8px;
            padding: 10px 15px;
        }
        .form-control:focus {
            border-color: var(--horizon-maroon);
            box-shadow: 0 0 0 0.2rem rgba(128, 0, 0, 0.15);
        }
        .btn-register {
            background-color: var(--horizon-maroon);
            color: white;
            padding: 12px;
            border-radius: 30px;
            font-weight: 600;
            border: none;
            width: 100%;
            transition: all 0.3s ease;
        }
        .btn-register:hover {
            background-color: var(--horizon-dark-maroon);
            color: white;
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="register-card p-4 p-md-5">
                    <div class="text-center mb-4">
                        <i class="fa-solid fa-user-plus fa-3x" style="color: var(--horizon-maroon);"></i>
                        <h3 class="fw-bold mt-3">Registrasi Akun Ormawa</h3>
                        <p class="text-muted small">Sistem Peminjaman Fasilitas — Horizon University</p>
                    </div>

                    <?php if (!empty($pesan_sukses)): ?>
                        <div class="alert alert-success"><?= $pesan_sukses ?></div>
                    <?php endif; ?>

                    <?php if (!empty($pesan_error)): ?>
                        <div class="alert alert-danger"><?= $pesan_error ?></div>
                    <?php endif; ?>

                    <form method="POST" action="register.php">
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">ID Pengguna (Contoh: U04)</label>
                            <input type="text" name="id_pengguna" class="form-control" placeholder="U04" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Nama Organisasi / Ormawa</label>
                            <input type="text" name="nama_ormawa" class="form-control" placeholder="HIMTIF / BEM / UKM" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Email Kampus</label>
                            <input type="email" name="email" class="form-control" placeholder="nama@kampus.ac.id" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Password</label>
                            <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-semibold small">Kontak Penanggung Jawab (No. HP/WhatsApp)</label>
                            <input type="text" name="kontak_pj" class="form-control" placeholder="081234567890" required>
                        </div>
                        <button type="submit" class="btn-register">Daftar Sekarang</button>
                    </form>

                    <div class="text-center mt-3">
                        <small class="text-muted">Sudah punya akun? <a href="login.php" style="color: var(--horizon-maroon); text-decoration: none; font-weight: 600;">Login di sini</a></small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>