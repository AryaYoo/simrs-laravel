<div class="flex flex-col gap-6 pb-8">
    {{-- Header / Breadcrumb --}}
    <div class="flex items-center justify-between">
        <div>
            <nav class="text-xs text-neutral-400 mb-0.5">
                <a href="{{ route('modul.index') }}" wire:navigate class="hover:underline">Modul</a>
                <span class="mx-1">/</span>
                <a href="{{ route('modul.rawat-inap.index') }}" wire:navigate class="hover:underline">Rawat Inap</a>
                <span class="mx-1">/</span>
                <span>Permintaan Rawat Inap</span>
            </nav>
            <h1 class="text-xl font-bold text-neutral-800 dark:text-neutral-100">Permintaan Rawat Inap</h1>
        </div>
    </div>

    @if($isCheckInOpen)
        {{-- Patient Card Banner --}}
        <div class="bg-[#4C5C2D] rounded-xl p-5 text-white shadow-sm flex flex-col md:flex-row justify-between md:items-center gap-4 relative overflow-hidden">
            <div class="absolute -right-10 -top-10 opacity-10 pointer-events-none">
                <flux:icon name="user" class="w-48 h-48" />
            </div>
            <div class="flex flex-col z-10">
                <div class="flex items-center gap-2 mb-1">
                    <span class="text-xs bg-white/20 px-2 py-0.5 rounded-full border border-white/20">{{ $checkInData['reg_periksa']['no_rkm_medis'] ?? '-' }}</span>
                    <span class="text-xs bg-white/20 px-2 py-0.5 rounded-full border border-white/20">{{ $checkInData['no_rawat'] ?? '-' }}</span>
                </div>
                <h2 class="text-xl font-bold tracking-tight">{{ $checkInData['reg_periksa']['pasien']['nm_pasien'] ?? '-' }}</h2>
                <div class="flex items-center gap-3 mt-1 text-sm text-white/80">
                    <span class="flex items-center gap-1.5"><flux:icon name="identification" class="w-4 h-4" /> {{ $checkInData['reg_periksa']['umurdaftar'] ?? '-' }} {{ $checkInData['reg_periksa']['sttsumur'] ?? '-' }}</span>
                    <span class="flex items-center gap-1.5"><flux:icon name="users" class="w-4 h-4" /> {{ ($checkInData['reg_periksa']['pasien']['jk'] ?? '') == 'L' ? 'Laki-laki' : 'Perempuan' }}</span>
                    <span class="flex items-center gap-1.5"><flux:icon name="phone" class="w-4 h-4" /> {{ $checkInData['reg_periksa']['pasien']['no_tlp'] ?? '-' }}</span>
                </div>
            </div>
            <div class="md:text-right border-t md:border-t-0 md:border-l border-white/20 pt-3 md:pt-0 md:pl-5 z-10">
                <p class="text-[10px] uppercase tracking-wider text-white/70 font-semibold mb-0.5">Kamar Diminta</p>
                <p class="font-bold text-sm">{{ $checkInData['kamar']['kd_kamar'] ?? '-' }} - {{ $checkInData['kamar']['bangsal']['nm_bangsal'] ?? '-' }}</p>
            </div>
        </div>

        {{-- Form Check In --}}
        <div class="bg-white dark:bg-neutral-800 rounded-xl border border-neutral-200 dark:border-neutral-700 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-neutral-100 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800/80 flex justify-between items-center">
                <h3 class="font-bold text-neutral-800 dark:text-neutral-200">Form Masuk / Check In</h3>
                <button wire:click="closeCheckIn" class="text-neutral-500 hover:text-red-500 transition-colors">
                    <flux:icon name="x-mark" class="w-5 h-5" />
                </button>
            </div>
            <div class="p-6 space-y-6">
                {{-- Kamar --}}
                <div class="flex flex-wrap md:flex-nowrap gap-3 items-end">
                    <flux:field class="w-full md:w-1/4">
                        <flux:label>Kode Bangsal</flux:label>
                        <flux:input wire:model="kd_bangsal" readonly class="bg-neutral-50 font-mono" />
                    </flux:field>
                    <flux:field class="w-full md:w-1/4">
                        <flux:label>Kode Kamar</flux:label>
                        <flux:input wire:model="kd_kamar" readonly class="bg-neutral-50 font-mono" />
                    </flux:field>
                    <flux:field class="w-full md:w-2/5">
                        <flux:label>Nama Bangsal / Kamar</flux:label>
                        <flux:input wire:model="nm_bangsal" readonly class="bg-neutral-50 font-medium" />
                    </flux:field>
                    <button type="button" wire:click="openKamarModal" class="h-10 px-3 text-neutral-500 hover:text-[#4C5C2D] transition-colors border border-neutral-200 dark:border-neutral-700 rounded-lg hover:bg-neutral-50 dark:hover:bg-neutral-800 flex items-center justify-center">
                        <flux:icon name="paper-clip" class="w-5 h-5" />
                    </button>
                    <flux:field class="w-full md:w-1/4">
                        <flux:label>Stts. Kamar</flux:label>
                        <flux:input wire:model="stts_kamar" readonly class="bg-neutral-50 font-mono font-bold text-center" />
                    </flux:field>
                </div>

                {{-- Tanggal & Jam + Diagnosa --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <flux:field>
                            <flux:label>Tanggal Masuk</flux:label>
                            <flux:input type="date" wire:model="tanggal_masuk" />
                        </flux:field>
                        <flux:field>
                            <flux:label>Jam Masuk</flux:label>
                            <flux:input type="time" step="1" wire:model="jam_masuk" />
                        </flux:field>
                    </div>
                    <div>
                        <flux:field>
                            <flux:label>Diagnosa Awal Masuk</flux:label>
                            <div class="flex gap-2">
                                <flux:input wire:model="diagnosa_awal" placeholder="Pilih diagnosa..." readonly class="bg-neutral-50" />
                                <button type="button" wire:click="openDiagnosaModal" class="h-10 px-3 text-neutral-500 hover:text-[#4C5C2D] transition-colors border border-neutral-200 dark:border-neutral-700 rounded-lg hover:bg-neutral-50 dark:hover:bg-neutral-800 flex items-center justify-center">
                                    <flux:icon name="paper-clip" class="w-5 h-5" />
                                </button>
                            </div>
                        </flux:field>
                    </div>
                </div>

                {{-- Biaya --}}
                <div class="flex flex-wrap md:flex-nowrap gap-3 items-end">
                    <flux:field class="w-full md:w-1/5">
                        <flux:label>Lama (Hari)</flux:label>
                        <flux:input type="number" wire:model.live="lama_inap" min="1" class="text-center" />
                    </flux:field>
                    <div class="pb-2 text-neutral-500 font-bold">X</div>
                    <flux:field class="w-full md:w-2/5">
                        <flux:label>Tarif Kamar</flux:label>
                        <flux:input type="number" wire:model.live="tarif_kamar" class="font-mono font-bold" />
                    </flux:field>
                    <div class="pb-2 text-neutral-500 font-bold">=</div>
                    <flux:field class="w-full md:w-2/5">
                        <flux:label>Total Biaya (Estimasi)</flux:label>
                        <flux:input value="{{ number_format($tarif_kamar * $lama_inap, 0, ',', '.') }}" readonly class="bg-neutral-50 font-mono font-bold text-[#d92d20]" />
                    </flux:field>
                </div>

                <div class="text-sm font-medium text-neutral-600 dark:text-neutral-400">
                    Proses: Masuk/Check In
                </div>
            </div>

            <div class="px-6 py-4 border-t border-neutral-100 dark:border-neutral-800 flex justify-between bg-neutral-50/50 dark:bg-neutral-900 rounded-b-xl border-dashed">
                <div class="flex gap-3">
                    <flux:button type="button" wire:click="saveCheckIn" icon="document-check" style="background-color: #f1f5f9; color: #334155; border: 1px solid #cbd5e1; font-weight: 600;">
                        <span class="underline underline-offset-2 decoration-1 text-[#0f172a]">S</span>impan
                    </flux:button>
                    <flux:button type="button" wire:click="closeCheckIn" icon="no-symbol" style="background-color: #f1f5f9; color: #ef4444; border: 1px solid #cbd5e1; font-weight: 600;">
                        <span class="underline underline-offset-2 decoration-1 text-[#ef4444]">B</span>atal
                    </flux:button>
                </div>
                <flux:button type="button" wire:click="closeCheckIn" icon="x-mark" style="background-color: #f1f5f9; color: #b91c1c; border: 1px solid #cbd5e1; font-weight: 600;">
                    <span class="underline underline-offset-2 decoration-1 text-[#b91c1c]">T</span>utup
                </flux:button>
            </div>
        </div>

    @else
        {{-- TABS --}}
        <div class="flex items-center gap-1 bg-neutral-100/50 dark:bg-neutral-900/50 p-1 rounded-lg border border-neutral-200/50 dark:border-neutral-700/50 w-fit">
            <button wire:click="$set('activeTab', 'antrian')"
                class="relative px-4 py-1.5 rounded-md text-sm font-medium transition-all {{ $activeTab === 'antrian' ? 'bg-white dark:bg-neutral-800 text-[#4C5C2D] dark:text-[#8CC7C4] shadow-sm ring-1 ring-neutral-200 dark:ring-neutral-700' : 'text-neutral-500 hover:text-neutral-700 dark:text-neutral-400' }}">
                Antrian Pending
                @if($pendingCount > 0)
                    <span class="absolute -top-1.5 -right-1.5 min-w-[18px] h-[18px] flex items-center justify-center text-[10px] font-bold bg-red-500 text-white rounded-full px-1 shadow">{{ $pendingCount }}</span>
                @endif
            </button>
            <button wire:click="$set('activeTab', 'riwayat')"
                class="px-4 py-1.5 rounded-md text-sm font-medium transition-all {{ $activeTab === 'riwayat' ? 'bg-white dark:bg-neutral-800 text-[#4C5C2D] dark:text-[#8CC7C4] shadow-sm ring-1 ring-neutral-200 dark:ring-neutral-700' : 'text-neutral-500 hover:text-neutral-700 dark:text-neutral-400' }}">
                Riwayat
            </button>
        </div>

        @if($activeTab === 'antrian')
            <div wire:key="tab-antrian" class="space-y-4">
                {{-- Search Antrian --}}
                <div class="flex flex-col sm:flex-row justify-between gap-4">
                <div class="relative w-full sm:w-96">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <flux:icon name="magnifying-glass" class="w-5 h-5 text-neutral-400" />
                    </div>
                    <input type="text" wire:model.live.debounce.300ms="search" class="w-full pl-10 pr-4 py-2 border border-neutral-200 dark:border-neutral-700 rounded-lg bg-white dark:bg-neutral-800 text-neutral-800 dark:text-neutral-200 focus:outline-none focus:ring-2 focus:ring-[#4C5C2D] shadow-sm placeholder:text-neutral-400" placeholder="Cari no RM atau nama pasien...">
                </div>
            </div>

            {{-- Table Antrian --}}
            <div class="bg-white dark:bg-neutral-800 rounded-xl border border-neutral-200 dark:border-neutral-700 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm whitespace-nowrap">
                        <thead>
                            <tr class="bg-neutral-50 dark:bg-neutral-800/80 border-b border-neutral-200 dark:border-neutral-700 text-xs text-neutral-500 uppercase tracking-wider">
                                <th class="px-4 py-3 font-semibold">No. Rawat</th>
                                <th class="px-4 py-3 font-semibold">No. RM</th>
                                <th class="px-4 py-3 font-semibold">Nama Pasien</th>
                                <th class="px-4 py-3 font-semibold text-center">J.K.</th>
                                <th class="px-4 py-3 font-semibold">Umur</th>
                                <th class="px-4 py-3 font-semibold">Kamar Diminta</th>
                                <th class="px-4 py-3 font-semibold">Tanggal</th>
                                <th class="px-4 py-3 font-semibold text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-100 dark:divide-neutral-800">
                            @forelse($listPermintaan as $ranap)
                                <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-800/50 transition-colors">
                                    <td class="px-4 py-3 text-neutral-700 dark:text-neutral-300 text-xs font-mono">{{ $ranap->no_rawat }}</td>
                                    <td class="px-4 py-3 font-medium text-neutral-700 dark:text-neutral-300">{{ $ranap->regPeriksa->no_rkm_medis }}</td>
                                    <td class="px-4 py-3 font-bold text-neutral-900 dark:text-neutral-100">{{ $ranap->regPeriksa->pasien->nm_pasien }}</td>
                                    <td class="px-4 py-3 text-center text-neutral-600 dark:text-neutral-400">{{ $ranap->regPeriksa->pasien->jk }}</td>
                                    <td class="px-4 py-3 text-neutral-600 dark:text-neutral-400">{{ $ranap->regPeriksa->umurdaftar }} {{ $ranap->regPeriksa->sttsumur }}</td>
                                    <td class="px-4 py-3 font-medium text-neutral-700 dark:text-neutral-300">{{ $ranap->kamar->kd_kamar ?? '' }} {{ $ranap->kamar->bangsal->nm_bangsal ?? '-' }}</td>
                                    <td class="px-4 py-3 text-neutral-600 dark:text-neutral-400">{{ $ranap->tanggal }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <div class="flex items-center justify-center gap-1">
                                            <button wire:click="showDetail('{{ str_replace('/', '-', $ranap->no_rawat) }}')" class="inline-flex items-center justify-center px-2 h-7 rounded bg-neutral-100 text-neutral-600 hover:bg-[#4C5C2D] hover:text-white transition-colors border border-neutral-200 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-300 shadow-sm gap-1" title="Detail">
                                                <flux:icon name="eye" class="w-4 h-4" /><span class="text-xs font-semibold">View</span>
                                            </button>
                                            <button wire:click="openCheckIn('{{ str_replace('/', '-', $ranap->no_rawat) }}')" class="inline-flex items-center justify-center px-2 h-7 rounded bg-[#4C5C2D]/10 text-[#4C5C2D] hover:bg-[#4C5C2D] hover:text-white transition-colors border border-[#4C5C2D]/20 shadow-sm gap-1" title="Check In">
                                                <flux:icon name="arrow-right-end-on-rectangle" class="w-4 h-4" /><span class="text-xs font-semibold">Input</span>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-4 py-10 text-center text-neutral-500">
                                        <div class="flex flex-col items-center justify-center">
                                            <flux:icon name="check-circle" class="w-8 h-8 text-green-300 mb-2" />
                                            <p>Tidak ada antrian permintaan rawat inap yang pending.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($listPermintaan->hasPages())
                    <div class="px-5 py-3 border-t border-neutral-100 dark:border-neutral-700 bg-neutral-50/50 dark:bg-neutral-800/50">
                        {{ $listPermintaan->links(data: ['scrollTo' => false]) }}
                    </div>
                @endif
            </div>
            </div>

        @elseif($activeTab === 'riwayat')
            <div wire:key="tab-riwayat" class="space-y-4">
                {{-- Filter Riwayat --}}
                <div class="bg-white dark:bg-neutral-800 rounded-xl border border-neutral-200 dark:border-neutral-700 shadow-sm p-4">
                <div class="flex flex-wrap gap-3 items-end">
                    <div class="flex flex-col gap-1">
                        <label class="text-xs text-neutral-500 font-medium">Tanggal Dari</label>
                        <input type="date" wire:model.live="filterTanggalMulai" class="text-sm rounded-lg border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-neutral-700 dark:text-neutral-200 h-9 px-3 focus:ring-2 focus:ring-[#4C5C2D]">
                    </div>
                    <div class="flex flex-col gap-1">
                        <label class="text-xs text-neutral-500 font-medium">s/d</label>
                        <input type="date" wire:model.live="filterTanggalSelesai" class="text-sm rounded-lg border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-neutral-700 dark:text-neutral-200 h-9 px-3 focus:ring-2 focus:ring-[#4C5C2D]">
                    </div>
                    <div class="flex flex-col gap-1">
                        <label class="text-xs text-neutral-500 font-medium">Cara Bayar</label>
                        <select wire:model.live="filterCaraBayar" class="text-sm rounded-lg border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-neutral-700 dark:text-neutral-200 h-9 py-0 px-3 focus:ring-2 focus:ring-[#4C5C2D]">
                            <option value="">Semua</option>
                            @foreach($listPenjab as $pj)
                                <option value="{{ $pj->kd_pj }}">{{ $pj->png_jawab }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="relative flex-1 min-w-[200px]">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <flux:icon name="magnifying-glass" class="w-4 h-4 text-neutral-400" />
                        </div>
                        <input type="text" wire:model.live.debounce.300ms="searchRiwayat" class="w-full pl-9 pr-4 py-2 h-9 border border-neutral-200 dark:border-neutral-700 rounded-lg bg-white dark:bg-neutral-900 text-neutral-800 dark:text-neutral-200 text-sm focus:outline-none focus:ring-2 focus:ring-[#4C5C2D] placeholder:text-neutral-400" placeholder="Cari no RM / nama pasien...">
                    </div>
                </div>
            </div>

            {{-- Table Riwayat --}}
            <div class="bg-white dark:bg-neutral-800 rounded-xl border border-neutral-200 dark:border-neutral-700 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm whitespace-nowrap">
                        <thead>
                            <tr class="bg-neutral-50 dark:bg-neutral-800/80 border-b border-neutral-200 dark:border-neutral-700 text-xs text-neutral-500 uppercase tracking-wider">
                                <th class="px-4 py-3 font-semibold">No. Rawat</th>
                                <th class="px-4 py-3 font-semibold">No. RM</th>
                                <th class="px-4 py-3 font-semibold">Nama Pasien</th>
                                <th class="px-4 py-3 font-semibold">Cara Bayar</th>
                                <th class="px-4 py-3 font-semibold">Kamar Diminta</th>
                                <th class="px-4 py-3 font-semibold">Tanggal</th>
                                <th class="px-4 py-3 font-semibold text-center">Status</th>
                                <th class="px-4 py-3 font-semibold text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-100 dark:divide-neutral-800">
                            @forelse($riwayatList as $ranap)
                                <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-800/50 transition-colors">
                                    <td class="px-4 py-3 text-xs font-mono text-neutral-600 dark:text-neutral-400">{{ $ranap->no_rawat }}</td>
                                    <td class="px-4 py-3 font-medium text-neutral-700 dark:text-neutral-300">{{ $ranap->regPeriksa->no_rkm_medis }}</td>
                                    <td class="px-4 py-3 font-bold text-neutral-900 dark:text-neutral-100">
                                        {{ $ranap->regPeriksa->pasien->nm_pasien }}
                                        <span class="ml-1 text-xs font-normal text-neutral-400">{{ $ranap->regPeriksa->umurdaftar }}{{ $ranap->regPeriksa->sttsumur }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-neutral-600 dark:text-neutral-400">{{ $ranap->regPeriksa->penjab->png_jawab ?? '-' }}</td>
                                    <td class="px-4 py-3 text-neutral-700 dark:text-neutral-300">{{ $ranap->kamar->kd_kamar ?? '' }} {{ $ranap->kamar->bangsal->nm_bangsal ?? '-' }}</td>
                                    <td class="px-4 py-3 text-neutral-600 dark:text-neutral-400">{{ $ranap->tanggal }}</td>
                                    <td class="px-4 py-3 text-center">
                                        @if($ranap->kamarInap)
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 text-xs rounded-full bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400 font-semibold">
                                                <flux:icon name="check-circle" class="w-3 h-3" /> Sudah Masuk
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 text-xs rounded-full bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400 font-semibold">
                                                <flux:icon name="clock" class="w-3 h-3" /> Menunggu
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <button wire:click="showDetail('{{ str_replace('/', '-', $ranap->no_rawat) }}')" class="inline-flex items-center justify-center px-2 h-7 rounded bg-neutral-100 text-neutral-600 hover:bg-[#4C5C2D] hover:text-white transition-colors border border-neutral-200 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-300 shadow-sm gap-1">
                                            <flux:icon name="eye" class="w-4 h-4" /><span class="text-xs font-semibold">View</span>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-4 py-10 text-center text-neutral-500">
                                        <div class="flex flex-col items-center justify-center">
                                            <flux:icon name="document-text" class="w-8 h-8 text-neutral-300 mb-2" />
                                            <p>Tidak ada data riwayat ditemukan pada periode ini.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($riwayatList->hasPages())
                    <div class="px-5 py-3 border-t border-neutral-100 dark:border-neutral-700 bg-neutral-50/50 dark:bg-neutral-800/50">
                        {{ $riwayatList->links(data: ['scrollTo' => false]) }}
                    </div>
                @endif
            </div>
            </div>
        @endif
    @endif

    {{-- Detail Modal --}}
    @if($detailModalOpen)
    <div class="fixed inset-0 z-[100] flex items-center justify-center bg-neutral-900/50 backdrop-blur-sm p-4 overflow-y-auto">
        <div class="bg-white dark:bg-neutral-900 rounded-2xl w-full max-w-3xl shadow-2xl overflow-hidden border border-neutral-200 dark:border-neutral-800 transform transition-all relative mt-10 mb-10">
            <div class="px-6 py-4 border-b border-neutral-100 dark:border-neutral-800 flex justify-between items-center bg-neutral-50/50 dark:bg-neutral-900">
                <h3 class="font-bold text-lg text-neutral-800 dark:text-neutral-200 flex items-center gap-2">
                    <flux:icon name="document-text" class="w-5 h-5 text-[#4C5C2D] dark:text-[#8CC7C4]" />
                    Detail Permintaan Rawat Inap
                </h3>
                <button wire:click="closeDetail" class="text-neutral-400 hover:text-red-500 transition-colors p-1 rounded-md hover:bg-red-50 dark:hover:bg-red-900/20">
                    <flux:icon name="x-mark" class="w-5 h-5" />
                </button>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4 text-sm">
                    <div class="space-y-4">
                        <div>
                            <p class="text-neutral-500 text-xs uppercase tracking-wider mb-1">No. Rawat</p>
                            <p class="font-medium text-neutral-800 dark:text-neutral-200">{{ $detailData['no_rawat'] ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-neutral-500 text-xs uppercase tracking-wider mb-1">Pasien</p>
                            <p class="font-medium text-neutral-800 dark:text-neutral-200">
                                {{ $detailData['reg_periksa']['no_rkm_medis'] ?? '-' }} - {{ $detailData['reg_periksa']['pasien']['nm_pasien'] ?? '-' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-neutral-500 text-xs uppercase tracking-wider mb-1">Kamar Diminta</p>
                            <p class="font-medium text-neutral-800 dark:text-neutral-200">{{ $detailData['kamar']['kd_kamar'] ?? '-' }} ({{ $detailData['kamar']['bangsal']['nm_bangsal'] ?? '-' }})</p>
                            <p class="text-xs text-neutral-500 mt-0.5">Tarif: Rp {{ number_format($detailData['kamar']['trf_kamar'] ?? 0, 0, ',', '.') }}</p>
                        </div>
                        <div>
                            <p class="text-neutral-500 text-xs uppercase tracking-wider mb-1">Tanggal Permintaan</p>
                            <p class="font-medium text-neutral-800 dark:text-neutral-200">{{ $detailData['tanggal'] ?? '-' }}</p>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div>
                            <p class="text-neutral-500 text-xs uppercase tracking-wider mb-1">Diagnosa Awal</p>
                            <p class="font-medium text-neutral-800 dark:text-neutral-200">{{ $detailData['diagnosa'] ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-neutral-500 text-xs uppercase tracking-wider mb-1">Asal Poli / Unit</p>
                            <p class="font-medium text-neutral-800 dark:text-neutral-200">{{ $detailData['reg_periksa']['poliklinik']['nm_poli'] ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-neutral-500 text-xs uppercase tracking-wider mb-1">Dokter DPJP</p>
                            <p class="font-medium text-neutral-800 dark:text-neutral-200">{{ $detailData['reg_periksa']['dokter']['nm_dokter'] ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-neutral-500 text-xs uppercase tracking-wider mb-1">Status</p>
                            @if(!empty($detailData['kamar_inap']))
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 text-xs rounded-full bg-green-100 text-green-700 font-semibold">
                                    <flux:icon name="check-circle" class="w-3 h-3" /> Sudah Masuk Ranap ({{ $detailData['kamar_inap']['tgl_masuk'] ?? '' }})
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 text-xs rounded-full bg-amber-100 text-amber-700 font-semibold">
                                    <flux:icon name="clock" class="w-3 h-3" /> Menunggu Check In
                                </span>
                            @endif
                        </div>
                        <div>
                            <p class="text-neutral-500 text-xs uppercase tracking-wider mb-1">Catatan</p>
                            <div class="bg-neutral-50 dark:bg-neutral-800 p-3 rounded-lg border border-neutral-100 dark:border-neutral-700">
                                <p class="text-neutral-700 dark:text-neutral-300 whitespace-pre-wrap">{{ $detailData['catatan'] ?? '-' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="px-6 py-4 border-t border-neutral-100 dark:border-neutral-800 flex justify-end bg-neutral-50/50 dark:bg-neutral-900 rounded-b-2xl">
                <button wire:click="closeDetail" class="px-5 py-2 text-sm font-medium text-neutral-700 dark:text-neutral-200 bg-white dark:bg-neutral-800 border border-neutral-300 dark:border-neutral-700 rounded-lg hover:bg-neutral-50 dark:hover:bg-neutral-700 transition-colors shadow-sm">
                    Tutup
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- Kamar Lookup Modal --}}
    @if($isKamarModalOpen)
    <div class="fixed inset-0 z-[100] flex items-center justify-center bg-neutral-900/50 backdrop-blur-sm p-4 overflow-y-auto">
        <div class="bg-white dark:bg-neutral-900 rounded-2xl w-full max-w-4xl shadow-2xl overflow-hidden border border-neutral-200 dark:border-neutral-800 relative mt-10 mb-10 flex flex-col h-[80vh]">
            <div class="px-6 py-4 border-b border-neutral-100 dark:border-neutral-800 flex justify-between items-center shrink-0">
                <h3 class="font-bold text-lg text-neutral-800 dark:text-neutral-200">Pilih Kamar / Bangsal</h3>
                <button wire:click="$set('isKamarModalOpen', false)" class="text-neutral-400 hover:text-red-500 transition-colors">
                    <flux:icon name="x-mark" class="w-5 h-5" />
                </button>
            </div>
            <div class="p-4 border-b border-neutral-100 dark:border-neutral-800 shrink-0">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <flux:icon name="magnifying-glass" class="w-5 h-5 text-neutral-400" />
                    </div>
                    <input type="text" wire:model.live.debounce.300ms="searchKamar" class="w-full pl-10 pr-4 py-2 border border-neutral-200 dark:border-neutral-700 rounded-lg focus:ring-2 focus:ring-[#4C5C2D]" placeholder="Cari kode kamar atau nama bangsal...">
                </div>
                <p class="text-xs text-neutral-500 mt-2">* Hanya menampilkan kamar yang kosong (Status != ISI)</p>
            </div>
            <div class="flex-1 overflow-y-auto">
                <table class="w-full text-left text-sm whitespace-nowrap">
                    <thead class="sticky top-0 bg-neutral-50 dark:bg-neutral-800 z-10 shadow-sm">
                        <tr class="border-b border-neutral-200 dark:border-neutral-700 text-xs text-neutral-500 uppercase tracking-wider">
                            <th class="px-4 py-3 font-semibold">Kd Kamar</th>
                            <th class="px-4 py-3 font-semibold">Nama Bangsal</th>
                            <th class="px-4 py-3 font-semibold">Kelas</th>
                            <th class="px-4 py-3 font-semibold text-right">Tarif</th>
                            <th class="px-4 py-3 font-semibold text-center">Status</th>
                            <th class="px-4 py-3 font-semibold text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-100 dark:divide-neutral-800">
                        @forelse($listKamar as $kmr)
                            <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-800/50 transition-colors">
                                <td class="px-4 py-2 font-mono text-neutral-700 dark:text-neutral-300">{{ $kmr->kd_kamar }}</td>
                                <td class="px-4 py-2 font-medium text-neutral-800 dark:text-neutral-200">{{ $kmr->bangsal->nm_bangsal ?? '-' }}</td>
                                <td class="px-4 py-2 text-neutral-600 dark:text-neutral-400">{{ $kmr->kelas }}</td>
                                <td class="px-4 py-2 text-right font-mono">{{ number_format($kmr->trf_kamar, 0, ',', '.') }}</td>
                                <td class="px-4 py-2 text-center text-xs font-semibold {{ $kmr->status == 'KOSONG' ? 'text-green-600' : 'text-amber-600' }}">{{ $kmr->status }}</td>
                                <td class="px-4 py-2 text-center">
                                    <button wire:click="selectKamar('{{ $kmr->kd_kamar }}', '{{ $kmr->kd_bangsal }}', '{{ $kmr->bangsal->nm_bangsal ?? '' }}', {{ $kmr->trf_kamar }}, '{{ $kmr->kelas }}', '{{ $kmr->status }}')" class="px-3 py-1 bg-[#4C5C2D] text-white rounded text-xs font-medium hover:bg-[#3a4722] transition-colors">
                                        Pilih
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-4 py-8 text-center text-neutral-500">Ketik minimal 2 karakter untuk mencari kamar.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    {{-- Diagnosa Lookup Modal --}}
    @if($isDiagnosaModalOpen)
    <div class="fixed inset-0 z-[100] flex items-center justify-center bg-neutral-900/50 backdrop-blur-sm p-4 overflow-y-auto">
        <div class="bg-white dark:bg-neutral-900 rounded-2xl w-full max-w-4xl shadow-2xl overflow-hidden border border-neutral-200 dark:border-neutral-800 relative mt-10 mb-10 flex flex-col h-[80vh]">
            <div class="px-6 py-4 border-b border-neutral-100 dark:border-neutral-800 flex justify-between items-center shrink-0">
                <h3 class="font-bold text-lg text-neutral-800 dark:text-neutral-200">Pilih Diagnosa (ICD-10)</h3>
                <button wire:click="$set('isDiagnosaModalOpen', false)" class="text-neutral-400 hover:text-red-500 transition-colors">
                    <flux:icon name="x-mark" class="w-5 h-5" />
                </button>
            </div>
            <div class="p-4 border-b border-neutral-100 dark:border-neutral-800 shrink-0">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <flux:icon name="magnifying-glass" class="w-5 h-5 text-neutral-400" />
                    </div>
                    <input type="text" wire:model.live.debounce.300ms="searchDiagnosa" class="w-full pl-10 pr-4 py-2 border border-neutral-200 dark:border-neutral-700 rounded-lg focus:ring-2 focus:ring-[#4C5C2D]" placeholder="Cari kode penyakit atau nama penyakit...">
                </div>
            </div>
            <div class="flex-1 overflow-y-auto">
                <table class="w-full text-left text-sm whitespace-nowrap">
                    <thead class="sticky top-0 bg-neutral-50 dark:bg-neutral-800 z-10 shadow-sm">
                        <tr class="border-b border-neutral-200 dark:border-neutral-700 text-xs text-neutral-500 uppercase tracking-wider">
                            <th class="px-4 py-3 font-semibold">Kode ICD</th>
                            <th class="px-4 py-3 font-semibold">Nama Penyakit</th>
                            <th class="px-4 py-3 font-semibold text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-100 dark:divide-neutral-800">
                        @forelse($listDiagnosa as $diag)
                            <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-800/50 transition-colors">
                                <td class="px-4 py-2 font-mono font-medium text-neutral-700 dark:text-neutral-300 w-24">{{ $diag->kd_penyakit }}</td>
                                <td class="px-4 py-2 font-medium text-neutral-800 dark:text-neutral-200 whitespace-normal">{{ $diag->nm_penyakit }}</td>
                                <td class="px-4 py-2 text-center w-24">
                                    <button wire:click="selectDiagnosa('{{ $diag->kd_penyakit }}', '{{ str_replace("'", "\'", $diag->nm_penyakit) }}')" class="px-3 py-1 bg-[#4C5C2D] text-white rounded text-xs font-medium hover:bg-[#3a4722] transition-colors">
                                        Pilih
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="px-4 py-8 text-center text-neutral-500">Ketik minimal 2 karakter untuk mencari diagnosa.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
</div>