-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jan 12, 2026 at 08:13 AM
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
-- Database: `pkl`
--

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hak_cipta`
--

CREATE TABLE `hak_cipta` (
  `id` bigint UNSIGNED NOT NULL,
  `no_pendaftaran` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jenis_cipta` enum('Buku','Modul','Program Komputer','Karya Rekaman Video','Lainnya') COLLATE utf8mb4_unicode_ci NOT NULL,
  `judul_cipta` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_pencipta` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `nip_nim` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `fakultas` enum('Sekolah Vokasi','Fakultas Teknik','Fakultas Sains dan Matematika','Fakultas Kesehatan Masyarakat','Fakultas Kedokteran','Fakultas Perikanan dan Ilmu Kelautan','Fakultas Pertanian dan Peternakan','Fakultas Ekonomika dan Bisnis','Fakultas Hukum','Fakultas Ilmu Sosial dan Ilmu Politik','Fakultas Ilmu Budaya','Fakultas Psikologi','Pasca Sarjana') COLLATE utf8mb4_unicode_ci NOT NULL,
  `no_hp` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nilai_perolehan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sumber_dana` enum('Universitas Diponegoro','APBN/APBD/Swasta','Mandiri') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Universitas Diponegoro',
  `skema_penelitian` enum('Penelitian Dasar (TKT 1 - 3)','Penelitian Terapan (TKT 4 - 6)','Penelitian Pengembangan (TKT 7 - 9)','Bukan dihasilkan dari Skema Penelitian') COLLATE utf8mb4_unicode_ci NOT NULL,
  `surat_permohonan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `surat_pernyataan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `surat_pengalihan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanda_terima` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `scan_ktp` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `hasil_ciptaan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `link_ciptaan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('terkirim','proses','revisi','diterima','ditolak') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'terkirim',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_01_09_072035_create_hak_cipta_table', 1),
(5, '2026_01_12_030444_create_paten_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `paten`
--

CREATE TABLE `paten` (
  `id` bigint UNSIGNED NOT NULL,
  `no_pendaftaran` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jenis_paten` enum('Paten','Paten Sederhana') COLLATE utf8mb4_unicode_ci NOT NULL,
  `judul_paten` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_pencipta` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `nip_nim` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `fakultas` enum('Sekolah Vokasi','Fakultas Teknik','Fakultas Sains dan Matematika','Fakultas Kesehatan Masyarakat','Fakultas Kedokteran','Fakultas Perikanan dan Ilmu Kelautan','Fakultas Pertanian dan Peternakan','Fakultas Ekonomika dan Bisnis','Fakultas Hukum','Fakultas Ilmu Sosial dan Ilmu Politik','Fakultas Ilmu Budaya','Fakultas Psikologi','Pasca Sarjana') COLLATE utf8mb4_unicode_ci NOT NULL,
  `no_hp` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `prototipe` enum('Sudah','Belum') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Belum',
  `nilai_perolehan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sumber_dana` enum('Universitas Diponegoro','APBN/APBD/Swasta','Mandiri') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Universitas Diponegoro',
  `skema_penelitian` enum('Penelitian Dasar (TKT 1 - 3)','Penelitian Terapan (TKT 4 - 6)','Penelitian Pengembangan (TKT 7 - 9)','Bukan dihasilkan dari Skema Penelitian') COLLATE utf8mb4_unicode_ci NOT NULL,
  `draft_paten` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `form_permohonan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `surat_kepemilikan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `surat_pengalihan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `scan_ktp` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanda_terima` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `gambar_prototipe` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deskripsi_singkat_prototipe` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('terkirim','proses','revisi','diterima','ditolak') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'terkirim',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('uHtUOOlqiEkdrZKPPmcaLqeF7NnzNAjwzXfx3GcK', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiUXc3OXpoWXJNRWt5VjFVa1JJcjBpNFZXNnk3ZHFueXh2QWdyNFA3diI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbi9kYXNoYm9hcmQ/dGFiPXBhdGVuIjtzOjU6InJvdXRlIjtzOjE1OiJhZG1pbi5kYXNoYm9hcmQiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjE1OiJhZG1pbl9sb2dnZWRfaW4iO2I6MTtzOjEwOiJhZG1pbl9uYW1lIjtzOjQ6ImFpdmEiO30=', 1768205557);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `hak_cipta`
--
ALTER TABLE `hak_cipta`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `hak_cipta_no_pendaftaran_unique` (`no_pendaftaran`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `paten`
--
ALTER TABLE `paten`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `paten_no_pendaftaran_unique` (`no_pendaftaran`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hak_cipta`
--
ALTER TABLE `hak_cipta`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `paten`
--
ALTER TABLE `paten`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
