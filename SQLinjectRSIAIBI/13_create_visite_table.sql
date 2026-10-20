-- Tabel Visite Farmasi / Rawat Inap RSIA IBI
-- Menampung data visite pasien rawat inap dengan format SOAPIE + Riwayat Pengobatan & Interaksi Obat

CREATE TABLE IF NOT EXISTS `visite` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `no_rawat` varchar(17) NOT NULL,
  `tgl_visite` date NOT NULL,
  `jam_visite` time NOT NULL,
  `nip` varchar(20) NOT NULL,
  `keluhan` text DEFAULT NULL COMMENT 'Subjek (Keluhan Utama)',
  `pemeriksaan` text DEFAULT NULL COMMENT 'Objek (Pemeriksaan Fisik)',
  `suhu_tubuh` varchar(5) DEFAULT NULL,
  `tensi` varchar(8) DEFAULT NULL,
  `berat` varchar(5) DEFAULT NULL,
  `tinggi` varchar(5) DEFAULT NULL,
  `respirasi` varchar(3) DEFAULT NULL,
  `nadi` varchar(3) DEFAULT NULL,
  `spo2` varchar(3) DEFAULT NULL,
  `gcs` varchar(10) DEFAULT NULL,
  `kesadaran` varchar(50) DEFAULT 'Compos Mentis',
  `alergi` varchar(255) DEFAULT NULL,
  `penilaian` text DEFAULT NULL COMMENT 'Asesmen (Penilaian Klinis)',
  `rtl` text DEFAULT NULL COMMENT 'Plan (Rencana Tindak Lanjut)',
  `instruksi` text DEFAULT NULL COMMENT 'Inst/Impl (Tindakan Dilakukan)',
  `evaluasi` text DEFAULT NULL COMMENT 'Evaluasi (Respon Pasien)',
  `riwayat_pengobatan` text DEFAULT NULL COMMENT 'Riwayat Pengobatan',
  `interaksi_obat` text DEFAULT NULL COMMENT 'Interaksi Obat',
  PRIMARY KEY (`id`),
  KEY `no_rawat` (`no_rawat`),
  KEY `nip` (`nip`),
  KEY `tgl_visite` (`tgl_visite`),
  KEY `idx_visite_composite` (`no_rawat`, `tgl_visite`, `jam_visite`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
