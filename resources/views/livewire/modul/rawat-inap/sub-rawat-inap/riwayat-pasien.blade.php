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

    {{-- Professional Patient Info Card --}}
    <div class="max-w-3xl" x-data="{ minimized: false, isFloating: false }" @scroll.window="isFloating = window.scrollY > 200">
        <div class="bg-white dark:bg-neutral-800 rounded-2xl border border-neutral-200 dark:border-neutral-700 shadow-sm overflow-hidden relative">
            {{-- Accent Line --}}
            <div class="absolute top-0 left-0 w-1.5 h-full bg-[#4C5C2D] dark:bg-[#8CC7C4]"></div>

            {{-- Card Header (always visible) --}}
            <div class="flex items-center justify-between px-6 sm:px-7 pt-5 pb-0">
                <div class="flex items-center gap-4 pl-1">
                    <div class="w-10 h-10 rounded-full bg-[#4C5C2D]/10 dark:bg-[#8CC7C4]/10 hidden sm:flex items-center justify-center shrink-0 border border-[#4C5C2D]/20 dark:border-[#8CC7C4]/20">
                        <flux:icon name="user" class="w-5 h-5 text-[#4C5C2D] dark:text-[#8CC7C4]" />
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-neutral-800 dark:text-neutral-100 leading-tight">{{ $regPeriksa->pasien->nm_pasien }}</h2>
                        <div class="flex flex-wrap items-center gap-2 mt-1">
                            <span class="inline-flex items-center rounded bg-neutral-100 dark:bg-neutral-700 px-1.5 py-0.5 text-[11px] font-semibold text-neutral-600 dark:text-neutral-300">
                                RM: {{ $regPeriksa->no_rkm_medis }}
                            </span>
                            <span class="inline-flex items-center rounded bg-sky-50 dark:bg-sky-500/10 px-1.5 py-0.5 text-[11px] font-semibold text-sky-700 dark:text-sky-400">
                                {{ $regPeriksa->pasien->jk == 'L' ? 'Laki-laki' : 'Perempuan' }}
                            </span>
                        </div>
                    </div>
                </div>
                {{-- Minimize Button --}}
                <button @click="minimized = !minimized"
                    class="flex items-center justify-center w-7 h-7 rounded-lg text-neutral-400 hover:text-neutral-600 dark:hover:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-700 transition-colors shrink-0"
                    :title="minimized ? 'Tampilkan detail' : 'Sembunyikan detail'">
                    <flux:icon name="chevron-up" class="w-4 h-4 transition-transform duration-200" x-bind:class="minimized ? 'rotate-180' : ''" />
                </button>
            </div>

            {{-- Collapsible Detail Grid --}}
            <div x-show="!minimized" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                class="px-6 sm:px-7 py-5 pl-8">
                <div class="grid grid-cols-3 gap-y-4 gap-x-6 border-t border-neutral-100 dark:border-neutral-700 pt-5">
                    <div class="flex flex-col">
                        <span class="text-[10px] font-semibold text-neutral-400 uppercase tracking-wider mb-1 flex items-center gap-1.5">
                            <flux:icon name="identification" class="w-3 h-3" /> Nama Ibu Kandung
                        </span>
                        <span class="text-sm font-medium text-neutral-800 dark:text-neutral-200">{{ $regPeriksa->pasien->nm_ibu ?? '-' }}</span>
                    </div>

                    <div class="flex flex-col">
                        <span class="text-[10px] font-semibold text-neutral-400 uppercase tracking-wider mb-1 flex items-center gap-1.5">
                            <flux:icon name="book-open" class="w-3 h-3" /> Agama
                        </span>
                        <span class="text-sm font-medium text-neutral-800 dark:text-neutral-200">{{ $regPeriksa->pasien->agama ?? '-' }}</span>
                    </div>

                    <div class="flex flex-col">
                        <span class="text-[10px] font-semibold text-neutral-400 uppercase tracking-wider mb-1 flex items-center gap-1.5">
                            <flux:icon name="academic-cap" class="w-3 h-3" /> Pendidikan
                        </span>
                        <span class="text-sm font-medium text-neutral-800 dark:text-neutral-200">{{ $regPeriksa->pasien->pnd ?? '-' }}</span>
                    </div>

                    <div class="flex flex-col">
                        <span class="text-[10px] font-semibold text-neutral-400 uppercase tracking-wider mb-1 flex items-center gap-1.5">
                            <flux:icon name="chat-bubble-left-right" class="w-3 h-3" /> Bahasa
                        </span>
                        <span class="text-sm font-medium text-neutral-800 dark:text-neutral-200">{{ $regPeriksa->pasien->bahasa->nama_bahasa ?? '-' }}</span>
                    </div>

                    <div class="flex flex-col">
                        <span class="text-[10px] font-semibold text-neutral-400 uppercase tracking-wider mb-1 flex items-center gap-1.5">
                            <flux:icon name="exclamation-circle" class="w-3 h-3" /> Cacat Fisik
                        </span>
                        <span class="text-sm font-medium text-neutral-800 dark:text-neutral-200">{{ $regPeriksa->pasien->cacatFisik->nama_cacat ?? '-' }}</span>
                    </div>

                    <div class="flex flex-col">
                        <span class="text-[10px] font-semibold text-neutral-400 uppercase tracking-wider mb-1 flex items-center gap-1.5">
                            <flux:icon name="cake" class="w-3 h-3" /> Tempat & Tgl. Lahir
                        </span>
                        <span class="text-sm font-medium text-neutral-800 dark:text-neutral-200">
                            {{ $regPeriksa->pasien->tmp_lahir ?? '-' }}, {{ $regPeriksa->pasien->tgl_lahir ? \Carbon\Carbon::parse($regPeriksa->pasien->tgl_lahir)->translatedFormat('d F Y') : '-' }}
                        </span>
                    </div>

                    <div class="flex flex-col col-span-3">
                        <span class="text-[10px] font-semibold text-neutral-400 uppercase tracking-wider mb-1 flex items-center gap-1.5">
                            <flux:icon name="map-pin" class="w-3 h-3" /> Alamat
                        </span>
                        <span class="text-sm font-medium text-neutral-800 dark:text-neutral-200 leading-relaxed">{{ $regPeriksa->pasien->alamat ?? '-' }}</span>
                    </div>
                </div>
            </div>

            {{-- Minimized Summary Bar --}}
            <div x-show="minimized" x-transition class="px-6 sm:px-7 py-3 pl-8">
                <p class="text-xs text-neutral-400 italic">Detail data diri disembunyikan. Klik ikon di kanan untuk menampilkan.</p>
            </div>
        </div>

        {{-- Floating Patient Info Card (Bottom Right) --}}
        <div x-show="isFloating" style="display: none;"
            x-transition:enter="transition ease-out duration-300 transform" 
            x-transition:enter-start="opacity-0 translate-y-8 scale-95" 
            x-transition:enter-end="opacity-100 translate-y-0 scale-100" 
            x-transition:leave="transition ease-in duration-200 transform" 
            x-transition:leave-start="opacity-100 translate-y-0 scale-100" 
            x-transition:leave-end="opacity-0 translate-y-8 scale-95"
            class="fixed bottom-6 right-6 z-50 w-72 sm:w-80 bg-white/90 dark:bg-neutral-800/90 backdrop-blur-xl rounded-2xl border border-neutral-200/80 dark:border-neutral-700/80 shadow-[0_8px_30px_rgb(0,0,0,0.12)] dark:shadow-[0_8px_30px_rgb(0,0,0,0.3)] overflow-hidden cursor-pointer hover:border-[#4C5C2D]/50 dark:hover:border-[#8CC7C4]/50 transition-colors group"
            @click="window.scrollTo({top: 0, behavior: 'smooth'})"
            title="Kembali ke atas">
            
            {{-- Accent Line --}}
            <div class="absolute top-0 left-0 w-1.5 h-full bg-[#4C5C2D] dark:bg-[#8CC7C4]"></div>
            
            <div class="flex items-center justify-between p-3.5 pl-5">
                <div class="flex flex-col gap-0.5">
                    <h2 class="text-sm font-bold text-neutral-800 dark:text-neutral-100 truncate max-w-[180px] sm:max-w-[210px]">{{ $regPeriksa->pasien->nm_pasien }}</h2>
                    <div class="flex flex-wrap items-center gap-2 mt-1">
                        <span class="inline-flex items-center rounded bg-neutral-100 dark:bg-neutral-700 px-1.5 py-0.5 text-[10px] font-semibold text-neutral-600 dark:text-neutral-300">
                            RM: {{ $regPeriksa->no_rkm_medis }}
                        </span>
                    </div>
                </div>
                <div class="w-8 h-8 rounded-full bg-neutral-100 dark:bg-neutral-700/50 flex items-center justify-center shrink-0 group-hover:bg-[#4C5C2D] group-hover:text-white dark:group-hover:bg-[#8CC7C4] dark:group-hover:text-neutral-900 transition-colors">
                    <flux:icon name="arrow-up" class="w-4 h-4 text-neutral-500 group-hover:text-white dark:group-hover:text-neutral-900" />
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-neutral-800 rounded-xl border border-neutral-200 dark:border-neutral-700 p-6 shadow-sm">
        {{-- Custom Tabs & Menu --}}
        <div class="inline-flex flex-wrap items-center gap-2 p-1 bg-neutral-100 dark:bg-neutral-900 rounded-xl mb-6">
            @php
                $tabs = [
                    ['id' => 'kunjungan', 'label' => 'Riwayat Kunjungan', 'icon' => 'clock'],
                    ['id' => 'soapie', 'label' => 'Riwayat Soapie', 'icon' => 'document-text'],
                    ['id' => 'perawatan', 'label' => 'Riwayat Perawatan', 'icon' => 'heart'],
                    ['id' => 'pembelian_obat', 'label' => 'Pembelian Obat', 'icon' => 'beaker'],
                    ['id' => 'piutang_obat', 'label' => 'Piutang Obat', 'icon' => 'banknotes'],
                    ['id' => 'retensi_berkas', 'label' => 'Retensi Berkas', 'icon' => 'folder'],
                ];
            @endphp

            @foreach($tabs as $tab)
                <button wire:click="$set('activeTab', '{{ $tab['id'] }}')"
                    class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition-all cursor-pointer {{ $activeTab === $tab['id'] ? 'bg-white dark:bg-neutral-800 text-neutral-800 dark:text-neutral-100 shadow-sm' : 'text-neutral-500 hover:text-neutral-700 dark:hover:text-neutral-300 hover:bg-neutral-200/50 dark:hover:bg-neutral-700/50' }}">
                    <flux:icon :name="$tab['icon']" class="w-4 h-4" />
                    {{ $tab['label'] }}
                </button>
            @endforeach
        </div>

        {{-- Tab Panels --}}
        <div class="mt-4 min-h-[300px]">
            @if($activeTab === 'kunjungan')
                @if($riwayatKunjungan->isEmpty())
                    <div class="flex flex-col items-center justify-center py-16 text-neutral-400 dark:text-neutral-600">
                        <flux:icon name="inbox" class="w-12 h-12 mb-3 opacity-50" />
                        <p class="text-sm font-medium">Tidak ada riwayat kunjungan</p>
                    </div>
                @else
                    <div class="overflow-x-auto rounded-xl border border-neutral-200 dark:border-neutral-700">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-neutral-50 dark:bg-neutral-900 border-b border-neutral-200 dark:border-neutral-700">
                                <tr>
                                    <th class="px-4 py-3 text-[11px] font-semibold text-neutral-500 uppercase tracking-wider whitespace-nowrap">No. Rawat</th>
                                    <th class="px-4 py-3 text-[11px] font-semibold text-neutral-500 uppercase tracking-wider whitespace-nowrap">Tanggal</th>
                                    <th class="px-4 py-3 text-[11px] font-semibold text-neutral-500 uppercase tracking-wider whitespace-nowrap">Jam</th>
                                    <th class="px-4 py-3 text-[11px] font-semibold text-neutral-500 uppercase tracking-wider whitespace-nowrap">Kode Dokter</th>
                                    <th class="px-4 py-3 text-[11px] font-semibold text-neutral-500 uppercase tracking-wider whitespace-nowrap">Nama Dokter</th>
                                    <th class="px-4 py-3 text-[11px] font-semibold text-neutral-500 uppercase tracking-wider whitespace-nowrap">Umur</th>
                                    <th class="px-4 py-3 text-[11px] font-semibold text-neutral-500 uppercase tracking-wider whitespace-nowrap">Poli / Kamar</th>
                                    <th class="px-4 py-3 text-[11px] font-semibold text-neutral-500 uppercase tracking-wider whitespace-nowrap">Jenis Bayar</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-neutral-100 dark:divide-neutral-700/50">
                                @foreach($riwayatKunjungan as $kunjungan)
                                    <tr class="bg-white dark:bg-neutral-800 hover:bg-neutral-50 dark:hover:bg-neutral-700/40 transition-colors {{ $kunjungan->no_rawat === $no_rawat ? 'ring-2 ring-inset ring-[#4C5C2D]/30 dark:ring-[#8CC7C4]/30' : '' }}">
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <span class="font-mono text-xs font-semibold text-[#4C5C2D] dark:text-[#8CC7C4]">
                                                {{ $kunjungan->no_rawat }}
                                            </span>
                                            @if($kunjungan->no_rawat === $no_rawat)
                                                <span class="ml-1 inline-flex items-center rounded bg-[#4C5C2D]/10 px-1.5 py-0.5 text-[10px] font-semibold text-[#4C5C2D] dark:text-[#8CC7C4]">Saat ini</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-neutral-700 dark:text-neutral-300">
                                            {{ \Carbon\Carbon::parse($kunjungan->tgl_registrasi)->translatedFormat('d M Y') }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-neutral-700 dark:text-neutral-300">
                                            {{ \Carbon\Carbon::parse($kunjungan->jam_reg)->format('H:i') }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <span class="inline-flex items-center rounded bg-neutral-100 dark:bg-neutral-700 px-2 py-0.5 text-xs font-mono text-neutral-600 dark:text-neutral-300">
                                                {{ $kunjungan->kd_dokter }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap font-medium text-neutral-800 dark:text-neutral-200">
                                            {{ $kunjungan->dokter->nm_dokter ?? '-' }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-neutral-700 dark:text-neutral-300">
                                            @php
                                                $tglLahir = $kunjungan->pasien->tgl_lahir ?? null;
                                                $tglReg   = $kunjungan->tgl_registrasi;
                                                $umur     = $tglLahir && $tglReg
                                                    ? \Carbon\Carbon::parse($tglLahir)->diffInYears(\Carbon\Carbon::parse($tglReg))
                                                    : '-';
                                            @endphp
                                            {{ is_numeric($umur) ? $umur . ' thn' : $umur }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-neutral-700 dark:text-neutral-300">
                                            {{ $kunjungan->kamarInap->kamar->kd_kamar ?? $kunjungan->kd_poli ?? '-' }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <span class="inline-flex items-center rounded-full px-2.5 py-1 text-[11px] font-semibold
                                                @if(strtolower($kunjungan->kd_pj) === 'bpjs' || str_contains(strtolower($kunjungan->penjab->png_jawab ?? ''), 'bpjs'))
                                                    bg-blue-50 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400
                                                @else
                                                    bg-green-50 text-green-700 dark:bg-green-500/10 dark:text-green-400
                                                @endif">
                                                {{ $kunjungan->penjab->png_jawab ?? $kunjungan->kd_pj ?? '-' }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <p class="mt-3 text-right text-xs text-neutral-400">Total: {{ $riwayatKunjungan->count() }} kunjungan</p>
                @endif

            @elseif($activeTab === 'soapie')
                @if($riwayatSoapie->isEmpty())
                    <div class="flex flex-col items-center justify-center py-16 text-neutral-400 dark:text-neutral-600">
                        <flux:icon name="inbox" class="w-12 h-12 mb-3 opacity-50" />
                        <p class="text-sm font-medium">Tidak ada data S.O.A.P.I.E</p>
                    </div>
                @else
                    <div class="flex flex-col gap-4">
                        @php
                            $totalSoapie = 0;
                            foreach($riwayatSoapie as $group) {
                                $totalSoapie += $group->count();
                            }
                        @endphp
                        <p class="text-xs text-neutral-400 text-right">Total: {{ $totalSoapie }} catatan dari {{ $riwayatSoapie->count() }} kunjungan</p>

                        @foreach($riwayatSoapie as $noRawatKey => $soapies)
                            @php
                                $firstSoapie = $soapies->first();
                            @endphp
                            <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 shadow-sm bg-white dark:bg-neutral-800">
                                {{-- Outer Card Header: No Rawat & Tgl Registrasi --}}
                                <div class="sticky top-0 z-10 backdrop-blur-md rounded-t-xl flex flex-wrap items-center justify-between gap-3 px-5 py-3 bg-neutral-50/90 dark:bg-neutral-800/90 border-b border-neutral-200 dark:border-neutral-700">
                                    <div class="flex flex-wrap items-center gap-4">
                                        <div class="flex items-center gap-1.5">
                                            <span class="text-[10px] font-semibold text-neutral-400 uppercase">No. Rawat:</span>
                                            <span class="font-mono text-sm font-bold text-[#4C5C2D] dark:text-[#8CC7C4]">
                                                {{ $noRawatKey }}
                                            </span>
                                            @if($noRawatKey === $no_rawat)
                                                <span class="ml-1 inline-flex items-center rounded bg-[#4C5C2D]/10 px-1.5 py-0.5 text-[10px] font-semibold text-[#4C5C2D] dark:text-[#8CC7C4]">Saat ini</span>
                                            @endif
                                        </div>
                                        <span class="text-neutral-300 dark:text-neutral-600">|</span>
                                        <div class="flex items-center gap-1.5">
                                            <span class="text-[10px] font-semibold text-neutral-400 uppercase">Tgl. Registrasi:</span>
                                            <span class="text-sm text-neutral-700 dark:text-neutral-300 font-medium">
                                                {{ \Carbon\Carbon::parse($firstSoapie->regPeriksa->tgl_registrasi ?? '')->translatedFormat('d F Y') }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="inline-flex items-center rounded-full bg-neutral-100 dark:bg-neutral-700 px-2.5 py-0.5 text-xs font-semibold text-neutral-600 dark:text-neutral-300">
                                            {{ $soapies->count() }} Observasi
                                        </span>
                                    </div>
                                </div>

                                {{-- Inner Observasi List --}}
                                <div class="flex flex-col divide-y divide-neutral-200 dark:divide-neutral-700">
                                    @foreach($soapies as $soapie)
                                        <div class="flex flex-col hover:bg-neutral-50/30 dark:hover:bg-neutral-700/10 transition-colors">
                                            {{-- Inner Header: Tanggal, Jam, Dokter, Status --}}
                                            <div class="flex flex-wrap items-center justify-between gap-3 px-5 py-2.5 bg-neutral-100 dark:bg-neutral-800/80 border-b border-neutral-200 dark:border-neutral-700">
                                                <div class="flex items-center gap-1.5">
                                                    <flux:icon name="clock" class="w-4 h-4 text-neutral-400" />
                                                    <span class="text-xs text-neutral-600 dark:text-neutral-300 font-medium">
                                                        {{ \Carbon\Carbon::parse($soapie->tgl_perawatan)->translatedFormat('d M Y') }}
                                                        <span class="font-bold ml-1 text-neutral-800 dark:text-neutral-100">{{ \Carbon\Carbon::parse($soapie->jam_rawat)->format('H:i') }}</span>
                                                    </span>
                                                </div>
                                                <div class="flex items-center gap-3">
                                                    <div class="flex items-center gap-1.5">
                                                        <flux:icon name="user" class="w-3.5 h-3.5 text-neutral-400" />
                                                        <span class="text-xs font-semibold text-neutral-700 dark:text-neutral-200">
                                                            {{ $soapie->pegawai->nama ?? $soapie->nip ?? '-' }}
                                                        </span>
                                                    </div>
                                                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-semibold {{ $soapie->regPeriksa->stts === 'Sudah' ? 'bg-green-50 text-green-700 dark:bg-green-500/10 dark:text-green-400' : 'bg-yellow-50 text-yellow-700 dark:bg-yellow-500/10 dark:text-yellow-400' }}">
                                                        {{ $soapie->regPeriksa->stts ?? '-' }}
                                                    </span>
                                                </div>
                                            </div>

                                            {{-- SOAPIE Fields Grid - 2 Rows, 3 Columns --}}
                                            <div class="grid grid-cols-1 md:grid-cols-3 gap-0 divide-y md:divide-y-0 md:divide-x divide-neutral-200/60 dark:divide-neutral-700/60">
                                                @php
                                                    $fields = [
                                                        ['label' => 'S — Subjek',     'icon' => 'chat-bubble-left',         'value' => $soapie->keluhan,      'color' => 'text-blue-600 dark:text-blue-400',       'bg' => 'bg-blue-100 dark:bg-blue-900/30'],
                                                        ['label' => 'O — Objek',      'icon' => 'clipboard-document-check', 'value' => $soapie->pemeriksaan,  'color' => 'text-emerald-600 dark:text-emerald-400', 'bg' => 'bg-emerald-100 dark:bg-emerald-900/30'],
                                                        ['label' => 'A — Asesmen',    'icon' => 'beaker',                   'value' => $soapie->penilaian,    'color' => 'text-purple-600 dark:text-purple-400',   'bg' => 'bg-purple-100 dark:bg-purple-900/30'],
                                                        ['label' => 'P — Plan',       'icon' => 'document-text',            'value' => $soapie->rtl,          'color' => 'text-amber-600 dark:text-amber-400',     'bg' => 'bg-amber-100 dark:bg-amber-900/30'],
                                                        ['label' => 'I — Inst/Impl',  'icon' => 'arrow-path',               'value' => $soapie->instruksi,    'color' => 'text-cyan-600 dark:text-cyan-400',       'bg' => 'bg-cyan-100 dark:bg-cyan-900/30'],
                                                        ['label' => 'E — Evaluasi',   'icon' => 'check-circle',             'value' => $soapie->evaluasi,     'color' => 'text-rose-600 dark:text-rose-400',       'bg' => 'bg-rose-100 dark:bg-rose-900/30'],
                                                    ];
                                                @endphp

                                                @foreach($fields as $index => $field)
                                                    <div class="p-5 flex flex-col gap-2 {{ $index >= 3 ? 'md:border-t md:border-neutral-200/60 md:dark:border-neutral-700/60' : '' }}">
                                                        <div class="flex items-center gap-2">
                                                            <div class="p-1.5 rounded-md {{ $field['bg'] }} {{ $field['color'] }}">
                                                                <flux:icon :name="$field['icon']" class="w-4 h-4" />
                                                            </div>
                                                            <span class="text-xs font-bold uppercase tracking-wide {{ $field['color'] }}">
                                                                {{ $field['label'] }}
                                                            </span>
                                                        </div>
                                                        <div class="pl-8">
                                                            <p class="text-[15px] font-medium text-neutral-600 dark:text-neutral-300 leading-relaxed whitespace-pre-line min-h-[1.5rem]">
                                                                {{ $field['value'] ?: '-' }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

            @elseif($activeTab === 'perawatan')
                <div class="text-neutral-500 py-8 text-center text-sm border-2 border-dashed border-neutral-200 dark:border-neutral-700 rounded-xl">
                    Konten Riwayat Perawatan
                </div>
            @elseif($activeTab === 'pembelian_obat')
                <div class="text-neutral-500 py-8 text-center text-sm border-2 border-dashed border-neutral-200 dark:border-neutral-700 rounded-xl">
                    Konten Pembelian Obat
                </div>
            @elseif($activeTab === 'piutang_obat')
                <div class="text-neutral-500 py-8 text-center text-sm border-2 border-dashed border-neutral-200 dark:border-neutral-700 rounded-xl">
                    Konten Piutang Obat
                </div>
            @elseif($activeTab === 'retensi_berkas')
                <div class="text-neutral-500 py-8 text-center text-sm border-2 border-dashed border-neutral-200 dark:border-neutral-700 rounded-xl">
                    Konten Retensi Berkas
                </div>
            @endif
        </div>
    </div>
</div>
