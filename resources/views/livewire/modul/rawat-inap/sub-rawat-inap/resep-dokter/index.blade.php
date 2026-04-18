<div>
    {{-- Main Header --}}
    <div class="flex items-center justify-between mb-6">
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
                    <span>Resep Dokter</span>
                </nav>
                <h1 class="text-xl font-bold text-neutral-800 dark:text-neutral-100">Peresepan Obat Oleh Dokter</h1>
            </div>
        </div>
    </div>

    {{-- Patient Info Banner --}}
    <div class="bg-[#4C5C2D] text-white p-4 rounded-xl shadow-md mb-6 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
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
                    <span class="flex items-center gap-1"><flux:icon name="identification" class="w-3 h-3"/> {{ $regPeriksa->pasien->no_ktp ?? '-' }}</span>
                    <span class="flex items-center gap-1"><flux:icon name="home" class="w-3 h-3"/> {{ $regPeriksa->kamarInap->first()->kamar->bangsal->nm_bangsal ?? 'Belum ada kamar' }}</span>
                </div>
            </div>
        </div>
        <div class="text-left md:text-right text-sm border-t md:border-t-0 md:border-l border-white/20 pt-3 md:pt-0 md:pl-4">
            <p class="text-white/80 text-xs mb-1">Dokter DPJP</p>
            <p class="font-semibold">{{ $regPeriksa->dokter->nm_dokter ?? '-' }}</p>
        </div>
    </div>

    {{-- Parameter Panel --}}
    <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-sm border border-neutral-200 dark:border-neutral-700 mb-6 p-4 text-sm text-neutral-700 dark:text-neutral-300">
        <div class="flex flex-col gap-4">
            {{-- Row 1: Tgl.Resep --}}
            <div class="flex flex-col md:flex-row items-start md:items-center gap-4 md:gap-8">
                <div class="flex items-center gap-2">
                    <span class="w-20 md:w-auto mt-1 md:mt-0 font-medium whitespace-nowrap">Tgl.Resep :</span>
                    <div class="flex items-center gap-2 flex-wrap">
                        <flux:input type="date" wire:model="tgl_peresepan" class="w-36 !py-1 !text-sm" />
                        
                        <div class="flex items-center gap-1">
                            <flux:select wire:model="jam_peresepan_jam" class="w-20 !py-1 !text-sm" :disabled="$auto_waktu">
                                @for($i = 0; $i < 24; $i++)
                                    <option value="{{ sprintf('%02d', $i) }}">{{ sprintf('%02d', $i) }}</option>
                                @endfor
                            </flux:select>
                            <span class="text-neutral-400">:</span>
                            <flux:select wire:model="jam_peresepan_menit" class="w-20 !py-1 !text-sm" :disabled="$auto_waktu">
                                @for($i = 0; $i < 60; $i++)
                                    <option value="{{ sprintf('%02d', $i) }}">{{ sprintf('%02d', $i) }}</option>
                                @endfor
                            </flux:select>
                            <span class="text-neutral-400">:</span>
                            <flux:select wire:model="jam_peresepan_detik" class="w-20 !py-1 !text-sm" :disabled="$auto_waktu">
                                @for($i = 0; $i < 60; $i++)
                                    <option value="{{ sprintf('%02d', $i) }}">{{ sprintf('%02d', $i) }}</option>
                                @endfor
                            </flux:select>
                        </div>
                        
                        <flux:checkbox wire:model.live="auto_waktu" class="ml-1" alt="Auto Waktu Saat Ini" />
                    </div>
                </div>
                
                <div class="flex items-center gap-8 md:ml-8 font-medium">
                    <span>Total : <span class="font-normal">{{ count($cart) }}</span></span>
                    <span>Total+PPN : <span class="font-normal">{{ count($cart) }}</span></span>
                </div>
            </div>

            {{-- Row 2: Peresep & No.Resep --}}
            <div class="flex flex-col md:flex-row items-start md:items-center gap-4 md:gap-8">
                <div class="flex items-center gap-2 w-full md:w-auto">
                    <span class="w-20 md:w-auto font-medium whitespace-nowrap">Peresep :</span>
                    <div class="flex items-center gap-2 flex-1 md:flex-none">
                        <flux:input wire:model="kd_dokter_peresep" class="w-24 !py-1 !text-sm text-center font-mono" readonly placeholder="Kode" />
                        <flux:input wire:model="nm_dokter_peresep" class="w-48 md:w-64 !py-1 !text-sm" readonly placeholder="Pilih Dokter" />
                        <button type="button" wire:click="openDokterModal" class="p-1.5 text-neutral-500 hover:text-[#4C5C2D] transition-colors border border-neutral-200 dark:border-neutral-700 rounded-lg hover:bg-neutral-50 dark:hover:bg-neutral-800">
                            <flux:icon name="paper-clip" class="w-4 h-4" />
                        </button>
                    </div>
                </div>

                <div class="flex items-center gap-2 md:ml-auto">
                    <span class="font-medium whitespace-nowrap">No.Resep :</span>
                    <flux:input wire:model="no_resep_input" class="w-40 !py-1 !text-sm text-center font-mono" :disabled="$auto_nomor" placeholder="Otomatis" />
                    <flux:checkbox wire:model.live="auto_nomor" alt="Auto Numbering" />
                </div>
            </div>
        </div>
    </div>

    {{-- Main Content Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 pb-6">
        
        {{-- Section 1: Kiri - Master Obat --}}
        <div class="lg:col-span-7 space-y-6">
            <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-sm border border-neutral-200 dark:border-neutral-700 flex flex-col overflow-hidden">
                <div class="p-4 border-b border-neutral-100 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800/50 flex flex-col gap-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <flux:icon name="beaker" class="w-5 h-5 text-[#4C5C2D] dark:text-[#8CC7C4]" />
                            <h3 class="font-semibold text-neutral-800 dark:text-neutral-200">Daftar Obat & BHP</h3>
                        </div>
                    </div>
                    <flux:input 
                        wire:model.live.debounce.300ms="searchObat" 
                        placeholder="Cari nama obat, kandungan, atau kode..." 
                        icon="magnifying-glass"
                        class="w-full"
                    />
                </div>

                <div class="flex-1 overflow-x-auto overflow-y-auto p-0" style="max-height: 500px;">
                    <table class="w-full text-[11px] text-left whitespace-nowrap">
                        <thead class="text-neutral-500 bg-neutral-50 dark:bg-neutral-800/80 uppercase border-b border-neutral-200 dark:border-neutral-700 sticky top-0 z-10 shadow-sm">
                            <tr>
                                <th class="px-3 py-2.5 font-semibold">Kode Barang</th>
                                <th class="px-3 py-2.5 font-semibold w-full">Nama Barang</th>
                                <th class="px-3 py-2.5 font-semibold">Satuan</th>
                                <th class="px-3 py-2.5 font-semibold">Komposisi</th>
                                <th class="px-3 py-2.5 font-semibold text-right">Harga (Rp)</th>
                                <th class="px-3 py-2.5 font-semibold">Jenis Obat</th>
                                <th class="px-3 py-2.5 font-semibold">I.F.</th>
                                <th class="px-3 py-2.5 font-semibold text-center">Stok</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-100 dark:divide-neutral-800">
                            @forelse($this->obatList as $obat)
                                <tr wire:key="obat-{{ $obat->kode_brng }}"
                                    x-data="{ clicked: false }"
                                    x-show="!clicked"
                                    x-transition:leave="transition ease-in duration-300"
                                    x-transition:leave-start="opacity-100 translate-x-0"
                                    x-transition:leave-end="opacity-0 translate-x-full"
                                    @click="clicked = true; setTimeout(() => $wire.pushToCart('{{ $obat->kode_brng }}', '{{ addslashes($obat->nama_brng) }}', '{{ $obat->kode_sat }}', {{ $obat->harga }}, {{ $obat->stok }}), 250)"
                                    class="bg-white dark:bg-neutral-900 hover:bg-[#F1F5E9] dark:hover:bg-[#4C5C2D]/20 transition-colors cursor-pointer group">
                                    <td class="px-3 py-2.5 font-mono text-neutral-500 group-hover:text-[#4C5C2D]">{{ $obat->kode_brng }}</td>
                                    <td class="px-3 py-2.5 font-bold text-neutral-800 dark:text-neutral-200 whitespace-normal min-w-[200px]">{{ $obat->nama_brng }}</td>
                                    <td class="px-3 py-2.5 text-neutral-600 dark:text-neutral-400">{{ $obat->kode_sat }}</td>
                                    <td class="px-3 py-2.5 text-neutral-500">{{ $obat->komposisi }}</td>
                                    <td class="px-3 py-2.5 text-right font-medium text-neutral-700 dark:text-neutral-300">{{ number_format($obat->harga, 0, ',', '.') }}</td>
                                    <td class="px-3 py-2.5 text-neutral-500">{{ $obat->jenis_obat }}</td>
                                    <td class="px-3 py-2.5 text-neutral-500">{{ $obat->industri_farmasi }}</td>
                                    <td class="px-3 py-2.5 text-center font-bold {{ $obat->stok > 0 ? 'text-[#4C5C2D] dark:text-[#8CC7C4]' : 'text-red-500' }}">
                                        {{ $obat->stok }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-4 py-8 text-center text-neutral-400">
                                        <flux:icon name="magnifying-glass" class="w-8 h-8 mx-auto mb-2 opacity-50" />
                                        <p>Obat tidak ditemukan.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                @if($this->obatList->hasPages())
                    <div class="px-4 py-3 border-t border-neutral-100 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800/30">
                        {{ $this->obatList->links(data: ['scrollTo' => false]) }}
                    </div>
                @endif
            </div>
        </div>

        {{-- Section 2: Kanan - Keranjang & Riwayat --}}
        <div class="lg:col-span-5 space-y-6 flex flex-col h-full">
            
            {{-- Keranjang Obat sementara --}}
            <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-sm border {{ count($cart) > 0 ? 'border-[#4C5C2D] shadow-md' : 'border-neutral-200 dark:border-neutral-700' }} flex flex-col overflow-hidden">
                <div class="px-4 py-3 border-b {{ count($cart) > 0 ? 'border-[#4C5C2D]/30 bg-[#4C5C2D]/5 dark:bg-[#4C5C2D]/20' : 'border-neutral-100 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800' }} flex items-center justify-between">
                    <h3 class="font-bold {{ count($cart) > 0 ? 'text-[#4C5C2D] dark:text-[#8CC7C4]' : 'text-neutral-600 dark:text-neutral-300' }} flex items-center gap-2">
                        <flux:icon name="shopping-cart" class="w-5 h-5" />
                        Daftar Permintaan Resep Obat
                    </h3>
                    @if(count($cart) > 0)
                        <span class="text-[10px] font-bold bg-[#4C5C2D] text-white px-2 py-0.5 rounded-full shadow-sm">{{ count($cart) }} Item</span>
                    @endif
                </div>
                
                <div class="flex-1 overflow-x-auto p-0 min-h-[250px]">
                    <table class="w-full text-xs text-left whitespace-nowrap">
                        <thead class="text-neutral-500 bg-neutral-50 dark:bg-neutral-800 uppercase border-b border-neutral-200 dark:border-neutral-700 sticky top-0 shadow-sm z-10">
                            <tr>
                                <th class="px-3 py-2 w-20">Jumlah</th>
                                <th class="px-3 py-2 w-32">Aturan Pakai</th>
                                <th class="px-3 py-2 w-full">Nama Obat</th>
                                <th class="px-3 py-2 text-right">Harga</th>
                                <th class="px-3 py-2 text-center">Stok</th>
                                <th class="px-3 py-2 text-center">Hapus</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-100 dark:divide-neutral-800">
                            @forelse($cart as $index => $item)
                                <tr class="bg-white dark:bg-neutral-900/50 hover:bg-neutral-50 dark:hover:bg-neutral-800/80 transition-colors">
                                    <td class="px-2 py-1.5 align-top">
                                        <div class="flex items-center">
                                            <input type="number" wire:model="cart.{{ $index }}.jml" min="0.1" step="0.1" class="w-14 px-2 py-1 text-xs border border-neutral-300 dark:border-neutral-600 rounded-md focus:ring-[#4C5C2D] focus:border-[#4C5C2D] dark:bg-neutral-800 dark:text-white" />
                                            <span class="ml-1 text-[10px] text-neutral-500">{{ $item['satuan'] }}</span>
                                        </div>
                                    </td>
                                    <td class="px-2 py-1.5 align-top">
                                        <input type="text" wire:model="cart.{{ $index }}.aturan_pakai" placeholder="Ketik aturan" class="w-full px-2 py-1 text-xs border border-neutral-300 dark:border-neutral-600 rounded-md focus:ring-[#4C5C2D] focus:border-[#4C5C2D] dark:bg-neutral-800 dark:text-white" />
                                    </td>
                                    <td class="px-3 py-2.5 align-top">
                                        <div class="font-semibold text-neutral-800 dark:text-neutral-200 whitespace-normal min-w-[120px]">{{ $item['nama_brng'] }}</div>
                                        <div class="text-[10px] text-neutral-400 font-mono">{{ $item['kode_brng'] }}</div>
                                    </td>
                                    <td class="px-3 py-2.5 text-right font-medium text-neutral-600 dark:text-neutral-400 align-top">
                                        {{ number_format($item['harga'] ?? 0, 0, ',', '.') }}
                                    </td>
                                    <td class="px-3 py-2.5 text-center font-bold text-neutral-500 align-top">
                                        {{ $item['stok'] ?? 0 }}
                                    </td>
                                    <td class="px-3 py-2.5 text-center align-top">
                                        <button wire:click="removeObat({{ $index }})" class="text-red-500 hover:text-white hover:bg-red-500 transition-colors p-1.5 rounded-lg border border-transparent hover:border-red-600">
                                            <flux:icon name="trash" class="w-4 h-4" />
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-12 text-center text-neutral-400">
                                        <flux:icon name="queue-list" class="w-10 h-10 mx-auto mb-3 opacity-30" />
                                        <p class="text-sm font-medium">Keranjang Permintaan Kosong</p>
                                        <p class="text-xs mt-1 opacity-70">Silakan pilih obat dari daftar di sebelah kiri.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if(count($cart) > 0)
                    <div class="p-4 border-t border-[#4C5C2D]/30 bg-[#F1F5E9] dark:bg-[#4C5C2D]/10">
                        <flux:button wire:click="save" variant="primary" icon="paper-airplane" class="w-full bg-[#4C5C2D] hover:bg-[#3D4A24] text-white shadow-md font-bold py-3 h-auto text-sm flex items-center justify-center gap-2">
                            Kirim Permintaan Resep
                        </flux:button>
                    </div>
                @endif
            </div>

            {{-- Riwayat Resep --}}
            <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-sm border border-neutral-200 dark:border-neutral-700 overflow-hidden flex flex-col flex-1">
                <div class="px-4 py-3 border-b border-neutral-100 dark:border-neutral-700 flex items-center justify-between bg-neutral-50 dark:bg-neutral-800">
                    <div class="flex items-center gap-2">
                        <flux:icon name="clock" class="w-5 h-5 text-[#4C5C2D] dark:text-[#8CC7C4]" />
                        <h3 class="font-bold text-neutral-800 dark:text-neutral-200">Riwayat Permintaan Resep</h3>
                    </div>
                </div>
                
                <div class="p-4 space-y-4 overflow-y-auto flex-1">
                    @forelse($savedResep as $resep)
                        <div class="border border-neutral-200 dark:border-neutral-700 rounded-lg overflow-hidden shadow-sm hover:shadow transition-shadow">
                            <div class="bg-gradient-to-r from-neutral-50 to-white dark:from-neutral-800 dark:to-neutral-900 px-3 py-2 border-b border-neutral-200 dark:border-neutral-700 flex flex-wrap gap-2 items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="bg-[#4C5C2D]/10 text-[#4C5C2D] dark:text-[#8CC7C4] px-2 py-1 rounded font-mono text-xs font-bold border border-[#4C5C2D]/20">#{{ $resep->no_resep }}</div>
                                    <div class="text-[10px] text-neutral-500 font-medium">
                                        <flux:icon name="calendar" class="w-3 h-3 inline mr-1 opacity-70"/>
                                        {{ \Carbon\Carbon::parse($resep->tgl_peresepan)->format('d/m/Y') }} {{ $resep->jam_peresepan }}
                                    </div>
                                    <div class="text-[10px] text-neutral-500 font-medium hidden sm:inline-flex items-center gap-1">
                                        <flux:icon name="user" class="w-3 h-3 opacity-70"/>
                                        {{ $resep->dokter->nm_dokter ?? 'Anonim' }}
                                    </div>
                                </div>
                                
                                <button type="button" 
                                    @click="
                                        Swal.fire({
                                            title: 'Hapus Resep?',
                                            text: 'Resep #{{ $resep->no_resep }} akan dihapus secara permanen!',
                                            icon: 'warning',
                                            showCancelButton: true,
                                            confirmButtonColor: '#4C5C2D',
                                            cancelButtonColor: '#ef4444',
                                            confirmButtonText: 'Ya, Hapus!',
                                            cancelButtonText: 'Batal',
                                            reverseButtons: true
                                        }).then((result) => {
                                            if (result.isConfirmed) {
                                                $wire.hapusResep('{{ $resep->no_resep }}')
                                            }
                                        })
                                    "
                                    class="text-xs text-red-500 hover:text-white border border-red-200 hover:bg-red-500 hover:border-red-500 px-2.5 py-1 rounded transition-colors font-medium flex items-center gap-1">
                                    <flux:icon name="trash" class="w-3 h-3" /> Hapus
                                </button>
                            </div>
                            <div class="p-0">
                                <table class="w-full text-[11px] text-left">
                                    <thead class="bg-neutral-50/50 dark:bg-neutral-800/30 text-neutral-500 uppercase border-b border-neutral-100 dark:border-neutral-800">
                                        <tr>
                                            <th class="px-3 py-1.5 font-medium">Nama Obat</th>
                                            <th class="px-3 py-1.5 font-medium text-center w-16">Jumlah</th>
                                            <th class="px-3 py-1.5 font-medium">Aturan Pakai</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-neutral-100 dark:divide-neutral-800/50">
                                        @foreach($resep->detail as $detail)
                                            <tr class="bg-white dark:bg-neutral-900 hover:bg-neutral-50 dark:hover:bg-neutral-900/80">
                                                <td class="px-3 py-2 font-medium text-neutral-700 dark:text-neutral-300">
                                                    {{ $detail->barang->nama_brng ?? $detail->kode_brng }}
                                                    <span class="text-[9px] text-neutral-400 block font-normal">{{ $detail->kode_brng }}</span>
                                                </td>
                                                <td class="px-3 py-2 text-center font-bold text-neutral-800 dark:text-neutral-200">{{ $detail->jml }}</td>
                                                <td class="px-3 py-2 text-neutral-600 dark:text-neutral-400 italic">"{{ $detail->aturan_pakai }}"</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-neutral-400 flex flex-col items-center justify-center">
                            <flux:icon name="clock" class="w-8 h-8 mb-2 opacity-30" />
                            <p class="text-xs">Belum ada riwayat permintaan resep pada kunjungan ini.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Cari Dokter --}}
    <flux:modal wire:model="isDokterModalOpen" class="w-full md:w-[600px] p-0" variant="flyout">
        <div class="flex flex-col h-full bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-700 rounded-xl overflow-hidden shadow-2xl">
            <div class="px-5 py-4 border-b border-neutral-200 dark:border-neutral-700 flex items-center justify-between bg-neutral-50 dark:bg-neutral-800">
                <h3 class="font-bold text-neutral-800 dark:text-neutral-200 flex items-center gap-2">
                    <flux:icon name="user-group" class="w-5 h-5 text-[#4C5C2D] dark:text-[#8CC7C4]" />
                    Pilih Dokter Peresep
                </h3>
                <button type="button" wire:click="$set('isDokterModalOpen', false)" class="text-neutral-400 hover:text-neutral-600 dark:hover:text-neutral-200">
                    <flux:icon name="x-mark" class="w-5 h-5" />
                </button>
            </div>
            
            <div class="p-5 flex-1 overflow-hidden flex flex-col">
                <flux:input 
                    wire:model.live.debounce.300ms="searchDokterModal" 
                    placeholder="Cari nama dokter..." 
                    icon="magnifying-glass"
                    class="mb-4"
                />

                <div class="flex-1 overflow-y-auto border border-neutral-200 dark:border-neutral-700 rounded-lg">
                    @if(count($listDokter) > 0)
                        <ul class="divide-y divide-neutral-100 dark:divide-neutral-800">
                            @foreach($listDokter as $dok)
                                <li>
                                    <button 
                                        type="button" 
                                        wire:click="selectDokter('{{ $dok['kd_dokter'] }}', '{{ addslashes($dok['nm_dokter']) }}')" 
                                        class="w-full text-left px-4 py-3 hover:bg-neutral-50 dark:hover:bg-neutral-800 hover:text-[#4C5C2D] dark:hover:text-[#8CC7C4] transition-colors focus:outline-none focus:bg-neutral-50 dark:focus:bg-neutral-800"
                                    >
                                        <div class="font-medium">{{ $dok['nm_dokter'] }}</div>
                                        <div class="text-xs text-neutral-500 font-mono">{{ $dok['kd_dokter'] }}</div>
                                    </button>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="p-8 text-center text-neutral-400 text-sm">
                            <flux:icon name="magnifying-glass" class="w-8 h-8 mx-auto mb-2 opacity-50" />
                            <p>Dokter tidak ditemukan.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </flux:modal>
</div>
