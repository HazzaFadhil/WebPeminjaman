Hari ke-3: Fitur Tambahan, Testing, & Gladi Persiapan Presentasi
Fokus: Memastikan semua ketentuan terpenuhi dan latihan demo.

Fitur Tambahan (Pencarian/Search): Tambahkan kolom search sederhana di halaman utama untuk mencari data peminjaman berdasarkan tujuan atau tanggal.

Query Aggregate (Jika diperlukan): Tambahkan sedikit informasi statistik di halaman utama, misalnya total peminjaman menggunakan fungsi COUNT() atau SUM().

Finalisasi Laporan: Gabungkan semua file laporan teknis (ERD, Normalisasi, Data Dictionary) ke dalam format PDF/Word.

Gladi Bersih Demo: Coba jalankan aplikasi dari awal (impor database .sql baru) untuk memastikan tidak ada error saat dipresentasikan di depan dosen/asisten praktikum.

============================================================

1.  Pada index.php
    buat halaman index.php yang menyambut pengguna dengan tampilan selamat datang dan menu navigasi atas (Navbar).

Komponen Navbar (Atas):

Logo Horizon University Karawang.

Menu link: Beranda (index.php), Ruangan (ruangan.php), Peminjaman (peminjaman.php), dan Login (login.php).

Hero Section: Judul besar "Selamat Datang di Sistem Peminjaman Fasilitas Horizon University" dengan latar belakang foto gedung kampus yang elegan.

2.  Halaman Daftar Ruangan & Fasilitas (Sesuai Gambar 3)
    Buat file baru bernama ruangan.php. Halaman ini bertugas menampilkan kartu-kartu (cards) ruangan berdasarkan data dari database (Gedung dan Ruangan).

Fitur di Halaman Ini:

Kotak Pencarian (Search Bar): Untuk mencari nama ruangan atau fasilitas.

Filter Lokasi (Dropdown): Memilih berdasarkan Gedung (Gedung A atau Gedung B).

Card Grid: Menampilkan nama ruangan, gedung, kapasitas, dan fasilitas dengan styling kotak rapi bernuansa warna merah marun khas kampus.

3.  Halaman Form Pengajuan Peminjaman (Sesuai Gambar 1)
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

===============================================
fadhil
1. Dashboard - login - setelah login 'login' sudah menjadi nama user
2. Lalu userflow Peminjaman diperbaiki, flowchart juga diperbaiki.
3. lalu setelah pengubahan pada userflow dan flowchart diubah juga di laporan dan ppt
4. Latar belakang tambahkan data


=============================================================
tahap git

1. Selesaikan Bagian Anda Dulu (Jika Masih Ada yang Mau Ditambah)
Kalau Anda masih mau melanjutkan fitur di branch Penyesuaian-admin sebelum digabung:

git add .
git commit -m "selesaikan penyesuaian admin"
git push origin Penyesuaian-admin
(Kalau yang barusan di-push sudah cukup untuk digabung sekarang, langkah ini bisa langsung dilewati).

2. Gabungkan Kedua Branch ke main di GitHub (Pull Request)
Buka halaman repository WebPeminjaman di GitHub:
Gabungkan fitur-login dulu (atau Penyesuaian-admin dulu, urutannya bebas):

Klik Compare & pull request pada branch fitur-login (atau masuk ke tab Pull requests->New pull request, pilih base: main<-compare: fitur-login).
Klik Create pull request, cek sekilas perubahannya, lalu klik Merge pull request-> Confirm merge.

Gabungkan branch kedua (Penyesuaian-admin) ke main:

Lakukan hal yang sama: buat Pull Request dari Penyesuaian-admin ke main.

Jika muncul tulisan Able to merge (hijau): Langsung klik Merge pull request->Confirm merge.

Jika muncul Can't automatically merge (ada konflik karena mengedit baris yang sama): Tarik dulu main terbaru ke branch Penyesuaian-admin di lokal Anda, bereskan konfliknya di VS Code, lalu push lagi:

git checkout main
git pull origin main
git checkout Penyesuaian-admin
git merge main
# (pilih kode yang dipakai di VS Code jika ada tanda bentrok, lalu simpan)
git add .
git commit -m "fix: selesaikan konflik dengan main"
git push origin Penyesuaian-admin

Setelah di-push, tombol Merge pull request di GitHub akan otomatis berubah hijau dan siap di-merge.

3. Samakan Komputer Anda dan Teman Anda ke Versi Terbaru
Begitu kedua branch tadi sudah resmi masuk ke main di GitHub, Anda dan teman Anda masing-masing cukup menjalankan dua perintah ini di terminal laptop masing-masing:

git checkout main
git pull origin main

Setelah menjalankan itu:

Folder proyek di laptop Anda maupun laptop teman Anda akan punya isi yang 100% identik (gabungan fitur-login + Penyesuaian-admin).

Saat nanti mau lanjut bikin fitur berikutnya lagi, tinggal buat branch baru lagi dari main yang sudah bersih dan terbaru itu (git checkout -b nama-fitur-baru).
