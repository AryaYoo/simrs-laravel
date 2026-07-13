<div class="flex flex-col gap-6 pb-8">
    {{-- Header / Breadcrumb --}}
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('modul.rawat-inap.perawatan-tindakan', str_replace('/', '-', $regPeriksa->no_rawat)) }}" wire:navigate
                class="flex items-center justify-center w-10 h-8 rounded-md bg-[#4C5C2D] transition-colors hover:bg-[#3d4b24] shadow-sm">
                <flux:icon name="chevron-left" class="w-5 h-5 text-white" />
            </a>
            <div>
                <nav class="text-xs text-neutral-400 mb-0.5">
                    <a href="{{ route('modul.rawat-inap.perawatan-tindakan', str_replace('/', '-', $regPeriksa->no_rawat)) }}" wire:navigate class="hover:underline">Perawatan & Tindakan</a>
                    <span class="mx-1">/</span>
                    <span>Surat Keterangan Rawat Inap</span>
                </nav>
                <h1 class="text-xl font-bold text-neutral-800 dark:text-neutral-100">Surat Keterangan Rawat Inap</h1>
            </div>
        </div>

        {{-- Action Buttons (Settings + Buat Surat) --}}
        <div class="flex items-center gap-2">
            <a href="{{ route('modul.rawat-inap.sub-rawat-inap.surat-keterangan-rawat-inap.settings', str_replace('/', '-', $regPeriksa->no_rawat)) }}"
                wire:navigate
                class="flex items-center justify-center w-9 h-9 rounded-lg border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 text-neutral-500 dark:text-neutral-400 hover:border-[#4C5C2D] hover:text-[#4C5C2D] transition-all shadow-sm"
                title="Pengaturan Nomor SKRI">
                <flux:icon name="cog-6-tooth" class="w-4.5 h-4.5" />
            </a>
            <flux:button wire:click="openCreateModal" icon="plus" variant="filled" size="sm"
                class="!flex !flex-row !items-center !bg-[#4C5C2D] !text-white hover:!bg-[#3d4a24]">
                Buat Surat
            </flux:button>
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
    <div class="bg-white dark:bg-neutral-800 rounded-xl border border-neutral-200 dark:border-neutral-700 shadow-sm overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-neutral-100 dark:border-neutral-700">
            <h3 class="text-sm font-bold text-neutral-700 dark:text-neutral-300 uppercase tracking-wider flex items-center gap-2">
                <flux:icon name="document-text" class="w-4 h-4 text-[#4C5C2D]" />
                Daftar Surat Keterangan Rawat Inap
            </h3>
            <span class="text-xs text-neutral-400 bg-neutral-100 dark:bg-neutral-900 px-2 py-0.5 rounded-full font-mono">
                {{ $suratList->count() }} surat
            </span>
        </div>

        @if($suratList->isEmpty())
            <div class="flex flex-col items-center justify-center py-16 text-neutral-400 dark:text-neutral-500">
                <flux:icon name="document-text" class="w-12 h-12 mb-3 opacity-30" />
                <p class="text-sm font-medium">Belum ada surat yang dibuat</p>
                <p class="text-xs mt-1 opacity-70">Klik tombol "Buat Surat" di pojok kanan atas untuk membuat surat baru</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-neutral-100 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800/50">
                            <th class="text-left px-5 py-3 text-xs font-bold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider whitespace-nowrap">No. Surat</th>
                            <th class="text-left px-4 py-3 text-xs font-bold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider whitespace-nowrap">No. Rawat</th>
                            <th class="text-left px-4 py-3 text-xs font-bold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider whitespace-nowrap">No. R.M.</th>
                            <th class="text-left px-4 py-3 text-xs font-bold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider">Nama Pasien</th>
                            <th class="text-left px-4 py-3 text-xs font-bold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider whitespace-nowrap">Dari Tanggal</th>
                            <th class="text-left px-4 py-3 text-xs font-bold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider whitespace-nowrap">Sampai Tanggal</th>
                            <th class="text-center px-4 py-3 text-xs font-bold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider whitespace-nowrap">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-100 dark:divide-neutral-700">
                        @foreach($suratList as $surat)
                            <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-800/50 transition-colors group">
                                <td class="px-5 py-3 whitespace-nowrap">
                                    <span class="font-mono text-xs font-semibold text-neutral-700 dark:text-neutral-200 bg-neutral-100 dark:bg-neutral-900 px-2 py-0.5 rounded">
                                        {{ $surat->no_surat }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span class="font-mono text-xs text-neutral-600 dark:text-neutral-300">{{ $surat->no_rawat }}</span>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span class="font-mono text-xs font-semibold text-[#4C5C2D] dark:text-[#8CC7C4]">{{ $regPeriksa->no_rkm_medis }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="font-semibold text-neutral-800 dark:text-neutral-200">{{ $regPeriksa->pasien->nm_pasien ?? '-' }}</span>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span class="text-red-600 dark:text-red-400 font-medium">{{ $surat->tanggalawal }}</span>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span class="text-red-600 dark:text-red-400 font-medium">{{ $surat->tanggalakhir }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-center gap-2">
                                        {{-- Cetak --}}
                                        <a href="{{ route('modul.rawat-inap.skri.cetak', ['no_rawat' => str_replace('/', '-', $surat->no_rawat), 'no_surat' => $surat->no_surat]) }}"
                                            target="_blank"
                                            class="flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg text-xs font-semibold bg-[#4C5C2D]/10 text-[#4C5C2D] dark:bg-[#4C5C2D]/20 dark:text-[#8CC7C4] hover:bg-[#4C5C2D] hover:text-white transition-all"
                                            title="Cetak Surat">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5zm-3 0h.008v.008H15V10.5z" />
                                            </svg>
                                            Cetak
                                        </a>
                                        {{-- Hapus --}}
                                        <button type="button"
                                            data-no-surat="{{ $surat->no_surat }}"
                                            x-on:click="Swal.fire({title:'Hapus Surat?',text:'Surat ini akan dihapus permanen.',icon:'warning',showCancelButton:true,confirmButtonColor:'#dc2626',cancelButtonColor:'#6b7280',confirmButtonText:'Ya, Hapus!',cancelButtonText:'Batal'}).then(function(r){if(r.isConfirmed){$wire.delete($el.dataset.noSurat)}})"
                                            class="flex items-center justify-center w-7 h-7 rounded-lg text-neutral-400 hover:bg-red-50 hover:text-red-500 dark:hover:bg-red-900/20 transition-all"
                                            title="Hapus">
                                            <flux:icon name="trash" class="w-3.5 h-3.5" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- ===== ALPINE.JS CREATE MODAL ===== --}}
    <div x-data="{ show: @entangle('showCreateModal') }">
        <template x-teleport="body">
            <div x-show="show"
                x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                class="fixed inset-0 z-[9999] flex items-center justify-center p-4"
                style="display:none;"
                @keydown.escape.window="$wire.closeCreateModal()">

                {{-- Backdrop --}}
                <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="$wire.closeCreateModal()"></div>

                {{-- Panel --}}
                <div x-show="show"
                    x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                    x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                    class="relative w-full max-w-md bg-white dark:bg-neutral-900 rounded-2xl shadow-2xl border border-neutral-200 dark:border-neutral-700 overflow-hidden"
                    @click.stop>

                    {{-- Modal Header --}}
                    <div class="flex items-center gap-3 px-6 py-4 bg-[#4C5C2D]">
                        <div class="w-9 h-9 rounded-lg bg-white/20 flex items-center justify-center flex-shrink-0">
                            <flux:icon name="document-plus" class="w-5 h-5 text-white" />
                        </div>
                        <div>
                            <h3 class="font-bold text-white text-base">Buat Surat Keterangan Rawat Inap</h3>
                            <p class="text-white/70 text-xs mt-0.5">Nomor surat akan digenerate otomatis</p>
                        </div>
                        <button @click="$wire.closeCreateModal()" class="ml-auto p-2 rounded-lg hover:bg-white/10 text-white/70 hover:text-white transition-colors">
                            <flux:icon name="x-mark" class="w-4 h-4" />
                        </button>
                    </div>

                    {{-- Modal Body --}}
                    <form wire:submit.prevent="store" class="p-6 space-y-4">
                        {{-- Pasien Info --}}
                        <div class="p-3 bg-neutral-50 dark:bg-neutral-800 rounded-lg border border-neutral-200 dark:border-neutral-700 text-sm">
                            <div class="font-semibold text-neutral-800 dark:text-neutral-100 uppercase">{{ $regPeriksa->pasien->nm_pasien ?? '-' }}</div>
                            <div class="text-neutral-500 dark:text-neutral-400 mt-0.5 text-xs font-mono">{{ $regPeriksa->no_rawat }}</div>
                            <div class="mt-2 pt-2 border-t border-neutral-200 dark:border-neutral-700">
                                <span class="text-xs text-neutral-500 dark:text-neutral-400 block mb-0.5">Pratinjau Nomor Surat Berikutnya:</span>
                                <span class="text-sm font-bold text-[#4C5C2D] dark:text-[#8CC7C4] font-mono">{{ $previewNoSurat }}</span>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-neutral-600 dark:text-neutral-400 mb-1">Dari Tanggal <span class="text-red-500">*</span></label>
                                <input type="date" wire:model="tanggal_awal"
                                    class="w-full rounded-lg border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 px-3 py-2 text-sm text-neutral-800 dark:text-neutral-100 focus:ring-2 focus:ring-[#4C5C2D]/30 focus:border-[#4C5C2D] outline-none transition-all" />
                                @error('tanggal_awal') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-neutral-600 dark:text-neutral-400 mb-1">Sampai Tanggal <span class="text-red-500">*</span></label>
                                <input type="date" wire:model="tanggal_akhir"
                                    class="w-full rounded-lg border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 px-3 py-2 text-sm text-neutral-800 dark:text-neutral-100 focus:ring-2 focus:ring-[#4C5C2D]/30 focus:border-[#4C5C2D] outline-none transition-all" />
                                @error('tanggal_akhir') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="flex items-center justify-end gap-2 pt-2 border-t border-neutral-100 dark:border-neutral-700">
                            <button type="button" wire:click="closeCreateModal"
                                class="px-4 py-2 text-sm font-medium text-neutral-600 dark:text-neutral-400 hover:bg-neutral-100 dark:hover:bg-neutral-800 rounded-lg transition-colors">
                                Batal
                            </button>
                            <button type="submit"
                                class="flex items-center gap-2 px-5 py-2 bg-[#4C5C2D] text-white text-sm font-semibold rounded-lg hover:bg-[#3d4b24] transition-colors">
                                <flux:icon name="document-check" class="w-4 h-4" />
                                Buat Surat
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </template>
    </div>
</div>
