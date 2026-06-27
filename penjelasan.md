# Penjelasan Sistem & Aturan Proyek: Sistem Rental Kendaraan (Rental AM)

Dokumen ini menjelaskan struktur arsitektur, basis data, alur bisnis, serta implementasi aturan/fitur (*rules*) yang diterapkan pada proyek **Sistem Rental Kendaraan (Rental AM)**. Proyek ini dibangun menggunakan **PHP Native** dengan arsitektur **MVC (Model-View-Controller)** murni tanpa framework, serta database **MySQL**.

---

## 1. Arsitektur Aplikasi: Pure PHP Native MVC

Aplikasi dirancang dengan pemisahan tugas yang jelas (*Separation of Concerns*) sesuai pola arsitektur **MVC**:
*   **Model (`models/`)**: Bertanggung jawab atas pengelolaan data, koneksi database, serta eksekusi query SQL menggunakan PDO (*PHP Data Objects*).
*   **View (`views/`)**: Menangani tampilan pengguna (HTML, CSS, JS) dan visualisasi data. Semua view menggunakan style modern-minimalis (CSS Flexbox/Grid) dan Font Awesome v6.
*   **Controller (`controllers/`)**: Bertindak sebagai jembatan antara Model dan View. Controller memproses input pengguna, memanggil fungsi bisnis di Model, dan menentukan View mana yang harus ditampilkan.
*   **Router (`index.php`)**: Berfungsi sebagai pintu masuk utama (*front controller*) yang menangani routing permintaan berdasarkan parameter `$_GET['action']`, memeriksa hak akses (*auth guard*), dan memanggil controller yang tepat.

---

## 2. Implementasi Ketentuan Umum Wajib

Berikut adalah pemetaan ketentuan wajib tugas ke dalam baris kode dan komponen sistem:

### A. Form Login & Manajemen Sesi (Session)
*   **Lokasi View**: [views/login.php](file:///c:/xampp/htdocs/Ayu/sim_rental/views/login.php)
*   **Lokasi Controller**: [controllers/AuthController.php](file:///c:/xampp/htdocs/Ayu/sim_rental/controllers/AuthController.php)
*   **Detail Fitur**:
    *   **Autentikasi Hak Akses**: Pengguna dapat masuk menggunakan *username* dan *password* dengan pilihan peran (*role/hak akses*): **Admin** atau **Operator**.
    *   **Enkripsi Password**: Password disimpan dalam database menggunakan algoritma enkripsi satu arah yang aman via fungsi native PHP `password_hash($password, PASSWORD_BCRYPT)`. Validasi login menggunakan `password_verify()`.
    *   **Manajemen Sesi**: Setelah login sukses, data user disimpan di `$_SESSION['user']` dan `$_SESSION['role']`.
    *   **Auth Guard (Middleware)**: Berada di bagian atas file [index.php](file:///c:/xampp/htdocs/Ayu/sim_rental/index.php#L21-L32). Jika pengguna belum login, akses ke modul manapun otomatis dialihkan (*redirect*) ke form login. Sebaliknya, jika pengguna sudah login dan mencoba mengakses halaman login, ia akan diarahkan kembali ke dashboard.
    *   **RBAC (Role-Based Access Control)**: Membatasi hak akses Operator. Operator dilarang mengakses modul master data (Kendaraan, Tarif, User) serta cetak laporan, dan akan dialihkan kembali ke Dashboard dengan pesan error jika melanggar ([index.php:L35-47](file:///c:/xampp/htdocs/Ayu/sim_rental/index.php#L35-L47)).

### B. Menu Utama & Navigasi
*   **Lokasi Navigasi**: [views/layout/sidebar.php](file:///c:/xampp/htdocs/Ayu/sim_rental/views/layout/sidebar.php)
*   **Detail Navigasi**:
    1.  **Master Data**: Menu khusus Admin untuk mengelola data master:
        *   Master Kendaraan
        *   Master Pelanggan
        *   Master Tarif Sewa
        *   Master User
    2.  **Transaksi**: Menu Transaksi Kasir untuk mencatat transaksi sewa baru, mengubah status sewa, serta memproses pengembalian kendaraan.
    3.  **Laporan**: Menu Laporan Keuangan (cetak laporan) yang merangkum data pendapatan rental, denda, dan total biaya.
    4.  **Logout**: Tombol keluar sistem (`index.php?action=logout`) untuk menghancurkan sesi (`session_destroy()`) dan kembali ke halaman login.
    5.  **Exit (Keluar Sistem)**: Diimplementasikan melalui fitur Logout yang menjamin sesi berakhir secara aman di sisi server sebelum pengguna meninggalkan aplikasi.

### C. Database MySQL (Minimal 5 Tabel)
Koneksi database dikonfigurasi pada port default **3306** dalam file [config/Database.php](file:///c:/xampp/htdocs/Ayu/sim_rental/config/Database.php#L5). Sistem menggunakan database bernama `db_rental_ayu` yang terdiri dari **5 tabel utama**:

| No | Nama Tabel | Deskripsi | Utama / Relasi |
|:---|:---|:---|:---|
| 1 | `user` | Menyimpan data otentikasi pengguna (Admin & Operator). | Master |
| 2 | `pelanggan` | Menyimpan data identitas pelanggan (NIK, Nama, Telp, Alamat). | Master |
| 3 | `tarif_sewa` | Menyimpan master tarif sewa per hari berdasarkan jenis kendaraan. | Master |
| 4 | `kendaraan` | Menyimpan data unit kendaraan, nomor plat, jenis, dan status ketersediaan. | Master |
| 5 | `transaksi_sewa` | Menyimpan rekaman transaksi rental, tanggal sewa, pengembalian, kalkulasi biaya, denda, diskon, dan total bayar. | Transaksi (Relasi ke `pelanggan` & `kendaraan`) |

Setiap tabel di atas mengimplementasikan fungsionalitas **CRUD** lengkap (*Create, Read, Update, Delete, Search, Sort*).

### D. Validasi Data yang Ketat
*   **Input Kosong Tidak Boleh Diproses**: Dilakukan pengecekan `empty()` pada Controller sebelum data dikirim ke Model (contoh: di `UserController::create()` dan `TransaksiController::create()`). Data kosong akan menghasilkan pesan error.
*   **Validasi Angka & Logika Tanggal**: 
    *   Pengisian tanggal kembali wajib setelah atau sama dengan tanggal sewa (`strtotime($tgl_kembali) >= strtotime($tgl_sewa)`).
    *   Tarif dasar, denda, dan lama sewa dipastikan bernilai positif (tidak boleh negatif).
*   **Pencegahan Data Duplikat**: Menggunakan indeks `UNIQUE` pada level database (`username`, `nik`, `no_plat`, `jenis_kendaraan`) serta divalidasi di sisi PHP dengan method check duplikat sebelum eksekusi `INSERT` (misalnya `checkUsernameDuplikat` pada Model `User`).
*   **Ketersediaan Data Master**: Transaksi sewa tidak dapat diproses jika master data kendaraan belum ada ([TransaksiController.php:L37-44](file:///c:/xampp/htdocs/Ayu/sim_rental/controllers/TransaksiController.php#L37-L44)). Sistem juga memvalidasi keberadaan master pelanggan dan master tarif kendaraan sejenis.

### E. Penanganan Error & Pesan Kesalahan PHP
*   Aplikasi menggunakan blok `try-catch` PDO Exception untuk menangani kesalahan koneksi dan query database ([config/Database.php:L26-34](file:///c:/xampp/htdocs/Ayu/sim_rental/config/Database.php#L26-L34)).
*   Jika terjadi kesalahan input atau kegagalan sistem, pesan error akan disimpan dalam variabel `$error` atau `$_SESSION['error_message']` dan dirender dengan komponen notifikasi berwarna merah (`alert alert-danger`) yang informatif tanpa memutus jalannya aplikasi.
*   Keamanan akses file direct dibatasi dengan pengecekan konstanta `SECURE_ACCESS`. Jika file dipanggil langsung tanpa melalui `index.php`, aplikasi akan langsung menolaknya (`die('Direct access not permitted')`).

### F. Fitur Pencarian (Search) & Pengurutan (Sort)
*   **Search**: Setiap modul master dan transaksi dilengkapi kolom pencarian. Pencarian diproses langsung ke query database melalui parameter `:search` pada klausa `LIKE` SQL (contoh: mencari pelanggan berdasarkan nama/NIK, kendaraan berdasarkan nama/plat).
*   **Sort**: Kolom tabel pada tampilan view dapat diklik untuk mengurutkan data secara dinamis (*Ascending* / *Descending*) berdasarkan field tertentu. Parameter `sort_by` dan `sort_order` dikirimkan melalui URL dan diproses dengan aman pada query SQL `ORDER BY`.

---

## 3. Logika Bisnis & Perhitungan Transaksi Sewa

Logika perhitungan biaya sewa kendaraan terletak pada file [models/Transaksi.php](file:///c:/xampp/htdocs/Ayu/sim_rental/models/Transaksi.php#L49-L106) di dalam method `calculateCost()`. Prosesnya adalah sebagai berikut:

1.  **Menghitung Lama Sewa**:
    Diambil dari selisih waktu (`timestamp`) antara tanggal kembali (`tgl_kembali`) dan tanggal sewa (`tgl_sewa`), kemudian dibulatkan ke atas dalam satuan hari. Jika hasil perhitungan $\le 0$, maka minimal lama sewa dibulatkan menjadi 1 hari.
    ```php
    $start = strtotime($tgl_sewa);
    $end = strtotime($tgl_kembali);
    $diff = $end - $start;
    $lama_sewa = ceil($diff / (60 * 60 * 24));
    ```

2.  **Menentukan Tarif Dasar**:
    Tarif dasar dicari dari tabel `tarif_sewa` berdasarkan `jenis_kendaraan` (Motor, City Car, SUV/Premium).

3.  **Menghitung Diskon**:
    Sistem memberikan potongan harga otomatis berdasarkan durasi sewa:
    *   Sewa **$\ge$ 7 hari**: Diskon **10%** dari total tarif sewa dasar.
    *   Sewa **$\ge$ 3 hari**: Diskon **5%** dari total tarif sewa dasar.
    *   Sewa **< 3 hari**: Tidak mendapatkan diskon.

4.  **Menghitung Denda Keterlambatan**:
    Jika kendaraan dikembalikan melebihi tanggal rencana kembali (`tgl_pengembalian_riil` > `tgl_kembali`), sistem akan menghitung hari keterlambatan dan mengenakan denda harian berdasarkan jenis kendaraan:
    *   **Motor**: Rp 25.000,- / hari terlambat.
    *   **City Car**: Rp 75.000,- / hari terlambat.
    *   **SUV / Premium**: Rp 150.000,- / hari terlambat.
    *   **Jenis Lain**: Rp 50.000,- / hari terlambat.

5.  **Menghitung Total Biaya**:
    Total bayar dihitung berdasarkan rumus:
    $$\text{Total Biaya} = (\text{Tarif Dasar} \times \text{Lama Sewa}) + \text{Denda Keterlambatan} - \text{Diskon}$$

---

## 4. Struktur Direktori Proyek

```text
sim_rental/
├── config/
│   └── Database.php          # Manajemen koneksi PDO MySQL & auto-migration
├── controllers/
│   ├── AuthController.php      # Controller login & logout
│   ├── DashboardController.php # Controller halaman utama/dashboard
│   ├── KendaraanController.php # CRUD Master Kendaraan
│   ├── PelangganController.php # CRUD Master Pelanggan
│   ├── TarifSewaController.php # CRUD Master Tarif Sewa
│   ├── TransaksiController.php # CRUD & Proses Transaksi Rental
│   ├── UserController.php      # CRUD Master User
│   └── LaporanController.php   # Cetak Laporan Keuangan
├── models/
│   ├── Kendaraan.php         # Model Query data Kendaraan
│   ├── Pelanggan.php         # Model Query data Pelanggan
│   ├── TarifSewa.php         # Model Query data Tarif Sewa
│   ├── Transaksi.php         # Model Query & Hitung Biaya Transaksi
│   └── User.php              # Model Query data User
├── views/
│   ├── layout/
│   │   └── sidebar.php       # Navigasi Menu Utama & Sidebar
│   ├── cetak_laporan.php     # View Cetak Laporan Keuangan
│   ├── dashboard.php         # View Dashboard Statistik
│   ├── form_transaksi.php    # View Transaksi Sewa & Pengembalian
│   ├── login.php             # View Form Login
│   ├── master_kendaraan.php  # View CRUD Kendaraan
│   ├── master_pelanggan.php  # View CRUD Pelanggan
│   ├── master_tarif_sewa.php # View CRUD Tarif Sewa
│   └── master_user.php       # View CRUD User
├── assets/                   # Aset CSS, Gambar, & Logo
├── db_rental.sql             # Schema Database awal & data seeders
├── index.php                 # Front Controller, Routing, & Auth Guard
├── AGENTS.md                 # Aturan Pengembangan Developer
└── penjelasan.md             # Dokumentasi penjelasan ini
```
