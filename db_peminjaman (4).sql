-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Apr 11, 2026 at 02:19 AM
-- Server version: 8.4.3
-- PHP Version: 8.3.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_peminjaman`
--

-- --------------------------------------------------------

--
-- Table structure for table `alat`
--

CREATE TABLE `alat` (
  `id_alat` int NOT NULL,
  `id_kategori` int NOT NULL,
  `nama_alat` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `harga_barang` int NOT NULL,
  `harga_sewa` int NOT NULL,
  `stok` int NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `alat`
--

INSERT INTO `alat` (`id_alat`, `id_kategori`, `nama_alat`, `harga_barang`, `harga_sewa`, `stok`, `created_at`) VALUES
(1, 1, 'Kamera Canon 80D', 9000000, 100000, 5, '2026-02-15 04:02:04'),
(2, 1, 'Laptop ASUS ROG', 15000000, 150000, 2, '2026-02-15 04:02:04'),
(3, 2, 'Bor Listrik', 500000, 20000, 2, '2026-02-15 04:02:04'),
(4, 3, 'Tripod Kamera', 250000, 10000, 6, '2026-02-15 04:02:04'),
(5, 1, 'Proyektor Epson', 5000000, 75000, 1, '2026-02-15 04:02:04'),
(6, 1, 'Laptop Axioo Pongo', 7000000, 80000, 0, '2026-04-06 11:04:22'),
(9, 1, 'Redmi 13 Note', 2800000, 80000, 2, '2026-04-10 09:54:40'),
(10, 6, 'Bola Basket', 100000, 5000, 5, '2026-04-14 01:11:24');

-- --------------------------------------------------------

--
-- Table structure for table `beban_denda`
--

CREATE TABLE `beban_denda` (
  `id_denda` int NOT NULL,
  `id_peminjaman` int NOT NULL,
  `id_pengembalian` int DEFAULT NULL,
  `tipe_denda` enum('Rusak','Telat') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'Telat',
  `jumlah_denda` int DEFAULT '0',
  `keterangan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status_pembayaran_denda` enum('Belum Dibayar','Lunas') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'Belum Dibayar',
  `tanggal_pembayaran_denda` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `beban_denda`
--

INSERT INTO `beban_denda` (`id_denda`, `id_peminjaman`, `id_pengembalian`, `tipe_denda`, `jumlah_denda`, `keterangan`, `status_pembayaran_denda`, `tanggal_pembayaran_denda`, `created_at`) VALUES
(1, 13, 4, 'Telat', 10000, 'Terlambat 2 hari × 1 unit Bola Basket (Rp 5.000/hari/unit)', 'Lunas', '2026-04-15 08:53:34', '2026-04-14 01:48:08'),
(2, 14, 5, 'Telat', 30000, 'Terlambat 3 hari × 2 unit Bola Basket (Rp 5.000/hari/unit)', 'Lunas', '2026-04-15 08:54:40', '2026-04-15 01:54:02'),
(3, 12, 6, 'Telat', 320000, 'Terlambat 4 hari × 1 unit Redmi 13 Note (Rp 80.000/hari/unit)', 'Lunas', '2026-04-15 08:54:54', '2026-04-15 01:54:47'),
(4, 11, 7, 'Telat', 80000, 'Terlambat 4 hari × 1 unit Bor Listrik (Rp 20.000/hari/unit)', 'Lunas', '2026-04-15 08:55:22', '2026-04-15 01:55:13'),
(5, 11, 7, 'Rusak', 125000, 'Bor Listrik dikembalikan kondisi Rusak Ringan (25% × 1 unit)', 'Lunas', '2026-04-15 08:55:22', '2026-04-15 01:55:13'),
(6, 8, 8, 'Telat', 100000, 'Terlambat 1 hari × 1 unit Kamera Canon 80D (Rp 100.000/hari/unit)', 'Lunas', '2026-04-11 09:00:59', '2026-04-11 02:00:34');

-- --------------------------------------------------------

--
-- Table structure for table `detail_peminjaman`
--

CREATE TABLE `detail_peminjaman` (
  `id_detail` int NOT NULL,
  `id_peminjaman` int NOT NULL,
  `id_alat` int NOT NULL,
  `jumlah` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `detail_peminjaman`
--

INSERT INTO `detail_peminjaman` (`id_detail`, `id_peminjaman`, `id_alat`, `jumlah`) VALUES
(1, 1, 1, 2),
(2, 1, 4, 1),
(3, 2, 2, 1),
(4, 3, 3, 1),
(5, 4, 3, 1),
(6, 5, 3, 1),
(7, 6, 4, 2),
(8, 7, 2, 1),
(9, 8, 1, 1),
(10, 9, 4, 1),
(11, 10, 4, 1),
(12, 11, 3, 1),
(13, 12, 9, 1),
(14, 13, 10, 1),
(15, 14, 10, 2),
(16, 15, 5, 1);

-- --------------------------------------------------------

--
-- Table structure for table `kategori`
--

CREATE TABLE `kategori` (
  `id_kategori` int NOT NULL,
  `nama_kategori` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `keterangan` text COLLATE utf8mb4_general_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kategori`
--

INSERT INTO `kategori` (`id_kategori`, `nama_kategori`, `keterangan`, `created_at`) VALUES
(1, 'Elektronik', 'Peralatan elektronik seperti kamera dan laptop', '2026-02-15 04:01:47'),
(2, 'Alat Berat', 'Peralatan untuk kebutuhan teknis berat', '2026-02-15 04:01:47'),
(3, 'Aksesoris', 'Perlengkapan tambahan alat utama', '2026-02-15 04:01:47'),
(6, 'Olahraga', 'Peralatan Olahraga seperti raket badminton dan sepatu lari', '2026-04-10 09:47:38');

-- --------------------------------------------------------

--
-- Table structure for table `log_aktivitas`
--

CREATE TABLE `log_aktivitas` (
  `id_log` int NOT NULL,
  `id_user` int NOT NULL,
  `aktivitas` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `tabel` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `id_referensi` int DEFAULT NULL,
  `waktu` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `log_aktivitas`
--

INSERT INTO `log_aktivitas` (`id_log`, `id_user`, `aktivitas`, `tabel`, `id_referensi`, `waktu`) VALUES
(1, 4, 'Melakukan peminjaman alat', NULL, NULL, '2026-02-15 04:02:36'),
(2, 5, 'Mengajukan peminjaman alat', NULL, NULL, '2026-02-15 04:02:36'),
(3, 6, 'Peminjaman ditolak oleh petugas', NULL, NULL, '2026-02-15 04:02:36'),
(4, 5, 'Menghapus user', 'users', 8, '2026-02-15 04:50:58'),
(5, 6, 'Mengubah status peminjaman menjadi Ditolak', 'peminjaman', 4, '2026-04-04 02:46:38'),
(6, 5, 'Menambah peminjaman baru', 'peminjaman', 5, '2026-04-04 03:08:26'),
(7, 5, 'Mengubah status peminjaman', 'peminjaman', 5, '2026-04-04 04:12:47'),
(8, 6, 'Mengubah status peminjaman menjadi Disetujui', 'peminjaman', 7, '2026-04-08 13:19:21'),
(9, 6, 'Mengubah status peminjaman menjadi Disetujui', 'peminjaman', 6, '2026-04-08 13:19:33'),
(10, 6, 'Mengubah status peminjaman menjadi Ditolak', 'peminjaman', 5, '2026-04-08 13:19:38'),
(11, 6, 'Mengubah status peminjaman menjadi Disetujui', 'peminjaman', 8, '2026-04-09 09:11:41'),
(12, 7, 'Melakukan pembayaran peminjaman sebesar Rp. 200.000', 'peminjaman', 8, '2026-04-09 09:12:58'),
(13, 6, 'Mengubah status peminjaman menjadi Disetujui', 'peminjaman', 9, '2026-04-09 09:15:08'),
(14, 7, 'Melakukan pembayaran peminjaman sebesar Rp. 20.000', 'peminjaman', 9, '2026-04-09 09:15:30'),
(15, 7, 'Melakukan pembayaran peminjaman sebesar Rp. 0', 'peminjaman', 5, '2026-04-09 09:15:59'),
(16, 6, 'Mengubah status peminjaman menjadi Disetujui', 'peminjaman', 10, '2026-04-09 09:31:35'),
(17, 7, 'Melakukan pembayaran peminjaman sebesar Rp. 20.000', 'peminjaman', 10, '2026-04-09 09:32:17'),
(18, 7, 'Melakukan pembayaran peminjaman sebesar Rp. 0', 'peminjaman', 7, '2026-04-09 09:36:02'),
(19, 5, 'Menambah alat baru: Redmi 13 Note', 'alat', 7, '2026-04-10 09:45:36'),
(20, 5, 'Menambah kategori baru: Olahraga', 'kategori', 6, '2026-04-10 09:47:39'),
(21, 5, 'Mengubah kategori: Olahraga', 'kategori', 6, '2026-04-10 09:48:43'),
(22, 5, 'Mengubah kategori: Olahraga', 'kategori', 6, '2026-04-10 09:52:19'),
(23, 5, 'Mengubah kategori: Olahraga', 'kategori', 6, '2026-04-10 09:52:30'),
(24, 5, 'Menghapus alat', 'alat', 7, '2026-04-10 09:52:47'),
(25, 5, 'Menambah alat baru: Redmi 13 Note', 'alat', 8, '2026-04-10 09:54:12'),
(26, 5, 'Menghapus alat', 'alat', 8, '2026-04-10 09:54:24'),
(27, 5, 'Menambah alat baru: Redmi 13 Note', 'alat', 9, '2026-04-10 09:54:40'),
(28, 6, 'Mengubah status peminjaman menjadi Disetujui', 'peminjaman', 12, '2026-04-10 10:05:41'),
(29, 6, 'Mengubah status peminjaman menjadi Disetujui', 'peminjaman', 11, '2026-04-10 10:05:44'),
(30, 5, 'Menambah alat baru: Bola Basket', 'alat', 10, '2026-04-14 01:11:24'),
(31, 5, 'Mengubah alat: Bola Basket', 'alat', 10, '2026-04-14 01:13:05'),
(32, 6, 'Mengubah status peminjaman menjadi Disetujui', 'peminjaman', 14, '2026-04-11 01:41:36'),
(33, 6, 'Mengubah status peminjaman menjadi Disetujui', 'peminjaman', 13, '2026-04-11 01:41:39'),
(34, 7, 'Melakukan pembayaran sebesar Rp. 0', 'peminjaman', 6, '2026-04-11 01:46:57'),
(35, 7, 'Mengajukan pengembalian alat', 'pengembalian', 4, '2026-04-14 01:48:08'),
(36, 7, 'Melakukan pembayaran sebesar Rp. 20.000', 'peminjaman', 13, '2026-04-15 01:53:34'),
(37, 7, 'Mengajukan pengembalian alat', 'pengembalian', 5, '2026-04-15 01:54:02'),
(38, 7, 'Melakukan pembayaran sebesar Rp. 50.000', 'peminjaman', 14, '2026-04-15 01:54:40'),
(39, 7, 'Mengajukan pengembalian alat', 'pengembalian', 6, '2026-04-15 01:54:47'),
(40, 7, 'Melakukan pembayaran sebesar Rp. 480.000', 'peminjaman', 12, '2026-04-15 01:54:54'),
(41, 7, 'Mengajukan pengembalian alat', 'pengembalian', 7, '2026-04-15 01:55:13'),
(42, 7, 'Melakukan pembayaran sebesar Rp. 245.000', 'peminjaman', 11, '2026-04-15 01:55:22'),
(43, 7, 'Mengajukan pengembalian alat', 'pengembalian', 8, '2026-04-11 02:00:34'),
(44, 7, 'Melakukan pembayaran sebesar Rp. 300.000', 'peminjaman', 8, '2026-04-11 02:00:59'),
(45, 6, 'Mengubah status peminjaman menjadi Disetujui', 'peminjaman', 15, '2026-04-11 02:03:03'),
(46, 7, 'Mengajukan pengembalian alat', 'pengembalian', 9, '2026-04-11 02:03:17'),
(47, 7, 'Melakukan pembayaran sebesar Rp. 150.000', 'peminjaman', 15, '2026-04-11 02:04:11');

-- --------------------------------------------------------

--
-- Table structure for table `pembayaran`
--

CREATE TABLE `pembayaran` (
  `id_pembayaran` int NOT NULL,
  `id_peminjaman` int NOT NULL,
  `id_user` int NOT NULL,
  `jumlah_pembayaran` int DEFAULT '0' COMMENT 'Jumlah pembayaran yang diperlukan',
  `status_pembayaran` enum('Belum Dibayar','Lunas') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'Belum Dibayar',
  `tanggal_pembayaran` datetime DEFAULT NULL,
  `catatan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pembayaran`
--

INSERT INTO `pembayaran` (`id_pembayaran`, `id_peminjaman`, `id_user`, `jumlah_pembayaran`, `status_pembayaran`, `tanggal_pembayaran`, `catatan`, `created_at`) VALUES
(1, 8, 7, 200000, 'Lunas', '2026-04-09 16:12:58', 'Biaya sewa peminjaman alat', '2026-04-09 09:11:20'),
(2, 9, 7, 20000, 'Lunas', '2026-04-09 16:15:30', 'Biaya sewa peminjaman alat', '2026-04-09 09:14:48'),
(3, 10, 7, 20000, 'Lunas', '2026-04-09 16:32:17', 'Biaya sewa peminjaman alat', '2026-04-09 09:31:01'),
(4, 11, 7, 40000, 'Lunas', '2026-04-15 08:55:22', 'Biaya sewa peminjaman alat', '2026-04-10 08:53:20'),
(5, 12, 7, 160000, 'Lunas', '2026-04-15 08:54:54', 'Biaya sewa peminjaman alat', '2026-04-10 09:58:34'),
(6, 13, 7, 10000, 'Lunas', '2026-04-15 08:53:34', 'Biaya sewa peminjaman alat', '2026-04-11 01:41:06'),
(7, 14, 7, 20000, 'Lunas', '2026-04-15 08:54:40', 'Biaya sewa peminjaman alat', '2026-04-11 01:41:15'),
(8, 15, 7, 150000, 'Lunas', '2026-04-11 09:04:11', 'Biaya sewa peminjaman alat', '2026-04-11 02:02:22');

-- --------------------------------------------------------

--
-- Table structure for table `peminjaman`
--

CREATE TABLE `peminjaman` (
  `id_peminjaman` int NOT NULL,
  `id_user` int NOT NULL,
  `tanggal_pinjam` date NOT NULL,
  `tanggal_kembali_rencana` date NOT NULL,
  `status` enum('Menunggu','Disetujui','Ditolak') COLLATE utf8mb4_general_ci DEFAULT 'Menunggu',
  `total_biaya` int DEFAULT '0',
  `status_pembayaran` enum('Belum Dibayar','Lunas') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'Belum Dibayar',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `peminjaman`
--

INSERT INTO `peminjaman` (`id_peminjaman`, `id_user`, `tanggal_pinjam`, `tanggal_kembali_rencana`, `status`, `total_biaya`, `status_pembayaran`, `created_at`) VALUES
(1, 4, '2026-02-01', '2026-02-05', 'Disetujui', 0, 'Belum Dibayar', '2026-02-15 04:02:24'),
(2, 5, '2026-02-10', '2026-02-15', 'Disetujui', 0, 'Belum Dibayar', '2026-02-15 04:02:24'),
(3, 6, '2026-02-12', '2026-02-18', 'Ditolak', 0, 'Belum Dibayar', '2026-02-15 04:02:24'),
(4, 7, '2026-02-15', '2026-02-17', 'Ditolak', 0, 'Belum Dibayar', '2026-02-15 04:35:02'),
(5, 7, '2026-04-04', '2026-04-06', 'Ditolak', 0, 'Belum Dibayar', '2026-04-04 03:08:26'),
(6, 7, '2026-04-08', '2026-04-09', 'Disetujui', 0, 'Belum Dibayar', '2026-04-08 13:17:34'),
(7, 7, '2026-04-08', '2026-04-09', 'Disetujui', 0, 'Belum Dibayar', '2026-04-08 13:18:54'),
(8, 7, '2026-04-09', '2026-04-10', 'Disetujui', 200000, 'Lunas', '2026-04-09 09:11:20'),
(9, 7, '2026-04-09', '2026-04-10', 'Disetujui', 20000, 'Lunas', '2026-04-09 09:14:48'),
(10, 7, '2026-04-09', '2026-04-10', 'Disetujui', 20000, 'Lunas', '2026-04-09 09:31:01'),
(11, 7, '2026-04-10', '2026-04-11', 'Disetujui', 40000, 'Lunas', '2026-04-10 08:53:20'),
(12, 7, '2026-04-10', '2026-04-11', 'Disetujui', 160000, 'Lunas', '2026-04-10 09:58:34'),
(13, 7, '2026-04-11', '2026-04-12', 'Disetujui', 10000, 'Lunas', '2026-04-11 01:41:06'),
(14, 7, '2026-04-11', '2026-04-12', 'Disetujui', 20000, 'Lunas', '2026-04-11 01:41:15'),
(15, 7, '2026-04-11', '2026-04-12', 'Disetujui', 150000, 'Lunas', '2026-04-11 02:02:22');

-- --------------------------------------------------------

--
-- Table structure for table `pengembalian`
--

CREATE TABLE `pengembalian` (
  `id_pengembalian` int NOT NULL,
  `id_peminjaman` int NOT NULL,
  `tanggal_kembali` date NOT NULL,
  `kondisi_kembali` enum('Baik','Rusak Ringan','Rusak Berat') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'Baik',
  `denda` int DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pengembalian`
--

INSERT INTO `pengembalian` (`id_pengembalian`, `id_peminjaman`, `tanggal_kembali`, `kondisi_kembali`, `denda`, `created_at`) VALUES
(1, 1, '2026-02-05', 'Baik', 0, '2026-02-15 04:03:03'),
(2, 10, '2026-04-09', 'Baik', 0, '2026-04-09 10:06:41'),
(3, 6, '2026-04-09', 'Baik', 0, '2026-04-09 10:07:41'),
(4, 13, '2026-04-14', 'Baik', 0, '2026-04-14 01:48:08'),
(5, 14, '2026-04-15', 'Baik', 0, '2026-04-15 01:54:02'),
(6, 12, '2026-04-15', 'Baik', 0, '2026-04-15 01:54:47'),
(7, 11, '2026-04-15', 'Rusak Ringan', 0, '2026-04-15 01:55:13'),
(8, 8, '2026-04-11', 'Baik', 0, '2026-04-11 02:00:34'),
(9, 15, '2026-04-11', 'Baik', 0, '2026-04-11 02:03:17');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id_user` int NOT NULL,
  `nama` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `username` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `role` enum('admin','petugas','peminjam') COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id_user`, `nama`, `username`, `password`, `role`) VALUES
(4, 'pppp', '123', '$2y$10$VCEFlJsEkL0RLYgRhqC3X.XIhTA2rE/SZDQRlfA8RaHbpBm43WLOe', 'admin'),
(5, 'Anas Faiq', 'admin1', '$2y$10$4FZ2/X904d4JB77t1JyPgO.OYQ1n2bsGUGnLxxVZe0reBMH1QbOvq', 'admin'),
(6, 'Anas Faiq', 'petugas1', '$2y$10$xeMQLv6GcKTVqM/pf1O8sOhaqE4z.6WSWe6EGZ2GZkIl4ynKlE3ki', 'petugas'),
(7, 'Anas Faiq', 'peminjam1', '$2y$10$hgbImHEc7X7I7xe0j6BqluQ/QgMXZqvu7fXFjh9hVyS2otTJxOYdO', 'peminjam'),
(10, 'Fahri', 'fahrikebab', '$2y$10$fjEmjmFKPWhA/CdwMV4.dOmTYeYWaYlNpq5jb3Nlz1LJ5TH2JfuaW', 'peminjam');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `alat`
--
ALTER TABLE `alat`
  ADD PRIMARY KEY (`id_alat`),
  ADD KEY `id_kategori` (`id_kategori`);

--
-- Indexes for table `beban_denda`
--
ALTER TABLE `beban_denda`
  ADD PRIMARY KEY (`id_denda`),
  ADD KEY `id_peminjaman` (`id_peminjaman`),
  ADD KEY `id_pengembalian` (`id_pengembalian`);

--
-- Indexes for table `detail_peminjaman`
--
ALTER TABLE `detail_peminjaman`
  ADD PRIMARY KEY (`id_detail`),
  ADD KEY `id_peminjaman` (`id_peminjaman`),
  ADD KEY `id_alat` (`id_alat`);

--
-- Indexes for table `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`id_kategori`);

--
-- Indexes for table `log_aktivitas`
--
ALTER TABLE `log_aktivitas`
  ADD PRIMARY KEY (`id_log`),
  ADD KEY `id_user` (`id_user`);

--
-- Indexes for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD PRIMARY KEY (`id_pembayaran`),
  ADD KEY `id_peminjaman` (`id_peminjaman`),
  ADD KEY `id_user` (`id_user`);

--
-- Indexes for table `peminjaman`
--
ALTER TABLE `peminjaman`
  ADD PRIMARY KEY (`id_peminjaman`),
  ADD KEY `id_user` (`id_user`);

--
-- Indexes for table `pengembalian`
--
ALTER TABLE `pengembalian`
  ADD PRIMARY KEY (`id_pengembalian`),
  ADD KEY `id_peminjaman` (`id_peminjaman`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `alat`
--
ALTER TABLE `alat`
  MODIFY `id_alat` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `beban_denda`
--
ALTER TABLE `beban_denda`
  MODIFY `id_denda` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `detail_peminjaman`
--
ALTER TABLE `detail_peminjaman`
  MODIFY `id_detail` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `kategori`
--
ALTER TABLE `kategori`
  MODIFY `id_kategori` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `log_aktivitas`
--
ALTER TABLE `log_aktivitas`
  MODIFY `id_log` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT for table `pembayaran`
--
ALTER TABLE `pembayaran`
  MODIFY `id_pembayaran` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `peminjaman`
--
ALTER TABLE `peminjaman`
  MODIFY `id_peminjaman` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `pengembalian`
--
ALTER TABLE `pengembalian`
  MODIFY `id_pengembalian` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `alat`
--
ALTER TABLE `alat`
  ADD CONSTRAINT `alat_ibfk_1` FOREIGN KEY (`id_kategori`) REFERENCES `kategori` (`id_kategori`);

--
-- Constraints for table `beban_denda`
--
ALTER TABLE `beban_denda`
  ADD CONSTRAINT `beban_denda_ibfk_1` FOREIGN KEY (`id_peminjaman`) REFERENCES `peminjaman` (`id_peminjaman`) ON DELETE CASCADE,
  ADD CONSTRAINT `beban_denda_ibfk_2` FOREIGN KEY (`id_pengembalian`) REFERENCES `pengembalian` (`id_pengembalian`) ON DELETE SET NULL;

--
-- Constraints for table `detail_peminjaman`
--
ALTER TABLE `detail_peminjaman`
  ADD CONSTRAINT `detail_peminjaman_ibfk_1` FOREIGN KEY (`id_peminjaman`) REFERENCES `peminjaman` (`id_peminjaman`),
  ADD CONSTRAINT `detail_peminjaman_ibfk_2` FOREIGN KEY (`id_alat`) REFERENCES `alat` (`id_alat`);

--
-- Constraints for table `log_aktivitas`
--
ALTER TABLE `log_aktivitas`
  ADD CONSTRAINT `log_aktivitas_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD CONSTRAINT `pembayaran_ibfk_1` FOREIGN KEY (`id_peminjaman`) REFERENCES `peminjaman` (`id_peminjaman`) ON DELETE CASCADE,
  ADD CONSTRAINT `pembayaran_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE;

--
-- Constraints for table `peminjaman`
--
ALTER TABLE `peminjaman`
  ADD CONSTRAINT `peminjaman_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `pengembalian`
--
ALTER TABLE `pengembalian`
  ADD CONSTRAINT `pengembalian_ibfk_1` FOREIGN KEY (`id_peminjaman`) REFERENCES `peminjaman` (`id_peminjaman`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
