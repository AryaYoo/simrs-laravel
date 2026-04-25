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
- [x] **Registrasi Pasien (Baru)**: Form pendaftaran pasien rawat jalan dengan otomatisasi No. Reg & No. Rawat, serta lookup modal premium.
- [x] **Registrasi Pasien**: List transaksi real-time, pencarian cepat, & filtering.
- [x] **Master Pasien**: Manajemen identitas dengan integrasi data wilayah & penjamin.
- [x] **Master Data Medis**: Pengelolaan data Dokter, Poliklinik, Penjamin, & Perujuk.
- [x] **Master Wilayah**: Manajemen data Provinsi, Kabupaten, Kecamatan, & Kelurahan.
- [x] **AI OCR KTP**: Ekstraksi otomatis data KTP menggunakan Google Vision API.
- [x] **Dashboard Dinamis**: Monitoring real-time pasien inhouse, tren registrasi harian, distribusi penjamin, dan okupansi bed.

### 🛌 Modul Rawat Inap (Ranap)
- [x] **Architecture Refactoring (SRP)**: Implementasi *Feature-Based Folder Structure* & *Repository Pattern* untuk seluruh sub-modul utama demi skalabilitas dan kemudahan *maintenance*.
- [x] **Mega Menu "Fused Tab"**: Navigasi intuitif untuk 50+ layanan pasien.
- [x] **Pemeriksaan (SOAPIE)**: Grafik Vital Signs & pencatatan riwayat medis terstruktur (Refactored to Repository).
- [x] **Perawatan & Tindakan**: Input tindakan medis, petugas, dan BHP secara terpadu (Refactored to Repository).
- [x] **Riwayat Pasien**: Timeline riwayat pemeriksaan dan kunjungan pasien (Refactored to Repository).
- [x] **Resume Medis**: Form pembuatan resume otomatis dengan sinkronisasi data SOAP/Pemeriksaan, lookup ICD-10 & ICD-9 CM, serta riwayat kontrol (Refactored to Feature-Based).
- [x] **Pindah & Pulang**: Modul perpindahan kamar pasien (4 opsi logika) dan proses check-out pasien terintegrasi (Refactored to Repository).
- [x] **E-Resep Ranap**: Digitalisasi resep obat Rawat Inap & Antarmuka Cepat (Split View) (Refactored to Repository).
- [x] **Integrasi Penunjang**: Laborat (PK, PA, MB) & Radiologi telah terintegrasi penuh dengan pola Repository.

### 🏥 Modul Rawat Jalan (Ralan)
- [x] **List Pasien Ralan**: Dashboard operasional poli.
- [ ] **Pemeriksaan Dokter**: SOAP & E-Resep khusus poli (In Progress).

### 💵 Modul Casemix
- [x] **Casemix Rawat Inap**: Dashboard pasien & Manajemen Resume Medis Casemix (Pola Daftar + Form Vertikal).
- [x] **Casemix Rawat Jalan**: Dashboard pasien & Manajemen Resume Medis Casemix (Pola Daftar + Form Vertikal).
- [x] **Resume List & Edit**: Alur kerja pengecekan resume per pasien dengan fitur Create, Edit, dan Delete.
- [x] **ICD Logic Support**: Integrasi pencarian ICD-10 & ICD-9 CM yang konsisten dengan modul medis utama.
- [x] **Column Mapping Mapping Resume**: Penyesuaian sumber data otomatis (Keluhan Utama -> `keluhan`, Pemeriksaan Fisik -> `pemeriksaan`, Penunjang RAD -> `rtl`).
- [x] **Integrasi Riwayat Medis**: Fitur *Attach* (Pilih Manual via Modal Premium) dan *Auto Fill* (Magic Wand) untuk menarik data Keluhan, Pemeriksaan, Lab, Tindakan, dan Obat-obatan secara otomatis ke dalam resume.

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
**Update:** Pastikan selalu menggunakan `$model->fresh()` saat validasi untuk tabel legacy dengan *composite key*.

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

### 5. Single Responsibility Principle (SRP)
Setiap class, component, atau function **WAJIB** memiliki satu tanggung jawab utama. Jangan menggabungkan logika bisnis, validasi, dan interaksi database dalam satu komponen Livewire yang sama.

#### 5.1 Struktur Folder Berbasis Fitur (Feature-Based Structure)
Sub-modul dengan operasi yang kompleks (Create, Read, Update, Detail) dilarang digabung dalam satu class file tunggal besar. Buatlah folder khusus untuk fitur tersebut (misal: `app/Livewire/Modul/RawatInap/ResumePasien/Index.php`, `Create.php`, dll).

#### 5.2 Pemisahan Layer Data (Repository Pattern)
Dilarang menumpuk proses query database berskala besar, `DB::transaction`, atau manipulasi *collection* multi-relasi lebih dari 5 baris di dalam Livewire Component. 
* Semua operasi *data fetching*/query yang intens harus dipindahkan ke dalam class `Repository` atau `Query` (misal: `app/Repositories/RawatInap/RiwayatPasienRepository.php`).
* Komponen Livewire harus setipis mungkin, hanya bertugas mengatur state UI dan menerima/mengirim *value* ke layer lain.

### 6. Modal dalam Komponen Livewire (PENTING!)

> [!WARNING]
> **Flux Modal (`<flux:modal>`) TIDAK RELIABLE** pada halaman Livewire yang kompleks (banyak `wire:model`, banyak interaksi server). Gunakan **Alpine.js Modal murni** sebagai gantinya.

**Masalah:** Livewire melakukan *DOM morphing* setiap kali ada interaksi (bukan full page reload). Proses morphing ini dapat menghancurkan *event listener* internal yang didaftarkan oleh Flux modal saat inisialisasi. Akibatnya, `$dispatch('open-modal', 'nama-modal')` tidak akan membuka modal meskipun kode terlihat benar.

**Solusi — Gunakan Alpine.js Modal:**
```blade
{{-- 1. Tambahkan x-data di root div komponen --}}
<div x-data="{ showModal: false }">

    {{-- 2. Tombol trigger: langsung toggle variable --}}
    <button type="button" @click="showModal = true">
        Buka Modal
    </button>

    {{-- 3. Modal: gunakan x-show + x-cloak --}}
    <div x-show="showModal" x-cloak class="fixed inset-0 z-[99]">
        <div class="fixed inset-0 bg-black/50" @click="showModal = false"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="bg-white rounded-2xl shadow-2xl p-6" @click.stop>
                {{-- Konten modal --}}
                <button @click="showModal = false">Tutup</button>
            </div>
        </div>
    </div>
</div>
```

**Kenapa Alpine Modal lebih reliable:**
| | Flux Modal | Alpine Modal |
|---|---|---|
| Trigger | Event dispatch (bisa hilang saat morph) | Variable boolean (selalu persisten) |
| Ketahanan DOM morph | ❌ Listener bisa rusak | ✅ State tetap hidup |
| Cocok untuk | Halaman statis / komponen sederhana | Halaman Livewire kompleks |

### 7. Penanganan Race Condition pada Async Update ($wire)

Saat membuka modal yang relies pada property Livewire (misal: menentukan kolom mana yang akan ditarik), sering terjadi **Race Condition** jika property di-set secara *deferred*. Modal Alpine akan terbuka sebelum server selesai mengupdate state Livewire.

**Solusi — Gunakan Promise `.then()` pada $wire:**
Pastikan method di Livewire (misal `prepareAttach`) me-return value atau gunakan chaining `.then()` di sisi Alpine untuk memastikan state sudah sinkron sebelum modal ditampilkan.

```javascript
// BURUK (Bisa menyebabkan data di modal salah/masih data sebelumnya)
@click="$wire.targetColumn = 'pemeriksaan'; showModal = true"

// BAIK (Modal hanya terbuka setelah server mengonfirmasi perubahan state)
@click="$wire.prepareAttach('pemeriksaan').then(() => { showModal = true })"
```

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
