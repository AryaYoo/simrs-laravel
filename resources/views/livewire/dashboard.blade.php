<div class="p-6 lg:p-8 transition-all duration-500 animate-in fade-in space-y-8">

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
        <div class="relative" style="height: 280px;">
            <canvas id="trendChart"></canvas>
        </div>
    </div>

    {{-- ═══ KELENGKAPAN DATA PASIEN ═══════════════════════════════════════════════ --}}
    <div>
        <div class="flex items-center gap-2 mb-4">
            <h2 class="text-base font-bold text-neutral-800 dark:text-neutral-100 flex items-center gap-2">
                <flux:icon name="clipboard-document-check" class="w-5 h-5 text-[#4C5C2D]" />
                Kelengkapan Pengisian Data Pasien
            </h2>
            <span class="text-xs text-neutral-400 bg-neutral-100 dark:bg-neutral-700 px-2.5 py-0.5 rounded-full font-medium">{{ $currentMonth }}</span>
        </div>

        @php
        $units = [
            [
                'key'   => 'ralan',
                'title' => 'Rawat Jalan',
                'icon'  => 'user-circle',
                'color' => '#4C5C2D',
                'data'  => $ralanKelengkapan,
            ],
            [
                'key'   => 'ranap',
                'title' => 'Rawat Inap',
                'icon'  => 'building-office',
                'color' => '#0284c7',
                'data'  => $ranapKelengkapan,
            ],
            [
                'key'   => 'farmasi',
                'title' => 'Farmasi',
                'icon'  => 'beaker',
                'color' => '#7c3aed',
                'data'  => $farmasiKelengkapan,
            ],
            [
                'key'   => 'lab',
                'title' => 'Laboratorium',
                'icon'  => 'eye-dropper',
                'color' => '#b45309',
                'data'  => $labKelengkapan,
            ],
            [
                'key'   => 'radiologi',
                'title' => 'Radiologi',
                'icon'  => 'photo',
                'color' => '#be185d',
                'data'  => $radiologiKelengkapan,
            ],
        ];
        @endphp

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
            @foreach($units as $unit)
            @php
                $avgPct = count($unit['data']['items']) > 0
                    ? round(collect($unit['data']['items'])->avg('pct'))
                    : 0;
                $circumference = 2 * M_PI * 42;
                $dashoffset = $circumference * (1 - $avgPct / 100);
            @endphp
            <div class="bg-white dark:bg-neutral-800 rounded-2xl ring-1 ring-neutral-200 dark:ring-neutral-700 shadow-sm p-5 hover:shadow-md transition-shadow duration-200">
                {{-- Card Header --}}
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-xl flex items-center justify-center" style="background: {{ $unit['color'] }}20">
                            <flux:icon name="{{ $unit['icon'] }}" class="w-5 h-5" style="color: {{ $unit['color'] }}" />
                        </div>
                        <div>
                            <div class="text-sm font-bold text-neutral-800 dark:text-neutral-100">{{ $unit['title'] }}</div>
                            <div class="text-[10px] text-neutral-400">{{ $unit['data']['total'] }} kunjungan bulan ini</div>
                        </div>
                    </div>

                    {{-- Donut Percentage --}}
                    <div class="relative w-16 h-16 flex-shrink-0">
                        <svg class="w-16 h-16 -rotate-90" viewBox="0 0 96 96">
                            <circle cx="48" cy="48" r="42" fill="none" stroke="currentColor" class="text-neutral-100 dark:text-neutral-700" stroke-width="9" />
                            <circle cx="48" cy="48" r="42" fill="none"
                                stroke="{{ $unit['color'] }}"
                                stroke-width="9"
                                stroke-linecap="round"
                                stroke-dasharray="{{ number_format($circumference, 2) }}"
                                stroke-dashoffset="{{ number_format($dashoffset, 2) }}"
                                style="transition: stroke-dashoffset 1s ease"
                            />
                        </svg>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <span class="text-xs font-extrabold" style="color: {{ $unit['color'] }}">{{ $avgPct }}%</span>
                        </div>
                    </div>
                </div>

                {{-- Progress Bars --}}
                <div class="space-y-2.5">
                    @foreach($unit['data']['items'] as $item)
                    <div>
                        <div class="flex justify-between items-center mb-1">
                            <span class="text-[11px] text-neutral-600 dark:text-neutral-400 font-medium leading-tight">{{ $item['label'] }}</span>
                            <div class="flex items-center gap-1.5 flex-shrink-0">
                                <span class="text-[10px] font-mono text-neutral-400">{{ number_format($item['value']) }}</span>
                                <span class="text-[10px] font-bold tabular-nums" style="color: {{ $unit['color'] }}; min-width: 3ch; text-align: right">{{ $item['pct'] }}%</span>
                            </div>
                        </div>
                        <div class="w-full bg-neutral-100 dark:bg-neutral-700 rounded-full overflow-hidden h-1.5">
                            <div class="h-full rounded-full transition-all duration-700" style="width: {{ $item['pct'] }}%; background: {{ $unit['color'] }}"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- ═══ REGISTRASI TERBARU ════════════════════════════════════════════════════ --}}
    <div class="bg-white dark:bg-neutral-800 rounded-2xl ring-1 ring-neutral-200 dark:ring-neutral-700 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-neutral-100 dark:border-neutral-700 flex items-center gap-2">
            <h2 class="text-sm font-bold text-neutral-800 dark:text-neutral-100">Registrasi Terbaru</h2>
            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-neutral-50 dark:bg-neutral-900/50 text-[10px] font-bold uppercase tracking-widest text-neutral-400 border-b border-neutral-100 dark:border-neutral-700">
                    <tr>
                        <th class="px-5 py-3 text-left">Waktu</th>
                        <th class="px-5 py-3 text-left">No. Rawat</th>
                        <th class="px-5 py-3 text-left">Pasien / RM</th>
                        <th class="px-5 py-3 text-left">Dokter DPJP</th>
                        <th class="px-5 py-3 text-center">Penjamin</th>
                        <th class="px-5 py-3 text-center">Jenis</th>
                        <th class="px-5 py-3 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100 dark:divide-neutral-700">
                    @foreach($recent as $reg)
                    <tr class="hover:bg-[#F7F9F3] dark:hover:bg-neutral-700/40 transition-colors">
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
                            @php $isRanap = $reg->status_lanjut === 'Ranap'; @endphp
                            <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-bold {{ $isRanap ? 'bg-sky-100 text-sky-700 dark:bg-sky-900/30 dark:text-sky-400' : 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' }}">
                                {{ $isRanap ? 'RANAP' : 'RALAN' }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-center whitespace-nowrap">
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold {{ $reg->stts === 'Belum' ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-700' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $reg->stts === 'Belum' ? 'bg-amber-500' : 'bg-emerald-500' }}"></span>
                                {{ $reg->stts }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
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
