<div class="flex flex-col gap-6 pb-12 max-w-7xl mx-auto w-full">
    {{-- Header & Breadcrumb dengan Tombol Back (SOP #8: history.back()) --}}
    <div class="flex items-center justify-between flex-wrap gap-4 bg-white dark:bg-neutral-800 p-4 rounded-xl border border-neutral-200 dark:border-neutral-700 shadow-sm sticky top-2 z-20">
        <div class="flex items-center gap-3">
            <button type="button" onclick="history.length > 1 ? history.back() : window.location.href = '{{ route('modul.farmasi.data-barang') }}'"
                class="flex items-center justify-center w-10 h-8 rounded-md bg-[#4C5C2D] transition-colors hover:bg-[#3d4b24] shadow-sm"
                title="Kembali">
                <flux:icon name="chevron-left" class="w-5 h-5 text-white" />
            </button>
            <div>
                <nav class="text-xs text-neutral-400 mb-0.5">
                    <a href="{{ route('modul.index') }}" wire:navigate class="hover:underline">Modul</a>
                    <span class="mx-1">/</span>
                    <a href="{{ route('modul.farmasi.index') }}" wire:navigate class="hover:underline">Farmasi</a>
                    <span class="mx-1">/</span>
                    <a href="{{ route('modul.farmasi.data-barang') }}" wire:navigate class="hover:underline">Data Obat, Alkes & BHP</a>
                    <span class="mx-1">/</span>
                    <span>Detail</span>
                </nav>
                <h1 class="text-lg font-bold text-neutral-800 dark:text-neutral-100 flex items-center gap-2">
                    Detail Data Obat, Alkes dan BHP Medis
                </h1>
            </div>
        </div>

        <div class="flex items-center gap-2.5">
            <span class="px-3.5 py-1.5 rounded-full text-xs font-semibold {{ $barang->status === '1' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300' : 'bg-neutral-200 text-neutral-700 dark:bg-neutral-700 dark:text-neutral-300' }}">
                Status: {{ $barang->status === '1' ? 'Aktif' : 'Non-Aktif' }}
            </span>
            <a href="{{ route('modul.farmasi.data-barang.edit', ['kode_brng' => $barang->kode_brng]) }}" wire:navigate
                class="inline-flex items-center gap-1.5 px-4 py-1.5 text-xs font-semibold rounded-lg bg-amber-600 hover:bg-amber-700 text-white shadow transition-colors"
                title="Edit Data Barang">
                <flux:icon name="pencil" class="w-3.5 h-3.5" />
                Edit Barang
            </a>
        </div>
    </div>

    {{-- Vertical Linear Flow (1 -> 2 -> 3 -> 4) --}}
    <div class="flex flex-col gap-6 mt-6">
        
        {{-- SEKSI 1: IDENTITAS & KLASIFIKASI BARANG --}}
        <div class="bg-white dark:bg-neutral-800 rounded-xl border border-neutral-200 dark:border-neutral-700 p-6 shadow-sm flex flex-col gap-5">
            <div class="flex items-center justify-between border-b border-neutral-200 dark:border-neutral-700 pb-3">
                <div class="flex items-center gap-2.5">
                    <span class="w-6 h-6 rounded-full bg-[#4C5C2D] text-white text-xs font-bold flex items-center justify-center">1</span>
                    <h2 class="text-sm font-bold uppercase tracking-wider text-neutral-800 dark:text-neutral-100">
                        Identitas & Klasifikasi Barang
                    </h2>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                {{-- 1. Kode Barang --}}
                <div>
                    <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 mb-1.5">Kode Barang</label>
                    <div class="px-3.5 py-2.5 text-xs font-mono font-bold rounded-lg border border-neutral-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-900/60 text-neutral-900 dark:text-neutral-100">
                        {{ $barang->kode_brng }}
                    </div>
                </div>

                {{-- 2. Nama Barang --}}
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 mb-1.5">Nama Lengkap Barang</label>
                    <div class="px-3.5 py-2.5 text-xs font-bold rounded-lg border border-neutral-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-900/60 text-neutral-900 dark:text-neutral-100">
                        {{ $barang->nama_brng }}
                    </div>
                </div>

                {{-- 3. Industri Farmasi --}}
                <div>
                    <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 mb-1.5">Industri Farmasi (Pabrikan)</label>
                    <div class="flex items-center gap-2">
                        <span class="px-2.5 py-2.5 text-xs font-mono rounded-lg border border-neutral-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-900/60 text-neutral-700 dark:text-neutral-300">
                            {{ $barang->kode_industri ?: '-' }}
                        </span>
                        <div class="flex-1 px-3.5 py-2.5 text-xs font-medium rounded-lg border border-neutral-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-900/60 text-neutral-900 dark:text-neutral-100 truncate">
                            {{ $barang->industri->nama_industri ?? '-' }}
                        </div>
                    </div>
                </div>

                {{-- 4. Kandungan / Letak --}}
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 mb-1.5">Kandungan / Zat Aktif / Letak Rak</label>
                    <div class="px-3.5 py-2.5 text-xs rounded-lg border border-neutral-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-900/60 text-neutral-900 dark:text-neutral-100">
                        {{ $barang->letak_barang ?: '-' }}
                    </div>
                </div>

                {{-- 5. Jenis --}}
                <div>
                    <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 mb-1.5">Jenis Barang</label>
                    <div class="px-3.5 py-2.5 text-xs font-medium rounded-lg border border-neutral-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-900/60 text-neutral-900 dark:text-neutral-100">
                        {{ $barang->jenis->nama ?? '-' }}
                    </div>
                </div>

                {{-- 6. Kategori --}}
                <div>
                    <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 mb-1.5">Kategori Barang</label>
                    <div class="px-3.5 py-2.5 text-xs font-medium rounded-lg border border-neutral-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-900/60 text-neutral-900 dark:text-neutral-100">
                        {{ $barang->kategori->nama ?? '-' }}
                    </div>
                </div>

                {{-- 7. Golongan --}}
                <div>
                    <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 mb-1.5">Golongan Barang</label>
                    <div class="px-3.5 py-2.5 text-xs font-medium rounded-lg border border-neutral-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-900/60 text-neutral-900 dark:text-neutral-100">
                        {{ $barang->golongan->nama ?? '-' }}
                    </div>
                </div>
            </div>
        </div>

        {{-- SEKSI 2: KEMASAN, SATUAN & LOGISTIK STOK --}}
        <div class="bg-white dark:bg-neutral-800 rounded-xl border border-neutral-200 dark:border-neutral-700 p-6 shadow-sm flex flex-col gap-5">
            <div class="flex items-center justify-between border-b border-neutral-200 dark:border-neutral-700 pb-3">
                <div class="flex items-center gap-2.5">
                    <span class="w-6 h-6 rounded-full bg-[#4C5C2D] text-white text-xs font-bold flex items-center justify-center">2</span>
                    <h2 class="text-sm font-bold uppercase tracking-wider text-neutral-800 dark:text-neutral-100">
                        Kemasan, Satuan & Logistik Stok
                    </h2>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                {{-- 8. Satuan Besar --}}
                <div class="lg:col-span-2">
                    <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 mb-1.5">Satuan Besar (Kemasan Luar)</label>
                    <div class="flex items-center gap-2">
                        <span class="px-2.5 py-2.5 text-xs font-mono rounded-lg border border-neutral-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-900/60 text-neutral-700 dark:text-neutral-300">
                            {{ $barang->kode_satbesar ?: '-' }}
                        </span>
                        <div class="flex-1 px-3.5 py-2.5 text-xs font-medium rounded-lg border border-neutral-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-900/60 text-neutral-900 dark:text-neutral-100">
                            {{ $barang->satuanBesar->satuan ?? '-' }}
                        </div>
                    </div>
                </div>

                {{-- 9. Isi Konversi --}}
                <div class="lg:col-span-2">
                    <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 mb-1.5">Isi Konversi</label>
                    <div class="px-3.5 py-2.5 text-xs font-bold text-right rounded-lg border border-neutral-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-900/60 text-neutral-900 dark:text-neutral-100">
                        {{ number_format($barang->isi, 1, '.', '') }}
                    </div>
                </div>

                {{-- 10. Satuan Kecil --}}
                <div class="lg:col-span-2">
                    <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 mb-1.5">Satuan Terkecil (Satuan Pakai)</label>
                    <div class="flex items-center gap-2">
                        <span class="px-2.5 py-2.5 text-xs font-mono rounded-lg border border-neutral-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-900/60 text-neutral-700 dark:text-neutral-300">
                            {{ $barang->kode_sat ?: '-' }}
                        </span>
                        <div class="flex-1 px-3.5 py-2.5 text-xs font-medium rounded-lg border border-neutral-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-900/60 text-neutral-900 dark:text-neutral-100">
                            {{ $barang->satuanKecil->satuan ?? '-' }}
                        </div>
                    </div>
                </div>

                {{-- 11. Kapasitas --}}
                <div class="lg:col-span-2">
                    <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 mb-1.5">Kapasitas / Dosis</label>
                    <div class="px-3.5 py-2.5 text-xs font-bold text-right rounded-lg border border-neutral-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-900/60 text-neutral-900 dark:text-neutral-100">
                        {{ number_format($barang->kapasitas, 1, '.', '') }}
                    </div>
                </div>

                {{-- 12. Stok Minimal --}}
                <div>
                    <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 mb-1.5">Stok Minimal</label>
                    <div class="px-3.5 py-2.5 text-xs font-bold text-right rounded-lg border border-neutral-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-900/60 text-neutral-900 dark:text-neutral-100">
                        {{ number_format($barang->stokminimal, 1, '.', '') }}
                    </div>
                </div>

                {{-- 13. Tanggal Kadaluwarsa --}}
                <div class="lg:col-span-2">
                    <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 mb-1.5">Tanggal Kadaluwarsa</label>
                    <div class="px-3.5 py-2.5 text-xs font-mono font-semibold rounded-lg border border-neutral-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-900/60 text-neutral-900 dark:text-neutral-100">
                        {{ $barang->expire ? date('d-m-Y', strtotime($barang->expire)) : '-' }}
                    </div>
                </div>

                {{-- 14. Status --}}
                <div>
                    <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 mb-1.5">Status Keaktifan</label>
                    <div class="px-3.5 py-2.5 text-xs font-semibold rounded-lg border border-neutral-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-900/60 text-neutral-900 dark:text-neutral-100">
                        {{ $barang->status === '1' ? 'Aktif' : 'Non-Aktif' }}
                    </div>
                </div>
            </div>
        </div>

        {{-- SEKSI 3: HARGA POKOK & TARIF PELAYANAN UMUM --}}
        <div class="bg-white dark:bg-neutral-800 rounded-xl border border-neutral-200 dark:border-neutral-700 p-6 shadow-sm flex flex-col gap-5">
            <div class="flex items-center justify-between border-b border-neutral-200 dark:border-neutral-700 pb-3">
                <div class="flex items-center gap-2.5">
                    <span class="w-6 h-6 rounded-full bg-[#4C5C2D] text-white text-xs font-bold flex items-center justify-center">3</span>
                    <h2 class="text-sm font-bold uppercase tracking-wider text-neutral-800 dark:text-neutral-100">
                        Harga Pokok & Tarif Pelayanan Umum
                    </h2>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                {{-- 15. Harga Dasar --}}
                <div>
                    <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 mb-1.5">Harga Dasar / Cost</label>
                    <div class="px-3.5 py-2.5 text-xs font-bold rounded-lg border border-neutral-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-900/60 text-neutral-900 dark:text-neutral-100">
                        Rp {{ number_format($barang->dasar, 1, '.', '') }}
                    </div>
                </div>

                {{-- 16. Harga Beli --}}
                <div>
                    <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 mb-1.5">Harga Beli / HPP</label>
                    <div class="px-3.5 py-2.5 text-xs font-bold text-red-600 dark:text-red-400 rounded-lg border border-neutral-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-900/60">
                        Rp {{ number_format($barang->h_beli, 1, '.', '') }}
                    </div>
                </div>

                {{-- 17. Tarif Rawat Jalan --}}
                <div>
                    <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 mb-1.5">Tarif Rawat Jalan (Ralan)</label>
                    <div class="px-3.5 py-2.5 text-xs font-bold text-blue-600 dark:text-blue-400 rounded-lg border border-neutral-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-900/60">
                        Rp {{ number_format($barang->ralan, 1, '.', '') }}
                    </div>
                </div>

                {{-- 18. Tarif Rnp Utama / BPJS --}}
                <div>
                    <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 mb-1.5">Tarif Rnp Utama / BPJS</label>
                    <div class="px-3.5 py-2.5 text-xs font-bold text-emerald-600 dark:text-emerald-400 rounded-lg border border-neutral-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-900/60">
                        Rp {{ number_format($barang->utama, 1, '.', '') }}
                    </div>
                </div>

                {{-- 19. Apotek Luar --}}
                <div>
                    <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 mb-1.5">Tarif Apotek Luar</label>
                    <div class="px-3.5 py-2.5 text-xs font-bold rounded-lg border border-neutral-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-900/60 text-neutral-900 dark:text-neutral-100">
                        Rp {{ number_format($barang->beliluar, 1, '.', '') }}
                    </div>
                </div>

                {{-- 20. Jual Bebas --}}
                <div>
                    <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 mb-1.5">Tarif Jual Bebas / OTC</label>
                    <div class="px-3.5 py-2.5 text-xs font-bold rounded-lg border border-neutral-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-900/60 text-neutral-900 dark:text-neutral-100">
                        Rp {{ number_format($barang->jualbebas, 1, '.', '') }}
                    </div>
                </div>

                {{-- 21. Tarif Karyawan --}}
                <div class="lg:col-span-2">
                    <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 mb-1.5">Tarif Karyawan</label>
                    <div class="px-3.5 py-2.5 text-xs font-bold rounded-lg border border-neutral-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-900/60 text-neutral-900 dark:text-neutral-100">
                        Rp {{ number_format($barang->karyawan, 1, '.', '') }}
                    </div>
                </div>
            </div>
        </div>

        {{-- SEKSI 4: MATRIKS TARIF RAWAT INAP BERJENJANG --}}
        <div class="bg-white dark:bg-neutral-800 rounded-xl border border-neutral-200 dark:border-neutral-700 p-6 shadow-sm flex flex-col gap-5">
            <div class="flex items-center justify-between border-b border-neutral-200 dark:border-neutral-700 pb-3">
                <div class="flex items-center gap-2.5">
                    <span class="w-6 h-6 rounded-full bg-[#4C5C2D] text-white text-xs font-bold flex items-center justify-center">4</span>
                    <h2 class="text-sm font-bold uppercase tracking-wider text-neutral-800 dark:text-neutral-100">
                        Matriks Tarif Rawat Inap (Kelas Kamar)
                    </h2>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
                {{-- 22. Kelas 1 --}}
                <div>
                    <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 mb-1.5">Tarif Kelas 1</label>
                    <div class="px-3.5 py-2.5 text-xs font-bold rounded-lg border border-neutral-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-900/60 text-neutral-900 dark:text-neutral-100">
                        Rp {{ number_format($barang->kelas1, 1, '.', '') }}
                    </div>
                </div>

                {{-- 23. Kelas 2 --}}
                <div>
                    <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 mb-1.5">Tarif Kelas 2</label>
                    <div class="px-3.5 py-2.5 text-xs font-bold rounded-lg border border-neutral-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-900/60 text-neutral-900 dark:text-neutral-100">
                        Rp {{ number_format($barang->kelas2, 1, '.', '') }}
                    </div>
                </div>

                {{-- 24. Kelas 3 --}}
                <div>
                    <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 mb-1.5">Tarif Kelas 3</label>
                    <div class="px-3.5 py-2.5 text-xs font-bold rounded-lg border border-neutral-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-900/60 text-neutral-900 dark:text-neutral-100">
                        Rp {{ number_format($barang->kelas3, 1, '.', '') }}
                    </div>
                </div>

                {{-- 25. Kelas VIP --}}
                <div>
                    <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 mb-1.5">Tarif Kelas VIP</label>
                    <div class="px-3.5 py-2.5 text-xs font-bold rounded-lg border border-neutral-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-900/60 text-neutral-900 dark:text-neutral-100">
                        Rp {{ number_format($barang->vip, 1, '.', '') }}
                    </div>
                </div>

                {{-- 26. Kelas VVIP --}}
                <div>
                    <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 mb-1.5">Tarif Kelas VVIP</label>
                    <div class="px-3.5 py-2.5 text-xs font-bold rounded-lg border border-neutral-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-900/60 text-neutral-900 dark:text-neutral-100">
                        Rp {{ number_format($barang->vvip, 1, '.', '') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
