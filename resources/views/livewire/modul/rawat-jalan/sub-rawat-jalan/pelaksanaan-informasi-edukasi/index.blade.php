<div class="flex flex-col gap-6 pb-8" x-data="{
    detailModalOpen: false
}">
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
                        <span>Edukasi</span>
                        <span class="mx-1">/</span>
                        <span class="text-neutral-600 dark:text-neutral-300 font-medium">Pelaksanaan Informasi & Edukasi</span>
                    </nav>
                    <h1 class="text-xl font-bold text-neutral-800 dark:text-neutral-100">Pelaksanaan Informasi dan Edukasi</h1>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <flux:button type="button" icon="book-open" variant="subtle" size="sm" class="!text-neutral-600 dark:!text-neutral-300 hover:!text-[#4C5C2D]" title="Panduan SOP Informasi & Edukasi">
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
                    <span class="font-medium text-neutral-600 dark:text-neutral-300">{{ $regPeriksa->dokter->nm_dokter ?? '-' }}</span>
                </div>
            </div>
        </div>
        <div class="text-right">
            <div class="text-xs text-neutral-400 mb-0.5">No. Rawat</div>
            <div class="font-mono font-bold text-[#4C5C2D] dark:text-[#8CC7C4] text-sm md:text-base">{{ $regPeriksa->no_rawat }}</div>
        </div>
    </div>

    {{-- Table List Data Edukasi --}}
    <div class="bg-white dark:bg-neutral-800 rounded-xl border border-neutral-200 dark:border-neutral-700 shadow-sm overflow-hidden flex flex-col p-4">
        <div class="flex items-center justify-between mb-4 px-2">
            <h3 class="text-sm font-bold text-neutral-700 dark:text-neutral-300 uppercase tracking-wider flex items-center gap-2">
                <flux:icon name="clipboard-document-list" class="w-4 h-4 text-[#4C5C2D] dark:text-[#8CC7C4]" />
                Daftar Pelaksanaan Informasi & Edukasi
                <span class="ml-1 px-2 py-0.5 text-xs font-semibold rounded-full bg-[#F1F5E9] text-[#4C5C2D] dark:bg-neutral-700 dark:text-[#8CC7C4]">
                    {{ count($edukasiList) }} Data
                </span>
            </h3>

            <flux:button
                type="button"
                wire:click="openPanel"
                icon="plus"
                variant="filled"
                size="sm"
                class="!flex !flex-row !items-center !bg-[#4C5C2D] !text-white hover:!bg-[#3d4b24]">
                Tambah Edukasi
            </flux:button>
        </div>

        <div class="overflow-x-auto">
            <flux:table class="whitespace-nowrap mt-2">
                <flux:table.columns>
                    <flux:table.column class="!pl-4 text-center">Action</flux:table.column>
                    <flux:table.column>Tanggal & Jam</flux:table.column>
                    <flux:table.column>Petugas / Edukator</flux:table.column>
                    <flux:table.column>Materi Edukasi</flux:table.column>
                    <flux:table.column>Diberikan Kepada</flux:table.column>
                    <flux:table.column class="text-center">Durasi & Metode</flux:table.column>
                    <flux:table.column class="text-center">Hasil Verifikasi</flux:table.column>
                    <flux:table.column class="text-center">Status</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @forelse ($edukasiList as $item)
                        @php
                            $tglString = $item->tanggal ? $item->tanggal->format('Y-m-d H:i:s') : '';
                        @endphp
                        <flux:table.row>
                            {{-- Action (Detail, Edit & Delete) --}}
                            <flux:table.cell class="!pl-4 text-center">
                                <div class="flex items-center justify-center gap-1">
                                    <flux:button
                                        type="button"
                                        wire:click="showDetail('{{ $tglString }}')"
                                        @click="detailModalOpen = true"
                                        icon="eye"
                                        size="xs"
                                        variant="ghost"
                                        class="!text-emerald-600 hover:!bg-emerald-50 dark:hover:!bg-emerald-900/30"
                                        title="Lihat Detail Lengkap" />
                                    <flux:button
                                        type="button"
                                        wire:click="edit('{{ $tglString }}')"
                                        icon="pencil-square"
                                        size="xs"
                                        variant="ghost"
                                        class="!text-blue-600 hover:!bg-blue-50 dark:hover:!bg-blue-900/30"
                                        title="Edit Edukasi" />
                                    <flux:button
                                        type="button"
                                        @click="
                                            Swal.fire({
                                                title: 'Hapus Catatan Edukasi?',
                                                text: 'Data pelaksanaan edukasi ini akan dihapus secara permanen.',
                                                icon: 'warning',
                                                showCancelButton: true,
                                                confirmButtonColor: '#dc2626',
                                                cancelButtonColor: '#6b7280',
                                                confirmButtonText: 'Ya, Hapus!',
                                                cancelButtonText: 'Batal'
                                            }).then((result) => {
                                                if (result.isConfirmed) {
                                                    $wire.delete('{{ $tglString }}');
                                                }
                                            });
                                        "
                                        icon="trash"
                                        size="xs"
                                        variant="ghost"
                                        class="!text-red-600 hover:!bg-red-50 dark:hover:!bg-red-900/30"
                                        title="Hapus Edukasi" />
                                </div>
                            </flux:table.cell>

                            {{-- Tanggal & Jam --}}
                            <flux:table.cell class="text-xs font-medium text-neutral-800 dark:text-neutral-200">
                                <div>{{ $item->tanggal ? $item->tanggal->format('d/m/Y') : '-' }}</div>
                                <div class="text-[11px] font-mono text-neutral-400">{{ $item->tanggal ? $item->tanggal->format('H:i') : '' }} WIB</div>
                            </flux:table.cell>

                            {{-- Petugas / Edukator --}}
                            <flux:table.cell class="text-xs">
                                <span class="font-mono font-bold text-neutral-800 dark:text-neutral-200 block">{{ $item->nik }}</span>
                                <span class="text-neutral-500 dark:text-neutral-400">{{ $item->pegawai->nama ?? ($item->petugas->nama ?? '-') }}</span>
                            </flux:table.cell>

                            {{-- Materi Edukasi --}}
                            <flux:table.cell class="text-xs max-w-xs">
                                <div class="font-medium text-neutral-800 dark:text-neutral-200 truncate max-w-[280px]" title="{{ $item->materi_edukasi }}">
                                    {{ $item->materi_edukasi ?: '-' }}
                                </div>
                                @if($item->keterangan)
                                    <div class="text-[11px] text-neutral-400 truncate max-w-[280px]" title="{{ $item->keterangan }}">
                                        Ket: {{ $item->keterangan }}
                                    </div>
                                @endif
                            </flux:table.cell>

                            {{-- Diberikan Kepada --}}
                            <flux:table.cell class="text-xs">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-neutral-100 dark:bg-neutral-700 text-neutral-700 dark:text-neutral-200">
                                    {{ $item->diberikan_pada ?: '-' }}
                                </span>
                                @if($item->keterangan_diberikan_pada)
                                    <div class="text-[11px] text-neutral-400 mt-0.5">({{ $item->keterangan_diberikan_pada }})</div>
                                @endif
                            </flux:table.cell>

                            {{-- Durasi & Metode --}}
                            <flux:table.cell class="text-center text-xs">
                                <div class="font-semibold text-neutral-800 dark:text-neutral-200">{{ $item->lama_edukasi ? $item->lama_edukasi . ' Menit' : '-' }}</div>
                                <div class="text-[11px] text-neutral-400">{{ $item->metode_edukasi ?: '-' }}</div>
                            </flux:table.cell>

                            {{-- Hasil Verifikasi --}}
                            <flux:table.cell class="text-center">
                                @php
                                    $verifClass = match($item->hasil_verifikasi) {
                                        'Sudah Mengerti' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300 border-emerald-300',
                                        'Re-Edukasi'     => 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300 border-amber-300',
                                        'Re-Demonstrasi' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300 border-blue-300',
                                        default          => 'bg-neutral-100 text-neutral-700 dark:bg-neutral-700 dark:text-neutral-300 border-neutral-300'
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold border {{ $verifClass }}">
                                    {{ $item->hasil_verifikasi ?: '-' }}
                                </span>
                            </flux:table.cell>

                            {{-- Status --}}
                            <flux:table.cell class="text-center">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold {{ $item->status === 'Awal' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300' : 'bg-purple-100 text-purple-800 dark:bg-purple-900/40 dark:text-purple-300' }}">
                                    {{ $item->status ?: 'Awal' }}
                                </span>
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="8">
                                <div class="flex flex-col items-center justify-center py-12 text-neutral-400 dark:text-neutral-500">
                                    <flux:icon name="academic-cap" class="w-12 h-12 mb-3 opacity-40 text-[#4C5C2D]" />
                                    <p class="text-sm font-medium">Belum ada catatan pelaksanaan informasi & edukasi untuk pasien ini</p>
                                    <flux:button type="button" wire:click="openPanel" icon="plus" size="sm" class="mt-3 !bg-[#4C5C2D] !text-white hover:!bg-[#3d4b24]">
                                        Input Edukasi Sekarang
                                    </flux:button>
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </div>
    </div>

    {{-- MODAL DETAIL POP UP (ALPINE.JS MURNI - SOP #6) --}}
    <div x-show="detailModalOpen"
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto"
         style="display: none;">
        {{-- Backdrop --}}
        <div class="fixed inset-0 bg-neutral-900/60 backdrop-blur-sm transition-opacity"
             @click="detailModalOpen = false"></div>

        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative bg-white dark:bg-neutral-800 rounded-2xl shadow-2xl max-w-2xl w-full border border-neutral-200 dark:border-neutral-700 overflow-hidden transform transition-all"
                 @click.stop>

                {{-- Modal Header --}}
                <div class="px-6 py-4 border-b border-neutral-200 dark:border-neutral-700 flex items-center justify-between bg-neutral-50 dark:bg-neutral-900/70">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-[#4C5C2D]/10 dark:bg-[#4C5C2D]/30 flex items-center justify-center flex-shrink-0">
                            <flux:icon name="academic-cap" class="w-5 h-5 text-[#4C5C2D] dark:text-[#8CC7C4]" />
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-neutral-800 dark:text-neutral-100">
                                Detail Pelaksanaan Informasi & Edukasi
                            </h3>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">Rincian materi, sasaran, metode, dan verifikasi edukasi</p>
                        </div>
                    </div>
                    <button type="button" @click="detailModalOpen = false" class="text-neutral-400 hover:text-neutral-600 dark:hover:text-neutral-200 p-1.5 rounded-lg hover:bg-neutral-100 dark:hover:bg-neutral-700 transition-colors">
                        <flux:icon name="x-mark" class="w-5 h-5" />
                    </button>
                </div>

                {{-- Modal Body --}}
                <div class="p-6 space-y-4 max-h-[75vh] overflow-y-auto">
                    @if($selectedEdukasi)
                        {{-- Identitas Ringkas --}}
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 bg-neutral-50 dark:bg-neutral-900/50 p-3.5 rounded-xl border border-neutral-200/80 dark:border-neutral-700 text-xs">
                            <div>
                                <span class="text-neutral-400 block text-[10px] uppercase font-bold">No. Rawat</span>
                                <span class="font-mono font-bold text-[#4C5C2D] dark:text-[#8CC7C4]">{{ $selectedEdukasi->no_rawat }}</span>
                            </div>
                            <div>
                                <span class="text-neutral-400 block text-[10px] uppercase font-bold">Waktu Edukasi</span>
                                <span class="font-medium text-neutral-800 dark:text-neutral-200">{{ $selectedEdukasi->tanggal ? $selectedEdukasi->tanggal->format('d/m/Y H:i') . ' WIB' : '-' }}</span>
                            </div>
                            <div class="col-span-2">
                                <span class="text-neutral-400 block text-[10px] uppercase font-bold">Petugas / Edukator</span>
                                <span class="font-medium text-neutral-800 dark:text-neutral-200">{{ $selectedEdukasi->pegawai->nama ?? ($selectedEdukasi->petugas->nama ?? '-') }} ({{ $selectedEdukasi->nik }})</span>
                            </div>
                        </div>

                        {{-- Materi Edukasi Card --}}
                        <div class="space-y-1.5">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-neutral-400">Materi Edukasi yang Diberikan</span>
                            <div class="p-4 rounded-xl bg-neutral-50/80 dark:bg-neutral-900/40 border border-neutral-200 dark:border-neutral-700 text-sm text-neutral-800 dark:text-neutral-100 whitespace-pre-line leading-relaxed">
                                {{ $selectedEdukasi->materi_edukasi }}
                            </div>
                            @if($selectedEdukasi->keterangan)
                                <div class="text-xs text-neutral-500 italic pl-1">
                                    Catatan Tambahan: {{ $selectedEdukasi->keterangan }}
                                </div>
                            @endif
                        </div>

                        {{-- Detail Parameter Grid --}}
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 bg-neutral-50/50 dark:bg-neutral-900/30 p-3.5 rounded-xl border border-neutral-200/70 dark:border-neutral-700 text-xs">
                            <div>
                                <span class="text-neutral-400 block text-[10px]">Diberikan Kepada</span>
                                <span class="font-bold text-neutral-800 dark:text-neutral-100">{{ $selectedEdukasi->diberikan_pada ?: '-' }}</span>
                                @if($selectedEdukasi->keterangan_diberikan_pada)
                                    <span class="text-[11px] text-neutral-400 block">({{ $selectedEdukasi->keterangan_diberikan_pada }})</span>
                                @endif
                            </div>
                            <div>
                                <span class="text-neutral-400 block text-[10px]">Lama Edukasi</span>
                                <span class="font-bold text-neutral-800 dark:text-neutral-100">{{ $selectedEdukasi->lama_edukasi ? $selectedEdukasi->lama_edukasi . ' Menit' : '-' }}</span>
                            </div>
                            <div>
                                <span class="text-neutral-400 block text-[10px]">Metode Edukasi</span>
                                <span class="font-bold text-neutral-800 dark:text-neutral-100">{{ $selectedEdukasi->metode_edukasi ?: '-' }}</span>
                            </div>
                            <div>
                                <span class="text-neutral-400 block text-[10px]">Tahap Status</span>
                                <span class="font-bold text-neutral-800 dark:text-neutral-100">{{ $selectedEdukasi->status ?: 'Awal' }}</span>
                            </div>
                        </div>

                        {{-- Hasil Verifikasi --}}
                        <div class="p-3.5 rounded-xl bg-[#F1F5E9] dark:bg-[#4C5C2D]/20 border border-[#4C5C2D]/30 flex items-center justify-between">
                            <div class="flex items-center gap-2.5">
                                <flux:icon name="check-badge" class="w-5 h-5 text-[#4C5C2D] dark:text-[#8CC7C4]" />
                                <div>
                                    <div class="text-xs font-bold text-neutral-800 dark:text-neutral-100">Hasil Verifikasi Pemahaman</div>
                                    <div class="text-[11px] text-neutral-500 dark:text-neutral-400">Tingkat penerimaan materi oleh pasien/keluarga</div>
                                </div>
                            </div>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-white dark:bg-neutral-800 text-[#4C5C2D] dark:text-[#8CC7C4] shadow-sm border border-[#4C5C2D]/20">
                                {{ $selectedEdukasi->hasil_verifikasi }}
                            </span>
                        </div>
                    @else
                        <div class="text-center py-8 text-neutral-400">Tidak ada data edukasi yang dipilih.</div>
                    @endif
                </div>

                {{-- Modal Footer --}}
                <div class="px-6 py-3 border-t border-neutral-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-900/70 flex justify-end">
                    <flux:button type="button" @click="detailModalOpen = false" variant="filled" size="sm" class="!bg-neutral-700 !text-white hover:!bg-neutral-800">
                        Tutup
                    </flux:button>
                </div>
            </div>
        </div>
    </div>

    {{-- SLIDE-IN RIGHT PANEL (TAMBAH / EDIT EDUKASI) --}}
    <div
        x-data="{ open: @entangle('isPanelOpen') }"
        x-show="open"
        x-cloak
        class="fixed inset-0 z-50 overflow-hidden"
        style="display: none;">

        {{-- Backdrop --}}
        <div
            x-show="open"
            x-transition:enter="transition-opacity ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            wire:click="closePanel"
            class="fixed inset-0 bg-neutral-900/50 backdrop-blur-sm transition-opacity"></div>

        <div class="fixed inset-y-0 right-0 max-w-full flex pl-10">
            <div
                x-show="open"
                x-transition:enter="transform transition ease-out duration-300"
                x-transition:enter-start="translate-x-full"
                x-transition:enter-end="translate-x-0"
                x-transition:leave="transform transition ease-in duration-200"
                x-transition:leave-start="translate-x-0"
                x-transition:leave-end="translate-x-full"
                class="w-screen max-w-2xl bg-white dark:bg-neutral-800 shadow-2xl border-l border-neutral-200 dark:border-neutral-700 flex flex-col">

                {{-- Panel Header --}}
                <div class="px-6 py-4 border-b border-neutral-200 dark:border-neutral-700 flex items-center justify-between bg-neutral-50 dark:bg-neutral-900/50">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-[#4C5C2D]/10 dark:bg-[#4C5C2D]/30 flex items-center justify-center text-[#4C5C2D] dark:text-[#8CC7C4]">
                            <flux:icon name="academic-cap" class="w-5 h-5" />
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-neutral-800 dark:text-neutral-100">
                                {{ $isEditMode ? 'Edit Pelaksanaan Informasi & Edukasi' : 'Tambah Pelaksanaan Informasi & Edukasi' }}
                            </h3>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">
                                {{ $isEditMode ? 'Perbarui data pencatatan edukasi pasien' : 'Formulir input edukasi terintegrasi pasien & keluarga' }}
                            </p>
                        </div>
                    </div>
                    <button
                        type="button"
                        wire:click="closePanel"
                        class="text-neutral-400 hover:text-neutral-600 dark:hover:text-neutral-200 p-1.5 rounded-lg hover:bg-neutral-100 dark:hover:bg-neutral-700 transition-colors">
                        <flux:icon name="x-mark" class="w-5 h-5" />
                    </button>
                </div>

                {{-- Panel Content / Form --}}
                <div class="flex-1 overflow-y-auto p-6 space-y-6">

                    {{-- Patient Summary Banner --}}
                    <div class="grid grid-cols-3 gap-3 bg-neutral-50 dark:bg-neutral-900/40 p-3.5 rounded-xl border border-neutral-200 dark:border-neutral-700 text-xs">
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
                            Waktu & Petugas Edukator
                        </h4>

                        <div class="grid grid-cols-1 sm:grid-cols-12 gap-4">
                            <div class="sm:col-span-4">
                                <flux:input wire:model="tanggal_date" type="date" label="Tanggal *" required />
                                @error('tanggal_date') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
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
                                @error('tanggal_time') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div class="sm:col-span-5 relative">
                                <flux:label>Petugas (NIP/Nama) *</flux:label>

                                @if($nik && $nmPetugas)
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
                                                    {{ $nik }}
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
                                @error('nik') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Section 2: Materi & Keterangan Edukasi --}}
                    <div class="bg-neutral-50/50 dark:bg-neutral-900/30 p-4 rounded-xl border border-neutral-200/80 dark:border-neutral-700 space-y-4">
                        <div class="flex items-center justify-between">
                            <h4 class="text-xs font-bold text-neutral-700 dark:text-neutral-300 uppercase tracking-wider flex items-center gap-2">
                                <flux:icon name="document-text" class="w-4 h-4 text-[#4C5C2D] dark:text-[#8CC7C4]" />
                                Materi & Keterangan Edukasi
                            </h4>
                            <flux:button
                                type="button"
                                wire:click="$set('showTemplateModal', true)"
                                icon="paper-clip"
                                size="xs"
                                variant="subtle"
                                class="!text-[#4C5C2D] dark:!text-[#8CC7C4] hover:!bg-[#4C5C2D]/10">
                                Pilih Template
                            </flux:button>
                        </div>

                        <div>
                            <flux:label>Materi Edukasi *</flux:label>
                            <flux:textarea
                                wire:model="materi_edukasi"
                                rows="3"
                                placeholder="Tuliskan materi informasi dan edukasi yang disampaikan kepada pasien/keluarga..."
                                required />
                            @error('materi_edukasi') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-12 gap-4">
                            <div class="sm:col-span-5">
                                <flux:input
                                    wire:model="keterangan"
                                    label="Keterangan (Maks. 50 Karakter)"
                                    placeholder="Contoh: Terlampir leaflet / brosur"
                                    maxlength="50" />
                                @error('keterangan') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="sm:col-span-4">
                                <flux:input
                                    wire:model="lama_edukasi"
                                    type="text"
                                    label="Lama Edukasi (Menit)"
                                    placeholder="Contoh: 15" />
                                @error('lama_edukasi') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="sm:col-span-3">
                                <flux:select wire:model="status" label="Status Edukasi *">
                                    <option value="Awal">Awal</option>
                                    <option value="Ulang">Ulang</option>
                                </flux:select>
                                @error('status') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Section 3: Sasaran, Metode & Hasil --}}
                    <div class="bg-neutral-50/50 dark:bg-neutral-900/30 p-4 rounded-xl border border-neutral-200/80 dark:border-neutral-700 space-y-4">
                        <h4 class="text-xs font-bold text-neutral-700 dark:text-neutral-300 uppercase tracking-wider flex items-center gap-2">
                            <flux:icon name="user-group" class="w-4 h-4 text-[#4C5C2D] dark:text-[#8CC7C4]" />
                            Sasaran, Metode & Hasil Edukasi
                        </h4>

                        <div class="grid grid-cols-1 sm:grid-cols-12 gap-4">
                            <div class="sm:col-span-4">
                                <flux:select wire:model.live="diberikan_pada" label="Diberikan Kepada *">
                                    <option value="Pasien">Pasien</option>
                                    <option value="Keluarga">Keluarga</option>
                                    <option value="Lain-lain">Lain-lain</option>
                                </flux:select>
                                @error('diberikan_pada') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="sm:col-span-8">
                                <flux:input
                                    wire:model="keterangan_diberikan_pada"
                                    label="Keterangan Edukasi Diberikan"
                                    placeholder="Contoh: Istri Pasien / Anak Kandung / Orang Tua"
                                    maxlength="40" />
                                @error('keterangan_diberikan_pada') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="sm:col-span-6">
                                <flux:select wire:model="metode_edukasi" label="Metode Edukasi *">
                                    <option value="Ceramah">Ceramah</option>
                                    <option value="Diskusi">Diskusi</option>
                                    <option value="Demonstrasi">Demonstrasi</option>
                                </flux:select>
                                @error('metode_edukasi') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="sm:col-span-6">
                                <flux:select wire:model="hasil_verifikasi" label="Hasil Verifikasi *">
                                    <option value="Sudah Mengerti">Sudah Mengerti</option>
                                    <option value="Re-Edukasi">Re-Edukasi</option>
                                    <option value="Re-Demonstrasi">Re-Demonstrasi</option>
                                </flux:select>
                                @error('hasil_verifikasi') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                </div>

                {{-- Panel Footer / Submit --}}
                <div class="px-6 py-4 border-t border-neutral-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-900/50 flex items-center justify-end gap-3">
                    <flux:button
                        type="button"
                        wire:click="closePanel"
                        variant="subtle">
                        Batal
                    </flux:button>
                    <flux:button
                        type="button"
                        wire:click="save"
                        variant="filled"
                        class="!bg-[#4C5C2D] !text-white hover:!bg-[#3d4b24]">
                        <span wire:loading.remove wire:target="save">
                            {{ $isEditMode ? 'Simpan Perubahan' : 'Simpan Edukasi' }}
                        </span>
                        <span wire:loading wire:target="save">
                            Menyimpan...
                        </span>
                    </flux:button>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL TEMPLATE SELECTOR (ALPINE.JS MURNI) --}}
    @if($showTemplateModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
            <div class="fixed inset-0 bg-neutral-900/60 backdrop-blur-sm" wire:click="$set('showTemplateModal', false)"></div>

            <div class="flex min-h-full items-center justify-center p-4">
                <div class="relative bg-white dark:bg-neutral-800 rounded-2xl shadow-2xl max-w-xl w-full border border-neutral-200 dark:border-neutral-700 overflow-hidden" @click.stop>
                    <div class="px-5 py-3.5 border-b border-neutral-200 dark:border-neutral-700 flex items-center justify-between bg-neutral-50 dark:bg-neutral-900/70">
                        <div class="flex items-center gap-2.5">
                            <flux:icon name="paper-clip" class="w-4 h-4 text-[#4C5C2D]" />
                            <h3 class="text-sm font-bold text-neutral-800 dark:text-neutral-100">
                                Pilih Template Materi Edukasi
                            </h3>
                        </div>
                        <button type="button" wire:click="$set('showTemplateModal', false)" class="text-neutral-400 hover:text-neutral-600 p-1">
                            <flux:icon name="x-mark" class="w-4 h-4" />
                        </button>
                    </div>

                    <div class="p-4 space-y-3">
                        <flux:input
                            wire:model.live.debounce.250ms="templateSearch"
                            placeholder="Cari materi template..."
                            icon="magnifying-glass"
                            size="sm"
                            autofocus />

                        <div class="max-h-64 overflow-y-auto space-y-2 pr-1">
                            @forelse($this->templates as $tpl)
                                <div
                                    wire:click="applyTemplate('{{ $tpl->no_template }}')"
                                    class="p-3 rounded-xl border border-neutral-200 dark:border-neutral-700 hover:border-[#4C5C2D] hover:bg-[#F1F5E9]/50 dark:hover:bg-[#4C5C2D]/10 cursor-pointer transition-all">
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="font-mono text-[11px] font-bold text-[#4C5C2D]">{{ $tpl->no_template }}</span>
                                        <div class="flex items-center gap-2 text-[10px] text-neutral-400">
                                            @if($tpl->lama_edukasi)
                                                <span>{{ $tpl->lama_edukasi }} Menit</span>
                                            @endif
                                            @if($tpl->metode_edukasi)
                                                <span>• {{ $tpl->metode_edukasi }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <p class="text-xs text-neutral-700 dark:text-neutral-200 line-clamp-2">
                                        {{ $tpl->materi_edukasi }}
                                    </p>
                                </div>
                            @empty
                                <div class="text-center py-6 text-neutral-400 text-xs">
                                    Tidak ada template edukasi yang ditemukan.
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <div class="px-5 py-3 border-t border-neutral-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-900/50 flex justify-end">
                        <flux:button type="button" wire:click="$set('showTemplateModal', false)" size="xs" variant="subtle">
                            Tutup
                        </flux:button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
