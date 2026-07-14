<div class="flex flex-col gap-6 pb-8">
    {{-- Header / Breadcrumb --}}
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div>
                <nav class="text-xs text-neutral-400 mb-0.5">
                    <a href="{{ route('modul.index') }}" wire:navigate class="hover:underline">Modul</a>
                    <span class="mx-1">/</span>
                    <a href="{{ route('modul.rawat-inap.index') }}" wire:navigate class="hover:underline">Rawat Inap</a>
                    <span class="mx-1">/</span>
                    <span>Permintaan Rawat Inap</span>
                </nav>
                <h1 class="text-xl font-bold text-neutral-800 dark:text-neutral-100">Permintaan Rawat Inap</h1>
            </div>
        </div>
    </div>

    <div
        class="bg-white dark:bg-neutral-800 rounded-xl border border-neutral-200 dark:border-neutral-700 shadow-sm p-6 text-center">
        <flux:icon name="document-text" class="w-12 h-12 mx-auto text-neutral-300 dark:text-neutral-600 mb-4" />
        <h2 class="text-lg font-semibold text-neutral-700 dark:text-neutral-300 mb-2">Halaman Permintaan Rawat Inap</h2>
        <p class="text-sm text-neutral-500 dark:text-neutral-400"></p>
    </div>Halaman ini masih dalam proses pengembangan. Data permintaan rawat inap akan ditampilkan di sini.
</div>