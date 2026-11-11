<!-- gunakan pdo untuk koneksi yang aman -->

<?php
$host = 'localhost';
$dbname = 'WebPeminjaman'; // Sesuaikan dengan nama database Anda
$username = 'root';
$password = ''; // Kosongkan jika pakai XAMPP default

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    // Set error mode ke exception agar mudah mendeteksi bug
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Koneksi database gagal: " . $e->getMessage());
}
?>