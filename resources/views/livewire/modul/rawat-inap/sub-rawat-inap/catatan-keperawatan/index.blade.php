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
                    <span>Catatan Keperawatan</span>
                </nav>
                <h1 class="text-xl font-bold text-neutral-800 dark:text-neutral-100">Catatan Keperawatan Rawat Inap</h1>
            </div>
        </div>
    </div>

    {{-- Patient Info Card --}}
    <div class="bg-white dark:bg-neutral-800 rounded-2xl border border-neutral-200 dark:border-neutral-700 p-6 shadow-sm">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-xl bg-[#4C5C2D]/10 flex items-center justify-center flex-shrink-0">
                    <flux:icon name="user" class="w-6 h-6 text-[#4C5C2D]" />
                </div>
                <div class="flex flex-col">
                    <h2 class="text-lg font-bold text-neutral-800 dark:text-neutral-100 uppercase tracking-tight">
                        {{ $regPeriksa->pasien->nm_pasien ?? '-' }}
                    </h2>
                    <div class="flex flex-wrap items-center gap-x-3 gap-y-1 mt-1">
                        <span class="px-2 py-0.5 rounded bg-neutral-100 dark:bg-neutral-700 text-[10px] font-bold text-neutral-500 font-mono tracking-wider border border-neutral-200 dark:border-neutral-600">
                            {{ $regPeriksa->no_rawat }}
                        </span>
                        <span class="text-xs font-medium text-neutral-400">
                            {{ $regPeriksa->no_rkm_medis }}
                        </span>
                        <span class="text-neutral-300">•</span>
                        <span class="text-xs font-semibold text-neutral-500 uppercase tracking-wide">
                            {{ $regPeriksa->kamarInap->last()->kamar->kd_kamar ?? '-' }}
                        </span>
                        <span class="text-neutral-300">•</span>
                        <span class="text-xs font-semibold text-neutral-500">
                            {{ $regPeriksa->dokter->nm_dokter ?? '-' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Table List --}}
    <div class="bg-white dark:bg-neutral-800 rounded-2xl border border-neutral-200 dark:border-neutral-700 p-4 shadow-sm">
        <div class="flex items-center justify-between mb-4 px-2">
            <h3 class="text-sm font-bold text-neutral-700 dark:text-neutral-300 uppercase tracking-wider flex items-center gap-2">
                <flux:icon name="list-bullet" class="w-4 h-4 text-[#4C5C2D]" />
                Daftar Catatan Keperawatan
            </h3>
            <flux:button wire:click="openModal" icon="plus" variant="filled" size="sm" class="!flex !flex-row !items-center !bg-[#4C5C2D] !text-white hover:!bg-[#3d4a24]">
                Input Catatan
            </flux:button>
        </div>

        <flux:table>
            <flux:table.columns>
                <flux:table.column>Umur</flux:table.column>
                <flux:table.column>JK</flux:table.column>
                <flux:table.column>Tgl. Lahir</flux:table.column>
                <flux:table.column>Tanggal</flux:table.column>
                <flux:table.column>Jam</flux:table.column>
                <flux:table.column>Uraian</flux:table.column>
                <flux:table.column>NIP</flux:table.column>
                <flux:table.column>Petugas</flux:table.column>
                <flux:table.column align="center">Action</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse ($catatans as $catatan)
                    <flux:table.row :key="$catatan['no_rawat'] . $catatan['tanggal'] . $catatan['jam']">
                        <flux:table.cell class="text-xs">{{ $catatan['reg_periksa']['umurdaftar'] ?? '' }} {{ $catatan['reg_periksa']['sttsumur'] ?? '' }}</flux:table.cell>
                        <flux:table.cell class="text-xs">{{ $catatan['reg_periksa']['pasien']['jk'] ?? '-' }}</flux:table.cell>
                        <flux:table.cell class="text-xs">{{ $catatan['reg_periksa']['pasien']['tgl_lahir'] ?? '-' }}</flux:table.cell>
                        <flux:table.cell class="text-xs">{{ $catatan['tanggal'] }}</flux:table.cell>
                        <flux:table.cell class="text-xs">{{ $catatan['jam'] }}</flux:table.cell>
                        <flux:table.cell class="text-xs italic text-neutral-500">
                            {{ \Illuminate\Support\Str::limit($catatan['uraian'], 40, ' ...') }}
                        </flux:table.cell>
                        <flux:table.cell class="text-[10px] font-mono">{{ $catatan['nip'] }}</flux:table.cell>
                        <flux:table.cell class="text-xs">{{ $catatan['petugas']['nama'] ?? '-' }}</flux:table.cell>
                        <flux:table.cell>
                            <div class="flex justify-center gap-2">
                                <flux:button wire:click="edit('{{ $catatan['tanggal'] }}', '{{ $catatan['jam'] }}')" icon="pencil-square" size="xs" variant="ghost" />
                                <flux:button 
                                    @click="
                                        Swal.fire({
                                            title: 'Hapus Catatan?',
                                            text: 'Data yang dihapus tidak dapat dikembalikan!',
                                            icon: 'warning',
                                            showCancelButton: true,
                                            confirmButtonColor: '#4C5C2D',
                                            cancelButtonColor: '#d33',
                                            confirmButtonText: 'Ya, Hapus!',
                                            cancelButtonText: 'Batal'
                                        }).then((result) => {
                                            if (result.isConfirmed) {
                                                $wire.delete('{{ $catatan['tanggal'] }}', '{{ $catatan['jam'] }}');
                                            }
                                        });
                                    "
                                    icon="trash" size="xs" variant="ghost" color="red" />
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="12">
                            <div class="flex flex-col items-center justify-center py-12 text-neutral-400 dark:text-neutral-500">
                                <flux:icon name="document-text" class="w-12 h-12 mb-3 opacity-40" />
                                <p class="text-sm font-medium">Belum ada catatan keperawatan</p>
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
                        $wire.tanggal = now.getFullYear() + '-' + String(now.getMonth()+1).padStart(2,'0') + '-' + String(now.getDate()).padStart(2,'0');
                        $wire.jam = String(now.getHours()).padStart(2,'0') + ':' + String(now.getMinutes()).padStart(2,'0') + ':' + String(now.getSeconds()).padStart(2,'0');
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
             class="relative w-full max-w-2xl bg-white dark:bg-neutral-800 rounded-3xl shadow-2xl overflow-hidden border border-neutral-200 dark:border-neutral-700">
            
            {{-- Header --}}
            <div class="px-8 py-6 border-b border-neutral-100 dark:border-neutral-700 flex items-center justify-between bg-neutral-50/50 dark:bg-neutral-900/50">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-xl bg-[#4C5C2D]/10 flex items-center justify-center">
                        <flux:icon name="pencil-square" class="w-5 h-5 text-[#4C5C2D]" />
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-neutral-800 dark:text-neutral-100">Input Catatan Keperawatan</h3>
                        <p class="text-xs text-neutral-500">Lengkapi form di bawah ini untuk menambahkan catatan baru.</p>
                    </div>
                </div>
                <button @click="open = false" class="text-neutral-400 hover:text-neutral-600 dark:hover:text-neutral-200 transition-colors">
                    <flux:icon name="x-mark" class="w-6 h-6" />
                </button>
            </div>

            {{-- Form Content --}}
            <div class="p-8">
                {{-- Mapped legacy fields (visually hidden to keep form clean) --}}
                <div class="hidden">
                    <input type="text" wire:model="no_rkm_medis" />
                    <input type="text" wire:model="nm_pasien" />
                    <input type="text" wire:model="tgl_lahir" />
                    <input type="text" wire:model="noRawat" />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <flux:input wire:model="tanggal" type="date" label="Tanggal" required />
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <flux:label class="!mb-0">Jam</flux:label>
                            <div class="flex items-center gap-1.5 mt-2">
                                <flux:checkbox wire:model="autoTime" id="autoTimeRanap" />
                                <label for="autoTimeRanap" class="text-xs font-medium text-neutral-600 dark:text-neutral-400 cursor-pointer">Waktu Otomatis</label>
                            </div>
                        </div>
                        <flux:input wire:model="jam" type="time" step="1" required />
                    </div>
                </div>

                <div class="mb-6 relative">
                    <flux:input wire:model.live.debounce.300ms="petugasSearch" 
                                label="Cari Petugas (NIP/Nama)" 
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

                <flux:textarea wire:model="uraian" label="Uraian Catatan" rows="4" placeholder="Tuliskan catatan keperawatan di sini..." required />
            </div>

            {{-- Footer --}}
            <div class="px-8 py-6 bg-neutral-50/50 dark:bg-neutral-900/50 border-t border-neutral-100 dark:border-neutral-700 flex justify-end gap-3">
                <flux:button @click="open = false" variant="ghost">Batal</flux:button>
                <flux:button wire:click="save" variant="filled" class="!bg-[#4C5C2D] !text-white hover:!bg-[#3d4a24]">
                    Simpan Catatan
                </flux:button>
            </div>
        </div>
    </div>
</div>
