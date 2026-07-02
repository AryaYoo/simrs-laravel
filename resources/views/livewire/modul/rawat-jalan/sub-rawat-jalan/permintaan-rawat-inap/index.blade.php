<div class="flex flex-col gap-6 pb-8">
    {{-- Header / Breadcrumb --}}
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('modul.rawat-jalan.perawatan-tindakan', str_replace('/', '-', $no_rawat)) }}" wire:navigate class="flex items-center justify-center w-10 h-8 rounded-md bg-[#4C5C2D] transition-colors hover:bg-[#3d4b24] shadow-sm">
                <flux:icon name="chevron-left" class="w-5 h-5 text-white" />
            </a>
            <div>
                <nav class="text-xs text-neutral-400 mb-0.5">
                    <a href="{{ route('modul.index') }}" wire:navigate class="hover:underline">Modul</a>
                    <span class="mx-1">/</span>
                    <a href="{{ route('modul.rawat-jalan.index') }}" wire:navigate class="hover:underline">Rawat Jalan</a>
                    <span class="mx-1">/</span>
                    <a href="{{ route('modul.rawat-jalan.perawatan-tindakan', str_replace('/', '-', $no_rawat)) }}" wire:navigate class="hover:underline">Perawatan/Tindakan</a>
                    <span class="mx-1">/</span>
                    <span>Permintaan Rawat Inap</span>
                </nav>
                <h1 class="text-xl font-bold text-neutral-800 dark:text-neutral-100">Permintaan Rawat Inap</h1>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-neutral-800 rounded-xl border border-neutral-200 dark:border-neutral-700 p-6 shadow-sm">
        <div class="flex flex-col items-center justify-center py-12 text-center">
            <div class="w-16 h-16 bg-neutral-100 dark:bg-neutral-900 rounded-full flex items-center justify-center mb-4">
                <flux:icon name="document-text" class="w-8 h-8 text-neutral-400" />
            </div>
            <h3 class="text-lg font-bold text-neutral-800 dark:text-neutral-200 mb-1">Belum Ada Data</h3>
            <p class="text-sm text-neutral-500">Halaman ini akan berisi form dan data permintaan rawat inap.</p>
        </div>
    </div>
</div>
