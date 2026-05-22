<div class="flex flex-col gap-6 pb-8">
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
                    <span>Observasi Rawat Inap</span>
                </nav>
                <h1 class="text-xl font-bold text-neutral-800 dark:text-neutral-100">Observasi Rawat Inap</h1>
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

    {{-- Data Table --}}
    <div class="bg-white dark:bg-neutral-800 rounded-xl border border-neutral-200 dark:border-neutral-700 shadow-sm overflow-hidden flex flex-col mt-2 relative p-4">
        <div class="flex items-center justify-between mb-4 px-2">
            <h3 class="text-sm font-bold text-neutral-700 dark:text-neutral-300 uppercase tracking-wider flex items-center gap-2">
                <flux:icon name="list-bullet" class="w-4 h-4 text-[#4C5C2D]" />
                Daftar Observasi Rawat Inap
            </h3>
            <flux:button wire:click="openModal" icon="plus" variant="filled" size="sm" class="!flex !flex-row !items-center !bg-[#4C5C2D] !text-white hover:!bg-[#3d4a24]">
                Tambah Observasi
            </flux:button>
        </div>

        <flux:table class="whitespace-nowrap mt-2">
            <flux:table.columns>
                <flux:table.column class="!pl-6">Tanggal</flux:table.column>
                <flux:table.column>Jam</flux:table.column>
                <flux:table.column>Umur</flux:table.column>
                <flux:table.column>JK</flux:table.column>
                <flux:table.column>Tgl Lahir</flux:table.column>
                <flux:table.column>GCS</flux:table.column>
                <flux:table.column>TD</flux:table.column>
                <flux:table.column>HR</flux:table.column>
                <flux:table.column>RR</flux:table.column>
                <flux:table.column>Suhu</flux:table.column>
                <flux:table.column>SpO2</flux:table.column>
                <flux:table.column>NIP</flux:table.column>
                <flux:table.column>Nama Petugas</flux:table.column>
                <flux:table.column>Action</flux:table.column>
            </flux:table.columns>
            
            <flux:table.rows>
                @forelse ($observasis as $obs)
                    <flux:table.row>
                        <flux:table.cell class="!pl-6 font-medium text-neutral-900 dark:text-neutral-100">
                            {{ \Carbon\Carbon::parse($obs['tgl_perawatan'])->format('d/m/Y') }}
                        </flux:table.cell>
                        <flux:table.cell class="text-neutral-600 dark:text-neutral-400">
                            {{ $obs['jam_rawat'] }}
                        </flux:table.cell>
                        <flux:table.cell class="text-neutral-600 dark:text-neutral-400">
                            {{ $regPeriksa->pasien->tgl_lahir ? \Carbon\Carbon::parse($regPeriksa->pasien->tgl_lahir)->age . ' Thn' : '-' }}
                        </flux:table.cell>
                        <flux:table.cell class="text-neutral-600 dark:text-neutral-400">
                            {{ $regPeriksa->pasien->jk }}
                        </flux:table.cell>
                        <flux:table.cell class="text-neutral-600 dark:text-neutral-400">
                            {{ $regPeriksa->pasien->tgl_lahir ? \Carbon\Carbon::parse($regPeriksa->pasien->tgl_lahir)->format('d/m/Y') : '-' }}
                        </flux:table.cell>
                        <flux:table.cell class="text-neutral-600 dark:text-neutral-400">
                            {{ $obs['gcs'] ?? '-' }}
                        </flux:table.cell>
                        <flux:table.cell class="text-neutral-600 dark:text-neutral-400">
                            {{ $obs['td'] ?? '-' }}
                        </flux:table.cell>
                        <flux:table.cell class="text-neutral-600 dark:text-neutral-400">
                            {{ $obs['hr'] ?? '-' }}
                        </flux:table.cell>
                        <flux:table.cell class="text-neutral-600 dark:text-neutral-400">
                            {{ $obs['rr'] ?? '-' }}
                        </flux:table.cell>
                        <flux:table.cell class="text-neutral-600 dark:text-neutral-400">
                            {{ $obs['suhu'] ?? '-' }}
                        </flux:table.cell>
                        <flux:table.cell class="text-neutral-600 dark:text-neutral-400">
                            {{ $obs['spo2'] ?? '-' }}
                        </flux:table.cell>
                        <flux:table.cell class="font-mono text-xs text-neutral-500">
                            {{ $obs['nip'] }}
                        </flux:table.cell>
                        <flux:table.cell class="text-xs">
                            {{ $obs['petugas']['nama'] ?? '-' }}
                        </flux:table.cell>
                        <flux:table.cell>
                            <div class="flex justify-center gap-2">
                                <flux:button wire:click="edit('{{ $obs['tgl_perawatan'] }}', '{{ $obs['jam_rawat'] }}')" icon="pencil-square" size="xs" variant="ghost" />
                                <flux:button 
                                    @click="
                                        Swal.fire({
                                            title: 'Hapus Observasi?',
                                            text: 'Data observasi ini akan dihapus secara permanen.',
                                            icon: 'warning',
                                            showCancelButton: true,
                                            confirmButtonColor: '#dc2626',
                                            cancelButtonColor: '#6b7280',
                                            confirmButtonText: 'Ya, Hapus!',
                                            cancelButtonText: 'Batal'
                                        }).then((result) => {
                                            if (result.isConfirmed) {
                                                $wire.delete('{{ $obs['tgl_perawatan'] }}', '{{ $obs['jam_rawat'] }}');
                                            }
                                        })
                                    "
                                    icon="trash" size="xs" variant="ghost" class="!text-red-500 hover:!bg-red-50 dark:hover:!bg-red-500/10" />
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="14">
                            <div class="flex flex-col items-center justify-center py-12 text-center">
                                <flux:icon name="inbox" class="w-12 h-12 text-neutral-300 dark:text-neutral-600 mb-4" />
                                <h3 class="text-lg font-bold text-neutral-700 dark:text-neutral-300">Belum Ada Catatan Observasi</h3>
                                <p class="text-neutral-500 mt-1 max-w-md">Belum ada catatan observasi rawat inap yang ditambahkan untuk pasien ini.</p>
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </div>

    {{-- Modal Input (SOP #6: Alpine.js Modal) --}}
    <div x-data="{ 
            open: @entangle('isModalOpen'),
            autoTime: @entangle('autoTime'),
            interval: null,
            init() {
                this.interval = setInterval(() => {
                    if(this.autoTime) {
                        let now = new Date();
                        $wire.tgl_perawatan = now.getFullYear() + '-' + String(now.getMonth()+1).padStart(2,'0') + '-' + String(now.getDate()).padStart(2,'0');
                        $wire.jam_rawat = String(now.getHours()).padStart(2,'0') + ':' + String(now.getMinutes()).padStart(2,'0') + ':' + String(now.getSeconds()).padStart(2,'0');
                    }
                }, 1000);
            }
         }" 
         @set-autotime.window="autoTime = $event.detail[0].status"
         x-show="open" 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 overflow-y-auto"
         style="display: none;">
        
        {{-- Backdrop --}}
        <div x-show="open" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-neutral-900/60 backdrop-blur-sm" 
             @click="open = false"></div>

        {{-- Modal Content --}}
        <div x-show="open"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95 translate-y-4"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-95 translate-y-4"
             class="relative w-full max-w-3xl bg-white dark:bg-neutral-800 rounded-3xl shadow-2xl overflow-hidden border border-neutral-200 dark:border-neutral-700">
            
            {{-- Header --}}
            <div class="px-8 py-6 border-b border-neutral-100 dark:border-neutral-700 flex items-center justify-between bg-neutral-50/50 dark:bg-neutral-900/50">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-xl bg-[#4C5C2D]/10 flex items-center justify-center">
                        <flux:icon name="clipboard-document-check" class="w-5 h-5 text-[#4C5C2D]" />
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-neutral-800 dark:text-neutral-100">
                            {{ $isEditMode ? 'Edit Observasi Rawat Inap' : 'Input Observasi Rawat Inap' }}
                        </h3>
                        <p class="text-xs text-neutral-500">Lengkapi data tanda-tanda vital pasien.</p>
                    </div>
                </div>
                <button @click="open = false" class="text-neutral-400 hover:text-neutral-600 dark:hover:text-neutral-200 transition-colors">
                    <flux:icon name="x-mark" class="w-6 h-6" />
                </button>
            </div>

            {{-- Form Content --}}
            <div class="p-8">
                {{-- Data Pasien Readonly --}}
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6 bg-neutral-50 dark:bg-neutral-900 p-4 rounded-xl border border-neutral-100 dark:border-neutral-800">
                    <div>
                        <div class="text-[10px] text-neutral-400 font-bold uppercase tracking-wider mb-1">No. Rawat</div>
                        <div class="font-mono text-sm font-semibold text-[#4C5C2D]">{{ $regPeriksa->no_rawat }}</div>
                    </div>
                    <div>
                        <div class="text-[10px] text-neutral-400 font-bold uppercase tracking-wider mb-1">No. RM</div>
                        <div class="font-mono text-sm font-medium text-neutral-800 dark:text-neutral-200">{{ $regPeriksa->no_rkm_medis }}</div>
                    </div>
                    <div>
                        <div class="text-[10px] text-neutral-400 font-bold uppercase tracking-wider mb-1">Nama Pasien</div>
                        <div class="text-sm font-bold text-neutral-800 dark:text-neutral-200">{{ $regPeriksa->pasien->nm_pasien }}</div>
                    </div>
                    <div>
                        <div class="text-[10px] text-neutral-400 font-bold uppercase tracking-wider mb-1">Tgl Lahir</div>
                        <div class="text-sm font-medium text-neutral-800 dark:text-neutral-200">{{ $regPeriksa->pasien->tgl_lahir ? \Carbon\Carbon::parse($regPeriksa->pasien->tgl_lahir)->format('d-m-Y') : '-' }}</div>
                    </div>
                </div>

                {{-- Tanggal, Jam, & Petugas --}}
                <div class="grid grid-cols-1 md:grid-cols-12 gap-6 mb-6">
                    <div class="md:col-span-3">
                        <flux:input wire:model="tgl_perawatan" type="date" label="Tanggal Observasi *" required />
                    </div>
                    <div class="md:col-span-3">
                        <div class="flex items-center justify-between mb-1">
                            <flux:label class="!mb-0">Jam *</flux:label>
                            <div class="flex items-center gap-1.5 mt-2">
                                <flux:checkbox wire:model="autoTime" id="autoTimeObs" />
                                <label for="autoTimeObs" class="text-xs font-medium text-neutral-600 dark:text-neutral-400 cursor-pointer">Waktu Otomatis</label>
                            </div>
                        </div>
                        <flux:input wire:model="jam_rawat" type="time" step="1" required />
                    </div>
                    <div class="md:col-span-6 relative">
                        <flux:input wire:model.live.debounce.300ms="petugasSearch" 
                                    label="Cari Petugas (NIP/Nama) *" 
                                    placeholder="Ketik minimal 3 karakter..." 
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
                            <div class="mt-2 p-3 bg-[#4C5C2D]/5 border border-[#4C5C2D]/20 rounded-xl flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <flux:icon name="user-circle" class="w-5 h-5 text-[#4C5C2D]" />
                                    <div>
                                        <div class="text-xs font-bold text-[#4C5C2D]">{{ $nmPetugas }}</div>
                                        <div class="text-[10px] text-neutral-500 font-mono tracking-wider">{{ $nip }}</div>
                                    </div>
                                </div>
                                <button wire:click="$set('nip', null)" class="text-neutral-400 hover:text-red-500 transition-colors">
                                    <flux:icon name="trash" size="xs" />
                                </button>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Vital Signs --}}
                <div class="grid grid-cols-2 md:grid-cols-6 gap-4">
                    <flux:input wire:model="gcs" label="GCS (E,V,M)" placeholder="Ex: 15" />
                    <flux:input wire:model="td" label="TD (mmHg) *" placeholder="Ex: 120/80" required />
                    <flux:input wire:model="hr" label="HR (x/m)" placeholder="Ex: 80" />
                    <flux:input wire:model="rr" label="RR (x/m)" placeholder="Ex: 20" />
                    <flux:input wire:model="suhu" label="Suhu (°C)" placeholder="Ex: 36.5" />
                    <flux:input wire:model="spo2" label="SpO2 (%) *" placeholder="Ex: 98" required />
                </div>
            </div>

            {{-- Footer --}}
            <div class="px-8 py-6 bg-neutral-50/50 dark:bg-neutral-900/50 border-t border-neutral-100 dark:border-neutral-700 flex justify-end gap-3">
                <flux:button @click="open = false" variant="ghost">Batal</flux:button>
                <flux:button wire:click="save" variant="filled" class="!bg-[#4C5C2D] !text-white hover:!bg-[#3d4a24]">
                    Simpan Observasi
                </flux:button>
            </div>
        </div>
    </div>

</div>
