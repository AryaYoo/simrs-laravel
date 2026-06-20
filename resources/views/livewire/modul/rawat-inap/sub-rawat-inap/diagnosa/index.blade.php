<div class="flex flex-col gap-6 pb-24" x-data="{
    handleDiagnosaCheck(kd, isChecked) {
        if (isChecked) {
            let maxAssigned = 0;
            if ($wire.urutDiagnosa) {
                Object.values($wire.urutDiagnosa).forEach(val => {
                    let parsed = parseInt(val);
                    if (!isNaN(parsed) && parsed > maxAssigned) {
                        maxAssigned = parsed;
                    }
                });
            }
            let max = Math.max($wire.maxPrioritasDiagnosa, maxAssigned);
            $wire.urutDiagnosa[kd] = max + 1;
        } else {
            $wire.urutDiagnosa[kd] = '';
        }
    },
    handleProsedurCheck(kd, isChecked) {
        if (isChecked) {
            let maxAssigned = 0;
            if ($wire.urutProsedur) {
                Object.values($wire.urutProsedur).forEach(val => {
                    let parsed = parseInt(val);
                    if (!isNaN(parsed) && parsed > maxAssigned) {
                        maxAssigned = parsed;
                    }
                });
            }
            let max = Math.max($wire.maxPrioritasProsedur, maxAssigned);
            $wire.urutProsedur[kd] = max + 1;
            
            if (!$wire.jmlProsedur[kd]) {
                $wire.jmlProsedur[kd] = '1';
            }
        } else {
            $wire.urutProsedur[kd] = '';
            $wire.jmlProsedur[kd] = '';
        }
    }
}">
    {{-- Header / Breadcrumb --}}
    <div class="flex flex-col gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('modul.rawat-inap.perawatan-tindakan', str_replace('/', '-', $no_rawat)) }}"
                class="flex items-center justify-center w-10 h-8 rounded-md bg-[#4C5C2D] transition-colors hover:bg-[#3d4b24] shadow-sm">
                <flux:icon name="chevron-left" class="w-5 h-5 text-white" />
            </a>
            <div class="flex-1">
                <nav class="text-xs text-neutral-400 mb-0.5">
                    <span>Modul</span>
                    <span class="mx-1">/</span>
                    <span>Rawat Inap</span>
                    <span class="mx-1">/</span>
                    <span>Diagnosa</span>
                </nav>
                <h1 class="text-xl font-bold text-neutral-800 dark:text-neutral-100">Diagnosa & Prosedur</h1>
                <div class="mt-1.5 flex flex-wrap items-center gap-2 text-sm">
                    <span class="text-neutral-500">No. Rawat:</span>
                    <span class="font-bold text-[#4C5C2D] dark:text-[#8CC7C4] font-mono bg-[#4C5C2D]/10 dark:bg-[#4C5C2D]/30 px-2 py-0.5 rounded">{{ $no_rawat }}</span>
                    <span class="text-neutral-300 mx-1">|</span>
                    <span class="text-neutral-500">Pasien:</span>
                    <span class="font-bold text-neutral-800 dark:text-neutral-100 bg-neutral-100 dark:bg-neutral-800 px-2 py-0.5 rounded">{{ $pasien->pasien->nm_pasien ?? '-' }}</span>
                    <span class="text-neutral-300 mx-1">|</span>
                    <span class="text-neutral-500">Status:</span>
                    <select wire:model.live="statusPilihan"
                        class="text-sm font-semibold border border-neutral-300 dark:border-neutral-600 rounded-md px-2 py-0.5 bg-white dark:bg-neutral-900 text-neutral-800 dark:text-neutral-100 focus:ring-[#4C5C2D] focus:border-[#4C5C2D]">
                        <option value="Ranap">Ranap</option>
                        <option value="Ralan">Ralan</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="flex border-b border-neutral-200 dark:border-neutral-700">
        <button wire:click="setActiveTab('input_data')"
            class="px-4 py-2 border-b-2 font-medium text-sm transition-colors {{ $activeTab === 'input_data' ? 'border-[#4C5C2D] text-[#4C5C2D] dark:text-[#8CC7C4] dark:border-[#8CC7C4]' : 'border-transparent text-neutral-500 hover:text-neutral-700 hover:border-neutral-300' }}">
            Input Data
        </button>
        <button wire:click="setActiveTab('data_diagnosa')"
            class="px-4 py-2 border-b-2 font-medium text-sm transition-colors {{ $activeTab === 'data_diagnosa' ? 'border-[#4C5C2D] text-[#4C5C2D] dark:text-[#8CC7C4] dark:border-[#8CC7C4]' : 'border-transparent text-neutral-500 hover:text-neutral-700 hover:border-neutral-300' }}">
            Data Diagnosa
        </button>
        <button wire:click="setActiveTab('data_prosedur')"
            class="px-4 py-2 border-b-2 font-medium text-sm transition-colors {{ $activeTab === 'data_prosedur' ? 'border-[#4C5C2D] text-[#4C5C2D] dark:text-[#8CC7C4] dark:border-[#8CC7C4]' : 'border-transparent text-neutral-500 hover:text-neutral-700 hover:border-neutral-300' }}">
            Data Prosedur
        </button>
    </div>

    {{-- Content --}}
    <div class="bg-white dark:bg-neutral-800 rounded-xl border border-neutral-200 dark:border-neutral-700 shadow-sm overflow-hidden">

        {{-- ============================== --}}
        {{-- TAB: INPUT DATA                --}}
        {{-- ============================== --}}
        @if($activeTab === 'input_data')
            <div class="p-6 flex flex-col gap-8">

                {{-- ── MASTER DIAGNOSA ── --}}
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                    {{-- KIRI: Tabel Browse --}}
                    <div class="lg:col-span-6 flex flex-col h-full">
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4">
                            <h2 class="text-lg font-bold text-neutral-800 dark:text-neutral-100">Cari Diagnosa</h2>
                            <div class="flex items-center gap-2 w-full sm:w-auto">
                                <input wire:model.live.debounce.300ms="searchDiagnosa" type="text"
                                    placeholder="Cari diagnosa..."
                                    class="px-3 py-1.5 text-sm border border-neutral-300 dark:border-neutral-600 rounded-md focus:ring-[#4C5C2D] focus:border-[#4C5C2D] bg-white dark:bg-neutral-900 text-neutral-800 dark:text-neutral-100 flex-1 sm:w-64">
                            </div>
                        </div>
                        <div class="overflow-x-auto border border-neutral-200 dark:border-neutral-700 rounded-lg flex-1">
                            <table class="w-full text-xs text-left text-neutral-600 dark:text-neutral-400">
                                <thead class="text-[10px] text-neutral-700 uppercase bg-neutral-50 dark:bg-neutral-900/50 dark:text-neutral-300">
                                    <tr>
                                        <th scope="col" class="px-2 py-2">Kode</th>
                                        <th scope="col" class="px-2 py-2">Nama Penyakit</th>
                                        <th scope="col" class="px-2 py-2 text-center w-8">VC</th>
                                        <th scope="col" class="px-2 py-2 text-center w-8">AP</th>
                                        <th scope="col" class="px-2 py-2 text-center w-8">Ast</th>
                                        <th scope="col" class="px-2 py-2 text-center w-8">IM</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($masterDiagnosa as $d)
                                    <tr wire:key="diag-{{ $d->kd_penyakit }}"
                                        wire:click="pushToCartDiagnosa('{{ $d->kd_penyakit }}', '{{ addslashes($d->nm_penyakit) }}', '{{ $d->validcode }}', '{{ $d->accpdx }}', '{{ $d->asterisk }}', '{{ $d->im }}')"
                                        class="cursor-pointer bg-white border-b dark:bg-neutral-800 dark:border-neutral-700 hover:bg-[#F1F5E9] dark:hover:bg-[#4C5C2D]/20 transition-colors">
                                        <td class="px-2 py-2 font-medium text-neutral-900 dark:text-white whitespace-nowrap">{{ $d->kd_penyakit }}</td>
                                        <td class="px-2 py-2 font-bold">{{ $d->nm_penyakit }}</td>
                                        <td class="px-2 py-2 text-center">{{ $d->validcode }}</td>
                                        <td class="px-2 py-2 text-center">{{ $d->accpdx }}</td>
                                        <td class="px-2 py-2 text-center">{{ $d->asterisk }}</td>
                                        <td class="px-2 py-2 text-center">{{ $d->im }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="px-4 py-8 text-center text-neutral-500">Tidak ada data master diagnosa.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4">
                            {{ $masterDiagnosa->links(data: ['scrollTo' => false]) }}
                        </div>
                    </div>

                    {{-- KANAN: Keranjang Diagnosa --}}
                    <div class="lg:col-span-6 flex flex-col h-full">
                        <div class="bg-white dark:bg-neutral-800 rounded-xl border {{ count($cartDiagnosa) > 0 ? 'border-[#4C5C2D]' : 'border-neutral-200 dark:border-neutral-700' }} flex flex-col h-full overflow-hidden shadow-sm">
                            <div class="px-4 py-3 border-b flex items-center justify-between {{ count($cartDiagnosa) > 0 ? 'bg-[#4C5C2D]/5 border-[#4C5C2D]/30' : 'bg-neutral-50 border-neutral-200 dark:bg-neutral-800 dark:border-neutral-700' }}">
                                <h3 class="font-bold flex items-center gap-2 {{ count($cartDiagnosa) > 0 ? 'text-[#4C5C2D] dark:text-[#8CC7C4]' : 'text-neutral-600 dark:text-neutral-300' }}">
                                    <flux:icon name="shopping-cart" class="w-5 h-5" />
                                    Daftar Diagnosa Dipilih
                                </h3>
                                @if(count($cartDiagnosa) > 0)
                                    <span class="text-[10px] font-bold bg-[#4C5C2D] text-white px-2 py-0.5 rounded-full">{{ count($cartDiagnosa) }} Item</span>
                                @endif
                            </div>
                            
                            <div class="flex-1 overflow-x-auto min-h-[250px] p-0">
                                <table class="w-full text-xs text-left">
                                    <thead class="text-[10px] text-neutral-500 uppercase bg-neutral-50 dark:bg-neutral-900 border-b border-neutral-200 dark:border-neutral-700 sticky top-0 z-10 shadow-sm">
                                        <tr>
                                            <th class="px-2 py-2 text-center w-14">Urut</th>
                                            <th class="px-2 py-2">Kode</th>
                                            <th class="px-2 py-2">Penyakit</th>
                                            <th class="px-1 py-2 text-center w-8">VC</th>
                                            <th class="px-1 py-2 text-center w-8">AP</th>
                                            <th class="px-1 py-2 text-center w-8">Ast</th>
                                            <th class="px-1 py-2 text-center w-8">IM</th>
                                            <th class="px-2 py-2 text-center w-10">Hapus</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-neutral-100 dark:divide-neutral-800">
                                        @forelse($cartDiagnosa as $index => $item)
                                            <tr class="bg-white dark:bg-neutral-900/50">
                                                <td class="px-2 py-2 text-center align-top">
                                                    <input type="number" wire:model="cartDiagnosa.{{ $index }}.urut" class="w-12 text-center text-xs border border-neutral-300 dark:border-neutral-600 rounded focus:ring-[#4C5C2D] focus:border-[#4C5C2D] bg-white dark:bg-neutral-800 dark:text-white px-1 py-1" />
                                                </td>
                                                <td class="px-2 py-2 align-top">
                                                    <span class="font-mono text-neutral-500">{{ $item['kd_penyakit'] }}</span>
                                                </td>
                                                <td class="px-2 py-2 align-top font-bold text-neutral-800 dark:text-neutral-200 whitespace-normal min-w-[120px]">
                                                    {{ $item['nm_penyakit'] }}
                                                </td>
                                                <td class="px-1 py-2 text-center align-top">
                                                    <input type="text" wire:model="cartDiagnosa.{{ $index }}.vc" class="w-8 text-center text-xs border border-neutral-300 dark:border-neutral-600 rounded px-1 py-1 bg-white dark:bg-neutral-800 dark:text-white" />
                                                </td>
                                                <td class="px-1 py-2 text-center align-top">
                                                    <input type="text" wire:model="cartDiagnosa.{{ $index }}.ap" class="w-8 text-center text-xs border border-neutral-300 dark:border-neutral-600 rounded px-1 py-1 bg-white dark:bg-neutral-800 dark:text-white" />
                                                </td>
                                                <td class="px-1 py-2 text-center align-top">
                                                    <input type="text" wire:model="cartDiagnosa.{{ $index }}.ast" class="w-8 text-center text-xs border border-neutral-300 dark:border-neutral-600 rounded px-1 py-1 bg-white dark:bg-neutral-800 dark:text-white" />
                                                </td>
                                                <td class="px-1 py-2 text-center align-top">
                                                    <input type="text" wire:model="cartDiagnosa.{{ $index }}.im" class="w-8 text-center text-xs border border-neutral-300 dark:border-neutral-600 rounded px-1 py-1 bg-white dark:bg-neutral-800 dark:text-white" />
                                                </td>
                                                <td class="px-2 py-2 text-center align-top">
                                                    <button type="button" wire:click="removeFromCartDiagnosa({{ $index }})" class="text-red-500 hover:text-white hover:bg-red-500 p-1.5 rounded transition-colors border border-transparent hover:border-red-600">
                                                        <flux:icon name="trash" class="w-4 h-4" />
                                                    </button>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="px-4 py-12 text-center text-neutral-400">
                                                    <flux:icon name="queue-list" class="w-10 h-10 mx-auto mb-2 opacity-30" />
                                                    <p>Daftar diagnosa kosong</p>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            
                            @if(count($cartDiagnosa) > 0)
                                <div class="p-4 border-t border-[#4C5C2D]/30 bg-[#F1F5E9] dark:bg-[#4C5C2D]/10 mt-auto">
                                    <flux:button wire:click="simpanDiagnosa" variant="primary" icon="check" class="w-full bg-[#4C5C2D] hover:bg-[#3D4A24] text-white shadow-md font-bold py-2 h-auto text-sm flex items-center justify-center gap-2">
                                        Simpan Diagnosa
                                    </flux:button>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <hr class="border-neutral-200 dark:border-neutral-700" />

                {{-- ── MASTER PROSEDUR ── --}}
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                    {{-- KIRI: Tabel Browse --}}
                    <div class="lg:col-span-6 flex flex-col h-full">
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4">
                            <h2 class="text-lg font-bold text-neutral-800 dark:text-neutral-100">Cari Prosedur</h2>
                            <div class="flex items-center gap-2 w-full sm:w-auto">
                                <input wire:model.live.debounce.300ms="searchProsedur" type="text"
                                    placeholder="Cari prosedur..."
                                    class="px-3 py-1.5 text-sm border border-neutral-300 dark:border-neutral-600 rounded-md focus:ring-[#4C5C2D] focus:border-[#4C5C2D] bg-white dark:bg-neutral-900 text-neutral-800 dark:text-neutral-100 flex-1 sm:w-64">
                            </div>
                        </div>
                        <div class="overflow-x-auto border border-neutral-200 dark:border-neutral-700 rounded-lg flex-1">
                            <table class="w-full text-xs text-left text-neutral-600 dark:text-neutral-400">
                                <thead class="text-[10px] text-neutral-700 uppercase bg-neutral-50 dark:bg-neutral-900/50 dark:text-neutral-300">
                                    <tr>
                                        <th scope="col" class="px-2 py-2">Kode</th>
                                        <th scope="col" class="px-2 py-2">Deskripsi Panjang</th>
                                        <th scope="col" class="px-2 py-2 text-center w-8">VC</th>
                                        <th scope="col" class="px-2 py-2 text-center w-8">AP</th>
                                        <th scope="col" class="px-2 py-2 text-center w-8">IM</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($masterProsedur as $p)
                                    <tr wire:key="pros-{{ $p->kode }}"
                                        wire:click="pushToCartProsedur('{{ $p->kode }}', '{{ addslashes($p->deskripsi_panjang) }}')"
                                        class="cursor-pointer bg-white border-b dark:bg-neutral-800 dark:border-neutral-700 hover:bg-[#F1F5E9] dark:hover:bg-[#4C5C2D]/20 transition-colors">
                                        <td class="px-2 py-2 font-medium text-neutral-900 dark:text-white whitespace-nowrap">{{ $p->kode }}</td>
                                        <td class="px-2 py-2 font-bold">{{ $p->deskripsi_panjang }}</td>
                                        <td class="px-2 py-2 text-center">{{ $p->validcode ?? '-' }}</td>
                                        <td class="px-2 py-2 text-center">{{ $p->accpdx ?? '-' }}</td>
                                        <td class="px-2 py-2 text-center">{{ $p->im ?? '-' }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="px-4 py-8 text-center text-neutral-500">Tidak ada data master prosedur.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4">
                            {{ $masterProsedur->links(data: ['scrollTo' => false]) }}
                        </div>
                    </div>

                    {{-- KANAN: Keranjang Prosedur --}}
                    <div class="lg:col-span-6 flex flex-col h-full">
                        <div class="bg-white dark:bg-neutral-800 rounded-xl border {{ count($cartProsedur) > 0 ? 'border-[#4C5C2D]' : 'border-neutral-200 dark:border-neutral-700' }} flex flex-col h-full overflow-hidden shadow-sm">
                            <div class="px-4 py-3 border-b flex items-center justify-between {{ count($cartProsedur) > 0 ? 'bg-[#4C5C2D]/5 border-[#4C5C2D]/30' : 'bg-neutral-50 border-neutral-200 dark:bg-neutral-800 dark:border-neutral-700' }}">
                                <h3 class="font-bold flex items-center gap-2 {{ count($cartProsedur) > 0 ? 'text-[#4C5C2D] dark:text-[#8CC7C4]' : 'text-neutral-600 dark:text-neutral-300' }}">
                                    <flux:icon name="shopping-cart" class="w-5 h-5" />
                                    Daftar Prosedur Dipilih
                                </h3>
                                @if(count($cartProsedur) > 0)
                                    <span class="text-[10px] font-bold bg-[#4C5C2D] text-white px-2 py-0.5 rounded-full">{{ count($cartProsedur) }} Item</span>
                                @endif
                            </div>
                            
                            <div class="flex-1 overflow-x-auto min-h-[250px] p-0">
                                <table class="w-full text-xs text-left">
                                    <thead class="text-[10px] text-neutral-500 uppercase bg-neutral-50 dark:bg-neutral-900 border-b border-neutral-200 dark:border-neutral-700 sticky top-0 z-10 shadow-sm">
                                        <tr>
                                            <th class="px-2 py-2 text-center w-14">Urut</th>
                                            <th class="px-2 py-2 text-center w-14">Jml</th>
                                            <th class="px-2 py-2">Kode</th>
                                            <th class="px-2 py-2">Deskripsi Prosedur</th>
                                            <th class="px-1 py-2 text-center w-8">VC</th>
                                            <th class="px-1 py-2 text-center w-8">AP</th>
                                            <th class="px-1 py-2 text-center w-8">IM</th>
                                            <th class="px-2 py-2 text-center w-10">Hapus</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-neutral-100 dark:divide-neutral-800">
                                        @forelse($cartProsedur as $index => $item)
                                            <tr class="bg-white dark:bg-neutral-900/50">
                                                <td class="px-2 py-2 text-center align-top">
                                                    <input type="number" wire:model="cartProsedur.{{ $index }}.urut" class="w-12 text-center text-xs border border-neutral-300 dark:border-neutral-600 rounded focus:ring-[#4C5C2D] focus:border-[#4C5C2D] bg-white dark:bg-neutral-800 dark:text-white px-1 py-1" />
                                                </td>
                                                <td class="px-2 py-2 text-center align-top">
                                                    <input type="number" wire:model="cartProsedur.{{ $index }}.jml" class="w-12 text-center text-xs border border-neutral-300 dark:border-neutral-600 rounded focus:ring-[#4C5C2D] focus:border-[#4C5C2D] bg-white dark:bg-neutral-800 dark:text-white px-1 py-1" />
                                                </td>
                                                <td class="px-2 py-2 align-top">
                                                    <span class="font-mono text-neutral-500">{{ $item['kode'] }}</span>
                                                </td>
                                                <td class="px-2 py-2 align-top font-bold text-neutral-800 dark:text-neutral-200 whitespace-normal min-w-[120px]">
                                                    {{ $item['deskripsi_panjang'] }}
                                                </td>
                                                <td class="px-1 py-2 text-center align-top">
                                                    <input type="text" wire:model="cartProsedur.{{ $index }}.vc" class="w-8 text-center text-xs border border-neutral-300 dark:border-neutral-600 rounded px-1 py-1 bg-white dark:bg-neutral-800 dark:text-white" />
                                                </td>
                                                <td class="px-1 py-2 text-center align-top">
                                                    <input type="text" wire:model="cartProsedur.{{ $index }}.ap" class="w-8 text-center text-xs border border-neutral-300 dark:border-neutral-600 rounded px-1 py-1 bg-white dark:bg-neutral-800 dark:text-white" />
                                                </td>
                                                <td class="px-1 py-2 text-center align-top">
                                                    <input type="text" wire:model="cartProsedur.{{ $index }}.im" class="w-8 text-center text-xs border border-neutral-300 dark:border-neutral-600 rounded px-1 py-1 bg-white dark:bg-neutral-800 dark:text-white" />
                                                </td>
                                                <td class="px-2 py-2 text-center align-top">
                                                    <button type="button" wire:click="removeFromCartProsedur({{ $index }})" class="text-red-500 hover:text-white hover:bg-red-500 p-1.5 rounded transition-colors border border-transparent hover:border-red-600">
                                                        <flux:icon name="trash" class="w-4 h-4" />
                                                    </button>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="px-4 py-12 text-center text-neutral-400">
                                                    <flux:icon name="queue-list" class="w-10 h-10 mx-auto mb-2 opacity-30" />
                                                    <p>Daftar prosedur kosong</p>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            
                            @if(count($cartProsedur) > 0)
                                <div class="p-4 border-t border-[#4C5C2D]/30 bg-[#F1F5E9] dark:bg-[#4C5C2D]/10 mt-auto">
                                    <flux:button wire:click="simpanProsedur" variant="primary" icon="check" class="w-full bg-[#4C5C2D] hover:bg-[#3D4A24] text-white shadow-md font-bold py-2 h-auto text-sm flex items-center justify-center gap-2">
                                        Simpan Prosedur
                                    </flux:button>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- ============================== --}}
        {{-- TAB: DATA DIAGNOSA             --}}
        {{-- ============================== --}}
        @if($activeTab === 'data_diagnosa')
            <div class="p-6">
                <div class="overflow-x-auto border border-neutral-200 dark:border-neutral-700 rounded-lg">
                    <table class="w-full text-xs text-left text-neutral-600 dark:text-neutral-400">
                        <thead class="text-[10px] text-neutral-700 uppercase bg-neutral-50 dark:bg-neutral-900/50 dark:text-neutral-300">
                            <tr>
                                <th scope="col" class="px-2 py-2 w-14 text-center">Urut</th>
                                <th scope="col" class="px-2 py-2">Kode</th>
                                <th scope="col" class="px-2 py-2">Nama Penyakit</th>
                                <th scope="col" class="px-2 py-2">Ciri-ciri Penyakit</th>
                                <th scope="col" class="px-2 py-2">Keterangan</th>
                                <th scope="col" class="px-2 py-2">Kategori</th>
                                <th scope="col" class="px-2 py-2">Ciri-ciri Umum</th>
                                <th scope="col" class="px-2 py-2 text-center w-10">VC</th>
                                <th scope="col" class="px-2 py-2 text-center w-10">AP</th>
                                <th scope="col" class="px-2 py-2 text-center w-10">Ast</th>
                                <th scope="col" class="px-2 py-2 text-center w-10">IM</th>
                                <th scope="col" class="px-2 py-2 text-center">Status</th>
                                <th scope="col" class="px-2 py-2 w-16 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($savedDiagnosa as $sd)
                            <tr wire:key="sdiag-{{ $sd->kd_penyakit }}"
                                class="bg-white border-b dark:bg-neutral-800 dark:border-neutral-700 hover:bg-neutral-50 dark:hover:bg-neutral-700/50">
                                <td class="px-2 py-2 text-center font-semibold">{{ $sd->prioritas }}</td>
                                <td class="px-2 py-2 font-medium text-neutral-900 dark:text-white whitespace-nowrap">{{ $sd->kd_penyakit }}</td>
                                <td class="px-2 py-2">{{ $sd->penyakit->nm_penyakit ?? '-' }}</td>
                                <td class="px-2 py-2">{{ $sd->penyakit->ciri_ciri ?? '-' }}</td>
                                <td class="px-2 py-2">{{ $sd->penyakit->keterangan ?? '-' }}</td>
                                <td class="px-2 py-2">{{ $sd->penyakit->kategoriPenyakit->nm_kategori ?? '-' }}</td>
                                <td class="px-2 py-2">{{ $sd->penyakit->kategoriPenyakit->ciri_umum ?? '-' }}</td>
                                <td class="px-2 py-2 text-center">{{ $sd->penyakit->validcode ?? '-' }}</td>
                                <td class="px-2 py-2 text-center">{{ $sd->penyakit->accpdx ?? '-' }}</td>
                                <td class="px-2 py-2 text-center">{{ $sd->penyakit->asterisk ?? '-' }}</td>
                                <td class="px-2 py-2 text-center">{{ $sd->penyakit->im ?? '-' }}</td>
                                <td class="px-2 py-2 text-center">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium {{ $sd->status_penyakit === 'Baru' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400' }}">
                                        {{ $sd->status_penyakit }}
                                    </span>
                                </td>
                                <td class="px-2 py-2 text-center">
                                    <button wire:click="hapusDiagnosa('{{ $sd->kd_penyakit }}')"
                                        class="text-red-500 hover:text-red-700 p-1 rounded-md hover:bg-red-50 dark:hover:bg-red-900/30 transition-colors"
                                        title="Hapus">
                                        <flux:icon name="trash" class="w-4 h-4" />
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="13" class="px-4 py-8 text-center text-neutral-500">Belum ada data
                                    diagnosa pasien.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        {{-- ============================== --}}
        {{-- TAB: DATA PROSEDUR             --}}
        {{-- ============================== --}}
        @if($activeTab === 'data_prosedur')
            <div class="p-6">
                <div class="overflow-x-auto border border-neutral-200 dark:border-neutral-700 rounded-lg">
                    <table class="w-full text-xs text-left text-neutral-600 dark:text-neutral-400">
                        <thead class="text-[10px] text-neutral-700 uppercase bg-neutral-50 dark:bg-neutral-900/50 dark:text-neutral-300">
                            <tr>
                                <th scope="col" class="px-2 py-2 w-14 text-center">Urut</th>
                                <th scope="col" class="px-2 py-2">Kode</th>
                                <th scope="col" class="px-2 py-2">Deskripsi Panjang</th>
                                <th scope="col" class="px-2 py-2">Deskripsi Pendek</th>
                                <th scope="col" class="px-2 py-2 text-center w-10">VC</th>
                                <th scope="col" class="px-2 py-2 text-center w-10">AP</th>
                                <th scope="col" class="px-2 py-2 text-center w-10">IM</th>
                                <th scope="col" class="px-2 py-2 text-center w-14">Jml</th>
                                <th scope="col" class="px-2 py-2 w-16 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($savedProsedur as $sp)
                            <tr wire:key="spros-{{ $sp->kode }}"
                                class="bg-white border-b dark:bg-neutral-800 dark:border-neutral-700 hover:bg-neutral-50 dark:hover:bg-neutral-700/50">
                                <td class="px-2 py-2 text-center font-semibold">{{ $sp->prioritas }}</td>
                                <td class="px-2 py-2 font-medium text-neutral-900 dark:text-white whitespace-nowrap">{{ $sp->kode }}</td>
                                <td class="px-2 py-2">{{ $sp->icd9->deskripsi_panjang ?? '-' }}</td>
                                <td class="px-2 py-2">{{ $sp->icd9->deskripsi_pendek ?? '-' }}</td>
                                <td class="px-2 py-2 text-center">{{ $sp->icd9->validcode ?? '-' }}</td>
                                <td class="px-2 py-2 text-center">{{ $sp->icd9->accpdx ?? '-' }}</td>
                                <td class="px-2 py-2 text-center">{{ $sp->icd9->im ?? '-' }}</td>
                                <td class="px-2 py-2 text-center">{{ $sp->jumlah }}</td>
                                <td class="px-2 py-2 text-center">
                                    <button wire:click="hapusProsedur('{{ $sp->kode }}')"
                                        class="text-red-500 hover:text-red-700 p-1 rounded-md hover:bg-red-50 dark:hover:bg-red-900/30 transition-colors"
                                        title="Hapus">
                                        <flux:icon name="trash" class="w-4 h-4" />
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="px-4 py-8 text-center text-neutral-500">Belum ada data
                                    prosedur pasien.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

    </div>
</div>
