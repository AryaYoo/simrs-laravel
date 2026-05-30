-- ============================================================
-- SQL Inject: Setting Gambar Latar Halaman Login
-- ============================================================
-- Menambahkan setting key baru di tabel `pengaturan_aplikasi`
-- untuk menyimpan gambar background halaman login (Base64).
--
-- CARA PENGGUNAAN:
--   1. Buka phpMyAdmin / Adminer / DBeaver
--   2. Pilih database SIMRS Anda
--   3. Copy-paste seluruh isi file ini lalu Execute
--
-- CATATAN: Menggunakan INSERT IGNORE sehingga AMAN
-- dijalankan berkali-kali tanpa duplikasi data.
-- ============================================================

INSERT IGNORE INTO `pengaturan_aplikasi` (`setting_key`, `setting_value`, `description`) 
VALUES ('LOGIN_BACKGROUND_IMAGE', '', 'Gambar latar halaman login (Base64 encoded, format WebP/JPG/PNG, maks 500KB)');
