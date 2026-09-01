<div class="flex flex-col gap-6 pb-24">
    {{-- Header & Breadcrumb --}}
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <button type="button" onclick="history.length > 1 ? history.back() : window.location.href = '{{ route('modul.rawat-inap.kelahiran-bayi') }}'" class="flex items-center justify-center w-10 h-8 rounded-md bg-[#4C5C2D] transition-colors hover:bg-[#3d4b24] shadow-sm">
                <flux:icon name="chevron-left" class="w-5 h-5 text-white" />
            </button>
            <div>
                <nav class="text-xs text-neutral-400 mb-0.5">
                    <a href="{{ route('modul.index') }}" wire:navigate class="hover:underline">Modul</a>
                    <span class="mx-1">/</span>
                    <a href="{{ route('modul.rawat-inap.index') }}" wire:navigate class="hover:underline">Rawat Inap</a>
                    <span class="mx-1">/</span>
                    <a href="{{ route('modul.rawat-inap.kelahiran-bayi') }}" wire:navigate class="hover:underline">Kelahiran Bayi</a>
                    <span class="mx-1">/</span>
                    <span class="text-neutral-600 dark:text-neutral-300 font-medium">SOP & Panduan</span>
                </nav>
                <h1 class="text-xl font-bold text-neutral-800 dark:text-neutral-100 flex items-center gap-2">
                    <flux:icon name="book-open" class="w-6 h-6 text-[#4C5C2D]" />
                    SOP & Panduan Fitur Kelahiran Bayi
                </h1>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <flux:button :href="route('modul.rawat-inap.kelahiran-bayi')" icon="arrow-left" variant="ghost" wire:navigate>
                Kembali ke Daftar Bayi
            </flux:button>
            <flux:button :href="route('modul.rawat-inap.kelahiran-bayi.create')" icon="plus" wire:navigate class="!bg-[#4C5C2D] !border-[#4C5C2D] !text-white hover:!bg-[#3E4A25]">
                Tambah Bayi
            </flux:button>
        </div>
    </div>

    {{-- Highlight Banner --}}
    <div class="bg-gradient-to-r from-[#4C5C2D]/15 via-[#4C5C2D]/5 to-transparent border border-[#4C5C2D]/20 rounded-2xl p-6 relative overflow-hidden">
        <div class="relative z-10 max-w-3xl">
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-[#4C5C2D] text-white mb-3">
                <flux:icon name="shield-check" class="w-3.5 h-3.5" />
                Standar Operasional Prosedur (SOP)
            </span>
            <h2 class="text-lg font-bold text-neutral-800 dark:text-neutral-100 mb-1">
                Panduan Operasional Pendaftaran Bayi & Cetak Surat Keterangan Lahir (SKL)
            </h2>
            <p class="text-xs text-neutral-600 dark:text-neutral-400 leading-relaxed">
                Halaman ini berisi petunjuk lengkap bagi Bidan, Perawat Nurse Station, dan Petugas Administrasi dalam menginput data kelahiran bayi, melakukan relasi data ibu kandung secara presisi, serta tata cara penerbitan dan cetak dokumen SKL resmi rumah sakit.
            </p>
        </div>
    </div>

    {{-- Navigation Tabs --}}
    <div class="flex flex-wrap gap-2 border-b border-neutral-200 dark:border-neutral-700 pb-2">
        <button type="button" wire:click="setTab('tentang')"
            class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 {{ $activeTab === 'tentang' ? 'bg-[#4C5C2D] text-white shadow-sm' : 'bg-white dark:bg-neutral-800 text-neutral-600 dark:text-neutral-400 hover:bg-neutral-100 dark:hover:bg-neutral-700 border border-neutral-200 dark:border-neutral-700' }}">
            <flux:icon name="information-circle" class="w-4 h-4" />
            1. Ikhtisar & Fitur
        </button>
        <button type="button" wire:click="setTab('sop_input')"
            class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 {{ $activeTab === 'sop_input' ? 'bg-[#4C5C2D] text-white shadow-sm' : 'bg-white dark:bg-neutral-800 text-neutral-600 dark:text-neutral-400 hover:bg-neutral-100 dark:hover:bg-neutral-700 border border-neutral-200 dark:border-neutral-700' }}">
            <flux:icon name="clipboard-document-check" class="w-4 h-4" />
            2. SOP Input Kelahiran Bayi
        </button>
        <button type="button" wire:click="setTab('sop_skl')"
            class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 {{ $activeTab === 'sop_skl' ? 'bg-[#4C5C2D] text-white shadow-sm' : 'bg-white dark:bg-neutral-800 text-neutral-600 dark:text-neutral-400 hover:bg-neutral-100 dark:hover:bg-neutral-700 border border-neutral-200 dark:border-neutral-700' }}">
            <flux:icon name="printer" class="w-4 h-4" />
            3. SOP Cetak SKL
        </button>
        <button type="button" wire:click="setTab('sop_koreksi')"
            class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 {{ $activeTab === 'sop_koreksi' ? 'bg-[#4C5C2D] text-white shadow-sm' : 'bg-white dark:bg-neutral-800 text-neutral-600 dark:text-neutral-400 hover:bg-neutral-100 dark:hover:bg-neutral-700 border border-neutral-200 dark:border-neutral-700' }}">
            <flux:icon name="arrow-path" class="w-4 h-4" />
            4. Koreksi / Update Data Ibu
        </button>
        <button type="button" wire:click="setTab('faq')"
            class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 {{ $activeTab === 'faq' ? 'bg-[#4C5C2D] text-white shadow-sm' : 'bg-white dark:bg-neutral-800 text-neutral-600 dark:text-neutral-400 hover:bg-neutral-100 dark:hover:bg-neutral-700 border border-neutral-200 dark:border-neutral-700' }}">
            <flux:icon name="question-mark-circle" class="w-4 h-4" />
            5. Tanya Jawab (FAQ)
        </button>
    </div>

    {{-- Content Area --}}
    <div class="space-y-6">

        {{-- TAB 1: IKHTISAR & FITUR --}}
        @if($activeTab === 'tentang')
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="md:col-span-2 space-y-6">
                <div class="bg-white dark:bg-neutral-800 rounded-2xl border border-neutral-200 dark:border-neutral-700 p-6 shadow-sm space-y-4">
                    <h3 class="text-base font-bold text-neutral-800 dark:text-neutral-100 flex items-center gap-2">
                        <flux:icon name="sparkles" class="w-5 h-5 text-[#4C5C2D]" />
                        Mengenai Modul Kelahiran Bayi
                    </h3>
                    <p class="text-xs text-neutral-600 dark:text-neutral-300 leading-relaxed">
                        Modul Kelahiran Bayi adalah modul rekam medis spesifik untuk Ruang Bersalin (VK), Kamar Operasi (OK Kebidanan), dan Ruang Perawatan Bayi (Perinatologi/NICU). Modul ini mengelola pencatatan partus, kondisi klinis bayi baru lahir, penilaian skor APGAR, hingga penerbitan Surat Keterangan Lahir (SKL) resmi.
                    </p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                        <div class="p-4 rounded-xl bg-neutral-50 dark:bg-neutral-900 border border-neutral-100 dark:border-neutral-700 space-y-2">
                            <div class="w-8 h-8 rounded-lg bg-[#4C5C2D]/10 text-[#4C5C2D] flex items-center justify-center font-bold">
                                <flux:icon name="link" class="w-4 h-4" />
                            </div>
                            <h4 class="text-xs font-bold text-neutral-800 dark:text-neutral-200">Relasi Presisi Data Ibu</h4>
                            <p class="text-[11px] text-neutral-500 leading-normal">
                                Menggunakan sistem <strong>Attach Pasien</strong> berbasis Nomor RM Ibu (<code class="font-mono text-[#4C5C2D]">no_rkm_medis_ibu</code>), menjamin data Pekerjaan, NIK, dan Alamat pada SKL selalu akurat.
                            </p>
                        </div>

                        <div class="p-4 rounded-xl bg-neutral-50 dark:bg-neutral-900 border border-neutral-100 dark:border-neutral-700 space-y-2">
                            <div class="w-8 h-8 rounded-lg bg-[#4C5C2D]/10 text-[#4C5C2D] flex items-center justify-center font-bold">
                                <flux:icon name="numbered-list" class="w-4 h-4" />
                            </div>
                            <h4 class="text-xs font-bold text-neutral-800 dark:text-neutral-200">Penomoran SKL Otomatis</h4>
                            <p class="text-[11px] text-neutral-500 leading-normal">
                                Nomor urut SKL tergenerate otomatis dengan format resmi rumah sakit (<code class="font-mono text-[#4C5C2D]">Nomor/KL.RSIAIBI/Bulan/Tahun</code>) dan dapat dikonfigurasi.
                            </p>
                        </div>

                        <div class="p-4 rounded-xl bg-neutral-50 dark:bg-neutral-900 border border-neutral-100 dark:border-neutral-700 space-y-2">
                            <div class="w-8 h-8 rounded-lg bg-[#4C5C2D]/10 text-[#4C5C2D] flex items-center justify-center font-bold">
                                <flux:icon name="heart" class="w-4 h-4" />
                            </div>
                            <h4 class="text-xs font-bold text-neutral-800 dark:text-neutral-200">Kalkulasi APGAR Otomatis</h4>
                            <p class="text-[11px] text-neutral-500 leading-normal">
                                Tabel penilaian APGAR 1 menit, 5 menit, dan 10 menit secara otomatis menghitung total skor untuk evaluasi asfiksia neonatus.
                            </p>
                        </div>

                        <div class="p-4 rounded-xl bg-neutral-50 dark:bg-neutral-900 border border-neutral-100 dark:border-neutral-700 space-y-2">
                            <div class="w-8 h-8 rounded-lg bg-[#4C5C2D]/10 text-[#4C5C2D] flex items-center justify-center font-bold">
                                <flux:icon name="printer" class="w-4 h-4" />
                            </div>
                            <h4 class="text-xs font-bold text-neutral-800 dark:text-neutral-200">Cetak Dokumen Terstandar</h4>
                            <p class="text-[11px] text-neutral-500 leading-normal">
                                Format cetak SKL siap cetak dalam ukuran standar dengan layout resmi, QR Code / tanda tangan, dan kop surat RSIA IBI.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="bg-neutral-50 dark:bg-neutral-900/50 rounded-2xl border border-neutral-200 dark:border-neutral-700 p-5 space-y-4">
                    <h3 class="text-xs font-bold text-neutral-700 dark:text-neutral-300 uppercase tracking-wider">Akses Cepat Fitur</h3>
                    <div class="space-y-2">
                        <flux:button :href="route('modul.rawat-inap.kelahiran-bayi.create')" wire:navigate variant="ghost" class="w-full !justify-start text-xs font-semibold !text-left">
                            <flux:icon name="plus-circle" class="w-4 h-4 text-[#4C5C2D] mr-2" />
                            Input Bayi Baru
                        </flux:button>
                        <flux:button :href="route('modul.rawat-inap.kelahiran-bayi')" wire:navigate variant="ghost" class="w-full !justify-start text-xs font-semibold !text-left">
                            <flux:icon name="table-cells" class="w-4 h-4 text-[#4C5C2D] mr-2" />
                            Lihat Daftar Pasien Bayi
                        </flux:button>
                        <flux:button :href="route('modul.rawat-inap.kelahiran-bayi.settings')" wire:navigate variant="ghost" class="w-full !justify-start text-xs font-semibold !text-left">
                            <flux:icon name="cog-6-tooth" class="w-4 h-4 text-[#4C5C2D] mr-2" />
                            Pengaturan Nomor Terakhir SKL
                        </flux:button>
                    </div>
                </div>

                <div class="bg-amber-50 dark:bg-amber-950/20 rounded-2xl border border-amber-200 dark:border-amber-800/40 p-4 text-xs text-amber-800 dark:text-amber-300 space-y-2">
                    <div class="flex items-center gap-2 font-bold">
                        <flux:icon name="exclamation-triangle" class="w-4 h-4 text-amber-600" />
                        Penting Diperhatikan!
                    </div>
                    <p class="text-[11px] leading-relaxed text-amber-700 dark:text-amber-400">
                        Untuk menjamin keabsahan data Dukcapil dan SKL, pastikan Nama dan Pekerjaan Ibu sudah valid di data pasien sebelum mencetak SKL.
                    </p>
                </div>
            </div>
        </div>
        @endif

        {{-- TAB 2: SOP INPUT KELAHIRAN BAYI --}}
        @if($activeTab === 'sop_input')
        <div class="bg-white dark:bg-neutral-800 rounded-2xl border border-neutral-200 dark:border-neutral-700 p-6 shadow-sm space-y-6">
            <div>
                <h3 class="text-base font-bold text-neutral-800 dark:text-neutral-100 flex items-center gap-2">
                    <flux:icon name="clipboard-document-list" class="w-5 h-5 text-[#4C5C2D]" />
                    SOP Penginputan Data Kelahiran Bayi
                </h3>
                <p class="text-xs text-neutral-500 mt-1">Ikuti langkah-langkah di bawah ini secara runtut untuk menginput data kelahiran bayi baru lahir:</p>
            </div>

            <div class="space-y-4">
                {{-- Step 1 --}}
                <div class="flex gap-4 items-start p-4 rounded-xl bg-neutral-50 dark:bg-neutral-900/60 border border-neutral-100 dark:border-neutral-700">
                    <div class="w-7 h-7 rounded-full bg-[#4C5C2D] text-white flex items-center justify-center font-bold text-xs flex-shrink-0 mt-0.5">
                        1
                    </div>
                    <div class="space-y-1 flex-1">
                        <h4 class="text-xs font-bold text-neutral-800 dark:text-neutral-200">Pastikan Bayi Sudah Terdaftar No. RM</h4>
                        <p class="text-xs text-neutral-600 dark:text-neutral-400 leading-relaxed">
                            Front Office / Petugas Admisi mendaftarkan identitas awal bayi (misal: <em>By Ny. [Nama Ibu]</em>) pada modul pendaftaran pasien untuk mendapatkan <strong>Nomor Rekam Medis Bayi</strong>.
                        </p>
                    </div>
                </div>

                {{-- Step 2 --}}
                <div class="flex gap-4 items-start p-4 rounded-xl bg-neutral-50 dark:bg-neutral-900/60 border border-neutral-100 dark:border-neutral-700">
                    <div class="w-7 h-7 rounded-full bg-[#4C5C2D] text-white flex items-center justify-center font-bold text-xs flex-shrink-0 mt-0.5">
                        2
                    </div>
                    <div class="space-y-1 flex-1">
                        <h4 class="text-xs font-bold text-neutral-800 dark:text-neutral-200">Buka Menu "Tambah Bayi" & Pilih Pasien Bayi</h4>
                        <p class="text-xs text-neutral-600 dark:text-neutral-400 leading-relaxed">
                            Di halaman <strong>Kelahiran Bayi</strong>, klik tombol <strong>Tambah Bayi</strong>. Pada kolom <em>Cari Pasien Bayi</em>, ketik No. RM atau nama bayi, lalu klik nama bayi dari daftar pencarian.
                        </p>
                    </div>
                </div>

                {{-- Step 3 (Crucial) --}}
                <div class="flex gap-4 items-start p-4 rounded-xl bg-[#4C5C2D]/5 dark:bg-[#4C5C2D]/10 border border-[#4C5C2D]/30">
                    <div class="w-7 h-7 rounded-full bg-[#4C5C2D] text-white flex items-center justify-center font-bold text-xs flex-shrink-0 mt-0.5">
                        3
                    </div>
                    <div class="space-y-2 flex-1">
                        <div class="flex items-center gap-2">
                            <h4 class="text-xs font-bold text-[#4C5C2D] uppercase">Wajib: Attach Pasien Ibu Kandung</h4>
                            <span class="px-2 py-0.5 rounded bg-red-100 dark:bg-red-900/40 text-red-700 dark:text-red-300 text-[10px] font-bold">Paling Penting</span>
                        </div>
                        <p class="text-xs text-neutral-700 dark:text-neutral-300 leading-relaxed">
                            Pada kotak <strong>Informasi Ibu</strong>, klik tombol <strong>Attach Pasien</strong>. Modal pencarian full-screen akan terbuka:
                        </p>
                        <ul class="list-disc list-inside text-xs text-neutral-600 dark:text-neutral-400 space-y-1 pl-1">
                            <li>Ketik Nama atau No. RM Ibu pada kolom pencarian (hanya menampilkan pasien berjenis kelamin Perempuan).</li>
                            <li>Periksa pratinjau data: Nama, No. RM, Umur, Pekerjaan, dan Alamat.</li>
                            <li>Klik tombol <strong>Pilih Pasien Ini</strong>. Data ibu akan terkunci di preview card secara read-only.</li>
                        </ul>
                        <div class="p-2.5 rounded-lg bg-white dark:bg-neutral-800 border border-[#4C5C2D]/20 text-[11px] text-[#4C5C2D] dark:text-[#8CC7C4]">
                            💡 <em>Dengan memilih lewat Attach Pasien, sistem otomatis merekam <code class="font-mono font-bold">no_rkm_medis_ibu</code>. Hal ini menjamin saat cetak SKL, pekerjaan dan identitas ibu ditarik langsung dari data induk pasien yang valid tanpa risiko tertukar nama.</em>
                        </div>
                    </div>
                </div>

                {{-- Step 4 --}}
                <div class="flex gap-4 items-start p-4 rounded-xl bg-neutral-50 dark:bg-neutral-900/60 border border-neutral-100 dark:border-neutral-700">
                    <div class="w-7 h-7 rounded-full bg-[#4C5C2D] text-white flex items-center justify-center font-bold text-xs flex-shrink-0 mt-0.5">
                        4
                    </div>
                    <div class="space-y-1 flex-1">
                        <h4 class="text-xs font-bold text-neutral-800 dark:text-neutral-200">Isi Informasi Ayah & Penolong Persalinan</h4>
                        <p class="text-xs text-neutral-600 dark:text-neutral-400 leading-relaxed">
                            Isi Nama dan Umur Ayah. Pada kolom <strong>Penolong Persalinan</strong>, klik <strong>Attach Pegawai</strong> untuk memilih dokter Sp.OG atau Bidan yang menolong persalinan.
                        </p>
                    </div>
                </div>

                {{-- Step 5 --}}
                <div class="flex gap-4 items-start p-4 rounded-xl bg-neutral-50 dark:bg-neutral-900/60 border border-neutral-100 dark:border-neutral-700">
                    <div class="w-7 h-7 rounded-full bg-[#4C5C2D] text-white flex items-center justify-center font-bold text-xs flex-shrink-0 mt-0.5">
                        5
                    </div>
                    <div class="space-y-1 flex-1">
                        <h4 class="text-xs font-bold text-neutral-800 dark:text-neutral-200">Lengkapi Fisik, Waktu, & Penilaian APGAR</h4>
                        <p class="text-xs text-neutral-600 dark:text-neutral-400 leading-relaxed">
                            Lengkapi data antropometri (Panjang Badan, Berat Badan, Lingkar Kepala/Dada/Perut), Tanggal & Jam Lahir, Riwayat Obstetri (G, P, A, UK), Keadaan Ketuban, Resusitasi, serta tabel <strong>Skor APGAR</strong> (1 menit, 5 menit, 10 menit).
                        </p>
                    </div>
                </div>

                {{-- Step 6 --}}
                <div class="flex gap-4 items-start p-4 rounded-xl bg-neutral-50 dark:bg-neutral-900/60 border border-neutral-100 dark:border-neutral-700">
                    <div class="w-7 h-7 rounded-full bg-[#4C5C2D] text-white flex items-center justify-center font-bold text-xs flex-shrink-0 mt-0.5">
                        6
                    </div>
                    <div class="space-y-1 flex-1">
                        <h4 class="text-xs font-bold text-neutral-800 dark:text-neutral-200">Simpan Data</h4>
                        <p class="text-xs text-neutral-600 dark:text-neutral-400 leading-relaxed">
                            Klik <strong>Simpan Data</strong>. Sistem akan menyimpan data kelahiran bayi, menyinkronkan data ke tabel pasien bayi, dan meng-update urutan nomor SKL terakhir.
                        </p>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- TAB 3: SOP CETAK SKL --}}
        @if($activeTab === 'sop_skl')
        <div class="bg-white dark:bg-neutral-800 rounded-2xl border border-neutral-200 dark:border-neutral-700 p-6 shadow-sm space-y-6">
            <div>
                <h3 class="text-base font-bold text-neutral-800 dark:text-neutral-100 flex items-center gap-2">
                    <flux:icon name="printer" class="w-5 h-5 text-[#4C5C2D]" />
                    SOP Pencetakan Surat Keterangan Lahir (SKL)
                </h3>
                <p class="text-xs text-neutral-500 mt-1">Tata cara penerbitan dan pencetakan dokumen fisik SKL untuk keluarga pasien:</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="p-4 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-900/40 space-y-3">
                    <h4 class="text-xs font-bold text-neutral-800 dark:text-neutral-200 flex items-center gap-2">
                        <flux:icon name="document-magnifying-glass" class="w-4 h-4 text-[#4C5C2D]" />
                        1. Verifikasi Sebelum Cetak
                    </h4>
                    <p class="text-xs text-neutral-600 dark:text-neutral-400 leading-relaxed">
                        Sebelum dokumen dicetak, pastikan petugas memeriksa hal-hal berikut:
                    </p>
                    <ul class="list-disc list-inside text-xs text-neutral-600 dark:text-neutral-400 space-y-1 pl-1">
                        <li><strong>Nama Bayi:</strong> Pastikan ejaan nama bayi sudah sesuai (atau gunakan nama khusus cetak SKL jika nama resmi sudah fix).</li>
                        <li><strong>Identitas Orang Tua:</strong> Nama Ayah, Nama Ibu, NIK, Pekerjaan Ibu, dan Alamat sesuai KTP/KK.</li>
                        <li><strong>Waktu & Antropometri:</strong> Hari, Tanggal lahir, Jam lahir, Jenis Kelamin, Berat, dan Panjang Badan.</li>
                        <li><strong>Penolong:</strong> Nama Dokter / Bidan penolong persalinan.</li>
                    </ul>
                </div>

                <div class="p-4 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-900/40 space-y-3">
                    <h4 class="text-xs font-bold text-neutral-800 dark:text-neutral-200 flex items-center gap-2">
                        <flux:icon name="printer" class="w-4 h-4 text-[#4C5C2D]" />
                        2. Pelaksanaan Cetak & Legalisasi
                    </h4>
                    <ul class="list-disc list-inside text-xs text-neutral-600 dark:text-neutral-400 space-y-1 pl-1">
                        <li>Klik ikon <strong>Cetak SKL</strong> pada baris pasien di Daftar Kelahiran Bayi.</li>
                        <li>Gunakan kertas ukuran standar (F4/A4) dan cetak dengan opsi <em>Print</em> browser (Ctrl + P).</li>
                        <li>Mintakan tanda tangan basah dan stempel dokter/bidan penolong persalinan serta cap resmi rumah sakit.</li>
                        <li>Dokumen asli diserahkan kepada orang tua bayi, dan salinan diarsipkan pada berkas Rekam Medis.</li>
                    </ul>
                </div>
            </div>
        </div>
        @endif

        {{-- TAB 4: KOREKSI / UPDATE DATA IBU --}}
        @if($activeTab === 'sop_koreksi')
        <div class="bg-white dark:bg-neutral-800 rounded-2xl border border-neutral-200 dark:border-neutral-700 p-6 shadow-sm space-y-6">
            <div>
                <h3 class="text-base font-bold text-neutral-800 dark:text-neutral-100 flex items-center gap-2">
                    <flux:icon name="arrow-path" class="w-5 h-5 text-[#4C5C2D]" />
                    SOP Koreksi / Perubahan Data Pasien Ibu
                </h3>
                <p class="text-xs text-neutral-500 mt-1">Panduan alur jika terdapat kesalahan data pekerjaan, alamat, atau nama ibu:</p>
            </div>

            <div class="p-4 rounded-xl bg-blue-50 dark:bg-blue-950/20 border border-blue-200 dark:border-blue-800/40 text-xs text-blue-900 dark:text-blue-200 space-y-2">
                <div class="flex items-center gap-2 font-bold text-blue-800 dark:text-blue-300">
                    <flux:icon name="information-circle" class="w-4 h-4 text-blue-600" />
                    Mengapa Pekerjaan Ibu tidak bisa diedit langsung di form kelahiran bayi?
                </div>
                <p class="text-[11px] leading-relaxed text-blue-800 dark:text-blue-300">
                    Untuk mencegah ketidaksinkronan data antar-unit, data ibu (Nama, Pekerjaan, Alamat, Umur) bersumber langsung dari master rekam medis pasien ibu di Front Office. Jika ada perubahan data identitas ibu, ikuti alur 2 langkah di bawah:
                </p>
            </div>

            <div class="space-y-4">
                <div class="flex gap-4 items-start p-4 rounded-xl bg-neutral-50 dark:bg-neutral-900/60 border border-neutral-100 dark:border-neutral-700">
                    <div class="w-7 h-7 rounded-full bg-[#4C5C2D] text-white flex items-center justify-center font-bold text-xs flex-shrink-0">
                        A
                    </div>
                    <div class="space-y-1 flex-1">
                        <h4 class="text-xs font-bold text-neutral-800 dark:text-neutral-200">Langkah 1: Front Office Mengupdate Data Pasien Ibu</h4>
                        <p class="text-xs text-neutral-600 dark:text-neutral-400 leading-relaxed">
                            Petugas FO / Rekam Medis membuka menu <strong>Pasien > Edit Pasien</strong> untuk No. RM ibu terkait, lalu mengubah data pekerjaan atau alamat yang benar, kemudian klik Simpan.
                        </p>
                    </div>
                </div>

                <div class="flex gap-4 items-start p-4 rounded-xl bg-neutral-50 dark:bg-neutral-900/60 border border-neutral-100 dark:border-neutral-700">
                    <div class="w-7 h-7 rounded-full bg-[#4C5C2D] text-white flex items-center justify-center font-bold text-xs flex-shrink-0">
                        B
                    </div>
                    <div class="space-y-1 flex-1">
                        <h4 class="text-xs font-bold text-neutral-800 dark:text-neutral-200">Langkah 2: Nurse Station Melakukan Re-Attach di Edit Bayi</h4>
                        <p class="text-xs text-neutral-600 dark:text-neutral-400 leading-relaxed">
                            Bidan/Perawat membuka menu <strong>Edit Kelahiran Bayi</strong> pada data bayi yang bersangkutan, klik tombol <strong>Ganti</strong> pada kotak Informasi Ibu, pilih kembali pasien ibu tersebut, lalu klik <strong>Simpan Data</strong>.
                        </p>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- TAB 5: FAQ --}}
        @if($activeTab === 'faq')
        <div class="bg-white dark:bg-neutral-800 rounded-2xl border border-neutral-200 dark:border-neutral-700 p-6 shadow-sm space-y-6">
            <div>
                <h3 class="text-base font-bold text-neutral-800 dark:text-neutral-100 flex items-center gap-2">
                    <flux:icon name="question-mark-circle" class="w-5 h-5 text-[#4C5C2D]" />
                    Pertanyaan yang Sering Diajukan (FAQ)
                </h3>
            </div>

            <div class="space-y-4 text-xs">
                <div class="p-4 rounded-xl bg-neutral-50 dark:bg-neutral-900/50 border border-neutral-200 dark:border-neutral-700 space-y-1.5">
                    <h4 class="font-bold text-neutral-800 dark:text-neutral-200">Q: Kenapa saat cetak SKL data lama, pekerjaan ibu tidak sesuai?</h4>
                    <p class="text-neutral-600 dark:text-neutral-400 leading-relaxed">
                        A: Pada data historis masa lalu sebelum sistem Attach Pasien diimplementasikan, kolom <code class="font-mono text-[#4C5C2D]">no_rkm_medis_ibu</code> masih bernilai kosong (NULL). Untuk memperbaikinya, cukup buka <strong>Edit Bayi</strong>, klik <strong>Attach Pasien</strong>, pilih ibu kandungnya, lalu Simpan.
                    </p>
                </div>

                <div class="p-4 rounded-xl bg-neutral-50 dark:bg-neutral-900/50 border border-neutral-200 dark:border-neutral-700 space-y-1.5">
                    <h4 class="font-bold text-neutral-800 dark:text-neutral-200">Q: Bagaimana jika nama bayi di Akta / SKL berbeda dengan nama pendaftaran RM?</h4>
                    <p class="text-neutral-600 dark:text-neutral-400 leading-relaxed">
                        A: Gunakan kolom <strong>"Nama Khusus Cetak SKL (Opsional)"</strong> pada formulir input/edit bayi. Jika kolom ini diisi, cetak SKL otomatis memprioritaskan nama tersebut tanpa mengubah nama rekam medis awal.
                    </p>
                </div>

                <div class="p-4 rounded-xl bg-neutral-50 dark:bg-neutral-900/50 border border-neutral-200 dark:border-neutral-700 space-y-1.5">
                    <h4 class="font-bold text-neutral-800 dark:text-neutral-200">Q: Bagaimana cara mengatur ulang nomor urut SKL saat pergantian tahun?</h4>
                    <p class="text-neutral-600 dark:text-neutral-400 leading-relaxed">
                        A: Buka ikon <strong>Pengaturan SKL (Gerigi)</strong> di pojok kanan atas Daftar Bayi, masukkan angka urutan awal yang dikehendaki (misal: 0 untuk memulai dari nomor 1), lalu klik Simpan Pengaturan.
                    </p>
                </div>
            </div>
        </div>
        @endif

    </div>
</div>
