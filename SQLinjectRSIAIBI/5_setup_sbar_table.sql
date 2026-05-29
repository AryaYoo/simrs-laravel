CREATE TABLE `catatan_sbar` (
  `no_rawat` varchar(17) NOT NULL,
  `tanggal` datetime NOT NULL,
  `nip` varchar(20) NOT NULL,
  `kd_dokter` varchar(20) NOT NULL,
  `situation` text NOT NULL,
  `background` text NOT NULL,
  `assessment` text NOT NULL,
  `recommendation` text NOT NULL,
  `advice` text NOT NULL,
  `status_baca` enum('Belum','Sudah') NOT NULL DEFAULT 'Belum',
  `status_konfirmasi` enum('Belum','Sudah') NOT NULL DEFAULT 'Belum',
  `status_verifikasi` enum('Belum','Sudah') NOT NULL DEFAULT 'Belum' COMMENT 'Reserved untuk fitur Tanda Tangan Digital DPJP',
  PRIMARY KEY (`no_rawat`,`tanggal`),
  KEY `nip` (`nip`),
  KEY `kd_dokter` (`kd_dokter`),
  CONSTRAINT `catatan_sbar_ibfk_1` FOREIGN KEY (`no_rawat`) REFERENCES `reg_periksa` (`no_rawat`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `catatan_sbar_ibfk_2` FOREIGN KEY (`nip`) REFERENCES `petugas` (`nip`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `catatan_sbar_ibfk_3` FOREIGN KEY (`kd_dokter`) REFERENCES `dokter` (`kd_dokter`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Jika tabel sudah ada (sudah diinject sebelumnya), jalankan ALTER ini:
ALTER TABLE `catatan_sbar`
  ADD COLUMN `status_verifikasi` enum('Belum','Sudah') NOT NULL DEFAULT 'Belum'
  COMMENT 'Reserved untuk fitur Tanda Tangan Digital DPJP'
  AFTER `status_konfirmasi`;
