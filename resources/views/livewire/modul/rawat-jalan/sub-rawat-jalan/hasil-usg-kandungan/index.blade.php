<div class="p-6 pb-24" x-data="{
    showModal: false,
    showPhotoModal: false,
    showDetailModal: false,
    showDeletePhotoConfirm: false,
    previewUrl: null,
    isDragging: false,
    isPasting: false,
    _pasteHandler: null,
    init() {
        Livewire.on('open-modal', () => { this.showModal = true; });
        Livewire.on('close-modal', () => { this.showModal = false; });
        Livewire.on('photo-uploaded', () => {
            this.closePhotoModal();
        });

        setInterval(() => {
            if ($wire.isAutoTimestamp) { $wire.updateTime(); }
        }, 1000);
    },
    handleFileSelect(event) {
        const file = event.target.files[0];
        if (file) this.setPreview(file);
    },
    handleDrop(event) {
        this.isDragging = false;
        const file = event.dataTransfer.files[0];
        if (file && file.type.startsWith('image/')) {
            this.syncFileToLivewire(file);
            this.setPreview(file);
        }
    },
    handlePaste(event) {
        if (!this.showPhotoModal) return;
        const items = event.clipboardData?.items;
        if (!items) return;
        for (const item of items) {
            if (item.type.startsWith('image/')) {
                const file = item.getAsFile();
                if (file) {
                    this.isPasting = true;
                    this.syncFileToLivewire(file);
                    this.setPreview(file);
                    setTimeout(() => { this.isPasting = false; }, 1000);
                }
                break;
            }
        }
    },
    syncFileToLivewire(file) {
        const dt = new DataTransfer();
        dt.items.add(file);
        const input = document.getElementById('photo-file-input');
        if (input) {
            input.files = dt.files;
            input.dispatchEvent(new Event('change'));
        }
    },
    setPreview(file) {
        const reader = new FileReader();
        reader.onload = (e) => { this.previewUrl = e.target.result; };
        reader.readAsDataURL(file);
    },
    clearPhoto() {
        this.previewUrl = null;
        const input = document.getElementById('photo-file-input');
        if (input) input.value = '';
        $wire.set('photoUpload', null);
    },
    openPhotoModal() {
        this.previewUrl = null;
        this.showPhotoModal = true;
        this._pasteHandler = (e) => this.handlePaste(e);
        window.addEventListener('paste', this._pasteHandler);
    },
    closePhotoModal() {
        this.showPhotoModal = false;
        this.previewUrl = null;
        if (this._pasteHandler) {
            window.removeEventListener('paste', this._pasteHandler);
            this._pasteHandler = null;
        }
    }
}">

    {{-- PAGE HEADER --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-neutral-800 dark:text-white">Hasil USG Kandungan</h1>
            <p class="text-sm text-neutral-500 mt-1">Pencatatan hasil pemeriksaan USG khusus untuk kandungan.</p>
        </div>
        <div class="flex gap-2">
            <flux:button href="{{ route('modul.rawat-jalan.perawatan-tindakan', str_replace('/', '-', $no_rawat)) }}"
                wire:navigate variant="ghost" icon="arrow-left">Kembali</flux:button>
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
            <flux:icon name="information-circle" class="h-5 w-5 text-blue-400 flex-shrink-0" />
            <div class="ml-3">
                <h3 class="text-sm font-bold text-blue-800 dark:text-blue-300">Informasi</h3>
                <p class="text-sm text-blue-700 dark:text-blue-400 mt-1">Hanya 1 (satu) data Hasil USG Kandungan yang diperbolehkan per pendaftaran pasien. Gunakan tombol <strong>Edit</strong> untuk melakukan perubahan.</p>
            </div>
        </div>
    </div>
    @endif

    {{-- DATA TABLE --}}
    <div class="bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-200 dark:border-neutral-800 overflow-hidden shadow-sm mb-6">
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
                            <td class="px-6 py-4 text-neutral-600 dark:text-neutral-400">{{ $item->diagnosa_klinis ?: '-' }}</td>
                            <td class="px-6 py-4 text-neutral-600 dark:text-neutral-400 max-w-xs truncate">{{ $item->kesimpulan ?: '-' }}</td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    {{-- View Detail Button --}}
                                    <button type="button" @click="showDetailModal = true"
                                        title="Lihat Detail"
                                        class="p-2 rounded-lg bg-neutral-100 dark:bg-neutral-800 text-neutral-500 hover:bg-sky-100 hover:text-sky-600 dark:hover:bg-sky-950/40 dark:hover:text-sky-400 transition-all cursor-pointer border-none flex items-center justify-center w-8 h-8">
                                        <flux:icon name="eye" class="w-4 h-4" />
                                    </button>

                                    {{-- Edit Button --}}
                                    <button type="button" wire:click="edit"
                                        title="Edit Hasil USG"
                                        class="p-2 rounded-lg bg-neutral-100 dark:bg-neutral-800 text-neutral-500 hover:bg-[#4C5C2D]/10 hover:text-[#4C5C2D] dark:hover:bg-[#4C5C2D]/20 dark:hover:text-[#8CC7C4] transition-all cursor-pointer border-none flex items-center justify-center w-8 h-8">
                                        <flux:icon name="pencil-square" class="w-4 h-4" />
                                    </button>

                                    {{-- Upload Photo Button --}}
                                    <button type="button" @click="openPhotoModal()"
                                        title="Upload Foto USG"
                                        class="p-2 rounded-lg bg-neutral-100 dark:bg-neutral-800 text-neutral-500 hover:bg-purple-100 hover:text-purple-600 dark:hover:bg-purple-950/40 dark:hover:text-purple-400 transition-all cursor-pointer border-none flex items-center justify-center w-8 h-8">
                                        <flux:icon name="camera" class="w-4 h-4" />
                                    </button>

                                    {{-- Delete Button --}}
                                    <div x-data="{ showConfirm: false }" class="relative">
                                        <button type="button" @click="showConfirm = true"
                                            title="Hapus Hasil USG"
                                            class="p-2 rounded-lg bg-neutral-100 dark:bg-neutral-800 text-neutral-500 hover:bg-red-100 hover:text-red-600 dark:hover:bg-red-950/40 dark:hover:text-red-400 transition-all cursor-pointer border-none flex items-center justify-center w-8 h-8">
                                            <flux:icon name="trash" class="w-4 h-4" />
                                        </button>
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

    {{-- ==================== DETAIL VIEW MODAL ==================== --}}
    <div x-show="showDetailModal" x-cloak
         class="fixed inset-0 z-[98] flex items-center justify-center p-4"
         x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="showDetailModal = false"></div>

        <div class="relative w-full max-w-3xl max-h-[90vh] bg-white dark:bg-neutral-900 rounded-2xl shadow-2xl flex flex-col overflow-hidden"
             x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 scale-95 translate-y-4"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             @click.stop>

            {{-- Header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-neutral-200 dark:border-neutral-800 bg-gradient-to-r from-sky-50 to-white dark:from-sky-900/20 dark:to-neutral-900 flex-shrink-0">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-sky-100 dark:bg-sky-900/40 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-sky-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-neutral-800 dark:text-white">Detail Hasil USG Kandungan</h3>
                        <p class="text-xs text-neutral-500">No. Rawat: {{ str_replace('/', '-', $no_rawat) }}</p>
                    </div>
                </div>
                <button @click="showDetailModal = false"
                    class="w-8 h-8 flex items-center justify-center rounded-lg text-neutral-400 hover:text-neutral-700 hover:bg-neutral-100 dark:hover:bg-neutral-800 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- Body scrollable --}}
            <div class="flex-1 overflow-y-auto">
                @forelse($dataUsg as $item)
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-0 divide-y lg:divide-y-0 lg:divide-x divide-neutral-100 dark:divide-neutral-800">

                    {{-- LEFT: Data detail --}}
                    <div class="p-6 space-y-5">
                        {{-- Waktu & Dokter --}}
                        <div>
                            <h4 class="text-xs font-bold uppercase tracking-widest text-neutral-400 mb-3">Waktu & Dokter</h4>
                            <div class="grid grid-cols-2 gap-3">
                                <div class="bg-neutral-50 dark:bg-neutral-800/50 rounded-xl p-3">
                                    <p class="text-[10px] text-neutral-400 uppercase tracking-wide mb-1">Tanggal</p>
                                    <p class="text-sm font-semibold text-neutral-800 dark:text-white">{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</p>
                                </div>
                                <div class="bg-neutral-50 dark:bg-neutral-800/50 rounded-xl p-3">
                                    <p class="text-[10px] text-neutral-400 uppercase tracking-wide mb-1">Jam</p>
                                    <p class="text-sm font-semibold text-neutral-800 dark:text-white">{{ \Carbon\Carbon::parse($item->tanggal)->format('H:i') }}</p>
                                </div>
                                <div class="bg-neutral-50 dark:bg-neutral-800/50 rounded-xl p-3 col-span-2">
                                    <p class="text-[10px] text-neutral-400 uppercase tracking-wide mb-1">Dokter DPJP</p>
                                    <p class="text-sm font-semibold text-neutral-800 dark:text-white">{{ $item->dokter->nm_dokter ?? '-' }}</p>
                                </div>
                            </div>
                        </div>

                        {{-- Informasi Klinis --}}
                        <div>
                            <h4 class="text-xs font-bold uppercase tracking-widest text-neutral-400 mb-3">Informasi Klinis</h4>
                            <div class="space-y-2">
                                @foreach([
                                    'Diagnosa Klinis' => $item->diagnosa_klinis,
                                    'Kiriman Dari'    => $item->kiriman_dari,
                                    'HTA'             => $item->hta,
                                    'Jenis Prestasi'  => $item->jenis_prestasi,
                                ] as $label => $value)
                                <div class="flex justify-between items-start py-1.5 border-b border-neutral-100 dark:border-neutral-800 last:border-0">
                                    <span class="text-xs text-neutral-500 flex-shrink-0 w-32">{{ $label }}</span>
                                    <span class="text-xs font-medium text-neutral-800 dark:text-neutral-200 text-right">{{ $value ?: '-' }}</span>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Biometri Janin --}}
                        <div>
                            <h4 class="text-xs font-bold uppercase tracking-widest text-neutral-400 mb-3">Biometri Janin</h4>
                            <div class="grid grid-cols-2 gap-2">
                                @foreach([
                                    'GS'  => $item->kantong_gestasi,
                                    'CRL' => $item->ukuran_bokongkepala,
                                    'DBP' => $item->diameter_biparietal,
                                    'TBJ' => $item->tafsiran_berat_janin,
                                    'FL'  => $item->panjang_femur,
                                    'AC'  => $item->lingkar_abdomen,
                                ] as $label => $value)
                                <div class="bg-neutral-50 dark:bg-neutral-800/50 rounded-lg px-3 py-2">
                                    <p class="text-[10px] text-neutral-400 uppercase tracking-wide">{{ $label }}</p>
                                    <p class="text-sm font-bold text-neutral-800 dark:text-white">{{ $value ?: '-' }}</p>
                                </div>
                                @endforeach
                                <div class="bg-neutral-50 dark:bg-neutral-800/50 rounded-lg px-3 py-2 col-span-2">
                                    <p class="text-[10px] text-neutral-400 uppercase tracking-wide">Usia Kehamilan</p>
                                    <p class="text-sm font-bold text-neutral-800 dark:text-white">{{ $item->usia_kehamilan ?: '-' }}</p>
                                </div>
                            </div>
                        </div>

                        {{-- Plasenta & Lainnya --}}
                        <div>
                            <h4 class="text-xs font-bold uppercase tracking-widest text-neutral-400 mb-3">Plasenta, Cairan & Lainnya</h4>
                            <div class="space-y-2">
                                @foreach([
                                    'Plasenta di'        => $item->plasenta_berimplatansi,
                                    'Derajat Maturitas'  => $item->derajat_maturitas,
                                    'Jml Air Ketuban'    => $item->jumlah_air_ketuban,
                                    'ICK'                => $item->indek_cairan_ketuban,
                                    'Kel. Kongenital'    => $item->kelainan_kongenital,
                                    'Peluang Sex'        => $item->peluang_sex,
                                ] as $label => $value)
                                <div class="flex justify-between items-start py-1.5 border-b border-neutral-100 dark:border-neutral-800 last:border-0">
                                    <span class="text-xs text-neutral-500 flex-shrink-0 w-36">{{ $label }}</span>
                                    <span class="text-xs font-medium text-neutral-800 dark:text-neutral-200 text-right">{{ $value ?: '-' }}</span>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Kesimpulan --}}
                        @if($item->kesimpulan)
                        <div>
                            <h4 class="text-xs font-bold uppercase tracking-widest text-neutral-400 mb-2">Kesimpulan</h4>
                            <div class="bg-[#F1F5E9] dark:bg-[#4C5C2D]/20 border border-[#4C5C2D]/20 rounded-xl p-4">
                                <p class="text-sm text-neutral-700 dark:text-neutral-300 leading-relaxed">{{ $item->kesimpulan }}</p>
                            </div>
                        </div>
                        @endif
                    </div>

                    {{-- RIGHT: Foto USG --}}
                    <div class="p-6 flex flex-col">
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="text-xs font-bold uppercase tracking-widest text-neutral-400">Foto Hasil USG</h4>
                            <div class="flex items-center gap-2">
                                @if($gambar && $gambar->photo)
                                    <a href="{{ asset($gambar->photo) }}" target="_blank"
                                       class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-sky-50 dark:bg-sky-900/20 text-sky-600 text-xs font-medium hover:bg-sky-100 transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" /></svg>
                                        Buka
                                    </a>
                                @endif
                                <button type="button" @click="showDetailModal = false; openPhotoModal()"
                                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-purple-50 dark:bg-purple-900/20 text-purple-600 text-xs font-medium hover:bg-purple-100 transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    {{ $gambar && $gambar->photo ? 'Ganti' : 'Upload' }}
                                </button>
                            </div>
                        </div>

                        @if($gambar && $gambar->photo)
                            {{-- Photo display --}}
                            <div class="relative flex-1 rounded-2xl overflow-hidden border-2 border-neutral-200 dark:border-neutral-700 bg-black min-h-48"
                                 x-data="{ showDeleteConfirm: false }">
                                <img src="{{ asset($gambar->photo) }}"
                                     alt="Foto USG Kandungan"
                                     class="w-full h-full object-contain"
                                     style="min-height: 12rem"
                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex'">
                                <div style="display:none" class="flex flex-col items-center justify-center h-48 bg-neutral-100 dark:bg-neutral-800 text-neutral-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909" /></svg>
                                    <p class="text-sm">Gambar tidak dapat ditampilkan</p>
                                </div>

                                {{-- Delete button --}}
                                <button @click="showDeleteConfirm = true"
                                    class="absolute top-2 right-2 w-7 h-7 bg-red-600/80 hover:bg-red-600 text-white rounded-lg flex items-center justify-center transition-colors backdrop-blur-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" /></svg>
                                </button>

                                {{-- Delete confirm --}}
                                <div x-show="showDeleteConfirm" x-cloak
                                     class="absolute inset-0 bg-black/70 flex items-center justify-center p-4">
                                    <div class="bg-white dark:bg-neutral-800 rounded-2xl p-5 w-full max-w-xs text-center shadow-xl">
                                        <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" /></svg>
                                        </div>
                                        <p class="text-xs text-neutral-500 mb-4">Hapus foto USG ini secara permanen?</p>
                                        <div class="flex gap-2">
                                            <button @click="showDeleteConfirm = false"
                                                class="flex-1 px-3 py-2 rounded-lg border border-neutral-200 dark:border-neutral-700 text-xs font-medium text-neutral-600 dark:text-neutral-400 hover:bg-neutral-50 transition-colors">
                                                Batal
                                            </button>
                                            <button wire:click="deletePhoto" @click="showDeleteConfirm = false; showDetailModal = false"
                                                class="flex-1 px-3 py-2 rounded-lg bg-red-600 text-white text-xs font-medium hover:bg-red-700 transition-colors">
                                                Hapus
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            {{-- No photo yet --}}
                            <div class="flex-1 flex flex-col items-center justify-center py-10 text-center rounded-2xl border-2 border-dashed border-neutral-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800/30">
                                <div class="w-16 h-16 rounded-2xl bg-neutral-100 dark:bg-neutral-800 flex items-center justify-center mb-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-neutral-300 dark:text-neutral-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </div>
                                <p class="text-sm font-medium text-neutral-500 mb-1">Belum ada foto</p>
                                <p class="text-xs text-neutral-400 mb-4">Klik "Upload" di atas untuk menambahkan foto</p>
                            </div>
                        @endif
                    </div>
                </div>
                @empty
                <div class="p-12 text-center text-neutral-400">Tidak ada data.</div>
                @endforelse
            </div>

            {{-- Footer --}}
            <div class="flex justify-end px-6 py-4 border-t border-neutral-200 dark:border-neutral-800 bg-neutral-50 dark:bg-neutral-800/50 flex-shrink-0">
                <button @click="showDetailModal = false"
                    class="px-5 py-2 rounded-xl bg-neutral-200 dark:bg-neutral-700 text-neutral-700 dark:text-neutral-300 text-sm font-medium hover:bg-neutral-300 dark:hover:bg-neutral-600 transition-colors">
                    Tutup
                </button>
            </div>
        </div>
    </div>


    {{-- ==================== PHOTO UPLOAD MODAL ==================== --}}
    <div x-show="showPhotoModal" x-cloak
         class="fixed inset-0 z-[100] flex items-center justify-center p-4"
         x-transition:enter="ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">

        {{-- Backdrop --}}
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="showPhotoModal = false"></div>

        {{-- Modal Panel --}}
        <div class="relative w-full max-w-lg bg-white dark:bg-neutral-900 rounded-2xl shadow-2xl overflow-hidden"
             x-transition:enter="ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95 translate-y-4"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-95 translate-y-4"
             @click.stop>

            {{-- Modal Header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-neutral-200 dark:border-neutral-800 bg-gradient-to-r from-purple-50 to-white dark:from-purple-900/20 dark:to-neutral-900">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-purple-100 dark:bg-purple-900/40 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-neutral-800 dark:text-white">Upload Foto USG Kandungan</h3>
                        <p class="text-xs text-neutral-500">JPEG, PNG, WebP · Maks 10MB</p>
                    </div>
                </div>
                <button @click="closePhotoModal()"
                    class="w-8 h-8 flex items-center justify-center rounded-lg text-neutral-400 hover:text-neutral-700 hover:bg-neutral-100 dark:hover:bg-neutral-800 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- Modal Body --}}
            <div class="p-6 space-y-5">

                {{-- Validation error --}}
                @error('photoUpload')
                <div class="flex items-center gap-2 p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl text-red-700 dark:text-red-400 text-sm">
                    <flux:icon name="exclamation-circle" class="w-4 h-4 flex-shrink-0" />
                    {{ $message }}
                </div>
                @enderror

                {{-- Step 1: No preview yet — show upload area --}}
                <div x-show="!previewUrl">
                    {{-- Drag & Drop Zone --}}
                    <div
                        class="relative border-2 border-dashed rounded-2xl transition-all duration-200 cursor-pointer"
                        :class="isDragging
                            ? 'border-purple-500 bg-purple-50 dark:bg-purple-900/20 scale-[1.01]'
                            : 'border-neutral-300 dark:border-neutral-700 hover:border-purple-400 hover:bg-purple-50/50 dark:hover:bg-purple-900/10'"
                        @dragover.prevent="isDragging = true"
                        @dragleave.prevent="isDragging = false"
                        @drop.prevent="handleDrop($event)"
                        @click="document.getElementById('photo-file-input').click()">

                        <div class="flex flex-col items-center justify-center py-12 px-6 text-center pointer-events-none">
                            <div class="w-16 h-16 rounded-2xl bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center mb-4 transition-transform"
                                 :class="isDragging ? 'scale-110' : ''">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V9.75m0 0l3 3m-3-3l-3 3M6.75 19.5a4.5 4.5 0 01-1.41-8.775 5.25 5.25 0 0110.338-2.32 5.25 5.25 0 011.86 8.075" />
                                </svg>
                            </div>
                            <h4 class="font-semibold text-neutral-700 dark:text-neutral-300 mb-1">
                                <span x-show="!isDragging">Seret & lepas foto di sini</span>
                                <span x-show="isDragging">Lepaskan untuk upload</span>
                            </h4>
                            <p class="text-sm text-neutral-400 mb-3">atau klik untuk memilih dari galeri</p>
                            <span class="px-4 py-1.5 bg-white dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-lg text-xs font-medium text-neutral-600 dark:text-neutral-400 shadow-sm">
                                Pilih File
                            </span>
                        </div>

                        <input id="photo-file-input" type="file" class="hidden"
                            wire:model="photoUpload"
                            accept="image/jpeg,image/jpg,image/png,image/webp"
                            @change="handleFileSelect($event)">
                    </div>

                    {{-- Paste hint banner --}}
                    <div
                        class="flex items-center justify-center gap-2 py-2 px-4 rounded-xl transition-all duration-300"
                        :class="isPasting
                            ? 'bg-green-50 dark:bg-green-900/20 border border-green-300 dark:border-green-700'
                            : 'bg-neutral-50 dark:bg-neutral-800/50 border border-neutral-200 dark:border-neutral-700'">
                        <template x-if="!isPasting">
                            <div class="flex items-center gap-2">
                                <div class="flex items-center gap-1">
                                    <kbd class="px-1.5 py-0.5 bg-white dark:bg-neutral-700 border border-neutral-300 dark:border-neutral-600 rounded text-[10px] font-mono font-bold text-neutral-600 dark:text-neutral-300 shadow-sm">Ctrl</kbd>
                                    <span class="text-neutral-400 text-xs">+</span>
                                    <kbd class="px-1.5 py-0.5 bg-white dark:bg-neutral-700 border border-neutral-300 dark:border-neutral-600 rounded text-[10px] font-mono font-bold text-neutral-600 dark:text-neutral-300 shadow-sm">V</kbd>
                                </div>
                                <span class="text-xs text-neutral-500">Tempel gambar dari clipboard</span>
                            </div>
                        </template>
                        <template x-if="isPasting">
                            <div class="flex items-center gap-2 text-green-700 dark:text-green-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 animate-bounce" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                                <span class="text-xs font-semibold">Gambar dari clipboard berhasil ditempel!</span>
                            </div>
                        </template>
                    </div>

                    {{-- Divider --}}
                    <div class="relative flex items-center my-1">
                        <div class="flex-1 border-t border-neutral-200 dark:border-neutral-700"></div>
                        <span class="mx-3 text-xs text-neutral-400 font-medium">atau</span>
                        <div class="flex-1 border-t border-neutral-200 dark:border-neutral-700"></div>
                    </div>

                    {{-- Camera Capture Button --}}
                    <button type="button"
                        @click="document.getElementById('photo-camera-input').click()"
                        class="w-full flex items-center justify-center gap-3 px-4 py-3.5 rounded-xl border-2 border-neutral-200 dark:border-neutral-700 hover:border-purple-400 dark:hover:border-purple-600 hover:bg-purple-50 dark:hover:bg-purple-900/10 transition-all group">
                        <div class="w-9 h-9 rounded-xl bg-neutral-100 dark:bg-neutral-800 group-hover:bg-purple-100 dark:group-hover:bg-purple-900/30 flex items-center justify-center transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-neutral-500 group-hover:text-purple-600 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <div class="text-left">
                            <p class="text-sm font-semibold text-neutral-700 dark:text-neutral-300 group-hover:text-purple-700 dark:group-hover:text-purple-300 transition-colors">Ambil Foto dengan Kamera</p>
                            <p class="text-xs text-neutral-400">Gunakan kamera perangkat untuk mengambil foto langsung</p>
                        </div>
                        <input id="photo-camera-input" type="file" class="hidden"
                            accept="image/*" capture="environment"
                            @change="
                                handleFileSelect($event);
                                // sync to livewire
                                const dt = new DataTransfer();
                                dt.items.add($event.target.files[0]);
                                document.getElementById('photo-file-input').files = dt.files;
                                document.getElementById('photo-file-input').dispatchEvent(new Event('change'));
                            ">
                    </button>
                </div>

                {{-- Step 2: Preview mode --}}
                <div x-show="previewUrl" class="space-y-4">
                    {{-- Preview label --}}
                    <div class="flex items-center gap-2 text-sm font-semibold text-neutral-700 dark:text-neutral-300">
                        <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                        Preview Foto
                    </div>

                    {{-- Image preview --}}
                    <div class="relative rounded-2xl overflow-hidden border-2 border-purple-200 dark:border-purple-800 bg-black">
                        <img :src="previewUrl" alt="Preview" class="w-full max-h-72 object-contain">
                        {{-- Remove preview button --}}
                        <button @click="clearPhoto()"
                            class="absolute top-2 right-2 w-8 h-8 bg-black/60 hover:bg-black/80 text-white rounded-lg flex items-center justify-center transition-colors backdrop-blur-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <p class="text-xs text-neutral-400 flex items-center gap-1.5">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                        Foto siap disimpan. Klik "Simpan Foto" untuk melanjutkan, atau klik ✕ pada foto untuk mengganti.
                    </p>
                </div>
            </div>

            {{-- Modal Footer --}}
            <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-neutral-200 dark:border-neutral-800 bg-neutral-50 dark:bg-neutral-800/50">
                <button type="button" @click="closePhotoModal(); clearPhoto()"
                    class="px-4 py-2 rounded-xl text-sm font-medium text-neutral-600 dark:text-neutral-400 hover:bg-neutral-200 dark:hover:bg-neutral-700 transition-colors">
                    Batal
                </button>
                <button type="button"
                    x-show="previewUrl"
                    wire:click="uploadPhoto"
                    wire:loading.attr="disabled"
                    class="inline-flex items-center gap-2 px-5 py-2 rounded-xl bg-purple-600 hover:bg-purple-700 text-white text-sm font-semibold transition-colors shadow-lg shadow-purple-200 dark:shadow-none disabled:opacity-60 disabled:cursor-not-allowed">
                    <span wire:loading.remove wire:target="uploadPhoto">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V9.75m0 0l3 3m-3-3l-3 3" /><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5" /></svg>
                    </span>
                    <span wire:loading.remove wire:target="uploadPhoto">Simpan Foto</span>
                    <span wire:loading wire:target="uploadPhoto" class="flex items-center gap-2">
                        <svg class="animate-spin w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        Mengupload...
                    </span>
                </button>
            </div>
        </div>
    </div>

    {{-- ==================== FORM SLIDE-OVER MODAL ==================== --}}
    <div x-show="showModal" x-cloak class="relative z-[99]" aria-labelledby="slide-over-title" role="dialog" aria-modal="true">
        <div x-show="showModal"
             x-transition:enter="ease-in-out duration-500" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in-out duration-500" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity"
             @click="showModal = false"></div>

        <div class="fixed inset-0 overflow-hidden">
            <div class="absolute inset-0 overflow-hidden">
                <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10">
                    <div x-show="showModal"
                         x-transition:enter="transform transition ease-in-out duration-500 sm:duration-700"
                         x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
                         x-transition:leave="transform transition ease-in-out duration-500 sm:duration-700"
                         x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full"
                         class="pointer-events-auto relative w-screen max-w-4xl">

                        <div x-show="showModal"
                             x-transition:enter="ease-in-out duration-500" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                             x-transition:leave="ease-in-out duration-500" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                             class="absolute left-0 top-0 -ml-8 flex pr-2 pt-4 sm:-ml-10 sm:pr-4">
                            <button type="button" @click="showModal = false" class="relative rounded-md text-gray-300 hover:text-white focus:outline-none focus:ring-2 focus:ring-white">
                                <span class="sr-only">Tutup panel</span>
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        </div>

                        <div class="flex h-full flex-col bg-white dark:bg-neutral-900 shadow-2xl">
                            <div class="px-6 py-5 border-b border-neutral-200 dark:border-neutral-800 bg-[#F1F5E9] dark:bg-neutral-800">
                                <h2 class="text-xl font-bold text-[#4C5C2D] dark:text-[#8CC7C4]" id="slide-over-title">
                                    {{ $isEdit ? 'Ubah Hasil USG Kandungan' : 'Buat Hasil USG Kandungan' }}
                                </h2>
                                <p class="text-xs text-neutral-500 mt-1">Formulir pemeriksaan USG khusus kandungan</p>
                            </div>

                            <div class="relative flex-1 px-6 py-6 overflow-y-auto">
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

                                    <div>
                                        <h3 class="text-sm font-bold text-neutral-800 dark:text-neutral-200 uppercase tracking-widest mb-4 pb-2 border-b border-neutral-200 dark:border-neutral-800 flex items-center gap-2">
                                            <flux:icon name="face-smile" class="w-4 h-4 text-neutral-400" /> Biometri Janin
                                        </h3>
                                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                                            <flux:input label="Kantong Gestasi (GS)" wire:model="form.kantong_gestasi" placeholder="0.0" />
                                            <flux:input label="Bokong - Kepala (CRL)" wire:model="form.ukuran_bokongkepala" placeholder="0.0" />
                                            <flux:input label="Diameter Biparietal (DBP)" wire:model="form.diameter_biparietal" placeholder="0.0" />
                                            <flux:input label="Tafsiran Berat Janin (TBJ)" wire:model="form.tafsiran_berat_janin" placeholder="0.0" />
                                            <flux:input label="Panjang Femur (FL)" wire:model="form.panjang_femur" placeholder="0.0" />
                                            <flux:input label="Lingkar Abdomen (AC)" wire:model="form.lingkar_abdomen" placeholder="0.0" />
                                            <flux:input label="Usia Kehamilan Sesuai" wire:model="form.usia_kehamilan" placeholder="Mgg/Hari" class="lg:col-span-2" />
                                        </div>
                                    </div>

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

    {{-- MODAL LOOKUP DOKTER --}}
    @livewire('shared.modal-lookup-dokter', ['eventTarget' => 'selectDokter'])

    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('selectDokter', (data) => {
                @this.call('selectDokter', data.kd_dokter, data.nm_dokter);
            });
        });
    </script>
</div>
