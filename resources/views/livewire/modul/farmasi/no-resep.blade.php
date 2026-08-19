<div class="flex flex-col gap-6 pb-8">
    {{-- Header & Breadcrumb --}}
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div>
            <nav class="text-xs text-neutral-400 mb-0.5">
                <a href="{{ route('modul.index') }}" wire:navigate class="hover:underline">Modul</a>
                <span class="mx-1">/</span>
                <span>Farmasi</span>
                <span class="mx-1">/</span>
                <span>No. Resep</span>
            </nav>
            <h1 class="text-xl font-bold text-neutral-800 dark:text-neutral-100 flex items-center gap-2">
                <flux:icon name="hashtag" class="w-5 h-5 text-[#4C5C2D]" />
                No. Resep
            </h1>
        </div>
    </div>

    {{-- Filter & Search Bar --}}
    <div class="bg-white dark:bg-neutral-800 rounded-xl border border-neutral-200 dark:border-neutral-700 p-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            {{-- Dari Tanggal --}}
            <div>
                <label class="text-[11px] font-semibold text-neutral-600 dark:text-neutral-400 block mb-1">Dari Tanggal</label>
                <input type="date" wire:model.live="dari"
                    class="w-full bg-white dark:bg-neutral-900 border border-neutral-300 dark:border-neutral-700 text-neutral-800 dark:text-neutral-100 rounded-lg text-xs px-3 py-2 shadow-sm focus:border-[#4C5C2D] focus:ring-[#4C5C2D]">
            </div>

            {{-- Sampai Tanggal --}}
            <div>
                <label class="text-[11px] font-semibold text-neutral-600 dark:text-neutral-400 block mb-1">Sampai Tanggal</label>
                <input type="date" wire:model.live="sampai"
                    class="w-full bg-white dark:bg-neutral-900 border border-neutral-300 dark:border-neutral-700 text-neutral-800 dark:text-neutral-100 rounded-lg text-xs px-3 py-2 shadow-sm focus:border-[#4C5C2D] focus:ring-[#4C5C2D]">
            </div>

            {{-- Search --}}
            <div class="lg:col-span-2">
                <label class="text-[11px] font-semibold text-neutral-600 dark:text-neutral-400 block mb-1">Pencarian Resep</label>
                <div class="flex gap-2">
                    <div class="relative flex-1">
                        <input type="text" wire:model.live.debounce.300ms="search"
                            placeholder="Cari No. Resep, No. Rawat, RM, Pasien, Dokter..."
                            class="w-full bg-white dark:bg-neutral-900 border border-neutral-300 dark:border-neutral-700 text-neutral-800 dark:text-neutral-100 rounded-lg text-xs pl-8 pr-3 py-2 shadow-sm focus:border-[#4C5C2D] focus:ring-[#4C5C2D]">
                        <flux:icon name="magnifying-glass" class="w-3.5 h-3.5 absolute left-2.5 top-2.5 text-neutral-400" />
                    </div>
                    <select wire:model.live="perPage"
                        class="bg-white dark:bg-neutral-900 border border-neutral-300 dark:border-neutral-700 text-neutral-800 dark:text-neutral-100 rounded-lg text-xs px-3 py-2 font-semibold shadow-sm focus:border-[#4C5C2D] focus:ring-[#4C5C2D]">
                        <option value="20">20 / hal</option>
                        <option value="50">50 / hal</option>
                        <option value="100">100 / hal</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white dark:bg-neutral-800 rounded-xl border border-neutral-200 dark:border-neutral-700 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead class="bg-neutral-50 dark:bg-neutral-800 text-neutral-600 dark:text-neutral-300 uppercase text-[10px] tracking-wider font-extrabold border-b border-neutral-200 dark:border-neutral-700 sticky top-0 z-10">
                    <tr>
                        <th class="px-3 py-2.5 text-center w-16">Action</th>
                        <th class="px-3 py-2.5 whitespace-nowrap">No. Resep</th>
                        <th class="px-3 py-2.5 whitespace-nowrap">Tanggal Resep</th>
                        <th class="px-3 py-2.5">Pasien</th>
                        <th class="px-3 py-2.5">Dokter Peresep</th>
                        <th class="px-3 py-2.5 text-center whitespace-nowrap">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100 dark:divide-neutral-700/60">
                    @forelse($reseps as $row)
                        <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-700/40 transition-colors">
                            {{-- Action --}}
                            <td class="px-3 py-2 text-center whitespace-nowrap">
                                <button type="button"
                                    wire:click="showDetail('{{ $row->no_resep }}')"
                                    class="inline-flex items-center justify-center w-7 h-7 rounded-md bg-[#4C5C2D]/10 hover:bg-[#4C5C2D] text-[#4C5C2D] hover:text-white transition-colors"
                                    title="Lihat Detail Resep">
                                    <flux:icon name="eye" class="w-3.5 h-3.5" />
                                </button>
                            </td>

                            {{-- No. Resep --}}
                            <td class="px-3 py-2 font-mono font-bold text-neutral-900 dark:text-neutral-100 whitespace-nowrap">
                                {{ $row->no_resep }}
                            </td>

                            {{-- Tanggal Resep --}}
                            <td class="px-3 py-2 whitespace-nowrap text-neutral-700 dark:text-neutral-300">
                                <div class="font-semibold">
                                    {{ $row->tgl_peresepan ? \Carbon\Carbon::parse($row->tgl_peresepan)->format('d/m/Y') : '-' }}
                                </div>
                                <div class="text-[10px] text-neutral-400 font-mono">{{ $row->jam_peresepan ?? '' }}</div>
                            </td>

                            {{-- Pasien --}}
                            <td class="px-3 py-2">
                                <div class="font-bold text-neutral-800 dark:text-neutral-100 leading-tight">
                                    {{ $row->regPeriksa->pasien->nm_pasien ?? '-' }}
                                </div>
                                <div class="text-[10px] font-mono text-neutral-500 dark:text-neutral-400 mt-0.5">
                                    RM: {{ $row->regPeriksa->no_rkm_medis ?? '-' }}
                                </div>
                            </td>

                            {{-- Dokter Peresep --}}
                            <td class="px-3 py-2 text-neutral-700 dark:text-neutral-300 font-medium">
                                {{ $row->dokter->nm_dokter ?? ($row->kd_dokter ?: '-') }}
                            </td>

                            {{-- Status --}}
                            <td class="px-3 py-2 text-center whitespace-nowrap">
                                @if($row->tgl_penyerahan)
                                    <span class="inline-block px-2 py-0.5 rounded text-[10px] font-extrabold uppercase bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300">
                                        Sudah Diserahkan
                                    </span>
                                @elseif($row->status === 'Sudah')
                                    <span class="inline-block px-2 py-0.5 rounded text-[10px] font-extrabold uppercase bg-sky-100 text-sky-800 dark:bg-sky-950/60 dark:text-sky-300">
                                        Sudah Divalidasi
                                    </span>
                                @else
                                    <span class="inline-block px-2 py-0.5 rounded text-[10px] font-extrabold uppercase bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300">
                                        Belum Divalidasi
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center text-neutral-400 dark:text-neutral-500">
                                <flux:icon name="document-magnifying-glass" class="w-10 h-10 mx-auto mb-2 opacity-50" />
                                <p class="text-xs font-semibold">Tidak ada data resep yang ditemukan.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($reseps->hasPages())
            <div class="px-4 py-3 border-t border-neutral-100 dark:border-neutral-700">
                {{ $reseps->links() }}
            </div>
        @endif
    </div>

    {{-- Detail Modal --}}
    @if($detailModalOpen && $activeResep)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4"
             x-data
             x-init="document.body.style.overflow='hidden'"
             x-on:keydown.escape.window="$wire.closeDetailModal()"
             @keydown.escape.window="$wire.closeDetailModal()">

            {{-- Backdrop --}}
            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"
                 wire:click="closeDetailModal"></div>

            {{-- Modal Panel --}}
            <div class="relative bg-white dark:bg-neutral-900 rounded-2xl shadow-2xl w-full max-w-3xl max-h-[90vh] flex flex-col overflow-hidden border border-neutral-200 dark:border-neutral-700">

                {{-- Modal Header --}}
                <div class="flex items-center justify-between px-6 py-4 border-b border-neutral-200 dark:border-neutral-700 flex-shrink-0">
                    <div>
                        <h2 class="text-base font-extrabold text-neutral-800 dark:text-neutral-100">
                            Detail Resep Obat
                        </h2>
                        <p class="text-xs text-neutral-500 dark:text-neutral-400 font-mono mt-0.5">
                            {{ $activeResep['no_resep'] }}
                        </p>
                    </div>
                    <button wire:click="closeDetailModal"
                        class="w-8 h-8 rounded-full flex items-center justify-center text-neutral-400 hover:bg-neutral-100 dark:hover:bg-neutral-800 hover:text-neutral-700 dark:hover:text-neutral-200 transition-colors">
                        <flux:icon name="x-mark" class="w-4 h-4" />
                    </button>
                </div>

                {{-- Patient Info Banner --}}
                <div class="px-6 py-3 bg-[#4C5C2D]/5 dark:bg-[#4C5C2D]/10 border-b border-[#4C5C2D]/20 flex-shrink-0">
                    <div class="flex flex-wrap gap-x-6 gap-y-1 text-xs">
                        <div>
                            <span class="text-neutral-500 dark:text-neutral-400">Pasien</span>
                            <span class="font-bold text-neutral-800 dark:text-neutral-100 ml-1">
                                {{ $activeResep['reg_periksa']['pasien']['nm_pasien'] ?? '-' }}
                            </span>
                        </div>
                        <div>
                            <span class="text-neutral-500 dark:text-neutral-400">No. RM</span>
                            <span class="font-mono font-semibold text-neutral-700 dark:text-neutral-300 ml-1">
                                {{ $activeResep['reg_periksa']['no_rkm_medis'] ?? '-' }}
                            </span>
                        </div>
                        <div>
                            <span class="text-neutral-500 dark:text-neutral-400">Dokter</span>
                            <span class="font-semibold text-neutral-700 dark:text-neutral-300 ml-1">
                                {{ $activeResep['dokter']['nm_dokter'] ?? '-' }}
                            </span>
                        </div>
                        <div>
                            <span class="text-neutral-500 dark:text-neutral-400">Tanggal</span>
                            <span class="font-semibold text-neutral-700 dark:text-neutral-300 ml-1">
                                {{ $activeResep['tgl_peresepan'] ? \Carbon\Carbon::parse($activeResep['tgl_peresepan'])->format('d/m/Y') : '-' }}
                                {{ $activeResep['jam_peresepan'] ?? '' }}
                            </span>
                        </div>
                        <div>
                            <span class="text-neutral-500 dark:text-neutral-400">Poli</span>
                            <span class="font-semibold text-neutral-700 dark:text-neutral-300 ml-1">
                                {{ $activeResep['reg_periksa']['poliklinik']['nm_poli'] ?? '-' }}
                            </span>
                        </div>
                        <div>
                            <span class="text-neutral-500 dark:text-neutral-400">Jenis Bayar</span>
                            <span class="font-semibold text-neutral-700 dark:text-neutral-300 ml-1">
                                {{ $activeResep['reg_periksa']['penjab']['png_jawab'] ?? '-' }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Detail Items Table --}}
                <div class="overflow-y-auto flex-1">
                    @php
                        $enrichedDetail = $activeResep['enriched_detail'] ?? [];
                        $grandTotal = collect($enrichedDetail)->sum('total');
                        $totalEmbalase = collect($enrichedDetail)->sum('embalase');
                        $totalTuslah   = collect($enrichedDetail)->sum('tuslah');
                        $hasValidation = collect($enrichedDetail)->contains(fn($d) => $d['total'] > 0);
                    @endphp

                    <table class="w-full text-left border-collapse text-xs">
                        <thead class="bg-neutral-50 dark:bg-neutral-800 text-neutral-600 dark:text-neutral-300 uppercase text-[10px] tracking-wider font-extrabold border-b border-neutral-200 dark:border-neutral-700 sticky top-0 z-10">
                            <tr>
                                <th class="px-4 py-2.5">#</th>
                                <th class="px-4 py-2.5">Nama Obat</th>
                                <th class="px-4 py-2.5 text-right whitespace-nowrap">Jml × Harga</th>
                                @if($hasValidation)
                                <th class="px-4 py-2.5 text-right">Embalase</th>
                                <th class="px-4 py-2.5 text-right">Tuslah</th>
                                <th class="px-4 py-2.5 text-right font-extrabold text-[#4C5C2D] dark:text-[#8CC7C4]">Total</th>
                                @endif
                                <th class="px-4 py-2.5 text-center">Aturan Pakai</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-100 dark:divide-neutral-700/60">
                            @forelse($enrichedDetail as $i => $item)
                                <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-700/40 transition-colors">
                                    <td class="px-4 py-2.5 text-neutral-400">{{ $i + 1 }}</td>

                                    {{-- Nama Obat --}}
                                    <td class="px-4 py-2.5 font-semibold text-neutral-800 dark:text-neutral-100 max-w-[200px]">
                                        {{ $item['nama_brng'] }}
                                    </td>

                                    {{-- Jml × Harga --}}
                                    <td class="px-4 py-2.5 text-right whitespace-nowrap text-neutral-700 dark:text-neutral-300">
                                        <span class="font-semibold">{{ number_format($item['jml'], 0, ',', '.') }}</span>
                                        @if($hasValidation && $item['h_beli'] > 0)
                                            <span class="text-neutral-400 mx-0.5">×</span>
                                            <span>Rp {{ number_format($item['h_beli'], 0, ',', '.') }}</span>
                                        @endif
                                    </td>

                                    @if($hasValidation)
                                    {{-- Embalase --}}
                                    <td class="px-4 py-2.5 text-right text-neutral-600 dark:text-neutral-400">
                                        @if($item['embalase'] > 0)
                                            Rp {{ number_format($item['embalase'], 0, ',', '.') }}
                                        @else
                                            <span class="text-neutral-300 dark:text-neutral-600">-</span>
                                        @endif
                                    </td>

                                    {{-- Tuslah --}}
                                    <td class="px-4 py-2.5 text-right text-neutral-600 dark:text-neutral-400">
                                        @if($item['tuslah'] > 0)
                                            Rp {{ number_format($item['tuslah'], 0, ',', '.') }}
                                        @else
                                            <span class="text-neutral-300 dark:text-neutral-600">-</span>
                                        @endif
                                    </td>

                                    {{-- Total --}}
                                    <td class="px-4 py-2.5 text-right font-extrabold text-[#4C5C2D] dark:text-[#8CC7C4] whitespace-nowrap">
                                        @if($item['total'] > 0)
                                            Rp {{ number_format($item['total'], 0, ',', '.') }}
                                        @else
                                            <span class="text-neutral-300 dark:text-neutral-600 font-normal">-</span>
                                        @endif
                                    </td>
                                    @endif

                                    {{-- Aturan Pakai --}}
                                    <td class="px-4 py-2.5 text-center">
                                        <span class="inline-block px-2 py-0.5 rounded bg-neutral-100 dark:bg-neutral-700 text-neutral-700 dark:text-neutral-300 font-semibold whitespace-nowrap">
                                            {{ $item['aturan_pakai'] ?: '-' }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-10 text-center text-neutral-400">
                                        <flux:icon name="inbox" class="w-8 h-8 mx-auto mb-2 opacity-40" />
                                        <p>Belum ada item dalam resep ini.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>

                        {{-- Grand Total Footer --}}
                        @if($hasValidation && count($enrichedDetail) > 0)
                            <tfoot class="bg-neutral-50 dark:bg-neutral-800 border-t-2 border-neutral-200 dark:border-neutral-700">
                                <tr>
                                    <td colspan="{{ 4 }}" class="px-4 py-2.5 text-right text-xs font-extrabold text-neutral-600 dark:text-neutral-300 uppercase tracking-wide">
                                        Grand Total
                                    </td>
                                    <td class="px-4 py-2.5 text-right text-xs font-extrabold text-[#4C5C2D] dark:text-[#8CC7C4]">
                                        Rp {{ number_format($totalEmbalase, 0, ',', '.') }}
                                    </td>
                                    <td class="px-4 py-2.5 text-right text-xs font-extrabold text-[#4C5C2D] dark:text-[#8CC7C4]">
                                        Rp {{ number_format($totalTuslah, 0, ',', '.') }}
                                    </td>
                                    <td class="px-4 py-2.5 text-right text-sm font-extrabold text-[#4C5C2D] dark:text-[#8CC7C4] whitespace-nowrap">
                                        Rp {{ number_format($grandTotal, 0, ',', '.') }}
                                    </td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>

                {{-- Modal Footer --}}
                <div class="flex justify-end px-6 py-3 border-t border-neutral-200 dark:border-neutral-700 flex-shrink-0">
                    <button wire:click="closeDetailModal"
                        class="px-4 py-2 rounded-lg text-xs font-semibold bg-neutral-100 dark:bg-neutral-800 text-neutral-700 dark:text-neutral-300 hover:bg-neutral-200 dark:hover:bg-neutral-700 transition-colors">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
