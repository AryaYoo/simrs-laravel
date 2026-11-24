<div class="flex flex-col gap-6 {{ $isEmbedded ? '' : 'pb-8' }}">
    @if(!$isEmbedded)
    {{-- Header / Breadcrumb --}}
    <div class="flex flex-col gap-1">
        <div class="flex items-center gap-3">
            <button type="button" onclick="history.length > 1 ? history.back() : window.location.href = '{{ route('modul.rawat-inap.index') }}'" class="flex items-center justify-center w-10 h-8 rounded-md bg-[#4C5C2D] transition-colors hover:bg-[#3d4b24] shadow-sm">
                <flux:icon name="chevron-left" class="w-5 h-5 text-white" />
            </button>
            <div>
                <nav class="text-xs text-neutral-400 mb-0.5">
                    <a href="{{ route('modul.rawat-inap.perawatan-tindakan', str_replace('/', '-', $regPeriksa->no_rawat)) }}" wire:navigate class="hover:underline">Perawatan & Tindakan</a>
                    <span class="mx-1">/</span>
                    <span>Catatan SBAR</span>
                </nav>
                <h1 class="text-xl font-bold text-neutral-800 dark:text-neutral-100">Catatan SBAR</h1>
            </div>
        </div>
    </div>

    {{-- Patient Info Card --}}
    <div class="bg-white dark:bg-neutral-800 rounded-xl border border-neutral-200 dark:border-neutral-700 p-4 shadow-sm flex items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-[#4C5C2D]/10 dark:bg-[#4C5C2D]/30 flex items-center justify-center flex-shrink-0">
                <flux:icon name="user" class="w-6 h-6 text-[#4C5C2D] dark:text-[#8CC7C4]" />
            </div>
            <div>
                <h2 class="font-bold text-lg text-neutral-800 dark:text-neutral-100 leading-tight">
                    {{ $regPeriksa->pasien->nm_pasien ?? '-' }}
                </h2>
                <div class="flex flex-wrap items-center gap-x-3 gap-y-1 mt-1 text-sm text-neutral-500">
                    <span class="font-mono bg-neutral-100 dark:bg-neutral-900 px-1.5 py-0.5 rounded">{{ $regPeriksa->no_rkm_medis }}</span>
                    <span>•</span>
                    <span>{{ $regPeriksa->pasien->jk === 'L' ? 'Laki-laki' : 'Perempuan' }}</span>
                    <span>•</span>
                    <span>{{ $regPeriksa->pasien->tgl_lahir ? \Carbon\Carbon::parse($regPeriksa->pasien->tgl_lahir)->age . ' Thn' : '-' }}</span>
                    @php
                        $topKamar = $regPeriksa->kamarInap->sortByDesc('tgl_masuk')->first() ?? $regPeriksa->kamarInap->last();
                    @endphp
                    @if($topKamar)
                        <span>•</span>
                        <span class="inline-flex items-center gap-1.5 bg-[#4C5C2D]/10 text-[#4C5C2D] dark:text-[#8CC7C4] text-xs font-semibold px-2 py-0.5 rounded-md">
                            <flux:icon name="building-office-2" class="w-3.5 h-3.5" />
                            {{ $topKamar->kamar->bangsal->nm_bangsal ?? '-' }} ({{ $topKamar->kd_kamar }})
                        </span>
                    @endif
                </div>
            </div>
        </div>
        <div class="text-right">
            <div class="text-sm text-neutral-500 mb-1">No. Rawat</div>
            <div class="font-mono font-bold text-[#4C5C2D] dark:text-[#8CC7C4]">{{ $regPeriksa->no_rawat }}</div>
        </div>
    </div>
    @endif

    {{-- Data Table --}}
    <div class="bg-white dark:bg-neutral-800 rounded-xl border border-neutral-200 dark:border-neutral-700 shadow-sm overflow-hidden flex flex-col mt-2 relative p-4">
        <div class="flex items-center justify-between mb-4 px-2">
            <h3 class="text-sm font-bold text-neutral-700 dark:text-neutral-300 uppercase tracking-wider flex items-center gap-2">
                <flux:icon name="list-bullet" class="w-4 h-4 text-[#4C5C2D]" />
                Daftar Catatan SBAR
            </h3>
            <flux:button wire:click="openPanel" icon="plus" variant="filled" size="sm" class="!flex !flex-row !items-center !bg-[#4C5C2D] !text-white hover:!bg-[#3d4a24]">
                Tambah Catatan
            </flux:button>
        </div>

        <div class="overflow-x-auto">
            <flux:table class="whitespace-nowrap mt-2">
                <flux:table.columns>
                    <flux:table.column class="!pl-6">Tanggal & Waktu</flux:table.column>
                    <flux:table.column>Dilakukan Oleh</flux:table.column>
                    <flux:table.column>DPJP</flux:table.column>
                    <flux:table.column>Situation (S)</flux:table.column>
                    <flux:table.column>Background (B)</flux:table.column>
                    <flux:table.column>Assessment (A)</flux:table.column>
                    <flux:table.column>Recommendation (R)</flux:table.column>
                    <flux:table.column>Status</flux:table.column>
                    <flux:table.column>Action</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @forelse ($catatans as $catatan)
                        <flux:table.row>
                            <flux:table.cell class="!pl-6 font-medium text-neutral-900 dark:text-neutral-100">
                                <div class="font-bold">{{ \Carbon\Carbon::parse($catatan['tanggal'])->format('d/m/Y') }}</div>
                                <div class="text-xs text-neutral-400">{{ \Carbon\Carbon::parse($catatan['tanggal'])->format('H:i') }}</div>
                            </flux:table.cell>
                            <flux:table.cell class="text-xs text-neutral-600 dark:text-neutral-400">
                                <span class="font-mono text-neutral-500 block mb-0.5">{{ $catatan['nip'] }}</span>
                                {{ $catatan['petugas']['nama'] ?? '-' }}
                            </flux:table.cell>
                            <flux:table.cell class="text-xs text-neutral-600 dark:text-neutral-400">
                                <span class="font-mono text-neutral-500 block mb-0.5">{{ $catatan['kd_dokter'] }}</span>
                                {{ $catatan['dokter']['nama'] ?? '-' }}
                            </flux:table.cell>
                            <flux:table.cell class="text-neutral-600 dark:text-neutral-400 max-w-[150px]">
                                <div class="truncate" title="{{ $catatan['situation'] }}">{{ $catatan['situation'] ? \Str::limit($catatan['situation'], 40) : '-' }}</div>
                            </flux:table.cell>
                            <flux:table.cell class="text-neutral-600 dark:text-neutral-400 max-w-[150px]">
                                <div class="truncate" title="{{ $catatan['background'] }}">{{ $catatan['background'] ? \Str::limit($catatan['background'], 40) : '-' }}</div>
                            </flux:table.cell>
                            <flux:table.cell class="text-neutral-600 dark:text-neutral-400 max-w-[150px]">
                                <div class="truncate" title="{{ $catatan['assessment'] }}">{{ $catatan['assessment'] ? \Str::limit($catatan['assessment'], 40) : '-' }}</div>
                            </flux:table.cell>
                            <flux:table.cell class="text-neutral-600 dark:text-neutral-400 max-w-[150px]">
                                <div class="truncate" title="{{ $catatan['recommendation'] }}">{{ $catatan['recommendation'] ? \Str::limit($catatan['recommendation'], 40) : '-' }}</div>
                            </flux:table.cell>
                            <flux:table.cell>
                                <div class="flex flex-col gap-1 text-xs">
                                    <div class="flex items-center gap-1">
                                        <span class="text-neutral-500 w-16">Baca:</span>
                                        @if($catatan['status_baca'] === 'Sudah')
                                            <flux:badge size="sm" color="green" class="!px-1.5 !py-0">Sudah</flux:badge>
                                        @else
                                            <flux:badge size="sm" color="zinc" class="!px-1.5 !py-0">Belum</flux:badge>
                                        @endif
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <span class="text-neutral-500 w-16">Konf:</span>
                                        @if($catatan['status_konfirmasi'] === 'Sudah')
                                            <flux:badge size="sm" color="green" class="!px-1.5 !py-0">Sudah</flux:badge>
                                        @else
                                            <flux:badge size="sm" color="zinc" class="!px-1.5 !py-0">Belum</flux:badge>
                                        @endif
                                    </div>
                                    {{-- Verif row hidden per request --}}
                                </div>
                            </flux:table.cell>
                            <flux:table.cell>
                                <div class="flex justify-center gap-1.5 items-center">
                                    <a href="{{ route('modul.rawat-inap.catatan-sbar.cetak', [str_replace('/', '-', $catatan['no_rawat']), urlencode($catatan['tanggal'])]) }}" target="_blank" title="Cetak Lembar SBAR">
                                        <flux:button icon="printer" size="xs" variant="ghost" class="!text-emerald-600 hover:!bg-emerald-50 dark:hover:!bg-emerald-500/10" />
                                    </a>
                                    <flux:button wire:click="showDetail('{{ $catatan['tanggal'] }}')" icon="eye" size="xs" variant="ghost" class="!text-blue-500 hover:!bg-blue-50 dark:hover:!bg-blue-500/10" title="Lihat Detail" />
                                    <flux:button wire:click="edit('{{ $catatan['tanggal'] }}')" icon="pencil-square" size="xs" variant="ghost" title="Edit Catatan" />
                                    <flux:button
                                        @click="
                                            Swal.fire({
                                                title: 'Hapus Catatan SBAR?',
                                                text: 'Data catatan ini akan dihapus secara permanen.',
                                                icon: 'warning',
                                                showCancelButton: true,
                                                confirmButtonColor: '#dc2626',
                                                cancelButtonColor: '#6b7280',
                                                confirmButtonText: 'Ya, Hapus!',
                                                cancelButtonText: 'Batal'
                                            }).then((result) => {
                                                if (result.isConfirmed) {
                                                    $wire.delete('{{ $catatan['tanggal'] }}');
                                                }
                                            })
                                        "
                                        icon="trash" size="xs" variant="ghost" class="!text-red-500 hover:!bg-red-50 dark:hover:!bg-red-500/10" />
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="9">
                                <div class="flex flex-col items-center justify-center py-12 text-center">
                                    <flux:icon name="inbox" class="w-12 h-12 text-neutral-300 dark:text-neutral-600 mb-4" />
                                    <h3 class="text-lg font-bold text-neutral-700 dark:text-neutral-300">Belum Ada Catatan SBAR</h3>
                                    <p class="text-neutral-500 mt-1 max-w-md">Belum ada catatan SBAR yang ditambahkan untuk pasien ini.</p>
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </div>
    </div>

    {{-- DETAIL MODAL --}}
    <div x-data="{ open: @entangle('detailSbar').live !== null }"
         x-init="$watch('$wire.detailSbar', v => { open = v !== null })"
         x-show="$wire.detailSbar !== null"
         class="fixed inset-0 z-[60] flex items-center justify-center"
         style="display: none;">

        {{-- Backdrop --}}
        <div class="absolute inset-0 bg-neutral-900/60 backdrop-blur-sm"
             @click="$wire.detailSbar = null"></div>

        {{-- Modal Box --}}
        <div class="relative z-10 w-full max-w-2xl mx-4 bg-white dark:bg-neutral-800 rounded-2xl shadow-2xl border border-neutral-200 dark:border-neutral-700 overflow-hidden"
             @click.stop>

            @if($detailSbar)
            {{-- Modal Header --}}
            <div class="px-6 py-4 border-b border-neutral-100 dark:border-neutral-700 flex items-center justify-between bg-neutral-50 dark:bg-neutral-900">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-[#4C5C2D]/10 flex items-center justify-center">
                        <flux:icon name="chat-bubble-bottom-center-text" class="w-5 h-5 text-[#4C5C2D]" />
                    </div>
                    <div>
                        <h3 class="font-bold text-neutral-800 dark:text-neutral-100 text-base">Detail Catatan SBAR</h3>
                        <p class="text-xs text-neutral-500">{{ \Carbon\Carbon::parse($detailSbar['tanggal'])->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
                <button type="button" wire:click="$set('detailSbar', null)" class="text-neutral-400 hover:text-neutral-600 transition-colors">
                    <flux:icon name="x-mark" class="w-5 h-5" />
                </button>
            </div>

            {{-- Meta Info --}}
            <div class="px-6 py-3 bg-neutral-50/80 dark:bg-neutral-900/60 flex flex-wrap gap-x-6 gap-y-1 text-sm border-b border-neutral-100 dark:border-neutral-800">
                <div class="flex items-center gap-2">
                    <flux:icon name="user" class="w-4 h-4 text-[#4C5C2D]" />
                    <span class="text-neutral-500">Petugas:</span>
                    <span class="font-semibold text-neutral-700 dark:text-neutral-300">{{ $detailSbar['petugas']['nama'] ?? '-' }}</span>
                </div>
                <div class="flex items-center gap-2">
                    <flux:icon name="star" class="w-4 h-4 text-blue-500" />
                    <span class="text-neutral-500">DPJP:</span>
                    <span class="font-semibold text-neutral-700 dark:text-neutral-300">{{ $detailSbar['dokter']['nama'] ?? '-' }}</span>
                </div>
            </div>

            {{-- SBAR Content --}}
            <div class="px-6 py-5 space-y-5 max-h-[55vh] overflow-y-auto">
                {{-- Situation --}}
                <div class="flex flex-col gap-1.5">
                    <div class="flex items-center gap-2">
                        <flux:icon name="information-circle" class="w-4 h-4 text-amber-700 dark:text-amber-400" />
                        <span class="text-xs font-bold uppercase tracking-wider text-amber-700 dark:text-amber-400">Situation (S)</span>
                    </div>
                    <div class="bg-amber-50 dark:bg-amber-500/10 rounded-xl px-4 py-3 text-sm text-neutral-800 dark:text-neutral-200 whitespace-pre-wrap border border-amber-200 dark:border-amber-500/20 leading-relaxed">{{ trim($detailSbar['situation'] ?? '') ?: '-' }}</div>
                </div>

                {{-- Background --}}
                @php
                    $hasDetailBg = !empty($detailSbar['bg_riwayat_penyakit_dahulu']) ||
                                   !empty($detailSbar['bg_riwayat_penyakit']) ||
                                   !empty($detailSbar['bg_informasi_klinis']) ||
                                   !empty($detailSbar['bg_penunjang']) ||
                                   !empty($detailSbar['bg_alergi']) ||
                                   !empty($detailSbar['bg_terapi']) ||
                                   !empty($detailSbar['bg_tensi']);
                @endphp
                <div class="flex flex-col gap-1.5">
                    <div class="flex items-center gap-2">
                        <flux:icon name="clock" class="w-4 h-4 text-blue-700 dark:text-blue-400" />
                        <span class="text-xs font-bold uppercase tracking-wider text-blue-700 dark:text-blue-400">Background (B)</span>
                    </div>
                    <div class="bg-blue-50 dark:bg-blue-500/10 rounded-xl px-4 py-3 text-sm text-neutral-800 dark:text-neutral-200 border border-blue-200 dark:border-blue-500/20 leading-relaxed space-y-3">
                        @if($hasDetailBg)
                            @if(!empty($detailSbar['bg_riwayat_penyakit_dahulu']))
                                <div>
                                    <div class="text-[11px] font-bold text-blue-800 dark:text-blue-300 uppercase tracking-wider">MRS / Riwayat Penyakit Dahulu</div>
                                    <div class="text-xs text-neutral-700 dark:text-neutral-300 mt-0.5 whitespace-pre-wrap">{{ trim($detailSbar['bg_riwayat_penyakit_dahulu']) }}</div>
                                </div>
                            @endif
                            @if(!empty($detailSbar['bg_riwayat_penyakit']))
                                <div>
                                    <div class="text-[11px] font-bold text-blue-800 dark:text-blue-300 uppercase tracking-wider">Riwayat Penyakit</div>
                                    <div class="text-xs text-neutral-700 dark:text-neutral-300 mt-0.5 whitespace-pre-wrap">{{ trim($detailSbar['bg_riwayat_penyakit']) }}</div>
                                </div>
                            @endif
                            @if(!empty($detailSbar['bg_informasi_klinis']))
                                <div>
                                    <div class="text-[11px] font-bold text-blue-800 dark:text-blue-300 uppercase tracking-wider">Informasi Klinis</div>
                                    <div class="text-xs text-neutral-700 dark:text-neutral-300 mt-0.5 whitespace-pre-wrap">{{ trim($detailSbar['bg_informasi_klinis']) }}</div>
                                </div>
                            @endif
                            @if(!empty($detailSbar['bg_penunjang']))
                                <div>
                                    <div class="text-[11px] font-bold text-blue-800 dark:text-blue-300 uppercase tracking-wider">Lab / Pemeriksaan Penunjang Lain</div>
                                    <div class="text-xs text-neutral-700 dark:text-neutral-300 mt-0.5 whitespace-pre-wrap">{{ trim($detailSbar['bg_penunjang']) }}</div>
                                </div>
                            @endif
                            @if(!empty($detailSbar['bg_alergi']))
                                <div>
                                    <div class="text-[11px] font-bold text-blue-800 dark:text-blue-300 uppercase tracking-wider">Riwayat Alergi</div>
                                    <div class="text-xs text-neutral-700 dark:text-neutral-300 mt-0.5">{{ trim($detailSbar['bg_alergi']) }}</div>
                                </div>
                            @endif
                            @if(!empty($detailSbar['bg_kesadaran']) || !empty($detailSbar['bg_tensi']) || !empty($detailSbar['bg_nadi']) || !empty($detailSbar['bg_respirasi']) || !empty($detailSbar['bg_suhu']) || !empty($detailSbar['bg_spo2']))
                                <div>
                                    <div class="text-[11px] font-bold text-blue-800 dark:text-blue-300 uppercase tracking-wider mb-1">Tanda Vital Saat Ini</div>
                                    <div class="flex flex-wrap gap-2 text-xs">
                                        @if(!empty($detailSbar['bg_kesadaran'])) <span class="bg-blue-100 dark:bg-blue-900/40 text-blue-800 dark:text-blue-200 px-2 py-0.5 rounded font-mono">Kes: {{ $detailSbar['bg_kesadaran'] }}</span> @endif
                                        @if(!empty($detailSbar['bg_tensi'])) <span class="bg-blue-100 dark:bg-blue-900/40 text-blue-800 dark:text-blue-200 px-2 py-0.5 rounded font-mono">TD: {{ $detailSbar['bg_tensi'] }} mmHg</span> @endif
                                        @if(!empty($detailSbar['bg_nadi'])) <span class="bg-blue-100 dark:bg-blue-900/40 text-blue-800 dark:text-blue-200 px-2 py-0.5 rounded font-mono">Nadi: {{ $detailSbar['bg_nadi'] }} x/m</span> @endif
                                        @if(!empty($detailSbar['bg_respirasi'])) <span class="bg-blue-100 dark:bg-blue-900/40 text-blue-800 dark:text-blue-200 px-2 py-0.5 rounded font-mono">RR: {{ $detailSbar['bg_respirasi'] }} /m</span> @endif
                                        @if(!empty($detailSbar['bg_suhu'])) <span class="bg-blue-100 dark:bg-blue-900/40 text-blue-800 dark:text-blue-200 px-2 py-0.5 rounded font-mono">S: {{ $detailSbar['bg_suhu'] }} °C</span> @endif
                                        @if(!empty($detailSbar['bg_spo2'])) <span class="bg-blue-100 dark:bg-blue-900/40 text-blue-800 dark:text-blue-200 px-2 py-0.5 rounded font-mono">SpO2: {{ $detailSbar['bg_spo2'] }}%</span> @endif
                                    </div>
                                </div>
                            @endif
                            @if(!empty($detailSbar['bg_terapi']))
                                <div>
                                    <div class="text-[11px] font-bold text-blue-800 dark:text-blue-300 uppercase tracking-wider">Terapi Saat Ini</div>
                                    <div class="text-xs text-neutral-700 dark:text-neutral-300 mt-0.5 whitespace-pre-wrap">{{ $detailSbar['bg_terapi'] }}</div>
                                </div>
                            @endif
                        @else
                            <div class="whitespace-pre-wrap">{{ trim($detailSbar['background'] ?? '') ?: '-' }}</div>
                        @endif
                    </div>
                </div>

                {{-- Assessment --}}
                <div class="flex flex-col gap-1.5">
                    <div class="flex items-center gap-2">
                        <flux:icon name="clipboard-document-check" class="w-4 h-4 text-purple-700 dark:text-purple-400" />
                        <span class="text-xs font-bold uppercase tracking-wider text-purple-700 dark:text-purple-400">Assessment (A)</span>
                    </div>
                    <div class="bg-purple-50 dark:bg-purple-500/10 rounded-xl px-4 py-3 text-sm text-neutral-800 dark:text-neutral-200 whitespace-pre-wrap border border-purple-200 dark:border-purple-500/20 leading-relaxed">{{ trim($detailSbar['assessment'] ?? '') ?: '-' }}</div>
                </div>

                {{-- Recommendation --}}
                <div class="flex flex-col gap-1.5">
                    <div class="flex items-center gap-2">
                        <flux:icon name="light-bulb" class="w-4 h-4 text-emerald-700 dark:text-emerald-400" />
                        <span class="text-xs font-bold uppercase tracking-wider text-emerald-700 dark:text-emerald-400">Recommendation (R)</span>
                    </div>
                    <div class="bg-emerald-50 dark:bg-emerald-500/10 rounded-xl px-4 py-3 text-sm text-neutral-800 dark:text-neutral-200 whitespace-pre-wrap border border-emerald-200 dark:border-emerald-500/20 leading-relaxed">{{ trim($detailSbar['recommendation'] ?? '') ?: '-' }}</div>
                </div>

                @if($detailSbar['advice'])
                <div class="flex flex-col gap-1.5 pt-2">
                    <div class="flex items-center gap-2">
                        <flux:icon name="chat-bubble-left-ellipsis" class="w-4 h-4 text-sky-600 dark:text-sky-400" />
                        <span class="text-xs font-bold uppercase tracking-wider text-sky-600 dark:text-sky-400">Advice Dokter</span>
                    </div>
                    <div class="bg-sky-50 dark:bg-sky-900/20 rounded-xl px-4 py-3 text-sm text-neutral-800 dark:text-neutral-200 whitespace-pre-wrap border border-sky-200 dark:border-sky-800 leading-relaxed shadow-sm">{{ trim($detailSbar['advice'] ?? '') }}</div>
                </div>
                @endif
            </div>

            {{-- Footer --}}
            <div class="px-6 py-4 border-t border-neutral-100 dark:border-neutral-700 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-neutral-50/70 dark:bg-neutral-900/60">
                
                {{-- Status Indicators --}}
                <div class="flex flex-wrap items-center gap-x-3 gap-y-2 text-sm">
                    <span class="text-neutral-500 text-[10px] uppercase tracking-wider font-bold">Status:</span>
                    <div class="flex items-center gap-1.5 bg-white dark:bg-neutral-800 px-2 py-1 rounded-lg border border-neutral-200 dark:border-neutral-700 shadow-sm">
                        <span class="text-xs text-neutral-500 font-medium">Baca</span>
                        @if($detailSbar['status_baca'] === 'Sudah')
                            <flux:badge size="sm" color="green" class="!px-1.5 !py-0">Sudah</flux:badge>
                        @else
                            <flux:badge size="sm" color="zinc" class="!px-1.5 !py-0">Belum</flux:badge>
                        @endif
                    </div>
                    <div class="flex items-center gap-1.5 bg-white dark:bg-neutral-800 px-2 py-1 rounded-lg border border-neutral-200 dark:border-neutral-700 shadow-sm">
                        <span class="text-xs text-neutral-500 font-medium">Konfirmasi</span>
                        @if($detailSbar['status_konfirmasi'] === 'Sudah')
                            <flux:badge size="sm" color="green" class="!px-1.5 !py-0">Sudah</flux:badge>
                        @else
                            <flux:badge size="sm" color="zinc" class="!px-1.5 !py-0">Belum</flux:badge>
                        @endif
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
                    @if(!empty($detailSbar['tanggal']))
                        <a href="{{ route('modul.rawat-inap.catatan-sbar.cetak', [str_replace('/', '-', $regPeriksa->no_rawat), urlencode($detailSbar['tanggal'])]) }}" target="_blank">
                            <flux:button icon="printer" variant="filled" size="sm" class="!bg-[#4C5C2D] !text-white hover:!bg-[#3d4a24]">
                                Cetak SBAR
                            </flux:button>
                        </a>
                    @endif
                    <flux:button wire:click="$set('detailSbar', null)" variant="ghost" class="w-full sm:w-auto">Tutup</flux:button>
                    
                    {{-- Verifikasi DPJP button hidden per request --}}
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- RIGHT SLIDE-IN PANEL --}}
    <div x-data="{
            open: @entangle('isPanelOpen'),
            autoTime: @entangle('autoTime'),
            interval: null,
            init() {
                this.interval = setInterval(() => {
                    if(this.autoTime && this.open) {
                        let now = new Date();
                        $wire.tanggal_date = now.getFullYear() + '-' + String(now.getMonth()+1).padStart(2,'0') + '-' + String(now.getDate()).padStart(2,'0');
                        $wire.tanggal_time = String(now.getHours()).padStart(2,'0') + ':' + String(now.getMinutes()).padStart(2,'0');
                    }
                }, 1000);
            }
        }"
        @set-autotime.window="autoTime = $event.detail[0].status"
        x-show="open"
        class="fixed inset-0 z-50 flex justify-end"
        style="display: none;">

        {{-- Backdrop --}}
        <div x-show="open"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="absolute inset-0 bg-neutral-900/50 backdrop-blur-sm"
             @click="open = false"></div>

        {{-- Right Panel --}}
        <div x-show="open"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="translate-x-full opacity-0"
             x-transition:enter-end="translate-x-0 opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="translate-x-0 opacity-100"
             x-transition:leave-end="translate-x-full opacity-0"
             class="relative z-10 w-full max-w-4xl bg-white dark:bg-neutral-800 h-full flex flex-col shadow-2xl border-l border-neutral-200 dark:border-neutral-700 overflow-hidden">

            {{-- Panel Header --}}
            <div class="px-8 py-5 border-b border-neutral-100 dark:border-neutral-700 flex items-center justify-between bg-neutral-50/70 dark:bg-neutral-900/60 flex-shrink-0">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-xl bg-[#4C5C2D]/10 flex items-center justify-center">
                        <flux:icon name="chat-bubble-bottom-center-text" class="w-5 h-5 text-[#4C5C2D]" />
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-neutral-800 dark:text-neutral-100">
                            {{ $isEditMode ? 'Edit Catatan SBAR' : 'Input Catatan SBAR' }}
                        </h3>
                        <p class="text-xs text-neutral-500">Formulir komunikasi Situation, Background, Assessment, Recommendation.</p>
                    </div>
                </div>
                <button type="button" @click="open = false" class="text-neutral-400 hover:text-neutral-600 dark:hover:text-neutral-200 transition-colors">
                    <flux:icon name="x-mark" class="w-6 h-6" />
                </button>
            </div>

            {{-- Panel Scrollable Body --}}
            <div class="flex-1 overflow-y-auto p-8 space-y-6">

                {{-- Patient Info Bar --}}
                @php
                    $lastKamar = $regPeriksa->kamarInap->sortByDesc('tgl_masuk')->first() ?? $regPeriksa->kamarInap->last();
                    $nmBangsal = $lastKamar->kamar->bangsal->nm_bangsal ?? null;
                    $kdKamar   = $lastKamar->kd_kamar ?? null;
                    $kelasKmr  = $lastKamar->kamar->kelas ?? null;
                @endphp
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 bg-neutral-50 dark:bg-neutral-900 p-4 rounded-xl border border-neutral-100 dark:border-neutral-800">
                    <div>
                        <div class="text-[10px] text-neutral-400 font-bold uppercase tracking-wider mb-1">No. Rawat</div>
                        <div class="font-mono text-sm font-semibold text-[#4C5C2D]">{{ $regPeriksa->no_rawat }}</div>
                    </div>
                    <div>
                        <div class="text-[10px] text-neutral-400 font-bold uppercase tracking-wider mb-1">No. RM — Nama</div>
                        <div class="text-sm font-bold text-neutral-800 dark:text-neutral-200 truncate" title="{{ $regPeriksa->pasien->nm_pasien ?? '-' }}">
                            {{ $regPeriksa->no_rkm_medis }} — {{ $regPeriksa->pasien->nm_pasien }}
                        </div>
                    </div>
                    <div>
                        <div class="text-[10px] text-neutral-400 font-bold uppercase tracking-wider mb-1">Tgl Lahir</div>
                        <div class="text-sm font-medium text-neutral-800 dark:text-neutral-200">
                            {{ $regPeriksa->pasien->tgl_lahir ? \Carbon\Carbon::parse($regPeriksa->pasien->tgl_lahir)->format('d-m-Y') : '-' }}
                        </div>
                    </div>
                    <div>
                        <div class="text-[10px] text-neutral-400 font-bold uppercase tracking-wider mb-1">Kamar / Bangsal</div>
                        <div class="text-sm font-bold text-neutral-800 dark:text-neutral-200 flex items-center gap-1.5 flex-wrap">
                            @if($nmBangsal)
                                <span>{{ $nmBangsal }}</span>
                                <span class="font-mono text-xs bg-[#4C5C2D]/10 dark:bg-[#4C5C2D]/20 text-[#4C5C2D] dark:text-[#8CC7C4] px-1.5 py-0.5 rounded font-bold border border-[#4C5C2D]/20">
                                    {{ $kdKamar }}
                                </span>
                            @else
                                <span class="text-neutral-400 font-normal italic">-</span>
                            @endif
                        </div>
                        @if($kelasKmr)
                            <div class="text-[11px] text-neutral-500 font-medium mt-0.5">{{ $kelasKmr }}</div>
                        @endif
                    </div>
                </div>

                {{-- Tanggal & Petugas/Dokter Row --}}
                <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
                    <div class="md:col-span-3">
                        <flux:input wire:model="tanggal_date" type="date" label="Tanggal *" required />
                    </div>
                    <div class="md:col-span-3">
                        <div class="flex items-center justify-between mb-1">
                            <flux:label class="!mb-0">Jam *</flux:label>
                            <div class="flex items-center gap-1.5 mt-2">
                                <flux:checkbox wire:model="autoTime" id="autoTimeSbar" />
                                <label for="autoTimeSbar" class="text-xs font-medium text-neutral-600 dark:text-neutral-400 cursor-pointer">Waktu Otomatis</label>
                            </div>
                        </div>
                        <flux:input wire:model="tanggal_time" type="time" required />
                    </div>
                    
                    {{-- Petugas Lookup --}}
                    <div class="md:col-span-3 relative">
                        <flux:input wire:model.live.debounce.300ms="petugasSearch"
                                    label="Dilakukan Oleh *"
                                    placeholder="NIP/Nama Perawat..."
                                    icon="magnifying-glass" />

                        @if(!empty($petugasList))
                            <div class="absolute z-[60] left-0 right-0 mt-1 bg-white dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl shadow-xl max-h-48 overflow-y-auto">
                                @foreach($petugasList as $p)
                                    <button type="button"
                                            wire:click="selectPetugas('{{ $p->nip }}', '{{ $p->nama }}')"
                                            class="w-full text-left px-4 py-3 hover:bg-neutral-50 dark:hover:bg-neutral-700 border-b border-neutral-100 dark:border-neutral-700 last:border-0 transition-colors group">
                                        <div class="font-bold text-sm text-neutral-700 dark:text-neutral-200 group-hover:text-[#4C5C2D]">{{ $p->nama }}</div>
                                        <div class="text-[10px] text-neutral-400 font-mono tracking-wider">{{ $p->nip }}</div>
                                    </button>
                                @endforeach
                            </div>
                        @endif

                        @if($nip)
                            <div class="mt-2 p-2 bg-[#4C5C2D]/5 border border-[#4C5C2D]/20 rounded-xl flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <flux:icon name="user" class="w-4 h-4 text-[#4C5C2D]" />
                                    <div>
                                        <div class="text-xs font-bold text-[#4C5C2D] truncate max-w-[100px]">{{ $nmPetugas }}</div>
                                    </div>
                                </div>
                                <button type="button" wire:click="$set('nip', null)" class="text-neutral-400 hover:text-red-500 transition-colors">
                                    <flux:icon name="x-mark" size="xs" />
                                </button>
                            </div>
                        @endif
                    </div>
                    
                    {{-- Dokter DPJP Lookup --}}
                    <div class="md:col-span-3 relative">
                        <flux:input wire:model.live.debounce.300ms="dokterSearch"
                                    label="DPJP *"
                                    placeholder="Kode/Nama Dokter..."
                                    icon="magnifying-glass" />

                        @if(!empty($dokterList))
                            <div class="absolute z-[60] left-0 right-0 mt-1 bg-white dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl shadow-xl max-h-48 overflow-y-auto">
                                @foreach($dokterList as $d)
                                    <button type="button"
                                            wire:click="selectDokter('{{ $d->kd_dokter }}', '{{ $d->nm_dokter }}')"
                                            class="w-full text-left px-4 py-3 hover:bg-neutral-50 dark:hover:bg-neutral-700 border-b border-neutral-100 dark:border-neutral-700 last:border-0 transition-colors group">
                                        <div class="font-bold text-sm text-neutral-700 dark:text-neutral-200 group-hover:text-[#4C5C2D]">{{ $d->nm_dokter }}</div>
                                        <div class="text-[10px] text-neutral-400 font-mono tracking-wider">{{ $d->kd_dokter }}</div>
                                    </button>
                                @endforeach
                            </div>
                        @endif

                        @if($kd_dokter)
                            <div class="mt-2 p-2 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <flux:icon name="star" class="w-4 h-4 text-blue-600 dark:text-blue-400" />
                                    <div>
                                        <div class="text-xs font-bold text-blue-700 dark:text-blue-300 truncate max-w-[100px]" title="{{ $nmDokter }}">{{ $nmDokter }}</div>
                                    </div>
                                </div>
                                <button type="button" wire:click="$set('kd_dokter', null)" class="text-blue-400 hover:text-red-500 transition-colors">
                                    <flux:icon name="x-mark" size="xs" />
                                </button>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Vertical Ordered SBAR Form (Urut Atas ke Bawah) --}}
                <div class="space-y-6">
                    {{-- 1. SITUATION (S) --}}
                    <div class="bg-amber-50/50 dark:bg-amber-500/5 border border-amber-100 dark:border-amber-500/10 rounded-xl p-5 transition-colors focus-within:bg-amber-50 dark:focus-within:bg-amber-500/10 focus-within:border-amber-200 dark:focus-within:border-amber-500/20">
                        <flux:label class="flex items-center justify-between !mb-2">
                            <div class="flex items-center gap-2">
                                <flux:icon name="information-circle" class="w-4 h-4 text-amber-600 dark:text-amber-400" />
                                <span class="text-amber-800 dark:text-amber-300 font-bold uppercase tracking-wider text-xs">Situation (S) *</span>
                            </div>
                            <flux:tooltip content="Apa yang terjadi pada saat ini ? Apa keluhannya, kapan terjadinya dan bagaimana  parahnya." position="left">
                                <span class="text-[11px] text-amber-700/90 dark:text-amber-400 cursor-help flex items-center gap-1 font-medium bg-amber-100/70 dark:bg-amber-500/20 px-2.5 py-0.5 rounded-full" title="Apa yang terjadi pada saat ini ? Apa keluhannya, kapan terjadinya dan bagaimana  parahnya.">
                                    <flux:icon name="question-mark-circle" class="w-3.5 h-3.5 text-amber-600" /> Keterangan
                                </span>
                            </flux:tooltip>
                        </flux:label>
                        <textarea wire:model="situation" rows="3"
                                  placeholder="Apa yang terjadi pada saat ini ?&#10;Apa keluhannya, kapan terjadinya dan bagaimana  parahnya."
                                  class="w-full rounded-lg border border-amber-200/60 dark:border-amber-500/20 bg-white dark:bg-neutral-900 text-neutral-800 dark:text-neutral-200 text-sm px-4 py-3 focus:outline-none focus:ring-2 focus:ring-amber-500/40 focus:border-amber-500 transition resize-y shadow-sm"></textarea>
                        @error('situation') <div class="text-red-500 text-xs mt-1">{{ $message }}</div> @enderror
                    </div>

                    {{-- 2. BACKGROUND (B) --}}
                    <div class="bg-blue-50/50 dark:bg-blue-500/5 border border-blue-100 dark:border-blue-500/10 rounded-xl p-5 transition-colors focus-within:bg-blue-50 dark:focus-within:bg-blue-500/10 focus-within:border-blue-200 dark:focus-within:border-blue-500/20 space-y-4">
                        <div class="flex items-center justify-between border-b border-blue-200/50 dark:border-blue-500/20 pb-3">
                            <div class="flex items-center gap-2">
                                <flux:icon name="clock" class="w-4 h-4 text-blue-600 dark:text-blue-400" />
                                <span class="text-blue-800 dark:text-blue-300 font-bold uppercase tracking-wider text-xs">Background (B) *</span>
                            </div>
                            <flux:tooltip content="Informasi yang berkaitan / mungkin berkaitan dengan problemnya   (Ringkas dan penting untuk menerangkan problem yang terjadi atau untuk menentukan tindakan selanjutnya) " position="left">
                                <span class="text-[11px] text-blue-700/90 dark:text-blue-400 cursor-help flex items-center gap-1 font-medium bg-blue-100/70 dark:bg-blue-500/20 px-2.5 py-0.5 rounded-full" title="Informasi yang berkaitan / mungkin berkaitan dengan problemnya   (Ringkas dan penting untuk menerangkan problem yang terjadi atau untuk menentukan tindakan selanjutnya) ">
                                    <flux:icon name="question-mark-circle" class="w-3.5 h-3.5 text-blue-600" /> Keterangan
                                </span>
                            </flux:tooltip>
                        </div>
                        <p class="text-xs text-blue-600/90 dark:text-blue-400/80 leading-relaxed italic">
                            Informasi yang berkaitan / mungkin berkaitan dengan problemnya (Ringkas dan penting untuk menerangkan problem yang terjadi atau untuk menentukan tindakan selanjutnya)
                        </p>

                        {{-- a. Riwayat penyakit dahulu / MRS --}}
                        <div>
                            <flux:label class="text-xs font-semibold text-neutral-700 dark:text-neutral-300 !mb-1.5">
                                Riwayat Penyakit Dahulu (MRS/Dirawat Dengan)
                            </flux:label>
                            <textarea wire:model="bg_riwayat_penyakit_dahulu" rows="2"
                                      placeholder="Contoh: Pasca SC 1 bulan yang lalu, tidak ada bendungan ASI..."
                                      class="w-full rounded-lg border border-blue-200/60 dark:border-blue-500/20 bg-white dark:bg-neutral-900 text-neutral-800 dark:text-neutral-200 text-sm px-3.5 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500/40 focus:border-blue-500 transition resize-y shadow-sm"></textarea>
                        </div>

                        {{-- b. Riwayat penyakit --}}
                        <div>
                            <flux:label class="text-xs font-semibold text-neutral-700 dark:text-neutral-300 !mb-1.5">
                                Riwayat Penyakit
                            </flux:label>
                            <textarea wire:model="bg_riwayat_penyakit" rows="2"
                                      placeholder="Contoh: Tidak ada riwayat penyakit, HT (-), DM (-)..."
                                      class="w-full rounded-lg border border-blue-200/60 dark:border-blue-500/20 bg-white dark:bg-neutral-900 text-neutral-800 dark:text-neutral-200 text-sm px-3.5 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500/40 focus:border-blue-500 transition resize-y shadow-sm"></textarea>
                        </div>

                        {{-- c. Informasi Klinis --}}
                        <div>
                            <flux:label class="text-xs font-semibold text-neutral-700 dark:text-neutral-300 !mb-1.5">
                                Informasi Klinis
                            </flux:label>
                            <textarea wire:model="bg_informasi_klinis" rows="2"
                                      placeholder="Contoh: K/U cukup, kes CM, GCS 4-5-6, mual (+)..."
                                      class="w-full rounded-lg border border-blue-200/60 dark:border-blue-500/20 bg-white dark:bg-neutral-900 text-neutral-800 dark:text-neutral-200 text-sm px-3.5 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500/40 focus:border-blue-500 transition resize-y shadow-sm"></textarea>
                        </div>

                        {{-- d. Lab / Pemeriksaan penunjang lain --}}
                        <div>
                            <flux:label class="text-xs font-semibold text-neutral-700 dark:text-neutral-300 !mb-1.5">
                                Lab / Pemeriksaan Penunjang Lain
                            </flux:label>
                            <textarea wire:model="bg_penunjang" rows="2"
                                      placeholder="Contoh: DL, WIDAL, Hasil USG, Radiologi..."
                                      class="w-full rounded-lg border border-blue-200/60 dark:border-blue-500/20 bg-white dark:bg-neutral-900 text-neutral-800 dark:text-neutral-200 text-sm px-3.5 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500/40 focus:border-blue-500 transition resize-y shadow-sm"></textarea>
                        </div>

                        {{-- e. Riwayat Alergi --}}
                        <div>
                            <flux:label class="text-xs font-semibold text-neutral-700 dark:text-neutral-300 !mb-1.5">
                                Riwayat Alergi
                            </flux:label>
                            <input wire:model="bg_alergi" type="text"
                                   placeholder="Contoh: Tidak ada alergi / Alergi Amoxicillin..."
                                   class="w-full rounded-lg border border-blue-200/60 dark:border-blue-500/20 bg-white dark:bg-neutral-900 text-neutral-800 dark:text-neutral-200 text-sm px-3.5 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500/40 focus:border-blue-500 transition shadow-sm" />
                        </div>

                        {{-- f. Tanda Vital Saat Ini --}}
                        <div class="bg-white/80 dark:bg-neutral-900/60 border border-blue-200/80 dark:border-blue-500/30 rounded-xl p-4 shadow-sm">
                            <div class="flex items-center gap-1.5 text-xs font-bold text-blue-900 dark:text-blue-200 uppercase tracking-wider mb-3">
                                <flux:icon name="heart" class="w-4 h-4 text-rose-500" />
                                Tanda Vital Saat Ini
                            </div>
                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-3">
                                <div>
                                    <flux:label class="!text-[11px] !mb-1 text-neutral-600 dark:text-neutral-400">Kesadaran</flux:label>
                                    <select wire:model="bg_kesadaran"
                                            class="w-full rounded-lg border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-neutral-800 dark:text-neutral-200 text-xs px-2.5 py-2 focus:ring-2 focus:ring-blue-500/40 focus:outline-none shadow-sm cursor-pointer">
                                        <option value="Compos Mentis">Compos Mentis</option>
                                        <option value="Somnolence">Somnolence</option>
                                        <option value="Sopor">Sopor</option>
                                        <option value="Coma">Coma</option>
                                        <option value="Apatis">Apatis</option>
                                        <option value="Delirium">Delirium</option>
                                    </select>
                                </div>
                                <div>
                                    <flux:label class="!text-[11px] !mb-1 text-neutral-600 dark:text-neutral-400">TD (mmHg)</flux:label>
                                    <input wire:model="bg_tensi" type="text" placeholder="137/89"
                                           class="w-full rounded-lg border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-neutral-800 dark:text-neutral-200 text-xs px-2.5 py-2 focus:ring-2 focus:ring-blue-500/40 focus:outline-none shadow-sm" />
                                </div>
                                <div>
                                    <flux:label class="!text-[11px] !mb-1 text-neutral-600 dark:text-neutral-400">Nadi (x/mnt)</flux:label>
                                    <input wire:model="bg_nadi" type="text" placeholder="119"
                                           class="w-full rounded-lg border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-neutral-800 dark:text-neutral-200 text-xs px-2.5 py-2 focus:ring-2 focus:ring-blue-500/40 focus:outline-none shadow-sm" />
                                </div>
                                <div>
                                    <flux:label class="!text-[11px] !mb-1 text-neutral-600 dark:text-neutral-400">Resp (/menit)</flux:label>
                                    <input wire:model="bg_respirasi" type="text" placeholder="20"
                                           class="w-full rounded-lg border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-neutral-800 dark:text-neutral-200 text-xs px-2.5 py-2 focus:ring-2 focus:ring-blue-500/40 focus:outline-none shadow-sm" />
                                </div>
                                <div>
                                    <flux:label class="!text-[11px] !mb-1 text-neutral-600 dark:text-neutral-400">Temp (°C)</flux:label>
                                    <input wire:model="bg_suhu" type="text" placeholder="38.5"
                                           class="w-full rounded-lg border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-neutral-800 dark:text-neutral-200 text-xs px-2.5 py-2 focus:ring-2 focus:ring-blue-500/40 focus:outline-none shadow-sm" />
                                </div>
                                <div>
                                    <flux:label class="!text-[11px] !mb-1 text-neutral-600 dark:text-neutral-400">SpO2 (%)</flux:label>
                                    <input wire:model="bg_spo2" type="text" placeholder="98"
                                           class="w-full rounded-lg border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-neutral-800 dark:text-neutral-200 text-xs px-2.5 py-2 focus:ring-2 focus:ring-blue-500/40 focus:outline-none shadow-sm" />
                                </div>
                            </div>
                        </div>

                        {{-- g. Terapi saat ini --}}
                        <div>
                            <flux:label class="text-xs font-semibold text-neutral-700 dark:text-neutral-300 !mb-1.5">
                                Terapi Saat Ini
                            </flux:label>
                            <textarea wire:model="bg_terapi" rows="2"
                                      placeholder="Contoh: Terpasang Infus NaCl 0.9%..."
                                      class="w-full rounded-lg border border-blue-200/60 dark:border-blue-500/20 bg-white dark:bg-neutral-900 text-neutral-800 dark:text-neutral-200 text-sm px-3.5 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500/40 focus:border-blue-500 transition resize-y shadow-sm"></textarea>
                        </div>
                    </div>

                    {{-- 3. ASSESSMENT (A) --}}
                    <div class="bg-purple-50/50 dark:bg-purple-500/5 border border-purple-100 dark:border-purple-500/10 rounded-xl p-5 transition-colors focus-within:bg-purple-50 dark:focus-within:bg-purple-500/10 focus-within:border-purple-200 dark:focus-within:border-purple-500/20">
                        <flux:label class="flex items-center justify-between !mb-2">
                            <div class="flex items-center gap-2">
                                <flux:icon name="clipboard-document-check" class="w-4 h-4 text-purple-600 dark:text-purple-400" />
                                <span class="text-purple-800 dark:text-purple-300 font-bold uppercase tracking-wider text-xs">Assessment (A) *</span>
                            </div>
                            <flux:tooltip content="Kesimpulan diagnosa dari situasi dan background" position="left">
                                <span class="text-[11px] text-purple-700/90 dark:text-purple-400 cursor-help flex items-center gap-1 font-medium bg-purple-100/70 dark:bg-purple-500/20 px-2.5 py-0.5 rounded-full" title="Kesimpulan diagnosa dari situasi dan background">
                                    <flux:icon name="question-mark-circle" class="w-3.5 h-3.5 text-purple-600" /> Keterangan
                                </span>
                            </flux:tooltip>
                        </flux:label>
                        <textarea wire:model="assessment" rows="3"
                                  placeholder="Kesimpulan diagnosa dari situasi dan background"
                                  class="w-full rounded-lg border border-purple-200/60 dark:border-purple-500/20 bg-white dark:bg-neutral-900 text-neutral-800 dark:text-neutral-200 text-sm px-4 py-3 focus:outline-none focus:ring-2 focus:ring-purple-500/40 focus:border-purple-500 transition resize-y shadow-sm"></textarea>
                        @error('assessment') <div class="text-red-500 text-xs mt-1">{{ $message }}</div> @enderror
                    </div>

                    {{-- 4. RECOMMENDATION (R) --}}
                    <div class="bg-emerald-50/50 dark:bg-emerald-500/5 border border-emerald-100 dark:border-emerald-500/10 rounded-xl p-5 transition-colors focus-within:bg-emerald-50 dark:focus-within:bg-emerald-500/10 focus-within:border-emerald-200 dark:focus-within:border-emerald-500/20">
                        <flux:label class="flex items-center justify-between !mb-2">
                            <div class="flex items-center gap-2">
                                <flux:icon name="light-bulb" class="w-4 h-4 text-emerald-600 dark:text-emerald-400" />
                                <span class="text-emerald-800 dark:text-emerald-300 font-bold uppercase tracking-wider text-xs">Recommendation (R) *</span>
                            </div>
                            <flux:tooltip content="usulan dan petunjuk untuk mengatasi problem" position="left">
                                <span class="text-[11px] text-emerald-700/90 dark:text-emerald-400 cursor-help flex items-center gap-1 font-medium bg-emerald-100/70 dark:bg-emerald-500/20 px-2.5 py-0.5 rounded-full" title="usulan dan petunjuk untuk mengatasi problem">
                                    <flux:icon name="question-mark-circle" class="w-3.5 h-3.5 text-emerald-600" /> Keterangan
                                </span>
                            </flux:tooltip>
                        </flux:label>
                        <textarea wire:model="recommendation" rows="3"
                                  placeholder="usulan dan petunjuk untuk mengatasi problem"
                                  class="w-full rounded-lg border border-emerald-200/60 dark:border-emerald-500/20 bg-white dark:bg-neutral-900 text-neutral-800 dark:text-neutral-200 text-sm px-4 py-3 focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-500 transition resize-y shadow-sm"></textarea>
                        @error('recommendation') <div class="text-red-500 text-xs mt-1">{{ $message }}</div> @enderror
                    </div>
                </div>

                {{-- Full width Advice & Status --}}
                <div class="space-y-6 border-t border-neutral-100 dark:border-neutral-800 pt-6">
                    <div class="bg-sky-50/50 dark:bg-sky-500/5 border border-sky-100 dark:border-sky-500/10 rounded-xl p-4 transition-colors focus-within:bg-sky-50 dark:focus-within:bg-sky-500/10 focus-within:border-sky-200 dark:focus-within:border-sky-500/20">
                        <flux:label class="flex items-center gap-2 !mb-3">
                            <flux:icon name="chat-bubble-left-ellipsis" class="w-4 h-4 text-sky-600 dark:text-sky-400" />
                            <span class="text-sky-800 dark:text-sky-300 font-bold uppercase tracking-wider text-xs">Advice Dokter</span>
                        </flux:label>
                        <textarea wire:model="advice" rows="3" placeholder="Tuliskan saran/instruksi balasan dari dokter..." class="w-full rounded-lg border border-sky-200/60 dark:border-sky-500/20 bg-white dark:bg-neutral-900 text-neutral-800 dark:text-neutral-200 text-sm px-4 py-3 focus:outline-none focus:ring-2 focus:ring-sky-500/40 focus:border-sky-500 transition resize-y shadow-sm"></textarea>
                    </div>
                    
                    <div class="bg-neutral-50 dark:bg-neutral-800/50 border border-neutral-200 dark:border-neutral-700 rounded-xl p-5">
                        <h4 class="text-[10px] font-bold text-neutral-500 uppercase tracking-wider mb-4 flex items-center gap-2">
                            <flux:icon name="adjustments-horizontal" class="w-4 h-4 text-neutral-400" />
                            Pengaturan Status
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <flux:label class="!mb-2">Status Baca</flux:label>
                                <select wire:model="status_baca" class="w-full rounded-lg border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-neutral-800 dark:text-neutral-200 text-sm px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-[#4C5C2D]/40 focus:border-[#4C5C2D] transition shadow-sm">
                                    <option value="Belum">Belum dibaca</option>
                                    <option value="Sudah">Sudah dibaca</option>
                                </select>
                            </div>
                            <div>
                                <flux:label class="!mb-2">Status Konfirmasi</flux:label>
                                <select wire:model="status_konfirmasi" class="w-full rounded-lg border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-neutral-800 dark:text-neutral-200 text-sm px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-[#4C5C2D]/40 focus:border-[#4C5C2D] transition shadow-sm">
                                    <option value="Belum">Belum dikonfirmasi</option>
                                    <option value="Sudah">Sudah dikonfirmasi</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Panel Footer --}}
            <div class="px-8 py-5 bg-neutral-50/70 dark:bg-neutral-900/60 border-t border-neutral-100 dark:border-neutral-700 flex justify-end gap-3 flex-shrink-0">
                <flux:button @click="open = false" variant="ghost">Batal</flux:button>
                <flux:button wire:click="save" variant="filled" class="!bg-[#4C5C2D] !text-white hover:!bg-[#3d4a24]">
                    Simpan Catatan
                </flux:button>
            </div>
        </div>
    </div>

</div>
