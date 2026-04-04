# SIMRS Laralite

Sistem Informasi Manajemen Rumah Sakit (SIMRS) berbasis Laravel 11 dengan antarmuka modern menggunakan Livewire 3 dan Flux UI.

> [!CAUTION]
> **PERINGATAN PENTING: LEGACY DATABASE**
> Proyek ini menggunakan basis data peninggalan (**Legacy Database**) dari sistem SIMRS sebelumnya. Pengembang **dilarang keras** mengubah struktur tabel atau data secara sembarangan, karena berpotensi merusak alur kerja sistem lama yang masih aktif berjalan. Segala perubahan skema harus dikoordinasikan dengan tim terkait.

## Fitur Utama

### 1. Modul Registrasi Pasien (Transaksi)
- **Daftar Registrasi**: Menampilkan list pendaftaran pasien berdasarkan rentang tanggal.
- **Pencarian Cepat**: Cari pendaftaran berdasarkan No Rawat, No RM, atau Nama Pasien.
- **Detail Registrasi**: Informasi lengkap mengenai pendaftaran, identitas pasien terkait, dan status pembayaran.

### 2. Modul Master Pasien (Data Induk)
- **Master Data**: Katalog lengkap identitas pasien dalam database.
- **Relasi Kompleks**: Menampilkan data Kelurahan, Kecamatan, Kabupaten, Suku/Bangsa, Bahasa, dan Perusahaan secara deskriptif (bukan sekadar kode).
- **Detail Rekam Medis**: Informasi mendalam identitas, alamat, kontak, hingga data penanggung jawab pasien.

### 3. Fitur UI/UX
- **Pagination Limit**: Pengguna dapat mengatur jumlah data yang tampil (20, 50, 100).
- **Modern UI**: Menggunakan komponen premium dari Flux UI dengan mode gelap/terang.
- **Responsif**: Tampilan optimal untuk berbagai ukuran layar.

## Teknologi Utama

- **Framework**: [Laravel 11](https://laravel.com)
- **Frontend**: [Livewire 3](https://livewire.laravel.com) & [Alpine.js](https://alpinejs.dev)
- **UI Components**: [Flux UI](https://fluxui.dev)
- **Database**: PHP/MySQL (MariaDB)

## Cara Instalasi

1. **Clone Repository**:
   ```bash
   git clone https://github.com/AryaYoo/simrs-laravel.git
   cd simrs-laravel
   ```

2. **Instal Dependensi**:
   ```bash
   composer install
   npm install
   ```

3. **Konfigurasi Environment**:
   Salin file `.env.example` ke `.env` dan sesuaikan pengaturan database Anda:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Jalankan Aplikasi**:
   ```bash
   php artisan serve
   npm run dev
   ```

## Repository
Dapat diakses di: [https://github.com/AryaYoo/simrs-laravel.git](https://github.com/AryaYoo/simrs-laravel.git)
