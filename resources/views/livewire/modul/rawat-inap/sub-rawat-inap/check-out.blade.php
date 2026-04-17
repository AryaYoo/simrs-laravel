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
                    <span>Check Out</span>
                </nav>
                <h1 class="text-xl font-bold text-neutral-800 dark:text-neutral-100">Proses Check Out / Pulang</h1>
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
        
        {{-- Left & Center Columns --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- 1. Informasi Kamar & Waktu Keluar --}}
            <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-sm border border-neutral-200 dark:border-neutral-700 overflow-hidden">
                <div class="p-4 border-b border-neutral-100 dark:border-neutral-700 bg-neutral-50/50 dark:bg-neutral-800/50 flex items-center gap-2">
                    <flux:icon name="clock" class="w-5 h-5 text-[#4C5C2D]" />
                    <h3 class="font-bold text-neutral-700 dark:text-neutral-300 uppercase tracking-tight text-sm">Informasi Stay & Waktu Keluar</h3>
                </div>
                <div class="p-5 space-y-5">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <flux:field>
                            <flux:label>Kamar / Bed Saat Ini</flux:label>
                            <flux:input value="{{ $currentKamarInapArray['kd_kamar'] ?? '-' }} ({{ $currentKamarInapArray['kamar']['bangsal']['nm_bangsal'] ?? '-' }})" readonly class="bg-neutral-50 font-medium" />
                        </flux:field>
                        <flux:field>
                            <flux:label>Tanggal & Jam Masuk</flux:label>
                            <flux:input value="{{ \Carbon\Carbon::parse($currentKamarInapArray['tgl_masuk'])->format('d/m/Y') }} {{ $currentKamarInapArray['jam_masuk'] }}" readonly class="bg-neutral-50 font-mono" />
                        </flux:field>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2 border-t border-neutral-100 dark:border-neutral-700/50">
                        <flux:field>
                            <flux:label>Tanggal Keluar / Pulang</flux:label>
                            <flux:input wire:model.live="tgl_keluar" type="date" />
                        </flux:field>
                        <flux:field>
                            <flux:label>Jam Keluar / Pulang</flux:label>
                            <flux:input wire:model.live="jam_keluar" type="time" step="1" />
                        </flux:field>
                    </div>
                </div>
            </div>

            {{-- 2. Informasi Diagnosa --}}
            <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-sm border border-neutral-200 dark:border-neutral-700 overflow-hidden">
                <div class="p-4 border-b border-neutral-100 dark:border-neutral-700 bg-neutral-50/50 dark:bg-neutral-800/50 flex items-center gap-2">
                    <flux:icon name="document-text" class="w-5 h-5 text-[#4C5C2D]" />
                    <h3 class="font-bold text-neutral-700 dark:text-neutral-300 uppercase tracking-tight text-sm">Diagnosa Pasien</h3>
                </div>
                <div class="p-5 space-y-5">
                    <flux:field>
                        <flux:label>Diagnosa Awal Masuk</flux:label>
                        <flux:input value="{{ $currentKamarInapArray['diagnosa_awal'] ?? '-' }}" readonly class="bg-neutral-50 italic" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Diagnosa Akhir Keluar (ICD-10)</flux:label>
                        <div class="flex gap-2">
                            <flux:input wire:model="kd_penyakit_akhir" placeholder="Kode ICD-10" readonly class="w-32 bg-neutral-50 font-mono" />
                            <flux:input wire:model="nm_penyakit_akhir" placeholder="Nama Penyakit..." readonly class="flex-1 bg-neutral-50" />
                            <button type="button" wire:click="openIcdModal" class="p-1.5 text-neutral-500 hover:text-[#4C5C2D] transition-colors border border-neutral-200 dark:border-neutral-700 rounded-lg hover:bg-neutral-50 dark:hover:bg-neutral-800">
                                <flux:icon name="paper-clip" class="w-4 h-4" />
                            </button>
                        </div>
                        <flux:error name="kd_penyakit_akhir" />
                    </flux:field>
                </div>
            </div>
        </div>

        {{-- Right Column: Billing & Status --}}
        <div class="space-y-6">
            
            {{-- 3. Status Pulang --}}
            <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-sm border border-neutral-200 dark:border-neutral-700 overflow-hidden">
                <div class="p-4 border-b border-neutral-100 dark:border-neutral-700 bg-neutral-50/50 dark:bg-neutral-800/50 flex items-center gap-2">
                    <flux:icon name="arrow-right-on-rectangle" class="w-5 h-5 text-[#4C5C2D]" />
                    <h3 class="font-bold text-neutral-700 dark:text-neutral-300 uppercase tracking-tight text-sm">Status Discharge</h3>
                </div>
                <div class="p-5">
                    <flux:field>
                        <flux:label>Status Pulang Pasien</flux:label>
                        <flux:select wire:model="stts_pulang">
                            @foreach($statusOptions as $option)
                                <option value="{{ $option }}">{{ $option }}</option>
                            @endforeach
                        </flux:select>
                    </flux:field>
                </div>
            </div>

            {{-- 4. Billing Summary --}}
            <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-sm border border-neutral-200 dark:border-neutral-700 overflow-hidden">
                <div class="p-4 border-b border-neutral-100 dark:border-neutral-700 bg-neutral-50/50 dark:bg-neutral-800/50 flex items-center gap-2">
                    <flux:icon name="calculator" class="w-5 h-5 text-[#4C5C2D]" />
                    <h3 class="font-bold text-neutral-700 dark:text-neutral-300 uppercase tracking-tight text-sm">Kalkulasi Kamar</h3>
                </div>
                <div class="p-5">
                    <div class="flex flex-col gap-4">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-neutral-500">Lama Inap</span>
                            <span class="font-bold text-[#4C5C2D]">{{ $lama }} Hari</span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-neutral-500">Tarif Kamar</span>
                            <span class="font-mono text-neutral-700 dark:text-neutral-300">Rp {{ number_format($currentKamarInapArray['trf_kamar'] ?? 0, 0, ',', '.') }}</span>
                        </div>
                        <div class="pt-3 border-t border-neutral-100 dark:border-neutral-700 flex flex-col items-center gap-1">
                            <p class="text-[10px] font-bold uppercase text-neutral-400 tracking-wider">Total Biaya Kamar</p>
                            <p class="text-2xl font-black text-neutral-800 dark:text-neutral-100">
                                <span class="text-xs font-normal text-neutral-400">Rp </span>{{ number_format($total_biaya, 0, ',', '.') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Submit Button --}}
            <div class="bg-[#F1F5E9] dark:bg-[#4C5C2D]/10 rounded-xl border border-[#4C5C2D]/20 p-5">
                <flux:button wire:click="save" variant="primary" icon="check-circle" class="w-full !bg-[#4C5C2D] hover:!bg-[#3D4A24] !border-none text-white shadow-md font-bold py-3 h-auto text-sm flex items-center justify-center gap-2">
                    Simpan Check Out
                </flux:button>
                <p class="text-[10px] text-neutral-500 text-center mt-3 italic">Pasien akan dinyatakan keluar dari kamar inap setelah proses ini.</p>
            </div>
        </div>
    </div>

    {{-- Modal ICD-10 Lookup --}}
    <flux:modal name="icd-lookup" wire:model="isIcdModalOpen" variant="flyout" class="w-full max-w-2xl">
        <div class="mb-4">
            <h3 class="font-bold text-neutral-800 dark:text-neutral-200 flex items-center gap-2 text-lg">
                <flux:icon name="magnifying-glass" class="w-5 h-5 text-[#4C5C2D] dark:text-[#8CC7C4]" />
                Cari Diagnosa (ICD-10)
            </h3>
        </div>

        <flux:input wire:model.live.debounce.300ms="searchIcd" icon="magnifying-glass" placeholder="Cari berdasarkan Kode atau Nama Penyakit..." autofocus class="mb-4" />

        <div class="overflow-y-auto border border-neutral-200 dark:border-neutral-700 rounded-lg" style="max-height: 60vh;">
            <table class="w-full text-sm text-left">
                <thead class="text-[10px] text-neutral-500 uppercase bg-neutral-50 dark:bg-neutral-900 border-b border-neutral-200 dark:border-neutral-700 sticky top-0 z-10 font-bold tracking-wider">
                    <tr>
                        <th class="px-4 py-3 w-32">Kode ICD-10</th>
                        <th class="px-4 py-3">Nama Penyakit</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100 dark:divide-neutral-800">
                    @forelse($listIcd as $icd)
                        <tr class="hover:bg-[#F1F5E9] dark:hover:bg-[#4C5C2D]/10 transition-colors cursor-pointer group" 
                            wire:click="selectIcd('{{ $icd->kd_penyakit }}', '{{ $icd->nm_penyakit }}')">
                            <td class="px-4 py-3 font-bold text-neutral-800 dark:text-neutral-100 group-hover:text-[#4C5C2D] font-mono">{{ $icd->kd_penyakit }}</td>
                            <td class="px-4 py-3 font-semibold text-neutral-700 dark:text-neutral-300">{{ $icd->nm_penyakit }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="px-4 py-12 text-center text-neutral-400">
                                <flux:icon name="magnifying-glass" class="w-8 h-8 mx-auto mb-2 opacity-30" />
                                <p class="text-sm font-medium">{{ strlen($searchIcd) < 3 ? 'Ketik minimal 3 karakter untuk mencari...' : 'Diagnosa tidak ditemukan.' }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </flux:modal>
</div>
