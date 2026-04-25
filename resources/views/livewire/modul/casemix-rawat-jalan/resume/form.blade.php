<div class="flex flex-col gap-6 pb-24">
    {{-- Sticky Header --}}
    <div class="sticky top-0 z-40 bg-white/80 dark:bg-neutral-900/80 backdrop-blur-md border-b border-neutral-200 dark:border-neutral-700 -mx-4 px-4 py-3 mb-2 flex items-center justify-between shadow-sm">
        <div class="flex items-center gap-3">
            <a href="{{ route('modul.casemix-rawat-jalan.resume', str_replace('/', '-', $no_rawat)) }}" wire:navigate
               class="p-2 rounded-lg hover:bg-neutral-100 dark:hover:bg-neutral-800 transition-colors">
                <flux:icon name="chevron-left" class="w-5 h-5 text-neutral-500" />
            </a>
            <div>
                <h1 class="text-base font-bold text-neutral-800 dark:text-neutral-100">Buat/Edit Resume Casemix</h1>
                <p class="text-[10px] text-neutral-500 font-medium uppercase tracking-wider">{{ $regPeriksa->pasien->nm_pasien }} ({{ $no_rawat }})</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <flux:button href="{{ route('modul.casemix-rawat-jalan.resume', str_replace('/', '-', $no_rawat)) }}" wire:navigate variant="ghost" class="h-9 text-sm">
                Batal
            </flux:button>
            <flux:button wire:click="save" variant="primary" icon="check" class="bg-[#4C5C2D] hover:bg-[#3D4A24] h-9 px-6 text-sm">
                Simpan Resume
            </flux:button>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-8 max-w-5xl mx-auto w-full">

        {{-- 1. Identitas & Admisi --}}
        <div class="bg-white dark:bg-neutral-800 rounded-2xl border border-neutral-200 dark:border-neutral-700 shadow-sm overflow-hidden">
            <div class="px-6 py-4 bg-neutral-50 dark:bg-neutral-900/50 border-b border-neutral-200 dark:border-neutral-700 flex items-center gap-3">
                <div class="w-6 h-6 rounded-full bg-[#4C5C2D]/10 flex items-center justify-center">
                    <span class="text-[#4C5C2D] font-bold text-xs">1</span>
                </div>
                <h2 class="text-sm font-bold text-neutral-800 dark:text-neutral-100 capitalize">Identitas Registrasi</h2>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <flux:input label="No. Rawat" value="{{ $no_rawat }}" disabled />
                    <flux:input label="Pasien" value="{{ $regPeriksa->pasien->nm_pasien }}" disabled />
                </div>
                <div class="space-y-4">
                    <flux:input label="Dokter P.J. (DPJP)" value="{{ $regPeriksa->dokter->nm_dokter }}" disabled />
                    <flux:input label="Poliklinik" value="{{ $regPeriksa->poliklinik->nm_poli ?? '-' }}" disabled />
                </div>
            </div>
        </div>

        {{-- 2. Ringkasan Klinis --}}
        <div class="bg-white dark:bg-neutral-800 rounded-2xl border border-neutral-200 dark:border-neutral-700 shadow-sm overflow-hidden">
            <div class="px-6 py-4 bg-neutral-50 dark:bg-neutral-900/50 border-b border-neutral-200 dark:border-neutral-700 flex items-center gap-3">
                <div class="w-6 h-6 rounded-full bg-[#4C5C2D]/10 flex items-center justify-center">
                    <span class="text-[#4C5C2D] font-bold text-xs">2</span>
                </div>
                <h2 class="text-sm font-bold text-neutral-800 dark:text-neutral-100 capitalize">Ringkasan Klinis</h2>
            </div>
            <div class="p-6 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <flux:textarea label="Keluhan Utama" wire:model="keluhan_utama" rows="3" placeholder="Isi keluhan utama pasien..." />
                    <flux:textarea label="Jalannya Penyakit" wire:model="jalannya_penyakit" rows="3" placeholder="Riwayat jalannya penyakit / pemeriksaan..." />
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <flux:textarea label="Pemeriksaan Penunjang" wire:model="pemeriksaan_penunjang" rows="3" placeholder="Hasil USG, Rontgen, CT-Scan, dll..." />
                    <flux:textarea label="Hasil Laboratorium" wire:model="hasil_laborat" rows="3" placeholder="Hasil Laboratorium darah, urin, dll..." />
                </div>
            </div>
        </div>

        {{-- 3. Diagnosa & Prosedur --}}
        <div class="bg-white dark:bg-neutral-800 rounded-2xl border border-neutral-200 dark:border-neutral-700 shadow-sm overflow-hidden">
             <div class="px-6 py-4 bg-neutral-50 dark:bg-neutral-900/50 border-b border-neutral-200 dark:border-neutral-700 flex items-center gap-3">
                <div class="w-6 h-6 rounded-full bg-[#4C5C2D]/10 flex items-center justify-center">
                    <span class="text-[#4C5C2D] font-bold text-xs">3</span>
                </div>
                <h2 class="text-sm font-bold text-neutral-800 dark:text-neutral-100 capitalize">Diagnosa & Prosedur Akhir</h2>
            </div>
            <div class="p-6 space-y-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                    {{-- Diangnosa (ICD-10) --}}
                    <div class="space-y-4">
                        <h3 class="text-xs font-black uppercase tracking-widest text-[#4C5C2D] mb-4">Diagnosa Akhir (ICD-10)</h3>
                        
                        {{-- Diagnosa Utama --}}
                        <div class="flex items-end gap-2">
                            <div class="flex-1 relative">
                                <flux:input label="Diagnosa Utama" wire:model.live.debounce.300ms="diagnosa_utama" placeholder="Ketik minimal 3 karakter..." @focus="$wire.activeSearchField = 'diagnosa_utama'" />
                                
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
                                    <flux:input label="Diagnosa Sekunder {{ $i }}" wire:model.live.debounce.300ms="{{ $field }}" @focus="$wire.activeSearchField = '{{ $field }}'" />
                                    
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
                                <flux:input label="Prosedur Utama" wire:model.live.debounce.300ms="prosedur_utama" placeholder="Ketik minimal 3 karakter..." @focus="$wire.activeSearchField = 'prosedur_utama'" />
                                
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
                                    <flux:input label="Prosedur Sekunder {{ $i }}" wire:model.live.debounce.300ms="{{ $field }}" @focus="$wire.activeSearchField = '{{ $field }}'" />
                                    
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
                    </div>
                </div>
            </div>
        </div>

        {{-- 4. Pemulangan --}}
        <div class="bg-white dark:bg-neutral-800 rounded-2xl border border-neutral-200 dark:border-neutral-700 shadow-sm overflow-hidden">
             <div class="px-6 py-4 bg-neutral-50 dark:bg-neutral-900/50 border-b border-neutral-200 dark:border-neutral-700 flex items-center gap-3">
                <div class="w-6 h-6 rounded-full bg-[#4C5C2D]/10 flex items-center justify-center">
                    <span class="text-[#4C5C2D] font-bold text-xs">4</span>
                </div>
                <h2 class="text-sm font-bold text-neutral-800 dark:text-neutral-100 capitalize">Pemulangan Pasien</h2>
            </div>
            <div class="p-6 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <flux:select label="Kondisi Pulang" wire:model="kondisi_pulang">
                        <flux:select.option value="Hidup">Hidup</flux:select.option>
                        <flux:select.option value="Meninggal">Meninggal</flux:select.option>
                    </flux:select>
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
</div>
