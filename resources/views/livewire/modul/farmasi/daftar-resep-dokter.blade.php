<div class="flex flex-col gap-6 pb-8">
    {{-- Header / Breadcrumb --}}
    <div class="flex items-center justify-between">
        <div>
            <nav class="text-xs text-neutral-400 mb-0.5">
                <a href="{{ route('modul.index') }}" wire:navigate class="hover:underline">Modul</a>
                <span class="mx-1">/</span>
                <span>Farmasi</span>
                <span class="mx-1">/</span>
                <span>Daftar Resep Dokter</span>
            </nav>
            <h1 class="text-xl font-bold text-neutral-800 dark:text-neutral-100">Daftar Resep Dokter</h1>
        </div>
    </div>

    {{-- Coming Soon Placeholder --}}
    <div class="flex flex-col items-center justify-center py-24 gap-5">
        <div class="w-20 h-20 rounded-2xl flex items-center justify-center shadow-md"
             style="background-color: #4C5C2D;">
            <flux:icon name="clipboard-document-list" class="w-10 h-10 text-white" />
        </div>
        <div class="text-center">
            <h2 class="text-lg font-bold text-neutral-700 dark:text-neutral-200 mb-1">Halaman Sedang Dikembangkan</h2>
            <p class="text-sm text-neutral-400 dark:text-neutral-500 max-w-sm">
                Fitur <span class="font-semibold text-neutral-600 dark:text-neutral-300">Daftar Resep Dokter</span>
                akan segera tersedia.
            </p>
        </div>
        <a href="{{ route('modul.index') }}" wire:navigate
            class="mt-2 inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium text-white transition-colors"
            style="background-color: #4C5C2D;">
            <flux:icon name="arrow-left" class="w-4 h-4" />
            Kembali ke Modul
        </a>
    </div>
</div>
