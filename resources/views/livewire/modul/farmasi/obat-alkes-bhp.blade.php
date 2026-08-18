<div class="flex flex-col gap-6 pb-8">
    {{-- Header & Breadcrumb dengan Tombol Back (SOP #8: history.back()) --}}
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center gap-3">
            <button type="button" onclick="history.back()" class="flex items-center justify-center w-10 h-8 rounded-md bg-[#4C5C2D] transition-colors hover:bg-[#3d4b24] shadow-sm" title="Kembali">
                <flux:icon name="chevron-left" class="w-5 h-5 text-white" />
            </button>
            <div>
                <nav class="text-xs text-neutral-400 mb-0.5">
                    <a href="{{ route('modul.index') }}" wire:navigate class="hover:underline">Modul</a>
                    <span class="mx-1">/</span>
                    <span>Farmasi</span>
                    <span class="mx-1">/</span>
                    <a href="{{ route('modul.farmasi.daftar-resep-dokter') }}" wire:navigate class="hover:underline">Daftar Resep Dokter</a>
                    <span class="mx-1">/</span>
                    <span>Data Obat, Alkes dan BHP Medis</span>
                </nav>
                <h1 class="text-xl font-bold text-neutral-800 dark:text-neutral-100 flex items-center gap-2">
                    Data Obat, Alkes dan BHP Medis
                </h1>
            </div>
        </div>
    </div>

    {{-- Content / Empty State --}}
    <div class="bg-white dark:bg-neutral-800 rounded-xl border border-neutral-200 dark:border-neutral-700 p-12 text-center flex flex-col items-center justify-center min-h-[400px] shadow-sm">
        <div class="w-20 h-20 rounded-2xl bg-[#4C5C2D]/10 text-[#4C5C2D] dark:bg-[#8CC7C4]/20 dark:text-[#8CC7C4] flex items-center justify-center mb-4 shadow-sm">
            <flux:icon name="check-badge" class="w-10 h-10" />
        </div>
        <h3 class="text-lg font-bold text-neutral-800 dark:text-neutral-100">Validasi Data Obat, Alkes dan BHP Medis</h3>
        <p class="text-xs text-neutral-500 dark:text-neutral-400 max-w-md mt-1.5 leading-relaxed">
            Halaman ini disiapkan untuk validasi Data Obat, Alkes, dan Bahan Habis Pakai (BHP) Medis.
        </p>
    </div>
</div>
