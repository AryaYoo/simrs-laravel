<div class="p-6 pb-24" x-data="{
    showModal: false,
    init() {
        Livewire.on('open-modal', () => { this.showModal = true; });
        Livewire.on('close-modal', () => { this.showModal = false; });
        setInterval(() => { if ($wire.isAutoTimestamp) { $wire.updateTime(); } }, 1000);
    }
}">
    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-neutral-800 dark:text-white">Hasil USG Gynecologi</h1>
            <p class="text-sm text-neutral-500 mt-1">Pencatatan hasil pemeriksaan USG Gynecologi pasien rawat inap.</p>
        </div>
        <div class="flex gap-2">
            <flux:button href="{{ route('modul.rawat-inap.perawatan-tindakan', str_replace('/', '-', $no_rawat)) }}"
                wire:navigate variant="ghost" icon="arrow-left">Kembali</flux:button>
            @if(count($dataUsg) == 0)
            <flux:button variant="primary" icon="plus" @click="$wire.resetForm(); showModal = true"
                class="!bg-[#4C5C2D] hover:!bg-[#3f4d25] !text-white !border-none">Buat Baru</flux:button>
            @endif
        </div>
    </div>

    {{-- Info Banner --}}
    @if(count($dataUsg) > 0)
    <div class="mb-6 bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-500 p-4 rounded-r-lg shadow-sm">
        <div class="flex items-start gap-3">
            <flux:icon name="information-circle" class="h-5 w-5 text-blue-400 flex-shrink-0 mt-0.5" />
            <p class="text-sm text-blue-700 dark:text-blue-400">Hanya 1 (satu) data Hasil USG Gynecologi yang diperbolehkan per pendaftaran pasien. Gunakan tombol <strong>Edit</strong> untuk melakukan perubahan.</p>
        </div>
    </div>
    @endif

    {{-- List Data --}}
    <div class="bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-200 dark:border-neutral-800 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-xs text-neutral-500 bg-neutral-50 dark:bg-neutral-800 uppercase border-b border-neutral-200 dark:border-neutral-700">
                    <tr>
                        <th class="px-6 py-4 font-bold">Tanggal & Jam</th>
                        <th class="px-6 py-4 font-bold">Dokter DPJP</th>
                        <th class="px-6 py-4 font-bold">Diagnosa Klinis</th>
                        <th class="px-6 py-4 font-bold">Kesimpulan</th>
                        <th class="px-6 py-4 font-bold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100 dark:divide-neutral-800">
                    @forelse ($dataUsg as $item)
                        <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-800/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-medium text-neutral-900 dark:text-white">{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</div>
                                <div class="text-neutral-500">{{ \Carbon\Carbon::parse($item->tanggal)->format('H:i:s') }}</div>
                            </td>
                            <td class="px-6 py-4 font-medium text-neutral-900 dark:text-white">{{ $item->dokter->nm_dokter ?? '-' }}</td>
                            <td class="px-6 py-4 text-neutral-600 dark:text-neutral-400">{{ $item->diagnosa_klinis ?: '-' }}</td>
                            <td class="px-6 py-4 text-neutral-600 dark:text-neutral-400 max-w-xs truncate">{{ $item->kesimpulan ?: '-' }}</td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <flux:button variant="ghost" size="sm" icon="pencil-square" wire:click="edit"
                                        class="text-[#4C5C2D] hover:bg-[#4C5C2D]/10" />
                                    <div x-data="{ showConfirm: false }" class="relative">
                                        <flux:button variant="ghost" size="sm" icon="trash" @click="showConfirm = true"
                                            class="text-red-500 hover:bg-red-50" />
                                        <div x-show="showConfirm" x-cloak @click.away="showConfirm = false"
                                            class="absolute right-0 bottom-full mb-2 w-64 bg-white dark:bg-neutral-800 rounded-lg shadow-xl border border-neutral-200 dark:border-neutral-700 p-4 z-50">
                                            <p class="text-sm text-neutral-600 dark:text-neutral-300 mb-3">Apakah Anda yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan.</p>
                                            <div class="flex gap-2 justify-end">
                                                <flux:button size="sm" variant="ghost" @click="showConfirm = false">Batal</flux:button>
                                                <flux:button size="sm" variant="danger" wire:click="delete" @click="showConfirm = false">Hapus</flux:button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-neutral-500">
                                <div class="flex flex-col items-center justify-center">
                                    <flux:icon name="document-text" class="h-12 w-12 text-neutral-300 mb-3" />
                                    <p class="font-medium">Belum ada data Hasil USG Gynecologi</p>
                                    <p class="text-xs mt-1">Klik "Buat Baru" untuk menambahkan data.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- RIGHT SLIDE-IN PANEL --}}
    <div x-show="showModal" x-cloak class="relative z-[99]" role="dialog" aria-modal="true">
        {{-- Backdrop --}}
        <div x-show="showModal"
             x-transition:enter="ease-in-out duration-500" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in-out duration-500" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="showModal = false"></div>

        <div class="fixed inset-0 overflow-hidden">
            <div class="absolute inset-0 overflow-hidden">
                <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10">
                    <div x-show="showModal"
                         x-transition:enter="transform transition ease-in-out duration-500 sm:duration-700"
                         x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
                         x-transition:leave="transform transition ease-in-out duration-500 sm:duration-700"
                         x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full"
                         class="pointer-events-auto relative w-screen max-w-2xl">

                        {{-- Close button --}}
                        <div x-show="showModal"
                             x-transition:enter="ease-in-out duration-500" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                             x-transition:leave="ease-in-out duration-500" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                             class="absolute left-0 top-0 -ml-8 flex pr-2 pt-4 sm:-ml-10 sm:pr-4">
                            <button type="button" @click="showModal = false" class="relative rounded-md text-gray-300 hover:text-white">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <div class="flex h-full flex-col bg-white dark:bg-neutral-900 shadow-2xl">
                            {{-- Header --}}
                            <div class="px-6 py-5 border-b border-neutral-200 dark:border-neutral-800 bg-[#F1F5E9] dark:bg-neutral-800">
                                <h2 class="text-xl font-bold text-[#4C5C2D] dark:text-[#8CC7C4]">
                                    {{ $isEdit ? 'Ubah Hasil USG Gynecologi' : 'Buat Hasil USG Gynecologi' }}
                                </h2>
                                <p class="text-xs text-neutral-500 mt-1">Formulir pemeriksaan USG Gynecologi</p>
                            </div>

                            {{-- Form Body --}}
                            <div class="relative flex-1 px-6 py-6 overflow-y-auto">
                                @if ($errors->any())
                                <div class="mb-6 p-4 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800">
                                    <div class="flex items-center gap-3 mb-2">
                                        <flux:icon name="exclamation-circle" class="w-5 h-5 text-red-500" />
                                        <h3 class="font-bold text-red-800 dark:text-red-400 text-sm">Terdapat kesalahan pengisian:</h3>
                                    </div>
                                    <ul class="list-disc list-inside text-xs text-red-700 dark:text-red-400 space-y-1 ml-8">
                                        @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                                    </ul>
                                </div>
                                @endif

                                <div class="space-y-7">
                                    {{-- Section: Waktu & Dokter --}}
                                    <div>
                                        <h3 class="text-xs font-bold text-neutral-800 dark:text-neutral-200 uppercase tracking-widest mb-4 pb-2 border-b border-neutral-200 dark:border-neutral-800 flex items-center gap-2">
                                            <flux:icon name="clock" class="w-4 h-4 text-neutral-400" /> Waktu & Dokter DPJP
                                        </h3>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                            <div class="bg-neutral-50 dark:bg-neutral-800/50 p-4 rounded-xl border border-neutral-200 dark:border-neutral-800 space-y-3">
                                                <div class="flex items-center justify-between">
                                                    <span class="text-sm font-medium text-neutral-700 dark:text-neutral-300">Waktu Pemeriksaan</span>
                                                    <flux:switch wire:model.live="isAutoTimestamp" label="Auto" />
                                                </div>
                                                <div class="grid grid-cols-2 gap-3">
                                                    <flux:input type="date" wire:model="form.tanggal" :disabled="$isAutoTimestamp" />
                                                    <flux:input type="time" wire:model="form.jam" step="1" :disabled="$isAutoTimestamp" />
                                                </div>
                                            </div>
                                            <div class="space-y-2">
                                                <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">Dokter DPJP <span class="text-red-500">*</span></label>
                                                <div class="flex gap-2">
                                                    <flux:input wire:model="form.kd_dokter" readonly placeholder="Kode" class="w-1/3 bg-neutral-50" />
                                                    <flux:input wire:model="form.nm_dokter" readonly placeholder="Nama Dokter" class="flex-1 bg-neutral-50" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Section: Informasi Klinis --}}
                                    <div>
                                        <h3 class="text-xs font-bold text-neutral-800 dark:text-neutral-200 uppercase tracking-widest mb-4 pb-2 border-b border-neutral-200 dark:border-neutral-800 flex items-center gap-2">
                                            <flux:icon name="clipboard-document-list" class="w-4 h-4 text-neutral-400" /> Informasi Klinis
                                        </h3>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                            <flux:input label="Kiriman Dari" wire:model="form.kiriman_dari" placeholder="Instansi perujuk" />
                                            <flux:input label="Diagnosa Klinis" wire:model="form.diagnosa_klinis" placeholder="Diagnosa klinis" />
                                        </div>
                                    </div>

                                    {{-- Section: Hasil Pemeriksaan --}}
                                    <div>
                                        <h3 class="text-xs font-bold text-neutral-800 dark:text-neutral-200 uppercase tracking-widest mb-4 pb-2 border-b border-neutral-200 dark:border-neutral-800 flex items-center gap-2">
                                            <flux:icon name="beaker" class="w-4 h-4 text-neutral-400" /> Hasil Pemeriksaan
                                        </h3>
                                        <div class="space-y-4">
                                            <flux:textarea label="Uterus" wire:model="form.uterus" rows="3" placeholder="Ketikkan hasil pemeriksaan uterus..." />
                                            <flux:textarea label="Parametrium" wire:model="form.parametrium" rows="3" placeholder="Ketikkan hasil pemeriksaan parametrium..." />
                                            <flux:textarea label="Ovarium" wire:model="form.ovarium" rows="3" placeholder="Ketikkan hasil pemeriksaan ovarium..." />
                                            <flux:textarea label="Doppler" wire:model="form.doppler" rows="3" placeholder="Ketikkan hasil pemeriksaan doppler..." />
                                        </div>
                                    </div>

                                    {{-- Section: Kesimpulan --}}
                                    <div>
                                        <h3 class="text-xs font-bold text-neutral-800 dark:text-neutral-200 uppercase tracking-widest mb-4 pb-2 border-b border-neutral-200 dark:border-neutral-800 flex items-center gap-2">
                                            <flux:icon name="document-check" class="w-4 h-4 text-neutral-400" /> Kesimpulan
                                        </h3>
                                        <flux:textarea label="Kesimpulan" wire:model="form.kesimpulan" rows="4" placeholder="Ketikkan kesimpulan hasil USG Gynecologi..." />
                                    </div>
                                </div>
                            </div>

                            {{-- Footer --}}
                            <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-neutral-200 dark:border-neutral-800 bg-neutral-50 dark:bg-neutral-800/50">
                                <flux:button variant="ghost" @click="showModal = false">Batal</flux:button>
                                <flux:button variant="primary" wire:click="save" icon="check-circle"
                                    class="!bg-[#4C5C2D] hover:!bg-[#3f4d25] !text-white !border-none">
                                    Simpan Hasil USG
                                </flux:button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
