<div class="p-6 lg:p-8 transition-all duration-500 animate-in fade-in space-y-8"
     x-data="{
         showDetail: false,
         selectedReg: null,
         checklist: null,
         openChecklist(reg, checklistData) {
             this.selectedReg = reg;
             this.checklist = checklistData;
             this.showDetail = true;
         }
     }">

    {{-- ═══ HEADER ══════════════════════════════════════════════════════════════ --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-neutral-800 dark:text-neutral-100 tracking-tight">
                Selamat Datang, <span class="text-[#4C5C2D]">{{ auth()->user()->name }}</span>
            </h1>
            <p class="text-neutral-500 dark:text-neutral-400 mt-1 flex items-center gap-2 text-sm">
                <flux:icon name="calendar-days" class="w-4 h-4" />
                {{ now()->translatedFormat('l, d F Y') }} &bull; RSIA IBI Surabaya
            </p>
        </div>
        <flux:button icon="arrow-path" wire:click="$refresh" variant="ghost" class="text-neutral-400 hover:text-[#4C5C2D]" />
    </div>

    {{-- ═══ KPI CARDS ════════════════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @php
        $kpiCards = [
            ['label'=>'Pasien Inhouse','value'=>$stats['inhouse'],'icon'=>'home','badge'=>'RAWAT INAP','color'=>'emerald','tip'=>'Pasien rawat inap yang masih menempati kamar.'],
            ['label'=>'Pendaftaran Hari Ini','value'=>$stats['today_reg'],'icon'=>'user-plus','badge'=>'TOTAL','color'=>'sky','tip'=>'Total registrasi hari ini (RJ + RI).'],
            ['label'=>'BPJS Hari Ini','value'=>$stats['today_bpjs'],'icon'=>'identification','badge'=>'BPJS','color'=>'olive','tip'=>'Registrasi BPJS Kesehatan hari ini.'],
            ['label'=>'Okupansi Bed','value'=>$stats['beds']['filled'].' / '.$stats['beds']['total'],'icon'=>'building-office-2','badge'=>null,'color'=>'amber','tip'=>'Kamar terisi vs total kamar aktif.','progress'=>$stats['beds']['total']>0?round($stats['beds']['filled']/$stats['beds']['total']*100):0],
        ];
        $colorMap = [
            'emerald'=>['ring'=>'ring-emerald-200 dark:ring-emerald-800','text'=>'text-emerald-700 dark:text-emerald-400','badge'=>'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-400','bar'=>'bg-emerald-500'],
            'sky'    =>['ring'=>'ring-sky-200 dark:ring-sky-800',       'text'=>'text-sky-700 dark:text-sky-400',          'badge'=>'bg-sky-100 text-sky-700 dark:bg-sky-900/40 dark:text-sky-400',         'bar'=>'bg-sky-500'],
            'olive'  =>['ring'=>'ring-[#4C5C2D]/30 dark:ring-[#4C5C2D]/50','text'=>'text-[#4C5C2D] dark:text-[#8CC7C4]',  'badge'=>'bg-[#4C5C2D]/10 text-[#4C5C2D] dark:bg-[#4C5C2D]/20 dark:text-[#8CC7C4]','bar'=>'bg-[#4C5C2D]'],
            'amber'  =>['ring'=>'ring-amber-200 dark:ring-amber-800',   'text'=>'text-amber-700 dark:text-amber-400',      'badge'=>'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-400',   'bar'=>'bg-amber-500'],
        ];
        @endphp

        @foreach($kpiCards as $card)
        @php $c = $colorMap[$card['color']]; @endphp
        <div class="relative overflow-hidden bg-white dark:bg-neutral-800 rounded-2xl p-5 ring-1 {{ $c['ring'] }} shadow-sm hover:shadow-lg transition-all duration-300 group">
            <div class="absolute top-3 right-3 opacity-10 group-hover:opacity-20 group-hover:scale-110 transition-all duration-300">
                <flux:icon name="{{ $card['icon'] }}" class="w-16 h-16 {{ $c['text'] }}" />
            </div>
            <div class="relative z-10">
                <div class="flex items-center gap-1.5 mb-1">
                    <span class="text-[10px] font-bold uppercase tracking-widest text-neutral-400">{{ $card['label'] }}</span>
                    <flux:tooltip content="{{ $card['tip'] }}" position="top">
                        <flux:icon name="information-circle" class="w-3.5 h-3.5 text-neutral-300 hover:text-[#4C5C2D] cursor-help" />
                    </flux:tooltip>
                </div>
                <div class="text-3xl font-black {{ $c['text'] }} leading-tight">{{ $card['value'] }}</div>
                @if($card['badge'])
                    <span class="mt-3 inline-flex px-2 py-0.5 rounded-md text-[10px] font-bold {{ $c['badge'] }}">{{ $card['badge'] }}</span>
                @endif
                @if(isset($card['progress']))
                    <div class="mt-3 w-full bg-neutral-100 dark:bg-neutral-700 rounded-full h-1.5 overflow-hidden">
                        <div class="{{ $c['bar'] }} h-full rounded-full transition-all duration-1000" style="width: {{ $card['progress'] }}%"></div>
                    </div>
                    <span class="text-[10px] text-neutral-400 mt-1 block">{{ $card['progress'] }}% terisi</span>
                @endif
            </div>
        </div>
        @endforeach
    </div>

    {{-- ═══ TREN KUNJUNGAN BULANAN ════════════════════════════════════════════════ --}}
    <div class="bg-white dark:bg-neutral-800 rounded-2xl ring-1 ring-neutral-200 dark:ring-neutral-700 shadow-sm p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-base font-bold text-neutral-800 dark:text-neutral-100 flex items-center gap-2">
                    <flux:icon name="chart-bar" class="w-5 h-5 text-[#4C5C2D]" />
                    Tren Kunjungan Bulanan
                </h2>
                <p class="text-xs text-neutral-400 mt-0.5">6 bulan terakhir – dibagi per jenis penjamin</p>
            </div>
            <div class="flex items-center gap-4 text-[11px] font-semibold">
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-sm bg-[#4C5C2D] inline-block"></span>BPJS</span>
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-sm bg-sky-500 inline-block"></span>Umum</span>
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-sm bg-neutral-300 inline-block"></span>Lainnya</span>
            </div>
        </div>

        {{-- Chart.js Canvas --}}
        <div class="relative" style="height: 280px;" wire:ignore>
            <canvas id="trendChart"></canvas>
        </div>
    </div>

    {{-- ═══ ANALISIS PENDAPATAN PASIEN UMUM (TOP 10) ═══════════════════════════ --}}
    <div class="bg-white dark:bg-neutral-800 rounded-2xl ring-1 ring-neutral-200 dark:ring-neutral-700 shadow-sm p-6">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h2 class="text-base font-bold text-neutral-800 dark:text-neutral-100 flex items-center gap-2">
                    <flux:icon name="chart-bar-square" class="w-5 h-5 text-[#4C5C2D]" />
                    Analisis Pendapatan Pasien Umum (Top 10)
                </h2>
                <p class="text-xs text-neutral-400 mt-0.5">Berdasarkan total tagihan billing seluruh kunjungan (pasien dengan penjamin Umum)</p>
            </div>
            <span class="hidden md:flex items-center gap-1 px-3 py-1 rounded-full bg-neutral-100 dark:bg-neutral-900/20 text-neutral-600 dark:text-neutral-400 text-xs font-bold ring-1 ring-neutral-200 dark:ring-neutral-800">
                <flux:icon name="document-chart-bar" class="w-3.5 h-3.5" />
                Detail Analitik
            </span>
        </div>

        @if($topUmumPatients->isEmpty())
            <div class="flex flex-col items-center justify-center py-12 text-neutral-400">
                <flux:icon name="presentation-chart-bar" class="w-12 h-12 mb-3 opacity-40" />
                <p class="text-sm font-medium">Belum ada data analitik pasien umum</p>
            </div>
        @else
        @php
            $maxSpend = $topUmumPatients->first()->total_pengeluaran;
        @endphp
        <div class="space-y-3">
            @foreach($topUmumPatients as $idx => $patient)
            @php
                $pct = $maxSpend > 0 ? round(($patient->total_pengeluaran / $maxSpend) * 100) : 0;
            @endphp
            <div wire:click="loadPatientVisits('{{ $patient->no_rkm_medis }}', '{{ addslashes($patient->nm_pasien) }}')"
                 class="flex items-center gap-3 group cursor-pointer rounded-xl px-2 py-2 -mx-2 hover:bg-neutral-50 dark:hover:bg-neutral-700/40 transition-colors">
                
                {{-- Rank Number --}}
                <div class="flex-shrink-0 w-8 h-8 rounded-md bg-neutral-100 dark:bg-neutral-700 text-neutral-500 dark:text-neutral-300 flex items-center justify-center text-xs font-bold shadow-sm">
                    {{ $idx + 1 }}
                </div>

                {{-- Patient Info + Bar --}}
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between mb-1 gap-2">
                        <div class="flex items-center gap-2 min-w-0">
                            <span class="text-sm font-semibold text-neutral-800 dark:text-neutral-100 truncate group-hover:text-[#4C5C2D] dark:group-hover:text-[#8CC7C4] transition-colors">{{ $patient->nm_pasien }}</span>
                            <span class="flex-shrink-0 font-mono text-[10px] text-neutral-400 dark:text-neutral-500">{{ $patient->no_rkm_medis }}</span>
                        </div>
                        <div class="flex items-center gap-2 flex-shrink-0">
                            <span class="text-[10px] px-1.5 py-0.5 rounded bg-[#4C5C2D]/10 dark:bg-[#4C5C2D]/20 text-[#4C5C2D] dark:text-[#8CC7C4] font-semibold">
                                {{ $patient->total_kunjungan }} kunjungan
                            </span>
                            <span class="text-sm font-bold text-neutral-700 dark:text-neutral-200 w-32 text-right">
                                Rp {{ number_format($patient->total_pengeluaran, 0, ',', '.') }}
                            </span>
                            <flux:icon name="eye" class="w-4 h-4 text-neutral-300 group-hover:text-[#4C5C2D] dark:group-hover:text-[#8CC7C4] transition-colors flex-shrink-0" />
                        </div>
                    </div>
                    <div class="w-full bg-neutral-100 dark:bg-neutral-700 rounded-full h-1.5 overflow-hidden">
                        <div class="h-full rounded-full transition-all duration-700 bg-[#4C5C2D]" style="width: {{ $pct }}%"></div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    {{-- ═══ POPUP: Detail Kunjungan Pasien ══════════════════════════════════════ --}}
    @if($patientVisitModalOpen)
    <template x-teleport="body">
        <div x-data="{ open: @entangle('patientVisitModalOpen') }"
             x-show="open"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-[9999] flex items-center justify-center p-4 sm:p-6"
             style="display: none;"
             @keydown.escape.window="open = false">

            {{-- Backdrop --}}
            <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="open = false"></div>

            {{-- Panel --}}
            <div x-show="open"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                 x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                 class="relative w-full max-w-2xl bg-white dark:bg-neutral-800 rounded-2xl shadow-2xl z-10 overflow-hidden flex flex-col max-h-[85vh]">

                {{-- Header --}}
                <div class="flex items-center justify-between px-6 py-4 border-b border-neutral-100 dark:border-neutral-700 bg-[#F7F9F3] dark:bg-neutral-900/50">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-[#4C5C2D]/10 dark:bg-[#4C5C2D]/20 flex items-center justify-center">
                            <flux:icon name="user" class="w-5 h-5 text-[#4C5C2D] dark:text-[#8CC7C4]" />
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-neutral-800 dark:text-neutral-100">{{ $selectedPatientName }}</h3>
                            <p class="text-xs text-neutral-400 font-mono">No. RM: {{ $selectedPatientRm }}</p>
                        </div>
                    </div>
                    <button @click="open = false" class="w-8 h-8 rounded-lg flex items-center justify-center text-neutral-400 hover:text-neutral-700 dark:hover:text-neutral-200 hover:bg-neutral-100 dark:hover:bg-neutral-700 transition-colors">
                        <flux:icon name="x-mark" class="w-5 h-5" />
                    </button>
                </div>

                {{-- Body --}}
                <div class="overflow-y-auto flex-1 p-5">
                    <p class="text-xs text-neutral-400 mb-4 font-semibold uppercase tracking-wider">Riwayat Kunjungan – Penjamin Umum</p>

                    @if(empty($patientVisits))
                        <div class="flex flex-col items-center justify-center py-10 text-neutral-400">
                            <flux:icon name="calendar-x-mark" class="w-10 h-10 mb-2 opacity-40" />
                            <p class="text-sm">Tidak ada riwayat kunjungan ditemukan.</p>
                        </div>
                    @else
                    <div class="space-y-3">
                        @foreach($patientVisits as $i => $visit)
                        @php
                            $visit = (object) $visit;
                            $sttsColor = match($visit->stts ?? '') {
                                'Sudah'   => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400',
                                'Batal'   => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                                'Belum'   => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
                                'Dirawat' => 'bg-sky-100 text-sky-700 dark:bg-sky-900/30 dark:text-sky-400',
                                default   => 'bg-neutral-100 text-neutral-500 dark:bg-neutral-700 dark:text-neutral-400',
                            };
                        @endphp
                        <div class="flex items-start gap-4 p-4 rounded-xl bg-neutral-50 dark:bg-neutral-900/40 border border-neutral-100 dark:border-neutral-700 hover:border-[#4C5C2D]/30 transition-colors">
                            {{-- Visit Number --}}
                            <div class="flex-shrink-0 w-7 h-7 rounded-full bg-[#4C5C2D]/10 dark:bg-[#4C5C2D]/20 flex items-center justify-center text-[10px] font-bold text-[#4C5C2D] dark:text-[#8CC7C4]">
                                {{ $i + 1 }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex flex-wrap items-start justify-between gap-2 mb-1.5">
                                    <div>
                                        <div class="text-xs font-bold font-mono text-neutral-700 dark:text-neutral-200">{{ $visit->no_rawat }}</div>
                                        <div class="text-[11px] text-neutral-400 mt-0.5">
                                            {{ \Carbon\Carbon::parse($visit->tgl_registrasi)->translatedFormat('d F Y') }}
                                            <span class="mx-1 opacity-40">·</span>
                                            {{ substr($visit->jam_reg ?? '', 0, 5) }}
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-1.5 flex-shrink-0">
                                        <span class="text-[10px] px-2 py-0.5 rounded-full font-bold {{ $sttsColor }}">{{ $visit->stts ?? '-' }}</span>
                                        @if($visit->status_lanjut)
                                        <span class="text-[10px] px-2 py-0.5 rounded-full font-bold bg-sky-100 text-sky-700 dark:bg-sky-900/30 dark:text-sky-400">{{ $visit->status_lanjut }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex items-center justify-between gap-2">
                                    <div class="flex items-center gap-1.5 text-xs text-neutral-500 dark:text-neutral-400">
                                        <flux:icon name="user-circle" class="w-3.5 h-3.5" />
                                        <span>{{ $visit->nm_dokter ?? 'Dokter tidak tercatat' }}</span>
                                    </div>
                                    @if($visit->total_biaya > 0)
                                    <span class="text-xs font-bold text-[#4C5C2D] dark:text-[#8CC7C4]">
                                        Rp {{ number_format($visit->total_biaya, 0, ',', '.') }}
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>

                {{-- Footer --}}
                <div class="px-6 py-3 border-t border-neutral-100 dark:border-neutral-700 flex justify-between items-center bg-neutral-50 dark:bg-neutral-900/30">
                    <span class="text-xs text-neutral-400">{{ count($patientVisits) }} kunjungan ditemukan</span>
                    <button @click="open = false" class="px-4 py-1.5 rounded-lg text-sm font-semibold bg-[#4C5C2D] text-white hover:bg-[#3d4b24] transition-colors">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </template>
    @endif

    {{-- ═══ MONITORING KELENGKAPAN DATA (PER NO RAWAT) ══════════════════════════ --}}
    <div class="bg-white dark:bg-neutral-800 rounded-2xl ring-1 ring-neutral-200 dark:ring-neutral-700 shadow-sm overflow-hidden flex flex-col">
        <div class="px-6 py-4 border-b border-neutral-100 dark:border-neutral-700 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-2">
                <h2 class="text-base font-bold text-neutral-800 dark:text-neutral-100 flex items-center gap-2">
                    <flux:icon name="clipboard-document-check" class="w-5 h-5 text-[#4C5C2D]" />
                    Monitoring Kelengkapan Data Pasien
                </h2>
            </div>
            <div class="flex items-center gap-3 w-full md:w-auto">
                <flux:input type="date" wire:model.live="filterDate" class="w-full md:w-40" />
                <flux:input type="text" wire:model.live.debounce.500ms="filterSearch" placeholder="Cari Pasien / No. Rawat..." class="w-full md:w-64" icon="magnifying-glass" />
            </div>
        </div>
        
        <div class="overflow-x-auto relative">
            <div wire:loading.delay.longer class="absolute inset-0 bg-white/50 dark:bg-neutral-900/50 backdrop-blur-sm z-10 flex items-center justify-center">
                <flux:icon name="arrow-path" class="w-8 h-8 text-[#4C5C2D] animate-spin" />
            </div>

            <table class="w-full text-sm">
                <thead class="bg-neutral-50 dark:bg-neutral-900/50 text-[10px] font-bold uppercase tracking-widest text-neutral-400 border-b border-neutral-100 dark:border-neutral-700">
                    <tr>
                        <th class="px-5 py-3 text-left w-32">Waktu</th>
                        <th class="px-5 py-3 text-left w-40">No. Rawat</th>
                        <th class="px-5 py-3 text-left min-w-[200px]">Pasien / RM</th>
                        <th class="px-5 py-3 text-left w-48">Dokter DPJP</th>
                        <th class="px-5 py-3 text-center w-28">Penjamin</th>
                        <th class="px-5 py-3 text-center w-24">Jenis</th>
                        <th class="px-5 py-3 text-center w-36">Kelengkapan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100 dark:divide-neutral-700">
                    @forelse($registrations as $reg)
                    @php 
                        $cl = $checklistData[$reg->no_rawat] ?? null;
                        $isRanap = $reg->status_lanjut === 'Ranap';
                        $regArray = [
                            'no_rawat' => $reg->no_rawat,
                            'nm_pasien' => $reg->nm_pasien,
                            'jenis' => $isRanap ? 'RANAP' : 'RALAN',
                        ];
                        
                        // Calculate percentage
                        $totalPoints = 0;
                        $earnedPoints = 0;
                        if ($cl) {
                            if ($isRanap) {
                                $totalPoints += 5;
                                if ($cl['ranap']['pengkajian']) $earnedPoints++;
                                if ($cl['ranap']['dr'] || $cl['ranap']['pr'] || $cl['ranap']['drpr']) $earnedPoints++;
                                if ($cl['ranap']['diagnosa']) $earnedPoints++;
                                if ($cl['ranap']['catatan']) $earnedPoints++;
                                if ($cl['ranap']['resume']) $earnedPoints++;
                            } else {
                                $totalPoints += 4;
                                if ($cl['ralan']['dr'] || $cl['ralan']['pr'] || $cl['ralan']['drpr']) $earnedPoints++;
                                if ($cl['ralan']['diagnosa']) $earnedPoints++;
                                if ($cl['ralan']['catatan']) $earnedPoints++;
                                if ($cl['ralan']['resume']) $earnedPoints++;
                            }

                            if ($cl['farmasi']['permintaan'] || $cl['farmasi']['pemberian'] || $cl['farmasi']['validasi']) {
                                $totalPoints += 2;
                                if ($cl['farmasi']['pemberian']) $earnedPoints++;
                                if ($cl['farmasi']['validasi']) $earnedPoints++;
                            }
                            if ($cl['lab']['permintaan'] || $cl['lab']['hasil']) {
                                $totalPoints += 1;
                                if ($cl['lab']['hasil']) $earnedPoints++;
                            }
                            if ($cl['radiologi']['permintaan'] || $cl['radiologi']['hasil']) {
                                $totalPoints += 2;
                                if ($cl['radiologi']['hasil']) $earnedPoints++;
                                if ($cl['radiologi']['selesai']) $earnedPoints++;
                            }
                        }
                        $pct = $totalPoints > 0 ? round(($earnedPoints / $totalPoints) * 100) : 0;
                        $pctColor = $pct >= 100 ? 'text-emerald-500' : ($pct >= 50 ? 'text-amber-500' : 'text-rose-500');
                    @endphp
                    <tr wire:key="{{ $reg->no_rawat }}" class="hover:bg-[#F7F9F3] dark:hover:bg-neutral-700/40 transition-colors">
                        <td class="px-5 py-3 whitespace-nowrap">
                            <div class="text-xs font-semibold text-neutral-700 dark:text-neutral-200">{{ $reg->tgl_registrasi }}</div>
                            <div class="text-[10px] text-neutral-400">{{ $reg->jam_reg }}</div>
                        </td>
                        <td class="px-5 py-3 whitespace-nowrap font-mono text-[11px] text-[#4C5C2D] dark:text-[#8CC7C4] font-bold">{{ $reg->no_rawat }}</td>
                        <td class="px-5 py-3 whitespace-nowrap">
                            <div class="text-sm font-semibold text-neutral-800 dark:text-neutral-100">{{ $reg->nm_pasien }}</div>
                            <div class="text-[10px] text-neutral-400">RM: {{ $reg->no_rkm_medis }}</div>
                        </td>
                        <td class="px-5 py-3 whitespace-nowrap text-xs text-neutral-600 dark:text-neutral-400">{{ $reg->nm_dokter ?? '-' }}</td>
                        <td class="px-5 py-3 text-center whitespace-nowrap">
                            @php $isBpjs = str_contains($reg->png_jawab ?? '', 'BPJS'); @endphp
                            <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $isBpjs ? 'bg-[#4C5C2D]/10 text-[#4C5C2D]' : 'bg-neutral-100 text-neutral-600 dark:bg-neutral-700 dark:text-neutral-300' }}">
                                {{ Str::limit($reg->png_jawab ?? '-', 12) }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-center whitespace-nowrap">
                            <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-bold {{ $isRanap ? 'bg-sky-100 text-sky-700 dark:bg-sky-900/30 dark:text-sky-400' : 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' }}">
                                {{ $isRanap ? 'RANAP' : 'RALAN' }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-center whitespace-nowrap">
                            <div class="flex items-center justify-center gap-3">
                                <span class="text-xs font-bold {{ $pctColor }}">{{ $pct }}%</span>
                                <button
                                    type="button"
                                    @click='openChecklist({{ json_encode($regArray, JSON_HEX_APOS) }}, {{ json_encode($cl, JSON_HEX_APOS) }})'
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-[11px] font-bold rounded-lg bg-[#4C5C2D] hover:bg-[#3b4723] text-white transition-colors shadow-sm"
                                >
                                    <flux:icon name="check-badge" class="w-4 h-4" />
                                    Cek Kelengkapan
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-5 py-10 text-center text-neutral-400">
                            <div class="flex flex-col items-center justify-center gap-2">
                                <flux:icon name="inbox" class="w-10 h-10 opacity-50" />
                                <span>Tidak ada data registrasi yang sesuai.</span>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($registrations->hasPages())
        <div class="px-6 py-4 border-t border-neutral-100 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-900/30">
            {{ $registrations->links() }}
        </div>
        @endif
    </div>

    {{-- ═══ MODAL CHECKLIST KELENGKAPAN (Alpine.js) ═════════════════════════════ --}}
    <div x-show="showDetail" x-cloak class="fixed inset-0 z-[99] flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-neutral-900/60 backdrop-blur-sm" @click="showDetail = false"></div>

        <div class="relative bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-200 dark:border-neutral-800 shadow-2xl max-w-2xl w-full overflow-hidden flex flex-col max-h-[90vh]" @click.stop>
            {{-- Modal Header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-neutral-100 dark:border-neutral-800 bg-neutral-50 dark:bg-neutral-900/50">
                <div class="flex flex-col">
                    <h3 class="text-lg font-bold text-neutral-800 dark:text-neutral-100 flex items-center gap-2">
                        <flux:icon name="clipboard-document-check" class="w-5 h-5 text-[#4C5C2D]" />
                        Checklist Kelengkapan Data
                    </h3>
                    <div class="flex items-center gap-2 mt-1">
                        <span class="text-xs font-medium text-neutral-500" x-text="selectedReg?.no_rawat"></span>
                        <span class="text-xs text-neutral-300">&bull;</span>
                        <span class="text-xs font-bold text-[#4C5C2D] dark:text-[#8CC7C4]" x-text="selectedReg?.nm_pasien"></span>
                    </div>
                </div>
                <button @click="showDetail = false" class="p-2 rounded-lg text-neutral-400 hover:bg-neutral-200 dark:hover:bg-neutral-800 transition-colors">
                    <flux:icon name="x-mark" class="w-5 h-5" />
                </button>
            </div>

            {{-- Modal Body --}}
            <template x-if="selectedReg && checklist">
                <div class="flex-1 overflow-y-auto p-6 space-y-6">
                    
                    {{-- 1. Tindakan & Pemeriksaan --}}
                    <div>
                        <h4 class="text-[11px] font-bold uppercase tracking-widest text-neutral-400 mb-3" x-text="'Tindakan & Pemeriksaan ' + selectedReg.jenis"></h4>
                        <div class="bg-white dark:bg-neutral-900 ring-1 ring-neutral-200 dark:ring-neutral-800 rounded-xl overflow-hidden divide-y divide-neutral-100 dark:divide-neutral-800">
                            
                            <template x-if="selectedReg.jenis === 'RANAP'">
                                <div class="flex items-center justify-between p-3">
                                    <span class="text-sm font-medium text-neutral-700 dark:text-neutral-300">Pengkajian Awal Keperawatan</span>
                                    <div class="inline-flex">
                                        <flux:icon name="check-circle" class="w-5 h-5 text-emerald-500" x-show="checklist.ranap.pengkajian" />
                                        <flux:icon name="x-circle" class="w-5 h-5 text-rose-500" x-show="!checklist.ranap.pengkajian" />
                                    </div>
                                </div>
                            </template>
                            
                            <div class="flex items-center justify-between p-3">
                                <span class="text-sm font-medium text-neutral-700 dark:text-neutral-300">Tindakan / Penanganan</span>
                                <div class="inline-flex">
                                    <flux:icon name="check-circle" class="w-5 h-5 text-emerald-500" x-show="selectedReg.jenis === 'RANAP' ? (checklist.ranap.dr || checklist.ranap.pr || checklist.ranap.drpr) : (checklist.ralan.dr || checklist.ralan.pr || checklist.ralan.drpr)" />
                                    <flux:icon name="x-circle" class="w-5 h-5 text-rose-500" x-show="selectedReg.jenis === 'RANAP' ? !(checklist.ranap.dr || checklist.ranap.pr || checklist.ranap.drpr) : !(checklist.ralan.dr || checklist.ralan.pr || checklist.ralan.drpr)" />
                                </div>
                            </div>

                            <div class="flex items-center justify-between p-3 bg-neutral-50 dark:bg-neutral-800/30">
                                <span class="text-sm font-medium text-neutral-700 dark:text-neutral-300">Diagnosa</span>
                                <div class="inline-flex">
                                    <flux:icon name="check-circle" class="w-5 h-5 text-emerald-500" x-show="(selectedReg.jenis === 'RANAP' ? checklist.ranap.diagnosa : checklist.ralan.diagnosa)" />
                                    <flux:icon name="x-circle" class="w-5 h-5 text-rose-500" x-show="!(selectedReg.jenis === 'RANAP' ? checklist.ranap.diagnosa : checklist.ralan.diagnosa)" />
                                </div>
                            </div>

                            <div class="flex items-center justify-between p-3 bg-neutral-50 dark:bg-neutral-800/30">
                                <span class="text-sm font-medium text-neutral-700 dark:text-neutral-300">Catatan Dokter</span>
                                <div class="inline-flex">
                                    <flux:icon name="check-circle" class="w-5 h-5 text-emerald-500" x-show="(selectedReg.jenis === 'RANAP' ? checklist.ranap.catatan : checklist.ralan.catatan)" />
                                    <flux:icon name="x-circle" class="w-5 h-5 text-rose-500" x-show="!(selectedReg.jenis === 'RANAP' ? checklist.ranap.catatan : checklist.ralan.catatan)" />
                                </div>
                            </div>

                            <div class="flex items-center justify-between p-3 bg-neutral-50 dark:bg-neutral-800/30">
                                <span class="text-sm font-medium text-neutral-700 dark:text-neutral-300">Resume Pasien</span>
                                <div class="inline-flex">
                                    <flux:icon name="check-circle" class="w-5 h-5 text-emerald-500" x-show="(selectedReg.jenis === 'RANAP' ? checklist.ranap.resume : checklist.ralan.resume)" />
                                    <flux:icon name="x-circle" class="w-5 h-5 text-rose-500" x-show="!(selectedReg.jenis === 'RANAP' ? checklist.ranap.resume : checklist.ralan.resume)" />
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 2. Penunjang --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Farmasi --}}
                        <div>
                            <h4 class="text-[11px] font-bold uppercase tracking-widest text-[#7c3aed] mb-3">Farmasi</h4>
                            <div class="bg-white dark:bg-neutral-900 ring-1 ring-neutral-200 dark:ring-neutral-800 rounded-xl overflow-hidden divide-y divide-neutral-100 dark:divide-neutral-800">
                                <div class="flex items-center justify-between p-2.5">
                                    <span class="text-xs font-medium text-neutral-700 dark:text-neutral-300">Permintaan Obat</span>
                                    <div>
                                        <flux:icon name="check-circle" class="w-4 h-4 text-emerald-500" x-show="checklist.farmasi.permintaan" />
                                        <span class="text-[10px] text-neutral-400 font-bold bg-neutral-100 px-1.5 py-0.5 rounded" x-show="!checklist.farmasi.permintaan">TIDAK ADA</span>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between p-2.5">
                                    <span class="text-xs font-medium text-neutral-700 dark:text-neutral-300">Pemberian Obat</span>
                                    <div>
                                        <flux:icon name="check-circle" class="w-4 h-4 text-emerald-500" x-show="checklist.farmasi.pemberian" />
                                        <span class="text-[10px] text-neutral-400 font-bold bg-neutral-100 px-1.5 py-0.5 rounded" x-show="!checklist.farmasi.pemberian" x-text="(checklist.farmasi.permintaan || checklist.farmasi.pemberian || checklist.farmasi.validasi) ? 'BELUM' : 'TIDAK ADA'"></span>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between p-2.5">
                                    <span class="text-xs font-medium text-neutral-700 dark:text-neutral-300">Status Validasi</span>
                                    <div>
                                        <flux:icon name="check-circle" class="w-4 h-4 text-emerald-500" x-show="checklist.farmasi.validasi" />
                                        <span class="text-[10px] text-neutral-400 font-bold bg-neutral-100 px-1.5 py-0.5 rounded" x-show="!checklist.farmasi.validasi" x-text="(checklist.farmasi.permintaan || checklist.farmasi.pemberian || checklist.farmasi.validasi) ? 'BELUM' : 'TIDAK ADA'"></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Laboratorium & Radiologi --}}
                        <div class="space-y-4">
                            <div>
                                <h4 class="text-[11px] font-bold uppercase tracking-widest text-[#b45309] mb-3">Laboratorium</h4>
                                <div class="bg-white dark:bg-neutral-900 ring-1 ring-neutral-200 dark:ring-neutral-800 rounded-xl overflow-hidden divide-y divide-neutral-100 dark:divide-neutral-800">
                                    <div class="flex items-center justify-between p-2.5">
                                        <span class="text-xs font-medium text-neutral-700 dark:text-neutral-300">Permintaan Lab</span>
                                        <div>
                                            <flux:icon name="check-circle" class="w-4 h-4 text-emerald-500" x-show="checklist.lab.permintaan" />
                                            <span class="text-[10px] text-neutral-400 font-bold bg-neutral-100 px-1.5 py-0.5 rounded" x-show="!checklist.lab.permintaan">TIDAK ADA</span>
                                        </div>
                                    </div>
                                    <div class="flex items-center justify-between p-2.5">
                                        <span class="text-xs font-medium text-neutral-700 dark:text-neutral-300">Hasil Lab</span>
                                        <div>
                                            <flux:icon name="check-circle" class="w-4 h-4 text-emerald-500" x-show="checklist.lab.hasil" />
                                            <span class="text-[10px] text-neutral-400 font-bold bg-neutral-100 px-1.5 py-0.5 rounded" x-show="!checklist.lab.hasil" x-text="(checklist.lab.permintaan || checklist.lab.hasil) ? 'BELUM' : 'TIDAK ADA'"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <h4 class="text-[11px] font-bold uppercase tracking-widest text-[#be185d] mb-3">Radiologi</h4>
                                <div class="bg-white dark:bg-neutral-900 ring-1 ring-neutral-200 dark:ring-neutral-800 rounded-xl overflow-hidden divide-y divide-neutral-100 dark:divide-neutral-800">
                                    <div class="flex items-center justify-between p-2.5">
                                        <span class="text-xs font-medium text-neutral-700 dark:text-neutral-300">Permintaan Rad</span>
                                        <div>
                                            <flux:icon name="check-circle" class="w-4 h-4 text-emerald-500" x-show="checklist.radiologi.permintaan" />
                                            <span class="text-[10px] text-neutral-400 font-bold bg-neutral-100 px-1.5 py-0.5 rounded" x-show="!checklist.radiologi.permintaan">TIDAK ADA</span>
                                        </div>
                                    </div>
                                    <div class="flex items-center justify-between p-2.5">
                                        <span class="text-xs font-medium text-neutral-700 dark:text-neutral-300">Hasil Rad</span>
                                        <div>
                                            <flux:icon name="check-circle" class="w-4 h-4 text-emerald-500" x-show="checklist.radiologi.hasil" />
                                            <span class="text-[10px] text-neutral-400 font-bold bg-neutral-100 px-1.5 py-0.5 rounded" x-show="!checklist.radiologi.hasil" x-text="(checklist.radiologi.permintaan || checklist.radiologi.hasil) ? 'BELUM' : 'TIDAK ADA'"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </template>
        </div>
    </div>
</div>

{{-- ─── Chart.js untuk Tren Kunjungan ─────────────────────────────────────── --}}
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    initTrendChart();
});

// Re-init saat Livewire refresh
document.addEventListener('livewire:navigated', function () {
    initTrendChart();
});

function initTrendChart() {
    const canvas = document.getElementById('trendChart');
    if (!canvas) return;

    // Destroy old instance if exists
    if (window.__trendChartInstance) {
        window.__trendChartInstance.destroy();
    }

    const isDark = document.documentElement.classList.contains('dark');

    const labels  = @json(array_column($trendData, 'label'));
    const bpjs    = @json(array_column($trendData, 'bpjs'));
    const umum    = @json(array_column($trendData, 'umum'));
    const lainnya = @json(array_column($trendData, 'lainnya'));

    window.__trendChartInstance = new Chart(canvas, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'BPJS',
                    data: bpjs,
                    backgroundColor: '#4C5C2D',
                    borderRadius: { topLeft: 0, topRight: 0, bottomLeft: 4, bottomRight: 4 },
                    borderSkipped: false,
                },
                {
                    label: 'Umum',
                    data: umum,
                    backgroundColor: '#0ea5e9',
                    borderRadius: { topLeft: 0, topRight: 0, bottomLeft: 0, bottomRight: 0 },
                    borderSkipped: false,
                },
                {
                    label: 'Lainnya',
                    data: lainnya,
                    backgroundColor: isDark ? '#525252' : '#d4d4d4',
                    borderRadius: { topLeft: 4, topRight: 4, bottomLeft: 0, bottomRight: 0 },
                    borderSkipped: false,
                },
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: isDark ? '#1f2937' : '#1f2937',
                    titleColor: '#f3f4f6',
                    bodyColor: '#d1d5db',
                    borderColor: '#374151',
                    borderWidth: 1,
                    callbacks: {
                        afterBody: function(items) {
                            const total = items.reduce((s, i) => s + i.parsed.y, 0);
                            return ['', `Total: ${total} kunjungan`];
                        }
                    }
                }
            },
            scales: {
                x: {
                    stacked: true,
                    grid: { display: false },
                    ticks: {
                        color: isDark ? '#9ca3af' : '#6b7280',
                        font: { size: 11, weight: '600' }
                    },
                    border: { display: false }
                },
                y: {
                    stacked: true,
                    grid: {
                        color: isDark ? '#374151' : '#f3f4f6',
                        drawTicks: false,
                    },
                    ticks: {
                        color: isDark ? '#9ca3af' : '#6b7280',
                        font: { size: 11 },
                        padding: 8,
                        precision: 0,
                    },
                    border: { display: false }
                }
            }
        }
    });
}
</script>
@endpush
