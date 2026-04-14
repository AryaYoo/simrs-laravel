<div class="p-6 lg:p-10 transition-all duration-500 animate-in fade-in">
    {{-- Header Section --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-10 gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-neutral-800 dark:text-neutral-100 tracking-tight">
                Selamat Datang, <span class="text-[#4C5C2D]">{{ auth()->user()->name }}</span>
            </h1>
            <p class="text-neutral-500 dark:text-neutral-400 mt-1 flex items-center gap-2">
                <flux:icon name="calendar-days" class="w-4 h-4" />
                {{ now()->translatedFormat('l, d F Y') }} &bull; RSIA IBI Surabaya
            </p>
        </div>
        <div class="flex items-center gap-3">
            <flux:button icon="arrow-path" wire:click="$refresh" variant="ghost" class="text-neutral-400 hover:text-[#4C5C2D]" />
        </div>
    </div>

    {{-- Main Stats Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
        {{-- Stat Card 1: Inhouse --}}
        <flux:card class="relative overflow-hidden group hover:shadow-xl transition-all duration-300 border-none bg-gradient-to-br from-white to-neutral-50 dark:from-neutral-800 dark:to-neutral-900 shadow-sm ring-1 ring-neutral-200 dark:ring-neutral-700">
            <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:scale-110 transition-transform">
                <flux:icon name="home" class="w-16 h-16 text-[#4C5C2D]" />
            </div>
            <div class="relative z-10">
                <div class="flex items-center gap-1.5">
                    <p class="text-[10px] font-bold uppercase tracking-widest text-[#4C5C2D]/60 dark:text-[#8CC7C4]/60 mb-0">Pasien Inhouse</p>
                    <flux:tooltip content="Jumlah pasien rawat inap yang saat ini masih menempati kamar (belum pulang)." position="top">
                        <flux:icon name="information-circle" class="w-3.5 h-3.5 text-neutral-300 hover:text-[#4C5C2D] transition-colors cursor-help" />
                    </flux:tooltip>
                </div>
                <h2 class="text-4xl font-black text-neutral-800 dark:text-white leading-tight">{{ $stats['inhouse'] }}</h2>
                <div class="mt-4 flex items-center gap-1.5">
                    <span class="inline-flex items-center px-1.5 py-0.5 rounded-md bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 text-[10px] font-bold">RAWAT INAP</span>
                </div>
            </div>
        </flux:card>

        {{-- Stat Card 2: Today Reg --}}
        <flux:card class="relative overflow-hidden group hover:shadow-xl transition-all duration-300 border-none bg-gradient-to-br from-white to-neutral-50 dark:from-neutral-800 dark:to-neutral-900 shadow-sm ring-1 ring-neutral-200 dark:ring-neutral-700">
            <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:scale-110 transition-transform">
                <flux:icon name="user-plus" class="w-16 h-16 text-[#4C5C2D]" />
            </div>
            <div class="relative z-10">
                <div class="flex items-center gap-1.5">
                    <p class="text-[10px] font-bold uppercase tracking-widest text-[#4C5C2D]/60 dark:text-[#8CC7C4]/60 mb-0">Pendaftaran Hari Ini</p>
                    <flux:tooltip content="Total seluruh registrasi pasien (Rawat Jalan + Rawat Inap) yang tercatat pada hari ini." position="top">
                        <flux:icon name="information-circle" class="w-3.5 h-3.5 text-neutral-300 hover:text-[#4C5C2D] transition-colors cursor-help" />
                    </flux:tooltip>
                </div>
                <h2 class="text-4xl font-black text-neutral-800 dark:text-white leading-tight">{{ $stats['today_reg'] }}</h2>
                <div class="mt-4 flex items-center gap-1.5 text-[10px]">
                    <span class="text-neutral-400 font-medium">Beban kerja stabil hari ini</span>
                </div>
            </div>
        </flux:card>

        {{-- Stat Card 3: BPJS --}}
        <flux:card class="relative overflow-hidden group hover:shadow-xl transition-all duration-300 border-none bg-gradient-to-br from-[#4C5C2D]/5 to-[#4C5C2D]/10 dark:from-[#4C5C2D]/10 dark:to-emerald-950/20 shadow-sm ring-1 ring-[#4C5C2D]/10 dark:ring-[#4C5C2D]/30">
            <div class="absolute top-0 right-0 p-4 opacity-20 group-hover:scale-110 transition-transform">
                <flux:icon name="identification" class="w-16 h-16 text-[#4C5C2D]" />
            </div>
            <div class="relative z-10">
                <div class="flex items-center gap-1.5">
                    <p class="text-[10px] font-bold uppercase tracking-widest text-[#4C5C2D] mb-0">Bridging BPJS</p>
                    <flux:tooltip content="Jumlah registrasi pasien hari ini yang menggunakan penjamin BPJS Kesehatan." position="top">
                        <flux:icon name="information-circle" class="w-3.5 h-3.5 text-[#4C5C2D]/40 hover:text-[#4C5C2D] transition-colors cursor-help" />
                    </flux:tooltip>
                </div>
                <h2 class="text-4xl font-black text-[#4C5C2D] dark:text-[#8CC7C4] leading-tight">{{ $stats['today_bpjs'] }}</h2>
                <div class="mt-4 flex items-center gap-1.5 text-[10px]">
                    <span class="flex items-center gap-1 text-[#4C5C2D] dark:text-[#8CC7C4] font-bold">
                        <flux:icon name="check-circle" class="w-3 h-3" /> E-Claim Aktif
                    </span>
                </div>
            </div>
        </flux:card>

        {{-- Stat Card 4: Beds --}}
        <flux:card class="relative overflow-hidden group hover:shadow-xl transition-all duration-300 border-none bg-gradient-to-br from-white to-neutral-50 dark:from-neutral-800 dark:to-neutral-900 shadow-sm ring-1 ring-neutral-200 dark:ring-neutral-700">
            <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:scale-110 transition-transform">
                <flux:icon name="building-office-2" class="w-16 h-16 text-[#4C5C2D]" />
            </div>
            <div class="relative z-10">
                <div class="flex items-center gap-1.5">
                    <p class="text-[10px] font-bold uppercase tracking-widest text-neutral-400 mb-0">Okupansi Bed</p>
                    <flux:tooltip content="Perbandingan kamar terisi vs total kamar aktif. Menunjukkan tingkat kepenuhan fasilitas rawat inap." position="top">
                        <flux:icon name="information-circle" class="w-3.5 h-3.5 text-neutral-300 hover:text-[#4C5C2D] transition-colors cursor-help" />
                    </flux:tooltip>
                </div>
                <div class="flex items-baseline gap-2">
                    <h2 class="text-4xl font-black text-neutral-800 dark:text-white leading-tight">{{ $stats['beds']['filled'] }}</h2>
                    <span class="text-lg font-bold text-neutral-300">/ {{ $stats['beds']['total'] }}</span>
                </div>
                <div class="mt-4 w-full bg-neutral-100 dark:bg-neutral-800 rounded-full h-1.5">
                    @php $percentage = $stats['beds']['total'] > 0 ? ($stats['beds']['filled'] / $stats['beds']['total']) * 100 : 0; @endphp
                    <div class="bg-[#4C5C2D] h-1.5 rounded-full transition-all duration-1000" style="width: {{ $percentage }}%"></div>
                </div>
            </div>
        </flux:card>
    </div>

    {{-- Secondary Content Row --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-stretch">
        
        {{-- Trend Chart Area --}}
        <div class="lg:col-span-8">
            <flux:card class="h-full flex flex-col p-6 border-none shadow-sm ring-1 ring-neutral-200 dark:ring-neutral-700">
                <div class="flex items-center justify-between mb-8">
                    <div>
                    <div class="flex items-center gap-2">
                        <h3 class="text-lg font-bold text-neutral-800 dark:text-neutral-100">Tren Kunjungan Pasien</h3>
                        <flux:tooltip content="Grafik batang yang menampilkan jumlah total registrasi pasien per hari selama 7 hari terakhir. Hover pada batang untuk melihat angka pasti.">
                            <flux:icon name="information-circle" class="w-4 h-4 text-neutral-300 hover:text-[#4C5C2D] transition-colors cursor-help" />
                        </flux:tooltip>
                    </div>
                    <p class="text-xs text-neutral-400">Total registrasi 7 hari terakhir</p>
                    </div>
                </div>

                {{-- Mock SVG Chart for performance (Can replace with Chart.js later) --}}
                <div class="flex-1 min-h-[250px] relative flex items-end justify-between px-4 pb-8 border-b border-neutral-100 dark:border-neutral-800">
                    <div class="absolute inset-0 flex flex-col justify-between py-8 opacity-50 pointer-events-none">
                        <div class="border-t border-neutral-100 dark:border-neutral-800 w-full"></div>
                        <div class="border-t border-neutral-100 dark:border-neutral-800 w-full"></div>
                        <div class="border-t border-neutral-100 dark:border-neutral-800 w-full"></div>
                    </div>
                    
                    @php $maxCount = collect($trendData)->max('count') ?: 1; @endphp
                    @foreach($trendData as $data)
                        <div class="flex flex-col items-center gap-4 z-10 group relative flex-1">
                            @php $height = ($data['count'] / $maxCount) * 100; @endphp
                            <div class="absolute -top-10 opacity-0 group-hover:opacity-100 transition-opacity bg-neutral-800 text-white text-[10px] px-2 py-1 rounded shadow-lg">
                                {{ $data['count'] }} Pasien
                            </div>
                            <div class="w-8 md:w-12 bg-gradient-to-t from-[#4C5C2D] to-[#4C5C2D]/60 rounded-t-lg transition-all duration-1000 ease-out hover:brightness-125" style="height: {{ max($height, 5) }}%"></div>
                            <span class="text-[10px] font-bold text-neutral-400 uppercase tracking-tighter">{{ $data['date'] }}</span>
                        </div>
                    @endforeach
                </div>
            </flux:card>
        </div>

        {{-- Insurance Distribution --}}
        <div class="lg:col-span-4">
            <flux:card class="h-full flex flex-col p-6 border-none shadow-sm ring-1 ring-neutral-200 dark:ring-neutral-700">
                <div class="flex items-center gap-2 mb-6">
                    <h3 class="text-lg font-bold text-neutral-800 dark:text-neutral-100">Top Penjamin</h3>
                    <flux:tooltip content="5 besar penjamin/asuransi berdasarkan jumlah registrasi pasien selama 30 hari terakhir.">
                        <flux:icon name="information-circle" class="w-4 h-4 text-neutral-300 hover:text-[#4C5C2D] transition-colors cursor-help" />
                    </flux:tooltip>
                </div>
                
                <div class="flex flex-col gap-5 flex-1">
                    @forelse($insuranceDist as $dist)
                        <div class="flex flex-col gap-1.5">
                            <div class="flex justify-between items-center text-xs">
                                <span class="font-bold text-neutral-700 dark:text-neutral-200">{{ $dist['label'] }}</span>
                                <span class="font-mono text-neutral-400">{{ $dist['count'] }}</span>
                            </div>
                            <div class="w-full bg-neutral-100 dark:bg-neutral-800 rounded-full h-1.5 overflow-hidden">
                                @php $distPerc = collect($insuranceDist)->sum('count') > 0 ? ($dist['count'] / collect($insuranceDist)->sum('count')) * 100 : 0; @endphp
                                <div class="bg-gradient-to-r from-[#4C5C2D]/80 to-[#4C5C2D] h-full rounded-full" style="width: {{ $distPerc }}%"></div>
                            </div>
                        </div>
                    @empty
                        <div class="flex-1 flex flex-col items-center justify-center text-center opacity-40">
                            <flux:icon name="identification" class="w-10 h-10 mb-2" />
                            <p class="text-xs uppercase font-bold tracking-widest">Data Kosong</p>
                        </div>
                    @endforelse
                </div>

            </flux:card>
        </div>
    </div>

    {{-- Recent Activity Table --}}
    <div class="mt-10">
        <div class="flex items-center mb-6">
            <div class="flex items-center gap-2">
                <h3 class="text-xl font-bold text-neutral-800 dark:text-neutral-100 px-1 border-l-4 border-[#4C5C2D]">Registrasi Terbaru</h3>
                <flux:tooltip content="Menampilkan 10 pendaftaran pasien terakhir secara real-time, meliputi informasi dokter, penjamin, dan status pelayanan.">
                    <flux:icon name="information-circle" class="w-4 h-4 text-neutral-300 hover:text-[#4C5C2D] transition-colors cursor-help" />
                </flux:tooltip>
            </div>
        </div>

        <div class="px-1">
            <flux:table class="shadow-sm ring-1 ring-neutral-200 dark:ring-neutral-700 rounded-2xl bg-white dark:bg-neutral-800">
            <flux:table.columns>
                <flux:table.column><div class="ps-6">Waktu</div></flux:table.column>
                <flux:table.column>No. Rawat</flux:table.column>
                <flux:table.column>Pasien</flux:table.column>
                <flux:table.column>Dokter DPJP</flux:table.column>
                <flux:table.column><div class="w-full text-center">Penjamin</div></flux:table.column>
                <flux:table.column><div class="w-full text-center">Status</div></flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @foreach($recent as $reg)
                    <flux:table.row class="group hover:bg-[#F1F5E9] dark:hover:bg-neutral-700/30 transition-colors">
                        <flux:table.cell class="whitespace-nowrap">
                            <div class="ps-6">
                                <span class="text-xs font-bold text-neutral-700 dark:text-neutral-200 block">{{ $reg->tgl_registrasi }}</span>
                                <span class="text-[10px] text-neutral-400">{{ $reg->jam_reg }}</span>
                            </div>
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-nowrap font-mono text-[11px] text-[#4C5C2D] font-bold">{{ $reg->no_rawat }}</flux:table.cell>
                        <flux:table.cell class="whitespace-nowrap">
                            <span class="text-sm font-bold text-neutral-800 dark:text-neutral-100 block">{{ $reg->pasien->nm_pasien ?? '-' }}</span>
                            <span class="text-[10px] text-neutral-400 font-medium">RM: {{ $reg->no_rkm_medis }}</span>
                        </flux:table.cell>
                        <flux:table.cell class="text-xs text-neutral-600 dark:text-neutral-400 whitespace-nowrap">{{ $reg->dokter->nm_dokter ?? '-' }}</flux:table.cell>
                        <flux:table.cell class="text-center whitespace-nowrap">
                            <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ str_contains($reg->penjab->png_jawab ?? '', 'BPJS') ? 'bg-blue-50 text-blue-600 border border-blue-100' : 'bg-neutral-50 text-neutral-600 border border-neutral-100' }}">
                                {{ $reg->penjab->png_jawab ?? '-' }}
                            </span>
                        </flux:table.cell>
                        <flux:table.cell class="text-center whitespace-nowrap">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-tight {{ $reg->stts == 'Belum' ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-700' }}">
                                <span class="w-1 h-1 rounded-full {{ $reg->stts == 'Belum' ? 'bg-amber-500' : 'bg-emerald-500' }}"></span>
                                {{ $reg->stts }}
                            </span>
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
    </div>
    </div>
</div>
