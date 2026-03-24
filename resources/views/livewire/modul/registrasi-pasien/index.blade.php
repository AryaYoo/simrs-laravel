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
                    <span>Registrasi</span>
                </nav>
                <h1 class="text-xl font-bold text-neutral-800 dark:text-neutral-100">Daftar Registrasi Pasien</h1>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <flux:button variant="primary" icon="plus" :href="route('modul.registrasi-pasien.create')" wire:navigate>Registrasi</flux:button>
            <flux:button icon="plus" :href="route('modul.registrasi-pasien.new')" wire:navigate>Pasien Baru</flux:button>
        </div>
    </div>

    <div class="bg-white dark:bg-neutral-800 rounded-xl border border-neutral-200 dark:border-neutral-700 p-4">
<div class="flex flex-col sm:flex-row gap-3 mb-4">
            <div class="flex-1">
                <flux:input wire:model.live.debounce.300ms="search"
                    placeholder="Cari No Rawat, No RM, atau Nama Pasien..."
                    icon="magnifying-glass" />
            </div>
            <div class="flex items-center gap-2 flex-shrink-0">
                <label class="text-xs font-semibold text-neutral-500 dark:text-neutral-400 whitespace-nowrap">Dari</label>
                <flux:input type="date" wire:model.live="dari" class="w-40" />
                <span class="text-xs text-neutral-400">s/d</span>
                <flux:input type="date" wire:model.live="sampai" class="w-40" />
            </div>
            <div class="flex items-center gap-2 border-l border-neutral-200 dark:border-neutral-700 pl-3 ml-1">
                <span class="text-xs font-medium text-neutral-500">Limit:</span>
                <flux:select wire:model.live="perPage" class="!w-24">
                    <flux:select.option value="20">20</flux:select.option>
                    <flux:select.option value="50">50</flux:select.option>
                    <flux:select.option value="100">100</flux:select.option>
                </flux:select>
            </div>
        </div>

        <flux:table :paginate="$regPeriksas">
            <flux:table.columns>
                <flux:table.column>{{ __('No Registrasi') }}</flux:table.column>
                <flux:table.column>{{ __('No Rawat') }}</flux:table.column>
                <flux:table.column>{{ __('Tanggal') }}</flux:table.column>
                <flux:table.column>{{ __('Jam') }}</flux:table.column>
                <flux:table.column>{{ __('Dokter') }}</flux:table.column>
                <flux:table.column>{{ __('Nomor RM') }}</flux:table.column>
                <flux:table.column>{{ __('Pasien') }}</flux:table.column>
                <flux:table.column>{{ __('Jenis Kelamin') }}</flux:table.column>
                <flux:table.column>{{ __('Action') }}</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse ($regPeriksas as $reg)
                    @php $isBpjs = str_contains(strtoupper($reg->penjab->png_jawab ?? ''), 'BPJS'); @endphp
                    <flux:table.row :key="$reg->no_rawat" :class="$isBpjs ? 'bg-[#4C5C2D]/5 dark:bg-[#4C5C2D]/10' : ''">
                        <flux:table.cell>{{ $reg->no_reg }}</flux:table.cell>
                        <flux:table.cell class="font-medium">{{ $reg->no_rawat }}</flux:table.cell>
                        <flux:table.cell>{{ $reg->tgl_registrasi }}</flux:table.cell>
                        <flux:table.cell>{{ $reg->jam_reg }}</flux:table.cell>
                        <flux:table.cell>{{ $reg->dokter->nm_dokter ?? '-' }}</flux:table.cell>
                        <flux:table.cell>{{ $reg->no_rkm_medis }}</flux:table.cell>
                        <flux:table.cell>{{ $reg->pasien->nm_pasien ?? '-' }}</flux:table.cell>
                        <flux:table.cell>{{ $reg->pasien->jk ?? '-' }}</flux:table.cell>
                        <flux:table.cell>
                            <flux:button icon="eye" size="xs" :href="route('modul.registrasi-pasien.show', $reg->no_rawat)" wire:navigate variant="ghost" />
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="9">
                            <div class="flex flex-col items-center justify-center py-12 text-neutral-400 dark:text-neutral-500">
                                <flux:icon name="calendar" class="w-12 h-12 mb-3 opacity-40" />
                                <p class="text-sm font-medium">Tidak ada pasien di tanggal ini</p>
                                <p class="text-xs mt-1 opacity-70">
                                    {{ $dari ? \Carbon\Carbon::parse($dari)->isoFormat('D MMM Y') : '-' }}
                                    @if($dari !== $sampai)
                                        &ndash; {{ $sampai ? \Carbon\Carbon::parse($sampai)->isoFormat('D MMM Y') : '-' }}
                                    @endif
                                </p>
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </div>
</div>
