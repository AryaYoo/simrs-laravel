<div class="flex flex-col gap-6 pb-8">
    {{-- Header / Breadcrumb --}}
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('modul.rawat-jalan.index') }}" wire:navigate
               class="flex items-center justify-center w-8 h-8 rounded-lg transition-colors hover:bg-neutral-100 dark:hover:bg-neutral-700">
                <flux:icon name="chevron-left" class="w-5 h-5 text-neutral-500" />
            </a>
            <div>
                <nav class="text-xs text-neutral-400 mb-0.5">
                    <a href="{{ route('modul.index') }}" wire:navigate class="hover:underline">Modul</a>
                    <span class="mx-1">/</span>
                    <a href="{{ route('modul.rawat-jalan.index') }}" wire:navigate class="hover:underline">Rawat Jalan</a>
                    <span class="mx-1">/</span>
                    <span>Perawatan & Tindakan</span>
                </nav>
                <h1 class="text-xl font-bold text-neutral-800 dark:text-neutral-100">Perawatan/Tindakan Pasien Rawat Jalan</h1>
            </div>
        </div>
    </div>

    {{-- Info Pasien Ringkas --}}
    <div class="bg-white dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl p-4 flex flex-col md:flex-row gap-4 justify-between items-start md:items-center shadow-sm">
        <div>
            <div class="font-bold text-lg text-[#4C5C2D] dark:text-[#8CC7C4]">
                {{ $regPeriksa->pasien->nm_pasien ?? '-' }}
                <span class="text-sm font-normal text-neutral-500 ml-2">({{ $regPeriksa->no_rkm_medis }})</span>
            </div>
            <div class="text-sm text-neutral-600 dark:text-neutral-400 mt-1">
                No. Rawat: <span class="font-medium text-neutral-800 dark:text-neutral-200">{{ $regPeriksa->no_rawat }}</span>
                <span class="mx-2">•</span>
                Poliklinik: <span class="font-medium text-neutral-800 dark:text-neutral-200">{{ $regPeriksa->poliklinik->nm_poli ?? '-' }}</span>
                <span class="mx-2">•</span>
                Dokter: <span class="font-medium text-neutral-800 dark:text-neutral-200">{{ $regPeriksa->dokter->nm_dokter ?? '-' }}</span>
            </div>
        </div>
    </div>

    {{-- Empty State Content (Untuk dikembangkan nanti) --}}
    <div class="bg-white dark:bg-neutral-800 rounded-xl border border-neutral-200 dark:border-neutral-700 p-12 flex flex-col items-center justify-center text-center shadow-sm">
        <div class="w-16 h-16 bg-neutral-100 dark:bg-neutral-700 rounded-full flex items-center justify-center mb-4">
            <flux:icon name="wrench-screwdriver" class="w-8 h-8 text-neutral-400 dark:text-neutral-500" />
        </div>
        <h3 class="text-lg font-bold text-neutral-700 dark:text-neutral-300">Halaman Perawatan & Tindakan Rawat Jalan</h3>
        <p class="text-neutral-500 dark:text-neutral-400 max-w-md mt-2 text-sm leading-relaxed">
            Halaman ini disiapkan untuk pencatatan tindakan dan perawatan pasien rawat jalan. Fitur akan diimplementasikan serupa dengan modul rawat inap.
        </p>
    </div>
</div>
