<div class="space-y-6">

    {{-- TOP NAVIGATION & PATIENT INFO --}}
    <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-sm border border-neutral-200/80 dark:border-neutral-700/80 p-4">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            {{-- Kiri: Tombol Back & Judul --}}
            <div class="flex items-center gap-3">
                <button type="button" onclick="history.back()" class="p-2 text-neutral-600 hover:text-neutral-900 dark:text-neutral-300 dark:hover:text-white rounded-lg hover:bg-neutral-100 dark:hover:bg-neutral-700 transition">
                    <flux:icon name="arrow-left" class="w-5 h-5" />
                </button>
                <div>
                    <h1 class="text-lg font-bold text-neutral-900 dark:text-white flex items-center gap-2">
                        <flux:icon name="clipboard-document-check" class="w-5 h-5 text-[#4C5C2D]" />
                        Data Checklist Pre Operasi
                    </h1>
                    <p class="text-xs text-neutral-500">Verifikasi dan keselamatan pasien pra-bedah rawat inap</p>
                </div>
            </div>

            {{-- Kanan: Tombol Buat Checklist --}}
            <div class="flex items-center gap-3">
                <flux:button wire:click="openPanel" icon="plus" variant="filled" class="!flex !flex-row !items-center !bg-[#4C5C2D] !text-white hover:!bg-[#3d4a24] shadow-sm cursor-pointer">
                    Buat Checklist
                </flux:button>
            </div>
        </div>

        {{-- Patient Info Strip --}}
        <div class="mt-4 pt-3 border-t border-neutral-100 dark:border-neutral-700 grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-6 gap-3 text-xs">
            <div>
                <span class="text-neutral-400 block font-medium">NO. RAWAT</span>
                <span class="font-bold text-[#4C5C2D] dark:text-[#8CC7C4]">{{ $regPeriksa->no_rawat }}</span>
            </div>
            <div>
                <span class="text-neutral-400 block font-medium">NO. RM — NAMA</span>
                <span class="font-bold text-neutral-800 dark:text-neutral-200 truncate block">
                    {{ $regPeriksa->no_rkm_medis }} — {{ $regPeriksa->pasien->nm_pasien ?? '-' }}
                </span>
            </div>
            <div>
                <span class="text-neutral-400 block font-medium">TGL LAHIR / UMUR</span>
                <span class="font-semibold text-neutral-800 dark:text-neutral-200">
                    {{ $regPeriksa->pasien->tgl_lahir ?? '-' }} ({{ $regPeriksa->umurdaftar }} {{ $regPeriksa->sttsumur }})
                </span>
            </div>
            <div>
                <span class="text-neutral-400 block font-medium">KAMAR / BANGSAL</span>
                <span class="font-semibold text-neutral-800 dark:text-neutral-200">
                    @php
                        $kamarInap = $regPeriksa->kamarInap->sortByDesc('tgl_masuk')->first() ?? $regPeriksa->kamarInap->last();
                    @endphp
                    @if($kamarInap)
                        {{ $kamarInap->kamar->bangsal->nm_bangsal ?? '-' }} ({{ $kamarInap->kd_kamar }})
                    @else
                        -
                    @endif
                </span>
            </div>
            <div>
                <span class="text-neutral-400 block font-medium">DOKTER DPJP</span>
                <span class="font-semibold text-neutral-800 dark:text-neutral-200 truncate block">
                    {{ $regPeriksa->dokter->nm_dokter ?? '-' }}
                </span>
            </div>
            <div>
                <span class="text-neutral-400 block font-medium">CARA BAYAR</span>
                <span class="font-semibold text-neutral-800 dark:text-neutral-200">
                    {{ $regPeriksa->penjab->png_jawab ?? '-' }}
                </span>
            </div>
        </div>
    </div>

    {{-- MAIN TABLE LIST --}}
    <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-sm border border-neutral-200/80 dark:border-neutral-700/80 overflow-hidden">
        {{-- Table Toolbar --}}
        <div class="p-4 border-b border-neutral-100 dark:border-neutral-700 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold uppercase tracking-wider text-neutral-500">Daftar Checklist</span>
                <flux:badge size="sm" color="zinc">{{ count($filteredChecklists) }} Data</flux:badge>
            </div>
            <div class="w-full sm:w-72">
                <flux:input wire:model.live.debounce.300ms="search" placeholder="Cari tindakan, dokter, SN/CN..." icon="magnifying-glass" size="sm" />
            </div>
        </div>

        {{-- Table Content --}}
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-neutral-700 dark:text-neutral-300">
                <thead class="bg-neutral-50 dark:bg-neutral-900/60 text-neutral-600 dark:text-neutral-400 uppercase text-[10px] tracking-wider font-semibold border-b border-neutral-200 dark:border-neutral-700">
                    <tr>
                        <th class="px-4 py-3 text-center w-28">Aksi</th>
                        <th class="px-4 py-3">Tanggal & Jam</th>
                        <th class="px-4 py-3">Tindakan / Operasi</th>
                        <th class="px-4 py-3">SN / CN</th>
                        <th class="px-4 py-3">Dokter Bedah & Anestesi</th>
                        <th class="px-4 py-3">Petugas Ruangan & OK</th>
                        <th class="px-4 py-3">Ringkasan Verifikasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100 dark:divide-neutral-700/60">
                    @forelse($filteredChecklists as $item)
                        <tr class="hover:bg-neutral-50/80 dark:hover:bg-neutral-750 transition">
                            {{-- Kolom Aksi --}}
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center justify-center gap-1">
                                    {{-- Edit --}}
                                    <button type="button" wire:click="edit('{{ $item['tanggal'] }}')" title="Edit Checklist" class="p-1.5 text-neutral-600 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-neutral-700 rounded-lg transition">
                                        <flux:icon name="pencil-square" class="w-4 h-4" />
                                    </button>

                                    {{-- View (Pop up) --}}
                                    <button type="button" wire:click="showDetail('{{ $item['tanggal'] }}')" title="Lihat Detail" class="p-1.5 text-neutral-600 hover:text-emerald-600 hover:bg-emerald-50 dark:hover:bg-neutral-700 rounded-lg transition">
                                        <flux:icon name="eye" class="w-4 h-4" />
                                    </button>

                                    {{-- Cetak --}}
                                    <a href="{{ route('modul.rawat-inap.checklist-pre-operasi.cetak', [str_replace('/', '-', $regPeriksa->no_rawat), urlencode($item['tanggal'])]) }}" target="_blank" title="Cetak Checklist Pre Operasi" class="p-1.5 text-[#4C5C2D] hover:text-[#3d4a24] hover:bg-neutral-100 dark:hover:bg-neutral-700 rounded-lg transition">
                                        <flux:icon name="printer" class="w-4 h-4" />
                                    </a>

                                    {{-- Delete --}}
                                    <button type="button" @click="
                                        Swal.fire({
                                            title: 'Hapus Checklist Pre Operasi?',
                                            text: 'Data tanggal {{ $item['tanggal'] }} akan dihapus secara permanen.',
                                            icon: 'warning',
                                            showCancelButton: true,
                                            confirmButtonColor: '#dc2626',
                                            cancelButtonColor: '#6b7280',
                                            confirmButtonText: 'Ya, Hapus!',
                                            cancelButtonText: 'Batal'
                                        }).then((result) => {
                                            if (result.isConfirmed) {
                                                $wire.delete('{{ $item['tanggal'] }}');
                                            }
                                        })
                                    " title="Hapus Data" class="p-1.5 text-neutral-600 hover:text-red-600 hover:bg-red-50 dark:hover:bg-neutral-700 rounded-lg transition">
                                        <flux:icon name="trash" class="w-4 h-4" />
                                    </button>
                                </div>
                            </td>

                            {{-- Tanggal --}}
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="font-bold text-neutral-800 dark:text-neutral-200 block">
                                    {{ \Carbon\Carbon::parse($item['tanggal'])->format('d-m-Y') }}
                                </span>
                                <span class="text-[11px] text-neutral-500">
                                    {{ \Carbon\Carbon::parse($item['tanggal'])->format('H:i:s') }} WIB
                                </span>
                            </td>

                            {{-- Tindakan --}}
                            <td class="px-4 py-3">
                                <span class="font-semibold text-neutral-900 dark:text-white block">{{ $item['tindakan'] }}</span>
                            </td>

                            {{-- SN/CN --}}
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="font-medium text-neutral-700 dark:text-neutral-300">{{ $item['sncn'] ?: '-' }}</span>
                            </td>

                            {{-- Dokter Bedah & Anestesi --}}
                            <td class="px-4 py-3">
                                <div class="space-y-0.5">
                                    <div class="flex items-center gap-1.5">
                                        <span class="text-[10px] uppercase font-bold text-neutral-400">Bedah:</span>
                                        <span class="font-medium text-neutral-800 dark:text-neutral-200">{{ $item['dokter_bedah']['nama'] ?? '-' }}</span>
                                    </div>
                                    <div class="flex items-center gap-1.5">
                                        <span class="text-[10px] uppercase font-bold text-neutral-400">Anest:</span>
                                        <span class="font-medium text-neutral-800 dark:text-neutral-200">{{ $item['dokter_anestesi']['nama'] ?? '-' }}</span>
                                    </div>
                                </div>
                            </td>

                            {{-- Petugas Ruangan & OK --}}
                            <td class="px-4 py-3">
                                <div class="space-y-0.5">
                                    <div class="flex items-center gap-1.5">
                                        <span class="text-[10px] uppercase font-bold text-neutral-400">Ruangan:</span>
                                        <span class="text-neutral-700 dark:text-neutral-300">{{ $item['petugas_ruangan']['nama'] ?? '-' }}</span>
                                    </div>
                                    <div class="flex items-center gap-1.5">
                                        <span class="text-[10px] uppercase font-bold text-neutral-400">OK:</span>
                                        <span class="text-neutral-700 dark:text-neutral-300">{{ $item['perawat_ok']['nama'] ?? '-' }}</span>
                                    </div>
                                </div>
                            </td>

                            {{-- Ringkasan Verifikasi --}}
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap gap-1">
                                    <flux:badge size="sm" color="{{ $item['keadaan_umum'] === 'Baik' ? 'green' : ($item['keadaan_umum'] === 'Sedang' ? 'yellow' : 'red') }}">
                                        KU: {{ $item['keadaan_umum'] }}
                                    </flux:badge>
                                    <flux:badge size="sm" color="{{ $item['identitas'] === 'Ya' ? 'green' : 'red' }}">
                                        ID: {{ $item['identitas'] }}
                                    </flux:badge>
                                    <flux:badge size="sm" color="{{ $item['surat_ijin_bedah'] === 'Ada' ? 'blue' : 'zinc' }}">
                                        Ijin Bedah: {{ $item['surat_ijin_bedah'] }}
                                    </flux:badge>
                                    @if($item['pemeriksaan_penunjang_usg'] === 'Ada')
                                        <flux:badge size="sm" color="purple">USG</flux:badge>
                                    @endif
                                    @if($item['persiapan_darah'] === 'Ada')
                                        <flux:badge size="sm" color="red">Darah</flux:badge>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center">
                                <div class="flex flex-col items-center justify-center text-neutral-400">
                                    <flux:icon name="clipboard-document" class="w-10 h-10 mb-2 stroke-1" />
                                    <p class="text-sm font-semibold">Belum Ada Data Checklist Pre Operasi</p>
                                    <p class="text-xs mt-1">Klik tombol "Buat Checklist" di kanan atas untuk menambahkan.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- DETAIL MODAL (VIEW POP-UP) --}}
    <div x-data="{ open: @entangle('showDetailModal') }"
         x-show="open"
         x-cloak
         class="fixed inset-0 z-[60] overflow-y-auto flex items-center justify-center p-4"
         style="display: none;">
        {{-- Backdrop --}}
        <div class="fixed inset-0 bg-neutral-900/60 backdrop-blur-sm transition-opacity"
             @click="open = false; $wire.showDetailModal = false; $wire.detailChecklist = null"></div>

        <div class="relative z-10 w-full max-w-3xl bg-white dark:bg-neutral-800 rounded-2xl shadow-2xl border border-neutral-200 dark:border-neutral-700 overflow-hidden transform transition-all" @click.stop>
                
                @if($detailChecklist)
                {{-- Modal Header --}}
                <div class="px-6 py-4 border-b border-neutral-100 dark:border-neutral-700 flex items-center justify-between bg-neutral-50/70 dark:bg-neutral-900/60">
                    <div>
                        <h3 class="text-base font-bold text-neutral-900 dark:text-white flex items-center gap-2">
                            <flux:icon name="clipboard-document-check" class="w-5 h-5 text-[#4C5C2D]" />
                            Detail Checklist Pre Operasi
                        </h3>
                        <p class="text-xs text-neutral-500">
                            Waktu Verifikasi: {{ \Carbon\Carbon::parse($detailChecklist['tanggal'])->format('d-m-Y H:i:s') }} WIB
                        </p>
                    </div>
                    <button type="button" @click="open = false; $wire.showDetailModal = false; $wire.detailChecklist = null" class="p-1.5 text-neutral-400 hover:text-neutral-700 dark:hover:text-white rounded-lg transition">
                        <flux:icon name="x-mark" class="w-5 h-5" />
                    </button>
                </div>

                {{-- Modal Body --}}
                <div class="p-6 space-y-5 max-h-[75vh] overflow-y-auto text-xs">
                    {{-- Seksi 1: Tindakan & Tim Medis --}}
                    <div class="bg-neutral-50 dark:bg-neutral-900/40 p-4 rounded-xl border border-neutral-200/80 dark:border-neutral-700 space-y-3">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <span class="text-neutral-400 font-semibold block uppercase text-[10px]">Tindakan Operasi</span>
                                <span class="text-sm font-bold text-neutral-900 dark:text-white">{{ $detailChecklist['tindakan'] }}</span>
                            </div>
                            <div>
                                <span class="text-neutral-400 font-semibold block uppercase text-[10px]">SN / CN</span>
                                <span class="text-sm font-semibold text-neutral-800 dark:text-neutral-200">{{ $detailChecklist['sncn'] ?: '-' }}</span>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2 border-t border-neutral-200/60 dark:border-neutral-700/60">
                            <div>
                                <span class="text-neutral-400 font-semibold block uppercase text-[10px]">Dokter Bedah</span>
                                <span class="font-bold text-neutral-800 dark:text-neutral-200">{{ $detailChecklist['dokter_bedah']['nama'] ?? '-' }}</span>
                            </div>
                            <div>
                                <span class="text-neutral-400 font-semibold block uppercase text-[10px]">Dokter Anestesi</span>
                                <span class="font-bold text-neutral-800 dark:text-neutral-200">{{ $detailChecklist['dokter_anestesi']['nama'] ?? '-' }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Seksi 2: Perawat Melakukan Konfirmasi --}}
                    <div>
                        <h4 class="text-xs font-bold uppercase tracking-wider text-neutral-600 dark:text-neutral-400 mb-2 flex items-center gap-1.5">
                            <flux:icon name="check-badge" class="w-4 h-4 text-[#4C5C2D]" />
                            Perawat Melakukan Konfirmasi
                        </h4>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5">
                            <div class="p-2.5 bg-white dark:bg-neutral-800 rounded-lg border border-neutral-200 dark:border-neutral-700">
                                <span class="text-neutral-400 block text-[10px]">Identitas</span>
                                <span class="font-bold text-neutral-900 dark:text-white">{{ $detailChecklist['identitas'] }}</span>
                            </div>
                            <div class="p-2.5 bg-white dark:bg-neutral-800 rounded-lg border border-neutral-200 dark:border-neutral-700">
                                <span class="text-neutral-400 block text-[10px]">Keadaan Umum</span>
                                <span class="font-bold text-neutral-900 dark:text-white">{{ $detailChecklist['keadaan_umum'] }}</span>
                            </div>
                            <div class="p-2.5 bg-white dark:bg-neutral-800 rounded-lg border border-neutral-200 dark:border-neutral-700">
                                <span class="text-neutral-400 block text-[10px]">Penandaan Area Operasi</span>
                                <span class="font-bold text-neutral-900 dark:text-white">{{ $detailChecklist['penandaan_area_operasi'] }}</span>
                            </div>
                            <div class="p-2.5 bg-white dark:bg-neutral-800 rounded-lg border border-neutral-200 dark:border-neutral-700">
                                <span class="text-neutral-400 block text-[10px]">Surat Ijin Bedah</span>
                                <span class="font-bold text-neutral-900 dark:text-white">{{ $detailChecklist['surat_ijin_bedah'] }}</span>
                            </div>
                            <div class="p-2.5 bg-white dark:bg-neutral-800 rounded-lg border border-neutral-200 dark:border-neutral-700">
                                <span class="text-neutral-400 block text-[10px]">Surat Ijin Anestesi</span>
                                <span class="font-bold text-neutral-900 dark:text-white">{{ $detailChecklist['surat_ijin_anestesi'] }}</span>
                            </div>
                            <div class="p-2.5 bg-white dark:bg-neutral-800 rounded-lg border border-neutral-200 dark:border-neutral-700">
                                <span class="text-neutral-400 block text-[10px]">Surat Ijin Tranfusi</span>
                                <span class="font-bold text-neutral-900 dark:text-white">{{ $detailChecklist['surat_ijin_transfusi'] }}</span>
                            </div>
                            <div class="p-2.5 bg-white dark:bg-neutral-800 rounded-lg border border-neutral-200 dark:border-neutral-700 col-span-2">
                                <span class="text-neutral-400 block text-[10px]">Persiapan Darah</span>
                                <span class="font-bold text-neutral-900 dark:text-white">{{ $detailChecklist['persiapan_darah'] }}</span>
                                @if(!empty($detailChecklist['keterangan_persiapan_darah']))
                                    <span class="text-neutral-500 block text-[11px] mt-0.5 font-medium">Ket: {{ $detailChecklist['keterangan_persiapan_darah'] }}</span>
                                @endif
                            </div>
                            <div class="p-2.5 bg-white dark:bg-neutral-800 rounded-lg border border-neutral-200 dark:border-neutral-700">
                                <span class="text-neutral-400 block text-[10px]">Perlengkapan Khusus / Implan</span>
                                <span class="font-bold text-neutral-900 dark:text-white">{{ $detailChecklist['perlengkapan_khusus'] }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Seksi 3: Hasil Pemeriksaan Penunjang --}}
                    <div>
                        <h4 class="text-xs font-bold uppercase tracking-wider text-neutral-600 dark:text-neutral-400 mb-2 flex items-center gap-1.5">
                            <flux:icon name="document-magnifying-glass" class="w-4 h-4 text-[#4C5C2D]" />
                            Hasil Pemeriksaan Penunjang
                        </h4>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                            <div class="p-2.5 bg-white dark:bg-neutral-800 rounded-lg border border-neutral-200 dark:border-neutral-700 flex justify-between items-center">
                                <div>
                                    <span class="text-neutral-400 block text-[10px]">Radiologi / Rontgen</span>
                                    <span class="font-bold text-neutral-900 dark:text-white">{{ $detailChecklist['pemeriksaan_penunjang_rontgen'] }}</span>
                                </div>
                                @if(!empty($detailChecklist['keterangan_pemeriksaan_penunjang_rontgen']))
                                    <span class="text-[11px] text-neutral-500 bg-neutral-100 dark:bg-neutral-700 px-2 py-0.5 rounded">{{ $detailChecklist['keterangan_pemeriksaan_penunjang_rontgen'] }}</span>
                                @endif
                            </div>
                            <div class="p-2.5 bg-white dark:bg-neutral-800 rounded-lg border border-neutral-200 dark:border-neutral-700 flex justify-between items-center">
                                <div>
                                    <span class="text-neutral-400 block text-[10px]">EKG</span>
                                    <span class="font-bold text-neutral-900 dark:text-white">{{ $detailChecklist['pemeriksaan_penunjang_ekg'] }}</span>
                                </div>
                                @if(!empty($detailChecklist['keterangan_pemeriksaan_penunjang_ekg']))
                                    <span class="text-[11px] text-neutral-500 bg-neutral-100 dark:bg-neutral-700 px-2 py-0.5 rounded">{{ $detailChecklist['keterangan_pemeriksaan_penunjang_ekg'] }}</span>
                                @endif
                            </div>
                            <div class="p-2.5 bg-white dark:bg-neutral-800 rounded-lg border border-neutral-200 dark:border-neutral-700 flex justify-between items-center">
                                <div>
                                    <span class="text-neutral-400 block text-[10px]">USG</span>
                                    <span class="font-bold text-neutral-900 dark:text-white">{{ $detailChecklist['pemeriksaan_penunjang_usg'] }}</span>
                                </div>
                                @if(!empty($detailChecklist['keterangan_pemeriksaan_penunjang_usg']))
                                    <span class="text-[11px] text-neutral-500 bg-neutral-100 dark:bg-neutral-700 px-2 py-0.5 rounded">{{ $detailChecklist['keterangan_pemeriksaan_penunjang_usg'] }}</span>
                                @endif
                            </div>
                            <div class="p-2.5 bg-white dark:bg-neutral-800 rounded-lg border border-neutral-200 dark:border-neutral-700 flex justify-between items-center">
                                <div>
                                    <span class="text-neutral-400 block text-[10px]">CT Scan</span>
                                    <span class="font-bold text-neutral-900 dark:text-white">{{ $detailChecklist['pemeriksaan_penunjang_ctscan'] }}</span>
                                </div>
                                @if(!empty($detailChecklist['keterangan_pemeriksaan_penunjang_ctscan']))
                                    <span class="text-[11px] text-neutral-500 bg-neutral-100 dark:bg-neutral-700 px-2 py-0.5 rounded">{{ $detailChecklist['keterangan_pemeriksaan_penunjang_ctscan'] }}</span>
                                @endif
                            </div>
                            <div class="p-2.5 bg-white dark:bg-neutral-800 rounded-lg border border-neutral-200 dark:border-neutral-700 flex justify-between items-center sm:col-span-2">
                                <div>
                                    <span class="text-neutral-400 block text-[10px]">MRI</span>
                                    <span class="font-bold text-neutral-900 dark:text-white">{{ $detailChecklist['pemeriksaan_penunjang_mri'] }}</span>
                                </div>
                                @if(!empty($detailChecklist['keterangan_pemeriksaan_penunjang_mri']))
                                    <span class="text-[11px] text-neutral-500 bg-neutral-100 dark:bg-neutral-700 px-2 py-0.5 rounded">{{ $detailChecklist['keterangan_pemeriksaan_penunjang_mri'] }}</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Seksi 4: Petugas --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2 border-t border-neutral-100 dark:border-neutral-700">
                        <div class="p-2.5 bg-neutral-50 dark:bg-neutral-900/40 rounded-lg border border-neutral-200/80 dark:border-neutral-700">
                            <span class="text-neutral-400 block text-[10px] font-semibold uppercase">Petugas Ruangan</span>
                            <span class="font-bold text-neutral-800 dark:text-neutral-200">{{ $detailChecklist['petugas_ruangan']['nama'] ?? '-' }}</span>
                        </div>
                        <div class="p-2.5 bg-neutral-50 dark:bg-neutral-900/40 rounded-lg border border-neutral-200/80 dark:border-neutral-700">
                            <span class="text-neutral-400 block text-[10px] font-semibold uppercase">Petugas OK</span>
                            <span class="font-bold text-neutral-800 dark:text-neutral-200">{{ $detailChecklist['perawat_ok']['nama'] ?? '-' }}</span>
                        </div>
                    </div>
                </div>

                {{-- Modal Footer --}}
                <div class="px-6 py-4 border-t border-neutral-100 dark:border-neutral-700 flex items-center justify-between bg-neutral-50/70 dark:bg-neutral-900/60">
                    <a href="{{ route('modul.rawat-inap.checklist-pre-operasi.cetak', [str_replace('/', '-', $regPeriksa->no_rawat), urlencode($detailChecklist['tanggal'])]) }}" target="_blank">
                        <flux:button icon="printer" variant="filled" size="sm" class="!bg-[#4C5C2D] !text-white hover:!bg-[#3d4a24]">
                            Cetak Dokumen
                        </flux:button>
                    </a>
                    <flux:button type="button" @click="open = false; $wire.showDetailModal = false; $wire.detailChecklist = null" variant="ghost">Tutup</flux:button>
                </div>
                @endif

            </div>
        </div>

    {{-- RIGHT SLIDE-IN PANEL (DRAWER FOR CREATE & EDIT) --}}
    <div x-data="{
            open: @entangle('isPanelOpen'),
            autoTime: @entangle('autoTime'),
            interval: null,
            init() {
                this.interval = setInterval(() => {
                    if (this.autoTime && this.open) {
                        let now = new Date();
                        let year = now.getFullYear();
                        let month = String(now.getMonth() + 1).padStart(2, '0');
                        let day = String(now.getDate()).padStart(2, '0');
                        let hours = String(now.getHours()).padStart(2, '0');
                        let minutes = String(now.getMinutes()).padStart(2, '0');
                        let seconds = String(now.getSeconds()).padStart(2, '0');
                        $wire.tanggal_date = `${year}-${month}-${day}`;
                        $wire.tanggal_time = `${hours}:${minutes}:${seconds}`;
                    }
                }, 1000);
            }
        }"
        @set-autotime.window="autoTime = ($event.detail && typeof $event.detail.status !== 'undefined') ? $event.detail.status : ($event.detail[0] ? $event.detail[0].status : true)"
        x-show="open"
        x-cloak
        class="fixed inset-0 z-50 flex justify-end overflow-hidden"
        style="display: none;">

        {{-- Backdrop --}}
        <div x-show="open"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="absolute inset-0 bg-neutral-900/50 backdrop-blur-sm"
             @click="open = false; $wire.isPanelOpen = false"></div>

        {{-- Right Panel --}}
        <div x-show="open"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="translate-x-full opacity-0"
             x-transition:enter-end="translate-x-0 opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="translate-x-0 opacity-100"
             x-transition:leave-end="translate-x-full opacity-0"
             class="relative z-10 w-full max-w-2xl bg-white dark:bg-neutral-800 h-full flex flex-col shadow-2xl border-l border-neutral-200 dark:border-neutral-700 overflow-hidden"
             @click.stop>
                
                {{-- Panel Header --}}
                <div class="px-6 py-4 border-b border-neutral-100 dark:border-neutral-700 flex items-center justify-between bg-neutral-50/70 dark:bg-neutral-900/60">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-[#4C5C2D]/10 text-[#4C5C2D] flex items-center justify-center font-bold">
                            <flux:icon name="clipboard-document-check" class="w-5 h-5" />
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-neutral-900 dark:text-white">
                                {{ $isEditMode ? 'Edit Checklist Pre Operasi' : 'Tambah Checklist Pre Operasi' }}
                            </h3>
                            <p class="text-xs text-neutral-500">Formulir pemeriksaan & konfirmasi pra-bedah</p>
                        </div>
                    </div>
                    <button type="button" @click="open = false; $wire.isPanelOpen = false" class="p-1.5 text-neutral-400 hover:text-neutral-700 dark:hover:text-white rounded-lg transition">
                        <flux:icon name="x-mark" class="w-5 h-5" />
                    </button>
                </div>

                {{-- Panel Body (Scrollable Form) --}}
                <div class="flex-1 overflow-y-auto p-6 space-y-6">
                    <form wire:submit="save" id="checklistForm" class="space-y-6">

                        {{-- SEKSI 1: WAKTU, SN/CN & TINDAKAN --}}
                        <div class="p-4 bg-neutral-50/70 dark:bg-neutral-900/40 rounded-xl border border-neutral-200/80 dark:border-neutral-700 space-y-4">
                            <div class="flex items-center justify-between border-b border-neutral-200/60 dark:border-neutral-700/60 pb-2">
                                <span class="text-xs font-bold text-neutral-800 dark:text-neutral-200 uppercase tracking-wider">
                                    Waktu & Data Operasi
                                </span>
                                @if(!$isEditMode)
                                <label class="flex items-center gap-2 cursor-pointer text-xs text-neutral-600 dark:text-neutral-400">
                                    <input type="checkbox" x-model="autoTime" class="rounded border-neutral-300 text-[#4C5C2D] focus:ring-[#4C5C2D]">
                                    <span>Waktu Realtime</span>
                                </label>
                                @endif
                            </div>

                            {{-- Tanggal & Jam --}}
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <flux:field>
                                        <flux:label class="text-xs font-semibold">Tanggal *</flux:label>
                                        <flux:input type="date" wire:model="tanggal_date" :disabled="$autoTime && !$isEditMode" size="sm" />
                                        <flux:error name="tanggal_date" />
                                    </flux:field>
                                </div>
                                <div>
                                    <flux:field>
                                        <flux:label class="text-xs font-semibold">Jam *</flux:label>
                                        <flux:input type="time" step="1" wire:model="tanggal_time" :disabled="$autoTime && !$isEditMode" size="sm" />
                                        <flux:error name="tanggal_time" />
                                    </flux:field>
                                </div>
                            </div>

                            {{-- SN/CN & Tindakan --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <flux:field>
                                        <flux:label class="text-xs font-semibold">SN / CN *</flux:label>
                                        <flux:input wire:model="sncn" placeholder="Contoh: Bidan Rosita" size="sm" />
                                        <flux:error name="sncn" />
                                    </flux:field>
                                </div>
                                <div>
                                    <flux:field>
                                        <flux:label class="text-xs font-semibold">Tindakan / Operasi *</flux:label>
                                        <flux:input wire:model="tindakan" placeholder="Contoh: SC / Kuretase / dll" size="sm" />
                                        <flux:error name="tindakan" />
                                    </flux:field>
                                </div>
                            </div>
                        </div>

                        {{-- SEKSI 2: TIM DOKTER (BEDAH & ANESTESI) --}}
                        <div class="p-4 bg-neutral-50/70 dark:bg-neutral-900/40 rounded-xl border border-neutral-200/80 dark:border-neutral-700 space-y-4">
                            <span class="text-xs font-bold text-neutral-800 dark:text-neutral-200 uppercase tracking-wider block border-b border-neutral-200/60 dark:border-neutral-700/60 pb-2">
                                Tim Dokter
                            </span>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                {{-- Dokter Bedah --}}
                                <div class="space-y-1 relative" x-data="{ open: false }">
                                    <flux:label class="text-xs font-semibold">Dokter Bedah *</flux:label>
                                    <div class="flex items-center gap-2">
                                        <flux:input wire:model="nm_dokter_bedah" readonly placeholder="Pilih Dokter Bedah..." size="sm" class="flex-1 !bg-neutral-100/60 dark:!bg-neutral-800 cursor-not-allowed" />
                                        <button type="button" @click="open = !open" class="px-2.5 py-1.5 bg-[#4C5C2D] text-white rounded-lg hover:bg-[#3d4a24] text-xs font-medium transition">
                                            Cari
                                        </button>
                                    </div>
                                    <flux:error name="kd_dokter_bedah" />

                                    {{-- Dropdown Search Dokter Bedah --}}
                                    <div x-show="open" @click.outside="open = false" class="absolute z-20 mt-1 w-full bg-white dark:bg-neutral-800 rounded-xl shadow-xl border border-neutral-200 dark:border-neutral-700 p-2 text-xs">
                                        <flux:input wire:model.live.debounce.250ms="dokterBedahSearch" placeholder="Ketik min 2 huruf..." size="sm" class="mb-2" />
                                        <div class="max-h-40 overflow-y-auto divide-y divide-neutral-100 dark:divide-neutral-700">
                                            @forelse($dokterBedahList as $dok)
                                                <div wire:click="selectDokterBedah('{{ $dok->kd_dokter }}', '{{ $dok->nm_dokter }}')" @click="open = false" class="p-2 hover:bg-neutral-50 dark:hover:bg-neutral-700/50 cursor-pointer rounded flex justify-between items-center">
                                                    <span class="font-medium text-neutral-800 dark:text-neutral-200">{{ $dok->nm_dokter }}</span>
                                                    <span class="text-[10px] text-neutral-400">{{ $dok->kd_dokter }}</span>
                                                </div>
                                            @empty
                                                <div class="p-2 text-center text-neutral-400">
                                                    {{ strlen($dokterBedahSearch) >= 2 ? 'Dokter tidak ditemukan' : 'Ketik min 2 huruf nama dokter' }}
                                                </div>
                                            @endforelse
                                        </div>
                                    </div>
                                </div>

                                {{-- Dokter Anestesi --}}
                                <div class="space-y-1 relative" x-data="{ open: false }">
                                    <flux:label class="text-xs font-semibold">Dokter Anestesi *</flux:label>
                                    <div class="flex items-center gap-2">
                                        <flux:input wire:model="nm_dokter_anestesi" readonly placeholder="Pilih Dokter Anestesi..." size="sm" class="flex-1 !bg-neutral-100/60 dark:!bg-neutral-800 cursor-not-allowed" />
                                        <button type="button" @click="open = !open" class="px-2.5 py-1.5 bg-[#4C5C2D] text-white rounded-lg hover:bg-[#3d4a24] text-xs font-medium transition">
                                            Cari
                                        </button>
                                    </div>
                                    <flux:error name="kd_dokter_anestesi" />

                                    {{-- Dropdown Search Dokter Anestesi --}}
                                    <div x-show="open" @click.outside="open = false" class="absolute z-20 mt-1 w-full bg-white dark:bg-neutral-800 rounded-xl shadow-xl border border-neutral-200 dark:border-neutral-700 p-2 text-xs">
                                        <flux:input wire:model.live.debounce.250ms="dokterAnestesiSearch" placeholder="Ketik min 2 huruf..." size="sm" class="mb-2" />
                                        <div class="max-h-40 overflow-y-auto divide-y divide-neutral-100 dark:divide-neutral-700">
                                            @forelse($dokterAnestesiList as $dok)
                                                <div wire:click="selectDokterAnestesi('{{ $dok->kd_dokter }}', '{{ $dok->nm_dokter }}')" @click="open = false" class="p-2 hover:bg-neutral-50 dark:hover:bg-neutral-700/50 cursor-pointer rounded flex justify-between items-center">
                                                    <span class="font-medium text-neutral-800 dark:text-neutral-200">{{ $dok->nm_dokter }}</span>
                                                    <span class="text-[10px] text-neutral-400">{{ $dok->kd_dokter }}</span>
                                                </div>
                                            @empty
                                                <div class="p-2 text-center text-neutral-400">
                                                    {{ strlen($dokterAnestesiSearch) >= 2 ? 'Dokter tidak ditemukan' : 'Ketik min 2 huruf nama dokter' }}
                                                </div>
                                            @endforelse
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- SEKSI 3: PERAWAT MELAKUKAN KONFIRMASI --}}
                        <div class="p-4 bg-white dark:bg-neutral-850 rounded-xl border border-neutral-200/80 dark:border-neutral-700 space-y-4">
                            <span class="text-xs font-bold text-[#4C5C2D] dark:text-[#8CC7C4] uppercase tracking-wider block border-b border-neutral-100 dark:border-neutral-700 pb-2">
                                Perawat Melakukan Konfirmasi
                            </span>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                                {{-- Identitas --}}
                                <div>
                                    <flux:label class="text-xs font-semibold">Identitas Pasien</flux:label>
                                    <flux:select wire:model="identitas" size="sm">
                                        <option value="Ya">Ya</option>
                                        <option value="Tidak">Tidak</option>
                                    </flux:select>
                                </div>

                                {{-- Keadaan Umum --}}
                                <div>
                                    <flux:label class="text-xs font-semibold">Keadaan Umum Pasien</flux:label>
                                    <flux:select wire:model="keadaan_umum" size="sm">
                                        <option value="Baik">Baik</option>
                                        <option value="Sedang">Sedang</option>
                                        <option value="Lemah">Lemah</option>
                                    </flux:select>
                                </div>

                                {{-- Penandaan Area Operasi --}}
                                <div>
                                    <flux:label class="text-xs font-semibold">Penandaan Area Operasi</flux:label>
                                    <flux:select wire:model="penandaan_area_operasi" size="sm">
                                        <option value="Ada">Ada</option>
                                        <option value="Tidak Ada">Tidak Ada</option>
                                        <option value="Tidak Diperlukan">Tidak Diperlukan</option>
                                    </flux:select>
                                </div>

                                {{-- Surat Ijin Bedah --}}
                                <div>
                                    <flux:label class="text-xs font-semibold">Surat Ijin Bedah</flux:label>
                                    <flux:select wire:model="surat_ijin_bedah" size="sm">
                                        <option value="Ada">Ada</option>
                                        <option value="Tidak Ada">Tidak Ada</option>
                                    </flux:select>
                                </div>

                                {{-- Surat Ijin Anestesi --}}
                                <div>
                                    <flux:label class="text-xs font-semibold">Surat Ijin Anestesi</flux:label>
                                    <flux:select wire:model="surat_ijin_anestesi" size="sm">
                                        <option value="Ada">Ada</option>
                                        <option value="Tidak Ada">Tidak Ada</option>
                                    </flux:select>
                                </div>

                                {{-- Surat Ijin Tranfusi --}}
                                <div>
                                    <flux:label class="text-xs font-semibold">Surat Ijin Tranfusi</flux:label>
                                    <flux:select wire:model="surat_ijin_transfusi" size="sm">
                                        <option value="Ada">Ada</option>
                                        <option value="Tidak Ada">Tidak Ada</option>
                                        <option value="Tidak Diperlukan">Tidak Diperlukan</option>
                                    </flux:select>
                                </div>

                                {{-- Persiapan Darah + Keterangan --}}
                                <div class="sm:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div>
                                        <flux:label class="text-xs font-semibold">Persiapan Darah</flux:label>
                                        <flux:select wire:model="persiapan_darah" size="sm">
                                            <option value="Ada">Ada</option>
                                            <option value="Tidak Ada">Tidak Ada</option>
                                            <option value="Tidak Diperlukan">Tidak Diperlukan</option>
                                        </flux:select>
                                    </div>
                                    <div>
                                        <flux:label class="text-xs font-semibold">Keterangan Darah (Jml/Golongan)</flux:label>
                                        <flux:input wire:model="keterangan_persiapan_darah" placeholder="Misal: 2 kolf PRC" size="sm" />
                                    </div>
                                </div>

                                {{-- Perlengkapan Khusus --}}
                                <div class="sm:col-span-2">
                                    <flux:label class="text-xs font-semibold">Perlengkapan Khusus, Alat/Implan</flux:label>
                                    <flux:select wire:model="perlengkapan_khusus" size="sm">
                                        <option value="Ada">Ada</option>
                                        <option value="Tidak Ada">Tidak Ada</option>
                                        <option value="Tidak Diperlukan">Tidak Diperlukan</option>
                                    </flux:select>
                                </div>
                            </div>
                        </div>

                        {{-- SEKSI 4: HASIL PEMERIKSAAN PENUNJANG --}}
                        <div class="p-4 bg-white dark:bg-neutral-850 rounded-xl border border-neutral-200/80 dark:border-neutral-700 space-y-4">
                            <span class="text-xs font-bold text-[#4C5C2D] dark:text-[#8CC7C4] uppercase tracking-wider block border-b border-neutral-100 dark:border-neutral-700 pb-2">
                                Hasil Pemeriksaan Penunjang
                            </span>

                            <div class="space-y-3">
                                {{-- Radiologi --}}
                                <div class="grid grid-cols-1 sm:grid-cols-12 gap-2 items-center">
                                    <div class="sm:col-span-3 text-xs font-semibold text-neutral-700 dark:text-neutral-300">
                                        Radiologi / Rontgen
                                    </div>
                                    <div class="sm:col-span-4">
                                        <flux:select wire:model="pemeriksaan_penunjang_rontgen" size="sm">
                                            <option value="Ada">Ada</option>
                                            <option value="Tidak Ada">Tidak Ada</option>
                                            <option value="Tidak Diperlukan">Tidak Diperlukan</option>
                                        </flux:select>
                                    </div>
                                    <div class="sm:col-span-5">
                                        <flux:input wire:model="keterangan_pemeriksaan_penunjang_rontgen" placeholder="Keterangan hasil..." size="sm" />
                                    </div>
                                </div>

                                {{-- EKG --}}
                                <div class="grid grid-cols-1 sm:grid-cols-12 gap-2 items-center">
                                    <div class="sm:col-span-3 text-xs font-semibold text-neutral-700 dark:text-neutral-300">
                                        EKG
                                    </div>
                                    <div class="sm:col-span-4">
                                        <flux:select wire:model="pemeriksaan_penunjang_ekg" size="sm">
                                            <option value="Ada">Ada</option>
                                            <option value="Tidak Ada">Tidak Ada</option>
                                            <option value="Tidak Diperlukan">Tidak Diperlukan</option>
                                        </flux:select>
                                    </div>
                                    <div class="sm:col-span-5">
                                        <flux:input wire:model="keterangan_pemeriksaan_penunjang_ekg" placeholder="Keterangan hasil..." size="sm" />
                                    </div>
                                </div>

                                {{-- USG --}}
                                <div class="grid grid-cols-1 sm:grid-cols-12 gap-2 items-center">
                                    <div class="sm:col-span-3 text-xs font-semibold text-neutral-700 dark:text-neutral-300">
                                        USG
                                    </div>
                                    <div class="sm:col-span-4">
                                        <flux:select wire:model="pemeriksaan_penunjang_usg" size="sm">
                                            <option value="Ada">Ada</option>
                                            <option value="Tidak Ada">Tidak Ada</option>
                                            <option value="Tidak Diperlukan">Tidak Diperlukan</option>
                                        </flux:select>
                                    </div>
                                    <div class="sm:col-span-5">
                                        <flux:input wire:model="keterangan_pemeriksaan_penunjang_usg" placeholder="Keterangan hasil..." size="sm" />
                                    </div>
                                </div>

                                {{-- CT Scan --}}
                                <div class="grid grid-cols-1 sm:grid-cols-12 gap-2 items-center">
                                    <div class="sm:col-span-3 text-xs font-semibold text-neutral-700 dark:text-neutral-300">
                                        CT Scan
                                    </div>
                                    <div class="sm:col-span-4">
                                        <flux:select wire:model="pemeriksaan_penunjang_ctscan" size="sm">
                                            <option value="Ada">Ada</option>
                                            <option value="Tidak Ada">Tidak Ada</option>
                                            <option value="Tidak Diperlukan">Tidak Diperlukan</option>
                                        </flux:select>
                                    </div>
                                    <div class="sm:col-span-5">
                                        <flux:input wire:model="keterangan_pemeriksaan_penunjang_ctscan" placeholder="Keterangan hasil..." size="sm" />
                                    </div>
                                </div>

                                {{-- MRI --}}
                                <div class="grid grid-cols-1 sm:grid-cols-12 gap-2 items-center">
                                    <div class="sm:col-span-3 text-xs font-semibold text-neutral-700 dark:text-neutral-300">
                                        MRI
                                    </div>
                                    <div class="sm:col-span-4">
                                        <flux:select wire:model="pemeriksaan_penunjang_mri" size="sm">
                                            <option value="Ada">Ada</option>
                                            <option value="Tidak Ada">Tidak Ada</option>
                                            <option value="Tidak Diperlukan">Tidak Diperlukan</option>
                                        </flux:select>
                                    </div>
                                    <div class="sm:col-span-5">
                                        <flux:input wire:model="keterangan_pemeriksaan_penunjang_mri" placeholder="Keterangan hasil..." size="sm" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- SEKSI 5: TIM PETUGAS (RUANGAN & OK) --}}
                        <div class="p-4 bg-neutral-50/70 dark:bg-neutral-900/40 rounded-xl border border-neutral-200/80 dark:border-neutral-700 space-y-4">
                            <span class="text-xs font-bold text-neutral-800 dark:text-neutral-200 uppercase tracking-wider block border-b border-neutral-200/60 dark:border-neutral-700/60 pb-2">
                                Petugas Verifikator
                            </span>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                {{-- Petugas Ruangan --}}
                                <div class="space-y-1 relative" x-data="{ open: false }">
                                    <flux:label class="text-xs font-semibold">Petugas Ruangan</flux:label>
                                    <div class="flex items-center gap-2">
                                        <flux:input wire:model="nm_petugas_ruangan" readonly placeholder="Pilih Petugas Ruangan..." size="sm" class="flex-1 !bg-neutral-100/60 dark:!bg-neutral-800 cursor-not-allowed" />
                                        <button type="button" @click="open = !open" class="px-2.5 py-1.5 bg-[#4C5C2D] text-white rounded-lg hover:bg-[#3d4a24] text-xs font-medium transition">
                                            Cari
                                        </button>
                                    </div>

                                    {{-- Dropdown Search Petugas Ruangan --}}
                                    <div x-show="open" @click.outside="open = false" class="absolute z-20 mt-1 w-full bg-white dark:bg-neutral-800 rounded-xl shadow-xl border border-neutral-200 dark:border-neutral-700 p-2 text-xs">
                                        <flux:input wire:model.live.debounce.250ms="petugasRuanganSearch" placeholder="Ketik min 2 huruf..." size="sm" class="mb-2" />
                                        <div class="max-h-40 overflow-y-auto divide-y divide-neutral-100 dark:divide-neutral-700">
                                            @forelse($petugasRuanganList as $p)
                                                <div wire:click="selectPetugasRuangan('{{ $p->nip }}', '{{ $p->nama }}')" @click="open = false" class="p-2 hover:bg-neutral-50 dark:hover:bg-neutral-700/50 cursor-pointer rounded flex justify-between items-center">
                                                    <span class="font-medium text-neutral-800 dark:text-neutral-200">{{ $p->nama }}</span>
                                                    <span class="text-[10px] text-neutral-400">{{ $p->nip }}</span>
                                                </div>
                                            @empty
                                                <div class="p-2 text-center text-neutral-400">
                                                    {{ strlen($petugasRuanganSearch) >= 2 ? 'Petugas tidak ditemukan' : 'Ketik min 2 huruf nama petugas' }}
                                                </div>
                                            @endforelse
                                        </div>
                                    </div>
                                </div>

                                {{-- Perawat OK --}}
                                <div class="space-y-1 relative" x-data="{ open: false }">
                                    <flux:label class="text-xs font-semibold">Petugas OK</flux:label>
                                    <div class="flex items-center gap-2">
                                        <flux:input wire:model="nm_perawat_ok" readonly placeholder="Pilih Petugas OK..." size="sm" class="flex-1 !bg-neutral-100/60 dark:!bg-neutral-800 cursor-not-allowed" />
                                        <button type="button" @click="open = !open" class="px-2.5 py-1.5 bg-[#4C5C2D] text-white rounded-lg hover:bg-[#3d4a24] text-xs font-medium transition">
                                            Cari
                                        </button>
                                    </div>

                                    {{-- Dropdown Search Perawat OK --}}
                                    <div x-show="open" @click.outside="open = false" class="absolute z-20 mt-1 w-full bg-white dark:bg-neutral-800 rounded-xl shadow-xl border border-neutral-200 dark:border-neutral-700 p-2 text-xs">
                                        <flux:input wire:model.live.debounce.250ms="perawatOkSearch" placeholder="Ketik min 2 huruf..." size="sm" class="mb-2" />
                                        <div class="max-h-40 overflow-y-auto divide-y divide-neutral-100 dark:divide-neutral-700">
                                            @forelse($perawatOkList as $p)
                                                <div wire:click="selectPerawatOk('{{ $p->nip }}', '{{ $p->nama }}')" @click="open = false" class="p-2 hover:bg-neutral-50 dark:hover:bg-neutral-700/50 cursor-pointer rounded flex justify-between items-center">
                                                    <span class="font-medium text-neutral-800 dark:text-neutral-200">{{ $p->nama }}</span>
                                                    <span class="text-[10px] text-neutral-400">{{ $p->nip }}</span>
                                                </div>
                                            @empty
                                                <div class="p-2 text-center text-neutral-400">
                                                    {{ strlen($perawatOkSearch) >= 2 ? 'Petugas tidak ditemukan' : 'Ketik min 2 huruf nama petugas' }}
                                                </div>
                                            @endforelse
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </form>
                </div>

                {{-- Panel Footer (Action Buttons) --}}
                <div class="px-6 py-4 border-t border-neutral-100 dark:border-neutral-700 flex items-center justify-end gap-3 bg-neutral-50/70 dark:bg-neutral-900/60">
                    <flux:button type="button" @click="open = false; $wire.isPanelOpen = false" variant="ghost">Batal</flux:button>
                    <flux:button type="submit" form="checklistForm" variant="filled" class="!bg-[#4C5C2D] !text-white hover:!bg-[#3d4a24]">
                        Simpan Checklist
                    </flux:button>
                </div>

            </div>
        </div>
    </div>
