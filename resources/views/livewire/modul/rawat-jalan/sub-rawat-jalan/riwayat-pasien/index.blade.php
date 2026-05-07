<div class="flex flex-col gap-6 pb-8">
    {{-- Header / Breadcrumb --}}
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('modul.rawat-jalan.perawatan-tindakan', str_replace('/', '-', $no_rawat)) }}" wire:navigate
               class="flex items-center justify-center w-8 h-8 rounded-lg transition-colors hover:bg-neutral-100 dark:hover:bg-neutral-700">
                <flux:icon name="chevron-left" class="w-5 h-5 text-neutral-500" />
            </a>
            <div>
                <nav class="text-xs text-neutral-400 mb-0.5">
                    <a href="{{ route('modul.index') }}" wire:navigate class="hover:underline">Modul</a>
                    <span class="mx-1">/</span>
                    <a href="{{ route('modul.rawat-jalan.index') }}" wire:navigate class="hover:underline">Rawat Jalan</a>
                    <span class="mx-1">/</span>
                    <a href="{{ route('modul.rawat-jalan.perawatan-tindakan', str_replace('/', '-', $no_rawat)) }}" wire:navigate class="hover:underline">Perawatan</a>
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
                                    <th class="px-4 py-3 text-[11px] font-semibold text-neutral-500 uppercase tracking-wider whitespace-nowrap w-12">No.</th>
                                    <th class="px-4 py-3 text-[11px] font-semibold text-neutral-500 uppercase tracking-wider whitespace-nowrap">No. Rawat</th>
                                    <th class="px-4 py-3 text-[11px] font-semibold text-neutral-500 uppercase tracking-wider whitespace-nowrap">Tanggal</th>
                                    <th class="px-4 py-3 text-[11px] font-semibold text-neutral-500 uppercase tracking-wider whitespace-nowrap">Jam</th>
                                    <th class="px-4 py-3 text-[11px] font-semibold text-neutral-500 uppercase tracking-wider whitespace-nowrap">Kd Dokter</th>
                                    <th class="px-4 py-3 text-[11px] font-semibold text-neutral-500 uppercase tracking-wider whitespace-nowrap">Dokter Dituju/DPJP</th>
                                    <th class="px-4 py-3 text-[11px] font-semibold text-neutral-500 uppercase tracking-wider whitespace-nowrap">Umur</th>
                                    <th class="px-4 py-3 text-[11px] font-semibold text-neutral-500 uppercase tracking-wider whitespace-nowrap">Poliklinik/Kamar</th>
                                    <th class="px-4 py-3 text-[11px] font-semibold text-neutral-500 uppercase tracking-wider whitespace-nowrap">Jenis Bayar</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-neutral-100 dark:divide-neutral-700/50">
                                @php $rowNum = 1; @endphp
                                @foreach($kunjunganDetail as $item)
                                    <tr class="bg-white dark:bg-neutral-800 hover:bg-neutral-50 dark:hover:bg-neutral-700/40 transition-colors {{ $item['is_current'] ? 'ring-2 ring-inset ring-[#4C5C2D]/30 dark:ring-[#8CC7C4]/30' : '' }}">
                                        <td class="px-4 py-3 whitespace-nowrap text-neutral-500 text-xs font-medium">
                                            @if($item['is_first'])
                                                {{ $rowNum++ }}
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <span class="font-mono text-xs font-semibold text-[#4C5C2D] dark:text-[#8CC7C4]">
                                                {{ $item['no_rawat'] }}
                                            </span>
                                            @if($item['is_current'] && $item['is_first'])
                                                <span class="ml-1 inline-flex items-center rounded bg-[#4C5C2D]/10 px-1.5 py-0.5 text-[10px] font-semibold text-[#4C5C2D] dark:text-[#8CC7C4]">Saat ini</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-neutral-700 dark:text-neutral-300">
                                            {{ \Carbon\Carbon::parse($item['tgl'])->translatedFormat('d M Y') }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-neutral-700 dark:text-neutral-300">
                                            {{ \Carbon\Carbon::parse($item['jam'])->format('H:i:s') }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <span class="inline-flex items-center rounded bg-neutral-100 dark:bg-neutral-700 px-2 py-0.5 text-xs font-mono text-neutral-600 dark:text-neutral-300">
                                                {{ $item['kd_dokter'] }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap font-medium text-neutral-800 dark:text-neutral-200">
                                            {{ $item['nm_dokter'] }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-neutral-700 dark:text-neutral-300">
                                            {{ $item['umur'] }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-neutral-700 dark:text-neutral-300 font-medium">
                                            {{ $item['lokasi'] }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <span class="inline-flex items-center rounded-full px-2.5 py-1 text-[11px] font-semibold
                                                @if(strtolower($item['kd_pj']) === 'bpjs' || str_contains(strtolower($item['png_jawab'] ?? ''), 'bpjs'))
                                                    bg-blue-50 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400
                                                @else
                                                    bg-green-50 text-green-700 dark:bg-green-500/10 dark:text-green-400
                                                @endif">
                                                {{ $item['png_jawab'] }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <p class="mt-3 text-right text-xs text-neutral-400 italic">Total: {{ $kunjunganDetail->count() }} baris riwayat</p>
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
                                <div class="sticky top-0 z-10 backdrop-blur-md rounded-t-xl flex flex-wrap items-center justify-between gap-3 px-5 py-3 bg-[#4C5C2D] border-b border-[#4C5C2D] shadow-sm">
                                    <div class="flex flex-wrap items-center gap-4">
                                        <div class="flex items-center gap-1.5">
                                            <span class="text-[10px] font-semibold text-white/70 uppercase">No. Rawat:</span>
                                            <span class="font-mono text-sm font-bold text-white">
                                                {{ $noRawatKey }}
                                            </span>
                                            @if($noRawatKey === $no_rawat)
                                                <span class="ml-1 inline-flex items-center rounded bg-white/20 px-1.5 py-0.5 text-[10px] font-bold text-white border border-white/20">Saat ini</span>
                                            @endif
                                        </div>
                                        <span class="text-white/30">|</span>
                                        <div class="flex items-center gap-1.5">
                                            <span class="text-[10px] font-semibold text-white/70 uppercase">Tgl. Registrasi:</span>
                                            <span class="text-xs text-white font-bold">
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
                                                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-bold uppercase {{ $soapie->status_lanjut === 'Ranap' ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-500/10 dark:text-indigo-400' : 'bg-amber-100 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400' }}">
                                                        {{ $soapie->status_lanjut }}
                                                    </span>
                                                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-semibold {{ ($soapie->regPeriksa->stts ?? '') === 'Sudah' ? 'bg-green-50 text-green-700 dark:bg-green-500/10 dark:text-green-400' : 'bg-yellow-50 text-yellow-700 dark:bg-yellow-500/10 dark:text-yellow-400' }}">
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
                @if($riwayatKunjungan->isEmpty())
                    <div class="flex flex-col items-center justify-center py-16 text-neutral-400 dark:text-neutral-600">
                        <flux:icon name="inbox" class="w-12 h-12 mb-3 opacity-50" />
                        <p class="text-sm font-medium">Tidak ada riwayat perawatan</p>
                    </div>
                @else
                    <div class="flex flex-col gap-4 pb-32" x-data="{ expanded: null }">
                        @foreach($riwayatKunjungan as $kunjungan)
                            <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 overflow-hidden shadow-sm transition-all"
                                 :class="expanded === '{{ $kunjungan->no_rawat }}' ? 'ring-1 ring-[#4C5C2D] dark:ring-[#8CC7C4]' : ''">
                                {{-- Accordion Header --}}
                                <button @click="expanded = expanded === '{{ $kunjungan->no_rawat }}' ? null : '{{ $kunjungan->no_rawat }}'"
                                        class="w-full group/header relative z-10 flex items-center justify-between px-5 py-4 text-left hover:bg-neutral-50 dark:hover:bg-neutral-700/30 transition-colors">
                                    <div class="flex flex-wrap items-center gap-x-6 gap-y-2">
                                        <div class="flex flex-col">
                                            <div class="flex items-center gap-2 mb-0.5">
                                                <span class="text-[10px] font-bold text-neutral-400 uppercase tracking-wider">Tgl. Registrasi</span>
                                                <span class="px-1.5 py-0.5 rounded text-[9px] font-bold uppercase tracking-tighter border {{ $kunjungan->status_lanjut == 'Ranap' ? 'bg-emerald-50 text-emerald-700 border-emerald-100 dark:bg-emerald-950/30 dark:text-emerald-400 dark:border-emerald-900/50' : 'bg-orange-50 text-orange-700 border-orange-100 dark:bg-orange-950/30 dark:text-orange-400 dark:border-orange-900/50' }}">
                                                    {{ $kunjungan->status_lanjut }}
                                                </span>
                                            </div>
                                            <span class="text-sm font-bold text-neutral-800 dark:text-neutral-100 italic">
                                                {{ \Carbon\Carbon::parse($kunjungan->tgl_registrasi)->translatedFormat('d F Y') }}
                                            </span>
                                        </div>
                                        <div class="w-px h-6 bg-neutral-200 dark:border-neutral-700 hidden sm:block"></div>
                                        <div class="flex flex-col">
                                            <span class="text-[10px] font-bold text-neutral-400 uppercase tracking-wider mb-0.5">Dokter Penanggung Jawab</span>
                                            <span class="text-sm font-semibold text-neutral-700 dark:text-neutral-300">
                                                {{ $kunjungan->dokter->nm_dokter ?? '-' }}
                                            </span>
                                        </div>
                                        <div class="w-px h-6 bg-neutral-200 dark:border-neutral-700 hidden sm:block"></div>
                                        <div class="flex flex-col">
                                            <span class="text-[10px] font-bold text-neutral-400 uppercase tracking-wider mb-0.5">No. Rawat</span>
                                            <span class="font-mono text-xs text-[#4C5C2D] dark:text-[#8CC7C4] font-bold">{{ $kunjungan->no_rawat }}</span>
                                        </div>
                                    </div>
                                    <flux:icon name="chevron-down" class="w-5 h-5 text-neutral-400 transition-transform duration-200"
                                                ::class="expanded === '{{ $kunjungan->no_rawat }}' ? 'rotate-180 text-[#4C5C2D]' : ''" />
                                </button>

                                {{-- Accordion Content --}}
                                <div x-show="expanded === '{{ $kunjungan->no_rawat }}'"
                                     x-transition:enter="transition ease-out duration-200"
                                     x-transition:enter-start="opacity-0 -translate-y-2"
                                     x-transition:enter-end="opacity-100 translate-y-0"
                                     class="p-5 border-t border-neutral-100 dark:border-neutral-700 bg-neutral-50/30 dark:bg-neutral-900/10">

                                    {{-- Section: Informasi Umum (Khanza Style) --}}
                                    <div class="mb-6 overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 shadow-sm">
                                        <div class="bg-neutral-50 dark:bg-neutral-900/50 px-4 py-2 border-b border-neutral-200 dark:border-neutral-700">
                                            <h4 class="text-xs font-extrabold text-[#4C5C2D] dark:text-[#8CC7C4] uppercase tracking-widest flex items-center gap-2">
                                                <flux:icon name="information-circle" class="w-4 h-4" /> Informasi pendaftaran & penanggung jawab
                                            </h4>
                                        </div>
                                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-y-4 gap-x-8 p-4 text-[11px]">
                                            {{-- Column 1 --}}
                                            <div class="space-y-2">
                                                <div class="flex items-start">
                                                    <span class="w-24 shrink-0 text-neutral-400 font-semibold italic">No. Rawat</span>
                                                    <span class="text-neutral-700 dark:text-neutral-300 font-mono font-bold">: {{ $kunjungan->no_rawat }}</span>
                                                </div>
                                                <div class="flex items-start">
                                                    <span class="w-24 shrink-0 text-neutral-400 font-semibold italic">No. Registrasi</span>
                                                    <span class="text-neutral-700 dark:text-neutral-300 font-bold">: {{ $kunjungan->no_reg }}</span>
                                                </div>
                                                <div class="flex items-start">
                                                    <span class="w-24 shrink-0 text-neutral-400 font-semibold italic">Tgl. Registrasi</span>
                                                    <span class="text-neutral-700 dark:text-neutral-300 font-bold">: {{ \Carbon\Carbon::parse($kunjungan->tgl_registrasi)->format('d-m-Y') }} {{ $kunjungan->jam_reg }}</span>
                                                </div>
                                                <div class="flex items-start">
                                                    <span class="w-24 shrink-0 text-neutral-400 font-semibold italic">Umur Saat Daftar</span>
                                                    <span class="text-neutral-700 dark:text-neutral-300 font-bold">: {{ $kunjungan->umur_daftar }}</span>
                                                </div>
                                            </div>

                                            {{-- Column 2 --}}
                                            <div class="space-y-2">
                                                <div class="flex items-start">
                                                    <span class="w-24 shrink-0 text-neutral-400 font-semibold italic">Unit/Poliklinik</span>
                                                    <span class="text-neutral-700 dark:text-neutral-300 font-bold">: {{ $kunjungan->poliklinik->nm_poli ?? '-' }}</span>
                                                </div>
                                                <div class="flex items-start">
                                                    <span class="w-24 shrink-0 text-neutral-400 font-semibold italic">Dokter Poli</span>
                                                    <span class="text-neutral-700 dark:text-neutral-300 font-bold">: {{ $kunjungan->dokter->nm_dokter ?? '-' }}</span>
                                                </div>
                                                <div class="flex items-start">
                                                    <span class="w-24 shrink-0 text-neutral-400 font-semibold italic">Cara Bayar</span>
                                                    <span class="text-neutral-700 dark:text-neutral-300 font-bold">: {{ $kunjungan->penjab->png_jawab ?? '-' }}</span>
                                                </div>
                                                <div class="flex items-start">
                                                    <span class="w-24 shrink-0 text-neutral-400 font-semibold italic">Status</span>
                                                    <span class="font-bold">: 
                                                        <span class="px-2 py-0.5 rounded {{ $kunjungan->status_lanjut === 'Ranap' ? 'bg-purple-100 text-purple-700 dark:bg-purple-500/10' : 'bg-blue-100 text-blue-700 dark:bg-blue-500/10' }}">
                                                            {{ $kunjungan->status_lanjut }}
                                                        </span>
                                                    </span>
                                                </div>
                                            </div>

                                            {{-- Column 3 --}}
                                            <div class="space-y-2 lg:border-l lg:border-neutral-100 lg:dark:border-neutral-700 lg:pl-6 text-[10px]">
                                                <div class="flex items-start">
                                                    <span class="w-24 shrink-0 text-neutral-400 font-semibold italic">P.J.</span>
                                                    <span class="text-neutral-700 dark:text-neutral-300 font-bold text-sky-600 dark:text-sky-400 uppercase">: {{ $kunjungan->p_jawab }}</span>
                                                </div>
                                                <div class="flex items-start">
                                                    <span class="w-24 shrink-0 text-neutral-400 font-semibold italic">Hubungan</span>
                                                    <span class="text-neutral-700 dark:text-neutral-300 font-bold">: {{ $kunjungan->hubunganpj }}</span>
                                                </div>
                                                <div class="flex items-start">
                                                    <span class="w-24 shrink-0 text-neutral-400 font-semibold italic">Alamat</span>
                                                    <span class="text-neutral-700 dark:text-neutral-300 font-bold leading-relaxed">: {{ $kunjungan->almt_pj }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Section: Data SEP BPJS --}}
                                    @if($kunjungan->bridgingSep)
                                        <div class="mb-6 overflow-hidden rounded-xl border border-blue-200 dark:border-blue-900/50 bg-white dark:bg-neutral-800 shadow-sm">
                                            <div class="bg-blue-50/50 dark:bg-blue-900/20 px-4 py-2 border-b border-blue-100 dark:border-blue-900/30 flex items-center justify-between">
                                                <h4 class="text-xs font-extrabold text-blue-700 dark:text-blue-400 uppercase tracking-widest flex items-center gap-2">
                                                    <flux:icon name="identification" class="w-4 h-4" /> Data SEP BPJS
                                                </h4>
                                                <span class="text-[10px] font-mono text-blue-500 font-bold">{{ $kunjungan->bridgingSep->no_sep }}</span>
                                            </div>
                                            <div class="p-4 space-y-6">
                                                {{-- Row 1: Informasi SEP & Kelas Rawat --}}
                                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                                                    <div class="space-y-4">
                                                        <h5 class="text-[10px] uppercase font-bold text-neutral-400 border-b border-neutral-100 dark:border-neutral-700 pb-1">Informasi SEP</h5>
                                                        <div class="grid grid-cols-2 gap-y-2 text-[11px]">
                                                            <div class="flex flex-col"><span class="text-neutral-400 italic">No. Kartu</span><span class="font-bold text-neutral-700 dark:text-neutral-300">{{ $kunjungan->bridgingSep->no_kartu }}</span></div>
                                                            <div class="flex flex-col"><span class="text-neutral-400 italic">No. SEP</span><span class="font-bold text-neutral-700 dark:text-neutral-300">{{ $kunjungan->bridgingSep->no_sep }}</span></div>
                                                            <div class="flex flex-col"><span class="text-neutral-400 italic">Tgl. SEP</span><span class="font-bold text-neutral-700 dark:text-neutral-300">{{ \Carbon\Carbon::parse($kunjungan->bridgingSep->tglsep)->format('d-m-Y') }}</span></div>
                                                            <div class="flex flex-col"><span class="text-neutral-400 italic">Jenis Pelayanan</span><span class="font-bold text-neutral-700 dark:text-neutral-300">{{ $kunjungan->bridgingSep->jnspelayanan == '1' ? '1. Ranap' : '2. Ralan' }}</span></div>
                                                        </div>
                                                    </div>
                                                    <div class="space-y-4">
                                                        <h5 class="text-[10px] uppercase font-bold text-neutral-400 border-b border-neutral-100 dark:border-neutral-700 pb-1">Kelas Rawat</h5>
                                                        <div class="grid grid-cols-2 gap-y-2 text-[11px]">
                                                            <div class="flex flex-col"><span class="text-neutral-400 italic">Hak Kelas Rawat</span><span class="font-bold text-neutral-700 dark:text-neutral-300">{{ $kunjungan->bridgingSep->klsrawat ?? '-' }}</span></div>
                                                            <div class="flex flex-col"><span class="text-neutral-400 italic">Naik Kelas</span><span class="font-bold text-neutral-700 dark:text-neutral-300">{{ $kunjungan->bridgingSep->klsnaik ?? '-' }}</span></div>
                                                            <div class="flex flex-col"><span class="text-neutral-400 italic">Pembiayaan</span><span class="font-bold text-neutral-700 dark:text-neutral-300">{{ $kunjungan->bridgingSep->pembiayaan ?? '-' }}</span></div>
                                                            <div class="flex flex-col"><span class="text-neutral-400 italic">P.J. Naik Kelas</span><span class="font-bold text-neutral-700 dark:text-neutral-300">{{ $kunjungan->bridgingSep->pjnaikkelas ?? '-' }}</span></div>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- Row 2: Rujukan --}}
                                                <div class="space-y-4">
                                                    <h5 class="text-[10px] uppercase font-bold text-neutral-400 border-b border-neutral-100 dark:border-neutral-700 pb-1 flex items-center gap-2">
                                                        <flux:icon name="truck" class="w-3.5 h-3.5" /> Rujukan
                                                    </h5>
                                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 text-[11px]">
                                                        <div class="flex flex-col"><span class="text-neutral-400 italic">Asal Rujukan</span><span class="font-bold text-neutral-700 dark:text-neutral-300">{{ $kunjungan->bridgingSep->asal_rujukan }}</span></div>
                                                        <div class="flex flex-col"><span class="text-neutral-400 italic">Tgl. Rujukan</span><span class="font-bold text-neutral-700 dark:text-neutral-300">{{ $kunjungan->bridgingSep->tglrujukan ? \Carbon\Carbon::parse($kunjungan->bridgingSep->tglrujukan)->format('d-m-Y') : '-' }}</span></div>
                                                        <div class="flex flex-col"><span class="text-neutral-400 italic">No. Rujukan</span><span class="font-bold text-neutral-700 dark:text-neutral-300">{{ $kunjungan->bridgingSep->no_rujukan ?? '-' }}</span></div>
                                                        <div class="flex flex-col"><span class="text-neutral-400 italic">PPK Rujukan</span><span class="font-bold text-neutral-700 dark:text-neutral-300">{{ $kunjungan->bridgingSep->nmppkrujukan ?? '-' }}</span></div>
                                                    </div>
                                                </div>

                                                {{-- Row 3: Keterangan & Jaminan --}}
                                                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                                                    <div class="space-y-4">
                                                        <h5 class="text-[10px] uppercase font-bold text-neutral-400 border-b border-neutral-100 dark:border-neutral-700 pb-1">Keterangan SEP</h5>
                                                        <div class="space-y-2 text-[11px]">
                                                            <div class="flex flex-col"><span class="text-neutral-400 italic">Catatan</span><span class="font-bold text-neutral-700 dark:text-neutral-300">{{ $kunjungan->bridgingSep->catatan ?? '-' }}</span></div>
                                                            <div class="flex flex-col"><span class="text-neutral-400 italic">Diagnosa Awal</span><span class="font-bold text-neutral-700 dark:text-neutral-300">{{ $kunjungan->bridgingSep->diagawal }} - {{ $kunjungan->bridgingSep->nmdiagnosaawal }}</span></div>
                                                        </div>
                                                    </div>
                                                    <div class="space-y-4">
                                                        <h5 class="text-[10px] uppercase font-bold text-neutral-400 border-b border-neutral-100 dark:border-neutral-700 pb-1">Informasi Poli & Katarak</h5>
                                                        <div class="grid grid-cols-2 gap-2 text-[11px]">
                                                            <div class="flex flex-col"><span class="text-neutral-400 italic">Tujuan</span><span class="font-bold text-neutral-700 dark:text-neutral-300">{{ $kunjungan->bridgingSep->nmpolitujuan }}</span></div>
                                                            <div class="flex flex-col"><span class="text-neutral-400 italic">Eksekutif</span><span class="font-bold text-neutral-700 dark:text-neutral-300">{{ $kunjungan->bridgingSep->eksekutif }}</span></div>
                                                            <div class="flex flex-col"><span class="text-neutral-400 italic">COB</span><span class="font-bold text-neutral-700 dark:text-neutral-300">{{ $kunjungan->bridgingSep->cob }}</span></div>
                                                            <div class="flex flex-col"><span class="text-neutral-400 italic">Katarak</span><span class="font-bold text-neutral-700 dark:text-neutral-300">{{ $kunjungan->bridgingSep->katarak }}</span></div>
                                                        </div>
                                                    </div>
                                                    <div class="space-y-4">
                                                        <h5 class="text-[10px] uppercase font-bold text-neutral-400 border-b border-neutral-100 dark:border-neutral-700 pb-1 flex items-center gap-2">
                                                            <flux:icon name="shield-check" class="w-3.5 h-3.5" /> Jaminan
                                                        </h5>
                                                        <div class="grid grid-cols-2 gap-2 text-[11px]">
                                                            <div class="flex flex-col"><span class="text-neutral-400 italic">Laka Lantas</span><span class="font-bold text-neutral-700 dark:text-neutral-300">{{ $kunjungan->bridgingSep->lakalantas }}</span></div>
                                                            <div class="flex flex-col"><span class="text-neutral-400 italic">Tanggal KLL</span><span class="font-bold text-neutral-700 dark:text-neutral-300">{{ $kunjungan->bridgingSep->tglkkl != '0000-00-00' ? $kunjungan->bridgingSep->tglkkl : '-' }}</span></div>
                                                            <div class="flex flex-col"><span class="text-neutral-400 italic">Suplesi</span><span class="font-bold text-neutral-700 dark:text-neutral-300">{{ $kunjungan->bridgingSep->suplesi }}</span></div>
                                                            <div class="flex flex-col"><span class="text-neutral-400 italic">Dokter DPJP</span><span class="font-bold text-neutral-700 dark:text-neutral-300">{{ $kunjungan->bridgingSep->nmdpdjp }}</span></div>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- Row 4: Keterangan Lain (New) --}}
                                                <div class="space-y-4 pt-2">
                                                    <h5 class="text-[10px] uppercase font-bold text-neutral-400 border-b border-neutral-100 dark:border-neutral-700 pb-1">Keterangan Lain</h5>
                                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-2 text-[11px]">
                                                        <div class="flex items-start">
                                                            <span class="w-32 shrink-0 text-neutral-400 italic">Tujuan Kunjungan</span>
                                                            <span class="font-bold text-neutral-700 dark:text-neutral-300">
                                                                : {{ match($kunjungan->bridgingSep->tujuankunjungan) {
                                                                    '0' => '0. Normal',
                                                                    '1' => '1. Prosedur',
                                                                    '2' => '2. Konsul Dokter',
                                                                    default => $kunjungan->bridgingSep->tujuankunjungan ?: '-'
                                                                } }}
                                                            </span>
                                                        </div>
                                                        <div class="flex items-start">
                                                            <span class="w-32 shrink-0 text-neutral-400 italic">Flag Prosedur</span>
                                                            <span class="font-bold text-neutral-700 dark:text-neutral-300">
                                                                : {{ match($kunjungan->bridgingSep->flagprosedur) {
                                                                    '0' => '0. Prosedur Tidak Berkelanjutan',
                                                                    '1' => '1. Prosedur Berkelanjutan',
                                                                    default => $kunjungan->bridgingSep->flagprosedur ?: '-'
                                                                } }}
                                                            </span>
                                                        </div>
                                                        <div class="flex items-start">
                                                            <span class="w-32 shrink-0 text-neutral-400 italic">Penunjang</span>
                                                            <span class="font-bold text-neutral-700 dark:text-neutral-300">
                                                                : {{ match($kunjungan->bridgingSep->penunjang) {
                                                                    '1' => '1. Radioterapi',
                                                                    '2' => '2. Kemoterapi',
                                                                    '3' => '3. Rehabilitasi Medik',
                                                                    '4' => '4. Rehabilitasi Psikososial',
                                                                    '5' => '5. Transfusi Darah',
                                                                    '6' => '6. Pelayanan Gigi',
                                                                    '7' => '7. Laboratorium',
                                                                    '8' => '8. USG',
                                                                    '9' => '9. Farmasi',
                                                                    '10' => '10. Lain-lain',
                                                                    '11' => '11. MRI',
                                                                    '12' => '12. CT-SCAN',
                                                                    default => $kunjungan->bridgingSep->penunjang ?: '-'
                                                                } }}
                                                            </span>
                                                        </div>
                                                        <div class="flex items-start">
                                                            <span class="w-32 shrink-0 text-neutral-400 italic">Asesmen Pelayanan</span>
                                                            <span class="font-bold text-neutral-700 dark:text-neutral-300">
                                                                : {{ match($kunjungan->bridgingSep->asesmenpelayanan) {
                                                                    '1' => '1. Poli spesialis tidak tersedia pada hari sebelumnya',
                                                                    '2' => '2. Jam operasional poli spesialis telah berakhir pada hari sebelumnya',
                                                                    '3' => '3. Dokter spesialis tidak ada pada hari sebelumnya',
                                                                    '4' => '4. Atas Instruksi RS',
                                                                    '5' => '5. Tujuan Kontrol',
                                                                    default => $kunjungan->bridgingSep->asesmenpelayanan ?: '-'
                                                                } }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    {{-- Section: Pemeriksaan Rawat Jalan (Khanza Style) --}}
                                    @if($kunjungan->pemeriksaanRalan->isNotEmpty())
                                        <div class="mb-6 overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 shadow-sm">
                                            <div class="bg-neutral-50 dark:bg-neutral-900/50 px-4 py-2 border-b border-neutral-200 dark:border-neutral-700">
                                                <h4 class="text-xs font-extrabold text-[#4C5C2D] dark:text-[#8CC7C4] uppercase tracking-widest flex items-center gap-2">
                                                    <flux:icon name="clipboard-document-check" class="w-4 h-4" /> Pemeriksaan Rawat Jalan
                                                </h4>
                                            </div>
                                            <div class="p-0 overflow-x-auto">
                                                <table class="w-full text-[11px] border-collapse">
                                                    @foreach($kunjungan->pemeriksaanRalan as $index => $soapRalan)
                                                        <tbody class="border-b-4 border-neutral-100 dark:border-neutral-700 last:border-0">
                                                            {{-- Header Row --}}
                                                            <tr class="bg-neutral-50/50 dark:bg-neutral-800/50 text-[10px] font-bold text-neutral-500 uppercase tracking-tighter">
                                                                <th class="px-3 py-2 border border-neutral-200 dark:border-neutral-700 w-10">No.</th>
                                                                <th class="px-3 py-2 border border-neutral-200 dark:border-neutral-700 w-40 text-left">Tanggal</th>
                                                                <th class="px-3 py-2 border border-neutral-200 dark:border-neutral-700 text-left">Dokter/Paramedis</th>
                                                                <th class="px-3 py-2 border border-neutral-200 dark:border-neutral-700 text-left">Profesi/Jabatan</th>
                                                            </tr>
                                                            <tr>
                                                                <td class="px-3 py-2 border border-neutral-200 dark:border-neutral-700 text-center font-bold">{{ $index + 1 }}</td>
                                                                <td class="px-3 py-2 border border-neutral-200 dark:border-neutral-700 text-neutral-600 dark:text-neutral-400 font-medium">
                                                                    {{ \Carbon\Carbon::parse($soapRalan->tgl_perawatan)->format('d-m-Y') }} {{ $soapRalan->jam_rawat }}
                                                                </td>
                                                                <td class="px-3 py-2 border border-neutral-200 dark:border-neutral-700 font-bold text-neutral-800 dark:text-neutral-200">
                                                                    {{ $soapRalan->pegawai->nama ?? $soapRalan->nip }}
                                                                </td>
                                                                <td class="px-3 py-2 border border-neutral-200 dark:border-neutral-700 text-neutral-500 italic">
                                                                    {{ $soapRalan->pegawai->jbtn ?? '-' }}
                                                                </td>
                                                            </tr>

                                                            {{-- SOAP Rows --}}
                                                            @if($soapRalan->keluhan)
                                                                <tr>
                                                                    <td colspan="2" class="px-3 py-1.5 border border-neutral-200 dark:border-neutral-700 bg-neutral-50/20 dark:bg-neutral-900/10 font-bold text-neutral-500 italic text-right">Subjek :</td>
                                                                    <td colspan="2" class="px-3 py-1.5 border border-neutral-200 dark:border-neutral-700 text-neutral-700 dark:text-neutral-300 whitespace-pre-line leading-relaxed">{{ $soapRalan->keluhan }}</td>
                                                                </tr>
                                                            @endif
                                                            @if($soapRalan->pemeriksaan)
                                                                <tr>
                                                                    <td colspan="2" class="px-3 py-1.5 border border-neutral-200 dark:border-neutral-700 bg-neutral-50/20 dark:bg-neutral-900/10 font-bold text-neutral-500 italic text-right">Objek :</td>
                                                                    <td colspan="2" class="px-3 py-1.5 border border-neutral-200 dark:border-neutral-700 text-neutral-700 dark:text-neutral-300 whitespace-pre-line leading-relaxed">{{ $soapRalan->pemeriksaan }}</td>
                                                                </tr>
                                                            @endif

                                                            {{-- Vital Signs Grid --}}
                                                            <tr class="bg-neutral-100/30 dark:bg-neutral-800/30">
                                                                <td colspan="4" class="p-0 border border-neutral-200 dark:border-neutral-700">
                                                                    <div class="grid grid-cols-5 md:grid-cols-10 divide-x divide-y md:divide-y-0 divide-neutral-200 dark:divide-neutral-700">
                                                                        <div class="p-2 flex flex-col items-center">
                                                                            <span class="text-[9px] uppercase text-neutral-400 font-bold">Suhu(C)</span>
                                                                            <span class="font-bold text-neutral-700 dark:text-neutral-300">{{ $soapRalan->suhu_tubuh ?: '-' }}</span>
                                                                        </div>
                                                                        <div class="p-2 flex flex-col items-center">
                                                                            <span class="text-[9px] uppercase text-neutral-400 font-bold">Tensi</span>
                                                                            <span class="font-bold text-neutral-700 dark:text-neutral-300">{{ $soapRalan->tensi ?: '-' }}</span>
                                                                        </div>
                                                                        <div class="p-2 flex flex-col items-center">
                                                                            <span class="text-[9px] uppercase text-neutral-400 font-bold">Nadi</span>
                                                                            <span class="font-bold text-neutral-700 dark:text-neutral-300">{{ $soapRalan->nadi ?: '-' }}</span>
                                                                        </div>
                                                                        <div class="p-2 flex flex-col items-center">
                                                                            <span class="text-[9px] uppercase text-neutral-400 font-bold">Resp</span>
                                                                            <span class="font-bold text-neutral-700 dark:text-neutral-300">{{ $soapRalan->respirasi ?: '-' }}</span>
                                                                        </div>
                                                                        <div class="p-2 flex flex-col items-center">
                                                                            <span class="text-[9px] uppercase text-neutral-400 font-bold">Tinggi</span>
                                                                            <span class="font-bold text-neutral-700 dark:text-neutral-300">{{ $soapRalan->tinggi ?: '-' }}</span>
                                                                        </div>
                                                                        <div class="p-2 flex flex-col items-center">
                                                                            <span class="text-[9px] uppercase text-neutral-400 font-bold">Berat</span>
                                                                            <span class="font-bold text-neutral-700 dark:text-neutral-300">{{ $soapRalan->berat ?: '-' }}</span>
                                                                        </div>
                                                                        <div class="p-2 flex flex-col items-center">
                                                                            <span class="text-[9px] uppercase text-neutral-400 font-bold">SpO2</span>
                                                                            <span class="font-bold text-neutral-700 dark:text-neutral-300">{{ $soapRalan->spo2 ?: '-' }}</span>
                                                                        </div>
                                                                        <div class="p-2 flex flex-col items-center">
                                                                            <span class="text-[9px] uppercase text-neutral-400 font-bold">GCS</span>
                                                                            <span class="font-bold text-neutral-700 dark:text-neutral-300">{{ $soapRalan->gcs ?: '-' }}</span>
                                                                        </div>
                                                                        <div class="p-2 flex flex-col items-center">
                                                                            <span class="text-[9px] uppercase text-neutral-400 font-bold">Kesadaran</span>
                                                                            <span class="font-bold text-neutral-700 dark:text-neutral-300">{{ $soapRalan->kesadaran ?: '-' }}</span>
                                                                        </div>
                                                                        <div class="p-2 flex flex-col items-center">
                                                                            <span class="text-[9px] uppercase text-neutral-400 font-bold">L.P.</span>
                                                                            <span class="font-bold text-neutral-700 dark:text-neutral-300">{{ $soapRalan->lingkar_perut ?: '-' }}</span>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                            </tr>

                                                            {{-- Assessment & Plan --}}
                                                            @if($soapRalan->penilaian)
                                                                <tr>
                                                                    <td colspan="2" class="px-3 py-1.5 border border-neutral-200 dark:border-neutral-700 bg-neutral-50/20 dark:bg-neutral-900/10 font-bold text-neutral-500 italic text-right">Asesmen :</td>
                                                                    <td colspan="2" class="px-3 py-1.5 border border-neutral-200 dark:border-neutral-700 text-neutral-700 dark:text-neutral-300 whitespace-pre-line font-medium">{{ $soapRalan->penilaian }}</td>
                                                                </tr>
                                                            @endif
                                                            @if($soapRalan->rtl)
                                                                <tr>
                                                                    <td colspan="2" class="px-3 py-1.5 border border-neutral-200 dark:border-neutral-700 bg-neutral-50/20 dark:bg-neutral-900/10 font-bold text-neutral-500 italic text-right">Plan :</td>
                                                                    <td colspan="2" class="px-3 py-1.5 border border-neutral-200 dark:border-neutral-700 text-[#4C5C2D] dark:text-[#8CC7C4] whitespace-pre-line font-bold">{{ $soapRalan->rtl }}</td>
                                                                </tr>
                                                            @endif
                                                            @if($soapRalan->instruksi)
                                                                <tr>
                                                                    <td colspan="2" class="px-3 py-1.5 border border-neutral-200 dark:border-neutral-700 bg-neutral-50/20 dark:bg-neutral-900/10 font-bold text-neutral-500 italic text-right">Inst/Impl :</td>
                                                                    <td colspan="2" class="px-3 py-1.5 border border-neutral-200 dark:border-neutral-700 text-cyan-600 dark:text-cyan-400 whitespace-pre-line font-medium italic">{{ $soapRalan->instruksi }}</td>
                                                                </tr>
                                                            @endif
                                                            @if($soapRalan->evaluasi)
                                                                <tr>
                                                                    <td colspan="2" class="px-3 py-1.5 border border-neutral-200 dark:border-neutral-700 bg-neutral-50/20 dark:bg-neutral-900/10 font-bold text-neutral-500 italic text-right">Evaluasi :</td>
                                                                    <td colspan="2" class="px-3 py-1.5 border border-neutral-200 dark:border-neutral-700 text-rose-600 dark:text-rose-400 whitespace-pre-line font-medium italic">{{ $soapRalan->evaluasi }}</td>
                                                                </tr>
                                                            @endif
                                                        </tbody>
                                                    @endforeach
                                                </table>
                                            </div>
                                        </div>
                                    @endif
 
                                    {{-- Section: Hasil USG Kandungan (New) --}}
                                    @if($kunjungan->hasilPemeriksaanUsg)
                                        <div class="mb-6 overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 shadow-sm transition-all hover:shadow-md">
                                            <div class="bg-neutral-50 dark:bg-neutral-900/50 px-4 py-2 border-b border-neutral-200 dark:border-neutral-700 flex justify-between items-center">
                                                <h4 class="text-xs font-extrabold text-[#4C5C2D] dark:text-[#8CC7C4] uppercase tracking-widest flex items-center gap-2">
                                                    <flux:icon name="magnifying-glass-circle" class="w-4 h-4" /> Hasil USG Kandungan
                                                </h4>
                                                <span class="text-[10px] font-bold text-neutral-400 uppercase tracking-tighter">Obstetric Ultrasound Report</span>
                                            </div>
                                            
                                            <div class="p-6">
                                                {{-- Part 1: Pengkajian & Gambar --}}
                                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                                                    {{-- Meta Data --}}
                                                    <div class="space-y-4">
                                                        <h5 class="text-[10px] uppercase font-bold text-neutral-400 border-b border-neutral-100 dark:border-neutral-700 pb-1 flex items-center gap-2">
                                                            <flux:icon name="user-circle" class="w-3.5 h-3.5" /> Informasi Pengkajian
                                                        </h5>
                                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-[11px]">
                                                            <div class="flex flex-col">
                                                                <span class="text-neutral-400 italic">Tanggal Pemeriksaan</span>
                                                                <span class="font-bold text-neutral-700 dark:text-neutral-300">
                                                                    {{ \Carbon\Carbon::parse($kunjungan->hasilPemeriksaanUsg->tanggal)->format('d-m-Y H:i:s') }}
                                                                </span>
                                                            </div>
                                                            <div class="flex flex-col">
                                                                <span class="text-neutral-400 italic">Dokter Pengkaji</span>
                                                                <span class="font-bold text-neutral-700 dark:text-neutral-300">
                                                                    {{ $kunjungan->hasilPemeriksaanUsg->dokter->nm_dokter ?? $kunjungan->hasilPemeriksaanUsg->kd_dokter }}
                                                                </span>
                                                            </div>
                                                            <div class="flex flex-col">
                                                                <span class="text-neutral-400 italic">Kiriman Dari</span>
                                                                <span class="font-bold text-neutral-700 dark:text-neutral-300">
                                                                    {{ $kunjungan->hasilPemeriksaanUsg->kiriman_dari ?: '-' }}
                                                                </span>
                                                            </div>
                                                            <div class="flex flex-col">
                                                                <span class="text-neutral-400 italic">Jenis Prestasi</span>
                                                                <span class="font-bold text-neutral-700 dark:text-neutral-300">
                                                                    {{ $kunjungan->hasilPemeriksaanUsg->jenis_prestasi ?: '-' }}
                                                                </span>
                                                            </div>
                                                            <div class="flex flex-col">
                                                                <span class="text-neutral-400 italic">HTA</span>
                                                                <span class="font-bold text-neutral-700 dark:text-neutral-300">
                                                                    {{ $kunjungan->hasilPemeriksaanUsg->hta ?: '-' }}
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                                                             {{-- Image Gallery --}}
                                                    <div class="space-y-4">
                                                        <h5 class="text-[10px] uppercase font-bold text-neutral-400 border-b border-neutral-100 dark:border-neutral-700 pb-1 flex items-center gap-2">
                                                            <flux:icon name="photo" class="w-3.5 h-3.5" /> Gallery Hasil USG
                                                        </h5>
                                                        @if($kunjungan->hasilPemeriksaanUsg->gambar && $kunjungan->hasilPemeriksaanUsg->gambar->photo)
                                                            <div class="grid grid-cols-1 gap-3">
                                                                <div class="relative group aspect-[4/3] rounded-lg overflow-hidden border border-neutral-200 dark:border-neutral-700 bg-black shadow-sm">
                                                                    <img src="{{ env('KHANZA_USG_URL') . $kunjungan->hasilPemeriksaanUsg->gambar->photo }}" 
                                                                         class="w-full h-full object-contain transition-transform duration-300 group-hover:scale-110" 
                                                                         alt="Hasil USG"
                                                                         onerror="this.onerror=null; this.src='/assets/images/placeholder-image.png';">
                                                                    <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                                                        <flux:button variant="ghost" size="sm" class="!text-white" icon="magnifying-glass-plus" onclick="window.open('{{ env('KHANZA_USG_URL') . $kunjungan->hasilPemeriksaanUsg->gambar->photo }}', '_blank')">Zoom</flux:button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @else
                                                            <div class="aspect-[4/3] flex flex-col items-center justify-center border-2 border-dashed border-neutral-200 dark:border-neutral-700 rounded-xl text-neutral-400 bg-neutral-50/50 dark:bg-neutral-900/50">
                                                                <flux:icon name="photo" class="w-8 h-8 mb-2 opacity-20" />
                                                                <span class="text-[10px] font-medium italic">Tidak ada lampiran gambar USG</span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
 
                                                {{-- Part 2: Detail Biometri --}}
                                                <div class="space-y-4">
                                                    <h5 class="text-[10px] uppercase font-bold text-neutral-400 border-b border-neutral-100 dark:border-neutral-700 pb-1 flex items-center gap-2">
                                                        <flux:icon name="beaker" class="w-3.5 h-3.5" /> Biometri Janin & Keterangan
                                                    </h5>
                                                    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-x-6 gap-y-4 text-[11px]">
                                                        <div class="flex flex-col"><span class="text-neutral-400 italic">Kantong (GS)</span><span class="font-bold text-neutral-700 dark:text-neutral-200">{{ $kunjungan->hasilPemeriksaanUsg->kantong_gestasi ?: '-' }}</span></div>
                                                        <div class="flex flex-col"><span class="text-neutral-400 italic">Bokong (CRL)</span><span class="font-bold text-neutral-700 dark:text-neutral-200">{{ $kunjungan->hasilPemeriksaanUsg->ukuran_bokongkepala ?: '-' }}</span></div>
                                                        <div class="flex flex-col"><span class="text-neutral-400 italic">Kepala (BPD)</span><span class="font-bold text-neutral-700 dark:text-neutral-200">{{ $kunjungan->hasilPemeriksaanUsg->diameter_biparietal ?: '-' }}</span></div>
                                                        <div class="flex flex-col"><span class="text-neutral-400 italic">Femur (FL)</span><span class="font-bold text-neutral-700 dark:text-neutral-200">{{ $kunjungan->hasilPemeriksaanUsg->panjang_femur ?: '-' }}</span></div>
                                                        <div class="flex flex-col"><span class="text-neutral-400 italic">Perut (AC)</span><span class="font-bold text-neutral-700 dark:text-neutral-200">{{ $kunjungan->hasilPemeriksaanUsg->lingkar_abdomen ?: '-' }}</span></div>
                                                        <div class="flex flex-col"><span class="text-neutral-400 italic">BB Janin (EFW)</span><span class="font-bold text-neutral-700 dark:text-neutral-200">{{ $kunjungan->hasilPemeriksaanUsg->tafsiran_berat_janin ?: '-' }}</span></div>
                                                        <div class="flex flex-col"><span class="text-neutral-400 italic">Usia Hamil (GA)</span><span class="font-bold text-neutral-700 dark:text-neutral-200">{{ $kunjungan->hasilPemeriksaanUsg->usia_kehamilan ?: '-' }}</span></div>
                                                        <div class="flex flex-col"><span class="text-neutral-400 italic">Air Ketuban</span><span class="font-bold text-neutral-700 dark:text-neutral-200">{{ $kunjungan->hasilPemeriksaanUsg->jumlah_air_ketuban ?: '-' }}</span></div>
                                                        <div class="flex flex-col"><span class="text-neutral-400 italic">Indeks (ICA)</span><span class="font-bold text-neutral-700 dark:text-neutral-200">{{ $kunjungan->hasilPemeriksaanUsg->indek_cairan_ketuban ?: '-' }}</span></div>
                                                        <div class="flex flex-col"><span class="text-neutral-400 italic">Plasenta</span><span class="font-bold text-neutral-700 dark:text-neutral-200">{{ $kunjungan->hasilPemeriksaanUsg->plasenta_berimplatansi ?: '-' }}</span></div>
                                                    </div>
                                                </div>
 
                                                {{-- Part 3: Kesimpulan --}}
                                                <div class="mt-8 pt-6 border-t border-neutral-100 dark:border-neutral-700">
                                                    <div class="bg-neutral-50 dark:bg-neutral-900/30 rounded-xl p-5 border border-neutral-200/50 dark:border-neutral-700/50">
                                                        <span class="text-[10px] font-bold text-neutral-400 uppercase tracking-widest block mb-2">Kesimpulan / Hasil Pemeriksaan:</span>
                                                        <p class="text-sm font-semibold text-neutral-800 dark:text-neutral-100 leading-relaxed whitespace-pre-line">
                                                            {{ $kunjungan->hasilPemeriksaanUsg->kesimpulan ?: 'Tidak ada kesimpulan tertulis.' }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>
