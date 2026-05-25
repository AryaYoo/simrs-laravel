<div class="flex flex-col gap-6 pb-8">
    {{-- Header / Breadcrumb --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('dashboard') }}" wire:navigate
           class="flex items-center justify-center w-8 h-8 rounded-lg transition-colors hover:bg-neutral-100 dark:hover:bg-neutral-700">
            <flux:icon name="chevron-left" class="w-5 h-5 text-neutral-500" />
        </a>
        <div>
            <nav class="text-xs text-neutral-400 mb-0.5">
                <a href="{{ route('dashboard') }}" wire:navigate class="hover:underline">Dashboard</a>
                <span class="mx-1">/</span>
                <span>Pengaturan</span>
            </nav>
            <h1 class="text-xl font-bold text-neutral-800 dark:text-neutral-100">Pengaturan Aplikasi</h1>
        </div>
    </div>

    <form wire:submit="save" class="bg-white dark:bg-neutral-800 rounded-xl border border-neutral-200 dark:border-neutral-700 overflow-hidden">
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                
                {{-- Panel Konfigurasi API --}}
                <div class="space-y-6">
                    <div>
                        <h2 class="text-base font-semibold text-neutral-800 dark:text-neutral-200">Kunci API (API Keys)</h2>
                        <p class="text-sm text-neutral-500 mt-1">Konfigurasi kunci untuk layanan pihak ketiga yang terintegrasi dengan SIMRS.</p>
                    </div>
                    
                    <div class="space-y-4 bg-neutral-50 dark:bg-neutral-900/50 p-4 rounded-lg border border-neutral-100 dark:border-neutral-800">
                        <flux:input wire:model="google_vision_api_key" label="Google Cloud Vision API Key" placeholder="AIzaSyB..........................." />
                        <p class="text-xs text-neutral-400">
                            Digunakan untuk fitur OCR Scanner (Membaca teks dari foto KTP).
                        </p>
                    </div>
                </div>

                {{-- Panel Konfigurasi Rumah Sakit --}}
                <div class="space-y-6">
                    <div>
                        <h2 class="text-base font-semibold text-neutral-800 dark:text-neutral-200">Profil Rumah Sakit</h2>
                        <p class="text-sm text-neutral-500 mt-1">Pengaturan identitas instansi kesehatan Anda.</p>
                    </div>
                    
                    <div class="space-y-4 bg-neutral-50 dark:bg-neutral-900/50 p-4 rounded-lg border border-neutral-100 dark:border-neutral-800">
                        <flux:input wire:model="nama_instansi" label="Nama Instansi / Rumah Sakit" placeholder="Contoh: RSUD Berkah" />
                    </div>
                </div>
                
            </div>

            {{-- Panel Konfigurasi Cetak Web Independen --}}
            <div class="mt-8 pt-8 border-t border-neutral-200 dark:border-neutral-700">
                <div class="mb-6">
                    <h2 class="text-base font-semibold text-neutral-800 dark:text-neutral-200">Pengaturan Cetak Dokumen Web (Independen)</h2>
                    <p class="text-sm text-neutral-500 mt-1">Identitas dan logo khusus yang digunakan saat mencetak dokumen langsung dari aplikasi web Laralite. Jika dikosongkan, sistem akan kembali menggunakan data utama (SIMRS Khanza).</p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 bg-neutral-50 dark:bg-neutral-900/50 p-6 rounded-lg border border-neutral-100 dark:border-neutral-800">
                    <div class="space-y-4">
                        <flux:input wire:model="cetak_nama_instansi" label="Nama Instansi (Web Cetak)" placeholder="Contoh: RSIA IBI Surabaya" />
                        <flux:input wire:model="cetak_alamat_instansi" label="Alamat Instansi" placeholder="Contoh: Jl. Dupak No. 15A" />
                        <div class="grid grid-cols-2 gap-4">
                            <flux:input wire:model="cetak_kabupaten" label="Kabupaten/Kota" placeholder="Contoh: Surabaya" />
                            <flux:input wire:model="cetak_propinsi" label="Provinsi" placeholder="Contoh: Jawa Timur" />
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <flux:input wire:model="cetak_kontak" label="No. Telepon / Kontak" placeholder="Contoh: 0315323837" />
                            <flux:input wire:model="cetak_email" label="Email" type="email" placeholder="Contoh: info@rsiaibi.com" />
                        </div>
                    </div>
                    
                    <div class="space-y-4">
                        <div class="flex flex-col gap-2">
                            <label class="text-sm font-medium text-neutral-700 dark:text-neutral-300">Logo Instansi</label>
                            
                            @if ($cetak_logo)
                                <div class="w-32 h-32 rounded-lg border border-neutral-200 overflow-hidden mb-2 bg-white flex items-center justify-center p-2">
                                    <img src="{{ $cetak_logo->temporaryUrl() }}" class="max-w-full max-h-full object-contain">
                                </div>
                            @elseif ($cetak_logo_preview)
                                <div class="w-32 h-32 rounded-lg border border-neutral-200 overflow-hidden mb-2 bg-white flex items-center justify-center p-2">
                                    <img src="data:image/jpeg;base64,{{ $cetak_logo_preview }}" class="max-w-full max-h-full object-contain">
                                </div>
                            @else
                                <div class="w-32 h-32 rounded-lg border-2 border-dashed border-neutral-300 overflow-hidden mb-2 bg-neutral-100 flex items-center justify-center text-neutral-400">
                                    <flux:icon name="photo" class="w-8 h-8" />
                                </div>
                            @endif
                            
                            <input type="file" wire:model="cetak_logo" class="block w-full text-sm text-neutral-500
                                file:mr-4 file:py-2 file:px-4
                                file:rounded-md file:border-0
                                file:text-sm file:font-semibold
                                file:bg-blue-50 file:text-blue-700
                                hover:file:bg-blue-100
                                dark:file:bg-blue-900/30 dark:file:text-blue-400
                            " accept="image/*" />
                            <div wire:loading wire:target="cetak_logo" class="text-xs text-blue-500 mt-1">Mengunggah logo...</div>
                            @error('cetak_logo') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                            <p class="text-xs text-neutral-500 mt-1">Format: JPG, PNG. Maksimal: 2MB.</p>
                        </div>
                        
                        <div class="flex flex-col gap-2 mt-4">
                            <label class="text-sm font-medium text-neutral-700 dark:text-neutral-300">Background / Watermark Cetak</label>
                            
                            @if ($cetak_background)
                                <div class="w-full h-32 rounded-lg border border-neutral-200 overflow-hidden mb-2 bg-white flex items-center justify-center p-2">
                                    <img src="{{ $cetak_background->temporaryUrl() }}" class="max-w-full max-h-full object-contain">
                                </div>
                            @elseif ($cetak_background_preview)
                                <div class="w-full h-32 rounded-lg border border-neutral-200 overflow-hidden mb-2 bg-white flex items-center justify-center p-2">
                                    <img src="data:image/jpeg;base64,{{ $cetak_background_preview }}" class="max-w-full max-h-full object-contain">
                                </div>
                            @else
                                <div class="w-full h-32 rounded-lg border-2 border-dashed border-neutral-300 overflow-hidden mb-2 bg-neutral-100 flex items-center justify-center text-neutral-400">
                                    <flux:icon name="photo" class="w-8 h-8" />
                                </div>
                            @endif
                            
                            <input type="file" wire:model="cetak_background" class="block w-full text-sm text-neutral-500
                                file:mr-4 file:py-2 file:px-4
                                file:rounded-md file:border-0
                                file:text-sm file:font-semibold
                                file:bg-blue-50 file:text-blue-700
                                hover:file:bg-blue-100
                                dark:file:bg-blue-900/30 dark:file:text-blue-400
                            " accept="image/*" />
                            <div wire:loading wire:target="cetak_background" class="text-xs text-blue-500 mt-1">Mengunggah background...</div>
                            @error('cetak_background') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                            <p class="text-xs text-neutral-500 mt-1">Format: JPG, PNG. Maksimal: 2MB.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="px-6 py-4 bg-neutral-50 dark:bg-neutral-800/50 border-t border-neutral-200 dark:border-neutral-700 flex justify-end gap-3 rounded-b-xl">
            <flux:button href="{{ route('dashboard') }}" wire:navigate variant="ghost">Batal</flux:button>
            <flux:button type="submit" variant="primary" icon="check">Simpan Perubahan</flux:button>
        </div>
    </form>
</div>
