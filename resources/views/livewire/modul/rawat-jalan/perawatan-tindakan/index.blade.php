<div class="flex flex-col gap-6 pb-8" x-data="{
        detailModalOpen: false,
        detail: {},
        showDetailModal(data) {
            this.detail = data;
            this.detailModalOpen = true;
        },
        closeDetailModal() {
            this.detailModalOpen = false;
        }
    }">
    {{-- Header / Breadcrumb --}}
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('modul.rawat-jalan.index') }}" wire:navigate
               class="flex items-center justify-center w-8 h-8 rounded-lg transition-colors hover:bg-neutral-100 dark:hover:bg-neutral-700">
                <flux:icon name="chevron-left" class="w-5 h-5 text-neutral-500" />
            </a>
            <div>
                <nav class="text-xs text-neutral-400 mb-0.5">
                    <a href="{{ route('modul.index') }}" wire:navigate class="hover:underline">Modul</a>
                    <span class="mx-1">/</span>
                    <a href="{{ route('modul.rawat-jalan.index') }}" wire:navigate class="hover:underline">Rawat Jalan</a>
                    <span class="mx-1">/</span>
                    <span>Perawatan/Tindakan</span>
                </nav>
                <h1 class="text-xl font-bold text-neutral-800 dark:text-neutral-100">Perawatan & Tindakan Rawat Jalan</h1>
                <div class="mt-1.5 flex items-center gap-2 text-sm">
                    <span class="text-neutral-500">No. Rawat:</span>
                    <span class="font-bold text-[#4C5C2D] dark:text-[#8CC7C4] font-mono bg-[#4C5C2D]/10 dark:bg-[#4C5C2D]/30 px-2 py-0.5 rounded">{{ $no_rawat }}</span>
                    <span class="text-neutral-300 mx-1">|</span>
                    <span class="text-neutral-500">Pasien:</span>
                    <span class="font-bold text-neutral-800 dark:text-neutral-100 bg-neutral-100 dark:bg-neutral-800 px-2 py-0.5 rounded">{{ $regPeriksa->pasien->nm_pasien ?? '-' }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-neutral-800 rounded-xl border border-neutral-200 dark:border-neutral-700 p-6 shadow-sm">
        {{-- Custom Tabs Grid --}}
        <div class="inline-flex flex-wrap items-center gap-2 p-1 bg-neutral-100 dark:bg-neutral-900 rounded-xl mb-6">
            @php
                $tabs = [
                    ['id' => 'penanganan_dokter', 'label' => 'Penanganan Dokter', 'icon' => 'user-plus'],
                    ['id' => 'penanganan_petugas', 'label' => 'Penanganan Petugas', 'icon' => 'users'],
                    ['id' => 'penanganan_dokter_petugas', 'label' => 'Penanganan Dokter & Petugas', 'icon' => 'user-group'],
                    ['id' => 'pemeriksaan', 'label' => 'Pemeriksaan', 'icon' => 'clipboard-document-check'],
                    ['id' => 'pemeriksaan_obstetri', 'label' => 'Pemeriksaan Obstetri', 'icon' => 'heart'],
                    ['id' => 'pemeriksaan_ginekologi', 'label' => 'Pemeriksaan Ginekologi', 'icon' => 'lifebuoy'],
                    ['id' => 'diagnosa', 'label' => 'Diagnosa', 'icon' => 'tag'],
                    ['id' => 'catatan_dokter', 'label' => 'Catatan Dokter', 'icon' => 'document-text'],
                ];
            @endphp

            @foreach($tabs as $tab)
                <button wire:click="$set('activeTab', '{{ $tab['id'] }}')" wire:loading.attr="disabled"
                    class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-medium transition-all relative cursor-pointer {{ $activeTab === $tab['id'] ? 'bg-white dark:bg-neutral-800 text-neutral-800 dark:text-neutral-100 shadow-sm' : 'text-neutral-500 hover:text-neutral-700 dark:hover:text-neutral-300 hover:bg-neutral-200/50 dark:hover:bg-neutral-700/50' }}">
                    <flux:icon wire:loading.remove.delay :name="$tab['icon']" class="w-4 h-4" />
                    <flux:icon wire:loading.delay name="arrow-path" class="w-4 h-4 animate-spin" />
                    {{ $tab['label'] }}
                </button>
            @endforeach
        </div>

        {{-- Tab Panels --}}
        <div class="mt-4">
            @if($activeTab === 'penanganan_dokter')
                @include('livewire.modul.rawat-jalan.perawatan-tindakan.penanganan-dokter')
            @elseif($activeTab === 'penanganan_petugas')
                @include('livewire.modul.rawat-jalan.perawatan-tindakan.penanganan-petugas')
            @elseif($activeTab === 'penanganan_dokter_petugas')
                @include('livewire.modul.rawat-jalan.perawatan-tindakan.penanganan-dokter-dan-petugas')
            @elseif($activeTab === 'pemeriksaan')
                @include('livewire.modul.rawat-jalan.perawatan-tindakan.pemeriksaan')
            @elseif($activeTab === 'pemeriksaan_obstetri')
                @include('livewire.modul.rawat-jalan.perawatan-tindakan.pemeriksaan-obstetri')
            @elseif($activeTab === 'pemeriksaan_ginekologi')
                @include('livewire.modul.rawat-jalan.perawatan-tindakan.pemeriksaan-ginekologi')
            @elseif($activeTab === 'diagnosa')
                @include('livewire.modul.rawat-jalan.perawatan-tindakan.diagnosa')
            @elseif($activeTab === 'catatan_dokter')
                @include('livewire.modul.rawat-jalan.perawatan-tindakan.catatan-dokter')
            @endif
        </div>
    </div>
</div>
