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

## ✅ Progress & Roadmap Fitur

Berikut adalah status pengembangan fitur SIMRS Laralite:

### 🏥 Modul Core & Registrasi
- [x] **Registrasi Pasien**: List transaksi real-time, pencarian cepat, & filtering.
- [x] **Master Pasien**: Manajemen identitas dengan integrasi data wilayah & penjamin.
- [x] **Master Data Medis**: Pengelolaan data Dokter, Poliklinik, Penjamin, & Perujuk.
- [x] **Master Wilayah**: Manajemen data Provinsi, Kabupaten, Kecamatan, & Kelurahan.
- [x] **AI OCR KTP**: Ekstraksi otomatis data KTP menggunakan Google Vision API.
- [x] **Dashboard Dinamis**: Monitoring real-time pasien inhouse, tren registrasi harian, distribusi penjamin, dan okupansi bed.

### 🛌 Modul Rawat Inap (Ranap)
- [x] **Mega Menu "Fused Tab"**: Navigasi intuitif untuk 50+ layanan pasien.
- [x] **Pemeriksaan (SOAPIE)**: Grafik Vital Signs & pencatatan riwayat medis terstruktur.
- [x] **Perawatan & Tindakan**: Input tindakan medis, petugas, dan BHP secara terpadu.
- [x] **Riwayat Pasien**: Timeline riwayat pemeriksaan dan kunjungan pasien.
- [x] **Resume Medis**: Form pembuatan resume otomatis dengan sinkronisasi data SOAP/Pemeriksaan, lookup ICD-10 & ICD-9 CM, serta riwayat kontrol.
- [ ] **E-Resep Ranap**: Digitalisasi resep obat Rawat Inap (Next).
- [ ] **Integrasi Penunjang**: Laborat, Radiologi, & Bank Darah (Next).

### 🏥 Modul Rawat Jalan (Ralan)
- [x] **List Pasien Ralan**: Dashboard operasional poli.
- [ ] **Pemeriksaan Dokter**: SOAP & E-Resep khusus poli (In Progress).

### 🌉 Bridging & Integrasi
- [x] **BPJS ERM**: Bridging klaim dan data pelayanan untuk BPJS Kesehatan.
- [ ] **Satu Sehat**: Integrasi SATUSEHAT Kemenkes (Roadmap).

### 🛠️ Keamanan & Standar
- [x] **Optimistic Locking**: Pencegahan *lost update* pada pengeditan bersama.
- [x] **Olive Green Branding**: UI/UX standar premium yang konsisten.
- [x] **Legacy Compatibility**: Bridge aman untuk database sistem lama.

---

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

### 3. Dokumentasi Fitur (WAJIB!)
Setiap penambahan fitur baru, perbaikan signifikan, atau modul baru **WAJIB** didokumentasikan minimal pada bagian [✅ Progress & Roadmap Fitur](#-progress--roadmap-fitur) di README ini. Hal ini penting untuk menjaga transparansi *progress* antar tim pengembang.

### 4. Keamanan Database
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
