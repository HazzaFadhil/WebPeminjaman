<?php
// Panggil file koneksi database
require_once 'koneksi.php';

$pesan_sukses = '';
$pesan_error = '';

// Ambil parameter id_ruangan jika dikirim dari halaman ruangan.php
$selected_ruangan = isset($_GET['id_ruangan']) ? $_GET['id_ruangan'] : '';

// Jika tombol submit dikirim
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_pengguna   = trim($_POST['id_pengguna']);
    $id_ruangan    = trim($_POST['id_ruangan']);
    $tgl_pinjam    = trim($_POST['tgl_pinjam']);
    $jam_mulai     = trim($_POST['jam_mulai']);
    $jam_selesai   = trim($_POST['jam_selesai']);
    $tujuan        = trim($_POST['tujuan']);
    
    // Data otomatis sistem
    $id_peminjaman = 'P' . rand(100, 999); // Generate ID acak sederhana, misal P789
    $tgl_pengajuan = date('Y-m-d');        // Tanggal hari ini
    $status        = 'Pending';            // Status awal wajib Pending

    // Validasi sederhana
    if (empty($id_pengguna) || empty($id_ruangan) || empty($tgl_pinjam) || empty($jam_mulai) || empty($jam_selesai) || empty($tujuan)) {
        $pesan_error = "Semua kolom form wajib diisi!";
    } else {
        try {
            $sql = "INSERT INTO Peminjaman (id_peminjaman, tgl_pengajuan, tgl_pinjam, jam_mulai, jam_selesai, tujuan, status, id_pengguna, id_ruangan) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id_peminjaman, $tgl_pengajuan, $tgl_pinjam, $jam_mulai, $jam_selesai, $tujuan, $status, $id_pengguna, $id_ruangan]);
            
            $pesan_sukses = "Pengajuan peminjaman berhasil dikirim! Status saat ini: Pending.";
        } catch (PDOException $e) {
            $pesan_error = "Gagal menyimpan data: " . $e->getMessage();
        }
    }
}

// Ambil data Pengguna/Ormawa untuk dropdown
$stmt_pengguna = $pdo->query("SELECT * FROM Pengguna ORDER BY nama_ormawa ASC");
$list_pengguna = $stmt_pengguna->fetchAll(PDO::FETCH_ASSOC);

// Ambil data Ruangan untuk dropdown
$stmt_ruangan = $pdo->query("SELECT r.*, g.nama_gedung FROM Ruangan r JOIN Gedung g ON r.id_gedung = g.id_gedung ORDER BY r.nama_ruangan ASC");
$list_ruangan = $stmt_ruangan->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Pengajuan Peminjaman — Horizon University</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts (Poppins) -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
        }

        :root {
            --horizon-maroon: #800000;
            --horizon-dark-maroon: #5a0000;
        }

        .navbar-horizon {
            background-color: var(--horizon-maroon);
            padding: 12px 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.15);
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.2rem;
        }

        .navbar-brand small {
            display: block;
            font-size: 0.65rem;
            font-weight: 400;
            letter-spacing: 2px;
        }

        .nav-link {
            color: rgba(255, 255, 255, 0.9) !important;
            font-weight: 500;
            margin: 0 5px;
            padding: 8px 18px !important;
            border-radius: 20px;
            transition: all 0.3s ease;
        }

        .nav-link:hover, .nav-link.active {
            background-color: #ffffff !important;
            color: var(--horizon-maroon) !important;
            font-weight: 600;
        }

        /* Form Container Styling (Sesuai Gambar 1) */
        .form-card-container {
            background: #ffffff;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            overflow: hidden;
            margin-top: 40px;
            margin-bottom: 50px;
            border-top: 5px solid var(--horizon-maroon);
        }

        .form-header-title {
            background-color: #fff;
            color: #1a1a1a;
            padding: 25px 30px;
            border-bottom: 1px solid #eee;
            font-weight: 700;
            font-size: 1.35rem;
        }

        .form-header-title i {
            color: var(--horizon-maroon);
        }

        .form-body-content {
            padding: 35px 30px;
        }

        .form-label-custom {
            font-weight: 600;
            color: #333;
            font-size: 0.95rem;
        }

        .form-label-custom i {
            color: var(--horizon-maroon);
            width: 25px;
            text-align: center;
        }

        .form-control, .form-select {
            border-radius: 8px;
            padding: 10px 15px;
            border: 1px solid #ced4da;
            transition: all 0.2s;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--horizon-maroon);
            box-shadow: 0 0 0 0.2rem rgba(128, 0, 0, 0.15);
        }

        /* Tombol Kirim Pengajuan */
        .btn-submit-horizon {
            background-color: var(--horizon-maroon);
            color: white;
            padding: 12px 35px;
            border-radius: 30px;
            font-weight: 600;
            border: none;
            box-shadow: 0 4px 15px rgba(128, 0, 0, 0.3);
            transition: all 0.3s ease;
        }

        .btn-submit-horizon:hover {
            background-color: var(--horizon-dark-maroon);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(128, 0, 0, 0.4);
        }
    </style>
</head>
<body>

    <!-- NAVBAR UTAMA -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-horizon sticky-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="index.php">
                <img src="path/to/logo.png" alt="Logo" width="40" height="40" class="me-2 d-inline-block align-text-top object-fit-contain" onerror="this.style.display='none'">
                <div>
                    HORIZON UNIVERSITY
                    <small>KARAWANG</small>
                </div>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item"><a class="nav-link" href="index.php">Beranda</a></li>
                    <li class="nav-item"><a class="nav-link" href="ruangan.php">Ruangan</a></li>
                    <li class="nav-item"><a class="nav-link active" href="tambah.php">Peminjaman</a></li>
                    <li class="nav-item ms-lg-2"><a class="nav-link text-white" href="login.php"><i class="fa-solid fa-right-to-bracket me-1"></i> Login</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- KONTEN FORM PEMINJAMAN -->
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-9">
                
                <div class="form-card-container">
                    <div class="form-header-title d-flex align-items-center">
                        <i class="fa-solid fa-calendar-days fs-4 me-3"></i>
                        <span>Form Pengajuan Peminjaman Fasilitas — Horizon University</span>
                    </div>

                    <div class="form-body-content">
                        
                        <!-- Notifikasi Pesan -->
                        <?php if (!empty($pesan_sukses)): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fa-solid fa-circle-check me-2"></i> <?= $pesan_sukses ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($pesan_error)): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fa-solid fa-triangle-exclamation me-2"></i> <?= $pesan_error ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <!-- FORM HTML (Sesuai Desain Gambar 1) -->
                        <form method="POST" action="tambah.php">
                            
                            <!-- 1. Nama Organisasi / Kelompok -->
                            <div class="row mb-4 align-items-center">
                                <label class="col-md-4 form-label-custom">
                                    <i class="fa-solid fa-users"></i> Nama Organisasi/Kelompok
                                </label>
                                <div class="col-md-8">
                                    <select name="id_pengguna" class="form-select" required>
                                        <option value="">-- Pilih Organisasi / Ormawa --</option>
                                        <?php foreach ($list_pengguna as $p): ?>
                                            <option value="<?= $p['id_pengguna'] ?>">
                                                <?= htmlspecialchars($p['nama_ormawa']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <!-- 2. Pilih Ruangan -->
                            <div class="row mb-4 align-items-center">
                                <label class="col-md-4 form-label-custom">
                                    <i class="fa-solid fa-door-open"></i> Pilih Ruangan
                                </label>
                                <div class="col-md-8">
                                    <select name="id_ruangan" class="form-select" required>
                                        <option value="">-- Pilih Ruangan / Fasilitas --</option>
                                        <?php foreach ($list_ruangan as $r): ?>
                                            <option value="<?= $r['id_ruangan'] ?>" <?= ($selected_ruangan == $r['id_ruangan']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($r['nama_ruangan']) ?> (<?= htmlspecialchars($r['nama_gedung']) ?> - Kapasitas: <?= $r['kapasitas'] ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <!-- 3. Tanggal Peminjaman -->
                            <div class="row mb-4 align-items-center">
                                <label class="col-md-4 form-label-custom">
                                    <i class="fa-solid fa-calendar-alt"></i> Tanggal Peminjaman
                                </label>
                                <div class="col-md-8">
                                    <input type="date" name="tgl_pinjam" class="form-control" required>
                                </div>
                            </div>

                            <!-- 4. Jam Mulai -->
                            <div class="row mb-4 align-items-center">
                                <label class="col-md-4 form-label-custom">
                                    <i class="fa-solid fa-clock"></i> Jam Mulai
                                </label>
                                <div class="col-md-8">
                                    <input type="time" name="jam_mulai" class="form-control" required>
                                </div>
                            </div>

                            <!-- 5. Jam Selesai -->
                            <div class="row mb-4 align-items-center">
                                <label class="col-md-4 form-label-custom">
                                    <i class="fa-solid fa-clock-rotate-left"></i> Jam Selesai
                                </label>
                                <div class="col-md-8">
                                    <input type="time" name="jam_selesai" class="form-control" required>
                                </div>
                            </div>

                            <!-- 6. Tujuan Penggunaan -->
                            <div class="row mb-4 align-items-start">
                                <label class="col-md-4 form-label-custom pt-2">
                                    <i class="fa-solid fa-file-lines"></i> Tujuan Penggunaan
                                </label>
                                <div class="col-md-8">
                                    <textarea name="tujuan" rows="4" class="form-control" placeholder="Jelaskan tujuan penggunaan fasilitas..." required></textarea>
                                </div>
                            </div>

                            <!-- Tombol Aksi -->
                            <div class="row">
                                <div class="col-md-8 offset-md-4">
                                    <button type="submit" class="btn-submit-horizon">
                                        <i class="fa-solid fa-paper-plane me-2"></i> Kirim Pengajuan
                                    </button>
                                </div>
                            </div>

                        </form>

                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>