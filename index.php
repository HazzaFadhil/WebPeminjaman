<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Peminjaman Fasilitas — Horizon University</title>
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

        /* Warna Utama Horizon University */
        :root {
            --horizon-maroon: #800000;
            --horizon-dark-maroon: #5a0000;
        }

        /* Navbar Styling */
        .navbar-horizon {
            background-color: var(--horizon-maroon);
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

        /* Tombol Menu Aktif (Pill Shape) */
        .nav-link.active {
            background-color: #ffffff !important;
            color: var(--horizon-maroon) !important;
            font-weight: 600;
        }

        /* Hero Section Styling (Gambar 2) */
        .hero-section {
            position: relative;
            min-height: 88vh;
            display: flex;
            align-items: center;
            background: linear-gradient(90deg, rgba(255,255,255,0.95) 0%, rgba(255,255,255,0.85) 45%, rgba(255,255,255,0.2) 100%), 
                        url('asset/Horizon.jpg') center/cover no-repeat;
            overflow: hidden;
        }

        /* Efek Watermark Logo Kampus Transparan di Latar Belakang */
        .hero-section::before {
            content: '';
            position: absolute;
            top: -10%;
            left: -5%;
            width: 500px;
            height: 700px;
            background: url('asset/Logo_Horizon_University.png') no-repeat center;
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

    <!-- NAVBAR UTAMA -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-horizon sticky-top">
        <div class="container">
            <!-- Logo & Nama Kampus -->
            <a class="navbar-brand d-flex align-items-center" href="index.php">
                <img src="asset/logohorizon-removebg-preview.png" alt="Logo Horizon University" width="50" height="50" class="me-2 d-inline-block align-text-top object-fit-contain">
                <div>
                    HORIZON UNIVERSITY
                    <small>KARAWANG</small>
                </div>
            </a>

            <!-- Toggle Button untuk Mobile -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Navigasi Menu (Sesuai Referensi Gambar UI) -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php">Beranda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="ruangan.php">Ruangan</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="tambah.php">Peminjaman</a>
                    </li>
                    <li class="nav-item ms-lg-2">
                        <a class="nav-link text-white" href="login.php">
                            <i class="fa-solid fa-right-to-bracket me-1"></i> Login
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- HERO SECTION (Sesuai Gambar 2) -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-7">
                    <h1 class="hero-title">
                        Selamat Datang di 
                        <span>Sistem Peminjaman Fasilitas</span>
                        Horizon University
                    </h1>
                    <p class="hero-subtitle">
                        Pinjam fasilitas kampus dengan mudah, cepat, dan terintegrasi untuk mendukung berbagai kegiatan ormawa dan akademis Anda.
                    </p>
                    <div>
                        <a href="tambah.php" class="btn-horizon">
                            <i class="fa-solid fa-calendar-plus me-2"></i>Ajukan Peminjaman
                        </a>
                        <a href="ruangan.php" class="btn-outline-horizon">
                            <i class="fa-solid fa-door-open me-2"></i>Lihat Ruangan
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>