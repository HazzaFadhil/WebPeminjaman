<?php
session_start();
require_once 'koneksi.php';

$pesan_sukses = '';
$pesan_error = '';

$selected_ruangan = isset($_GET['id_ruangan'])
    ? $_GET['id_ruangan'] : '';

// 1. Cek login sejak awal halaman diakses
if (empty($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$nama_ormawa_aktif = $_SESSION['nama_user'] ?? 'Organisasi / Pengguna';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Kunci id_pengguna dari session user yang sedang login
    $id_pengguna = $_SESSION['user_id'];
    $id_ruangan = trim($_POST['id_ruangan']);
    $tgl_pinjam = trim($_POST['tgl_pinjam']);
    $jam_mulai = trim($_POST['jam_mulai']);
    $jam_selesai = trim($_POST['jam_selesai']);
    $tujuan = trim($_POST['tujuan']);

    $id_peminjaman = 'P' . rand(100, 999);
    $tgl_pengajuan = date('Y-m-d');
    $status = 'Pending';

    if (
        empty($id_pengguna) ||
        empty($id_ruangan) ||
        empty($tgl_pinjam) ||
        empty($jam_mulai) ||
        empty($jam_selesai) ||
        empty($tujuan)
    ) {
        $pesan_error = "Semua kolom form wajib diisi!";

    } elseif ($jam_mulai >= $jam_selesai) {
        $pesan_error = "Jam selesai harus lebih besar dari jam mulai!";

    } else {

        try {

            // CEK JADWAL YANG BENTROK
            $sqlCek = "SELECT COUNT(*)
                       FROM Peminjaman
                       WHERE id_ruangan = ?
                       AND tgl_pinjam = ?
                       AND status IN ('Pending', 'Disetujui')
                       AND jam_mulai < ?
                       AND jam_selesai > ?";

            $stmtCek = $pdo->prepare($sqlCek);

            $stmtCek->execute([
                $id_ruangan,
                $tgl_pinjam,
                $jam_selesai,
                $jam_mulai
            ]);

            $jumlahBentrok = $stmtCek->fetchColumn();

            if ($jumlahBentrok > 0) {

                $pesan_error = "Maaf, ruangan sudah memiliki pengajuan peminjaman pada jadwal tersebut. Silakan pilih jam yang berbeda.";

            } else {

                // SIMPAN PENGAJUAN
                $sql = "INSERT INTO Peminjaman
                        (id_peminjaman, tgl_pengajuan, tgl_pinjam,
                         jam_mulai, jam_selesai, tujuan, status,
                         id_pengguna, id_ruangan)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

                $stmt = $pdo->prepare($sql);

                $stmt->execute([
                    $id_peminjaman,
                    $tgl_pengajuan,
                    $tgl_pinjam,
                    $jam_mulai,
                    $jam_selesai,
                    $tujuan,
                    $status,
                    $id_pengguna,
                    $id_ruangan
                ]);

                $_SESSION['flash_sukses'] =
                    "Pengajuan peminjaman berhasil dikirim! Status saat ini: Pending.";

                header("Location: progress.php");
                exit;
            }

        } catch (PDOException $e) {
            $pesan_error = "Gagal menyimpan data: " . $e->getMessage();
        }
    }
}

// AMBIL SEMUA RUANGAN
// Jangan membatasi hanya ruangan dengan ketersediaan Tersedia
$stmt_ruangan = $pdo->query(
    "SELECT r.*, g.nama_gedung
     FROM ruangan r
     JOIN Gedung g ON r.id_gedung = g.id_gedung
     ORDER BY r.nama_ruangan ASC"
);

$list_ruangan = $stmt_ruangan->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Pengajuan Peminjaman — Horizon University</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

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
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.15);
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

        .nav-link:hover,
        .nav-link.active {
            background-color: #ffffff !important;
            color: var(--horizon-maroon) !important;
            font-weight: 600;
        }

        .form-card-container {
            background: #ffffff;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
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

        .form-control,
        .form-select {
            border-radius: 8px;
            padding: 10px 15px;
            border: 1px solid #ced4da;
            transition: all 0.2s;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--horizon-maroon);
            box-shadow: 0 0 0 0.2rem rgba(128, 0, 0, 0.15);
        }

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

        .form-select {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            padding-right: 45px !important;
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-dark navbar-horizon sticky-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="index.php">
                <img src="asset/logohorizon-removebg-preview.png" alt="Logo" width="50" height="50"
                    class="me-2 d-inline-block align-text-top object-fit-contain" onerror="this.style.display='none'">
                <div>
                    HORIZON UNIVERSITY
                    <small>INDONESIA</small>
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
                    <li class="nav-item ms-lg-2">
                        <a class="nav-link btn btn-progress" href="progress.php">Progress</a>
                    </li>
                    <li class="nav-item ms-lg-2"><a class="nav-link text-white" href="login.php"><i
                                class="fa-solid fa-right-to-bracket me-1"></i> Login</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-9">

                <div class="form-card-container">
                    <div class="form-header-title d-flex align-items-center">
                        <i class="fa-solid fa-calendar-days fs-4 me-3"></i>
                        <span>Form Pengajuan Peminjaman Fasilitas — Horizon University</span>
                    </div>

                    <div class="form-body-content">

                        <?php if (!empty($pesan_sukses)): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fa-solid fa-circle-check me-2"></i> <?= $pesan_sukses ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif ?>

                        <?php if (!empty($pesan_error)): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fa-solid fa-triangle-exclamation me-2"></i> <?= $pesan_error ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif ?>

                        <form method="POST" action="tambah.php">

                            <div class="row mb-4 align-items-center">
                                <label class="col-md-4 form-label-custom">
                                    <i class="fa-solid fa-users"></i> Nama Organisasi/Kelompok
                                </label>
                                <div class="col-md-8">
                                    <input type="text"
                                        class="form-control bg-light"
                                        value="<?= htmlspecialchars($nama_ormawa_aktif) ?>"
                                        readonly>
                                </div>
                            </div>

                            <div class="row mb-4 align-items-center">
                                <label class="col-md-4 form-label-custom">
                                    <i class="fa-solid fa-door-open"></i> Pilih Ruangan
                                </label>
                                <div class="col-md-8">
                                    <select name="id_ruangan" class="form-select" required>
                                        <option value="">-- Pilih Ruangan / Fasilitas --</option>
                                        <?php foreach ($list_ruangan as $r): ?>
                                            <option value="<?= $r['id_ruangan'] ?>" <?= ($selected_ruangan == $r['id_ruangan']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($r['nama_ruangan']) ?>
                                                (<?= htmlspecialchars($r['nama_gedung']) ?> - Kapasitas:
                                                <?= $r['kapasitas'] ?>)
                                            </option>
                                        <?php endforeach ?>
                                    </select>
                                </div>
                            </div>

                            <div class="row mb-4 align-items-center">
                                <label class="col-md-4 form-label-custom">
                                    <i class="fa-solid fa-calendar-alt"></i> Tanggal Peminjaman
                                </label>
                                <div class="col-md-8">
                                    <input type="date" name="tgl_pinjam" class="form-control" required>
                                </div>
                            </div>

                            <div class="row mb-4 align-items-center">
                                <label class="col-md-4 form-label-custom">
                                    <i class="fa-solid fa-clock"></i> Jam Mulai
                                </label>
                                <div class="col-md-8">
                                    <input type="time" name="jam_mulai" class="form-control" required>
                                </div>
                            </div>

                            <div class="row mb-4 align-items-center">
                                <label class="col-md-4 form-label-custom">
                                    <i class="fa-solid fa-clock-rotate-left"></i> Jam Selesai
                                </label>
                                <div class="col-md-8">
                                    <input type="time" name="jam_selesai" class="form-control" required>
                                </div>
                            </div>

                            <div class="row mb-4 align-items-start">
                                <label class="col-md-4 form-label-custom pt-2">
                                    <i class="fa-solid fa-file-lines"></i> Tujuan Penggunaan
                                </label>
                                <div class="col-md-8">
                                    <textarea name="tujuan" rows="4" class="form-control"
                                        placeholder="Jelaskan tujuan penggunaan fasilitas..." required></textarea>
                                </div>
                            </div>

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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>