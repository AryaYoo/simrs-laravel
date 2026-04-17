<div class="flex flex-col gap-6 pb-8">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('modul.rawat-inap.show', str_replace('/', '-', $no_rawat)) }}" wire:navigate
               class="flex items-center justify-center w-8 h-8 rounded-lg transition-colors hover:bg-neutral-100 dark:hover:bg-neutral-700">
                <flux:icon name="chevron-left" class="w-5 h-5 text-neutral-500" />
            </a>
            <div>
                <nav class="text-xs text-neutral-400 mb-0.5">
                    <a href="{{ route('modul.index') }}" wire:navigate class="hover:underline">Modul</a>
                    <span class="mx-1">/</span>
                    <a href="{{ route('modul.rawat-inap.index') }}" wire:navigate class="hover:underline">Rawat Inap</a>
                    <span class="mx-1">/</span>
                    <a href="{{ route('modul.rawat-inap.show', str_replace('/', '-', $no_rawat)) }}" wire:navigate class="hover:underline">Detail</a>
                    <span class="mx-1">/</span>
                    <span>Pindah Kamar</span>
                </nav>
                <h1 class="text-xl font-bold text-neutral-800 dark:text-neutral-100">Pindah Kamar Inap Pasien</h1>
            </div>
        </div>
    </div>

    {{-- Patient Info Banner --}}
    <div class="bg-[#4C5C2D] text-white p-4 rounded-xl shadow-md flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center flex-shrink-0">
                <flux:icon name="user" class="w-6 h-6 text-white" />
            </div>
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="font-mono text-xs bg-white/20 px-2 py-0.5 rounded">{{ $regPeriksa->no_rawat }}</span>
                    <span class="font-mono text-xs bg-white/20 px-2 py-0.5 rounded">{{ $regPeriksa->no_rkm_medis }}</span>
                </div>
                <h2 class="text-lg font-bold">{{ $regPeriksa->pasien->nm_pasien ?? '-' }}</h2>
                <div class="text-xs text-white/80 mt-1 flex flex-wrap items-center gap-3">
                    <span class="flex items-center gap-1"><flux:icon name="calendar" class="w-3 h-3"/> {{ $regPeriksa->umurdaftar }} {{ $regPeriksa->sttsumur }}</span>
                    @if($currentKamarInapArray)
                        <span class="flex items-center gap-1"><flux:icon name="home" class="w-3 h-3"/> {{ $currentKamarInapArray['kamar']['bangsal']['nm_bangsal'] ?? '-' }} &middot; {{ $currentKamarInapArray['kd_kamar'] ?? '-' }}</span>
                    @endif
                </div>
            </div>
        </div>
        <div class="text-left md:text-right text-sm border-t md:border-t-0 md:border-l border-white/20 pt-3 md:pt-0 md:pl-4">
            <p class="text-white/80 text-xs mb-1">Dokter DPJP</p>
            <p class="font-semibold">{{ $regPeriksa->dokter->nm_dokter ?? '-' }}</p>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Left Column: Room & Date Fields --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Kamar Baru --}}
            <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-sm border border-neutral-200 dark:border-neutral-700 overflow-hidden">
                <div class="p-4 border-b border-neutral-100 dark:border-neutral-700 bg-neutral-50/50 dark:bg-neutral-800/50 flex items-center gap-2">
                    <flux:icon name="arrow-path" class="w-5 h-5 text-[#4C5C2D]" />
                    <h3 class="font-bold text-neutral-700 dark:text-neutral-300 uppercase tracking-tight text-sm">Kamar Tujuan</h3>
                </div>
                <div class="p-5 space-y-5">
                    {{-- Room Selection Row --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <flux:field>
                            <flux:label>Kode Kamar / Bed</flux:label>
                            <div class="flex gap-2">
                                <flux:input wire:model="kd_kamar" placeholder="Pilih kamar..." readonly class="flex-1 bg-neutral-50 font-mono" />
                                <button type="button" wire:click="openKamarModal" class="p-1.5 text-neutral-500 hover:text-[#4C5C2D] transition-colors border border-neutral-200 dark:border-neutral-700 rounded-lg hover:bg-neutral-50 dark:hover:bg-neutral-800">
                                    <flux:icon name="paper-clip" class="w-4 h-4" />
                                </button>
                            </div>
                        </flux:field>
                        <flux:field>
                            <flux:label>Nama Bangsal / Ruangan</flux:label>
                            <flux:input wire:model="nm_bangsal" readonly class="bg-neutral-50" />
                        </flux:field>
                    </div>

                    {{-- Room Details Row --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <flux:field>
                            <flux:label>Status Kamar</flux:label>
                            <flux:input value="{{ $status_kamar ?: 'BELUM DIPILIH' }}" readonly class="bg-neutral-50 text-xs font-bold {{ $status_kamar === 'KOSONG' ? '!text-green-600' : ($status_kamar === 'ISI' ? '!text-red-500' : '!text-neutral-400') }}" />
                        </flux:field>
                        <flux:field>
                            <flux:label>Kelas</flux:label>
                            <flux:input wire:model="kelas_kamar" readonly class="bg-neutral-50 font-medium" />
                        </flux:field>
                        <flux:field>
                            <flux:label>Tarif / Hari (Rp)</flux:label>
                            <flux:input wire:model="trf_kamar" readonly class="bg-neutral-50 font-mono font-bold" />
                        </flux:field>
                    </div>
                </div>
            </div>

            {{-- Waktu Pindah --}}
            <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-sm border border-neutral-200 dark:border-neutral-700 overflow-hidden">
                <div class="p-4 border-b border-neutral-100 dark:border-neutral-700 bg-neutral-50/50 dark:bg-neutral-800/50 flex items-center gap-2">
                    <flux:icon name="clock" class="w-5 h-5 text-[#4C5C2D]" />
                    <h3 class="font-bold text-neutral-700 dark:text-neutral-300 uppercase tracking-tight text-sm">Waktu Perpindahan</h3>
                </div>
                <div class="p-5">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <flux:field>
                            <flux:label>Tanggal Pindah</flux:label>
                            <flux:input wire:model.live="tgl_pindah" type="date" />
                        </flux:field>
                        <flux:field>
                            <flux:label>Jam Pindah</flux:label>
                            <flux:input wire:model.live="jam_pindah" type="time" step="1" />
                        </flux:field>
                    </div>
                </div>
            </div>

            {{-- Kalkulasi Biaya --}}
            <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-sm border border-neutral-200 dark:border-neutral-700 overflow-hidden">
                <div class="p-4 border-b border-neutral-100 dark:border-neutral-700 bg-neutral-50/50 dark:bg-neutral-800/50 flex items-center gap-2">
                    <flux:icon name="calculator" class="w-5 h-5 text-[#4C5C2D]" />
                    <h3 class="font-bold text-neutral-700 dark:text-neutral-300 uppercase tracking-tight text-sm">Kalkulasi Biaya Inap Sebelumnya</h3>
                </div>
                <div class="p-5">
                    <div class="grid grid-cols-3 gap-4 text-center">
                        <div class="bg-[#4C5C2D]/5 border border-[#4C5C2D]/10 rounded-xl p-4">
                            <p class="text-[10px] font-bold uppercase text-neutral-400 tracking-wider mb-1">Lama Inap</p>
                            <p class="text-2xl font-black text-[#4C5C2D]">{{ $lama }}</p>
                            <p class="text-[10px] text-neutral-500">Hari</p>
                        </div>
                        <div class="flex items-center justify-center">
                            <span class="text-neutral-300 text-2xl font-light">&times;</span>
                        </div>
                        <div class="bg-neutral-50 dark:bg-neutral-900/50 border border-neutral-100 dark:border-neutral-700 rounded-xl p-4">
                            <p class="text-[10px] font-bold uppercase text-neutral-400 tracking-wider mb-1">Total Biaya</p>
                            <p class="text-xl font-black text-neutral-800 dark:text-neutral-100">
                                <span class="text-xs font-normal text-neutral-400">Rp </span>{{ number_format($total, 0, ',', '.') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Column: Options & Submit --}}
        <div class="space-y-6">
            {{-- Pilihan Logika --}}
            <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-sm border border-neutral-200 dark:border-neutral-700 overflow-hidden">
                <div class="p-4 border-b border-neutral-100 dark:border-neutral-700 bg-neutral-50/50 dark:bg-neutral-800/50 flex items-center gap-2">
                    <flux:icon name="cog-6-tooth" class="w-5 h-5 text-[#4C5C2D]" />
                    <h3 class="font-bold text-neutral-700 dark:text-neutral-300 uppercase tracking-tight text-sm">Pilihan Perpindahan</h3>
                </div>
                <div class="p-5">
                    <flux:radio.group wire:model.live="pilihan" class="flex flex-col gap-3">
                        <label class="flex items-start gap-3 p-3 rounded-lg border cursor-pointer transition-all duration-200 {{ $pilihan == 1 ? 'border-[#4C5C2D]/40 bg-[#F1F5E9]' : 'border-neutral-100 dark:border-neutral-700 hover:bg-neutral-50 dark:hover:bg-neutral-900/50' }}">
                            <flux:radio value="1" class="mt-0.5" />
                            <div>
                                <p class="text-sm font-bold text-neutral-800 dark:text-neutral-200">Hapus Kamar Lama</p>
                                <p class="text-xs text-neutral-500 mt-0.5">Record inap sebelumnya dihapus dari billing.</p>
                            </div>
                        </label>
                        <label class="flex items-start gap-3 p-3 rounded-lg border cursor-pointer transition-all duration-200 {{ $pilihan == 2 ? 'border-[#4C5C2D]/40 bg-[#F1F5E9]' : 'border-neutral-100 dark:border-neutral-700 hover:bg-neutral-50 dark:hover:bg-neutral-900/50' }}">
                            <flux:radio value="2" class="mt-0.5" />
                            <div>
                                <p class="text-sm font-bold text-neutral-800 dark:text-neutral-200">Ganti Kamar (Merge)</p>
                                <p class="text-xs text-neutral-500 mt-0.5">Kamar diganti di record yang sama, tarif menyesuaikan.</p>
                            </div>
                        </label>
                        <label class="flex items-start gap-3 p-3 rounded-lg border cursor-pointer transition-all duration-200 {{ $pilihan == 3 ? 'border-[#4C5C2D]/40 bg-[#F1F5E9]' : 'border-neutral-100 dark:border-neutral-700 hover:bg-neutral-50 dark:hover:bg-neutral-900/50' }}">
                            <flux:radio value="3" class="mt-0.5" />
                            <div>
                                <p class="text-sm font-bold text-neutral-800 dark:text-neutral-200">Status Pindah (Standar)</p>
                                <p class="text-xs text-neutral-500 mt-0.5">Kamar lama ditutup & dihitung biayanya, pasien masuk kamar baru.</p>
                            </div>
                        </label>
                        <label class="flex items-start gap-3 p-3 rounded-lg border cursor-pointer transition-all duration-200 {{ $pilihan == 4 ? 'border-[#4C5C2D]/40 bg-[#F1F5E9]' : 'border-neutral-100 dark:border-neutral-700 hover:bg-neutral-50 dark:hover:bg-neutral-900/50' }}">
                            <flux:radio value="4" class="mt-0.5" />
                            <div>
                                <p class="text-sm font-bold text-neutral-800 dark:text-neutral-200">Pindah (Harga Tertinggi)</p>
                                <p class="text-xs text-neutral-500 mt-0.5">Sama seperti standar, namun tarif menyesuaikan yang tertinggi.</p>
                            </div>
                        </label>
                    </flux:radio.group>
                </div>
            </div>

            {{-- Submit Button --}}
            <div class="bg-[#F1F5E9] dark:bg-[#4C5C2D]/10 rounded-xl border border-[#4C5C2D]/20 p-5">
                <flux:button wire:click="save" variant="primary" icon="paper-airplane" class="w-full !bg-[#4C5C2D] hover:!bg-[#3D4A24] !border-none text-white shadow-md font-bold py-3 h-auto text-sm flex items-center justify-center gap-2">
                    Proses Pindah Kamar
                </flux:button>
                <p class="text-[10px] text-neutral-500 text-center mt-3">Pastikan data sudah benar sebelum memproses perpindahan kamar.</p>
            </div>
        </div>
    </div>

    {{-- Modal Kamar Lookup --}}
    <flux:modal name="kamar-lookup" wire:model="isKamarModalOpen" variant="flyout" class="w-full max-w-2xl">
        <div class="mb-4">
            <h3 class="font-bold text-neutral-800 dark:text-neutral-200 flex items-center gap-2 text-lg">
                <flux:icon name="magnifying-glass" class="w-5 h-5 text-[#4C5C2D] dark:text-[#8CC7C4]" />
                Cari Kamar Inap / Bed
            </h3>
        </div>

        <flux:input wire:model.live.debounce.300ms="searchKamar" icon="magnifying-glass" placeholder="Cari berdasarkan No. Bed atau Nama Bangsal..." autofocus class="mb-4" />

        <div class="overflow-y-auto border border-neutral-200 dark:border-neutral-700 rounded-lg" style="max-height: 60vh;">
            <table class="w-full text-sm text-left">
                <thead class="text-[10px] text-neutral-500 uppercase bg-neutral-50 dark:bg-neutral-900 border-b border-neutral-200 dark:border-neutral-700 sticky top-0 z-10 font-bold tracking-wider">
                    <tr>
                        <th class="px-4 py-3">Nomer Bed</th>
                        <th class="px-4 py-3">Kode Bangsal</th>
                        <th class="px-4 py-3">Nama Kamar</th>
                        <th class="px-4 py-3">Kelas</th>
                        <th class="px-4 py-3 text-right">Tarif</th>
                        <th class="px-4 py-3 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100 dark:divide-neutral-800">
                    @forelse($listKamar as $kamar)
                        <tr class="hover:bg-[#F1F5E9] dark:hover:bg-[#4C5C2D]/10 transition-colors cursor-pointer group" 
                            wire:click="selectKamar('{{ $kamar->kd_kamar }}', '{{ $kamar->bangsal->nm_bangsal ?? '-' }}', {{ $kamar->trf_kamar }}, '{{ $kamar->status }}', '{{ $kamar->kelas }}')">
                            <td class="px-4 py-3 font-bold text-neutral-800 dark:text-neutral-100 group-hover:text-[#4C5C2D]">{{ $kamar->kd_kamar }}</td>
                            <td class="px-4 py-3 font-mono text-xs text-neutral-500">{{ $kamar->kd_bangsal }}</td>
                            <td class="px-4 py-3 font-semibold text-neutral-700 dark:text-neutral-300">{{ $kamar->bangsal->nm_bangsal ?? '-' }}</td>
                            <td class="px-4 py-3 text-xs font-medium text-neutral-500">{{ $kamar->kelas }}</td>
                            <td class="px-4 py-3 text-right font-mono text-neutral-600 dark:text-neutral-400">{{ number_format($kamar->trf_kamar, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-center">
                                @if($kamar->status === 'KOSONG')
                                    <span class="px-2 py-0.5 rounded-full bg-green-100 text-green-600 text-[10px] font-bold border border-green-200">{{ $kamar->status }}</span>
                                @elseif($kamar->status === 'ISI')
                                    <span class="px-2 py-0.5 rounded-full bg-red-100 text-red-500 text-[10px] font-bold border border-red-200">{{ $kamar->status }}</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full bg-neutral-100 text-neutral-500 text-[10px] font-bold border border-neutral-200">{{ $kamar->status }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center text-neutral-400">
                                <flux:icon name="magnifying-glass" class="w-8 h-8 mx-auto mb-2 opacity-30" />
                                <p class="text-sm font-medium">{{ strlen($searchKamar) < 2 ? 'Ketik minimal 2 karakter untuk mencari...' : 'Kamar tidak ditemukan.' }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </flux:modal>
</div>
