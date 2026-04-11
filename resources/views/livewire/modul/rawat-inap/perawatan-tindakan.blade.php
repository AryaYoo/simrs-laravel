<div class="flex flex-col gap-6 pb-8"
     x-data="{
        detailModalOpen: false,
        detail: {},
        showDetailModal(data) {
            this.detail = data;
            this.detailModalOpen = true;
        },
        closeDetailModal() {
            this.detailModalOpen = false;
        },

         menuModalOpen: false,
         searchQuery: '',
         activeSubMenu: null,
         cols: 2,
         init() {
            this.updateCols();
            window.addEventListener('resize', () => this.updateCols());
         },
         updateCols() {
            if (window.innerWidth >= 1024) this.cols = 5;
            else if (window.innerWidth >= 768) this.cols = 4;
            else if (window.innerWidth >= 640) this.cols = 3;
            else this.cols = 2;
         },
         chunk(items, size) {
            const chunks = [];
            for (let i = 0; i < items.length; i += size) {
                chunks.push(items.slice(i, i + size));
            }
            return chunks;
         },
         toggleSubMenu(key) { 
            this.activeSubMenu = (this.activeSubMenu === key) ? null : key; 
         },
         isSubMenuOpen(key) { return this.activeSubMenu === key; },
        menuGroups: [
            {
                label: 'Data Rekam Medis',
                items: [
                    { label: 'Pengkajian Awal', children: [
                        { label: 'Keperawatan Umum', url: '#' },
                        { label: 'Keperawatan Kebidanan & Kandungan', url: '#' },
                        { label: 'Keperawatan Neonatus', url: '#' },
                        { label: 'Keperawatan Bayi/Anak', url: '#' },
                        { label: 'Medis Umum', url: '#' },
                        { label: 'Pengkajian Bayi Baru Lahir', url: '#' },
                        { label: 'Medis Neonatus', url: '#' },
                        { label: 'Medis Kebidanan & Kandungan', url: '#' },
                        { label: 'Medis Psikiatri', url: '#' },
                        { label: 'Medis Hemodialisa', url: '#' },
                        { label: 'Medis Jantung', url: '#' },
                        { label: 'Fisioterapi', url: '#' },
                    ]},
                    { label: 'RM Operasi', children: [
                        { label: 'Pengkajian Pre Induksi', url: '#' },
                        { label: 'Check List Pre Operasi', url: '#' },
                        { label: 'Sign-In Sebelum Anestesi', url: '#' },
                        { label: 'Time-Out Sebelum Insisi', url: '#' },
                        { label: 'Sign-Out Sebelum Menutup Luka', url: '#' },
                        { label: 'Check List Post Operasi', url: '#' },
                        { label: 'Pengkajian Pre Operasi', url: '#' },
                        { label: 'Catatan Anestesi-Sedasi', url: '#' },
                        { label: 'Pengkajian Pre Anestesi', url: '#' },
                        { label: 'Check List Kesiapan Anestesi', url: '#' },
                        { label: 'Skor Aldrette Pasca Anestesi', url: '#' },
                        { label: 'Skor Steward Pasca Anestesi', url: '#' },
                        { label: 'Skor Bromage Pasca Anestesi', url: '#' },
                        { label: 'Catatan Pengkajian Paska Operasi', url: '#' },
                    ]},
                    { label: 'RM HCU, ICU, NICU & PICU', children: [
                        { label: 'Check List Kriteria Masuk HCU', url: '#' },
                        { label: 'Check List Kriteria Keluar HCU', url: '#' },
                        { label: 'Check List Kriteria Masuk ICU', url: '#' },
                        { label: 'Check List Kriteria Keluar ICU', url: '#' },
                        { label: 'Check List Kriteria Masuk NICU', url: '#' },
                        { label: 'Check List Kriteria Keluar NICU', url: '#' },
                        { label: 'Check List Kriteria Masuk PICU', url: '#' },
                        { label: 'Check List Kriteria Keluar PICU', url: '#' },
                    ]},
                    { label: 'Uji Fungsi/Prosedur KFR', url: '#' },
                    { label: 'Risiko Jatuh, Fungsional & Dekubitus', children: [
                        { label: 'Pengkajian Lanjutan Risiko Jatuh Dewasa', url: '#' },
                        { label: 'Pengkajian Lanjutan Risiko Jatuh Anak', url: '#' },
                        { label: 'Pengkajian Lanjutan Risiko Jatuh Lansia', url: '#' },
                        { label: 'Pengkajian Lanjutan Risiko Jatuh Neonatus', url: '#' },
                        { label: 'Pengkajian Lanjutan Risiko Jatuh Geriatri', url: '#' },
                        { label: 'Pengkajian Lanjutan Risiko Jatuh Psikiatri', url: '#' },
                        { label: 'Pengkajian Lanjutan Skrining Fungsional', url: '#' },
                        { label: 'Pengkajian Risiko Dekubitus', url: '#' },
                    ]},
                    { label: 'Pengkajian Lain', children: [
                        { label: 'Pengkajian Tambahan Pasien Geriatri', url: '#' },
                        { label: 'Pengkajian Tambahan Bunuh Diri', url: '#' },
                        { label: 'Pengkajian Tambahan Perilaku Kekerasan', url: '#' },
                        { label: 'Pengkajian Tambahan Melarikan Diri', url: '#' },
                        { label: 'Pengkajian Pasien Terminal', url: '#' },
                        { label: 'Pengkajian Korban Kekerasan', url: '#' },
                        { label: 'Pengkajian Kecemasan Pasien Anak', url: '#' },
                        { label: 'Pengkajian Pasien Penyakit Menular', url: '#' },
                        { label: 'Pengkajian Pasien Imunitas Rendah', url: '#' },
                        { label: 'Pengkajian Derajat Dehidrasi', url: '#' },
                        { label: 'Pengkajian Psikologi', url: '#' },
                        { label: 'Pengkajian Psikologi Klinis', url: '#' },
                        { label: 'Hemodialisa', url: '#' },
                        { label: 'Pengkajian Restrain', url: '#' },
                    ]},
                    { label: 'Pemantauan EWS', children: [
                        { label: 'EWS Anak', url: '#' },
                        { label: 'EWS Dewasa', url: '#' },
                        { label: 'EWS Obstetri', url: '#' },
                        { label: 'EWS Neonatus', url: '#' },
                    ]},
                    { label: 'RM Farmasi', children: [
                        { label: 'Konseling Farmasi', url: '#' },
                        { label: 'Rekonsiliasi Obat', url: '#' },
                    ]},
                    { label: 'Catatan & Dokumentasi', children: [
                        { label: 'Observasi', url: '#' },
                        { label: 'Follow Up DBD', url: '#' },
                        { label: 'Catatan Keperawatan', url: '#' },
                        { label: 'Cek GDS', url: '#' },
                        { label: 'Monitoring Reaksi Transfusi', url: '#' },
                        { label: 'Hasil USG', url: '#' },
                        { label: 'Hasil Pemeriksaan', url: '#' },
                        { label: 'Hasil Endoskopi', url: '#' },
                        { label: 'Dokumentasi Tindakan ESWL', url: '#' },
                        { label: 'Pengkajian Ulang Nyeri', url: '#' },
                        { label: 'Catatan Persalinan', url: '#' },
                        { label: 'Keseimbangan Cairan', url: '#' },
                        { label: 'Catatan Cairan Hemodialisa', url: '#' },
                        { label: 'Checklist Pemberian Fibrinolitik', url: '#' },
                        { label: 'Laporan Tindakan', url: '#' },
                    ]},
                    { label: 'Diagnosa', url: '#' },
                    { label: 'RM Gizi', children: [
                        { label: 'Skrining Nutrisi Pasien Dewasa', url: '#' },
                        { label: 'Skrining Nutrisi Pasien Lansia', url: '#' },
                        { label: 'Skrining Nutrisi Pasien Anak', url: '#' },
                        { label: 'Skrining Gizi Lanjut', url: '#' },
                        { label: 'Asuhan Gizi', url: '#' },
                        { label: 'Monitoring Gizi', url: '#' },
                        { label: 'Catatan ADIME Gizi', url: '#' },
                    ]},
                    { label: 'Transfer Antar Ruang', url: '#' },
                    { label: 'Perencanaan Pemulangan', url: '#' },
                    { label: 'Edukasi (Pelaksanaan Informasi & Edukasi)', url: '#' },
                    { label: 'Resume', url: '{{ route('modul.rawat-inap.sub-rawat-inap.resume', str_replace('/', '-', $no_rawat)) }}' },
                    { label: 'Riwayat Perawatan', url: '{{ route('modul.rawat-inap.sub-rawat-inap.riwayat-pasien', str_replace('/', '-', $no_rawat)) }}', target: '_blank' },
                ]
            },
            {
                label: 'Permintaan',
                items: [
                    { label: 'Jadwal Operasi', url: '#' },
                    { label: 'Pemeriksaan Lab', url: '#' },
                    { label: 'Pemeriksaan Radiologi', url: '#' },
                    { label: 'Informasi Obat', url: '#' },
                    { label: 'Konsultasi Medik', url: '#' },
                ]
            },
            {
                label: 'Tindakan & Pemeriksaan',
                items: [
                    { label: 'Data Tagihan/Tindakan Rawat Jalan', url: '#' },
                    { label: 'Data Tagihan/Tindakan Rawat Inap', url: '#' },
                    { label: 'Periksa Lab PK', url: '#' },
                    { label: 'Periksa Lab PA', url: '#' },
                    { label: 'Periksa Lab MB', url: '#' },
                    { label: 'Periksa Radiologi', url: '#' },
                    { label: 'Tagihan Operasi/VK', url: '#' },
                    { label: 'Diet Pasien', url: '#' },
                    { label: 'Data Operasi', url: '#' },
                ]
            },
            {
                label: 'Obat',
                items: [
                    { label: 'Data Pemberian Obat/BHP', url: '#' },
                    { label: 'Input Resep Pulang', url: '#' },
                    { label: 'Input No. Resep', url: '#' },
                    { label: 'Input Resep Dokter', url: '#' },
                    { label: 'Permintaan Stok Obat Pasien', url: '#' },
                    { label: 'Permintaan Resep Pulang', url: '#' },
                    { label: 'Data Stok Obat Pasien', url: '#' },
                    { label: 'Data Resep Pulang', url: '#' },
                    { label: 'Retur Obat/Barang/Alkes', url: '#' },
                    { label: 'Penjualan Obat/Alkes/Barang', url: '#' },
                ]
            },
            {
                label: 'Deposit & Billing',
                items: [
                    { label: 'Deposit/Titipan Pasien', url: '#' },
                    { label: 'Billing/Pembayaran Pasien', url: '#' },
                ]
            },
            {
                label: 'Laporan & Dokumen',
                items: [
                    { label: 'Laporan & Surat', url: '#' },
                    { label: 'Label & Gelang Pasien', url: '#' },
                ]
            },
            {
                label: 'Ranap & Kamar',
                items: [
                    { label: 'Ranap Gabung Ibu & Bayi', url: '#' },
                    { label: 'Gabungkan Ke Kamar Ibu', url: '#' },
                ]
            },
            {
                label: 'Dokter DPJP',
                items: [
                    { label: 'Input Dokter', url: '#' },
                    { label: 'Tampilkan Dokter', url: '#' },
                    { label: 'Filter Dokter', url: '#' },
                ]
            },
            {
                label: 'Rujukan',
                items: [
                    { label: 'Rujukan Keluar', url: '#' },
                    { label: 'Rujukan Masuk', url: '#' },
                ]
            },
            {
                label: 'Bridging BPJS & Lainnya',
                items: [
                    { label: 'Pencarian Peserta BPJS by No. Kepesertaan', url: '#' },
                    { label: 'Pencarian Peserta BPJS by NIK/No.KTP', url: '#' },
                    { label: 'Bridging SEP VClaim', url: '#' },
                    { label: 'Data SEP BPJS', url: '#' },
                    { label: 'Rencana Kontrol BPJS', url: '#' },
                    { label: 'Perintah Rawat Inap BPJS', url: '#' },
                    { label: 'Suplesi Jasa Raharja BPJS', url: '#' },
                    { label: 'Data Induk Kecelakaan BPJS', url: '#' },
                    { label: 'Belum Terbit SEP BPJS', url: '#' },
                    { label: 'Sudah Terbit SEP BPJS', url: '#' },
                    { label: 'Rujuk Keluar Via Sisrute', url: '#' },
                    { label: 'Pasien Corona Kemenkes', url: '#' },
                    { label: 'Perawatan Pasien Corona INACBG', url: '#' },
                    { label: 'Teridentifikasi TB Kemenkes', url: '#' },
                    { label: 'Data PCare', url: '#' },
                ]
            },
            {
                label: 'Lainnya',
                items: [
                    { label: 'Riwayat Pasien', url: '{{ route('modul.rawat-inap.sub-rawat-inap.riwayat-pasien', str_replace('/', '-', $no_rawat)) }}', target: '_blank' },
                    { label: 'Berkas Digital', url: '#' },
                    { label: 'Surat Kontrol', url: '#' },
                ]
            },
        ],
        get allItems() {
            const all = [];
            for (const g of this.menuGroups) {
                for (const item of g.items) {
                    if (item.children) {
                        all.push({ label: item.label + ' ▸ (lihat sub-menu)', url: null, _parentLabel: g.label });
                        for (const c of item.children) all.push({ ...c, _parentLabel: g.label + ' > ' + item.label });
                    } else {
                        all.push({ ...item, _parentLabel: g.label });
                    }
                }
            }
            return all;
        },
        get filteredGroups() {
            const q = this.searchQuery.toLowerCase().trim();
            if (!q) return this.menuGroups;
            return this.menuGroups.map(g => {
                const filteredItems = g.items.map(item => {
                    if (!item.children) return item.label.toLowerCase().includes(q) ? item : null;
                    const fc = item.children.filter(c => c.label.toLowerCase().includes(q));
                    if (fc.length || item.label.toLowerCase().includes(q)) return { ...item, children: fc.length ? fc : item.children };
                    return null;
                }).filter(Boolean);
                return filteredItems.length ? { ...g, items: filteredItems } : null;
            }).filter(Boolean);
        }
     }">
    {{-- Header / Breadcrumb --}}
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('modul.rawat-inap.index') }}" wire:navigate
               class="flex items-center justify-center w-8 h-8 rounded-lg transition-colors hover:bg-neutral-100 dark:hover:bg-neutral-700">
                <flux:icon name="chevron-left" class="w-5 h-5 text-neutral-500" />
            </a>
            <div>
                <nav class="text-xs text-neutral-400 mb-0.5">
                    <a href="{{ route('modul.index') }}" wire:navigate class="hover:underline">Modul</a>
                    <span class="mx-1">/</span>
                    <a href="{{ route('modul.rawat-inap.index') }}" wire:navigate class="hover:underline">Rawat Inap</a>
                    <span class="mx-1">/</span>
                    <span>Perawatan/Tindakan</span>
                </nav>
                <h1 class="text-xl font-bold text-neutral-800 dark:text-neutral-100">Perawatan & Tindakan</h1>
                <div class="mt-1.5 flex items-center gap-2 text-sm">
                    <span class="text-neutral-500">No. Rawat:</span>
                    <span class="font-bold text-[#4C5C2D] dark:text-[#8CC7C4] font-mono bg-[#4C5C2D]/10 dark:bg-[#4C5C2D]/30 px-2 py-0.5 rounded">{{ $no_rawat }}</span>
                    <span class="text-neutral-300 mx-1">|</span>
                    <span class="text-neutral-500">Pasien:</span>
                    <span class="font-bold text-neutral-800 dark:text-neutral-100 bg-neutral-100 dark:bg-neutral-800 px-2 py-0.5 rounded">{{ $regPeriksa->pasien->nm_pasien ?? '-' }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-neutral-800 rounded-xl border border-neutral-200 dark:border-neutral-700 p-6 shadow-sm">
        {{-- Custom Tabs & Menu Grid --}}
        <div class="inline-flex flex-wrap items-center gap-2 p-1 bg-neutral-100 dark:bg-neutral-900 rounded-xl mb-6">
            
            {{-- Tombol Grid Menu --}}
            <button type="button" @click="menuModalOpen = true" title="Menu Lainnya"
                class="flex items-center justify-center w-9 h-9 rounded-lg transition-all cursor-pointer text-neutral-500 hover:text-neutral-700 hover:bg-neutral-200/50 dark:hover:text-neutral-300 dark:hover:bg-neutral-700/50 focus:outline-none flex-shrink-0">
                <flux:icon name="squares-2x2" class="w-5 h-5" />
            </button>

            {{-- Divider --}}
            <div class="w-px h-6 bg-neutral-200 dark:bg-neutral-700 mx-1 flex-shrink-0"></div>

            @php
                // TABS LAIN DISEMBUNYIKAN (DI-COMMENT) KARENA BELUM TERPAKAI
                // BUKA COMMENT JIKA SEWAKTU-WAKTU DIBUTUHKAN
                $tabs = [
                    // ['id' => 'penanganan_dokter', 'label' => 'Penanganan Dokter', 'icon' => 'user-plus'],
                    // ['id' => 'penanganan_petugas', 'label' => 'Penanganan Petugas', 'icon' => 'users'],
                    ['id' => 'penanganan_dokter_petugas', 'label' => 'Penanganan Dokter & Petugas', 'icon' => 'user-group'],
                    ['id' => 'pemeriksaan', 'label' => 'Pemeriksaan', 'icon' => 'clipboard-document-check'],
                    // ['id' => 'pemeriksaan_obstetri', 'label' => 'Pemeriksaan Obstetri', 'icon' => 'heart'],
                    // ['id' => 'pemeriksaan_ginekologi', 'label' => 'Pemeriksaan Ginekologi', 'icon' => 'lifebuoy'],
                ];
            @endphp

            @foreach($tabs as $tab)
                <button wire:click="$set('activeTab', '{{ $tab['id'] }}')"
                    class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition-all cursor-pointer {{ $activeTab === $tab['id'] ? 'bg-white dark:bg-neutral-800 text-neutral-800 dark:text-neutral-100 shadow-sm' : 'text-neutral-500 hover:text-neutral-700 dark:hover:text-neutral-300 hover:bg-neutral-200/50 dark:hover:bg-neutral-700/50' }}">
                    <flux:icon :name="$tab['icon']" class="w-4 h-4" />
                    {{ $tab['label'] }}
                </button>
            @endforeach
        </div>

        {{-- Tab Panels --}}
        <div class="mt-4">
            @if($activeTab === 'penanganan_dokter')
                @include('livewire.modul.rawat-inap.perawatan-tindakan.penanganan-dokter')
            @elseif($activeTab === 'penanganan_petugas')
                @include('livewire.modul.rawat-inap.perawatan-tindakan.penanganan-petugas')
            @elseif($activeTab === 'penanganan_dokter_petugas')
                @include('livewire.modul.rawat-inap.perawatan-tindakan.penanganan-dokter-dan-petugas')
            @elseif($activeTab === 'pemeriksaan')
                @include('livewire.modul.rawat-inap.perawatan-tindakan.pemeriksaan')
            @elseif($activeTab === 'pemeriksaan_obstetri')
                @include('livewire.modul.rawat-inap.perawatan-tindakan.pemeriksaan-obstetri')
            @elseif($activeTab === 'pemeriksaan_ginekologi')
                @include('livewire.modul.rawat-inap.perawatan-tindakan.pemeriksaan-ginekologi')
            @endif
        </div>
    </div>

    {{-- ===== MENU GRID MODAL (Alpine.js) ===== --}}
    <template x-teleport="body">
        <div
            x-show="menuModalOpen"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-[9999] flex items-center justify-center p-4 sm:p-6"
            style="display: none;">

            {{-- Backdrop --}}
            <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="menuModalOpen = false"></div>

            {{-- Panel --}}
            <div
                x-show="menuModalOpen"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                class="relative w-full max-w-6xl max-h-[90vh] flex flex-col bg-white dark:bg-neutral-900 rounded-2xl shadow-2xl border border-neutral-200 dark:border-neutral-700 overflow-hidden"
                @click.stop>

                {{-- Header (Search & Sort) --}}
                <div class="flex flex-col sm:flex-row items-center gap-4 justify-between px-6 py-4 border-b border-neutral-200 dark:border-[#4C5C2D] bg-[#4C5C2D] flex-shrink-0 shadow-lg">
                    <div class="flex items-center gap-3 w-full sm:w-auto text-white">
                        <div class="p-2.5 rounded-xl bg-white/20">
                            <flux:icon name="squares-2x2" class="w-5 h-5 text-white" />
                        </div>
                        <h2 class="font-bold text-white text-lg">Sub Menu</h2>
                    </div>
                    
                    <div class="flex flex-col sm:flex-row items-center gap-3 w-full sm:w-auto">
                        {{-- Search Input --}}
                        <div class="relative w-full sm:w-64">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <flux:icon name="magnifying-glass" class="w-4 h-4 text-white/60" />
                            </div>
                            <input x-model="searchQuery" type="text" placeholder="Cari menu..." 
                                class="block w-full pl-9 pr-3 py-2 text-sm border border-white/20 rounded-lg bg-white/10 text-white focus:ring-2 focus:ring-white/30 focus:border-white/40 transition-colors placeholder-white/50">
                        </div>

                        <button @click="menuModalOpen = false" class="hidden sm:flex p-2 rounded-lg hover:bg-white/10 text-white/70 hover:text-white transition-colors flex-shrink-0">
                            <flux:icon name="x-mark" class="w-5 h-5" />
                        </button>
                    </div>
                </div>

                {{-- Grouped Menu Body --}}
                <div class="overflow-y-auto flex-1 p-5 bg-white dark:bg-neutral-900 space-y-6">

                    {{-- No results --}}
                    <template x-if="filteredGroups.length === 0">
                        <div class="py-16 flex flex-col items-center justify-center text-center">
                            <flux:icon name="magnifying-glass" class="w-12 h-12 text-neutral-300 dark:text-neutral-700 mb-4" />
                            <h3 class="text-lg font-semibold text-neutral-700 dark:text-neutral-300">Menu tidak ditemukan</h3>
                            <p class="text-sm text-neutral-500 mt-1">Gunakan kata kunci pencarian yang lain.</p>
                        </div>
                    </template>

                    {{-- Render each group --}}
                    <template x-for="group in filteredGroups" :key="group.label">
                        <div>
                            {{-- Group Header --}}
                            <div class="flex items-center gap-2 mb-3">
                                <span class="text-xs font-bold uppercase tracking-widest text-[#4C5C2D] dark:text-[#8CC7C4]" x-text="group.label"></span>
                                <div class="flex-1 h-px bg-[#4C5C2D]/20 dark:bg-[#8CC7C4]/20"></div>
                            </div>
                            <div class="flex flex-col gap-2">
                                <template x-for="(row, ridx) in chunk(group.items, cols)" :key="ridx">
                                    <div class="w-full flex flex-col">
                                        {{-- Row Grid --}}
                                        <div class="grid grid-cols-5 gap-3">
                                            <template x-for="(item, btnIdx) in row" :key="btnIdx">
                                                <div class="h-full flex flex-col">
                                                    {{-- Item WITH children = expandable --}}
                                                    <template x-if="item.children && item.children.length > 0">
                                                        <div class="h-full flex flex-col">
                                                            <button @click="toggleSubMenu(group.label + '_' + item.label)"
                                                                :class="isSubMenuOpen(group.label + '_' + item.label) ? 'bg-[#F1F5E9] text-[#4C5C2D] border-x-2 border-t-2 border-b-0 border-neutral-200 dark:border-neutral-700 rounded-t-2xl z-30' : 'bg-neutral-100 dark:bg-neutral-800 text-neutral-600 dark:text-neutral-400 rounded-xl hover:bg-neutral-200 dark:hover:bg-neutral-700 border border-transparent'"
                                                                class="group w-full h-[72px] flex items-center gap-3 p-3 transition-all duration-300 ease-in-out text-left relative">
                                                                <div :class="isSubMenuOpen(group.label + '_' + item.label) ? 'bg-[#4C5C2D]/10 text-[#4C5C2D]' : 'bg-neutral-200 dark:bg-neutral-700 text-neutral-500'" 
                                                                    class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0 transition-all duration-300">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                                                                </div>
                                                                <span class="text-[11px] font-semibold leading-tight flex-1 line-clamp-2" x-text="item.label"></span>
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 flex-shrink-0 transition-transform duration-200" :class="[isSubMenuOpen(group.label + '_' + item.label) ? 'rotate-90 text-[#4C5C2D]' : 'text-neutral-400']" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
                                                                
                                                                {{-- Connector Bridge to maintain fusion across the whitespace gap --}}
                                                                <div x-show="isSubMenuOpen(group.label + '_' + item.label)" 
                                                                    class="absolute -bottom-4 -left-[2px] -right-[2px] h-4 bg-[#F1F5E9] z-40 border-x-2 border-neutral-200 dark:border-neutral-700 overflow-visible">
                                                                    
                                                                    {{-- Left Concave Corner (Fillet) - Seamlessly welded (Hidden on leftmost item) --}}
                                                                    <svg x-show="btnIdx !== 0" class="absolute bottom-[-2px] -left-[17px] w-[18px] h-[18px] text-neutral-200 dark:text-neutral-700" viewBox="0 0 18 18" fill="none">
                                                                        <path d="M18 18V0C18 9.94112 9.94112 18 0 18H18Z" class="fill-[#F1F5E9] dark:fill-neutral-900"/>
                                                                        <path d="M18 0C18 9.94112 9.94112 18 0 18" stroke="currentColor" stroke-width="2"/>
                                                                        {{-- Overlay to hide the vertical split --}}
                                                                        <rect x="17" y="0" width="2" height="18" class="fill-[#F1F5E9] dark:fill-neutral-900" />
                                                                    </svg>
                                                                    
                                                                    {{-- Right Concave Corner (Fillet) - Seamlessly welded (Hidden on rightmost item) --}}
                                                                    <svg x-show="btnIdx !== row.length - 1" class="absolute bottom-[-2px] -right-[17px] w-[18px] h-[18px] text-neutral-200 dark:text-neutral-700" viewBox="0 0 18 18" fill="none">
                                                                        <path d="M0 18V0C0 9.94112 8.05888 18 18 18H0Z" class="fill-[#F1F5E9] dark:fill-neutral-900"/>
                                                                        <path d="M0 0C0 9.94112 8.05888 18 18 18" stroke="currentColor" stroke-width="2"/>
                                                                        {{-- Overlay to hide the vertical split --}}
                                                                        <rect x="-1" y="0" width="2" height="18" class="fill-[#F1F5E9] dark:fill-neutral-900" />
                                                                    </svg>
                                                                </div>
                                                                {{-- Mask to hide panel top border under the bridge (Always Instant) --}}
                                                                <div x-show="isSubMenuOpen(group.label + '_' + item.label)" 
                                                                    class="absolute -bottom-[18px] left-[0px] right-[0px] h-[4px] bg-[#F1F5E9] z-50"></div>
                                                            </button>
                                                        </div>
                                                    </template>

                                                    {{-- Item WITHOUT children = direct link --}}
                                                    <template x-if="!item.children || item.children.length === 0">
                                                        <div class="h-full flex flex-col">
                                                            <a :href="item.url" :target="item.target || '_self'"
                                                                class="group w-full h-[72px] flex items-center gap-3 p-3 rounded-xl bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-700 text-neutral-600 dark:text-neutral-400 hover:border-[#4C5C2D] hover:bg-[#4C5C2D]/5 transition-all shadow-sm">
                                                                <div class="w-9 h-9 rounded-lg flex items-center justify-center bg-neutral-100 dark:bg-neutral-800 text-neutral-500 group-hover:bg-[#4C5C2D] group-hover:text-white transition-colors flex-shrink-0">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                                                </div>
                                                                <span class="text-[11px] font-semibold leading-tight flex-1 line-clamp-2" x-text="item.label"></span>
                                                            </a>
                                                        </div>
                                                    </template>
                                                </div>
                                            </template>
                                        </div>

                                        {{-- Mega Menu Panel for this row --}}
                                        <template x-if="row.some(it => isSubMenuOpen(group.label + '_' + it.label))">
                                            <div :class="{
                                                    'rounded-tl-none': row[0] && isSubMenuOpen(group.label + '_' + row[0].label),
                                                    'rounded-tr-none': row[row.length-1] && isSubMenuOpen(group.label + '_' + row[row.length-1].label)
                                                }"
                                                class="relative mt-4 mb-4 bg-[#F1F5E9] dark:bg-neutral-800 border-2 border-neutral-200 dark:border-neutral-700 rounded-2xl p-6 z-20 w-full overflow-hidden">
                                                
                                                {{-- Keyed Wrapper for Content (Transition Removed for snappiness) --}}
                                                <div :key="activeSubMenu">
                                                    


                                                    <div :class="`grid grid-cols-${cols} gap-3`"
                                                        :style="`grid-template-columns: repeat(${cols}, minmax(0, 1fr))`">
                                                        <template x-for="(child, idx) in row.find(it => isSubMenuOpen(group.label + '_' + it.label))?.children" :key="child.label">
                                                            <a :href="child.url" :target="child.target || '_self'"
                                                                class="flex items-center gap-3 p-3 h-[64px] rounded-xl border border-neutral-100 dark:border-neutral-700 bg-white dark:bg-neutral-900 hover:border-[#4C5C2D] hover:bg-[#4C5C2D]/5 transition-all group/child">
                                                                <div class="flex-shrink-0 w-7 h-7 rounded-lg bg-neutral-100 dark:bg-neutral-800 flex items-center justify-center text-[10px] font-bold text-neutral-500 group-hover/child:bg-[#4C5C2D] group-hover/child:text-white transition-colors" x-text="idx + 1"></div>
                                                                <span x-text="child.label" class="text-[11px] font-medium text-neutral-600 dark:text-neutral-400 group-hover/child:text-[#4C5C2D] dark:group-hover/child:text-[#8CC7C4] leading-tight line-clamp-2"></span>
                                                            </a>
                                                        </template>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>

                </div>

            </div>
        </div>
    </template>
</div>
