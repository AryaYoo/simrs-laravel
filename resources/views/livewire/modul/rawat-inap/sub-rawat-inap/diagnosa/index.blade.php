<div class="flex flex-col gap-6 pb-24">
    {{-- Header / Breadcrumb --}}
    <div class="flex flex-col gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('modul.rawat-inap.perawatan-tindakan', str_replace('/', '-', $no_rawat)) }}"
                class="flex items-center justify-center w-10 h-8 rounded-md bg-[#4C5C2D] transition-colors hover:bg-[#3d4b24] shadow-sm">
                <flux:icon name="chevron-left" class="w-5 h-5 text-white" />
            </a>
            <div>
                <nav class="text-xs text-neutral-400 mb-0.5">
                    <span>Modul</span>
                    <span class="mx-1">/</span>
                    <span>Rawat Inap</span>
                    <span class="mx-1">/</span>
                    <span>Diagnosa</span>
                </nav>
                <h1 class="text-xl font-bold text-neutral-800 dark:text-neutral-100">Diagnosa</h1>
            </div>
        </div>
    </div>

    {{-- Content --}}
    <div class="bg-white dark:bg-neutral-800 rounded-xl border border-neutral-200 dark:border-neutral-700 p-8 shadow-sm">
        <div class="flex flex-col items-center justify-center text-center py-16">
            <div class="w-16 h-16 bg-neutral-100 dark:bg-neutral-700 rounded-full flex items-center justify-center mb-4">
                <flux:icon name="document-text" class="w-8 h-8 text-neutral-400 dark:text-neutral-500" />
            </div>
            <h2 class="text-lg font-bold text-neutral-800 dark:text-neutral-200 mb-2">Halaman Diagnosa</h2>
            <p class="text-sm text-neutral-500 dark:text-neutral-400 max-w-md">
                Halaman ini masih kosong dan dalam tahap pengembangan.
            </p>
        </div>
    </div>
</div>
