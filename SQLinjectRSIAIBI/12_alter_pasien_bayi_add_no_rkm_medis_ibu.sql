-- Fix: Tambah kolom no_rkm_medis_ibu pada tabel pasien_bayi
-- untuk menghindari ambiguitas lookup data ibu berdasarkan nama
-- yang bisa salah jika ada pasien dengan nama yang sama.
-- 
-- Jalankan di LOKAL dulu, lalu di PRODUCTION setelah deploy kode.

ALTER TABLE pasien_bayi 
ADD COLUMN no_rkm_medis_ibu VARCHAR(15) DEFAULT NULL 
AFTER no_rkm_medis;
