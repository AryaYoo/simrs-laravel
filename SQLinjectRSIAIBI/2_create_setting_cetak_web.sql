-- Tabel Pengaturan Cetak Dokumen Web Independen
-- Dieksekusi secara manual via Adminer

CREATE TABLE IF NOT EXISTS `setting_cetak_web` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_instansi` varchar(100) NOT NULL,
  `alamat_instansi` varchar(150) DEFAULT NULL,
  `kabupaten` varchar(30) DEFAULT NULL,
  `propinsi` varchar(30) DEFAULT NULL,
  `kontak` varchar(50) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `logo` longtext DEFAULT NULL COMMENT 'Base64 Encoded Image',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
