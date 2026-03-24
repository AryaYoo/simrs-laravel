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
        sortMode: 'default',
        menus: [
            { label: 'Riwayat Pasien', url: '{{ route('modul.rawat-inap.sub-rawat-inap.riwayat-pasien', str_replace('/', '-', $no_rawat)) }}', target: '_blank' },
            { label: 'Input Resep', url: '#' },
            { label: 'Copy Resep', url: '#' },
            { label: 'Permintaan Stok Pasien', url: '#' },
            { label: 'Permintaan Resep Pulang', url: '#' },
            { label: 'Input Obat & BHP', url: '#' },
            { label: 'Data Obat & BHP', url: '#' },
            { label: 'Berkas Digital', url: '#' },
            { label: 'Permintaan Lab', url: '#' },
            { label: 'Permintaan Rad', url: '#' },
            { label: 'Konsultasi Medik', url: '#' },
            { label: 'Jadwal Operasi', url: '#' },
            { label: 'Surat Kontrol', url: '#' },
            { label: 'Rujuk Keluar', url: '#' },
            { label: 'Diagnosa', url: '#' },
            { label: 'Resume Pasien', url: '#' },
            { label: 'Awal Keperawatan Umum', url: '#' },
            { label: 'Awal Keperawatan Kandungan', url: '#' },
            { label: 'Awal Keperawatan Neonatus', url: '#' },
            { label: 'Awal Keperawatan Bayi/Anak', url: '#' },
            { label: 'Awal Fisioterapi', url: '#' },
            { label: 'Awal Medis Umum', url: '#' },
            { label: 'Awal Medis Kandungan', url: '#' },
            { label: 'Awal Medis Neonatus', url: '#' },
            { label: 'Awal Medis Psikiatri', url: '#' },
            { label: 'Awal Medis Hemodialisa', url: '#' },
            { label: 'Awal Medis Jantung', url: '#' },
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
            { label: 'Pengkajian Pasca Operasi', url: '#' },
            { label: 'Lanjutan Risiko Jatuh Dewasa', url: '#' },
            { label: 'Lanjutan Risiko Jatuh Anak', url: '#' },
            { label: 'Lanjutan Risiko Jatuh Lansia', url: '#' },
            { label: 'Lanjutan Risiko Jatuh Neonatus', url: '#' },
            { label: 'Lanjutan Risiko Jatuh Geriatri', url: '#' },
            { label: 'Lanjutan Risiko Jatuh Psikiatri', url: '#' },
            { label: 'Lanjutan Skrining Fungsional', url: '#' },
            { label: 'Risiko Dekubitus', url: '#' },
            { label: 'Pengkajian Derajat Dehidrasi', url: '#' },
            { label: 'Hasil USG Kandungan', url: '#' },
            { label: 'Hasil USG Urologi', url: '#' },
            { label: 'Hasil USG Neonatus', url: '#' },
            { label: 'Hasil USG Gynecologi', url: '#' },
            { label: 'Hasil Pemeriksaan EKG', url: '#' },
            { label: 'Hasil Pemeriksaan ECHO', url: '#' },
            { label: 'Hasil ECHO Pediatrik', url: '#' },
            { label: 'Hasil Pemeriksaan Slit Lamp', url: '#' },
            { label: 'Hasil Pemeriksaan OCT', url: '#' },
            { label: 'Hasil Pemeriksaan Treadmill', url: '#' },
            { label: 'Hasil Endoskopi Faring/Laring', url: '#' },
            { label: 'Hasil Endoskopi Hidung', url: '#' },
            { label: 'Hasil Endoskopi Telinga', url: '#' },
            { label: 'Dokumentasi Tindakan ESWL', url: '#' },
            { label: 'Laporan Tindakan', url: '#' },
            { label: 'Observasi Hemodialisa', url: '#' },
            { label: 'Cairan Hemodialisa', url: '#' },
            { label: 'Catatan Persalinan', url: '#' },
            { label: 'Observasi Kebidanan', url: '#' },
            { label: 'Observasi Post Partum', url: '#' },
            { label: 'Observasi Induksi Persalinan', url: '#' },
            { label: 'Observasi Bayi', url: '#' },
            { label: 'Pengkajian Bayi Baru Lahir', url: '#' },
            { label: 'Observasi Ranap', url: '#' },
            { label: 'Observasi CHBP', url: '#' },
            { label: 'Observasi Ventilator', url: '#' },
            { label: 'Keseimbangan Cairan', url: '#' },
            { label: 'Pemantauan PEWS Anak', url: '#' },
            { label: 'Pemantauan EWS Dewasa', url: '#' },
            { label: 'Pemantauan MEOWS Obstetri', url: '#' },
            { label: 'Pemantauan EWS Neonatus', url: '#' },
            { label: 'Psikologi & Khusus', url: '#' },
            { label: 'Pengkajian Psikologi', url: '#' },
            { label: 'Pengkajian Psikologi Klinis', url: '#' },
            { label: 'Pengkajian Restrain', url: '#' },
            { label: 'Observasi Restrain Nonfarma', url: '#' },
            { label: 'Pengkajian Pasien Terminal', url: '#' },
            { label: 'Pengkajian Korban Kekerasan', url: '#' },
            { label: 'Pengkajian Kecemasan Anak', url: '#' },
            { label: 'Pasien Penyakit Menular', url: '#' },
            { label: 'Pasien Imunitas Rendah', url: '#' },
            { label: 'Perencanaan Pemulangan', url: '#' },
            { label: 'Catatan Pasien', url: '#' },
            { label: 'Check List Pemberian Fibrinoli', url: '#' },
            { label: 'Follow Up DBD', url: '#' },
            { label: 'Catatan Keperawatan', url: '#' },
            { label: 'Catatan Cek GDS', url: '#' },
            { label: 'Pengkajian Ulang Nyeri', url: '#' },
            { label: 'Check List Masuk/Keluar HCU', url: '#' },
            { label: 'Check List Masuk/Keluar ICU', url: '#' },
            { label: 'Transfer Antar Ruang', url: '#' },
            { label: 'Pelaksanaan Edukasi', url: '#' },
            { label: 'Tambahan Pasien Geriatri', url: '#' },
            { label: 'Tambahan Bunuh Diri', url: '#' },
            { label: 'Tambahan Perilaku Kekerasan', url: '#' },
            { label: 'Tambahan Melarikan Diri', url: '#' }
        ],
        get filteredMenus() {
            let result = this.menus;
            if (this.searchQuery) {
                const lowerQ = this.searchQuery.toLowerCase();
                result = result.filter(m => m.label.toLowerCase().includes(lowerQ));
            }
            if (this.sortMode === 'abjad') {
                return [...result].sort((a, b) => a.label.localeCompare(b.label));
            }
            return result;
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
                <div class="flex flex-col sm:flex-row items-center gap-4 justify-between px-6 py-4 border-b border-neutral-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800/50 flex-shrink-0">
                    <div class="flex items-center gap-3 w-full sm:w-auto">
                        <div class="p-2.5 rounded-xl bg-[#4C5C2D]/10 dark:bg-[#4C5C2D]/30">
                            <flux:icon name="squares-2x2" class="w-5 h-5 text-[#4C5C2D] dark:text-[#8CC7C4]" />
                        </div>
                        <h2 class="font-bold text-neutral-800 dark:text-neutral-100 text-lg">Menu & Modul Lainnya</h2>
                    </div>
                    
                    <div class="flex flex-col sm:flex-row items-center gap-3 w-full sm:w-auto">
                        {{-- Search Input --}}
                        <div class="relative w-full sm:w-64">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <flux:icon name="magnifying-glass" class="w-4 h-4 text-neutral-400" />
                            </div>
                            <input x-model="searchQuery" type="text" placeholder="Cari menu..." 
                                class="block w-full pl-9 pr-3 py-2 text-sm border border-neutral-300 dark:border-neutral-600 rounded-lg bg-white dark:bg-neutral-800 text-neutral-900 dark:text-neutral-100 focus:ring-2 focus:ring-[#4C5C2D] focus:border-[#4C5C2D] transition-colors placeholder-neutral-400">
                        </div>

                        {{-- Sort Toggle --}}
                        <div class="flex items-center bg-neutral-200/80 dark:bg-neutral-700/80 p-1 rounded-lg flex-shrink-0 w-full sm:w-auto justify-center">
                            <button @click="sortMode = 'default'" 
                                :class="sortMode === 'default' ? 'bg-white dark:bg-neutral-800 text-neutral-800 dark:text-neutral-100 shadow-sm' : 'text-neutral-500 dark:text-neutral-400 hover:text-neutral-700'"
                                class="px-3 py-1.5 text-xs font-semibold rounded-md transition-all">Default</button>
                            <button @click="sortMode = 'abjad'" 
                                :class="sortMode === 'abjad' ? 'bg-white dark:bg-neutral-800 text-neutral-800 dark:text-neutral-100 shadow-sm' : 'text-neutral-500 dark:text-neutral-400 hover:text-neutral-700'"
                                class="px-3 py-1.5 text-xs font-semibold rounded-md transition-all">A-Z</button>
                        </div>
                        
                        <button @click="menuModalOpen = false" class="hidden sm:flex p-2 rounded-lg hover:bg-neutral-200 dark:hover:bg-neutral-700 text-neutral-400 hover:text-neutral-600 dark:hover:text-neutral-300 transition-colors flex-shrink-0">
                            <flux:icon name="x-mark" class="w-5 h-5" />
                        </button>
                    </div>
                </div>

                {{-- Grid Menu Body --}}
                <div class="overflow-y-auto flex-1 p-6 bg-white dark:bg-neutral-900">
                    <template x-if="filteredMenus.length === 0">
                        <div class="py-16 flex flex-col items-center justify-center text-center">
                            <flux:icon name="magnifying-glass" class="w-12 h-12 text-neutral-300 dark:text-neutral-700 mb-4" />
                            <h3 class="text-lg font-semibold text-neutral-700 dark:text-neutral-300">Menu tidak ditemukan</h3>
                            <p class="text-sm text-neutral-500 mt-1">Gunakan kata kunci pencarian yang lain.</p>
                        </div>
                    </template>

                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-3" x-show="filteredMenus.length > 0">
                        <template x-for="item in filteredMenus" :key="item.label">
                            <a :href="item.url" :target="item.target || '_self'" class="group flex items-center gap-3 p-3 rounded-xl border border-neutral-200 dark:border-neutral-700/60 bg-white dark:bg-neutral-800 hover:border-[#6A7E3F] dark:hover:border-[#6A7E3F] hover:bg-[#6A7E3F]/5 dark:hover:bg-[#6A7E3F]/10 transition-all cursor-pointer shadow-sm">
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center bg-neutral-100 dark:bg-neutral-700 text-neutral-500 dark:text-neutral-400 group-hover:bg-[#6A7E3F]/10 dark:group-hover:bg-[#6A7E3F]/30 group-hover:text-[#4C5C2D] dark:group-hover:text-[#8CC7C4] transition-colors flex-shrink-0">
                                    <flux:icon name="document-text" class="w-4 h-4" />
                                </div>
                                <span class="text-sm font-medium text-neutral-700 dark:text-neutral-300 group-hover:text-[#4C5C2D] dark:group-hover:text-[#8CC7C4] leading-tight" x-text="item.label"></span>
                            </a>
                        </template>
                    </div>
                </div>

            </div>
        </div>
    </template>
</div>
