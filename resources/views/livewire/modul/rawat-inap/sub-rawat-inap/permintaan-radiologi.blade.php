<div class="flex flex-col gap-6 pb-8" x-data="{ activeTab: 'input' }">
    {{-- Header --}}
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
                    <a href="{{ route('modul.rawat-inap.perawatan-tindakan', str_replace('/', '-', $no_rawat)) }}" wire:navigate class="hover:underline">Perawatan</a>
                    <span class="mx-1">/</span>
                    <span>Permintaan Radiologi</span>
                </nav>
                <h1 class="text-xl font-bold text-neutral-800 dark:text-neutral-100">Permintaan Radiologi</h1>
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
                    @if($regPeriksa->kamarInap->first())
                        <span class="flex items-center gap-1"><flux:icon name="home" class="w-3 h-3"/> {{ $regPeriksa->kamarInap->first()->kamar->bangsal->nm_bangsal ?? '-' }} &middot; {{ $regPeriksa->kamarInap->first()->kd_kamar ?? '-' }}</span>
                    @endif
                </div>
            </div>
        </div>
        <div class="text-left md:text-right text-sm border-t md:border-t-0 md:border-l border-white/20 pt-3 md:pt-0 md:pl-4">
            <p class="text-white/80 text-xs mb-1">Dokter DPJP</p>
            <p class="font-semibold">{{ $regPeriksa->dokter->nm_dokter ?? '-' }}</p>
        </div>
    </div>

    {{-- Tab Navigation --}}
    <div class="border-b border-neutral-200 dark:border-neutral-700">
        <nav class="flex gap-6">
            <button @click="activeTab = 'input'"
                :class="activeTab === 'input' ? 'border-[#4C5C2D] text-[#4C5C2D] font-bold' : 'border-transparent text-neutral-400 hover:text-neutral-600'"
                class="flex items-center gap-2 px-1 pb-3 text-sm border-b-2 transition-all">
                <flux:icon name="pencil-square" class="w-4 h-4" />
                Input Permintaan
            </button>
            <button @click="activeTab = 'riwayat'"
                :class="activeTab === 'riwayat' ? 'border-[#4C5C2D] text-[#4C5C2D] font-bold' : 'border-transparent text-neutral-400 hover:text-neutral-600'"
                class="flex items-center gap-2 px-1 pb-3 text-sm border-b-2 transition-all">
                <flux:icon name="clock" class="w-4 h-4" />
                Riwayat
                @if(count($history) > 0)
                    <span class="ml-1 px-1.5 py-0.5 rounded-full bg-[#4C5C2D]/10 text-[#4C5C2D] text-[10px] font-black">{{ count($history) }}</span>
                @endif
            </button>
        </nav>
    </div>

    {{-- ===== TAB 1: INPUT PERMINTAAN ===== --}}
    <div x-show="activeTab === 'input'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="space-y-6">

        {{-- Main Content --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Left Column: Form Fields --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- Informasi Permintaan --}}
                <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-sm border border-neutral-200 dark:border-neutral-700 overflow-hidden">
                    <div class="p-4 border-b border-neutral-100 dark:border-neutral-700 bg-neutral-50/50 dark:bg-neutral-800/50 flex items-center gap-2">
                        <flux:icon name="pencil-square" class="w-5 h-5 text-[#4C5C2D]" />
                        <h3 class="font-bold text-neutral-700 dark:text-neutral-300 uppercase tracking-tight text-sm">Informasi Permintaan</h3>
                    </div>
                    <div class="p-5 space-y-5">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <flux:field>
                                <flux:label>Tanggal Periksa</flux:label>
                                <flux:input type="date" wire:model.live="tgl_permintaan" />
                            </flux:field>
                            <flux:field>
                                <div class="flex items-center justify-between mb-1">
                                    <flux:label>Jam Periksa</flux:label>
                                    <label class="flex items-center gap-1 cursor-pointer">
                                        <flux:checkbox wire:model.live="auto_waktu" class="size-3" />
                                        <span class="text-[10px] font-bold text-neutral-400 uppercase">Auto</span>
                                    </label>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <flux:input type="number" wire:model="jam_permintaan_jam" class="text-center font-mono" min="0" max="23" :disabled="$auto_waktu" />
                                    <span class="text-neutral-300 font-bold">:</span>
                                    <flux:input type="number" wire:model="jam_permintaan_menit" class="text-center font-mono" min="0" max="59" :disabled="$auto_waktu" />
                                    <span class="text-neutral-300 font-bold">:</span>
                                    <flux:input type="number" wire:model="jam_permintaan_detik" class="text-center font-mono" min="0" max="59" :disabled="$auto_waktu" />
                                </div>
                            </flux:field>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <flux:field>
                                <flux:label>No. Permintaan</flux:label>
                                <flux:input wire:model="predictedOrderNo" readonly class="bg-neutral-50 font-mono font-bold text-[#4C5C2D]" />
                                <flux:description class="text-[10px] mt-1 italic">Nomor ini bersifat sementara & akan dikunci saat simpan.</flux:description>
                            </flux:field>
                            <flux:field>
                                <flux:label>Dokter Perujuk (DPJP)</flux:label>
                                <div class="flex gap-2">
                                    <flux:input wire:model="nm_dokter_perujuk" readonly class="flex-1 bg-neutral-50 font-bold" />
                                    <button type="button" wire:click="openDokterModal" class="p-1.5 text-neutral-500 hover:text-[#4C5C2D] transition-colors border border-neutral-200 dark:border-neutral-700 rounded-lg hover:bg-neutral-50 dark:hover:bg-neutral-800">
                                        <flux:icon name="paper-clip" class="w-4 h-4" />
                                    </button>
                                </div>
                            </flux:field>
                        </div>
                    </div>
                </div>

                {{-- Diagnosis & Info Tambahan --}}
                <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-sm border border-neutral-200 dark:border-neutral-700 overflow-hidden">
                    <div class="p-4 border-b border-neutral-100 dark:border-neutral-700 bg-neutral-50/50 dark:bg-neutral-800/50 flex items-center gap-2">
                        <flux:icon name="document-text" class="w-5 h-5 text-[#4C5C2D]" />
                        <h3 class="font-bold text-neutral-700 dark:text-neutral-300 uppercase tracking-tight text-sm">Indikasi & Informasi Tambahan</h3>
                    </div>
                    <div class="p-5 space-y-5">
                        <flux:field>
                            <flux:label>Indikasi Pemeriksaan / Diagnosis Klinis</flux:label>
                            <flux:textarea wire:model="diagnosa_klinis" rows="2" placeholder="Masukkan diagnosa klinis atau alasan pemeriksaan..." class="resize-none" />
                        </flux:field>
                        <flux:field>
                            <flux:label>Informasi Tambahan Permintaan Foto</flux:label>
                            <flux:textarea wire:model="informasi_tambahan" rows="2" placeholder="Contoh: Thorax 2 posisi, Cito, dll..." class="resize-none" />
                        </flux:field>
                    </div>
                </div>
            </div>

            {{-- Right Column: Summary & Submit --}}
            <div class="space-y-6">
                {{-- Selected Items Summary --}}
                <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-sm border border-neutral-200 dark:border-neutral-700 overflow-hidden">
                    <div class="p-4 border-b border-neutral-100 dark:border-neutral-700 bg-neutral-50/50 dark:bg-neutral-800/50 flex items-center gap-2">
                        <flux:icon name="clipboard-document-list" class="w-5 h-5 text-[#4C5C2D]" />
                        <h3 class="font-bold text-neutral-700 dark:text-neutral-300 uppercase tracking-tight text-sm">Item Terpilih</h3>
                    </div>
                    <div class="p-5">
                        @if(count($selectedTests) > 0)
                            <div class="space-y-2 mb-4">
                                @foreach($selectedTests as $kd)
                                    @php $item = \App\Models\JnsPerawatanRadiologi::find($kd); @endphp
                                    @if($item)
                                    <div class="flex items-center justify-between p-2.5 rounded-lg bg-[#F1F5E9] dark:bg-[#4C5C2D]/10 border border-[#4C5C2D]/10">
                                        <div>
                                            <p class="text-xs font-bold text-neutral-800 dark:text-neutral-200 uppercase tracking-tight">{{ $item->nm_perawatan }}</p>
                                            <p class="text-[10px] font-mono text-neutral-400">{{ $item->kd_jenis_prw }}</p>
                                        </div>
                                        <p class="text-xs font-mono font-bold text-neutral-600">Rp {{ number_format($item->total_byr, 0, ',', '.') }}</p>
                                    </div>
                                    @endif
                                @endforeach
                            </div>
                            <div class="flex items-center justify-between p-3 rounded-lg bg-neutral-50 dark:bg-neutral-900/50 border border-neutral-100 dark:border-neutral-700">
                                <span class="text-xs font-bold text-neutral-500 uppercase tracking-wider">Total Item</span>
                                <span class="text-lg font-black text-[#4C5C2D]">{{ count($selectedTests) }}</span>
                            </div>
                        @else
                            <div class="py-8 text-center">
                                <flux:icon name="clipboard-document-list" class="w-10 h-10 mx-auto mb-2 text-neutral-200" />
                                <p class="text-xs font-bold text-neutral-400 uppercase tracking-wide">Belum ada pemeriksaan dipilih</p>
                                <p class="text-[10px] text-neutral-400 mt-1">Pilih dari tabel di bawah</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Submit Button --}}
                <div class="bg-[#F1F5E9] dark:bg-[#4C5C2D]/10 rounded-xl border border-[#4C5C2D]/20 p-5">
                    <flux:button wire:click="save" variant="primary" icon="paper-airplane" class="w-full !bg-[#4C5C2D] hover:!bg-[#3D4A24] !border-none text-white shadow-md font-bold py-3 h-auto text-sm flex items-center justify-center gap-2" wire:loading.attr="disabled">
                        Kirim Permintaan Radiologi
                    </flux:button>
                    <p class="text-[10px] text-neutral-500 text-center mt-3">Pastikan data sudah benar sebelum mengirim permintaan.</p>
                </div>
            </div>
        </div>

        {{-- Pemeriksaan Selection Table --}}
        <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-sm border border-neutral-200 dark:border-neutral-700 overflow-hidden">
            <div class="p-4 border-b border-neutral-100 dark:border-neutral-700 bg-neutral-50/50 dark:bg-neutral-800/50 flex flex-col md:flex-row md:items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <flux:icon name="magnifying-glass" class="w-5 h-5 text-[#4C5C2D]" />
                    <h3 class="font-bold text-neutral-700 dark:text-neutral-300 uppercase tracking-tight text-sm">Pilih Pemeriksaan Radiologi</h3>
                </div>
                <div class="w-full md:w-80">
                    <flux:input wire:model.live.debounce.300ms="searchPemeriksaan" placeholder="Cari kode atau nama pemeriksaan..." icon="magnifying-glass" />
                </div>
            </div>

            <div class="overflow-x-auto">
                <flux:table :paginate="$pemeriksaanList">
                    <flux:table.columns>
                        <flux:table.column width="40px" align="center">P</flux:table.column>
                        <flux:table.column>Kode Periksa</flux:table.column>
                        <flux:table.column>Nama Pemeriksaan</flux:table.column>
                        <flux:table.column align="right">Tarif</flux:table.column>
                    </flux:table.columns>
                    <flux:table.rows>
                        @forelse ($pemeriksaanList as $pemeriksaan)
                            <flux:table.row :key="$pemeriksaan->kd_jenis_prw" class="hover:bg-[#F1F5E9] dark:hover:bg-[#4C5C2D]/10 transition-colors cursor-pointer">
                                <flux:table.cell align="center">
                                    <flux:checkbox wire:model.live="selectedTests" value="{{ $pemeriksaan->kd_jenis_prw }}" />
                                </flux:table.cell>
                                <flux:table.cell class="font-mono text-xs font-bold text-neutral-500">{{ $pemeriksaan->kd_jenis_prw }}</flux:table.cell>
                                <flux:table.cell class="font-semibold text-neutral-700 dark:text-neutral-300 uppercase tracking-tight">{{ $pemeriksaan->nm_perawatan }}</flux:table.cell>
                                <flux:table.cell align="right" class="font-mono text-xs text-neutral-600 dark:text-neutral-400">Rp {{ number_format($pemeriksaan->total_byr, 0, ',', '.') }}</flux:table.cell>
                            </flux:table.row>
                        @empty
                            <flux:table.row>
                                <flux:table.cell colspan="4" class="py-12 text-center">
                                    <flux:icon name="magnifying-glass" class="w-8 h-8 mx-auto mb-2 opacity-30" />
                                    <p class="text-sm font-medium text-neutral-400">Pemeriksaan tidak ditemukan</p>
                                </flux:table.cell>
                            </flux:table.row>
                        @endforelse
                    </flux:table.rows>
                </flux:table>
            </div>
        </div>
    </div>

    {{-- ===== TAB 2: RIWAYAT ===== --}}
    <div x-show="activeTab === 'riwayat'" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
        <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-sm border border-neutral-200 dark:border-neutral-700 overflow-hidden">
            <div class="p-4 border-b border-neutral-100 dark:border-neutral-700 bg-neutral-50/50 dark:bg-neutral-800/50 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <flux:icon name="clock" class="w-5 h-5 text-[#4C5C2D]" />
                    <h3 class="font-bold text-neutral-700 dark:text-neutral-300 uppercase tracking-tight text-sm">Riwayat Permintaan Radiologi</h3>
                </div>
                <span class="text-xs font-bold text-neutral-400">{{ count($history) }} permintaan</span>
            </div>

            @forelse($history as $item)
                <div class="border-b border-neutral-100 dark:border-neutral-700 last:border-b-0">
                    {{-- Row Header --}}
                    <div class="px-5 py-4 flex flex-col md:flex-row md:items-center justify-between gap-3">
                        <div class="flex items-center gap-4">
                            {{-- Status Indicator --}}
                            <div class="flex-shrink-0">
                                @if($item->tgl_sampel == '1000-01-01' || $item->tgl_sampel == '0000-00-00')
                                    <div class="w-10 h-10 rounded-lg bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 flex items-center justify-center">
                                        <flux:icon name="clock" class="w-5 h-5 text-amber-500" />
                                    </div>
                                @else
                                    <div class="w-10 h-10 rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 flex items-center justify-center">
                                        <flux:icon name="check-circle" class="w-5 h-5 text-green-500" />
                                    </div>
                                @endif
                            </div>

                            {{-- Order Info --}}
                            <div>
                                <div class="flex items-center gap-2 mb-0.5">
                                    <span class="font-mono text-sm font-black text-[#4C5C2D]">{{ $item->noorder }}</span>
                                    @if($item->tgl_sampel == '1000-01-01' || $item->tgl_sampel == '0000-00-00')
                                        <span class="px-1.5 py-0.5 rounded bg-amber-100 text-amber-600 text-[9px] font-bold uppercase tracking-tight border border-amber-200">Menunggu</span>
                                    @else
                                        <span class="px-1.5 py-0.5 rounded bg-green-100 text-green-600 text-[9px] font-bold uppercase tracking-tight border border-green-200">Diproses</span>
                                    @endif
                                </div>
                                <p class="text-xs text-neutral-500">
                                    {{ \Carbon\Carbon::parse($item->tgl_permintaan)->format('d M Y') }}
                                    <span class="font-mono text-neutral-400">&middot; {{ $item->jam_permintaan }}</span>
                                </p>
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="flex items-center gap-2 ml-14 md:ml-0">
                            @if($item->tgl_sampel == '1000-01-01' || $item->tgl_sampel == '0000-00-00')
                                <flux:button variant="ghost" size="xs" icon="trash" class="text-rose-500 hover:text-rose-600 hover:bg-rose-50 rounded-lg"
                                    wire:click="batalPermintaan('{{ $item->noorder }}')"
                                    wire:confirm="Yakin ingin membatalkan permintaan ini?">
                                    Batalkan
                                </flux:button>
                            @endif
                        </div>
                    </div>

                    {{-- Details Grid --}}
                    <div class="px-5 pb-4 ml-14">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 p-4 rounded-lg bg-neutral-50/80 dark:bg-neutral-900/30 border border-neutral-100 dark:border-neutral-700/50">
                            {{-- Dokter --}}
                            <div>
                                <p class="text-[10px] font-bold text-neutral-400 uppercase tracking-wider mb-1">Dokter Perujuk</p>
                                <p class="text-xs font-semibold text-neutral-700 dark:text-neutral-300">{{ $item->dokter->nm_dokter ?? '-' }}</p>
                            </div>
                            {{-- Indikasi --}}
                            <div>
                                <p class="text-[10px] font-bold text-neutral-400 uppercase tracking-wider mb-1">Indikasi Klinis</p>
                                <p class="text-xs text-neutral-600 dark:text-neutral-400 leading-snug">{{ $item->diagnosa_klinis ?: '-' }}</p>
                            </div>
                            {{-- Info Tambahan --}}
                            <div>
                                <p class="text-[10px] font-bold text-neutral-400 uppercase tracking-wider mb-1">Info Tambahan</p>
                                <p class="text-xs text-neutral-600 dark:text-neutral-400 leading-snug">{{ $item->informasi_tambahan ?: '-' }}</p>
                            </div>
                        </div>

                        {{-- Pemeriksaan Items --}}
                        <div class="mt-3">
                            <p class="text-[10px] font-bold text-neutral-400 uppercase tracking-wider mb-2">Pemeriksaan Diminta</p>
                            <div class="flex flex-wrap gap-1.5">
                                @foreach($item->detailPemeriksaan as $detail)
                                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-md bg-[#F1F5E9] dark:bg-[#4C5C2D]/10 border border-[#4C5C2D]/10 text-[10px] font-bold text-neutral-700 dark:text-neutral-300 uppercase tracking-tight">
                                        <flux:icon name="photo" class="w-3 h-3 text-[#4C5C2D] opacity-50" />
                                        {{ $detail->pemeriksaan->nm_perawatan ?? $detail->kd_jenis_prw }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="py-16 text-center">
                    <flux:icon name="clock" class="w-12 h-12 mx-auto mb-3 text-neutral-200" />
                    <p class="text-sm font-bold text-neutral-400">Belum ada riwayat permintaan</p>
                    <p class="text-xs text-neutral-400 mt-1">Permintaan yang telah dikirim akan muncul di sini</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Modal Dokter Lookup --}}
    <flux:modal wire:model="isDokterModalOpen" variant="flyout" class="w-full max-w-lg">
        <div class="mb-4">
            <h3 class="font-bold text-neutral-800 dark:text-neutral-200 flex items-center gap-2 text-lg">
                <flux:icon name="magnifying-glass" class="w-5 h-5 text-[#4C5C2D]" />
                Cari Dokter Perujuk
            </h3>
            <p class="text-sm text-neutral-500 mt-1">Pilih dokter yang melakukan rujukan/permintaan.</p>
        </div>

        <flux:input wire:model.live.debounce.300ms="searchDokterModal" icon="magnifying-glass" placeholder="Cari berdasarkan nama dokter..." autofocus class="mb-4" />

        <div class="overflow-y-auto border border-neutral-200 dark:border-neutral-700 rounded-lg" style="max-height: 60vh;">
            <table class="w-full text-sm text-left">
                <thead class="text-[10px] text-neutral-500 uppercase bg-neutral-50 dark:bg-neutral-900 border-b border-neutral-200 dark:border-neutral-700 sticky top-0 z-10 font-bold tracking-wider">
                    <tr>
                        <th class="px-4 py-3">Kode</th>
                        <th class="px-4 py-3">Nama Dokter</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100 dark:divide-neutral-800">
                    @forelse($listDokter as $doc)
                        <tr class="hover:bg-[#F1F5E9] dark:hover:bg-[#4C5C2D]/10 transition-colors cursor-pointer group"
                            wire:click="selectDokter('{{ $doc['kd_dokter'] }}', '{{ $doc['nm_dokter'] }}')">
                            <td class="px-4 py-3 font-mono text-xs text-neutral-500">{{ $doc['kd_dokter'] }}</td>
                            <td class="px-4 py-3 font-bold text-neutral-800 dark:text-neutral-100 group-hover:text-[#4C5C2D]">{{ $doc['nm_dokter'] }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="px-4 py-12 text-center text-neutral-400">
                                <flux:icon name="magnifying-glass" class="w-8 h-8 mx-auto mb-2 opacity-30" />
                                <p class="text-sm font-medium">Ketik nama dokter untuk mencari...</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </flux:modal>
</div>
