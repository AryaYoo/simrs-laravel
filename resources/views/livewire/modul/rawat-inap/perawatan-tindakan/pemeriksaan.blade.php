<div class="animate-in fade-in duration-300">
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-2">
            <flux:icon name="clipboard-document-check" class="w-5 h-5 text-[#4C5C2D]" />
            <h3 class="text-sm font-semibold text-neutral-700 dark:text-neutral-200">Data Pemeriksaan Rawat Inap</h3>
        </div>
        <span class="text-xs text-neutral-400 bg-neutral-100 dark:bg-neutral-700 px-2 py-1 rounded-full">{{ $pemeriksaanRanap->count() }} catatan</span>
    </div>

    <flux:table>
        <flux:table.columns>
            <flux:table.column>{{ __('Tanggal Rawat') }}</flux:table.column>
            <flux:table.column>{{ __('Jam') }}</flux:table.column>
            <flux:table.column>{{ __('Suhu (°C)') }}</flux:table.column>
            <flux:table.column>{{ __('Tensi') }}</flux:table.column>
            <flux:table.column>{{ __('Nadi (/mnt)') }}</flux:table.column>
            <flux:table.column>{{ __('Respirasi (/mnt)') }}</flux:table.column>
            <flux:table.column>{{ __('Tinggi (cm)') }}</flux:table.column>
            <flux:table.column>{{ __('Berat (kg)') }}</flux:table.column>
            <flux:table.column>{{ __('SpO2 (%)') }}</flux:table.column>
            <flux:table.column>{{ __('GCS (E,V,M)') }}</flux:table.column>
            <flux:table.column>{{ __('Dokter / Paramedis') }}</flux:table.column>
            <flux:table.column>{{ __('Aksi') }}</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse ($pemeriksaanRanap as $item)
                @php
                    $detailJson = json_encode([
                        'no_rawat'      => $item->no_rawat,
                        'tgl_perawatan' => $item->tgl_perawatan,
                        'jam_rawat'     => $item->jam_rawat,
                        'no_rkm_medis'  => $item->regPeriksa->no_rkm_medis ?? '-',
                        'nm_pasien'     => $item->regPeriksa->pasien->nm_pasien ?? '-',
                        'suhu_tubuh'    => $item->suhu_tubuh,
                        'tensi'         => $item->tensi,
                        'nadi'          => $item->nadi,
                        'respirasi'     => $item->respirasi,
                        'tinggi'        => $item->tinggi,
                        'berat'         => $item->berat,
                        'spo2'          => $item->spo2,
                        'gcs'           => $item->gcs,
                        'kesadaran'     => $item->kesadaran,
                        'keluhan'       => $item->keluhan,
                        'pemeriksaan'   => $item->pemeriksaan,
                        'alergi'        => $item->alergi,
                        'penilaian'     => $item->penilaian,
                        'rtl'           => $item->rtl,
                        'instruksi'     => $item->instruksi,
                        'evaluasi'      => $item->evaluasi,
                        'nip'           => $item->nip,
                        'nm_pegawai'    => $item->pegawai->nama ?? '-',
                        'jbtn_pegawai'  => $item->pegawai->jbtn ?? '-',
                    ]);
                @endphp
                <flux:table.row :key="$item->no_rawat . $item->tgl_perawatan . $item->jam_rawat">
                    <flux:table.cell class="whitespace-nowrap font-medium">{{ $item->tgl_perawatan }}</flux:table.cell>
                    <flux:table.cell class="whitespace-nowrap">{{ $item->jam_rawat }}</flux:table.cell>
                    <flux:table.cell class="whitespace-nowrap text-center">
                        @if($item->suhu_tubuh)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold {{ $item->suhu_tubuh > 37.5 ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' : 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' }}">{{ $item->suhu_tubuh }}</span>
                        @else <span class="text-neutral-300">—</span>
                        @endif
                    </flux:table.cell>
                    <flux:table.cell class="whitespace-nowrap text-center font-mono text-xs">{{ $item->tensi ?: '—' }}</flux:table.cell>
                    <flux:table.cell class="whitespace-nowrap text-center">{{ $item->nadi ?: '—' }}</flux:table.cell>
                    <flux:table.cell class="whitespace-nowrap text-center">{{ $item->respirasi ?: '—' }}</flux:table.cell>
                    <flux:table.cell class="whitespace-nowrap text-center">{{ $item->tinggi ?: '—' }}</flux:table.cell>
                    <flux:table.cell class="whitespace-nowrap text-center">{{ $item->berat ?: '—' }}</flux:table.cell>
                    <flux:table.cell class="whitespace-nowrap text-center">
                        @if($item->spo2)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold {{ $item->spo2 < 95 ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400' : 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400' }}">{{ $item->spo2 }}%</span>
                        @else <span class="text-neutral-300">—</span>
                        @endif
                    </flux:table.cell>
                    <flux:table.cell class="whitespace-nowrap text-center">
                        @if($item->gcs)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-violet-100 dark:bg-violet-900/30 text-violet-700 dark:text-violet-400 text-xs font-mono font-semibold">{{ $item->gcs }}</span>
                        @else <span class="text-neutral-300">—</span>
                        @endif
                    </flux:table.cell>
                    <flux:table.cell>
                        <p class="text-sm font-medium">{{ $item->pegawai->nama ?? '-' }}</p>
                        @if($item->pegawai?->jbtn)
                            <p class="text-xs text-neutral-400">{{ $item->pegawai->jbtn }}</p>
                        @endif
                    </flux:table.cell>
                    <flux:table.cell>
                        <button
                            type="button"
                            @click="showDetailModal({{ $detailJson }})"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium bg-[#4C5C2D]/10 text-[#4C5C2D] hover:bg-[#4C5C2D]/20 dark:bg-[#4C5C2D]/30 dark:text-[#4C5C2D] dark:hover:bg-[#4C5C2D]/50 transition-colors cursor-pointer border border-[#4C5C2D]/20 dark:border-[#4C5C2D]/50">
                            <flux:icon name="eye" class="w-3.5 h-3.5" />
                            Detail
                        </button>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="12" class="text-center py-12">
                        <flux:icon name="clipboard-document-check" class="w-10 h-10 mx-auto mb-3 text-neutral-200 dark:text-neutral-700" />
                        <p class="text-sm text-neutral-400">Belum ada data pemeriksaan untuk pasien ini.</p>
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>
</div>

{{-- ===== DETAIL MODAL (Alpine.js - pure client-side) ===== --}}
{{-- Positioned via x-teleport to body to completely escape parent CSS stacking contexts --}}
<template x-teleport="body">
    <div
        x-show="detailModalOpen"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-[9999] flex items-center justify-center p-4"
        style="display: none;">

        {{-- Backdrop --}}
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="closeDetailModal()"></div>

        {{-- Panel --}}
        <div
            x-show="detailModalOpen"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95 translate-y-2"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-95 translate-y-2"
            class="relative w-[95%] max-w-6xl max-h-[90vh] flex flex-col bg-white dark:bg-neutral-900 rounded-2xl shadow-2xl border border-neutral-200 dark:border-neutral-700 overflow-hidden"
            @click.stop>

            {{-- Header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-neutral-200 dark:border-neutral-700 bg-gradient-to-r from-[#4C5C2D]/10 to-white dark:from-[#4C5C2D]/20 dark:to-neutral-900 flex-shrink-0">
                <div class="flex items-center gap-3">
                    <div class="p-2.5 rounded-xl bg-[#4C5C2D]/10 dark:bg-[#4C5C2D]/30">
                        <flux:icon name="clipboard-document-check" class="w-5 h-5 text-[#4C5C2D]" />
                    </div>
                    <div>
                        <h2 class="font-bold text-neutral-800 dark:text-neutral-100 text-base">Detail Pemeriksaan</h2>
                        <p class="text-xs text-neutral-500">
                            <span x-text="detail.tgl_perawatan"></span> &bull;
                            <span x-text="detail.jam_rawat"></span> &bull;
                            <span class="font-mono" x-text="detail.no_rawat"></span>
                        </p>
                    </div>
                </div>
                <button @click="closeDetailModal()" class="p-1.5 rounded-lg hover:bg-white/60 dark:hover:bg-neutral-800 transition-colors cursor-pointer text-neutral-400 hover:text-neutral-600">
                    <flux:icon name="x-mark" class="w-5 h-5" />
                </button>
            </div>

            {{-- Scrollable Content --}}
            <div class="overflow-y-auto flex-1 p-6 flex flex-col gap-5 bg-neutral-50/50 dark:bg-neutral-900/30">

                {{-- Identitas (Top Full Width) --}}
                <section class="bg-white dark:bg-neutral-800/80 p-5 rounded-2xl border border-neutral-200 dark:border-neutral-700 shadow-sm">
                    <p class="text-[11px] font-bold uppercase tracking-widest text-[#6A7E3F] mb-4 flex items-center gap-1.5"><flux:icon name="identification" class="w-3.5 h-3.5" /> Identitas Pasien</p>
                    <div class="grid grid-cols-2 lg:grid-cols-5 gap-3">
                        <div class="bg-neutral-50 dark:bg-neutral-800 rounded-xl p-3 border border-neutral-100 dark:border-neutral-700/50">
                            <p class="text-[10px] text-neutral-400 font-medium uppercase tracking-wide mb-1">Tanggal Rawat</p>
                            <p class="text-sm font-semibold text-neutral-700 dark:text-neutral-200" x-text="detail.tgl_perawatan"></p>
                        </div>
                        <div class="bg-neutral-50 dark:bg-neutral-800 rounded-xl p-3 border border-neutral-100 dark:border-neutral-700/50">
                            <p class="text-[10px] text-neutral-400 font-medium uppercase tracking-wide mb-1">Jam</p>
                            <p class="text-sm font-semibold text-neutral-700 dark:text-neutral-200" x-text="detail.jam_rawat"></p>
                        </div>
                        <div class="bg-neutral-50 dark:bg-neutral-800 rounded-xl p-3 border border-neutral-100 dark:border-neutral-700/50">
                            <p class="text-[10px] text-neutral-400 font-medium uppercase tracking-wide mb-1">No. Rawat</p>
                            <p class="text-sm font-semibold text-neutral-700 dark:text-neutral-200 font-mono text-xs" x-text="detail.no_rawat"></p>
                        </div>
                        <div class="bg-neutral-50 dark:bg-neutral-800 rounded-xl p-3 border border-neutral-100 dark:border-neutral-700/50">
                            <p class="text-[10px] text-neutral-400 font-medium uppercase tracking-wide mb-1">No. RM</p>
                            <p class="text-sm font-semibold text-neutral-700 dark:text-neutral-200 font-mono" x-text="detail.no_rkm_medis"></p>
                        </div>
                        <div class="bg-[#4C5C2D]/10 dark:bg-[#4C5C2D]/20 rounded-xl p-3 col-span-2 lg:col-span-1 border border-[#4C5C2D]/20 dark:border-[#4C5C2D]/50">
                            <p class="text-[10px] text-[#4C5C2D] font-medium uppercase tracking-wide mb-1">Nama Pasien</p>
                            <p class="text-base font-bold text-[#4C5C2D] dark:text-[#8CC7C4] truncate" x-text="detail.nm_pasien" :title="detail.nm_pasien"></p>
                        </div>
                    </div>
                </section>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">
                    
                    {{-- Tanda Vital (Left Column, col-span-3 or 4) --}}
                    <div class="lg:col-span-4 flex flex-col">
                        <section class="bg-white dark:bg-neutral-800/80 p-5 rounded-2xl border border-neutral-200 dark:border-neutral-700 shadow-sm flex-1 flex flex-col">
                            <p class="text-[11px] font-bold uppercase tracking-widest text-[#6A7E3F] mb-4 flex items-center gap-1.5"><flux:icon name="heart" class="w-3.5 h-3.5" /> Tanda-Tanda Vital</p>
                            
                            <div class="grid grid-cols-2 gap-3 flex-1">
                                <div class="bg-neutral-50 dark:bg-neutral-800 rounded-xl p-3 text-center border border-neutral-100 dark:border-neutral-700/50 flex flex-col justify-center">
                                    <p class="text-[10px] text-neutral-400 font-medium uppercase tracking-wide mb-1.5 leading-tight">Suhu (°C)</p>
                                    <p class="text-xl font-bold font-mono text-neutral-700 dark:text-neutral-200" x-text="detail.suhu_tubuh || '—'"></p>
                                </div>
                                <div class="bg-neutral-50 dark:bg-neutral-800 rounded-xl p-3 text-center border border-neutral-100 dark:border-neutral-700/50 flex flex-col justify-center">
                                    <p class="text-[10px] text-neutral-400 font-medium uppercase tracking-wide mb-1.5 leading-tight">Tensi</p>
                                    <p class="text-xl font-bold font-mono text-neutral-700 dark:text-neutral-200" x-text="detail.tensi || '—'"></p>
                                </div>
                                <div class="bg-neutral-50 dark:bg-neutral-800 rounded-xl p-3 text-center border border-neutral-100 dark:border-neutral-700/50 flex flex-col justify-center">
                                    <p class="text-[10px] text-neutral-400 font-medium uppercase tracking-wide mb-1.5 leading-tight">Nadi (/mnt)</p>
                                    <p class="text-xl font-bold font-mono text-neutral-700 dark:text-neutral-200" x-text="detail.nadi || '—'"></p>
                                </div>
                                <div class="bg-neutral-50 dark:bg-neutral-800 rounded-xl p-3 text-center border border-neutral-100 dark:border-neutral-700/50 flex flex-col justify-center">
                                    <p class="text-[10px] text-neutral-400 font-medium uppercase tracking-wide mb-1.5 leading-tight">Respirasi</p>
                                    <p class="text-xl font-bold font-mono text-neutral-700 dark:text-neutral-200" x-text="detail.respirasi || '—'"></p>
                                </div>
                                <div class="bg-neutral-50 dark:bg-neutral-800 rounded-xl p-3 text-center border border-neutral-100 dark:border-neutral-700/50 flex flex-col justify-center">
                                    <p class="text-[10px] text-neutral-400 font-medium uppercase tracking-wide mb-1.5 leading-tight">Tinggi (cm)</p>
                                    <p class="text-lg font-bold font-mono text-neutral-700 dark:text-neutral-200" x-text="detail.tinggi || '—'"></p>
                                </div>
                                <div class="bg-neutral-50 dark:bg-neutral-800 rounded-xl p-3 text-center border border-neutral-100 dark:border-neutral-700/50 flex flex-col justify-center">
                                    <p class="text-[10px] text-neutral-400 font-medium uppercase tracking-wide mb-1.5 leading-tight">Berat (kg)</p>
                                    <p class="text-lg font-bold font-mono text-neutral-700 dark:text-neutral-200" x-text="detail.berat || '—'"></p>
                                </div>
                                <div class="bg-neutral-50 dark:bg-neutral-800 rounded-xl p-3 text-center border border-neutral-100 dark:border-neutral-700/50 flex flex-col justify-center">
                                    <p class="text-[10px] text-neutral-400 font-medium uppercase tracking-wide mb-1.5 leading-tight">SpO2 (%)</p>
                                    <p class="text-lg font-bold font-mono text-neutral-700 dark:text-neutral-200" x-text="detail.spo2 ? detail.spo2 + '%' : '—'"></p>
                                </div>
                                <div class="bg-neutral-50 dark:bg-neutral-800 rounded-xl p-3 text-center border border-neutral-100 dark:border-neutral-700/50 flex flex-col justify-center">
                                    <p class="text-[10px] text-neutral-400 font-medium uppercase tracking-wide mb-1.5 leading-tight">GCS</p>
                                    <p class="text-lg font-bold font-mono text-neutral-700 dark:text-neutral-200" x-text="detail.gcs || '—'"></p>
                                </div>
                            </div>
                            
                            <div class="mt-3 flex flex-col gap-2">
                                <template x-if="detail.kesadaran">
                                    <div class="flex items-center gap-2 bg-sky-50 dark:bg-sky-900/20 border border-sky-200 dark:border-sky-800/50 rounded-lg px-3 py-2">
                                        <flux:icon name="eye" class="w-4 h-4 text-sky-500 flex-shrink-0" />
                                        <p class="text-sm text-sky-700 dark:text-sky-300"><span class="font-semibold">Kesadaran:</span> <span x-text="detail.kesadaran"></span></p>
                                    </div>
                                </template>
                                <template x-if="detail.alergi">
                                    <div class="flex items-center gap-2 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800/50 rounded-lg px-3 py-2">
                                        <flux:icon name="exclamation-triangle" class="w-4 h-4 text-red-500 flex-shrink-0" />
                                        <p class="text-sm text-red-700 dark:text-red-300"><span class="font-semibold">Alergi:</span> <span x-text="detail.alergi"></span></p>
                                    </div>
                                </template>
                            </div>
                        </section>
                    </div>

                    {{-- SOAP & Evaluasi (Right Column, col-span-8) --}}
                    <div class="lg:col-span-8 flex flex-col gap-5">
                        
                        {{-- SOAP --}}
                        <section class="bg-white dark:bg-neutral-800/80 p-5 rounded-2xl border border-neutral-200 dark:border-neutral-700 shadow-sm flex-1">
                            <p class="text-[11px] font-bold uppercase tracking-widest text-[#6A7E3F] mb-4 flex items-center gap-1.5"><flux:icon name="document-text" class="w-3.5 h-3.5" /> Catatan SOAP</p>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="border border-neutral-100 dark:border-neutral-700/70 rounded-xl overflow-hidden shadow-sm">
                                    <div class="px-4 py-2.5 bg-neutral-50/80 dark:bg-neutral-800 border-b border-neutral-100 dark:border-neutral-700/70 flex items-center gap-2">
                                        <span class="w-5 h-5 rounded-md bg-white dark:bg-neutral-700 shadow-sm text-neutral-700 dark:text-neutral-300 text-xs font-bold flex items-center justify-center flex-shrink-0 border border-neutral-200 dark:border-neutral-600">S</span>
                                        <p class="text-xs font-semibold text-neutral-600 dark:text-neutral-400">Subjek (Keluhan)</p>
                                    </div>
                                    <div class="px-4 py-3 min-h-[70px] bg-white dark:bg-neutral-800/40">
                                        <p class="text-sm text-neutral-700 dark:text-neutral-300 whitespace-pre-wrap leading-relaxed" x-text="detail.keluhan || '-'"></p>
                                    </div>
                                </div>

                                <div class="border border-neutral-100 dark:border-neutral-700/70 rounded-xl overflow-hidden shadow-sm">
                                    <div class="px-4 py-2.5 bg-neutral-50/80 dark:bg-neutral-800 border-b border-neutral-100 dark:border-neutral-700/70 flex items-center gap-2">
                                        <span class="w-5 h-5 rounded-md bg-white dark:bg-neutral-700 shadow-sm text-neutral-700 dark:text-neutral-300 text-xs font-bold flex items-center justify-center flex-shrink-0 border border-neutral-200 dark:border-neutral-600">O</span>
                                        <p class="text-xs font-semibold text-neutral-600 dark:text-neutral-400">Objek (Pemeriksaan)</p>
                                    </div>
                                    <div class="px-4 py-3 min-h-[70px] bg-white dark:bg-neutral-800/40">
                                        <p class="text-sm text-neutral-700 dark:text-neutral-300 whitespace-pre-wrap leading-relaxed" x-text="detail.pemeriksaan || '-'"></p>
                                    </div>
                                </div>

                                <div class="border border-neutral-100 dark:border-neutral-700/70 rounded-xl overflow-hidden shadow-sm">
                                    <div class="px-4 py-2.5 bg-neutral-50/80 dark:bg-neutral-800 border-b border-neutral-100 dark:border-neutral-700/70 flex items-center gap-2">
                                        <span class="w-5 h-5 rounded-md bg-white dark:bg-neutral-700 shadow-sm text-neutral-700 dark:text-neutral-300 text-xs font-bold flex items-center justify-center flex-shrink-0 border border-neutral-200 dark:border-neutral-600">A</span>
                                        <p class="text-xs font-semibold text-neutral-600 dark:text-neutral-400">Asesment (Penilaian)</p>
                                    </div>
                                    <div class="px-4 py-3 min-h-[70px] bg-white dark:bg-neutral-800/40">
                                        <p class="text-sm text-neutral-700 dark:text-neutral-300 whitespace-pre-wrap leading-relaxed" x-text="detail.penilaian || '-'"></p>
                                    </div>
                                </div>

                                <div class="border border-neutral-100 dark:border-neutral-700/70 rounded-xl overflow-hidden shadow-sm">
                                    <div class="px-4 py-2.5 bg-neutral-50/80 dark:bg-neutral-800 border-b border-neutral-100 dark:border-neutral-700/70 flex items-center gap-2">
                                        <span class="w-5 h-5 rounded-md bg-white dark:bg-neutral-700 shadow-sm text-neutral-700 dark:text-neutral-300 text-xs font-bold flex items-center justify-center flex-shrink-0 border border-neutral-200 dark:border-neutral-600">P</span>
                                        <p class="text-xs font-semibold text-neutral-600 dark:text-neutral-400">Plan (RTL)</p>
                                    </div>
                                    <div class="px-4 py-3 min-h-[70px] bg-white dark:bg-neutral-800/40">
                                        <p class="text-sm text-neutral-700 dark:text-neutral-300 whitespace-pre-wrap leading-relaxed" x-text="detail.rtl || '-'"></p>
                                    </div>
                                </div>
                            </div>
                        </section>

                        {{-- Instruksi & Evaluasi --}}
                        <template x-if="detail.instruksi || detail.evaluasi">
                            <section class="bg-white dark:bg-neutral-800/80 p-5 rounded-2xl border border-neutral-200 dark:border-neutral-700 shadow-sm">
                                <p class="text-[11px] font-bold uppercase tracking-widest text-[#6A7E3F] mb-4 flex items-center gap-1.5"><flux:icon name="arrow-path" class="w-3.5 h-3.5" /> Tindak Lanjut & Evaluasi</p>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <template x-if="detail.instruksi">
                                        <div class="border border-neutral-100 dark:border-neutral-700/70 rounded-xl overflow-hidden shadow-sm">
                                            <div class="px-4 py-2 bg-neutral-50/80 dark:bg-neutral-800 border-b border-neutral-100 dark:border-neutral-700/70">
                                                <p class="text-xs font-semibold text-neutral-600 dark:text-neutral-400">Inst / Impl (Instruksi)</p>
                                            </div>
                                            <div class="px-4 py-3 bg-white dark:bg-neutral-800/40">
                                                <p class="text-sm text-neutral-700 dark:text-neutral-300 whitespace-pre-wrap leading-relaxed" x-text="detail.instruksi"></p>
                                            </div>
                                        </div>
                                    </template>
                                    <template x-if="detail.evaluasi">
                                        <div class="border border-neutral-100 dark:border-neutral-700/70 rounded-xl overflow-hidden shadow-sm">
                                            <div class="px-4 py-2 bg-neutral-50/80 dark:bg-neutral-800 border-b border-neutral-100 dark:border-neutral-700/70">
                                                <p class="text-xs font-semibold text-neutral-600 dark:text-neutral-400">Evaluasi</p>
                                            </div>
                                            <div class="px-4 py-3 bg-white dark:bg-neutral-800/40">
                                                <p class="text-sm text-neutral-700 dark:text-neutral-300 whitespace-pre-wrap leading-relaxed" x-text="detail.evaluasi"></p>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </section>
                        </template>
                    </div>
                </div>

                {{-- Petugas (Bottom Full Width) --}}
                <section class="bg-white dark:bg-neutral-800/80 p-5 rounded-2xl border border-neutral-200 dark:border-neutral-700 shadow-sm">
                    <p class="text-[11px] font-bold uppercase tracking-widest text-[#6A7E3F] mb-4 flex items-center gap-1.5"><flux:icon name="user-circle" class="w-3.5 h-3.5" /> Dokter / Paramedis</p>
                    <div class="flex items-center gap-4 bg-neutral-50/50 dark:bg-neutral-800/50 rounded-xl p-3 border border-neutral-100 dark:border-neutral-700/50">
                        <div class="w-12 h-12 rounded-xl bg-[#4C5C2D]/10 dark:bg-[#4C5C2D]/30 flex items-center justify-center flex-shrink-0">
                            <flux:icon name="user" class="w-6 h-6 text-[#4C5C2D]" />
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-bold text-neutral-800 dark:text-neutral-100 text-sm" x-text="detail.nm_pegawai"></p>
                            <p class="text-xs text-neutral-500 mt-0.5" x-text="detail.jbtn_pegawai"></p>
                        </div>
                        <div class="text-right flex-shrink-0 px-2">
                            <p class="text-[10px] text-neutral-400 uppercase font-medium">NIP / NIK</p>
                            <p class="text-sm font-mono font-medium text-neutral-700 dark:text-neutral-300" x-text="detail.nip"></p>
                        </div>
                    </div>
                </section>

            </div>

            {{-- Footer --}}
            <div class="px-6 py-4 border-t border-neutral-200 dark:border-neutral-700 bg-neutral-50/80 dark:bg-neutral-800/60 flex justify-end flex-shrink-0">
                <button @click="closeDetailModal()" class="inline-flex items-center gap-2 px-5 py-2 rounded-lg text-sm font-medium bg-white dark:bg-neutral-700 text-neutral-700 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-600 transition-colors cursor-pointer border border-neutral-200 dark:border-neutral-600 shadow-sm">
                    <flux:icon name="x-mark" class="w-4 h-4" />
                    Tutup
                </button>
            </div>
        </div>
    </div>
</template>
