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
                    <span>Detail</span>
                </nav>
                <h1 class="text-xl font-bold text-neutral-800 dark:text-neutral-100">Detail Pasien Rawat Inap</h1>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <flux:button icon="arrow-path" :href="route('modul.rawat-inap.sub-rawat-inap.pindah', str_replace('/', '-', $no_rawat))" wire:navigate
                style="background-color: #4C5C2D; color: white; border: none; font-weight: 700; padding-left: 1.25rem; padding-right: 1.25rem;">Pindah</flux:button>
            <flux:button icon="arrow-right-start-on-rectangle" :href="route('modul.rawat-inap.sub-rawat-inap.pulang', str_replace('/', '-', $no_rawat))" wire:navigate
                style="background-color: #6A7E3F; color: white; border: none; font-weight: 700; padding-left: 1.25rem; padding-right: 1.25rem;">Pulang</flux:button>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- Data Pasien & Registrasi --}}
        <div class="bg-white dark:bg-neutral-800 rounded-xl border border-neutral-200 dark:border-neutral-700 overflow-hidden">
            <div class="px-5 py-4 border-b border-neutral-100 dark:border-neutral-700 bg-neutral-50/50 dark:bg-neutral-800/50">
                <h3 class="font-bold text-neutral-700 dark:text-neutral-200">Informasi Pasien</h3>
            </div>
            <div class="p-5 space-y-4">
                <flux:description>
                    <flux:label>Nomor Rawat</flux:label>
                    <div class="text-neutral-800 dark:text-neutral-100 font-medium">{{ $regPeriksa->no_rawat }}</div>
                </flux:description>

                <flux:description>
                    <flux:label>Nomor Rekam Medis</flux:label>
                    <div class="text-neutral-800 dark:text-neutral-100 font-medium">{{ $regPeriksa->no_rkm_medis }}</div>
                </flux:description>

                <flux:description>
                    <flux:label>Nama Pasien</flux:label>
                    <div class="text-neutral-800 dark:text-neutral-100 font-medium">{{ $regPeriksa->pasien->nm_pasien ?? '-' }}</div>
                </flux:description>

                <flux:description>
                    <flux:label>Alamat Pasien</flux:label>
                    <div class="text-neutral-800 dark:text-neutral-100">{{ $regPeriksa->pasien->alamat ?? '-' }}</div>
                </flux:description>

                <flux:description>
                    <flux:label>Agama</flux:label>
                    <div class="text-neutral-800 dark:text-neutral-100">{{ $regPeriksa->pasien->agama ?? '-' }}</div>
                </flux:description>

                <div class="grid grid-cols-2 gap-4">
                    <flux:description>
                        <flux:label>Penanggung Jawab</flux:label>
                        <div class="text-neutral-800 dark:text-neutral-100">{{ $regPeriksa->p_jawab }}</div>
                    </flux:description>
                    <flux:description>
                        <flux:label>Hubungan</flux:label>
                        <div class="text-neutral-800 dark:text-neutral-100">{{ $regPeriksa->hubunganpj }}</div>
                    </flux:description>
                </div>

                <div class="grid grid-cols-2 gap-4">
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

                <flux:description>
                    <flux:label>Dokter Penanggung Jawab</flux:label>
                    <div class="text-neutral-800 dark:text-neutral-100 font-medium">{{ $regPeriksa->dokter->nm_dokter ?? '-' }}</div>
                </flux:description>
            </div>
        </div>

        {{-- Detail Kamar & Inap --}}
        <div class="bg-white dark:bg-neutral-800 rounded-xl border border-neutral-200 dark:border-neutral-700 overflow-hidden">
            <div class="px-5 py-4 border-b border-neutral-100 dark:border-neutral-700 bg-neutral-50/50 dark:bg-neutral-800/50">
                <h3 class="font-bold text-neutral-700 dark:text-neutral-200">Informasi Kamar & Inap</h3>
            </div>
            
            @php
                // Kamar inap is a hasMany relation, get the last (most recent) record
                $kamarInap = $regPeriksa->kamarInap->last();
            @endphp
            
            <div class="p-5 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <flux:description>
                        <flux:label>Kamar</flux:label>
                        <div class="text-neutral-800 dark:text-neutral-100 font-medium">{{ $regPeriksa->permintaanRanap->kd_kamar ?? ($kamarInap->kd_kamar ?? '-') }}</div>
                    </flux:description>
                    <flux:description>
                        <flux:label>Tarif Kamar</flux:label>
                        <div class="text-neutral-800 dark:text-neutral-100 font-medium">Rp {{ number_format($kamarInap->kamar->trf_kamar ?? ($regPeriksa->permintaanRanap->kamar->trf_kamar ?? 0), 0, ',', '.') }}</div>
                    </flux:description>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <flux:description>
                        <flux:label>Tanggal Masuk</flux:label>
                        <div class="text-neutral-800 dark:text-neutral-100">{{ $kamarInap->tgl_masuk ?? '-' }} {{ $kamarInap->jam_masuk ?? '' }}</div>
                    </flux:description>
                    <flux:description>
                        <flux:label>Tanggal Keluar</flux:label>
                        <div class="text-neutral-800 dark:text-neutral-100">{{ $kamarInap->tgl_keluar ?? '-' }} {{ $kamarInap->jam_keluar ?? '' }}</div>
                    </flux:description>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <flux:description>
                        <flux:label>Lama Inap</flux:label>
                        <div class="text-neutral-800 dark:text-neutral-100">{{ $kamarInap->lama ?? 0 }} Hari</div>
                    </flux:description>
                    <flux:description>
                        <flux:label>Status Pulang</flux:label>
                        <div class="text-neutral-800 dark:text-neutral-100">{{ $kamarInap->stts_pulang ?? '-' }}</div>
                    </flux:description>
                </div>

                <flux:description>
                    <flux:label>Diagnosa Awal</flux:label>
                    <div class="text-neutral-800 dark:text-neutral-100">{{ $kamarInap->diagnosa_awal ?? '-' }}</div>
                </flux:description>

                <flux:description>
                    <flux:label>Diagnosa Akhir</flux:label>
                    <div class="text-neutral-800 dark:text-neutral-100">{{ $kamarInap->diagnosa_akhir ?? '-' }}</div>
                </flux:description>

                <div class="pt-4 border-t border-neutral-100 dark:border-neutral-700">
                    <flux:description>
                        <flux:label>Total Biaya Kamar</flux:label>
                        <div class="text-2xl font-bold text-neutral-800 dark:text-neutral-100">Rp {{ number_format($kamarInap->ttl_biaya ?? 0, 0, ',', '.') }}</div>
                    </flux:description>
                </div>
            </div>
        </div>
    </div>
</div>
