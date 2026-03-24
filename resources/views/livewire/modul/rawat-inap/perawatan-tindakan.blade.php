<div class="flex flex-col gap-6 pb-8">
    {{-- Header / Breadcrumb --}}
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('modul.rawat-inap.index') }}" wire:navigate
               class="flex items-center justify-center w-8 h-8 rounded-lg transition-colors hover:bg-neutral-100 dark:hover:bg-neutral-700">
                <flux:icon name="chevron-left" class="w-5 h-5 text-neutral-500" />
            </a>
            <div>
                <nav class="text-xs text-neutral-400 mb-0.5">
                    <a href="{{ route('modul.index') }}" wire:navigate class="hover:underline">Modul</a>
                    <span class="mx-1">/</span>
                    <a href="{{ route('modul.rawat-inap.index') }}" wire:navigate class="hover:underline">Rawat Inap</a>
                    <span class="mx-1">/</span>
                    <span>Perawatan/Tindakan</span>
                </nav>
                <h1 class="text-xl font-bold text-neutral-800 dark:text-neutral-100">Perawatan & Tindakan</h1>
                <p class="text-xs text-neutral-500">No. Rawat: {{ $no_rawat }} | Pasien: {{ $regPeriksa->pasien->nm_pasien ?? '-' }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-neutral-800 rounded-xl border border-neutral-200 dark:border-neutral-700 p-6 shadow-sm">
        {{-- Custom Tabs --}}
        <div class="flex flex-wrap gap-2 p-1 bg-neutral-100 dark:bg-neutral-900 rounded-xl mb-6">
            @php
                $tabs = [
                    ['id' => 'penanganan_dokter', 'label' => 'Penanganan Dokter', 'icon' => 'user-plus'],
                    ['id' => 'penanganan_petugas', 'label' => 'Penanganan Petugas', 'icon' => 'users'],
                    ['id' => 'penanganan_dokter_petugas', 'label' => 'Penanganan Dokter & Petugas', 'icon' => 'user-group'],
                    ['id' => 'pemeriksaan', 'label' => 'Pemeriksaan', 'icon' => 'clipboard-document-check'],
                    ['id' => 'pemeriksaan_obstetri', 'label' => 'Pemeriksaan Obstetri', 'icon' => 'heart'],
                    ['id' => 'pemeriksaan_ginekologi', 'label' => 'Pemeriksaan Ginekologi', 'icon' => 'lifebuoy'],
                ];
            @endphp

            @foreach($tabs as $tab)
                <button wire:click="$set('activeTab', '{{ $tab['id'] }}')" 
                    class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition-all {{ $activeTab === $tab['id'] ? 'bg-white dark:bg-neutral-800 text-neutral-800 dark:text-neutral-100 shadow-sm' : 'text-neutral-500 hover:text-neutral-700 dark:hover:text-neutral-300' }}">
                    <flux:icon :name="$tab['icon']" class="w-4 h-4" />
                    {{ $tab['label'] }}
                </button>
            @endforeach
        </div>

        {{-- Tab Panels --}}
        <div class="mt-4">
            @if($activeTab === 'penanganan_dokter')
                <div class="py-10 text-center animate-in fade-in duration-300">
                    <flux:icon name="user-plus" class="w-12 h-12 mx-auto mb-3 opacity-20" />
                    <h3 class="text-lg font-medium text-neutral-800 dark:text-neutral-200">Penanganan Dokter</h3>
                    <p class="text-sm text-neutral-500">Form penanganan oleh dokter akan tampil di sini.</p>
                </div>
            @elseif($activeTab === 'penanganan_petugas')
                <div class="py-10 text-center animate-in fade-in duration-300">
                    <flux:icon name="users" class="w-12 h-12 mx-auto mb-3 opacity-20" />
                    <h3 class="text-lg font-medium text-neutral-800 dark:text-neutral-200">Penanganan Petugas</h3>
                    <p class="text-sm text-neutral-500">Form penanganan oleh petugas akan tampil di sini.</p>
                </div>
            @elseif($activeTab === 'penanganan_dokter_petugas')
                <div class="animate-in fade-in duration-300">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-semibold text-neutral-700 dark:text-neutral-200">Data Penanganan Dokter & Petugas</h3>
                    </div>

                    <flux:table>
                        <flux:table.columns>
                            <flux:table.column>{{ __('No. Rawat') }}</flux:table.column>
                            <flux:table.column>{{ __('No. RM') }}</flux:table.column>
                            <flux:table.column>{{ __('Nama Pasien') }}</flux:table.column>
                            <flux:table.column>{{ __('Perawatan/Tindakan') }}</flux:table.column>
                            <flux:table.column>{{ __('Dokter Yang Menangani') }}</flux:table.column>
                            <flux:table.column>{{ __('Petugas Yang Menangani') }}</flux:table.column>
                            <flux:table.column>{{ __('Tanggal') }}</flux:table.column>
                            <flux:table.column>{{ __('Jam') }}</flux:table.column>
                            <flux:table.column>{{ __('Biaya') }}</flux:table.column>
                        </flux:table.columns>

                        <flux:table.rows>
                            @forelse ($rawatInapDrpr as $item)
                                <flux:table.row :key="$item->no_rawat . $item->tgl_perawatan . $item->jam_rawat . $item->kd_jns_prw">
                                    <flux:table.cell class="whitespace-nowrap">{{ $item->no_rawat }}</flux:table.cell>
                                    <flux:table.cell class="whitespace-nowrap">{{ $item->regPeriksa->no_rkm_medis ?? '-' }}</flux:table.cell>
                                    <flux:table.cell>{{ $item->regPeriksa->pasien->nm_pasien ?? '-' }}</flux:table.cell>
                                    <flux:table.cell>{{ $item->jnsPerawatan->nm_perawatan ?? '-' }}</flux:table.cell>
                                    <flux:table.cell>
                                        <flux:tooltip :content="$item->kd_dokter">
                                            <span>{{ $item->dokter->nm_dokter ?? '-' }}</span>
                                        </flux:tooltip>
                                    </flux:table.cell>
                                    <flux:table.cell>
                                        <flux:tooltip :content="$item->nip">
                                            <span>{{ $item->petugas->nama ?? '-' }}</span>
                                        </flux:tooltip>
                                    </flux:table.cell>
                                    <flux:table.cell class="whitespace-nowrap">{{ $item->tgl_perawatan }}</flux:table.cell>
                                    <flux:table.cell class="whitespace-nowrap">{{ $item->jam_rawat }}</flux:table.cell>
                                    <flux:table.cell class="whitespace-nowrap text-right">{{ number_format($item->biaya_rawat, 0, ',', '.') }}</flux:table.cell>
                                </flux:table.row>
                            @empty
                                <flux:table.row>
                                    <flux:table.cell colspan="9" class="text-center py-12 text-neutral-400">
                                        Tidak ada data penanganan dokter & petugas
                                    </flux:table.cell>
                                </flux:table.row>
                            @endforelse
                        </flux:table.rows>
                    </flux:table>
                </div>
            @elseif($activeTab === 'pemeriksaan')
                <div class="py-10 text-center animate-in fade-in duration-300">
                    <flux:icon name="clipboard-document-check" class="w-12 h-12 mx-auto mb-3 opacity-20" />
                    <h3 class="text-lg font-medium text-neutral-800 dark:text-neutral-200">Pemeriksaan</h3>
                    <p class="text-sm text-neutral-500">Form pemeriksaan fisik dan penunjang akan tampil di sini.</p>
                </div>
            @elseif($activeTab === 'pemeriksaan_obstetri')
                <div class="py-10 text-center animate-in fade-in duration-300">
                    <flux:icon name="heart" class="w-12 h-12 mx-auto mb-3 opacity-20" />
                    <h3 class="text-lg font-medium text-neutral-800 dark:text-neutral-200">Pemeriksaan Obstetri</h3>
                    <p class="text-sm text-neutral-500">Form pemeriksaan obstetri akan tampil di sini.</p>
                </div>
            @elseif($activeTab === 'pemeriksaan_ginekologi')
                <div class="py-10 text-center animate-in fade-in duration-300">
                    <flux:icon name="lifebuoy" class="w-12 h-12 mx-auto mb-3 opacity-20" />
                    <h3 class="text-lg font-medium text-neutral-800 dark:text-neutral-200">Pemeriksaan Ginekologi</h3>
                    <p class="text-sm text-neutral-500">Form pemeriksaan ginekologi akan tampil di sini.</p>
                </div>
            @endif
        </div>
    </div>
</div>
