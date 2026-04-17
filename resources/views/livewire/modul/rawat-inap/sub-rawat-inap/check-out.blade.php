<div class="flex flex-col gap-6 pb-8">
    <div class="flex items-center gap-3">
        <a href="{{ route('modul.rawat-inap.show', str_replace('/', '-', $no_rawat)) }}" wire:navigate
           class="flex items-center justify-center w-8 h-8 rounded-lg transition-colors hover:bg-neutral-100 dark:hover:bg-neutral-700">
            <flux:icon name="chevron-left" class="w-5 h-5 text-neutral-500" />
        </a>
        <div>
            <nav class="text-xs text-neutral-400 mb-0.5">
                <a href="{{ route('modul.index') }}" wire:navigate class="hover:underline">Modul</a>
                <span class="mx-1">/</span>
                <a href="{{ route('modul.rawat-inap.index') }}" wire:navigate class="hover:underline">Rawat Inap</a>
                <span class="mx-1">/</span>
                <a href="{{ route('modul.rawat-inap.show', str_replace('/', '-', $no_rawat)) }}" wire:navigate class="hover:underline">Detail</a>
                <span class="mx-1">/</span>
                <span>Check Out</span>
            </nav>
            <h1 class="text-xl font-bold text-neutral-800 dark:text-neutral-100">Check Out Pasien</h1>
        </div>
    </div>

    <div class="bg-white dark:bg-neutral-800 rounded-xl border border-neutral-200 dark:border-neutral-700 p-8 flex flex-col items-center justify-center text-center">
        <flux:icon name="arrow-right-start-on-rectangle" class="w-16 h-16 text-[#6A7E3F] opacity-80 mb-4" />
        <h2 class="text-lg font-bold text-neutral-700 dark:text-neutral-200">Halaman Check Out Pasien</h2>
        <p class="text-neutral-500 dark:text-neutral-400 max-w-md mt-2">
            Halaman ini adalah placeholder untuk modul Check Out Pasien dengan nomor rawat <span class="font-mono font-bold">{{ $no_rawat }}</span>.
        </p>
    </div>
</div>
