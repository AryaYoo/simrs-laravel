<div class="flex flex-col gap-6 {{ $isEmbedded ? '' : 'pb-8' }}" x-data="{
        confirmDelete(id) {
            Swal.fire({
                title: 'Hapus Data Visite?',
                text: 'Data visite ini akan dihapus secara permanen dari sistem.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $wire.deleteVisite(id);
                }
            });
        }
    }"
    x-init="
        window.addEventListener('swal', event => {
            const data = event.detail[0] || event.detail;
            Swal.fire({
                title: data.title || 'Informasi',
                text: data.text || '',
                icon: data.icon || 'info',
                confirmButtonColor: '#4C5C2D'
            });
        });
    ">

    @if(!$isEmbedded)
    {{-- Header / Breadcrumbs --}}
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <button type="button" onclick="history.length > 1 ? history.back() : window.location.href = '{{ route('modul.farmasi.visite') }}'"
                class="flex items-center justify-center w-10 h-8 rounded-md bg-[#4C5C2D] transition-colors hover:bg-[#3d4b24] shadow-sm">
                <flux:icon name="chevron-left" class="w-5 h-5 text-white" />
            </button>
            <div>
                <nav class="text-xs text-neutral-400 mb-0.5">
                    <a href="{{ route('modul.index') }}" wire:navigate class="hover:underline">Modul</a>
                    <span class="mx-1">/</span>
                    <a href="{{ route('modul.farmasi.index') }}" wire:navigate class="hover:underline">Farmasi</a>
                    <span class="mx-1">/</span>
                    <a href="{{ route('modul.farmasi.visite') }}" wire:navigate class="hover:underline">Visite</a>
                    <span class="mx-1">/</span>
                    <span>{{ $regPeriksa->pasien->nm_pasien ?? 'Pasien' }}</span>
                </nav>
                <h1 class="text-xl font-bold text-neutral-800 dark:text-neutral-100">Visite Pasien Rawat Inap</h1>
            </div>
        </div>
    </div>

    {{-- Banner Identitas Pasien --}}
    @php
        $kamarActive = $regPeriksa->kamarInap->firstWhere('tgl_keluar', '0000-00-00');
        $kamarDisplay = $kamarActive?->kd_kamar ?? $regPeriksa->kamarInap->first()?->kd_kamar ?? $regPeriksa->permintaanRanap->kd_kamar ?? '-';
        $isBpjs = str_contains(strtoupper($regPeriksa->penjab->png_jawab ?? ''), 'BPJS');
    @endphp
    <div class="bg-white dark:bg-neutral-800 rounded-xl border border-neutral-200 dark:border-neutral-700 p-4 shadow-sm">
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-3">
            <div class="bg-neutral-50 dark:bg-neutral-900/50 p-3 rounded-lg border border-neutral-100 dark:border-neutral-800">
                <p class="text-[10px] text-neutral-400 font-semibold uppercase tracking-wider mb-1">No. Rawat</p>
                <p class="text-xs font-bold text-neutral-800 dark:text-neutral-200 font-mono">{{ $regPeriksa->no_rawat }}</p>
            </div>
            <div class="bg-neutral-50 dark:bg-neutral-900/50 p-3 rounded-lg border border-neutral-100 dark:border-neutral-800">
                <p class="text-[10px] text-neutral-400 font-semibold uppercase tracking-wider mb-1">No. RM</p>
                <p class="text-xs font-bold text-neutral-800 dark:text-neutral-200 font-mono">{{ $regPeriksa->no_rkm_medis }}</p>
            </div>
            <div class="bg-[#F1F5E9] dark:bg-[#4C5C2D]/20 p-3 rounded-lg border border-[#4C5C2D]/30 col-span-2">
                <p class="text-[10px] text-[#4C5C2D] dark:text-[#8CC7C4] font-semibold uppercase tracking-wider mb-1">Nama Pasien</p>
                <div class="flex items-center gap-2">
                    <p class="text-sm font-bold text-neutral-800 dark:text-neutral-100 truncate">{{ $regPeriksa->pasien->nm_pasien ?? '-' }}</p>
                    @if(($regPeriksa->pasien->jk ?? '') === 'L')
                        <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300">L</span>
                    @elseif(($regPeriksa->pasien->jk ?? '') === 'P')
                        <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-pink-100 text-pink-700 dark:bg-pink-900/40 dark:text-pink-300">P</span>
                    @endif
                </div>
            </div>
            <div class="bg-neutral-50 dark:bg-neutral-900/50 p-3 rounded-lg border border-neutral-100 dark:border-neutral-800">
                <p class="text-[10px] text-neutral-400 font-semibold uppercase tracking-wider mb-1">Kamar</p>
                <p class="text-xs font-bold text-[#4C5C2D] dark:text-[#8CC7C4]">{{ $kamarDisplay }}</p>
            </div>
            <div class="bg-neutral-50 dark:bg-neutral-900/50 p-3 rounded-lg border border-neutral-100 dark:border-neutral-800">
                <p class="text-[10px] text-neutral-400 font-semibold uppercase tracking-wider mb-1">Jenis Bayar</p>
                @if($isBpjs)
                    <span class="inline-flex px-2 py-0.5 text-[11px] font-semibold rounded bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300">
                        {{ $regPeriksa->penjab->png_jawab ?? 'BPJS' }}
                    </span>
                @else
                    <p class="text-xs font-semibold text-neutral-700 dark:text-neutral-300">{{ $regPeriksa->penjab->png_jawab ?? '-' }}</p>
                @endif
            </div>
        </div>
    </div>
    @endif

    {{-- Main Table Container --}}
    <div class="bg-white dark:bg-neutral-800 rounded-xl border border-neutral-200 dark:border-neutral-700 shadow-sm overflow-hidden flex flex-col {{ $isEmbedded ? 'mt-2 p-4' : 'p-5' }}">
        @if($isEmbedded)
            <div class="flex items-center justify-between mb-4 px-2">
                <h3 class="text-sm font-bold text-neutral-700 dark:text-neutral-300 uppercase tracking-wider flex items-center gap-2">
                    <flux:icon name="beaker" class="w-4 h-4 text-[#4C5C2D]" />
                    Daftar Visite Farmasi
                </h3>
                <flux:button wire:click="openCreateModal" icon="plus" variant="filled" size="sm" class="!flex !flex-row !items-center !bg-[#4C5C2D] !text-white hover:!bg-[#3d4a24]">
                    Tambah Visite
                </flux:button>
            </div>
        @else
            <div class="flex items-center justify-between mb-5 pb-3 border-b border-neutral-100 dark:border-neutral-700">
                <div>
                    <h2 class="text-base font-bold text-neutral-800 dark:text-neutral-100">Daftar Kunjungan Visite</h2>
                    <p class="text-xs text-neutral-400">Riwayat visite klinis farmasi dan pemeriksaan berkala pasien</p>
                </div>
                <flux:button wire:click="openCreateModal" icon="plus" class="bg-[#4C5C2D] hover:bg-[#3d4b24] text-white text-xs font-semibold">
                    Tambah Visite
                </flux:button>
            </div>
        @endif

        {{-- Tabel List Visite Sesuai Permintaan --}}
        <flux:table :paginate="$visiteList">
            <flux:table.columns>
                <flux:table.column>{{ __('Tanggal Visite') }}</flux:table.column>
                <flux:table.column>{{ __('Jam') }}</flux:table.column>
                <flux:table.column>{{ __('Riwayat pengobatan') }}</flux:table.column>
                <flux:table.column>{{ __('Interaksi Obat') }}</flux:table.column>
                <flux:table.column align="center">{{ __('Action') }}</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse($visiteList as $item)
                    <flux:table.row :key="$item->id">
                        <flux:table.cell class="font-medium">
                            {{ \Carbon\Carbon::parse($item->tgl_visite)->format('d/m/Y') }}
                        </flux:table.cell>
                        <flux:table.cell class="font-mono text-xs">
                            {{ $item->jam_visite }}
                        </flux:table.cell>
                        <flux:table.cell class="max-w-xs">
                            <span class="text-xs text-neutral-700 dark:text-neutral-300" title="{{ $item->riwayat_pengobatan }}">
                                {{ \Illuminate\Support\Str::words($item->riwayat_pengobatan ?: '-', 4, '...') }}
                            </span>
                        </flux:table.cell>
                        <flux:table.cell class="max-w-xs">
                            <span class="text-xs text-neutral-700 dark:text-neutral-300" title="{{ $item->interaksi_obat }}">
                                {{ \Illuminate\Support\Str::words($item->interaksi_obat ?: '-', 4, '...') }}
                            </span>
                        </flux:table.cell>
                        <flux:table.cell align="center">
                            <div class="flex items-center justify-center gap-1.5">
                                <flux:button wire:click="showDetail({{ $item->id }})" size="xs" variant="ghost" icon="eye"
                                    class="text-neutral-600 hover:text-[#4C5C2D]" title="Lihat Detail Pemeriksaan" />
                                <flux:button wire:click="editVisite({{ $item->id }})" size="xs" variant="ghost" icon="pencil-square"
                                    class="text-neutral-600 hover:text-amber-600" title="Edit Data Visite" />
                                <flux:button type="button" @click="confirmDelete({{ $item->id }})" size="xs" variant="ghost" icon="trash"
                                    class="text-neutral-400 hover:text-red-600" title="Hapus Data Visite" />
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="5">
                            <div class="flex flex-col items-center justify-center py-12 text-neutral-400 dark:text-neutral-500">
                                <flux:icon name="clipboard-document-list" class="w-12 h-12 mb-3 opacity-30" />
                                <p class="text-sm font-medium">Belum ada catatan visite untuk pasien ini</p>
                                <p class="text-xs mt-1">Klik tombol <strong>Tambah Visite</strong> untuk memasukkan catatan visite baru</p>
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </div>

    {{-- ===== RIGHT SLIDE-IN PANEL (Consistent with SBAR & ADIME) ===== --}}
    <div x-data="{
            open: @entangle('isPanelOpen'),
            autoTime: @entangle('autoTime'),
            interval: null,
            init() {
                this.interval = setInterval(() => {
                    if(this.autoTime && this.open) {
                        let now = new Date();
                        $wire.tgl_visite = now.getFullYear() + '-' + String(now.getMonth()+1).padStart(2,'0') + '-' + String(now.getDate()).padStart(2,'0');
                        $wire.jam_visite = String(now.getHours()).padStart(2,'0') + ':' + String(now.getMinutes()).padStart(2,'0') + ':' + String(now.getSeconds()).padStart(2,'0');
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
                        <flux:icon name="beaker" class="w-5 h-5 text-[#4C5C2D]" />
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-neutral-800 dark:text-neutral-100">
                            {{ $isEditMode ? 'Edit Visite Farmasi' : 'Input Visite Farmasi' }}
                        </h3>
                        <p class="text-xs text-neutral-500">Catatan visite klinis farmasi dan pemantauan terapi obat pasien.</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <div x-show="!$wire.isEditMode" title="Isi otomatis data dari visite sebelumnya & petugas yang login" x-cloak>
                        <flux:button wire:click="fillAutoData" size="xs" variant="subtle" icon="bolt" class="text-[#4C5C2D] hover:bg-[#4C5C2D]/10">
                            Isi Otomatis
                        </flux:button>
                    </div>
                    <button @click="open = false" class="text-neutral-400 hover:text-neutral-600 dark:hover:text-neutral-200 transition-colors">
                        <flux:icon name="x-mark" class="w-6 h-6" />
                    </button>
                </div>
            </div>

            {{-- Reference Alert --}}
            @if($lastReference && !$isEditMode)
                <div class="px-8 py-3 bg-amber-50 dark:bg-amber-900/20 border-b border-amber-200 dark:border-amber-800/50 flex items-center gap-3 flex-shrink-0 animate-in slide-in-from-top-1 duration-300">
                    <flux:icon name="information-circle" class="w-5 h-5 text-amber-500 flex-shrink-0" />
                    <p class="text-[11px] text-amber-800 dark:text-amber-300 leading-tight">
                        <span class="font-bold">Mode Patokan Aktif:</span> Menampilkan rujukan dari data terakhir tanggal <span class="font-bold">{{ $lastReference['tanggal'] }}</span> jam <span class="font-bold">{{ $lastReference['jam'] }}</span>.
                        Input yang berwarna <span class="text-amber-600 dark:text-amber-400 font-bold italic">kuning gading</span> adalah data rujukan, silakan sesuaikan dengan kondisi terbaru.
                    </p>
                </div>
            @endif

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

                {{-- Tanggal, Jam & Petugas Row --}}
                <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
                    <div class="md:col-span-3">
                        <flux:input wire:model="tgl_visite" type="date" label="Tanggal *" required />
                        @error('tgl_visite') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="md:col-span-3">
                        <div class="flex items-center justify-between mb-1">
                            <flux:label class="!mb-0">Jam *</flux:label>
                            <div class="flex items-center gap-1.5 mt-2">
                                <flux:checkbox wire:model="autoTime" id="autoTimeVisite" />
                                <label for="autoTimeVisite" class="text-xs font-medium text-neutral-600 dark:text-neutral-400 cursor-pointer">Waktu Otomatis</label>
                            </div>
                        </div>
                        <flux:input wire:model="jam_visite" type="time" step="1" required />
                        @error('jam_visite') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="md:col-span-6 relative">
                        <flux:input wire:model.live.debounce.300ms="pegawaiSearch"
                                    label="Petugas Farmasi (NIP/Nama) *"
                                    placeholder="Ketik minimal 3 karakter..."
                                    icon="magnifying-glass" />

                        @if(!empty($pegawaiList) && count($pegawaiList) > 0)
                            <div class="absolute z-[60] left-0 right-0 mt-1 bg-white dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl shadow-xl max-h-48 overflow-y-auto">
                                @foreach($pegawaiList as $pg)
                                    <button type="button"
                                            wire:click="selectPetugas('{{ $pg->nik }}', '{{ $pg->nama }}')"
                                            class="w-full text-left px-4 py-3 hover:bg-neutral-50 dark:hover:bg-neutral-700 border-b border-neutral-100 dark:border-neutral-700 last:border-0 transition-colors group">
                                        <div class="font-bold text-sm text-neutral-700 dark:text-neutral-200 group-hover:text-[#4C5C2D]">{{ $pg->nama }}</div>
                                        <div class="text-[10px] text-neutral-400 font-mono tracking-wider">{{ $pg->jbtn }} | {{ $pg->nik }}</div>
                                    </button>
                                @endforeach
                            </div>
                        @endif

                        @if($nip)
                            @php $selectedPegawai = \App\Models\Pegawai::find($nip); @endphp
                            <div class="mt-2 p-3 bg-[#4C5C2D]/5 border border-[#4C5C2D]/20 rounded-xl flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <flux:icon name="user-circle" class="w-5 h-5 text-[#4C5C2D]" />
                                    <div>
                                        <div class="text-xs font-bold text-[#4C5C2D]">{{ $selectedPegawai->nama ?? $nip }}</div>
                                        <div class="text-[10px] text-neutral-500 font-mono tracking-wider">{{ $selectedPegawai->jbtn ?? $currentJabatan ?: '-' }} &bull; {{ $selectedPegawai->nik ?? $nip }}</div>
                                    </div>
                                </div>
                                <button type="button" wire:click="$set('nip', '')" class="text-neutral-400 hover:text-red-500 transition-colors">
                                    <flux:icon name="trash" class="w-4 h-4" />
                                </button>
                            </div>
                        @endif
                        @error('nip') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Khusus Farmasi Klinis --}}
                <div class="bg-[#F1F5E9] dark:bg-[#4C5C2D]/15 p-5 rounded-xl border border-[#4C5C2D]/30 flex flex-col gap-4">
                    <div class="flex items-center justify-between">
                        <p class="text-xs font-bold text-[#4C5C2D] dark:text-[#8CC7C4] uppercase tracking-wider flex items-center gap-1.5">
                            <flux:icon name="beaker" class="w-4 h-4" /> Evaluasi Farmasi Klinis
                        </p>
                        <span class="text-[11px] text-neutral-500">Telaah terapi obat dan interaksi klinis</span>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <flux:label>Riwayat Pengobatan</flux:label>
                            <textarea wire:model="riwayat_pengobatan"
                                      rows="3"
                                      placeholder="Masukkan riwayat penggunaan obat pasien..."
                                      class="mt-1 w-full rounded-xl border border-neutral-200 dark:border-neutral-600 bg-white dark:bg-neutral-900 text-neutral-800 dark:text-neutral-200 text-sm px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#4C5C2D]/40 focus:border-[#4C5C2D] transition resize-y"
                            ></textarea>
                        </div>
                        <div>
                            <flux:label>Interaksi Obat</flux:label>
                            <textarea wire:model="interaksi_obat"
                                      rows="3"
                                      placeholder="Catatan / potensi efek interaksi obat..."
                                      class="mt-1 w-full rounded-xl border border-neutral-200 dark:border-neutral-600 bg-white dark:bg-neutral-900 text-neutral-800 dark:text-neutral-200 text-sm px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#4C5C2D]/40 focus:border-[#4C5C2D] transition resize-y"
                            ></textarea>
                        </div>
                    </div>
                </div>

                {{-- 2-Column SOAP & Vital Signs --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start">
                    {{-- LEFT COLUMN --}}
                    <div class="space-y-5">
                        <div>
                            <flux:label>Subjek (Keluhan Utama)</flux:label>
                            <textarea wire:model="keluhan"
                                      rows="3"
                                      placeholder="Masukkan subjek/keluhan utama pasien..."
                                      class="mt-1 w-full rounded-xl border border-neutral-200 dark:border-neutral-600 bg-white dark:bg-neutral-900 text-neutral-800 dark:text-neutral-200 text-sm px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#4C5C2D]/40 focus:border-[#4C5C2D] transition resize-y"
                            ></textarea>
                        </div>

                        <div>
                            <flux:label>Objek (Pemeriksaan Fisik)</flux:label>
                            <textarea wire:model="pemeriksaan"
                                      rows="3"
                                      placeholder="Masukkan objek/pemeriksaan fisik..."
                                      class="mt-1 w-full rounded-xl border border-neutral-200 dark:border-neutral-600 bg-white dark:bg-neutral-900 text-neutral-800 dark:text-neutral-200 text-sm px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#4C5C2D]/40 focus:border-[#4C5C2D] transition resize-y"
                            ></textarea>
                        </div>

                        {{-- Vital Signs Grid --}}
                        <div class="bg-neutral-50 dark:bg-neutral-900 p-5 rounded-xl border border-neutral-100 dark:border-neutral-800 shadow-sm space-y-4">
                            <p class="text-[10px] font-bold uppercase tracking-wider text-neutral-400 flex items-center gap-1.5">
                                <flux:icon name="heart" class="w-3.5 h-3.5 text-rose-500" /> Tanda-Tanda Vital
                            </p>
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                                <div>
                                    <div class="flex items-center justify-between mb-1">
                                        <flux:label class="!mb-0 text-xs">Suhu (&deg;C)</flux:label>
                                        @if($lastReference)<span class="text-[9px] text-amber-600 dark:text-amber-400 font-medium">{{ $lastReference['suhu_tubuh'] }}</span>@endif
                                    </div>
                                    <flux:input wire:model="suhu_tubuh" size="sm" placeholder="36.5" class="{{ $lastReference && $suhu_tubuh == $lastReference['suhu_tubuh'] ? 'bg-amber-50/50 dark:bg-amber-900/10' : '' }}" />
                                </div>
                                <div>
                                    <div class="flex items-center justify-between mb-1">
                                        <flux:label class="!mb-0 text-xs">Tensi</flux:label>
                                        @if($lastReference)<span class="text-[9px] text-amber-600 dark:text-amber-400 font-medium">{{ $lastReference['tensi'] }}</span>@endif
                                    </div>
                                    <flux:input wire:model="tensi" size="sm" placeholder="120/80" class="{{ $lastReference && $tensi == $lastReference['tensi'] ? 'bg-amber-50/50 dark:bg-amber-900/10' : '' }}" />
                                </div>
                                <div>
                                    <div class="flex items-center justify-between mb-1">
                                        <flux:label class="!mb-0 text-xs">Berat (Kg)</flux:label>
                                        @if($lastReference)<span class="text-[9px] text-amber-600 dark:text-amber-400 font-medium">{{ $lastReference['berat'] }}</span>@endif
                                    </div>
                                    <flux:input wire:model="berat" size="sm" placeholder="60" class="{{ $lastReference && $berat == $lastReference['berat'] ? 'bg-amber-50/50 dark:bg-amber-900/10' : '' }}" />
                                </div>
                                <div>
                                    <div class="flex items-center justify-between mb-1">
                                        <flux:label class="!mb-0 text-xs">TB (Cm)</flux:label>
                                        @if($lastReference)<span class="text-[9px] text-amber-600 dark:text-amber-400 font-medium">{{ $lastReference['tinggi'] }}</span>@endif
                                    </div>
                                    <flux:input wire:model="tinggi" size="sm" placeholder="165" class="{{ $lastReference && $tinggi == $lastReference['tinggi'] ? 'bg-amber-50/50 dark:bg-amber-900/10' : '' }}" />
                                </div>
                                <div>
                                    <div class="flex items-center justify-between mb-1">
                                        <flux:label class="!mb-0 text-xs">RR (/mnt)</flux:label>
                                        @if($lastReference)<span class="text-[9px] text-amber-600 dark:text-amber-400 font-medium">{{ $lastReference['respirasi'] }}</span>@endif
                                    </div>
                                    <flux:input wire:model="respirasi" size="sm" placeholder="20" class="{{ $lastReference && $respirasi == $lastReference['respirasi'] ? 'bg-amber-50/50 dark:bg-amber-900/10' : '' }}" />
                                </div>
                                <div>
                                    <div class="flex items-center justify-between mb-1">
                                        <flux:label class="!mb-0 text-xs">Nadi (/mnt)</flux:label>
                                        @if($lastReference)<span class="text-[9px] text-amber-600 dark:text-amber-400 font-medium">{{ $lastReference['nadi'] }}</span>@endif
                                    </div>
                                    <flux:input wire:model="nadi" size="sm" placeholder="80" class="{{ $lastReference && $nadi == $lastReference['nadi'] ? 'bg-amber-50/50 dark:bg-amber-900/10' : '' }}" />
                                </div>
                                <div>
                                    <div class="flex items-center justify-between mb-1">
                                        <flux:label class="!mb-0 text-xs">SpO2 (%)</flux:label>
                                        @if($lastReference)<span class="text-[9px] text-amber-600 dark:text-amber-400 font-medium">{{ $lastReference['spo2'] }}%</span>@endif
                                    </div>
                                    <flux:input wire:model="spo2" size="sm" placeholder="98" class="{{ $lastReference && $spo2 == $lastReference['spo2'] ? 'bg-amber-50/50 dark:bg-amber-900/10' : '' }}" />
                                </div>
                                <div>
                                    <div class="flex items-center justify-between mb-1">
                                        <flux:label class="!mb-0 text-xs">GCS</flux:label>
                                        @if($lastReference)<span class="text-[9px] text-amber-600 dark:text-amber-400 font-medium">{{ $lastReference['gcs'] }}</span>@endif
                                    </div>
                                    <flux:input wire:model="gcs" size="sm" placeholder="15" class="{{ $lastReference && $gcs == $lastReference['gcs'] ? 'bg-amber-50/50 dark:bg-amber-900/10' : '' }}" />
                                </div>
                            </div>
                            <div class="pt-3 border-t border-neutral-200 dark:border-neutral-700/50 flex items-center gap-3">
                                <flux:label class="!mb-0 text-xs w-24">Kesadaran :</flux:label>
                                <flux:select wire:model="kesadaran" class="flex-1" size="sm">
                                    <flux:select.option value="Compos Mentis">Compos Mentis</flux:select.option>
                                    <flux:select.option value="Somnolence">Somnolence</flux:select.option>
                                    <flux:select.option value="Sopor">Sopor</flux:select.option>
                                    <flux:select.option value="Coma">Coma</flux:select.option>
                                    <flux:select.option value="Apatis">Apatis</flux:select.option>
                                    <flux:select.option value="Delirium">Delirium</flux:select.option>
                                </flux:select>
                            </div>
                        </div>
                    </div>

                    {{-- RIGHT COLUMN --}}
                    <div class="space-y-5">
                        <div>
                            <flux:label>Asesmen (Penilaian Klinis)</flux:label>
                            <textarea wire:model="penilaian"
                                      rows="3"
                                      placeholder="Masukkan penilaian klinis..."
                                      class="mt-1 w-full rounded-xl border border-neutral-200 dark:border-neutral-600 bg-white dark:bg-neutral-900 text-neutral-800 dark:text-neutral-200 text-sm px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#4C5C2D]/40 focus:border-[#4C5C2D] transition resize-y"
                            ></textarea>
                            @error('penilaian') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <flux:label>Plan (Rencana Tindak Lanjut)</flux:label>
                            <textarea wire:model="rtl"
                                      rows="3"
                                      placeholder="Masukkan rencana tindak lanjut farmakoterapi..."
                                      class="mt-1 w-full rounded-xl border border-neutral-200 dark:border-neutral-600 bg-white dark:bg-neutral-900 text-neutral-800 dark:text-neutral-200 text-sm px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#4C5C2D]/40 focus:border-[#4C5C2D] transition resize-y"
                            ></textarea>
                            @error('rtl') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <flux:label>Instruksi / Implementasi</flux:label>
                            <textarea wire:model="instruksi"
                                      rows="3"
                                      placeholder="Masukkan instruksi khusus atau tindakan..."
                                      class="mt-1 w-full rounded-xl border border-neutral-200 dark:border-neutral-600 bg-white dark:bg-neutral-900 text-neutral-800 dark:text-neutral-200 text-sm px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#4C5C2D]/40 focus:border-[#4C5C2D] transition resize-y"
                            ></textarea>
                        </div>

                        <div>
                            <flux:label>Evaluasi (Respon Pasien)</flux:label>
                            <textarea wire:model="evaluasi"
                                      rows="3"
                                      placeholder="Masukkan respon / evaluasi pengobatan pasien..."
                                      class="mt-1 w-full rounded-xl border border-neutral-200 dark:border-neutral-600 bg-white dark:bg-neutral-900 text-neutral-800 dark:text-neutral-200 text-sm px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#4C5C2D]/40 focus:border-[#4C5C2D] transition resize-y"
                            ></textarea>
                        </div>

                        <div>
                            <flux:label class="font-bold text-red-600 dark:text-red-400">Riwayat Alergi</flux:label>
                            <flux:input wire:model="alergi" placeholder="Ada alergi makanan/obat? (Isi '-' jika tidak ada)" class="border-red-200 focus:border-red-500" />
                        </div>
                    </div>
                </div>
            </div>

            {{-- Panel Footer --}}
            <div class="px-8 py-5 bg-neutral-50/70 dark:bg-neutral-900/60 border-t border-neutral-100 dark:border-neutral-700 flex justify-end gap-3 flex-shrink-0">
                <flux:button @click="open = false" variant="ghost">Batal</flux:button>
                <flux:button wire:click="save" variant="filled" class="!bg-[#4C5C2D] !text-white hover:!bg-[#3d4a24]" wire:loading.attr="disabled">
                    <div class="flex items-center justify-center gap-2">
                        <flux:icon name="check" class="w-4 h-4" wire:loading.remove wire:target="save" />
                        <flux:icon name="arrow-path" class="w-4 h-4 animate-spin" wire:loading wire:target="save" />
                        <span x-text="$wire.isEditMode ? 'Update Perubahan' : 'Simpan Visite'"></span>
                    </div>
                </flux:button>
            </div>
        </div>
    </div>

    {{-- ===== MODAL DETAIL VISITE (Alpine.js Pure Modal) ===== --}}
    <template x-teleport="body">
        <div x-show="$wire.detailModalOpen" x-cloak
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-[9999] flex items-center justify-center p-4" style="display: none;"
            @keydown.escape.window="$wire.detailModalOpen = false">

            {{-- Backdrop --}}
            <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="$wire.detailModalOpen = false"></div>

            {{-- Panel --}}
            <div x-show="$wire.detailModalOpen"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95 translate-y-3"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 scale-95 translate-y-3"
                class="relative w-full max-w-5xl max-h-[92vh] flex flex-col bg-white dark:bg-neutral-900 rounded-2xl shadow-2xl border border-neutral-200 dark:border-neutral-700 overflow-hidden"
                @click.stop>

                {{-- Header --}}
                <div class="flex items-center justify-between px-6 py-4 border-b border-neutral-200 dark:border-neutral-700 bg-gradient-to-r from-[#4C5C2D]/10 to-transparent flex-shrink-0">
                    <div class="flex items-center gap-3">
                        <div class="p-2.5 rounded-xl bg-[#4C5C2D]/15 dark:bg-[#4C5C2D]/30 text-[#4C5C2D] dark:text-[#8CC7C4]">
                            <flux:icon name="clipboard-document-check" class="w-5 h-5" />
                        </div>
                        <div>
                            <h2 class="font-bold text-neutral-800 dark:text-neutral-100 text-base">Detail Kunjungan Visite</h2>
                            <p class="text-xs text-neutral-400">
                                <span>{{ $activeDetail['tgl_visite'] ?? '' }}</span> &bull;
                                <span>{{ $activeDetail['jam_visite'] ?? '' }}</span> &bull;
                                <span class="font-mono">{{ $activeDetail['no_rawat'] ?? '' }}</span>
                            </p>
                        </div>
                    </div>
                    <button type="button" @click="$wire.detailModalOpen = false" class="p-1.5 rounded-lg text-neutral-400 hover:text-neutral-600 dark:hover:text-neutral-200 transition-colors">
                        <flux:icon name="x-mark" class="w-5 h-5" />
                    </button>
                </div>

                {{-- Scrollable Detail Content --}}
                <div class="overflow-y-auto flex-1 p-6 flex flex-col gap-5 bg-neutral-50/40 dark:bg-neutral-900/30">
                    {{-- Identitas Pasien --}}
                    <div class="bg-white dark:bg-neutral-800 p-4 rounded-xl border border-neutral-200 dark:border-neutral-700 shadow-sm">
                        <p class="text-[10px] font-bold uppercase tracking-wider text-[#4C5C2D] dark:text-[#8CC7C4] mb-3 flex items-center gap-1.5">
                            <flux:icon name="identification" class="w-3.5 h-3.5" /> Identitas Pasien
                        </p>
                        <div class="grid grid-cols-2 md:grid-cols-5 gap-3 text-xs">
                            <div class="p-2.5 bg-neutral-50 dark:bg-neutral-900/50 rounded-lg">
                                <p class="text-[10px] text-neutral-400 uppercase">Tanggal Visite</p>
                                <p class="font-bold text-neutral-800 dark:text-neutral-200">{{ $activeDetail['tgl_visite'] ?? '-' }}</p>
                            </div>
                            <div class="p-2.5 bg-neutral-50 dark:bg-neutral-900/50 rounded-lg">
                                <p class="text-[10px] text-neutral-400 uppercase">Jam</p>
                                <p class="font-bold text-neutral-800 dark:text-neutral-200 font-mono">{{ $activeDetail['jam_visite'] ?? '-' }}</p>
                            </div>
                            <div class="p-2.5 bg-neutral-50 dark:bg-neutral-900/50 rounded-lg">
                                <p class="text-[10px] text-neutral-400 uppercase">No. Rawat</p>
                                <p class="font-bold text-neutral-800 dark:text-neutral-200 font-mono">{{ $activeDetail['no_rawat'] ?? '-' }}</p>
                            </div>
                            <div class="p-2.5 bg-neutral-50 dark:bg-neutral-900/50 rounded-lg">
                                <p class="text-[10px] text-neutral-400 uppercase">No. RM</p>
                                <p class="font-bold text-neutral-800 dark:text-neutral-200 font-mono">{{ $activeDetail['no_rkm_medis'] ?? '-' }}</p>
                            </div>
                            <div class="p-2.5 bg-[#F1F5E9] dark:bg-[#4C5C2D]/20 rounded-lg border border-[#4C5C2D]/30 col-span-2 md:col-span-1">
                                <p class="text-[10px] text-[#4C5C2D] uppercase font-semibold">Nama Pasien</p>
                                <p class="font-bold text-neutral-900 dark:text-neutral-100 truncate">{{ $activeDetail['nm_pasien'] ?? '-' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
                        {{-- Tanda-Tanda Vital --}}
                        <div class="bg-white dark:bg-neutral-800 p-4 rounded-xl border border-neutral-200 dark:border-neutral-700 shadow-sm flex flex-col gap-3">
                            <p class="text-[10px] font-bold uppercase tracking-wider text-neutral-400 flex items-center gap-1.5">
                                <flux:icon name="heart" class="w-3.5 h-3.5 text-rose-500" /> Tanda-Tanda Vital
                            </p>
                            <div class="grid grid-cols-2 gap-2 text-xs">
                                <div class="p-2 bg-neutral-50 dark:bg-neutral-900/50 rounded">
                                    <p class="text-[10px] text-neutral-400">Suhu</p>
                                    <p class="font-semibold">{{ $activeDetail['suhu_tubuh'] ?? '-' }} &deg;C</p>
                                </div>
                                <div class="p-2 bg-neutral-50 dark:bg-neutral-900/50 rounded">
                                    <p class="text-[10px] text-neutral-400">Tensi</p>
                                    <p class="font-semibold">{{ $activeDetail['tensi'] ?? '-' }}</p>
                                </div>
                                <div class="p-2 bg-neutral-50 dark:bg-neutral-900/50 rounded">
                                    <p class="text-[10px] text-neutral-400">Nadi</p>
                                    <p class="font-semibold">{{ $activeDetail['nadi'] ?? '-' }} /mnt</p>
                                </div>
                                <div class="p-2 bg-neutral-50 dark:bg-neutral-900/50 rounded">
                                    <p class="text-[10px] text-neutral-400">Respirasi</p>
                                    <p class="font-semibold">{{ $activeDetail['respirasi'] ?? '-' }} /mnt</p>
                                </div>
                                <div class="p-2 bg-neutral-50 dark:bg-neutral-900/50 rounded">
                                    <p class="text-[10px] text-neutral-400">Tinggi</p>
                                    <p class="font-semibold">{{ $activeDetail['tinggi'] ?? '-' }} cm</p>
                                </div>
                                <div class="p-2 bg-neutral-50 dark:bg-neutral-900/50 rounded">
                                    <p class="text-[10px] text-neutral-400">Berat</p>
                                    <p class="font-semibold">{{ $activeDetail['berat'] ?? '-' }} kg</p>
                                </div>
                                <div class="p-2 bg-neutral-50 dark:bg-neutral-900/50 rounded">
                                    <p class="text-[10px] text-neutral-400">SpO2</p>
                                    <p class="font-semibold">{{ $activeDetail['spo2'] ?? '-' }}%</p>
                                </div>
                                <div class="p-2 bg-neutral-50 dark:bg-neutral-900/50 rounded">
                                    <p class="text-[10px] text-neutral-400">GCS</p>
                                    <p class="font-semibold">{{ $activeDetail['gcs'] ?? '-' }}</p>
                                </div>
                            </div>
                            <div class="mt-2 p-2.5 bg-neutral-100 dark:bg-neutral-800 rounded-lg text-xs flex items-center justify-between">
                                <span class="text-neutral-500 font-medium">Kesadaran:</span>
                                <span class="font-bold text-[#4C5C2D] dark:text-[#8CC7C4]">{{ $activeDetail['kesadaran'] ?? '-' }}</span>
                            </div>
                            @if(!empty($activeDetail['alergi']) && $activeDetail['alergi'] !== '-')
                                <div class="p-2 bg-rose-50 dark:bg-rose-950/30 rounded border border-rose-200 dark:border-rose-900/50 text-xs">
                                    <p class="text-[10px] font-bold text-rose-600">Alergi Pasien:</p>
                                    <p class="text-rose-700 dark:text-rose-300 font-medium">{{ $activeDetail['alergi'] }}</p>
                                </div>
                            @endif
                        </div>

                        {{-- Catatan Pemeriksaan (SOAPIE) --}}
                        <div class="lg:col-span-2 flex flex-col gap-4">
                            <div class="bg-white dark:bg-neutral-800 p-4 rounded-xl border border-neutral-200 dark:border-neutral-700 shadow-sm flex flex-col gap-3">
                                <p class="text-[10px] font-bold uppercase tracking-wider text-neutral-400 flex items-center gap-1.5">
                                    <flux:icon name="document-text" class="w-3.5 h-3.5" /> Catatan Pemeriksaan (SOAPIE)
                                </p>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-xs">
                                    <div class="p-3 bg-neutral-50 dark:bg-neutral-900/50 rounded-lg border border-neutral-100 dark:border-neutral-800">
                                        <p class="text-[10px] font-bold text-neutral-400 uppercase mb-1">Subjek (Keluhan Utama)</p>
                                        <p class="whitespace-pre-line text-neutral-800 dark:text-neutral-200">{{ $activeDetail['keluhan'] ?? '-' }}</p>
                                    </div>
                                    <div class="p-3 bg-neutral-50 dark:bg-neutral-900/50 rounded-lg border border-neutral-100 dark:border-neutral-800">
                                        <p class="text-[10px] font-bold text-neutral-400 uppercase mb-1">Objek (Pemeriksaan Fisik)</p>
                                        <p class="whitespace-pre-line text-neutral-800 dark:text-neutral-200">{{ $activeDetail['pemeriksaan'] ?? '-' }}</p>
                                    </div>
                                    <div class="p-3 bg-neutral-50 dark:bg-neutral-900/50 rounded-lg border border-neutral-100 dark:border-neutral-800">
                                        <p class="text-[10px] font-bold text-neutral-400 uppercase mb-1">Asesmen (Penilaian Klinis)</p>
                                        <p class="whitespace-pre-line text-neutral-800 dark:text-neutral-200">{{ $activeDetail['penilaian'] ?? '-' }}</p>
                                    </div>
                                    <div class="p-3 bg-neutral-50 dark:bg-neutral-900/50 rounded-lg border border-neutral-100 dark:border-neutral-800">
                                        <p class="text-[10px] font-bold text-neutral-400 uppercase mb-1">Plan (RTL)</p>
                                        <p class="whitespace-pre-line text-neutral-800 dark:text-neutral-200">{{ $activeDetail['rtl'] ?? '-' }}</p>
                                    </div>
                                    <div class="p-3 bg-neutral-50 dark:bg-neutral-900/50 rounded-lg border border-neutral-100 dark:border-neutral-800">
                                        <p class="text-[10px] font-bold text-neutral-400 uppercase mb-1">Inst/Impl (Tindakan)</p>
                                        <p class="whitespace-pre-line text-neutral-800 dark:text-neutral-200">{{ $activeDetail['instruksi'] ?? '-' }}</p>
                                    </div>
                                    <div class="p-3 bg-neutral-50 dark:bg-neutral-900/50 rounded-lg border border-neutral-100 dark:border-neutral-800">
                                        <p class="text-[10px] font-bold text-neutral-400 uppercase mb-1">Evaluasi (Respon)</p>
                                        <p class="whitespace-pre-line text-neutral-800 dark:text-neutral-200">{{ $activeDetail['evaluasi'] ?? '-' }}</p>
                                    </div>
                                </div>
                            </div>

                            {{-- Catatan Farmasi Klinis --}}
                            <div class="bg-[#F1F5E9] dark:bg-[#4C5C2D]/20 p-4 rounded-xl border border-[#4C5C2D]/30 flex flex-col gap-3">
                                <p class="text-[10px] font-bold uppercase tracking-wider text-[#4C5C2D] dark:text-[#8CC7C4] flex items-center gap-1.5">
                                    <flux:icon name="beaker" class="w-3.5 h-3.5" /> Evaluasi Farmasi Klinis
                                </p>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-xs">
                                    <div class="p-3 bg-white dark:bg-neutral-800 rounded-lg border border-[#4C5C2D]/20 shadow-sm">
                                        <p class="text-[10px] font-bold text-[#4C5C2D] uppercase mb-1">Riwayat Pengobatan</p>
                                        <p class="whitespace-pre-line text-neutral-800 dark:text-neutral-200">{{ $activeDetail['riwayat_pengobatan'] ?? '-' }}</p>
                                    </div>
                                    <div class="p-3 bg-white dark:bg-neutral-800 rounded-lg border border-[#4C5C2D]/20 shadow-sm">
                                        <p class="text-[10px] font-bold text-[#4C5C2D] uppercase mb-1">Interaksi Obat</p>
                                        <p class="whitespace-pre-line text-neutral-800 dark:text-neutral-200">{{ $activeDetail['interaksi_obat'] ?? '-' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Petugas Pemeriksa --}}
                    <div class="bg-white dark:bg-neutral-800 p-4 rounded-xl border border-neutral-200 dark:border-neutral-700 shadow-sm flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-neutral-100 dark:bg-neutral-700 flex items-center justify-center text-neutral-500">
                            <flux:icon name="user" class="w-5 h-5" />
                        </div>
                        <div>
                            <p class="text-xs font-bold text-neutral-800 dark:text-neutral-100">{{ $activeDetail['nm_pegawai'] ?? '-' }}</p>
                            <p class="text-[10px] text-neutral-400">{{ $activeDetail['jbtn'] ?? '-' }} &bull; NIP/NIK: {{ $activeDetail['nip'] ?? '-' }}</p>
                        </div>
                    </div>
                </div>

                {{-- Footer Modal Detail --}}
                <div class="px-6 py-4 border-t border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 flex items-center justify-between flex-shrink-0">
                    <div class="flex items-center gap-2">
                        @if(!empty($activeDetail['id']))
                            <flux:button wire:click="editVisite({{ $activeDetail['id'] }})" size="sm" variant="subtle" icon="pencil-square" class="text-amber-600 hover:bg-amber-50">
                                Edit Data Ini
                            </flux:button>
                            <flux:button type="button" @click="confirmDelete({{ $activeDetail['id'] }})" size="sm" variant="subtle" icon="trash" class="text-red-600 hover:bg-red-50">
                                Hapus Data
                            </flux:button>
                        @endif
                    </div>
                    <flux:button type="button" @click="$wire.detailModalOpen = false" variant="ghost">Tutup</flux:button>
                </div>
            </div>
        </div>
    </template>
</div>
