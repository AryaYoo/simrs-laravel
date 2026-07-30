<div class="flex flex-col gap-6 pb-8">

    {{-- Header & Breadcrumb --}}
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center gap-3">
            <a href="{{ route('modul.farmasi.input-penjualan') }}" wire:navigate
                class="flex items-center justify-center w-9 h-9 rounded-lg bg-[#4C5C2D] text-white hover:bg-[#3d4b24] transition-colors shadow-sm"
                title="Kembali ke Daftar Penjualan">
                <flux:icon name="chevron-left" class="w-5 h-5" />
            </a>
            <div>
                <nav class="text-xs text-neutral-400 mb-0.5">
                    <a href="{{ route('modul.index') }}" wire:navigate class="hover:underline">Modul</a>
                    <span class="mx-1">/</span>
                    <span>Farmasi</span>
                    <span class="mx-1">/</span>
                    <a href="{{ route('modul.farmasi.input-penjualan') }}" wire:navigate class="hover:underline">Penjualan Obat & BHP</a>
                    <span class="mx-1">/</span>
                    <span>Detail Nota</span>
                </nav>
                <h1 class="text-xl font-bold text-neutral-800 dark:text-neutral-100 flex items-center gap-2">
                    Detail Penjualan Obat & BHP
                    <span class="font-mono text-sm px-2.5 py-0.5 rounded-md bg-[#F1F5E9] text-[#4C5C2D] border border-[#4C5C2D]/20">
                        {{ $notaJual }}
                    </span>
                </h1>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('modul.farmasi.input-penjualan') }}" wire:navigate
                class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-sm font-medium border border-neutral-200 dark:border-neutral-700 text-neutral-600 dark:text-neutral-300 bg-white dark:bg-neutral-800 hover:bg-neutral-50 dark:hover:bg-neutral-700 transition-colors">
                <flux:icon name="arrow-left" class="w-4 h-4" />
                Kembali
            </a>
        </div>
    </div>

    @if($detailData && isset($detailData['penjualan']))
        @php $penjualan = $detailData['penjualan']; @endphp

        {{-- Information Banner / Cards --}}
        <div class="bg-white dark:bg-neutral-800 rounded-xl border border-neutral-200 dark:border-neutral-700 p-5 shadow-sm">
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4 text-xs">
                
                <div class="flex flex-col gap-0.5">
                    <span class="text-neutral-400 font-medium">No. Nota</span>
                    <span class="font-mono font-bold text-sm text-[#4C5C2D] dark:text-[#8CC7C4]">{{ $penjualan->nota_jual }}</span>
                </div>

                <div class="flex flex-col gap-0.5">
                    <span class="text-neutral-400 font-medium">Tanggal Transaksi</span>
                    <span class="font-semibold text-neutral-800 dark:text-neutral-100">
                        {{ $penjualan->tgl_jual?->format('d/m/Y') ?? '-' }}
                    </span>
                </div>

                <div class="flex flex-col gap-0.5">
                    <span class="text-neutral-400 font-medium">Nama Pasien</span>
                    <span class="font-semibold text-neutral-800 dark:text-neutral-100">{{ $penjualan->nm_pasien ?: '-' }}</span>
                    @if($penjualan->no_rkm_medis && $penjualan->no_rkm_medis !== '-')
                        <span class="text-[10px] text-neutral-400 font-mono">No. RM: {{ $penjualan->no_rkm_medis }}</span>
                    @endif
                </div>

                <div class="flex flex-col gap-0.5">
                    <span class="text-neutral-400 font-medium">Petugas</span>
                    <span class="font-semibold text-neutral-800 dark:text-neutral-100">{{ $penjualan->petugas->nama ?? $penjualan->nip ?? '-' }}</span>
                </div>

                <div class="flex flex-col gap-0.5">
                    <span class="text-neutral-400 font-medium">Jenis Jual</span>
                    <div>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold {{ $penjualan->jnsJualBadgeClass() }}">
                            {{ $penjualan->jnsJualLabel() }}
                        </span>
                    </div>
                </div>

                <div class="flex flex-col gap-0.5">
                    <span class="text-neutral-400 font-medium">Status Pembayaran</span>
                    <div>
                        @if($penjualan->isSudahDibayar())
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700">
                                <flux:icon name="check-circle" class="w-3 h-3" />
                                {{ $penjualan->status }}
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-100 text-red-600">
                                <flux:icon name="clock" class="w-3 h-3" />
                                {{ $penjualan->status }}
                            </span>
                        @endif
                    </div>
                </div>

                <div class="flex flex-col gap-0.5">
                    <span class="text-neutral-400 font-medium">Asal Stok</span>
                    <span class="font-semibold text-neutral-800 dark:text-neutral-100">{{ $penjualan->bangsal->nm_bangsal ?? $penjualan->kd_bangsal ?? '-' }}</span>
                </div>

                <div class="flex flex-col gap-0.5">
                    <span class="text-neutral-400 font-medium">Akun Bayar</span>
                    <span class="font-semibold text-neutral-800 dark:text-neutral-100">{{ $penjualan->nama_bayar ?? '-' }}</span>
                </div>

                <div class="flex flex-col gap-0.5 sm:col-span-2">
                    <span class="text-neutral-400 font-medium">Keterangan</span>
                    <span class="font-semibold text-neutral-800 dark:text-neutral-100">{{ $penjualan->keterangan ?: '-' }}</span>
                </div>

                <div class="flex flex-col gap-0.5">
                    <span class="text-neutral-400 font-medium">PPN</span>
                    <span class="font-mono font-semibold text-neutral-800 dark:text-neutral-100">Rp {{ number_format($penjualan->ppn ?? 0, 0, ',', '.') }}</span>
                </div>

                <div class="flex flex-col gap-0.5">
                    <span class="text-neutral-400 font-medium">Ongkos Kirim</span>
                    <span class="font-mono font-semibold text-neutral-800 dark:text-neutral-100">Rp {{ number_format($penjualan->ongkir ?? 0, 0, ',', '.') }}</span>
                </div>

            </div>
        </div>

        {{-- Tabel Detail Item (Sesuai Gambar 2) --}}
        <div class="bg-white dark:bg-neutral-800 rounded-xl border border-neutral-200 dark:border-neutral-700 overflow-hidden shadow-sm">
            <div class="px-4 py-3 border-b border-neutral-100 dark:border-neutral-700 flex items-center justify-between">
                <h2 class="text-sm font-bold text-neutral-800 dark:text-neutral-100 flex items-center gap-2">
                    <flux:icon name="clipboard-document-list" class="w-4 h-4 text-[#4C5C2D]" />
                    Daftar Obat & BHP yang Dijual
                </h2>
                <span class="text-xs text-neutral-400">{{ count($detailData['items']) }} item</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left border-collapse">
                    <thead>
                        <tr class="bg-neutral-100 dark:bg-neutral-700/60 text-neutral-700 dark:text-neutral-200 border-b border-neutral-200 dark:border-neutral-600 font-semibold">
                            <th class="px-2.5 py-2.5 text-center border-r border-neutral-200 dark:border-neutral-700 whitespace-nowrap">No.</th>
                            <th class="px-2.5 py-2.5 border-r border-neutral-200 dark:border-neutral-700 whitespace-nowrap">Kode Barang</th>
                            <th class="px-2.5 py-2.5 border-r border-neutral-200 dark:border-neutral-700 whitespace-nowrap">Nama Barang</th>
                            <th class="px-2.5 py-2.5 text-center border-r border-neutral-200 dark:border-neutral-700 whitespace-nowrap">Jml</th>
                            <th class="px-2.5 py-2.5 text-center border-r border-neutral-200 dark:border-neutral-700 whitespace-nowrap">Satuan</th>
                            <th class="px-2.5 py-2.5 text-right border-r border-neutral-200 dark:border-neutral-700 whitespace-nowrap">Harga (Rp)</th>
                            <th class="px-2.5 py-2.5 text-right border-r border-neutral-200 dark:border-neutral-700 whitespace-nowrap">Subtotal (Rp)</th>
                            <th class="px-2.5 py-2.5 text-right border-r border-neutral-200 dark:border-neutral-700 whitespace-nowrap">Ptg (%)</th>
                            <th class="px-2.5 py-2.5 text-right border-r border-neutral-200 dark:border-neutral-700 whitespace-nowrap">Potongan (Rp)</th>
                            <th class="px-2.5 py-2.5 text-right border-r border-neutral-200 dark:border-neutral-700 whitespace-nowrap">Tambahan (Rp)</th>
                            <th class="px-2.5 py-2.5 text-right border-r border-neutral-200 dark:border-neutral-700 whitespace-nowrap">Embalase (Rp)</th>
                            <th class="px-2.5 py-2.5 text-right border-r border-neutral-200 dark:border-neutral-700 whitespace-nowrap">Tuslah (Rp)</th>
                            <th class="px-2.5 py-2.5 text-right border-r border-neutral-200 dark:border-neutral-700 whitespace-nowrap">Total (Rp)</th>
                            <th class="px-2.5 py-2.5 border-r border-neutral-200 dark:border-neutral-700 whitespace-nowrap">Aturan Pakai</th>
                            <th class="px-2.5 py-2.5 whitespace-nowrap">No. Batch</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-100 dark:divide-neutral-700/50 font-mono text-[11px]">
                        @forelse($detailData['items'] as $index => $item)
                        <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-700/30 transition-colors">
                            <td class="px-2.5 py-2 text-center text-neutral-400 border-r border-neutral-100 dark:border-neutral-700">{{ $index + 1 }}</td>
                            <td class="px-2.5 py-2 font-semibold text-neutral-700 dark:text-neutral-300 border-r border-neutral-100 dark:border-neutral-700">{{ $item->kode_brng }}</td>
                            <td class="px-2.5 py-2 font-sans font-medium text-neutral-800 dark:text-neutral-200 border-r border-neutral-100 dark:border-neutral-700 min-w-[170px]">
                                {{ $item->namaBarang() }}
                            </td>
                            <td class="px-2.5 py-2 text-center font-bold border-r border-neutral-100 dark:border-neutral-700">{{ $item->jumlah }}</td>
                            <td class="px-2.5 py-2 text-center font-sans text-neutral-600 dark:text-neutral-400 border-r border-neutral-100 dark:border-neutral-700">{{ $item->namaSatuan() }}</td>
                            <td class="px-2.5 py-2 text-right border-r border-neutral-100 dark:border-neutral-700">{{ number_format($item->h_jual ?? 0, 0, ',', '.') }}</td>
                            <td class="px-2.5 py-2 text-right border-r border-neutral-100 dark:border-neutral-700">{{ number_format($item->subtotal ?? 0, 0, ',', '.') }}</td>
                            <td class="px-2.5 py-2 text-right border-r border-neutral-100 dark:border-neutral-700">{{ $item->dis ?? 0 }}</td>
                            <td class="px-2.5 py-2 text-right border-r border-neutral-100 dark:border-neutral-700">{{ number_format($item->bsr_dis ?? 0, 0, ',', '.') }}</td>
                            <td class="px-2.5 py-2 text-right border-r border-neutral-100 dark:border-neutral-700">{{ number_format($item->tambahan ?? 0, 0, ',', '.') }}</td>
                            <td class="px-2.5 py-2 text-right border-r border-neutral-100 dark:border-neutral-700">{{ number_format($item->embalase ?? 0, 0, ',', '.') }}</td>
                            <td class="px-2.5 py-2 text-right border-r border-neutral-100 dark:border-neutral-700">{{ number_format($item->tuslah ?? 0, 0, ',', '.') }}</td>
                            <td class="px-2.5 py-2 text-right font-bold text-neutral-900 dark:text-neutral-100 border-r border-neutral-100 dark:border-neutral-700">{{ number_format($item->total ?? 0, 0, ',', '.') }}</td>
                            <td class="px-2.5 py-2 font-sans text-neutral-600 dark:text-neutral-400 border-r border-neutral-100 dark:border-neutral-700 min-w-[130px]">{{ $item->aturan_pakai ?: '-' }}</td>
                            <td class="px-2.5 py-2 text-neutral-600 dark:text-neutral-400">{{ $item->no_batch ?: '-' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="15" class="px-4 py-8 text-center text-neutral-400">
                                Tidak ada detail obat/BHP dalam nota ini.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>

                    {{-- Baris Total Sesuai Gambar 2 --}}
                    <tfoot>
                        <tr class="bg-neutral-100/80 dark:bg-neutral-700/80 font-bold border-t-2 border-neutral-300 dark:border-neutral-600 text-[11px] font-mono">
                            <td colspan="6" class="px-3 py-2.5 text-left font-sans text-xs">
                                Status : <span class="{{ $penjualan->status === 'Sudah Dibayar' ? 'text-emerald-600' : 'text-red-500' }}">{{ $penjualan->status }}</span>
                            </td>
                            <td class="px-2.5 py-2.5 text-right text-neutral-800 dark:text-neutral-200">{{ number_format($detailData['sumSubtotal'], 0, ',', '.') }}</td>
                            <td class="px-2.5 py-2.5 text-right">0</td>
                            <td class="px-2.5 py-2.5 text-right text-neutral-800 dark:text-neutral-200">{{ number_format($detailData['sumDiskon'], 0, ',', '.') }}</td>
                            <td class="px-2.5 py-2.5 text-right text-neutral-800 dark:text-neutral-200">{{ number_format($detailData['sumTambahan'], 0, ',', '.') }}</td>
                            <td class="px-2.5 py-2.5 text-right text-neutral-800 dark:text-neutral-200">{{ number_format($detailData['sumEmbalase'], 0, ',', '.') }}</td>
                            <td class="px-2.5 py-2.5 text-right text-neutral-800 dark:text-neutral-200">{{ number_format($detailData['sumTuslah'], 0, ',', '.') }}</td>
                            <td class="px-2.5 py-2.5 text-right text-[#4C5C2D] dark:text-[#8CC7C4] text-xs">{{ number_format($detailData['sumTotal'], 0, ',', '.') }}</td>
                            <td colspan="2" class="px-2.5 py-2.5"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            {{-- Footer Ringkasan Angka (Sesuai Gambar 2) --}}
            <div class="px-4 py-3 bg-neutral-50 dark:bg-neutral-800/80 border-t border-neutral-200 dark:border-neutral-700 flex items-center justify-between flex-wrap gap-4 text-xs font-mono">
                <div class="flex items-center gap-4 flex-wrap text-neutral-600 dark:text-neutral-300">
                    <span>Jml.Subtotal : <strong class="text-neutral-800 dark:text-neutral-100">{{ number_format($detailData['sumSubtotal'], 0, ',', '.') }}</strong></span>
                    <span>Jml.Diskon : <strong class="text-neutral-800 dark:text-neutral-100">{{ number_format($detailData['sumDiskon'], 0, ',', '.') }}</strong></span>
                    <span>Jml.Tambahan : <strong class="text-neutral-800 dark:text-neutral-100">{{ number_format($detailData['sumTambahan'], 0, ',', '.') }}</strong></span>
                    <span>Jml.Embalase : <strong class="text-neutral-800 dark:text-neutral-100">{{ number_format($detailData['sumEmbalase'], 0, ',', '.') }}</strong></span>
                    <span>Jml.Tuslah : <strong class="text-neutral-800 dark:text-neutral-100">{{ number_format($detailData['sumTuslah'], 0, ',', '.') }}</strong></span>
                </div>
                <div class="text-right">
                    <span class="text-sm font-bold text-[#4C5C2D] dark:text-[#8CC7C4]">
                        Jml.Total : {{ number_format($detailData['grandTotal'], 0, ',', '.') }}
                    </span>
                </div>
            </div>
        </div>

    @endif

</div>
