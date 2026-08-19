<div class="flex flex-col gap-6 pb-8">
    {{-- Header / Breadcrumb dengan Tombol Back --}}
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
                    <span>Penyerahan Resep Obat Rawat Jalan</span>
                </nav>
                <h1 class="text-xl font-bold text-neutral-800 dark:text-neutral-100 flex items-center gap-2">
                    Penyerahan Resep Obat Rawat Jalan
                </h1>
            </div>
        </div>
    </div>

    {{-- Premium Coming Soon Placeholder --}}
    <div class="bg-white dark:bg-neutral-800 rounded-2xl border border-neutral-200 dark:border-neutral-700 shadow-sm p-12 flex flex-col items-center justify-center min-h-[450px] relative overflow-hidden">
        {{-- Decorative background details --}}
        <div class="absolute -right-16 -top-16 w-48 h-48 rounded-full bg-[#4C5C2D]/5 blur-3xl"></div>
        <div class="absolute -left-16 -bottom-16 w-48 h-48 rounded-full bg-[#4C5C2D]/5 blur-3xl"></div>

        <div class="w-20 h-20 rounded-2xl flex items-center justify-center shadow-lg transition-transform hover:scale-105 duration-300 relative group"
             style="background: linear-gradient(135deg, #4C5C2D 0%, #3D4A24 100%);">
            <flux:icon name="paper-airplane" class="w-10 h-10 text-white animate-pulse" />
        </div>
        
        <div class="text-center mt-6 max-w-md">
            <h2 class="text-lg font-bold text-neutral-700 dark:text-neutral-200 mb-2">Halaman Sedang Dikembangkan</h2>
            <p class="text-xs text-neutral-400 dark:text-neutral-500 leading-relaxed">
                Fitur <span class="font-bold text-[#4C5C2D] dark:text-[#8CC7C4]">Penyerahan Resep Obat Rawat Jalan</span> sedang dalam tahap perancangan dan integrasi sistem.
                @if($no_resep)
                    <span class="block mt-2 font-mono text-[10px] bg-neutral-100 dark:bg-neutral-900 px-2.5 py-1 rounded text-neutral-500">No. Resep Terkait: {{ $no_resep }}</span>
                @endif
            </p>
        </div>

        <a href="{{ route('modul.farmasi.daftar-resep-dokter') }}" wire:navigate
            class="mt-8 inline-flex items-center gap-2 px-5 py-2.5 rounded-lg text-xs font-bold text-white transition-all shadow-md hover:shadow-lg active:scale-95"
            style="background-color: #4C5C2D;">
            <flux:icon name="arrow-left" class="w-3.5 h-3.5" />
            Kembali ke Daftar Resep
        </a>
    </div>
</div>
