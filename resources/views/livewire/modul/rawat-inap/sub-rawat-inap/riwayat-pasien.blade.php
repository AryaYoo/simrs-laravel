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
                    <span>Riwayat Pasien</span>
                </nav>
                <h1 class="text-xl font-bold text-neutral-800 dark:text-neutral-100">Riwayat Pasien</h1>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        {{-- Patient Info Card --}}
        <div class="md:col-span-1">
            <div class="bg-white dark:bg-neutral-800 rounded-xl border border-neutral-200 dark:border-neutral-700 p-6 shadow-sm">
                <div class="flex flex-col items-center mb-6">
                    <div class="w-24 h-24 rounded-full bg-neutral-100 dark:bg-neutral-700 flex items-center justify-center mb-4">
                        <flux:icon name="user" class="w-12 h-12 text-neutral-400" />
                    </div>
                    <h2 class="text-lg font-bold text-neutral-800 dark:text-neutral-100 text-center">{{ $regPeriksa->pasien->nm_pasien }}</h2>
                    <p class="text-sm text-neutral-500">{{ $regPeriksa->no_rkm_medis }}</p>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="text-xs font-semibold text-neutral-400 uppercase tracking-wider">Jenis Kelamin</label>
                        <p class="text-sm text-neutral-700 dark:text-neutral-300">
                            {{ $regPeriksa->pasien->jk == 'L' ? 'Laki-laki' : 'Perempuan' }}
                        </p>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-neutral-400 uppercase tracking-wider">Tempat, Tanggal Lahir</label>
                        <p class="text-sm text-neutral-700 dark:text-neutral-300">
                            {{ $regPeriksa->pasien->tmp_lahir }}, {{ \Carbon\Carbon::parse($regPeriksa->pasien->tgl_lahir)->format('d F Y') }}
                        </p>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-neutral-400 uppercase tracking-wider">Alamat</label>
                        <p class="text-sm text-neutral-700 dark:text-neutral-300 whitespace-pre-line">
                            {{ $regPeriksa->pasien->alamat }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Main History Content --}}
        <div class="md:col-span-2">
            <div class="bg-white dark:bg-neutral-800 rounded-xl border border-neutral-200 dark:border-neutral-700 p-6 shadow-sm min-h-[400px]">
                <h3 class="text-md font-bold text-neutral-800 dark:text-neutral-100 mb-4">Detail Riwayat</h3>
                <div class="flex flex-col items-center justify-center py-20 text-neutral-400">
                    <flux:icon name="clock" class="w-12 h-12 mb-3 opacity-20" />
                    <p class="text-sm">Riwayat pemeriksaan lainnya akan ditampilkan di sini.</p>
                </div>
            </div>
        </div>
    </div>
</div>
