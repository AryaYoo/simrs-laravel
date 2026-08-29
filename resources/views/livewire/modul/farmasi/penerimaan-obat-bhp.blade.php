<div class="flex flex-col gap-6 pb-12 max-w-7xl mx-auto w-full">
    {{-- Header & Breadcrumb dengan Tombol Back (SOP #8: history.back()) --}}
    <div class="flex items-center justify-between flex-wrap gap-4 bg-white dark:bg-neutral-800 p-4 rounded-xl border border-neutral-200 dark:border-neutral-700 shadow-sm sticky top-2 z-20">
        <div class="flex items-center gap-3">
            <button type="button" onclick="history.length > 1 ? history.back() : window.location.href = '{{ route('modul.farmasi.index') }}'"
                class="flex items-center justify-center w-10 h-8 rounded-md bg-[#4C5C2D] transition-colors hover:bg-[#3d4b24] shadow-sm"
                title="Kembali">
                <flux:icon name="chevron-left" class="w-5 h-5 text-white" />
            </button>
            <div>
                <nav class="text-xs text-neutral-400 mb-0.5">
                    <a href="{{ route('modul.index') }}" wire:navigate class="hover:underline">Modul</a>
                    <span class="mx-1">/</span>
                    <a href="{{ route('modul.farmasi.index') }}" wire:navigate class="hover:underline">Farmasi</a>
                    <span class="mx-1">/</span>
                    <span>Penerimaan Obat & BHP</span>
                </nav>
                <h1 class="text-lg font-bold text-neutral-800 dark:text-neutral-100 flex items-center gap-2">
                    Penerimaan Obat, Alkes dan BHP Medis
                </h1>
            </div>
        </div>
    </div>

    {{-- Content Placeholder Card --}}
    <div class="bg-white dark:bg-neutral-800 rounded-xl border border-neutral-200 dark:border-neutral-700 p-12 shadow-sm flex flex-col items-center justify-center text-center">
        <div class="w-16 h-16 rounded-2xl bg-[#4C5C2D]/10 text-[#4C5C2D] flex items-center justify-center mb-4">
            <flux:icon name="inbox-arrow-down" class="w-8 h-8" />
        </div>
        <h2 class="text-base font-bold text-neutral-800 dark:text-neutral-100 mb-1">
            Modul Penerimaan Obat, Alkes & BHP
        </h2>
        <p class="text-xs text-neutral-500 dark:text-neutral-400 max-w-md">
            Halaman penerimaan / pengadaan barang farmasi dari suplier siap untuk dikembangkan.
        </p>
    </div>
</div>
