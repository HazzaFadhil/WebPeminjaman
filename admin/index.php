<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php?pesan=akses_ditolak");
    exit;
}

require_once '../koneksi.php';

$pesan = '';

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$csrf_token = $_SESSION['csrf_token'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aksi'])) {

    $aksi = $_POST['aksi'];
    $id = $_POST['id'] ?? '';
    $token = $_POST['csrf_token'] ?? '';

    if (!hash_equals($_SESSION['csrf_token'], $token)) {
        $pesan = 'Permintaan tidak valid. Silakan coba lagi.';
    } elseif (empty($id)) {
        $pesan = 'ID peminjaman tidak ditemukan.';
    } else {

        try {

            // Ambil data peminjaman
            $stmt = $pdo->prepare(
                "SELECT * FROM Peminjaman WHERE id_peminjaman = ?"
            );
            $stmt->execute([$id]);
            $pinjam = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$pinjam) {
                throw new Exception('Data peminjaman tidak ditemukan.');
            }

            if ($aksi === 'setujui') {

                if ($pinjam['status'] !== 'Pending') {
                    throw new Exception(
                        'Peminjaman ini bukan lagi berstatus Pending.'
                    );
                }

                $pdo->beginTransaction();

                // Periksa bentrok dengan jadwal lain
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
                    $pinjam['id_ruangan'],
                    $pinjam['tgl_pinjam'],
                    $pinjam['jam_selesai'],
                    $pinjam['jam_mulai'],
                    $id
                ]);

                if ($stmtCek->fetchColumn() > 0) {

                    $pdo->rollBack();

                    $pesan = 'Peminjaman tidak dapat disetujui karena jadwal ruangan bertabrakan dengan pengajuan lain.';

                } else {

                    // Update status menjadi Disetujui
                    $stmtUpdate = $pdo->prepare(
                        "UPDATE Peminjaman
                         SET status = 'Disetujui'
                         WHERE id_peminjaman = ?
                         AND status = 'Pending'"
                    );

                    $stmtUpdate->execute([$id]);

                    $pdo->commit();

                    $pesan = 'Pengajuan peminjaman berhasil disetujui!';
                }

            } elseif ($aksi === 'tolak') {

                if ($pinjam['status'] !== 'Pending') {
                    throw new Exception(
                        'Hanya peminjaman Pending yang dapat ditolak.'
                    );
                }

                $stmt = $pdo->prepare(
                    "UPDATE Peminjaman
                     SET status = 'Ditolak'
                     WHERE id_peminjaman = ?"
                );

                $stmt->execute([$id]);

                $pesan = 'Pengajuan peminjaman berhasil ditolak.';

            } elseif ($aksi === 'selesai') {

                if ($pinjam['status'] !== 'Disetujui') {
                    throw new Exception(
                        'Hanya peminjaman Disetujui yang dapat diselesaikan.'
                    );
                }

                $stmt = $pdo->prepare(
                    "UPDATE Peminjaman
                     SET status = 'Selesai'
                     WHERE id_peminjaman = ?"
                );

                $stmt->execute([$id]);

                $pesan = 'Peminjaman berhasil diselesaikan!';

            } else {
                throw new Exception('Aksi tidak dikenal.');
            }

        } catch (Exception $e) {

            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            $pesan = $e->getMessage();

        } catch (PDOException $e) {

            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            $pesan = 'Terjadi kesalahan database. Silakan coba lagi.';
        }
    }

    // Redirect agar form tidak terkirim ulang
    $_SESSION['pesan_admin'] = $pesan;

    header("Location: index.php");
    exit;
}

if (isset($_SESSION['pesan_admin'])) {
    $pesan = $_SESSION['pesan_admin'];
    unset($_SESSION['pesan_admin']);
}

$cari = trim($_GET['cari'] ?? '');

$query = "
    SELECT
        p.*,
        pg.nama_ormawa,
        r.nama_ruangan,
        g.nama_gedung
    FROM Peminjaman p
    JOIN Pengguna pg ON p.id_pengguna = pg.id_pengguna
    JOIN Ruangan r ON p.id_ruangan = r.id_ruangan
    JOIN Gedung g ON r.id_gedung = g.id_gedung
";

if ($cari !== '') {

    $query .= "
        WHERE p.tujuan LIKE ?
        OR pg.nama_ormawa LIKE ?
        OR r.nama_ruangan LIKE ?
        OR g.nama_gedung LIKE ?
        OR p.id_peminjaman LIKE ?
    ";

    $query .= " ORDER BY p.tgl_pengajuan DESC";

    $stmt = $pdo->prepare($query);

    $keyword = "%$cari%";

    $stmt->execute([
        $keyword,
        $keyword,
        $keyword,
        $keyword,
        $keyword
    ]);

} else {

    $query .= " ORDER BY p.tgl_pengajuan DESC";

    $stmt = $pdo->query($query);
}

$data_peminjaman = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fungsi untuk mengamankan output HTML
function e($value)
{
    return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Data Peminjaman Ruangan Kampus</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background: #f5f5f5;
            color: #333;
        }

        h2 {
            color: #800000;
        }

        a {
            text-decoration: none;
        }

        .btn {
            padding: 6px 10px;
            border-radius: 4px;
            color: white;
            font-size: 13px;
            margin: 2px;
            display: inline-block;
            border: none;
            cursor: pointer;
        }

        .btn-setujui {
            background: #28a745;
        }

        .btn-tolak {
            background: #dc3545;
        }

        .btn-selesai {
            background: #6c757d;
        }

        .btn-edit {
            background: #007bff;
        }

        .btn-tambah {
            background: #800000;
            color: white;
            padding: 8px 15px;
            border-radius: 5px;
            display: inline-block;
        }

        .btn-kembali {
            background: #666;
            color: white;
            padding: 8px 15px;
            border-radius: 5px;
            display: inline-block;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            margin-top: 15px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        th,
        td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background: #800000;
            color: white;
        }

        tr:hover {
            background: #f9f9f9;
        }

        .Pending {
            color: #e6a700;
            font-weight: bold;
        }

        .Disetujui {
            color: #28a745;
            font-weight: bold;
        }

        .Ditolak {
            color: #dc3545;
            font-weight: bold;
        }

        .Selesai {
            color: #6c757d;
            font-weight: bold;
        }

        .pesan {
            padding: 10px 15px;
            margin: 10px 0;
            border-radius: 4px;
            background: #e9f9e9;
            color: #226622;
            border: 1px solid #b3e6b3;
        }

        .form-cari {
            margin-top: 20px;
        }

        .form-cari input {
            padding: 9px;
            width: 250px;
            max-width: 70%;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .form-cari button {
            padding: 9px 15px;
            background: #800000;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .form-cari a {
            margin-left: 8px;
            color: #800000;
        }

        .aksi-form {
            display: inline;
        }

        .kosong {
            text-align: center;
            color: #777;
        }

        .table-wrapper {
            overflow-x: auto;
        }

        @media (max-width: 768px) {
            body {
                margin: 10px;
            }

            table {
                min-width: 900px;
            }
        }
    </style>
</head>

<body>

    <h2>Data Peminjaman Ruangan</h2>

    <a href="../index.php" class="btn-kembali">
        ← Kembali ke Beranda
    </a>

    <!-- <a href="../tambah.php" class="btn-tambah">
        + Tambah Peminjaman Baru
    </a> -->

    <br><br>

    <?php if (!empty($pesan)): ?>
        <div class="pesan">
            <?= e($pesan); ?>
        </div>
    <?php endif; ?>

    <!-- Form Pencarian -->
    <form method="GET" action="index.php" class="form-cari">

        <input type="text" name="cari" placeholder="Cari tujuan, ormawa, atau ruangan..." value="<?= e($cari); ?>">

        <button type="submit">Cari</button>

        <a href="index.php">Reset</a>

    </form>

    <br>

    <div class="table-wrapper">

        <table>

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

                    <?php $no = 1; ?>

                    <?php foreach ($data_peminjaman as $row): ?>

                        <tr>

                            <td><?= $no++; ?></td>

                            <td>
                                <?= e($row['id_peminjaman']); ?>
                            </td>

                            <td>
                                <?= e($row['nama_ormawa']); ?>
                            </td>

                            <td>
                                <?= e($row['nama_gedung'] . ' - ' . $row['nama_ruangan']); ?>
                            </td>

                            <td>
                                <?= e($row['tgl_pinjam']); ?>
                            </td>

                            <td>
                                <?= e(substr($row['jam_mulai'], 0, 5)); ?>
                                -
                                <?= e(substr($row['jam_selesai'], 0, 5)); ?>
                            </td>

                            <td>
                                <?= e($row['tujuan']); ?>
                            </td>

                            <td class="<?= e($row['status']); ?>">
                                <?= e($row['status']); ?>
                            </td>

                            <td>

                                <?php if ($row['status'] === 'Pending'): ?>

                                    <!-- Tombol Setujui -->
                                    <form method="POST" class="aksi-form" onsubmit="return confirm('Setujui peminjaman ini?');">

                                        <input type="hidden" name="csrf_token" value="<?= e($csrf_token); ?>">

                                        <input type="hidden" name="id" value="<?= e($row['id_peminjaman']); ?>">

                                        <input type="hidden" name="aksi" value="setujui">

                                        <button type="submit" class="btn btn-setujui">
                                            Setujui
                                        </button>

                                    </form>

                                    <!-- Tombol Tolak -->
                                    <form method="POST" class="aksi-form" onsubmit="return confirm('Tolak peminjaman ini?');">

                                        <input type="hidden" name="csrf_token" value="<?= e($csrf_token); ?>">

                                        <input type="hidden" name="id" value="<?= e($row['id_peminjaman']); ?>">

                                        <input type="hidden" name="aksi" value="tolak">

                                        <button type="submit" class="btn btn-tolak">
                                            Tolak
                                        </button>

                                    </form>

                                <?php elseif ($row['status'] === 'Disetujui'): ?>

                                    <!-- Tombol Selesai -->
                                    <form method="POST" class="aksi-form"
                                        onsubmit="return confirm('Tandai peminjaman ini selesai?');">

                                        <input type="hidden" name="csrf_token" value="<?= e($csrf_token); ?>">

                                        <input type="hidden" name="id" value="<?= e($row['id_peminjaman']); ?>">

                                        <input type="hidden" name="aksi" value="selesai">

                                        <button type="submit" class="btn btn-selesai">
                                            Selesai
                                        </button>

                                    </form>

                                <?php elseif (
                                    $row['status'] === 'Selesai' ||
                                    $row['status'] === 'Ditolak'
                                ): ?>

                                    <span style="color:#999;">
                                        Tidak ada aksi
                                    </span>

                                <?php endif; ?>

                                <!-- <a href="edit.php?id=<?= urlencode($row['id_peminjaman']); ?>" class="btn btn-edit">
                                    Edit
                                </a> -->

                                

                            </td>

                        </tr>

                    <?php endforeach; ?>

                <?php else: ?>

                    <tr>
                        <td colspan="9" class="kosong">
                            Belum ada data peminjaman.
                        </td>
                    </tr>

                <?php endif; ?>

            </tbody>

        </table>

    </div>

</body>

</html>