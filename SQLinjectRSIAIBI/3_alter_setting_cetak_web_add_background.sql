-- Script untuk menambahkan kolom background pada tabel setting_cetak_web
-- Eksekusi di Adminer jika tabel setting_cetak_web sudah ada

ALTER TABLE `setting_cetak_web` 
ADD COLUMN `background` longtext DEFAULT NULL COMMENT 'Base64 Encoded Background Image' AFTER `logo`;
