<?php
// Panggil file koneksi database
require_once 'koneksi.php';

// Ambil keyword pencarian dan filter lokasi jika ada
$keyword = isset($_GET['cari']) ? trim($_GET['cari']) : '';
$lokasi = isset($_GET['lokasi']) ? $_GET['lokasi'] : '';

// Query dasar dengan JOIN ke tabel Gedung
$query = "SELECT r.*, g.nama_gedung, g.lokasi as nama_lokasi 
          FROM Ruangan r 
          JOIN Gedung g ON r.id_gedung = g.id_gedung 
          WHERE 1=1";

$params = [];

// Jika ada pencarian nama ruangan atau fasilitas
if ($keyword !== '') {
    $query .= " AND (r.nama_ruangan LIKE ? OR r.jenis_fasilitas LIKE ?)";
    $params[] = "%$keyword%";
    $params[] = "%$keyword%";
}

// Jika ada filter lokasi gedung
if ($lokasi !== '') {
    $query .= " AND r.id_gedung = ?";
    $params[] = $lokasi;
}

$query .= " ORDER BY r.id_ruangan ASC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$list_ruangan = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Ambil data gedung untuk dropdown filter
$stmt_gedung = $pdo->query("SELECT * FROM Gedung");
$list_gedung = $stmt_gedung->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Ruangan & Fasilitas — Horizon University</title>
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

        /* Header Banner Ruangan */
        .page-header {
            background: linear-gradient(135deg, var(--horizon-maroon), var(--horizon-dark-maroon));
            color: white;
            padding: 40px 0;
            margin-bottom: 30px;
            box-shadow: inset 0 -5px 15px rgba(0,0,0,0.2);
        }

        /* Card Ruangan Styling (Sesuai Gambar 3) */
        .room-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            overflow: hidden;
            background: #fff;
            height: 100%;
        }

        .room-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }

        .room-header-bg {
            background-color: var(--horizon-maroon);
            color: white;
            padding: 15px 20px;
            font-weight: 600;
            font-size: 1.15rem;
        }

        .room-body {
            padding: 20px;
        }

        .room-info {
            font-size: 0.9rem;
            color: #555;
            margin-bottom: 10px;
        }

        .room-info i {
            width: 25px;
            color: var(--horizon-maroon);
        }

        .badge-available {
            background-color: #d1e7dd;
            color: #0f5132;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            display: inline-block;
        }

        .btn-detail {
            background-color: var(--horizon-maroon);
            color: white;
            border-radius: 20px;
            padding: 6px 18px;
            font-size: 0.85srem;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .btn-detail:hover {
            background-color: var(--horizon-dark-maroon);
            color: white;
        }

        /* Search & Filter Bar */
        .filter-container {
            background: #fff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            margin-bottom: 35px;
        }
    </style>
</head>
<body>

    <!-- NAVBAR UTAMA -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-horizon sticky-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="index.php">
                <img src="asset/logohorizon-removebg-preview.png" alt="Logo" width="50" height="50" class="me-2 d-inline-block align-text-top object-fit-contain" onerror="this.style.display='none'">
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
                    <li class="nav-item"><a class="nav-link active" href="ruangan.php">Ruangan</a></li>
                    <li class="nav-item"><a class="nav-link" href="tambah.php">Peminjaman</a></li>
                    <li class="nav-item ms-lg-2"><a class="nav-link text-white" href="login.php"><i class="fa-solid fa-right-to-bracket me-1"></i> Login</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- HEADER BANNER -->
    <div class="page-header text-center">
        <div class="container">
            <h2><i class="fa-solid fa-building-columns me-2"></i> Daftar Ruangan & Fasilitas</h2>
            <p class="mb-0 text-white-50">Horizon University Karawang</p>
        </div>
    </div>

    <!-- KONTEN UTAMA -->
    <div class="container mb-5">
        
        <!-- SEARCH & FILTER FORM (Sesuai Gambar 3) -->
        <form method="GET" action="ruangan.php" class="filter-container">
            <div class="row g-3 align-items-center">
                <div class="col-lg-8">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                        <input type="text" name="cari" class="form-control border-start-0 ps-0" placeholder="Cari nama ruangan atau fasilitas..." value="<?= htmlspecialchars($keyword) ?>">
                    </div>
                </div>
                <div class="col-lg-3">
                    <select name="lokasi" class="form-select">
                        <option value="">Semua Lokasi / Gedung</option>
                        <?php foreach ($list_gedung as $g): ?>
                            <option value="<?= $g['id_gedung'] ?>" <?= $lokasi == $g['id_gedung'] ? 'selected' : '' ?>>
                                <?= $g['nama_gedung'] ?> (<?= $g['lokasi'] ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-lg-1">
                    <button type="submit" class="btn btn-danger w-100" style="background-color: var(--horizon-maroon);">Cari</button>
                </div>
            </div>
        </form>

        <!-- GRID KARTU RUANGAN -->
        <div class="row g-4">
            <?php if (count($list_ruangan) > 0): ?>
                <?php foreach ($list_ruangan as $row): ?>
                    <div class="col-md-4">
                        <div class="room-card">
                            <div class="room-header-bg d-flex justify-content-between align-items-center">
                                <span><?= htmlspecialchars($row['nama_ruangan']) ?></span>
                                <small class="badge bg-light text-dark"><?= htmlspecialchars($row['id_ruangan']) ?></small>
                            </div>
                            <div class="room-body">
                                <p class="room-info mb-2">
                                    <i class="fa-solid fa-location-dot"></i> <strong><?= htmlspecialchars($row['nama_gedung']) ?></strong> 
                                </p>
                                <p class="room-info mb-2">
                                    <i class="fa-solid fa-users"></i> Kapasitas: <strong><?= htmlspecialchars($row['kapasitas']) ?> orang</strong>
                                </p>
                                <p class="room-info mb-3">
                                    <i class="fa-solid fa-couch"></i> Fasilitas: <?= htmlspecialchars($row['jenis_fasilitas']) ?>
                                </p>
                                <div class="d-flex justify-content-between align-items-center mt-4 pt-2 border-top">
                                    <span class="badge-available">
                                        <i class="fa-solid fa-circle text-success me-1" style="font-size: 8px;"></i> Tersedia
                                    </span>
                                    <a href="tambah.php?id_ruangan=<?= $row['id_ruangan'] ?>" class="btn-detail">
                                        Pinjam <i class="fa-solid fa-arrow-right ms-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <div class="alert alert-warning py-4">
                        <i class="fa-solid fa-triangle-exclamation fa-2x mb-3 text-warning"></i>
                        <h5>Maaf, ruangan yang Anda cari tidak ditemukan.</h5>
                        <p class="mb-0 text-muted">Coba gunakan kata kunci lain atau reset filter pencarian.</p>
                        <a href="ruangan.php" class="btn btn-sm btn-dark mt-3">Reset Pencarian</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>

    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>