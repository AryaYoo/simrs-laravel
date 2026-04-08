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

### 4. Fitur Otomatisasi (OCR KTP)
- **Ekstraksi Data Instan**: Mampu membaca teks dari foto KTP dan memasukkannya langsung ke dalam formulir Pasien Baru (membaca NIK, Nama, Tanggal Lahir, Jenis Kelamin, dll).
- **Pengaturan Dinamis**: API Key dan profil instansi diatur langsung lewat menu *Settings* tanpa menyentuh *source code*.

### 5. Modul Riwayat Pasien & Resume Medis (Rekam Medis Digital)
- **Sinkronisasi Riwayat**: Menampilkan seluruh riwayat kunjungan pasien secara kronologis (Rawat Jalan & Rawat Inap).
- **Data SEP BPJS**: Integrasi informasi penjaminan, rujukan, dan data bridging SEP secara *real-time*.
- **Pemeriksaan SOAPIE**: Visualisasi detail riwayat pemeriksaan klinis (Subjek, Objek, Asesmen, Plan) serta grafik *Vital Signs* yang komprehensif.
- **Kodifikasi ICD**: Pencatatan Diagnosa (ICD-10) dan Prosedur/Tindakan (ICD-9) dalam format tabel terstruktur.
- **Resume Medis**: Ringkasan medis otomatis untuk setiap visit yang mencakup keluhan, jalannya penyakit, hasil penunjang, hingga instruksi pulang.
- **Informasi Biaya**: Transparansi rincian biaya tindakan, jasa medis, dan obat-obatan per kunjungan.

## Panduan Penggunaan & Setup Fitur OCR KTP

Fitur pembacaan KTP otomatis (OCR) menggunakan teknologi kecerdasan buatan dari Google Cloud Vision API. Ikuti langkah berikut untuk persiapan hingga penggunaanya:

1. **Persiapan Database (Pengaturan Aplikasi)**  
   Dikarenakan proyek ini melarang penggunaan *migration* bawaan Laravel (untuk menghindari rusaknya *Legacy Database*), tabel pengaturan dibuat manual menggunakan SQL mentah.
   - Buka alat pengelola *database* Anda (contoh: phpMyAdmin, DBeaver, HeidiSQL).
   - Temukan file `setup_pengaturan_aplikasi.sql` di root direktori (*folder*) aplikasi ini.
   - *Copy-paste* seluruh isi file tersebut dan jalankan (*Execute*) terhadap *database* Anda. File ini akan membuat tabel baru bernama `pengaturan_aplikasi`.

2. **Mendapatkan API Key Google Cloud Vision**  
   - Buat akun atau masuk ke [Google Cloud Console](https://console.cloud.google.com/).
   - Buat sebuah *Project* baru.
   - Cari layanan **Cloud Vision API**, klik tombol *Enable* (Aktifkan).
   - Buka bagian **Credentials**, buat kredensial baru bertipe **API Key**. *Copy* (salin) kode acak tersebut.

3. **Penginputan Kunci (*Inject*) ke Sistem SIMRS**  
   - Jalankan SIMRS dan login menggunakan akun dengan _Role Admin_.
   - Pada panel navigasi (*Sidebar*) sebelah kiri, gulir ke bawah dan temukan sub-menu **Pengaturan Aplikasi** di dalam kategori *Master Data*.
   - Letakkan / paste *API Key* Google Anda pada kolom **"Google Cloud Vision API Key"**, lalu klik **Simpan Perubahan**.

4. **Uji Coba Ekstraksi di Formulir (Pemakaian Sehari-hari)**  
   - Akses halaman formulir di menu **Modul -> Pasien -> Tambah Pasien Baru**.
   - Klik tombol **"Pilih Foto KTP"** (ikon kamera).
   - Gunakan foto KTP pasien.
   - Apabila gambar cukup jelas, dalam hitungan detik seluruh kolom seperti *No. KTP, Nama, Alamat, Agama, Status Perkawinan, dan Tempat/Tanggal Lahir* akan **terisi otomatis** berkat keajaiban AI!

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

4. **Setup Database (Tanpa Migration)**:
   Proyek ini menggunakan *Raw SQL* untuk menghindari risiko kerusakan *Legacy Database*.
   Jalankan kedua file SQL berikut secara berurutan menggunakan phpMyAdmin / HeidiSQL / DBeaver:

   | Urutan | File | Keterangan |
   |---|---|---|
   | 1 | `setup_laravel_tables.sql` | Tabel infrastruktur Laravel (sessions, cache, jobs) |
   | 2 | `setup_pengaturan_aplikasi.sql` | Tabel pengaturan aplikasi (API Key OCR, dsb.) |

   > [!WARNING]
   > **JANGAN** menjalankan `php artisan migrate` di server *production* untuk menghindari konflik dengan *Legacy Database*.

5. **Jalankan Aplikasi**:
   ```bash
   php artisan serve
   npm run dev
   ```


## Repository
Dapat diakses di: [https://github.com/AryaYoo/simrs-laravel.git](https://github.com/AryaYoo/simrs-laravel.git)
