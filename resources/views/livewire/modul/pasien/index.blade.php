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
                    <span>Pasien</span>
                </nav>
                <h1 class="text-xl font-bold text-neutral-800 dark:text-neutral-100">Daftar Master Data Pasien</h1>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <flux:button variant="primary" icon="plus" :href="route('modul.registrasi-pasien.new')" wire:navigate>Pasien Baru</flux:button>
        </div>
    </div>

    <div class="bg-white dark:bg-neutral-800 rounded-xl border border-neutral-200 dark:border-neutral-700 p-4">
        <div class="flex flex-col sm:flex-row gap-3 mb-4 items-end sm:items-center">
            <div class="flex-1 w-full">
                <flux:input wire:model.live.debounce.300ms="search"
                    placeholder="Cari No. RM atau Nama Pasien..."
                    icon="magnifying-glass" />
            </div>
            <div class="flex items-center gap-2">
                <span class="text-xs font-medium text-neutral-500">Limit:</span>
                <flux:select wire:model.live="perPage" variant="filled" class="!w-24">
                    <flux:select.option value="20">20</flux:select.option>
                    <flux:select.option value="50">50</flux:select.option>
                    <flux:select.option value="100">100</flux:select.option>
                </flux:select>
            </div>
        </div>

        <flux:table :paginate="$pasiens">
            <flux:table.columns>
                <flux:table.column>{{ __('No Rekam Medis') }}</flux:table.column>
                <flux:table.column>{{ __('Nama Pasien') }}</flux:table.column>
                <flux:table.column>{{ __('Jenis Kelamin') }}</flux:table.column>
                <flux:table.column>{{ __('Tempat Lahir') }}</flux:table.column>
                <flux:table.column>{{ __('Tanggal Lahir') }}</flux:table.column>
                <flux:table.column>{{ __('Nama Ibu') }}</flux:table.column>
                <flux:table.column>{{ __('Alamat') }}</flux:table.column>
                <flux:table.column>{{ __('Action') }}</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse ($pasiens as $pasien)
                    <flux:table.row :key="$pasien->no_rkm_medis">
                        <flux:table.cell class="font-medium">{{ $pasien->no_rkm_medis }}</flux:table.cell>
                        <flux:table.cell>{{ $pasien->nm_pasien }}</flux:table.cell>
                        <flux:table.cell>{{ $pasien->jk }}</flux:table.cell>
                        <flux:table.cell>{{ $pasien->tmp_lahir }}</flux:table.cell>
                        <flux:table.cell>{{ $pasien->tgl_lahir }}</flux:table.cell>
                        <flux:table.cell>{{ $pasien->nm_ibu }}</flux:table.cell>
                        <flux:table.cell class="truncate max-w-[200px]" title="{{ $pasien->alamat }}">{{ $pasien->alamat }}</flux:table.cell>
                        <flux:table.cell>
                            <flux:button icon="eye" size="xs" :href="route('modul.pasien.show', $pasien->no_rkm_medis)" wire:navigate variant="ghost" />
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="8">
                            <div class="flex flex-col items-center justify-center py-12 text-neutral-400 dark:text-neutral-500">
                                <flux:icon name="user-group" class="w-12 h-12 mb-3 opacity-40" />
                                <p class="text-sm font-medium">Data Pasien tidak ditemukan</p>
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </div>
</div>
