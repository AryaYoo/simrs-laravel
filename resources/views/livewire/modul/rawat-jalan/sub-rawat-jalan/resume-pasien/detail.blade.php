<div class="flex flex-col gap-6 pb-24 relative" 
     x-data="{ showScrollTop: false }" 
     @scroll.window="showScrollTop = (window.pageYOffset > 400)">
    
    {{-- Floating Action Buttons --}}
    <div class="fixed bottom-8 right-8 flex flex-col gap-3 z-50 no-print">
        {{-- Scroll to Top --}}
        <button x-show="showScrollTop" 
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-10"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-300"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 translate-y-10"
                @click="window.scrollTo({ top: 0, behavior: 'smooth' })"
                class="w-12 h-12 rounded-full bg-white dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 shadow-2xl flex items-center justify-center text-neutral-500 hover:text-[#4C5C2D] dark:hover:text-[#8CC7C4] transition-all group">
            <flux:icon name="chevron-up" class="w-6 h-6 group-hover:-translate-y-1 transition-transform" />
        </button>

        {{-- Quick Edit FAB --}}
        <a href="{{ route('modul.rawat-jalan.sub-rawat-jalan.resume-form', ['no_rawat' => str_replace('/', '-', $resume->no_rawat), 'mode' => 'edit']) }}" wire:navigate
           class="w-14 h-14 rounded-full bg-[#4C5C2D] shadow-2xl flex items-center justify-center text-white hover:bg-[#3D4A24] transition-all group active:scale-95">
            <flux:icon name="pencil-square" class="w-7 h-7 group-hover:rotate-12 transition-transform" />
        </a>
    </div>

    {{-- Header / Breadcrumb --}}
    <div class="flex items-center justify-between no-print">
        <div class="flex items-center gap-3">
            <a href="{{ route('modul.rawat-jalan.sub-rawat-jalan.resume', str_replace('/', '-', $resume->no_rawat)) }}" wire:navigate class="flex items-center justify-center w-10 h-8 rounded-md bg-[#4C5C2D] transition-colors hover:bg-[#3d4b24] shadow-sm">
            <flux:icon name="chevron-left" class="w-5 h-5 text-white" />
        </a>
            <div>
                <nav class="text-xs text-neutral-400 mb-0.5">
                    <a href="{{ route('modul.index') }}" wire:navigate class="hover:underline">Modul</a>
                    <span class="mx-1">/</span>
                    <a href="{{ route('modul.rawat-jalan.index') }}" wire:navigate class="hover:underline">Rawat Jalan</a>
                    <span class="mx-1">/</span>
                    <a href="{{ route('modul.rawat-jalan.perawatan-tindakan', str_replace('/', '-', $resume->no_rawat)) }}" wire:navigate class="hover:underline">Perawatan/Tindakan</a>
                    <span class="mx-1">/</span>
                    <a href="{{ route('modul.rawat-jalan.sub-rawat-jalan.resume', str_replace('/', '-', $resume->no_rawat)) }}" wire:navigate class="hover:underline">Resume</a>
                    <span class="mx-1">/</span>
                    <span>Detail Resume</span>
                </nav>
                <h1 class="text-xl font-bold text-neutral-800 dark:text-neutral-100">Ringkasan Resume Medis</h1>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('modul.rawat-jalan.sub-rawat-jalan.resume-form', ['no_rawat' => str_replace('/', '-', $resume->no_rawat), 'mode' => 'edit']) }}" wire:navigate
               class="flex items-center gap-2 px-4 py-2 rounded-xl bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700 text-sm font-bold text-amber-700 dark:text-amber-400 hover:bg-amber-100 transition-all shadow-sm">
                <flux:icon name="pencil-square" class="w-4 h-4" />
                Edit Resume
            </a>
            <button onclick="window.print()" class="flex items-center gap-2 px-4 py-2 rounded-xl bg-white dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 text-sm font-bold text-neutral-600 dark:text-neutral-300 hover:bg-neutral-50 transition-all shadow-sm">
                <flux:icon name="printer" class="w-4 h-4" />
                Cetak Resume
            </button>
        </div>
    </div>

    {{-- Main Document Container --}}
    <div class="bg-white dark:bg-neutral-800 rounded-3xl border border-neutral-200 dark:border-neutral-700 shadow-xl print:border-0 print:shadow-none print:rounded-none">
        
        {{-- Administrative Header (Sticky) --}}
        <div class="sticky top-0 z-30 p-8 bg-neutral-50/90 dark:bg-neutral-900/90 backdrop-blur-md border-b border-neutral-100 dark:border-neutral-700 rounded-t-3xl relative overflow-hidden shadow-sm">
            <div class="absolute top-0 right-0 p-12 opacity-[0.03] rotate-12 pointer-events-none">
                <flux:icon name="identification" class="w-64 h-64 text-[#4C5C2D]" />
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-12 gap-8 relative z-10">
                {{-- Patient Identity --}}
                <div class="md:col-span-5 flex items-start gap-5">
                    <div class="w-16 h-16 rounded-2xl bg-[#4C5C2D]/10 dark:bg-[#4C5C2D]/30 flex items-center justify-center text-[#4C5C2D] dark:text-[#8CC7C4] flex-shrink-0">
                        <flux:icon name="user-circle" class="w-9 h-9" />
                    </div>
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-[10px] font-black uppercase tracking-widest text-[#4C5C2D] dark:text-[#8CC7C4] px-1.5 py-0.5 rounded bg-[#4C5C2D]/10 dark:bg-[#4C5C2D]/20">{{ $resume->regPeriksa->no_rkm_medis }}</span>
                            <span class="text-[10px] font-bold text-neutral-400 uppercase tracking-widest">Pasien Rawat Jalan</span>
                        </div>
                        <h2 class="text-2xl font-black text-neutral-800 dark:text-neutral-100 uppercase leading-snug tracking-tighter">{{ $resume->regPeriksa->pasien->nm_pasien ?? '-' }}</h2>
                        <div class="mt-2 flex items-center gap-2 text-xs font-mono text-neutral-500">
                            <span class="bg-neutral-200/50 dark:bg-neutral-700/50 px-2 py-0.5 rounded">{{ $resume->no_rawat }}</span>
                        </div>
                    </div>
                </div>

                {{-- Visit Info Grid --}}
                <div class="md:col-span-7 grid grid-cols-1 sm:grid-cols-2 gap-y-5 gap-x-6 border-l border-neutral-200/50 dark:border-neutral-700/50 pl-8">
                    <div class="flex flex-col min-w-0">
                        <span class="text-[10px] font-bold uppercase text-neutral-400 tracking-widest leading-tight mb-1.5">
                            DPJP Utama
                        </span>
                        <span class="text-xs font-bold text-neutral-700 dark:text-neutral-300 leading-relaxed break-words">
                            {{ $resume->dokter->nm_dokter ?? '-' }}
                        </span>
                    </div>

                    <div class="flex flex-col min-w-0">
                        <span class="text-[10px] font-bold uppercase text-neutral-400 tracking-widest leading-tight mb-1.5">
                            Poliklinik
                        </span>
                        <span class="text-xs font-bold text-neutral-700 dark:text-neutral-300 leading-relaxed break-words">
                            {{ $resume->regPeriksa->poliklinik->nm_poli ?? '-' }}
                        </span>
                    </div>

                    <div class="flex flex-col min-w-0 border-t border-neutral-100 dark:border-neutral-700 pt-3">
                        <span class="text-[10px] font-bold uppercase text-neutral-400 tracking-widest leading-tight mb-1.5">
                            Tanggal Registrasi
                        </span>
                        <span class="text-xs font-bold text-neutral-700 dark:text-neutral-300 leading-relaxed">
                            {{ \Carbon\Carbon::parse($resume->regPeriksa->tgl_registrasi)->translatedFormat('d F Y') }}
                            <span class="block text-[10px] text-neutral-400 font-mono mt-0.5">
                                {{ $resume->regPeriksa->jam_reg }}
                            </span>
                        </span>
                    </div>

                    <div class="flex flex-col min-w-0 border-t border-neutral-100 dark:border-neutral-700 pt-3">
                        <span class="text-[10px] font-bold uppercase text-neutral-400 tracking-widest leading-tight mb-1.5">
                            Cara Bayar
                        </span>
                        <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400">
                            {{ $resume->regPeriksa->penjab->png_jawab ?? '-' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Clinical Content --}}
        <div class="p-8 space-y-10">
            
            {{-- History Section --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
                <div class="space-y-6">
                    <div>
                        <div class="flex items-center gap-2 mb-3">
                            <flux:icon name="chat-bubble-bottom-center-text" class="w-4 h-4 text-[#4C5C2D] dark:text-[#8CC7C4]" />
                            <h4 class="text-xs font-black uppercase tracking-widest text-neutral-400">Keluhan Utama</h4>
                        </div>
                        <div class="p-5 rounded-2xl bg-neutral-50 dark:bg-neutral-900/50 border border-neutral-100 dark:border-neutral-700 text-sm italic text-neutral-700 dark:text-neutral-300 leading-relaxed whitespace-pre-line min-h-[100px]">
                            {{ $resume->keluhan_utama ?: '-' }}
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    <div>
                        <div class="flex items-center gap-2 mb-3">
                            <flux:icon name="arrow-trending-up" class="w-4 h-4 text-[#4C5C2D] dark:text-[#8CC7C4]" />
                            <h4 class="text-xs font-black uppercase tracking-widest text-neutral-400">Jalannya Penyakit</h4>
                        </div>
                        <div class="p-5 rounded-2xl bg-neutral-50 dark:bg-neutral-900/50 border border-neutral-100 dark:border-neutral-700 text-sm text-neutral-700 dark:text-neutral-300 leading-relaxed whitespace-pre-line min-h-[100px]">
                            {{ $resume->jalannya_penyakit ?: '-' }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="w-full h-px bg-neutral-100 dark:bg-neutral-700"></div>

            {{-- Support & Therapy Section --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
                <div class="space-y-8">
                    <div>
                        <div class="flex items-center gap-2 mb-3">
                            <flux:icon name="beaker" class="w-4 h-4 text-[#4C5C2D] dark:text-[#8CC7C4]" />
                            <h4 class="text-xs font-black uppercase tracking-widest text-neutral-400">Pemeriksaan Penunjang</h4>
                        </div>
                        <div class="p-4 rounded-xl bg-blue-50/30 dark:bg-blue-900/10 border border-blue-100/50 dark:border-blue-900/30">
                            <p class="text-xs text-neutral-700 dark:text-neutral-300 italic whitespace-pre-line min-h-[80px]">{{ $resume->pemeriksaan_penunjang ?: '-' }}</p>
                        </div>
                    </div>
                </div>

                <div class="space-y-8">
                    <div>
                        <div class="flex items-center gap-2 mb-3">
                            <flux:icon name="clipboard-document-check" class="w-4 h-4 text-[#4C5C2D] dark:text-[#8CC7C4]" />
                            <h4 class="text-xs font-black uppercase tracking-widest text-neutral-400">Hasil Laboratorium</h4>
                        </div>
                        <div class="p-4 rounded-xl bg-emerald-50/30 dark:bg-emerald-900/10 border border-emerald-100/50 dark:border-emerald-900/30">
                            <p class="text-xs text-neutral-700 dark:text-neutral-300 italic whitespace-pre-line min-h-[80px]">{{ $resume->hasil_laborat ?: '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="w-full h-px bg-neutral-100 dark:bg-neutral-700"></div>

            {{-- Diagnosis & Procedure ICD Section --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
                {{-- Diagnosis Grid --}}
                <div class="space-y-6">
                    <div class="flex items-center gap-2 mb-4">
                        <flux:icon name="magnifying-glass-circle" class="w-4 h-4 text-[#4C5C2D] dark:text-[#8CC7C4]" />
                        <h4 class="text-xs font-black uppercase tracking-widest text-neutral-400">Diagnosis Akhir (ICD-10)</h4>
                    </div>
                    
                    <div class="space-y-3">
                        <div class="flex items-center gap-3">
                            <div class="flex-1 p-3.5 rounded-xl bg-[#4C5C2D] border border-[#4C5C2D] flex items-center justify-between">
                                <span class="text-xs font-bold text-white uppercase truncate">{{ $resume->diagnosa_utama ?: '-' }}</span>
                                <span class="px-2 py-0.5 rounded-lg bg-white/20 text-white text-[10px] font-black font-mono">{{ $resume->kd_diagnosa_utama ?: '-' }}</span>
                            </div>
                            <span class="text-[9px] font-black text-[#4C5C2D] uppercase w-12 text-center leading-none">Utama</span>
                        </div>

                        @foreach([1, 2, 3, 4] as $idx)
                            @php 
                                $diag = "diagnosa_sekunder" . ($idx == 1 ? '' : $idx); 
                                $kd = "kd_diagnosa_sekunder" . ($idx == 1 ? '' : $idx);
                            @endphp
                            @if($resume->$diag)
                                <div class="flex items-center gap-3">
                                    <div class="flex-1 p-3 rounded-xl bg-neutral-100 dark:bg-neutral-700/50 border border-neutral-200/50 dark:border-neutral-700 flex items-center justify-between">
                                        <span class="text-xs font-medium text-neutral-800 dark:text-neutral-200 truncate">{{ $resume->$diag }}</span>
                                        <span class="px-2 py-0.5 rounded-lg bg-neutral-200 dark:bg-neutral-700 text-neutral-500 dark:text-neutral-400 text-[10px] font-bold font-mono">{{ $resume->$kd }}</span>
                                    </div>
                                    <span class="text-[8px] font-bold text-neutral-400 uppercase w-12 text-center leading-none">Sek. {{ $idx }}</span>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>

                {{-- Procedure Grid --}}
                <div class="space-y-6">
                    <div class="flex items-center gap-2 mb-4">
                        <flux:icon name="wrench-screwdriver" class="w-4 h-4 text-[#4C5C2D] dark:text-[#8CC7C4]" />
                        <h4 class="text-xs font-black uppercase tracking-widest text-neutral-400">Prosedur (ICD-9)</h4>
                    </div>

                    <div class="space-y-3">
                         <div class="flex items-center gap-3">
                            <div class="flex-1 p-3.5 rounded-xl bg-[#6A7E3F] border border-[#6A7E3F] flex items-center justify-between shadow-lg shadow-[#6A7E3F]/10">
                                <span class="text-xs font-bold text-white uppercase truncate">{{ $resume->prosedur_utama ?: '-' }}</span>
                                <span class="px-2 py-0.5 rounded-lg bg-white/20 text-white text-[10px] font-black font-mono">{{ $resume->kd_prosedur_utama ?: '-' }}</span>
                            </div>
                            <span class="text-[9px] font-black text-[#6A7E3F] uppercase w-12 text-center leading-none">Utama</span>
                        </div>

                        @foreach([1, 2, 3] as $idx)
                            @php 
                                $proc = "prosedur_sekunder" . ($idx == 1 ? '' : $idx); 
                                $kd = "kd_prosedur_sekunder" . ($idx == 1 ? '' : $idx);
                            @endphp
                            @if($resume->$proc)
                                <div class="flex items-center gap-3">
                                    <div class="flex-1 p-3 rounded-xl bg-neutral-100 dark:bg-neutral-700/50 border border-neutral-200/50 dark:border-neutral-700 flex items-center justify-between">
                                        <span class="text-xs font-medium text-neutral-800 dark:text-neutral-200 truncate">{{ $resume->$proc }}</span>
                                        <span class="px-2 py-0.5 rounded-lg bg-neutral-200 dark:bg-neutral-700 text-neutral-500 dark:text-neutral-400 text-[10px] font-bold font-mono">{{ $resume->$kd }}</span>
                                    </div>
                                    <span class="text-[8px] font-bold text-neutral-400 uppercase w-12 text-center leading-none">Sek. {{ $idx }}</span>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="w-full h-px bg-neutral-100 dark:bg-neutral-700"></div>

            {{-- Discharge & Instructions Section --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div>
                    <span class="text-[10px] font-black uppercase text-neutral-400 mb-3 block">Kondisi Pulang</span>
                    <div class="p-5 rounded-2xl bg-neutral-50 dark:bg-neutral-900/50 border border-neutral-100 dark:border-neutral-700 flex items-center justify-center min-h-[80px]">
                        @if($resume->kondisi_pulang === 'Meninggal')
                            <span class="px-3 py-1 rounded-full bg-red-100 text-red-700 text-sm font-bold uppercase tracking-wider">{{ $resume->kondisi_pulang }}</span>
                        @else
                            <span class="px-3 py-1 rounded-full bg-emerald-100 text-emerald-700 text-sm font-bold uppercase tracking-wider">{{ $resume->kondisi_pulang }}</span>
                        @endif
                    </div>
                </div>
                <div>
                    <span class="text-[10px] font-black uppercase text-neutral-400 mb-3 block">Obat Pulang / Edukasi</span>
                    <div class="p-5 rounded-2xl bg-neutral-100 dark:bg-neutral-700/50 border border-neutral-200/50 dark:border-neutral-700 text-xs font-bold text-neutral-700 dark:text-neutral-300 leading-relaxed min-h-[80px] whitespace-pre-line">
                        {{ $resume->obat_pulang ?: '-' }}
                    </div>
                </div>
            </div>
        </div>

        {{-- Verification Footer --}}
        <div class="p-8 bg-neutral-50 dark:bg-neutral-900/50 border-t border-neutral-100 dark:border-neutral-700 flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-full bg-white dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 flex items-center justify-center text-neutral-400">
                    <flux:icon name="pencil" class="w-5 h-5" />
                </div>
                <div>
                    <span class="text-[9px] uppercase font-bold text-neutral-400 block mb-0.5">Resume Dibuat Oleh</span>
                    <p class="text-sm font-bold text-neutral-800 dark:text-neutral-100">{{ $resume->dokter->nm_dokter ?? $resume->kd_dokter }}</p>
                </div>
            </div>
            <div class="text-center md:text-right">
                <p class="text-[9px] text-neutral-400 font-medium">Dokumen ini diterbitkan secara elektronik oleh Sistem Informasi SIMRS Laralite</p>
                <p class="text-[10px] font-bold text-[#4C5C2D] dark:text-[#8CC7C4] mt-1 italic uppercase tracking-tighter">Valid as Official Medical Resume</p>
            </div>
        </div>
    </div>
</div>

<style>
    @media print {
        .no-print { display: none !important; }
        body { background: white !important; }
        * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
    }
</style>
