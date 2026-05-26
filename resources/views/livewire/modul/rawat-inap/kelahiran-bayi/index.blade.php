<div class="flex flex-col gap-6 pb-8">
    {{-- Header / Breadcrumb --}}
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('modul.rawat-inap.index') }}" wire:navigate
               class="flex items-center justify-center w-8 h-8 rounded-lg transition-colors hover:bg-neutral-100 dark:hover:bg-neutral-700">
                <flux:icon name="chevron-left" class="w-5 h-5 text-neutral-500" />
            </a>
            <div>
                <nav class="text-xs text-neutral-400 mb-0.5">
                    <a href="{{ route('modul.index') }}" wire:navigate class="hover:underline">Modul</a>
                    <span class="mx-1">/</span>
                    <a href="{{ route('modul.rawat-inap.index') }}" wire:navigate class="hover:underline">Rawat Inap</a>
                    <span class="mx-1">/</span>
                    <span>Kelahiran Bayi</span>
                </nav>
                <h1 class="text-xl font-bold text-neutral-800 dark:text-neutral-100">Data Kelahiran Bayi</h1>
            </div>
        </div>
    </div>

    {{-- Main Content Area --}}
    <div class="bg-white dark:bg-neutral-800 rounded-xl border border-neutral-200 dark:border-neutral-700 p-8 shadow-sm">
        <div class="flex flex-col items-center justify-center text-center py-12">
            <div class="w-16 h-16 bg-[#4C5C2D]/10 text-[#4C5C2D] dark:bg-[#8CC7C4]/10 dark:text-[#8CC7C4] rounded-full flex items-center justify-center mb-4">
                <flux:icon name="face-smile" class="w-8 h-8" />
            </div>
            <h2 class="text-lg font-bold text-neutral-800 dark:text-neutral-100 mb-2">Halaman Sedang Dalam Pengembangan</h2>
            <p class="text-sm text-neutral-500 max-w-md">
                Modul Kelahiran Bayi saat ini masih dalam tahap perancangan dan akan segera tersedia pada pembaruan berikutnya.
            </p>
        </div>
    </div>
</div>
