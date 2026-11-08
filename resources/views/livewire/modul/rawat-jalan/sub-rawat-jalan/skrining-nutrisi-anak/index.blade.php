<div class="flex flex-col gap-6 pb-8" x-data="{
    detailModalOpen: false
}">
    {{-- Header / Breadcrumb --}}
    <div class="flex flex-col gap-1">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <button type="button" onclick="history.length > 1 ? history.back() : window.location.href = '{{ route('modul.rawat-jalan.index') }}'" class="flex items-center justify-center w-10 h-8 rounded-md bg-[#4C5C2D] transition-colors hover:bg-[#3d4b24] shadow-sm">
                    <flux:icon name="chevron-left" class="w-5 h-5 text-white" />
                </button>
                <div>
                    <nav class="text-xs text-neutral-400 mb-0.5">
                        <a href="{{ route('modul.rawat-jalan.perawatan-tindakan', str_replace('/', '-', $regPeriksa->no_rawat)) }}" wire:navigate class="hover:underline">Perawatan & Tindakan</a>
                        <span class="mx-1">/</span>
                        <span>RM Gizi</span>
                        <span class="mx-1">/</span>
                        <span class="text-neutral-600 dark:text-neutral-300 font-medium">Skrining Nutrisi Pasien Anak</span>
                    </nav>
                    <h1 class="text-xl font-bold text-neutral-800 dark:text-neutral-100">Skrining Nutrisi Pasien Anak</h1>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <flux:button type="button" icon="book-open" variant="subtle" size="sm" class="!text-neutral-600 dark:!text-neutral-300 hover:!text-[#4C5C2D]" title="Panduan SOP Skrining Gizi">
                    SOP
                </flux:button>
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
                    <span class="font-mono bg-neutral-100 dark:bg-neutral-900 px-1.5 py-0.5 rounded text-xs">{{ $regPeriksa->no_rkm_medis }}</span>
                    <span>•</span>
                    <span>{{ $regPeriksa->pasien->jk === 'L' ? 'Laki-laki' : 'Perempuan' }}</span>
                    <span>•</span>
                    <span>{{ $regPeriksa->pasien->tgl_lahir ? \Carbon\Carbon::parse($regPeriksa->pasien->tgl_lahir)->age . ' Thn' : '-' }}</span>
                    <span>•</span>
                    <span class="font-medium text-neutral-600 dark:text-neutral-300">{{ $regPeriksa->dokter->nm_dokter ?? '-' }}</span>
                    <span>•</span>
                    <span class="font-medium text-neutral-500">{{ $regPeriksa->poliklinik->nm_poli ?? '-' }}</span>
                </div>
            </div>
        </div>
        <div class="text-right">
            <div class="text-xs text-neutral-400 mb-0.5">No. Rawat</div>
            <div class="font-mono font-bold text-[#4C5C2D] dark:text-[#8CC7C4] text-sm md:text-base">{{ $regPeriksa->no_rawat }}</div>
        </div>
    </div>

    {{-- Table List Data Skrining Nutrisi Pasien Anak --}}
    <div class="bg-white dark:bg-neutral-800 rounded-xl border border-neutral-200 dark:border-neutral-700 shadow-sm overflow-hidden flex flex-col p-4">
        <div class="flex items-center justify-between mb-4 px-2">
            <h3 class="text-sm font-bold text-neutral-700 dark:text-neutral-300 uppercase tracking-wider flex items-center gap-2">
                <flux:icon name="clipboard-document-list" class="w-4 h-4 text-[#4C5C2D] dark:text-[#8CC7C4]" />
                Daftar Skrining Nutrisi Pasien Anak (STRONG-kids)
                <span class="ml-1 px-2 py-0.5 text-xs font-semibold rounded-full bg-[#F1F5E9] text-[#4C5C2D] dark:bg-neutral-700 dark:text-[#8CC7C4]">
                    {{ count($skrinings) }} Data
                </span>
            </h3>

            {{-- Tombol Tambah Skrining (Disabled jika sudah ada data) --}}
            @if(count($skrinings) > 0)
                <flux:button
                    type="button"
                    icon="plus"
                    variant="subtle"
                    size="sm"
                    disabled
                    class="!flex !flex-row !items-center !opacity-50 !cursor-not-allowed"
                    title="Data skrining sudah ada (maksimal 1 data per pasien). Silakan gunakan tombol edit untuk mengubah data.">
                    Tambah Skrining
                </flux:button>
            @else
                <flux:button
                    type="button"
                    wire:click="openPanel"
                    icon="plus"
                    variant="filled"
                    size="sm"
                    class="!flex !flex-row !items-center !bg-[#4C5C2D] !text-white hover:!bg-[#3d4b24]">
                    Tambah Skrining
                </flux:button>
            @endif
        </div>

        <div class="overflow-x-auto">
            <flux:table class="whitespace-nowrap mt-2">
                <flux:table.columns>
                    <flux:table.column class="!pl-4 text-center">Action</flux:table.column>
                    <flux:table.column>Tanggal & Jam</flux:table.column>
                    <flux:table.column>Petugas Pemeriksa</flux:table.column>
                    <flux:table.column class="text-center">BB (Kg)</flux:table.column>
                    <flux:table.column class="text-center">TB/PB (Cm)</flux:table.column>
                    <flux:table.column class="text-center">Total Skor</flux:table.column>
                    <flux:table.column class="text-center">Hasil Skrining</flux:table.column>
                    <flux:table.column class="text-center">Lapor Dietisien/Dokter</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @forelse ($skrinings as $item)
                        <flux:table.row>
                            {{-- Action (Detail, Edit & Delete) --}}
                            <flux:table.cell class="!pl-4 text-center">
                                <div class="flex items-center justify-center gap-1">
                                    <flux:button
                                        type="button"
                                        wire:click="showDetail"
                                        @click="detailModalOpen = true"
                                        icon="eye"
                                        size="xs"
                                        variant="ghost"
                                        class="!text-emerald-600 hover:!bg-emerald-50 dark:hover:!bg-emerald-900/30"
                                        title="Lihat Detail Lengkap" />
                                    <flux:button
                                        type="button"
                                        wire:click="edit"
                                        icon="pencil-square"
                                        size="xs"
                                        variant="ghost"
                                        class="!text-blue-600 hover:!bg-blue-50 dark:hover:!bg-blue-900/30"
                                        title="Edit Skrining" />
                                    <flux:button
                                        type="button"
                                        @click="
                                            Swal.fire({
                                                title: 'Hapus Skrining Nutrisi Anak?',
                                                text: 'Data skrining nutrisi ini akan dihapus secara permanen.',
                                                icon: 'warning',
                                                showCancelButton: true,
                                                confirmButtonColor: '#dc2626',
                                                cancelButtonColor: '#6b7280',
                                                confirmButtonText: 'Ya, Hapus!',
                                                cancelButtonText: 'Batal'
                                            }).then((result) => {
                                                if (result.isConfirmed) {
                                                    $wire.delete();
                                                }
                                            });
                                        "
                                        icon="trash"
                                        size="xs"
                                        variant="ghost"
                                        class="!text-red-600 hover:!bg-red-50 dark:hover:!bg-red-900/30"
                                        title="Hapus Skrining" />
                                </div>
                            </flux:table.cell>

                            {{-- Tanggal & Jam --}}
                            <flux:table.cell class="text-xs font-medium text-neutral-800 dark:text-neutral-200">
                                <div>{{ $item->tanggal ? $item->tanggal->format('d/m/Y') : '-' }}</div>
                                <div class="text-[11px] font-mono text-neutral-400">{{ $item->tanggal ? $item->tanggal->format('H:i') : '' }} WIB</div>
                            </flux:table.cell>

                            {{-- Petugas Pemeriksa --}}
                            <flux:table.cell class="text-xs">
                                <span class="font-mono font-bold text-neutral-800 dark:text-neutral-200 block">{{ $item->nip ?: '-' }}</span>
                                <span class="text-neutral-500 dark:text-neutral-400">{{ $item->petugas->nama ?? '-' }}</span>
                            </flux:table.cell>

                            {{-- BB (Kg) --}}
                            <flux:table.cell class="text-center font-medium text-neutral-800 dark:text-neutral-200">
                                {{ $item->bb ?: '-' }}
                            </flux:table.cell>

                            {{-- TB/PB (Cm) --}}
                            <flux:table.cell class="text-center font-medium text-neutral-800 dark:text-neutral-200">
                                {{ $item->tbpb ?: '-' }}
                            </flux:table.cell>

                            {{-- Total Skor --}}
                            <flux:table.cell class="text-center">
                                <span class="inline-flex items-center justify-center min-w-[32px] px-2 py-0.5 rounded-full text-xs font-bold font-mono {{ ($item->total_hasil == 0) ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300' : (($item->total_hasil <= 3) ? 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300' : 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300') }}">
                                    {{ $item->total_hasil ?? 0 }} / 4
                                </span>
                            </flux:table.cell>

                            {{-- Hasil Skrining --}}
                            <flux:table.cell class="text-center">
                                @php
                                    $skor = $item->skor_nutrisi ?? ($item->total_hasil == 0 ? 'Risiko Rendah' : ($item->total_hasil <= 3 ? 'Risiko Sedang' : 'Risiko Berat'));
                                    $isBerat = str_contains($skor, 'Berat');
                                    $isSedang = str_contains($skor, 'Sedang');
                                    $badgeClass = $isBerat 
                                        ? 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300 border-red-300' 
                                        : ($isSedang 
                                            ? 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300 border-amber-300' 
                                            : 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300 border-emerald-300');
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold border {{ $badgeClass }}">
                                    {{ $isBerat ? 'Risiko Berat' : $skor }}
                                </span>
                            </flux:table.cell>

                            {{-- Diketahui Dietisien/Dokter --}}
                            <flux:table.cell class="text-center text-xs">
                                @if(($item->diketahui_dietisien ?? '') === 'Ya')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full font-semibold bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300">
                                        <flux:icon name="check" class="w-3 h-3" />
                                        Ya ({{ $item->keterangan_diketahui_dietisien ?: '-' }})
                                    </span>
                                @else
                                    <span class="text-neutral-400">Tidak</span>
                                @endif
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="8">
                                <div class="flex flex-col items-center justify-center py-12 text-neutral-400 dark:text-neutral-500">
                                    <flux:icon name="clipboard-document-list" class="w-12 h-12 mb-3 opacity-40 text-[#4C5C2D]" />
                                    <p class="text-sm font-medium">Belum ada data skrining nutrisi anak untuk pasien ini</p>
                                    <flux:button type="button" wire:click="openPanel" icon="plus" size="sm" class="mt-3 !bg-[#4C5C2D] !text-white hover:!bg-[#3d4b24]">
                                        Input Skrining Sekarang
                                    </flux:button>
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </div>
    </div>

    {{-- MODAL DETAIL POP UP (ALPINE.JS MURNI - SOP #6) --}}
    <div x-show="detailModalOpen"
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto"
         style="display: none;">
        {{-- Backdrop --}}
        <div class="fixed inset-0 bg-neutral-900/60 backdrop-blur-sm transition-opacity"
             @click="detailModalOpen = false"></div>

        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative bg-white dark:bg-neutral-800 rounded-2xl shadow-2xl max-w-3xl w-full border border-neutral-200 dark:border-neutral-700 overflow-hidden transform transition-all"
                 @click.stop>

                {{-- Modal Header --}}
                <div class="px-6 py-4 border-b border-neutral-200 dark:border-neutral-700 flex items-center justify-between bg-neutral-50 dark:bg-neutral-900/70">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-[#4C5C2D]/10 dark:bg-[#4C5C2D]/30 flex items-center justify-center flex-shrink-0 text-[#4C5C2D] dark:text-[#8CC7C4]">
                            <flux:icon name="clipboard-document-check" class="w-5 h-5 text-[#4C5C2D] dark:text-[#8CC7C4]" />
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-neutral-800 dark:text-neutral-100">
                                Detail Skrining Nutrisi Pasien Anak (STRONG-kids)
                            </h3>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">Rincian parameter skrining malnutrisi anak terintegrasi</p>
                        </div>
                    </div>
                    <button type="button" @click="detailModalOpen = false" class="text-neutral-400 hover:text-neutral-600 dark:hover:text-neutral-200 p-1.5 rounded-lg hover:bg-neutral-100 dark:hover:bg-neutral-700 transition-colors">
                        <flux:icon name="x-mark" class="w-5 h-5" />
                    </button>
                </div>

                {{-- Modal Body --}}
                <div class="p-6 space-y-5 max-h-[75vh] overflow-y-auto">
                    @if($selectedSkrining)
                        {{-- Identitas Ringkas --}}
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 bg-neutral-50 dark:bg-neutral-900/50 p-3.5 rounded-xl border border-neutral-200/80 dark:border-neutral-700 text-xs">
                            <div>
                                <span class="text-neutral-400 block text-[10px] uppercase font-bold">No. Rawat</span>
                                <span class="font-mono font-bold text-[#4C5C2D] dark:text-[#8CC7C4]">{{ $selectedSkrining->no_rawat }}</span>
                            </div>
                            <div>
                                <span class="text-neutral-400 block text-[10px] uppercase font-bold">Waktu Pemeriksaan</span>
                                <span class="font-medium text-neutral-800 dark:text-neutral-200">{{ $selectedSkrining->tanggal ? $selectedSkrining->tanggal->format('d/m/Y H:i') . ' WIB' : '-' }}</span>
                            </div>
                            <div class="col-span-2">
                                <span class="text-neutral-400 block text-[10px] uppercase font-bold">Petugas Pemeriksa</span>
                                <span class="font-medium text-neutral-800 dark:text-neutral-200">{{ $selectedSkrining->petugas->nama ?? '-' }} ({{ $selectedSkrining->nip }})</span>
                            </div>
                        </div>

                        {{-- Tanda Vital & Antropometri --}}
                        <div class="space-y-2">
                            <h4 class="text-xs font-bold text-neutral-700 dark:text-neutral-300 uppercase tracking-wider flex items-center gap-2">
                                <flux:icon name="heart" class="w-4 h-4 text-[#4C5C2D]" />
                                Tanda Vital & Antropometri
                            </h4>
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 bg-neutral-50/70 dark:bg-neutral-900/30 p-3.5 rounded-xl border border-neutral-200/70 dark:border-neutral-700 text-xs">
                                <div>
                                    <span class="text-neutral-400 block text-[10px]">Berat Badan (BB)</span>
                                    <span class="font-bold text-neutral-800 dark:text-neutral-100">{{ $selectedSkrining->bb ? $selectedSkrining->bb . ' Kg' : '-' }}</span>
                                </div>
                                <div>
                                    <span class="text-neutral-400 block text-[10px]">Tinggi / Panjang (TB)</span>
                                    <span class="font-bold text-neutral-800 dark:text-neutral-100">{{ $selectedSkrining->tbpb ? $selectedSkrining->tbpb . ' Cm' : '-' }}</span>
                                </div>
                                <div>
                                    <span class="text-neutral-400 block text-[10px]">Tekanan Darah (TD)</span>
                                    <span class="font-bold text-neutral-800 dark:text-neutral-100">{{ $selectedSkrining->td ? $selectedSkrining->td . ' mmHg' : '-' }}</span>
                                </div>
                                <div>
                                    <span class="text-neutral-400 block text-[10px]">Denyut Nadi (HR)</span>
                                    <span class="font-bold text-neutral-800 dark:text-neutral-100">{{ $selectedSkrining->hr ? $selectedSkrining->hr . ' x/mnt' : '-' }}</span>
                                </div>
                                <div>
                                    <span class="text-neutral-400 block text-[10px]">Laju Pernapasan (RR)</span>
                                    <span class="font-bold text-neutral-800 dark:text-neutral-100">{{ $selectedSkrining->rr ? $selectedSkrining->rr . ' x/mnt' : '-' }}</span>
                                </div>
                                <div>
                                    <span class="text-neutral-400 block text-[10px]">Suhu Tubuh</span>
                                    <span class="font-bold text-neutral-800 dark:text-neutral-100">{{ $selectedSkrining->suhu ? $selectedSkrining->suhu . ' °C' : '-' }}</span>
                                </div>
                                <div>
                                    <span class="text-neutral-400 block text-[10px]">SpO2</span>
                                    <span class="font-bold text-neutral-800 dark:text-neutral-100">{{ $selectedSkrining->spo2 ? $selectedSkrining->spo2 . ' %' : '-' }}</span>
                                </div>
                                <div>
                                    <span class="text-neutral-400 block text-[10px]">Riwayat Alergi</span>
                                    <span class="font-bold text-neutral-800 dark:text-neutral-100 truncate block">{{ $selectedSkrining->alergi ?: '-' }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- Rincian 4 Parameter STRONG-kids --}}
                        <div class="space-y-2">
                            <h4 class="text-xs font-bold text-neutral-700 dark:text-neutral-300 uppercase tracking-wider flex items-center gap-2">
                                <flux:icon name="scale" class="w-4 h-4 text-[#4C5C2D]" />
                                Pengkajian STRONG-kids (Screening Tool for Risk on Nutritional Status and Growth)
                            </h4>

                            <div class="divide-y divide-neutral-200 dark:divide-neutral-700 border border-neutral-200 dark:border-neutral-700 rounded-xl overflow-hidden bg-white dark:bg-neutral-800 text-xs">
                                {{-- 1 --}}
                                <div class="p-3 flex items-center justify-between gap-4">
                                    <div class="space-y-0.5">
                                        <div class="font-bold text-neutral-700 dark:text-neutral-200">1. Apakah pasien tampak kurus?</div>
                                        <div class="text-neutral-600 dark:text-neutral-400 font-medium">{{ $selectedSkrining->sg1 }}</div>
                                    </div>
                                    <span class="font-mono font-bold px-2.5 py-1 rounded-lg bg-neutral-100 dark:bg-neutral-700 text-neutral-800 dark:text-neutral-200">
                                        Skor: {{ $selectedSkrining->nilai1 }}
                                    </span>
                                </div>

                                {{-- 2 --}}
                                <div class="p-3 flex items-center justify-between gap-4">
                                    <div class="space-y-0.5">
                                        <div class="font-bold text-neutral-700 dark:text-neutral-200">2. Apakah terdapat penurunan berat badan selama satu bulan terakhir? (atau bayi &lt; 1 th BB tidak naik 3 bln)</div>
                                        <div class="text-neutral-600 dark:text-neutral-400 font-medium">{{ $selectedSkrining->sg2 }}</div>
                                    </div>
                                    <span class="font-mono font-bold px-2.5 py-1 rounded-lg bg-neutral-100 dark:bg-neutral-700 text-neutral-800 dark:text-neutral-200">
                                        Skor: {{ $selectedSkrining->nilai2 }}
                                    </span>
                                </div>

                                {{-- 3 --}}
                                <div class="p-3 flex items-center justify-between gap-4">
                                    <div class="space-y-0.5">
                                        <div class="font-bold text-neutral-700 dark:text-neutral-200">3. Apakah terdapat kondisi diare &gt; 5x/hr / muntah &gt; 3x/hr / asupan berkurang seminggu terakhir?</div>
                                        <div class="text-neutral-600 dark:text-neutral-400 font-medium">{{ $selectedSkrining->sg3 }}</div>
                                    </div>
                                    <span class="font-mono font-bold px-2.5 py-1 rounded-lg bg-neutral-100 dark:bg-neutral-700 text-neutral-800 dark:text-neutral-200">
                                        Skor: {{ $selectedSkrining->nilai3 }}
                                    </span>
                                </div>

                                {{-- 4 --}}
                                <div class="p-3 flex items-center justify-between gap-4">
                                    <div class="space-y-0.5">
                                        <div class="font-bold text-neutral-700 dark:text-neutral-200">4. Apakah terdapat penyakit atau keadaan yang menyebabkan pasien beresiko mengalami malnutrisi?</div>
                                        <div class="text-neutral-600 dark:text-neutral-400 font-medium">{{ $selectedSkrining->sg4 }}</div>
                                    </div>
                                    <span class="font-mono font-bold px-2.5 py-1 rounded-lg bg-neutral-100 dark:bg-neutral-700 text-neutral-800 dark:text-neutral-200">
                                        Skor: {{ $selectedSkrining->nilai4 }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- Pelaporan Dietisien --}}
                        <div class="bg-blue-50/70 dark:bg-blue-950/30 p-3.5 rounded-xl border border-blue-200 dark:border-blue-800 text-xs flex items-center justify-between">
                            <div>
                                <span class="text-blue-900 dark:text-blue-200 font-bold block">Diketahui Dietisien / Dilaporkan Kepada Dokter ?</span>
                                <span class="text-neutral-600 dark:text-neutral-400">{{ $selectedSkrining->diketahui_dietisien === 'Ya' ? 'Sudah dilaporkan kepada Dietisien / DPJP' : 'Belum dilaporkan' }}</span>
                            </div>
                            <div class="text-right">
                                <span class="text-blue-900 dark:text-blue-200 font-bold block">Jam Dilaporkan</span>
                                <span class="font-mono font-bold text-blue-700 dark:text-blue-300">{{ $selectedSkrining->keterangan_diketahui_dietisien ?: '-' }}</span>
                            </div>
                        </div>

                        {{-- Total Hasil & Rekomendasi --}}
                        @php
                            $totalHasil = $selectedSkrining->total_hasil ?? 0;
                            $skorNutrisi = $selectedSkrining->skor_nutrisi ?? ($totalHasil == 0 ? 'Risiko Rendah' : ($totalHasil <= 3 ? 'Risiko Sedang' : 'Risiko Berat'));
                            $isBerat = str_contains($skorNutrisi, 'Berat');
                            $isSedang = str_contains($skorNutrisi, 'Sedang');
                        @endphp
                        <div class="p-4 rounded-xl border {{ $isBerat ? 'bg-red-50 dark:bg-red-950/30 border-red-200 dark:border-red-800' : ($isSedang ? 'bg-amber-50 dark:bg-amber-950/30 border-amber-200 dark:border-amber-800' : 'bg-emerald-50 dark:bg-emerald-950/30 border-emerald-200 dark:border-emerald-800') }}">
                            <div class="flex items-center justify-between gap-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 rounded-xl flex items-center justify-center font-mono font-bold text-lg text-white {{ $isBerat ? 'bg-red-600' : ($isSedang ? 'bg-amber-600' : 'bg-emerald-600') }}">
                                        {{ $totalHasil }}/4
                                    </div>
                                    <div>
                                        <div class="text-xs font-bold uppercase tracking-wider {{ $isBerat ? 'text-red-800 dark:text-red-300' : ($isSedang ? 'text-amber-800 dark:text-amber-300' : 'text-emerald-800 dark:text-emerald-300') }}">
                                            Hasil: {{ $isBerat ? 'Risiko Berat (Tinggi)' : $skorNutrisi }}
                                        </div>
                                        <div class="text-xs text-neutral-600 dark:text-neutral-300 mt-0.5">
                                            @if($isBerat)
                                                Skor 4 - 5: Pasien berisiko malnutrisi berat. Wajib segera konsultasikan ke Ahli Gizi / Dietisien & Dokter Spesialis Anak!
                                            @elseif($isSedang)
                                                Skor 1 - 3: Pasien berisiko malnutrisi sedang. Lakukan intervensi nutrisi dan evaluasi berkala.
                                            @else
                                                Skor 0: Pasien berisiko malnutrisi rendah. Skrining ulang berkala selama perawatan.
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-8 text-neutral-400">
                            Tidak ada data detail.
                        </div>
                    @endif
                </div>

                {{-- Modal Footer --}}
                <div class="px-6 py-3.5 border-t border-neutral-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-900/70 flex justify-end">
                    <flux:button type="button" @click="detailModalOpen = false" variant="subtle" size="sm">
                        Tutup
                    </flux:button>
                </div>
            </div>
        </div>
    </div>

    {{-- RIGHT SLIDE-IN POP UP PANEL (SOP #6: Alpine.js murni modal/panel) --}}
    <div x-data="{
            open: @entangle('isPanelOpen'),
            autoTime: @entangle('autoTime'),
            interval: null,
            init() {
                this.interval = setInterval(() => {
                    if (this.autoTime && this.open) {
                        let now = new Date();
                        $wire.tanggal_date = now.getFullYear() + '-' + String(now.getMonth()+1).padStart(2,'0') + '-' + String(now.getDate()).padStart(2,'0');
                        $wire.tanggal_time = String(now.getHours()).padStart(2,'0') + ':' + String(now.getMinutes()).padStart(2,'0');
                    }
                }, 1000);
            }
        }"
        @set-autotime.window="autoTime = $event.detail[0].status"
        x-show="open"
        class="fixed inset-0 z-50 flex justify-end"
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
             @click="open = false"></div>

        {{-- Right Slide-In Container --}}
        <div x-show="open"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="translate-x-full opacity-0"
             x-transition:enter-end="translate-x-0 opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="translate-x-0 opacity-100"
             x-transition:leave-end="translate-x-full opacity-0"
             class="relative z-10 w-full max-w-3xl bg-white dark:bg-neutral-800 h-full flex flex-col shadow-2xl border-l border-neutral-200 dark:border-neutral-700 overflow-hidden">

            {{-- Panel Header --}}
            <div class="px-6 py-4 border-b border-neutral-200 dark:border-neutral-700 flex items-center justify-between bg-neutral-50/80 dark:bg-neutral-900/70 flex-shrink-0">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-[#4C5C2D]/10 dark:bg-[#4C5C2D]/30 flex items-center justify-center flex-shrink-0 text-[#4C5C2D] dark:text-[#8CC7C4]">
                        <flux:icon name="clipboard-document-check" class="w-5 h-5 text-[#4C5C2D] dark:text-[#8CC7C4]" />
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-neutral-800 dark:text-neutral-100">
                            {{ $isEditMode ? 'Edit Skrining Nutrisi Pasien Anak' : 'Input Skrining Nutrisi Pasien Anak' }}
                        </h3>
                        <p class="text-xs text-neutral-500 dark:text-neutral-400">Pengkajian STRONG-kids untuk deteksi dini risiko malnutrisi anak.</p>
                    </div>
                </div>
                <button type="button" @click="open = false" class="text-neutral-400 hover:text-neutral-600 dark:hover:text-neutral-200 p-1.5 rounded-lg hover:bg-neutral-100 dark:hover:bg-neutral-700 transition-colors">
                    <flux:icon name="x-mark" class="w-5 h-5" />
                </button>
            </div>

            {{-- Panel Scrollable Body --}}
            <div class="flex-1 overflow-y-auto p-6 space-y-6">

                {{-- Mini Patient Banner --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 bg-neutral-50 dark:bg-neutral-900/60 p-3.5 rounded-xl border border-neutral-200/80 dark:border-neutral-700">
                    <div>
                        <div class="text-[10px] text-neutral-400 font-bold uppercase tracking-wider mb-0.5">No. Rawat</div>
                        <div class="font-mono text-xs font-bold text-[#4C5C2D] dark:text-[#8CC7C4]">{{ $regPeriksa->no_rawat }}</div>
                    </div>
                    <div>
                        <div class="text-[10px] text-neutral-400 font-bold uppercase tracking-wider mb-0.5">Pasien (RM)</div>
                        <div class="text-xs font-bold text-neutral-800 dark:text-neutral-100 truncate" title="{{ $regPeriksa->pasien->nm_pasien }}">
                            {{ $regPeriksa->pasien->nm_pasien }} <span class="font-mono text-neutral-400">({{ $regPeriksa->no_rkm_medis }})</span>
                        </div>
                    </div>
                    <div>
                        <div class="text-[10px] text-neutral-400 font-bold uppercase tracking-wider mb-0.5">Tgl. Lahir / JK</div>
                        <div class="text-xs font-medium text-neutral-700 dark:text-neutral-300">
                            {{ $regPeriksa->pasien->tgl_lahir ? \Carbon\Carbon::parse($regPeriksa->pasien->tgl_lahir)->format('d-m-Y') : '-' }}
                            ({{ $regPeriksa->pasien->jk === 'L' ? 'L' : 'P' }})
                        </div>
                    </div>
                </div>

                {{-- Section 1: Waktu & Petugas --}}
                <div class="bg-neutral-50/50 dark:bg-neutral-900/30 p-4 rounded-xl border border-neutral-200/80 dark:border-neutral-700 space-y-4">
                    <h4 class="text-xs font-bold text-neutral-700 dark:text-neutral-300 uppercase tracking-wider flex items-center gap-2">
                        <flux:icon name="clock" class="w-4 h-4 text-[#4C5C2D] dark:text-[#8CC7C4]" />
                        Waktu & Petugas Pemeriksa
                    </h4>

                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-4">
                        <div class="sm:col-span-4">
                            <flux:input wire:model="tanggal_date" type="date" label="Tanggal *" required />
                            @error('tanggal_date') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div class="sm:col-span-3">
                            <div class="flex items-center justify-between mb-1">
                                <flux:label class="!mb-0">Jam *</flux:label>
                                <label class="flex items-center gap-1.5 cursor-pointer select-none">
                                    <input
                                        type="checkbox"
                                        x-model="autoTime"
                                        wire:model.live="autoTime"
                                        class="rounded text-[#4C5C2D] focus:ring-[#4C5C2D] border-neutral-300 dark:border-neutral-600 dark:bg-neutral-800 cursor-pointer h-3.5 w-3.5" />
                                    <span class="text-[11px] text-neutral-500 font-medium">Auto</span>
                                </label>
                            </div>
                            <flux:input wire:model="tanggal_time" type="time" required />
                            @error('tanggal_time') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div class="sm:col-span-5 relative">
                            <flux:label>Petugas (NIP/Nama) *</flux:label>

                            @if($nip && $nmPetugas)
                                <div class="flex items-center justify-between px-3 py-2 bg-white dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl h-10 shadow-sm mt-1">
                                    <div class="flex items-center gap-2.5 min-w-0">
                                        <div class="w-6 h-6 rounded-lg bg-[#4C5C2D]/10 dark:bg-[#4C5C2D]/30 flex items-center justify-center flex-shrink-0 text-[#4C5C2D] dark:text-[#8CC7C4]">
                                            <flux:icon name="user" class="w-3.5 h-3.5" />
                                        </div>
                                        <div class="min-w-0">
                                            <div class="text-xs font-bold text-neutral-800 dark:text-neutral-100 truncate" title="{{ $nmPetugas }}">
                                                {{ $nmPetugas }}
                                            </div>
                                            <div class="text-[10px] font-mono text-neutral-400 leading-none">
                                                {{ $nip }}
                                            </div>
                                        </div>
                                    </div>
                                    <button
                                        type="button"
                                        wire:click="clearPetugas"
                                        class="text-[11px] font-medium text-neutral-400 hover:text-red-500 dark:hover:text-red-400 px-2 py-1 rounded-md hover:bg-neutral-100 dark:hover:bg-neutral-700 transition-colors flex items-center gap-1 flex-shrink-0"
                                        title="Ganti Petugas">
                                        <flux:icon name="pencil-square" class="w-3.5 h-3.5" />
                                        <span>Ganti</span>
                                    </button>
                                </div>
                            @else
                                <div class="relative mt-1">
                                    <flux:input
                                        wire:model.live.debounce.300ms="petugasSearch"
                                        placeholder="Cari NIP / Nama petugas..."
                                        icon="magnifying-glass"
                                        autofocus />

                                    @if(!empty($petugasList))
                                        <div class="absolute z-30 left-0 right-0 mt-1 bg-white dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl shadow-xl max-h-48 overflow-y-auto">
                                            @foreach($petugasList as $p)
                                                <button
                                                    type="button"
                                                    wire:click="selectPetugas('{{ $p->nip }}', '{{ $p->nama }}')"
                                                    class="w-full text-left px-3.5 py-2 hover:bg-neutral-50 dark:hover:bg-neutral-700 border-b border-neutral-100 dark:border-neutral-700 last:border-0 transition-colors">
                                                    <div class="font-bold text-xs text-neutral-700 dark:text-neutral-200">{{ $p->nama }}</div>
                                                    <div class="font-mono text-[11px] text-neutral-400">{{ $p->nip }}</div>
                                                </button>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endif
                            @error('nip') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                {{-- Section 2: Antropometri & Tanda Vital --}}
                <div class="bg-neutral-50/50 dark:bg-neutral-900/30 p-4 rounded-xl border border-neutral-200/80 dark:border-neutral-700 space-y-4">
                    <div class="flex items-center justify-between">
                        <h4 class="text-xs font-bold text-neutral-700 dark:text-neutral-300 uppercase tracking-wider flex items-center gap-2">
                            <flux:icon name="heart" class="w-4 h-4 text-[#4C5C2D] dark:text-[#8CC7C4]" />
                            Antropometri & Tanda Vital
                        </h4>

                        <flux:button
                            type="button"
                            wire:click="fillLatestPemeriksaan"
                            icon="arrow-path"
                            size="xs"
                            variant="subtle"
                            class="!text-xs !text-[#4C5C2D] hover:!bg-[#4C5C2D]/10 dark:!text-[#8CC7C4]"
                            title="Tarik data antropometri & tanda vital dari pemeriksaan SOAP terakhir">
                            <span wire:loading.remove wire:target="fillLatestPemeriksaan">Isi Otomatis</span>
                            <span wire:loading wire:target="fillLatestPemeriksaan">Menarik Data...</span>
                        </flux:button>
                    </div>

                    {{-- Baris 1: BB, TB, TD, HR, RR --}}
                    <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
                        <div>
                            <flux:input wire:model="bb" label="BB (Kg)" placeholder="Contoh: 15" />
                        </div>
                        <div>
                            <flux:input wire:model="tbpb" label="TB/PB (Cm)" placeholder="Contoh: 95" />
                        </div>
                        <div>
                            <flux:input wire:model="td" label="TD (mmHg)" placeholder="100/70" />
                        </div>
                        <div>
                            <flux:input wire:model="hr" label="HR (x/mnt)" placeholder="95" />
                        </div>
                        <div>
                            <flux:input wire:model="rr" label="RR (x/mnt)" placeholder="24" />
                        </div>
                    </div>

                    {{-- Baris 2: Suhu, SpO2, Alergi --}}
                    <div class="grid grid-cols-2 sm:grid-cols-12 gap-3">
                        <div class="sm:col-span-3">
                            <flux:input wire:model="suhu" label="Suhu (°C)" placeholder="36.8" />
                        </div>
                        <div class="sm:col-span-3">
                            <flux:input wire:model="spo2" label="SpO2 (%)" placeholder="99" />
                        </div>
                        <div class="col-span-2 sm:col-span-6">
                            <flux:input wire:model="alergi" label="Riwayat Alergi" placeholder="Tulis alergi atau tanda '-' jika tidak ada" />
                        </div>
                    </div>
                </div>

                {{-- Section 3: Skrining Gizi Awal Dengan STRONG-kids --}}
                <div class="bg-white dark:bg-neutral-800 rounded-xl border-2 border-[#4C5C2D]/30 dark:border-[#4C5C2D]/50 p-4 space-y-4 shadow-sm">
                    <div class="border-b border-neutral-100 dark:border-neutral-700 pb-2 flex items-center justify-between">
                        <div>
                            <h4 class="text-sm font-bold text-neutral-800 dark:text-neutral-100 flex items-center gap-2">
                                <flux:icon name="scale" class="w-4 h-4 text-[#4C5C2D]" />
                                Skrining Gizi Awal Dengan STRONG-kids
                            </h4>
                            <p class="text-xs text-neutral-500 mt-0.5">Pilih Tidak/Ya pada 4 parameter berikut untuk mendeteksi risiko malnutrisi anak.</p>
                        </div>
                        <span class="text-[11px] font-bold px-2 py-0.5 rounded bg-[#F1F5E9] text-[#4C5C2D] dark:bg-neutral-700 dark:text-[#8CC7C4]">
                            STRONG-kids
                        </span>
                    </div>

                    {{-- Pertanyaan 1 --}}
                    <div class="bg-neutral-50 dark:bg-neutral-900/40 p-3.5 rounded-xl border border-neutral-200/70 dark:border-neutral-700 space-y-2">
                        <label class="block text-xs font-bold text-neutral-700 dark:text-neutral-200">
                            1. Apakah pasien tampak kurus? <span class="text-red-500">*</span>
                        </label>
                        <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
                            <div class="sm:col-span-9">
                                <flux:select wire:model.live="sg1">
                                    <flux:select.option value="Tidak">Tidak (Skor: 0)</flux:select.option>
                                    <flux:select.option value="Ya">Ya (Skor: 1)</flux:select.option>
                                </flux:select>
                                @error('sg1') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div class="sm:col-span-3 flex items-center justify-end sm:justify-center gap-2">
                                <span class="text-xs text-neutral-500 font-medium">Nilai :</span>
                                <span class="w-8 h-8 rounded-lg bg-neutral-200/80 dark:bg-neutral-700 font-mono font-bold text-sm text-neutral-800 dark:text-neutral-100 flex items-center justify-center">
                                    {{ $nilai1 }}
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Pertanyaan 2 --}}
                    <div class="bg-neutral-50 dark:bg-neutral-900/40 p-3.5 rounded-xl border border-neutral-200/70 dark:border-neutral-700 space-y-2">
                        <label class="block text-xs font-bold text-neutral-700 dark:text-neutral-200">
                            2. Apakah terdapat penurunan berat badan selama satu bulan terakhir? (berdasarkan penilaian objektif data berat badan bila ada atau untuk bayi &lt; 1 tahun ; berat badan tidak naik selama 3 bulan terakhir) <span class="text-red-500">*</span>
                        </label>
                        <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
                            <div class="sm:col-span-9">
                                <flux:select wire:model.live="sg2">
                                    <flux:select.option value="Tidak">Tidak (Skor: 0)</flux:select.option>
                                    <flux:select.option value="Ya">Ya (Skor: 1)</flux:select.option>
                                </flux:select>
                                @error('sg2') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div class="sm:col-span-3 flex items-center justify-end sm:justify-center gap-2">
                                <span class="text-xs text-neutral-500 font-medium">Nilai :</span>
                                <span class="w-8 h-8 rounded-lg bg-neutral-200/80 dark:bg-neutral-700 font-mono font-bold text-sm text-neutral-800 dark:text-neutral-100 flex items-center justify-center">
                                    {{ $nilai2 }}
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Pertanyaan 3 --}}
                    <div class="bg-neutral-50 dark:bg-neutral-900/40 p-3.5 rounded-xl border border-neutral-200/70 dark:border-neutral-700 space-y-2">
                        <label class="block text-xs font-bold text-neutral-700 dark:text-neutral-200">
                            3. Apakah terdapat salah satu dari kondisi tersebut? Diare &gt; 5 kali/hari dan/muntah &gt; 3 kali/hari dalam seminggu terakhir; Asupan makanan berkurang selama 1 minggu terakhir <span class="text-red-500">*</span>
                        </label>
                        <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
                            <div class="sm:col-span-9">
                                <flux:select wire:model.live="sg3">
                                    <flux:select.option value="Tidak">Tidak (Skor: 0)</flux:select.option>
                                    <flux:select.option value="Ya">Ya (Skor: 1)</flux:select.option>
                                </flux:select>
                                @error('sg3') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div class="sm:col-span-3 flex items-center justify-end sm:justify-center gap-2">
                                <span class="text-xs text-neutral-500 font-medium">Nilai :</span>
                                <span class="w-8 h-8 rounded-lg bg-neutral-200/80 dark:bg-neutral-700 font-mono font-bold text-sm text-neutral-800 dark:text-neutral-100 flex items-center justify-center">
                                    {{ $nilai3 }}
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Pertanyaan 4 --}}
                    <div class="bg-neutral-50 dark:bg-neutral-900/40 p-3.5 rounded-xl border border-neutral-200/70 dark:border-neutral-700 space-y-2">
                        <label class="block text-xs font-bold text-neutral-700 dark:text-neutral-200">
                            4. Apakah terdapat penyakit atau keadaan yang menyebabkan pasien beresiko mengalami malnutrisi? <span class="text-red-500">*</span>
                        </label>
                        <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
                            <div class="sm:col-span-9">
                                <flux:select wire:model.live="sg4">
                                    <flux:select.option value="Tidak">Tidak (Skor: 0)</flux:select.option>
                                    <flux:select.option value="Ya">Ya (Skor: 1)</flux:select.option>
                                </flux:select>
                                @error('sg4') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div class="sm:col-span-3 flex items-center justify-end sm:justify-center gap-2">
                                <span class="text-xs text-neutral-500 font-medium">Nilai :</span>
                                <span class="w-8 h-8 rounded-lg bg-neutral-200/80 dark:bg-neutral-700 font-mono font-bold text-sm text-neutral-800 dark:text-neutral-100 flex items-center justify-center">
                                    {{ $nilai4 }}
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Lapor Dietisien / Dokter --}}
                    <div class="bg-neutral-50 dark:bg-neutral-900/40 p-3.5 rounded-xl border border-neutral-200/70 dark:border-neutral-700 space-y-3">
                        <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
                            <div class="sm:col-span-7">
                                <flux:select wire:model.live="diketahui_dietisien" label="Diketahui Dietisien/Dilaporkan Kepada Dokter ?">
                                    <flux:select.option value="Tidak">Tidak</flux:select.option>
                                    <flux:select.option value="Ya">Ya</flux:select.option>
                                </flux:select>
                            </div>
                            <div class="sm:col-span-5">
                                <flux:input wire:model="keterangan_diketahui_dietisien" label="Jam dilaporkan" placeholder="Contoh: 14:30" />
                            </div>
                        </div>
                    </div>

                    {{-- Hasil Total Skor & Kesimpulan --}}
                    @php
                        $isBerat = $total_hasil >= 4;
                        $isSedang = $total_hasil >= 1 && $total_hasil <= 3;
                        $colorClass = $isBerat 
                            ? 'bg-red-50 dark:bg-red-950/30 border-red-200 dark:border-red-900 text-red-800 dark:text-red-300' 
                            : ($isSedang 
                                ? 'bg-amber-50 dark:bg-amber-950/30 border-amber-200 dark:border-amber-900 text-amber-800 dark:text-amber-300' 
                                : 'bg-emerald-50 dark:bg-emerald-950/30 border-emerald-200 dark:border-emerald-900 text-emerald-800 dark:text-emerald-300');
                        $badgeBg = $isBerat ? 'bg-red-600' : ($isSedang ? 'bg-amber-600' : 'bg-emerald-600');
                        $displayStatus = $isBerat ? 'Risiko Berat' : $skor_nutrisi;
                    @endphp
                    <div class="p-4 rounded-xl border transition-colors {{ $colorClass }}">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-xl flex items-center justify-center font-mono font-bold text-xl text-white shadow-md {{ $badgeBg }}">
                                    {{ $total_hasil }}
                                </div>
                                <div>
                                    <div class="text-xs font-bold uppercase tracking-wider">
                                        Total Skor: {{ $total_hasil }} / 4 &bull; Status: {{ $displayStatus }}
                                    </div>
                                    <div class="text-xs font-semibold mt-0.5">
                                        @if($isBerat)
                                            ⛔ Risiko Berat (Skor 4-5). Wajib segera konsultasikan ke Ahli Gizi / Dietisien & Dokter Spesialis Anak!
                                        @elseif($isSedang)
                                            ⚠️ Risiko Sedang (Skor 1-3). Lakukan intervensi nutrisi dan evaluasi berkala.
                                        @else
                                            ✅ Risiko Rendah (Skor 0). Pasien tidak mengalami risiko malnutrisi akut.
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <span class="inline-flex self-start sm:self-auto items-center px-3 py-1 rounded-full text-xs font-bold shadow-sm text-white {{ $badgeBg }}">
                                {{ $displayStatus }}
                            </span>
                        </div>
                    </div>
                </div>

            </div>

            {{-- Panel Footer (Sticky Actions) --}}
            <div class="px-6 py-4 border-t border-neutral-200 dark:border-neutral-700 flex items-center justify-end gap-3 bg-neutral-50/80 dark:bg-neutral-900/70 flex-shrink-0">
                <flux:button
                    type="button"
                    @click="open = false"
                    variant="subtle"
                    size="sm">
                    Batal
                </flux:button>

                <flux:button
                    type="button"
                    wire:click="save"
                    variant="filled"
                    size="sm"
                    class="!bg-[#4C5C2D] !text-white hover:!bg-[#3d4b24]">
                    <span wire:loading.remove wire:target="save">
                        {{ $isEditMode ? 'Perbarui Data' : 'Simpan Skrining' }}
                    </span>
                    <span wire:loading wire:target="save">
                        Menyimpan...
                    </span>
                </flux:button>
            </div>
        </div>
    </div>
</div>
