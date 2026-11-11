<!-- Buat form HTML sederhana dengan elemen <select> dropdown untuk memilih ruangan dan pengguna, lalu simpan menggunakan query INSERT / UPDATE. -->

<?php
include 'koneksi.php';

// Ambil data Pengguna untuk dropdown
$pengguna = $pdo->query("SELECT * FROM Pengguna")->fetchAll(PDO::FETCH_ASSOC);
// Ambil data Ruangan untuk dropdown
$ruangan = $pdo->query("SELECT r.*, g.nama_gedung FROM Ruangan r JOIN Gedung g ON r.id_gedung = g.id_gedung")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_peminjaman = $_POST['id_peminjaman'];
    $tgl_pengajuan = date('Y-m-d'); // Otomatis tanggal hari ini
    $tgl_pinjam = $_POST['tgl_pinjam'];
    $jam_mulai = $_POST['jam_mulai'];
    $jam_selesai = $_POST['jam_selesai'];
    $tujuan = $_POST['tujuan'];
    $id_pengguna = $_POST['id_pengguna'];
    $id_ruangan = $_POST['id_ruangan'];

    $sql = "INSERT INTO Peminjaman (id_peminjaman, tgl_pengajuan, tgl_pinjam, jam_mulai, jam_selesai, tujuan, id_pengguna, id_ruangan) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_peminjaman, $tgl_pengajuan, $tgl_pinjam, $jam_mulai, $jam_selesai, $tujuan, $id_pengguna, $id_ruangan]);

    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Peminjaman</title>
</head>
<body>
    <h2>Form Tambah Peminjaman Ruangan</h2>
    <form method="POST">
        <label>ID Peminjaman (Contoh: P001):</label><br>
        <input type="text" name="id_peminjaman" required><br><br>

        <label>Pilih Ormawa (Pengguna):</label><br>
        <select name="id_pengguna" required>
            <option value="">-- Pilih Ormawa --</option>
            <?php foreach ($pengguna as $p): ?>
                <option value="<?= $p['id_pengguna']; ?>"><?= $p['nama_ormawa']; ?></option>
            <?php endforeach; ?>
        </select><br><br>

        <label>Pilih Ruangan:</label><br>
        <select name="id_ruangan" required>
            <option value="">-- Pilih Ruangan --</option>
            <?php foreach ($ruangan as $r): ?>
                <option value="<?= $r['id_ruangan']; ?>"><?= $r['nama_gedung'] . ' - ' . $r['nama_ruangan']; ?></option>
            <?php endforeach; ?>
        </select><br><br>

        <label>Tanggal Pinjam:</label><br>
        <input type="date" name="tgl_pinjam" required><br><br>

        <label>Jam Mulai:</label><br>
        <input type="time" name="jam_mulai" required><br><br>

        <label>Jam Selesai:</label><br>
        <input type="time" name="jam_selesai" required><br><br>

        <label>Tujuan Peminjaman:</label><br>
        <textarea name="tujuan" required></textarea><br><br>

        <button type="submit">Simpan</button>
        <a href="index.php">Batal</a>
    </form>
</body>
</html>
