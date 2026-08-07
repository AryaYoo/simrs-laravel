<div class="flex flex-col gap-6 pb-12">
    {{-- Header / Navigation --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <button onclick="history.back()" class="flex items-center justify-center w-10 h-8 rounded-md bg-[#4C5C2D] transition-colors hover:bg-[#3d4b24] shadow-sm">
                <flux:icon name="chevron-left" class="w-5 h-5 text-white" />
            </button>
            <div>
                <nav class="text-xs text-neutral-400 mb-0.5">
                    <a href="{{ route('modul.index') }}" wire:navigate class="hover:underline">Modul</a>
                    <span class="mx-1">/</span>
                    <a href="{{ route('modul.casemix.kustom-laporan-operasi') }}" wire:navigate class="hover:underline">Casemix</a>
                    <span class="mx-1">/</span>
                    <span class="text-neutral-700 dark:text-neutral-300 font-medium">Kustom Cetak Laporan Operasi</span>
                </nav>
                <h1 class="text-xl font-bold text-neutral-800 dark:text-neutral-100">Kustomisasi Cetak Laporan Operasi</h1>
            </div>
        </div>


    </div>

    {{-- Info Pasien & Operasi Banner --}}
    <div class="bg-white dark:bg-neutral-800 rounded-xl border border-neutral-200 dark:border-neutral-700 p-4 sm:p-5 shadow-sm">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-xs">
            <div>
                <span class="text-neutral-400 block mb-0.5 font-medium uppercase tracking-wider text-[11px]">No. Rawat</span>
                <span class="font-bold text-neutral-800 dark:text-neutral-100 text-sm">{{ $laporan->no_rawat }}</span>
            </div>
            <div>
                <span class="text-neutral-400 block mb-0.5 font-medium uppercase tracking-wider text-[11px]">Nama Pasien / RM</span>
                <span class="font-bold text-neutral-800 dark:text-neutral-100 text-sm">{{ $laporan->regPeriksa->pasien->nm_pasien ?? '-' }}</span>
                <span class="text-neutral-400 block text-[11px]">({{ $laporan->regPeriksa->pasien->no_rkm_medis ?? '-' }})</span>
            </div>
            <div>
                <span class="text-neutral-400 block mb-0.5 font-medium uppercase tracking-wider text-[11px]">Tanggal Operasi</span>
                <span class="font-bold text-[#4C5C2D] dark:text-[#8CC7C4] text-sm">{{ date('d-m-Y H:i', strtotime($laporan->tanggal)) }}</span>
            </div>
            <div>
                <span class="text-neutral-400 block mb-0.5 font-medium uppercase tracking-wider text-[11px]">Diagnosa Preop</span>
                <span class="font-semibold text-neutral-800 dark:text-neutral-200 block truncate">{{ $laporan->diagnosa_preop ?: '-' }}</span>
            </div>
        </div>
    </div>

    {{-- Main 2-Column Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Kolom Kiri: Pilih Sumber Pemeriksaan (2/3 width) --}}
        <div class="lg:col-span-2 flex flex-col gap-4">

            <div class="bg-white dark:bg-neutral-800 rounded-xl border border-neutral-200 dark:border-neutral-700 p-4 sm:p-5 shadow-sm">
                <div class="flex items-center justify-between mb-4 pb-2 border-b border-neutral-200 dark:border-neutral-700">
                    <div>
                        <h2 class="text-sm font-bold text-neutral-800 dark:text-neutral-100 flex items-center gap-2">
                            <flux:icon name="document-text" class="w-4 h-4 text-[#4C5C2D] dark:text-[#8CC7C4]" />
                            <span>Pilih Data Pre Surgical Assessment</span>
                        </h2>
                        <p class="text-xs text-neutral-400 mt-0.5">Pilih salah satu pemeriksaan medis di bawah untuk dimasukkan ke dalam dokumen cetak PDF.</p>
                    </div>

                    {{-- Tab Switcher --}}
                    <div class="flex items-center bg-neutral-100 dark:bg-neutral-900 p-1 rounded-lg">
                        <button type="button"
                            wire:click="$set('tab', 'ranap')"
                            class="px-3 py-1.5 rounded-md text-xs font-bold transition-all {{ $tab === 'ranap' ? 'bg-white dark:bg-neutral-700 text-[#4C5C2D] dark:text-[#8CC7C4] shadow-sm' : 'text-neutral-500 hover:text-neutral-800 dark:hover:text-neutral-200' }}">
                            Rawat Inap ({{ count($pemeriksaanRanapList) }})
                        </button>
                        <button type="button"
                            wire:click="$set('tab', 'ralan')"
                            class="px-3 py-1.5 rounded-md text-xs font-bold transition-all {{ $tab === 'ralan' ? 'bg-white dark:bg-neutral-700 text-[#4C5C2D] dark:text-[#8CC7C4] shadow-sm' : 'text-neutral-500 hover:text-neutral-800 dark:hover:text-neutral-200' }}">
                            Rawat Jalan ({{ count($pemeriksaanRalanList) }})
                        </button>
                    </div>
                </div>

                {{-- Opsi Tanpa Pre Surgical --}}
                <div class="mb-4">
                    <button type="button"
                        wire:click="disablePreSurgical"
                        class="w-full p-3 rounded-xl border text-left transition-all flex items-center justify-between {{ $selectedSource === 'none' ? 'border-[#4C5C2D] bg-[#4C5C2D]/5 dark:bg-[#4C5C2D]/20' : 'border-dashed border-neutral-300 dark:border-neutral-600 hover:border-neutral-400' }}">
                        <div class="flex items-center gap-3">
                            <div class="w-4 h-4 rounded-full border-2 flex items-center justify-center {{ $selectedSource === 'none' ? 'border-[#4C5C2D] bg-[#4C5C2D]' : 'border-neutral-400' }}">
                                @if($selectedSource === 'none')
                                    <div class="w-1.5 h-1.5 rounded-full bg-white"></div>
                                @endif
                            </div>
                            <div>
                                <span class="text-xs font-bold text-neutral-800 dark:text-neutral-200 block">Tanpa Section PRE SURGICAL ASSESMENT</span>
                                <span class="text-[11px] text-neutral-400">Cetak dokumen Laporan Operasi langsung dari Post Surgical Report tanpa menyertakan riwayat pemeriksaan.</span>
                            </div>
                        </div>
                    </button>
                </div>

                {{-- List Pemeriksaan Rawat Inap --}}
                @if($tab === 'ranap')
                    <div class="flex flex-col gap-3">
                        @forelse($pemeriksaanRanapList as $item)
                            @php
                                $isSelected = ($selectedSource === 'ranap' && $selectedTgl === $item->tgl_perawatan && $selectedJam === $item->jam_rawat);
                            @endphp
                            <div wire:click="selectPemeriksaan('ranap', '{{ $item->tgl_perawatan }}', '{{ $item->jam_rawat }}')"
                                class="p-3.5 rounded-xl border cursor-pointer transition-all hover:shadow-sm flex flex-col gap-2 {{ $isSelected ? 'border-[#4C5C2D] bg-[#4C5C2D]/5 dark:bg-[#4C5C2D]/20 ring-1 ring-[#4C5C2D]' : 'border-neutral-200 dark:border-neutral-700 bg-neutral-50/50 dark:bg-neutral-900/30 hover:border-neutral-300' }}">

                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <div class="w-4 h-4 rounded-full border-2 flex items-center justify-center {{ $isSelected ? 'border-[#4C5C2D] bg-[#4C5C2D]' : 'border-neutral-400' }}">
                                            @if($isSelected)
                                                <div class="w-1.5 h-1.5 rounded-full bg-white"></div>
                                            @endif
                                        </div>
                                        <span class="font-bold text-xs text-neutral-800 dark:text-neutral-100">
                                            📅 {{ date('d-m-Y', strtotime($item->tgl_perawatan)) }} (Jam {{ $item->jam_rawat }})
                                        </span>
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300">
                                            Ranap
                                        </span>
                                    </div>
                                    <span class="text-xs font-medium text-neutral-500">
                                        Petugas: {{ $item->nm_dokter ?? $item->nama_petugas ?? '-' }}
                                    </span>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs pt-1 border-t border-neutral-200/60 dark:border-neutral-700/60">
                                    <div>
                                        <span class="text-neutral-400 font-semibold block text-[11px]">Keluhan:</span>
                                        <span class="text-neutral-700 dark:text-neutral-300 italic line-clamp-2">{{ $item->keluhan ?: '-' }}</span>
                                    </div>
                                    <div>
                                        <span class="text-neutral-400 font-semibold block text-[11px]">Penilaian:</span>
                                        <span class="text-neutral-700 dark:text-neutral-300 italic line-clamp-2">{{ $item->penilaian ?: '-' }}</span>
                                    </div>
                                </div>

                                <div class="flex flex-wrap items-center gap-3 text-[11px] text-neutral-500 pt-1">
                                    <span>Tensi: <strong class="text-neutral-700 dark:text-neutral-300">{{ $item->tensi ?: '-' }}</strong></span>
                                    <span>Nadi: <strong class="text-neutral-700 dark:text-neutral-300">{{ $item->nadi ?: '-' }}</strong></span>
                                    <span>Suhu: <strong class="text-neutral-700 dark:text-neutral-300">{{ $item->suhu_tubuh ?: '-' }} °C</strong></span>
                                    <span>SPO2: <strong class="text-neutral-700 dark:text-neutral-300">{{ $item->spo2 ?: '-' }}%</strong></span>
                                </div>
                            </div>
                        @empty
                            <div class="py-8 text-center text-neutral-400 text-xs bg-neutral-50 dark:bg-neutral-900/50 rounded-xl border border-dashed border-neutral-300 dark:border-neutral-700">
                                <flux:icon name="exclamation-triangle" class="w-6 h-6 mx-auto mb-2 opacity-50" />
                                Tidak ada data pemeriksaan rawat inap (`pemeriksaan_ranap`) untuk no. rawat ini.
                            </div>
                        @endforelse
                    </div>
                @endif

                {{-- List Pemeriksaan Rawat Jalan --}}
                @if($tab === 'ralan')
                    <div class="flex flex-col gap-3">
                        @forelse($pemeriksaanRalanList as $item)
                            @php
                                $isSelected = ($selectedSource === 'ralan' && $selectedTgl === $item->tgl_perawatan && $selectedJam === $item->jam_rawat);
                            @endphp
                            <div wire:click="selectPemeriksaan('ralan', '{{ $item->tgl_perawatan }}', '{{ $item->jam_rawat }}')"
                                class="p-3.5 rounded-xl border cursor-pointer transition-all hover:shadow-sm flex flex-col gap-2 {{ $isSelected ? 'border-[#4C5C2D] bg-[#4C5C2D]/5 dark:bg-[#4C5C2D]/20 ring-1 ring-[#4C5C2D]' : 'border-neutral-200 dark:border-neutral-700 bg-neutral-50/50 dark:bg-neutral-900/30 hover:border-neutral-300' }}">

                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <div class="w-4 h-4 rounded-full border-2 flex items-center justify-center {{ $isSelected ? 'border-[#4C5C2D] bg-[#4C5C2D]' : 'border-neutral-400' }}">
                                            @if($isSelected)
                                                <div class="w-1.5 h-1.5 rounded-full bg-white"></div>
                                            @endif
                                        </div>
                                        <span class="font-bold text-xs text-neutral-800 dark:text-neutral-100">
                                            📅 {{ date('d-m-Y', strtotime($item->tgl_perawatan)) }} (Jam {{ $item->jam_rawat }})
                                        </span>
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-900/50 dark:text-emerald-300">
                                            Rajal
                                        </span>
                                        @if(($item->no_rawat_ralan ?? '') !== $laporan->no_rawat)
                                            <span class="text-[10px] text-neutral-400">({{ $item->no_rawat_ralan }})</span>
                                        @endif
                                    </div>
                                    <span class="text-xs font-medium text-neutral-500">
                                        Petugas: {{ $item->nm_dokter ?? $item->nama_petugas ?? '-' }}
                                    </span>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs pt-1 border-t border-neutral-200/60 dark:border-neutral-700/60">
                                    <div>
                                        <span class="text-neutral-400 font-semibold block text-[11px]">Keluhan:</span>
                                        <span class="text-neutral-700 dark:text-neutral-300 italic line-clamp-2">{{ $item->keluhan ?: '-' }}</span>
                                    </div>
                                    <div>
                                        <span class="text-neutral-400 font-semibold block text-[11px]">Penilaian:</span>
                                        <span class="text-neutral-700 dark:text-neutral-300 italic line-clamp-2">{{ $item->penilaian ?: '-' }}</span>
                                    </div>
                                </div>

                                <div class="flex flex-wrap items-center gap-3 text-[11px] text-neutral-500 pt-1">
                                    <span>Tensi: <strong class="text-neutral-700 dark:text-neutral-300">{{ $item->tensi ?: '-' }}</strong></span>
                                    <span>Nadi: <strong class="text-neutral-700 dark:text-neutral-300">{{ $item->nadi ?: '-' }}</strong></span>
                                    <span>Suhu: <strong class="text-neutral-700 dark:text-neutral-300">{{ $item->suhu_tubuh ?: '-' }} °C</strong></span>
                                    <span>SPO2: <strong class="text-neutral-700 dark:text-neutral-300">{{ $item->spo2 ?: '-' }}%</strong></span>
                                </div>
                            </div>
                        @empty
                            <div class="py-8 text-center text-neutral-400 text-xs bg-neutral-50 dark:bg-neutral-900/50 rounded-xl border border-dashed border-neutral-300 dark:border-neutral-700">
                                <flux:icon name="exclamation-triangle" class="w-6 h-6 mx-auto mb-2 opacity-50" />
                                Tidak ada data pemeriksaan rawat jalan (`pemeriksaan_ralan`) untuk pasien ini.
                            </div>
                        @endforelse
                    </div>
                @endif
            </div>

        </div>

        {{-- Kolom Kanan: Preview Terpilih & Action Cetak (1/3 width) --}}
        <div class="flex flex-col gap-4">
            <div class="bg-white dark:bg-neutral-800 rounded-xl border border-neutral-200 dark:border-neutral-700 p-4 sm:p-5 shadow-sm sticky top-4">
                <h3 class="text-xs font-bold uppercase tracking-wider text-neutral-400 mb-3 pb-2 border-b border-neutral-200 dark:border-neutral-700 flex items-center gap-1.5">
                    <flux:icon name="eye" class="w-4 h-4 text-[#4C5C2D] dark:text-[#8CC7C4]" />
                    <span>Preview Ringkasan Cetak</span>
                </h3>

                @if($selectedSource === 'none')
                    <div class="p-4 rounded-xl bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800/40 text-amber-800 dark:text-amber-300 text-xs mb-4">
                        <span class="font-bold block mb-1">⚠️ Tanpa Pre Surgical Assessment</span>
                        Dokumen PDF hanya akan mencetak section Laporan Operasi & Tagihan Perawatan.
                    </div>
                @elseif($selectedItem)
                    <div class="flex flex-col gap-2.5 text-xs mb-4 p-3 rounded-xl bg-neutral-50 dark:bg-neutral-900/50 border border-neutral-200 dark:border-neutral-700">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-[#4C5C2D] dark:text-[#8CC7C4]">
                                {{ $selectedSource === 'ranap' ? 'Pemeriksaan Rawat Inap' : 'Pemeriksaan Rawat Jalan' }}
                            </span>
                            <span class="text-[11px] text-neutral-500 font-semibold">
                                {{ date('d-m-Y', strtotime($selectedItem->tgl_perawatan)) }} {{ $selectedItem->jam_rawat }}
                            </span>
                        </div>

                        <div>
                            <span class="text-neutral-400 text-[11px] font-semibold block">Dokter Bedah / Pemeriksa:</span>
                            <span class="font-bold text-neutral-800 dark:text-neutral-100">
                                {{ $selectedItem->nm_dokter ?? $selectedItem->nama_petugas ?? '-' }}
                            </span>
                        </div>

                        <div>
                            <span class="text-neutral-400 text-[11px] font-semibold block">Keluhan:</span>
                            <span class="italic text-neutral-700 dark:text-neutral-300 block">
                                {{ $selectedItem->keluhan ?: '-' }}
                            </span>
                        </div>

                        <div>
                            <span class="text-neutral-400 text-[11px] font-semibold block">Penilaian:</span>
                            <span class="italic text-neutral-700 dark:text-neutral-300 block">
                                {{ $selectedItem->penilaian ?: '-' }}
                            </span>
                        </div>

                        <div class="pt-2 border-t border-neutral-200 dark:border-neutral-700 grid grid-cols-2 gap-1 text-[11px]">
                            <span>Tensi: <strong>{{ $selectedItem->tensi ?: '-' }}</strong></span>
                            <span>Nadi: <strong>{{ $selectedItem->nadi ?: '-' }}</strong></span>
                            <span>Suhu: <strong>{{ $selectedItem->suhu_tubuh ?: '-' }} °C</strong></span>
                            <span>SPO2: <strong>{{ $selectedItem->spo2 ?: '-' }}%</strong></span>
                        </div>
                    </div>
                @endif

                <a href="{{ $printUrl }}"
                    target="_blank"
                    class="w-full py-3 px-4 rounded-xl text-xs font-bold bg-[#4C5C2D] text-white hover:bg-[#3d4b24] transition-all shadow-md flex items-center justify-center gap-2">
                    <flux:icon name="printer" class="w-4 h-4" />
                    <span>Buka PDF Laporan Operasi</span>
                </a>
            </div>
        </div>

    </div>
</div>
