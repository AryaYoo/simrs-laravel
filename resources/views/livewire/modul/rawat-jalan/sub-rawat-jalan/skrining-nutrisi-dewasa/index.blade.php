<div class="flex flex-col gap-6 pb-8">
    {{-- Header / Breadcrumb --}}
    <div class="flex flex-col gap-1">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <button type="button" onclick="history.length > 1 ? history.back() : window.location.href = '{{ route('modul.rawat-jalan.index') }}'" class="flex items-center justify-center w-10 h-8 rounded-md bg-[#4C5C2D] transition-colors hover:bg-[#3d4b24] shadow-sm">
                    <flux:icon name="chevron-left" class="w-5 h-5 text-white" />
                </button>
                <div>
                    <nav class="text-xs text-neutral-400 mb-0.5">
                        <a href="{{ route('modul.rawat-jalan.perawatan-tindakan', str_replace('/', '-', $regPeriksa->no_rawat)) }}" wire:navigate class="hover:underline">Perawatan & Tindakan</a>
                        <span class="mx-1">/</span>
                        <span>RM Gizi</span>
                        <span class="mx-1">/</span>
                        <span class="text-neutral-600 dark:text-neutral-300 font-medium">Skrining Nutrisi Pasien Dewasa</span>
                    </nav>
                    <h1 class="text-xl font-bold text-neutral-800 dark:text-neutral-100">Skrining Nutrisi Pasien Dewasa</h1>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <flux:button type="button" icon="book-open" variant="subtle" size="sm" class="!text-neutral-600 dark:!text-neutral-300 hover:!text-[#4C5C2D]" title="Panduan SOP Skrining Gizi">
                    SOP
                </flux:button>
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
                    <span class="font-mono bg-neutral-100 dark:bg-neutral-900 px-1.5 py-0.5 rounded text-xs">{{ $regPeriksa->no_rkm_medis }}</span>
                    <span>•</span>
                    <span>{{ $regPeriksa->pasien->jk === 'L' ? 'Laki-laki' : 'Perempuan' }}</span>
                    <span>•</span>
                    <span>{{ $regPeriksa->pasien->tgl_lahir ? \Carbon\Carbon::parse($regPeriksa->pasien->tgl_lahir)->age . ' Thn' : '-' }}</span>
                    <span>•</span>
                    <span class="font-medium text-neutral-600 dark:text-neutral-300">{{ $regPeriksa->poliklinik->nm_poli ?? '-' }}</span>
                    <span>•</span>
                    <span class="font-medium text-neutral-600 dark:text-neutral-300">{{ $regPeriksa->dokter->nm_dokter ?? '-' }}</span>
                </div>
            </div>
        </div>
        <div class="text-right">
            <div class="text-xs text-neutral-400 mb-0.5">No. Rawat</div>
            <div class="font-mono font-bold text-[#4C5C2D] dark:text-[#8CC7C4] text-sm md:text-base">{{ $regPeriksa->no_rawat }}</div>
        </div>
    </div>

    {{-- Table List Data Skrining Nutrisi Pasien Dewasa --}}
    <div class="bg-white dark:bg-neutral-800 rounded-xl border border-neutral-200 dark:border-neutral-700 shadow-sm overflow-hidden flex flex-col p-4">
        <div class="flex items-center justify-between mb-4 px-2">
            <h3 class="text-sm font-bold text-neutral-700 dark:text-neutral-300 uppercase tracking-wider flex items-center gap-2">
                <flux:icon name="clipboard-document-list" class="w-4 h-4 text-[#4C5C2D] dark:text-[#8CC7C4]" />
                Daftar Skrining Nutrisi Pasien Dewasa
                <span class="ml-1 px-2 py-0.5 text-xs font-semibold rounded-full bg-[#F1F5E9] text-[#4C5C2D] dark:bg-neutral-700 dark:text-[#8CC7C4]">
                    {{ count($skrinings) }} Data
                </span>
            </h3>

            {{-- Tombol Tambah Skrining (Disabled jika sudah ada 1 data) --}}
            @if(count($skrinings) > 0)
                <flux:button
                    type="button"
                    icon="plus"
                    variant="subtle"
                    size="sm"
                    disabled
                    class="!flex !flex-row !items-center !opacity-50 !cursor-not-allowed"
                    title="Data skrining sudah ada (maksimal 1 data per pasien). Silakan gunakan tombol edit untuk mengubah data.">
                    Tambah Skrining
                </flux:button>
            @else
                <flux:button
                    type="button"
                    wire:click="openPanel"
                    icon="plus"
                    variant="filled"
                    size="sm"
                    class="!flex !flex-row !items-center !bg-[#4C5C2D] !text-white hover:!bg-[#3d4b24]">
                    Tambah Skrining
                </flux:button>
            @endif
        </div>

        <div class="overflow-x-auto">
            <flux:table class="whitespace-nowrap mt-2">
                <flux:table.columns>
                    <flux:table.column class="!pl-4 text-center">Action</flux:table.column>
                    <flux:table.column>Kode Petugas & Nama Petugas</flux:table.column>
                    <flux:table.column class="text-center">BB(Kg)</flux:table.column>
                    <flux:table.column class="text-center">TB/PB(Cm)</flux:table.column>
                    <flux:table.column class="text-center">TD(mmHg)</flux:table.column>
                    <flux:table.column class="text-center">HR(x/menit)</flux:table.column>
                    <flux:table.column class="text-center">RR(x/menit)</flux:table.column>
                    <flux:table.column class="text-center">Suhu</flux:table.column>
                    <flux:table.column class="text-center">SpO2</flux:table.column>
                    <flux:table.column>Alergi</flux:table.column>
                    <flux:table.column>Skrining Gizi 1</flux:table.column>
                    <flux:table.column class="text-center">Nilai 1</flux:table.column>
                    <flux:table.column class="text-center">Skrining Gizi 2</flux:table.column>
                    <flux:table.column class="text-center">Nilai 2</flux:table.column>
                    <flux:table.column class="text-center">Total Skor</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @forelse ($skrinings as $item)
                        <flux:table.row>
                            {{-- Action (Edit & Delete) --}}
                            <flux:table.cell class="!pl-4 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <flux:button
                                        type="button"
                                        wire:click="edit"
                                        icon="pencil-square"
                                        size="xs"
                                        variant="ghost"
                                        class="!text-blue-600 hover:!bg-blue-50 dark:hover:!bg-blue-900/30"
                                        title="Edit Skrining" />
                                    <flux:button
                                        type="button"
                                        @click="
                                            Swal.fire({
                                                title: 'Hapus Skrining Nutrisi?',
                                                text: 'Data skrining nutrisi ini akan dihapus secara permanen.',
                                                icon: 'warning',
                                                showCancelButton: true,
                                                confirmButtonColor: '#dc2626',
                                                cancelButtonColor: '#6b7280',
                                                confirmButtonText: 'Ya, Hapus!',
                                                cancelButtonText: 'Batal'
                                            }).then((result) => {
                                                if (result.isConfirmed) {
                                                    $wire.delete();
                                                }
                                            });
                                        "
                                        icon="trash"
                                        size="xs"
                                        variant="ghost"
                                        class="!text-red-600 hover:!bg-red-50 dark:hover:!bg-red-900/30"
                                        title="Hapus Skrining" />
                                </div>
                            </flux:table.cell>

                            {{-- Kode Petugas & Nama Petugas --}}
                            <flux:table.cell class="text-xs">
                                <span class="font-mono font-bold text-neutral-800 dark:text-neutral-200 block">{{ $item->nip ?: '-' }}</span>
                                <span class="text-neutral-500 dark:text-neutral-400">{{ $item->petugas->nama ?? '-' }}</span>
                            </flux:table.cell>

                            {{-- BB(Kg) --}}
                            <flux:table.cell class="text-center font-medium text-neutral-800 dark:text-neutral-200">
                                {{ $item->bb ?: '-' }}
                            </flux:table.cell>

                            {{-- TB/PB(Cm) --}}
                            <flux:table.cell class="text-center font-medium text-neutral-800 dark:text-neutral-200">
                                {{ $item->tbpb ?: '-' }}
                            </flux:table.cell>

                            {{-- TD(mmHg) --}}
                            <flux:table.cell class="text-center font-mono text-xs text-neutral-700 dark:text-neutral-300">
                                {{ $item->td ?: '-' }}
                            </flux:table.cell>

                            {{-- HR(x/menit) --}}
                            <flux:table.cell class="text-center font-mono text-xs text-neutral-700 dark:text-neutral-300">
                                {{ $item->hr ?: '-' }}
                            </flux:table.cell>

                            {{-- RR(x/menit) --}}
                            <flux:table.cell class="text-center font-mono text-xs text-neutral-700 dark:text-neutral-300">
                                {{ $item->rr ?: '-' }}
                            </flux:table.cell>

                            {{-- Suhu --}}
                            <flux:table.cell class="text-center font-mono text-xs text-neutral-700 dark:text-neutral-300">
                                {{ $item->suhu ? $item->suhu . ' °C' : '-' }}
                            </flux:table.cell>

                            {{-- SpO2 --}}
                            <flux:table.cell class="text-center font-mono text-xs text-neutral-700 dark:text-neutral-300">
                                {{ $item->spo2 ? $item->spo2 . ' %' : '-' }}
                            </flux:table.cell>

                            {{-- Alergi --}}
                            <flux:table.cell class="text-xs max-w-[130px] truncate text-neutral-700 dark:text-neutral-300" title="{{ $item->alergi }}">
                                {{ $item->alergi ?: '-' }}
                            </flux:table.cell>

                            {{-- Skrining Gizi 1 --}}
                            <flux:table.cell class="text-xs max-w-[180px] truncate text-neutral-700 dark:text-neutral-300" title="{{ $item->sg1 }}">
                                {{ $item->sg1 ?: '-' }}
                            </flux:table.cell>

                            {{-- Nilai 1 --}}
                            <flux:table.cell class="text-center font-mono font-bold text-neutral-800 dark:text-neutral-200">
                                {{ $item->nilai1 ?? '0' }}
                            </flux:table.cell>

                            {{-- Skrining Gizi 2 --}}
                            <flux:table.cell class="text-xs text-center text-neutral-700 dark:text-neutral-300">
                                {{ $item->sg2 ?: '-' }}
                            </flux:table.cell>

                            {{-- Nilai 2 --}}
                            <flux:table.cell class="text-center font-mono font-bold text-neutral-800 dark:text-neutral-200">
                                {{ $item->nilai2 ?? '0' }}
                            </flux:table.cell>

                            {{-- Total Skor --}}
                            <flux:table.cell class="text-center">
                                <span class="inline-flex items-center justify-center min-w-[28px] px-2 py-0.5 rounded-full text-xs font-bold font-mono {{ ($item->total_hasil >= 2) ? 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300' : 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300' }}">
                                    {{ $item->total_hasil ?? 0 }}
                                </span>
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="15">
                                <div class="flex flex-col items-center justify-center py-12 text-neutral-400 dark:text-neutral-500">
                                    <flux:icon name="clipboard-document-list" class="w-12 h-12 mb-3 opacity-40 text-[#4C5C2D]" />
                                    <p class="text-sm font-medium">Belum ada data skrining nutrisi dewasa untuk pasien ini</p>
                                    <flux:button type="button" wire:click="openPanel" icon="plus" size="sm" class="mt-3 !bg-[#4C5C2D] !text-white hover:!bg-[#3d4b24]">
                                        Input Skrining Sekarang
                                    </flux:button>
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </div>
    </div>

    {{-- RIGHT SLIDE-IN POP UP PANEL (SOP #6: Alpine.js murni modal/panel) --}}
    <div x-data="{
            open: @entangle('isPanelOpen'),
            autoTime: @entangle('autoTime'),
            interval: null,
            init() {
                this.interval = setInterval(() => {
                    if (this.autoTime && this.open) {
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

        {{-- Right Slide-In Container --}}
        <div x-show="open"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="translate-x-full opacity-0"
             x-transition:enter-end="translate-x-0 opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="translate-x-0 opacity-100"
             x-transition:leave-end="translate-x-full opacity-0"
             class="relative z-10 w-full max-w-3xl bg-white dark:bg-neutral-800 h-full flex flex-col shadow-2xl border-l border-neutral-200 dark:border-neutral-700 overflow-hidden">

            {{-- Panel Header --}}
            <div class="px-6 py-4 border-b border-neutral-200 dark:border-neutral-700 flex items-center justify-between bg-neutral-50/80 dark:bg-neutral-900/70 flex-shrink-0">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-[#4C5C2D]/10 dark:bg-[#4C5C2D]/30 flex items-center justify-center flex-shrink-0">
                        <flux:icon name="clipboard-document-check" class="w-5 h-5 text-[#4C5C2D] dark:text-[#8CC7C4]" />
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-neutral-800 dark:text-neutral-100">
                            {{ $isEditMode ? 'Edit Skrining Nutrisi Pasien Dewasa' : 'Input Skrining Nutrisi Pasien Dewasa' }}
                        </h3>
                        <p class="text-xs text-neutral-500 dark:text-neutral-400">Pengkajian Malnutrition Screening Tool (MST) untuk mendeteksi risiko malnutrisi.</p>
                    </div>
                </div>
                <button type="button" @click="open = false" class="text-neutral-400 hover:text-neutral-600 dark:hover:text-neutral-200 p-1.5 rounded-lg hover:bg-neutral-100 dark:hover:bg-neutral-700 transition-colors">
                    <flux:icon name="x-mark" class="w-5 h-5" />
                </button>
            </div>

            {{-- Panel Scrollable Body --}}
            <div class="flex-1 overflow-y-auto p-6 space-y-6">

                {{-- Mini Patient Banner --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 bg-neutral-50 dark:bg-neutral-900/60 p-3.5 rounded-xl border border-neutral-200/80 dark:border-neutral-700">
                    <div>
                        <div class="text-[10px] text-neutral-400 font-bold uppercase tracking-wider mb-0.5">No. Rawat</div>
                        <div class="font-mono text-xs font-bold text-[#4C5C2D] dark:text-[#8CC7C4]">{{ $regPeriksa->no_rawat }}</div>
                    </div>
                    <div>
                        <div class="text-[10px] text-neutral-400 font-bold uppercase tracking-wider mb-0.5">Pasien (RM)</div>
                        <div class="text-xs font-bold text-neutral-800 dark:text-neutral-100 truncate" title="{{ $regPeriksa->pasien->nm_pasien }}">
                            {{ $regPeriksa->pasien->nm_pasien }} <span class="font-mono text-neutral-400">({{ $regPeriksa->no_rkm_medis }})</span>
                        </div>
                    </div>
                    <div>
                        <div class="text-[10px] text-neutral-400 font-bold uppercase tracking-wider mb-0.5">Tgl. Lahir / JK</div>
                        <div class="text-xs font-medium text-neutral-700 dark:text-neutral-300">
                            {{ $regPeriksa->pasien->tgl_lahir ? \Carbon\Carbon::parse($regPeriksa->pasien->tgl_lahir)->format('d-m-Y') : '-' }}
                            ({{ $regPeriksa->pasien->jk === 'L' ? 'L' : 'P' }})
                        </div>
                    </div>
                </div>

                {{-- Section 1: Waktu & Petugas --}}
                <div class="bg-neutral-50/50 dark:bg-neutral-900/30 p-4 rounded-xl border border-neutral-200/80 dark:border-neutral-700 space-y-4">
                    <h4 class="text-xs font-bold text-neutral-700 dark:text-neutral-300 uppercase tracking-wider flex items-center gap-2">
                        <flux:icon name="clock" class="w-4 h-4 text-[#4C5C2D] dark:text-[#8CC7C4]" />
                        Waktu & Petugas Pemeriksa
                    </h4>

                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-4">
                        <div class="sm:col-span-4">
                            <flux:input wire:model="tanggal_date" type="date" label="Tanggal *" required />
                        </div>
                        <div class="sm:col-span-3">
                            <div class="flex items-center justify-between mb-1">
                                <flux:label class="!mb-0">Jam *</flux:label>
                                <label class="flex items-center gap-1.5 cursor-pointer select-none">
                                    <input
                                        type="checkbox"
                                        x-model="autoTime"
                                        wire:model.live="autoTime"
                                        class="rounded text-[#4C5C2D] focus:ring-[#4C5C2D] border-neutral-300 dark:border-neutral-600 dark:bg-neutral-800 cursor-pointer h-3.5 w-3.5" />
                                    <span class="text-[11px] text-neutral-500 font-medium">Auto</span>
                                </label>
                            </div>
                            <flux:input wire:model="tanggal_time" type="time" required />
                        </div>
                        <div class="sm:col-span-5 relative">
                            <flux:label>Petugas (NIP/Nama) *</flux:label>

                            @if($nip && $nmPetugas)
                                <div class="flex items-center justify-between px-3 py-2 bg-white dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl h-10 shadow-sm mt-1">
                                    <div class="flex items-center gap-2.5 min-w-0">
                                        <div class="w-6 h-6 rounded-lg bg-[#4C5C2D]/10 dark:bg-[#4C5C2D]/30 flex items-center justify-center flex-shrink-0 text-[#4C5C2D] dark:text-[#8CC7C4]">
                                            <flux:icon name="user" class="w-3.5 h-3.5" />
                                        </div>
                                        <div class="min-w-0">
                                            <div class="text-xs font-bold text-neutral-800 dark:text-neutral-100 truncate" title="{{ $nmPetugas }}">
                                                {{ $nmPetugas }}
                                            </div>
                                            <div class="text-[10px] font-mono text-neutral-400 leading-none">
                                                {{ $nip }}
                                            </div>
                                        </div>
                                    </div>
                                    <button
                                        type="button"
                                        wire:click="clearPetugas"
                                        class="text-[11px] font-medium text-neutral-400 hover:text-red-500 dark:hover:text-red-400 px-2 py-1 rounded-md hover:bg-neutral-100 dark:hover:bg-neutral-700 transition-colors flex items-center gap-1 flex-shrink-0"
                                        title="Ganti Petugas">
                                        <flux:icon name="pencil-square" class="w-3.5 h-3.5" />
                                        <span>Ganti</span>
                                    </button>
                                </div>
                            @else
                                <div class="relative mt-1">
                                    <flux:input
                                        wire:model.live.debounce.300ms="petugasSearch"
                                        placeholder="Cari NIP / Nama petugas..."
                                        icon="magnifying-glass"
                                        autofocus />

                                    @if(!empty($petugasList))
                                        <div class="absolute z-30 left-0 right-0 mt-1 bg-white dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl shadow-xl max-h-48 overflow-y-auto">
                                            @foreach($petugasList as $p)
                                                <button
                                                    type="button"
                                                    wire:click="selectPetugas('{{ $p->nip }}', '{{ $p->nama }}')"
                                                    class="w-full text-left px-3.5 py-2 hover:bg-neutral-50 dark:hover:bg-neutral-700 border-b border-neutral-100 dark:border-neutral-700 last:border-0 transition-colors">
                                                    <div class="font-bold text-xs text-neutral-700 dark:text-neutral-200">{{ $p->nama }}</div>
                                                    <div class="font-mono text-[11px] text-neutral-400">{{ $p->nip }}</div>
                                                </button>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endif
                            @error('nip') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                {{-- Section 2: Antropometri & Tanda Vital --}}
                <div class="bg-neutral-50/50 dark:bg-neutral-900/30 p-4 rounded-xl border border-neutral-200/80 dark:border-neutral-700 space-y-4">
                    <div class="flex items-center justify-between">
                        <h4 class="text-xs font-bold text-neutral-700 dark:text-neutral-300 uppercase tracking-wider flex items-center gap-2">
                            <flux:icon name="heart" class="w-4 h-4 text-[#4C5C2D] dark:text-[#8CC7C4]" />
                            Antropometri & Tanda Vital
                        </h4>

                        <flux:button
                            type="button"
                            wire:click="fillLatestPemeriksaan"
                            icon="arrow-path"
                            size="xs"
                            variant="subtle"
                            class="!text-xs !text-[#4C5C2D] hover:!bg-[#4C5C2D]/10 dark:!text-[#8CC7C4]"
                            title="Tarik data antropometri & tanda vital dari pemeriksaan SOAP terakhir">
                            <span wire:loading.remove wire:target="fillLatestPemeriksaan">Isi Otomatis</span>
                            <span wire:loading wire:target="fillLatestPemeriksaan">Menarik Data...</span>
                        </flux:button>
                    </div>

                    {{-- Baris 1: BB, TB, TD, HR, RR --}}
                    <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
                        <div>
                            <flux:input wire:model="bb" label="BB (Kg)" placeholder="Contoh: 60" />
                        </div>
                        <div>
                            <flux:input wire:model="tbpb" label="TB/PB (Cm)" placeholder="Contoh: 165" />
                        </div>
                        <div>
                            <flux:input wire:model="td" label="TD (mmHg)" placeholder="120/80" />
                        </div>
                        <div>
                            <flux:input wire:model="hr" label="HR (x/mnt)" placeholder="80" />
                        </div>
                        <div>
                            <flux:input wire:model="rr" label="RR (x/mnt)" placeholder="20" />
                        </div>
                    </div>

                    {{-- Baris 2: Suhu, SpO2, Alergi --}}
                    <div class="grid grid-cols-2 sm:grid-cols-12 gap-3">
                        <div class="sm:col-span-3">
                            <flux:input wire:model="suhu" label="Suhu (°C)" placeholder="36.5" />
                        </div>
                        <div class="sm:col-span-3">
                            <flux:input wire:model="spo2" label="SpO2 (%)" placeholder="98" />
                        </div>
                        <div class="col-span-2 sm:col-span-6">
                            <flux:input wire:model="alergi" label="Riwayat Alergi" placeholder="Tulis alergi atau tanda '-' jika tidak ada" />
                        </div>
                    </div>
                </div>

                {{-- Section 3: Skrining Gizi Awal Dengan MST (Malnutrition Screening Tool) --}}
                <div class="bg-white dark:bg-neutral-800 rounded-xl border-2 border-[#4C5C2D]/30 dark:border-[#4C5C2D]/50 p-4 space-y-4 shadow-sm">
                    <div class="border-b border-neutral-100 dark:border-neutral-700 pb-2 flex items-center justify-between">
                        <div>
                            <h4 class="text-sm font-bold text-neutral-800 dark:text-neutral-100 flex items-center gap-2">
                                <flux:icon name="scale" class="w-4 h-4 text-[#4C5C2D]" />
                                Skrining Gizi Awal Dengan MST (Bagi Perawat)
                            </h4>
                            <p class="text-xs text-neutral-500 mt-0.5">Pilih jawaban pada setiap pertanyaan, skor dan risiko akan dihitung otomatis.</p>
                        </div>
                        <span class="text-[11px] font-bold px-2 py-0.5 rounded bg-[#F1F5E9] text-[#4C5C2D] dark:bg-neutral-700 dark:text-[#8CC7C4]">
                            MST Dewasa
                        </span>
                    </div>

                    {{-- Pertanyaan 1 --}}
                    <div class="bg-neutral-50 dark:bg-neutral-900/40 p-3.5 rounded-xl border border-neutral-200/70 dark:border-neutral-700 space-y-2">
                        <label class="block text-xs font-bold text-neutral-700 dark:text-neutral-200">
                            1. Apakah Ada Penurunan Berat Badan Yang Tidak Diinginkan Selama 6 Bulan Terakhir?
                        </label>
                        <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
                            <div class="sm:col-span-9">
                                <flux:select wire:model.live="sg1">
                                    <flux:select.option value="Tidak">Tidak (Skor: 0)</flux:select.option>
                                    <flux:select.option value="Tidak Yakin">Tidak Yakin (Skor: 2)</flux:select.option>
                                    <flux:select.option value="Ya, 1-5 Kg">Ya, ada penurunan 1 - 5 Kg (Skor: 1)</flux:select.option>
                                    <flux:select.option value="Ya, 6-10 Kg">Ya, ada penurunan 6 - 10 Kg (Skor: 2)</flux:select.option>
                                    <flux:select.option value="Ya, 11-15 Kg">Ya, ada penurunan 11 - 15 Kg (Skor: 3)</flux:select.option>
                                    <flux:select.option value="Ya, >15 Kg">Ya, ada penurunan > 15 Kg (Skor: 4)</flux:select.option>
                                </flux:select>
                            </div>
                            <div class="sm:col-span-3 flex items-center justify-end sm:justify-center gap-2">
                                <span class="text-xs text-neutral-500 font-medium">Nilai 1:</span>
                                <span class="w-8 h-8 rounded-lg bg-neutral-200/80 dark:bg-neutral-700 font-mono font-bold text-sm text-neutral-800 dark:text-neutral-100 flex items-center justify-center">
                                    {{ $nilai1 }}
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Pertanyaan 2 --}}
                    <div class="bg-neutral-50 dark:bg-neutral-900/40 p-3.5 rounded-xl border border-neutral-200/70 dark:border-neutral-700 space-y-2">
                        <label class="block text-xs font-bold text-neutral-700 dark:text-neutral-200">
                            2. Apakah Asupan Makan Berkurang Karena Tidak Nafsu Makan / Kesulitan Makan?
                        </label>
                        <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
                            <div class="sm:col-span-9">
                                <flux:select wire:model.live="sg2">
                                    <flux:select.option value="Tidak">Tidak (Skor: 0)</flux:select.option>
                                    <flux:select.option value="Ya">Ya (Skor: 1)</flux:select.option>
                                </flux:select>
                            </div>
                            <div class="sm:col-span-3 flex items-center justify-end sm:justify-center gap-2">
                                <span class="text-xs text-neutral-500 font-medium">Nilai 2:</span>
                                <span class="w-8 h-8 rounded-lg bg-neutral-200/80 dark:bg-neutral-700 font-mono font-bold text-sm text-neutral-800 dark:text-neutral-100 flex items-center justify-center">
                                    {{ $nilai2 }}
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Hasil Total Skor & Kesimpulan --}}
                    <div class="p-4 rounded-xl border transition-colors {{ $total_hasil >= 2 ? 'bg-red-50 dark:bg-red-950/30 border-red-200 dark:border-red-900' : 'bg-emerald-50 dark:bg-emerald-950/30 border-emerald-200 dark:border-emerald-900' }}">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-xl flex items-center justify-center font-mono font-bold text-xl {{ $total_hasil >= 2 ? 'bg-red-600 text-white shadow-md' : 'bg-emerald-600 text-white shadow-md' }}">
                                    {{ $total_hasil }}
                                </div>
                                <div>
                                    <div class="text-xs font-bold uppercase tracking-wider {{ $total_hasil >= 2 ? 'text-red-700 dark:text-red-400' : 'text-emerald-700 dark:text-emerald-400' }}">
                                        Total Skor Skrining: {{ $total_hasil }}
                                    </div>
                                    <div class="text-xs {{ $total_hasil >= 2 ? 'text-red-600 dark:text-red-300 font-semibold' : 'text-emerald-600 dark:text-emerald-300' }}">
                                        @if($total_hasil >= 2)
                                            ⚠️ Bila Skor &ge; 2, Pasien Berisiko Malnutrisi. Konsulkan ke Ahli Gizi / Dietisien!
                                        @else
                                            ✅ Pasien Tidak Berisiko Malnutrisi (Skor &lt; 2). Skrining ulang berkala.
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <span class="inline-flex self-start sm:self-auto items-center px-2.5 py-1 rounded-full text-xs font-bold {{ $total_hasil >= 2 ? 'bg-red-200 text-red-800 dark:bg-red-900/60 dark:text-red-200' : 'bg-emerald-200 text-emerald-800 dark:bg-emerald-900/60 dark:text-emerald-200' }}">
                                {{ $total_hasil >= 2 ? 'Risiko Malnutrisi' : 'Risiko Rendah' }}
                            </span>
                        </div>
                    </div>
                </div>

            </div>

            {{-- Panel Footer (Sticky Actions) --}}
            <div class="px-6 py-4 border-t border-neutral-200 dark:border-neutral-700 flex items-center justify-end gap-3 bg-neutral-50/80 dark:bg-neutral-900/70 flex-shrink-0">
                <flux:button
                    type="button"
                    @click="open = false"
                    variant="subtle"
                    size="sm">
                    Batal
                </flux:button>

                <flux:button
                    type="button"
                    wire:click="save"
                    variant="filled"
                    size="sm"
                    class="!bg-[#4C5C2D] !text-white hover:!bg-[#3d4b24]">
                    <span wire:loading.remove wire:target="save">
                        {{ $isEditMode ? 'Perbarui Data' : 'Simpan Skrining' }}
                    </span>
                    <span wire:loading wire:target="save">
                        Menyimpan...
                    </span>
                </flux:button>
            </div>
        </div>
    </div>
</div>
