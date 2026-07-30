<div class="flex flex-col gap-6 pb-8" x-data="{
    formatRupiah(number) {
        return new Intl.NumberFormat('id-ID').format(number);
    }
}">
    {{-- Header & Breadcrumb --}}
    <div class="flex items-center gap-3">
        <button type="button"
            onclick="history.back()"
            class="flex items-center justify-center w-9 h-9 rounded-lg bg-[#4C5C2D] text-white hover:bg-[#3d4b24] transition-colors shadow-sm"
            title="Kembali">
            <flux:icon name="chevron-left" class="w-5 h-5" />
        </button>
        <div>
            <nav class="text-xs text-neutral-400 mb-0.5">
                <a href="{{ route('modul.index') }}" wire:navigate class="hover:underline">Modul</a>
                <span class="mx-1">/</span>
                <span>Farmasi</span>
                <span class="mx-1">/</span>
                <a href="{{ route('modul.farmasi.input-penjualan') }}" wire:navigate class="hover:underline">Penjualan Obat & BHP</a>
                <span class="mx-1">/</span>
                <span>Tambah Penjualan</span>
            </nav>
            <h1 class="text-xl font-bold text-neutral-800 dark:text-neutral-100 flex items-center gap-2">
                Tambah Penjualan Obat & BHP
            </h1>
        </div>
    </div>

    {{-- Form Header & Billing Card --}}
    <div class="bg-white dark:bg-neutral-800 rounded-2xl border border-neutral-200/80 dark:border-neutral-700 shadow-sm overflow-hidden">
        
        {{-- Card Section Tabs / Headers --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 divide-y lg:divide-y-0 lg:divide-x divide-neutral-200 dark:divide-neutral-700">
            
            {{-- Left Sub-Panel: Informasi Transaksi --}}
            <div class="lg:col-span-7 p-5 space-y-4">
                <div class="flex items-center gap-2 pb-2 border-b border-neutral-100 dark:border-neutral-700/60">
                    <div class="w-2 h-4 bg-[#4C5C2D] rounded-full"></div>
                    <h2 class="text-sm font-bold text-neutral-800 dark:text-neutral-200 uppercase tracking-wider">Informasi Transaksi</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- No Nota & Tanggal --}}
                    <div class="flex flex-col gap-1">
                        <label class="text-xs font-semibold text-neutral-700 dark:text-neutral-300">No. Nota</label>
                        <input type="text" wire:model="nota_jual" readonly class="w-full bg-neutral-100 dark:bg-neutral-900/60 border border-neutral-300 dark:border-neutral-700 text-[#4C5C2D] dark:text-[#8CC7C4] rounded-lg text-xs px-3 py-2 font-mono font-bold cursor-not-allowed">
                    </div>

                    <div class="flex flex-col gap-1">
                        <label class="text-xs font-semibold text-neutral-700 dark:text-neutral-300">Tanggal Transaksi</label>
                        <input type="date" wire:model="tgl_jual" class="w-full bg-white dark:bg-neutral-900 border border-neutral-300 dark:border-neutral-700 text-neutral-800 dark:text-neutral-100 rounded-lg text-xs px-3 py-2 shadow-sm focus:border-[#4C5C2D] focus:ring-[#4C5C2D]">
                    </div>

                    {{-- Jenis Jual --}}
                    <div class="flex flex-col gap-1">
                        <label class="text-xs font-semibold text-neutral-700 dark:text-neutral-300">Jenis Jual</label>
                        <select wire:model="jns_jual" class="w-full bg-white dark:bg-neutral-900 border border-neutral-300 dark:border-neutral-700 text-neutral-800 dark:text-neutral-100 rounded-lg text-xs px-3 py-2 font-medium shadow-sm focus:border-[#4C5C2D] focus:ring-[#4C5C2D]">
                            <option value="Jual Bebas">Jual Bebas</option>
                            <option value="Karyawan">Karyawan</option>
                            <option value="Rawat Jalan">Rawat Jalan</option>
                            <option value="Utama/BPJS">Utama/BPJS</option>
                            <option value="VIP">VIP</option>
                            <option value="VVIP">VVIP</option>
                        </select>
                    </div>

                    {{-- Lokasi Gudang / Bangsal --}}
                    <div class="flex flex-col gap-1">
                        <label class="text-xs font-semibold text-neutral-700 dark:text-neutral-300 flex items-center justify-between">
                            <span>Lokasi Gudang / Bangsal</span>
                            <span class="text-[10px] text-red-500 font-bold">*Wajib</span>
                        </label>
                        <div class="flex gap-1.5">
                            <div class="flex-1 flex rounded-lg shadow-sm overflow-hidden cursor-pointer border border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-900" wire:click="openBangsalModal">
                                <input type="text" wire:model="kd_bangsal" placeholder="Kode" readonly class="w-1/3 bg-neutral-100 dark:bg-neutral-800 text-xs px-2.5 py-2 font-bold text-[#4C5C2D] dark:text-[#8CC7C4] cursor-pointer border-r border-neutral-300 dark:border-neutral-700 focus:ring-0">
                                <input type="text" wire:model="nm_bangsal" placeholder="Pilih Lokasi Gudang..." readonly class="w-2/3 bg-transparent text-xs px-2.5 py-2 font-semibold text-neutral-800 dark:text-neutral-100 cursor-pointer focus:ring-0 truncate">
                            </div>
                            <button type="button" wire:click="openBangsalModal" class="flex-shrink-0 px-2.5 py-2 bg-[#F1F5E9] dark:bg-[#4C5C2D]/30 hover:bg-[#e2ebd3] text-[#4C5C2D] dark:text-[#8CC7C4] rounded-lg border border-[#4C5C2D]/40 transition-colors shadow-sm" title="Pilih Lokasi Gudang">
                                <flux:icon name="paper-clip" class="w-4 h-4" />
                            </button>
                        </div>
                    </div>

                    {{-- Pasien --}}
                    <div class="flex flex-col gap-1">
                        <label class="text-xs font-semibold text-neutral-700 dark:text-neutral-300">Pasien</label>
                        <div class="flex gap-1.5">
                            <div class="flex-1 flex rounded-lg shadow-sm overflow-hidden cursor-pointer border border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-900" wire:click="openPasienModal">
                                <input type="text" wire:model="no_rkm_medis" placeholder="No. RM" readonly class="w-1/3 bg-neutral-100 dark:bg-neutral-800 text-xs px-2.5 py-2 font-mono text-neutral-600 dark:text-neutral-400 cursor-pointer border-r border-neutral-300 dark:border-neutral-700 focus:ring-0">
                                <input type="text" wire:model="nm_pasien" placeholder="Pilih Pasien..." readonly class="w-2/3 bg-transparent text-xs px-2.5 py-2 text-neutral-800 dark:text-neutral-100 cursor-pointer focus:ring-0 truncate">
                            </div>
                            <button type="button" wire:click="openPasienModal" class="flex-shrink-0 px-2.5 py-2 bg-neutral-100 dark:bg-neutral-800 hover:bg-neutral-200 dark:hover:bg-neutral-700 text-neutral-600 dark:text-neutral-300 rounded-lg border border-neutral-300 dark:border-neutral-700 transition-colors shadow-sm" title="Pilih Pasien">
                                <flux:icon name="paper-clip" class="w-4 h-4" />
                            </button>
                        </div>
                    </div>

                    {{-- Petugas --}}
                    <div class="flex flex-col gap-1">
                        <label class="text-xs font-semibold text-neutral-700 dark:text-neutral-300">Petugas</label>
                        <div class="flex gap-1.5">
                            <div class="flex-1 flex rounded-lg shadow-sm overflow-hidden cursor-pointer border border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-900" wire:click="openPetugasModal">
                                <input type="text" wire:model="nip" placeholder="NIP" readonly class="w-1/3 bg-neutral-100 dark:bg-neutral-800 text-xs px-2.5 py-2 font-mono text-neutral-600 dark:text-neutral-400 cursor-pointer border-r border-neutral-300 dark:border-neutral-700 focus:ring-0">
                                <input type="text" wire:model="nm_petugas" placeholder="Pilih Petugas..." readonly class="w-2/3 bg-transparent text-xs px-2.5 py-2 text-neutral-800 dark:text-neutral-100 cursor-pointer focus:ring-0 truncate">
                            </div>
                            <button type="button" wire:click="openPetugasModal" class="flex-shrink-0 px-2.5 py-2 bg-neutral-100 dark:bg-neutral-800 hover:bg-neutral-200 dark:hover:bg-neutral-700 text-neutral-600 dark:text-neutral-300 rounded-lg border border-neutral-300 dark:border-neutral-700 transition-colors shadow-sm" title="Pilih Petugas">
                                <flux:icon name="paper-clip" class="w-4 h-4" />
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Catatan --}}
                <div class="flex flex-col gap-1 pt-1">
                    <label class="text-xs font-semibold text-neutral-700 dark:text-neutral-300">Catatan Tambahan</label>
                    <input type="text" wire:model="keterangan" placeholder="Keterangan transaksi (opsional)..." class="w-full bg-white dark:bg-neutral-900 border border-neutral-300 dark:border-neutral-700 text-neutral-800 dark:text-neutral-100 rounded-lg text-xs px-3 py-2 shadow-sm focus:border-[#4C5C2D] focus:ring-[#4C5C2D]">
                </div>
            </div>

            {{-- Right Sub-Panel: Kalkulasi Pembayaran & Billing --}}
            <div class="lg:col-span-5 p-5 bg-neutral-50/70 dark:bg-neutral-900/40 space-y-4 flex flex-col justify-between">
                <div class="space-y-4">
                    <div class="flex items-center justify-between pb-2 border-b border-neutral-200/80 dark:border-neutral-700/80">
                        <div class="flex items-center gap-2">
                            <div class="w-2 h-4 bg-emerald-500 rounded-full"></div>
                            <h2 class="text-sm font-bold text-neutral-800 dark:text-neutral-200 uppercase tracking-wider">Kalkulasi Pembayaran</h2>
                        </div>
                        <button type="button" wire:click="save" class="px-4 py-2 bg-[#4C5C2D] hover:bg-[#3d4b24] text-white font-bold text-xs rounded-lg shadow-sm hover:shadow transition-all flex items-center gap-1.5 active:scale-95">
                            <flux:icon name="document-check" class="w-4 h-4" />
                            Simpan Transaksi
                        </button>
                    </div>

                    {{-- Summary Totals Header --}}
                    <div class="grid grid-cols-2 gap-3 p-3 bg-white dark:bg-neutral-800 rounded-xl border border-neutral-300/70 dark:border-neutral-700 shadow-sm">
                        <div>
                            <span class="text-[10px] font-bold text-neutral-400 uppercase tracking-wider block">Grand Total</span>
                            <span class="text-lg font-black text-[#4C5C2D] dark:text-[#8CC7C4]">
                                Rp <span x-text="formatRupiah($wire.grand_total)"></span>
                            </span>
                        </div>
                        <div class="text-right">
                            <span class="text-[10px] font-bold text-neutral-400 uppercase tracking-wider block">Total Tagihan (+PPN)</span>
                            <span class="text-lg font-black text-neutral-800 dark:text-neutral-100">
                                Rp <span x-text="formatRupiah($wire.tagihan)"></span>
                            </span>
                        </div>
                    </div>

                    {{-- Inputs Grid --}}
                    <div class="grid grid-cols-2 gap-3 text-xs">
                        {{-- PPN Obat --}}
                        <div class="flex flex-col gap-1">
                            <label class="text-[11px] font-semibold text-neutral-700 dark:text-neutral-300">PPN (%) / Rp</label>
                            <div class="flex gap-1.5">
                                <input type="number" step="0.1" wire:model.live="ppn_persen" placeholder="%" class="w-1/3 bg-white dark:bg-neutral-900 border border-neutral-300 dark:border-neutral-700 text-neutral-800 dark:text-neutral-100 rounded-lg text-xs px-2 py-1.5 text-right font-medium shadow-sm focus:border-[#4C5C2D] focus:ring-[#4C5C2D]">
                                <input type="number" wire:model="ppn_rp" readonly class="w-2/3 bg-neutral-100 dark:bg-neutral-900/60 border border-neutral-200 dark:border-neutral-700/60 rounded-lg text-xs px-2 py-1.5 text-right text-neutral-500 font-mono">
                            </div>
                        </div>

                        {{-- Ongkos Kirim --}}
                        <div class="flex flex-col gap-1">
                            <label class="text-[11px] font-semibold text-neutral-700 dark:text-neutral-300">Ongkos Kirim (Rp)</label>
                            <input type="number" wire:model.live="ongkir" placeholder="0" class="w-full bg-white dark:bg-neutral-900 border border-neutral-300 dark:border-neutral-700 text-neutral-800 dark:text-neutral-100 rounded-lg text-xs px-2.5 py-1.5 text-right font-medium shadow-sm focus:border-[#4C5C2D] focus:ring-[#4C5C2D]">
                        </div>

                        {{-- Akun Bayar --}}
                        <div class="flex flex-col gap-1 col-span-2">
                            <label class="text-[11px] font-semibold text-neutral-700 dark:text-neutral-300">Akun / Metode Bayar</label>
                            <div class="flex gap-1.5">
                                <select wire:model.live="nama_bayar" class="w-full bg-white dark:bg-neutral-900 border border-neutral-300 dark:border-neutral-700 text-neutral-800 dark:text-neutral-100 rounded-lg text-xs px-3 py-1.5 font-semibold shadow-sm focus:border-[#4C5C2D] focus:ring-[#4C5C2D]">
                                    @foreach($listAkunBayar as $akun)
                                        <option value="{{ $akun->nama_bayar }}">{{ $akun->nama_bayar }}</option>
                                    @endforeach
                                </select>
                                <button type="button" wire:click="loadListAkunBayar" class="px-2.5 py-1.5 bg-white dark:bg-neutral-800 hover:bg-neutral-100 dark:hover:bg-neutral-700 text-neutral-600 dark:text-neutral-300 rounded-lg border border-neutral-300 dark:border-neutral-700 transition-colors shadow-sm" title="Refresh Akun Bayar">
                                    <flux:icon name="arrow-path" class="w-3.5 h-3.5" />
                                </button>
                            </div>
                        </div>

                        {{-- Jumlah Bayar --}}
                        <div class="flex flex-col gap-1 col-span-2">
                            <label class="text-[11px] font-semibold text-neutral-700 dark:text-neutral-300">Jumlah Bayar (Rp)</label>
                            <input type="number" wire:model.live="jumlah_bayar" placeholder="0" class="w-full bg-white dark:bg-neutral-900 border border-neutral-300 dark:border-neutral-700 text-neutral-900 dark:text-neutral-100 rounded-lg text-sm px-3 py-2 text-right font-extrabold shadow-sm focus:border-[#4C5C2D] focus:ring-[#4C5C2D]">
                        </div>
                    </div>
                </div>

                {{-- Kembali Result Banner --}}
                <div class="p-3.5 rounded-xl border flex items-center justify-between"
                     :class="$wire.kembali >= 0 ? 'bg-emerald-50 dark:bg-emerald-950/40 border-emerald-200 dark:border-emerald-800/60' : 'bg-rose-50 dark:bg-rose-950/40 border-rose-200 dark:border-rose-800/60'">
                    <div>
                        <span class="text-[10px] font-extrabold uppercase tracking-wider block" :class="$wire.kembali >= 0 ? 'text-emerald-700 dark:text-emerald-400' : 'text-rose-700 dark:text-rose-400'">
                            <span x-text="$wire.kembali >= 0 ? 'Kembalian' : 'Kurang Bayar'"></span>
                        </span>
                        <span class="text-xs text-neutral-500 dark:text-neutral-400 font-medium">Uang kembalian kasir</span>
                    </div>
                    <div class="text-2xl font-black font-mono" :class="$wire.kembali >= 0 ? 'text-emerald-700 dark:text-emerald-300' : 'text-rose-600 dark:text-rose-400'">
                        Rp <span x-text="formatRupiah(Math.abs($wire.kembali))"></span>
                    </div>
                </div>

            </div>

        </div>

    </div>

    {{-- Main Content Grid: Cart & List --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        {{-- Panel Kiri: List Obat --}}
        <div class="lg:col-span-4 space-y-4">
            <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-sm border border-neutral-200 dark:border-neutral-700 flex flex-col overflow-hidden h-[600px]">
                <div class="p-3 border-b border-neutral-100 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800/50">
                    <flux:input 
                        wire:model.live.debounce.300ms="searchObat" 
                        placeholder="Cari obat dari lokasi terpilih..." 
                        icon="magnifying-glass"
                        class="w-full"
                    />
                </div>
                
                @if(empty($kd_bangsal))
                    <div class="flex-1 flex flex-col items-center justify-center text-center p-6 opacity-50">
                        <flux:icon name="building-storefront" class="w-12 h-12 mb-3" />
                        <p class="text-sm font-medium">Pilih Lokasi/Gudang terlebih dahulu di form atas untuk melihat stok obat.</p>
                    </div>
                @else
                    <div class="flex-1 overflow-y-auto p-0">
                        <table class="w-full text-xs text-left">
                            <thead class="text-neutral-500 bg-neutral-50 dark:bg-neutral-800 sticky top-0 z-10 shadow-sm uppercase text-[10px]">
                                <tr>
                                    <th class="px-3 py-2 font-semibold">Nama Barang</th>
                                    <th class="px-3 py-2 font-semibold text-right">Harga</th>
                                    <th class="px-3 py-2 font-semibold text-center">Stok</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-neutral-100 dark:divide-neutral-800">
                                @forelse($this->obatList as $obat)
                                    <tr wire:key="obat-{{ $obat->kode_brng }}"
                                        x-data="{ clicked: false }"
                                        x-show="!clicked"
                                        x-transition:leave="transition ease-in duration-300"
                                        x-transition:leave-start="opacity-100 translate-x-0"
                                        x-transition:leave-end="opacity-0 translate-x-full"
                                        @click="clicked = true; setTimeout(() => $wire.pushToCart('{{ $obat->kode_brng }}', '{{ addslashes($obat->nama_brng) }}', '{{ $obat->kode_sat }}', {{ $obat->harga }}, {{ $obat->stok }}), 250)"
                                        class="bg-white dark:bg-neutral-900 hover:bg-[#F1F5E9] transition-colors cursor-pointer group">
                                        <td class="px-3 py-2 font-bold text-neutral-700 dark:text-neutral-300">
                                            {{ $obat->nama_brng }}
                                            <div class="text-[10px] text-neutral-400 font-normal font-mono">{{ $obat->kode_brng }} &bull; {{ $obat->kode_sat }}</div>
                                        </td>
                                        <td class="px-3 py-2 text-right font-medium text-neutral-600">{{ number_format($obat->harga, 0, ',', '.') }}</td>
                                        <td class="px-3 py-2 text-center font-bold {{ $obat->stok > 0 ? 'text-[#4C5C2D]' : 'text-red-500' }}">
                                            {{ $obat->stok }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-4 py-8 text-center text-neutral-400">Obat tidak ditemukan.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        {{-- Panel Kanan: Keranjang --}}
        <div class="lg:col-span-8 space-y-4">
            <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-sm border border-neutral-200 dark:border-neutral-700 overflow-hidden h-[600px] flex flex-col">
                <div class="p-3 border-b border-neutral-100 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800/50 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <flux:icon name="shopping-cart" class="w-5 h-5 text-[#4C5C2D] dark:text-[#8CC7C4]" />
                        <h3 class="font-semibold text-neutral-800 dark:text-neutral-200">Keranjang Belanja</h3>
                    </div>
                    <div class="text-xs font-semibold text-neutral-500 bg-white dark:bg-neutral-900 px-2.5 py-1 rounded-md border border-neutral-200 dark:border-neutral-700">
                        Total Item: {{ count($cart) }}
                    </div>
                </div>

                <div class="flex-1 overflow-auto">
                    @if(empty($cart))
                        <div class="flex flex-col items-center justify-center h-full text-neutral-400 opacity-60">
                            <flux:icon name="shopping-cart" class="w-16 h-16 mb-4" />
                            <p class="font-medium">Keranjang masih kosong.</p>
                            <p class="text-sm">Klik baris obat di panel kiri untuk menambahkan.</p>
                        </div>
                    @else
                        <table class="w-full text-[11px] text-left whitespace-nowrap">
                            <thead class="text-neutral-500 bg-neutral-50 dark:bg-neutral-800 sticky top-0 z-10 shadow-sm uppercase text-[10px]">
                                <tr>
                                    <th class="px-2 py-2 w-8 text-center">#</th>
                                    <th class="px-2 py-2">Barang</th>
                                    <th class="px-2 py-2 text-right">Harga</th>
                                    <th class="px-2 py-2 text-center w-20">Jml</th>
                                    <th class="px-2 py-2 w-16">Dis(%)</th>
                                    <th class="px-2 py-2 w-24">Dis(Rp)</th>
                                    <th class="px-2 py-2 w-24">Tmbh(Rp)</th>
                                    <th class="px-2 py-2 w-32">Aturan Pakai</th>
                                    <th class="px-2 py-2 w-24">No Batch</th>
                                    <th class="px-2 py-2 w-24">No Faktur</th>
                                    <th class="px-2 py-2 text-right">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-neutral-100 dark:divide-neutral-800">
                                @foreach($cart as $index => $item)
                                    <tr wire:key="cart-{{ $item['id'] }}" class="hover:bg-neutral-50 dark:hover:bg-neutral-800/50">
                                        <td class="px-2 py-2 text-center">
                                            <button type="button" wire:click="removeObat({{ $index }})" class="text-red-500 hover:text-red-700 p-1 bg-red-50 hover:bg-red-100 rounded">
                                                <flux:icon name="trash" class="w-3.5 h-3.5" />
                                            </button>
                                        </td>
                                        <td class="px-2 py-2">
                                            <div class="font-bold text-neutral-700 dark:text-neutral-200 truncate w-32" title="{{ $item['nama_brng'] }}">{{ $item['nama_brng'] }}</div>
                                            <div class="text-[10px] text-neutral-400 font-mono">{{ $item['kode_brng'] }}</div>
                                        </td>
                                        <td class="px-2 py-2 text-right">{{ number_format($item['h_jual'], 0, ',', '.') }}</td>
                                        <td class="px-2 py-2 text-center">
                                            <input type="number" step="any" wire:model.live.debounce.300ms="cart.{{ $index }}.jml" class="w-16 text-center bg-white dark:bg-neutral-900 border border-neutral-300 dark:border-neutral-700 rounded-md py-1 px-1.5 text-xs font-semibold focus:border-[#4C5C2D] focus:ring-[#4C5C2D] shadow-sm">
                                        </td>
                                        <td class="px-2 py-2">
                                            <input type="number" step="any" wire:model.live.debounce.300ms="cart.{{ $index }}.dis_persen" class="w-14 text-right bg-white dark:bg-neutral-900 border border-neutral-300 dark:border-neutral-700 rounded-md py-1 px-1.5 text-xs focus:border-[#4C5C2D] focus:ring-[#4C5C2D] shadow-sm" placeholder="0">
                                        </td>
                                        <td class="px-2 py-2">
                                            <input type="number" step="any" wire:model.live.debounce.300ms="cart.{{ $index }}.dis_rp" class="w-20 text-right bg-white dark:bg-neutral-900 border border-neutral-300 dark:border-neutral-700 rounded-md py-1 px-1.5 text-xs focus:border-[#4C5C2D] focus:ring-[#4C5C2D] shadow-sm" placeholder="0">
                                        </td>
                                        <td class="px-2 py-2">
                                            <input type="number" step="any" wire:model.live.debounce.300ms="cart.{{ $index }}.tambahan" class="w-20 text-right bg-white dark:bg-neutral-900 border border-neutral-300 dark:border-neutral-700 rounded-md py-1 px-1.5 text-xs focus:border-[#4C5C2D] focus:ring-[#4C5C2D] shadow-sm" placeholder="0">
                                        </td>
                                        <td class="px-2 py-2">
                                            <input type="text" wire:model.live.debounce.300ms="cart.{{ $index }}.aturan_pakai" class="w-36 bg-white dark:bg-neutral-900 border border-neutral-300 dark:border-neutral-700 rounded-md py-1 px-2 text-xs focus:border-[#4C5C2D] focus:ring-[#4C5C2D] shadow-sm" placeholder="Aturan pakai...">
                                        </td>
                                        <td class="px-2 py-2">
                                            <input type="text" wire:model.live.debounce.300ms="cart.{{ $index }}.no_batch" class="w-28 bg-white dark:bg-neutral-900 border border-neutral-300 dark:border-neutral-700 rounded-md py-1 px-2 text-xs focus:border-[#4C5C2D] focus:ring-[#4C5C2D] shadow-sm font-mono" placeholder="No. Batch">
                                        </td>
                                        <td class="px-2 py-2">
                                            <input type="text" wire:model.live.debounce.300ms="cart.{{ $index }}.no_faktur" class="w-28 bg-white dark:bg-neutral-900 border border-neutral-300 dark:border-neutral-700 rounded-md py-1 px-2 text-xs focus:border-[#4C5C2D] focus:ring-[#4C5C2D] shadow-sm font-mono" placeholder="No. Faktur">
                                        </td>
                                        <td class="px-2 py-2 text-right font-bold text-[#4C5C2D] dark:text-[#8CC7C4]">
                                            @php
                                                $sub = ($item['jml'] * $item['h_jual']) - $item['dis_rp'] + $item['tambahan'];
                                            @endphp
                                            {{ number_format($sub, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>

    </div>

    {{-- ========================================== --}}
    {{-- MODAL PENCARIAN --}}
    {{-- ========================================== --}}

    {{-- Modal Pasien --}}
    <div x-data="{ open: @entangle('isPasienModalOpen') }">
        <div x-show="open" x-cloak class="fixed inset-0 z-[99]">
            <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="open = false"></div>
            <div class="relative z-10 flex min-h-full items-center justify-center p-4">
                <div class="bg-white dark:bg-neutral-800 rounded-2xl shadow-2xl w-full max-w-2xl flex flex-col overflow-hidden max-h-[85vh]" @click.stop>
                    <div class="px-5 py-3 border-b border-neutral-200 flex items-center justify-between bg-[#F1F5E9] dark:bg-[#4C5C2D]/30">
                        <h3 class="font-bold text-sm text-[#4C5C2D] dark:text-[#8CC7C4] tracking-wider">:::[ Pasien ]:::</h3>
                        <button type="button" @click="open = false" class="text-neutral-400 hover:text-red-500 transition-colors"><flux:icon name="x-mark" class="w-5 h-5"/></button>
                    </div>
                    <div class="p-3 border-b border-neutral-100 dark:border-neutral-700">
                        <flux:input wire:model.live.debounce.300ms="searchPasienModal" placeholder="Cari No RM atau Nama..." icon="magnifying-glass" />
                    </div>
                    <div class="flex-1 overflow-y-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-[#4C5C2D] text-white sticky top-0 z-10 text-xs uppercase">
                                <tr>
                                    <th class="px-4 py-2.5 font-semibold w-36">No RM</th>
                                    <th class="px-4 py-2.5 font-semibold">Nama Pasien</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-neutral-100 dark:divide-neutral-700">
                                @forelse($listPasien as $p)
                                    <tr wire:key="pasien-{{ $p['no_rkm_medis'] }}"
                                        wire:click="selectPasien('{{ $p['no_rkm_medis'] }}', '{{ addslashes($p['nm_pasien']) }}')"
                                        class="cursor-pointer hover:bg-[#F1F5E9] dark:hover:bg-[#4C5C2D]/20 transition-colors {{ $loop->even ? 'bg-neutral-50 dark:bg-neutral-900/30' : 'bg-white dark:bg-neutral-800' }}">
                                        <td class="px-4 py-2.5 font-mono font-bold text-[#4C5C2D] dark:text-[#8CC7C4]">{{ $p['no_rkm_medis'] }}</td>
                                        <td class="px-4 py-2.5 text-neutral-800 dark:text-neutral-200 font-medium">{{ $p['nm_pasien'] }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="2" class="px-4 py-8 text-center text-neutral-400">Pasien tidak ditemukan.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Petugas --}}
    <div x-data="{ open: @entangle('isPetugasModalOpen') }">
        <div x-show="open" x-cloak class="fixed inset-0 z-[99]">
            <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="open = false"></div>
            <div class="relative z-10 flex min-h-full items-center justify-center p-4">
                <div class="bg-white dark:bg-neutral-800 rounded-2xl shadow-2xl w-full max-w-2xl flex flex-col overflow-hidden max-h-[85vh]" @click.stop>
                    <div class="px-5 py-3 border-b border-neutral-200 flex items-center justify-between bg-[#F1F5E9] dark:bg-[#4C5C2D]/30">
                        <h3 class="font-bold text-sm text-[#4C5C2D] dark:text-[#8CC7C4] tracking-wider">:::[ Petugas ]:::</h3>
                        <button type="button" @click="open = false" class="text-neutral-400 hover:text-red-500 transition-colors"><flux:icon name="x-mark" class="w-5 h-5"/></button>
                    </div>
                    <div class="p-3 border-b border-neutral-100 dark:border-neutral-700">
                        <flux:input wire:model.live.debounce.300ms="searchPetugasModal" placeholder="Cari NIP atau Nama..." icon="magnifying-glass" />
                    </div>
                    <div class="flex-1 overflow-y-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-[#4C5C2D] text-white sticky top-0 z-10 text-xs uppercase">
                                <tr>
                                    <th class="px-4 py-2.5 font-semibold w-40">NIP</th>
                                    <th class="px-4 py-2.5 font-semibold">Nama Petugas</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-neutral-100 dark:divide-neutral-700">
                                @forelse($listPetugas as $p)
                                    <tr wire:key="petugas-{{ $p['nip'] }}"
                                        wire:click="selectPetugas('{{ $p['nip'] }}', '{{ addslashes($p['nama']) }}')"
                                        class="cursor-pointer hover:bg-[#F1F5E9] dark:hover:bg-[#4C5C2D]/20 transition-colors {{ $loop->even ? 'bg-neutral-50 dark:bg-neutral-900/30' : 'bg-white dark:bg-neutral-800' }}">
                                        <td class="px-4 py-2.5 font-mono font-bold text-[#4C5C2D] dark:text-[#8CC7C4]">{{ $p['nip'] }}</td>
                                        <td class="px-4 py-2.5 text-neutral-800 dark:text-neutral-200 font-medium">{{ $p['nama'] }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="2" class="px-4 py-8 text-center text-neutral-400">Petugas tidak ditemukan.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Bangsal --}}
    <div x-data="{ open: @entangle('isBangsalModalOpen') }">
        <div x-show="open" x-cloak class="fixed inset-0 z-[99]">
            <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="open = false"></div>
            <div class="relative z-10 flex min-h-full items-center justify-center p-4">
                <div class="bg-white dark:bg-neutral-800 rounded-2xl shadow-2xl w-full max-w-2xl flex flex-col overflow-hidden max-h-[85vh]" @click.stop>
                    {{-- Header --}}
                    <div class="px-5 py-3 border-b border-neutral-200 flex items-center justify-between bg-[#F1F5E9] dark:bg-[#4C5C2D]/30">
                        <h3 class="font-bold text-sm text-[#4C5C2D] dark:text-[#8CC7C4] tracking-wider">:::[ Lokasi / Bangsal ]:::</h3>
                        <button type="button" @click="open = false" class="text-neutral-400 hover:text-red-500 transition-colors">
                            <flux:icon name="x-mark" class="w-5 h-5"/>
                        </button>
                    </div>
                    {{-- Search --}}
                    <div class="p-3 border-b border-neutral-100 dark:border-neutral-700">
                        <flux:input wire:model.live.debounce.300ms="searchBangsalModal" placeholder="Cari Kode atau Nama Lokasi..." icon="magnifying-glass" />
                    </div>
                    {{-- Table --}}
                    <div class="flex-1 overflow-y-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-[#4C5C2D] text-white sticky top-0 z-10 text-xs uppercase">
                                <tr>
                                    <th class="px-4 py-2.5 font-semibold w-40">Kode Bangsal</th>
                                    <th class="px-4 py-2.5 font-semibold">Nama Bangsal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-neutral-100 dark:divide-neutral-700">
                                @forelse($listBangsal as $p)
                                    <tr wire:key="bangsal-{{ $p['kd_bangsal'] }}"
                                        wire:click="selectBangsal('{{ $p['kd_bangsal'] }}', '{{ addslashes($p['nm_bangsal']) }}')"
                                        class="cursor-pointer hover:bg-[#F1F5E9] dark:hover:bg-[#4C5C2D]/20 transition-colors {{ $loop->even ? 'bg-neutral-50 dark:bg-neutral-900/30' : 'bg-white dark:bg-neutral-800' }}">
                                        <td class="px-4 py-2.5 font-mono font-bold text-[#4C5C2D] dark:text-[#8CC7C4]">{{ $p['kd_bangsal'] }}</td>
                                        <td class="px-4 py-2.5 text-neutral-800 dark:text-neutral-200 font-medium">{{ $p['nm_bangsal'] }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="px-4 py-8 text-center text-neutral-400">
                                            Tidak ada lokasi ditemukan.
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
