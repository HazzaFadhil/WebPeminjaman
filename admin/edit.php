<?php
include '../koneksi.php';

$pesan_error = '';

$id = $_GET['id'] ?? '';

if (empty($id)) {
    header("Location: ../admin/index.php");
    exit;
}

// Ambil data peminjaman berdasarkan ID
$stmt = $pdo->prepare(
    "SELECT * FROM Peminjaman WHERE id_peminjaman = ?"
);
$stmt->execute([$id]);
$peminjaman = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$peminjaman) {
    header("Location: ../admin/index.php");
    exit;
}

// Ambil data pengguna untuk dropdown
$pengguna = $pdo->query(
    "SELECT * FROM Pengguna ORDER BY nama_ormawa ASC"
)->fetchAll(PDO::FETCH_ASSOC);

// Ambil data ruangan untuk dropdown
$ruangan = $pdo->query(
    "SELECT r.*, g.nama_gedung
     FROM Ruangan r
     JOIN Gedung g ON r.id_gedung = g.id_gedung
     ORDER BY r.nama_ruangan ASC"
)->fetchAll(PDO::FETCH_ASSOC);

// Proses update data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $tgl_pinjam = $_POST['tgl_pinjam'] ?? '';
    $jam_mulai = $_POST['jam_mulai'] ?? '';
    $jam_selesai = $_POST['jam_selesai'] ?? '';
    $tujuan = trim($_POST['tujuan'] ?? '');
    $status = $_POST['status'] ?? '';
    $id_pengguna = $_POST['id_pengguna'] ?? '';
    $id_ruangan = $_POST['id_ruangan'] ?? '';

    // Validasi input wajib
    if (
        empty($tgl_pinjam) ||
        empty($jam_mulai) ||
        empty($jam_selesai) ||
        empty($tujuan) ||
        empty($id_pengguna) ||
        empty($id_ruangan) ||
        !in_array($status, ['Pending', 'Disetujui', 'Ditolak'])
    ) {
        $pesan_error = 'Semua data wajib diisi dengan benar.';
    }

    // Validasi jam
    elseif ($jam_mulai >= $jam_selesai) {
        $pesan_error = 'Jam selesai harus lebih besar dari jam mulai.';
    }

    // Cek apakah pengguna dan ruangan tersedia
    else {
        $cekPengguna = $pdo->prepare(
            "SELECT COUNT(*) FROM Pengguna WHERE id_pengguna = ?"
        );
        $cekPengguna->execute([$id_pengguna]);

        $cekRuangan = $pdo->prepare(
            "SELECT COUNT(*) FROM Ruangan WHERE id_ruangan = ?"
        );
        $cekRuangan->execute([$id_ruangan]);

        if (
            $cekPengguna->fetchColumn() == 0 ||
            $cekRuangan->fetchColumn() == 0
        ) {
            $pesan_error = 'Pengguna atau ruangan tidak ditemukan.';
        }
    }

    // Cek bentrok jadwal
    if (
        $pesan_error === '' &&
        in_array($status, ['Pending', 'Disetujui'])
    ) {
        $sqlCek = "
            SELECT COUNT(*)
            FROM Peminjaman
            WHERE id_ruangan = ?
            AND tgl_pinjam = ?
            AND status IN ('Pending', 'Disetujui')
            AND jam_mulai < ?
            AND jam_selesai > ?
            AND id_peminjaman != ?
        ";

        $stmtCek = $pdo->prepare($sqlCek);
        $stmtCek->execute([
            $id_ruangan,
            $tgl_pinjam,
            $jam_selesai,
            $jam_mulai,
            $id
        ]);

        if ($stmtCek->fetchColumn() > 0) {
            $pesan_error = 'Jadwal ruangan bertabrakan dengan peminjaman lain!';
        }
    }

    if ($pesan_error === '') {

        $sql = "
            UPDATE Peminjaman
            SET
                tgl_pinjam = ?,
                jam_mulai = ?,
                jam_selesai = ?,
                tujuan = ?,
                status = ?,
                id_pengguna = ?,
                id_ruangan = ?
            WHERE id_peminjaman = ?
        ";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            $tgl_pinjam,
            $jam_mulai,
            $jam_selesai,
            $tujuan,
            $status,
            $id_pengguna,
            $id_ruangan,
            $id
        ]);

        header("Location: ../admin/index.php");
        exit;
    }

    // Pertahankan input jika terjadi kesalahan
    $peminjaman['tgl_pinjam'] = $tgl_pinjam;
    $peminjaman['jam_mulai'] = $jam_mulai;
    $peminjaman['jam_selesai'] = $jam_selesai;
    $peminjaman['tujuan'] = $tujuan;
    $peminjaman['status'] = $status;
    $peminjaman['id_pengguna'] = $id_pengguna;
    $peminjaman['id_ruangan'] = $id_ruangan;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Peminjaman</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            margin: 0;
            padding: 30px;
        }

        .container {
            max-width: 550px;
            margin: auto;
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        h2 {
            color: #800000;
            margin-top: 0;
            text-align: center;
        }

        label {
            display: block;
            margin-top: 15px;
            margin-bottom: 6px;
            font-weight: bold;
        }

        input, select, textarea {
            width: 100%;
            padding: 10px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
        }

        textarea {
            min-height: 90px;
            resize: vertical;
        }

        input:disabled {
            background: #eee;
        }

        .pesan-error {
            background: #ffe5e5;
            color: #b00020;
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 15px;
        }

        .tombol {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }

        button, .batal {
            flex: 1;
            padding: 11px;
            border: none;
            border-radius: 5px;
            text-align: center;
            text-decoration: none;
            font-size: 14px;
            cursor: pointer;
        }

        button {
            background: #800000;
            color: white;
        }

        button:hover {
            background: #600000;
        }

        .batal {
            background: #ddd;
            color: #333;
        }
    </style>
</head>

<body>

<div class="container">

    <h2>Form Edit Peminjaman Ruangan</h2>

    <?php if (!empty($pesan_error)): ?>
        <div class="pesan-error">
            <?= htmlspecialchars($pesan_error); ?>
        </div>
    <?php endif; ?>

    <form method="POST">

        <label>ID Peminjaman</label>
        <input
            type="text"
            value="<?= htmlspecialchars($peminjaman['id_peminjaman']); ?>"
            disabled
        >

        <label>Pilih Ormawa (Pengguna)</label>
        <select name="id_pengguna" required>
            <?php foreach ($pengguna as $p): ?>
                <option
                    value="<?= htmlspecialchars($p['id_pengguna']); ?>"
                    <?= ($p['id_pengguna'] == $peminjaman['id_pengguna']) ? 'selected' : ''; ?>
                >
                    <?= htmlspecialchars($p['nama_ormawa']); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label>Pilih Ruangan</label>
        <select name="id_ruangan" required>
            <?php foreach ($ruangan as $r): ?>
                <option
                    value="<?= htmlspecialchars($r['id_ruangan']); ?>"
                    <?= ($r['id_ruangan'] == $peminjaman['id_ruangan']) ? 'selected' : ''; ?>
                >
                    <?= htmlspecialchars($r['nama_gedung'] . ' - ' . $r['nama_ruangan']); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label>Tanggal Pinjam</label>
        <input
            type="date"
            name="tgl_pinjam"
            value="<?= htmlspecialchars($peminjaman['tgl_pinjam']); ?>"
            required
        >

        <label>Jam Mulai</label>
        <input
            type="time"
            name="jam_mulai"
            value="<?= htmlspecialchars(substr($peminjaman['jam_mulai'], 0, 5)); ?>"
            required
        >

        <label>Jam Selesai</label>
        <input
            type="time"
            name="jam_selesai"
            value="<?= htmlspecialchars(substr($peminjaman['jam_selesai'], 0, 5)); ?>"
            required
        >

        <label>Tujuan Peminjaman</label>
        <textarea name="tujuan" required><?= htmlspecialchars($peminjaman['tujuan']); ?></textarea>

        <label>Status Peminjaman</label>
        <select name="status" required>
            <option value="Pending"
                <?= ($peminjaman['status'] == 'Pending') ? 'selected' : ''; ?>>
                Pending
            </option>

            <option value="Disetujui"
                <?= ($peminjaman['status'] == 'Disetujui') ? 'selected' : ''; ?>>
                Disetujui
            </option>

            <option value="Ditolak"
                <?= ($peminjaman['status'] == 'Ditolak') ? 'selected' : ''; ?>>
                Ditolak
            </option>
        </select>

        <div class="tombol">
            <button type="submit">Update Peminjaman</button>
            <a href="../admin/index.php" class="batal">Batal</a>
        </div>

    </form>

</div>

</body>
</html> -->
