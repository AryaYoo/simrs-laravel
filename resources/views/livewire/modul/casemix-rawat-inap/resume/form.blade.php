<div class="flex flex-col gap-6 pb-24" x-data="{ showKeluhanModal: false }">
    {{-- Sticky Header --}}
    <div class="sticky top-0 z-40 bg-white/80 dark:bg-neutral-900/80 backdrop-blur-md border-b border-neutral-200 dark:border-neutral-700 -mx-4 px-4 py-3 mb-2 flex items-center justify-between shadow-sm">
        <div class="flex items-center gap-3">
            <a href="{{ route('modul.casemix-rawat-inap.resume', str_replace('/', '-', $no_rawat)) }}" wire:navigate
               class="p-2 rounded-lg hover:bg-neutral-100 dark:hover:bg-neutral-800 transition-colors">
                <flux:icon name="chevron-left" class="w-5 h-5 text-neutral-500" />
            </a>
            <div>
                <h1 class="text-base font-bold text-neutral-800 dark:text-neutral-100">Buat/Edit Resume Casemix</h1>
                <p class="text-[10px] text-neutral-500 font-medium uppercase tracking-wider">{{ $regPeriksa->pasien->nm_pasien }} ({{ $no_rawat }})</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <flux:button href="{{ route('modul.casemix-rawat-inap.resume', str_replace('/', '-', $no_rawat)) }}" wire:navigate variant="ghost" class="h-9 text-sm">
                Batal
            </flux:button>
            <flux:button wire:click="save" variant="primary" icon="check" class="bg-[#4C5C2D] hover:bg-[#3D4A24] h-9 px-6 text-sm">
                Simpan Resume
            </flux:button>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-8 max-w-5xl mx-auto w-full">

        {{-- 1. Identitas & Admisi --}}
        <div id="section-1" class="bg-white dark:bg-neutral-800 rounded-2xl border border-neutral-200 dark:border-neutral-700 shadow-sm overflow-hidden">
            <div class="px-6 py-4 bg-neutral-50 dark:bg-neutral-900/50 border-b border-neutral-200 dark:border-neutral-700 flex items-center gap-3">
                <div class="w-6 h-6 rounded-full bg-[#4C5C2D]/10 flex items-center justify-center">
                    <span class="text-[#4C5C2D] font-bold text-xs">1</span>
                </div>
                <h2 class="text-sm font-bold text-neutral-800 dark:text-neutral-100 capitalize">Identitas & Admisi</h2>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <flux:input label="No. Rawat" value="{{ $no_rawat }}" disabled />
                    <flux:input label="Pasien" value="{{ $regPeriksa->pasien->nm_pasien }}" disabled />
                    <flux:input label="Dokter P.J. (DPJP)" value="{{ $regPeriksa->dokter->nm_dokter }}" disabled />
                    <flux:input label="Kamar/Bangsal" value="{{ $regPeriksa->kamarInap->last()->kamar->kd_kamar ?? '-' }} / {{ $regPeriksa->kamarInap->last()->kamar->bangsal->nm_bangsal ?? '-' }}" disabled />
                </div>
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <flux:input label="Tgl Masuk" value="{{ $regPeriksa->kamarInap->first()->tgl_masuk ?? '-' }}" disabled />
                        <flux:input label="Jam Masuk" value="{{ $regPeriksa->kamarInap->first()->jam_masuk ?? '-' }}" disabled />
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <flux:input label="Tgl Keluar" value="{{ $regPeriksa->kamarInap->last()->tgl_keluar ?? '-' }}" disabled />
                        <flux:input label="Jam Keluar" value="{{ $regPeriksa->kamarInap->last()->jam_keluar ?? '-' }}" disabled />
                    </div>
                    <flux:input label="Diagnosa Awal Masuk" wire:model="diagnosa_awal" placeholder="Keluhan / Diagnosa saat masuk..." />
                    <flux:textarea label="Alasan Masuk Dirawat" wire:model="alasan" rows="2" placeholder="Mengapa pasien dirawat?" />
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
                    {{-- 1. Keluhan Utama --}}
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <flux:label>Keluhan Utama & Riwayat Penyakit</flux:label>
                            <div class="flex items-center gap-2">
                                <flux:button wire:click="attachEarliest('keluhan_utama', 'keluhan')" variant="primary" size="xs" icon="clock" class="bg-[#4C5C2D] hover:bg-[#3D4A24] text-white" title="Ambil Keluhan Pertama" />
                                <button type="button" @click="$wire.prepareAttach('keluhan_utama', 'keluhan').then(() => { showKeluhanModal = true })" class="inline-flex items-center justify-center rounded-lg text-sm font-medium px-2 py-1 bg-[#4C5C2D] hover:bg-[#3D4A24] text-white transition" title="Pilih Keluhan">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M18.375 12.739l-7.693 7.693a4.5 4.5 0 01-6.364-6.364l10.94-10.94A3 3 0 1119.5 7.372L8.552 18.32m.009-.01l-.01.01m5.699-9.941l-7.81 7.81a1.5 1.5 0 002.112 2.13" /></svg>
                                </button>
                            </div>
                        </div>
                        <flux:textarea wire:model="keluhan_utama" rows="3" placeholder="Isi keluhan utama..." />
                    </div>

                    {{-- 2. Pemeriksaan Fisik --}}
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <flux:label>Pemeriksaan Fisik</flux:label>
                            <div class="flex items-center gap-2">
                                <flux:button wire:click="attachEarliest('pemeriksaan_fisik', 'pemeriksaan')" variant="primary" size="xs" icon="clock" class="bg-[#4C5C2D] hover:bg-[#3D4A24] text-white" title="Ambil Pemeriksaan Fisik Pertama" />
                                <button type="button" @click="$wire.prepareAttach('pemeriksaan_fisik', 'pemeriksaan').then(() => { showKeluhanModal = true })" class="inline-flex items-center justify-center rounded-lg text-sm font-medium px-2 py-1 bg-[#4C5C2D] hover:bg-[#3D4A24] text-white transition" title="Pilih Pemeriksaan Fisik">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M18.375 12.739l-7.693 7.693a4.5 4.5 0 01-6.364-6.364l10.94-10.94A3 3 0 1119.5 7.372L8.552 18.32m.009-.01l-.01.01m5.699-9.941l-7.81 7.81a1.5 1.5 0 002.112 2.13" /></svg>
                                </button>
                            </div>
                        </div>
                        <flux:textarea wire:model="pemeriksaan_fisik" rows="3" placeholder="Status generalis dan lokal..." />
                    </div>

                    {{-- 3. Riwayat Penyakit Sekarang (Jalannya Penyakit) --}}
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <flux:label>Riwayat Penyakit Sekarang (Jalannya Penyakit)</flux:label>
                            <div class="flex items-center gap-2">
                                <flux:button wire:click="attachEarliest('jalannya_penyakit', 'keluhan')" variant="primary" size="xs" icon="clock" class="bg-[#4C5C2D] hover:bg-[#3D4A24] text-white" title="Ambil Keluhan Pertama" />
                                <button type="button" @click="$wire.prepareAttach('jalannya_penyakit', 'keluhan').then(() => { showKeluhanModal = true })" class="inline-flex items-center justify-center rounded-lg text-sm font-medium px-2 py-1 bg-[#4C5C2D] hover:bg-[#3D4A24] text-white transition" title="Pilih Keluhan">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M18.375 12.739l-7.693 7.693a4.5 4.5 0 01-6.364-6.364l10.94-10.94A3 3 0 1119.5 7.372L8.552 18.32m.009-.01l-.01.01m5.699-9.941l-7.81 7.81a1.5 1.5 0 002.112 2.13" /></svg>
                                </button>
                            </div>
                        </div>
                        <flux:textarea wire:model="jalannya_penyakit" rows="3" placeholder="Perkembangan penyakit selama perawatan..." />
                    </div>

                    {{-- 4. RAD --}}
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <flux:label>Penunjang RAD Terpenting</flux:label>
                            <div class="flex items-center gap-2">
                                <button type="button" @click="$wire.prepareAttach('pemeriksaan_penunjang', 'rtl').then(() => { showKeluhanModal = true })" class="inline-flex items-center justify-center rounded-lg text-sm font-medium px-2 py-1 bg-[#4C5C2D] hover:bg-[#3D4A24] text-white transition" title="Ambil dari RTL">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M18.375 12.739l-7.693 7.693a4.5 4.5 0 01-6.364-6.364l10.94-10.94A3 3 0 1119.5 7.372L8.552 18.32m.009-.01l-.01.01m5.699-9.941l-7.81 7.81a1.5 1.5 0 002.112 2.13" /></svg>
                                </button>
                            </div>
                        </div>
                        <flux:textarea wire:model="pemeriksaan_penunjang" rows="3" placeholder="Hasil USG, Rontgen, CT-Scan, dll..." />
                    </div>

                    {{-- 5. LAB --}}
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <flux:label>Penunjang LAB Terpenting</flux:label>
                            <div class="flex items-center gap-2">
                                <button type="button" @click="$wire.prepareAttach('hasil_laborat', 'lab_hasil').then(() => { showKeluhanModal = true })" class="inline-flex items-center justify-center rounded-lg text-sm font-medium px-2 py-1 bg-[#4C5C2D] hover:bg-[#3D4A24] text-white transition" title="Ambil dari Pemeriksaan Lab">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M18.375 12.739l-7.693 7.693a4.5 4.5 0 01-6.364-6.364l10.94-10.94A3 3 0 1119.5 7.372L8.552 18.32m.009-.01l-.01.01m5.699-9.941l-7.81 7.81a1.5 1.5 0 002.112 2.13" /></svg>
                                </button>
                            </div>
                        </div>
                    <flux:textarea wire:model="hasil_laborat" rows="3" placeholder="Hasil Laboratorium darah, urin, dll..." />
                    </div>

                    {{-- 6. Tindakan & Obat --}}
                    <div class="grid grid-cols-1 gap-6 pt-2">
                        <flux:textarea label="Tindakan/Operasi Selama Perawatan" wire:model="tindakan_dan_operasi" rows="3" placeholder="Sebutkan tindakan medis atau operasi yang dilakukan..." />
                        <flux:textarea label="Obat-obatan Selama Perawatan" wire:model="obat_di_rs" rows="3" placeholder="Daftar obat-obatan selama pasien dirawat..." />
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
                                                wire:click="selectAutocompleteItem('{{ $result['kd_penyakit'] ?? $result['kode'] }}', '{{ $result['nm_penyakit'] ?? $result['deskripsi_panjang'] }}')"
                                                class="w-full text-left px-4 py-2.5 hover:bg-neutral-100 dark:hover:bg-neutral-700 transition-colors border-b last:border-0 border-neutral-100 dark:border-neutral-800">
                                                <p class="text-[11px] font-bold text-neutral-800 dark:text-neutral-100 leading-tight">{{ $result['nm_penyakit'] ?? $result['deskripsi_panjang'] }}</p>
                                                <p class="text-[10px] text-neutral-500 uppercase mt-1">{{ $result['kd_penyakit'] ?? $result['kode'] }}</p>
                                            </button>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                            <div class="w-24">
                                <flux:input label="Kode" wire:model="kd_diagnosa_utama" readonly />
                            </div>
                            <flux:button variant="ghost" icon="magnifying-glass" title="Cari ICD-10" @click="$wire.targetIcdField = 'diagnosa_utama'; $dispatch('open-modal', 'icd10-modal')" class="mb-0.5" />
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
                                                    wire:click="selectAutocompleteItem('{{ $result['kd_penyakit'] ?? $result['kode'] }}', '{{ $result['nm_penyakit'] ?? $result['deskripsi_panjang'] }}')"
                                                    class="w-full text-left px-4 py-2.5 hover:bg-neutral-100 dark:hover:bg-neutral-700 transition-colors border-b last:border-0 border-neutral-100 dark:border-neutral-800">
                                                    <p class="text-[11px] font-bold text-neutral-800 dark:text-neutral-100 leading-tight">{{ $result['nm_penyakit'] ?? $result['deskripsi_panjang'] }}</p>
                                                    <p class="text-[10px] text-neutral-500 uppercase mt-1">{{ $result['kd_penyakit'] ?? $result['kode'] }}</p>
                                                </button>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                                <div class="w-24">
                                    <flux:input wire:model="kd_{{ $field }}" readonly />
                                </div>
                                <flux:button variant="ghost" icon="magnifying-glass" @click="$wire.targetIcdField = '{{ $field }}'; $dispatch('open-modal', 'icd10-modal')" size="sm" />
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
                                                wire:click="selectAutocompleteItem('{{ $result['kode'] ?? $result['kd_penyakit'] }}', '{{ $result['deskripsi_panjang'] ?? $result['nm_penyakit'] }}')"
                                                class="w-full text-left px-4 py-2.5 hover:bg-neutral-100 dark:hover:bg-neutral-700 transition-colors border-b last:border-0 border-neutral-100 dark:border-neutral-800">
                                                <p class="text-[11px] font-bold text-neutral-800 dark:text-neutral-100 leading-tight">{{ $result['deskripsi_panjang'] ?? $result['nm_penyakit'] }}</p>
                                                <p class="text-[10px] text-neutral-500 uppercase mt-1">{{ $result['kode'] ?? $result['kd_penyakit'] }}</p>
                                            </button>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                            <div class="w-24">
                                <flux:input label="Kode" wire:model="kd_prosedur_utama" readonly />
                            </div>
                            <flux:button variant="ghost" icon="magnifying-glass" @click="$wire.targetIcdField = 'prosedur_utama'; $dispatch('open-modal', 'icd9-modal')" class="mb-0.5" />
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
                                                    wire:click="selectAutocompleteItem('{{ $result['kode'] ?? $result['kd_penyakit'] }}', '{{ $result['deskripsi_panjang'] ?? $result['nm_penyakit'] }}')"
                                                    class="w-full text-left px-4 py-2.5 hover:bg-neutral-100 dark:hover:bg-neutral-700 transition-colors border-b last:border-0 border-neutral-100 dark:border-neutral-800">
                                                    <p class="text-[11px] font-bold text-neutral-800 dark:text-neutral-100 leading-tight">{{ $result['deskripsi_panjang'] ?? $result['nm_penyakit'] }}</p>
                                                    <p class="text-[10px] text-neutral-500 uppercase mt-1">{{ $result['kode'] ?? $result['kd_penyakit'] }}</p>
                                                </button>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                                <div class="w-24">
                                    <flux:input wire:model="kd_{{ $field }}" readonly />
                                </div>
                                <flux:button variant="ghost" icon="magnifying-glass" @click="$wire.targetIcdField = '{{ $field }}'; $dispatch('open-modal', 'icd9-modal')" size="sm" />
                            </div>
                        @endfor

                        <div class="pt-4">
                            <flux:input label="Alergi Obat" wire:model="alergi" icon="exclamation-triangle" placeholder="Daftar alergi obat..." />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 4. Pemulangan & Follow Up --}}
        <div id="section-4" class="bg-white dark:bg-neutral-800 rounded-2xl border border-neutral-200 dark:border-neutral-700 shadow-sm overflow-hidden">
             <div class="px-6 py-4 bg-neutral-50 dark:bg-neutral-900/50 border-b border-neutral-200 dark:border-neutral-700 flex items-center gap-3">
                <div class="w-6 h-6 rounded-full bg-[#4C5C2D]/10 flex items-center justify-center">
                    <span class="text-[#4C5C2D] font-bold text-xs">4</span>
                </div>
                <h2 class="text-sm font-bold text-neutral-800 dark:text-neutral-100 capitalize">Pemulangan & Instruksi Lanjutan</h2>
            </div>
            <div class="p-6 space-y-6">
                <div class="grid grid-cols-1 gap-6">
                    <flux:textarea label="Diet Selama di Rumah" wire:model="diet" rows="2" />
                    <flux:textarea label="Hasil Lab yang Belum Selesai (Pending)" wire:model="lab_belum" rows="2" />
                    <flux:textarea label="Instruksi / Anjuran & Edukasi (Follow Up)" wire:model="edukasi" rows="3" />
                </div>
                
                <div class="grid grid-cols-1 gap-6 pt-4">
                    <div class="space-y-4">
                        <flux:select label="Keadaan Pulang" wire:model="keadaan">
                            <flux:select.option value="Membaik">Membaik</flux:select.option>
                            <flux:select.option value="Sembuh">Sembuh</flux:select.option>
                            <flux:select.option value="Keadaan Khusus">Keadaan Khusus</flux:select.option>
                            <flux:select.option value="Meninggal">Meninggal</flux:select.option>
                        </flux:select>
                        <flux:input wire:model="ket_keadaan" placeholder="Keterangan tambahan..." />
                    </div>
                    <div class="space-y-4">
                        <flux:select label="Cara Keluar" wire:model="cara_keluar">
                            <flux:select.option value="Atas Izin Dokter">Atas Izin Dokter</flux:select.option>
                            <flux:select.option value="Pindah RS">Pindah RS</flux:select.option>
                            <flux:select.option value="Pulang Atas Permintaan Sendiri">Pulang Atas Permintaan Sendiri</flux:select.option>
                            <flux:select.option value="Dirujuk">Dirujuk</flux:select.option>
                            <flux:select.option value="Lainnya">Lainnya</flux:select.option>
                        </flux:select>
                        <flux:input wire:model="ket_keluar" placeholder="Keterangan tambahan..." />
                    </div>
                    <div class="space-y-4">
                        <flux:select label="Dilanjutkan" wire:model="dilanjutkan">
                            <flux:select.option value="Kembali Ke RS">Kembali Ke RS</flux:select.option>
                            <flux:select.option value="RS Lain">RS Lain</flux:select.option>
                            <flux:select.option value="Dokter Luar">Dokter Luar</flux:select.option>
                            <flux:select.option value="Puskesmas">Puskesmas</flux:select.option>
                            <flux:select.option value="Lainnya">Lainnya</flux:select.option>
                        </flux:select>
                        <flux:input wire:model="ket_dilanjutkan" placeholder="Keterangan tambahan..." />
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-6 pt-4">
                    <flux:input type="datetime-local" label="Tanggal & Jam Kontrol" wire:model="kontrol" />
                    <flux:textarea label="Obat Pulang" wire:model="obat_pulang" rows="3" placeholder="Daftar obat yang dibawa pulang..." />
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
                                <td class="px-4 py-3 text-neutral-700 dark:text-neutral-300">{{ $icd->deskripsi }}</td>
                                <td class="px-4 py-3 text-center">
                                    <flux:button variant="ghost" size="sm" icon="plus" @click="$wire.selectIcd9('{{ $icd->kode }}', '{{ addslashes($icd->deskripsi) }}')">Pilih</flux:button>
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



    {{-- Floating Minimap Sidebar --}}
    <div x-data="{ 
            activeSection: 1,
            sections: [
                { id: 1, label: 'Identitas', icon: 'user' },
                { id: 2, label: 'Klinis', icon: 'clipboard-document-list' },
                { id: 3, label: 'Diagnosa', icon: 'bookmark-square' },
                { id: 4, label: 'Instruksi', icon: 'document-text' }
            ],
            init() {
                window.addEventListener('scroll', () => {
                    this.sections.forEach(s => {
                        const el = document.getElementById('section-' + s.id);
                        if (el) {
                            const rect = el.getBoundingClientRect();
                            if (rect.top <= 150 && rect.bottom >= 150) {
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

        <div class="group flex items-center justify-end gap-3">
            <span :class="activeSection === 4 ? 'opacity-100 text-[#4C5C2D] scale-100' : 'opacity-0 group-hover:opacity-100 text-neutral-400 scale-95 translate-x-2 group-hover:translate-x-0'"
                  class="text-[10px] font-black uppercase tracking-widest transition-all duration-300">Instruksi</span>
            <button @click="scrollTo(4)"
                    :class="activeSection === 4 ? 'bg-[#4C5C2D] text-white scale-110 shadow-lg' : 'bg-white dark:bg-neutral-800 text-neutral-400 hover:text-[#4C5C2D] border border-neutral-200 dark:border-neutral-700 shadow-sm hover:scale-105'"
                    class="w-10 h-10 rounded-xl flex items-center justify-center transition-all duration-300">
                <flux:icon name="document-text" class="w-5 h-5" />
            </button>
        </div>
    </div>
    {{-- MODAL ATTACH KELUHAN (Pure Alpine.js) --}}
    <div x-show="showKeluhanModal" x-cloak
         class="fixed inset-0 z-[99] overflow-y-auto"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        {{-- Backdrop --}}
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="showKeluhanModal = false"></div>
        {{-- Modal Content --}}
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative w-full max-w-2xl bg-white dark:bg-neutral-800 rounded-2xl shadow-2xl border border-neutral-200 dark:border-neutral-700"
                 x-show="showKeluhanModal"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                 @click.stop>
                <div class="p-6 space-y-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-lg font-bold text-neutral-800 dark:text-neutral-100">Ambil Data dari Pemeriksaan</h2>
                            <p class="text-sm text-neutral-500">Pilih satu atau beberapa data untuk ditambahkan ke Resume.</p>
                        </div>
                        <button type="button" wire:click="refreshData" class="p-2 text-neutral-400 hover:text-[#4C5C2D] transition-colors" title="Refresh Data">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 antialiased {{ $this->targetAttachColumn == 'lab_hasil' ? 'animate-none' : '' }}" wire:loading.class="animate-spin"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" /></svg>
                        </button>
                    </div>
                    
                    <div class="max-h-[500px] overflow-y-auto rounded-xl border border-neutral-200 dark:border-neutral-700">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-neutral-50 dark:bg-neutral-900/50 text-neutral-500 uppercase text-[10px] font-bold tracking-widest border-b border-neutral-200 sticky top-0">
                                <tr>
                                    <th class="px-4 py-3 w-12 text-center">
                                        <input type="checkbox" wire:click="toggleSelectAll" 
                                            class="rounded border-neutral-300 text-[#4C5C2D] focus:ring-[#4C5C2D]" 
                                            {{ ($targetAttachColumn == 'lab_hasil' ? (count($selectedLab) === count($regPeriksa->detailPeriksaLab) && count($selectedLab) > 0) : (count($selectedKeluhan) === count($regPeriksa->pemeriksaanRanap) && count($selectedKeluhan) > 0)) ? 'checked' : '' }}
                                        />
                                    </th>
                                    <th class="px-4 py-3">Tanggal/Jam</th>
                                    <th class="px-4 py-3">Isi Data</th>
                                    @if($targetAttachColumn == 'lab_hasil')
                                        <th class="px-4 py-3">Nilai Normal</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-neutral-100 dark:divide-neutral-800">
                                @if($targetAttachColumn == 'lab_hasil')
                                    @forelse($regPeriksa->detailPeriksaLab->sortByDesc(fn($lab) => $lab->tgl_periksa . ' ' . $lab->jam) as $lab)
                                        <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-800 transition-colors">
                                            <td class="px-4 py-3 text-center">
                                                <input type="checkbox" wire:model="selectedLab" value="{{ $lab->tgl_periksa . '|' . $lab->jam . '|' . $lab->kd_jenis_prw . '|' . $lab->id_template }}" class="rounded border-neutral-300 text-[#4C5C2D] focus:ring-[#4C5C2D]" />
                                            </td>
                                            <td class="px-4 py-3">
                                                <p class="text-xs font-bold text-neutral-700 dark:text-neutral-200">{{ $lab->tgl_periksa }}</p>
                                                <p class="text-[10px] text-neutral-500">{{ $lab->jam }}</p>
                                            </td>
                                            <td class="px-4 py-3 text-xs text-neutral-600 dark:text-neutral-400 leading-relaxed">
                                                <span class="font-medium text-neutral-800 dark:text-neutral-200">{{ $lab->template->Pemeriksaan ?? '-' }}</span> : <span class="text-[#4C5C2D] font-bold">{{ $lab->nilai }}</span>
                                            </td>
                                            <td class="px-4 py-3 text-xs text-neutral-500 italic">
                                                {{ $lab->nilai_rujukan }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="px-4 py-10 text-center text-neutral-400 italic">
                                                Data pemeriksaan lab belum tersedia untuk pasien ini.
                                            </td>
                                        </tr>
                                    @endforelse
                                @else
                                    @forelse($regPeriksa->pemeriksaanRanap as $pemeriksaan)
                                        <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-800 transition-colors">
                                            <td class="px-4 py-3 text-center">
                                                <input type="checkbox" wire:model="selectedKeluhan" value="{{ $pemeriksaan->tgl_perawatan . '|' . $pemeriksaan->jam_rawat }}" class="rounded border-neutral-300 text-[#4C5C2D] focus:ring-[#4C5C2D]" />
                                            </td>
                                            <td class="px-4 py-3">
                                                <p class="text-xs font-bold text-neutral-700 dark:text-neutral-200">{{ $pemeriksaan->tgl_perawatan }}</p>
                                                <p class="text-[10px] text-neutral-500">{{ $pemeriksaan->jam_rawat }}</p>
                                            </td>
                                            <td class="px-4 py-3 text-xs text-neutral-600 dark:text-neutral-400 leading-relaxed">
                                                <div class="space-y-1">
                                                    @if($targetAttachColumn == 'pemeriksaan')
                                                        <p class="font-bold text-[10px] text-[#4C5C2D] uppercase tracking-tighter">Pemeriksaan Fisik:</p>
                                                        <p>{{ $pemeriksaan->pemeriksaan }}</p>
                                                    @elseif($targetAttachColumn == 'rtl')
                                                        <p class="font-bold text-[10px] text-[#4C5C2D] uppercase tracking-tighter">RTL:</p>
                                                        <p>{{ $pemeriksaan->rtl }}</p>
                                                    @else
                                                        <p class="font-bold text-[10px] text-[#4C5C2D] uppercase tracking-tighter">Keluhan:</p>
                                                        <p>{{ $pemeriksaan->keluhan }}</p>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="px-4 py-10 text-center text-neutral-400 italic">
                                                Data pemeriksaan belum tersedia untuk pasien ini.
                                            </td>
                                        </tr>
                                    @endforelse
                                @endif
                            </tbody>
                        </table>
                    </div>

                    <div class="flex justify-between items-center pt-2">
                        <button type="button" @click="showKeluhanModal = false" class="px-4 py-2 text-sm text-neutral-600 hover:text-neutral-800 transition">Batal</button>
                        <button type="button" wire:click="{{ $targetAttachColumn == 'lab_hasil' ? 'attachLab' : 'attachKeluhan' }}" @click="showKeluhanModal = false" class="px-4 py-2 text-sm font-medium text-white bg-[#4C5C2D] hover:bg-[#3D4A24] rounded-lg transition">
                            Tambahkan yang Dipilih
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
