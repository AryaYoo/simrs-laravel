<div class="flex flex-col gap-6 pb-8">
    @if(!$isCreating)
    {{-- Header / Breadcrumb --}}
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('modul.rawat-jalan.perawatan-tindakan', str_replace('/', '-', $no_rawat)) }}" wire:navigate class="flex items-center justify-center w-10 h-8 rounded-md bg-[#4C5C2D] transition-colors hover:bg-[#3d4b24] shadow-sm">
                <flux:icon name="chevron-left" class="w-5 h-5 text-white" />
            </a>
            <div>
                <nav class="text-xs text-neutral-400 mb-0.5">
                    <a href="{{ route('modul.index') }}" wire:navigate class="hover:underline">Modul</a>
                    <span class="mx-1">/</span>
                    <a href="{{ route('modul.rawat-jalan.index') }}" wire:navigate class="hover:underline">Rawat Jalan</a>
                    <span class="mx-1">/</span>
                    <a href="{{ route('modul.rawat-jalan.perawatan-tindakan', str_replace('/', '-', $no_rawat)) }}" wire:navigate class="hover:underline">Perawatan/Tindakan</a>
                    <span class="mx-1">/</span>
                    <span>Permintaan Rawat Inap</span>
                </nav>
                <h1 class="text-xl font-bold text-neutral-800 dark:text-neutral-100">Permintaan Rawat Inap</h1>
            </div>
        </div>
    </div>
    @endif

    {{-- Patient Card Banner --}}
    <div class="bg-[#4C5C2D] rounded-xl p-5 text-white shadow-sm flex flex-col md:flex-row justify-between md:items-center gap-4 relative overflow-hidden mb-6">
        {{-- Decorative pattern --}}
        <div class="absolute right-0 top-0 opacity-10 pointer-events-none">
            <svg width="200" height="200" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                <circle cx="90" cy="10" r="40" fill="currentColor" />
                <circle cx="10" cy="90" r="30" fill="currentColor" />
                <circle cx="50" cy="50" r="20" fill="currentColor" />
            </svg>
        </div>

        <div class="flex items-start gap-4 z-10">
            <div class="w-14 h-14 bg-white/20 rounded-full flex items-center justify-center flex-shrink-0 border-2 border-white/30 shadow-inner">
                <flux:icon name="user" class="w-7 h-7 text-white" />
            </div>
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="bg-white/20 text-white text-[10px] font-bold px-2 py-0.5 rounded shadow-sm border border-white/10 tracking-wide">{{ $regPeriksa->no_rawat }}</span>
                    <span class="bg-white/20 text-white text-[10px] font-bold px-2 py-0.5 rounded shadow-sm border border-white/10 tracking-wide">{{ $regPeriksa->no_rkm_medis }}</span>
                </div>
                <h2 class="text-lg font-black tracking-wide text-white uppercase drop-shadow-sm">{{ $regPeriksa->pasien->nm_pasien }}</h2>
                <div class="flex items-center gap-4 mt-1.5 text-xs text-white/90 font-medium">
                    <div class="flex items-center gap-1.5">
                        <flux:icon name="phone" class="w-3.5 h-3.5 opacity-80" />
                        <span>{{ $regPeriksa->pasien->no_tlp ?? '-' }}</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <flux:icon name="home" class="w-3.5 h-3.5 opacity-80" />
                        <span>{{ $regPeriksa->poliklinik->nm_poli ?? '-' }}</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <flux:icon name="credit-card" class="w-3.5 h-3.5 opacity-80" />
                        <span>{{ $regPeriksa->penjab->png_jawab ?? '-' }}</span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="md:text-right border-t md:border-t-0 md:border-l border-white/20 pt-3 md:pt-0 md:pl-5 z-10">
            <p class="text-[10px] uppercase tracking-wider text-white/70 font-semibold mb-0.5">Dokter DPJP Pasien</p>
            <p class="font-bold text-sm">{{ $regPeriksa->dokter->nm_dokter ?? '-' }}</p>
        </div>
    </div>

    @if($isCreating)
        {{-- Form Buat Permintaan --}}
        <div class="bg-white dark:bg-neutral-800 rounded-xl border border-neutral-200 dark:border-neutral-700 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-neutral-100 dark:border-neutral-700 bg-[#4C5C2D] text-white flex justify-between items-center">
                <h3 class="font-bold">Form Buat Permintaan Rawat Inap</h3>
                <button wire:click="cancelCreate" class="text-white hover:text-red-200 transition-colors">
                    <flux:icon name="x-mark" class="w-5 h-5" />
                </button>
            </div>
            <div class="p-6 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <flux:field>
                        <flux:label>Tanggal Permintaan</flux:label>
                        <flux:input type="date" wire:model="tanggal" />
                    </flux:field>
                </div>

                {{-- Kamar Section --}}
                <div class="border-t border-neutral-100 dark:border-neutral-700 pt-4">
                    <flux:label class="block font-semibold mb-2">Pilih Kamar / Ruangan</flux:label>
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
                        <flux:field class="w-full md:w-1/4">
                            <flux:label>Tarif Kamar</flux:label>
                            <flux:input value="{{ $trf_kamar ? number_format($trf_kamar, 0, ',', '.') : 0 }}" readonly class="bg-neutral-50 font-mono font-bold" />
                        </flux:field>
                        <button type="button" wire:click="openKamarModal" class="h-10 px-3 text-neutral-500 hover:text-[#4C5C2D] transition-colors border border-neutral-200 dark:border-neutral-700 rounded-lg hover:bg-neutral-50 dark:hover:bg-neutral-800 flex items-center justify-center">
                            <flux:icon name="paper-clip" class="w-5 h-5" />
                        </button>
                    </div>
                </div>

                {{-- Diagnosa Section --}}
                <div class="border-t border-neutral-100 dark:border-neutral-700 pt-4">
                    <flux:label class="block font-semibold mb-2">Diagnosa Awal</flux:label>
                    <div class="flex gap-3 items-end">
                        <flux:field class="flex-1">
                            <flux:input value="{{ $kd_penyakit ? ($kd_penyakit . ' - ' . $nm_penyakit) : '' }}" placeholder="Pilih diagnosa..." readonly class="bg-neutral-50 font-medium" />
                        </flux:field>
                        <button type="button" wire:click="openDiagnosaModal" class="h-10 px-3 text-neutral-500 hover:text-[#4C5C2D] transition-colors border border-neutral-200 dark:border-neutral-700 rounded-lg hover:bg-neutral-50 dark:hover:bg-neutral-800 flex items-center justify-center">
                            <flux:icon name="paper-clip" class="w-5 h-5" />
                        </button>
                    </div>
                </div>

                {{-- Catatan Section --}}
                <div class="border-t border-neutral-100 dark:border-neutral-700 pt-4">
                    <flux:field>
                        <flux:label>Catatan Tambahan</flux:label>
                        <flux:input wire:model="catatan" placeholder="Masukkan catatan tambahan..." />
                    </flux:field>
                </div>
            </div>
            <div class="px-6 py-4 border-t border-neutral-100 dark:border-neutral-800 flex justify-end gap-3 bg-neutral-50/50 dark:bg-neutral-900 rounded-b-xl">
                <button type="button" wire:click="cancelCreate" class="px-5 py-2 text-sm font-medium text-neutral-700 dark:text-neutral-200 bg-white dark:bg-neutral-800 border border-neutral-300 dark:border-neutral-700 rounded-lg hover:bg-neutral-50 dark:hover:bg-neutral-700 transition-colors shadow-sm">
                    Kembali
                </button>
                <flux:button type="button" wire:click="savePermintaan" variant="primary" style="background-color: #4C5C2D; color: white; border: none; font-weight: 700;">
                    Simpan Permintaan
                </flux:button>
            </div>
        </div>
    @else
        <div class="space-y-6">


            {{-- Table Section --}}
            <div class="bg-white dark:bg-neutral-800 rounded-xl border border-neutral-200 dark:border-neutral-700 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-neutral-100 dark:border-neutral-700 bg-neutral-50/50 dark:bg-neutral-800/50">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                        <div class="flex items-center gap-2">
                            <div class="flex items-center gap-1 bg-neutral-100/50 dark:bg-neutral-900/50 p-1 rounded-lg border border-neutral-200/50 dark:border-neutral-700/50">
                                <button wire:click="$set('activeTab', 'pasien')" class="px-4 py-1.5 rounded-md text-sm font-medium transition-all {{ $activeTab === 'pasien' ? 'bg-white dark:bg-neutral-800 text-[#4C5C2D] dark:text-[#8CC7C4] shadow-sm ring-1 ring-neutral-200 dark:ring-neutral-700' : 'text-neutral-500 hover:text-neutral-700 dark:text-neutral-400 dark:hover:text-neutral-200' }}">Permintaan Pasien</button>
                                <button wire:click="$set('activeTab', 'semua')" class="px-4 py-1.5 rounded-md text-sm font-medium transition-all {{ $activeTab === 'semua' ? 'bg-white dark:bg-neutral-800 text-[#4C5C2D] dark:text-[#8CC7C4] shadow-sm ring-1 ring-neutral-200 dark:ring-neutral-700' : 'text-neutral-500 hover:text-neutral-700 dark:text-neutral-400 dark:hover:text-neutral-200' }}">Semua Riwayat</button>
                            </div>
                        </div>
                        
                        <div class="flex items-center gap-2">
                            @if($activeTab === 'pasien')
                                <flux:button wire:click="showCreateForm" icon="plus" size="sm" style="background-color: #4C5C2D; color: white; border: none; font-weight: 700; height: 36px;">
                                    Buat Permintaan
                                </flux:button>
                            @endif
                            
                            @if($activeTab === 'semua')
                                <input type="date" wire:model.live="filterTanggalMulai" class="text-sm rounded-lg border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-neutral-700 dark:text-neutral-200 h-9">
                                <span class="text-neutral-500 text-xs">s/d</span>
                                <input type="date" wire:model.live="filterTanggalSelesai" class="text-sm rounded-lg border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-neutral-700 dark:text-neutral-200 h-9">
                                <select wire:model.live="filterStatus" class="text-sm rounded-lg border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-neutral-700 dark:text-neutral-200 h-9 py-0">
                                    <option value="semua">Semua Status</option>
                                    <option value="menunggu">Menunggu Masuk</option>
                                    <option value="sudah_masuk">Sudah Masuk Ranap</option>
                                </select>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm whitespace-nowrap">
                        <thead>
                            <tr class="bg-neutral-50 dark:bg-neutral-800/80 border-b border-neutral-200 dark:border-neutral-700 text-xs text-neutral-500 uppercase tracking-wider">
                                <th class="px-4 py-3 font-semibold">No. Rawat</th>
                                <th class="px-4 py-3 font-semibold">No. RM</th>
                                <th class="px-4 py-3 font-semibold">Nama Pasien</th>
                                <th class="px-4 py-3 font-semibold text-center">J.K.</th>
                                <th class="px-4 py-3 font-semibold">Umur</th>
                                <th class="px-4 py-3 font-semibold">No. Telp</th>
                                <th class="px-4 py-3 font-semibold">Cara Bayar</th>
                                <th class="px-4 py-3 font-semibold">Asal Poli/Unit</th>
                                <th class="px-4 py-3 font-semibold">Dokter</th>
                                <th class="px-4 py-3 font-semibold">Tanggal</th>
                                <th class="px-4 py-3 font-semibold">Kamar Diminta</th>
                                <th class="px-4 py-3 font-semibold text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-100 dark:divide-neutral-800">
                            @php
                                $loopData = $activeTab === 'pasien' ? $permintaanRanaps : $semuaPermintaan;
                            @endphp
                            @forelse($loopData as $ranap)
                                <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-800/50 transition-colors group">
                                    <td class="px-4 py-3 text-[#d92d20] dark:text-[#f04438] font-medium">{{ $ranap->no_rawat }}</td>
                                    <td class="px-4 py-3 text-[#d92d20] dark:text-[#f04438] font-medium">{{ $ranap->regPeriksa->no_rkm_medis }}</td>
                                    <td class="px-4 py-3 text-[#d92d20] dark:text-[#f04438] font-medium">{{ $ranap->regPeriksa->pasien->nm_pasien }}</td>
                                    <td class="px-4 py-3 text-center text-[#d92d20] dark:text-[#f04438] font-medium">{{ $ranap->regPeriksa->pasien->jk }}</td>
                                    <td class="px-4 py-3 text-[#d92d20] dark:text-[#f04438] font-medium">{{ $ranap->regPeriksa->umurdaftar }} {{ $ranap->regPeriksa->sttsumur }}</td>
                                    <td class="px-4 py-3 text-[#d92d20] dark:text-[#f04438] font-medium">{{ $ranap->regPeriksa->pasien->no_tlp ?? '-' }}</td>
                                    <td class="px-4 py-3 text-[#d92d20] dark:text-[#f04438] font-medium">{{ $ranap->regPeriksa->penjab->png_jawab ?? '-' }}</td>
                                    <td class="px-4 py-3 text-[#d92d20] dark:text-[#f04438] font-medium">{{ $ranap->regPeriksa->poliklinik->nm_poli ?? '-' }}</td>
                                    <td class="px-4 py-3 text-[#d92d20] dark:text-[#f04438] font-medium">{{ $ranap->regPeriksa->dokter->nm_dokter ?? '-' }}</td>
                                    <td class="px-4 py-3 text-[#d92d20] dark:text-[#f04438] font-medium">{{ $ranap->tanggal }}</td>
                                    <td class="px-4 py-3 text-[#d92d20] dark:text-[#f04438] font-medium">{{ $ranap->kamar->kd_kamar ?? '' }} {{ $ranap->kamar->bangsal->nm_bangsal ?? '-' }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <div class="flex items-center justify-center gap-1">
                                            <button wire:click="showDetail('{{ str_replace('/', '-', $ranap->no_rawat) }}')" class="inline-flex items-center justify-center w-7 h-7 rounded bg-neutral-100 text-neutral-600 hover:bg-[#4C5C2D] hover:text-white transition-colors border border-neutral-200 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-300 dark:hover:bg-[#8CC7C4] dark:hover:text-neutral-900 shadow-sm" title="Detail">
                                                <flux:icon name="eye" class="w-4 h-4" />
                                            </button>
                                            @if(!$ranap->kamarInap)
                                                <button x-on:click="Swal.fire({title: 'Hapus Data?', text: 'Data permintaan rawat inap ini akan dihapus permanen.', icon: 'warning', showCancelButton: true, confirmButtonColor: '#dc2626', cancelButtonColor: '#6b7280', confirmButtonText: 'Ya, Hapus!'}).then((result) => { if (result.isConfirmed) { $wire.deletePermintaan('{{ str_replace('/', '-', $ranap->no_rawat) }}') } })" class="inline-flex items-center justify-center w-7 h-7 rounded bg-red-50 text-red-600 hover:bg-red-600 hover:text-white transition-colors border border-red-200 dark:border-red-900/30 dark:bg-red-900/20 dark:text-red-400 dark:hover:bg-red-600 dark:hover:text-white shadow-sm" title="Hapus">
                                                    <flux:icon name="trash" class="w-4 h-4" />
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="12" class="px-4 py-8 text-center text-neutral-500">
                                        <div class="flex flex-col items-center justify-center">
                                            <flux:icon name="document-text" class="w-8 h-8 text-neutral-300 mb-2" />
                                            <p>Belum ada permintaan rawat inap ditemukan.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($activeTab === 'semua' && $semuaPermintaan->hasPages())
                    <div class="px-5 py-3 border-t border-neutral-100 dark:border-neutral-700 bg-neutral-50/50 dark:bg-neutral-800/50">
                        {{ $semuaPermintaan->links(data: ['scrollTo' => false]) }}
                    </div>
                @endif
            </div>
        </div>
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
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4">
                    <div class="space-y-1">
                        <span class="text-xs font-semibold text-neutral-500 uppercase tracking-wider">No. Rawat</span>
                        <p class="text-sm font-medium text-neutral-800 dark:text-neutral-200">{{ $detailData['no_rawat'] ?? '-' }}</p>
                    </div>
                    <div class="space-y-1">
                        <span class="text-xs font-semibold text-neutral-500 uppercase tracking-wider">Tanggal Diminta</span>
                        <p class="text-sm font-medium text-neutral-800 dark:text-neutral-200">{{ $detailData['tanggal'] ?? '-' }}</p>
                    </div>
                    <div class="space-y-1">
                        <span class="text-xs font-semibold text-neutral-500 uppercase tracking-wider">No. RM</span>
                        <p class="text-sm font-medium text-neutral-800 dark:text-neutral-200">{{ $detailData['reg_periksa']['no_rkm_medis'] ?? '-' }}</p>
                    </div>
                    <div class="space-y-1">
                        <span class="text-xs font-semibold text-neutral-500 uppercase tracking-wider">Nama Pasien</span>
                        <p class="text-sm font-medium text-neutral-800 dark:text-neutral-200">{{ $detailData['reg_periksa']['pasien']['nm_pasien'] ?? '-' }}</p>
                    </div>
                    <div class="space-y-1">
                        <span class="text-xs font-semibold text-neutral-500 uppercase tracking-wider">Jenis Kelamin / Umur</span>
                        <p class="text-sm font-medium text-neutral-800 dark:text-neutral-200">
                            @if(isset($detailData['reg_periksa']['pasien']['jk']))
                                {{ $detailData['reg_periksa']['pasien']['jk'] == 'L' ? 'Laki-laki' : ($detailData['reg_periksa']['pasien']['jk'] == 'P' ? 'Perempuan' : '-') }}
                            @else
                                -
                            @endif
                            / 
                            {{ $detailData['reg_periksa']['umurdaftar'] ?? '-' }} {{ $detailData['reg_periksa']['sttsumur'] ?? '' }}
                        </p>
                    </div>
                    <div class="space-y-1">
                        <span class="text-xs font-semibold text-neutral-500 uppercase tracking-wider">No. Telp</span>
                        <p class="text-sm font-medium text-neutral-800 dark:text-neutral-200">{{ $detailData['reg_periksa']['pasien']['no_tlp'] ?? '-' }}</p>
                    </div>
                    <div class="space-y-1">
                        <span class="text-xs font-semibold text-neutral-500 uppercase tracking-wider">Cara Bayar</span>
                        <p class="text-sm font-medium text-neutral-800 dark:text-neutral-200">{{ $detailData['reg_periksa']['penjab']['png_jawab'] ?? '-' }}</p>
                    </div>
                    <div class="space-y-1">
                        <span class="text-xs font-semibold text-neutral-500 uppercase tracking-wider">Asal Poli / Unit</span>
                        <p class="text-sm font-medium text-neutral-800 dark:text-neutral-200">{{ $detailData['reg_periksa']['poliklinik']['nm_poli'] ?? '-' }}</p>
                    </div>
                    <div class="space-y-1">
                        <span class="text-xs font-semibold text-neutral-500 uppercase tracking-wider">Dokter Pendaftar</span>
                        <p class="text-sm font-medium text-neutral-800 dark:text-neutral-200">{{ $detailData['reg_periksa']['dokter']['nm_dokter'] ?? '-' }}</p>
                    </div>
                    
                    <div class="col-span-1 md:col-span-2 border-t border-neutral-100 dark:border-neutral-800 mt-2 pt-4 grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4">
                        <div class="space-y-1">
                            <span class="text-xs font-semibold text-neutral-500 uppercase tracking-wider">Kamar Diminta</span>
                            <p class="text-sm font-medium text-neutral-800 dark:text-neutral-200">
                                {{ $detailData['kamar']['kd_kamar'] ?? '' }} 
                                {{ $detailData['kamar']['bangsal']['nm_bangsal'] ?? '-' }}
                            </p>
                        </div>
                        <div class="space-y-1">
                            <span class="text-xs font-semibold text-neutral-500 uppercase tracking-wider">Tarif Kamar</span>
                            <p class="text-sm font-medium text-neutral-800 dark:text-neutral-200">
                                @if(isset($detailData['kamar']['trf_kamar']))
                                    Rp {{ number_format($detailData['kamar']['trf_kamar'], 0, ',', '.') }}
                                @else
                                    -
                                @endif
                            </p>
                        </div>
                        <div class="space-y-1 md:col-span-2">
                            <span class="text-xs font-semibold text-neutral-500 uppercase tracking-wider">Diagnosa Awal</span>
                            <div class="mt-1 p-3 bg-neutral-50 dark:bg-neutral-800/50 rounded-lg border border-neutral-100 dark:border-neutral-800 text-sm text-neutral-700 dark:text-neutral-300">
                                {{ $detailData['diagnosa'] ?? '-' }}
                            </div>
                        </div>
                        <div class="space-y-1 md:col-span-2">
                            <span class="text-xs font-semibold text-neutral-500 uppercase tracking-wider">Catatan</span>
                            <div class="mt-1 p-3 bg-neutral-50 dark:bg-neutral-800/50 rounded-lg border border-neutral-100 dark:border-neutral-800 text-sm text-neutral-700 dark:text-neutral-300">
                                {{ $detailData['catatan'] ?? '-' }}
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

    {{-- Modal Kamar Lookup --}}
    @if($isKamarModalOpen)
    <div class="fixed inset-0 z-[110] flex items-center justify-center bg-neutral-900/50 backdrop-blur-sm p-4 overflow-y-auto">
        <div class="bg-white dark:bg-neutral-900 rounded-2xl w-full max-w-2xl shadow-2xl overflow-hidden border border-neutral-200 dark:border-neutral-800 transform transition-all relative my-10">
            <div class="px-6 py-4 border-b border-neutral-100 dark:border-neutral-800 flex justify-between items-center bg-neutral-50/50 dark:bg-neutral-900">
                <h3 class="font-bold text-lg text-neutral-800 dark:text-neutral-200 flex items-center gap-2">
                    <flux:icon name="magnifying-glass" class="w-5 h-5 text-[#4C5C2D] dark:text-[#8CC7C4]" />
                    Cari Kamar Inap / Bed
                </h3>
                <button wire:click="$set('isKamarModalOpen', false)" class="text-neutral-400 hover:text-red-500 transition-colors">
                    <flux:icon name="x-mark" class="w-5 h-5" />
                </button>
            </div>
            <div class="p-6">
                <flux:input wire:model.live.debounce.300ms="searchKamar" icon="magnifying-glass" placeholder="Cari berdasarkan No. Bed atau Nama Bangsal..." autofocus class="mb-4" />

                <div class="overflow-y-auto border border-neutral-200 dark:border-neutral-700 rounded-lg" style="max-height: 50vh;">
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
                                    wire:click="selectKamar('{{ $kamar->kd_kamar }}', '{{ $kamar->kd_bangsal }}', '{{ $kamar->bangsal->nm_bangsal ?? '-' }}', {{ $kamar->trf_kamar }}, '{{ $kamar->kelas }}')">
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
            </div>
        </div>
    </div>
    @endif

    {{-- Modal Diagnosa Lookup --}}
    @if($isDiagnosaModalOpen)
    <div class="fixed inset-0 z-[110] flex items-center justify-center bg-neutral-900/50 backdrop-blur-sm p-4 overflow-y-auto">
        <div class="bg-white dark:bg-neutral-900 rounded-2xl w-full max-w-2xl shadow-2xl overflow-hidden border border-neutral-200 dark:border-neutral-800 transform transition-all relative my-10">
            <div class="px-6 py-4 border-b border-neutral-100 dark:border-neutral-800 flex justify-between items-center bg-neutral-50/50 dark:bg-neutral-900">
                <h3 class="font-bold text-lg text-neutral-800 dark:text-neutral-200 flex items-center gap-2">
                    <flux:icon name="magnifying-glass" class="w-5 h-5 text-[#4C5C2D] dark:text-[#8CC7C4]" />
                    Cari Diagnosa ICD-10
                </h3>
                <button wire:click="$set('isDiagnosaModalOpen', false)" class="text-neutral-400 hover:text-red-500 transition-colors">
                    <flux:icon name="x-mark" class="w-5 h-5" />
                </button>
            </div>
            <div class="p-6">
                <flux:input wire:model.live.debounce.300ms="searchDiagnosa" icon="magnifying-glass" placeholder="Cari berdasarkan Kode ICD-10 atau deskripsi penyakit..." autofocus class="mb-4" />

                <div class="overflow-y-auto border border-neutral-200 dark:border-neutral-700 rounded-lg" style="max-height: 50vh;">
                    <table class="w-full text-sm text-left">
                        <thead class="text-[10px] text-neutral-500 uppercase bg-neutral-50 dark:bg-neutral-900 border-b border-neutral-200 dark:border-neutral-700 sticky top-0 z-10 font-bold tracking-wider">
                            <tr>
                                <th class="px-4 py-3">Kode ICD-10</th>
                                <th class="px-4 py-3">Deskripsi Penyakit</th>
                                <th class="px-4 py-3">Ciri-ciri</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-100 dark:divide-neutral-800">
                            @forelse($listDiagnosa as $diag)
                                <tr class="hover:bg-[#F1F5E9] dark:hover:bg-[#4C5C2D]/10 transition-colors cursor-pointer group" 
                                    wire:click="selectDiagnosa('{{ $diag->kd_penyakit }}', '{{ $diag->nm_penyakit }}')">
                                    <td class="px-4 py-3 font-bold text-neutral-800 dark:text-neutral-100 group-hover:text-[#4C5C2D]">{{ $diag->kd_penyakit }}</td>
                                    <td class="px-4 py-3 font-semibold text-neutral-700 dark:text-neutral-300">{{ $diag->nm_penyakit }}</td>
                                    <td class="px-4 py-3 text-xs text-neutral-500">{{ $diag->ciri_ciri ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-4 py-12 text-center text-neutral-400">
                                        <flux:icon name="magnifying-glass" class="w-8 h-8 mx-auto mb-2 opacity-30" />
                                        <p class="text-sm font-medium">{{ strlen($searchDiagnosa) < 2 ? 'Ketik minimal 2 karakter untuk mencari...' : 'Diagnosa tidak ditemukan.' }}</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
