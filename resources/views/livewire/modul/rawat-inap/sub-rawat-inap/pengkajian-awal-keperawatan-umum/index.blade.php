<div class="flex flex-col gap-6 pb-8">
    {{-- Header / Breadcrumb --}}
    <div class="flex flex-col gap-1">
        <div class="flex items-center gap-3">
            <a href="{{ route('modul.rawat-inap.perawatan-tindakan', str_replace('/', '-', $regPeriksa->no_rawat)) }}" wire:navigate
               class="flex items-center justify-center w-8 h-8 rounded-lg transition-colors hover:bg-neutral-100 dark:hover:bg-neutral-700">
                <flux:icon name="chevron-left" class="w-5 h-5 text-neutral-500" />
            </a>
            <div>
                <nav class="text-xs text-neutral-400 mb-0.5">
                    <a href="{{ route('modul.rawat-inap.perawatan-tindakan', str_replace('/', '-', $regPeriksa->no_rawat)) }}" wire:navigate class="hover:underline">Perawatan & Tindakan</a>
                    <span class="mx-1">/</span>
                    <span>Pengkajian Awal Keperawatan Umum</span>
                </nav>
                <h1 class="text-xl font-bold text-neutral-800 dark:text-neutral-100">Pengkajian Awal Keperawatan Umum</h1>
            </div>
        </div>
    </div>

    {{-- Patient Info Card --}}
    <div class="bg-white dark:bg-neutral-800 rounded-xl border border-neutral-200 dark:border-neutral-700 p-4 shadow-sm flex items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-[#4C5C2D]/10 dark:bg-[#4C5C2D]/30 flex items-center justify-center flex-shrink-0">
                <flux:icon name="user" class="w-6 h-6 text-[#4C5C2D] dark:text-[#8CC7C4]" />
            </div>
            <div>
                <h2 class="font-bold text-lg text-neutral-800 dark:text-neutral-100 leading-tight">
                    {{ $regPeriksa->pasien->nm_pasien ?? '-' }}
                </h2>
                <div class="flex flex-wrap items-center gap-x-3 gap-y-1 mt-1 text-sm text-neutral-500">
                    <span class="font-mono bg-neutral-100 dark:bg-neutral-900 px-1.5 py-0.5 rounded">{{ $regPeriksa->no_rkm_medis }}</span>
                    <span>•</span>
                    <span>{{ $regPeriksa->pasien->jk === 'L' ? 'Laki-laki' : 'Perempuan' }}</span>
                    <span>•</span>
                    <span>{{ $regPeriksa->pasien->tgl_lahir ? \Carbon\Carbon::parse($regPeriksa->pasien->tgl_lahir)->age . ' Thn' : '-' }}</span>
                </div>
            </div>
        </div>
        <div class="text-right">
            <div class="text-sm text-neutral-500 mb-1">No. Rawat</div>
            <div class="font-mono font-bold text-[#4C5C2D] dark:text-[#8CC7C4]">{{ $regPeriksa->no_rawat }}</div>
            <div class="text-xs text-neutral-500 mt-1">Kamar: {{ $regPeriksa->kamarInap->last()->kamar->kd_kamar ?? '-' }} ({{ $regPeriksa->kamarInap->last()->kamar->bangsal->nm_bangsal ?? '-' }})</div>
        </div>
    </div>

    {{-- Data Table --}}
    <div class="bg-white dark:bg-neutral-800 rounded-xl border border-neutral-200 dark:border-neutral-700 shadow-sm overflow-hidden flex flex-col mt-2 relative p-4">
        <div class="flex items-center justify-between mb-4 px-2">
            <h3 class="text-sm font-bold text-neutral-700 dark:text-neutral-300 uppercase tracking-wider flex items-center gap-2">
                <flux:icon name="list-bullet" class="w-4 h-4 text-[#4C5C2D]" />
                Daftar Pengkajian Awal Keperawatan Umum
            </h3>
            @if(empty($pengkajianList))
                <flux:button href="{{ route('modul.rawat-inap.sub-rawat-inap.pengkajian-awal-keperawatan-umum.form', str_replace('/', '-', $regPeriksa->no_rawat)) }}" wire:navigate icon="plus" variant="filled" size="sm" class="!flex !flex-row !items-center !bg-[#4C5C2D] !text-white hover:!bg-[#3d4a24]">
                    Tambah Pengkajian
                </flux:button>
            @endif
        </div>

        <flux:table class="whitespace-nowrap mt-2">
            <flux:table.columns>
                <flux:table.column class="!pl-6">Tanggal</flux:table.column>
                <flux:table.column>Jam</flux:table.column>
                <flux:table.column>Petugas 1</flux:table.column>
                <flux:table.column>Petugas 2</flux:table.column>
                <flux:table.column><div class="w-full text-center">Action</div></flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse ($pengkajianList as $item)
                    <flux:table.row>
                        <flux:table.cell class="!pl-6 font-medium text-neutral-900 dark:text-neutral-100">
                            {{ \Carbon\Carbon::parse($item['tanggal'])->format('d/m/Y') }}
                        </flux:table.cell>
                        <flux:table.cell class="text-neutral-600 dark:text-neutral-400">
                            {{ \Carbon\Carbon::parse($item['tanggal'])->format('H:i:s') }}
                        </flux:table.cell>
                        <flux:table.cell class="text-xs">
                            <div class="font-medium text-neutral-800">{{ $item['petugas1']['nama'] ?? '-' }}</div>
                            <div class="text-neutral-500 font-mono">{{ $item['nip1'] }}</div>
                        </flux:table.cell>
                        <flux:table.cell class="text-xs">
                            <div class="font-medium text-neutral-800">{{ $item['petugas2']['nama'] ?? '-' }}</div>
                            <div class="text-neutral-500 font-mono">{{ $item['nip2'] }}</div>
                        </flux:table.cell>
                        <flux:table.cell>
                            <div class="flex justify-center gap-2">
                                <flux:button wire:click="viewData('{{ $item['no_rawat'] }}')" icon="eye" size="xs" variant="ghost" class="text-blue-500 hover:bg-blue-50 dark:hover:bg-blue-500/10" />
                                <flux:button href="{{ route('modul.rawat-inap.sub-rawat-inap.pengkajian-awal-keperawatan-umum.form', str_replace('/', '-', $item['no_rawat'])) }}" wire:navigate icon="pencil-square" size="xs" variant="ghost" />
                                <flux:button
                                    @click="
                                        Swal.fire({
                                            title: 'Hapus Pengkajian?',
                                            text: 'Data pengkajian ini akan dihapus secara permanen.',
                                            icon: 'warning',
                                            showCancelButton: true,
                                            confirmButtonColor: '#dc2626',
                                            cancelButtonColor: '#6b7280',
                                            confirmButtonText: 'Ya, Hapus!',
                                            cancelButtonText: 'Batal'
                                        }).then((result) => {
                                            if (result.isConfirmed) {
                                                $wire.delete('{{ $item['no_rawat'] }}');
                                            }
                                        })
                                    "
                                    icon="trash" size="xs" variant="ghost" class="!text-red-500 hover:!bg-red-50 dark:hover:!bg-red-500/10" />
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="5">
                            <div class="flex flex-col items-center justify-center py-12 text-center">
                                <flux:icon name="inbox" class="w-12 h-12 text-neutral-300 dark:text-neutral-600 mb-4" />
                                <h3 class="text-lg font-bold text-neutral-700 dark:text-neutral-300">Belum Ada Data Pengkajian</h3>
                                <p class="text-neutral-500 mt-1 max-w-md">Belum ada data pengkajian awal keperawatan umum untuk pasien ini.</p>
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </div>

    {{-- Modal View Detail (SOP #6: Alpine.js Modal) --}}
    <div x-data="{ open: @entangle('isDetailModalOpen') }"
         x-show="open" 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 overflow-y-auto"
         style="display: none;">
         
         {{-- Backdrop --}}
         <div x-show="open" 
              x-transition:enter="transition ease-out duration-300"
              x-transition:enter-start="opacity-0"
              x-transition:enter-end="opacity-100"
              x-transition:leave="transition ease-in duration-200"
              x-transition:leave-start="opacity-100"
              x-transition:leave-end="opacity-0"
              class="fixed inset-0 bg-neutral-900/60 backdrop-blur-sm" 
              @click="open = false"></div>

         {{-- Modal Content --}}
         <div x-show="open"
              x-transition:enter="transition ease-out duration-300"
              x-transition:enter-start="opacity-0 scale-95 translate-y-4"
              x-transition:enter-end="opacity-100 scale-100 translate-y-0"
              x-transition:leave="transition ease-in duration-200"
              x-transition:leave-start="opacity-100 scale-100 translate-y-0"
              x-transition:leave-end="opacity-0 scale-95 translate-y-4"
              class="relative w-full max-w-4xl bg-white dark:bg-neutral-800 rounded-3xl shadow-2xl overflow-hidden border border-neutral-200 dark:border-neutral-700 max-h-[90vh] flex flex-col">
              
              {{-- Header --}}
              <div class="px-8 py-6 border-b border-neutral-100 dark:border-neutral-700 flex items-center justify-between bg-neutral-50/50 dark:bg-neutral-900/50 shrink-0">
                  <div class="flex items-center gap-4">
                      <div class="w-10 h-10 rounded-xl bg-[#4C5C2D]/10 flex items-center justify-center">
                          <flux:icon name="document-text" class="w-5 h-5 text-[#4C5C2D]" />
                      </div>
                      <div>
                          <h3 class="text-lg font-bold text-neutral-800 dark:text-neutral-100">Detail Pengkajian Umum</h3>
                          <p class="text-xs text-neutral-500">Rekam medis pengkajian awal keperawatan rawat inap</p>
                      </div>
                  </div>
                  <button @click="open = false" class="text-neutral-400 hover:text-neutral-600 dark:hover:text-neutral-200 transition-colors">
                      <flux:icon name="x-mark" class="w-6 h-6" />
                  </button>
              </div>

              {{-- Body --}}
              <div class="p-8 overflow-y-auto bg-white dark:bg-neutral-800 space-y-8">
                 @if($detailData)
                     {{-- 1. Identitas --}}
                     <div>
                         <h4 class="text-xs font-black text-[#4C5C2D] uppercase tracking-wider mb-3 border-b border-neutral-200 dark:border-neutral-700 pb-2">1. Informasi Pengkajian</h4>
                         <div class="grid grid-cols-2 md:grid-cols-4 gap-4 p-4 rounded-xl bg-neutral-50 dark:bg-neutral-900 border border-neutral-100 dark:border-neutral-800">
                             <div>
                                 <div class="text-[10px] text-neutral-400 font-bold uppercase tracking-wider mb-1">Tanggal & Jam</div>
                                 <div class="text-sm font-medium text-neutral-800 dark:text-neutral-200">{{ \Carbon\Carbon::parse($detailData['tanggal'])->format('d/m/Y H:i:s') }}</div>
                             </div>
                             <div>
                                 <div class="text-[10px] text-neutral-400 font-bold uppercase tracking-wider mb-1">Informasi Dari</div>
                                 <div class="text-sm font-medium text-neutral-800 dark:text-neutral-200">{{ $detailData['informasi'] }}</div>
                             </div>
                             <div class="col-span-2">
                                 <div class="text-[10px] text-neutral-400 font-bold uppercase tracking-wider mb-1">Petugas</div>
                                 <div class="text-sm font-medium text-neutral-800 dark:text-neutral-200">{{ $detailData['petugas1']['nama'] ?? $detailData['nip1'] }} & {{ $detailData['petugas2']['nama'] ?? $detailData['nip2'] }}</div>
                             </div>
                         </div>
                     </div>

                     {{-- 2. Riwayat Kesehatan --}}
                     <div>
                         <h4 class="text-xs font-black text-[#4C5C2D] uppercase tracking-wider mb-3 border-b border-neutral-200 dark:border-neutral-700 pb-2">2. Riwayat Kesehatan</h4>
                         <div class="space-y-4">
                             <div>
                                 <div class="text-[10px] text-neutral-400 font-bold uppercase tracking-wider mb-1">Riwayat Penyakit Sekarang (RPS)</div>
                                 <div class="text-sm text-neutral-800 dark:text-neutral-200">{{ $detailData['rps'] ?? '-' }}</div>
                             </div>
                             <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                 <div>
                                     <div class="text-[10px] text-neutral-400 font-bold uppercase tracking-wider mb-1">Riwayat Penyakit Dahulu</div>
                                     <div class="text-sm text-neutral-800 dark:text-neutral-200">{{ $detailData['rpd'] ?? '-' }}</div>
                                 </div>
                                 <div>
                                     <div class="text-[10px] text-neutral-400 font-bold uppercase tracking-wider mb-1">Riwayat Penggunaan Obat</div>
                                     <div class="text-sm text-neutral-800 dark:text-neutral-200">{{ $detailData['rpo'] ?? '-' }}</div>
                                 </div>
                             </div>
                         </div>
                     </div>

                     {{-- 3. Tanda Vital --}}
                     <div>
                         <h4 class="text-xs font-black text-[#4C5C2D] uppercase tracking-wider mb-3 border-b border-neutral-200 dark:border-neutral-700 pb-2">3. Tanda Vital</h4>
                         <div class="grid grid-cols-2 md:grid-cols-4 gap-y-4 gap-x-4">
                             <div><span class="block text-[10px] text-neutral-500 uppercase">GCS</span><span class="text-sm font-medium">{{ $detailData['pemeriksaan_gcs'] ?? '-' }}</span></div>
                             <div><span class="block text-[10px] text-neutral-500 uppercase">Tekanan Darah</span><span class="text-sm font-medium">{{ $detailData['pemeriksaan_td'] ?? '-' }}</span></div>
                             <div><span class="block text-[10px] text-neutral-500 uppercase">Nadi (HR)</span><span class="text-sm font-medium">{{ $detailData['pemeriksaan_nadi'] ?? '-' }}</span></div>
                             <div><span class="block text-[10px] text-neutral-500 uppercase">Suhu</span><span class="text-sm font-medium">{{ $detailData['pemeriksaan_suhu'] ?? '-' }}</span></div>
                             
                             <div><span class="block text-[10px] text-neutral-500 uppercase">Keadaan Umum</span><span class="text-sm font-medium">{{ $detailData['pemeriksaan_keadaan_umum'] ?? '-' }}</span></div>
                             <div><span class="block text-[10px] text-neutral-500 uppercase">Pernafasan (RR)</span><span class="text-sm font-medium">{{ $detailData['pemeriksaan_rr'] ?? '-' }}</span></div>
                             <div><span class="block text-[10px] text-neutral-500 uppercase">SpO2</span><span class="text-sm font-medium">{{ $detailData['pemeriksaan_spo2'] ?? '-' }}</span></div>
                             <div><span class="block text-[10px] text-neutral-500 uppercase">Tinggi / Berat</span><span class="text-sm font-medium">{{ $detailData['pemeriksaan_tb'] ?? '-' }} / {{ $detailData['pemeriksaan_bb'] ?? '-' }}</span></div>
                         </div>
                     </div>

                     {{-- 4. Rencana Keperawatan --}}
                     <div>
                         <h4 class="text-xs font-black text-[#4C5C2D] uppercase tracking-wider mb-3 border-b border-neutral-200 dark:border-neutral-700 pb-2">4. Masalah & Rencana Keperawatan</h4>
                         
                         <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                             <div class="border border-neutral-200 dark:border-neutral-700 rounded-xl overflow-hidden">
                                 <div class="bg-neutral-50 dark:bg-neutral-900/50 px-4 py-2 border-b border-neutral-200 dark:border-neutral-700">
                                     <span class="text-[10px] font-bold text-neutral-600 uppercase tracking-wider">Masalah Keperawatan</span>
                                 </div>
                                 <ul class="list-disc pl-8 py-3 space-y-1 text-sm text-neutral-700 dark:text-neutral-300">
                                     @forelse($detailData['masalah'] ?? [] as $m)
                                         <li>{{ $m['nama_masalah'] }}</li>
                                     @empty
                                         <li class="text-neutral-400 italic list-none -ml-4">Tidak ada masalah</li>
                                     @endforelse
                                 </ul>
                             </div>
                             <div class="border border-neutral-200 dark:border-neutral-700 rounded-xl overflow-hidden">
                                 <div class="bg-neutral-50 dark:bg-neutral-900/50 px-4 py-2 border-b border-neutral-200 dark:border-neutral-700">
                                     <span class="text-[10px] font-bold text-neutral-600 uppercase tracking-wider">Rencana Keperawatan</span>
                                 </div>
                                 <ul class="list-disc pl-8 py-3 space-y-1 text-sm text-neutral-700 dark:text-neutral-300">
                                     @forelse($detailData['detail_rencana'] ?? [] as $r)
                                         <li>{{ $r['rencana_keperawatan'] }}</li>
                                     @empty
                                         <li class="text-neutral-400 italic list-none -ml-4">Tidak ada rencana</li>
                                     @endforelse
                                 </ul>
                                 @if(!empty($detailData['rencana']))
                                     <div class="px-4 py-2 text-sm text-neutral-700 dark:text-neutral-300 border-t border-neutral-100 dark:border-neutral-700 bg-neutral-50/30">
                                         <span class="font-bold block text-[10px] uppercase text-neutral-500 mb-1">Rencana Lainnya:</span>
                                         {{ $detailData['rencana'] }}
                                     </div>
                                 @endif
                             </div>
                         </div>
                     </div>
                 @else
                     <div class="flex items-center justify-center p-8">
                         <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-[#4C5C2D]"></div>
                     </div>
                 @endif
              </div>
              
              {{-- Footer --}}
              <div class="px-8 py-6 bg-neutral-50/50 dark:bg-neutral-900/50 border-t border-neutral-100 dark:border-neutral-700 flex justify-end gap-3 shrink-0">
                  <flux:button @click="open = false" variant="filled" class="!bg-[#4C5C2D] !text-white hover:!bg-[#3d4a24]">Tutup</flux:button>
              </div>
         </div>
    </div>
</div>
