<div class="flex flex-col gap-6 pb-8">
    {{-- Header / Breadcrumb --}}
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('modul.index') }}" wire:navigate
                class="flex items-center justify-center w-8 h-8 rounded-lg transition-colors hover:bg-neutral-100 dark:hover:bg-neutral-700">
                <flux:icon name="chevron-left" class="w-5 h-5 text-neutral-500" />
            </a>
            <div>
                <nav class="text-xs text-neutral-400 mb-0.5">
                    <a href="{{ route('modul.index') }}" wire:navigate class="hover:underline">Modul</a>
                    <span class="mx-1">/</span>
                    <span>Casemix Rawat Inap</span>
                </nav>
                <h1 class="text-xl font-bold text-neutral-800 dark:text-neutral-100">Daftar Pasien Casemix Rawat Inap</h1>
            </div>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        <div wire:click="setFilter('')"
            class="cursor-pointer transition-all duration-300 group rounded-xl border-2 p-3.5 flex flex-col shadow-sm {{ $filterType === '' ? 'bg-[#4C5C2D] border-[#4C5C2D] shadow-lg scale-[1.02]' : 'bg-white dark:bg-neutral-800 border-neutral-200 dark:border-neutral-700 hover:border-neutral-300' }}">
            <span
                class="text-xs font-semibold mb-0.5 transition-colors {{ $filterType === '' ? 'text-white/80' : 'text-neutral-500 dark:text-neutral-400 group-hover:text-[#4C5C2D]' }}">Total
                Pasien</span>
            <span
                class="text-xl font-bold transition-colors {{ $filterType === '' ? 'text-white' : 'text-neutral-800 dark:text-neutral-100' }}">{{ number_format($summary['total']) }}</span>
        </div>

        <div wire:click="setFilter('bpjs')"
            class="cursor-pointer transition-all duration-300 group rounded-xl border-2 p-3.5 flex flex-col shadow-sm {{ $filterType === 'bpjs' ? 'bg-[#4C5C2D] border-[#4C5C2D] shadow-lg scale-[1.02]' : 'bg-white dark:bg-neutral-800 border-neutral-200 dark:border-neutral-700 hover:border-neutral-300' }}">
            <span
                class="text-xs font-semibold mb-0.5 transition-colors {{ $filterType === 'bpjs' ? 'text-white/80' : 'text-neutral-500 dark:text-neutral-400 group-hover:text-[#4C5C2D]' }}">Pasien
                BPJS</span>
            <span
                class="text-xl font-bold transition-colors {{ $filterType === 'bpjs' ? 'text-white' : 'text-neutral-800 dark:text-neutral-100' }}">{{ number_format($summary['bpjs']) }}</span>
        </div>

        <div wire:click="setFilter('umum')"
            class="cursor-pointer transition-all duration-300 group rounded-xl border-2 p-3.5 flex flex-col shadow-sm {{ $filterType === 'umum' ? 'bg-[#4C5C2D] border-[#4C5C2D] shadow-lg scale-[1.02]' : 'bg-white dark:bg-neutral-800 border-neutral-200 dark:border-neutral-700 hover:border-neutral-300' }}">
            <span
                class="text-xs font-semibold mb-0.5 transition-colors {{ $filterType === 'umum' ? 'text-white/80' : 'text-neutral-500 dark:text-neutral-400 group-hover:text-[#4C5C2D]' }}">Pasien
                Umum</span>
            <span
                class="text-xl font-bold transition-colors {{ $filterType === 'umum' ? 'text-white' : 'text-neutral-800 dark:text-neutral-100' }}">{{ number_format($summary['umum']) }}</span>
        </div>

        <div wire:click="setFilter('lainnya')"
            class="cursor-pointer transition-all duration-300 group rounded-xl border-2 p-3.5 flex flex-col shadow-sm {{ $filterType === 'lainnya' ? 'bg-[#4C5C2D] border-[#4C5C2D] shadow-lg scale-[1.02]' : 'bg-white dark:bg-neutral-800 border-neutral-200 dark:border-neutral-700 hover:border-neutral-300' }}">
            <span
                class="text-xs font-semibold mb-0.5 transition-colors {{ $filterType === 'lainnya' ? 'text-white/80' : 'text-neutral-500 dark:text-neutral-400 group-hover:text-[#4C5C2D]' }}">Lainnya</span>
            <span
                class="text-xl font-bold transition-colors {{ $filterType === 'lainnya' ? 'text-white' : 'text-neutral-800 dark:text-neutral-100' }}">{{ number_format($summary['lainnya']) }}</span>
        </div>
    </div>

    <div class="bg-white dark:bg-neutral-800 rounded-xl border border-neutral-200 dark:border-neutral-700 p-4">
        {{-- Status Filter Tabs --}}
        <div class="flex items-center gap-4 mb-6 pb-4 border-b border-neutral-100 dark:border-neutral-700">
            <div class="flex items-center gap-1 p-1 bg-neutral-100 dark:bg-neutral-900 rounded-xl">
                <button wire:click="$set('statusFilter', 'belum_pulang')"
                    class="px-4 py-2 rounded-lg text-xs font-bold transition-all {{ $statusFilter === 'belum_pulang' ? 'bg-white dark:bg-neutral-800 text-[#4C5C2D] shadow-sm' : 'text-neutral-400 hover:text-neutral-600 dark:hover:text-neutral-200' }}">
                    <div class="flex items-center gap-2">
                        <span class="relative flex h-2 w-2">
                            <span
                                class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                        </span>
                        Aktif (Belum Pulang)
                    </div>
                </button>
                <button wire:click="$set('statusFilter', 'semua')"
                    class="px-4 py-2 rounded-lg text-xs font-bold transition-all {{ $statusFilter === 'semua' ? 'bg-white dark:bg-neutral-800 text-[#4C5C2D] shadow-sm' : 'text-neutral-400 hover:text-neutral-600 dark:hover:text-neutral-200' }}">
                    Semua Pasien
                </button>
            </div>
            <div class="hidden md:block h-4 w-px bg-neutral-200 dark:bg-neutral-700"></div>
            <p class="hidden md:block text-[10px] font-bold text-neutral-400 uppercase tracking-widest">
                {{ $statusFilter === 'belum_pulang' ? 'Menampilkan pasien yang saat ini masih dirawat' : 'Menampilkan seluruh riwayat pasien dalam periode tanggal' }}
            </p>
        </div>

        <div class="flex flex-col sm:flex-row gap-3 mb-4">
            <div class="flex-1">
                <flux:input wire:model.live.debounce.300ms="search"
                    placeholder="Cari No Rawat, No RM, atau Nama Pasien..." icon="magnifying-glass" />
            </div>
            <div class="flex items-center gap-2 flex-shrink-0">
                <label
                    class="text-xs font-semibold text-neutral-500 dark:text-neutral-400 whitespace-nowrap">Dari</label>
                <flux:input type="date" wire:model.live.debounce.500ms="dari" class="w-40" />
                <span class="text-xs text-neutral-400">s/d</span>
                <flux:input type="date" wire:model.live.debounce.500ms="sampai" class="w-40" />
            </div>
        </div>

        <div class="relative">
            <div wire:loading.flex class="absolute inset-0 z-50 bg-white/50 dark:bg-neutral-800/50 backdrop-blur-[1px] items-center justify-center rounded-xl">
                <flux:spacer />
                <flux:icon name="arrow-path" class="w-8 h-8 animate-spin text-[#4C5C2D]" />
                <flux:spacer />
            </div>
            
            <flux:table :paginate="$regPeriksas">
            <flux:table.columns>
                <flux:table.column>{{ __('No. Rawat') }}</flux:table.column>
                <flux:table.column>{{ __('No. RM') }}</flux:table.column>
                <flux:table.column>{{ __('Nama Pasien') }}</flux:table.column>
                <flux:table.column>{{ __('Alamat Pasien') }}</flux:table.column>
                <flux:table.column>{{ __('Penanggung Jawab') }}</flux:table.column>
                <flux:table.column>{{ __('Jenis Bayar') }}</flux:table.column>
                <flux:table.column>{{ __('Kamar') }}</flux:table.column>
                <flux:table.column align="center">{{ __('Resume') }}</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse ($regPeriksas as $reg)
                    @php $isBpjs = str_contains(strtoupper($reg->penjab->png_jawab ?? ''), 'BPJS'); @endphp
                    <flux:table.row :key="$reg->no_rawat" :class="$isBpjs ? 'bg-[#4C5C2D]/5 dark:bg-[#4C5C2D]/10' : ''">
                        <flux:table.cell class="font-medium tracking-tight">{{ $reg->no_rawat }}</flux:table.cell>
                        <flux:table.cell>{{ $reg->no_rkm_medis }}</flux:table.cell>
                        <flux:table.cell>{{ $reg->pasien->nm_pasien ?? '-' }}</flux:table.cell>
                        <flux:table.cell class="max-w-xs truncate">{{ $reg->pasien->alamat ?? '-' }}</flux:table.cell>
                        <flux:table.cell>{{ $reg->p_jawab }}</flux:table.cell>
                        <flux:table.cell>{{ $reg->penjab->png_jawab ?? '-' }}</flux:table.cell>
                        <flux:table.cell>{{ $reg->permintaanRanap->kd_kamar ?? '-' }}</flux:table.cell>
                        <flux:table.cell>
                            <div class="flex justify-center text-center">
                                <a href="{{ route('modul.casemix-rawat-inap.resume', str_replace('/', '-', $reg->no_rawat)) }}" target="_blank"
                                   class="inline-flex items-center justify-center w-8 h-8 rounded-md bg-[#4C5C2D]/10 text-[#4C5C2D] hover:bg-[#4C5C2D] hover:text-white transition-colors border border-[#4C5C2D]/20 hover:border-[#4C5C2D]"
                                   title="Resume (CASEMIX)">
                                    <flux:icon name="document-text" class="w-4 h-4" />
                                </a>
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="8">
                            <div
                                class="flex flex-col items-center justify-center py-12 text-neutral-400 dark:text-neutral-500">
                                <flux:icon name="calendar" class="w-12 h-12 mb-3 opacity-40" />
                                <p class="text-sm font-medium">Tidak ada pasien rawat inap di periode ini</p>
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
        </div>
    </div>
</div>
