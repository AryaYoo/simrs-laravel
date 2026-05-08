<div class="flex flex-col gap-6 pb-8">
    {{-- Header --}}
    <div class="flex items-center justify-between bg-white dark:bg-neutral-800 p-4 rounded-2xl border border-neutral-200 dark:border-neutral-700 shadow-sm">
        <div class="flex items-center gap-4">
            <a href="{{ route('modul.rawat-jalan.perawatan-tindakan', str_replace('/', '-', $no_rawat)) }}" wire:navigate
               class="flex items-center justify-center w-10 h-10 rounded-xl bg-neutral-100 dark:bg-neutral-700 hover:bg-[#4C5C2D] hover:text-white transition-all group">
                <flux:icon name="chevron-left" class="w-5 h-5 text-neutral-500 group-hover:text-white" />
            </a>
            <div>
                <nav class="flex items-center gap-2 text-xs text-neutral-400 mb-0.5">
                    <a href="{{ route('modul.index') }}" wire:navigate class="hover:text-[#4C5C2D]">Modul</a>
                    <flux:icon name="chevron-right" class="w-3 h-3" />
                    <a href="{{ route('modul.rawat-jalan.index') }}" wire:navigate class="hover:text-[#4C5C2D]">Rawat Jalan</a>
                    <flux:icon name="chevron-right" class="w-3 h-3" />
                    <a href="{{ route('modul.rawat-jalan.perawatan-tindakan', str_replace('/', '-', $no_rawat)) }}" wire:navigate class="hover:text-[#4C5C2D]">Perawatan / Tindakan</a>
                    <flux:icon name="chevron-right" class="w-3 h-3" />
                    <span class="text-neutral-500">Triase IGD</span>
                </nav>
                <h1 class="text-xl font-bold text-neutral-800 dark:text-neutral-100">Data Triase IGD</h1>
            </div>
        </div>
        
        <div class="flex items-center gap-3">
             <div class="hidden md:flex flex-col items-end text-right">
                <span class="text-sm font-bold text-[#4C5C2D] dark:text-[#8CC7C4]">{{ $regPeriksa->pasien->nm_pasien ?? '-' }}</span>
                <span class="text-[10px] text-neutral-500 font-mono tracking-tighter">{{ $regPeriksa->no_rawat }}</span>
            </div>
            <div class="w-10 h-10 rounded-full bg-[#4C5C2D]/10 flex items-center justify-center border border-[#4C5C2D]/20">
                <flux:icon name="user" class="w-6 h-6 text-[#4C5C2D]" />
            </div>
        </div>
    </div>

    {{-- Main Tabs --}}
    <div class="flex flex-col gap-4">
        <div class="flex items-center gap-2 p-1 bg-neutral-100 dark:bg-neutral-800 rounded-xl w-fit">
            <button wire:click="$set('activeTab', 'input')" 
                class="px-6 py-2 text-sm font-bold rounded-lg transition-all {{ $activeTab === 'input' ? 'bg-[#4C5C2D] text-white shadow-md' : 'text-neutral-500 hover:bg-neutral-200 dark:hover:bg-neutral-700' }}">
                Input Data Triase
            </button>
            <button wire:click="$set('activeTab', 'data')" 
                class="px-6 py-2 text-sm font-bold rounded-lg transition-all {{ $activeTab === 'data' ? 'bg-[#4C5C2D] text-white shadow-md' : 'text-neutral-500 hover:bg-neutral-200 dark:hover:bg-neutral-700' }}">
                Data Triase
            </button>
        </div>

        @if($activeTab === 'input')
            <div class="flex flex-col gap-6">
                {{-- Card: Input Data Utama --}}
                <div class="bg-white dark:bg-neutral-800 rounded-2xl border border-neutral-200 dark:border-neutral-700 shadow-sm overflow-hidden">
                    <div class="px-5 py-3 bg-[#4C5C2D]/5 border-b border-neutral-200 dark:border-neutral-700 flex items-center gap-2">
                        <flux:icon name="document-text" class="w-5 h-5 text-[#4C5C2D]" />
                        <h2 class="font-bold text-[#4C5C2D] dark:text-white uppercase tracking-wider text-sm">Input Data Utama</h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                            {{-- Col 1 --}}
                            <div class="flex flex-col gap-5">
                                <flux:field>
                                    <flux:label>No. Rawat & RM</flux:label>
                                    <div class="flex items-center gap-2">
                                        <flux:input value="{{ $no_rawat }}" disabled class="bg-neutral-50 dark:bg-neutral-900 font-mono text-[10px]" />
                                        <flux:input value="{{ $regPeriksa->no_rkm_medis }}" disabled class="bg-neutral-50 dark:bg-neutral-900 font-mono text-[10px] w-28" />
                                    </div>
                                </flux:field>
                                <flux:field>
                                    <flux:label>Nama Pasien</flux:label>
                                    <flux:input value="{{ $regPeriksa->pasien->nm_pasien ?? '-' }}" disabled class="bg-neutral-50 dark:bg-neutral-900 font-semibold" />
                                </flux:field>
                                <flux:select wire:model="alat_transportasi" label="Transportasi">
                                    <option value="-">-</option>
                                    <option value="AGD">AGD</option>
                                    <option value="Sendiri">Sendiri</option>
                                    <option value="Swasta">Swasta</option>
                                </flux:select>
                            </div>

                            {{-- Col 2 --}}
                            <div class="flex flex-col gap-5">
                                <flux:input type="datetime-local" wire:model="tgl_kunjungan" label="Tgl. Kunjungan" />
                                <flux:select wire:model="cara_masuk" label="Cara Masuk">
                                    <option value="Jalan">Jalan</option>
                                    <option value="Brankar">Brankar</option>
                                    <option value="Kursi Roda">Kursi Roda</option>
                                    <option value="Digendong">Digendong</option>
                                </flux:select>
                                <flux:select wire:model="alasan_kedatangan" label="Alasan Kedatangan">
                                    <option value="Datang Sendiri">Datang Sendiri</option>
                                    <option value="Polisi">Polisi</option>
                                    <option value="Rujukan">Rujukan</option>
                                    <option value="Bidan">Bidan</option>
                                    <option value="Puskesmas">Puskesmas</option>
                                    <option value="Rumah Sakit">Rumah Sakit</option>
                                    <option value="Poliklinik">Poliklinik</option>
                                    <option value="Faskes Lain">Faskes Lain</option>
                                    <option value="-">-</option>
                                </flux:select>
                            </div>

                            {{-- Col 3 --}}
                            <div class="flex flex-col gap-5">
                                <flux:field>
                                    <flux:label>Macam Kasus</flux:label>
                                    <div class="flex items-center gap-2">
                                        <flux:select wire:model="kode_kasus" class="flex-1">
                                            <option value="">-- Pilih Macam Kasus --</option>
                                            @foreach($this->macamKasusList as $kasus)
                                                <option value="{{ $kasus->kode_kasus }}">{{ $kasus->macam_kasus }}</option>
                                            @endforeach
                                        </flux:select>
                                        <flux:button icon="paper-clip" variant="ghost" size="sm" class="shrink-0" />
                                    </div>
                                </flux:field>
                                <flux:textarea wire:model="keterangan_kedatangan" label="Keterangan" placeholder="Keterangan tambahan..." rows="4" resize="none" />
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Card: Input Triase --}}
                <div class="bg-white dark:bg-neutral-800 rounded-2xl border border-neutral-200 dark:border-neutral-700 shadow-sm overflow-hidden">
                    <div class="px-5 py-0 bg-neutral-50 dark:bg-neutral-900 border-b border-neutral-200 dark:border-neutral-700 flex items-center justify-between">
                        <div class="flex items-center gap-1">
                            <button type="button" wire:click="$set('activeSubTab', 'primer')" 
                                class="px-6 py-4 text-[11px] font-bold transition-all border-b-2 uppercase tracking-widest {{ $activeSubTab === 'primer' ? 'border-[#4C5C2D] text-[#4C5C2D]' : 'border-transparent text-neutral-400 hover:text-neutral-600' }}">
                                Triase Primer
                            </button>
                            <button type="button" wire:click="$set('activeSubTab', 'sekunder')" 
                                class="px-6 py-4 text-[11px] font-bold transition-all border-b-2 uppercase tracking-widest {{ $activeSubTab === 'sekunder' ? 'border-[#4C5C2D] text-[#4C5C2D]' : 'border-transparent text-neutral-400 hover:text-neutral-600' }}">
                                Triase Sekunder
                            </button>
                        </div>
                        <div class="flex items-center gap-2 px-4">
                             <flux:icon name="heart" class="w-4 h-4 text-red-500 animate-pulse" />
                             <span class="text-[10px] font-bold text-neutral-400 uppercase tracking-tighter">Clinical Observation</span>
                        </div>
                    </div>
                        {{-- Unified Triage Assessment Content --}}
                        <div class="flex flex-col gap-6 p-6">
                            {{-- Section 1: Header Fields (Dynamic based on Tab) --}}
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                                <div class="md:col-span-2">
                                    @if($activeSubTab === 'primer')
                                        <flux:textarea wire:model="keluhan_utama" label="Keluhan Utama" rows="4" placeholder="Keluhan utama pasien..." />
                                    @else
                                        <flux:textarea wire:model="anamnesa_singkat" label="Anamnesa Singkat" rows="4" placeholder="Anamnesa singkat pasien..." />
                                    @endif
                                </div>
                                <div class="md:col-span-2 grid grid-cols-2 gap-4">
                                    <flux:input wire:model="suhu" label="Suhu (C)" />
                                    <flux:input wire:model="nyeri" label="Skala Nyeri" />
                                    <flux:input wire:model="tekanan_darah" label="Tensi (mmHg)" />
                                    <flux:input wire:model="nadi" label="Nadi (/menit)" />
                                    <flux:input wire:model="saturasi_o2" label="SpO2 (%)" />
                                    <flux:input wire:model="pernapasan" label="Respirasi (/menit)" />
                                </div>
                            </div>

                            @if($activeSubTab === 'primer')
                                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                                    <div class="md:col-span-2">
                                        <flux:select wire:model="kebutuhan_khusus" label="Kebutuhan Khusus">
                                            <option value="-">-</option>
                                            <option value="UPPA">UPPA</option>
                                            <option value="Airborne">Airborne</option>
                                            <option value="Dekontaminan">Dekontaminan</option>
                                        </flux:select>
                                    </div>
                                </div>
                            @endif

                            {{-- Section 2: Scale Selection --}}
                            <div class="mt-2 border border-neutral-200 dark:border-neutral-700 rounded-xl overflow-hidden flex flex-col md:flex-row min-h-[400px] shadow-sm">
                                {{-- Left: Pemeriksaan List --}}
                                <div class="w-full md:w-1/3 bg-neutral-50 dark:bg-neutral-900 border-r border-neutral-200 dark:border-neutral-700 flex flex-col">
                                    <div class="p-3 bg-neutral-100 dark:bg-neutral-800 border-b border-neutral-200 dark:border-neutral-700 text-[10px] font-black uppercase tracking-widest text-neutral-500 text-center">
                                        Pemeriksaan
                                    </div>
                                    <div class="flex flex-col divide-y divide-neutral-100 dark:divide-neutral-800 overflow-y-auto max-h-[400px]">
                                        @foreach($this->masterPemeriksaan as $p)
                                            <button type="button" wire:click="selectPemeriksaan('{{ $p->kode_pemeriksaan }}')"
                                                wire:key="pemeriksaan-{{ $p->kode_pemeriksaan }}"
                                                class="px-5 py-4 text-left transition-all relative group {{ $selectedPemeriksaan === $p->kode_pemeriksaan ? 'bg-[#F1F5E9] text-[#4C5C2D]' : 'hover:bg-neutral-100 text-neutral-600' }}">
                                                <div class="flex items-center justify-between">
                                                    <span class="text-[11px] font-bold uppercase tracking-tight">{{ $p->nama_pemeriksaan }}</span>
                                                    @if($selectedPemeriksaan === $p->kode_pemeriksaan)
                                                        <div class="w-1.5 h-6 bg-[#4C5C2D] absolute left-0 top-1/2 -translate-y-1/2 rounded-r-full"></div>
                                                    @endif
                                                </div>
                                            </button>
                                        @endforeach
                                    </div>
                                </div>

                                {{-- Right: Scale Options --}}
                                <div class="w-full md:w-2/3 bg-white dark:bg-neutral-800 flex flex-col">
                                    {{-- Sub-Tabs for Scales --}}
                                    <div class="flex border-b border-neutral-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-900">
                                        @if($activeSubTab === 'primer')
                                            <button type="button" wire:click="$set('activeSkalaTab', 'skala1')" 
                                                class="flex-1 py-3 text-[10px] font-black uppercase tracking-widest border-b-2 transition-all {{ $activeSkalaTab === 'skala1' ? 'border-red-600 text-red-600 bg-red-50/30' : 'border-transparent text-neutral-400 hover:text-neutral-600' }}">
                                                Skala 1
                                            </button>
                                            <button type="button" wire:click="$set('activeSkalaTab', 'skala2')" 
                                                class="flex-1 py-3 text-[10px] font-black uppercase tracking-widest border-b-2 transition-all {{ $activeSkalaTab === 'skala2' ? 'border-orange-500 text-orange-500 bg-orange-50/30' : 'border-transparent text-neutral-400 hover:text-neutral-600' }}">
                                                Skala 2
                                            </button>
                                        @else
                                            <button type="button" wire:click="$set('activeSkalaTab', 'skala3')" 
                                                class="flex-1 py-3 text-[10px] font-black uppercase tracking-widest border-b-2 transition-all {{ $activeSkalaTab === 'skala3' ? 'border-yellow-500 text-yellow-600 bg-yellow-50/30' : 'border-transparent text-neutral-400 hover:text-neutral-600' }}">
                                                Skala 3
                                            </button>
                                            <button type="button" wire:click="$set('activeSkalaTab', 'skala4')" 
                                                class="flex-1 py-3 text-[10px] font-black uppercase tracking-widest border-b-2 transition-all {{ $activeSkalaTab === 'skala4' ? 'border-green-500 text-green-600 bg-green-50/30' : 'border-transparent text-neutral-400 hover:text-neutral-600' }}">
                                                Skala 4
                                            </button>
                                            <button type="button" wire:click="$set('activeSkalaTab', 'skala5')" 
                                                class="flex-1 py-3 text-[10px] font-black uppercase tracking-widest border-b-2 transition-all {{ $activeSkalaTab === 'skala5' ? 'border-blue-500 text-blue-600 bg-blue-50/30' : 'border-transparent text-neutral-400 hover:text-neutral-600' }}">
                                                Skala 5
                                            </button>
                                        @endif
                                    </div>

                                    <div class="p-6 overflow-y-auto max-h-[350px]">
                                        @php
                                            $activePemeriksaan = $this->masterPemeriksaan->where('kode_pemeriksaan', $selectedPemeriksaan)->first();
                                            $currentSkalaKey = match($activeSkalaTab) {
                                                'skala1' => 'skala1', 'skala2' => 'skala2', 'skala3' => 'skala3', 'skala4' => 'skala4', 'skala5' => 'skala5'
                                            };
                                            $options = $activePemeriksaan->$currentSkalaKey ?? [];
                                            $selectedArray = $this->{"selected" . ucfirst($activeSkalaTab)};
                                            $skalaNum = substr($activeSkalaTab, -1);
                                            $colorClass = match($activeSkalaTab) {
                                                'skala1' => 'red', 'skala2' => 'orange', 'skala3' => 'yellow', 'skala4' => 'green', 'skala5' => 'blue'
                                            };
                                            $labelField = "pengkajian_$activeSkalaTab";
                                            $kodeField = "kode_$activeSkalaTab";
                                        @endphp

                                        <div class="grid grid-cols-1 gap-2">
                                            @forelse($options as $opt)
                                                <label wire:key="item-{{ $activeSkalaTab }}-{{ $opt->$kodeField }}"
                                                    class="flex items-center gap-3 p-3 rounded-lg border border-neutral-100 hover:border-neutral-200 cursor-pointer transition-all {{ in_array($opt->$kodeField, $selectedArray) ? "bg-$colorClass-50 border-$colorClass-100" : "bg-white" }}">
                                                    <input type="checkbox" wire:click="toggleSkala({{ $skalaNum }}, '{{ $opt->$kodeField }}')" {{ in_array($opt->$kodeField, $selectedArray) ? 'checked' : '' }}
                                                        class="w-4 h-4 rounded border-neutral-300 text-{{ $colorClass }}-600 focus:ring-{{ $colorClass }}-500">
                                                    <span class="text-xs font-bold {{ in_array($opt->$kodeField, $selectedArray) ? "text-$colorClass-800" : "text-neutral-600" }}">
                                                        {{ $opt->$labelField }}
                                                    </span>
                                                </label>
                                            @empty
                                                <div class="flex flex-col items-center justify-center py-20 text-neutral-400 opacity-40 italic">
                                                    <flux:icon name="magnifying-glass" class="w-10 h-10 mb-2" />
                                                    <p class="text-xs">Pilih pemeriksaan untuk melihat opsi {{ $activeSkalaTab }}</p>
                                                </div>
                                            @endforelse
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Section 3: Catatan & Plan --}}
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 items-center mt-2">
                                <div class="md:col-span-2">
                                    <flux:input wire:model="{{ $activeSubTab === 'primer' ? 'catatan_primer' : 'catatan_sekunder' }}" label="Catatan Tambahan" placeholder="..." />
                                </div>
                                <div class="md:col-span-2 flex flex-col gap-3">
                                    <flux:label class="text-[10px] font-black uppercase tracking-widest text-neutral-400">Plan / Keputusan</flux:label>
                                    <div class="flex gap-6">
                                        @if($activeSubTab === 'primer')
                                            <label class="flex items-center gap-2 cursor-pointer group">
                                                <input type="radio" wire:model="plan_primer" value="Ruang Resusitasi" class="w-4 h-4 text-red-600 focus:ring-red-500">
                                                <span class="text-xs font-black uppercase text-neutral-500 group-hover:text-red-600 transition-colors">Resusitasi</span>
                                            </label>
                                            <label class="flex items-center gap-2 cursor-pointer group">
                                                <input type="radio" wire:model="plan_primer" value="Ruang Kritis" class="w-4 h-4 text-orange-600 focus:ring-orange-500">
                                                <span class="text-xs font-black uppercase text-neutral-500 group-hover:text-orange-600 transition-colors">Kritis</span>
                                            </label>
                                        @else
                                            <label class="flex items-center gap-2 cursor-pointer group">
                                                <input type="radio" wire:model="plan_sekunder" value="Zona Kuning" class="w-4 h-4 text-yellow-500 focus:ring-yellow-500">
                                                <span class="text-xs font-black uppercase text-neutral-500 group-hover:text-yellow-600 transition-colors">Zona Kuning</span>
                                            </label>
                                            <label class="flex items-center gap-2 cursor-pointer group">
                                                <input type="radio" wire:model="plan_sekunder" value="Zona Hijau" class="w-4 h-4 text-green-600 focus:ring-green-500">
                                                <span class="text-xs font-black uppercase text-neutral-500 group-hover:text-green-600 transition-colors">Zona Hijau</span>
                                            </label>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        </div>
                </div>

                {{-- Action Bar --}}
                <div class="flex items-center justify-end gap-3 pt-2">
                    <flux:button wire:click="loadData" variant="ghost">Batal / Reset</flux:button>
                    <flux:button wire:click="save" class="bg-[#4C5C2D] hover:bg-[#3d4a24] text-white px-10 h-11 text-sm font-bold shadow-lg shadow-[#4C5C2D]/20">
                        <flux:icon name="check" class="w-4 h-4 mr-2" />
                        Simpan Data Triase
                    </flux:button>
                </div>
            </div>
        @else
            {{-- Data Triase List --}}
            <div class="bg-white dark:bg-neutral-800 rounded-2xl border border-neutral-200 dark:border-neutral-700 shadow-sm overflow-hidden">
                <div class="px-5 py-3 bg-[#4C5C2D]/5 border-b border-neutral-200 dark:border-neutral-700 flex items-center gap-2">
                    <flux:icon name="list-bullet" class="w-5 h-5 text-[#4C5C2D]" />
                    <h2 class="font-bold text-[#4C5C2D] dark:text-white uppercase tracking-wider text-sm">List Data</h2>
                </div>
                <flux:table>
                    <flux:table.columns>
                        <flux:table.column class="!pl-6">Tgl. Kunjungan</flux:table.column>
                        <flux:table.column>Cara Masuk</flux:table.column>
                        <flux:table.column>Transportasi</flux:table.column>
                        <flux:table.column>Alasan</flux:table.column>
                        <flux:table.column>Macam Kasus</flux:table.column>
                        <flux:table.column>Keterangan</flux:table.column>
                        <flux:table.column align="center">Action</flux:table.column>
                    </flux:table.columns>
                    <flux:table.rows>
                        @forelse($this->triaseHistory as $item)
                            <flux:table.row :key="$item->no_rawat">
                                <flux:table.cell class="!pl-6 font-medium whitespace-nowrap">{{ $item->tgl_kunjungan }}</flux:table.cell>
                                <flux:table.cell>
                                    <span class="px-2 py-1 rounded-md bg-neutral-100 dark:bg-neutral-700 text-xs font-semibold">{{ $item->cara_masuk }}</span>
                                </flux:table.cell>
                                <flux:table.cell>{{ $item->alat_transportasi }}</flux:table.cell>
                                <flux:table.cell>{{ $item->alasan_kedatangan }}</flux:table.cell>
                                <flux:table.cell>
                                    <span class="text-[#4C5C2D] dark:text-[#8CC7C4] font-bold">{{ $item->macamKasus->macam_kasus ?? '-' }}</span>
                                </flux:table.cell>
                                <flux:table.cell class="max-w-xs truncate">{{ $item->keterangan_kedatangan }}</flux:table.cell>
                                <flux:table.cell>
                                    <div class="flex justify-center gap-1">
                                        <flux:button icon="pencil-square" size="xs" variant="ghost" 
                                            href="{{ route('modul.rawat-jalan.sub-rawat-jalan.triase-igd.index', str_replace('/', '-', $item->no_rawat)) }}" 
                                            wire:navigate />
                                        <flux:button icon="trash" size="xs" variant="ghost" class="text-red-500" 
                                            @click="
                                                Swal.fire({
                                                    title: 'Hapus Data Triase?',
                                                    text: 'Data yang dihapus tidak dapat dikembalikan!',
                                                    icon: 'warning',
                                                    showCancelButton: true,
                                                    confirmButtonColor: '#4C5C2D',
                                                    cancelButtonColor: '#d33',
                                                    confirmButtonText: 'Ya, Hapus!',
                                                    cancelButtonText: 'Batal'
                                                }).then((result) => {
                                                    if (result.isConfirmed) {
                                                        $wire.delete('{{ $item->no_rawat }}');
                                                    }
                                                });
                                            " />
                                    </div>
                                </flux:table.cell>
                            </flux:table.row>
                        @empty
                            <flux:table.row>
                                <flux:table.cell colspan="7">
                                    <div class="flex flex-col items-center justify-center py-20 text-neutral-400">
                                        <flux:icon name="inbox" class="w-16 h-16 mb-4 opacity-10" />
                                        <p class="text-sm font-medium">Belum ada data triase untuk pasien ini.</p>
                                    </div>
                                </flux:table.cell>
                            </flux:table.row>
                        @endforelse
                    </flux:table.rows>
                </flux:table>
            </div>
        @endif
    </div>
</div>
