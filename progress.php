<?php
session_start();
require_once "koneksi.php";

// Pastikan user sudah login dan gunakan nama session yang sesuai dengan login.php
if (empty($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$idPengguna = $_SESSION['user_id'];

try {
    // Hanya mengambil data peminjaman milik pengguna yang sedang login
$is_login = isset($_SESSION['user_id']);
$foto_profil = null;

if ($is_login) {
    $stmtUser = $pdo->prepare("SELECT foto_profil FROM pengguna WHERE id_pengguna = ?");
    $stmtUser->execute([$_SESSION['user_id']]);
    $user = $stmtUser->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $foto_profil = $user['foto_profil'];
    }
}

    $sql = "SELECT p.*, r.nama_ruangan
            FROM Peminjaman p
            LEFT JOIN Ruangan r ON p.id_ruangan = r.id_ruangan
            WHERE p.id_pengguna = :id_pengguna
            ORDER BY p.tgl_pinjam DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id_pengguna' => $idPengguna]);

    $stmt->execute();
    $dataPeminjaman = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error Query: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Progress Peminjaman | Horizon University</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        * {
            font-family: 'Poppins', sans-serif;
        }

        :root {
            --maroon: #800000;
            --horizon-dark-maroon: #5a0000;
            --light-red: #fff0f3;
        }

        .navbar-horizon {
            background-color: var(--maroon);
            padding: 12px 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.15);
        }

        .navbar-brand {
            font-weight: 700;
            letter-spacing: 0.5px;
            font-size: 1.2rem;
        }

        .navbar-brand small {
            display: block;
            font-size: 0.65rem;
            font-weight: 400;
            letter-spacing: 2px;
        }

        .nav-link {
            color: rgba(255,255,255,0.9) !important;
            font-weight: 500;
            margin: 0 5px;
            padding: 8px 18px !important;
            border-radius: 20px;
            transition: all 0.3s ease;
        }

        .nav-link:hover {
            color: #fff !important;
            background-color: rgba(255,255,255,0.15);
        }

        .nav-link.active {
            background-color: #fff !important;
            color: var(--maroon) !important;
            font-weight: 600;
        }

        .profile-toggle {
            border: none !important;
            background: transparent !important;
            padding: 0 !important;
            margin-left: 10px;
            display: flex;
            align-items: center;
            color: white !important;
            text-decoration: none !important;
            box-shadow: none !important;
            outline: none !important;
            line-height: 1;
        }

        .profile-toggle::after {
            display: inline-block !important;
            margin-left: 7px;
            vertical-align: middle;
            content: "";
            border-top: 5px solid white;
            border-right: 4px solid transparent;
            border-bottom: 0;
            border-left: 4px solid transparent;
        }

        .profile-toggle:hover,
        .profile-toggle:focus,
        .profile-toggle:active,
        .profile-toggle.show {
            border: none !important;
            background: transparent !important;
            color: white !important;
            box-shadow: none !important;
            outline: none !important;
            text-decoration: none !important;
        }

        .profile-navbar {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid white;
            display: block;
        }

        .profile-icon {
            width: 36px;
            height: 36px;
            font-size: 36px;
            line-height: 36px;
            color: white;
            display: block;
        }

        .profile-dropdown {
            margin-top: 8px !important;
            min-width: 160px;
            padding: 8px;
            border: none !important;
            border-radius: 12px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }

        .profile-dropdown .dropdown-item {
            padding: 10px 12px;
            border-radius: 8px;
            font-weight: 500;
        }

        .profile-dropdown .dropdown-item:hover {
            background-color: #f8f9fa;
            color: var(--maroon);
        }

        .badge-pending {
            background-color: var(--light-red);
            color: var(--maroon);
            border-radius: 20px;
            padding: 8px 16px;
            font-weight: 600;
        }

        .badge-approved {
            background-color: #d1e7dd;
            color: #0f5132;
            border-radius: 20px;
            padding: 8px 16px;
            font-weight: 600;
        }

        .badge-rejected {
            background-color: #f8d7da;
            color: #842029;
            border-radius: 20px;
            padding: 8px 16px;
            font-weight: 600;
        }

        .badge-finished {
            background-color: #e2e3e5;
            color: #41464b;
            border-radius: 20px;
            padding: 8px 16px;
            font-weight: 600;
        }

        .card-pinjam {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            margin-bottom: 20px;
        }

        .icon-kalender {
            background-color: var(--maroon);
            color: white;
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .bg-gradient {
            background: linear-gradient(135deg, #fff5f8 0%, #ffffff 100%);
        }
    </style>
</head>

<body class="bg-gradient">

<nav class="navbar navbar-expand-lg navbar-dark navbar-horizon sticky-top">
    <div class="container">

        <a class="navbar-brand d-flex align-items-center" href="index.php">
            <img src="asset/logohorizon-removebg-preview.png"
                 alt="Logo"
                 width="50"
                 height="50"
                 class="me-2 d-inline-block align-text-top object-fit-contain"
                 onerror="this.style.display='none'">

            <div>
                HORIZON UNIVERSITY
                <small>INDONESIA</small>
            </div>
        </a>

        <button class="navbar-toggler"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center">

                <li class="nav-item">
                    <a class="nav-link" href="index.php">Beranda</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="ruangan.php">Ruangan</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="tambah.php">Peminjaman</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link active" href="progress.php">Progress</a>
                </li>

                <?php if ($is_login): ?>

                    <li class="nav-item dropdown ms-lg-2">

                        <button type="button"
                                class="profile-toggle"
                                data-bs-toggle="dropdown"
                                data-bs-display="static"
                                aria-expanded="false">

                            <?php if (!empty($foto_profil)): ?>

                                <img src="<?= htmlspecialchars($foto_profil) ?>"
                                     alt="Foto Profil"
                                     class="profile-navbar">

                            <?php else: ?>

                                <i class="fa-solid fa-circle-user profile-icon"></i>

                            <?php endif; ?>

                        </button>

                        <ul class="dropdown-menu dropdown-menu-end profile-dropdown">
                            <li>
                                <a class="dropdown-item text-danger" href="logout.php">
                                    <i class="fa-solid fa-right-from-bracket me-2"></i>
                                    Logout
                                </a>
                            </li>
                        </ul>

                    </li>

                <?php else: ?>

                    <li class="nav-item ms-lg-2">
                        <a class="nav-link text-white" href="login.php">
                            <i class="fa-solid fa-right-to-bracket me-1"></i>
                            Login
                        </a>
                    </li>

                <?php endif; ?>

            </ul>
        </div>
    </div>
</nav>

<div class="container py-5">

    <div class="d-flex align-items-center gap-3 mb-4">

        <div class="icon-kalender">
            <i class="fa-solid fa-clock-rotate-left fa-2x"></i>
        </div>

        <div>
            <h2 class="fw-bold mb-1">Progress Peminjaman</h2>
            <p class="text-muted mb-0">
                Pantau status pengajuan peminjaman fasilitas Anda di sini.
            </p>
        </div>

    </div>

    <?php if (count($dataPeminjaman) == 0): ?>

        <div class="alert alert-info">
            Belum ada pengajuan peminjaman.
        </div>

    <?php else: ?>

        <?php foreach ($dataPeminjaman as $row): ?>

            <div class="card card-pinjam p-4">

                <div class="row align-items-center">

                    <div class="col-md-8">

                        <div class="d-flex align-items-center gap-3">

                            <div class="icon-kalender">
                                <i class="fa-solid fa-calendar-day fa-2x"></i>
                            </div>

                            <div>

                                <h5 class="fw-bold mb-1">
                                    <?= htmlspecialchars($row['nama_ruangan'] ?? 'Ruangan tidak ditemukan') ?>
                                </h5>

                                <h6 class="fw-bold mb-1">
                                    ID Ruangan:
                                    <?= htmlspecialchars($row['id_ruangan']) ?>
                                </h6>

                                <p class="text-muted mb-0">
                                    <i class="fa-solid fa-clock me-1"></i>
                                    Tanggal Pinjam:
                                    <?= htmlspecialchars($row['tgl_pinjam']) ?>
                                    •
                                    <?= htmlspecialchars($row['jam_mulai']) ?> -
                                    <?= htmlspecialchars($row['jam_selesai']) ?>
                                </p>

                                <p class="text-muted mb-0">
                                    <i class="fa-solid fa-file-pen me-1"></i>
                                    Tujuan:
                                    <?= htmlspecialchars($row['tujuan']) ?>
                                </p>

                            </div>

                        </div>

                    </div>

                    <div class="col-md-4 text-md-end mt-3 mt-md-0">

                        <?php if ($row['status'] == 'Pending'): ?>

                            <span class="badge-pending">
                                <i class="fa-solid fa-clock me-1"></i>
                                Menunggu Persetujuan Admin
                            </span>

                            <p class="text-muted small mt-2 mb-0">
                                Pengajuan Anda sedang diproses oleh admin.
                            </p>

                        <?php elseif ($row['status'] == 'Disetujui'): ?>

                            <span class="badge-approved">
                                <i class="fa-solid fa-circle-check me-1"></i>
                                Disetujui
                            </span>

                            <p class="text-muted small mt-2 mb-0">
                                Pengajuan peminjaman Anda telah disetujui admin.
                            </p>

                        <?php elseif ($row['status'] == 'Ditolak'): ?>

                            <span class="badge-rejected">
                                <i class="fa-solid fa-circle-xmark me-1"></i>
                                Ditolak
                            </span>

                            <p class="text-muted small mt-2 mb-0">
                                Maaf, pengajuan peminjaman Anda ditolak oleh admin.
                            </p>

                        <?php elseif ($row['status'] == 'Selesai'): ?>

                            <span class="badge-finished">
                                <i class="fa-solid fa-flag-checkered me-1"></i>
                                Selesai
                            </span>

                            <p class="text-muted small mt-2 mb-0">
                                Peminjaman telah selesai.
                            </p>

                        <?php else: ?>

                            <span class="badge-pending">
                                <?= htmlspecialchars($row['status']) ?>
                            </span>

                        <?php endif; ?>

                    </div>

                </div>

            </div>

        <?php endforeach; ?>

    <?php endif; ?>

    <div class="alert alert-light mt-4 border-0 rounded-3"
         style="background-color:#fff0f3;">

        <i class="fa-solid fa-circle-info me-2"
           style="color:#800000;"></i>

        <span class="small fw-semibold">
            Status akan diperbarui setelah admin memproses
            pengajuan peminjaman.
        </span>

    </div>

</div>

<footer class="py-3 mt-5">
    <div class="container text-center small text-muted">
        © 2026 Horizon University.
        All rights reserved.
        Together for a Better Future
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>