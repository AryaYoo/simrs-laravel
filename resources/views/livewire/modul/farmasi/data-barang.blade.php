<div class="flex flex-col gap-6 pb-8"
x-data="{
    init() {
        @if(session()->has('swal:toast'))
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: '{{ session('swal:toast.icon', 'success') }}',
                title: '{{ addslashes(session('swal:toast.title')) }}',
                showConfirmButton: false,
                timer: 3500,
                timerProgressBar: true
            });
        @endif
    }
}"
x-on:swal:toast.window="
    Swal.fire({
        toast: true,
        position: 'top-end',
        icon: $event.detail[0].icon || 'success',
        title: $event.detail[0].title,
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true
    });
">
    {{-- Header / Breadcrumb --}}
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center gap-3">
            <button type="button" onclick="history.length > 1 ? history.back() : window.location.href = '{{ route('modul.farmasi.index') }}'"
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
                    <span>Data Obat, Alkes & BHP</span>
                </nav>
                <h1 class="text-xl font-bold text-neutral-800 dark:text-neutral-100">Data Obat, Alkes & BHP</h1>
            </div>
        </div>

        {{-- Tombol Tambah Barang --}}
        <a href="{{ route('modul.farmasi.data-barang.create') }}" wire:navigate
            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white shadow transition-colors"
            style="background-color: #4C5C2D;"
            title="Tambah data obat / barang baru">
            <flux:icon name="plus" class="w-4 h-4" />
            Tambah Barang
        </a>
    </div>

    {{-- Filter & Search Bar --}}
    <div class="bg-white dark:bg-neutral-800 rounded-xl border border-neutral-200 dark:border-neutral-700 p-4 flex flex-col gap-3">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3">
            {{-- Search --}}
            <div class="relative lg:col-span-3" wire:key="filter-search-col">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <flux:icon name="magnifying-glass" class="w-4 h-4 text-neutral-400" />
                </div>
                <input type="text" wire:model.live.debounce.300ms="search" id="input_search_barang"
                    class="block w-full pl-9 pr-3 py-2 text-sm rounded-lg border border-neutral-200 dark:border-neutral-700
                           bg-white dark:bg-neutral-800 text-neutral-900 dark:text-neutral-100
                           placeholder-neutral-400 focus:outline-none focus:ring-2 focus:ring-[#4C5C2D]/30 focus:border-[#4C5C2D] transition-colors"
                    placeholder="Cari Nama, Kode, Letak..." />
            </div>

            {{-- Filter Jenis --}}
            <div class="lg:col-span-2" wire:key="filter-jenis-col">
                <select wire:model.live="kdjns" id="select_kdjns"
                    class="block w-full py-2 px-3 text-sm rounded-lg border border-neutral-200 dark:border-neutral-700
                           bg-white dark:bg-neutral-800 text-neutral-900 dark:text-neutral-100
                           focus:outline-none focus:ring-2 focus:ring-[#4C5C2D]/30 focus:border-[#4C5C2D] transition-colors">
                    <option value="">Semua Jenis</option>
                    @foreach($listJenis as $jns)
                        <option value="{{ $jns->kdjns }}">{{ $jns->nama }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Filter Kategori --}}
            <div class="lg:col-span-2" wire:key="filter-kategori-col">
                <select wire:model.live="kode_kategori" id="select_kode_kategori"
                    class="block w-full py-2 px-3 text-sm rounded-lg border border-neutral-200 dark:border-neutral-700
                           bg-white dark:bg-neutral-800 text-neutral-900 dark:text-neutral-100
                           focus:outline-none focus:ring-2 focus:ring-[#4C5C2D]/30 focus:border-[#4C5C2D] transition-colors">
                    <option value="">Semua Kategori</option>
                    @foreach($listKategori as $kat)
                        <option value="{{ $kat->kode }}">{{ $kat->nama }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Filter Golongan --}}
            <div class="lg:col-span-1" wire:key="filter-golongan-col">
                <select wire:model.live="kode_golongan" id="select_kode_golongan"
                    class="block w-full py-2 px-2 text-sm rounded-lg border border-neutral-200 dark:border-neutral-700
                           bg-white dark:bg-neutral-800 text-neutral-900 dark:text-neutral-100
                           focus:outline-none focus:ring-2 focus:ring-[#4C5C2D]/30 focus:border-[#4C5C2D] transition-colors">
                    <option value="">Golongan</option>
                    @foreach($listGolongan as $gol)
                        <option value="{{ $gol->kode }}">{{ $gol->nama }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Status --}}
            <div class="lg:col-span-1" wire:key="filter-status-col">
                <select wire:model.live="status" id="select_status"
                    class="block w-full py-2 px-2 text-sm rounded-lg border border-neutral-200 dark:border-neutral-700
                           bg-white dark:bg-neutral-800 text-neutral-900 dark:text-neutral-100
                           focus:outline-none focus:ring-2 focus:ring-[#4C5C2D]/30 focus:border-[#4C5C2D] transition-colors">
                    <option value="">Status</option>
                    <option value="1">Aktif</option>
                    <option value="0">Non-Aktif</option>
                </select>
            </div>

            {{-- Filter Stok (Kritis, Habis, Aman) --}}
            <div class="lg:col-span-2" wire:key="filter-stok-col">
                <select wire:model.live="stok_filter" id="select_stok_filter" wire:key="select-stok-filter-elem"
                    class="block w-full py-2 px-2.5 text-sm rounded-lg border {{ $stok_filter === 'kritis' ? 'border-red-500 font-bold text-red-600 dark:text-red-400 ring-2 ring-red-500/20' : 'border-neutral-200 dark:border-neutral-700 text-neutral-900 dark:text-neutral-100' }}
                           bg-white dark:bg-neutral-800 focus:outline-none focus:ring-2 focus:ring-[#4C5C2D]/30 focus:border-[#4C5C2D] transition-colors">
                    <option value="">Semua Stok</option>
                    <option value="kritis">⚠️ Stok Kritis (≤ Min)</option>
                    <option value="habis">⛔ Stok Habis (0)</option>
                    <option value="aman">✅ Stok Aman (> Min)</option>
                </select>
            </div>

            {{-- Reset --}}
            <div class="lg:col-span-1" wire:key="filter-reset-col">
                <button type="button" wire:click="resetFilter"
                    class="inline-flex items-center justify-center gap-1.5 w-full py-2 px-3 text-sm font-medium rounded-lg border border-neutral-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-900/50 hover:bg-neutral-100 dark:hover:bg-neutral-700/50 text-neutral-700 dark:text-neutral-200 transition-colors shadow-sm"
                    title="Reset Filter">
                    <flux:icon name="arrow-path" class="w-4 h-4 text-neutral-500" />
                </button>
            </div>
        </div>
    </div>

    {{-- Tabel Data Barang --}}
    <div class="bg-white dark:bg-neutral-800 rounded-xl border border-neutral-200 dark:border-neutral-700 shadow-sm overflow-hidden flex-1 flex flex-col">
        <div class="overflow-x-auto flex-1">
            <table class="w-full text-left text-neutral-600 dark:text-neutral-300" style="font-size: 0.72rem;">
                <thead class="bg-neutral-50 dark:bg-neutral-900/50 text-neutral-700 dark:text-neutral-200 border-b border-neutral-200 dark:border-neutral-700 sticky top-0 z-10">
                    <tr>
                        <th class="px-2 py-2 font-bold whitespace-nowrap text-center w-[95px]">Action</th>
                        <th class="px-3 py-2 font-semibold min-w-[200px]">Nama Barang & Kode</th>
                        <th class="px-3 py-2 font-semibold min-w-[140px]">Kategori & Golongan</th>
                        <th class="px-3 py-2 font-semibold min-w-[130px]">Industri Farmasi</th>
                        <th class="px-3 py-2 font-semibold min-w-[130px]">Satuan Besar</th>
                        <th class="px-3 py-2 font-semibold min-w-[130px]">Satuan Kecil</th>
                        <th class="px-3 py-2 font-semibold whitespace-nowrap text-center min-w-[95px]">Stok Total</th>
                        <th class="px-3 py-2 font-semibold whitespace-nowrap text-right min-w-[70px]">Kapasitas</th>
                        <th class="px-3 py-2 font-semibold whitespace-nowrap min-w-[95px]">Kadaluarsa</th>
                        <th class="px-3 py-2 font-semibold min-w-[130px]">Kandungan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                    @php
                        $newlyCreated = session('newly_created_barang', []);
                    @endphp
                    @forelse($barangList as $b)
                        @php
                            $isNew = in_array($b->kode_brng, $newlyCreated);
                            $totalStok = (float)($b->total_stok ?? 0);
                            $stokMin = (float)($b->stokminimal ?? 0);
                            $isKritis = ($stokMin > 0 && $totalStok <= $stokMin);
                            $isHabis = ($totalStok <= 0);
                        @endphp
                        <tr wire:key="brng-{{ $b->kode_brng }}" class="hover:bg-neutral-50 dark:hover:bg-neutral-800/50 transition-colors {{ $isNew ? 'bg-emerald-50/40 dark:bg-emerald-950/20' : ($isKritis ? 'bg-red-50/20 dark:bg-red-950/10' : '') }}">
                            {{-- Action: Detail, Edit, Hapus --}}
                            <td class="px-2 py-1.5 text-center align-middle whitespace-nowrap">
                                <div class="flex items-center justify-center gap-1">
                                    {{-- Detail --}}
                                    <a href="{{ route('modul.farmasi.data-barang.detail', ['kode_brng' => $b->kode_brng]) }}" wire:navigate
                                        class="text-blue-600 hover:text-blue-800 dark:text-blue-400 p-1 rounded hover:bg-blue-50 dark:hover:bg-blue-500/10 transition-colors inline-flex items-center justify-center"
                                        title="Lihat Detail Barang">
                                        <flux:icon name="eye" class="w-3.5 h-3.5" />
                                    </a>

                                    {{-- Edit --}}
                                    <a href="{{ route('modul.farmasi.data-barang.edit', ['kode_brng' => $b->kode_brng]) }}" wire:navigate
                                        class="text-amber-600 hover:text-amber-800 dark:text-amber-400 p-1 rounded hover:bg-amber-50 dark:hover:bg-amber-500/10 transition-colors inline-flex items-center justify-center"
                                        title="Edit Data Barang">
                                        <flux:icon name="pencil" class="w-3.5 h-3.5" />
                                    </a>

                                    {{-- Hapus --}}
                                    <button type="button"
                                        @click="
                                            Swal.fire({
                                                title: 'Hapus Data Barang?',
                                                text: 'Barang {{ addslashes($b->nama_brng) }} ({{ $b->kode_brng }}) akan dihapus.',
                                                icon: 'warning',
                                                showCancelButton: true,
                                                confirmButtonColor: '#dc2626',
                                                cancelButtonColor: '#6b7280',
                                                confirmButtonText: 'Ya, Hapus!',
                                                cancelButtonText: 'Batal'
                                            }).then((result) => {
                                                if (result.isConfirmed) {
                                                    $wire.deleteBarang('{{ $b->kode_brng }}');
                                                }
                                            });
                                        "
                                        class="text-red-500 hover:text-red-700 dark:hover:text-red-400 p-1 rounded hover:bg-red-50 dark:hover:bg-red-500/10 transition-colors"
                                        title="Hapus Barang">
                                        <flux:icon name="trash" class="w-3.5 h-3.5" />
                                    </button>
                                </div>
                            </td>

                            {{-- Nama Barang & Kode Barang --}}
                            <td class="px-3 py-1.5 align-middle">
                                <div class="font-bold text-neutral-800 dark:text-neutral-100 flex items-center gap-1.5 flex-wrap">
                                    <span>{{ $b->nama_brng }}</span>
                                    @if($isNew)
                                        <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[9px] font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-900/60 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-700 animate-pulse" title="Barang Baru Ditambahkan (Klik Detail untuk membaca)">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-600 dark:bg-emerald-400"></span>
                                            Baru
                                        </span>
                                    @endif
                                    @if($isKritis)
                                        <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[9px] font-bold bg-rose-100 text-rose-700 dark:bg-rose-950/70 dark:text-rose-300 border border-rose-300 dark:border-rose-800" title="Stok Kritis! Stok saat ini ({{ number_format($totalStok, 0, ',', '.') }}) mencapai batas minimal ({{ number_format($stokMin, 0, ',', '.') }})">
                                            <flux:icon name="exclamation-triangle" class="w-2.5 h-2.5 text-rose-600 dark:text-rose-400" />
                                            Kritis
                                        </span>
                                    @endif
                                    @if($b->status === '0')
                                        <span class="text-[9px] font-normal px-1 py-0.2 rounded bg-neutral-100 dark:bg-neutral-700 text-neutral-500">Non-Aktif</span>
                                    @endif
                                </div>
                                <div class="text-[10px] text-neutral-400 dark:text-neutral-500 font-mono">
                                    {{ $b->kode_brng }}
                                </div>
                            </td>

                            {{-- Kategori & Golongan --}}
                            <td class="px-3 py-1.5 align-middle">
                                <div class="font-medium text-neutral-800 dark:text-neutral-200">
                                    {{ $b->kategori->nama ?? '-' }}
                                </div>
                                <div class="text-[10px] text-neutral-400 dark:text-neutral-500">
                                    {{ $b->golongan->nama ?? '-' }}
                                </div>
                            </td>

                            {{-- Industri Farmasi --}}
                            <td class="px-3 py-1.5 align-middle">
                                <div class="font-medium text-neutral-800 dark:text-neutral-200">
                                    {{ $b->industri->nama_industri ?? '-' }}
                                </div>
                                <div class="text-[10px] text-neutral-400 dark:text-neutral-500 font-mono">
                                    {{ $b->kode_industri ?: '-' }}
                                </div>
                            </td>

                            {{-- Nama Satuan Besar & Kd. Sat Besar --}}
                            <td class="px-3 py-1.5 align-middle font-medium text-neutral-800 dark:text-neutral-200">
                                <div>{{ $b->satuanBesar->satuan ?? '-' }}</div>
                                <div class="text-[10px] font-mono text-neutral-400">{{ $b->kode_satbesar ?: '-' }}</div>
                            </td>

                            {{-- Nama Sat Kecil & Kd. Sat Kecil --}}
                            <td class="px-3 py-1.5 align-middle font-medium text-neutral-800 dark:text-neutral-200">
                                <div>{{ $b->satuanKecil->satuan ?? '-' }}</div>
                                <div class="text-[10px] font-mono text-neutral-400">{{ $b->kode_sat ?: '-' }}</div>
                            </td>

                            {{-- Stok Total & Stok Minimal --}}
                            <td class="px-3 py-1.5 align-middle text-center whitespace-nowrap">
                                @if($isKritis)
                                    <div class="inline-flex flex-col items-center">
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-rose-100 text-rose-700 dark:bg-rose-950/70 dark:text-rose-300 border border-rose-300 dark:border-rose-800 shadow-xs" title="Stok Kritis! Stok: {{ number_format($totalStok, 0, ',', '.') }}, Min: {{ number_format($stokMin, 0, ',', '.') }}">
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-600 animate-pulse"></span>
                                            {{ number_format($totalStok, 0, ',', '.') }}
                                        </span>
                                        <span class="text-[9px] text-rose-600 dark:text-rose-400 font-semibold mt-0.5">Min: {{ number_format($stokMin, 0, ',', '.') }}</span>
                                    </div>
                                @elseif($isHabis)
                                    <div class="inline-flex flex-col items-center">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-neutral-100 text-neutral-500 dark:bg-neutral-800 dark:text-neutral-400 border border-neutral-200 dark:border-neutral-700 font-mono">
                                            0
                                        </span>
                                        @if($stokMin > 0)
                                            <span class="text-[9px] text-neutral-400 mt-0.5">Min: {{ number_format($stokMin, 0, ',', '.') }}</span>
                                        @endif
                                    </div>
                                @else
                                    <div class="inline-flex flex-col items-center">
                                        <span class="font-bold text-neutral-800 dark:text-neutral-200 font-mono text-[11px]">
                                            {{ number_format($totalStok, 0, ',', '.') }}
                                        </span>
                                        @if($stokMin > 0)
                                            <span class="text-[9px] text-neutral-400 mt-0.5">Min: {{ number_format($stokMin, 0, ',', '.') }}</span>
                                        @endif
                                    </div>
                                @endif
                            </td>

                            {{-- Kapasitas --}}
                            <td class="px-3 py-1.5 align-middle whitespace-nowrap text-right font-semibold text-neutral-800 dark:text-neutral-200">
                                {{ number_format($b->kapasitas, 0, ',', '.') }}
                            </td>

                            {{-- Kadaluarsa --}}
                            <td class="px-3 py-1.5 align-middle whitespace-nowrap font-mono text-[11px]">
                                @if($b->expire)
                                    @php
                                        $isExpired = strtotime($b->expire) < time();
                                    @endphp
                                    <span class="{{ $isExpired ? 'text-red-600 dark:text-red-400 font-bold' : 'text-neutral-700 dark:text-neutral-300' }}">
                                        {{ date('d-m-Y', strtotime($b->expire)) }}
                                    </span>
                                @else
                                    <span class="text-neutral-400">-</span>
                                @endif
                            </td>

                            {{-- Kandungan / Letak / Keterangan --}}
                            <td class="px-3 py-1.5 align-middle text-neutral-600 dark:text-neutral-400 max-w-[180px] truncate" title="{{ $b->letak_barang }}">
                                {{ $b->letak_barang ?: '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-4 py-8 text-center text-neutral-400 dark:text-neutral-500">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <flux:icon name="inbox" class="w-8 h-8 text-neutral-300 dark:text-neutral-600" />
                                    <span>Data obat / barang tidak ditemukan</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($barangList->hasPages())
            <div class="px-4 py-3 border-t border-neutral-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-900/50">
                {{ $barangList->links('vendor.livewire.tailwind') }}
            </div>
        @endif
    </div>

    {{-- MODAL DETAIL BARANG --}}
    @if($showDetailModal && $selectedBarang)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
        x-transition:enter="ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0">
        
        <div class="bg-white dark:bg-neutral-800 rounded-2xl border border-neutral-200 dark:border-neutral-700 shadow-xl w-full max-w-2xl max-h-[90vh] overflow-hidden flex flex-col"
            @click.outside="$wire.closeDetail()">
            
            {{-- Modal Header --}}
            <div class="px-6 py-4 border-b border-neutral-200 dark:border-neutral-700 flex items-center justify-between bg-neutral-50/50 dark:bg-neutral-900/30">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-[#4C5C2D]/10 text-[#4C5C2D] dark:text-[#8CC7C4] flex items-center justify-center">
                        <flux:icon name="beaker" class="w-5 h-5" />
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-neutral-800 dark:text-neutral-100">Detail Obat & BHP</h3>
                        <p class="text-xs font-mono text-neutral-400">{{ $selectedBarang->kode_brng }}</p>
                    </div>
                </div>
                <button type="button" wire:click="closeDetail" class="text-neutral-400 hover:text-neutral-600 dark:hover:text-neutral-200 p-1.5 rounded-lg hover:bg-neutral-100 dark:hover:bg-neutral-700 transition-colors">
                    <flux:icon name="x-mark" class="w-5 h-5" />
                </button>
            </div>

            {{-- Modal Content --}}
            <div class="p-6 overflow-y-auto flex-1 flex flex-col gap-4 text-xs">
                {{-- Master Info Grid --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 bg-neutral-50 dark:bg-neutral-900/40 p-4 rounded-xl border border-neutral-200 dark:border-neutral-700">
                    <div>
                        <span class="text-neutral-400">Nama Barang:</span>
                        <div class="font-bold text-sm text-neutral-900 dark:text-neutral-100">{{ $selectedBarang->nama_brng }}</div>
                    </div>
                    <div>
                        <span class="text-neutral-400">Status:</span>
                        <div>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $selectedBarang->status === '1' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-400' : 'bg-neutral-200 text-neutral-600 dark:bg-neutral-700 dark:text-neutral-400' }}">
                                {{ $selectedBarang->status === '1' ? 'Aktif' : 'Non-Aktif' }}
                            </span>
                        </div>
                    </div>
                    <div>
                        <span class="text-neutral-400">Satuan Besar:</span>
                        <div class="font-medium text-neutral-800 dark:text-neutral-200">{{ $selectedBarang->satuanBesar->satuan ?? '-' }} ({{ $selectedBarang->kode_satbesar }})</div>
                    </div>
                    <div>
                        <span class="text-neutral-400">Satuan Kecil:</span>
                        <div class="font-medium text-neutral-800 dark:text-neutral-200">{{ $selectedBarang->satuanKecil->satuan ?? '-' }} ({{ $selectedBarang->kode_sat }})</div>
                    </div>
                    <div>
                        <span class="text-neutral-400">Kapasitas / Isi:</span>
                        <div class="font-medium text-neutral-800 dark:text-neutral-200">{{ $selectedBarang->kapasitas }} (Isi: {{ $selectedBarang->isi }})</div>
                    </div>
                    <div>
                        <span class="text-neutral-400">Total Stok Saat Ini:</span>
                        @php
                            $modalStok = (float)($selectedBarang->total_stok ?? 0);
                            $modalMin = (float)($selectedBarang->stokminimal ?? 0);
                            $modalKritis = ($modalMin > 0 && $modalStok <= $modalMin);
                        @endphp
                        <div class="mt-0.5">
                            @if($modalKritis)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-bold bg-rose-100 text-rose-700 dark:bg-rose-950/70 dark:text-rose-300 border border-rose-300 dark:border-rose-800">
                                    <flux:icon name="exclamation-triangle" class="w-3.5 h-3.5 text-rose-600" />
                                    {{ number_format($modalStok, 0, ',', '.') }} (Stok Kritis ≤ Min {{ number_format($modalMin, 0, ',', '.') }})
                                </span>
                            @else
                                <span class="font-bold text-neutral-800 dark:text-neutral-200 font-mono">{{ number_format($modalStok, 0, ',', '.') }}</span>
                            @endif
                        </div>
                    </div>
                    <div>
                        <span class="text-neutral-400">Stok Minimal (Ambang Batas):</span>
                        <div class="font-medium text-neutral-800 dark:text-neutral-200 mt-0.5 font-mono">{{ number_format($selectedBarang->stokminimal, 0, ',', '.') }}</div>
                    </div>
                    <div>
                        <span class="text-neutral-400">Kategori / Golongan / Jenis:</span>
                        <div class="font-medium text-neutral-800 dark:text-neutral-200">
                            {{ $selectedBarang->kategori->nama ?? '-' }} / {{ $selectedBarang->golongan->nama ?? '-' }} / {{ $selectedBarang->jenis->nama ?? '-' }}
                        </div>
                    </div>
                    <div>
                        <span class="text-neutral-400">Industri Farmasi / Letak:</span>
                        <div class="font-medium text-neutral-800 dark:text-neutral-200">
                            {{ $selectedBarang->industri->nama_industri ?? '-' }} (Letak: {{ $selectedBarang->letak_barang ?: '-' }})
                        </div>
                    </div>
                </div>

                {{-- Price Structure --}}
                <div class="border border-neutral-200 dark:border-neutral-700 rounded-xl overflow-hidden">
                    <div class="bg-neutral-100 dark:bg-neutral-700/50 px-3 py-2 font-semibold text-neutral-800 dark:text-neutral-200">
                        Struktur Tarif & Harga
                    </div>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5 p-3 bg-white dark:bg-neutral-800">
                        <div>
                            <span class="text-neutral-400">Harga Beli:</span>
                            <div class="font-bold text-neutral-900 dark:text-neutral-100">Rp {{ number_format($selectedBarang->h_beli, 0, ',', '.') }}</div>
                        </div>
                        <div>
                            <span class="text-neutral-400">Rawat Jalan:</span>
                            <div class="font-bold text-blue-600 dark:text-blue-400">Rp {{ number_format($selectedBarang->ralan, 0, ',', '.') }}</div>
                        </div>
                        <div>
                            <span class="text-neutral-400">Kelas 1:</span>
                            <div class="font-bold text-neutral-800 dark:text-neutral-200">Rp {{ number_format($selectedBarang->kelas1, 0, ',', '.') }}</div>
                        </div>
                        <div>
                            <span class="text-neutral-400">Kelas 2:</span>
                            <div class="font-bold text-neutral-800 dark:text-neutral-200">Rp {{ number_format($selectedBarang->kelas2, 0, ',', '.') }}</div>
                        </div>
                        <div>
                            <span class="text-neutral-400">Kelas 3:</span>
                            <div class="font-bold text-neutral-800 dark:text-neutral-200">Rp {{ number_format($selectedBarang->kelas3, 0, ',', '.') }}</div>
                        </div>
                        <div>
                            <span class="text-neutral-400">Utama / VIP:</span>
                            <div class="font-bold text-neutral-800 dark:text-neutral-200">Rp {{ number_format($selectedBarang->utama, 0, ',', '.') }} / {{ number_format($selectedBarang->vip, 0, ',', '.') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Modal Footer --}}
            <div class="px-6 py-3 border-t border-neutral-200 dark:border-neutral-700 flex justify-end bg-neutral-50/50 dark:bg-neutral-900/30">
                <button type="button" wire:click="closeDetail" class="px-4 py-2 text-xs font-semibold rounded-lg bg-neutral-200 dark:bg-neutral-700 text-neutral-700 dark:text-neutral-200 hover:bg-neutral-300 dark:hover:bg-neutral-600 transition-colors">
                    Tutup
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- MODAL FORM (CREATE / EDIT) --}}
    @if($showFormModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
        x-transition:enter="ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0">
        
        <div class="bg-white dark:bg-neutral-800 rounded-2xl border border-neutral-200 dark:border-neutral-700 shadow-xl w-full max-w-3xl max-h-[90vh] overflow-hidden flex flex-col"
            @click.outside="$wire.closeForm()">
            
            {{-- Modal Header --}}
            <div class="px-6 py-4 border-b border-neutral-200 dark:border-neutral-700 flex items-center justify-between bg-neutral-50/50 dark:bg-neutral-900/30">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-[#4C5C2D]/10 text-[#4C5C2D] dark:text-[#8CC7C4] flex items-center justify-center">
                        <flux:icon name="{{ $isEditing ? 'pencil' : 'plus' }}" class="w-5 h-5" />
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-neutral-800 dark:text-neutral-100">
                            {{ $isEditing ? 'Edit Data Barang' : 'Tambah Data Barang Baru' }}
                        </h3>
                        <p class="text-xs text-neutral-400">Lengkapi informasi spesifikasi obat / BHP</p>
                    </div>
                </div>
                <button type="button" wire:click="closeForm" class="text-neutral-400 hover:text-neutral-600 dark:hover:text-neutral-200 p-1.5 rounded-lg hover:bg-neutral-100 dark:hover:bg-neutral-700 transition-colors">
                    <flux:icon name="x-mark" class="w-5 h-5" />
                </button>
            </div>

            {{-- Modal Content Form --}}
            <form wire:submit.prevent="saveBarang" class="p-6 overflow-y-auto flex-1 flex flex-col gap-4 text-xs">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                    {{-- Kode Barang --}}
                    <div>
                        <label class="block font-semibold text-neutral-700 dark:text-neutral-300 mb-1">
                            Kode Barang <span class="text-red-500">*</span>
                        </label>
                        <input type="text" wire:model="kode_brng" {{ $isEditing ? 'readonly' : '' }}
                            class="block w-full px-3 py-2 text-xs rounded-lg border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-neutral-900 dark:text-neutral-100 focus:ring-2 focus:ring-[#4C5C2D]/30 focus:border-[#4C5C2D] {{ $isEditing ? 'bg-neutral-100 dark:bg-neutral-800 cursor-not-allowed opacity-75' : '' }}"
                            placeholder="Misal: B000000001" required />
                        @error('kode_brng') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
                    </div>

                    {{-- Nama Barang --}}
                    <div class="sm:col-span-2">
                        <label class="block font-semibold text-neutral-700 dark:text-neutral-300 mb-1">
                            Nama Barang <span class="text-red-500">*</span>
                        </label>
                        <input type="text" wire:model="nama_brng"
                            class="block w-full px-3 py-2 text-xs rounded-lg border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-neutral-900 dark:text-neutral-100 focus:ring-2 focus:ring-[#4C5C2D]/30 focus:border-[#4C5C2D]"
                            placeholder="Nama Lengkap Obat / Alkes / BHP" required />
                        @error('nama_brng') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
                    </div>

                    {{-- Satuan Besar --}}
                    <div>
                        <label class="block font-semibold text-neutral-700 dark:text-neutral-300 mb-1">
                            Satuan Besar <span class="text-red-500">*</span>
                        </label>
                        <select wire:model="kode_satbesar"
                            class="block w-full px-3 py-2 text-xs rounded-lg border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-neutral-900 dark:text-neutral-100 focus:ring-2 focus:ring-[#4C5C2D]/30 focus:border-[#4C5C2D]" required>
                            <option value="">-- Pilih Satuan Besar --</option>
                            @foreach($listSatuan as $sat)
                                <option value="{{ $sat->kode_sat }}">{{ $sat->satuan }} ({{ $sat->kode_sat }})</option>
                            @endforeach
                        </select>
                        @error('kode_satbesar') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
                    </div>

                    {{-- Satuan Kecil --}}
                    <div>
                        <label class="block font-semibold text-neutral-700 dark:text-neutral-300 mb-1">
                            Satuan Kecil <span class="text-red-500">*</span>
                        </label>
                        <select wire:model="kode_sat"
                            class="block w-full px-3 py-2 text-xs rounded-lg border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-neutral-900 dark:text-neutral-100 focus:ring-2 focus:ring-[#4C5C2D]/30 focus:border-[#4C5C2D]" required>
                            <option value="">-- Pilih Satuan Kecil --</option>
                            @foreach($listSatuan as $sat)
                                <option value="{{ $sat->kode_sat }}">{{ $sat->satuan }} ({{ $sat->kode_sat }})</option>
                            @endforeach
                        </select>
                        @error('kode_sat') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
                    </div>

                    {{-- Kapasitas --}}
                    <div>
                        <label class="block font-semibold text-neutral-700 dark:text-neutral-300 mb-1">Kapasitas</label>
                        <input type="number" step="any" wire:model="kapasitas"
                            class="block w-full px-3 py-2 text-xs rounded-lg border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-neutral-900 dark:text-neutral-100 focus:ring-2 focus:ring-[#4C5C2D]/30 focus:border-[#4C5C2D]"
                            placeholder="0" />
                    </div>

                    {{-- Harga Beli --}}
                    <div>
                        <label class="block font-semibold text-neutral-700 dark:text-neutral-300 mb-1">Harga Beli (Rp)</label>
                        <input type="number" step="any" wire:model="h_beli"
                            class="block w-full px-3 py-2 text-xs rounded-lg border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-neutral-900 dark:text-neutral-100 focus:ring-2 focus:ring-[#4C5C2D]/30 focus:border-[#4C5C2D]" />
                    </div>

                    {{-- Harga Ralan --}}
                    <div>
                        <label class="block font-semibold text-neutral-700 dark:text-neutral-300 mb-1">Harga Rawat Jalan (Rp)</label>
                        <input type="number" step="any" wire:model="ralan"
                            class="block w-full px-3 py-2 text-xs rounded-lg border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-neutral-900 dark:text-neutral-100 focus:ring-2 focus:ring-[#4C5C2D]/30 focus:border-[#4C5C2D]" />
                    </div>

                    {{-- Letak Barang --}}
                    <div>
                        <label class="block font-semibold text-neutral-700 dark:text-neutral-300 mb-1">Letak Barang / Kandungan</label>
                        <input type="text" wire:model="letak_barang"
                            class="block w-full px-3 py-2 text-xs rounded-lg border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-neutral-900 dark:text-neutral-100 focus:ring-2 focus:ring-[#4C5C2D]/30 focus:border-[#4C5C2D]"
                            placeholder="Letak rak / kandungan" />
                    </div>

                    {{-- Kategori --}}
                    <div>
                        <label class="block font-semibold text-neutral-700 dark:text-neutral-300 mb-1">Kategori</label>
                        <select wire:model="form_kode_kategori"
                            class="block w-full px-3 py-2 text-xs rounded-lg border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-neutral-900 dark:text-neutral-100 focus:ring-2 focus:ring-[#4C5C2D]/30 focus:border-[#4C5C2D]">
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($listKategori as $kat)
                                <option value="{{ $kat->kode }}">{{ $kat->nama }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Golongan --}}
                    <div>
                        <label class="block font-semibold text-neutral-700 dark:text-neutral-300 mb-1">Golongan</label>
                        <select wire:model="form_kode_golongan"
                            class="block w-full px-3 py-2 text-xs rounded-lg border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-neutral-900 dark:text-neutral-100 focus:ring-2 focus:ring-[#4C5C2D]/30 focus:border-[#4C5C2D]">
                            <option value="">-- Pilih Golongan --</option>
                            @foreach($listGolongan as $gol)
                                <option value="{{ $gol->kode }}">{{ $gol->nama }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Jenis --}}
                    <div>
                        <label class="block font-semibold text-neutral-700 dark:text-neutral-300 mb-1">Jenis</label>
                        <select wire:model="form_kdjns"
                            class="block w-full px-3 py-2 text-xs rounded-lg border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-neutral-900 dark:text-neutral-100 focus:ring-2 focus:ring-[#4C5C2D]/30 focus:border-[#4C5C2D]">
                            <option value="">-- Pilih Jenis --</option>
                            @foreach($listJenis as $jns)
                                <option value="{{ $jns->kdjns }}">{{ $jns->nama }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Industri --}}
                    <div>
                        <label class="block font-semibold text-neutral-700 dark:text-neutral-300 mb-1">Industri Farmasi</label>
                        <select wire:model="kode_industri"
                            class="block w-full px-3 py-2 text-xs rounded-lg border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-neutral-900 dark:text-neutral-100 focus:ring-2 focus:ring-[#4C5C2D]/30 focus:border-[#4C5C2D]">
                            <option value="">-- Pilih Industri --</option>
                            @foreach($listIndustri as $ind)
                                <option value="{{ $ind->kode_industri }}">{{ $ind->nama_industri }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Status --}}
                    <div>
                        <label class="block font-semibold text-neutral-700 dark:text-neutral-300 mb-1">Status</label>
                        <select wire:model="form_status"
                            class="block w-full px-3 py-2 text-xs rounded-lg border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-neutral-900 dark:text-neutral-100 focus:ring-2 focus:ring-[#4C5C2D]/30 focus:border-[#4C5C2D]">
                            <option value="1">Aktif</option>
                            <option value="0">Non-Aktif</option>
                        </select>
                    </div>
                </div>

                {{-- Modal Action Footer --}}
                <div class="mt-4 pt-3 border-t border-neutral-200 dark:border-neutral-700 flex justify-end gap-2">
                    <button type="button" wire:click="closeForm" class="px-4 py-2 rounded-lg border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 text-neutral-700 dark:text-neutral-200 hover:bg-neutral-50 dark:hover:bg-neutral-700 transition-colors font-medium">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 rounded-lg text-white font-semibold shadow transition-colors" style="background-color: #4C5C2D;">
                        {{ $isEditing ? 'Simpan Perubahan' : 'Tambah Barang' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
