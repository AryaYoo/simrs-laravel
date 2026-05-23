<div class="p-6 pb-24" x-data="{
    showModal: false,
    showDokterModal: false,
    showPaketModal: false,
    showRuangModal: false,
    init() {
        Livewire.on('open-modal', () => { this.showModal = true; });
        Livewire.on('close-modal', () => { this.showModal = false; });
        Livewire.on('close-modal-dokter', () => { this.showDokterModal = false; });
        Livewire.on('close-modal-paket', () => { this.showPaketModal = false; });
        Livewire.on('close-modal-ruang', () => { this.showRuangModal = false; });
    }
}">
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
                    <span>Jadwal Operasi</span>
                </nav>
                <h1 class="text-xl font-bold text-neutral-800 dark:text-neutral-100">Jadwal Operasi</h1>
            </div>
        </div>
        <div class="flex gap-2">
            <flux:button variant="primary" icon="plus" @click="$wire.prepareAttach().then(() => { showModal = true })"
                class="!bg-[#4C5C2D] hover:!bg-[#3f4d25] !text-white !border-none">Buat Baru</flux:button>
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
                    <span class="font-mono text-xs bg-white/20 px-2 py-0.5 rounded">{{ $pasien->no_rawat }}</span>
                    <span class="font-mono text-xs bg-white/20 px-2 py-0.5 rounded">{{ $pasien->no_rkm_medis }}</span>
                </div>
                <h2 class="text-lg font-bold">{{ $pasien->pasien->nm_pasien ?? '-' }}</h2>
                <div class="text-xs text-white/80 mt-1 flex flex-wrap items-center gap-3">
                    <span class="flex items-center gap-1"><flux:icon name="identification" class="w-3 h-3"/> {{ $pasien->pasien->no_ktp ?? '-' }}</span>
                    <span class="flex items-center gap-1"><flux:icon name="home" class="w-3 h-3"/> {{ $pasien->kamarInap->first()->kamar->bangsal->nm_bangsal ?? 'Belum ada kamar' }}</span>
                </div>
            </div>
        </div>
        <div class="text-left md:text-right text-sm border-t md:border-t-0 md:border-l border-white/20 pt-3 md:pt-0 md:pl-4">
            <p class="text-white/80 text-xs mb-1">Dokter DPJP Pasien</p>
            <p class="font-semibold">{{ $pasien->dokter->nm_dokter ?? '-' }}</p>
        </div>
    </div>

    {{-- List Data --}}
    <div class="bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-200 dark:border-neutral-800 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-xs text-neutral-500 bg-neutral-50 dark:bg-neutral-800 uppercase border-b border-neutral-200 dark:border-neutral-700">
                    <tr>
                        <th class="px-6 py-4 font-bold">Jadwal</th>
                        <th class="px-6 py-4 font-bold">Ruang OK</th>
                        <th class="px-6 py-4 font-bold">Operator</th>
                        <th class="px-6 py-4 font-bold">Operasi</th>
                        <th class="px-6 py-4 font-bold">Status</th>
                        <th class="px-6 py-4 font-bold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100 dark:divide-neutral-800">
                    @forelse ($dataOperasi as $item)
                        <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-800/50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-medium text-neutral-900 dark:text-white">{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</div>
                                <div class="text-neutral-500 text-xs mt-0.5">
                                    {{ \Carbon\Carbon::parse($item->jam_mulai)->format('H:i') }} 
                                    @if($item->jam_selesai) - {{ \Carbon\Carbon::parse($item->jam_selesai)->format('H:i') }} @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 font-medium text-neutral-900 dark:text-white">{{ $item->ruangOk->nm_ruang_ok ?? $item->kd_ruang_ok }}</td>
                            <td class="px-6 py-4 font-medium text-neutral-900 dark:text-white">{{ $item->dokter->nm_dokter ?? $item->kd_dokter }}</td>
                            <td class="px-6 py-4 text-neutral-600 dark:text-neutral-400">{{ $item->paketOperasi->nm_perawatan ?? $item->kode_paket }}</td>
                            <td class="px-6 py-4">
                                @php
                                    $statusColor = match($item->status) {
                                        'Menunggu' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
                                        'Proses Operasi' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
                                        'Selesai' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
                                        default => 'bg-neutral-100 text-neutral-800 dark:bg-neutral-800 dark:text-neutral-400'
                                    };
                                @endphp
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $statusColor }}">
                                    {{ $item->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <flux:button variant="ghost" size="sm" icon="pencil-square" 
                                        @click="$wire.prepareEdit('{{ $item->kode_paket }}', '{{ $item->tanggal }}', '{{ $item->jam_mulai }}').then((success) => { if(success) showModal = true })"
                                        class="text-[#4C5C2D] hover:bg-[#4C5C2D]/10" />
                                    <div x-data="{ showConfirm: false }" class="relative">
                                        <flux:button variant="ghost" size="sm" icon="trash" @click="showConfirm = true"
                                            class="text-red-500 hover:bg-red-50" />
                                        <div x-show="showConfirm" x-cloak @click.away="showConfirm = false"
                                            class="absolute right-0 bottom-full mb-2 w-64 bg-white dark:bg-neutral-800 rounded-lg shadow-xl border border-neutral-200 dark:border-neutral-700 p-4 z-50">
                                            <p class="text-sm text-neutral-600 dark:text-neutral-300 mb-3 text-left whitespace-normal">Apakah Anda yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan.</p>
                                            <div class="flex gap-2 justify-end">
                                                <flux:button size="sm" variant="ghost" @click="showConfirm = false">Batal</flux:button>
                                                <flux:button size="sm" variant="danger" wire:click="delete('{{ $item->kode_paket }}', '{{ $item->tanggal }}', '{{ $item->jam_mulai }}')" @click="showConfirm = false">Hapus</flux:button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-neutral-500">
                                <div class="flex flex-col items-center justify-center">
                                    <flux:icon name="calendar-days" class="h-12 w-12 text-neutral-300 mb-3" />
                                    <p class="font-medium">Belum ada Jadwal Operasi</p>
                                    <p class="text-xs mt-1">Klik "Buat Baru" untuk menambahkan data.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- RIGHT SLIDE-IN PANEL (FORM) --}}
    <div x-show="showModal" x-cloak class="relative z-[90]" role="dialog" aria-modal="true">
        <div x-show="showModal"
             x-transition:enter="ease-in-out duration-500" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in-out duration-500" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="showModal = false"></div>

        <div class="fixed inset-0 overflow-hidden">
            <div class="absolute inset-0 overflow-hidden">
                <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10">
                    <div x-show="showModal"
                         x-transition:enter="transform transition ease-in-out duration-500 sm:duration-700"
                         x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
                         x-transition:leave="transform transition ease-in-out duration-500 sm:duration-700"
                         x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full"
                         class="pointer-events-auto relative w-screen max-w-2xl">

                        <div x-show="showModal"
                             x-transition:enter="ease-in-out duration-500" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                             x-transition:leave="ease-in-out duration-500" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                             class="absolute left-0 top-0 -ml-8 flex pr-2 pt-4 sm:-ml-10 sm:pr-4">
                            <button type="button" @click="showModal = false" class="relative rounded-md text-gray-300 hover:text-white">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <div class="flex h-full flex-col bg-white dark:bg-neutral-900 shadow-2xl">
                            {{-- Header --}}
                            <div class="px-6 py-5 border-b border-neutral-200 dark:border-neutral-800 bg-[#F1F5E9] dark:bg-neutral-800">
                                <h2 class="text-xl font-bold text-[#4C5C2D] dark:text-[#8CC7C4]">
                                    {{ $isEdit ? 'Ubah Jadwal Operasi' : 'Buat Jadwal Operasi' }}
                                </h2>
                                <p class="text-xs text-neutral-500 mt-1">Formulir booking jadwal operasi pasien</p>
                            </div>

                            {{-- Form Body --}}
                            <div class="relative flex-1 px-6 py-6 overflow-y-auto">
                                @if ($errors->any())
                                <div class="mb-6 p-4 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800">
                                    <div class="flex items-center gap-3 mb-2">
                                        <flux:icon name="exclamation-circle" class="w-5 h-5 text-red-500" />
                                        <h3 class="font-bold text-red-800 dark:text-red-400 text-sm">Terdapat kesalahan pengisian:</h3>
                                    </div>
                                    <ul class="list-disc list-inside text-xs text-red-700 dark:text-red-400 space-y-1 ml-8">
                                        @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                                    </ul>
                                </div>
                                @endif

                                <div class="space-y-7">
                                    {{-- Section: Waktu & Status --}}
                                    <div>
                                        <h3 class="text-xs font-bold text-neutral-800 dark:text-neutral-200 uppercase tracking-widest mb-4 pb-2 border-b border-neutral-200 dark:border-neutral-800 flex items-center gap-2">
                                            <flux:icon name="clock" class="w-4 h-4 text-neutral-400" /> Waktu & Status
                                        </h3>
                                        <div class="grid grid-cols-1 gap-5">
                                            <div class="grid grid-cols-3 gap-3">
                                                <flux:input type="date" label="Tanggal *" wire:model="form.tanggal" />
                                                <flux:input type="time" step="1" label="Mulai *" wire:model="form.jam_mulai" />
                                                <flux:input type="time" step="1" label="s.d. (Selesai)" wire:model="form.jam_selesai" />
                                            </div>
                                            <div class="w-1/3">
                                                <flux:select label="Status *" wire:model="form.status">
                                                    <option value="Menunggu">Menunggu</option>
                                                    <option value="Proses Operasi">Proses Operasi</option>
                                                    <option value="Selesai">Selesai</option>
                                                </flux:select>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Section: Ruang & Tenaga Medis --}}
                                    <div>
                                        <h3 class="text-xs font-bold text-neutral-800 dark:text-neutral-200 uppercase tracking-widest mb-4 pb-2 border-b border-neutral-200 dark:border-neutral-800 flex items-center gap-2">
                                            <flux:icon name="users" class="w-4 h-4 text-neutral-400" /> Operasi & Ruangan
                                        </h3>
                                        <div class="space-y-4">
                                            {{-- Ruang OK Lookup --}}
                                            <div class="space-y-3">
                                                <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">Ruang OK <span class="text-red-500">*</span></label>
                                                <div class="flex gap-2">
                                                    <flux:input wire:model="form.kd_ruang_ok" readonly placeholder="Kode" class="w-1/4 bg-neutral-50" />
                                                    <div class="relative w-full">
                                                        <flux:input wire:model="form.nm_ruang_ok" readonly placeholder="Pilih Ruang OK" class="w-full bg-neutral-50" />
                                                        <flux:button variant="primary" size="sm" icon="magnifying-glass" class="absolute right-1.5 top-1.5 px-2" @click="showRuangModal = true" />
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Operator Lookup --}}
                                            <div class="space-y-3">
                                                <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">Operator (Dokter) <span class="text-red-500">*</span></label>
                                                <div class="flex gap-2">
                                                    <flux:input wire:model="form.kd_dokter" readonly placeholder="Kode" class="w-1/4 bg-neutral-50" />
                                                    <div class="relative w-full">
                                                        <flux:input wire:model="form.nm_dokter" readonly placeholder="Pilih Operator" class="w-full bg-neutral-50" />
                                                        <flux:button variant="primary" size="sm" icon="magnifying-glass" class="absolute right-1.5 top-1.5 px-2" @click="showDokterModal = true" />
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Operasi (Paket) Lookup --}}
                                            <div class="space-y-3">
                                                <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">Operasi (Paket) <span class="text-red-500">*</span></label>
                                                <div class="flex gap-2">
                                                    <flux:input wire:model="form.kode_paket" readonly placeholder="Kode" class="w-1/4 bg-neutral-50" />
                                                    <div class="relative w-full">
                                                        <flux:input wire:model="form.nm_perawatan" readonly placeholder="Pilih Operasi" class="w-full bg-neutral-50" />
                                                        <flux:button variant="primary" size="sm" icon="magnifying-glass" class="absolute right-1.5 top-1.5 px-2" @click="showPaketModal = true" />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Footer --}}
                            <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-neutral-200 dark:border-neutral-800 bg-neutral-50 dark:bg-neutral-800/50">
                                <flux:button variant="ghost" @click="showModal = false">Batal</flux:button>
                                <flux:button variant="primary" wire:click="save" icon="check-circle"
                                    class="!bg-[#4C5C2D] hover:!bg-[#3f4d25] !text-white !border-none">
                                    Simpan Jadwal
                                </flux:button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL LOOKUP RUANG OK --}}
    <div x-show="showRuangModal" x-cloak class="relative z-[100]" role="dialog" aria-modal="true">
        <div x-show="showRuangModal" x-transition.opacity class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="showRuangModal = false"></div>
        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4">
                <div x-show="showRuangModal" x-transition class="relative w-full max-w-2xl rounded-2xl bg-white dark:bg-neutral-900 shadow-2xl p-6">
                    <h3 class="text-lg font-bold mb-4">Pilih Ruang OK</h3>
                    <flux:input wire:model.live.debounce.300ms="searchRuang" icon="magnifying-glass" placeholder="Cari kode atau nama ruang..." class="mb-4" />
                    <div class="max-h-96 overflow-y-auto border border-neutral-200 dark:border-neutral-800 rounded-lg">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-neutral-50 dark:bg-neutral-800 sticky top-0">
                                <tr>
                                    <th class="px-4 py-2">Kode</th>
                                    <th class="px-4 py-2">Nama Ruang</th>
                                    <th class="px-4 py-2"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($this->ruangs as $ruang)
                                <tr class="border-t border-neutral-200 dark:border-neutral-800 hover:bg-neutral-50 dark:hover:bg-neutral-800/50">
                                    <td class="px-4 py-2">{{ $ruang->kd_ruang_ok }}</td>
                                    <td class="px-4 py-2">{{ $ruang->nm_ruang_ok }}</td>
                                    <td class="px-4 py-2 text-right">
                                        <flux:button size="sm" variant="primary" wire:click="selectRuangOk('{{ $ruang->kd_ruang_ok }}', '{{ $ruang->nm_ruang_ok }}')">Pilih</flux:button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4 flex justify-end">
                        <flux:button variant="ghost" @click="showRuangModal = false">Tutup</flux:button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL LOOKUP OPERATOR --}}
    <div x-show="showDokterModal" x-cloak class="relative z-[100]" role="dialog" aria-modal="true">
        <div x-show="showDokterModal" x-transition.opacity class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="showDokterModal = false"></div>
        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4">
                <div x-show="showDokterModal" x-transition class="relative w-full max-w-2xl rounded-2xl bg-white dark:bg-neutral-900 shadow-2xl p-6">
                    <h3 class="text-lg font-bold mb-4">Pilih Operator (Dokter)</h3>
                    <flux:input wire:model.live.debounce.300ms="searchDokter" icon="magnifying-glass" placeholder="Cari kode atau nama dokter..." class="mb-4" />
                    <div class="max-h-96 overflow-y-auto border border-neutral-200 dark:border-neutral-800 rounded-lg">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-neutral-50 dark:bg-neutral-800 sticky top-0">
                                <tr>
                                    <th class="px-4 py-2">Kode</th>
                                    <th class="px-4 py-2">Nama Dokter</th>
                                    <th class="px-4 py-2"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($this->dokters as $dok)
                                <tr class="border-t border-neutral-200 dark:border-neutral-800 hover:bg-neutral-50 dark:hover:bg-neutral-800/50">
                                    <td class="px-4 py-2">{{ $dok->kd_dokter }}</td>
                                    <td class="px-4 py-2">{{ $dok->nm_dokter }}</td>
                                    <td class="px-4 py-2 text-right">
                                        <flux:button size="sm" variant="primary" wire:click="selectDokter('{{ $dok->kd_dokter }}', '{{ $dok->nm_dokter }}')">Pilih</flux:button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4 flex justify-end">
                        <flux:button variant="ghost" @click="showDokterModal = false">Tutup</flux:button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL LOOKUP OPERASI (PAKET) --}}
    <div x-show="showPaketModal" x-cloak class="relative z-[100]" role="dialog" aria-modal="true">
        <div x-show="showPaketModal" x-transition.opacity class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="showPaketModal = false"></div>
        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4">
                <div x-show="showPaketModal" x-transition class="relative w-full max-w-2xl rounded-2xl bg-white dark:bg-neutral-900 shadow-2xl p-6">
                    <h3 class="text-lg font-bold mb-4">Pilih Operasi (Paket)</h3>
                    <flux:input wire:model.live.debounce.300ms="searchPaket" icon="magnifying-glass" placeholder="Cari kode atau nama paket operasi..." class="mb-4" />
                    <div class="max-h-96 overflow-y-auto border border-neutral-200 dark:border-neutral-800 rounded-lg">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-neutral-50 dark:bg-neutral-800 sticky top-0">
                                <tr>
                                    <th class="px-4 py-2">Kode</th>
                                    <th class="px-4 py-2">Nama Operasi</th>
                                    <th class="px-4 py-2"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($this->pakets as $paket)
                                <tr class="border-t border-neutral-200 dark:border-neutral-800 hover:bg-neutral-50 dark:hover:bg-neutral-800/50">
                                    <td class="px-4 py-2">{{ $paket->kode_paket }}</td>
                                    <td class="px-4 py-2">{{ $paket->nm_perawatan }}</td>
                                    <td class="px-4 py-2 text-right">
                                        <flux:button size="sm" variant="primary" wire:click="selectPaket('{{ $paket->kode_paket }}', '{{ $paket->nm_perawatan }}')">Pilih</flux:button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4 flex justify-end">
                        <flux:button variant="ghost" @click="showPaketModal = false">Tutup</flux:button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
