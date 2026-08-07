<div class="flex flex-col gap-6 pb-8">
    {{-- Header / Breadcrumb --}}
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('modul.index') }}" wire:navigate class="flex items-center justify-center w-10 h-8 rounded-md bg-[#4C5C2D] transition-colors hover:bg-[#3d4b24] shadow-sm">
                <flux:icon name="chevron-left" class="w-5 h-5 text-white" />
            </a>
            <div>
                <nav class="text-xs text-neutral-400 mb-0.5">
                    <a href="{{ route('modul.index') }}" wire:navigate class="hover:underline">Modul</a>
                    <span class="mx-1">/</span>
                    <span class="hover:underline text-neutral-500">Casemix</span>
                    <span class="mx-1">/</span>
                    <span class="text-neutral-700 dark:text-neutral-300 font-medium">Kustom Laporan Operasi</span>
                </nav>
                <h1 class="text-xl font-bold text-neutral-800 dark:text-neutral-100">Kustom Laporan Operasi</h1>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <flux:button wire:click="resetFilter" variant="filled" icon="arrow-path" class="!text-xs">Reset Filter</flux:button>
        </div>
    </div>

    {{-- Main Container Card --}}
    <div class="bg-white dark:bg-neutral-800 rounded-xl border border-neutral-200 dark:border-neutral-700 p-4 sm:p-5 shadow-sm">
        {{-- Filter & Search Bar --}}
        <div class="flex flex-col lg:flex-row gap-3 mb-6">
            <div class="flex-1">
                <flux:input wire:model.live.debounce.300ms="search"
                    placeholder="Cari No Rawat, No RM, Diagnosa, atau Nama Pasien..." icon="magnifying-glass" />
            </div>
            <div class="flex flex-wrap items-center gap-2 flex-shrink-0">
                <div class="flex items-center gap-2">
                    <label class="text-xs font-semibold text-neutral-500 dark:text-neutral-400 whitespace-nowrap">Dari</label>
                    <flux:input type="date" wire:model.live.debounce.500ms="dari" class="w-36" />
                    <span class="text-xs text-neutral-400">s/d</span>
                    <flux:input type="date" wire:model.live.debounce.500ms="sampai" class="w-36" />
                </div>
            </div>
        </div>

        {{-- Table Container --}}
        <div class="relative overflow-x-auto rounded-lg border border-neutral-200 dark:border-neutral-700">
            <table class="w-full text-sm text-left text-neutral-600 dark:text-neutral-300">
                <thead class="text-xs text-neutral-700 uppercase bg-neutral-50 dark:bg-neutral-900/50 dark:text-neutral-300 border-b border-neutral-200 dark:border-neutral-700">
                    <tr>
                        <th scope="col" class="px-4 py-3 font-bold">Tanggal Operasi</th>
                        <th scope="col" class="px-4 py-3 font-bold">No. Rawat</th>
                        <th scope="col" class="px-4 py-3 font-bold">Nama Pasien</th>
                        <th scope="col" class="px-4 py-3 font-bold">Diagnosa Pre Operatif</th>
                        <th scope="col" class="px-4 py-3 font-bold">Diagnosa Post Operatif</th>
                        <th scope="col" class="px-4 py-3 font-bold">Selesai Operasi</th>
                        <th scope="col" class="px-4 py-3 font-bold text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                    @forelse($laporanList as $item)
                        <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-700/50 transition-colors">
                            {{-- Tanggal Operasi --}}
                            <td class="px-4 py-3 whitespace-nowrap font-medium text-neutral-900 dark:text-neutral-100">
                                {{ date('d-m-Y H:i', strtotime($item->tanggal)) }}
                            </td>

                            {{-- No. Rawat --}}
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="flex flex-col">
                                    <span class="font-mono text-xs font-semibold text-neutral-800 dark:text-neutral-200">{{ $item->no_rawat }}</span>
                                    <span class="text-[11px] text-neutral-400">RM: {{ $item->regPeriksa->pasien->no_rkm_medis ?? '-' }}</span>
                                </div>
                            </td>

                            {{-- Nama Pasien --}}
                            <td class="px-4 py-3">
                                <div class="font-semibold text-neutral-900 dark:text-neutral-100">
                                    {{ $item->regPeriksa->pasien->nm_pasien ?? '-' }}
                                </div>
                                <div class="text-[11px] text-neutral-400">
                                    {{ ($item->regPeriksa->pasien->jk ?? '') == 'L' ? 'Laki-laki' : 'Perempuan' }}
                                </div>
                            </td>

                            {{-- Diagnosa Pre Operatif --}}
                            <td class="px-4 py-3">
                                <span class="inline-block max-w-xs truncate text-xs bg-amber-50 dark:bg-amber-950/40 text-amber-800 dark:text-amber-300 px-2 py-1 rounded border border-amber-200/60 dark:border-amber-800/40">
                                    {{ $item->diagnosa_preop ?: '-' }}
                                </span>
                            </td>

                            {{-- Diagnosa Post Operatif --}}
                            <td class="px-4 py-3">
                                <span class="inline-block max-w-xs truncate text-xs bg-emerald-50 dark:bg-emerald-950/40 text-emerald-800 dark:text-emerald-300 px-2 py-1 rounded border border-emerald-200/60 dark:border-emerald-800/40">
                                    {{ $item->diagnosa_postop ?: '-' }}
                                </span>
                            </td>

                            {{-- Selesai Operasi --}}
                            <td class="px-4 py-3 whitespace-nowrap text-xs text-neutral-600 dark:text-neutral-400">
                                {{ date('d-m-Y H:i', strtotime($item->selesaioperasi)) }}
                            </td>

                            {{-- Action --}}
                            <td class="px-4 py-3 whitespace-nowrap text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    {{-- Tombol Detail (Halaman Baru) --}}
                                    <a href="{{ route('modul.casemix.kustom-laporan-operasi.detail', [str_replace('/', '-', $item->no_rawat), str_replace(' ', '_', $item->tanggal)]) }}"
                                        wire:navigate
                                        class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-semibold bg-neutral-100 dark:bg-neutral-700 text-neutral-700 dark:text-neutral-200 hover:bg-[#4C5C2D] hover:text-white dark:hover:bg-[#4C5C2D] transition-all shadow-sm">
                                        <flux:icon name="eye" class="w-3.5 h-3.5" />
                                        <span>Detail</span>
                                    </a>

                                    {{-- Tombol Cetak --}}
                                    <a href="{{ route('modul.casemix.kustom-laporan-operasi.kustom-cetak', [str_replace('/', '-', $item->no_rawat), str_replace(' ', '_', $item->tanggal)]) }}"
                                        wire:navigate
                                        class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-semibold bg-[#4C5C2D] text-white hover:bg-[#3d4b24] transition-all shadow-sm">
                                        <flux:icon name="printer" class="w-3.5 h-3.5" />
                                        <span>Cetak</span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center">
                                <div class="max-w-md mx-auto flex flex-col items-center justify-center">
                                    <div class="w-12 h-12 rounded-xl bg-neutral-100 dark:bg-neutral-700 flex items-center justify-center mb-3 text-neutral-400">
                                        <flux:icon name="document-chart-bar" class="w-6 h-6" />
                                    </div>
                                    <h3 class="text-sm font-bold text-neutral-700 dark:text-neutral-300 mb-1">
                                        Tidak Ada Data Laporan Operasi
                                    </h3>
                                    <p class="text-xs text-neutral-400">
                                        Tidak ditemukan data laporan operasi untuk periode atau kriteria pencarian ini.
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($laporanList->hasPages())
            <div class="mt-4">
                {{ $laporanList->links() }}
            </div>
        @endif
    </div>
</div>