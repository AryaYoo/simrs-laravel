<div class="flex flex-col gap-6 pb-8" x-data="{ openConfirm: false }">
    {{-- Header / Breadcrumb --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.sql-tracker') }}" wire:navigate class="flex items-center justify-center w-10 h-8 rounded-md bg-[#4C5C2D] transition-colors hover:bg-[#3d4b24] shadow-sm">
            <flux:icon name="chevron-left" class="w-5 h-5 text-white" />
        </a>
        <div>
            <nav class="text-xs text-neutral-400 mb-0.5">
                <a href="{{ route('dashboard') }}" wire:navigate class="hover:underline">Dashboard</a>
                <span class="mx-1">/</span>
                <a href="{{ route('admin.sql-tracker') }}" wire:navigate class="hover:underline">SQL Tracker</a>
                <span class="mx-1">/</span>
                <span>Pengaturan</span>
            </nav>
            <h1 class="text-xl font-bold text-neutral-800 dark:text-neutral-100">Pengelolaan Riwayat SQL Tracker</h1>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- Stat Total --}}
        <div class="bg-white dark:bg-neutral-800 p-6 rounded-2xl border border-neutral-200 dark:border-neutral-700 flex items-center justify-between shadow-sm">
            <div>
                <span class="text-sm font-semibold text-neutral-400 dark:text-neutral-500 block">Total Baris Log SQL</span>
                <span class="text-3xl font-extrabold text-neutral-800 dark:text-neutral-100 font-mono mt-1 block">
                    {{ number_format($totalLogs) }}
                </span>
                <span class="text-xs text-neutral-400 mt-2 block">Seluruh perubahan database terhitung</span>
            </div>
            <div class="p-4 bg-green-50 dark:bg-green-950/30 text-[#4C5C2D] dark:text-[#8CC7C4] rounded-2xl">
                <flux:icon name="circle-stack" class="w-8 h-8" />
            </div>
        </div>

        {{-- Stat Size --}}
        <div class="bg-white dark:bg-neutral-800 p-6 rounded-2xl border border-neutral-200 dark:border-neutral-700 flex items-center justify-between shadow-sm">
            <div>
                <span class="text-sm font-semibold text-neutral-400 dark:text-neutral-500 block">Ukuran Disk Database</span>
                <span class="text-3xl font-extrabold text-neutral-800 dark:text-neutral-100 font-mono mt-1 block">
                    {{ $dbSize }}
                </span>
                <span class="text-xs text-neutral-400 mt-2 block">Kapasitas penyimpanan tabel `trackersql`</span>
            </div>
            <div class="p-4 bg-amber-50 dark:bg-amber-950/30 text-amber-600 dark:text-amber-400 rounded-2xl">
                <flux:icon name="folder" class="w-8 h-8" />
            </div>
        </div>
    </div>

    {{-- Configuration Cards --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- Card: Export --}}
        <div class="bg-white dark:bg-neutral-800 rounded-2xl border border-neutral-200 dark:border-neutral-700 p-6 flex flex-col justify-between shadow-sm">
            <div>
                <div class="flex items-center gap-3 mb-4">
                    <div class="p-2.5 bg-[#4C5C2D]/10 text-[#4C5C2D] dark:text-[#8CC7C4] rounded-xl">
                        <flux:icon name="arrow-down-tray" class="w-6 h-6" />
                    </div>
                    <h3 class="text-md font-bold text-neutral-800 dark:text-neutral-100">Ekspor & Backup Log</h3>
                </div>
                <p class="text-sm text-neutral-500 dark:text-neutral-400 mb-6">
                    Unduh semua riwayat perubahan database ke komputer lokal sebagai file cadangan. Tersedia dalam format CSV maupun SQL INSERT statement.
                </p>
                <div class="bg-neutral-50 dark:bg-neutral-950 p-4 rounded-xl border border-neutral-100 dark:border-neutral-800 text-xs text-neutral-500 mb-6">
                    <span class="font-bold block text-neutral-600 dark:text-neutral-400 mb-1">Catatan Proses:</span>
                    Proses ekspor menggunakan database streaming chunking sehingga aman dijalankan dan tidak memberatkan memori RAM server.
                </div>
            </div>
            <div class="flex flex-col gap-2">
                <flux:button wire:click="exportLogs" variant="filled" class="!bg-[#4C5C2D] hover:!bg-[#3d4b24] text-white w-full" icon="arrow-down-tray">
                    Ekspor ke CSV
                </flux:button>
                <flux:button wire:click="exportSql" variant="filled" class="!bg-neutral-700 hover:!bg-neutral-900 dark:!bg-neutral-600 dark:hover:!bg-neutral-500 text-white w-full" icon="arrow-down-tray">
                    Ekspor ke SQL
                </flux:button>
            </div>
        </div>

        {{-- Card: Prune / Delete --}}
        <div class="bg-white dark:bg-neutral-800 rounded-2xl border border-neutral-200 dark:border-neutral-700 p-6 flex flex-col justify-between shadow-sm">
            <div>
                <div class="flex items-center gap-3 mb-4">
                    <div class="p-2.5 bg-red-50 dark:bg-red-950/30 text-red-600 dark:text-red-400 rounded-xl">
                        <flux:icon name="trash" class="w-6 h-6" />
                    </div>
                    <h3 class="text-md font-bold text-neutral-800 dark:text-neutral-100">Pembersihan Log (Pruning)</h3>
                </div>
                <p class="text-sm text-neutral-500 dark:text-neutral-400 mb-6">
                    Bersihkan log yang sudah terlalu lama untuk membebaskan kapasitas memori database. Disarankan untuk membackup data terlebih dahulu.
                </p>
                
                <div class="mb-6">
                    <flux:select wire:model="pruneDays" label="Pilih Rentang Waktu Hapus">
                        <option value="30">Lebih lama dari 30 Hari</option>
                        <option value="90">Lebih lama dari 90 Hari</option>
                        <option value="180">Lebih lama dari 180 Hari</option>
                        <option value="365">Lebih lama dari 1 Tahun (365 Hari)</option>
                        <option value="all">Hapus Semua Log (Kosongkan Tabel)</option>
                    </flux:select>
                </div>
            </div>
            <flux:button @click="openConfirm = true" variant="filled" class="!bg-red-600 hover:!bg-red-700 text-white w-full" icon="trash">
                Bersihkan Sekarang
            </flux:button>
        </div>

        {{-- Card: Inject / Restore --}}
        <div class="bg-white dark:bg-neutral-800 rounded-2xl border border-neutral-200 dark:border-neutral-700 p-6 flex flex-col justify-between shadow-sm">
            <div>
                <div class="flex items-center gap-3 mb-4">
                    <div class="p-2.5 bg-blue-50 dark:bg-blue-950/30 text-blue-600 dark:text-blue-400 rounded-xl">
                        <flux:icon name="arrow-up-tray" class="w-6 h-6" />
                    </div>
                    <h3 class="text-md font-bold text-neutral-800 dark:text-neutral-100">Impor & Inject Log</h3>
                </div>
                <p class="text-sm text-neutral-500 dark:text-neutral-400 mb-6">
                    Masukkan kembali arsip data log (.csv) hasil ekspor sebelumnya ke dalam database SQL tracker.
                </p>

                <div 
                    x-data="{ isUploading: false, progress: 0 }" 
                    x-on:livewire-upload-start="isUploading = true"
                    x-on:livewire-upload-finish="isUploading = false"
                    x-on:livewire-upload-error="isUploading = false"
                    x-on:livewire-upload-progress="progress = $event.detail.progress"
                    class="space-y-4"
                >
                    <div class="flex items-center justify-center w-full">
                        <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-neutral-300 dark:border-neutral-600 border-dashed rounded-xl cursor-pointer bg-neutral-50 dark:bg-neutral-900 hover:bg-neutral-100 dark:hover:bg-neutral-800/50 transition-colors">
                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                <flux:icon name="document-arrow-up" class="w-8 h-8 text-neutral-400 dark:text-neutral-500 mb-2" />
                                <p class="text-xs text-neutral-500 dark:text-neutral-400">
                                    <span class="font-semibold text-[#4C5C2D]">Klik untuk upload</span> file CSV
                                </p>
                                <p class="text-[10px] text-neutral-400">Maks. 50MB (.csv / .txt)</p>
                            </div>
                            <input type="file" wire:model="importedFile" class="hidden" accept=".csv,.txt" />
                        </label>
                    </div>

                    {{-- Loading / Progress Bar --}}
                    <div x-show="isUploading" x-cloak class="w-full bg-neutral-200 dark:bg-neutral-700 rounded-full h-1.5 overflow-hidden">
                        <div class="bg-[#4C5C2D] h-1.5 rounded-full transition-all duration-150" x-bind:style="'width: ' + progress + '%'"></div>
                    </div>
                </div>

                {{-- Status Messages --}}
                @if($importStatus === 'loading')
                    <div class="mt-4 p-3 bg-blue-50 dark:bg-blue-950/20 text-blue-700 dark:text-blue-400 border border-blue-100 dark:border-blue-900/30 rounded-xl text-xs flex items-center gap-2">
                        <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span>{{ $importMessage }}</span>
                    </div>
                @elseif($importStatus === 'success')
                    <div class="mt-4 p-3 bg-green-50 dark:bg-green-950/20 text-green-700 dark:text-green-400 border border-green-100 dark:border-green-900/30 rounded-xl text-xs">
                        {{ $importMessage }}
                    </div>
                @elseif($importStatus === 'error')
                    <div class="mt-4 p-3 bg-red-50 dark:bg-red-950/20 text-red-700 dark:text-red-400 border border-red-100 dark:border-red-900/30 rounded-xl text-xs">
                        {{ $importMessage }}
                    </div>
                @endif
            </div>

            @if($importedFile)
                <flux:button wire:click="injectLogs" variant="filled" class="!bg-blue-600 hover:!bg-blue-700 text-white w-full mt-4" icon="arrow-up-tray">
                    Mulai Impor File
                </flux:button>
            @else
                <flux:button disabled variant="filled" class="w-full mt-4" icon="arrow-up-tray">
                    Pilih File Terlebih Dahulu
                </flux:button>
            @endif
        </div>

    </div>

    {{-- Confirm Pruning Modal (Alpine.js - SOP #6) --}}
    <div x-show="openConfirm" x-cloak class="fixed inset-0 z-[99] flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-neutral-900/60 backdrop-blur-sm" @click="openConfirm = false"></div>
        <div class="relative w-full max-w-md bg-white dark:bg-neutral-800 rounded-2xl shadow-xl border border-neutral-200 dark:border-neutral-700/80 p-6">
            <div class="flex items-center gap-3 text-red-600 mb-4">
                <flux:icon name="exclamation-triangle" class="w-7 h-7" />
                <h3 class="text-lg font-bold text-neutral-800 dark:text-neutral-100">Hapus Log Permanen?</h3>
            </div>
            
            <p class="text-sm text-neutral-500 dark:text-neutral-400 mb-6">
                Apakah Anda yakin ingin menghapus data log SQL tracker 
                <span class="font-semibold text-red-600">
                    @if($pruneDays === 'all')
                        semua data (total pembersihan tabel)
                    @else
                        yang lebih lama dari {{ $pruneDays }} hari
                    @endif
                </span>?
                Tindakan ini bersifat merusak dan data yang sudah dihapus tidak dapat dipulihkan kembali jika Anda belum membuat salinan cadangan.
            </p>

            <div class="flex justify-end gap-3">
                <flux:button variant="ghost" @click="openConfirm = false">Batal</flux:button>
                <flux:button variant="filled" class="!bg-red-600 hover:!bg-red-700 text-white" @click="$wire.pruneLogs(); openConfirm = false">
                    Ya, Bersihkan Log
                </flux:button>
            </div>
        </div>
    </div>
</div>
