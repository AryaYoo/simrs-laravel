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
    <div class="max-w-3xl" x-data="{ minimized: false }">
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
                <div class="text-neutral-500 py-8 text-center text-sm border-2 border-dashed border-neutral-200 dark:border-neutral-700 rounded-xl">
                    Konten Riwayat Soapie
                </div>
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
