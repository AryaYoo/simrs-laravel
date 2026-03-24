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
                <span>Detail</span>
            </nav>
            <h1 class="text-xl font-bold text-neutral-800 dark:text-neutral-100">Detail Registrasi Pasien</h1>
        </div>
    </div>

    {{-- Patient Identity Hero --}}
    <div class="rounded-2xl overflow-hidden shadow-sm border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800">
        <div class="h-2 w-full" style="background: linear-gradient(90deg, #4C5C2D 0%, #6A7E3F 100%);"></div>
        <div class="p-6 flex flex-col sm:flex-row items-start sm:items-center gap-5">
            {{-- Avatar --}}
            <div class="flex-shrink-0 w-16 h-16 rounded-full flex items-center justify-center text-xl font-bold text-white"
                 style="background: linear-gradient(135deg, #4C5C2D, #6A7E3F);">
                {{ strtoupper(substr($regPeriksa->pasien->nm_pasien ?? 'P', 0, 1)) }}
            </div>
            {{-- Info utama --}}
            <div class="flex-1 min-w-0">
                <p class="text-xl font-bold text-neutral-800 dark:text-neutral-100 truncate">
                    {{ $regPeriksa->pasien->nm_pasien ?? '-' }}
                </p>
                <div class="flex flex-wrap items-center gap-3 mt-1 text-sm text-neutral-500 dark:text-neutral-400">
                    <span class="flex items-center gap-1">
                        <flux:icon name="identification" class="w-4 h-4" />
                        RM: <strong class="text-neutral-700 dark:text-neutral-200 ml-1">{{ $regPeriksa->no_rkm_medis }}</strong>
                    </span>
                    <span class="flex items-center gap-1">
                        <flux:icon name="user" class="w-4 h-4" />
                        {{ $regPeriksa->pasien->jk === 'L' ? 'Laki-laki' : ($regPeriksa->pasien->jk === 'P' ? 'Perempuan' : ($regPeriksa->pasien->jk ?? '-')) }}
                    </span>
                    <span class="flex items-center gap-1">
                        <flux:icon name="phone" class="w-4 h-4" />
                        {{ $regPeriksa->pasien->no_telp ?? '-' }}
                    </span>
                </div>
            </div>
            {{-- Badges --}}
            <div class="flex flex-wrap gap-2 self-start sm:self-center">
                @if($regPeriksa->status_bayar === 'Sudah Bayar')
                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700 border border-green-200 dark:bg-green-900/40 dark:text-green-300 dark:border-green-800">
                        <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>{{ $regPeriksa->status_bayar }}
                    </span>
                @else
                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700 border border-red-200 dark:bg-red-900/40 dark:text-red-300 dark:border-red-800">
                        <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>{{ $regPeriksa->status_bayar }}
                    </span>
                @endif
                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700 border border-blue-200 dark:bg-blue-900/40 dark:text-blue-300 dark:border-blue-800">
                    {{ $regPeriksa->stts_daftar }}
                </span>
            </div>
        </div>
    </div>

    {{-- Content Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

        {{-- Informasi Registrasi --}}
        <div class="rounded-2xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-neutral-100 dark:border-neutral-700 flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg flex items-center justify-center" style="background-color: rgba(76,92,45,0.1);">
                    <flux:icon name="clipboard-document-list" class="w-4 h-4" style="color: #4C5C2D;" />
                </div>
                <h2 class="font-semibold text-sm text-neutral-700 dark:text-neutral-200">Informasi Registrasi</h2>
            </div>
            <div class="p-5 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    @include('partials._field', ['label' => 'No Registrasi', 'value' => $regPeriksa->no_reg])
                    @include('partials._field', ['label' => 'No Rawat', 'value' => $regPeriksa->no_rawat, 'bold' => true])
                </div>
                <div class="grid grid-cols-2 gap-4">
                    @include('partials._field', ['label' => 'Tanggal', 'value' => $regPeriksa->tgl_registrasi])
                    @include('partials._field', ['label' => 'Jam', 'value' => $regPeriksa->jam_reg])
                </div>
                <div class="grid grid-cols-2 gap-4">
                    @include('partials._field', ['label' => 'Kode Dokter', 'value' => $regPeriksa->kd_dokter])
                    @include('partials._field', ['label' => 'Dokter', 'value' => $regPeriksa->dokter->nm_dokter ?? '-'])
                </div>
            </div>
        </div>

        {{-- Informasi Pembayaran --}}
        <div class="rounded-2xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-neutral-100 dark:border-neutral-700 flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg flex items-center justify-center" style="background-color: rgba(76,92,45,0.1);">
                    <flux:icon name="banknotes" class="w-4 h-4" style="color: #4C5C2D;" />
                </div>
                <h2 class="font-semibold text-sm text-neutral-700 dark:text-neutral-200">Pembayaran</h2>
            </div>
            <div class="p-5 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    @include('partials._field', ['label' => 'Kode Bayar', 'value' => $regPeriksa->kd_pj])
                    @include('partials._field', ['label' => 'Jenis Bayar', 'value' => $regPeriksa->penjab->png_jawab ?? '-'])
                </div>
                <div class="grid grid-cols-2 gap-4">
                    @include('partials._field', ['label' => 'Biaya Registrasi', 'value' => 'Rp ' . number_format($regPeriksa->biaya_reg, 0, ',', '.')])
                    @include('partials._field', ['label' => 'Penanggung Jawab', 'value' => $regPeriksa->p_jawab ?: '-'])
                </div>
                <div class="grid grid-cols-2 gap-4">
                    @include('partials._field', ['label' => 'Hubungan PJ', 'value' => $regPeriksa->hubungan_pj ?: '-'])
                    @include('partials._field', ['label' => 'Alamat PJ', 'value' => $regPeriksa->almt_pj ?: '-'])
                </div>
            </div>
        </div>

        {{-- Status --}}
        <div class="rounded-2xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 shadow-sm overflow-hidden lg:col-span-2">
            <div class="px-5 py-4 border-b border-neutral-100 dark:border-neutral-700 flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg flex items-center justify-center" style="background-color: rgba(76,92,45,0.1);">
                    <flux:icon name="chart-bar" class="w-4 h-4" style="color: #4C5C2D;" />
                </div>
                <h2 class="font-semibold text-sm text-neutral-700 dark:text-neutral-200">Status</h2>
            </div>
            <div class="p-5">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                    {{-- Status Daftar --}}
                    <div class="flex flex-col gap-1">
                        <p class="text-xs font-semibold uppercase tracking-wider text-neutral-400">Status Daftar</p>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200 dark:bg-blue-900/30 dark:text-blue-300 dark:border-blue-800 w-fit">
                            {{ $regPeriksa->stts_daftar ?: '-' }}
                        </span>
                    </div>
                    {{-- Status Rawat --}}
                    <div class="flex flex-col gap-1">
                        <p class="text-xs font-semibold uppercase tracking-wider text-neutral-400">Status Rawat</p>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-purple-50 text-purple-700 border border-purple-200 dark:bg-purple-900/30 dark:text-purple-300 dark:border-purple-800 w-fit">
                            {{ $regPeriksa->stts ?: '-' }}
                        </span>
                    </div>
                    {{-- Status Poli --}}
                    <div class="flex flex-col gap-1">
                        <p class="text-xs font-semibold uppercase tracking-wider text-neutral-400">Status Poli</p>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-yellow-50 text-yellow-700 border border-yellow-200 dark:bg-yellow-900/30 dark:text-yellow-300 dark:border-yellow-800 w-fit">
                            {{ $regPeriksa->stts_poli ?: '-' }}
                        </span>
                    </div>
                    {{-- Status Bayar --}}
                    <div class="flex flex-col gap-1">
                        <p class="text-xs font-semibold uppercase tracking-wider text-neutral-400">Status Bayar</p>
                        @if($regPeriksa->status_bayar === 'Sudah Bayar')
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-green-50 text-green-700 border border-green-200 dark:bg-green-900/30 dark:text-green-300 dark:border-green-800 w-fit">
                                <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>{{ $regPeriksa->status_bayar }}
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-red-50 text-red-700 border border-red-200 dark:bg-red-900/30 dark:text-red-300 dark:border-red-800 w-fit">
                                <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>{{ $regPeriksa->status_bayar }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
