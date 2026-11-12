Hari ke-3: Fitur Tambahan, Testing, & Gladi Persiapan Presentasi
Fokus: Memastikan semua ketentuan terpenuhi dan latihan demo.

Fitur Tambahan (Pencarian/Search): Tambahkan kolom search sederhana di halaman utama untuk mencari data peminjaman berdasarkan tujuan atau tanggal.

Query Aggregate (Jika diperlukan): Tambahkan sedikit informasi statistik di halaman utama, misalnya total peminjaman menggunakan fungsi COUNT() atau SUM().

Finalisasi Laporan: Gabungkan semua file laporan teknis (ERD, Normalisasi, Data Dictionary) ke dalam format PDF/Word.

Gladi Bersih Demo: Coba jalankan aplikasi dari awal (impor database .sql baru) untuk memastikan tidak ada error saat dipresentasikan di depan dosen/asisten praktikum.

============================================================

1.
Pada index.php
buat halaman index.php yang menyambut pengguna dengan tampilan selamat datang dan menu navigasi atas (Navbar).

Komponen Navbar (Atas):

Logo Horizon University Karawang.

Menu link: Beranda (index.php), Ruangan (ruangan.php), Peminjaman (peminjaman.php), dan Login (login.php).

Hero Section: Judul besar "Selamat Datang di Sistem Peminjaman Fasilitas Horizon University" dengan latar belakang foto gedung kampus yang elegan.

2. 
Halaman Daftar Ruangan & Fasilitas (Sesuai Gambar 3)
Buat file baru bernama ruangan.php. Halaman ini bertugas menampilkan kartu-kartu (cards) ruangan berdasarkan data dari database (Gedung dan Ruangan).

Fitur di Halaman Ini:

Kotak Pencarian (Search Bar): Untuk mencari nama ruangan atau fasilitas.

Filter Lokasi (Dropdown): Memilih berdasarkan Gedung (Gedung A atau Gedung B).

Card Grid: Menampilkan nama ruangan, gedung, kapasitas, dan fasilitas dengan styling kotak rapi bernuansa warna merah marun khas kampus.

3. 
Halaman Form Pengajuan Peminjaman (Sesuai Gambar 1)
Inilah inti dari fungsionalitas CRUD (Create), yaitu file tambah.php (atau form-peminjaman.php) yang menduplikasi persis desain form pada Gambar 1.

Elemen Form yang Harus Ada di Kode HTML/PHP:

Nama Organisasi/Kelompok: Menggunakan dropdown <select> dari tabel Pengguna (Ormawa).

Pilih Ruangan: Menggunakan dropdown <select> dari tabel Ruangan yang digabung dengan Gedung.

Tanggal Peminjaman: Input bertipe type="date".

Jam Mulai & Jam Selesai: Input bertipe type="time".

Tujuan Penggunaan: Menggunakan elemen <textarea>.

Tombol "Kirim Pengajuan": Bertipe type="submit" untuk mengeksekusi query INSERT INTO Peminjaman ke database dengan status default 'Pending'.

=======================================================================================

perubahan pada struktur folder dan file, yaitu admin dan user.

lalu hari ini akan belajar RBAC dan membuat Register dan Login, jadi saya menambahkan register.php , login.php , 404.php.

dan dikhawatirkan akan error pada website.

ketika nanti nambahin RBAC didalam website ini pastiin table didatabase nya udah memiliki kolom role bertipe varchar(20) agar bisa membedakan hak akses admin dan mahasiswa.


