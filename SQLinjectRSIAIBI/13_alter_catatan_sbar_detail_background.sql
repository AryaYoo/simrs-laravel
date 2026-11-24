-- =========================================================================
-- SQL Injection: Penyesuaian Detail Sub-Form Background pada Catatan SBAR
-- =========================================================================
-- Menambahkan kolom-kolom terstruktur untuk bagian Background (B)
-- Menggunakan IF NOT EXISTS sehingga aman dieksekusi berurutan (1..13)
-- maupun dieksekusi berkali-kali tanpa error duplicate column (1060).
-- Data lama yang ada pada kolom `background` tetap aman 100%.
-- =========================================================================

ALTER TABLE `catatan_sbar`
  ADD COLUMN IF NOT EXISTS `bg_riwayat_penyakit_dahulu` text DEFAULT NULL COMMENT 'MRS / Dirawat dengan / Riwayat Penyakit Dahulu' AFTER `background`,
  ADD COLUMN IF NOT EXISTS `bg_riwayat_penyakit` text DEFAULT NULL COMMENT 'Riwayat Penyakit' AFTER `bg_riwayat_penyakit_dahulu`,
  ADD COLUMN IF NOT EXISTS `bg_informasi_klinis` text DEFAULT NULL COMMENT 'Informasi Klinis' AFTER `bg_riwayat_penyakit`,
  ADD COLUMN IF NOT EXISTS `bg_penunjang` text DEFAULT NULL COMMENT 'Lab / Pemeriksaan Penunjang lain' AFTER `bg_informasi_klinis`,
  ADD COLUMN IF NOT EXISTS `bg_alergi` text DEFAULT NULL COMMENT 'Riwayat Alergi' AFTER `bg_penunjang`,
  ADD COLUMN IF NOT EXISTS `bg_kesadaran` varchar(50) DEFAULT NULL COMMENT 'Tanda Vital: Kesadaran' AFTER `bg_alergi`,
  ADD COLUMN IF NOT EXISTS `bg_tensi` varchar(20) DEFAULT NULL COMMENT 'Tanda Vital: TD mmHg' AFTER `bg_kesadaran`,
  ADD COLUMN IF NOT EXISTS `bg_nadi` varchar(20) DEFAULT NULL COMMENT 'Tanda Vital: Nadi x/mnt' AFTER `bg_tensi`,
  ADD COLUMN IF NOT EXISTS `bg_respirasi` varchar(20) DEFAULT NULL COMMENT 'Tanda Vital: Resp /menit' AFTER `bg_nadi`,
  ADD COLUMN IF NOT EXISTS `bg_suhu` varchar(20) DEFAULT NULL COMMENT 'Tanda Vital: Temp °C' AFTER `bg_respirasi`,
  ADD COLUMN IF NOT EXISTS `bg_spo2` varchar(20) DEFAULT NULL COMMENT 'Tanda Vital: SpO2 %' AFTER `bg_suhu`,
  ADD COLUMN IF NOT EXISTS `bg_terapi` text DEFAULT NULL COMMENT 'Terapi saat ini' AFTER `bg_spo2`;
