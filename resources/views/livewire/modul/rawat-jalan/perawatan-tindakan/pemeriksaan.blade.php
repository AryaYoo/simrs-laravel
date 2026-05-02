<div class="animate-in fade-in duration-300">
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-2">
            <flux:icon name="clipboard-document-check" class="w-5 h-5 text-[#4C5C2D]" />
            <h3 class="text-sm font-semibold text-neutral-700 dark:text-neutral-200">Data Pemeriksaan Rawat Jalan</h3>
        </div>
        <div class="flex items-center gap-3">
            <span class="text-xs text-neutral-400 bg-neutral-100 dark:bg-neutral-700 px-2 py-1 rounded-full">{{ $this->pemeriksaanRalan->count() }} catatan</span>
            <flux:button wire:click="openCreateModal" icon="plus" size="sm" variant="primary">
                Tambah Pemeriksaan
            </flux:button>
        </div>
    </div>

    <flux:table>
        <flux:table.columns>
            <flux:table.column>{{ __('Tanggal Rawat') }}</flux:table.column>
            <flux:table.column>{{ __('Jam') }}</flux:table.column>
            <flux:table.column>{{ __('Suhu (°C)') }}</flux:table.column>
            <flux:table.column>{{ __('Tensi') }}</flux:table.column>
            <flux:table.column>{{ __('Nadi (/mnt)') }}</flux:table.column>
            <flux:table.column>{{ __('Respirasi (/mnt)') }}</flux:table.column>
            <flux:table.column>{{ __('Tinggi (cm)') }}</flux:table.column>
            <flux:table.column>{{ __('Berat (kg)') }}</flux:table.column>
            <flux:table.column>{{ __('SpO2 (%)') }}</flux:table.column>
            <flux:table.column>{{ __('GCS (E,V,M)') }}</flux:table.column>
            <flux:table.column>{{ __('Dokter / Paramedis') }}</flux:table.column>
            <flux:table.column>{{ __('Aksi') }}</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse ($this->pemeriksaanRalan as $item)
                @php
                    $detailJson = json_encode([
                        'no_rawat'      => $item->no_rawat,
                        'tgl_perawatan' => $item->tgl_perawatan,
                        'jam_rawat'     => $item->jam_rawat,
                        'no_rkm_medis'  => $item->regPeriksa->no_rkm_medis ?? '-',
                        'nm_pasien'     => $item->regPeriksa->pasien->nm_pasien ?? '-',
                        'suhu_tubuh'    => $item->suhu_tubuh,
                        'tensi'         => $item->tensi,
                        'nadi'          => $item->nadi,
                        'respirasi'     => $item->respirasi,
                        'tinggi'        => $item->tinggi,
                        'berat'         => $item->berat,
                        'spo2'          => $item->spo2,
                        'gcs'           => $item->gcs,
                        'kesadaran'     => $item->kesadaran,
                        'keluhan'       => $item->keluhan,
                        'pemeriksaan'   => $item->pemeriksaan,
                        'alergi'        => $item->alergi,
                        'lingkar_perut' => $item->lingkar_perut,
                        'penilaian'     => $item->penilaian,
                        'rtl'           => $item->rtl,
                        'instruksi'     => $item->instruksi,
                        'evaluasi'      => $item->evaluasi,
                        'nip'           => $item->nip,
                        'nm_pegawai'    => $item->pegawai->nama ?? '-',
                        'jbtn_pegawai'  => $item->pegawai->jbtn ?? '-',
                    ]);
                @endphp
                <flux:table.row :key="$item->no_rawat . $item->tgl_perawatan . $item->jam_rawat">
                    <flux:table.cell class="whitespace-nowrap font-medium">{{ $item->tgl_perawatan }}</flux:table.cell>
                    <flux:table.cell class="whitespace-nowrap">{{ $item->jam_rawat }}</flux:table.cell>
                    <flux:table.cell class="whitespace-nowrap text-center">
                        @if($item->suhu_tubuh)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold {{ $item->suhu_tubuh > 37.5 ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' : 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' }}">{{ $item->suhu_tubuh }}</span>
                        @else <span class="text-neutral-300">—</span>
                        @endif
                    </flux:table.cell>
                    <flux:table.cell class="whitespace-nowrap text-center font-mono text-xs">{{ $item->tensi ?: '—' }}</flux:table.cell>
                    <flux:table.cell class="whitespace-nowrap text-center">{{ $item->nadi ?: '—' }}</flux:table.cell>
                    <flux:table.cell class="whitespace-nowrap text-center">{{ $item->respirasi ?: '—' }}</flux:table.cell>
                    <flux:table.cell class="whitespace-nowrap text-center">{{ $item->tinggi ?: '—' }}</flux:table.cell>
                    <flux:table.cell class="whitespace-nowrap text-center">{{ $item->berat ?: '—' }}</flux:table.cell>
                    <flux:table.cell class="whitespace-nowrap text-center">
                        @if($item->spo2)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold {{ $item->spo2 < 95 ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400' : 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400' }}">{{ $item->spo2 }}%</span>
                        @else <span class="text-neutral-300">—</span>
                        @endif
                    </flux:table.cell>
                    <flux:table.cell class="whitespace-nowrap text-center">
                        @if($item->gcs)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-violet-100 dark:bg-violet-900/30 text-violet-700 dark:text-violet-400 text-xs font-mono font-semibold">{{ $item->gcs }}</span>
                        @else <span class="text-neutral-300">—</span>
                        @endif
                    </flux:table.cell>
                    <flux:table.cell>
                        <p class="text-sm font-medium">{{ $item->pegawai->nama ?? '-' }}</p>
                        @if($item->pegawai?->jbtn)
                            <p class="text-xs text-neutral-400">{{ $item->pegawai->jbtn }}</p>
                        @endif
                    </flux:table.cell>
                    <flux:table.cell>
                        <div class="flex items-center gap-1.5">
                            <button
                                type="button"
                                @click="showDetailModal({{ $detailJson }})"
                                class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-medium bg-[#4C5C2D]/10 text-[#4C5C2D] hover:bg-[#4C5C2D]/20 transition-colors cursor-pointer border border-[#4C5C2D]/20">
                                <flux:icon name="eye" class="w-3.5 h-3.5" />
                                Detail
                            </button>
                            <button
                                type="button"
                                wire:click="editPemeriksaan({{ $detailJson }})"
                                class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-medium bg-amber-50 text-amber-600 hover:bg-amber-100 transition-colors cursor-pointer border border-amber-200">
                                <flux:icon name="pencil-square" class="w-3.5 h-3.5" />
                                Edit
                            </button>
                            <button
                                type="button"
                                @click="
                                    Swal.fire({
                                        title: 'Hapus Pemeriksaan?',
                                        text: 'Data yang dihapus tidak dapat dikembalikan!',
                                        icon: 'warning',
                                        showCancelButton: true,
                                        confirmButtonColor: '#4C5C2D',
                                        cancelButtonColor: '#d33',
                                        confirmButtonText: 'Ya, Hapus!',
                                        cancelButtonText: 'Batal'
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            $wire.deletePemeriksaan('{{ $item->tgl_perawatan }}', '{{ $item->jam_rawat }}');
                                        }
                                    });
                                "
                                class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-medium bg-red-50 text-red-600 hover:bg-red-100 transition-colors cursor-pointer border border-red-200">
                                <flux:icon name="trash" class="w-3.5 h-3.5" />
                                Hapus
                            </button>
                        </div>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="12" class="text-center py-12">
                        <flux:icon name="clipboard-document-check" class="w-10 h-10 mx-auto mb-3 text-neutral-200 dark:text-neutral-700" />
                        <p class="text-sm text-neutral-400">Belum ada data pemeriksaan untuk pasien ini.</p>
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>
</div>

{{-- ===== CREATE MODAL (Livewire + Flux) ===== --}}
<flux:modal wire:model="createModalOpen" class="w-full max-w-6xl" variant="flyout">
    <div class="bg-white dark:bg-neutral-900 flex flex-col items-stretch max-h-[95vh]">
        {{-- Header --}}
        <div class="px-6 py-4 border-b border-neutral-200 dark:border-neutral-700 flex items-center justify-between flex-shrink-0">
            <div class="flex items-center gap-3">
                <div class="p-2 rounded-lg bg-[#4C5C2D]/10 text-[#4C5C2D]">
                    <flux:icon name="document-plus" class="w-5 h-5" />
                </div>
                <h2 class="text-lg font-bold text-neutral-800 dark:text-neutral-100" x-text="$wire.isEditMode ? 'Edit Pemeriksaan Rawat Jalan' : 'Tambah Pemeriksaan Rawat Jalan'"></h2>
            </div>

        </div>

        {{-- Reference Alert --}}
        @if($lastPemeriksaan && !$isEditMode)
            <div class="px-6 py-3 bg-amber-50 dark:bg-amber-900/20 border-b border-amber-200 dark:border-amber-800/50 flex items-center gap-3 animate-in slide-in-from-top-1 duration-300">
                <flux:icon name="information-circle" class="w-5 h-5 text-amber-500 flex-shrink-0" />
                <p class="text-[11px] text-amber-800 dark:text-amber-300 leading-tight">
                    <span class="font-bold">Mode Patokan Aktif:</span> Menampilkan rujukan dari pemeriksaan terakhir tanggal <span class="font-bold">{{ $lastPemeriksaan->tgl_perawatan }}</span> jam <span class="font-bold">{{ $lastPemeriksaan->jam_rawat }}</span>. 
                    Input yang berwarna <span class="text-amber-600 dark:text-amber-400 font-bold italic">kuning gading</span> adalah data rujukan, silakan sesuaikan dengan kondisi terbaru.
                </p>
            </div>
        @endif

        {{-- Form Content --}}
        <div class="flex-1 overflow-y-auto p-6 flex flex-col gap-6">
            {{-- Header Row: Time & Date --}}
            <div class="grid grid-cols-2 gap-4">
                <flux:field>
                    <flux:input wire:model="tgl_perawatan" type="date" label="Tanggal Pemeriksaan" x-bind:disabled="$wire.isEditMode" />
                    @error('tgl_perawatan') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </flux:field>
                <flux:field>
                    <flux:input wire:model="jam_rawat" type="time" step="1" label="Jam Pemeriksaan" x-bind:disabled="$wire.isEditMode" />
                    @error('jam_rawat') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </flux:field>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-start">
                
                {{-- LEFT COLUMN: Staff + S & O + Vitals --}}
                <div class="flex flex-col gap-6">
                    {{-- Petugas Section --}}
                    <div class="bg-blue-50/30 dark:bg-blue-900/10 p-5 rounded-xl border border-blue-100 dark:border-blue-800/50">
                        <div class="grid grid-cols-1 gap-4">
                            {{-- Dilakukan (Staff Selection) --}}
                            <flux:field>
                                <flux:label class="text-blue-600 font-bold tracking-tight">Dilakukan :</flux:label>
                                <div class="relative">
                                    <flux:input wire:model.live.debounce.300ms="pegawaiSearch" icon="magnifying-glass" placeholder="Cari Nama / NIK Petugas..." />
                                    
                                    @if($nip)
                                        @php $selectedPegawai = \App\Models\Pegawai::find($nip); @endphp
                                        <div class="mt-2 flex items-center justify-between p-2.5 bg-white dark:bg-neutral-800 rounded-lg border border-emerald-500 shadow-sm animate-in fade-in duration-300">
                                            <div class="flex items-center gap-2">
                                                <div class="p-1 px-2 rounded-md bg-emerald-500 text-white text-[10px] font-bold">{{ $selectedPegawai->nik }}</div>
                                                <span class="text-sm font-bold text-neutral-800 dark:text-white">{{ $selectedPegawai->nama }}</span>
                                            </div>
                                            <button type="button" wire:click="$set('nip', null)" class="p-1 text-neutral-400 hover:text-red-500 transition-colors">
                                                <flux:icon name="x-mark" class="w-4 h-4" />
                                            </button>
                                        </div>
                                    @elseif(!empty($pegawaiList))
                                        <div class="absolute w-full mt-1 max-h-48 overflow-y-auto rounded-lg border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 shadow-lg z-50">
                                            @foreach($pegawaiList as $pg)
                                                <button type="button" wire:click="$set('nip', '{{ $pg->nik }}')" class="w-full flex flex-col text-left px-4 py-2 hover:bg-neutral-100 dark:hover:bg-neutral-700 transition-colors border-b border-neutral-50 dark:border-neutral-700/50 last:border-0">
                                                    <span class="text-sm font-semibold text-neutral-700 dark:text-neutral-200">{{ $pg->nama }}</span>
                                                    <span class="text-[10px] text-neutral-400">{{ $pg->jbtn }} | {{ $pg->nik }}</span>
                                                </button>
                                            @endforeach
                                        </div>
                                    @endif
                                    @error('nip') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                                </div>
                            </flux:field>

                            {{-- Profesi / Jabatan / Departemen (Auto-filled) --}}
                            <flux:field>
                                <flux:label class="text-neutral-500">Profesi / Jabatan / Departemen :</flux:label>
                                <flux:input wire:model="currentJabatan" read-only disabled class="bg-neutral-100/50 dark:bg-neutral-800/50" />
                            </flux:field>
                        </div>
                    </div>

                    {{-- SOAP: S & O --}}
                    <div class="flex flex-col gap-4">
                        <flux:textarea wire:model="keluhan" label="Subjek (Keluhan Utama) :" placeholder="Masukkan subjek/keluhan..." resize="none" rows="3" />
                        <flux:textarea wire:model="pemeriksaan" label="Objek (Pemeriksaan Fisik) :" placeholder="Masukkan objek/pemeriksaan..." resize="none" rows="3" />
                    </div>

                    {{-- Vital Signs Grid (Refined) --}}
                    <div class="bg-white dark:bg-neutral-800 p-5 rounded-xl border border-neutral-200 dark:border-neutral-700 shadow-sm mt-2">
                        <p class="text-[10px] font-bold uppercase tracking-wider text-neutral-400 mb-5 flex items-center gap-1.5"><flux:icon name="heart" class="w-3.5 h-3.5" /> Tanda-Tanda Vital</p>
                        
                        {{-- Numeric Vitals: 4 Column Grid --}}
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-x-4 gap-y-5">
                            {{-- Suhu --}}
                            <flux:field>
                                <div class="flex items-center justify-between mb-1.5">
                                    <flux:label class="!mb-0">Suhu (C)</flux:label>
                                    @if($lastPemeriksaan)<span class="text-[10px] text-amber-600 dark:text-amber-400 font-medium bg-amber-50 dark:bg-amber-900/20 px-1.5 rounded">Lalu: {{ $lastPemeriksaan->suhu_tubuh }}</span>@endif
                                </div>
                                <flux:input wire:model="suhu_tubuh" size="sm" placeholder="36,1" class="{{ $lastPemeriksaan && $suhu_tubuh == $lastPemeriksaan->suhu_tubuh ? 'bg-amber-50/50 dark:bg-amber-900/10' : '' }}" />
                            </flux:field>

                            {{-- Tensi --}}
                            <flux:field>
                                <div class="flex items-center justify-between mb-1.5">
                                    <flux:label class="!mb-0">Tensi (mmHg)</flux:label>
                                    @if($lastPemeriksaan)<span class="text-[10px] text-amber-600 dark:text-amber-400 font-medium bg-amber-50 dark:bg-amber-900/20 px-1.5 rounded">Lalu: {{ $lastPemeriksaan->tensi }}</span>@endif
                                </div>
                                <flux:input wire:model="tensi" size="sm" placeholder="120/80" class="{{ $lastPemeriksaan && $tensi == $lastPemeriksaan->tensi ? 'bg-amber-50/50 dark:bg-amber-900/10' : '' }}" />
                            </flux:field>

                            {{-- Berat --}}
                            <flux:field>
                                <div class="flex items-center justify-between mb-1.5">
                                    <flux:label class="!mb-0">Berat (Kg)</flux:label>
                                    @if($lastPemeriksaan)<span class="text-[10px] text-amber-600 dark:text-amber-400 font-medium bg-amber-50 dark:bg-amber-900/20 px-1.5 rounded">Lalu: {{ $lastPemeriksaan->berat }}</span>@endif
                                </div>
                                <flux:input wire:model="berat" size="sm" placeholder="65" class="{{ $lastPemeriksaan && $berat == $lastPemeriksaan->berat ? 'bg-amber-50/50 dark:bg-amber-900/10' : '' }}" />
                            </flux:field>

                            {{-- TB --}}
                            <flux:field>
                                <div class="flex items-center justify-between mb-1.5">
                                    <flux:label class="!mb-0">TB (Cm)</flux:label>
                                    @if($lastPemeriksaan)<span class="text-[10px] text-amber-600 dark:text-amber-400 font-medium bg-amber-50 dark:bg-amber-900/20 px-1.5 rounded">Lalu: {{ $lastPemeriksaan->tinggi }}</span>@endif
                                </div>
                                <flux:input wire:model="tinggi" size="sm" placeholder="170" class="{{ $lastPemeriksaan && $tinggi == $lastPemeriksaan->tinggi ? 'bg-amber-50/50 dark:bg-amber-900/10' : '' }}" />
                            </flux:field>

                            {{-- RR --}}
                            <flux:field>
                                <div class="flex items-center justify-between mb-1.5">
                                    <flux:label class="!mb-0">RR (/mnt)</flux:label>
                                    @if($lastPemeriksaan)<span class="text-[10px] text-amber-600 dark:text-amber-400 font-medium bg-amber-50 dark:bg-amber-900/20 px-1.5 rounded">Lalu: {{ $lastPemeriksaan->respirasi }}</span>@endif
                                </div>
                                <flux:input wire:model="respirasi" size="sm" placeholder="18" class="{{ $lastPemeriksaan && $respirasi == $lastPemeriksaan->respirasi ? 'bg-amber-50/50 dark:bg-amber-900/10' : '' }}" />
                            </flux:field>

                            {{-- Nadi --}}
                            <flux:field>
                                <div class="flex items-center justify-between mb-1.5">
                                    <flux:label class="!mb-0">Nadi (/mnt)</flux:label>
                                    @if($lastPemeriksaan)<span class="text-[10px] text-amber-600 dark:text-amber-400 font-medium bg-amber-50 dark:bg-amber-900/20 px-1.5 rounded">Lalu: {{ $lastPemeriksaan->nadi }}</span>@endif
                                </div>
                                <flux:input wire:model="nadi" size="sm" placeholder="75" class="{{ $lastPemeriksaan && $nadi == $lastPemeriksaan->nadi ? 'bg-amber-50/50 dark:bg-amber-900/10' : '' }}" />
                            </flux:field>

                            {{-- SpO2 --}}
                            <flux:field>
                                <div class="flex items-center justify-between mb-1.5">
                                    <flux:label class="!mb-0">SpO2 (%)</flux:label>
                                    @if($lastPemeriksaan)<span class="text-[10px] text-amber-600 dark:text-amber-400 font-medium bg-amber-50 dark:bg-amber-900/20 px-1.5 rounded">Lalu: {{ $lastPemeriksaan->spo2 }}%</span>@endif
                                </div>
                                <flux:input wire:model="spo2" size="sm" placeholder="98" class="{{ $lastPemeriksaan && $spo2 == $lastPemeriksaan->spo2 ? 'bg-amber-50/50 dark:bg-amber-900/10' : '' }}" />
                            </flux:field>

                            {{-- GCS --}}
                            <flux:field>
                                <div class="flex items-center justify-between mb-1.5">
                                    <flux:label class="!mb-0">GCS (E,V,M)</flux:label>
                                    @if($lastPemeriksaan)<span class="text-[10px] text-amber-600 dark:text-amber-400 font-medium bg-amber-50 dark:bg-amber-900/20 px-1.5 rounded">Lalu: {{ $lastPemeriksaan->gcs }}</span>@endif
                                </div>
                                <flux:input wire:model="gcs" size="sm" placeholder="15" class="{{ $lastPemeriksaan && $gcs == $lastPemeriksaan->gcs ? 'bg-amber-50/50 dark:bg-amber-900/10' : '' }}" />
                            </flux:field>
                        </div>

                        {{-- Final Row: Kesadaran (Wider) --}}
                        <div class="mt-6 pt-5 border-t border-neutral-100 dark:border-neutral-700/50">
                            <flux:field variant="horizontal">
                                <flux:label class="!mb-0 w-32">Kesadaran :</flux:label>
                                <div class="flex-1">
                                    <flux:select wire:model="kesadaran" placeholder="Pilih Tingkat Kesadaran..." class="w-full">
                                        <flux:select.option value="Compos Mentis">Compos Mentis</flux:select.option>
                                        <flux:select.option value="Somnolence">Somnolence</flux:select.option>
                                        <flux:select.option value="Sopor">Sopor</flux:select.option>
                                        <flux:select.option value="Coma">Coma</flux:select.option>
                                        <flux:select.option value="Apatis">Apatis</flux:select.option>
                                        <flux:select.option value="Delirium">Delirium</flux:select.option>
                                    </flux:select>
                                    @error('kesadaran') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                                </div>
                            </flux:field>
                        </div>
                    </div>
                </div>

                {{-- RIGHT COLUMN: Alergi + L.P. + A, P, Inst/Impl, Eval --}}
                <div class="flex flex-col gap-6">
                    {{-- Alergi & L.P. --}}
                    <div class="grid grid-cols-2 gap-4">
                        <flux:field>
                            <flux:label class="font-bold text-red-500">Alergi :</flux:label>
                            <flux:input wire:model="alergi" placeholder="Ada alergi makanan/obat?" class="border-red-200 focus:border-red-500" />
                        </flux:field>
                        
                        <flux:field>
                            <flux:label class="font-bold text-neutral-600">L.P. (Cm) :</flux:label>
                            <flux:input wire:model="lingkar_perut" placeholder="Misal: 80" />
                        </flux:field>
                    </div>

                    {{-- SOAP: A & P --}}
                    <div class="flex flex-col gap-5 mt-2">
                        <flux:field>
                            <flux:textarea wire:model="penilaian" label="Asesmen (Penilaian Klinis)" placeholder="Masukkan penilaian..." resize="none" rows="4" />
                            @error('penilaian') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </flux:field>
                        <flux:field>
                            <flux:textarea wire:model="rtl" label="Plan (Rencana Tindak Lanjut)" placeholder="Masukkan rencana..." resize="none" rows="4" />
                            @error('rtl') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </flux:field>
                    </div>

                    {{-- Inst/Impl & Evaluasi --}}
                    <div class="flex flex-col gap-5">
                        <flux:textarea wire:model="instruksi" label="Inst/Impl (Tindakan Dilakukan)" placeholder="Masukkan instruksi..." resize="none" rows="4" />
                        <flux:textarea wire:model="evaluasi" label="Evaluasi (Respon Pasien)" placeholder="Masukkan evaluasi..." resize="none" rows="4" />
                    </div>
                </div>
            </div>
        </div>

        {{-- Footer Actions --}}
        <div class="px-6 py-4 border-t border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 flex items-center justify-end gap-3 flex-shrink-0">
            <flux:modal.close>
                <flux:button variant="ghost">Batal</flux:button>
            </flux:modal.close>
            <flux:button wire:click="save" variant="primary" class="px-8" wire:loading.attr="disabled">
                <div class="flex items-center justify-center gap-2">
                    <flux:icon name="check" class="w-4 h-4" wire:loading.remove wire:target="save" />
                    <flux:icon name="arrow-path" class="w-4 h-4 animate-spin" wire:loading wire:target="save" />
                    <span x-text="$wire.isEditMode ? 'Update Perubahan' : 'Simpan Pemeriksaan'"></span>
                </div>
            </flux:button>
        </div>
    </div>
</flux:modal>

{{-- ===== DETAIL MODAL (Alpine.js - pure client-side) ===== --}}
{{-- Positioned via x-teleport to body to completely escape parent CSS stacking contexts --}}
<template x-teleport="body">
    <div
        x-show="detailModalOpen"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-[9999] flex items-center justify-center p-4"
        style="display: none;">

        {{-- Backdrop --}}
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="closeDetailModal()"></div>

        {{-- Panel --}}
        <div
            x-show="detailModalOpen"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95 translate-y-2"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-95 translate-y-2"
            class="relative w-[95%] max-w-6xl max-h-[90vh] flex flex-col bg-white dark:bg-neutral-900 rounded-2xl shadow-2xl border border-neutral-200 dark:border-neutral-700 overflow-hidden"
            @click.stop>

            {{-- Header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-neutral-200 dark:border-neutral-700 bg-gradient-to-r from-[#4C5C2D]/10 to-white dark:from-[#4C5C2D]/20 dark:to-neutral-900 flex-shrink-0">
                <div class="flex items-center gap-3">
                    <div class="p-2.5 rounded-xl bg-[#4C5C2D]/10 dark:bg-[#4C5C2D]/30">
                        <flux:icon name="clipboard-document-check" class="w-5 h-5 text-[#4C5C2D]" />
                    </div>
                    <div>
                        <h2 class="font-bold text-neutral-800 dark:text-neutral-100 text-base">Detail Pemeriksaan</h2>
                        <p class="text-xs text-neutral-500">
                            <span x-text="detail.tgl_perawatan"></span> &bull;
                            <span x-text="detail.jam_rawat"></span> &bull;
                            <span class="font-mono" x-text="detail.no_rawat"></span>
                        </p>
                    </div>
                </div>
                <button @click="closeDetailModal()" class="p-1.5 rounded-lg hover:bg-white/60 dark:hover:bg-neutral-800 transition-colors cursor-pointer text-neutral-400 hover:text-neutral-600">
                    <flux:icon name="x-mark" class="w-5 h-5" />
                </button>
            </div>

            {{-- Scrollable Content --}}
            <div class="overflow-y-auto flex-1 p-6 flex flex-col gap-5 bg-neutral-50/50 dark:bg-neutral-900/30">

                {{-- Identitas (Top Full Width) --}}
                <section class="bg-white dark:bg-neutral-800/80 p-5 rounded-2xl border border-neutral-200 dark:border-neutral-700 shadow-sm">
                    <p class="text-[11px] font-bold uppercase tracking-widest text-[#6A7E3F] mb-4 flex items-center gap-1.5"><flux:icon name="identification" class="w-3.5 h-3.5" /> Identitas Pasien</p>
                    <div class="grid grid-cols-2 lg:grid-cols-5 gap-3">
                        <div class="bg-neutral-50 dark:bg-neutral-800 rounded-xl p-3 border border-neutral-100 dark:border-neutral-700/50">
                            <p class="text-[10px] text-neutral-400 font-medium uppercase tracking-wide mb-1">Tanggal Rawat</p>
                            <p class="text-sm font-semibold text-neutral-700 dark:text-neutral-200" x-text="detail.tgl_perawatan"></p>
                        </div>
                        <div class="bg-neutral-50 dark:bg-neutral-800 rounded-xl p-3 border border-neutral-100 dark:border-neutral-700/50">
                            <p class="text-[10px] text-neutral-400 font-medium uppercase tracking-wide mb-1">Jam</p>
                            <p class="text-sm font-semibold text-neutral-700 dark:text-neutral-200" x-text="detail.jam_rawat"></p>
                        </div>
                        <div class="bg-neutral-50 dark:bg-neutral-800 rounded-xl p-3 border border-neutral-100 dark:border-neutral-700/50">
                            <p class="text-[10px] text-neutral-400 font-medium uppercase tracking-wide mb-1">No. Rawat</p>
                            <p class="text-sm font-semibold text-neutral-700 dark:text-neutral-200 font-mono text-xs" x-text="detail.no_rawat"></p>
                        </div>
                        <div class="bg-neutral-50 dark:bg-neutral-800 rounded-xl p-3 border border-neutral-100 dark:border-neutral-700/50">
                            <p class="text-[10px] text-neutral-400 font-medium uppercase tracking-wide mb-1">No. RM</p>
                            <p class="text-sm font-semibold text-neutral-700 dark:text-neutral-200 font-mono" x-text="detail.no_rkm_medis"></p>
                        </div>
                        <div class="bg-[#4C5C2D]/10 dark:bg-[#4C5C2D]/20 rounded-xl p-3 col-span-2 lg:col-span-1 border border-[#4C5C2D]/20 dark:border-[#4C5C2D]/50">
                            <p class="text-[10px] text-[#4C5C2D] font-medium uppercase tracking-wide mb-1">Nama Pasien</p>
                            <p class="text-base font-bold text-[#4C5C2D] dark:text-[#8CC7C4] truncate" x-text="detail.nm_pasien" :title="detail.nm_pasien"></p>
                        </div>
                    </div>
                </section>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">
                    
                    {{-- Tanda Vital (Left Column, col-span-3 or 4) --}}
                    <div class="lg:col-span-4 flex flex-col">
                        <section class="bg-white dark:bg-neutral-800/80 p-5 rounded-2xl border border-neutral-200 dark:border-neutral-700 shadow-sm flex-1 flex flex-col">
                            <p class="text-[11px] font-bold uppercase tracking-widest text-[#6A7E3F] mb-4 flex items-center gap-1.5"><flux:icon name="heart" class="w-3.5 h-3.5" /> Tanda-Tanda Vital</p>
                            
                            <div class="grid grid-cols-2 gap-3 flex-1">
                                <div class="bg-neutral-50 dark:bg-neutral-800 rounded-xl p-3 text-center border border-neutral-100 dark:border-neutral-700/50 flex flex-col justify-center">
                                    <p class="text-[10px] text-neutral-400 font-medium uppercase tracking-wide mb-1.5 leading-tight">Suhu (°C)</p>
                                    <p class="text-xl font-bold font-mono text-neutral-700 dark:text-neutral-200" x-text="detail.suhu_tubuh || '—'"></p>
                                </div>
                                <div class="bg-neutral-50 dark:bg-neutral-800 rounded-xl p-3 text-center border border-neutral-100 dark:border-neutral-700/50 flex flex-col justify-center">
                                    <p class="text-[10px] text-neutral-400 font-medium uppercase tracking-wide mb-1.5 leading-tight">Tensi</p>
                                    <p class="text-xl font-bold font-mono text-neutral-700 dark:text-neutral-200" x-text="detail.tensi || '—'"></p>
                                </div>
                                <div class="bg-neutral-50 dark:bg-neutral-800 rounded-xl p-3 text-center border border-neutral-100 dark:border-neutral-700/50 flex flex-col justify-center">
                                    <p class="text-[10px] text-neutral-400 font-medium uppercase tracking-wide mb-1.5 leading-tight">Nadi (/mnt)</p>
                                    <p class="text-xl font-bold font-mono text-neutral-700 dark:text-neutral-200" x-text="detail.nadi || '—'"></p>
                                </div>
                                <div class="bg-neutral-50 dark:bg-neutral-800 rounded-xl p-3 text-center border border-neutral-100 dark:border-neutral-700/50 flex flex-col justify-center">
                                    <p class="text-[10px] text-neutral-400 font-medium uppercase tracking-wide mb-1.5 leading-tight">Respirasi</p>
                                    <p class="text-xl font-bold font-mono text-neutral-700 dark:text-neutral-200" x-text="detail.respirasi || '—'"></p>
                                </div>
                                <div class="bg-neutral-50 dark:bg-neutral-800 rounded-xl p-3 text-center border border-neutral-100 dark:border-neutral-700/50 flex flex-col justify-center">
                                    <p class="text-[10px] text-neutral-400 font-medium uppercase tracking-wide mb-1.5 leading-tight">Tinggi (cm)</p>
                                    <p class="text-lg font-bold font-mono text-neutral-700 dark:text-neutral-200" x-text="detail.tinggi || '—'"></p>
                                </div>
                                <div class="bg-neutral-50 dark:bg-neutral-800 rounded-xl p-3 text-center border border-neutral-100 dark:border-neutral-700/50 flex flex-col justify-center">
                                    <p class="text-[10px] text-neutral-400 font-medium uppercase tracking-wide mb-1.5 leading-tight">Berat (kg)</p>
                                    <p class="text-lg font-bold font-mono text-neutral-700 dark:text-neutral-200" x-text="detail.berat || '—'"></p>
                                </div>
                                <div class="bg-neutral-50 dark:bg-neutral-800 rounded-xl p-3 text-center border border-neutral-100 dark:border-neutral-700/50 flex flex-col justify-center">
                                    <p class="text-[10px] text-neutral-400 font-medium uppercase tracking-wide mb-1.5 leading-tight">SpO2 (%)</p>
                                    <p class="text-lg font-bold font-mono text-neutral-700 dark:text-neutral-200" x-text="detail.spo2 ? detail.spo2 + '%' : '—'"></p>
                                </div>
                                <div class="bg-neutral-50 dark:bg-neutral-800 rounded-xl p-3 text-center border border-neutral-100 dark:border-neutral-700/50 flex flex-col justify-center">
                                    <p class="text-[10px] text-neutral-400 font-medium uppercase tracking-wide mb-1.5 leading-tight">GCS</p>
                                    <p class="text-lg font-bold font-mono text-neutral-700 dark:text-neutral-200" x-text="detail.gcs || '—'"></p>
                                </div>
                            </div>
                            
                            <div class="mt-3 flex flex-col gap-2">
                                <template x-if="detail.kesadaran">
                                    <div class="flex items-center gap-2 bg-sky-50 dark:bg-sky-900/20 border border-sky-200 dark:border-sky-800/50 rounded-lg px-3 py-2">
                                        <flux:icon name="eye" class="w-4 h-4 text-sky-500 flex-shrink-0" />
                                        <p class="text-sm text-sky-700 dark:text-sky-300"><span class="font-semibold">Kesadaran:</span> <span x-text="detail.kesadaran"></span></p>
                                    </div>
                                </template>
                                <template x-if="detail.alergi">
                                    <div class="flex items-center gap-2 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800/50 rounded-lg px-3 py-2">
                                        <flux:icon name="exclamation-triangle" class="w-4 h-4 text-red-500 flex-shrink-0" />
                                        <p class="text-sm text-red-700 dark:text-red-300"><span class="font-semibold">Alergi:</span> <span x-text="detail.alergi"></span></p>
                                    </div>
                                </template>
                                <template x-if="detail.lingkar_perut && detail.lingkar_perut !== '-'">
                                    <div class="flex items-center gap-2 bg-neutral-100 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700/50 rounded-lg px-3 py-2">
                                        <flux:icon name="arrows-right-left" class="w-4 h-4 text-neutral-500 flex-shrink-0" />
                                        <p class="text-sm text-neutral-700 dark:text-neutral-300"><span class="font-semibold">Lingkar Perut:</span> <span x-text="detail.lingkar_perut"></span> cm</p>
                                    </div>
                                </template>
                            </div>
                        </section>
                    </div>

                        {{-- SOAP & Evaluasi (Right Column, col-span-8) --}}
                        <div class="lg:col-span-8 flex flex-col gap-5">
                            
                            {{-- SOAP --}}
                            <section class="bg-white dark:bg-neutral-800/80 p-5 rounded-2xl border border-neutral-200 dark:border-neutral-700 shadow-sm flex-1">
                                <p class="text-[11px] font-bold uppercase tracking-widest text-[#6A7E3F] mb-4 flex items-center gap-1.5"><flux:icon name="document-text" class="w-3.5 h-3.5" /> Catatan Pemeriksaan (SOAPIE)</p>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    {{-- S --}}
                                    <div class="border border-neutral-100 dark:border-neutral-700/70 rounded-xl overflow-hidden shadow-sm">
                                        <div class="px-4 py-2.5 bg-neutral-50/80 dark:bg-neutral-800 border-b border-neutral-100 dark:border-neutral-700/70 flex items-center gap-2">
                                            <span class="w-5 h-5 rounded-md bg-white dark:bg-neutral-700 shadow-sm text-neutral-700 dark:text-neutral-300 text-xs font-bold flex items-center justify-center flex-shrink-0 border border-neutral-200 dark:border-neutral-600">S</span>
                                            <p class="text-xs font-semibold text-neutral-600 dark:text-neutral-400">Subjek (Keluhan)</p>
                                        </div>
                                        <div class="px-4 py-3 min-h-[70px] bg-white dark:bg-neutral-800/40">
                                            <p class="text-sm text-neutral-700 dark:text-neutral-300 whitespace-pre-wrap leading-relaxed" x-text="detail.keluhan || '-'"></p>
                                        </div>
                                    </div>

                                    {{-- O --}}
                                    <div class="border border-neutral-100 dark:border-neutral-700/70 rounded-xl overflow-hidden shadow-sm">
                                        <div class="px-4 py-2.5 bg-neutral-50/80 dark:bg-neutral-800 border-b border-neutral-100 dark:border-neutral-700/70 flex items-center gap-2">
                                            <span class="w-5 h-5 rounded-md bg-white dark:bg-neutral-700 shadow-sm text-neutral-700 dark:text-neutral-300 text-xs font-bold flex items-center justify-center flex-shrink-0 border border-neutral-200 dark:border-neutral-600">O</span>
                                            <p class="text-xs font-semibold text-neutral-600 dark:text-neutral-400">Objek (Pemeriksaan)</p>
                                        </div>
                                        <div class="px-4 py-3 min-h-[70px] bg-white dark:bg-neutral-800/40">
                                            <p class="text-sm text-neutral-700 dark:text-neutral-300 whitespace-pre-wrap leading-relaxed" x-text="detail.pemeriksaan || '-'"></p>
                                        </div>
                                    </div>

                                    {{-- A --}}
                                    <div class="border border-neutral-100 dark:border-neutral-700/70 rounded-xl overflow-hidden shadow-sm">
                                        <div class="px-4 py-2.5 bg-neutral-50/80 dark:bg-neutral-800 border-b border-neutral-100 dark:border-neutral-700/70 flex items-center gap-2">
                                            <span class="w-5 h-5 rounded-md bg-white dark:bg-neutral-700 shadow-sm text-neutral-700 dark:text-neutral-300 text-xs font-bold flex items-center justify-center flex-shrink-0 border border-neutral-200 dark:border-neutral-600">A</span>
                                            <p class="text-xs font-semibold text-neutral-600 dark:text-neutral-400">Asesment (Penilaian)</p>
                                        </div>
                                        <div class="px-4 py-3 min-h-[70px] bg-white dark:bg-neutral-800/40">
                                            <p class="text-sm text-neutral-700 dark:text-neutral-300 whitespace-pre-wrap leading-relaxed" x-text="detail.penilaian || '-'"></p>
                                        </div>
                                    </div>

                                    {{-- P --}}
                                    <div class="border border-neutral-100 dark:border-neutral-700/70 rounded-xl overflow-hidden shadow-sm">
                                        <div class="px-4 py-2.5 bg-neutral-50/80 dark:bg-neutral-800 border-b border-neutral-100 dark:border-neutral-700/70 flex items-center gap-2">
                                            <span class="w-5 h-5 rounded-md bg-white dark:bg-neutral-700 shadow-sm text-neutral-700 dark:text-neutral-300 text-xs font-bold flex items-center justify-center flex-shrink-0 border border-neutral-200 dark:border-neutral-600">P</span>
                                            <p class="text-xs font-semibold text-neutral-600 dark:text-neutral-400">Plan (RTL)</p>
                                        </div>
                                        <div class="px-4 py-3 min-h-[70px] bg-white dark:bg-neutral-800/40">
                                            <p class="text-sm text-neutral-700 dark:text-neutral-300 whitespace-pre-wrap leading-relaxed" x-text="detail.rtl || '-'"></p>
                                        </div>
                                    </div>

                                    {{-- I --}}
                                    <div class="border border-neutral-100 dark:border-neutral-700/70 rounded-xl overflow-hidden shadow-sm">
                                        <div class="px-4 py-2.5 bg-neutral-50/80 dark:bg-neutral-800 border-b border-neutral-100 dark:border-neutral-700/70 flex items-center gap-2">
                                            <span class="w-5 h-5 rounded-md bg-white dark:bg-neutral-700 shadow-sm text-neutral-700 dark:text-neutral-300 text-xs font-bold flex items-center justify-center flex-shrink-0 border border-neutral-200 dark:border-neutral-600">I</span>
                                            <p class="text-xs font-semibold text-neutral-600 dark:text-neutral-400">Inst / Impl (Instruksi)</p>
                                        </div>
                                        <div class="px-4 py-3 min-h-[70px] bg-white dark:bg-neutral-800/40">
                                            <p class="text-sm text-neutral-700 dark:text-neutral-300 whitespace-pre-wrap leading-relaxed" x-text="detail.instruksi || '-'"></p>
                                        </div>
                                    </div>

                                    {{-- E --}}
                                    <div class="border border-neutral-100 dark:border-neutral-700/70 rounded-xl overflow-hidden shadow-sm">
                                        <div class="px-4 py-2.5 bg-neutral-50/80 dark:bg-neutral-800 border-b border-neutral-100 dark:border-neutral-700/70 flex items-center gap-2">
                                            <span class="w-5 h-5 rounded-md bg-white dark:bg-neutral-700 shadow-sm text-neutral-700 dark:text-neutral-300 text-xs font-bold flex items-center justify-center flex-shrink-0 border border-neutral-200 dark:border-neutral-600">E</span>
                                            <p class="text-xs font-semibold text-neutral-600 dark:text-neutral-400">Evaluasi</p>
                                        </div>
                                        <div class="px-4 py-3 min-h-[70px] bg-white dark:bg-neutral-800/40">
                                            <p class="text-sm text-neutral-700 dark:text-neutral-300 whitespace-pre-wrap leading-relaxed" x-text="detail.evaluasi || '-'"></p>
                                        </div>
                                    </div>
                                </div>
                            </section>
                        </div>
                </div>

                {{-- Petugas (Bottom Full Width) --}}
                <section class="bg-white dark:bg-neutral-800/80 p-5 rounded-2xl border border-neutral-200 dark:border-neutral-700 shadow-sm">
                    <p class="text-[11px] font-bold uppercase tracking-widest text-[#6A7E3F] mb-4 flex items-center gap-1.5"><flux:icon name="user-circle" class="w-3.5 h-3.5" /> Dokter / Paramedis</p>
                    <div class="flex items-center gap-4 bg-neutral-50/50 dark:bg-neutral-800/50 rounded-xl p-3 border border-neutral-100 dark:border-neutral-700/50">
                        <div class="w-12 h-12 rounded-xl bg-[#4C5C2D]/10 dark:bg-[#4C5C2D]/30 flex items-center justify-center flex-shrink-0">
                            <flux:icon name="user" class="w-6 h-6 text-[#4C5C2D]" />
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-bold text-neutral-800 dark:text-neutral-100 text-sm" x-text="detail.nm_pegawai"></p>
                            <p class="text-xs text-neutral-500 mt-0.5" x-text="detail.jbtn_pegawai"></p>
                        </div>
                        <div class="text-right flex-shrink-0 px-2">
                            <p class="text-[10px] text-neutral-400 uppercase font-medium">NIP / NIK</p>
                            <p class="text-sm font-mono font-medium text-neutral-700 dark:text-neutral-300" x-text="detail.nip"></p>
                        </div>
                    </div>
                </section>

            </div>

            {{-- Footer --}}
            <div class="px-6 py-4 border-t border-neutral-200 dark:border-neutral-700 bg-neutral-50/80 dark:bg-neutral-800/60 flex items-center justify-between flex-shrink-0">
                <div class="flex items-center gap-2">
                    <button 
                        @click="closeDetailModal(); $wire.editPemeriksaan(detail)"
                        class="inline-flex items-center gap-2 px-5 py-2 rounded-lg text-sm font-medium bg-amber-50 text-amber-700 hover:bg-amber-100 transition-colors cursor-pointer border border-amber-200 shadow-sm">
                        <flux:icon name="pencil-square" class="w-4 h-4" />
                        Edit Data Ini
                    </button>
                    <button 
                        @click="
                            closeDetailModal(); 
                            Swal.fire({
                                title: 'Hapus Pemeriksaan?',
                                text: 'Data yang dihapus tidak dapat dikembalikan!',
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonColor: '#4C5C2D',
                                cancelButtonColor: '#d33',
                                confirmButtonText: 'Ya, Hapus!',
                                cancelButtonText: 'Batal'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    $wire.deletePemeriksaan(detail.tgl_perawatan, detail.jam_rawat);
                                }
                            });
                        "
                        class="inline-flex items-center gap-2 px-5 py-2 rounded-lg text-sm font-medium bg-red-50 text-red-700 hover:bg-red-100 transition-colors cursor-pointer border border-red-200 shadow-sm">
                        <flux:icon name="trash" class="w-4 h-4" />
                        Hapus Data
                    </button>
                </div>

                <div class="flex items-center gap-2">
                    <button @click="closeDetailModal()" class="inline-flex items-center gap-2 px-5 py-2 rounded-lg text-sm font-medium bg-white dark:bg-neutral-700 text-neutral-700 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-600 transition-colors cursor-pointer border border-neutral-200 dark:border-neutral-600 shadow-sm">
                        <flux:icon name="x-mark" class="w-4 h-4" />
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
