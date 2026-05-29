<div class="flex flex-col gap-6 {{ $isEmbedded ? '' : 'pb-8' }}">
    @if(!$isEmbedded)
    {{-- Header / Breadcrumb --}}
    <div class="flex flex-col gap-1">
        <div class="flex items-center gap-3">
            <a href="{{ route('modul.rawat-inap.perawatan-tindakan', str_replace('/', '-', $regPeriksa->no_rawat)) }}" wire:navigate
               class="flex items-center justify-center w-8 h-8 rounded-lg transition-colors hover:bg-neutral-100 dark:hover:bg-neutral-700">
                <flux:icon name="chevron-left" class="w-5 h-5 text-neutral-500" />
            </a>
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
                                    <div class="flex items-center gap-1">
                                        <span class="text-neutral-500 w-16">Verif:</span>
                                        @if($catatan['status_verifikasi'] === 'Sudah')
                                            <flux:badge size="sm" color="lime" class="!px-1.5 !py-0">✓ DPJP</flux:badge>
                                        @else
                                            <flux:badge size="sm" color="zinc" class="!px-1.5 !py-0">Belum</flux:badge>
                                        @endif
                                    </div>
                                </div>
                            </flux:table.cell>
                            <flux:table.cell>
                                <div class="flex justify-center gap-2">
                                    <flux:button wire:click="showDetail('{{ $catatan['tanggal'] }}')" icon="eye" size="xs" variant="ghost" class="!text-blue-500 hover:!bg-blue-50 dark:hover:!bg-blue-500/10" />
                                    <flux:button wire:click="edit('{{ $catatan['tanggal'] }}')" icon="pencil-square" size="xs" variant="ghost" />
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
                <button wire:click="$set('detailSbar', null)" class="text-neutral-400 hover:text-neutral-600 transition-colors">
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
            <div class="px-6 py-5 space-y-4 max-h-[55vh] overflow-y-auto">
                @foreach([
                    ['label' => 'Situation (S)',      'value' => $detailSbar['situation'],      'color' => 'amber'],
                    ['label' => 'Background (B)',     'value' => $detailSbar['background'],     'color' => 'blue'],
                    ['label' => 'Assessment (A)',     'value' => $detailSbar['assessment'],     'color' => 'purple'],
                    ['label' => 'Recommendation (R)', 'value' => $detailSbar['recommendation'], 'color' => 'green'],
                ] as $row)
                <div class="flex gap-3">
                    <div class="w-36 flex-shrink-0">
                        <span class="text-xs font-bold uppercase tracking-wider text-neutral-500">{{ $row['label'] }}</span>
                    </div>
                    <div class="flex-1 bg-neutral-50 dark:bg-neutral-900 rounded-lg px-3 py-2 text-sm text-neutral-800 dark:text-neutral-200 whitespace-pre-wrap border border-neutral-100 dark:border-neutral-800">
                        {{ $row['value'] ?: '-' }}
                    </div>
                </div>
                @endforeach

                @if($detailSbar['advice'])
                <div class="flex gap-3">
                    <div class="w-36 flex-shrink-0">
                        <span class="text-xs font-bold uppercase tracking-wider text-neutral-500">Advice Dokter</span>
                    </div>
                    <div class="flex-1 bg-blue-50 dark:bg-blue-900/20 rounded-lg px-3 py-2 text-sm text-neutral-800 dark:text-neutral-200 whitespace-pre-wrap border border-blue-100 dark:border-blue-800">
                        {{ $detailSbar['advice'] }}
                    </div>
                </div>
                @endif
            </div>

            {{-- Footer --}}
            <div class="px-6 py-4 border-t border-neutral-100 dark:border-neutral-700 flex items-center justify-between bg-neutral-50/70 dark:bg-neutral-900/60">
                <div class="flex items-center gap-4 text-sm">
                    <div class="flex items-center gap-1.5">
                        <span class="text-neutral-500">Baca:</span>
                        @if($detailSbar['status_baca'] === 'Sudah')
                            <flux:badge size="sm" color="green">Sudah</flux:badge>
                        @else
                            <flux:badge size="sm" color="zinc">Belum</flux:badge>
                        @endif
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="text-neutral-500">Konfirmasi:</span>
                        @if($detailSbar['status_konfirmasi'] === 'Sudah')
                            <flux:badge size="sm" color="green">Sudah</flux:badge>
                        @else
                            <flux:badge size="sm" color="zinc">Belum</flux:badge>
                        @endif
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="text-neutral-500">Verifikasi DPJP:</span>
                        @if($detailSbar['status_verifikasi'] === 'Sudah')
                            <flux:badge size="sm" color="lime">✓ Terverifikasi</flux:badge>
                        @else
                            <flux:badge size="sm" color="zinc">Belum</flux:badge>
                        @endif
                    </div>
                </div>

                <div class="flex gap-2">
                    <flux:button wire:click="$set('detailSbar', null)" variant="ghost" size="sm">Tutup</flux:button>
                    @if($detailSbar['status_verifikasi'] !== 'Sudah')
                        <flux:button
                            wire:click="verifikasi"
                            wire:confirm="Verifikasi catatan SBAR ini sebagai DPJP?"
                            variant="filled"
                            size="sm"
                            icon="check-circle"
                            class="!bg-[#4C5C2D] !text-white hover:!bg-[#3d4a24]">
                            Verifikasi DPJP
                        </flux:button>
                    @else
                        <flux:button variant="filled" size="sm" icon="check-circle" disabled class="!bg-green-600 !text-white opacity-60 cursor-not-allowed">
                            Sudah Diverifikasi
                        </flux:button>
                    @endif
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
                <button @click="open = false" class="text-neutral-400 hover:text-neutral-600 dark:hover:text-neutral-200 transition-colors">
                    <flux:icon name="x-mark" class="w-6 h-6" />
                </button>
            </div>

            {{-- Panel Scrollable Body --}}
            <div class="flex-1 overflow-y-auto p-8 space-y-6">

                {{-- Patient Info Bar --}}
                <div class="grid grid-cols-3 gap-4 bg-neutral-50 dark:bg-neutral-900 p-4 rounded-xl border border-neutral-100 dark:border-neutral-800">
                    <div>
                        <div class="text-[10px] text-neutral-400 font-bold uppercase tracking-wider mb-1">No. Rawat</div>
                        <div class="font-mono text-sm font-semibold text-[#4C5C2D]">{{ $regPeriksa->no_rawat }}</div>
                    </div>
                    <div>
                        <div class="text-[10px] text-neutral-400 font-bold uppercase tracking-wider mb-1">No. RM — Nama</div>
                        <div class="text-sm font-bold text-neutral-800 dark:text-neutral-200">
                            {{ $regPeriksa->no_rkm_medis }} — {{ $regPeriksa->pasien->nm_pasien }}
                        </div>
                    </div>
                    <div>
                        <div class="text-[10px] text-neutral-400 font-bold uppercase tracking-wider mb-1">Tgl Lahir</div>
                        <div class="text-sm font-medium text-neutral-800 dark:text-neutral-200">
                            {{ $regPeriksa->pasien->tgl_lahir ? \Carbon\Carbon::parse($regPeriksa->pasien->tgl_lahir)->format('d-m-Y') : '-' }}
                        </div>
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
                                <button wire:click="$set('nip', null)" class="text-neutral-400 hover:text-red-500 transition-colors">
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
                                <button wire:click="$set('kd_dokter', null)" class="text-blue-400 hover:text-red-500 transition-colors">
                                    <flux:icon name="x-mark" size="xs" />
                                </button>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- 2-Column SBAR Textareas --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- LEFT COLUMN --}}
                    <div class="space-y-5">
                        <div>
                            <flux:label>Situation (S) *</flux:label>
                            <textarea wire:model="situation"
                                      rows="4"
                                      placeholder="Tuliskan situasi pasien saat ini..."
                                      class="mt-1 w-full rounded-xl border border-neutral-200 dark:border-neutral-600 bg-white dark:bg-neutral-900 text-neutral-800 dark:text-neutral-200 text-sm px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#4C5C2D]/40 focus:border-[#4C5C2D] transition resize-y"
                            ></textarea>
                            @error('situation') <div class="text-red-500 text-xs mt-1">{{ $message }}</div> @enderror
                        </div>
                        <div>
                            <flux:label>Background (B) *</flux:label>
                            <textarea wire:model="background"
                                      rows="4"
                                      placeholder="Tuliskan latar belakang medis yang relevan..."
                                      class="mt-1 w-full rounded-xl border border-neutral-200 dark:border-neutral-600 bg-white dark:bg-neutral-900 text-neutral-800 dark:text-neutral-200 text-sm px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#4C5C2D]/40 focus:border-[#4C5C2D] transition resize-y"
                            ></textarea>
                            @error('background') <div class="text-red-500 text-xs mt-1">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    {{-- RIGHT COLUMN --}}
                    <div class="space-y-5">
                        <div>
                            <flux:label>Assessment (A) *</flux:label>
                            <textarea wire:model="assessment"
                                      rows="4"
                                      placeholder="Tuliskan hasil asesmen kesimpulan klinis..."
                                      class="mt-1 w-full rounded-xl border border-neutral-200 dark:border-neutral-600 bg-white dark:bg-neutral-900 text-neutral-800 dark:text-neutral-200 text-sm px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#4C5C2D]/40 focus:border-[#4C5C2D] transition resize-y"
                            ></textarea>
                            @error('assessment') <div class="text-red-500 text-xs mt-1">{{ $message }}</div> @enderror
                        </div>
                        <div>
                            <flux:label>Recommendation (R) *</flux:label>
                            <textarea wire:model="recommendation"
                                      rows="4"
                                      placeholder="Tuliskan rekomendasi asuhan atau intervensi..."
                                      class="mt-1 w-full rounded-xl border border-neutral-200 dark:border-neutral-600 bg-white dark:bg-neutral-900 text-neutral-800 dark:text-neutral-200 text-sm px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#4C5C2D]/40 focus:border-[#4C5C2D] transition resize-y"
                            ></textarea>
                            @error('recommendation') <div class="text-red-500 text-xs mt-1">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

                {{-- Full width Advice & Status --}}
                <div class="space-y-5 border-t border-neutral-100 dark:border-neutral-800 pt-6">
                    <div>
                        <flux:label>Advice Dokter</flux:label>
                        <textarea wire:model="advice"
                                  rows="3"
                                  placeholder="Tuliskan saran/instruksi balasan dari dokter..."
                                  class="mt-1 w-full rounded-xl border border-neutral-200 dark:border-neutral-600 bg-blue-50/50 dark:bg-blue-900/10 text-neutral-800 dark:text-neutral-200 text-sm px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500/40 focus:border-blue-500 transition resize-y"
                        ></textarea>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                        <flux:label class="!mb-1">Status Baca</flux:label>
                        <select wire:model="status_baca"
                                class="mt-1 w-full rounded-xl border border-neutral-200 dark:border-neutral-600 bg-white dark:bg-neutral-900 text-neutral-800 dark:text-neutral-200 text-sm px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-[#4C5C2D]/40 focus:border-[#4C5C2D] transition">
                            <option value="Belum">Belum dibaca</option>
                            <option value="Sudah">Sudah dibaca</option>
                        </select>
                    </div>
                    <div>
                        <flux:label class="!mb-1">Status Konfirmasi</flux:label>
                        <select wire:model="status_konfirmasi"
                                class="mt-1 w-full rounded-xl border border-neutral-200 dark:border-neutral-600 bg-white dark:bg-neutral-900 text-neutral-800 dark:text-neutral-200 text-sm px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-[#4C5C2D]/40 focus:border-[#4C5C2D] transition">
                            <option value="Belum">Belum dikonfirmasi</option>
                            <option value="Sudah">Sudah dikonfirmasi</option>
                        </select>
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
