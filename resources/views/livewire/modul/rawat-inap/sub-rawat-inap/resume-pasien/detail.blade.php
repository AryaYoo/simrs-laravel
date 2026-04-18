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
        <a href="{{ route('modul.rawat-inap.sub-rawat-inap.resume-edit', str_replace('/', '-', $resume->no_rawat)) }}" wire:navigate
           class="w-14 h-14 rounded-full bg-[#4C5C2D] shadow-2xl flex items-center justify-center text-white hover:bg-[#3D4A24] transition-all group active:scale-95">
            <flux:icon name="pencil-square" class="w-7 h-7 group-hover:rotate-12 transition-transform" />
        </a>
    </div>

    {{-- Header / Breadcrumb --}}
    <div class="flex items-center justify-between no-print">
        <div class="flex items-center gap-3">
            <a href="{{ route('modul.rawat-inap.sub-rawat-inap.resume', str_replace('/', '-', $resume->regPeriksa->no_rawat)) }}" wire:navigate
               class="flex items-center justify-center w-8 h-8 rounded-lg transition-colors hover:bg-neutral-100 dark:hover:bg-neutral-700">
                <flux:icon name="chevron-left" class="w-5 h-5 text-neutral-500" />
            </a>
            <div>
                <nav class="text-xs text-neutral-400 mb-0.5">
                    <a href="{{ route('modul.index') }}" wire:navigate class="hover:underline">Modul</a>
                    <span class="mx-1">/</span>
                    <a href="{{ route('modul.rawat-inap.index') }}" wire:navigate class="hover:underline">Rawat Inap</a>
                    <span class="mx-1">/</span>
                    <a href="{{ route('modul.rawat-inap.perawatan-tindakan', str_replace('/', '-', $resume->regPeriksa->no_rawat)) }}" wire:navigate class="hover:underline">Perawatan/Tindakan</a>
                    <span class="mx-1">/</span>
                    <a href="{{ route('modul.rawat-inap.sub-rawat-inap.resume', str_replace('/', '-', $resume->regPeriksa->no_rawat)) }}" wire:navigate class="hover:underline">Resume</a>
                    <span class="mx-1">/</span>
                    <span>Detail Resume</span>
                </nav>
                <h1 class="text-xl font-bold text-neutral-800 dark:text-neutral-100">Ringkasan Resume Medis</h1>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('modul.rawat-inap.sub-rawat-inap.resume-edit', str_replace('/', '-', $resume->no_rawat)) }}" wire:navigate
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
                <div class="md:col-span-4 flex items-start gap-5">
                    <div class="w-16 h-16 rounded-2xl bg-[#4C5C2D]/10 dark:bg-[#4C5C2D]/30 flex items-center justify-center text-[#4C5C2D] dark:text-[#8CC7C4] flex-shrink-0">
                        <flux:icon name="user-circle" class="w-9 h-9" />
                    </div>
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-[10px] font-black uppercase tracking-widest text-[#4C5C2D] dark:text-[#8CC7C4] px-1.5 py-0.5 rounded bg-[#4C5C2D]/10 dark:bg-[#4C5C2D]/20">{{ $resume->regPeriksa->no_rkm_medis }}</span>
                            <span class="text-[10px] font-bold text-neutral-400 uppercase tracking-widest">{{ $resume->regPeriksa->status_lanjut }}</span>
                        </div>
                        <h2 class="text-2xl font-black text-neutral-800 dark:text-neutral-100 uppercase leading-snug tracking-tighter">{{ $resume->regPeriksa->pasien->nm_pasien ?? '-' }}</h2>
                        <div class="mt-2 flex items-center gap-2 text-xs font-mono text-neutral-500">
                            <span class="bg-neutral-200/50 dark:bg-neutral-700/50 px-2 py-0.5 rounded">{{ $resume->no_rawat }}</span>
                        </div>
                    </div>
                </div>

                {{-- Visit Info Grid --}}
                <div class="md:col-span-8 grid grid-cols-2 lg:grid-cols-4 gap-y-6 gap-x-4 border-l border-neutral-200/50 dark:border-neutral-700/50 pl-8">
                    <div class="flex flex-col">
                        <span class="text-[10px] font-bold uppercase text-neutral-400 tracking-widest leading-none mb-2">DPJP Utama</span>
                        <span class="text-xs font-bold text-neutral-700 dark:text-neutral-300">{{ $resume->regPeriksa->dokter->nm_dokter ?? '-' }}</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-[10px] font-bold uppercase text-neutral-400 tracking-widest leading-none mb-2">Bangsal / Kamar</span>
                        <span class="text-xs font-bold text-neutral-700 dark:text-neutral-300">
                            @php $room = $resume->regPeriksa->kamarInap->first(); @endphp
                            {{ $room->kamar->bangsal->nm_bangsal ?? '-' }} ({{ $room->kd_kamar ?? '-' }})
                        </span>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-[10px] font-bold uppercase text-neutral-400 tracking-widest leading-none mb-2">Dokter Pengirim</span>
                        <span class="text-xs font-bold text-neutral-700 dark:text-neutral-300">
                            {{ $resume->regPeriksa->rujukMasuk->perujuk ?? $resume->regPeriksa->rujukMasuk->dokter_perujuk ?? '-' }}
                        </span>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-[10px] font-bold uppercase text-neutral-400 tracking-widest leading-none mb-2">Cara Bayar</span>
                        <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400">{{ $resume->regPeriksa->penjab->png_jawab ?? '-' }}</span>
                    </div>
                    <div class="flex flex-col border-t border-neutral-100 dark:border-neutral-700 pt-3">
                        <span class="text-[10px] font-bold uppercase text-neutral-400 tracking-widest leading-none mb-2">Tanggal Masuk</span>
                        <span class="text-xs font-bold text-neutral-700 dark:text-neutral-300">
                            {{ \Carbon\Carbon::parse($room->tgl_masuk)->translatedFormat('d F Y') }}
                            <span class="text-[10px] text-neutral-400 font-mono ml-1">{{ $room->jam_masuk }}</span>
                        </span>
                    </div>
                    <div class="flex flex-col border-t border-neutral-100 dark:border-neutral-700 pt-3">
                        <span class="text-[10px] font-bold uppercase text-neutral-400 tracking-widest leading-none mb-2">Tanggal Keluar</span>
                        <span class="text-xs font-bold text-neutral-700 dark:text-neutral-300">
                            {{ ($room && $room->tgl_keluar != '0000-00-00') ? \Carbon\Carbon::parse($room->tgl_keluar)->translatedFormat('d F Y') : '-' }}
                            @if($room && $room->tgl_keluar != '0000-00-00')
                                <span class="text-[10px] text-neutral-400 font-mono ml-1">{{ $room->jam_keluar }}</span>
                            @endif
                        </span>
                    </div>
                    <div class="flex flex-col lg:col-span-2 border-t border-neutral-100 dark:border-neutral-700 pt-3">
                        <span class="text-[10px] font-bold uppercase text-neutral-400 tracking-widest leading-none mb-2">Alasan Masuk Dirawat</span>
                        <span class="text-xs font-medium italic text-neutral-600 dark:text-neutral-400 leading-relaxed">{{ $resume->alasan ?: ($resume->diagnosa_awal ?: '-') }}</span>
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
                            <h4 class="text-xs font-black uppercase tracking-widest text-neutral-400">Keluhan Utama & Riwayat Penyakit</h4>
                        </div>
                        <div class="p-5 rounded-2xl bg-neutral-50 dark:bg-neutral-900/50 border border-neutral-100 dark:border-neutral-700 text-sm italic text-neutral-700 dark:text-neutral-300 leading-relaxed whitespace-pre-line min-h-[100px]">
                            {{ $resume->keluhan_utama ?: '-' }}
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center gap-2 mb-3">
                            <flux:icon name="bars-3-bottom-left" class="w-4 h-4 text-[#4C5C2D] dark:text-[#8CC7C4]" />
                            <h4 class="text-xs font-black uppercase tracking-widest text-neutral-400">Pemeriksaan Fisik</h4>
                        </div>
                        <div class="p-5 rounded-2xl bg-neutral-50 dark:bg-neutral-900/50 border border-neutral-100 dark:border-neutral-700 text-sm text-neutral-700 dark:text-neutral-300 leading-relaxed whitespace-pre-line min-h-[120px]">
                            {{ $resume->pemeriksaan_fisik ?: '-' }}
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    <div>
                        <div class="flex items-center gap-2 mb-3">
                            <flux:icon name="arrow-trending-up" class="w-4 h-4 text-[#4C5C2D] dark:text-[#8CC7C4]" />
                            <h4 class="text-xs font-black uppercase tracking-widest text-neutral-400">Riwayat Penyakit Sekarang</h4>
                        </div>
                        <div class="p-5 rounded-2xl bg-neutral-50 dark:bg-neutral-900/50 border border-neutral-100 dark:border-neutral-700 text-sm text-neutral-700 dark:text-neutral-300 leading-relaxed whitespace-pre-line min-h-[250px]">
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
                            <h4 class="text-xs font-black uppercase tracking-widest text-neutral-400">Pemeriksaan Penunjang (Lab & Rad)</h4>
                        </div>
                        <div class="space-y-3">
                            <div class="p-4 rounded-xl bg-blue-50/30 dark:bg-blue-900/10 border border-blue-100/50 dark:border-blue-900/30">
                                <span class="text-[9px] font-black uppercase tracking-tighter text-blue-500 mb-1 block">Radiologi Terpenting</span>
                                <p class="text-xs text-neutral-700 dark:text-neutral-300 italic">{{ $resume->pemeriksaan_penunjang ?: '-' }}</p>
                            </div>
                            <div class="p-4 rounded-xl bg-emerald-50/30 dark:bg-emerald-900/10 border border-emerald-100/50 dark:border-emerald-900/30">
                                <span class="text-[9px] font-black uppercase tracking-tighter text-emerald-500 mb-1 block">Laborat Terpenting</span>
                                <p class="text-xs text-neutral-700 dark:text-neutral-300 italic">{{ $resume->hasil_laborat ?: '-' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="space-y-8">
                    <div>
                        <div class="flex items-center gap-2 mb-3">
                            <flux:icon name="shield-check" class="w-4 h-4 text-[#4C5C2D] dark:text-[#8CC7C4]" />
                            <h4 class="text-xs font-black uppercase tracking-widest text-neutral-400">Terapi & Tindakan Selama Perawatan</h4>
                        </div>
                        <div class="space-y-3">
                            <div class="p-4 rounded-xl bg-neutral-50 dark:bg-neutral-900/30 border border-neutral-100 dark:border-neutral-700">
                                <span class="text-[9px] font-black uppercase tracking-tighter text-neutral-400 mb-1 block">Tindakan / Operasi</span>
                                <p class="text-xs text-neutral-700 dark:text-neutral-300 whitespace-pre-line">{{ $resume->tindakan_dan_operasi ?: '-' }}</p>
                            </div>
                            <div class="p-4 rounded-xl bg-neutral-50 dark:bg-neutral-900/30 border border-neutral-100 dark:border-neutral-700">
                                <span class="text-[9px] font-black uppercase tracking-tighter text-neutral-400 mb-1 block">Obat Selama RS</span>
                                <p class="text-xs text-neutral-700 dark:text-neutral-300 whitespace-pre-line leading-relaxed">{{ $resume->obat_di_rs ?: '-' }}</p>
                            </div>
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
                        <h4 class="text-xs font-black uppercase tracking-widest text-neutral-400">Prosedur Utama & Sekunder (ICD-9)</h4>
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
            <div class="space-y-8">
                <div class="flex items-center gap-2 mb-4">
                    <flux:icon name="home-modern" class="w-4 h-4 text-[#4C5C2D] dark:text-[#8CC7C4]" />
                    <h4 class="text-xs font-black uppercase tracking-widest text-neutral-400">Instruksi Pulang & Rencana Tindak Lanjut</h4>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="p-5 rounded-2xl bg-neutral-50 dark:bg-neutral-900/50 border border-neutral-100 dark:border-neutral-700">
                        <span class="text-[10px] font-black uppercase text-neutral-400 mb-3 block">Status Akhir</span>
                        <div class="space-y-4">
                            <div>
                                <span class="text-[9px] uppercase font-bold text-neutral-400">Keadaan Pulang</span>
                                <p class="text-sm font-bold text-neutral-800 dark:text-neutral-100">{{ $resume->keadaan ?: '-' }} <span class="font-normal text-xs text-neutral-500 ml-1">({{ $resume->ket_keadaan ?: '-' }})</span></p>
                            </div>
                            <div>
                                <span class="text-[9px] uppercase font-bold text-neutral-400">Cara Keluar</span>
                                <p class="text-sm font-bold text-neutral-800 dark:text-neutral-100">{{ $resume->cara_keluar ?: '-' }} <span class="font-normal text-xs text-neutral-500 ml-1">({{ $resume->ket_keluar ?: '-' }})</span></p>
                            </div>
                        </div>
                    </div>

                    <div class="p-5 rounded-2xl bg-amber-50/20 dark:bg-amber-900/10 border border-amber-100/50 dark:border-amber-900/30">
                        <span class="text-[10px] font-black uppercase text-amber-600 mb-3 block">Alergi & Diet</span>
                        <div class="space-y-4">
                            <div>
                                <span class="text-[9px] uppercase font-bold text-amber-500">Alergi Obat</span>
                                <p class="text-xs font-bold text-rose-600 dark:text-rose-400">{{ $resume->alergi ?: '-' }}</p>
                            </div>
                            <div>
                                <span class="text-[9px] uppercase font-bold text-amber-500">Diet</span>
                                <p class="text-xs text-neutral-600 dark:text-neutral-400 italic leading-relaxed">{{ $resume->diet ?: '-' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-5 rounded-2xl bg-[#4C5C2D]/5 dark:bg-[#4C5C2D]/10 border border-[#4C5C2D]/10 dark:border-[#4C5C2D]/20">
                        <span class="text-[10px] font-black uppercase text-[#4C5C2D] dark:text-[#8CC7C4] mb-3 block">Rencana Kontrol</span>
                        <div class="space-y-4">
                            <div>
                                <span class="text-[9px] uppercase font-bold text-neutral-400">Tanggal & Jam Kontrol</span>
                                <p class="text-sm font-mono font-black text-[#4C5C2D] dark:text-[#8CC7C4]">
                                    {{ $resume->kontrol != '0000-00-00 00:00:00' ? \Carbon\Carbon::parse($resume->kontrol)->translatedFormat('d F Y H:i') : '-' }}
                                </p>
                            </div>
                            <div>
                                <span class="text-[9px] uppercase font-bold text-neutral-400">Tindak Lanjut</span>
                                <p class="text-xs text-neutral-700 dark:text-neutral-300">{{ $resume->dilanjutkan ?: '-' }} <span class="italic text-neutral-500">({{ $resume->ket_dilanjutkan ?: '-' }})</span></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <div>
                        <span class="text-[10px] font-black uppercase text-neutral-400 mb-3 block">Edukasi & Follow Up</span>
                        <div class="p-5 rounded-2xl bg-neutral-50 dark:bg-neutral-900/50 border border-neutral-100 dark:border-neutral-700 text-xs text-neutral-600 dark:text-neutral-400 leading-relaxed min-h-[80px]">
                            {{ $resume->edukasi ?: '-' }}
                        </div>
                    </div>
                    <div>
                        <span class="text-[10px] font-black uppercase text-neutral-400 mb-3 block">Obat Belanja / Obat Pulang</span>
                        <div class="p-5 rounded-2xl bg-neutral-100 dark:bg-neutral-700/50 border border-neutral-200/50 dark:border-neutral-700 text-xs font-bold text-neutral-700 dark:text-neutral-300 leading-relaxed min-h-[80px]">
                            {{ $resume->obat_pulang ?: '-' }}
                        </div>
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
