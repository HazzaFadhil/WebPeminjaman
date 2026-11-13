<!-- Buat form HTML sederhana dengan elemen <select> dropdown untuk memilih ruangan dan pengguna, lalu simpan menggunakan query INSERT / UPDATE. -->

<?php
include '../koneksi.php';

$id = $_GET['id'] ?? '';
if (!$id) {
    header("Location: ../admin/index.php");
    exit;
}

// Ambil data peminjaman berdasarkan ID
$stmt = $pdo->prepare("SELECT * FROM Peminjaman WHERE id_peminjaman = ?");
$stmt->execute([$id]);
$peminjaman = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$peminjaman) {
    header("Location: ../admin/index.php");
    exit;
}

// Ambil data dropdown
$pengguna = $pdo->query("SELECT * FROM Pengguna")->fetchAll(PDO::FETCH_ASSOC);
$ruangan = $pdo->query("SELECT r.*, g.nama_gedung FROM Ruangan r JOIN Gedung g ON r.id_gedung = g.id_gedung")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tgl_pinjam = $_POST['tgl_pinjam'];
    $jam_mulai = $_POST['jam_mulai'];
    $jam_selesai = $_POST['jam_selesai'];
    $tujuan = $_POST['tujuan'];
    $status = $_POST['status'];
    $id_pengguna = $_POST['id_pengguna'];
    $id_ruangan = $_POST['id_ruangan'];

    $sql = "UPDATE Peminjaman SET tgl_pinjam = ?, jam_mulai = ?, jam_selesai = ?, tujuan = ?, status = ?, id_pengguna = ?, id_ruangan = ? WHERE id_peminjaman = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$tgl_pinjam, $jam_mulai, $jam_selesai, $tujuan, $status, $id_pengguna, $id_ruangan, $id]);

    header("Location: ../admin/index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Peminjaman</title>
</head>
<body>
    <h2>Form Edit Peminjaman Ruangan</h2>
    <form method="POST">
        <label>ID Peminjaman (Tidak dapat diubah):</label><br>
        <input type="text" value="<?= $peminjaman['id_peminjaman']; ?>" disabled><br><br>

        <label>Pilih Ormawa (Pengguna):</label><br>
        <select name="id_pengguna" required>
            <?php foreach ($pengguna as $p): ?>
                <option value="<?= $p['id_pengguna']; ?>" <?= ($p['id_pengguna'] == $peminjaman['id_pengguna']) ? 'selected' : ''; ?>>
                    <?= $p['nama_ormawa']; ?>
                </option>
            <?php endforeach; ?>
        </select><br><br>

        <label>Pilih Ruangan:</label><br>
        <select name="id_ruangan" required>
            <?php foreach ($ruangan as $r): ?>
                <option value="<?= $r['id_ruangan']; ?>" <?= ($r['id_ruangan'] == $peminjaman['id_ruangan']) ? 'selected' : ''; ?>>
                    <?= $r['nama_gedung'] . ' - ' . $r['nama_ruangan']; ?>
                </option>
            <?php endforeach; ?>
        </select><br><br>

        <label>Tanggal Pinjam:</label><br>
        <input type="date" name="tgl_pinjam" value="<?= $peminjaman['tgl_pinjam']; ?>" required><br><br>

        <label>Jam Mulai:</label><br>
        <input type="time" name="jam_mulai" value="<?= $peminjaman['jam_mulai']; ?>" required><br><br>

        <label>Jam Selesai:</label><br>
        <input type="time" name="jam_selesai" value="<?= $peminjaman['jam_selesai']; ?>" required><br><br>

        <label>Tujuan Peminjaman:</label><br>
        <textarea name="tujuan" required><?= $peminjaman['tujuan']; ?></textarea><br><br>

        <label>Status:</label><br>
        <select name="status">
            <option value="Pending" <?= ($peminjaman['status'] == 'Pending') ? 'selected' : ''; ?>>Pending</option>
            <option value="Disetujui" <?= ($peminjaman['status'] == 'Disetujui') ? 'selected' : ''; ?>>Disetujui</option>
            <option value="Ditolak" <?= ($peminjaman['status'] == 'Ditolak') ? 'selected' : ''; ?>>Ditolak</option>
        </select><br><br>

        <button type="submit">Update</button>
        <a href="../admin/index.php">Batal</a>
    </form>
</body>
</html>