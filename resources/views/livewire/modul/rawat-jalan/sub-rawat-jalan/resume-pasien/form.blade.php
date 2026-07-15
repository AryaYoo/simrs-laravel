<div class="flex flex-col gap-6 pb-24" 
    x-data="{ 
        isSubmitting: false,
        attachModal: false,
        attachType: ''
    }"
    x-init="
        Livewire.hook('commit', ({ succeed, fail }) => {
            succeed(() => {
                setTimeout(() => { isSubmitting = false; }, 500);
            });
            fail(() => {
                isSubmitting = false;
            });
        });
    "
>
    {{-- Sticky Header --}}
    <div class="sticky top-0 z-40 bg-white/80 dark:bg-neutral-900/80 backdrop-blur-md border-b border-neutral-200 dark:border-neutral-700 -mx-4 px-4 py-3 mb-2 flex items-center justify-between shadow-sm">
        <div class="flex items-center gap-3">
            <a href="{{ route('modul.rawat-jalan.sub-rawat-jalan.resume', str_replace('/', '-', $no_rawat)) }}" wire:navigate
               class="flex items-center justify-center w-8 h-8 rounded-lg bg-[#4C5C2D] hover:bg-[#3d4b24] transition-colors shadow-sm">
                <flux:icon name="chevron-left" class="w-5 h-5 text-white" />
            </a>
            <div>
                <h1 class="text-base font-bold text-neutral-800 dark:text-neutral-100">{{ $mode === 'create' ? 'Buat Resume Medis' : 'Edit Resume Medis' }}</h1>
                <p class="text-[10px] text-neutral-500 font-medium uppercase tracking-wider">{{ $regPeriksa->pasien->nm_pasien }} ({{ $no_rawat }})</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <flux:button href="{{ route('modul.rawat-jalan.sub-rawat-jalan.resume', str_replace('/', '-', $no_rawat)) }}" wire:navigate variant="ghost" class="h-9 text-sm">
                Batal
            </flux:button>
            <flux:button wire:click="save" @click="isSubmitting = true" variant="primary" icon="check" class="bg-[#4C5C2D] hover:bg-[#3D4A24] h-9 px-6 text-sm">
                Simpan Resume
            </flux:button>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-8 max-w-5xl mx-auto w-full">

        {{-- 1. Informasi Dasar --}}
        <div id="section-1" class="bg-white dark:bg-neutral-800 rounded-2xl border border-neutral-200 dark:border-neutral-700 shadow-sm overflow-hidden">
            <div class="px-6 py-4 bg-neutral-50 dark:bg-neutral-900/50 border-b border-neutral-200 dark:border-neutral-700 flex items-center gap-3">
                <div class="w-6 h-6 rounded-full bg-[#4C5C2D]/10 flex items-center justify-center">
                    <span class="text-[#4C5C2D] font-bold text-xs">1</span>
                </div>
                <h2 class="text-sm font-bold text-neutral-800 dark:text-neutral-100 capitalize">Informasi Dasar</h2>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <flux:input label="No. Rawat" value="{{ $no_rawat }}" disabled />
                    <flux:input label="Pasien" value="{{ $regPeriksa->pasien->nm_pasien ?? '-' }}" disabled />
                    <flux:input label="Poli / Poliklinik" value="{{ $regPeriksa->kd_poli }}" disabled />
                    <flux:input label="Tgl Registrasi" value="{{ \Carbon\Carbon::parse($regPeriksa->tgl_registrasi)->format('d F Y') }}" disabled />
                </div>
                <div class="space-y-4">
                    {{-- Dokter DPJP (editable with autocomplete) --}}
                    <div class="relative" x-data>
                        <flux:label>Dokter P.J. (DPJP) <span class="text-red-500">*</span></flux:label>
                        <div class="relative mt-1">
                            <input
                                type="text"
                                wire:model.live="searchDokter"
                                placeholder="{{ $nmDokter ?: 'Cari nama dokter...' }}"
                                class="w-full px-3 py-2 text-sm border border-neutral-300 dark:border-neutral-600 rounded-lg bg-white dark:bg-neutral-800 text-neutral-800 dark:text-neutral-100 focus:outline-none focus:ring-2 focus:ring-[#4C5C2D] placeholder-neutral-400"
                                autocomplete="off"
                            />
                            @if($nmDokter)
                                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-[10px] font-bold text-[#4C5C2D] bg-[#4C5C2D]/10 px-2 py-0.5 rounded-full">{{ $kd_dokter }}</span>
                            @endif
                        </div>
                        @if(!empty($dokterResults))
                            <ul class="absolute z-50 w-full mt-1 bg-white dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl shadow-xl max-h-56 overflow-y-auto text-sm">
                                @foreach($dokterResults as $d)
                                    <li wire:click="selectDokter('{{ $d['kd_dokter'] }}', '{{ addslashes($d['nm_dokter']) }}')"
                                        class="px-4 py-2.5 hover:bg-[#4C5C2D]/10 cursor-pointer flex items-center justify-between gap-2">
                                        <span class="font-medium text-neutral-800 dark:text-neutral-100">{{ $d['nm_dokter'] }}</span>
                                        <span class="text-[10px] text-neutral-400">{{ $d['kd_dokter'] }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                        @if($nmDokter && empty($dokterResults) && empty($searchDokter))
                            <p class="mt-1 text-xs text-neutral-500">Terpilih: <span class="font-semibold text-[#4C5C2D]">{{ $nmDokter }}</span></p>
                        @endif
                        @error('kd_dokter') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    
                    <div>
                        <flux:label>Kondisi Pasien Pulang <span class="text-red-500">*</span></flux:label>
                        <div class="mt-1 flex gap-3">
                            @foreach(['Hidup', 'Meninggal'] as $opt)
                                <label wire:click="$set('kondisi_pulang', '{{ $opt }}')"
                                    class="flex-1 flex items-center justify-center gap-2 py-2.5 rounded-xl border-2 cursor-pointer transition-all
                                        {{ $kondisi_pulang === $opt
                                            ? ($opt === 'Hidup' ? 'border-emerald-500 bg-emerald-50 text-emerald-700' : 'border-red-500 bg-red-50 text-red-700')
                                            : 'border-neutral-200 dark:border-neutral-700 text-neutral-500 hover:border-neutral-300' }}">
                                    <flux:icon name="{{ $opt === 'Hidup' ? 'heart' : 'x-circle' }}" class="w-4 h-4" />
                                    <span class="text-sm font-semibold">{{ $opt }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('kondisi_pulang') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- 2. Ringkasan Klinis --}}
        <div id="section-2" class="bg-white dark:bg-neutral-800 rounded-2xl border border-neutral-200 dark:border-neutral-700 shadow-sm overflow-hidden">
            <div class="px-6 py-4 bg-neutral-50 dark:bg-neutral-900/50 border-b border-neutral-200 dark:border-neutral-700 flex items-center gap-3">
                <div class="w-6 h-6 rounded-full bg-[#4C5C2D]/10 flex items-center justify-center">
                    <span class="text-[#4C5C2D] font-bold text-xs">2</span>
                </div>
                <h2 class="text-sm font-bold text-neutral-800 dark:text-neutral-100 capitalize">Ringkasan Klinis</h2>
            </div>
            <div class="p-6 space-y-6">
                <div class="grid grid-cols-1 gap-6">

                    {{-- Keluhan Utama --}}
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <flux:label>Keluhan Utama & Riwayat Penyakit</flux:label>
                            <div class="flex gap-1.5">
                                <button type="button"
                                    x-on:click="$wire.prepareAttach('keluhan_utama', 'keluhan').then(() => { attachType = 'keluhan'; attachModal = true; })"
                                    class="inline-flex items-center gap-1 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide rounded-lg bg-[#4C5C2D]/10 text-[#4C5C2D] hover:bg-[#4C5C2D] hover:text-white transition-all border border-[#4C5C2D]/20">
                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                    Attach
                                </button>
                            </div>
                        </div>
                        <flux:textarea wire:model="keluhan_utama" rows="5" placeholder="Tuliskan keluhan utama pasien..." />
                    </div>

                    {{-- Jalannya Penyakit --}}
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <flux:label>Jalannya Penyakit Selama Perawatan</flux:label>
                            <div class="flex gap-1.5">
                                <button type="button"
                                    x-on:click="$wire.prepareAttach('jalannya_penyakit', 'pemeriksaan').then(() => { attachType = 'keluhan'; attachModal = true; })"
                                    class="inline-flex items-center gap-1 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide rounded-lg bg-[#4C5C2D]/10 text-[#4C5C2D] hover:bg-[#4C5C2D] hover:text-white transition-all border border-[#4C5C2D]/20">
                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                    Attach
                                </button>
                            </div>
                        </div>
                        <flux:textarea wire:model="jalannya_penyakit" rows="5" placeholder="Tuliskan jalannya penyakit..." />
                    </div>

                    {{-- Pemeriksaan Penunjang --}}
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <flux:label>Pemeriksaan Penunjang Yang Positif</flux:label>
                            <div class="flex gap-1.5">
                                <button type="button"
                                    x-on:click="$wire.prepareAttach('pemeriksaan_penunjang', 'radiologi').then(() => { attachType = 'radiologi'; attachModal = true; })"
                                    class="inline-flex items-center gap-1 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide rounded-lg bg-purple-50 text-purple-600 hover:bg-purple-600 hover:text-white transition-all border border-purple-200">
                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                    Attach Rad
                                </button>
                            </div>
                        </div>
                        <flux:textarea wire:model="pemeriksaan_penunjang" rows="5" placeholder="Hasil pemeriksaan penunjang..." />
                    </div>

                    {{-- Hasil Laboratorium --}}
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <flux:label>Hasil Laboratorium Yang Positif</flux:label>
                            <div class="flex gap-1.5">
                                <button type="button"
                                    x-on:click="$wire.prepareAttach('hasil_laborat', 'lab').then(() => { attachType = 'lab'; attachModal = true; })"
                                    class="inline-flex items-center gap-1 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white transition-all border border-blue-200">
                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                    Attach Lab
                                </button>
                            </div>
                        </div>
                        <flux:textarea wire:model="hasil_laborat" rows="5" placeholder="Hasil pemeriksaan laboratorium..." />
                    </div>

                    {{-- Obat Pulang --}}
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <flux:label>Obat-obatan Waktu Pulang / Nasihat</flux:label>
                            <div class="flex gap-1.5">
                                <button type="button"
                                    x-on:click="$wire.prepareAttach('obat_pulang', 'obat').then(() => { attachType = 'obat'; attachModal = true; })"
                                    class="inline-flex items-center gap-1 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-600 hover:text-white transition-all border border-amber-200">
                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                    Attach Obat
                                </button>
                            </div>
                        </div>
                        <flux:textarea wire:model="obat_pulang" rows="5" placeholder="Daftar obat dan edukasi/nasihat saat pulang..." />
                    </div>

                </div>
            </div>
        </div>

        {{-- 3. Diagnosa & Prosedur Akhir --}}
        <div id="section-3" class="bg-white dark:bg-neutral-800 rounded-2xl border border-neutral-200 dark:border-neutral-700 shadow-sm overflow-hidden">
             <div class="px-6 py-4 bg-neutral-50 dark:bg-neutral-900/50 border-b border-neutral-200 dark:border-neutral-700 flex items-center gap-3">
                <div class="w-6 h-6 rounded-full bg-[#4C5C2D]/10 flex items-center justify-center">
                    <span class="text-[#4C5C2D] font-bold text-xs">3</span>
                </div>
                <h2 class="text-sm font-bold text-neutral-800 dark:text-neutral-100 capitalize">Diagnosa & Prosedur Akhir</h2>
            </div>
            <div class="p-6 space-y-8">
                <div class="grid grid-cols-1 gap-12">
                    {{-- Diangnosa (ICD-10) --}}
                    <div class="space-y-4">
                        <h3 class="text-xs font-black uppercase tracking-widest text-[#4C5C2D] mb-4">Diagnosa Akhir (ICD-10)</h3>
                        
                        {{-- Diagnosa Utama --}}
                        <div class="flex items-end gap-2">
                            <div class="flex-1 relative">
                                <flux:input label="Diagnosa Utama" wire:model.live.debounce.500ms="diagnosa_utama" placeholder="Ketik minimal 3 karakter..." @focus="$wire.activeSearchField = 'diagnosa_utama'" />
                                <div wire:loading wire:target="diagnosa_utama" class="absolute right-3 top-[34px]">
                                    <flux:icon name="arrow-path" class="w-4 h-4 animate-spin text-neutral-400" />
                                </div>
                                
                                @if($activeSearchField === 'diagnosa_utama' && !empty($autocompleteResults))
                                    <div class="absolute z-50 w-full mt-1 bg-white dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-lg shadow-xl overflow-hidden max-h-60 overflow-y-auto" x-on:click.outside="$wire.clearAutocomplete()">
                                        @foreach($autocompleteResults as $result)
                                            <button type="button" 
                                                wire:click="selectAutocompleteItem('{{ $result['kd_penyakit'] }}', '{{ addslashes($result['nm_penyakit']) }}')"
                                                class="w-full text-left px-4 py-2.5 hover:bg-neutral-100 dark:hover:bg-neutral-700 transition-colors border-b last:border-0 border-neutral-100 dark:border-neutral-800">
                                                <p class="text-[11px] font-bold text-neutral-800 dark:text-neutral-100 leading-tight">{{ $result['nm_penyakit'] }}</p>
                                                <p class="text-[10px] text-neutral-500 uppercase mt-1">{{ $result['kd_penyakit'] }}</p>
                                            </button>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                            <div class="w-24">
                                <flux:input label="Kode" wire:model="kd_diagnosa_utama" readonly />
                            </div>
                            <flux:button variant="ghost" icon="magnifying-glass" title="Cari ICD-10" @click="$wire.set('targetIcdField', 'diagnosa_utama'); $dispatch('open-modal', 'icd10-modal')" class="mb-0.5" />
                        </div>

                        {{-- Diagnosa Sekunder --}}
                        @for($i = 1; $i <= 4; $i++)
                            @php $field = 'diagnosa_sekunder' . ($i === 1 ? '' : $i); @endphp
                            <div class="flex items-end gap-2">
                                <div class="flex-1 relative">
                                    <flux:input label="Diagnosa Sekunder {{ $i }}" wire:model.live.debounce.500ms="{{ $field }}" @focus="$wire.activeSearchField = '{{ $field }}'" />
                                    <div wire:loading wire:target="{{ $field }}" class="absolute right-3 top-[34px]">
                                        <flux:icon name="arrow-path" class="w-4 h-4 animate-spin text-neutral-400" />
                                    </div>
                                    
                                    @if($activeSearchField === $field && !empty($autocompleteResults))
                                        <div class="absolute z-50 w-full mt-1 bg-white dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-lg shadow-xl overflow-hidden max-h-60 overflow-y-auto" x-on:click.outside="$wire.clearAutocomplete()">
                                            @foreach($autocompleteResults as $result)
                                                <button type="button" 
                                                    wire:click="selectAutocompleteItem('{{ $result['kd_penyakit'] }}', '{{ addslashes($result['nm_penyakit']) }}')"
                                                    class="w-full text-left px-4 py-2.5 hover:bg-neutral-100 dark:hover:bg-neutral-700 transition-colors border-b last:border-0 border-neutral-100 dark:border-neutral-800">
                                                    <p class="text-[11px] font-bold text-neutral-800 dark:text-neutral-100 leading-tight">{{ $result['nm_penyakit'] }}</p>
                                                    <p class="text-[10px] text-neutral-500 uppercase mt-1">{{ $result['kd_penyakit'] }}</p>
                                                </button>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                                <div class="w-24">
                                    <flux:input wire:model="kd_{{ $field }}" readonly />
                                </div>
                                <flux:button variant="ghost" icon="magnifying-glass" @click="$wire.set('targetIcdField', '{{ $field }}'); $dispatch('open-modal', 'icd10-modal')" size="sm" />
                            </div>
                        @endfor
                    </div>

                    {{-- Prosedur (ICD-9) --}}
                    <div class="space-y-4">
                        <h3 class="text-xs font-black uppercase tracking-widest text-[#4C5C2D] mb-4">Prosedur Akhir (ICD-9 CM)</h3>
                        
                        {{-- Prosedur Utama --}}
                        <div class="flex items-end gap-2">
                            <div class="flex-1 relative">
                                <flux:input label="Prosedur Utama" wire:model.live.debounce.500ms="prosedur_utama" placeholder="Ketik minimal 3 karakter..." @focus="$wire.activeSearchField = 'prosedur_utama'" />
                                <div wire:loading wire:target="prosedur_utama" class="absolute right-3 top-[34px]">
                                    <flux:icon name="arrow-path" class="w-4 h-4 animate-spin text-neutral-400" />
                                </div>
                                
                                @if($activeSearchField === 'prosedur_utama' && !empty($autocompleteResults))
                                    <div class="absolute z-50 w-full mt-1 bg-white dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-lg shadow-xl overflow-hidden max-h-60 overflow-y-auto" x-on:click.outside="$wire.clearAutocomplete()">
                                        @foreach($autocompleteResults as $result)
                                            <button type="button" 
                                                wire:click="selectAutocompleteItem('{{ $result['kode'] }}', '{{ addslashes($result['deskripsi_panjang']) }}')"
                                                class="w-full text-left px-4 py-2.5 hover:bg-neutral-100 dark:hover:bg-neutral-700 transition-colors border-b last:border-0 border-neutral-100 dark:border-neutral-800">
                                                <p class="text-[11px] font-bold text-neutral-800 dark:text-neutral-100 leading-tight">{{ $result['deskripsi_panjang'] }}</p>
                                                <p class="text-[10px] text-neutral-500 uppercase mt-1">{{ $result['kode'] }}</p>
                                            </button>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                            <div class="w-24">
                                <flux:input label="Kode" wire:model="kd_prosedur_utama" readonly />
                            </div>
                            <flux:button variant="ghost" icon="magnifying-glass" @click="$wire.set('targetIcdField', 'prosedur_utama'); $dispatch('open-modal', 'icd9-modal')" class="mb-0.5" />
                        </div>

                        {{-- Prosedur Sekunder --}}
                        @for($i = 1; $i <= 3; $i++)
                            @php $field = 'prosedur_sekunder' . ($i === 1 ? '' : $i); @endphp
                            <div class="flex items-end gap-2">
                                <div class="flex-1 relative">
                                    <flux:input label="Prosedur Sekunder {{ $i }}" wire:model.live.debounce.500ms="{{ $field }}" @focus="$wire.activeSearchField = '{{ $field }}'" />
                                    <div wire:loading wire:target="{{ $field }}" class="absolute right-3 top-[34px]">
                                        <flux:icon name="arrow-path" class="w-4 h-4 animate-spin text-neutral-400" />
                                    </div>
                                    
                                    @if($activeSearchField === $field && !empty($autocompleteResults))
                                        <div class="absolute z-50 w-full mt-1 bg-white dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-lg shadow-xl overflow-hidden max-h-60 overflow-y-auto" x-on:click.outside="$wire.clearAutocomplete()">
                                            @foreach($autocompleteResults as $result)
                                                <button type="button" 
                                                    wire:click="selectAutocompleteItem('{{ $result['kode'] }}', '{{ addslashes($result['deskripsi_panjang']) }}')"
                                                    class="w-full text-left px-4 py-2.5 hover:bg-neutral-100 dark:hover:bg-neutral-700 transition-colors border-b last:border-0 border-neutral-100 dark:border-neutral-800">
                                                    <p class="text-[11px] font-bold text-neutral-800 dark:text-neutral-100 leading-tight">{{ $result['deskripsi_panjang'] }}</p>
                                                    <p class="text-[10px] text-neutral-500 uppercase mt-1">{{ $result['kode'] }}</p>
                                                </button>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                                <div class="w-24">
                                    <flux:input wire:model="kd_{{ $field }}" readonly />
                                </div>
                                <flux:button variant="ghost" icon="magnifying-glass" @click="$wire.set('targetIcdField', '{{ $field }}'); $dispatch('open-modal', 'icd9-modal')" size="sm" />
                            </div>
                        @endfor
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- MODAL LOOKUP ICD-10 --}}
    <flux:modal name="icd10-modal" class="md:w-3/4 lg:w-1/2">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Cari Diagnosa (ICD-10)</flux:heading>
                <flux:subheading>Masukkan setidaknya 3 karakter untuk mencari diagnosa dari database.</flux:subheading>
            </div>
            
            <flux:input wire:model.live.debounce.300ms="searchIcd10" placeholder="Kode atau Nama Penyakit..." icon="magnifying-glass" autofocus />

            <div class="max-h-96 overflow-y-auto rounded-xl border border-neutral-200 dark:border-neutral-700">
                <table class="w-full text-left text-sm">
                    <thead class="bg-neutral-50 dark:bg-neutral-900/50 text-neutral-500 uppercase text-[10px] font-bold tracking-widest border-b border-neutral-200">
                        <tr>
                            <th class="px-4 py-3">Kode</th>
                            <th class="px-4 py-3">Nama Diagnosa</th>
                            <th class="px-4 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-100 dark:divide-neutral-800">
                        @forelse($icd10List as $icd)
                            <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-800 transition-colors">
                                <td class="px-4 py-3 font-bold text-[#4C5C2D] dark:text-[#8CC7C4]">{{ $icd->kd_penyakit }}</td>
                                <td class="px-4 py-3 text-neutral-700 dark:text-neutral-300">{{ $icd->nm_penyakit }}</td>
                                <td class="px-4 py-3 text-center">
                                    <flux:button variant="ghost" size="sm" icon="plus" @click="$wire.selectIcd10('{{ $icd->kd_penyakit }}', '{{ addslashes($icd->nm_penyakit) }}')">Pilih</flux:button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-4 py-10 text-center text-neutral-400 italic">
                                    {{ strlen($searchIcd10) < 3 ? 'Ketikkan minimal 3 karakter untuk mencari...' : 'Tidak ada diagnosis ditemukan.' }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="flex justify-end">
                <flux:modal.close>
                    <flux:button variant="ghost">Batal</flux:button>
                </flux:modal.close>
            </div>
        </div>
    </flux:modal>

    {{-- MODAL LOOKUP ICD-9 --}}
    <flux:modal name="icd9-modal" class="md:w-3/4 lg:w-1/2">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Cari Prosedur (ICD-9 CM)</flux:heading>
                <flux:subheading>Masukkan setidaknya 3 karakter untuk mencari prosedur dari database.</flux:subheading>
            </div>
            
            <flux:input wire:model.live.debounce.300ms="searchIcd9" placeholder="Kode atau Deskripsi Prosedur..." icon="magnifying-glass" />

            <div class="max-h-96 overflow-y-auto rounded-xl border border-neutral-200 dark:border-neutral-700">
                <table class="w-full text-left text-sm">
                    <thead class="bg-neutral-50 dark:bg-neutral-900/50 text-neutral-500 uppercase text-[10px] font-bold tracking-widest border-b border-neutral-200">
                        <tr>
                            <th class="px-4 py-3">Kode</th>
                            <th class="px-4 py-3">Deskripsi Prosedur</th>
                            <th class="px-4 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-100 dark:divide-neutral-800">
                        @forelse($icd9List as $icd)
                            <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-800 transition-colors">
                                <td class="px-4 py-3 font-bold text-[#4C5C2D] dark:text-[#8CC7C4]">{{ $icd->kode }}</td>
                                <td class="px-4 py-3 text-neutral-700 dark:text-neutral-300">{{ $icd->deskripsi_panjang }}</td>
                                <td class="px-4 py-3 text-center">
                                    <flux:button variant="ghost" size="sm" icon="plus" @click="$wire.selectIcd9('{{ $icd->kode }}', '{{ addslashes($icd->deskripsi_panjang) }}')">Pilih</flux:button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-4 py-10 text-center text-neutral-400 italic">
                                    {{ strlen($searchIcd9) < 3 ? 'Ketikkan minimal 3 karakter untuk mencari...' : 'Tidak ada prosedur ditemukan.' }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="flex justify-end">
                <flux:modal.close>
                    <flux:button variant="ghost">Batal</flux:button>
                </flux:modal.close>
            </div>
        </div>
    </flux:modal>

    {{-- ================================================================== --}}
    {{-- ATTACH MODAL (Alpine.js murni, sesuai SOP #6) --}}
    {{-- ================================================================== --}}
    <div x-show="attachModal" x-cloak
         class="fixed inset-0 z-[200] flex items-center justify-center p-4 bg-neutral-900/60 backdrop-blur-sm"
         x-on:keydown.escape.window="attachModal = false">
        <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-2xl border border-neutral-200 dark:border-neutral-800 w-full max-w-3xl flex flex-col h-[80vh]" @click.stop>

            {{-- Modal Header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-neutral-100 dark:border-neutral-800 shrink-0">
                <div>
                    <h3 class="font-bold text-lg text-neutral-800 dark:text-neutral-100">
                        <span x-show="attachType === 'keluhan'">Pilih Riwayat SOAP</span>
                        <span x-show="attachType === 'lab'">Pilih Hasil Laboratorium</span>
                        <span x-show="attachType === 'radiologi'">Pilih Hasil Radiologi</span>
                        <span x-show="attachType === 'obat'">Pilih Resep Obat</span>
                    </h3>
                    <p class="text-xs text-neutral-500 mt-0.5">Centang item yang ingin ditempel ke form. Data akan ditambahkan (append).</p>
                </div>
                <button @click="attachModal = false" class="text-neutral-400 hover:text-red-500 transition-colors p-1 rounded-md">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- Modal Body: Keluhan SOAP --}}
            <div x-show="attachType === 'keluhan'" class="flex-1 overflow-y-auto">
                <div class="px-4 py-3 border-b border-neutral-100 dark:border-neutral-800 bg-neutral-50/50 flex items-center justify-between">
                    <span class="text-xs text-neutral-500">{{ $this->keluhanData()->count() }} riwayat ditemukan</span>
                    <button wire:click="toggleSelectAll" class="text-xs font-semibold text-[#4C5C2D] hover:underline">Pilih Semua</button>
                </div>
                <table class="w-full text-sm">
                    <thead class="sticky top-0 bg-neutral-50 dark:bg-neutral-800 z-10">
                        <tr class="text-xs text-neutral-500 uppercase tracking-wider border-b border-neutral-200 dark:border-neutral-700">
                            <th class="px-4 py-2 w-10"></th>
                            <th class="px-4 py-2 text-left">Tanggal & Jam</th>
                            <th class="px-4 py-2 text-left">Keluhan / SOAP</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-100 dark:divide-neutral-800">
                        @forelse($this->keluhanData() as $item)
                            @php $key = $item->tgl_perawatan.'|'.$item->jam_rawat; @endphp
                            <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-800/50 transition-colors">
                                <td class="px-4 py-2.5 w-10">
                                    <input type="checkbox" value="{{ $key }}" wire:model="selectedKeluhan"
                                        class="w-4 h-4 accent-[#4C5C2D] rounded cursor-pointer">
                                </td>
                                <td class="px-4 py-2.5 text-xs text-neutral-500 whitespace-nowrap">
                                    {{ $item->tgl_perawatan }} {{ $item->jam_rawat }}
                                </td>
                                <td class="px-4 py-2.5 text-xs text-neutral-700 dark:text-neutral-300 max-w-xs truncate">
                                    @php $col = $targetAttachColumn; @endphp
                                    {{ $item->$col ? Str::limit($item->$col, 120) : '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="px-4 py-8 text-center text-neutral-400 text-sm">Belum ada riwayat SOAP.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Modal Body: Hasil Lab --}}
            <div x-show="attachType === 'lab'" class="flex-1 overflow-y-auto">
                <div class="px-4 py-3 border-b border-neutral-100 dark:border-neutral-800 bg-neutral-50/50 flex items-center justify-between">
                    <span class="text-xs text-neutral-500">{{ $this->labHasilData()->count() }} hasil ditemukan</span>
                    <button wire:click="toggleSelectAll" class="text-xs font-semibold text-blue-600 hover:underline">Pilih Semua</button>
                </div>
                <table class="w-full text-sm">
                    <thead class="sticky top-0 bg-neutral-50 dark:bg-neutral-800 z-10">
                        <tr class="text-xs text-neutral-500 uppercase tracking-wider border-b border-neutral-200 dark:border-neutral-700">
                            <th class="px-4 py-2 w-10"></th>
                            <th class="px-4 py-2 text-left">Tanggal</th>
                            <th class="px-4 py-2 text-left">Pemeriksaan</th>
                            <th class="px-4 py-2 text-left">Nilai</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-100 dark:divide-neutral-800">
                        @forelse($this->labHasilData() as $item)
                            @php $key = $item->tgl_periksa.'|'.$item->jam.'|'.$item->kd_jenis_prw.'|'.$item->id_template; @endphp
                            <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-800/50 transition-colors">
                                <td class="px-4 py-2.5 w-10">
                                    <input type="checkbox" value="{{ $key }}" wire:model="selectedLab"
                                        class="w-4 h-4 accent-blue-600 rounded cursor-pointer">
                                </td>
                                <td class="px-4 py-2.5 text-xs text-neutral-500 whitespace-nowrap">{{ $item->tgl_periksa }}</td>
                                <td class="px-4 py-2.5 text-xs font-medium text-neutral-700 dark:text-neutral-300">{{ $item->template->Pemeriksaan ?? '-' }}</td>
                                <td class="px-4 py-2.5 text-xs text-neutral-600 dark:text-neutral-400">{{ $item->nilai ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-4 py-8 text-center text-neutral-400 text-sm">Belum ada hasil laboratorium.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Modal Body: Hasil Radiologi --}}
            <div x-show="attachType === 'radiologi'" class="flex-1 overflow-y-auto">
                <div class="px-4 py-3 border-b border-neutral-100 dark:border-neutral-800 bg-neutral-50/50 flex items-center justify-between">
                    <span class="text-xs text-neutral-500">{{ $this->radiologiData()->count() }} hasil ditemukan</span>
                    <button wire:click="toggleSelectAll" class="text-xs font-semibold text-purple-600 hover:underline">Pilih Semua</button>
                </div>
                <table class="w-full text-sm">
                    <thead class="sticky top-0 bg-neutral-50 dark:bg-neutral-800 z-10">
                        <tr class="text-xs text-neutral-500 uppercase tracking-wider border-b border-neutral-200 dark:border-neutral-700">
                            <th class="px-4 py-2 w-10"></th>
                            <th class="px-4 py-2 text-left">Tanggal</th>
                            <th class="px-4 py-2 text-left">Pemeriksaan</th>
                            <th class="px-4 py-2 text-left">Hasil</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-100 dark:divide-neutral-800">
                        @forelse($this->radiologiData() as $item)
                            @php $key = $item->tgl_periksa.'|'.$item->jam.'|'.$item->kd_jenis_prw; @endphp
                            <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-800/50 transition-colors">
                                <td class="px-4 py-2.5 w-10">
                                    <input type="checkbox" value="{{ $key }}" wire:model="selectedRadiologi"
                                        class="w-4 h-4 accent-purple-600 rounded cursor-pointer">
                                </td>
                                <td class="px-4 py-2.5 text-xs text-neutral-500 whitespace-nowrap">{{ $item->tgl_periksa }}</td>
                                <td class="px-4 py-2.5 text-xs font-medium text-neutral-700 dark:text-neutral-300">{{ $item->jnsPerawatan->nm_perawatan ?? '-' }}</td>
                                <td class="px-4 py-2.5 text-xs text-neutral-600 dark:text-neutral-400 max-w-xs truncate">{{ $item->hasil ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-4 py-8 text-center text-neutral-400 text-sm">Belum ada hasil radiologi.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Modal Body: Obat --}}
            <div x-show="attachType === 'obat'" class="flex-1 overflow-y-auto">
                <div class="px-4 py-3 border-b border-neutral-100 dark:border-neutral-800 bg-neutral-50/50 flex items-center justify-between">
                    <span class="text-xs text-neutral-500">{{ $this->obatData()->count() }} item obat ditemukan</span>
                    <button wire:click="toggleSelectAll" class="text-xs font-semibold text-amber-600 hover:underline">Pilih Semua</button>
                </div>
                <table class="w-full text-sm">
                    <thead class="sticky top-0 bg-neutral-50 dark:bg-neutral-800 z-10">
                        <tr class="text-xs text-neutral-500 uppercase tracking-wider border-b border-neutral-200 dark:border-neutral-700">
                            <th class="px-4 py-2 w-10"></th>
                            <th class="px-4 py-2 text-left">Nama Obat</th>
                            <th class="px-4 py-2 text-left">Jumlah</th>
                            <th class="px-4 py-2 text-left">Aturan Pakai</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-100 dark:divide-neutral-800">
                        @forelse($this->obatData() as $item)
                            @php $key = $item->no_resep.'|'.$item->kode_brng; @endphp
                            <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-800/50 transition-colors">
                                <td class="px-4 py-2.5 w-10">
                                    <input type="checkbox" value="{{ $key }}" wire:model="selectedObat"
                                        class="w-4 h-4 accent-amber-500 rounded cursor-pointer">
                                </td>
                                <td class="px-4 py-2.5 text-xs font-medium text-neutral-700 dark:text-neutral-300">{{ $item->barang->nama_brng ?? '-' }}</td>
                                <td class="px-4 py-2.5 text-xs text-neutral-500">{{ $item->jml ?? '-' }} {{ $item->barang->kode_sat ?? '' }}</td>
                                <td class="px-4 py-2.5 text-xs text-neutral-500">{{ $item->aturan_pakai ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-4 py-8 text-center text-neutral-400 text-sm">Belum ada data resep obat.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Modal Footer --}}
            <div class="px-6 py-4 border-t border-neutral-100 dark:border-neutral-800 bg-neutral-50/50 dark:bg-neutral-900 flex justify-between items-center shrink-0 rounded-b-2xl">
                <button @click="attachModal = false" class="px-4 py-2 text-sm font-medium text-neutral-500 hover:text-neutral-700 transition-colors">Batal</button>
                <button
                    x-show="attachType === 'keluhan'"
                    wire:click="attachKeluhan"
                    @click="attachModal = false"
                    class="px-5 py-2 text-sm font-bold rounded-xl bg-[#4C5C2D] text-white hover:bg-[#3d4b24] transition-colors shadow-sm"
                >Tempel ke Form</button>
                <button
                    x-show="attachType === 'lab'"
                    wire:click="attachLab"
                    @click="attachModal = false"
                    class="px-5 py-2 text-sm font-bold rounded-xl bg-blue-600 text-white hover:bg-blue-700 transition-colors shadow-sm"
                >Tempel ke Form</button>
                <button
                    x-show="attachType === 'radiologi'"
                    wire:click="attachRadiologi"
                    @click="attachModal = false"
                    class="px-5 py-2 text-sm font-bold rounded-xl bg-purple-600 text-white hover:bg-purple-700 transition-colors shadow-sm"
                >Tempel ke Form</button>
                <button
                    x-show="attachType === 'obat'"
                    wire:click="attachObat"
                    @click="attachModal = false"
                    class="px-5 py-2 text-sm font-bold rounded-xl bg-amber-500 text-white hover:bg-amber-600 transition-colors shadow-sm"
                >Tempel ke Form</button>
            </div>
        </div>
    </div>

    {{-- Floating Minimap Sidebar --}}
    <div x-data="{ 
            activeSection: 1,
            sections: [
                { id: 1, label: 'Identitas', icon: 'user' },
                { id: 2, label: 'Klinis', icon: 'clipboard-document-list' },
                { id: 3, label: 'Diagnosa', icon: 'bookmark-square' }
            ],
            init() {
                window.addEventListener('scroll', () => {
                    this.sections.forEach(s => {
                        const el = document.getElementById('section-' + s.id);
                        if (el) {
                            const rect = el.getBoundingClientRect();
                            if (rect.top <= 200 && rect.bottom >= 200) {
                                this.activeSection = s.id;
                            }
                        }
                    });
                });
            },
            scrollTo(id) {
                const el = document.getElementById('section-' + id);
                if (el) {
                    const top = el.offsetTop - 100;
                    window.scrollTo({ top: top, behavior: 'smooth' });
                }
            }
         }" 
         class="fixed right-6 top-1/2 -translate-y-1/2 z-40 hidden xl:flex flex-col gap-3">
         
        <div class="group flex items-center justify-end gap-3">
            <span :class="activeSection === 1 ? 'opacity-100 text-[#4C5C2D] scale-100' : 'opacity-0 group-hover:opacity-100 text-neutral-400 scale-95 translate-x-2 group-hover:translate-x-0'"
                  class="text-[10px] font-black uppercase tracking-widest transition-all duration-300">Identitas</span>
            <button @click="scrollTo(1)"
                    :class="activeSection === 1 ? 'bg-[#4C5C2D] text-white scale-110 shadow-lg' : 'bg-white dark:bg-neutral-800 text-neutral-400 hover:text-[#4C5C2D] border border-neutral-200 dark:border-neutral-700 shadow-sm hover:scale-105'"
                    class="w-10 h-10 rounded-xl flex items-center justify-center transition-all duration-300">
                <flux:icon name="user" class="w-5 h-5" />
            </button>
        </div>

        <div class="group flex items-center justify-end gap-3">
            <span :class="activeSection === 2 ? 'opacity-100 text-[#4C5C2D] scale-100' : 'opacity-0 group-hover:opacity-100 text-neutral-400 scale-95 translate-x-2 group-hover:translate-x-0'"
                  class="text-[10px] font-black uppercase tracking-widest transition-all duration-300">Klinis</span>
            <button @click="scrollTo(2)"
                    :class="activeSection === 2 ? 'bg-[#4C5C2D] text-white scale-110 shadow-lg' : 'bg-white dark:bg-neutral-800 text-neutral-400 hover:text-[#4C5C2D] border border-neutral-200 dark:border-neutral-700 shadow-sm hover:scale-105'"
                    class="w-10 h-10 rounded-xl flex items-center justify-center transition-all duration-300">
                <flux:icon name="clipboard-document-list" class="w-5 h-5" />
            </button>
        </div>

        <div class="group flex items-center justify-end gap-3">
            <span :class="activeSection === 3 ? 'opacity-100 text-[#4C5C2D] scale-100' : 'opacity-0 group-hover:opacity-100 text-neutral-400 scale-95 translate-x-2 group-hover:translate-x-0'"
                  class="text-[10px] font-black uppercase tracking-widest transition-all duration-300">Diagnosa</span>
            <button @click="scrollTo(3)"
                    :class="activeSection === 3 ? 'bg-[#4C5C2D] text-white scale-110 shadow-lg' : 'bg-white dark:bg-neutral-800 text-neutral-400 hover:text-[#4C5C2D] border border-neutral-200 dark:border-neutral-700 shadow-sm hover:scale-105'"
                    class="w-10 h-10 rounded-xl flex items-center justify-center transition-all duration-300">
                <flux:icon name="bookmark-square" class="w-5 h-5" />
            </button>
        </div>
    </div>
</div>
