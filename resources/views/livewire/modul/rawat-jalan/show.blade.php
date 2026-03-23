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
                    <span>Detail</span>
                </nav>
                <h1 class="text-xl font-bold text-neutral-800 dark:text-neutral-100">Detail Pasien Rawat Jalan</h1>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- Data Pasien & Registrasi --}}
        <div class="bg-white dark:bg-neutral-800 rounded-xl border border-neutral-200 dark:border-neutral-700 overflow-hidden">
            <div class="px-5 py-4 border-b border-neutral-100 dark:border-neutral-700 bg-neutral-50/50 dark:bg-neutral-800/50">
                <h3 class="font-bold text-neutral-700 dark:text-neutral-200">Informasi Registrasi</h3>
            </div>
            <div class="p-5 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <flux:description>
                        <flux:label>Nomor Rawat</flux:label>
                        <div class="text-neutral-800 dark:text-neutral-100 font-medium">{{ $regPeriksa->no_rawat }}</div>
                    </flux:description>
                    <flux:description>
                        <flux:label>Nomor Registrasi</flux:label>
                        <div class="text-neutral-800 dark:text-neutral-100 font-medium">{{ $regPeriksa->no_reg }}</div>
                    </flux:description>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <flux:description>
                        <flux:label>Tanggal & Jam</flux:label>
                        <div class="text-neutral-800 dark:text-neutral-100">{{ $regPeriksa->tgl_registrasi }} {{ $regPeriksa->jam_reg }}</div>
                    </flux:description>
                    <flux:description>
                        <flux:label>Biaya Registrasi</flux:label>
                        <div class="text-neutral-800 dark:text-neutral-100 font-medium">Rp {{ number_format($regPeriksa->biaya_reg, 0, ',', '.') }}</div>
                    </flux:description>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <flux:description>
                        <flux:label>Status Daftar</flux:label>
                        <div class="text-neutral-800 dark:text-neutral-100">{{ $regPeriksa->stts_daftar }}</div>
                    </flux:description>
                    <flux:description>
                        <flux:label>Status</flux:label>
                        <div class="text-neutral-800 dark:text-neutral-100">{{ $regPeriksa->stts }}</div>
                    </flux:description>
                </div>

                <flux:description>
                    <flux:label>Dokter Tujuan</flux:label>
                    <div class="text-neutral-800 dark:text-neutral-100 font-medium">[{{ $regPeriksa->kd_dokter }}] {{ $regPeriksa->dokter->nm_dokter ?? '-' }}</div>
                </flux:description>

                <flux:description>
                    <flux:label>Poliklinik</flux:label>
                    <div class="text-neutral-800 dark:text-neutral-100 font-medium">{{ $regPeriksa->poliklinik->nm_poli ?? '-' }}</div>
                </flux:description>

                <div class="grid grid-cols-2 gap-4 pt-2 border-t border-neutral-100 dark:border-neutral-700">
                    <flux:description>
                        <flux:label>Jenis Bayar</flux:label>
                        <div class="text-neutral-800 dark:text-neutral-100">{{ $regPeriksa->penjab->png_jawab ?? '-' }}</div>
                    </flux:description>
                    <flux:description>
                        <flux:label>Status Bayar</flux:label>
                        <div class="text-neutral-800 dark:text-neutral-100">
                            <flux:badge size="sm" color="{{ $regPeriksa->status_bayar === 'Sudah Bayar' ? 'green' : 'red' }}">
                                {{ $regPeriksa->status_bayar }}
                            </flux:badge>
                        </div>
                    </flux:description>
                </div>
            </div>
        </div>

        {{-- Detail Pasien & Pj --}}
        <div class="bg-white dark:bg-neutral-800 rounded-xl border border-neutral-200 dark:border-neutral-700 overflow-hidden">
            <div class="px-5 py-4 border-b border-neutral-100 dark:border-neutral-700 bg-neutral-50/50 dark:bg-neutral-800/50">
                <h3 class="font-bold text-neutral-700 dark:text-neutral-200">Informasi Pasien & Pj</h3>
            </div>
            <div class="p-5 space-y-4">
                <flux:description>
                    <flux:label>Nomor Rekam Medis</flux:label>
                    <div class="text-neutral-800 dark:text-neutral-100 font-medium">{{ $regPeriksa->no_rkm_medis }}</div>
                </flux:description>

                <flux:description>
                    <flux:label>Pasien</flux:label>
                    <div class="text-neutral-800 dark:text-neutral-100 font-medium">{{ $regPeriksa->pasien->nm_pasien ?? '-' }}</div>
                </flux:description>

                <flux:description>
                    <flux:label>Nomor Telepon Pasien</flux:label>
                    <div class="text-neutral-800 dark:text-neutral-100">{{ $regPeriksa->pasien->no_tlp ?? '-' }}</div>
                </flux:description>

                <div class="pt-4 border-t border-neutral-100 dark:border-neutral-700 space-y-4">
                    <flux:description>
                        <flux:label>Penanggung Jawab</flux:label>
                        <div class="text-neutral-800 dark:text-neutral-100 font-medium">{{ $regPeriksa->p_jawab }}</div>
                    </flux:description>

                    <flux:description>
                        <flux:label>Alamat Penanggung Jawab</flux:label>
                        <div class="text-neutral-800 dark:text-neutral-100">{{ $regPeriksa->almt_pj }}</div>
                    </flux:description>

                    <flux:description>
                        <flux:label>Hubungan Penanggung Jawab</flux:label>
                        <div class="text-neutral-800 dark:text-neutral-100">{{ $regPeriksa->hubunganpj }}</div>
                    </flux:description>
                </div>
            </div>
        </div>
    </div>
</div>
