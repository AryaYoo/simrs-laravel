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
        </div>
        
        <div class="px-6 py-4 bg-neutral-50 dark:bg-neutral-800/50 border-t border-neutral-200 dark:border-neutral-700 flex justify-end gap-3 rounded-b-xl">
            <flux:button href="{{ route('dashboard') }}" wire:navigate variant="ghost">Batal</flux:button>
            <flux:button type="submit" variant="primary" icon="check">Simpan Perubahan</flux:button>
        </div>
    </form>
</div>
