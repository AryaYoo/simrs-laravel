<div class="flex flex-col gap-6 pb-8" x-data="{
    menuModalOpen: false,
    verifikasiModalOpen: false,
    searchQuery: '',
    selectedNamaBayar: '',
    activeItem: { notaJual: '', nmPasien: '', noRM: '', namaBayar: '' },
    openMenu(notaJual, nmPasien, noRM, namaBayar) {
        this.activeItem = { notaJual: notaJual, nmPasien: nmPasien, noRM: noRM, namaBayar: namaBayar };
        this.selectedNamaBayar = namaBayar || ($wire.listAkunBayar.length > 0 ? $wire.listAkunBayar[0].nama_bayar : '');
        this.menuModalOpen = true;
    },
    openVerifikasiModal() {
        this.menuModalOpen = false;
        this.selectedNamaBayar = this.activeItem.namaBayar || ($wire.listAkunBayar.length > 0 ? $wire.listAkunBayar[0].nama_bayar : '');
        this.verifikasiModalOpen = true;
    }
}">

    {{-- Header / Breadcrumb --}}
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div>
            <nav class="text-xs text-neutral-400 mb-0.5">
                <a href="{{ route('modul.index') }}" wire:navigate class="hover:underline">Modul</a>
                <span class="mx-1">/</span>
                <span>Farmasi</span>
                <span class="mx-1">/</span>
                <span>Penjualan Obat & BHP</span>
            </nav>
            <h1 class="text-xl font-bold text-neutral-800 dark:text-neutral-100">Penjualan Obat & BHP</h1>
        </div>
        {{-- Tombol Tambah --}}
        <a href="{{ route('modul.farmasi.create-penjualan') }}" wire:navigate
            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white shadow transition-colors"
            style="background-color: #4C5C2D;"
            title="Tambah transaksi penjualan baru">
            <flux:icon name="plus" class="w-4 h-4" />
            Tambah Penjualan
        </a>
    </div>

    {{-- Filter & Search Bar --}}
    <div class="bg-white dark:bg-neutral-800 rounded-xl border border-neutral-200 dark:border-neutral-700 p-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">

            {{-- Search --}}
            <div class="relative lg:col-span-2">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <flux:icon name="magnifying-glass" class="w-4 h-4 text-neutral-400" />
                </div>
                <input type="text" wire:model.live.debounce.300ms="search"
                    class="block w-full pl-9 pr-3 py-2 text-sm rounded-lg border border-neutral-200 dark:border-neutral-700
                           bg-white dark:bg-neutral-800 text-neutral-900 dark:text-neutral-100
                           placeholder-neutral-400 focus:outline-none focus:ring-2 focus:ring-[#4C5C2D]/30 focus:border-[#4C5C2D] transition-colors"
                    placeholder="Cari nama pasien, No. RM, atau No. Nota..." />
            </div>

            {{-- Filter Petugas --}}
            <div>
                <select wire:model.live="nip"
                    class="block w-full py-2 px-3 text-sm rounded-lg border border-neutral-200 dark:border-neutral-700
                           bg-white dark:bg-neutral-800 text-neutral-900 dark:text-neutral-100
                           focus:outline-none focus:ring-2 focus:ring-[#4C5C2D]/30 focus:border-[#4C5C2D] transition-colors">
                    <option value="">— Semua Petugas —</option>
                    @foreach($petugasList as $p)
                        <option value="{{ $p['nip'] }}">{{ $p['nama'] }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Per Page --}}
            <div>
                <select wire:model.live="perPage"
                    class="block w-full py-2 px-3 text-sm rounded-lg border border-neutral-200 dark:border-neutral-700
                           bg-white dark:bg-neutral-800 text-neutral-900 dark:text-neutral-100
                           focus:outline-none focus:ring-2 focus:ring-[#4C5C2D]/30 focus:border-[#4C5C2D] transition-colors">
                    <option value="10">10 / halaman</option>
                    <option value="20">20 / halaman</option>
                    <option value="50">50 / halaman</option>
                    <option value="100">100 / halaman</option>
                </select>
            </div>

            {{-- Filter Tanggal Dari --}}
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <flux:icon name="calendar" class="w-4 h-4 text-neutral-400" />
                </div>
                <input type="date" wire:model.live="dari"
                    class="block w-full pl-9 pr-3 py-2 text-sm rounded-lg border border-neutral-200 dark:border-neutral-700
                           bg-white dark:bg-neutral-800 text-neutral-900 dark:text-neutral-100
                           focus:outline-none focus:ring-2 focus:ring-[#4C5C2D]/30 focus:border-[#4C5C2D] transition-colors" />
            </div>

            {{-- Filter Tanggal Sampai --}}
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <flux:icon name="calendar" class="w-4 h-4 text-neutral-400" />
                </div>
                <input type="date" wire:model.live="sampai"
                    class="block w-full pl-9 pr-3 py-2 text-sm rounded-lg border border-neutral-200 dark:border-neutral-700
                           bg-white dark:bg-neutral-800 text-neutral-900 dark:text-neutral-100
                           focus:outline-none focus:ring-2 focus:ring-[#4C5C2D]/30 focus:border-[#4C5C2D] transition-colors" />
            </div>

            {{-- Reset Filter --}}
            <div class="flex items-end">
                <button type="button"
                    wire:click="$set('search', ''); $set('nip', ''); $set('dari', '{{ now()->format('Y-m-d') }}'); $set('sampai', '{{ now()->format('Y-m-d') }}')"
                    class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg text-sm font-medium border border-neutral-200 dark:border-neutral-600
                           text-neutral-600 dark:text-neutral-300 bg-white dark:bg-neutral-800 hover:bg-neutral-50 dark:hover:bg-neutral-700 transition-colors">
                    <flux:icon name="arrow-path" class="w-3.5 h-3.5" />
                    Reset
                </button>
            </div>

        </div>
    </div>

    {{-- Tabel Utama --}}
    <div
        class="bg-white dark:bg-neutral-800 rounded-xl border border-neutral-200 dark:border-neutral-700 overflow-hidden">

        {{-- Info total --}}
        <div class="px-4 py-2.5 border-b border-neutral-100 dark:border-neutral-700 flex items-center justify-between">
            <p class="text-xs text-neutral-500 dark:text-neutral-400">
                Total: <span
                    class="font-semibold text-neutral-700 dark:text-neutral-200">{{ $penjualans->total() }}</span>
                transaksi
            </p>
            <div wire:loading class="flex items-center gap-1.5 text-xs text-neutral-400">
                <svg class="animate-spin w-3.5 h-3.5" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                Memuat...
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-neutral-100 dark:border-neutral-700" style="background-color: #F1F5E9;">
                        <th
                            class="px-3 py-2.5 text-center text-xs font-semibold text-neutral-600 dark:text-neutral-300 whitespace-nowrap">
                            Menu</th>
                        <th
                            class="px-3 py-2.5 text-left text-xs font-semibold text-neutral-600 dark:text-neutral-300 whitespace-nowrap">
                            No. Nota</th>
                        <th
                            class="px-3 py-2.5 text-left text-xs font-semibold text-neutral-600 dark:text-neutral-300 whitespace-nowrap">
                            Tanggal</th>
                        <th
                            class="px-3 py-2.5 text-left text-xs font-semibold text-neutral-600 dark:text-neutral-300 whitespace-nowrap">
                            Petugas</th>
                        <th
                            class="px-3 py-2.5 text-left text-xs font-semibold text-neutral-600 dark:text-neutral-300 whitespace-nowrap">
                            Pasien</th>
                        <th
                            class="px-3 py-2.5 text-left text-xs font-semibold text-neutral-600 dark:text-neutral-300 whitespace-nowrap">
                            Keterangan</th>
                        <th
                            class="px-3 py-2.5 text-left text-xs font-semibold text-neutral-600 dark:text-neutral-300 whitespace-nowrap">
                            Jenis Jual</th>
                        <th
                            class="px-3 py-2.5 text-right text-xs font-semibold text-neutral-600 dark:text-neutral-300 whitespace-nowrap">
                            PPN (Rp)</th>
                        <th
                            class="px-3 py-2.5 text-left text-xs font-semibold text-neutral-600 dark:text-neutral-300 whitespace-nowrap">
                            Asal Stok</th>
                        <th
                            class="px-3 py-2.5 text-right text-xs font-semibold text-neutral-600 dark:text-neutral-300 whitespace-nowrap">
                            Ongkos Kirim</th>
                        <th
                            class="px-3 py-2.5 text-left text-xs font-semibold text-neutral-600 dark:text-neutral-300 whitespace-nowrap">
                            Akun Bayar</th>
                        <th
                            class="px-3 py-2.5 text-center text-xs font-semibold text-neutral-600 dark:text-neutral-300 whitespace-nowrap">
                            Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100 dark:divide-neutral-700">
                    @forelse($penjualans as $row)
                        <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-700/40 transition-colors">

                            {{-- Sub Menu Penjualan (Grid Icon + Eye Icon di sebelah kanannya) --}}
                            <td class="px-3 py-2.5 whitespace-nowrap text-center">
                                <div class="inline-flex items-center justify-center gap-1">
                                    {{-- Tombol Pop-up Sub Menu --}}
                                    <button type="button"
                                        @click="openMenu('{{ $row->nota_jual }}', '{{ addslashes($row->nm_pasien ?? '-') }}', '{{ $row->no_rkm_medis ?? '-' }}', '{{ addslashes($row->nama_bayar ?? '') }}')"
                                        class="inline-flex items-center justify-center shrink-0 w-8 h-8 rounded-md hover:bg-neutral-100 dark:hover:bg-neutral-700 text-neutral-500 transition-colors"
                                        title="Sub Menu Penjualan">
                                        <flux:icon name="squares-2x2" class="w-4 h-4" />
                                    </button>
                                    {{-- Tombol Mata (Buka Halaman Detail Khusus) --}}
                                    <a href="{{ route('modul.farmasi.detail-penjualan', $row->nota_jual) }}" wire:navigate
                                        class="inline-flex items-center justify-center shrink-0 w-8 h-8 rounded-md hover:bg-[#4C5C2D]/10 text-[#4C5C2D] transition-colors"
                                        title="Lihat Detail Transaksi">
                                        <flux:icon name="eye" class="w-4 h-4" />
                                    </a>
                                </div>
                            </td>

                            {{-- No. Nota --}}
                            <td class="px-3 py-2.5 whitespace-nowrap">
                                <span
                                    class="font-mono text-xs font-semibold text-[#4C5C2D] bg-[#F1F5E9] px-2 py-0.5 rounded">
                                    {{ $row->nota_jual }}
                                </span>
                            </td>

                            {{-- Tanggal --}}
                            <td class="px-3 py-2.5 whitespace-nowrap text-neutral-700 dark:text-neutral-200 text-xs">
                                {{ $row->tgl_jual?->format('d/m/Y') ?? '-' }}
                            </td>

                            {{-- Petugas --}}
                            <td class="px-3 py-2.5 whitespace-nowrap text-xs text-neutral-700 dark:text-neutral-200">
                                {{ $row->petugas->nama ?? $row->nip ?? '-' }}
                            </td>

                            {{-- Pasien --}}
                            <td class="px-3 py-2.5">
                                <div class="flex flex-col">
                                    <span class="text-xs font-medium text-neutral-800 dark:text-neutral-100 leading-tight">
                                        {{ $row->nm_pasien ?? '-' }}
                                    </span>
                                    @if($row->no_rkm_medis && $row->no_rkm_medis !== '-')
                                        <span class="text-[10px] text-neutral-400 font-mono">{{ $row->no_rkm_medis }}</span>
                                    @endif
                                </div>
                            </td>

                            {{-- Keterangan --}}
                            <td class="px-3 py-2.5 max-w-[140px]">
                                <span class="text-xs text-neutral-500 dark:text-neutral-400 truncate block"
                                    title="{{ $row->keterangan }}">
                                    {{ $row->keterangan ?: '-' }}
                                </span>
                            </td>

                            {{-- Jenis Jual --}}
                            <td class="px-3 py-2.5 whitespace-nowrap">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $row->jnsJualBadgeClass() }}">
                                    {{ $row->jnsJualLabel() }}
                                </span>
                            </td>

                            {{-- PPN --}}
                            <td
                                class="px-3 py-2.5 whitespace-nowrap text-right text-xs text-neutral-700 dark:text-neutral-200">
                                {{ number_format($row->ppn ?? 0, 0, ',', '.') }}
                            </td>

                            {{-- Asal Stok --}}
                            <td class="px-3 py-2.5 whitespace-nowrap text-xs text-neutral-700 dark:text-neutral-200">
                                {{ $row->bangsal->nm_bangsal ?? $row->kd_bangsal ?? '-' }}
                            </td>

                            {{-- Ongkos Kirim --}}
                            <td
                                class="px-3 py-2.5 whitespace-nowrap text-right text-xs text-neutral-700 dark:text-neutral-200">
                                {{ $row->ongkir ? number_format($row->ongkir, 0, ',', '.') : '-' }}
                            </td>

                            {{-- Akun Bayar --}}
                            <td class="px-3 py-2.5 whitespace-nowrap text-xs text-neutral-700 dark:text-neutral-200">
                                {{ $row->nama_bayar ?? '-' }}
                            </td>

                            {{-- Status --}}
                            <td class="px-3 py-2.5 text-center whitespace-nowrap">
                                @if($row->isSudahDibayar())
                                    <span
                                        class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-100 text-emerald-700">
                                        <flux:icon name="check-circle" class="w-3 h-3" />
                                        Lunas
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-red-100 text-red-600">
                                        <flux:icon name="clock" class="w-3 h-3" />
                                        Belum
                                    </span>
                                @endif
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="12" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <div
                                        class="w-12 h-12 rounded-xl flex items-center justify-center bg-neutral-100 dark:bg-neutral-700">
                                        <flux:icon name="shopping-bag" class="w-6 h-6 text-neutral-400" />
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-neutral-500 dark:text-neutral-400">Tidak ada data
                                            penjualan</p>
                                        <p class="text-xs text-neutral-400 dark:text-neutral-500 mt-0.5">Coba ubah filter
                                            pencarian atau rentang tanggal</p>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($penjualans->hasPages())
            <div class="px-4 py-3 border-t border-neutral-100 dark:border-neutral-700">
                {{ $penjualans->links() }}
            </div>
        @endif

    </div>

    {{-- Modal Popup Sub Menu Penjualan --}}
    <template x-teleport="body">
        <div x-show="menuModalOpen" x-cloak
            class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 sm:p-6">

            {{-- Backdrop --}}
            <div x-show="menuModalOpen" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0" @click="menuModalOpen = false"
                class="fixed inset-0 bg-neutral-900/60 backdrop-blur-sm"></div>

            {{-- Modal Panel --}}
            <div x-show="menuModalOpen" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                class="relative w-full max-w-4xl max-h-[85vh] flex flex-col bg-white dark:bg-neutral-900 rounded-2xl shadow-2xl border border-neutral-200 dark:border-neutral-700 overflow-hidden z-10"
                @click.stop>

                {{-- Modal Header --}}
                <div
                    class="flex flex-col sm:flex-row items-center gap-4 justify-between px-6 py-4 bg-[#4C5C2D] text-white flex-shrink-0 shadow-lg">
                    <div class="flex flex-col gap-1">
                        <div class="flex items-center gap-3">
                            <div class="p-2 rounded-lg bg-white/20">
                                <flux:icon name="squares-2x2" class="w-5 h-5 text-white" />
                            </div>
                            <h2 class="font-bold text-white text-lg">Sub Menu Penjualan</h2>
                        </div>
                        <div class="flex items-center gap-2 mt-1 text-xs opacity-90">
                            <span class="bg-white/20 px-2 py-0.5 rounded font-mono" x-text="activeItem.notaJual"></span>
                            <span class="opacity-50">•</span>
                            <span class="font-semibold uppercase tracking-wide" x-text="activeItem.nmPasien"></span>
                            <template x-if="activeItem.noRM && activeItem.noRM !== '-'">
                                <span class="opacity-75" x-text="'(' + activeItem.noRM + ')'"></span>
                            </template>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 w-full sm:w-auto">
                        <button type="button" @click="menuModalOpen = false"
                            class="p-2 rounded-lg hover:bg-white/10 text-white/70 hover:text-white transition-colors">
                            <flux:icon name="x-mark" class="w-5 h-5" />
                        </button>
                    </div>
                </div>

                {{-- Modal Body: 4 Action Buttons (Konsisten dengan Sub Menu Layanan Rawat Jalan) --}}
                <div class="overflow-y-auto flex-1 p-6 bg-white dark:bg-neutral-900">
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        {{-- 1. Cetak Ulang Nota --}}
                        <a :href="'/modul/farmasi/penjualan/' + encodeURIComponent(activeItem.notaJual) + '/cetak-nota'"
                            target="_blank"
                            class="group w-full h-[72px] flex items-center gap-3 p-3 rounded-xl bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-700 text-neutral-700 dark:text-neutral-300 hover:border-[#4C5C2D] hover:bg-[#4C5C2D]/5 transition-all shadow-sm">
                            <div class="w-9 h-9 rounded-lg flex items-center justify-center bg-neutral-100 dark:bg-neutral-800 text-neutral-500 group-hover:bg-[#4C5C2D] group-hover:text-white transition-colors flex-shrink-0">
                                <flux:icon name="printer" class="w-4.5 h-4.5" />
                            </div>
                            <span class="text-[11px] font-semibold leading-tight flex-1 line-clamp-2">Cetak Ulang Nota</span>
                        </a>

                        {{-- 2. Cetak Aturan Pakai --}}
                        <a :href="'/modul/farmasi/penjualan/' + encodeURIComponent(activeItem.notaJual) + '/cetak-aturan-pakai'"
                            target="_blank"
                            class="group w-full h-[72px] flex items-center gap-3 p-3 rounded-xl bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-700 text-neutral-700 dark:text-neutral-300 hover:border-[#4C5C2D] hover:bg-[#4C5C2D]/5 transition-all shadow-sm">
                            <div class="w-9 h-9 rounded-lg flex items-center justify-center bg-neutral-100 dark:bg-neutral-800 text-neutral-500 group-hover:bg-[#4C5C2D] group-hover:text-white transition-colors flex-shrink-0">
                                <flux:icon name="document-text" class="w-4.5 h-4.5" />
                            </div>
                            <span class="text-[11px] font-semibold leading-tight flex-1 line-clamp-2">Cetak Aturan Pakai</span>
                        </a>

                        {{-- 3. Verifikasi --}}
                        <button type="button"
                            @click="openVerifikasiModal()"
                            class="group w-full h-[72px] flex items-center gap-3 p-3 rounded-xl bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-700 text-neutral-700 dark:text-neutral-300 hover:border-[#4C5C2D] hover:bg-[#4C5C2D]/5 transition-all shadow-sm text-left">
                            <div class="w-9 h-9 rounded-lg flex items-center justify-center bg-neutral-100 dark:bg-neutral-800 text-neutral-500 group-hover:bg-[#4C5C2D] group-hover:text-white transition-colors flex-shrink-0">
                                <flux:icon name="check-badge" class="w-4.5 h-4.5" />
                            </div>
                            <span class="text-[11px] font-semibold leading-tight flex-1 line-clamp-2">Verifikasi</span>
                        </button>

                        {{-- 4. Hapus Penjualan --}}
                        <button type="button"
                            @click="
                                menuModalOpen = false;
                                Swal.fire({
                                    title: 'Hapus Penjualan?',
                                    text: 'Transaksi ' + activeItem.notaJual + ' akan dihapus permanen dari database.',
                                    icon: 'warning',
                                    showCancelButton: true,
                                    confirmButtonColor: '#dc2626',
                                    cancelButtonColor: '#6b7280',
                                    confirmButtonText: 'Ya, Hapus!'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        $wire.deletePenjualan(activeItem.notaJual);
                                    }
                                });
                            "
                            class="group w-full h-[72px] flex items-center gap-3 p-3 rounded-xl bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-700 text-neutral-700 dark:text-neutral-300 hover:border-rose-500 hover:bg-rose-50/50 dark:hover:bg-rose-950/20 transition-all shadow-sm text-left">
                            <div class="w-9 h-9 rounded-lg flex items-center justify-center bg-neutral-100 dark:bg-neutral-800 text-neutral-500 group-hover:bg-rose-600 group-hover:text-white transition-colors flex-shrink-0">
                                <flux:icon name="trash" class="w-4.5 h-4.5" />
                            </div>
                            <span class="text-[11px] font-semibold leading-tight flex-1 line-clamp-2 text-rose-600 dark:text-rose-400">Hapus Penjualan</span>
                        </button>
                    </div>
                </div>



            </div>
        </div>
    </template>

    {{-- Modal Popup Verifikasi Penjualan --}}
    <template x-teleport="body">
        <div x-show="verifikasiModalOpen" x-cloak
            class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 sm:p-6">

            {{-- Backdrop --}}
            <div x-show="verifikasiModalOpen" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0" @click="verifikasiModalOpen = false"
                class="fixed inset-0 bg-neutral-900/60 backdrop-blur-sm"></div>

            {{-- Modal Panel --}}
            <div x-show="verifikasiModalOpen" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                class="relative w-full max-w-md flex flex-col bg-white dark:bg-neutral-900 rounded-2xl shadow-2xl border border-neutral-200 dark:border-neutral-700 overflow-hidden z-10"
                @click.stop>

                {{-- Header --}}
                <div class="flex items-center justify-between px-5 py-3.5 border-b border-neutral-200 dark:border-neutral-700 bg-[#4C5C2D] text-white">
                    <div>
                        <h3 class="font-bold text-white text-sm flex items-center gap-2">
                            <flux:icon name="check-badge" class="w-4 h-4 text-white" />
                            Verifikasi Penjualan
                        </h3>
                        <p class="text-[11px] text-white/80 mt-0.5" x-text="activeItem.notaJual + ' • ' + activeItem.nmPasien"></p>
                    </div>
                    <button type="button" @click="verifikasiModalOpen = false" class="p-1 rounded-lg hover:bg-white/10 text-white/80 hover:text-white transition-colors">
                        <flux:icon name="x-mark" class="w-4 h-4" />
                    </button>
                </div>

                {{-- Body --}}
                <div class="p-5 bg-white dark:bg-neutral-900">
                    <div class="flex items-center gap-3">
                        <label class="text-xs font-semibold text-neutral-700 dark:text-neutral-300 shrink-0">Akun Bayar :</label>
                        
                        <div class="flex-1 flex items-center gap-1.5">
                            <select x-model="selectedNamaBayar" class="w-full bg-white dark:bg-neutral-800 border border-neutral-300 dark:border-neutral-600 text-neutral-800 dark:text-neutral-100 rounded-lg text-xs px-3 py-2 font-semibold shadow-sm focus:border-[#4C5C2D] focus:ring-[#4C5C2D]">
                                <template x-for="akun in $wire.listAkunBayar" :key="akun.nama_bayar">
                                    <option :value="akun.nama_bayar" x-text="akun.nama_bayar" :selected="akun.nama_bayar === selectedNamaBayar"></option>
                                </template>
                            </select>
                            
                            <button type="button" @click="$wire.loadListAkunBayar()" class="p-2 bg-neutral-100 dark:bg-neutral-800 hover:bg-neutral-200 dark:hover:bg-neutral-700 text-neutral-600 dark:text-neutral-300 rounded-lg border border-neutral-300 dark:border-neutral-600 transition-colors shadow-sm" title="Refresh Akun Bayar">
                                <flux:icon name="arrow-path" class="w-3.5 h-3.5" />
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Footer Action Buttons (Simpan & Tutup) --}}
                <div class="px-5 py-3.5 bg-neutral-50 dark:bg-neutral-800/50 border-t border-neutral-200 dark:border-neutral-700 flex justify-end items-center gap-2">
                    <button type="button"
                        @click="
                            verifikasiModalOpen = false;
                            $wire.verifikasiPenjualan(activeItem.notaJual, selectedNamaBayar);
                        "
                        class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg bg-[#4C5C2D] hover:bg-[#3d4b24] text-white font-semibold text-xs transition-colors shadow-sm">
                        <flux:icon name="circle-stack" class="w-3.5 h-3.5" />
                        Simpan
                    </button>

                    <button type="button" @click="verifikasiModalOpen = false"
                        class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg bg-neutral-200 dark:bg-neutral-700 hover:bg-neutral-300 dark:hover:bg-neutral-600 text-neutral-700 dark:text-neutral-200 font-semibold text-xs transition-colors">
                        <flux:icon name="x-mark" class="w-3.5 h-3.5 text-rose-500" />
                        Tutup
                    </button>
                </div>

            </div>
        </div>
    </template>

</div>