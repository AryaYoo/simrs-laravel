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
