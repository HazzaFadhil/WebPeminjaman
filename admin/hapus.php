<!-- Jalankan query DELETE berdasarkan ID. -->

<?php
include 'koneksi.php';

$id = $_GET['id'] ?? '';

if ($id) {
    $stmt = $pdo->prepare("DELETE FROM Peminjaman WHERE id_peminjaman = ?");
    $stmt->execute([$id]);
}

header("Location: ../admin/index.php");
exit;
?>