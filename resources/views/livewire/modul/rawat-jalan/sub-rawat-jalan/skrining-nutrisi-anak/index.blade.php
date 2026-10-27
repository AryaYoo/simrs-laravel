<div class="flex flex-col gap-6 pb-8">
    {{-- Header / Breadcrumb --}}
    <div class="flex flex-col gap-1">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <button type="button" onclick="history.length > 1 ? history.back() : window.location.href = '{{ route('modul.rawat-jalan.index') }}'" class="flex items-center justify-center w-10 h-8 rounded-md bg-[#4C5C2D] transition-colors hover:bg-[#3d4b24] shadow-sm">
                    <flux:icon name="chevron-left" class="w-5 h-5 text-white" />
                </button>
                <div>
                    <nav class="text-xs text-neutral-400 mb-0.5">
                        <a href="{{ route('modul.rawat-jalan.perawatan-tindakan', str_replace('/', '-', $regPeriksa->no_rawat)) }}" wire:navigate class="hover:underline">Perawatan & Tindakan</a>
                        <span class="mx-1">/</span>
                        <span>RM Gizi</span>
                        <span class="mx-1">/</span>
                        <span class="text-neutral-600 dark:text-neutral-300 font-medium">Skrining Nutrisi Pasien Anak</span>
                    </nav>
                    <h1 class="text-xl font-bold text-neutral-800 dark:text-neutral-100">Skrining Nutrisi Pasien Anak</h1>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <flux:button type="button" icon="book-open" variant="subtle" size="sm" class="!text-neutral-600 dark:!text-neutral-300 hover:!text-[#4C5C2D]" title="Panduan SOP Skrining Gizi">
                    SOP
                </flux:button>
            </div>
        </div>
    </div>

    {{-- Patient Info Card --}}
    <div class="bg-white dark:bg-neutral-800 rounded-xl border border-neutral-200 dark:border-neutral-700 p-4 shadow-sm flex items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-[#4C5C2D]/10 dark:bg-[#4C5C2D]/30 flex items-center justify-center flex-shrink-0">
                <flux:icon name="user" class="w-6 h-6 text-[#4C5C2D] dark:text-[#8CC7C4]" />
            </div>
            <div>
                <h2 class="font-bold text-lg text-neutral-800 dark:text-neutral-100 leading-tight">
                    {{ $regPeriksa->pasien->nm_pasien ?? '-' }}
                </h2>
                <div class="flex flex-wrap items-center gap-x-3 gap-y-1 mt-1 text-sm text-neutral-500">
                    <span class="font-mono bg-neutral-100 dark:bg-neutral-900 px-1.5 py-0.5 rounded text-xs">{{ $regPeriksa->no_rkm_medis }}</span>
                    <span>•</span>
                    <span>{{ $regPeriksa->pasien->jk === 'L' ? 'Laki-laki' : 'Perempuan' }}</span>
                    <span>•</span>
                    <span>{{ $regPeriksa->pasien->tgl_lahir ? \Carbon\Carbon::parse($regPeriksa->pasien->tgl_lahir)->age . ' Thn' : '-' }}</span>
                    <span>•</span>
                    <span class="font-medium text-neutral-600 dark:text-neutral-300">{{ $regPeriksa->poliklinik->nm_poli ?? '-' }}</span>
                    <span>•</span>
                    <span class="font-medium text-neutral-600 dark:text-neutral-300">{{ $regPeriksa->dokter->nm_dokter ?? '-' }}</span>
                </div>
            </div>
        </div>
        <div class="text-right">
            <div class="text-xs text-neutral-400 mb-0.5">No. Rawat</div>
            <div class="font-mono font-bold text-[#4C5C2D] dark:text-[#8CC7C4] text-sm md:text-base">{{ $regPeriksa->no_rawat }}</div>
        </div>
    </div>

    {{-- Placeholder Container --}}
    <div class="bg-white dark:bg-neutral-800 rounded-2xl border border-neutral-200 dark:border-neutral-700 p-12 shadow-sm text-center">
        <div class="max-w-md mx-auto flex flex-col items-center">
            <div class="w-16 h-16 rounded-2xl bg-[#4C5C2D]/10 dark:bg-[#4C5C2D]/20 flex items-center justify-center text-[#4C5C2D] dark:text-[#8CC7C4] mb-4 shadow-inner">
                <flux:icon name="clipboard-document-check" class="w-8 h-8" />
            </div>
            <h3 class="text-lg font-bold text-neutral-800 dark:text-neutral-100 mb-2">
                Skrining Nutrisi Pasien Anak
            </h3>
            <p class="text-sm text-neutral-500 dark:text-neutral-400 mb-6 leading-relaxed">
                Halaman pengkajian skrining nutrisi pasien anak / pediatrik rawat jalan (Metode STRONG-kids). Formulir dan riwayat skrining nutrisi anak siap dikembangkan pada tahap selanjutnya.
            </p>
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-[#F1F5E9] dark:bg-neutral-700 text-[#4C5C2D] dark:text-[#8CC7C4] text-xs font-semibold">
                <span class="w-2 h-2 rounded-full bg-[#4C5C2D] animate-pulse"></span>
                Halaman Siap Digunakan (Placeholder)
            </div>
        </div>
    </div>
</div>
