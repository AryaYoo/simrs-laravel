<div class="flex flex-col gap-6 pb-8">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('modul.rawat-inap.index') }}" wire:navigate
                class="flex items-center justify-center w-8 h-8 rounded-lg transition-colors hover:bg-neutral-100 dark:hover:bg-neutral-700">
                <flux:icon name="chevron-left" class="w-5 h-5 text-neutral-500" />
            </a>
            <div>
                <nav class="text-xs text-neutral-400 mb-0.5">
                    <a href="{{ route('modul.index') }}" wire:navigate class="hover:underline">Modul</a>
                    <span class="mx-1">/</span>
                    <a href="{{ route('modul.rawat-inap.index') }}" wire:navigate class="hover:underline">Rawat Inap</a>
                    <span class="mx-1">/</span>
                    <span>Kelahiran Bayi</span>
                </nav>
                <h1 class="text-xl font-bold text-neutral-800 dark:text-neutral-100">Daftar Pasien Bayi</h1>
            </div>
        </div>
        <flux:button :href="route('modul.rawat-inap.kelahiran-bayi.create')" icon="plus" wire:navigate class="!bg-[#4C5C2D] !border-[#4C5C2D] !text-white hover:!bg-[#3E4A25]">
            Tambah Bayi
        </flux:button>
    </div>

    {{-- Main Card --}}
    <div class="bg-white dark:bg-neutral-800 rounded-xl border border-neutral-200 dark:border-neutral-700 p-4">
        {{-- Toolbar --}}
        <div class="flex flex-col md:flex-row gap-3 mb-6 items-center justify-between border-b border-neutral-100 dark:border-neutral-700 pb-4">
            <div class="w-full md:w-1/3">
                <flux:input wire:model.live.debounce.300ms="search"
                    placeholder="Cari No RM, Nama Bayi, Ibu, atau Ayah..." icon="magnifying-glass" />
            </div>
            <div class="flex flex-col sm:flex-row items-center gap-3 w-full md:w-auto justify-end">
                <div class="flex items-center gap-2 w-full sm:w-auto">
                    <span class="text-xs font-semibold text-neutral-500 whitespace-nowrap">Lahir:</span>
                    <flux:input type="date" wire:model.live="dari" class="w-full sm:w-36" />
                    <span class="text-xs text-neutral-400">s/d</span>
                    <flux:input type="date" wire:model.live="sampai" class="w-full sm:w-36" />
                </div>
                <div class="w-full sm:w-40">
                    <flux:select wire:model.live="jk" class="w-full">
                        <flux:select.option value="">Semua Gender</flux:select.option>
                        <flux:select.option value="L">Laki-Laki</flux:select.option>
                        <flux:select.option value="P">Perempuan</flux:select.option>
                    </flux:select>
                </div>
            </div>
        </div>

        {{-- Table --}}
        <flux:table :paginate="$bayis">
            <flux:table.columns>
                <flux:table.column>No. RM</flux:table.column>
                <flux:table.column>Nama Anak/Bayi</flux:table.column>
                <flux:table.column>Tgl. Lahir</flux:table.column>
                <flux:table.column>Jam Lahir</flux:table.column>
                <flux:table.column>Umur</flux:table.column>
                <flux:table.column>Tgl. Daftar</flux:table.column>
                <flux:table.column>Nama Ibu</flux:table.column>
                <flux:table.column>Nama Ayah</flux:table.column>
                <flux:table.column>Action</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse ($bayis as $bayi)
                    <flux:table.row :key="$bayi->no_rkm_medis">
                        <flux:table.cell class="font-medium tracking-tight font-mono text-xs">{{ $bayi->no_rkm_medis }}</flux:table.cell>
                        <flux:table.cell>
                            @if($bayi->pasien?->jk === 'L')
                                <span class="inline-flex items-center gap-1.5 font-semibold text-blue-600 dark:text-blue-400">
                                    <flux:icon name="user" class="w-3.5 h-3.5 opacity-70" /> {{ $bayi->pasien->nm_pasien ?? '-' }}
                                </span>
                            @elseif($bayi->pasien?->jk === 'P')
                                <span class="inline-flex items-center gap-1.5 font-semibold text-pink-600 dark:text-pink-400">
                                    <flux:icon name="user" class="w-3.5 h-3.5 opacity-70" /> {{ $bayi->pasien->nm_pasien ?? '-' }}
                                </span>
                            @else
                                <span class="text-neutral-700 dark:text-neutral-300">{{ $bayi->pasien->nm_pasien ?? '-' }}</span>
                            @endif
                        </flux:table.cell>
                        <flux:table.cell>{{ $bayi->pasien ? \Carbon\Carbon::parse($bayi->pasien->tgl_lahir)->format('d-m-Y') : '-' }}</flux:table.cell>
                        <flux:table.cell>{{ $bayi->jam_lahir }}</flux:table.cell>
                        <flux:table.cell>{{ $bayi->pasien->umur ?? '-' }}</flux:table.cell>
                        <flux:table.cell>{{ $bayi->pasien ? \Carbon\Carbon::parse($bayi->pasien->tgl_daftar)->format('d-m-Y') : '-' }}</flux:table.cell>
                        <flux:table.cell>{{ $bayi->pasien->nm_ibu ?? '-' }}</flux:table.cell>
                        <flux:table.cell>{{ $bayi->nama_ayah ?? '-' }}</flux:table.cell>
                        <flux:table.cell>
                            <div class="flex items-center gap-1">
                                {{-- Detail --}}
                                <flux:button wire:click="showDetail('{{ $bayi->no_rkm_medis }}')" icon="eye" size="xs" variant="ghost" title="Lihat Detail" />

                                {{-- Cetak SKL --}}
                                <a href="{{ route('modul.rawat-inap.kelahiran-bayi.cetak-skl', $bayi->no_rkm_medis) }}" target="_blank">
                                    <flux:button icon="printer" size="xs" variant="ghost" class="text-[#4C5C2D] hover:bg-[#4C5C2D]/10" title="Cetak SKL" />
                                </a>

                                {{-- Edit --}}
                                <flux:button :href="route('modul.rawat-inap.kelahiran-bayi.edit', $bayi->no_rkm_medis)" wire:navigate icon="pencil-square" size="xs" variant="ghost" title="Edit Data Bayi" />

                                {{-- Delete --}}
                                <div x-data="{
                                    confirmDelete() {
                                        Swal.fire({
                                            title: 'Hapus Pasien Bayi?',
                                            text: 'Data yang dihapus tidak dapat dikembalikan!',
                                            icon: 'warning',
                                            showCancelButton: true,
                                            confirmButtonColor: '#d33',
                                            cancelButtonColor: '#3085d6',
                                            confirmButtonText: 'Ya, hapus!',
                                            cancelButtonText: 'Batal'
                                        }).then((result) => {
                                            if (result.isConfirmed) { @this.deleteData('{{ $bayi->no_rkm_medis }}'); }
                                        });
                                    }
                                }">
                                    <flux:button @click="confirmDelete" icon="trash" size="xs" variant="ghost" class="text-red-500 hover:text-red-700 hover:bg-red-50" title="Hapus Bayi" />
                                </div>
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="10">
                            <div class="flex flex-col items-center justify-center py-12 text-neutral-400">
                                <flux:icon name="face-smile" class="w-12 h-12 mb-3 opacity-40" />
                                <p class="text-sm font-medium">Belum ada data kelahiran bayi</p>
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </div>

    {{-- MODAL DETAIL (Pure Alpine.js - SOP #6) --}}
    @if($detailBayi)
    <div
        x-data="{ open: false }"
        x-on:open-detail-modal.window="open = true"
        x-on:close-detail-modal.window="open = false"
        x-show="open"
        x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
    >
        {{-- Backdrop --}}
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="$wire.closeDetail()"></div>

        {{-- Modal Panel --}}
        <div class="relative bg-white dark:bg-neutral-800 rounded-2xl shadow-2xl w-full max-w-3xl max-h-[90vh] overflow-hidden flex flex-col z-10">

            {{-- Modal Header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-neutral-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-900/50">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-[#4C5C2D]/10 flex items-center justify-center">
                        <flux:icon name="user" class="w-5 h-5 text-[#4C5C2D]" />
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-neutral-800 dark:text-neutral-100">Detail Kelahiran Bayi</h2>
                        <p class="text-[10px] text-neutral-500 font-mono">{{ $detailBayi['no_rkm_medis'] }} &mdash; {{ $detailBayi['nm_pasien'] }}</p>
                    </div>
                </div>
                <button wire:click="closeDetail" class="p-2 rounded-lg hover:bg-neutral-100 dark:hover:bg-neutral-700 transition-colors text-neutral-400 hover:text-neutral-600">
                    <flux:icon name="x-mark" class="w-5 h-5" />
                </button>
            </div>

            {{-- Modal Body (Scrollable) --}}
            <div class="overflow-y-auto flex-1 p-6 space-y-6">

                {{-- Row 1: Identitas --}}
                <div>
                    <h3 class="text-[10px] font-black uppercase tracking-widest text-[#4C5C2D] mb-3">Identitas</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div>
                            <p class="text-[10px] text-neutral-500 mb-0.5">No. RM</p>
                            <p class="text-xs font-bold font-mono">{{ $detailBayi['no_rkm_medis'] }}</p>
                        </div>
                        <div class="col-span-1 md:col-span-2">
                            <p class="text-[10px] text-neutral-500 mb-0.5">Nama Anak/Bayi</p>
                            <p class="text-xs font-bold">{{ $detailBayi['nm_pasien'] }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] text-neutral-500 mb-0.5">Jenis Kelamin</p>
                            <p class="text-xs font-bold">{{ $detailBayi['jk'] === 'L' ? 'Laki-Laki' : ($detailBayi['jk'] === 'P' ? 'Perempuan' : '-') }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] text-neutral-500 mb-0.5">Nama Ibu</p>
                            <p class="text-xs font-semibold">{{ $detailBayi['nm_ibu'] }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] text-neutral-500 mb-0.5">Umur Ibu</p>
                            <p class="text-xs font-semibold">{{ $detailBayi['umur_ibu'] }} Tahun</p>
                        </div>
                        <div>
                            <p class="text-[10px] text-neutral-500 mb-0.5">Nama Ayah</p>
                            <p class="text-xs font-semibold">{{ $detailBayi['nama_ayah'] }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] text-neutral-500 mb-0.5">Umur Ayah</p>
                            <p class="text-xs font-semibold">{{ $detailBayi['umur_ayah'] }} Tahun</p>
                        </div>
                        <div class="col-span-2 md:col-span-4">
                            <p class="text-[10px] text-neutral-500 mb-0.5">Alamat</p>
                            <p class="text-xs">{{ $detailBayi['alamat'] }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] text-neutral-500 mb-0.5">No. SKL</p>
                            <p class="text-xs font-mono">{{ $detailBayi['no_skl'] }}</p>
                        </div>
                    </div>
                </div>

                <hr class="border-neutral-100 dark:border-neutral-700" />

                {{-- Row 2: Antropometri --}}
                <div>
                    <h3 class="text-[10px] font-black uppercase tracking-widest text-[#4C5C2D] mb-3">Antropometri & Waktu</h3>
                    <div class="grid grid-cols-3 md:grid-cols-6 gap-4">
                        <div class="bg-[#4C5C2D]/5 rounded-xl p-3 text-center">
                            <p class="text-[9px] font-bold text-neutral-500 uppercase">Tgl. Lahir</p>
                            <p class="text-xs font-bold mt-1 text-[#4C5C2D]">{{ \Carbon\Carbon::parse($detailBayi['tgl_lahir'])->format('d/m/Y') }}</p>
                        </div>
                        <div class="bg-[#4C5C2D]/5 rounded-xl p-3 text-center">
                            <p class="text-[9px] font-bold text-neutral-500 uppercase">Jam Lahir</p>
                            <p class="text-xs font-bold mt-1 text-[#4C5C2D]">{{ $detailBayi['jam_lahir'] }}</p>
                        </div>
                        <div class="bg-blue-50 dark:bg-blue-950/20 rounded-xl p-3 text-center">
                            <p class="text-[9px] font-bold text-neutral-500 uppercase">Berat</p>
                            <p class="text-xs font-bold mt-1 text-blue-600">{{ $detailBayi['berat_badan'] }} gr</p>
                        </div>
                        <div class="bg-blue-50 dark:bg-blue-950/20 rounded-xl p-3 text-center">
                            <p class="text-[9px] font-bold text-neutral-500 uppercase">Panjang</p>
                            <p class="text-xs font-bold mt-1 text-blue-600">{{ $detailBayi['panjang_badan'] }} cm</p>
                        </div>
                        <div class="bg-neutral-50 dark:bg-neutral-900/40 rounded-xl p-3 text-center">
                            <p class="text-[9px] font-bold text-neutral-500 uppercase">LK</p>
                            <p class="text-xs font-bold mt-1">{{ $detailBayi['lingkar_kepala'] }} cm</p>
                        </div>
                        <div class="bg-neutral-50 dark:bg-neutral-900/40 rounded-xl p-3 text-center">
                            <p class="text-[9px] font-bold text-neutral-500 uppercase">LD</p>
                            <p class="text-xs font-bold mt-1">{{ $detailBayi['lingkar_dada'] }} cm</p>
                        </div>
                    </div>
                </div>

                <hr class="border-neutral-100 dark:border-neutral-700" />

                {{-- Row 3: Persalinan --}}
                <div>
                    <h3 class="text-[10px] font-black uppercase tracking-widest text-[#4C5C2D] mb-3">Persalinan</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div>
                            <p class="text-[10px] text-neutral-500 mb-0.5">Proses Kelahiran</p>
                            <p class="text-xs font-semibold">{{ $detailBayi['proses_lahir'] }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] text-neutral-500 mb-0.5">Anak Ke / G-P-A</p>
                            <p class="text-xs font-semibold">{{ $detailBayi['anakke'] }} / G{{ $detailBayi['g'] }}P{{ $detailBayi['p'] }}A{{ $detailBayi['a'] }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] text-neutral-500 mb-0.5">Ketuban</p>
                            <p class="text-xs font-semibold">{{ $detailBayi['ketuban'] }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] text-neutral-500 mb-0.5">Penyulit Kehamilan</p>
                            <p class="text-xs">{{ $detailBayi['penyulit_kehamilan'] }}</p>
                        </div>
                        <div class="col-span-2">
                            <p class="text-[10px] text-neutral-500 mb-0.5">Penolong</p>
                            <p class="text-xs font-semibold">{{ $detailBayi['penolong_nama'] }} <span class="text-neutral-400 font-mono">({{ $detailBayi['penolong_nik'] }})</span></p>
                        </div>
                        <div>
                            <p class="text-[10px] text-neutral-500 mb-0.5">Diagnosa</p>
                            <p class="text-xs">{{ $detailBayi['diagnosa'] }}</p>
                        </div>
                        <div class="col-span-2 md:col-span-4">
                            <p class="text-[10px] text-neutral-500 mb-0.5">Keterangan</p>
                            <p class="text-xs">{{ $detailBayi['keterangan'] }}</p>
                        </div>
                    </div>
                </div>

                <hr class="border-neutral-100 dark:border-neutral-700" />

                {{-- Row 4: APGAR --}}
                <div>
                    <h3 class="text-[10px] font-black uppercase tracking-widest text-[#4C5C2D] mb-3">Skor APGAR</h3>
                    <div class="grid grid-cols-3 gap-4 mb-4">
                        @foreach(['1' => $detailBayi['n1'], '5' => $detailBayi['n5'], '10' => $detailBayi['n10']] as $menit => $nilai)
                        @php $val = (int)$nilai; $color = $val >= 7 ? 'text-green-600 bg-green-50' : ($val >= 4 ? 'text-yellow-600 bg-yellow-50' : 'text-red-600 bg-red-50'); @endphp
                        <div class="rounded-xl p-4 text-center {{ $color }}">
                            <p class="text-[9px] font-bold uppercase opacity-70">Menit ke-{{ $menit }}</p>
                            <p class="text-3xl font-black mt-1">{{ $val }}</p>
                            <p class="text-[9px] font-bold mt-1">{{ $val >= 7 ? 'Normal' : ($val >= 4 ? 'Asfiksia Sedang' : 'Asfiksia Berat') }}</p>
                        </div>
                        @endforeach
                    </div>
                    <div class="overflow-x-auto rounded-xl border border-neutral-200 dark:border-neutral-700">
                        <table class="w-full text-xs border-collapse">
                            <thead>
                                <tr class="bg-neutral-50 dark:bg-neutral-900/40 text-neutral-500 border-b border-neutral-200">
                                    <th class="px-3 py-2 text-left font-bold border-r border-neutral-200">Tanda</th>
                                    <th class="px-3 py-2 text-center font-bold border-r border-neutral-200">1'</th>
                                    <th class="px-3 py-2 text-center font-bold border-r border-neutral-200">5'</th>
                                    <th class="px-3 py-2 text-center font-bold">10'</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                                @php
                                    $apgar = [
                                        'Frekuensi Jantung' => ['f1','f5','f10'],
                                        'Usaha Nafas'       => ['u1','u5','u10'],
                                        'Tonus Otot'        => ['t1','t5','t10'],
                                        'Refleks'           => ['r1','r5','r10'],
                                        'Warna Kulit'       => ['w1','w5','w10'],
                                    ];
                                @endphp
                                @foreach($apgar as $label => $fields)
                                <tr>
                                    <td class="px-3 py-2 font-semibold border-r border-neutral-200">{{ $label }}</td>
                                    @foreach($fields as $f)
                                    <td class="px-3 py-2 text-center font-bold text-[#4C5C2D] border-r border-neutral-200 last:border-r-0">{{ $detailBayi[$f] }}</td>
                                    @endforeach
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <hr class="border-neutral-100 dark:border-neutral-700" />

                {{-- Row 5: Kondisi Post-Natal --}}
                <div>
                    <h3 class="text-[10px] font-black uppercase tracking-widest text-[#4C5C2D] mb-3">Kondisi Post-Natal</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-[10px] text-neutral-500 mb-0.5">Resusitasi</p>
                            <p class="text-xs">{{ $detailBayi['resusitas'] }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] text-neutral-500 mb-0.5">Obat Yang Diberikan</p>
                            <p class="text-xs">{{ $detailBayi['obat_diberikan'] }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] text-neutral-500 mb-0.5">Mikasi (BAK Pertama)</p>
                            <p class="text-xs font-semibold">{{ $detailBayi['mikasi'] }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] text-neutral-500 mb-0.5">Mikonium (BAB Pertama)</p>
                            <p class="text-xs font-semibold">{{ $detailBayi['mikonium'] }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Modal Footer --}}
            <div class="px-6 py-4 border-t border-neutral-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-900/30 flex items-center justify-between">
                <flux:button :href="route('modul.rawat-inap.kelahiran-bayi.edit', $detailBayi['no_rkm_medis'])" wire:navigate icon="pencil-square" variant="ghost" size="sm">
                    Edit Data Ini
                </flux:button>
                <flux:button wire:click="closeDetail" variant="ghost" size="sm">
                    Tutup
                </flux:button>
            </div>
        </div>
    </div>
    @endif
</div>
