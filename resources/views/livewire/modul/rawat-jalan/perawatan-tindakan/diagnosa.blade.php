<div class="animate-in fade-in duration-300" x-data="{ 
    diagnosaModalOpen: @entangle('diagnosaModalOpen'),
    prosedurModalOpen: @entangle('prosedurModalOpen')
}">
    {{-- Header & Segmented Controls --}}
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-2">
            <div class="inline-flex bg-neutral-100 dark:bg-neutral-800 p-1 rounded-xl">
                <button wire:click="$set('diagnosaSubTab', 'diagnosa')"
                    class="px-4 py-1.5 rounded-lg text-sm font-bold transition-all {{ $diagnosaSubTab === 'diagnosa' ? 'bg-white dark:bg-neutral-700 text-[#4C5C2D] dark:text-[#8CC7C4] shadow-sm' : 'text-neutral-500 hover:text-neutral-700' }}">
                    <div class="flex items-center gap-2">
                        <flux:icon name="tag" class="w-4 h-4" />
                        Diagnosa (ICD-10)
                    </div>
                </button>
                <button wire:click="$set('diagnosaSubTab', 'prosedur')"
                    class="px-4 py-1.5 rounded-lg text-sm font-bold transition-all {{ $diagnosaSubTab === 'prosedur' ? 'bg-white dark:bg-neutral-700 text-blue-600 dark:text-blue-400 shadow-sm' : 'text-neutral-500 hover:text-neutral-700' }}">
                    <div class="flex items-center gap-2">
                        <flux:icon name="scissors" class="w-4 h-4" />
                        Prosedur (ICD-9)
                    </div>
                </button>
            </div>
        </div>
        <div class="flex items-center gap-3">
            @if($diagnosaSubTab === 'diagnosa')
                <span class="text-xs text-neutral-400 bg-neutral-100 dark:bg-neutral-700 px-2 py-1 rounded-full">{{ count($this->listDiagnosa) }} data</span>
                <flux:button wire:click="openDiagnosaModal" icon="plus" size="sm" variant="primary">
                    Tambah Diagnosa
                </flux:button>
            @else
                <span class="text-xs text-neutral-400 bg-neutral-100 dark:bg-neutral-700 px-2 py-1 rounded-full">{{ count($this->listProsedur) }} data</span>
                <flux:button wire:click="openProsedurModal" icon="plus" size="sm" variant="primary" class="bg-blue-600 hover:bg-blue-700 border-blue-600 text-white">
                    Tambah Prosedur
                </flux:button>
            @endif
        </div>
    </div>

    {{-- DYNAMIC TAB CONTENT --}}
    @if($diagnosaSubTab === 'diagnosa')
        {{-- TAB: DIAGNOSA --}}
        <div class="animate-in fade-in slide-in-from-bottom-2 duration-300">
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>{{ __('Tgl & No.Rawat') }}</flux:table.column>
                    <flux:table.column>{{ __('Kode') }}</flux:table.column>
                    <flux:table.column>{{ __('Nama Penyakit') }}</flux:table.column>
                    <flux:table.column><div class="w-full text-center">{{ __('Kasus') }}</div></flux:table.column>
                    <flux:table.column><div class="w-full text-center">{{ __('Urut') }}</div></flux:table.column>
                    <flux:table.column><div class="w-full text-center">{{ __('Aksi') }}</div></flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @forelse ($this->listDiagnosa as $item)
                        <flux:table.row :key="$item['no_rawat'] . $item['kd_penyakit']">
                            <flux:table.cell class="whitespace-nowrap">
                                <span class="text-xs font-mono font-bold text-neutral-800 dark:text-neutral-100 block">{{ $item['no_rawat'] }}</span>
                            </flux:table.cell>
                            <flux:table.cell>
                                <span class="text-xs font-bold font-mono text-[#4C5C2D] bg-[#4C5C2D]/10 px-2 py-0.5 rounded">{{ $item['kd_penyakit'] }}</span>
                            </flux:table.cell>
                            <flux:table.cell>
                                <span class="text-xs font-bold text-neutral-800 dark:text-neutral-100">{{ $item['nm_penyakit'] }}</span>
                            </flux:table.cell>
                            <flux:table.cell class="text-center">
                                <span class="text-[10px] font-bold uppercase {{ $item['kasus'] === 'Baru' ? 'text-amber-600 bg-amber-50' : 'text-indigo-600 bg-indigo-50' }} px-2 py-0.5 rounded-full">
                                    {{ $item['kasus'] }}
                                </span>
                            </flux:table.cell>
                            <flux:table.cell class="text-center">
                                <span class="text-xs font-mono font-bold">{{ $item['prioritas'] }}</span>
                            </flux:table.cell>
                            <flux:table.cell class="text-center">
                                <div class="flex items-center justify-center">
                                    <button type="button" @click="
                                        Swal.fire({
                                            title: 'Hapus Diagnosa?',
                                            text: 'Data yang dihapus tidak dapat dikembalikan!',
                                            icon: 'warning',
                                            showCancelButton: true,
                                            confirmButtonColor: '#4C5C2D',
                                            cancelButtonColor: '#d33',
                                            confirmButtonText: 'Ya, Hapus!',
                                            cancelButtonText: 'Batal'
                                        }).then((result) => {
                                            if (result.isConfirmed) {
                                                $wire.deleteDiagnosa('{{ $item['kd_penyakit'] }}');
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
                            <flux:table.cell colspan="6" class="text-center py-12">
                                <flux:icon name="tag" class="w-10 h-10 mx-auto mb-3 text-neutral-200 dark:text-neutral-700" />
                                <p class="text-xs text-neutral-400 uppercase tracking-widest font-medium italic">Belum ada diagnosa tercatat</p>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </div>
    @else
        {{-- TAB: PROSEDUR --}}
        <div class="animate-in fade-in slide-in-from-bottom-2 duration-300">
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>{{ __('Tgl & No.Rawat') }}</flux:table.column>
                    <flux:table.column>{{ __('Kode') }}</flux:table.column>
                    <flux:table.column>{{ __('Nama Prosedur') }}</flux:table.column>
                    <flux:table.column><div class="w-full text-center">{{ __('Urut') }}</div></flux:table.column>
                    <flux:table.column><div class="w-full text-center">{{ __('Jml') }}</div></flux:table.column>
                    <flux:table.column><div class="w-full text-center">{{ __('Aksi') }}</div></flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @forelse ($this->listProsedur as $item)
                        <flux:table.row :key="$item['no_rawat'] . $item['kode']">
                            <flux:table.cell class="whitespace-nowrap">
                                <span class="text-xs font-mono font-bold text-neutral-800 dark:text-neutral-100 block">{{ $item['no_rawat'] }}</span>
                            </flux:table.cell>
                            <flux:table.cell>
                                <span class="text-xs font-bold font-mono text-blue-600 bg-blue-50 px-2 py-0.5 rounded">{{ $item['kode'] }}</span>
                            </flux:table.cell>
                            <flux:table.cell>
                                <span class="text-xs font-bold text-neutral-800 dark:text-neutral-100">{{ $item['nm_prosedur'] }}</span>
                            </flux:table.cell>
                            <flux:table.cell class="text-center">
                                <span class="text-xs font-mono font-bold">{{ $item['prioritas'] }}</span>
                            </flux:table.cell>
                            <flux:table.cell class="text-center">
                                <span class="text-xs font-mono font-bold">{{ $item['jumlah'] }}</span>
                            </flux:table.cell>
                            <flux:table.cell class="text-center">
                                <div class="flex items-center justify-center">
                                    <button type="button" @click="
                                        Swal.fire({
                                            title: 'Hapus Prosedur?',
                                            text: 'Data yang dihapus tidak dapat dikembalikan!',
                                            icon: 'warning',
                                            showCancelButton: true,
                                            confirmButtonColor: '#4C5C2D',
                                            cancelButtonColor: '#d33',
                                            confirmButtonText: 'Ya, Hapus!',
                                            cancelButtonText: 'Batal'
                                        }).then((result) => {
                                            if (result.isConfirmed) {
                                                $wire.deleteProsedur('{{ $item['kode'] }}');
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
                            <flux:table.cell colspan="6" class="text-center py-12">
                                <flux:icon name="scissors" class="w-10 h-10 mx-auto mb-3 text-neutral-200 dark:text-neutral-700" />
                                <p class="text-xs text-neutral-400 uppercase tracking-widest font-medium italic">Belum ada prosedur tercatat</p>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </div>
    @endif

    {{-- ========================================================= --}}
    {{-- MODAL DIAGNOSA (ICD-10) - ALPINE VERSION (SOP COMPLIANT) --}}
    {{-- ========================================================= --}}
    <div x-show="diagnosaModalOpen" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-6 overflow-hidden" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="diagnosaModalOpen = false"></div>
        <div class="relative w-full max-w-5xl max-h-[90vh] bg-white dark:bg-neutral-900 rounded-2xl shadow-2xl border border-neutral-200 dark:border-neutral-700 flex flex-col overflow-hidden animate-in zoom-in-95 duration-200">
            <div class="p-6 border-b border-neutral-100 dark:border-neutral-800 bg-neutral-50/50 dark:bg-neutral-800/50 flex-shrink-0">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-lg font-bold text-neutral-800 dark:text-neutral-100 flex items-center gap-2 uppercase tracking-wide">
                        <flux:icon name="tag" class="w-5 h-5 text-[#4C5C2D]" />
                        Input Diagnosa Pasien (ICD-10)
                    </h2>
                    <button @click="diagnosaModalOpen = false" class="text-neutral-400 hover:text-neutral-600 transition-colors">
                        <flux:icon name="x-mark" class="w-6 h-6" />
                    </button>
                </div>
                
                <div class="flex items-center gap-4 mb-4">
                    <div class="flex-1">
                        <flux:label class="text-[10px] font-bold uppercase text-neutral-400 mb-2 block tracking-widest">Cari Penyakit :</flux:label>
                        <flux:input wire:model.live.debounce.300ms="diagnosaSearch" placeholder="Ketik nama penyakit atau kode ICD-10..." icon="magnifying-glass" clearable />
                    </div>
                    <div class="w-48">
                        <flux:label class="text-[10px] font-bold uppercase text-neutral-400 mb-2 block tracking-widest">Kasus Default :</flux:label>
                        <flux:select wire:model.live="status_penyakit">
                            <flux:select.option value="Baru">Kasus Baru</flux:select.option>
                            <flux:select.option value="Lama">Kasus Lama</flux:select.option>
                        </flux:select>
                    </div>
                </div>

                <div class="bg-[#F1F5E9] border border-[#4C5C2D]/20 rounded-xl p-4 flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="flex flex-col">
                            <span class="text-[10px] font-bold text-neutral-400 uppercase tracking-widest">Item Dipilih</span>
                            <span class="text-sm font-bold text-[#4C5C2D]">{{ count($selectedDiagnosa) }} Penyakit</span>
                        </div>
                        @if(count($selectedDiagnosa) > 0)
                            <div class="h-8 w-px bg-neutral-200"></div>
                            <div class="flex flex-wrap gap-2 max-w-md overflow-hidden">
                                @foreach($selectedDiagnosa as $sel)
                                    <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded bg-white border border-[#4C5C2D]/20 text-[10px] font-bold text-neutral-700">
                                        <span class="text-[#4C5C2D]">{{ $sel['kd_penyakit'] }}</span>
                                        <span class="text-neutral-400 font-mono">#{{ $sel['prioritas'] }}</span>
                                        <button wire:click="toggleDiagnosa('{{ $sel['kd_penyakit'] }}', '')" class="hover:text-red-500">
                                            <flux:icon name="x-mark" class="w-3 h-3" />
                                        </button>
                                    </span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                    <flux:button wire:click="saveDiagnosa" variant="primary" class="px-8" :disabled="empty($selectedDiagnosa)">
                        Simpan Semua ({{ count($selectedDiagnosa) }})
                    </flux:button>
                </div>
            </div>

            <div class="flex-1 overflow-y-auto p-0 relative">
                <table class="w-full text-left border-collapse">
                    <thead class="sticky top-0 z-10 bg-neutral-100 dark:bg-neutral-900 border-b border-neutral-200">
                        <tr>
                            <th colspan="4" class="p-2 pl-6 bg-neutral-50 text-[9px] font-bold text-neutral-400 uppercase tracking-widest border-b border-neutral-100">
                                {{ strlen($diagnosaSearch) < 2 ? 'Sering Digunakan / Daftar Teratas' : 'Hasil Pencarian : ' . $diagnosaSearch }}
                            </th>
                        </tr>
                        <tr>
                            <th class="p-3 pl-6 w-12 text-center text-[10px] font-bold text-neutral-400 uppercase tracking-widest">P</th>
                            <th class="p-3 text-[10px] font-bold text-neutral-400 uppercase tracking-widest w-24">Kode</th>
                            <th class="p-3 text-[10px] font-bold text-neutral-400 uppercase tracking-widest">Nama Penyakit</th>
                            <th class="p-3 text-[10px] font-bold text-neutral-400 uppercase tracking-widest text-center w-20">Urut</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-100 dark:divide-neutral-800">
                        @forelse($this->listPenyakitMaster as $t)
                            @php
                                $isSelected = collect($selectedDiagnosa)->contains('kd_penyakit', $t->kd_penyakit);
                                $selItem = collect($selectedDiagnosa)->firstWhere('kd_penyakit', $t->kd_penyakit);
                            @endphp
                            <tr class="transition-colors cursor-pointer group {{ $isSelected ? 'bg-[#F1F5E9]/50' : 'hover:bg-neutral-50 dark:hover:bg-neutral-800' }}" 
                                wire:click="toggleDiagnosa('{{ $t->kd_penyakit }}', '{{ addslashes($t->nm_penyakit) }}')">
                                <td class="p-3 pl-6 text-center">
                                    <div class="flex items-center justify-center">
                                        <input type="checkbox" class="rounded border-neutral-300 text-[#4C5C2D] focus:ring-[#4C5C2D]" {{ $isSelected ? 'checked' : '' }} />
                                    </div>
                                </td>
                                <td class="p-3 text-xs font-mono font-bold {{ $isSelected ? 'text-[#4C5C2D]' : 'text-neutral-600' }}">{{ $t->kd_penyakit }}</td>
                                <td class="p-3">
                                    <span class="text-sm font-bold {{ $isSelected ? 'text-[#4C5C2D]' : 'text-neutral-700' }} dark:text-neutral-200 uppercase">{{ $t->nm_penyakit }}</span>
                                </td>
                                <td class="p-3 text-center">
                                    @if($isSelected)
                                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-[#4C5C2D] text-white text-[10px] font-bold animate-in zoom-in-50">
                                            {{ $selItem['prioritas'] }}
                                        </span>
                                    @else
                                        <span class="text-neutral-200 dark:text-neutral-700">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="p-12 text-center text-sm text-neutral-400 italic">
                                    <flux:icon name="magnifying-glass" class="w-10 h-10 mx-auto mb-3 text-neutral-200" />
                                    Tidak ada hasil ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ========================================================= --}}
    {{-- MODAL PROSEDUR (ICD-9) - ALPINE VERSION (SOP COMPLIANT) --}}
    {{-- ========================================================= --}}
    <div x-show="prosedurModalOpen" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-6 overflow-hidden" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="prosedurModalOpen = false"></div>
        <div class="relative w-full max-w-5xl max-h-[90vh] bg-white dark:bg-neutral-900 rounded-2xl shadow-2xl border border-neutral-200 dark:border-neutral-700 flex flex-col overflow-hidden animate-in zoom-in-95 duration-200">
            <div class="p-6 border-b border-neutral-100 dark:border-neutral-800 bg-neutral-50/50 dark:bg-neutral-800/50 flex-shrink-0">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-lg font-bold text-neutral-800 dark:text-neutral-100 flex items-center gap-2 uppercase tracking-wide">
                        <flux:icon name="scissors" class="w-5 h-5 text-blue-600" />
                        Input Prosedur Pasien (ICD-9)
                    </h2>
                    <button @click="prosedurModalOpen = false" class="text-neutral-400 hover:text-neutral-600 transition-colors">
                        <flux:icon name="x-mark" class="w-6 h-6" />
                    </button>
                </div>
                
                <div class="flex items-center gap-4 mb-4">
                    <div class="flex-1">
                        <flux:label class="text-[10px] font-bold uppercase text-neutral-400 mb-2 block tracking-widest">Cari Prosedur :</flux:label>
                        <flux:input wire:model.live.debounce.300ms="prosedurSearch" placeholder="Ketik nama prosedur atau kode ICD-9..." icon="magnifying-glass" clearable />
                    </div>
                </div>

                <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="flex flex-col">
                            <span class="text-[10px] font-bold text-neutral-400 uppercase tracking-widest">Item Dipilih</span>
                            <span class="text-sm font-bold text-blue-600">{{ count($selectedProsedur) }} Prosedur</span>
                        </div>
                        @if(count($selectedProsedur) > 0)
                            <div class="h-8 w-px bg-neutral-200"></div>
                            <div class="flex flex-wrap gap-2 max-w-md overflow-hidden">
                                @foreach($selectedProsedur as $sel)
                                    <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded bg-white border border-blue-200 text-[10px] font-bold text-neutral-700">
                                        <span class="text-blue-600">{{ $sel['kode'] }}</span>
                                        <span class="text-neutral-400 font-mono">#{{ $sel['prioritas'] }}</span>
                                        <button wire:click="toggleProsedur('{{ $sel['kode'] }}', '')" class="hover:text-red-500">
                                            <flux:icon name="x-mark" class="w-3 h-3" />
                                        </button>
                                    </span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                    <flux:button wire:click="saveProsedur" variant="primary" class="px-8 bg-blue-600 hover:bg-blue-700 border-blue-600 text-white" :disabled="empty($selectedProsedur)">
                        Simpan Semua ({{ count($selectedProsedur) }})
                    </flux:button>
                </div>
            </div>

            <div class="flex-1 overflow-y-auto p-0 relative">
                <table class="w-full text-left border-collapse">
                    <thead class="sticky top-0 z-10 bg-neutral-100 dark:bg-neutral-900 border-b border-neutral-200">
                        <tr>
                            <th colspan="5" class="p-2 pl-6 bg-neutral-50 text-[9px] font-bold text-neutral-400 uppercase tracking-widest border-b border-neutral-100">
                                {{ strlen($prosedurSearch) < 2 ? 'Sering Digunakan / Daftar Teratas' : 'Hasil Pencarian : ' . $prosedurSearch }}
                            </th>
                        </tr>
                        <tr>
                            <th class="p-3 pl-6 w-12 text-center text-[10px] font-bold text-neutral-400 uppercase tracking-widest">P</th>
                            <th class="p-3 text-[10px] font-bold text-neutral-400 uppercase tracking-widest w-24">Kode</th>
                            <th class="p-3 text-[10px] font-bold text-neutral-400 uppercase tracking-widest">Deskripsi Prosedur</th>
                            <th class="p-3 text-[10px] font-bold text-neutral-400 uppercase tracking-widest text-center w-20">Urut</th>
                            <th class="p-3 text-[10px] font-bold text-neutral-400 uppercase tracking-widest text-center w-20">Jml</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-100 dark:divide-neutral-800">
                        @forelse($this->listIcd9Master as $t)
                            @php
                                $isSelected = collect($selectedProsedur)->contains('kode', $t->kode);
                                $selItem = collect($selectedProsedur)->firstWhere('kode', $t->kode);
                            @endphp
                            <tr class="transition-colors cursor-pointer group {{ $isSelected ? 'bg-blue-50/50' : 'hover:bg-neutral-50 dark:hover:bg-neutral-800' }}" 
                                wire:click="toggleProsedur('{{ $t->kode }}', '{{ addslashes($t->deskripsi_panjang) }}')">
                                <td class="p-3 pl-6 text-center">
                                    <div class="flex items-center justify-center">
                                        <input type="checkbox" class="rounded border-neutral-300 text-blue-600 focus:ring-blue-500" {{ $isSelected ? 'checked' : '' }} />
                                    </div>
                                </td>
                                <td class="p-3 text-xs font-mono font-bold {{ $isSelected ? 'text-blue-600' : 'text-neutral-600' }}">{{ $t->kode }}</td>
                                <td class="p-3">
                                    <span class="text-sm font-bold {{ $isSelected ? 'text-blue-600' : 'text-neutral-700' }} dark:text-neutral-200 uppercase">{{ $t->deskripsi_panjang }}</span>
                                </td>
                                <td class="p-3 text-center">
                                    @if($isSelected)
                                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-blue-600 text-white text-[10px] font-bold animate-in zoom-in-50">
                                            {{ $selItem['prioritas'] }}
                                        </span>
                                    @else
                                        <span class="text-neutral-200 dark:text-neutral-700">-</span>
                                    @endif
                                </td>
                                <td class="p-3 text-center">
                                    @if($isSelected)
                                        <span class="text-xs font-mono font-bold text-blue-600">{{ $selItem['jumlah'] }}</span>
                                    @else
                                        <span class="text-neutral-200 dark:text-neutral-700">-</span>
                                    @endif
                                </td>
                            </tr>
@empty
                            <tr>
                                <td colspan="5" class="p-12 text-center text-sm text-neutral-400 italic">
                                    <flux:icon name="magnifying-glass" class="w-10 h-10 mx-auto mb-3 text-neutral-200" />
                                    Tidak ada hasil ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
