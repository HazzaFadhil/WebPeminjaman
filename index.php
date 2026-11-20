<?php
session_start();
require_once 'koneksi.php';

$is_login = isset($_SESSION['user_id']);
$foto_profil = null;

if ($is_login) {
    $stmt = $pdo->prepare("SELECT foto_profil FROM pengguna WHERE id_pengguna = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $foto_profil = $user['foto_profil'];
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Sistem Peminjaman Fasilitas — Horizon University</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Google Font -->
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

        /* NAVBAR */
        .navbar-horizon {
            background-color: var(--horizon-maroon);
            padding: 12px 0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.15);
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
            color: rgba(255, 255, 255, 0.9) !important;
            font-weight: 500;
            margin: 0 5px;
            padding: 8px 18px !important;
            border-radius: 20px;
            transition: all 0.3s ease;
        }

        .nav-link:hover {
            color: #fff !important;
            background-color: rgba(255, 255, 255, 0.15);
        }

        .nav-link.active {
            background-color: #ffffff !important;
            color: var(--horizon-maroon) !important;
            font-weight: 600;
        }

        /* PROFILE */
        .profile-toggle {
            padding: 4px 8px !important;
            background: transparent !important;
        }

        .profile-toggle::after {
            margin-left: 6px;
            vertical-align: middle;
        }

        .profile-image {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #fff;
        }

        .profile-icon {
            color: #fff;
            font-size: 36px;
        }

        .profile-dropdown {
            min-width: 150px;
            border: none;
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
            margin-top: 8px !important;
        }

        .profile-dropdown .dropdown-item {
            padding: 10px 15px;
            font-size: 14px;
        }

        .profile-dropdown .dropdown-item:hover {
            background-color: #f8f9fa;
        }

        /* HERO */
        .hero-section {
            position: relative;
            min-height: 88vh;
            display: flex;
            align-items: center;

            background:
                linear-gradient(
                    90deg,
                    rgba(255, 255, 255, 0.95) 0%,
                    rgba(255, 255, 255, 0.85) 45%,
                    rgba(255, 255, 255, 0.2) 100%
                ),
                url('asset/Horizon.jpg') center/cover no-repeat;

            overflow: hidden;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: -10%;
            left: -5%;
            width: 500px;
            height: 700px;

            background: url('asset/Logo_Horizon_University.png')
                no-repeat center;

            background-size: contain;
            opacity: 0.05;
            pointer-events: none;
        }

        .hero-title {
            color: #1a1a1a;
            font-weight: 700;
            font-size: 2.8rem;
            line-height: 1.2;
        }

        .hero-title span {
            color: var(--horizon-maroon);
            display: block;
        }

        .hero-subtitle {
            color: #555;
            font-size: 1.1rem;
            margin-top: 15px;
            margin-bottom: 30px;
        }

        /* BUTTON */
        .btn-horizon {
            background-color: var(--horizon-maroon);
            color: #fff;
            padding: 12px 30px;
            border-radius: 30px;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(128, 0, 0, 0.3);
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .btn-horizon:hover {
            background-color: var(--horizon-dark-maroon);
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(128, 0, 0, 0.4);
        }

        .btn-outline-horizon {
            border: 2px solid var(--horizon-maroon);
            color: var(--horizon-maroon);
            padding: 10px 28px;
            border-radius: 30px;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            margin-left: 10px;
        }

        .btn-outline-horizon:hover {
            background-color: var(--horizon-maroon);
            color: #fff;
        }
    </style>
</head>

<body>

    <!-- ================= NAVBAR ================= -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-horizon sticky-top">

        <div class="container">

            <!-- LOGO -->
            <a class="navbar-brand d-flex align-items-center" href="index.php">

                <img
                    src="asset/logohorizon-removebg-preview.png"
                    alt="Logo Horizon University"
                    width="50"
                    height="50"
                    class="me-2 d-inline-block align-text-top object-fit-contain"
                >

                <div>
                    HORIZON UNIVERSITY
                    <small>INDONESIA</small>
                </div>

            </a>

            <!-- MOBILE TOGGLE -->
            <button
                class="navbar-toggler"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#navbarNav"
            >
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- NAVIGATION -->
            <div class="collapse navbar-collapse" id="navbarNav">

                <ul class="navbar-nav ms-auto align-items-center">

                    <!-- BERANDA -->
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php">
                            Beranda
                        </a>
                    </li>

                    <!-- RUANGAN -->
                    <li class="nav-item">
                        <a class="nav-link" href="ruangan.php">
                            Ruangan
                        </a>
                    </li>

                    <?php if ($is_login): ?>

                        <!-- PEMINJAMAN -->
                        <li class="nav-item">
                            <a class="nav-link" href="tambah.php">
                                Peminjaman
                            </a>
                        </li>

                        <!-- PROGRESS -->
                        <li class="nav-item">
                            <a class="nav-link" href="progress.php">
                                Progress
                            </a>
                        </li>

                        <!-- PROFILE -->
                        <li class="nav-item dropdown ms-lg-2">

                            <a
                                class="nav-link dropdown-toggle profile-toggle d-flex align-items-center"
                                href="#"
                                role="button"
                                data-bs-toggle="dropdown"
                                aria-expanded="false"
                            >

                                <?php if (!empty($foto_profil)): ?>

                                    <img
                                        src="<?= htmlspecialchars($foto_profil) ?>"
                                        alt="Foto Profil"
                                        class="profile-image"
                                    >

                                <?php else: ?>

                                    <i class="fa-solid fa-circle-user profile-icon"></i>

                                <?php endif; ?>

                            </a>

                            <!-- DROPDOWN PROFILE -->
                            <ul class="dropdown-menu dropdown-menu-end profile-dropdown">

                                <li>
                                    <a
                                        class="dropdown-item text-danger"
                                        href="logout.php"
                                    >
                                        <i class="fa-solid fa-right-from-bracket me-2"></i>
                                        Logout
                                    </a>
                                </li>

                            </ul>

                        </li>

                    <?php else: ?>

                        <!-- LOGIN -->
                        <li class="nav-item ms-lg-2">
                            <a
                                class="nav-link text-white"
                                href="login.php"
                            >
                                <i class="fa-solid fa-right-to-bracket me-1"></i>
                                Login
                            </a>
                        </li>

                    <?php endif; ?>

                </ul>

            </div>
        </div>
    </nav>


    <!-- ================= HERO ================= -->
    <section class="hero-section">

        <div class="container">

            <div class="row align-items-center">

                <div class="col-lg-7">

                    <h1 class="hero-title">

                        Selamat Datang di

                        <span>
                            Sistem Peminjaman Fasilitas
                        </span>

                        Horizon University

                    </h1>

                    <p class="hero-subtitle">
                        Pinjam fasilitas kampus dengan mudah, cepat, dan
                        terintegrasi untuk mendukung berbagai kegiatan
                        ormawa dan akademis Anda.
                    </p>

                    <div>

                        <!-- AJUKAN PEMINJAMAN -->
                        <a
                            href="<?= $is_login ? 'tambah.php' : 'login.php' ?>"
                            class="btn-horizon"
                        >
                            <i class="fa-solid fa-calendar-plus me-2"></i>
                            Ajukan Peminjaman
                        </a>

                        <!-- LIHAT RUANGAN -->
                        <a
                            href="ruangan.php"
                            class="btn-outline-horizon"
                        >
                            <i class="fa-solid fa-door-open me-2"></i>
                            Lihat Ruangan
                        </a>

                    </div>

                </div>

            </div>

        </div>

    </section>


    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>