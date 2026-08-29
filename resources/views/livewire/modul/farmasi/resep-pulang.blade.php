<div class="flex flex-col gap-6 pb-8"
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
                <span>Resep Pulang</span>
            </nav>
            <h1 class="text-xl font-bold text-neutral-800 dark:text-neutral-100">Resep Pulang</h1>
        </div>
    </div>

    {{-- Filter & Search Bar --}}
    <div class="bg-white dark:bg-neutral-800 rounded-xl border border-neutral-200 dark:border-neutral-700 p-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3">
            {{-- Search --}}
            <div class="relative lg:col-span-5">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <flux:icon name="magnifying-glass" class="w-4 h-4 text-neutral-400" />
                </div>
                <input type="text" wire:model.live.debounce.300ms="search"
                    class="block w-full pl-9 pr-3 py-2 text-sm rounded-lg border border-neutral-200 dark:border-neutral-700
                           bg-white dark:bg-neutral-800 text-neutral-900 dark:text-neutral-100
                           placeholder-neutral-400 focus:outline-none focus:ring-2 focus:ring-[#4C5C2D]/30 focus:border-[#4C5C2D] transition-colors"
                    placeholder="Cari Pasien, No Rawat, No RM, Nama Obat, Dosis..." />
            </div>

            {{-- Filter Tanggal --}}
            <div class="flex items-center gap-2 lg:col-span-4">
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
            <div class="lg:col-span-2">
                <select wire:model.live="perPage"
                    class="block w-full py-2 px-3 text-sm rounded-lg border border-neutral-200 dark:border-neutral-700
                           bg-white dark:bg-neutral-800 text-neutral-900 dark:text-neutral-100
                           focus:outline-none focus:ring-2 focus:ring-[#4C5C2D]/30 focus:border-[#4C5C2D] transition-colors">
                    <option value="15">15 / halaman</option>
                    <option value="30">30 / halaman</option>
                    <option value="50">50 / halaman</option>
                    <option value="100">100 / halaman</option>
                </select>
            </div>

            {{-- Reset --}}
            <div class="lg:col-span-1">
                <button type="button" wire:click="resetFilter"
                    class="inline-flex items-center justify-center gap-1.5 w-full py-2 px-3 text-sm font-medium rounded-lg border border-neutral-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-900/50 hover:bg-neutral-100 dark:hover:bg-neutral-700/50 text-neutral-700 dark:text-neutral-200 transition-colors shadow-sm"
                    title="Reset Filter">
                    <flux:icon name="arrow-path" class="w-4 h-4 text-neutral-500" />
                </button>
            </div>
        </div>
    </div>

    {{-- Tabel Data Resep Pulang --}}
    <div class="bg-white dark:bg-neutral-800 rounded-xl border border-neutral-200 dark:border-neutral-700 shadow-sm overflow-hidden flex-1 flex flex-col">
        <div class="overflow-x-auto flex-1">
            <table class="w-full text-left text-neutral-600 dark:text-neutral-300" style="font-size: 0.72rem;">
                <thead class="bg-neutral-50 dark:bg-neutral-900/50 text-neutral-700 dark:text-neutral-200 border-b border-neutral-200 dark:border-neutral-700 sticky top-0 z-10">
                    <tr>
                        <th class="px-2 py-2 font-bold whitespace-nowrap text-center w-[75px]">Action</th>
                        <th class="px-2.5 py-2 font-semibold min-w-[190px]">Pasien / No. Rawat / No. RM</th>
                        <th class="px-2.5 py-2 font-semibold min-w-[160px]">Nama Obat / Barang</th>
                        <th class="px-2.5 py-2 font-semibold whitespace-nowrap">Tanggal & Jam</th>
                        <th class="px-2.5 py-2 font-semibold whitespace-nowrap text-right">Jumlah</th>
                        <th class="px-2.5 py-2 font-semibold whitespace-nowrap text-right">Harga</th>
                        <th class="px-2.5 py-2 font-semibold whitespace-nowrap text-right">Total</th>
                        <th class="px-2.5 py-2 font-semibold min-w-[130px]">Dosis</th>
                        <th class="px-2.5 py-2 font-semibold whitespace-nowrap w-24">No. Batch</th>
                        <th class="px-2.5 py-2 font-semibold whitespace-nowrap w-24">No. Faktur</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                    @forelse($resepPulangList as $item)
                        @php
                            $rowKey = md5($item->no_rawat . $item->kode_brng . $item->tanggal . $item->jam . $item->no_batch . $item->no_faktur);
                            $cetakUrl = route('modul.farmasi.resep-pulang.cetak-aturan-pakai', [
                                'no_rawat'  => $item->no_rawat,
                                'kode_brng' => $item->kode_brng,
                                'tanggal'   => $item->tanggal,
                                'jam'       => $item->jam,
                                'no_batch'  => $item->no_batch,
                                'no_faktur' => $item->no_faktur,
                            ]);
                        @endphp
                        <tr wire:key="resep-{{ $rowKey }}" class="hover:bg-neutral-50 dark:hover:bg-neutral-800/50 transition-colors">
                            {{-- Action --}}
                            <td class="px-2 py-1.5 text-center align-middle whitespace-nowrap">
                                <div class="flex items-center justify-center gap-1">
                                    {{-- Tombol Cetak Aturan Pakai --}}
                                    <a href="{{ $cetakUrl }}" target="_blank"
                                        class="text-emerald-600 hover:text-emerald-800 dark:text-emerald-400 dark:hover:text-emerald-300 p-1 rounded hover:bg-emerald-50 dark:hover:bg-emerald-500/10 transition-colors"
                                        title="Cetak Aturan Pakai (Etiket)">
                                        <flux:icon name="printer" class="w-3.5 h-3.5" />
                                    </a>

                                    {{-- Tombol Hapus --}}
                                    <button type="button"
                                        @click="
                                            Swal.fire({
                                                title: 'Hapus Resep Pulang?',
                                                text: 'Data resep obat pulang ini akan dihapus permanen.',
                                                icon: 'warning',
                                                showCancelButton: true,
                                                confirmButtonColor: '#dc2626',
                                                cancelButtonColor: '#6b7280',
                                                confirmButtonText: 'Ya, Hapus!',
                                                cancelButtonText: 'Batal'
                                            }).then((result) => {
                                                if (result.isConfirmed) {
                                                    $wire.deleteResepPulang(
                                                        '{{ $item->no_rawat }}',
                                                        '{{ $item->kode_brng }}',
                                                        '{{ $item->tanggal }}',
                                                        '{{ $item->jam }}',
                                                        '{{ addslashes($item->no_batch) }}',
                                                        '{{ addslashes($item->no_faktur) }}'
                                                    );
                                                }
                                            });
                                        "
                                        class="text-red-500 hover:text-red-700 dark:hover:text-red-400 p-1 rounded hover:bg-red-50 dark:hover:bg-red-500/10 transition-colors"
                                        title="Hapus Resep Pulang">
                                        <flux:icon name="trash" class="w-3.5 h-3.5" />
                                    </button>
                                </div>
                            </td>

                            {{-- Pasien, No. Rawat, No. RM --}}
                            <td class="px-2.5 py-1.5 align-middle">
                                <div class="font-bold text-neutral-800 dark:text-neutral-100">
                                    {{ $item->regPeriksa->pasien->nm_pasien ?? 'PASIEN TIDAK DIKETAHUI' }}
                                </div>
                                <div class="flex items-center gap-2 text-[10px] text-neutral-400 dark:text-neutral-500 font-mono">
                                    <span>{{ $item->no_rawat }}</span>
                                    <span>•</span>
                                    <span>RM: {{ $item->regPeriksa->no_rkm_medis ?? '-' }}</span>
                                </div>
                            </td>

                            {{-- Nama Obat / Barang --}}
                            <td class="px-2.5 py-1.5 align-middle font-medium text-neutral-800 dark:text-neutral-200">
                                <div title="{{ $item->barang->nama_brng ?? $item->kode_brng }}">
                                    {{ $item->barang->nama_brng ?? '-' }}
                                </div>
                                <div class="text-[10px] font-mono text-neutral-400 dark:text-neutral-500">
                                    {{ $item->kode_brng }}
                                </div>
                            </td>

                            {{-- Tanggal Resep & Jam --}}
                            <td class="px-2.5 py-1.5 align-middle whitespace-nowrap text-neutral-700 dark:text-neutral-300">
                                <div>{{ date('d-m-Y', strtotime($item->tanggal)) }}</div>
                                <div class="text-[10px] text-neutral-400 font-mono">{{ $item->jam }}</div>
                            </td>

                            {{-- Jumlah --}}
                            <td class="px-2.5 py-1.5 align-middle whitespace-nowrap text-right font-semibold text-neutral-800 dark:text-neutral-200">
                                {{ number_format($item->jml_barang, 0, ',', '.') }}
                                <span class="text-[10px] font-normal text-neutral-400">{{ $item->barang->satuanKecil->satuan ?? '' }}</span>
                            </td>

                            {{-- Harga --}}
                            <td class="px-2.5 py-1.5 align-middle whitespace-nowrap text-right text-neutral-600 dark:text-neutral-400">
                                {{ number_format($item->harga, 0, ',', '.') }}
                            </td>

                            {{-- Total --}}
                            <td class="px-2.5 py-1.5 align-middle whitespace-nowrap text-right font-bold text-neutral-800 dark:text-neutral-100">
                                {{ number_format($item->total, 0, ',', '.') }}
                            </td>

                            {{-- Dosis --}}
                            <td class="px-2.5 py-1.5 align-middle max-w-[150px] truncate font-medium text-blue-600 dark:text-blue-400" title="{{ $item->dosis }}">
                                {{ $item->dosis ?: '-' }}
                            </td>

                            {{-- No. Batch --}}
                            <td class="px-2.5 py-1.5 align-middle whitespace-nowrap text-neutral-500 dark:text-neutral-400">
                                {{ $item->no_batch ?: '-' }}
                            </td>

                            {{-- No. Faktur --}}
                            <td class="px-2.5 py-1.5 align-middle whitespace-nowrap text-neutral-500 dark:text-neutral-400">
                                {{ $item->no_faktur ?: '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-4 py-8 text-center text-neutral-400 dark:text-neutral-500">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <flux:icon name="inbox" class="w-8 h-8 text-neutral-300 dark:text-neutral-600" />
                                    <span>Data resep pulang tidak ditemukan</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($resepPulangList->hasPages())
            <div class="px-4 py-3 border-t border-neutral-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-900/50">
                {{ $resepPulangList->links('vendor.livewire.tailwind') }}
            </div>
        @endif
    </div>
</div>
