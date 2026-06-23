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
- [x] **Mega Menu "Fused Tab"**: Navigasi intuitif untuk 50+ layanan pasien dengan fitur *Contextual Target Navigation*.
- [x] **Diagnosa & Prosedur (Ranap)**: Implementasi *Dual-Panel Cart System* dengan fitur *Full Sync*, *Auto-Increment* prioritas, dan form *manual override* (VC, AP, AST, IM).
- [x] **Pemeriksaan (SOAPIE)**: Grafik Vital Signs & pencatatan riwayat medis terstruktur (Refactored to Repository).
- [x] **Perawatan & Tindakan**: Input tindakan medis, petugas, dan BHP secara terpadu (Refactored to Repository).
- [x] **Riwayat Pasien**: Timeline riwayat pemeriksaan dan kunjungan pasien (Refactored to Repository).
- [x] **Resume Medis (Ranap)**: Form pembuatan resume otomatis dengan tata letak *continuous scroll*, navigasi *minimap/scrollspy*, integrasi riwayat klinis otomatis (*auto-fill*), fitur *attachment* (Tindakan, Obat, Lab) via Alpine Modal, serta penguncian status *checkout* (Refactored to Feature-Based & Repository).
- [x] **Pindah & Pulang**: Modul perpindahan kamar pasien (4 opsi logika) dan proses check-out pasien terintegrasi (Refactored to Repository).
- [x] **E-Resep Ranap**: Digitalisasi resep obat Rawat Inap & Antarmuka Cepat (Split View) (Refactored to Repository).
- [x] **Catatan Keperawatan Ranap**: Modul pencatatan asuhan keperawatan rawat inap dengan sinkronisasi waktu dan UI premium.
- [x] **Pengkajian Awal Keperawatan Umum (Ranap)**: Form vertikal 10 tahap interaktif lengkap dengan navigasi scrollspy, auto-kalkulasi Risiko Jatuh (Morse & Sydney), dan integrasi checklist masalah/rencana keperawatan.
- [x] **Observasi Rawat Inap**: Halaman khusus untuk pemantauan tanda-tanda vital pasien dengan *modal form* berbasis Alpine.js yang ditenagai oleh pola Repository.
- [x] **Observasi Rawat Inap Kebidanan**: Modul pencatatan vital signs & kebidanan (Kontraksi, BJJ, PPV, VT) dengan Alpine.js Modal dan pola Repository.
- [x] **Observasi Rawat Inap Post Partum**: Modul pencatatan vital signs & post partum (TFU, Kontraksi, Perdarahan, Keterangan) dengan Alpine.js Modal dan pola Repository.
- [x] **Observasi CHBP**: Modul pencatatan harian bayi dan partus (DJJ, HIS, PPV) dengan Alpine.js Modal dan pola Repository.
- [x] **Observasi Induksi Persalinan**: Modul pencatatan pemberian obat induksi (Obat, Cairan, Dosis, HIS, DJJ, Keterangan) dengan Alpine.js Modal dan pola Repository.
- [x] **Catatan ADIME Gizi**: Modul pencatatan asuhan gizi ADIME (Asesmen, Diagnosis, Intervensi, Monitoring, Evaluasi, Instruksi) dengan *Left/Right Slide-In Panel* Alpine.js dan pola Repository.
- [x] **Komunikasi SBAR**: Modul pencatatan serah terima pasien SBAR (Situation, Background, Assessment, Recommendation) dengan *Right Slide-In Panel* Alpine.js, lookup Petugas & DPJP, status Baca/Konfirmasi, dan pola Repository. Terintegrasi sebagai sub-tab di dalam tab Pemeriksaan.
- [x] **Hasil USG Kandungan**: Modul pencatatan hasil pemeriksaan USG kandungan (Biometri Janin, Plasenta, Cairan Ketuban, Kelainan Kongenital) dengan *Right Slide-In Panel* Alpine.js dan pola Repository.
- [x] **Hasil USG Gynecologi**: Modul pencatatan hasil pemeriksaan USG Gynecologi (Uterus, Parametrium, Ovarium, Doppler, Kesimpulan) dengan *Right Slide-In Panel* Alpine.js dan pola Repository.
- [x] **Integrasi Penunjang**: Laborat (PK, PA, MB) terintegrasi penuh dengan pola Repository, termasuk rekap riwayat Tab Hasil (Master-Detail).
- [x] **Kelahiran Bayi**: Modul pencatatan data kelahiran bayi (Antropometri, Skor APGAR interaktif, Data Orang Tua, Proses Persalinan) dengan form vertikal 4 card, floating minimap, modal detail premium, serta halaman CRUD (Create, Edit, View Modal) berbasis Repository Pattern dan Flux UI.

### 🏥 Modul Rawat Jalan (Ralan)
- [x] **List Pasien Ralan**: Dashboard operasional poli dengan filter lanjutan (Dokter, Poliklinik, & Periode).
- [x] **Diagnosa & Prosedur (ICD-10 & ICD-9)**: Antarmuka terpadu dengan fitur *Multi-Select*, penomoran urut otomatis, dan tampilan *Top 10* diagnosa teratas (Refactored to Repository).
- [x] **Riwayat Pasien**: Integrasi rekam medis lengkap (SOAPIE, Kunjungan, Hasil USG) dengan standar UI premium.
- [x] **Permintaan Lab**: Modul permintaan pemeriksaan laboratorium (PK, PA, MB) dengan lookup pemeriksaan dinamis serta rekap riwayat Tab Hasil (Master-Detail) (Refactored to Repository).
- [ ] **Permintaan Radiologi**: Modul permintaan pemeriksaan radiologi (Roadmap).
- [x] **Triase IGD**: Modul triase gawat darurat lengkap (Skala 1-5, Primer & Sekunder) dengan antarmuka tabbed premium (Navigation, Edit, & Delete features fully implemented & stabilized).
- [x] **Pengkajian Awal Keperawatan IGD**: Form vertikal 6 tahap lengkap dengan floating minimap, panduan skala nyeri visual, integrasi tabel masalah/rencana keperawatan, dan View Modal Alpine.js (Refactored to Repository, Optimistic Locking).
- [x] **Catatan Keperawatan Ralan**: Modul pencatatan asuhan keperawatan rawat jalan terintegrasi dengan legacy database.
- [x] **Observasi IGD**: Modul pencatatan tanda-tanda vital pasien IGD (GCS, TD, HR, RR, Suhu, SpO2) dengan CRUD penuh, validasi ketat, dan sinkronisasi legacy database (Refactored to Repository).
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
- [x] **Login Split-Screen Modern**: Halaman login *split-screen* (kiri: gambar showcase yang dapat dikelola admin via Pengaturan Aplikasi, kanan: form login). Mendukung format WebP/JPG/PNG (maks 500KB) dengan *fallback* gradient Olive Green otomatis.

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

**ATURAN KRUSIAL:** Setelah melakukan operasi simpan (insert/update/delete) pada *database* yang dapat mengubah state tabel referensi utama (misal: merubah status `RegPeriksa` dari "Belum" menjadi "Sudah"), Anda **WAJIB** memperbarui penguncian (lock) di memori komponen menggunakan `$this->initializeLock($model->fresh())` pada akhir proses simpan. Jika hal ini dilewatkan, akan terjadi `CONCURRENCY_ERROR` palsu saat pengguna yang sama mencoba melakukan aksi kedua kalinya di halaman yang sama tanpa me-refresh browser.

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

        // 3. Eksekusi update data
        $model->update([...]);
        
        // 4. (WAJIB) Perbarui kunci/hash setelah data tersimpan 
        // agar pengguna bisa lanjut menggunakan form tanpa error
        $this->initializeLock($model->fresh());
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

### 7. Standar Halaman Cetak Dokumen (Web Print)
Saat mengembangkan halaman pratinjau dan cetak dokumen (seperti Permintaan Laboratorium, Resep, dll), ikuti panduan tata letak fisik A4/F4 ini:

1. **Pengaturan Kop Surat Mandiri:** 
   Selalu gunakan data dari tabel `setting_cetak_web` sebagai prioritas utama (memungkinkan kustomisasi Kop Surat, Logo Base64, dan Background/Watermark khusus cetak Web tanpa merusak data Legacy Khanza). Jika data mandiri kosong, buatlah logika *fallback* ke tabel `setting` standar.
2. **Paginasi Cerdas (*Smart Chunking*):** 
   Dilarang membiarkan `div` halaman meregang/memanjang tak terbatas jika datanya panjang. Ini akan memancing *browser* memuntahkan halaman kosong (*spill-over*) dan merusak *background*. Potong *array* di Controller secara dinamis: **~22 baris** untuk Halaman Pertama (karena terpotong Kop Surat), dan **~32 baris** untuk Halaman Kedua dan seterusnya. Kirim variabel `$pages` ke Blade untuk di-looping.
3. **Penyembunyian Kop Surat:** 
   Untuk menghemat ruang kertas, elemen Kop Surat lengkap (Logo, Alamat RS) secara eksklusif hanya ditampilkan di halaman pertama (`$pageIndex == 0`). Halaman selanjutnya cukup melanjutkan judul dokumen dan tabel datanya.
4. **Penomoran & Watermark:** 
   Sematkan indikator penomoran halaman (contoh: `Halaman 1 / 2`) di pojok kertas (misal: kiri bawah) menggunakan `position: absolute` dengan warna abu-abu muda (`#999; opacity: 0.7`) agar tidak mendistraksi data medis.
5. **Posisi Tanda Tangan (QR):** 
   Blok tanda tangan elektronik (QR Code) dokter/petugas serta waktu cetak harus diletakkan murni hanya pada halaman yang paling terakhir (`@if($loop->last)`). Gunakan API publik ringan seperti `api.qrserver.com` (contoh: `<img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=TEXT">`) untuk melakukan *generate* Barcode secara *on-the-fly* di sisi *View* tanpa perlu menginstall *library* PHP yang berat.
6. **Desain Cetak Penuh & Anti Halaman Kosong (*True Full-Bleed Print CSS*):** 
   Agar gambar *background* dapat menyentuh ujung kertas secara absolut tanpa terpotong, dan mencegah *browser* merender halaman ke-3 yang kosong akibat "Fractional Spill-over Bug", gunakan aturan ketat berikut:
   - Untuk pratinjau layar, buat ukuran presisi (misal A4: `width: 210mm; min-height: 297mm; padding: 1.5cm; box-sizing: border-box;`).
   - Di dalam `@media print`, pastikan margin kertas fisik di-nol-kan (`@page { margin: 0; }`).
   - Di dalam `@media print`, **susutkan tinggi kertas 1 milimeter** (misal A4: `height: 296mm !important; min-height: 296mm; max-height: 296mm; overflow: hidden;`). Ini sangat krusial agar sisa desimal *scaling* Chrome tidak menyentuh ujung bawah yang akan memicu halaman kosong baru.
   - Tambahkan paksaan `-webkit-print-color-adjust: exact !important;` pada body.
   - Akhiri dengan paksaan pemutus halaman: `.document-page { page-break-after: always !important; }` dan batalkan di elemen terakhir: `.document-page:last-of-type { page-break-after: auto !important; }`.
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

### 8. Penanganan Berkas & Media External (Khanza Integration)

Laralite dirancang untuk berjalan berdampingan dengan sistem legacy (Khanza). Karena penyimpanan file media (seperti Foto USG, Berkas Digital) seringkali berada di server yang berbeda atau dikelola oleh web server Khanza (Apache), ikuti standar berikut:

#### 8.1 Konfigurasi URL External
Jangan melakukan *hardcode* IP atau domain server Khanza di dalam view. Gunakan variabel environment di `.env`:
```env
# .env
KHANZA_USG_URL=http://192.168.100.2/webapps/hasilpemeriksaanusg/
```

#### 8.2 Cara Pemanggilan di View
Gunakan helper `env()` atau `config()` untuk menggabungkan base URL dengan path yang tersimpan di database:
```blade
{{-- BAIK: Dinamis dan mudah dikelola --}}
<img src="{{ env('KHANZA_USG_URL') . $img->photo }}">

{{-- BURUK: Hardcode IP (Sulit dimaintain) --}}
<img src="http://192.168.100.2/webapps/hasilpemeriksaanusg/{{ $img->photo }}">
```

#### 8.3 Sinkronisasi Folder (Opsi Offline)
Jika ingin menjalankan file secara lokal tanpa jaringan ke server Khanza, salin folder berkas dari Khanza ke `storage/app/public` dan jalankan `php artisan storage:link`. Gunakan pengecekan `file_exists` jika diperlukan transisi antar metode.

### 10. Konfirmasi Aksi & Perlindungan Navigasi (SweetAlert2)

Dilarang keras menggunakan fungsi bawaan browser `confirm()` untuk meminta persetujuan pengguna (seperti menghapus data atau keluar dari form yang belum disimpan). Selalu gunakan **SweetAlert2** agar tampilan tetap profesional dan konsisten dengan *branding* proyek.

**Kasus 1: Hapus Data**
Pastikan warna tombol selaras (Merah untuk peringatan).
```javascript
Swal.fire({
    title: 'Hapus Data?',
    text: 'Data ini akan dihapus permanen.',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#dc2626',
    cancelButtonColor: '#6b7280',
    confirmButtonText: 'Ya, Hapus!'
}).then((result) => {
    if (result.isConfirmed) { $wire.delete(id); }
});
```

**Kasus 2: Dirty Form Guard (Cegah Navigasi)**
Gunakan SweetAlert2 pada event `livewire:navigating` untuk mencegah pasien kehilangan data yang belum disimpan (*isDirty*). Jika pengguna memaksa pindah, navigasi dilakukan secara manual menggunakan `window.location.href`.

### 11. Penanganan Specificity pada Flux UI (PENTING!)

Komponen Flux UI seringkali memiliki *style* internal yang sangat spesifik. Hal ini sering menyebabkan utilitas Tailwind standar (seperti `padding`, `margin`, atau `width`) tidak berfungsi karena kalah dalam urutan prioritas CSS (*CSS Specificity*).

**Masalah Umum:**
Menambahkan `class="pl-6"` pada `<flux:table.column>` tidak memberikan efek karena tertimpa gaya bawaan Flux.

**Solusi — Gunakan Modifier Important (!):**
Selalu gunakan tanda seru `!` untuk memaksa *style* kita agar diprioritaskan oleh browser.
```blade
{{-- SALAH: Style mungkin tidak muncul --}}
<flux:table.column class="pl-6">Nama</flux:table.column>

{{-- BENAR: Menggunakan ! untuk memaksa prioritas --}}
<flux:table.column class="!pl-6">Nama</flux:table.column>
```

### 12. Pembuatan Sub-Menu (Tingkat 3) pada Mega Menu Panel

Untuk menjaga kerapian *Mega Menu*, fitur dropdown tingkat 3 (Sub-Menu di dalam Sub-Menu) telah didukung oleh sistem Alpine.js bawaan.

**Cara Penggunaan di Konfigurasi Array Menu:**
Tambahkan properti `children` pada sebuah *item* menu. Komponen UI akan otomatis merendernya sebagai tombol *dropdown* yang melayang saat diklik, bukan *link* biasa.

```javascript
// Contoh pada file index.blade.php (Rawat Inap)
{ label: 'Catatan & Dokumentasi', children: [
    { label: 'Follow Up DBD', url: '#' },
    { label: 'Hasil USG', children: [ // <-- Level 3 Dropdown
        { label: 'Hasil USG Kandungan', url: '#' },
        { label: 'Hasil USG Urologi', url: '#' },
    ]},
]},
```

### 13. Manajemen State pada Tab Dinamis & Include (Livewire Morphdom)

Saat membuat sistem *tab* atau menu dinamis yang melakukan *swap* antar tampilan menggunakan `@include` berdasarkan variabel Livewire (misalnya pola `@if($activeTab === ...)`), **WAJIB** membungkus setiap *include* dengan `<div wire:key="...">` yang unik. 

**Masalah:** Tanpa `wire:key`, algoritma *morphdom* Livewire akan mengira elemen dari tab lama adalah elemen yang sama dengan tab baru. Ini dapat menyebabkan *Morphdom State Leakage*, di mana atribut seperti `wire:ignore` atau state Alpine.js dari tab sebelumnya tertinggal dan merusak tampilan (elemen hilang/tumpang tindih).

**Solusi:**
```blade
{{-- BENAR: Livewire akan menghancurkan DOM lama dan merender ulang DOM baru --}}
@if($activeTab === 'pemeriksaan')
    <div wire:key="tab-pemeriksaan">
        @include('livewire.modul.rawat-inap.perawatan-tindakan.pemeriksaan')
    </div>
@elseif($activeTab === 'penanganan')
    <div wire:key="tab-penanganan">
        @include('livewire.modul.rawat-inap.perawatan-tindakan.penanganan')
    </div>
@endif
```

### 14. Konvensi Identitas Pengguna (mlite_users ↔ pegawai)

> [!IMPORTANT]
> **`mlite_users.username` WAJIB diisi sama persis dengan `pegawai.nik`.**

Sistem menggunakan konvensi ini untuk melakukan *cross-table identity resolution* antara tabel autentikasi (`mlite_users`) dan tabel kepegawaian (`pegawai`). Fitur-fitur yang bergantung pada konvensi ini antara lain:

* **Isi Otomatis Pemeriksaan Rawat Inap**: Secara otomatis mendeteksi petugas yang sedang login dan mengisi kolom NIP/Petugas tanpa pencarian manual.
* **Audit Trail & Logging**: Mencocokkan identitas login dengan data pegawai yang tercatat di rekam medis.

**Cara Kerja:**
```php
// Di Livewire Component
$loggedInUsername = auth()->user()->username; // dari mlite_users
$pegawai = Pegawai::find($loggedInUsername);  // lookup ke tabel pegawai via nik
$this->nip = $pegawai->nik;
$this->currentJabatan = $pegawai->jbtn;
```

**Saat Membuat User Baru:**
Pastikan field `username` diisi dengan NIK pegawai yang bersangkutan (bukan email, bukan nama, bukan ID lain).

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
