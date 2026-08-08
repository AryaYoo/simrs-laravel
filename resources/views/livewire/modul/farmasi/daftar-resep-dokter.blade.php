<div class="flex flex-col gap-6 pb-8">
    {{-- Header & Breadcrumb --}}
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div>
            <nav class="text-xs text-neutral-400 mb-0.5">
                <a href="{{ route('modul.index') }}" wire:navigate class="hover:underline">Modul</a>
                <span class="mx-1">/</span>
                <span>Farmasi</span>
                <span class="mx-1">/</span>
                <span>Daftar Resep Dokter</span>
            </nav>
            <h1 class="text-xl font-bold text-neutral-800 dark:text-neutral-100 flex items-center gap-2">
                Daftar Resep Dokter
            </h1>
        </div>
    </div>

    {{-- Tabs Navigation --}}
    <div class="border-b border-neutral-200 dark:border-neutral-700 flex items-center gap-2">
        <button type="button"
            wire:click="setTab('ralan')"
            class="px-4 py-2.5 text-xs font-bold transition-all border-b-2 flex items-center gap-2 {{ $activeTab === 'ralan' ? 'border-[#4C5C2D] text-[#4C5C2D] dark:text-[#8CC7C4] dark:border-[#8CC7C4]' : 'border-transparent text-neutral-500 hover:text-neutral-700 dark:text-neutral-400 dark:hover:text-neutral-200' }}">
            <flux:icon name="building-storefront" class="w-4 h-4" />
            <span>Rawat Jalan</span>
            <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold {{ $activeTab === 'ralan' ? 'bg-[#4C5C2D]/10 text-[#4C5C2D] dark:bg-[#8CC7C4]/20 dark:text-[#8CC7C4]' : 'bg-neutral-100 text-neutral-600 dark:bg-neutral-800 dark:text-neutral-400' }}">
                {{ number_format($countRalan, 0, ',', '.') }}
            </span>
        </button>

        <button type="button"
            wire:click="setTab('ranap')"
            class="px-4 py-2.5 text-xs font-bold transition-all border-b-2 flex items-center gap-2 {{ $activeTab === 'ranap' ? 'border-[#4C5C2D] text-[#4C5C2D] dark:text-[#8CC7C4] dark:border-[#8CC7C4]' : 'border-transparent text-neutral-500 hover:text-neutral-700 dark:text-neutral-400 dark:hover:text-neutral-200' }}">
            <flux:icon name="building-office-2" class="w-4 h-4" />
            <span>Rawat Inap</span>
            <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-neutral-100 text-neutral-500 dark:bg-neutral-800 dark:text-neutral-400">
                0
            </span>
        </button>
    </div>

    {{-- Filter & Search Bar --}}
    <div class="bg-white dark:bg-neutral-800 rounded-xl border border-neutral-200 dark:border-neutral-700 p-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            {{-- Dari Tanggal --}}
            <div>
                <label class="text-[11px] font-semibold text-neutral-600 dark:text-neutral-400 block mb-1">Dari Tanggal</label>
                <input type="date" wire:model.live="dari" class="w-full bg-white dark:bg-neutral-900 border border-neutral-300 dark:border-neutral-700 text-neutral-800 dark:text-neutral-100 rounded-lg text-xs px-3 py-2 shadow-sm focus:border-[#4C5C2D] focus:ring-[#4C5C2D]">
            </div>

            {{-- Sampai Tanggal --}}
            <div>
                <label class="text-[11px] font-semibold text-neutral-600 dark:text-neutral-400 block mb-1">Sampai Tanggal</label>
                <input type="date" wire:model.live="sampai" class="w-full bg-white dark:bg-neutral-900 border border-neutral-300 dark:border-neutral-700 text-neutral-800 dark:text-neutral-100 rounded-lg text-xs px-3 py-2 shadow-sm focus:border-[#4C5C2D] focus:ring-[#4C5C2D]">
            </div>

            {{-- Search Bar --}}
            <div class="lg:col-span-2">
                <label class="text-[11px] font-semibold text-neutral-600 dark:text-neutral-400 block mb-1">Pencarian Resep</label>
                <div class="flex gap-2">
                    <div class="relative flex-1">
                        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari No. Resep, No. Rawat, RM, Pasien, Dokter, Poli..." class="w-full bg-white dark:bg-neutral-900 border border-neutral-300 dark:border-neutral-700 text-neutral-800 dark:text-neutral-100 rounded-lg text-xs pl-8 pr-3 py-2 shadow-sm focus:border-[#4C5C2D] focus:ring-[#4C5C2D]">
                        <flux:icon name="magnifying-glass" class="w-3.5 h-3.5 absolute left-2.5 top-2.5 text-neutral-400" />
                    </div>
                    <select wire:model.live="perPage" class="bg-white dark:bg-neutral-900 border border-neutral-300 dark:border-neutral-700 text-neutral-800 dark:text-neutral-100 rounded-lg text-xs px-3 py-2 font-semibold shadow-sm focus:border-[#4C5C2D] focus:ring-[#4C5C2D]">
                        <option value="20">20 / hal</option>
                        <option value="50">50 / hal</option>
                        <option value="100">100 / hal</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    {{-- Content Table --}}
    @if($activeTab === 'ranap')
        {{-- Rawat Inap Empty Placeholder --}}
        <div class="bg-white dark:bg-neutral-800 rounded-xl border border-neutral-200 dark:border-neutral-700 p-12 text-center flex flex-col items-center justify-center min-h-[300px]">
            <div class="w-16 h-16 rounded-2xl bg-neutral-100 dark:bg-neutral-700 text-neutral-400 flex items-center justify-center mb-3">
                <flux:icon name="building-office-2" class="w-8 h-8" />
            </div>
            <h3 class="text-base font-bold text-neutral-700 dark:text-neutral-200">Data Resep Rawat Inap</h3>
            <p class="text-xs text-neutral-400 max-w-md mt-1">Belum ada data resep dokter untuk kategori Rawat Inap.</p>
        </div>
    @else
        {{-- Rawat Jalan Compact Table --}}
        <div class="bg-white dark:bg-neutral-800 rounded-xl border border-neutral-200 dark:border-neutral-700 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead class="bg-neutral-50 dark:bg-neutral-800 text-neutral-600 dark:text-neutral-300 uppercase text-[10px] tracking-wider font-extrabold border-b border-neutral-200 dark:border-neutral-700 sticky top-0 z-10">
                        <tr>
                            <th class="px-3 py-2.5 text-center w-16">Action</th>
                            <th class="px-3 py-2.5">No. Resep</th>
                            <th class="px-3 py-2.5 whitespace-nowrap">Tgl Peresepan</th>
                            <th class="px-3 py-2.5 whitespace-nowrap">No. Rawat</th>
                            <th class="px-3 py-2.5">No. RM & Nama Pasien</th>
                            <th class="px-3 py-2.5">Dokter</th>
                            <th class="px-3 py-2.5 text-center">Status</th>
                            <th class="px-3 py-2.5">Poli</th>
                            <th class="px-3 py-2.5">Jenis Bayar</th>
                            <th class="px-3 py-2.5 whitespace-nowrap">Tgl & Jam Validasi</th>
                            <th class="px-3 py-2.5 whitespace-nowrap">Penyerahan & Jam</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-100 dark:divide-neutral-700/60">
                        @forelse($reseps as $row)
                            <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-700/40 transition-colors">
                                {{-- 1. Action (Tombol Detail) --}}
                                <td class="px-3 py-2 text-center whitespace-nowrap">
                                    <button type="button"
                                        wire:click="showDetail('{{ $row->no_resep }}')"
                                        class="inline-flex items-center justify-center w-7 h-7 rounded-md bg-[#4C5C2D]/10 hover:bg-[#4C5C2D] text-[#4C5C2D] hover:text-white transition-colors"
                                        title="Lihat Detail Item Resep">
                                        <flux:icon name="eye" class="w-3.5 h-3.5" />
                                    </button>
                                </td>

                                {{-- 2. No Resep --}}
                                <td class="px-3 py-2 font-mono font-bold text-neutral-900 dark:text-neutral-100 whitespace-nowrap">
                                    {{ $row->no_resep }}
                                </td>

                                {{-- 3. Tgl Peresepan --}}
                                <td class="px-3 py-2 whitespace-nowrap text-neutral-700 dark:text-neutral-300">
                                    <div class="font-semibold">{{ $row->tgl_peresepan ? \Carbon\Carbon::parse($row->tgl_peresepan)->format('d/m/Y') : '-' }}</div>
                                    <div class="text-[10px] text-neutral-400 font-mono">{{ $row->jam_peresepan ?? '' }}</div>
                                </td>

                                {{-- 4. No Rawat --}}
                                <td class="px-3 py-2 font-mono text-neutral-600 dark:text-neutral-400 whitespace-nowrap text-[11px]">
                                    {{ $row->no_rawat }}
                                </td>

                                {{-- 5. No RM & Nama Pasien --}}
                                <td class="px-3 py-2">
                                    <div class="font-bold text-neutral-800 dark:text-neutral-100 leading-tight">
                                        {{ $row->regPeriksa->pasien->nm_pasien ?? '-' }}
                                    </div>
                                    <div class="text-[10px] font-mono text-neutral-500 dark:text-neutral-400 mt-0.5">
                                        RM: {{ $row->regPeriksa->no_rkm_medis ?? '-' }}
                                    </div>
                                </td>

                                {{-- 6. Dokter DPJP --}}
                                <td class="px-3 py-2 text-neutral-700 dark:text-neutral-300 font-medium">
                                    {{ $row->dokter->nm_dokter ?? ($row->kd_dokter ?: '-') }}
                                </td>

                                {{-- 7. Status --}}
                                <td class="px-3 py-2 text-center whitespace-nowrap">
                                    <span class="inline-block px-2 py-0.5 rounded text-[10px] font-extrabold uppercase bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300">
                                        {{ $row->status }}
                                    </span>
                                </td>

                                {{-- 8. Poli --}}
                                <td class="px-3 py-2 text-neutral-700 dark:text-neutral-300 font-medium whitespace-nowrap">
                                    {{ $row->regPeriksa->poliklinik->nm_poli ?? '-' }}
                                </td>

                                {{-- 9. Jenis Bayar --}}
                                <td class="px-3 py-2 whitespace-nowrap">
                                    <span class="inline-block px-2 py-0.5 rounded text-[10px] font-semibold bg-neutral-100 text-neutral-700 dark:bg-neutral-700 dark:text-neutral-300">
                                        {{ $row->regPeriksa->penjab->png_jawab ?? '-' }}
                                    </span>
                                </td>

                                {{-- 10. Tgl & Jam Validasi --}}
                                <td class="px-3 py-2 whitespace-nowrap text-neutral-600 dark:text-neutral-400">
                                    @if($row->tgl_perawatan && $row->tgl_perawatan !== '0000-00-00')
                                        <div class="font-medium">{{ \Carbon\Carbon::parse($row->tgl_perawatan)->format('d/m/Y') }}</div>
                                        <div class="text-[10px] font-mono text-neutral-400">{{ $row->jam !== '00:00:00' ? $row->jam : '' }}</div>
                                    @else
                                        <span class="text-neutral-400">-</span>
                                    @endif
                                </td>

                                {{-- 11. Penyerahan & Jam Penyerahan --}}
                                <td class="px-3 py-2 whitespace-nowrap text-neutral-600 dark:text-neutral-400">
                                    @if($row->tgl_penyerahan && $row->tgl_penyerahan !== '0000-00-00')
                                        <div class="font-medium">{{ \Carbon\Carbon::parse($row->tgl_penyerahan)->format('d/m/Y') }}</div>
                                        <div class="text-[10px] font-mono text-neutral-400">{{ $row->jam_penyerahan !== '00:00:00' ? $row->jam_penyerahan : '' }}</div>
                                    @else
                                        <span class="text-neutral-400">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="px-4 py-12 text-center text-neutral-400 dark:text-neutral-500">
                                    <flux:icon name="clipboard-document-list" class="w-10 h-10 mx-auto mb-2 opacity-50" />
                                    <p class="text-xs font-semibold">Tidak ada data resep dokter yang ditemukan.</p>
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
    @endif

    {{-- Detail Modal Popup --}}
    @if($detailModalOpen && $activeResep)
        <div class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 sm:p-6" style="background-color: rgba(0,0,0,0.6); backdrop-filter: blur(4px);">
            <div class="relative w-full max-w-2xl bg-white dark:bg-neutral-900 rounded-2xl shadow-2xl border border-neutral-200 dark:border-neutral-700 overflow-hidden" @click.stop>
                
                {{-- Header --}}
                <div class="flex items-center justify-between px-6 py-4 bg-[#4C5C2D] text-white">
                    <div>
                        <h3 class="font-bold text-white text-base flex items-center gap-2">
                            <flux:icon name="clipboard-document-list" class="w-5 h-5 text-white" />
                            Detail Resep {{ $activeResep['no_resep'] }}
                        </h3>
                        <p class="text-xs text-white/80 mt-0.5">
                            Pasien: {{ $activeResep['reg_periksa']['pasien']['nm_pasien'] ?? '-' }} (RM: {{ $activeResep['reg_periksa']['no_rkm_medis'] ?? '-' }})
                        </p>
                    </div>
                    <button type="button" wire:click="closeDetailModal" class="p-1.5 rounded-lg hover:bg-white/10 text-white/80 hover:text-white transition-colors">
                        <flux:icon name="x-mark" class="w-5 h-5" />
                    </button>
                </div>

                {{-- Body: Table Item Resep --}}
                <div class="p-6 space-y-4 max-h-[65vh] overflow-y-auto">
                    <div class="grid grid-cols-2 gap-3 text-xs p-3 bg-neutral-50 dark:bg-neutral-800 rounded-lg border border-neutral-200 dark:border-neutral-700">
                        <div><span class="text-neutral-400">Dokter DPJP:</span> <strong class="text-neutral-800 dark:text-neutral-200 block">{{ $activeResep['dokter']['nm_dokter'] ?? '-' }}</strong></div>
                        <div><span class="text-neutral-400">Poliklinik:</span> <strong class="text-neutral-800 dark:text-neutral-200 block">{{ $activeResep['reg_periksa']['poliklinik']['nm_poli'] ?? '-' }}</strong></div>
                        <div><span class="text-neutral-400">No. Rawat:</span> <strong class="font-mono text-neutral-800 dark:text-neutral-200 block">{{ $activeResep['no_rawat'] }}</strong></div>
                        <div><span class="text-neutral-400">Jenis Bayar:</span> <strong class="text-neutral-800 dark:text-neutral-200 block">{{ $activeResep['reg_periksa']['penjab']['png_jawab'] ?? '-' }}</strong></div>
                    </div>

                    <h4 class="text-xs font-bold uppercase tracking-wider text-neutral-600 dark:text-neutral-400">Daftar Obat / Item Resep</h4>
                    <table class="w-full text-xs text-left border-collapse">
                        <thead class="bg-neutral-100 dark:bg-neutral-800 text-neutral-600 dark:text-neutral-400 font-bold uppercase text-[10px]">
                            <tr>
                                <th class="p-2 border border-neutral-200 dark:border-neutral-700 w-8 text-center">No</th>
                                <th class="p-2 border border-neutral-200 dark:border-neutral-700">Nama Barang / Obat</th>
                                <th class="p-2 border border-neutral-200 dark:border-neutral-700 text-center w-16">Jumlah</th>
                                <th class="p-2 border border-neutral-200 dark:border-neutral-700">Aturan Pakai</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($activeResep['detail'] ?? [] as $idx => $d)
                                <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-800/40">
                                    <td class="p-2 border border-neutral-200 dark:border-neutral-700 text-center font-mono">{{ $idx + 1 }}</td>
                                    <td class="p-2 border border-neutral-200 dark:border-neutral-700 font-medium">
                                        {{ $d['barang']['nama_brng'] ?? $d['kode_brng'] }}
                                    </td>
                                    <td class="p-2 border border-neutral-200 dark:border-neutral-700 text-center font-bold">
                                        {{ number_format($d['jml'] ?? 0, 0, ',', '.') }}
                                    </td>
                                    <td class="p-2 border border-neutral-200 dark:border-neutral-700 text-neutral-600 dark:text-neutral-300">
                                        {{ $d['aturan_pakai'] ?: '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="p-4 text-center text-neutral-400 italic">Belum ada item obat terdaftar pada resep ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Footer --}}
                <div class="px-6 py-3.5 bg-neutral-50 dark:bg-neutral-800/50 border-t border-neutral-200 dark:border-neutral-700 flex justify-end">
                    <button type="button" wire:click="closeDetailModal" class="px-4 py-1.5 rounded-lg bg-neutral-200 dark:bg-neutral-700 hover:bg-neutral-300 dark:hover:bg-neutral-600 text-neutral-700 dark:text-neutral-200 font-semibold text-xs transition-colors">
                        Tutup
                    </button>
                </div>

            </div>
        </div>
    @endif
</div>
