<div class="flex flex-col gap-6 pb-24 relative" x-data="{ showScrollTop: false }" @scroll.window="showScrollTop = (window.pageYOffset > 400)">
    
    {{-- Header / Breadcrumb --}}
    <div class="flex items-center justify-between no-print">
        <div class="flex items-center gap-3">
            <a href="{{ route('modul.rawat-inap.perawatan-tindakan', str_replace('/', '-', $no_rawat)) }}" wire:navigate
               class="flex items-center justify-center w-8 h-8 rounded-lg transition-colors hover:bg-neutral-100 dark:hover:bg-neutral-700">
                <flux:icon name="chevron-left" class="w-5 h-5 text-neutral-500" />
            </a>
            <div>
                <nav class="text-xs text-neutral-400 mb-0.5">
                    <a href="{{ route('modul.index') }}" wire:navigate class="hover:underline">Modul</a>
                    <span class="mx-1">/</span>
                    <a href="{{ route('modul.rawat-inap.index') }}" wire:navigate class="hover:underline">Rawat Inap</a>
                    <span class="mx-1">/</span>
                    <a href="{{ route('modul.rawat-inap.perawatan-tindakan', str_replace('/', '-', $no_rawat)) }}" wire:navigate class="hover:underline">Perawatan</a>
                    <span class="mx-1">/</span>
                    <span>Permintaan Radiologi</span>
                </nav>
                <h1 class="text-xl font-bold text-neutral-800 dark:text-neutral-100">Input Permintaan Radiologi</h1>
            </div>
        </div>
    </div>

    {{-- Patient Information Banner --}}
    <div class="bg-[#4C5C2D] rounded-3xl p-6 text-white shadow-xl shadow-[#4C5C2D]/20 relative overflow-hidden group transition-all duration-500 hover:shadow-2xl hover:shadow-[#4C5C2D]/30 no-print">
        <div class="absolute top-0 right-0 p-12 opacity-10 group-hover:scale-110 transition-transform duration-700">
            <flux:icon name="identification" class="w-48 h-48" />
        </div>
        
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 relative z-10">
            <div class="flex items-center gap-5">
                <div class="w-16 h-16 rounded-2xl bg-white/20 backdrop-blur-md flex items-center justify-center text-white border border-white/30 shadow-inner group-hover:rotate-3 transition-transform">
                    <flux:icon name="user-circle" class="w-10 h-10" />
                </div>
                <div>
                    <div class="flex items-center gap-2 mb-1.5">
                        <span class="bg-white/20 px-2 py-0.5 rounded text-[10px] font-black tracking-widest uppercase border border-white/10">{{ $regPeriksa->no_rawat }}</span>
                        <span class="bg-amber-400 text-[#4C5C2D] px-2 py-0.5 rounded text-[10px] font-black tracking-widest uppercase">{{ $regPeriksa->no_rkm_medis }}</span>
                    </div>
                    <h2 class="text-2xl font-black uppercase tracking-tighter leading-none">{{ $regPeriksa->pasien->nm_pasien ?? '-' }}</h2>
                    <p class="text-white/60 text-xs mt-2 font-medium">
                        DPJP: <span class="text-white font-bold">{{ $regPeriksa->dokter->nm_dokter ?? '-' }}</span> 
                        <span class="mx-2 opacity-30">|</span> 
                        Kamar: <span class="text-white font-bold">{{ $regPeriksa->kamarInap->first()->kamar->bangsal->nm_bangsal ?? '-' }} ({{ $regPeriksa->kamarInap->first()->kd_kamar ?? '-' }})</span>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        {{-- Main Input Form --}}
        <div class="lg:col-span-12 space-y-6">
            <div class="bg-white dark:bg-neutral-800 rounded-3xl border border-neutral-200 dark:border-neutral-700 shadow-sm overflow-hidden">
                <div class="p-5 border-b border-neutral-100 dark:border-neutral-700 bg-neutral-50/50 dark:bg-neutral-900/50 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-[#4C5C2D]/10 text-[#4C5C2D] flex items-center justify-center">
                            <flux:icon name="pencil-square" class="w-5 h-5" />
                        </div>
                        <div>
                            <h3 class="font-bold text-neutral-800 dark:text-neutral-100">Detail Permintaan</h3>
                            <p class="text-[10px] text-neutral-400 font-bold uppercase tracking-widest">Informasi Waktu & Dokter Perujuk</p>
                        </div>
                    </div>
                </div>

                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
                        {{-- Left Column: Basic Info --}}
                        <div class="md:col-span-5 space-y-6">
                            <div class="grid grid-cols-2 gap-4">
                                <flux:field>
                                    <flux:label class="text-[10px] uppercase font-black text-neutral-400 mb-2">Tanggal Periksa</flux:label>
                                    <flux:input type="date" wire:model.live="tgl_permintaan" class="rounded-xl border-neutral-200" />
                                </flux:field>
                                
                                <flux:field>
                                    <div class="flex items-center justify-between mb-2">
                                        <flux:label class="text-[10px] uppercase font-black text-neutral-400">Jam Periksa</flux:label>
                                        <div class="flex items-center gap-1">
                                            <flux:checkbox wire:model.live="auto_waktu" class="size-3" />
                                            <span class="text-[9px] font-bold text-neutral-400 uppercase">Auto</span>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-1.5">
                                        <flux:input type="number" wire:model="jam_permintaan_jam" class="w-full text-center font-mono rounded-xl border-neutral-200" min="0" max="23" :disabled="$auto_waktu" />
                                        <span class="text-neutral-300 font-bold">:</span>
                                        <flux:input type="number" wire:model="jam_permintaan_menit" class="w-full text-center font-mono rounded-xl border-neutral-200" min="0" max="59" :disabled="$auto_waktu" />
                                        <span class="text-neutral-300 font-bold">:</span>
                                        <flux:input type="number" wire:model="jam_permintaan_detik" class="w-full text-center font-mono rounded-xl border-neutral-200" min="0" max="59" :disabled="$auto_waktu" />
                                    </div>
                                </flux:field>
                            </div>

                            <flux:field>
                                <flux:label class="text-[10px] uppercase font-black text-neutral-400 mb-2">No. Permintaan</flux:label>
                                <flux:input wire:model="predictedOrderNo" readonly class="bg-neutral-50 dark:bg-neutral-900/50 font-mono font-bold text-[#4C5C2D] rounded-xl border-neutral-200" />
                                <flux:description class="text-[9px] mt-1 italic">Nomor ini bersifat sementara & akan diverifikasi ulang saat simpan.</flux:description>
                            </flux:field>

                            <flux:field>
                                <flux:label class="text-[10px] uppercase font-black text-neutral-400 mb-2">Dokter Perujuk (DPJP)</flux:label>
                                <div class="flex items-center gap-2">
                                    <flux:input wire:model="nm_dokter_perujuk" readonly class="flex-1 bg-neutral-50 dark:bg-neutral-900/50 font-bold rounded-xl border-neutral-200 cursor-default" />
                                    <flux:button icon="paper-clip" variant="ghost" class="rounded-xl border border-neutral-200" wire:click="openDokterModal" />
                                </div>
                            </flux:field>
                        </div>

                        {{-- Right Column: Clinical Content --}}
                        <div class="md:col-span-7 space-y-6">
                            <flux:field>
                                <flux:label class="text-[10px] uppercase font-black text-neutral-400 mb-2">Indikasi Pemeriksaan / Diagnosis Klinis</flux:label>
                                <flux:textarea wire:model="diagnosa_klinis" rows="3" placeholder="Masukkan diagnosa klinis atau alasan pemeriksaan..." class="rounded-2xl border-neutral-200 resize-none focus:ring-[#4C5C2D] transition-all" />
                            </flux:field>

                            <flux:field>
                                <flux:label class="text-[10px] uppercase font-black text-neutral-400 mb-2">Informasi Tambahan Permintaan Foto</flux:label>
                                <flux:textarea wire:model="informasi_tambahan" rows="3" placeholder="Contoh: Thorax 2 posisi, Cito, dll..." class="rounded-2xl border-neutral-200 resize-none focus:ring-[#4C5C2D] transition-all" />
                            </flux:field>
                        </div>
                    </div>
                </div>

                {{-- Examination Selection Section --}}
                <div class="border-t border-neutral-100 dark:border-neutral-700">
                    <div class="p-5 bg-neutral-50/30 dark:bg-neutral-900/10 flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center">
                                <flux:icon name="magnifying-glass" class="w-4 h-4" />
                            </div>
                            <h4 class="text-sm font-bold text-neutral-700 dark:text-neutral-300">Pilih Pemeriksaan Radiologi</h4>
                        </div>
                        <div class="w-full md:w-96">
                            <flux:input wire:model.live.debounce.300ms="searchPemeriksaan" placeholder="Cari kode atau nama pemeriksaan..." icon="magnifying-glass" class="rounded-xl" />
                        </div>
                    </div>

                    <div class="p-0 overflow-x-auto">
                        <flux:table :paginate="$pemeriksaanList">
                            <flux:table.columns>
                                <flux:table.column width="40px" align="center">P</flux:table.column>
                                <flux:table.column>Kode Periksa</flux:table.column>
                                <flux:table.column>Nama Pemeriksaan</flux:table.column>
                                <flux:table.column align="right">Tarif</flux:table.column>
                            </flux:table.columns>
                            <flux:table.rows>
                                @forelse ($pemeriksaanList as $pemeriksaan)
                                    <flux:table.row :key="$pemeriksaan->kd_jenis_prw" class="group hover:bg-[#F1F5E9] transition-colors">
                                        <flux:table.cell align="center">
                                            <flux:checkbox wire:model.live="selectedTests" value="{{ $pemeriksaan->kd_jenis_prw }}" class="accent-[#4C5C2D]" />
                                        </flux:table.cell>
                                        <flux:table.cell class="font-mono text-[11px] font-bold text-neutral-500">{{ $pemeriksaan->kd_jenis_prw }}</flux:table.cell>
                                        <flux:table.cell class="font-bold text-neutral-700 dark:text-neutral-200 uppercase tracking-tight">{{ $pemeriksaan->nm_perawatan }}</flux:table.cell>
                                        <flux:table.cell align="right" class="font-mono text-xs text-neutral-500">Rp {{ number_format($pemeriksaan->total_byr) }}</flux:table.cell>
                                    </flux:table.row>
                                @empty
                                    <flux:table.row>
                                        <flux:table.cell colspan="4" class="py-12 text-center">
                                            <div class="flex flex-col items-center gap-2 opacity-30">
                                                <flux:icon name="magnifying-glass" class="w-12 h-12" />
                                                <p class="text-sm font-bold">Pemeriksaan tidak ditemukan</p>
                                            </div>
                                        </flux:table.cell>
                                    </flux:table.row>
                                @endforelse
                            </flux:table.rows>
                        </flux:table>
                    </div>
                </div>

                {{-- Footer Save Area --}}
                <div class="p-6 bg-neutral-50 dark:bg-neutral-900 border-t border-neutral-100 dark:border-neutral-700 flex items-center justify-between">
                    <div class="flex flex-col">
                        <span class="text-[10px] font-bold text-neutral-400 uppercase tracking-widest leading-none mb-1">Item Terpilih</span>
                        <div class="flex items-center gap-2">
                            <div class="px-2 py-0.5 rounded bg-[#4C5C2D] text-white text-xs font-black">{{ count($selectedTests) }}</div>
                            <span class="text-xs font-bold text-neutral-600 dark:text-neutral-400">Pemeriksaan Radiologi</span>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-3">
                        <flux:button variant="ghost" class="rounded-xl px-6" :href="route('modul.rawat-inap.perawatan-tindakan', str_replace('/', '-', $no_rawat))" wire:navigate>Batal</flux:button>
                        <flux:button wire:click="save" variant="primary" icon="paper-airplane" class="px-8 rounded-xl bg-[#4C5C2D] hover:bg-[#3D4A24] shadow-lg shadow-[#4C5C2D]/30" wire:loading.attr="disabled">
                            Kirim Permintaan
                        </flux:button>
                    </div>
                </div>
            </div>
        </div>

        {{-- History Section --}}
        <div class="lg:col-span-12 mt-4 no-print">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center">
                    <flux:icon name="clock" class="w-4 h-4" />
                </div>
                <h3 class="font-bold text-neutral-800 dark:text-neutral-100">Riwayat Permintaan Radiologi</h3>
            </div>

            <div class="bg-white dark:bg-neutral-800 rounded-2xl border border-neutral-200 dark:border-neutral-700 shadow-sm overflow-hidden">
                <flux:table>
                    <flux:table.columns>
                        <flux:table.column>No. Permintaan</flux:table.column>
                        <flux:table.column>Dokter Perujuk</flux:table.column>
                        <flux:table.column>Waktu Permintaan</flux:table.column>
                        <flux:table.column>Diagnosis/Info</flux:table.column>
                        <flux:table.column>Pemeriksaan</flux:table.column>
                        <flux:table.column align="center">Hapus</flux:table.column>
                    </flux:table.columns>
                    
                    <flux:table.rows>
                        @forelse($history as $item)
                        <flux:table.row class="align-top">
                            <flux:table.cell class="font-mono text-xs font-black text-[#4C5C2D]">{{ $item->noorder }}</flux:table.cell>
                            <flux:table.cell class="text-xs font-bold">{{ $item->dokter->nm_dokter ?? '-' }}</flux:table.cell>
                            <flux:table.cell class="text-[10px] text-neutral-500 font-medium">
                                {{ \Carbon\Carbon::parse($item->tgl_permintaan)->format('d/m/Y') }}<br>
                                <span class="font-mono">{{ $item->jam_permintaan }}</span>
                            </flux:table.cell>
                            <flux:table.cell class="max-w-xs overflow-hidden">
                                <p class="text-[10px] font-bold text-neutral-400 uppercase tracking-tighter mb-1">Indikasi:</p>
                                <p class="text-xs text-neutral-600 italic leading-snug truncate">{{ $item->diagnosa_klinis }}</p>
                            </flux:table.cell>
                            <flux:table.cell>
                                <div class="flex flex-wrap gap-1">
                                    @foreach($item->detailPemeriksaan as $detail)
                                        <span class="inline-flex px-1.5 py-0.5 rounded bg-neutral-100 dark:bg-neutral-700 text-[9px] font-bold text-neutral-600 uppercase tracking-tight">
                                            {{ $detail->pemeriksaan->nm_perawatan ?? $detail->kd_jenis_prw }}
                                        </span>
                                    @endforeach
                                </div>
                            </flux:table.cell>
                            <flux:table.cell align="center">
                                @if($item->tgl_sampel == '1000-01-01' || $item->tgl_sampel == '0000-00-00')
                                    <flux:button variant="ghost" size="xs" icon="trash" class="text-rose-500 hover:text-rose-600 hover:bg-rose-50" 
                                        wire:click="batalPermintaan('{{ $item->noorder }}')" 
                                        wire:confirm="Yakin ingin membatalkan permintaan ini?" />
                                @else
                                    <flux:badge size="xs" color="emerald" class="text-[9px] uppercase font-black tracking-tighter">Processed</flux:badge>
                                @endif
                            </flux:table.cell>
                        </flux:table.row>
                        @empty
                        <flux:table.row>
                            <flux:table.cell colspan="6" class="py-8 text-center opacity-30 text-xs font-bold uppercase tracking-widest">Belum ada riwayat permintaan</flux:table.cell>
                        </flux:table.row>
                        @endforelse
                    </flux:table.rows>
                </flux:table>
            </div>
        </div>
    </div>

    {{-- Dokter Search Modal --}}
    <flux:modal wire:model="isDokterModalOpen" variant="flyout" class="w-full max-w-lg no-print">
        <div class="space-y-6">
            <header>
                <flux:heading size="lg">Cari Dokter Perujuk</flux:heading>
                <flux:subheading>Pilih dokter yang melakukan rujukan/permintaan.</flux:subheading>
            </header>

            <flux:input wire:model.live.debounce.300ms="searchDokterModal" placeholder="Nama dokter..." icon="magnifying-glass" />

            <div class="flex flex-col gap-2 max-h-[60vh] overflow-y-auto">
                @foreach($listDokter as $doc)
                    <button wire:click="selectDokter('{{ $doc['kd_dokter'] }}', '{{ $doc['nm_dokter'] }}')" 
                        class="w-full p-4 rounded-xl border border-neutral-100 dark:border-neutral-700 hover:border-[#4C5C2D] hover:bg-[#F1F5E9] transition-all flex items-center justify-between group">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-neutral-100 dark:bg-neutral-800 flex items-center justify-center text-neutral-400 group-hover:bg-[#4C5C2D] group-hover:text-white transition-colors">
                                <flux:icon name="user" class="w-5 h-5" />
                            </div>
                            <div class="text-left">
                                <p class="text-sm font-bold text-neutral-700 dark:text-neutral-200">{{ $doc['nm_dokter'] }}</p>
                                <p class="text-[10px] font-mono text-neutral-400 uppercase">{{ $doc['kd_dokter'] }}</p>
                            </div>
                        </div>
                        <flux:icon name="chevron-right" class="w-4 h-4 text-neutral-300 group-hover:text-[#4C5C2D]" />
                    </button>
                @endforeach
            </div>

            <div class="flex justify-end">
                <flux:button variant="ghost" x-on:click="$modal.close()">Tutup</flux:button>
            </div>
        </div>
    </flux:modal>
    
</div>
