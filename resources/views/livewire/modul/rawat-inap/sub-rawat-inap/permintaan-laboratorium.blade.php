<div>
    {{-- Main Header --}}
    <div class="flex items-center justify-between mb-6">
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
                    <a href="{{ route('modul.rawat-inap.perawatan-tindakan', str_replace('/', '-', $no_rawat)) }}" wire:navigate class="hover:underline">Tindakan</a>
                    <span class="mx-1">/</span>
                    <span>Permintaan Laboratorium</span>
                </nav>
                <h1 class="text-xl font-bold text-neutral-800 dark:text-neutral-100">Permintaan Pemeriksaan Laboratorium</h1>
            </div>
        </div>
    </div>

    {{-- Patient Info Banner --}}
    <div class="bg-[#4C5C2D] text-white p-4 rounded-xl shadow-md mb-6 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center flex-shrink-0">
                <flux:icon name="user" class="w-6 h-6 text-white" />
            </div>
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="font-mono text-xs bg-white/20 px-2 py-0.5 rounded">{{ $regPeriksa->no_rawat }}</span>
                    <span class="font-mono text-xs bg-white/20 px-2 py-0.5 rounded">{{ $regPeriksa->no_rkm_medis }}</span>
                </div>
                <h2 class="text-lg font-bold">{{ $regPeriksa->pasien->nm_pasien ?? '-' }}</h2>
                <div class="text-xs text-white/80 mt-1 flex flex-wrap items-center gap-3">
                    <span class="flex items-center gap-1"><flux:icon name="identification" class="w-3 h-3"/> {{ $regPeriksa->pasien->no_ktp ?? '-' }}</span>
                    <span class="flex items-center gap-1"><flux:icon name="home" class="w-3 h-3"/> {{ $regPeriksa->kamarInap->first()->kamar->bangsal->nm_bangsal ?? 'Belum ada kamar' }}</span>
                </div>
            </div>
        </div>
        <div class="text-left md:text-right text-sm border-t md:border-t-0 md:border-l border-white/20 pt-3 md:pt-0 md:pl-4">
            <p class="text-white/80 text-xs mb-1">Dokter DPJP Pasien</p>
            <p class="font-semibold">{{ $regPeriksa->dokter->nm_dokter ?? '-' }}</p>
        </div>
    </div>

    {{-- Parameter Panel (New Top Section) --}}
    <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-sm border border-neutral-200 dark:border-neutral-700 mb-6 p-4 text-sm text-neutral-700 dark:text-neutral-300">
        <div class="flex flex-col gap-4">
            {{-- Row 1: Tgl & Waktu --}}
            <div class="flex flex-col md:flex-row items-start md:items-center gap-4 md:gap-8">
                <div class="flex items-center gap-2">
                    <span class="w-24 md:w-auto font-medium whitespace-nowrap text-xs uppercase text-neutral-500">Tgl.Permintaan :</span>
                    <div class="flex items-center gap-2 flex-wrap">
                        <flux:input type="date" wire:model="tgl_permintaan" class="w-36 !py-1 !text-sm" />
                        
                        <div class="flex items-center gap-1">
                            <flux:select wire:model="jam_permintaan_jam" class="w-20 !py-1 !text-sm" :disabled="$auto_waktu">
                                @for($i = 0; $i < 24; $i++)
                                    <option value="{{ sprintf('%02d', $i) }}">{{ sprintf('%02d', $i) }}</option>
                                @endfor
                            </flux:select>
                            <span class="text-neutral-400">:</span>
                            <flux:select wire:model="jam_permintaan_menit" class="w-20 !py-1 !text-sm" :disabled="$auto_waktu">
                                @for($i = 0; $i < 60; $i++)
                                    <option value="{{ sprintf('%02d', $i) }}">{{ sprintf('%02d', $i) }}</option>
                                @endfor
                            </flux:select>
                            <span class="text-neutral-400">:</span>
                            <flux:select wire:model="jam_permintaan_detik" class="w-20 !py-1 !text-sm" :disabled="$auto_waktu">
                                @for($i = 0; $i < 60; $i++)
                                    <option value="{{ sprintf('%02d', $i) }}">{{ sprintf('%02d', $i) }}</option>
                                @endfor
                            </flux:select>
                        </div>
                        
                        <flux:checkbox wire:model.live="auto_waktu" label="Auto" class="ml-1" />
                    </div>
                </div>
            </div>

            {{-- Row 2: Perujuk --}}
            <div class="flex flex-col md:flex-row items-start md:items-center gap-4 md:gap-8">
                <div class="flex items-center gap-2 w-full md:w-auto">
                    <span class="w-24 md:w-auto font-medium whitespace-nowrap text-xs uppercase text-neutral-500">Perujuk :</span>
                    <div class="flex items-center gap-2 flex-1 md:flex-none">
                        <flux:input wire:model="kd_dokter_perujuk" class="w-24 !py-1 !text-sm text-center font-mono" readonly placeholder="Kode" />
                        <flux:input wire:model="nm_dokter_perujuk" class="w-48 md:w-64 !py-1 !text-sm" readonly placeholder="Pilih Dokter Perujuk..." />
                        <button type="button" wire:click="openDokterModal" class="p-1.5 text-neutral-500 hover:text-[#4C5C2D] transition-colors border border-neutral-200 dark:border-neutral-700 rounded-lg hover:bg-neutral-50 dark:hover:bg-neutral-800">
                            <flux:icon name="paper-clip" class="w-4 h-4" />
                        </button>
                    </div>
                </div>
            </div>

            {{-- Row 3: Diagnosa & Catatan (Tampil Horizontal) --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2 border-t border-neutral-100 dark:border-neutral-700/50">
                <div class="flex flex-col gap-1.5">
                    <label class="font-medium text-xs uppercase text-neutral-400 tracking-wider">Indikasi Klinis :</label>
                    <flux:textarea wire:model="diagnosa_klinis" placeholder="Masukkan indikasi klinis..." rows="1" class="!text-xs !py-1.5" />
                </div>
                <div class="flex flex-col gap-1.5">
                    <label class="font-medium text-xs uppercase text-neutral-400 tracking-wider">Informasi Tambahan :</label>
                    <flux:textarea wire:model="informasi_tambahan" placeholder="Catatan tambahan..." rows="1" class="!text-xs !py-1.5" />
                </div>
            </div>
        </div>
    </div>

    {{-- Tabs Header --}}
    <div class="flex items-center gap-1 mb-6 border-b border-neutral-200 dark:border-neutral-700">
        <button wire:click="$set('kategori', 'PK')" class="px-6 py-3 text-sm font-bold transition-all border-b-2 {{ $kategori === 'PK' ? 'border-[#4C5C2D] text-[#4C5C2D]' : 'border-transparent text-neutral-400 hover:text-neutral-600' }}">Patologi Klinis</button>
        <button wire:click="$set('kategori', 'PA')" class="px-6 py-3 text-sm font-bold transition-all border-b-2 {{ $kategori === 'PA' ? 'border-[#4C5C2D] text-[#4C5C2D]' : 'border-transparent text-neutral-400 hover:text-neutral-600' }}">Patologi Anatomi</button>
        <button wire:click="$set('kategori', 'MB')" class="px-6 py-3 text-sm font-bold transition-all border-b-2 {{ $kategori === 'MB' ? 'border-[#4C5C2D] text-[#4C5C2D]' : 'border-transparent text-neutral-400 hover:text-neutral-600' }}">Mikrobiologi & Bio Molekuler</button>
        <button wire:click="$set('kategori', 'RIWAYAT')" class="px-6 py-3 text-sm font-bold transition-all border-b-2 {{ $kategori === 'RIWAYAT' ? 'border-[#4C5C2D] text-[#4C5C2D]' : 'border-transparent text-neutral-400 hover:text-neutral-600' }}">Riwayat</button>
    </div>

    @if($kategori === 'PK')
        {{-- Patologi Klinis: Twin Tables Master-Detail --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">
            
            {{-- Panel Kiri: Daftar Pemeriksaan (Master) --}}
            <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-sm border border-neutral-200 dark:border-neutral-700 overflow-hidden flex flex-col h-[700px]">
                <div class="p-4 border-b border-neutral-100 dark:border-neutral-700 bg-neutral-50/50 dark:bg-neutral-800/50 flex flex-col gap-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <flux:icon name="beaker" class="w-5 h-5 text-[#4C5C2D]" />
                            <h3 class="font-bold text-neutral-700 dark:text-neutral-300 uppercase tracking-tight text-sm">Pemeriksaan</h3>
                        </div>
                        <div class="flex items-center gap-2 px-3 py-1 bg-[#4C5C2D]/10 rounded-lg text-[#4C5C2D] border border-[#4C5C2D]/20">
                            <span class="text-[10px] font-bold uppercase">No. Permintaan :</span>
                            <span class="text-xs font-mono font-bold">{{ $predictedOrderNo }}</span>
                        </div>
                    </div>
                    
                    <flux:input wire:model.live.debounce.300ms="searchPemeriksaan" 
                                placeholder="Cari Kode atau Nama Pemeriksaan..." 
                                icon="magnifying-glass" 
                                class="!py-1.5 !text-xs" />
                </div>

                <div class="flex-1 overflow-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="sticky top-0 bg-white dark:bg-neutral-800 shadow-sm z-10 font-bold text-neutral-500 dark:text-neutral-400 text-[10px] uppercase tracking-wider">
                            <tr>
                                <th class="px-4 py-3 w-8">#</th>
                                <th class="px-4 py-3 w-32 text-center">Kode</th>
                                <th class="px-4 py-3">Nama Pemeriksaan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-100 dark:divide-neutral-700">
                            @forelse($pemeriksaanList as $item)
                                <tr class="hover:bg-[#4C5C2D]/5 transition-colors group">
                                    <td class="px-4 py-3 text-center">
                                        <flux:checkbox wire:model.live="selectedTests" value="{{ $item->kd_jenis_prw }}" class="accent-[#4C5C2D]" />
                                    </td>
                                    <td class="px-4 py-3 text-center font-mono text-xs text-neutral-500">{{ $item->kd_jenis_prw }}</td>
                                    <td class="px-4 py-3 text-sm font-semibold text-neutral-700 dark:text-neutral-200 group-hover:text-[#4C5C2D]">{{ $item->nm_perawatan }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="py-12 text-center text-neutral-400 italic text-sm">Tidak ditemukan data.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <div class="p-3 border-t border-neutral-100 dark:border-neutral-700 bg-neutral-50/30">
                    {{ $pemeriksaanList->links() }}
                </div>
            </div>

            {{-- Panel Kanan: Detail Pemeriksaan (Detail) --}}
            <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-sm border border-neutral-200 dark:border-neutral-700 overflow-hidden flex flex-col h-[700px]">
                <div class="p-4 border-b border-neutral-100 dark:border-neutral-700 bg-neutral-50/50 dark:bg-neutral-800/50 flex flex-col gap-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <flux:icon name="magnifying-glass-plus" class="w-5 h-5 text-[#4C5C2D]" />
                            <h3 class="font-bold text-neutral-700 dark:text-neutral-300 uppercase tracking-tight text-sm">Detail Pemeriksaan</h3>
                        </div>
                        <div class="flex items-center gap-2">
                            {{-- Bulk Action Dropdown --}}
                            <flux:dropdown>
                                <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" class="!px-2 !py-1 text-xs">Bulk Action</flux:button>
                                <flux:menu>
                                    <flux:menu.item wire:click="toggleAllDetails(true)">Pilih Semua</flux:menu.item>
                                    <flux:menu.item wire:click="toggleAllDetails(false)">Clear Pilihan</flux:menu.item>
                                </flux:menu>
                            </flux:dropdown>

                            <flux:button wire:click="save" variant="primary" size="sm" icon="paper-airplane" class="!bg-[#4C5C2D] !border-none !text-xs shadow-md">Kirim Permintaan</flux:button>
                        </div>
                    </div>
                    <flux:input wire:model.live.debounce.300ms="searchDetail" 
                                placeholder="Cari dalam detail pemeriksaan..." 
                                icon="magnifying-glass" 
                                class="!py-1.5 !text-xs" />
                </div>

                <div class="flex-1 overflow-auto">
                    @if(count($selectedTests) > 0)
                        <table class="w-full text-left border-collapse">
                            <thead class="sticky top-0 bg-white dark:bg-neutral-800 shadow-sm z-10 font-bold text-neutral-500 dark:text-neutral-400 text-[10px] uppercase tracking-wider">
                                <tr>
                                    <th class="px-4 py-3 w-8">#</th>
                                    <th class="px-4 py-3">Pemeriksaan</th>
                                    <th class="px-4 py-3 text-center">Satuan</th>
                                    <th class="px-4 py-3 text-center">Nilai Rujukan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-neutral-100 dark:divide-neutral-700">
                                @foreach($detailParameters->groupBy('kd_jenis_prw') as $kd_jenis_prw => $group)
                                    {{-- Group Header --}}
                                    @php
                                        $groupIds = $group->pluck('id_template')->map(fn($id) => (string)$id)->toArray();
                                        $isGroupSelected = collect($groupIds)->every(fn($id) => in_array($id, $selectedDetails));
                                    @endphp
                                    <tr class="bg-[#4C5C2D]/10 dark:bg-[#4C5C2D]/20 border-y border-[#4C5C2D]/10">
                                        <td class="px-4 py-2 text-center">
                                            <flux:checkbox wire:click="toggleGroup('{{ $kd_jenis_prw }}')" :checked="$isGroupSelected" class="accent-[#4C5C2D]" />
                                        </td>
                                        <td colspan="3" class="px-4 py-2">
                                            <div class="flex items-center gap-2">
                                                <div class="w-1 h-4 bg-[#4C5C2D] rounded-full"></div>
                                                <span class="text-[10px] font-extrabold uppercase text-[#4C5C2D] tracking-wider">
                                                    {{ $group->first()->pemeriksaanHeader->nm_perawatan ?? 'Pemeriksaan' }}
                                                </span>
                                            </div>
                                        </td>
                                    </tr>

                                    @foreach($group as $detail)
                                        <tr class="hover:bg-[#4C5C2D]/5 transition-colors group">
                                            <td class="px-4 py-2 text-center">
                                                <flux:checkbox wire:model.live="selectedDetails" value="{{ (string)$detail->id_template }}" class="accent-[#4C5C2D]" />
                                            </td>
                                            <td class="px-4 py-2">
                                                <p class="text-xs font-bold text-neutral-700 dark:text-neutral-200">{{ $detail->Pemeriksaan }}</p>
                                            </td>
                                            <td class="px-4 py-2 text-center text-xs text-neutral-500">{{ $detail->satuan ?: '-' }}</td>
                                            <td class="px-4 py-2 text-center text-[11px] text-neutral-500">
                                                <div class="flex flex-col gap-0.5">
                                                    <span>LD: {{ $detail->nilai_rujukan_ld }} | LA: {{ $detail->nilai_rujukan_la }}</span>
                                                    <span>PD: {{ $detail->nilai_rujukan_pd }} | PA: {{ $detail->nilai_rujukan_pa }}</span>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="flex flex-col items-center justify-center h-full text-neutral-400 p-8 text-center">
                            <flux:icon name="hand-raised" class="w-12 h-12 mb-4 opacity-20" />
                            <p class="text-sm font-medium">Silakan centang jenis pemeriksaan di panel kiri untuk memunculkan detail parameter.</p>
                        </div>
                    @endif
                </div>

                @if(count($selectedDetails) > 0)
                    <div class="p-3 border-t border-neutral-100 dark:border-neutral-700 bg-[#4C5C2D]/5 flex items-center justify-between">
                        <span class="text-xs font-bold text-[#4C5C2D]">{{ count($selectedDetails) }} Parameter dipilih</span>
                    </div>
                @endif
            </div>

        </div>

    @elseif($kategori === 'PA')
        {{-- Patologi Anatomi UI --}}
        <div class="flex flex-col gap-6">
            {{-- Form Header PA --}}
            <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-sm border border-neutral-200 dark:border-neutral-700 p-6">
                <div class="flex items-center gap-2 mb-6 pb-4 border-b border-neutral-100 dark:border-neutral-700">
                    <flux:icon name="clipboard-document-list" class="w-5 h-5 text-[#4C5C2D]" />
                    <h3 class="font-bold text-neutral-800 dark:text-neutral-200 uppercase tracking-tight text-sm">Data Klinis Patologi Anatomi</h3>
                    
                    <div class="ml-auto flex items-center gap-2 px-3 py-1 bg-[#4C5C2D]/10 rounded-lg text-[#4C5C2D] border border-[#4C5C2D]/20">
                        <span class="text-[10px] font-bold uppercase">No. Permintaan :</span>
                        <span class="text-xs font-mono font-bold">{{ $predictedOrderNo }}</span>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <flux:input wire:model="pa_pengambilan_bahan" type="date" label="Tgl. Pengambilan Bahan" />
                    <flux:input wire:model="pa_diperoleh_dengan" label="Diperoleh Dengan" placeholder="Misal: Operasi, Biopsi, dsb" />
                    <flux:input wire:model="pa_lokasi_jaringan" label="Lokasi Pengambilan Jaringan" placeholder="Misal: Payudara Kiri, Tiroid, dsb" />
                    <flux:input wire:model="pa_diawetkan_dengan" label="Diawetkan/Direndam Dengan" placeholder="Misal: Formalin 10%" />
                    <flux:input wire:model="pa_pernah_dilakukan_di" label="Pernah Dilakukan PA Di" placeholder="Nama RS/Lab sebelumnya" />
                    <flux:input wire:model="pa_tanggal_sebelumnya" type="date" label="Pada Tanggal (Sebelumnya)" />
                    <flux:input wire:model="pa_nomor_sebelumnya" label="Dengan Nomor PA (Sebelumnya)" />
                    <flux:textarea wire:model="pa_diagnosa_sebelumnya" label="Dengan Diagnosa PA (Sebelumnya)" placeholder="Diagnosa hasil PA sebelumnya..." class="lg:col-span-2" rows="1" />
                </div>
            </div>

            {{-- Daftar Pemeriksaan PA --}}
            <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-sm border border-neutral-200 dark:border-neutral-700 overflow-hidden flex flex-col">
                <div class="p-4 border-b border-neutral-100 dark:border-neutral-700 bg-neutral-50/50 dark:bg-neutral-800/50 flex flex-col gap-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <flux:icon name="list-bullet" class="w-5 h-5 text-[#4C5C2D]" />
                            <h3 class="font-bold text-neutral-700 dark:text-neutral-300 uppercase tracking-tight text-sm">Permintaan Pemeriksaan PA</h3>
                        </div>
                        <flux:button wire:click="save" variant="primary" size="sm" icon="paper-airplane" class="!bg-[#4C5C2D] !border-none text-xs shadow-md">Kirim Permintaan PA</flux:button>
                    </div>
                    <flux:input wire:model.live.debounce.300ms="searchPemeriksaan" 
                                placeholder="Cari jenis pemeriksaan Patologi Anatomi..." 
                                icon="magnifying-glass" 
                                class="!py-1.5 !text-xs" />
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-white dark:bg-neutral-800 font-bold text-neutral-500 dark:text-neutral-400 text-[10px] uppercase tracking-wider">
                            <tr>
                                <th class="px-6 py-4 w-12 text-center">#</th>
                                <th class="px-6 py-4 w-40 text-center">Kode</th>
                                <th class="px-6 py-4">Nama Pemeriksaan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-100 dark:divide-neutral-700">
                            @forelse($pemeriksaanList as $item)
                                <tr class="hover:bg-[#4C5C2D]/5 transition-colors group">
                                    <td class="px-6 py-4 text-center">
                                        <flux:checkbox wire:model.live="selectedTests" value="{{ $item->kd_jenis_prw }}" class="accent-[#4C5C2D]" />
                                    </td>
                                    <td class="px-6 py-4 text-center font-mono text-xs text-neutral-500">{{ $item->kd_jenis_prw }}</td>
                                    <td class="px-6 py-4 text-sm font-semibold text-neutral-700 dark:text-neutral-200 group-hover:text-[#4C5C2D]">{{ $item->nm_perawatan }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="py-20 text-center flex flex-col items-center justify-center text-neutral-400">
                                        <flux:icon name="magnifying-glass" class="w-12 h-12 mb-4 opacity-10" />
                                        <p class="text-sm font-medium italic">Tidak ditemukan jenis pemeriksaan PA yang sesuai.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-3 border-t border-neutral-100 dark:border-neutral-700 bg-neutral-50/30">
                    {{ $pemeriksaanList->links() }}
                </div>
            </div>
        </div>

    @elseif($kategori === 'MB')
        {{-- Mikrobiologi & Bio Molekuler: Twin Tables Master-Detail (Sama seperti PK) --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">
            
            {{-- Panel Kiri: Daftar Pemeriksaan (Master) --}}
            <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-sm border border-neutral-200 dark:border-neutral-700 overflow-hidden flex flex-col h-[700px]">
                <div class="p-4 border-b border-neutral-100 dark:border-neutral-700 bg-neutral-50/50 dark:bg-neutral-800/50 flex flex-col gap-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <flux:icon name="beaker" class="w-5 h-5 text-[#4C5C2D]" />
                            <h3 class="font-bold text-neutral-700 dark:text-neutral-300 uppercase tracking-tight text-sm">Pemeriksaan Mikrobiologi</h3>
                        </div>
                        <div class="flex items-center gap-2 px-3 py-1 bg-[#4C5C2D]/10 rounded-lg text-[#4C5C2D] border border-[#4C5C2D]/20">
                            <span class="text-[10px] font-bold uppercase">No. Permintaan :</span>
                            <span class="text-xs font-mono font-bold">{{ $predictedOrderNo }}</span>
                        </div>
                    </div>
                    
                    <flux:input wire:model.live.debounce.300ms="searchPemeriksaan" 
                                placeholder="Cari Kode atau Nama Pemeriksaan MB..." 
                                icon="magnifying-glass" 
                                class="!py-1.5 !text-xs" />
                </div>

                <div class="flex-1 overflow-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="sticky top-0 bg-white dark:bg-neutral-800 shadow-sm z-10 font-bold text-neutral-500 dark:text-neutral-400 text-[10px] uppercase tracking-wider">
                            <tr>
                                <th class="px-4 py-3 w-8">#</th>
                                <th class="px-4 py-3 w-32 text-center">Kode</th>
                                <th class="px-4 py-3">Nama Pemeriksaan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-100 dark:divide-neutral-700">
                            @forelse($pemeriksaanList as $item)
                                <tr class="hover:bg-[#4C5C2D]/5 transition-colors group">
                                    <td class="px-4 py-3 text-center">
                                        <flux:checkbox wire:model.live="selectedTests" value="{{ $item->kd_jenis_prw }}" class="accent-[#4C5C2D]" />
                                    </td>
                                    <td class="px-4 py-3 text-center font-mono text-xs text-neutral-500">{{ $item->kd_jenis_prw }}</td>
                                    <td class="px-4 py-3 text-sm font-semibold text-neutral-700 dark:text-neutral-200 group-hover:text-[#4C5C2D]">{{ $item->nm_perawatan }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="py-12 text-center text-neutral-400 italic text-sm">Tidak ditemukan data.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <div class="p-3 border-t border-neutral-100 dark:border-neutral-700 bg-neutral-50/30">
                    {{ $pemeriksaanList->links() }}
                </div>
            </div>

            {{-- Panel Kanan: Detail Pemeriksaan (Detail) --}}
            <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-sm border border-neutral-200 dark:border-neutral-700 overflow-hidden flex flex-col h-[700px]">
                <div class="p-4 border-b border-neutral-100 dark:border-neutral-700 bg-neutral-50/50 dark:bg-neutral-800/50 flex flex-col gap-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <flux:icon name="magnifying-glass-plus" class="w-5 h-5 text-[#4C5C2D]" />
                            <h3 class="font-bold text-neutral-700 dark:text-neutral-300 uppercase tracking-tight text-sm">Detail MB</h3>
                        </div>
                        <div class="flex items-center gap-2">
                            {{-- Bulk Action Dropdown --}}
                            <flux:dropdown>
                                <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" class="!px-2 !py-1 text-xs">Bulk Action</flux:button>
                                <flux:menu>
                                    <flux:menu.item wire:click="toggleAllDetails(true)">Pilih Semua</flux:menu.item>
                                    <flux:menu.item wire:click="toggleAllDetails(false)">Clear Pilihan</flux:menu.item>
                                </flux:menu>
                            </flux:dropdown>

                            <flux:button wire:click="save" variant="primary" size="sm" icon="paper-airplane" class="!bg-[#4C5C2D] !border-none !text-xs shadow-md">Kirim Permintaan</flux:button>
                        </div>
                    </div>
                    <flux:input wire:model.live.debounce.300ms="searchDetail" 
                                placeholder="Cari dalam detail pemeriksaan..." 
                                icon="magnifying-glass" 
                                class="!py-1.5 !text-xs" />
                </div>

                <div class="flex-1 overflow-auto">
                    @if(count($selectedTests) > 0)
                        <table class="w-full text-left border-collapse">
                            <thead class="sticky top-0 bg-white dark:bg-neutral-800 shadow-sm z-10 font-bold text-neutral-500 dark:text-neutral-400 text-[10px] uppercase tracking-wider">
                                <tr>
                                    <th class="px-4 py-3 w-8">#</th>
                                    <th class="px-4 py-3">Pemeriksaan</th>
                                    <th class="px-4 py-3 text-center">Satuan</th>
                                    <th class="px-4 py-3 text-center">Nilai Rujukan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-neutral-100 dark:divide-neutral-700">
                                @foreach($detailParameters->groupBy('kd_jenis_prw') as $kd_jenis_prw => $group)
                                    {{-- Group Header --}}
                                    @php
                                        $groupIds = $group->pluck('id_template')->map(fn($id) => (string)$id)->toArray();
                                        $isGroupSelected = collect($groupIds)->every(fn($id) => in_array($id, $selectedDetails));
                                    @endphp
                                    <tr class="bg-[#4C5C2D]/10 dark:bg-[#4C5C2D]/20 border-y border-[#4C5C2D]/10">
                                        <td class="px-4 py-2 text-center">
                                            <flux:checkbox wire:click="toggleGroup('{{ $kd_jenis_prw }}')" :checked="$isGroupSelected" class="accent-[#4C5C2D]" />
                                        </td>
                                        <td colspan="3" class="px-4 py-2">
                                            <div class="flex items-center gap-2">
                                                <div class="w-1 h-4 bg-[#4C5C2D] rounded-full"></div>
                                                <span class="text-[10px] font-extrabold uppercase text-[#4C5C2D] tracking-wider">
                                                    {{ $group->first()->pemeriksaanHeader->nm_perawatan ?? 'Pemeriksaan' }}
                                                </span>
                                            </div>
                                        </td>
                                    </tr>

                                    @foreach($group as $detail)
                                        <tr class="hover:bg-[#4C5C2D]/5 transition-colors group">
                                            <td class="px-4 py-2 text-center">
                                                <flux:checkbox wire:model.live="selectedDetails" value="{{ (string)$detail->id_template }}" class="accent-[#4C5C2D]" />
                                            </td>
                                            <td class="px-4 py-2">
                                                <p class="text-xs font-bold text-neutral-700 dark:text-neutral-200">{{ $detail->Pemeriksaan }}</p>
                                            </td>
                                            <td class="px-4 py-2 text-center text-xs text-neutral-500">{{ $detail->satuan ?: '-' }}</td>
                                            <td class="px-4 py-2 text-center text-[11px] text-neutral-500">
                                                <div class="flex flex-col gap-0.5">
                                                    <span>LD: {{ $detail->nilai_rujukan_ld }} | LA: {{ $detail->nilai_rujukan_la }}</span>
                                                    <span>PD: {{ $detail->nilai_rujukan_pd }} | PA: {{ $detail->nilai_rujukan_pa }}</span>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="flex flex-col items-center justify-center h-full text-neutral-400 p-8 text-center">
                            <flux:icon name="hand-raised" class="w-12 h-12 mb-4 opacity-20" />
                            <p class="text-sm font-medium">Silakan centang jenis pemeriksaan Mikrobiologi di panel kiri.</p>
                        </div>
                    @endif
                </div>

                @if(count($selectedDetails) > 0)
                    <div class="p-3 border-t border-neutral-100 dark:border-neutral-700 bg-[#4C5C2D]/5 flex items-center justify-between">
                        <span class="text-xs font-bold text-[#4C5C2D]">{{ count($selectedDetails) }} Parameter dipilih</span>
                    </div>
                @endif
            </div>

        </div>

    @elseif($kategori === 'RIWAYAT')
        {{-- Riwayat Permintaan --}}
        <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-sm border border-neutral-200 dark:border-neutral-700 overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead class="bg-neutral-50 dark:bg-neutral-800/50 text-[10px] uppercase font-bold text-neutral-500 tracking-wider">
                    <tr>
                        <th class="px-6 py-4">No. Order & Waktu</th>
                        <th class="px-6 py-4">Dokter Perujuk</th>
                        <th class="px-6 py-4">Pemeriksaan</th>
                        <th class="px-6 py-4 text-center">Status</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100 dark:divide-neutral-700">
                    @forelse($this->pemeriksaanHistory as $history)
                        <tr class="hover:bg-neutral-50/50 dark:hover:bg-neutral-900/30 transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-bold text-sm text-neutral-700 dark:text-neutral-200">{{ $history->noorder }}</div>
                                <div class="text-[10px] text-neutral-400 font-medium tracking-tight">{{ date('d/m/Y', strtotime($history->tgl_permintaan)) }} - {{ $history->jam_permintaan }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-neutral-600 dark:text-neutral-400 font-medium">
                                {{ $history->dokter->nm_dokter ?? '-' }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-1 max-w-md">
                                    @foreach($history->detailPemeriksaan as $det)
                                        <span class="px-2 py-0.5 rounded bg-[#4C5C2D]/10 text-[#4C5C2D] text-[9px] font-bold border border-[#4C5C2D]/20 leading-tight">
                                            {{ $det->pemeriksaan->nm_perawatan ?? '' }}
                                        </span>
                                    @endforeach
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($history->tgl_sampel == '1000-01-01' || $history->tgl_sampel == '0000-00-00')
                                    <span class="px-2.5 py-1 rounded-full bg-neutral-100 text-neutral-500 text-[10px] font-bold border border-neutral-200">Menunggu Sampel</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full bg-green-100 text-green-600 text-[10px] font-bold border border-green-200 tracking-tight">Sudah Diproses</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($history->tgl_sampel == '1000-01-01' || $history->tgl_sampel == '0000-00-00')
                                    <button 
                                        type="button"
                                        onclick="confirm('Apakah Anda yakin ingin membatalkan permintaan ini?') || event.stopImmediatePropagation()"
                                        wire:click="batalPermintaan('{{ $history->noorder }}')"
                                        class="p-2 text-red-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-all"
                                        title="Batal Kirim"
                                    >
                                        <flux:icon name="trash" class="w-4 h-4" />
                                    </button>
                                @else
                                    <span class="text-[10px] text-neutral-300 italic">Tidak dapat dibatalkan</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-20">
                                <div class="flex flex-col items-center justify-center text-center text-neutral-400">
                                    <flux:icon name="archive-box" class="w-12 h-12 mb-4 opacity-20" />
                                    <p class="text-sm font-medium italic">Belum ada riwayat permintaan untuk pasien ini.</p>
                                </div>
                            </td>
                        </tr>

                    @endforelse
                </tbody>
            </table>
        </div>
    @endif



    {{-- MODAL LOOKUP DOKTER --}}
    <flux:modal name="dokter_modal" wire:model="isDokterModalOpen" variant="flyout" class="w-full max-w-lg p-0">
        <div class="flex flex-col h-full bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-700 rounded-xl overflow-hidden shadow-2xl">
            <div class="px-5 py-4 border-b border-neutral-200 dark:border-neutral-700 flex items-center justify-between bg-neutral-50 dark:bg-neutral-800">
                <h3 class="font-bold text-neutral-800 dark:text-neutral-200 flex items-center gap-2">
                    <flux:icon name="user-group" class="w-5 h-5 text-[#4C5C2D]" />
                    Pilih Dokter Perujuk
                </h3>
                <button type="button" wire:click="$set('isDokterModalOpen', false)" class="text-neutral-400 hover:text-neutral-600 dark:hover:text-neutral-200">
                    <flux:icon name="x-mark" class="w-5 h-5" />
                </button>
            </div>
            
            <div class="p-5 flex-1 overflow-hidden flex flex-col">
                <flux:input wire:model.live.debounce.300ms="searchDokterModal" icon="magnifying-glass" placeholder="Cari nama dokter..." class="mb-4" />

                <div class="flex-1 overflow-y-auto border border-neutral-200 dark:border-neutral-700 rounded-lg">
                    @if(count($listDokter) > 0)
                        <ul class="divide-y divide-neutral-100 dark:divide-neutral-800">
                            @foreach($listDokter as $dr)
                                <li class="group">
                                    <button 
                                        type="button" 
                                        wire:click="selectDokter('{{ $dr['kd_dokter'] }}', '{{ addslashes($dr['nm_dokter']) }}')" 
                                        class="w-full text-left px-5 py-4 hover:bg-[#F1F5E9] dark:hover:bg-neutral-800 transition-all duration-200 focus:outline-none group-last:rounded-b-lg"
                                    >
                                        <div class="flex items-start justify-between gap-3">
                                            <div class="flex-1">
                                                <div class="font-semibold text-sm text-neutral-800 dark:text-neutral-200 group-hover:text-[#4C5C2D] transition-colors">
                                                    {{ $dr['nm_dokter'] }}
                                                </div>
                                                <div class="text-[10px] text-neutral-400 font-mono mt-1 flex items-center gap-1">
                                                    <flux:icon name="identification" class="w-3 h-3 opacity-50" />
                                                    {{ $dr['kd_dokter'] }}
                                                </div>
                                            </div>
                                            <flux:icon name="chevron-right" class="w-4 h-4 text-neutral-300 opacity-0 group-hover:opacity-100 -translate-x-2 group-hover:translate-x-0 transition-all" />
                                        </div>
                                    </button>
                                </li>
                            @endforeach
                        </ul>

                    @else
                        <div class="p-8 text-center text-neutral-400 italic text-sm">Dokter tidak ditemukan.</div>
                    @endif
                </div>
            </div>
        </div>
    </flux:modal>
</div>
