<div class="flex flex-col gap-6 pb-8">
    {{-- Header / Breadcrumb --}}
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div>
            <nav class="text-xs text-neutral-400 mb-0.5">
                <a href="{{ route('modul.index') }}" wire:navigate class="hover:underline">Modul</a>
                <span class="mx-1">/</span>
                <a href="{{ route('modul.farmasi.index') }}" wire:navigate class="hover:underline">Farmasi</a>
                <span class="mx-1">/</span>
                <span>Stock Opname</span>
            </nav>
            <h1 class="text-xl font-bold text-neutral-800 dark:text-neutral-100">Stock Opname</h1>
        </div>

        {{-- Tombol Input Stok Opname --}}
        <a href="{{ route('modul.farmasi.input-stock-opname') }}" wire:navigate
            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white shadow transition-colors"
            style="background-color: #4C5C2D;"
            title="Input stok opname baru">
            <flux:icon name="plus" class="w-4 h-4" />
            Input Stok Opname
        </a>
    </div>

    {{-- Filter & Search Bar --}}
    <div class="bg-white dark:bg-neutral-800 rounded-xl border border-neutral-200 dark:border-neutral-700 p-4 flex flex-col gap-3">
        {{-- Baris 1: Pencarian, Tanggal, & Paginasi --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            {{-- Search --}}
            <div class="relative sm:col-span-2 lg:col-span-2">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <flux:icon name="magnifying-glass" class="w-4 h-4 text-neutral-400" />
                </div>
                <input type="text" wire:model.live.debounce.300ms="search"
                    class="block w-full pl-9 pr-3 py-2 text-sm rounded-lg border border-neutral-200 dark:border-neutral-700
                           bg-white dark:bg-neutral-800 text-neutral-900 dark:text-neutral-100
                           placeholder-neutral-400 focus:outline-none focus:ring-2 focus:ring-[#4C5C2D]/30 focus:border-[#4C5C2D] transition-colors"
                    placeholder="Cari Kode Barang, Nama Barang, No Batch, No Faktur..." />
            </div>

            {{-- Filter Tanggal --}}
            <div class="flex items-center gap-2 sm:col-span-2 lg:col-span-2">
                <div class="relative flex-1">
                    <input type="date" wire:model.live="tanggal1"
                        class="block w-full px-3 py-2 text-sm rounded-lg border border-neutral-200 dark:border-neutral-700
                               bg-white dark:bg-neutral-800 text-neutral-900 dark:text-neutral-100
                               focus:outline-none focus:ring-2 focus:ring-[#4C5C2D]/30 focus:border-[#4C5C2D] transition-colors" />
                </div>
                <span class="text-neutral-500">-</span>
                <div class="relative flex-1">
                    <input type="date" wire:model.live="tanggal2"
                        class="block w-full px-3 py-2 text-sm rounded-lg border border-neutral-200 dark:border-neutral-700
                               bg-white dark:bg-neutral-800 text-neutral-900 dark:text-neutral-100
                               focus:outline-none focus:ring-2 focus:ring-[#4C5C2D]/30 focus:border-[#4C5C2D] transition-colors" />
                </div>
            </div>

            {{-- Per Page --}}
            <div>
                <select wire:model.live="perPage"
                    class="block w-full py-2 px-3 text-sm rounded-lg border border-neutral-200 dark:border-neutral-700
                           bg-white dark:bg-neutral-800 text-neutral-900 dark:text-neutral-100
                           focus:outline-none focus:ring-2 focus:ring-[#4C5C2D]/30 focus:border-[#4C5C2D] transition-colors">
                    <option value="10">10 / halaman</option>
                    <option value="20">20 / halaman</option>
                    <option value="50">50 / halaman</option>
                    <option value="100">100 / halaman</option>
                </select>
            </div>
        </div>

        {{-- Baris 2: Filter Lokasi, Jenis, Kategori, Golongan, & Reset --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 pt-2 border-t border-neutral-100 dark:border-neutral-700/60">
            {{-- Filter Lokasi (Bangsal) --}}
            <div>
                <select wire:model.live="kd_bangsal"
                    class="block w-full py-2 px-3 text-sm rounded-lg border border-neutral-200 dark:border-neutral-700
                           bg-white dark:bg-neutral-800 text-neutral-900 dark:text-neutral-100
                           focus:outline-none focus:ring-2 focus:ring-[#4C5C2D]/30 focus:border-[#4C5C2D] transition-colors">
                    <option value="">— Semua Lokasi —</option>
                    @foreach($listBangsal as $b)
                        <option value="{{ $b->kd_bangsal }}">{{ $b->nm_bangsal }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Filter Jenis --}}
            <div>
                <select wire:model.live="kdjns"
                    class="block w-full py-2 px-3 text-sm rounded-lg border border-neutral-200 dark:border-neutral-700
                           bg-white dark:bg-neutral-800 text-neutral-900 dark:text-neutral-100
                           focus:outline-none focus:ring-2 focus:ring-[#4C5C2D]/30 focus:border-[#4C5C2D] transition-colors">
                    <option value="">— Semua Jenis —</option>
                    @foreach($listJenis as $j)
                        <option value="{{ $j->kdjns }}">{{ $j->nama }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Filter Kategori --}}
            <div>
                <select wire:model.live="kode_kategori"
                    class="block w-full py-2 px-3 text-sm rounded-lg border border-neutral-200 dark:border-neutral-700
                           bg-white dark:bg-neutral-800 text-neutral-900 dark:text-neutral-100
                           focus:outline-none focus:ring-2 focus:ring-[#4C5C2D]/30 focus:border-[#4C5C2D] transition-colors">
                    <option value="">— Semua Kategori —</option>
                    @foreach($listKategori as $k)
                        <option value="{{ $k->kode }}">{{ $k->nama }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Filter Golongan --}}
            <div>
                <select wire:model.live="kode_golongan"
                    class="block w-full py-2 px-3 text-sm rounded-lg border border-neutral-200 dark:border-neutral-700
                           bg-white dark:bg-neutral-800 text-neutral-900 dark:text-neutral-100
                           focus:outline-none focus:ring-2 focus:ring-[#4C5C2D]/30 focus:border-[#4C5C2D] transition-colors">
                    <option value="">— Semua Golongan —</option>
                    @foreach($listGolongan as $g)
                        <option value="{{ $g->kode }}">{{ $g->nama }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Tombol Reset Filter --}}
            <div>
                <button type="button" wire:click="resetFilter"
                    class="inline-flex items-center justify-center gap-2 w-full py-2 px-3 text-sm font-medium rounded-lg border border-neutral-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-900/50 hover:bg-neutral-100 dark:hover:bg-neutral-700/50 text-neutral-700 dark:text-neutral-200 transition-colors shadow-sm"
                    title="Reset semua filter ke kondisi awal">
                    <flux:icon name="arrow-path" class="w-4 h-4 text-neutral-500" />
                    Reset Filter
                </button>
            </div>
        </div>
    </div>

    {{-- Tabel Data --}}
    <div class="bg-white dark:bg-neutral-800 rounded-xl border border-neutral-200 dark:border-neutral-700 shadow-sm overflow-hidden flex-1 flex flex-col">
        <div class="overflow-x-auto flex-1">
            <table class="w-full text-left text-neutral-600 dark:text-neutral-300" style="font-size: 0.7rem;">
                <thead class="bg-neutral-50 dark:bg-neutral-900/50 text-neutral-700 dark:text-neutral-200 border-b border-neutral-200 dark:border-neutral-700 sticky top-0 z-10">
                    <tr>
                        <th class="px-2 py-1.5 font-semibold whitespace-nowrap w-[40px] text-center">Action</th>
                        <th class="px-2 py-1.5 font-semibold whitespace-nowrap">Kode Barang</th>
                        <th class="px-2 py-1.5 font-semibold whitespace-nowrap w-[120px] max-w-[120px]">Nama Barang</th>
                        <th class="px-2 py-1.5 font-semibold whitespace-nowrap text-right">Harga Beli</th>
                        <th class="px-2 py-1.5 font-semibold whitespace-nowrap">Satuan</th>
                        <th class="px-2 py-1.5 font-semibold whitespace-nowrap">Tanggal</th>
                        <th class="px-2 py-1.5 font-semibold whitespace-nowrap text-right">Stok</th>
                        <th class="px-2 py-1.5 font-semibold whitespace-nowrap text-right">Real</th>
                        <th class="px-2 py-1.5 font-semibold whitespace-nowrap text-right">Selisih</th>
                        <th class="px-2 py-1.5 font-semibold whitespace-nowrap text-right">Lebih</th>
                        <th class="px-2 py-1.5 font-semibold whitespace-nowrap text-right">Total Real</th>
                        <th class="px-2 py-1.5 font-semibold whitespace-nowrap text-right">Nomi Hilang(Rp)</th>
                        <th class="px-2 py-1.5 font-semibold whitespace-nowrap text-right">Nomi Lebih(Rp)</th>
                        <th class="px-2 py-1.5 font-semibold whitespace-nowrap">Keterangan</th>
                        <th class="px-2 py-1.5 font-semibold whitespace-nowrap">Nama Lokasi</th>
                        <th class="px-2 py-1.5 font-semibold whitespace-nowrap">No. Batch</th>
                        <th class="px-2 py-1.5 font-semibold whitespace-nowrap">No. Faktur</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                    @forelse($opnames as $opname)
                        <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-800/50 transition-colors">
                            <td class="px-2 py-1 text-center align-middle">
                                <button type="button" 
                                    @click="
                                        Swal.fire({
                                            title: 'Hapus Data?',
                                            text: 'Data opname ini akan dihapus permanen.',
                                            icon: 'warning',
                                            showCancelButton: true,
                                            confirmButtonColor: '#dc2626',
                                            cancelButtonColor: '#6b7280',
                                            confirmButtonText: 'Ya, Hapus!',
                                            cancelButtonText: 'Batal'
                                        }).then((result) => {
                                            if (result.isConfirmed) { 
                                                $wire.deleteOpname('{{ $opname->kode_brng }}', '{{ $opname->tanggal }}', '{{ $opname->kd_bangsal }}', '{{ $opname->no_batch }}', '{{ $opname->no_faktur }}'); 
                                            }
                                        });
                                    "
                                    class="text-red-500 hover:text-red-700 dark:hover:text-red-400 p-1 rounded hover:bg-red-50 dark:hover:bg-red-500/10 transition-colors"
                                    title="Hapus">
                                    <flux:icon name="trash" class="w-3.5 h-3.5" />
                                </button>
                            </td>
                            <td class="px-2 py-1 align-middle whitespace-nowrap font-medium text-neutral-800 dark:text-neutral-200">
                                {{ $opname->kode_brng }}
                            </td>
                            <td class="px-2 py-1 align-middle font-medium text-neutral-800 dark:text-neutral-200 whitespace-nowrap" title="{{ $opname->dataBarang->nama_brng ?? '' }}">
                                {{ \Illuminate\Support\Str::limit($opname->dataBarang->nama_brng ?? '-', 12) }}
                            </td>
                            <td class="px-2 py-1 align-middle whitespace-nowrap text-right">
                                {{ number_format($opname->h_beli, 0, ',', '.') }}
                            </td>
                            <td class="px-2 py-1 align-middle whitespace-nowrap">
                                {{ $opname->dataBarang->satuanKecil->satuan ?? '-' }}
                            </td>
                            <td class="px-2 py-1 align-middle whitespace-nowrap">
                                {{ date('d-m-Y', strtotime($opname->tanggal)) }}
                            </td>
                            <td class="px-2 py-1 align-middle whitespace-nowrap text-right">
                                {{ number_format($opname->stok, 0, ',', '.') }}
                            </td>
                            <td class="px-2 py-1 align-middle whitespace-nowrap text-right">
                                {{ number_format($opname->real, 0, ',', '.') }}
                            </td>
                            <td class="px-2 py-1 align-middle whitespace-nowrap text-right">
                                {{ number_format($opname->selisih, 0, ',', '.') }}
                            </td>
                            <td class="px-2 py-1 align-middle whitespace-nowrap text-right">
                                {{ number_format($opname->lebih, 0, ',', '.') }}
                            </td>
                            <td class="px-2 py-1 align-middle whitespace-nowrap text-right font-medium text-neutral-800 dark:text-neutral-200">
                                {{ number_format($opname->real * $opname->h_beli, 0, ',', '.') }}
                            </td>
                            <td class="px-2 py-1 align-middle whitespace-nowrap text-right text-red-600 dark:text-red-400">
                                {{ number_format($opname->nomihilang, 0, ',', '.') }}
                            </td>
                            <td class="px-2 py-1 align-middle whitespace-nowrap text-right text-green-600 dark:text-green-400">
                                {{ number_format($opname->nomilebih, 0, ',', '.') }}
                            </td>
                            <td class="px-2 py-1 align-middle max-w-[120px] truncate" title="{{ $opname->keterangan }}">
                                {{ $opname->keterangan ?: '-' }}
                            </td>
                            <td class="px-2 py-1 align-middle whitespace-nowrap">
                                {{ $opname->bangsal->nm_bangsal ?? '-' }}
                            </td>
                            <td class="px-2 py-1 align-middle whitespace-nowrap">
                                {{ $opname->no_batch ?: '-' }}
                            </td>
                            <td class="px-2 py-1 align-middle whitespace-nowrap">
                                {{ $opname->no_faktur ?: '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="17" class="px-2 py-6 text-center text-neutral-400 dark:text-neutral-500">
                                <div class="flex flex-col items-center justify-center gap-1.5">
                                    <flux:icon name="inbox" class="w-6 h-6 text-neutral-300 dark:text-neutral-600" />
                                    <span>Data stock opname tidak ditemukan</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- Summary Info Bar --}}
        <div class="px-4 py-2.5 bg-neutral-50 dark:bg-neutral-900/60 border-t border-neutral-200 dark:border-neutral-700 flex flex-wrap items-center justify-between gap-4 text-xs">
            <div class="flex flex-wrap items-center gap-6 text-neutral-700 dark:text-neutral-300">
                <div class="flex items-center gap-1.5">
                    <span class="text-neutral-500 dark:text-neutral-400">Record :</span>
                    <span class="font-bold font-mono text-neutral-900 dark:text-neutral-100">{{ number_format($summary['record'], 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="text-neutral-500 dark:text-neutral-400">Real :</span>
                    <span class="font-bold font-mono text-blue-600 dark:text-blue-400">{{ number_format($summary['real'], 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="text-neutral-500 dark:text-neutral-400">Hilang :</span>
                    <span class="font-bold font-mono text-red-600 dark:text-red-400">{{ number_format($summary['hilang'], 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="text-neutral-500 dark:text-neutral-400">Lebih :</span>
                    <span class="font-bold font-mono text-green-600 dark:text-green-400">{{ number_format($summary['lebih'], 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        {{-- Pagination --}}
        @if($opnames->hasPages())
            <div class="px-4 py-3 border-t border-neutral-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-900/50">
                {{ $opnames->links('vendor.livewire.tailwind') }}
            </div>
        @endif
    </div>
</div>
