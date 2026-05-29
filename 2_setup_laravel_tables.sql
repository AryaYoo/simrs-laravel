-- ============================================================
-- SQL Setup: Tabel Infrastruktur Laravel (Pengganti Migration)
-- ============================================================
-- Digunakan sebagai PENGGANTI dari `php artisan migrate`
-- agar tidak berisiko mengganggu database legacy SIMRS.
--
-- CARA PENGGUNAAN:
--   1. Buka phpMyAdmin / HeidiSQL / DBeaver
--   2. Pilih database SIMRS Anda
--   3. Copy-paste seluruh isi file ini lalu Execute
--
-- CATATAN: Semua perintah menggunakan IF NOT EXISTS sehingga
-- AMAN dijalankan berkali-kali tanpa merusak data yang ada.
-- ============================================================


-- ------------------------------------------------------------
-- 1. Tabel `migrations` (Pelacak migrasi Laravel)
--    Wajib ada agar Laravel tidak mendeteksi migrasi pending
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tandai semua migration sudah dijalankan (agar artisan tidak komplain)
INSERT IGNORE INTO `migrations` (`migration`, `batch`) VALUES
  ('2026_03_20_114839_create_sessions_table', 1),
  ('2026_03_20_114938_create_cache_table', 1),
  ('2026_03_20_114939_create_jobs_table', 1),
  ('2026_03_20_115016_create_failed_jobs_table', 1);


-- ------------------------------------------------------------
-- 2. Tabel `sessions` (Sesi Login Pengguna)
--    Dibutuhkan karena SESSION_DRIVER=database di .env
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ------------------------------------------------------------
-- 3. Tabel `cache` (Cache Aplikasi)
--    Dibutuhkan karena CACHE_STORE=database di .env
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ------------------------------------------------------------
-- 4. Tabel `jobs` (Antrian Pekerjaan Background)
--    Dibutuhkan karena QUEUE_CONNECTION=database di .env
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ------------------------------------------------------------
-- 5. Tabel `failed_jobs` (Log Pekerjaan Gagal)
--    Dibutuhkan karena QUEUE_CONNECTION=database di .env
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================
-- SELESAI. Semua tabel infrastruktur Laravel telah dibuat.
-- ============================================================
