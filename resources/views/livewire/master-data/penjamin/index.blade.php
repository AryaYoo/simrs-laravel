<div class="flex flex-col gap-6 pb-8">
    {{-- Header / Breadcrumb --}}
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('master-data.index') }}" wire:navigate
               class="flex items-center justify-center w-8 h-8 rounded-lg transition-colors hover:bg-neutral-100 dark:hover:bg-neutral-700">
                <flux:icon name="chevron-left" class="w-5 h-5 text-neutral-500" />
            </a>
            <div>
                <nav class="text-xs text-neutral-400 mb-0.5">
                    <a href="{{ route('master-data.index') }}" wire:navigate class="hover:underline">Master Data</a>
                    <span class="mx-1">/</span>
                    <span>Penjamin</span>
                </nav>
                <h1 class="text-xl font-bold text-neutral-800 dark:text-neutral-100">Daftar Master Data Penjamin</h1>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-neutral-800 rounded-xl border border-neutral-200 dark:border-neutral-700 p-4">
        <div class="flex flex-col sm:flex-row gap-3 mb-4 items-end sm:items-center">
            <div class="flex-1 w-full">
                <flux:input wire:model.live.debounce.300ms="search"
                    placeholder="Cari Kode, Nama Penjamin, atau Perusahaan..."
                    icon="magnifying-glass" />
            </div>
            <div class="flex items-center gap-2">
                <span class="text-xs font-medium text-neutral-500">Limit:</span>
                <flux:select wire:model.live="perPage" class="!w-24">
                    <flux:select.option value="20">20</flux:select.option>
                    <flux:select.option value="50">50</flux:select.option>
                    <flux:select.option value="100">100</flux:select.option>
                </flux:select>
            </div>
        </div>

        <flux:table :paginate="$penjamins">
            <flux:table.columns>
                <flux:table.column>{{ __('Kode Penjamin') }}</flux:table.column>
                <flux:table.column>{{ __('Nama Penjamin') }}</flux:table.column>
                <flux:table.column>{{ __('Nama Perusahaan') }}</flux:table.column>
                <flux:table.column>{{ __('Alamat Asuransi') }}</flux:table.column>
                <flux:table.column>{{ __('No. Telephone') }}</flux:table.column>
                <flux:table.column>{{ __('Attn (Contact Person)') }}</flux:table.column>
                <flux:table.column>{{ __('Status') }}</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse ($penjamins as $penjamin)
                    <flux:table.row :key="$penjamin->kd_pj">
                        <flux:table.cell class="font-medium">{{ $penjamin->kd_pj }}</flux:table.cell>
                        <flux:table.cell>{{ $penjamin->png_jawab }}</flux:table.cell>
                        <flux:table.cell>{{ $penjamin->nama_perusahaan }}</flux:table.cell>
                        <flux:table.cell class="truncate max-w-[200px]" title="{{ $penjamin->alamat_asuransi }}">{{ $penjamin->alamat_asuransi }}</flux:table.cell>
                        <flux:table.cell>{{ $penjamin->no_telp }}</flux:table.cell>
                        <flux:table.cell>{{ $penjamin->attn }}</flux:table.cell>
                        <flux:table.cell>
                            @if($penjamin->status == '1')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium"
                                    style="background: #D1FAE5; color: #065F46;">
                                    Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium"
                                    style="background: #FEE2E2; color: #991B1B;">
                                    Non-Aktif
                                </span>
                            @endif
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="7">
                            <div class="flex flex-col items-center justify-center py-12 text-neutral-400 dark:text-neutral-500">
                                <flux:icon name="shield-check" class="w-12 h-12 mb-3 opacity-40" />
                                <p class="text-sm font-medium">Data Penjamin tidak ditemukan</p>
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </div>
</div>
