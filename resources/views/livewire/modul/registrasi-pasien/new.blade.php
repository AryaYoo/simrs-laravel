<div class="flex flex-col gap-6 pb-8">
    {{-- Header / Breadcrumb --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('modul.registrasi-pasien.index') }}" wire:navigate
           class="flex items-center justify-center w-8 h-8 rounded-lg transition-colors hover:bg-neutral-100 dark:hover:bg-neutral-700">
            <flux:icon name="chevron-left" class="w-5 h-5 text-neutral-500" />
        </a>
        <div>
            <nav class="text-xs text-neutral-400 mb-0.5">
                <a href="{{ route('modul.index') }}" wire:navigate class="hover:underline">Modul</a>
                <span class="mx-1">/</span>
                <a href="{{ route('modul.registrasi-pasien.index') }}" wire:navigate class="hover:underline">Registrasi</a>
                <span class="mx-1">/</span>
                <span>Master Pasien</span>
            </nav>
            <h1 class="text-xl font-bold text-neutral-800 dark:text-neutral-100">Tambah Pasien Baru</h1>
        </div>
    </div>

    <div class="bg-white dark:bg-neutral-800 rounded-xl border border-neutral-200 dark:border-neutral-700 p-6">
        <div class="flex flex-col items-center justify-center py-12 text-neutral-400 dark:text-neutral-500">
            <flux:icon name="user-plus" class="w-12 h-12 mb-3 opacity-40" />
            <p class="text-sm font-medium">Halaman Form Tambah Data Pasien Baru (Dalam Pengembangan)</p>
        </div>
    </div>
</div>
