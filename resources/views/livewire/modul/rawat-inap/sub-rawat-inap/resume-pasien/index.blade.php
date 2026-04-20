<div class="flex flex-col gap-6 pb-8">
    {{-- Header / Breadcrumb --}}
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('modul.rawat-inap.perawatan-tindakan', str_replace('/', '-', $no_rawat)) }}" wire:navigate
               class="flex items-center justify-center w-8 h-8 rounded-lg transition-colors hover:bg-neutral-100 dark:hover:bg-neutral-700">
                <flux:icon name="chevron-left" class="w-5 h-5 text-neutral-500" />
            </a>
            <div>
                <nav class="text-xs text-neutral-400 mb-0.5">
                    <a href="{{ route('modul.index') }}" wire:navigate class="hover:underline">Modul</a>
                    <span class="mx-1">/</span>
                    <a href="{{ route('modul.rawat-inap.index') }}" wire:navigate class="hover:underline">Rawat Inap</a>
                    <span class="mx-1">/</span>
                    <a href="{{ route('modul.rawat-inap.perawatan-tindakan', str_replace('/', '-', $no_rawat)) }}" wire:navigate class="hover:underline">Perawatan/Tindakan</a>
                    <span class="mx-1">/</span>
                    <span>Resume Medis</span>
                </nav>
                <h1 class="text-xl font-bold text-neutral-800 dark:text-neutral-100">Data Resume Medis Pasien</h1>
            </div>
        </div>
    </div>

    {{-- Patient Summary Card --}}
    <div class="bg-white dark:bg-neutral-800 rounded-2xl border border-neutral-200 dark:border-neutral-700 p-5 shadow-sm overflow-hidden relative">
        <div class="absolute top-0 right-0 p-8 opacity-5">
            <flux:icon name="clipboard-document-list" class="w-32 h-32 text-[#4C5C2D]" />
        </div>
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="flex items-center gap-5">
                <div class="w-14 h-14 rounded-2xl bg-[#4C5C2D]/10 dark:bg-[#4C5C2D]/30 flex items-center justify-center text-[#4C5C2D] dark:text-[#8CC7C4]">
                    <flux:icon name="user" class="w-7 h-7" />
                </div>
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <span class="text-xs font-bold uppercase tracking-widest text-[#4C5C2D] dark:text-[#8CC7C4]">{{ $regPeriksa->no_rkm_medis }}</span>
                        <span class="w-1 h-1 rounded-full bg-neutral-300"></span>
                        <span class="text-xs font-medium text-neutral-500">Pasien Rawat Inap</span>
                    </div>
                    <h2 class="text-2xl font-black text-neutral-800 dark:text-neutral-100 uppercase tracking-tight">{{ $regPeriksa->pasien->nm_pasien ?? '-' }}</h2>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <div class="px-4 py-2 rounded-xl bg-neutral-50 dark:bg-neutral-900/50 border border-neutral-100 dark:border-neutral-700 flex flex-col items-center">
                    <span class="text-[10px] uppercase font-bold text-neutral-400 leading-none mb-1">No. Rawat</span>
                    <span class="text-sm font-bold font-mono text-[#4C5C2D] dark:text-[#8CC7C4]">{{ $no_rawat }}</span>
                </div>
                <div class="px-4 py-2 rounded-xl bg-neutral-50 dark:bg-neutral-900/50 border border-neutral-100 dark:border-neutral-700 flex flex-col items-center">
                    <span class="text-[10px] uppercase font-bold text-neutral-400 leading-none mb-1">DPJP Utama</span>
                    <span class="text-sm font-bold text-neutral-700 dark:text-neutral-300">{{ $regPeriksa->dokter->nm_dokter ?? '-' }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Main List Card --}}
    <div class="bg-white dark:bg-neutral-800 rounded-2xl border border-neutral-200 dark:border-neutral-700 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-neutral-100 dark:border-neutral-700 bg-neutral-50/50 dark:bg-neutral-900/20 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <h3 class="font-bold text-neutral-800 dark:text-neutral-100">Daftar Resume Medis</h3>
                <span class="px-2.5 py-1 rounded-full bg-[#4C5C2D] text-white text-[10px] font-bold uppercase tracking-wider">{{ $resumes->total() }} Total</span>
            </div>
            
            <a href="{{ route('modul.rawat-inap.sub-rawat-inap.resume-create', str_replace('/', '-', $no_rawat)) }}" wire:navigate>
                <flux:button variant="primary" icon="plus" class="bg-[#4C5C2D] hover:bg-[#3D4A24] text-[11px] h-8 font-bold uppercase tracking-wider">
                    Buat Resume Baru
                </flux:button>
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-neutral-50 dark:bg-neutral-900/50 text-neutral-500 uppercase font-black text-[10px] tracking-widest border-b border-neutral-200 dark:border-neutral-700">
                        <th class="px-6 py-4">No. Rawat</th>
                        <th class="px-6 py-4">No. RM</th>
                        <th class="px-6 py-4">Nama Pasien</th>
                        <th class="px-6 py-4">DPJP</th>
                        <th class="px-6 py-4">Dokter Pengirim</th>
                        <th class="px-6 py-4">Kamar</th>
                        <th class="px-6 py-4">Tgl Masuk</th>
                        <th class="px-6 py-4">Tgl Keluar</th>
                        <th class="px-6 py-4 text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100 dark:divide-neutral-700">
                    @forelse($resumes as $resume)
                        @php
                            $room = $resume->regPeriksa->kamarInap->first();
                            // Fallback for Dokter Pengirim: use rujukMasuk or admission doctor
                            $pengirim = $resume->regPeriksa->rujukMasuk->perujuk 
                                     ?? $resume->regPeriksa->rujukMasuk->dokter_perujuk 
                                     ?? $resume->regPeriksa->dokter->nm_dokter 
                                     ?? '-';
                        @endphp
                        <tr class="hover:bg-neutral-50/50 dark:hover:bg-neutral-800/10 transition-colors group">
                            <td class="px-6 py-4 font-mono text-xs font-bold text-[#4C5C2D] dark:text-[#8CC7C4]">{{ $resume->no_rawat }}</td>
                            <td class="px-6 py-4 text-xs font-semibold text-neutral-500">{{ $resume->regPeriksa->no_rkm_medis }}</td>
                            <td class="px-6 py-4">
                                <span class="text-xs font-bold text-neutral-800 dark:text-neutral-100 truncate block max-w-[150px] uppercase">{{ $resume->regPeriksa->pasien->nm_pasien ?? '-' }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-xs font-medium text-neutral-600 dark:text-neutral-400">{{ $resume->regPeriksa->dokter->nm_dokter ?? '-' }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-xs text-neutral-500 italic">{{ $pengirim }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-0.5 rounded-md bg-neutral-100 dark:bg-neutral-700 text-[10px] font-bold text-neutral-600 dark:text-neutral-300 font-mono">{{ $room->kd_kamar ?? '-' }}</span>
                            </td>
                            <td class="px-6 py-4 text-xs tabular-nums text-neutral-600 dark:text-neutral-400">
                                {{ $room ? \Carbon\Carbon::parse($room->tgl_masuk)->format('d/m/Y') : '-' }}
                            </td>
                            <td class="px-6 py-4 text-xs tabular-nums text-neutral-600 dark:text-neutral-400">
                                {{ ($room && $room->tgl_keluar != '0000-00-00') ? \Carbon\Carbon::parse($room->tgl_keluar)->format('d/m/Y') : '-' }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('modul.rawat-inap.sub-rawat-inap.resume-detail', str_replace('/', '-', $resume->no_rawat)) }}" wire:navigate
                                       title="Detail Resume" class="p-2 rounded-lg bg-neutral-100 dark:bg-neutral-700 text-neutral-500 hover:bg-[#4C5C2D]/10 hover:text-[#4C5C2D] dark:hover:text-[#8CC7C4] transition-all">
                                        <flux:icon name="eye" class="w-4 h-4" />
                                    </a>
                                    <a href="{{ route('modul.rawat-inap.sub-rawat-inap.resume-edit', str_replace('/', '-', $resume->no_rawat)) }}" wire:navigate
                                       title="Edit Resume" class="p-2 rounded-lg bg-neutral-100 dark:bg-neutral-700 text-neutral-500 hover:bg-amber-100 hover:text-amber-600 transition-all">
                                        <flux:icon name="pencil-square" class="w-4 h-4" />
                                    </a>
                                    <button type="button" 
                                         @click="Swal.fire({
                                             title: 'Hapus Resume Medis?',
                                             text: 'Apakah Anda yakin? Data ini tidak dapat dikembalikan!',
                                             icon: 'warning',
                                             showCancelButton: true,
                                             confirmButtonColor: '#4C5C2D',
                                             cancelButtonColor: '#d33',
                                             confirmButtonText: 'Ya, Hapus!',
                                             cancelButtonText: 'Batal'
                                         }).then((result) => {
                                             if (result.isConfirmed) {
                                                 $wire.delete('{{ $resume->no_rawat }}');
                                             }
                                         })"
                                         title="Hapus Resume" class="p-2 rounded-lg bg-neutral-100 dark:bg-neutral-700 text-neutral-500 hover:bg-red-100 hover:text-red-600 transition-all cursor-pointer border-none">
                                        <flux:icon name="trash" class="w-4 h-4" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-20 text-center">
                                <div class="flex flex-col items-center justify-center opacity-20">
                                    <flux:icon name="clipboard-document-list" class="w-16 h-16 mb-4" />
                                    <p class="text-sm italic font-medium">Belum ada data resume medis untuk pasien ini</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($resumes->hasPages())
            <div class="px-6 py-4 border-t border-neutral-100 dark:border-neutral-700 bg-neutral-50/30 dark:bg-neutral-900/10">
                {{ $resumes->links() }}
            </div>
        @endif
    </div>
</div>
