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
                    <a href="{{ route('modul.rawat-inap.perawatan-tindakan', str_replace('/', '-', $no_rawat)) }}" wire:navigate class="hover:underline">Tindakan</a>
                    <span class="mx-1">/</span>
                    <span>Permintaan Laboratorium</span>
                </nav>
                <h1 class="text-xl font-bold text-neutral-800 dark:text-neutral-100">Permintaan Pemeriksaan Laboratorium</h1>
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
            <p class="text-white/80 text-xs mb-1">Dokter DPJP Pasien</p>
            <p class="font-semibold">{{ $regPeriksa->dokter->nm_dokter ?? '-' }}</p>
        </div>
    </div>

    {{-- Parameter Panel (New Top Section) --}}
    <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-sm border border-neutral-200 dark:border-neutral-700 mb-6 p-4 text-sm text-neutral-700 dark:text-neutral-300">
        <div class="flex flex-col gap-4">
            {{-- Row 1: Tgl & Waktu --}}
            <div class="flex flex-col md:flex-row items-start md:items-center gap-4 md:gap-8">
                <div class="flex items-center gap-2">
                    <span class="w-24 md:w-auto font-medium whitespace-nowrap text-xs uppercase text-neutral-500">Tgl.Permintaan :</span>
                    <div class="flex items-center gap-2 flex-wrap">
                        <flux:input type="date" wire:model="tgl_permintaan" class="w-36 !py-1 !text-sm" />
                        
                        <div class="flex items-center gap-1">
                            <flux:select wire:model="jam_permintaan_jam" class="w-20 !py-1 !text-sm" :disabled="$auto_waktu">
                                @for($i = 0; $i < 24; $i++)
                                    <option value="{{ sprintf('%02d', $i) }}">{{ sprintf('%02d', $i) }}</option>
                                @endfor
                            </flux:select>
                            <span class="text-neutral-400">:</span>
                            <flux:select wire:model="jam_permintaan_menit" class="w-20 !py-1 !text-sm" :disabled="$auto_waktu">
                                @for($i = 0; $i < 60; $i++)
                                    <option value="{{ sprintf('%02d', $i) }}">{{ sprintf('%02d', $i) }}</option>
                                @endfor
                            </flux:select>
                            <span class="text-neutral-400">:</span>
                            <flux:select wire:model="jam_permintaan_detik" class="w-20 !py-1 !text-sm" :disabled="$auto_waktu">
                                @for($i = 0; $i < 60; $i++)
                                    <option value="{{ sprintf('%02d', $i) }}">{{ sprintf('%02d', $i) }}</option>
                                @endfor
                            </flux:select>
                        </div>
                        
                        <flux:checkbox wire:model.live="auto_waktu" label="Auto" class="ml-1" />
                    </div>
                </div>
            </div>

            {{-- Row 2: Perujuk --}}
            <div class="flex flex-col md:flex-row items-start md:items-center gap-4 md:gap-8">
                <div class="flex items-center gap-2 w-full md:w-auto">
                    <span class="w-24 md:w-auto font-medium whitespace-nowrap text-xs uppercase text-neutral-500">Perujuk :</span>
                    <div class="flex items-center gap-2 flex-1 md:flex-none">
                        <flux:input wire:model="kd_dokter_perujuk" class="w-24 !py-1 !text-sm text-center font-mono" readonly placeholder="Kode" />
                        <flux:input wire:model="nm_dokter_perujuk" class="w-48 md:w-64 !py-1 !text-sm" readonly placeholder="Pilih Dokter Perujuk..." />
                        <button type="button" wire:click="openDokterModal" class="p-1.5 text-neutral-500 hover:text-[#4C5C2D] transition-colors border border-neutral-200 dark:border-neutral-700 rounded-lg hover:bg-neutral-50 dark:hover:bg-neutral-800">
                            <flux:icon name="paper-clip" class="w-4 h-4" />
                        </button>
                    </div>
                </div>
            </div>

            {{-- Row 3: Diagnosa & Catatan (Tampil Horizontal) --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2 border-t border-neutral-100 dark:border-neutral-700/50">
                <div class="flex flex-col gap-1.5">
                    <label class="font-medium text-xs uppercase text-neutral-400 tracking-wider">Indikasi Klinis :</label>
                    <flux:textarea wire:model="diagnosa_klinis" placeholder="Masukkan indikasi klinis..." rows="1" class="!text-xs !py-1.5" />
                </div>
                <div class="flex flex-col gap-1.5">
                    <label class="font-medium text-xs uppercase text-neutral-400 tracking-wider">Informasi Tambahan :</label>
                    <flux:textarea wire:model="informasi_tambahan" placeholder="Catatan tambahan..." rows="1" class="!text-xs !py-1.5" />
                </div>
            </div>
        </div>
    </div>

    {{-- Main Content Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        {{-- Left Panel: Item Search --}}
        <div class="lg:col-span-7 space-y-6">
            <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-sm border border-neutral-200 dark:border-neutral-700 overflow-hidden flex flex-col">
                <div class="p-4 border-b border-neutral-100 dark:border-neutral-700 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-neutral-50/50 dark:bg-neutral-800/50">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-[#4C5C2D]/10 flex items-center justify-center">
                            <flux:icon name="magnifying-glass" class="w-4 h-4 text-[#4C5C2D]" />
                        </div>
                        <h3 class="font-bold text-neutral-700 dark:text-neutral-300">Pilih Pemeriksaan</h3>
                    </div>
                </div>

                <div class="p-4">
                    <flux:input wire:model.live.debounce.300ms="searchPemeriksaan" 
                                placeholder="Cari nama pemeriksaan lab..." 
                                icon="magnifying-glass" 
                                class="mb-4" />

                    {{-- Category Toggle (PK, PA, MB) --}}
                    <div class="flex items-center gap-2 mb-4 p-1 bg-neutral-100 dark:bg-neutral-900 rounded-lg w-fit">
                        <button wire:click="$set('kategori', 'PK')" class="px-3 py-1.5 rounded-md text-xs font-bold transition-all {{ $kategori === 'PK' ? 'bg-white dark:bg-neutral-800 text-[#4C5C2D] shadow-sm' : 'text-neutral-500 hover:text-neutral-700' }}">Laborat Klinik (PK)</button>
                        <button wire:click="$set('kategori', 'PA')" class="px-3 py-1.5 rounded-md text-xs font-bold transition-all {{ $kategori === 'PA' ? 'bg-white dark:bg-neutral-800 text-[#4C5C2D] shadow-sm' : 'text-neutral-500 hover:text-neutral-700' }}">Laborat PA</button>
                        <button wire:click="$set('kategori', 'MB')" class="px-3 py-1.5 rounded-md text-xs font-bold transition-all {{ $kategori === 'MB' ? 'bg-white dark:bg-neutral-800 text-[#4C5C2D] shadow-sm' : 'text-neutral-500 hover:text-neutral-700' }}">Laborat Mikro (MB)</button>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        @forelse($pemeriksaanList as $item)
                            <button type="button" 
                                    wire:click="addToCart('{{ $item->kd_jenis_prw }}', '{{ $item->nm_perawatan }}', {{ $item->total_byr }})"
                                    class="flex items-center gap-3 p-3 text-left bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-700 rounded-xl hover:border-[#4C5C2D] hover:bg-[#4C5C2D]/5 transition-all group group-hover:shadow-md">
                                <div class="w-10 h-10 rounded-lg bg-neutral-100 dark:bg-neutral-800 text-neutral-400 group-hover:bg-[#4C5C2D] group-hover:text-white transition-colors">
                                    <flux:icon name="plus" class="w-5 h-5" />
                                </div>
                                <div class="flex-1 overflow-hidden">
                                    <p class="text-sm font-bold text-neutral-700 dark:text-neutral-200 truncate">{{ $item->nm_perawatan }}</p>
                                    <p class="text-[10px] text-neutral-400 italic">Rp {{ number_format($item->total_byr, 0, ',', '.') }}</p>
                                </div>
                            </button>
                        @empty
                            <div class="col-span-full py-12 flex flex-col items-center justify-center text-neutral-400">
                                <flux:icon name="beaker" class="w-12 h-12 mb-2 opacity-20" />
                                <p class="text-sm">Tidak ditemukan jenis pemeriksaan.</p>
                            </div>
                        @endforelse
                    </div>

                    <div class="mt-6">
                        {{ $pemeriksaanList->links() }}
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Panel: Cart Only --}}
        <div class="lg:col-span-5 space-y-6">
            <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-lg border-2 border-[#4C5C2D]/20 overflow-hidden flex flex-col sticky top-6">
                <div class="px-4 py-3 border-b border-[#4C5C2D]/10 flex items-center justify-between bg-[#F1F5E9] dark:bg-[#4C5C2D]/10">
                    <div class="flex items-center gap-2">
                        <flux:icon name="shopping-cart" class="w-5 h-5 text-[#4C5C2D]" />
                        <h3 class="font-bold text-[#4C5C2D]">Pemeriksaan Dipilih</h3>
                    </div>
                    <span class="px-2 py-0.5 rounded-full bg-[#4C5C2D] text-white text-[10px] font-bold">{{ count($cart) }} Item</span>
                </div>

                <div class="p-4 space-y-4">
                    <div class="space-y-2 max-h-[500px] overflow-y-auto pr-1">
                        @forelse($cart as $index => $item)
                            <div class="flex items-center gap-3 p-2.5 rounded-xl bg-neutral-50 dark:bg-neutral-900/50 border border-neutral-100 dark:border-neutral-800 group hover:border-red-200 transition-colors">
                                <div class="flex-1 overflow-hidden">
                                    <p class="text-xs font-semibold text-neutral-700 dark:text-neutral-200 line-clamp-1">{{ $item['nm_perawatan'] }}</p>
                                    <p class="text-[10px] text-neutral-400">Rp {{ number_format($item['total_byr'], 0, ',', '.') }}</p>
                                </div>
                                <button type="button" 
                                        wire:click="removeFromCart({{ $index }})" 
                                        class="p-2 text-neutral-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-all"
                                        title="Hapus">
                                    <flux:icon name="trash" class="w-4 h-4" />
                                </button>
                            </div>
                        @empty
                            <div class="py-8 text-center text-neutral-400 italic text-xs">
                                Belum ada pemeriksaan yang dipilih.
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- Action Footer --}}
                @if(count($cart) > 0)
                    <div class="p-4 bg-neutral-50 dark:bg-neutral-900/50 border-t border-neutral-100 dark:border-neutral-700">
                        <div class="flex items-center justify-between mb-4 px-1">
                            <span class="text-sm text-neutral-500">Estimasi Biaya:</span>
                            <span class="text-lg font-bold text-[#4C5C2D]">Rp {{ number_format(collect($cart)->sum('total_byr'), 0, ',', '.') }}</span>
                        </div>
                        <flux:button wire:click="save" variant="primary" icon="paper-airplane" class="w-full bg-[#4C5C2D] hover:bg-[#3D4A24] text-white shadow-md font-bold py-3 h-auto text-sm flex items-center justify-center gap-2">
                            Kirim Permintaan Pemeriksaan
                        </flux:button>
                    </div>
                @endif
            </div>
        </div>

    </div>

    {{-- MODAL LOOKUP DOKTER --}}
    <flux:modal name="dokter_modal" wire:model="isDokterModalOpen" variant="flyout" class="w-full max-w-lg p-0">
        <div class="flex flex-col h-full bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-700 rounded-xl overflow-hidden shadow-2xl">
            <div class="px-5 py-4 border-b border-neutral-200 dark:border-neutral-700 flex items-center justify-between bg-neutral-50 dark:bg-neutral-800">
                <h3 class="font-bold text-neutral-800 dark:text-neutral-200 flex items-center gap-2">
                    <flux:icon name="user-group" class="w-5 h-5 text-[#4C5C2D]" />
                    Pilih Dokter Perujuk
                </h3>
                <button type="button" wire:click="$set('isDokterModalOpen', false)" class="text-neutral-400 hover:text-neutral-600 dark:hover:text-neutral-200">
                    <flux:icon name="x-mark" class="w-5 h-5" />
                </button>
            </div>
            
            <div class="p-5 flex-1 overflow-hidden flex flex-col">
                <flux:input wire:model.live.debounce.300ms="searchDokterModal" icon="magnifying-glass" placeholder="Cari nama dokter..." class="mb-4" />

                <div class="flex-1 overflow-y-auto border border-neutral-200 dark:border-neutral-700 rounded-lg">
                    @if(count($listDokter) > 0)
                        <ul class="divide-y divide-neutral-100 dark:divide-neutral-800">
                            @foreach($listDokter as $dr)
                                <li>
                                    <button 
                                        type="button" 
                                        wire:click="selectDokter('{{ $dr['kd_dokter'] }}', '{{ addslashes($dr['nm_dokter']) }}')" 
                                        class="w-full text-left px-4 py-3 hover:bg-neutral-50 dark:hover:bg-neutral-800 hover:text-[#4C5C2D] transition-colors focus:outline-none focus:bg-neutral-50 dark:focus:bg-neutral-800"
                                    >
                                        <div class="font-medium text-sm">{{ $dr['nm_dokter'] }}</div>
                                        <div class="text-[10px] text-neutral-500 font-mono">{{ $dr['kd_dokter'] }}</div>
                                    </button>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="p-8 text-center text-neutral-400 italic text-sm">Dokter tidak ditemukan.</div>
                    @endif
                </div>
            </div>
        </div>
    </flux:modal>
</div>
