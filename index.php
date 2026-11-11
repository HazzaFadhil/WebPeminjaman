<!-- Tampilkan data peminjaman menggunakan perintah SELECT dengan teknik JOIN -->

<?php
include 'koneksi.php';

// Query JOIN dari 4 tabel
$query = "SELECT p.*, pg.nama_ormawa, r.nama_ruangan, g.nama_gedung 
          FROM Peminjaman p
          JOIN Pengguna pg ON p.id_pengguna = pg.id_pengguna
          JOIN Ruangan r ON p.id_ruangan = r.id_ruangan
          JOIN Gedung g ON r.id_gedung = g.id_gedung
          ORDER BY p.tgl_pengajuan DESC";
$stmt = $pdo->query($query);
$data_peminjaman = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Peminjaman Ruangan Kampus</title>
</head>
<body>
    <h2>Data Peminjaman Ruangan</h2>
    <a href="tambah.php">+ Tambah Peminjaman Baru</a>
    <br><br>

    <table border="1" cellpadding="10" cellspacing="0">
        <thead>
            <tr>
                <th>No</th>
                <th>ID Peminjaman</th>
                <th>Ormawa</th>
                <th>Gedung & Ruangan</th>
                <th>Tgl Pinjam</th>
                <th>Jam</th>
                <th>Tujuan</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($data_peminjaman) > 0): ?>
                <?php $no = 1; foreach ($data_peminjaman as $row): ?>
                <tr>
                    <td><?= $no++; ?></td>
                    <td><?= htmlspecialchars($row['id_peminjaman']); ?></td>
                    <td><?= htmlspecialchars($row['nama_ormawa']); ?></td>
                    <td><?= htmlspecialchars($row['nama_gedung'] . ' - ' . $row['nama_ruangan']); ?></td>
                    <td><?= htmlspecialchars($row['tgl_pinjam']); ?></td>
                    <td><?= htmlspecialchars($row['jam_mulai'] . ' - ' . $row['jam_selesai']); ?></td>
                    <td><?= htmlspecialchars($row['tujuan']); ?></td>
                    <td><?= htmlspecialchars($row['status']); ?></td>
                    <td>
                        <a href="edit.php?id=<?= $row['id_peminjaman']; ?>">Edit</a> | 
                        <a href="hapus.php?id=<?= $row['id_peminjaman']; ?>" onclick="return confirm('Yakin ingin menghapus data ini?')">Hapus</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="9" align="center">Belum ada data peminjaman.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>