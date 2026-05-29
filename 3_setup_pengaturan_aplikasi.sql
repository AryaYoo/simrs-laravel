-- SQL Script untuk Tabel Pengaturan Aplikasi (Settings)
-- Eksekusi file ini langsung ke dalam database / phpMyAdmin / DBeaver Anda.

CREATE TABLE IF NOT EXISTS `pengaturan_aplikasi` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `setting_key_unique` (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- Memasukkan data awal (default settings) untuk OCR Google Vision API
INSERT IGNORE INTO `pengaturan_aplikasi` (`setting_key`, `setting_value`, `description`) 
VALUES ('GOOGLE_VISION_API_KEY', '', 'Kunci API (API Key) untuk layanan Google Cloud Vision OCR Foto KTP');

-- Contoh setting bawaan lain yang mungkin berguna nantinya untuk SIMRS
INSERT IGNORE INTO `pengaturan_aplikasi` (`setting_key`, `setting_value`, `description`) 
VALUES ('NAMA_INSTANSI', 'RSUD Coba Saja', 'Nama Instansi / Rumah Sakit');
