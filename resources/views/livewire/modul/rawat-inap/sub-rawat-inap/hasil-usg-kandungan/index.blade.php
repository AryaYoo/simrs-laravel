<div class="p-6 pb-24" x-data="{
    showModal: false,
    init() {
        Livewire.on('open-modal', () => {
            this.showModal = true;
        });
        Livewire.on('close-modal', () => {
            this.showModal = false;
        });
        
        setInterval(() => {
            if ($wire.isAutoTimestamp) {
                $wire.updateTime();
            }
        }, 1000);
    }
}">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-neutral-800 dark:text-white">Hasil USG Kandungan</h1>
            <p class="text-sm text-neutral-500 mt-1">Pencatatan hasil pemeriksaan USG khusus untuk kandungan.</p>
        </div>
        <div class="flex gap-2">
            <flux:button href="{{ route('modul.rawat-inap.perawatan-tindakan', str_replace('/', '-', $no_rawat)) }}"
                wire:navigate variant="ghost" icon="arrow-left" size="sm">Kembali</flux:button>
            @if(count($dataUsg) == 0)
            <flux:button variant="primary" icon="plus" @click="$wire.resetForm(); showModal = true"
                class="!bg-[#4C5C2D] hover:!bg-[#3f4d25] !text-white !border-none">Buat Baru</flux:button>
            @endif
        </div>
    </div>

    {{-- Alert Info / Warning --}}
    @if(count($dataUsg) > 0)
    <div class="mb-6 bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-500 p-4 rounded-r-lg shadow-sm">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <flux:icon name="information-circle" class="h-5 w-5 text-blue-400" />
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-bold text-blue-800 dark:text-blue-300">Informasi</h3>
                <p class="text-sm text-blue-700 dark:text-blue-400 mt-1">Hanya 1 (satu) data Hasil USG Kandungan yang diperbolehkan per pendaftaran pasien. Silakan gunakan tombol Edit untuk melakukan perubahan.</p>
            </div>
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
                            <td class="px-6 py-4">
                                <div class="font-medium text-neutral-900 dark:text-white">{{ $item->dokter->nm_dokter ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 text-neutral-600 dark:text-neutral-400">
                                {{ $item->diagnosa_klinis ?: '-' }}
                            </td>
                            <td class="px-6 py-4 text-neutral-600 dark:text-neutral-400 max-w-xs truncate">
                                {{ $item->kesimpulan ?: '-' }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <flux:button variant="ghost" size="sm" icon="pencil-square"
                                        wire:click="edit"
                                        class="text-[#4C5C2D] hover:bg-[#4C5C2D]/10" />

                                    <div x-data="{ showConfirm: false }" class="relative">
                                        <flux:button variant="ghost" size="sm" icon="trash" @click="showConfirm = true"
                                            class="text-red-500 hover:bg-red-50" />

                                        <div x-show="showConfirm" x-cloak @click.away="showConfirm = false"
                                            class="absolute right-0 bottom-full mb-2 w-64 bg-white dark:bg-neutral-800 rounded-lg shadow-xl border border-neutral-200 dark:border-neutral-700 p-4 z-50">
                                            <p class="text-sm text-neutral-600 dark:text-neutral-300 mb-3 text-left whitespace-normal">
                                                Apakah Anda yakin ingin menghapus data USG ini? Tindakan ini tidak dapat dibatalkan.
                                            </p>
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
                                    <p class="font-medium">Belum ada data Hasil USG Kandungan</p>
                                    <p class="text-xs mt-1">Klik "Buat Baru" untuk menambahkan data.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- RIGHT SLIDE-IN PANEL MODAL (Alpine.js) --}}
    <div x-show="showModal" x-cloak class="relative z-[99]" aria-labelledby="slide-over-title" role="dialog" aria-modal="true">
        {{-- Background backdrop --}}
        <div x-show="showModal"
             x-transition:enter="ease-in-out duration-500"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in-out duration-500"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity"
             @click="showModal = false"></div>

        <div class="fixed inset-0 overflow-hidden">
            <div class="absolute inset-0 overflow-hidden">
                <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10">
                    {{-- Slide-over panel --}}
                    <div x-show="showModal"
                         x-transition:enter="transform transition ease-in-out duration-500 sm:duration-700"
                         x-transition:enter-start="translate-x-full"
                         x-transition:enter-end="translate-x-0"
                         x-transition:leave="transform transition ease-in-out duration-500 sm:duration-700"
                         x-transition:leave-start="translate-x-0"
                         x-transition:leave-end="translate-x-full"
                         class="pointer-events-auto relative w-screen max-w-4xl">
                        
                        {{-- Close button --}}
                        <div x-show="showModal"
                             x-transition:enter="ease-in-out duration-500"
                             x-transition:enter-start="opacity-0"
                             x-transition:enter-end="opacity-100"
                             x-transition:leave="ease-in-out duration-500"
                             x-transition:leave-start="opacity-100"
                             x-transition:leave-end="opacity-0"
                             class="absolute left-0 top-0 -ml-8 flex pr-2 pt-4 sm:-ml-10 sm:pr-4">
                            <button type="button" @click="showModal = false" class="relative rounded-md text-gray-300 hover:text-white focus:outline-none focus:ring-2 focus:ring-white">
                                <span class="absolute -inset-2.5"></span>
                                <span class="sr-only">Tutup panel</span>
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        {{-- Panel Content --}}
                        <div class="flex h-full flex-col bg-white dark:bg-neutral-900 shadow-2xl">
                            {{-- Header --}}
                            <div class="px-6 py-5 border-b border-neutral-200 dark:border-neutral-800 bg-[#F1F5E9] dark:bg-neutral-800">
                                <h2 class="text-xl font-bold text-[#4C5C2D] dark:text-[#8CC7C4]" id="slide-over-title">
                                    {{ $isEdit ? 'Ubah Hasil USG Kandungan' : 'Buat Hasil USG Kandungan' }}
                                </h2>
                                <p class="text-xs text-neutral-500 mt-1">Formulir pemeriksaan USG khusus kandungan</p>
                            </div>

                            {{-- Form Body --}}
                            <div class="relative flex-1 px-6 py-6 overflow-y-auto">
                                {{-- Alert Error Validation --}}
                                @if ($errors->any())
                                <div class="mb-6 p-4 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800">
                                    <div class="flex items-center gap-3 mb-2">
                                        <flux:icon name="exclamation-circle" class="w-5 h-5 text-red-500" />
                                        <h3 class="font-bold text-red-800 dark:text-red-400 text-sm">Terdapat kesalahan pengisian:</h3>
                                    </div>
                                    <ul class="list-disc list-inside text-xs text-red-700 dark:text-red-400 space-y-1 ml-8">
                                        @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                                @endif

                                <div class="space-y-8">
                                    {{-- Section: Waktu & Petugas --}}
                                    <div>
                                        <h3 class="text-sm font-bold text-neutral-800 dark:text-neutral-200 uppercase tracking-widest mb-4 pb-2 border-b border-neutral-200 dark:border-neutral-800 flex items-center gap-2">
                                            <flux:icon name="clock" class="w-4 h-4 text-neutral-400" /> Waktu & Dokter DPJP
                                        </h3>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                            <div class="space-y-4 bg-neutral-50 dark:bg-neutral-800/50 p-4 rounded-xl border border-neutral-200 dark:border-neutral-800">
                                                <div class="flex items-center justify-between">
                                                    <span class="text-sm font-medium text-neutral-700 dark:text-neutral-300">Waktu Pemeriksaan</span>
                                                    <flux:switch wire:model.live="isAutoTimestamp" label="Auto (Realtime)" />
                                                </div>
                                                <div class="grid grid-cols-2 gap-3">
                                                    <flux:input type="date" wire:model="form.tanggal" :disabled="$isAutoTimestamp" />
                                                    <flux:input type="time" wire:model="form.jam" step="1" :disabled="$isAutoTimestamp" />
                                                </div>
                                            </div>

                                            <div class="space-y-3">
                                                <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">Dokter DPJP <span class="text-red-500">*</span></label>
                                                <div class="flex gap-2">
                                                    <flux:input wire:model="form.kd_dokter" readonly placeholder="Kode" class="w-1/3 bg-neutral-50" />
                                                    <div class="relative w-full">
                                                        <flux:input wire:model="form.nm_dokter" readonly placeholder="Pilih Dokter DPJP" class="w-full bg-neutral-50" />
                                                        <flux:button variant="primary" size="sm" icon="magnifying-glass" class="absolute right-1.5 top-1.5 px-2"
                                                            onclick="window.Livewire.dispatch('open-modal', { id: 'modal-dokter-usg' })" />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Section: Informasi Klinis --}}
                                    <div>
                                        <h3 class="text-sm font-bold text-neutral-800 dark:text-neutral-200 uppercase tracking-widest mb-4 pb-2 border-b border-neutral-200 dark:border-neutral-800 flex items-center gap-2">
                                            <flux:icon name="clipboard-document-list" class="w-4 h-4 text-neutral-400" /> Informasi Klinis
                                        </h3>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                            <flux:input label="Kiriman Dari" wire:model="form.kiriman_dari" placeholder="Masukkan instansi perujuk" />
                                            <flux:input label="Diagnosa Klinis" wire:model="form.diagnosa_klinis" placeholder="Masukkan diagnosa klinis" />
                                            <flux:input label="HTA (Hari Taksiran Anak/HPHT)" wire:model="form.hta" placeholder="Masukkan HTA" />
                                            <flux:input label="Jenis Prestasi" wire:model="form.jenis_prestasi" placeholder="Masukkan jenis presentasi" />
                                        </div>
                                    </div>

                                    {{-- Section: Biometri Janin --}}
                                    <div>
                                        <h3 class="text-sm font-bold text-neutral-800 dark:text-neutral-200 uppercase tracking-widest mb-4 pb-2 border-b border-neutral-200 dark:border-neutral-800 flex items-center gap-2">
                                            <flux:icon name="face-smile" class="w-4 h-4 text-neutral-400" /> Biometri Janin
                                        </h3>
                                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                                            <flux:input label="Ukuran Kantong Gestasi (GS)" wire:model="form.kantong_gestasi" placeholder="0.0" />
                                            <flux:input label="Ukuran Bokong - Kepala (CRL)" wire:model="form.ukuran_bokongkepala" placeholder="0.0" />
                                            <flux:input label="Diameter Biparietal (DBP)" wire:model="form.diameter_biparietal" placeholder="0.0" />
                                            <flux:input label="Tafsiran berat Janin (TBJ)" wire:model="form.tafsiran_berat_janin" placeholder="0.0" />
                                            
                                            <flux:input label="Panjang Femur (FL)" wire:model="form.panjang_femur" placeholder="0.0" />
                                            <flux:input label="Lingkar Abdomen (AC)" wire:model="form.lingkar_abdomen" placeholder="0.0" />
                                            <flux:input label="Usia Kehamilan Sesuai" wire:model="form.usia_kehamilan" placeholder="Mgg/Hari" class="lg:col-span-2" />
                                        </div>
                                    </div>

                                    {{-- Section: Plasenta & Cairan Ketuban --}}
                                    <div>
                                        <h3 class="text-sm font-bold text-neutral-800 dark:text-neutral-200 uppercase tracking-widest mb-4 pb-2 border-b border-neutral-200 dark:border-neutral-800 flex items-center gap-2">
                                            <flux:icon name="beaker" class="w-4 h-4 text-neutral-400" /> Plasenta & Cairan Ketuban
                                        </h3>
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                                            <flux:input label="Plasenta Berimplatansi Di" wire:model="form.plasenta_berimplatansi" placeholder="Lokasi plasenta" class="md:col-span-2" />
                                            <flux:select label="Derajat Maturitas Plasenta" wire:model="form.derajat_maturitas">
                                                <option value="0">0</option>
                                                <option value="1">1</option>
                                                <option value="2">2</option>
                                                <option value="3">3</option>
                                            </flux:select>
                                            
                                            <flux:select label="Jumlah Air Ketuban" wire:model="form.jumlah_air_ketuban">
                                                <option value="Cukup">Cukup</option>
                                                <option value="Berkurang">Berkurang</option>
                                            </flux:select>
                                            <flux:input label="Indeks Cairan Ketuban (ICK)" wire:model="form.indek_cairan_ketuban" placeholder="Nilai ICK" class="md:col-span-2" />
                                        </div>
                                    </div>

                                    {{-- Section: Kondisi Lainnya --}}
                                    <div>
                                        <h3 class="text-sm font-bold text-neutral-800 dark:text-neutral-200 uppercase tracking-widest mb-4 pb-2 border-b border-neutral-200 dark:border-neutral-800 flex items-center gap-2">
                                            <flux:icon name="heart" class="w-4 h-4 text-neutral-400" /> Kondisi Lainnya & Kesimpulan
                                        </h3>
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                                            <flux:input label="Kelainan Kongenital Mayor" wire:model="form.kelainan_kongenital" placeholder="Kosongkan jika normal" class="md:col-span-2" />
                                            <flux:select label="Peluang Sex" wire:model="form.peluang_sex">
                                                <option value="Laki-laki">Laki-laki</option>
                                                <option value="Perempuan">Perempuan</option>
                                                <option value="-">-</option>
                                            </flux:select>
                                        </div>
                                        <div class="mt-5">
                                            <flux:textarea label="Kesimpulan" wire:model="form.kesimpulan" rows="4" placeholder="Ketikkan kesimpulan hasil USG di sini..." />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Footer Actions --}}
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
    
    {{-- MODAL LOOKUP DOKTER (menggunakan komponen shared) --}}
    @livewire('shared.modal-lookup-dokter', ['eventTarget' => 'selectDokter'])

    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('selectDokter', (data) => {
                @this.call('selectDokter', data.kd_dokter, data.nm_dokter);
            });
        });
    </script>
</div>
