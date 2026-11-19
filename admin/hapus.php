
<?php
session_start();
require_once '../koneksi.php';

// Pastikan pengguna adalah admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Hapus hanya melalui POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit;
}

// Periksa token CSRF
if (
    !isset($_POST['csrf_token'], $_SESSION['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
) {
    $_SESSION['pesan_error'] = 'Permintaan tidak valid.';
    header("Location: index.php");
    exit;
}

$id = $_POST['id'] ?? '';

if ($id !== '') {
    $stmt = $pdo->prepare(
        "DELETE FROM Peminjaman WHERE id_peminjaman = ?"
    );
    $stmt->execute([$id]);

    $_SESSION['pesan_sukses'] = 'Data peminjaman berhasil dihapus.';
}

header("Location: index.php");
exit;
