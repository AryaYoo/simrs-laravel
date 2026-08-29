<div class="flex flex-col gap-6 pb-8" x-data="{
    formatRupiah(num) {
        return new Intl.NumberFormat('id-ID').format(num || 0);
    }
}"
x-on:swal:success.window="
    Swal.fire({
        title: $event.detail[0].title || 'Berhasil',
        text: $event.detail[0].text,
        icon: 'success',
        confirmButtonColor: '#4C5C2D'
    });
"
x-on:swal:error.window="
    Swal.fire({
        title: $event.detail[0].title || 'Peringatan',
        text: $event.detail[0].text,
        icon: 'error',
        confirmButtonColor: '#dc2626'
    });
">

    {{-- Header / Breadcrumb --}}
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center gap-3">
            <button type="button" onclick="history.back()"
                class="flex items-center justify-center w-9 h-9 rounded-lg bg-[#4C5C2D] text-white hover:bg-[#3d4b24] transition-colors shadow-sm"
                title="Kembali">
                <flux:icon name="chevron-left" class="w-5 h-5" />
            </button>
            <div>
                <nav class="text-xs text-neutral-400 mb-0.5">
                    <a href="{{ route('modul.index') }}" wire:navigate class="hover:underline">Modul</a>
                    <span class="mx-1">/</span>
                    <a href="{{ route('modul.farmasi.index') }}" wire:navigate class="hover:underline">Farmasi</a>
                    <span class="mx-1">/</span>
                    <a href="{{ route('modul.farmasi.stock-opname') }}" wire:navigate class="hover:underline">Stock Opname</a>
                    <span class="mx-1">/</span>
                    <span>Input Stok Opname</span>
                </nav>
                <h1 class="text-xl font-bold text-neutral-800 dark:text-neutral-100 flex items-center gap-2">
                    Input Stok Opname
                </h1>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="flex items-center gap-2">
            <button type="button" onclick="history.back()"
                class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg text-sm font-medium border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 text-neutral-700 dark:text-neutral-200 hover:bg-neutral-50 dark:hover:bg-neutral-700/50 shadow-sm transition-colors">
                <flux:icon name="arrow-left" class="w-4 h-4" />
                Kembali
            </button>

            @if($kd_bangsal)
                <button type="button" wire:click="save"
                    wire:loading.attr="disabled"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white shadow transition-colors disabled:opacity-50"
                    style="background-color: #4C5C2D;"
                    title="Simpan perubahan stok opname">
                    <flux:icon name="check" class="w-4 h-4" />
                    <span wire:loading.remove wire:target="save">Simpan Opname</span>
                    <span wire:loading wire:target="save">Menyimpan...</span>
                </button>
            @endif
        </div>
    </div>

    {{-- Form Header Card: Tanggal, Lokasi, Keterangan --}}
    <div class="bg-white dark:bg-neutral-800 rounded-2xl border border-neutral-200/80 dark:border-neutral-700 shadow-sm p-5">
        <div class="flex items-center gap-2 pb-3 mb-4 border-b border-neutral-100 dark:border-neutral-700/60">
            <div class="w-2 h-4 bg-[#4C5C2D] rounded-full"></div>
            <h2 class="text-xs font-bold text-neutral-800 dark:text-neutral-200 uppercase tracking-wider">Parameter Stok Opname</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            {{-- Tanggal Opname --}}
            <div class="flex flex-col gap-1">
                <label class="text-xs font-semibold text-neutral-700 dark:text-neutral-300 flex items-center justify-between">
                    <span>Tanggal Opname</span>
                    <span class="text-[10px] text-red-500 font-bold">*Wajib</span>
                </label>
                <div class="relative">
                    <input type="date" wire:model="tanggal"
                        class="w-full bg-white dark:bg-neutral-900 border border-neutral-300 dark:border-neutral-700 text-neutral-800 dark:text-neutral-100 rounded-lg text-xs px-3 py-2 shadow-sm focus:border-[#4C5C2D] focus:ring-[#4C5C2D]">
                </div>
            </div>

            {{-- Lokasi / Bangsal --}}
            <div class="flex flex-col gap-1">
                <label class="text-xs font-semibold text-neutral-700 dark:text-neutral-300 flex items-center justify-between">
                    <span>Lokasi / Bangsal</span>
                    <span class="text-[10px] text-red-500 font-bold">*Wajib Dipilih</span>
                </label>
                <div class="flex gap-1.5">
                    <div class="flex-1 flex rounded-lg shadow-sm overflow-hidden cursor-pointer border border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-900"
                        wire:click="openBangsalModal">
                        <input type="text" wire:model="kd_bangsal" placeholder="Kode" readonly
                            class="w-1/3 bg-neutral-100 dark:bg-neutral-800 text-xs px-2.5 py-2 font-bold text-[#4C5C2D] dark:text-[#8CC7C4] cursor-pointer border-r border-neutral-300 dark:border-neutral-700 focus:ring-0">
                        <input type="text" wire:model="nm_bangsal" placeholder="Klik untuk pilih Lokasi..." readonly
                            class="w-2/3 bg-transparent text-xs px-2.5 py-2 font-semibold text-neutral-800 dark:text-neutral-100 cursor-pointer focus:ring-0 truncate">
                    </div>
                    <button type="button" wire:click="openBangsalModal"
                        class="flex-shrink-0 px-3 py-2 bg-[#F1F5E9] dark:bg-[#4C5C2D]/30 hover:bg-[#e2ebd3] text-[#4C5C2D] dark:text-[#8CC7C4] rounded-lg border border-[#4C5C2D]/40 transition-colors shadow-sm"
                        title="Buka Pop-up Lokasi">
                        <flux:icon name="magnifying-glass" class="w-4 h-4" />
                    </button>
                </div>
            </div>

            {{-- Keterangan --}}
            <div class="flex flex-col gap-1">
                <label class="text-xs font-semibold text-neutral-700 dark:text-neutral-300">Keterangan</label>
                <input type="text" wire:model="keterangan" placeholder="Contoh: Stok Opname Bulanan"
                    class="w-full bg-white dark:bg-neutral-900 border border-neutral-300 dark:border-neutral-700 text-neutral-800 dark:text-neutral-100 rounded-lg text-xs px-3 py-2 shadow-sm focus:border-[#4C5C2D] focus:ring-[#4C5C2D]">
            </div>
        </div>
    </div>

    @if(!$kd_bangsal)
        {{-- State: Lokasi Belum Dipilih --}}
        <div class="bg-white dark:bg-neutral-800 rounded-2xl border border-neutral-200 dark:border-neutral-700 p-12 flex flex-col items-center justify-center text-center shadow-sm">
            <div class="w-16 h-16 rounded-2xl flex items-center justify-center shadow-md mb-4 bg-[#F1F5E9] dark:bg-[#4C5C2D]/20 text-[#4C5C2D] dark:text-[#8CC7C4]">
                <flux:icon name="building-office-2" class="w-8 h-8" />
            </div>
            <h3 class="text-base font-bold text-neutral-800 dark:text-neutral-100 mb-1">Lokasi Belum Dipilih</h3>
            <p class="text-xs text-neutral-400 dark:text-neutral-500 max-w-sm mb-4">
                Silakan tentukan Lokasi / Bangsal di atas untuk memunculkan daftar stok barang yang akan di-opname.
            </p>
            <button type="button" wire:click="openBangsalModal"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-xs font-semibold text-white shadow transition-colors"
                style="background-color: #4C5C2D;">
                <flux:icon name="magnifying-glass" class="w-4 h-4" />
                Pilih Lokasi Sekarang
            </button>
        </div>
    @else
        {{-- State: Lokasi Terpilih -> Tampilkan Tabel Barang --}}
        <div class="bg-white dark:bg-neutral-800 rounded-2xl border border-neutral-200 dark:border-neutral-700 shadow-sm overflow-hidden flex flex-col">
            
            {{-- Filter & Search Bar --}}
            <div class="p-4 border-b border-neutral-200 dark:border-neutral-700 bg-neutral-50/60 dark:bg-neutral-900/30 flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold text-neutral-700 dark:text-neutral-200">Daftar Stok di Lokasi:</span>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-[#F1F5E9] dark:bg-[#4C5C2D]/30 text-[#4C5C2D] dark:text-[#8CC7C4] border border-[#4C5C2D]/20">
                        {{ $nm_bangsal }} ({{ $kd_bangsal }})
                    </span>
                </div>

                <div class="flex items-center gap-3">
                    {{-- Search --}}
                    <div class="relative w-64 sm:w-80">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <flux:icon name="magnifying-glass" class="w-3.5 h-3.5 text-neutral-400" />
                        </div>
                        <input type="text" wire:model.live.debounce.300ms="search"
                            class="block w-full pl-8 pr-3 py-1.5 text-xs rounded-lg border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 text-neutral-900 dark:text-neutral-100 placeholder-neutral-400 focus:outline-none focus:ring-1 focus:ring-[#4C5C2D] focus:border-[#4C5C2D]"
                            placeholder="Cari Kode, Nama, Jenis..." />
                    </div>

                    {{-- Per Page --}}
                    <div>
                        <select wire:model.live="perPage"
                            class="block py-1.5 px-2.5 text-xs rounded-lg border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 text-neutral-900 dark:text-neutral-100 focus:outline-none focus:ring-1 focus:ring-[#4C5C2D] focus:border-[#4C5C2D]">
                            <option value="10">10 / hal</option>
                            <option value="20">20 / hal</option>
                            <option value="50">50 / hal</option>
                            <option value="100">100 / hal</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto flex-1">
                <table class="w-full text-left text-neutral-600 dark:text-neutral-300" style="font-size: 0.72rem;">
                    <thead class="bg-neutral-50 dark:bg-neutral-900/50 text-neutral-700 dark:text-neutral-200 border-b border-neutral-200 dark:border-neutral-700 sticky top-0 z-10">
                        <tr>
                            <th class="px-2.5 py-2 font-bold whitespace-nowrap text-center text-[#4C5C2D] dark:text-[#8CC7C4] w-24">Real</th>
                            <th class="px-2.5 py-2 font-semibold whitespace-nowrap">Kode Barang</th>
                            <th class="px-2.5 py-2 font-semibold min-w-[160px]">Nama Barang</th>
                            <th class="px-2.5 py-2 font-semibold whitespace-nowrap">Jenis</th>
                            <th class="px-2.5 py-2 font-semibold whitespace-nowrap">Satuan</th>
                            <th class="px-2.5 py-2 font-semibold whitespace-nowrap text-right">Harga</th>
                            <th class="px-2.5 py-2 font-semibold whitespace-nowrap text-right">Stok</th>
                            <th class="px-2.5 py-2 font-semibold whitespace-nowrap text-right">Selisih</th>
                            <th class="px-2.5 py-2 font-semibold whitespace-nowrap text-right">Lebih</th>
                            <th class="px-2.5 py-2 font-semibold whitespace-nowrap text-right">Nominal Hilang(Rp)</th>
                            <th class="px-2.5 py-2 font-semibold whitespace-nowrap text-right">Nominal Lebih(Rp)</th>
                            <th class="px-2.5 py-2 font-semibold whitespace-nowrap w-28">No.Batch</th>
                            <th class="px-2.5 py-2 font-semibold whitespace-nowrap w-28">No.Faktur</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                        @forelse($items as $item)
                            @php
                                $itemKey = $this->encodeKey($item->kode_brng, $item->no_batch, $item->no_faktur);
                                $stok = (float)$item->stok;
                                $h_beli = (float)$item->h_beli;
                                $existingReal = $realValues[$itemKey] ?? '';
                            @endphp
                            <tr wire:key="row-{{ $itemKey }}"
                                x-data="{
                                    real: '{{ $existingReal }}',
                                    stok: {{ $stok }},
                                    harga: {{ $h_beli }},
                                    get selisih() {
                                        if (this.real === '' || this.real === null || isNaN(this.real)) return 0;
                                        return Number(this.stok) > Number(this.real) ? (Number(this.stok) - Number(this.real)) : 0;
                                    },
                                    get lebih() {
                                        if (this.real === '' || this.real === null || isNaN(this.real)) return 0;
                                        return Number(this.real) > Number(this.stok) ? (Number(this.real) - Number(this.stok)) : 0;
                                    },
                                    get nomiHilang() {
                                        return this.selisih * this.harga;
                                    },
                                    get nomiLebih() {
                                        return this.lebih * this.harga;
                                    }
                                }"
                                class="hover:bg-neutral-50 dark:hover:bg-neutral-800/50 transition-colors">
                                
                                {{-- Real Input --}}
                                <td class="px-2 py-1 align-middle text-center">
                                    <input type="number" step="any"
                                        x-model="real"
                                        wire:model="realValues.{{ $itemKey }}"
                                        placeholder="0"
                                        class="w-20 px-2 py-1 text-center font-bold text-xs rounded border border-neutral-300 dark:border-neutral-600 bg-white dark:bg-neutral-900 text-blue-600 dark:text-blue-400 focus:outline-none focus:ring-1 focus:ring-[#4C5C2D] focus:border-[#4C5C2D] shadow-inner" />
                                </td>

                                {{-- Kode Barang --}}
                                <td class="px-2.5 py-1.5 align-middle whitespace-nowrap font-mono font-medium text-neutral-800 dark:text-neutral-200">
                                    {{ $item->kode_brng }}
                                </td>

                                {{-- Nama Barang --}}
                                <td class="px-2.5 py-1.5 align-middle font-medium text-neutral-800 dark:text-neutral-200" title="{{ $item->nama_brng }}">
                                    {{ $item->nama_brng }}
                                </td>

                                {{-- Jenis --}}
                                <td class="px-2.5 py-1.5 align-middle whitespace-nowrap text-neutral-600 dark:text-neutral-400">
                                    {{ $item->jenis->nama ?? '-' }}
                                </td>

                                {{-- Satuan --}}
                                <td class="px-2.5 py-1.5 align-middle whitespace-nowrap text-neutral-600 dark:text-neutral-400">
                                    {{ $item->satuanKecil->satuan ?? '-' }}
                                </td>

                                {{-- Harga --}}
                                <td class="px-2.5 py-1.5 align-middle whitespace-nowrap text-right font-medium">
                                    {{ number_format($item->h_beli, 0, ',', '.') }}
                                </td>

                                {{-- Stok Saat Ini --}}
                                <td class="px-2.5 py-1.5 align-middle whitespace-nowrap text-right font-bold text-neutral-700 dark:text-neutral-300">
                                    {{ number_format($item->stok, 0, ',', '.') }}
                                </td>

                                {{-- Selisih (Kekurangan) --}}
                                <td class="px-2.5 py-1.5 align-middle whitespace-nowrap text-right font-medium text-amber-600 dark:text-amber-400">
                                    <span x-text="formatRupiah(selisih)">0</span>
                                </td>

                                {{-- Lebih (Kelebihan) --}}
                                <td class="px-2.5 py-1.5 align-middle whitespace-nowrap text-right font-medium text-emerald-600 dark:text-emerald-400">
                                    <span x-text="formatRupiah(lebih)">0</span>
                                </td>

                                {{-- Nominal Hilang (Rp) --}}
                                <td class="px-2.5 py-1.5 align-middle whitespace-nowrap text-right font-semibold text-red-600 dark:text-red-400">
                                    <span x-text="formatRupiah(nomiHilang)">0</span>
                                </td>

                                {{-- Nominal Lebih (Rp) --}}
                                <td class="px-2.5 py-1.5 align-middle whitespace-nowrap text-right font-semibold text-green-600 dark:text-green-400">
                                    <span x-text="formatRupiah(nomiLebih)">0</span>
                                </td>

                                {{-- No. Batch --}}
                                <td class="px-2 py-1 align-middle">
                                    <input type="text"
                                        wire:model="batchValues.{{ $itemKey }}"
                                        placeholder="{{ $item->no_batch ?: '-' }}"
                                        class="w-full px-1.5 py-0.5 text-xs rounded border border-neutral-200 dark:border-neutral-700 bg-transparent text-neutral-800 dark:text-neutral-200 focus:bg-white focus:ring-1 focus:ring-[#4C5C2D]" />
                                </td>

                                {{-- No. Faktur --}}
                                <td class="px-2 py-1 align-middle">
                                    <input type="text"
                                        wire:model="fakturValues.{{ $itemKey }}"
                                        placeholder="{{ $item->no_faktur ?: '-' }}"
                                        class="w-full px-1.5 py-0.5 text-xs rounded border border-neutral-200 dark:border-neutral-700 bg-transparent text-neutral-800 dark:text-neutral-200 focus:bg-white focus:ring-1 focus:ring-[#4C5C2D]" />
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="13" class="px-4 py-8 text-center text-neutral-400 dark:text-neutral-500">
                                    <div class="flex flex-col items-center justify-center gap-2">
                                        <flux:icon name="inbox" class="w-8 h-8 text-neutral-300 dark:text-neutral-600" />
                                        <span>Data barang tidak ditemukan untuk filter ini</span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($items && $items->hasPages())
                <div class="px-4 py-3 border-t border-neutral-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-900/50">
                    {{ $items->links('vendor.livewire.tailwind') }}
                </div>
            @endif
        </div>
    @endif

    {{-- Modal Pop-up Lokasi (Bangsal) --}}
    <div x-data="{ open: @entangle('isBangsalModalOpen') }">
        <div x-show="open" x-cloak class="fixed inset-0 z-[99]">
            <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="open = false"></div>
            <div class="relative z-10 flex min-h-full items-center justify-center p-4">
                <div class="bg-white dark:bg-neutral-800 rounded-2xl shadow-2xl w-full max-w-2xl flex flex-col overflow-hidden max-h-[85vh]" @click.stop>
                    {{-- Header Modal --}}
                    <div class="px-5 py-3 border-b border-neutral-200 flex items-center justify-between bg-[#F1F5E9] dark:bg-[#4C5C2D]/30">
                        <div class="flex items-center gap-2">
                            <flux:icon name="building-office-2" class="w-5 h-5 text-[#4C5C2D] dark:text-[#8CC7C4]" />
                            <h3 class="font-bold text-sm text-[#4C5C2D] dark:text-[#8CC7C4] tracking-wider uppercase">Pilih Lokasi / Bangsal</h3>
                        </div>
                        <button type="button" @click="open = false" class="text-neutral-400 hover:text-red-500 transition-colors">
                            <flux:icon name="x-mark" class="w-5 h-5"/>
                        </button>
                    </div>

                    {{-- Search Bar Modal --}}
                    <div class="p-3 border-b border-neutral-100 dark:border-neutral-700">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <flux:icon name="magnifying-glass" class="w-4 h-4 text-neutral-400" />
                            </div>
                            <input type="text" wire:model.live.debounce.300ms="searchBangsalModal"
                                class="block w-full pl-9 pr-3 py-2 text-xs rounded-lg border border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-neutral-900 dark:text-neutral-100 placeholder-neutral-400 focus:outline-none focus:ring-2 focus:ring-[#4C5C2D]/30 focus:border-[#4C5C2D]"
                                placeholder="Cari Kode atau Nama Lokasi/Bangsal..." />
                        </div>
                    </div>

                    {{-- List Table Modal --}}
                    <div class="flex-1 overflow-y-auto">
                        <table class="w-full text-xs text-left">
                            <thead class="bg-[#4C5C2D] text-white sticky top-0 z-10 uppercase text-[11px]">
                                <tr>
                                    <th class="px-4 py-2.5 font-semibold w-36">Kode Lokasi</th>
                                    <th class="px-4 py-2.5 font-semibold">Nama Lokasi / Bangsal</th>
                                    <th class="px-4 py-2.5 font-semibold w-24 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-neutral-100 dark:divide-neutral-700">
                                @forelse($listBangsal as $b)
                                    <tr wire:key="bangsal-{{ $b['kd_bangsal'] }}"
                                        wire:click="selectBangsal('{{ $b['kd_bangsal'] }}', '{{ addslashes($b['nm_bangsal']) }}')"
                                        class="cursor-pointer hover:bg-[#F1F5E9] dark:hover:bg-[#4C5C2D]/20 transition-colors {{ $loop->even ? 'bg-neutral-50 dark:bg-neutral-900/30' : 'bg-white dark:bg-neutral-800' }}">
                                        <td class="px-4 py-2.5 font-mono font-bold text-[#4C5C2D] dark:text-[#8CC7C4]">{{ $b['kd_bangsal'] }}</td>
                                        <td class="px-4 py-2.5 text-neutral-800 dark:text-neutral-200 font-medium">{{ $b['nm_bangsal'] }}</td>
                                        <td class="px-4 py-2.5 text-center">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-[#4C5C2D] text-white">
                                                Pilih
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-4 py-8 text-center text-neutral-400">
                                            Lokasi tidak ditemukan.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
