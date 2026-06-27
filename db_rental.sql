CREATE DATABASE IF NOT EXISTS db_rental_ayu;
USE db_rental_ayu;

CREATE TABLE IF NOT EXISTS user (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nama_lengkap VARCHAR(100) NOT NULL,
    role ENUM('Admin', 'Operator') NOT NULL
);

CREATE TABLE IF NOT EXISTS pelanggan (
    id_pelanggan INT AUTO_INCREMENT PRIMARY KEY,
    nama_pelanggan VARCHAR(100) NOT NULL,
    no_telp VARCHAR(20) NOT NULL,
    alamat TEXT NOT NULL,
    nik VARCHAR(30) NOT NULL UNIQUE
);

CREATE TABLE IF NOT EXISTS tarif_sewa (
    id_tarif INT AUTO_INCREMENT PRIMARY KEY,
    jenis_kendaraan VARCHAR(50) NOT NULL UNIQUE,
    tarif_per_hari DECIMAL(10,2) NOT NULL
);

CREATE TABLE IF NOT EXISTS kendaraan (
    id_kendaraan INT AUTO_INCREMENT PRIMARY KEY,
    nama_kendaraan VARCHAR(100) NOT NULL,
    no_plat VARCHAR(20) NOT NULL UNIQUE,
    jenis_kendaraan VARCHAR(50) NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'Tersedia',
    foto_kendaraan VARCHAR(100) DEFAULT 'default.jpg'
);

CREATE TABLE IF NOT EXISTS transaksi_sewa (
    id_transaksi INT AUTO_INCREMENT PRIMARY KEY,
    id_pelanggan INT NOT NULL,
    id_kendaraan INT NOT NULL,
    tgl_sewa DATE NOT NULL,
    tgl_kembali DATE NOT NULL,
    tgl_pengembalian_riil DATE NULL,
    lama_sewa INT NOT NULL,
    tarif_dasar DECIMAL(10,2) NOT NULL,
    denda DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    diskon DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    total_biaya DECIMAL(10,2) NOT NULL,
    status_transaksi VARCHAR(20) NOT NULL DEFAULT 'Berjalan',
    FOREIGN KEY (id_pelanggan) REFERENCES pelanggan(id_pelanggan),
    FOREIGN KEY (id_kendaraan) REFERENCES kendaraan(id_kendaraan)
);

INSERT IGNORE INTO user (id, username, password, nama_lengkap, role) VALUES 
<<<<<<< HEAD
(1, 'Ayu Mulyasih', '$2y$10$u.2WS5gxxOXnamOm75bnEeAxyDKvOnlzVD2BZxpWEB1.xDkZsDDwO', 'Ayu Mulyasih', 'Admin'),
(2, 'operator', '$2y$10$7aUEmeJhJAWMo8wAePgsIe8sZWN9LgA0Ksd/CHFiaNjKZ8kq.hB9K', 'Ayu Mulyasih', 'Operator');
=======
(1, 'Ayu Mulyasih', '$2y$10$vRbDl9NWq75H6OGlRqlQZeyYixmi2VrFGNNyS./GzgRzYMxZSgmyG', 'Ayu Mulyasih', 'Admin'),
(2, 'Ayuu', '$2y$10$sI3N0BUCF2f85fxrsiawdOaabSPqlhiecNvz7QoHS8WXw0PLNAKyi', 'Ayu Mulyasih', 'Operator');
>>>>>>> 072298097503d1ea46dbea2925d494f32e5ac267

INSERT IGNORE INTO tarif_sewa (id_tarif, jenis_kendaraan, tarif_per_hari) VALUES
(1, 'Motor', 80000.00),
(2, 'City Car', 350000.00),
(3, 'SUV/Premium', 700000.00);

INSERT IGNORE INTO pelanggan (id_pelanggan, nama_pelanggan, no_telp, alamat, nik) VALUES
(1, 'Budi Santoso', '081234567890', 'Jl. Mawar No. 12, Jakarta', '3171012345670001'),
(2, 'Siti Aminah', '082345678901', 'Jl. Melati No. 5, Bandung', '3273012345670002'),
(3, 'Dewi Lestari', '083456789012', 'Jl. Anggrek No. 8, Surabaya', '3578012345670003'),
(4, 'Andi Wijaya', '085678901234', 'Jl. Kenanga No. 20, Yogyakarta', '3404012345670004'),
(5, 'Eko Prasetyo', '087890123456', 'Jl. Dahlia No. 15, Semarang', '3374012345670005');

INSERT IGNORE INTO kendaraan (id_kendaraan, nama_kendaraan, no_plat, jenis_kendaraan, status, foto_kendaraan) VALUES
(1, 'Honda Vario 160', 'B 1234 ABC', 'Motor', 'Tersedia', 'default.jpg'),
(2, 'Toyota Avanza', 'D 5678 DEF', 'City Car', 'Tersedia', 'default.jpg'),
(3, 'Mitsubishi Pajero Sport', 'L 9012 GHI', 'SUV/Premium', 'Tersedia', 'default.jpg'),
(4, 'Honda Beat', 'AB 4567 XY', 'Motor', 'Disewa', 'default.jpg'),
(5, 'Toyota Innova Zenix', 'H 8888 ZZ', 'SUV/Premium', 'Tersedia', 'default.jpg');

INSERT IGNORE INTO transaksi_sewa (id_transaksi, id_pelanggan, id_kendaraan, tgl_sewa, tgl_kembali, tgl_pengembalian_riil, lama_sewa, tarif_dasar, denda, diskon, total_biaya, status_transaksi) VALUES
(1, 1, 1, '2026-06-15', '2026-06-16', '2026-06-16', 1, 80000.00, 0.00, 0.00, 80000.00, 'Selesai'),
(2, 2, 2, '2026-06-10', '2026-06-13', '2026-06-13', 3, 350000.00, 0.00, 52500.00, 997500.00, 'Selesai'),
(3, 3, 3, '2026-06-18', '2026-06-20', '2026-06-21', 2, 700000.00, 150000.00, 0.00, 1550000.00, 'Selesai'),
(4, 5, 4, '2026-06-21', '2026-06-23', NULL, 2, 80000.00, 0.00, 0.00, 160000.00, 'Berjalan'),
(5, 4, 5, '2026-06-12', '2026-06-17', '2026-06-19', 5, 700000.00, 300000.00, 175000.00, 3625000.00, 'Selesai');

