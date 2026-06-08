-- ============================================================
-- SQL Inject: Perbesar Kolom setting_value ke MEDIUMTEXT
-- ============================================================
-- Kolom TEXT hanya mampu menyimpan 65KB.
-- Gambar 500KB setelah di-encode Base64 menjadi ~666KB,
-- melebihi batas TEXT sehingga data terpotong dan gambar
-- tidak dapat ditampilkan (preview hitam).
--
-- MEDIUMTEXT mampu menyimpan hingga 16MB.
--
-- CARA PENGGUNAAN:
--   1. Buka phpMyAdmin / Adminer / DBeaver
--   2. Pilih database SIMRS Anda (sik2 / sesuai .env)
--   3. Copy-paste seluruh isi file ini lalu Execute
--
-- CATATAN: Perintah ALTER TABLE aman dijalankan
-- berkali-kali karena hanya mengubah tipe kolom.
-- ============================================================

ALTER TABLE `pengaturan_aplikasi`
MODIFY COLUMN `setting_value` MEDIUMTEXT DEFAULT NULL;
