# SIMRS Laralite

Sistem Informasi Manajemen Rumah Sakit (SIMRS) modern berbasis **Laravel 11**, **Livewire 3**, dan **Flux UI**. Dirancang untuk performa tinggi, keamanan data maksimal, dan antarmuka pengguna yang sangat responsif.

---

> [!CAUTION]
> ### 🚨 PERINGATAN REKAYASA DATABASE (LEGACY)
> Proyek ini beroperasi di atas basis data peninggalan (**Legacy Database**) yang masih digunakan oleh sistem lain. 
> * **DILARANG** menjalankan `php artisan migrate`.
> * **DILARANG** mengubah struktur tabel (Rename/Delete column) tanpa koordinasi.
> * Semua penambahan tabel baru harus dilakukan via **Raw SQL** atau script setup yang disediakan.

---

## 📑 Daftar Isi
- [Fitur Utama](#-fitur-utama)
- [Instalasi](#-instalasi)
- [Standard Pengembangan (SOP)](#-standard-pengembangan-sop)
- [Panduan OCR KTP](#-panduan-ocr-ktp)
- [Branding & Design](#-branding--design)

---

## ✨ Fitur Utama

| Kategori | Fitur Unggulan |
| :--- | :--- |
| **Registrasi** | List transaksi real-time, pencarian cepat, & detail pendaftaran. |
| **Master Pasien** | Katalog identitas lengkap dengan relasi deskriptif (wilayah & penjamin). |
| **Rekam Medis** | Grafik Vital Signs, riwayat SOAPIE, kodifikasi ICD-9/10, & Resume Medis otomatis. |
| **Navigasi** | **Mega Menu "Fused Tab"** yang intuitif untuk akses 50+ layanan pasien dalam satu klik. |
| **Dashboard** | Statistik interaktif yang berfungsi ganda sebagai filter kategori pasien (BPJS/Umum/Lainnya). |
| **AI Powered** | Ekstraksi data KTP menggunakan Google Vision API (OCR). |

---

## 🚀 Instalasi

1. **Clone & Install**
   ```bash
   git clone https://github.com/AryaYoo/simrs-laravel.git
   composer install && npm install
   ```

2. **Environment Setup**
   Salin `.env.example` menjadi `.env` dan atur koneksi database Anda.

3. **Database Initialization**
   Jalankan file SQL berikut di phpMyAdmin/DBeaver untuk menyiapkan tabel infrastruktur:
   1. `setup_laravel_tables.sql`
   2. `setup_pengaturan_aplikasi.sql`
   3. `sik.sql` *jika belum instalasi khanza sebelumnya
   4. `mlite_only.sql` *jika belum instalasi khanza sebelumnya

4. **Run Server**
   ```bash
   php artisan serve
   npm run dev
   ```

---

## 🛠 Standard Pengembangan (SOP)

Untuk menjaga kualitas dan integritas data, setiap pengembang **WAJIB** mengikuti standar berikut:

### 1. Concurrency Control (PENTING!)
Untuk mencegah data tertimpa (*Lost Update*) saat dua user mengedit data yang sama, gunakan Trait `WithOptimisticLocking`.

**Cara Implementasi di Livewire:**
```php
use App\Livewire\Concerns\WithOptimisticLocking;

class EditData extends Component {
    use WithOptimisticLocking;

    public function mount($id) {
        $model = Model::find($id);
        // 1. Inisialisasi kunci saat data dimuat
        $this->initializeLock($model); 
    }

    public function save() {
        $model = Model::find($this->id);
        // 2. Validasi kunci sebelum menyimpan
        $this->validateLock($model); 

        $model->update([...]);
    }
}
```

### 2. UI/UX & Branding
Aplikasi ini menggunakan identitas **Olive Green**.
* **Primary Color**: `#4C5C2D` (Olive Green)
* **Secondary Color**: `#8CC7C4` (Teal Accent)
* **Background Aktif**: Gunakan `#F1F5E9` untuk elemen yang sedang dipilih/aktif.
* **Komponen**: Selalu prioritaskan komponen dari **Flux UI** untuk menjaga konsistensi.

### 3. Keamanan Database
Jangan gunakan fitur `Model::create` atau `Model::update` tanpa validasi input yang ketat. Selalu gunakan `DB::beginTransaction()` untuk transaksi yang melibatkan lebih dari satu tabel.

---

## 📸 Panduan OCR KTP
Fitur AI untuk membaca KTP otomatis dapat diaktifkan melalui:
1. Masuk ke **Master Data -> Pengaturan Aplikasi**.
2. Masukkan **Google Cloud Vision API Key**.
3. Pastikan layanan *Cloud Vision API* sudah aktif di Google Cloud Console Anda.

---

## 📦 Repository
Dapat diakses di: [https://github.com/AryaYoo/simrs-laravel.git](https://github.com/AryaYoo/simrs-laravel.git)

---
*Dikembangkan dengan ❤️ untuk kemajuan Layanan Kesehatan Indonesia.*
