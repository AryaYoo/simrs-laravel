<x-layouts::app :title="__('Master Data')">
    @php
    $masterData = [
        ['title' => 'Dokter',          'icon' => 'user',            'route' => route('master-data.dokter.index')],
        ['title' => 'Poliklinik',       'icon' => 'puzzle-piece',    'route' => route('master-data.poliklinik.index')],
        ['title' => 'Penjamin',        'icon' => 'shield-check',    'route' => route('master-data.penjamin.index')],
        ['title' => 'Kabupaten',       'icon' => 'building-office',  'route' => route('master-data.kabupaten.index')],
        ['title' => 'Kecamatan',       'icon' => 'building-office-2','route' => route('master-data.kecamatan.index')],
        ['title' => 'Kelurahan',       'icon' => 'map-pin',         'route' => route('master-data.kelurahan.index')],
        ['title' => 'Provinsi',        'icon' => 'map',             'route' => route('master-data.provinsi.index')],
        ['title' => 'Suku Bangsa',     'icon' => 'globe-alt',       'route' => route('master-data.suku-bangsa.index')],
        ['title' => 'Bahasa Pasien',   'icon' => 'language',        'route' => route('master-data.bahasa-pasien.index')],
        ['title' => 'Cacat Fisik',     'icon' => 'heart',           'route' => route('master-data.cacat-fisik.index')],
        ['title' => 'Perusahaan',      'icon' => 'briefcase',       'route' => route('master-data.perusahaan-pasien.index')],
        ['title' => 'Perujuk',         'icon' => 'users',           'route' => route('master-data.perujuk.index')],
    ];
    @endphp

    <div class="flex flex-col w-full h-full pb-8">

        {{-- Header / Breadcrumb --}}
        <div class="flex items-center gap-3 mb-6">
            <div>
                <nav class="text-xs text-neutral-400 mb-0.5">
                    <span>Master Data</span>
                </nav>
                <h1 class="text-xl font-bold text-neutral-800 dark:text-neutral-100">Daftar Master Data</h1>
            </div>
        </div>

        {{-- Search bar --}}
        <div class="relative mb-4">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <flux:icon name="magnifying-glass" class="w-4 h-4 text-neutral-400" />
            </div>
            <input
                type="text"
                id="masterdata-search"
                class="block w-full pl-9 pr-3 py-2.5 text-sm rounded-lg border border-neutral-200 dark:border-neutral-700
                       bg-white dark:bg-neutral-800 text-neutral-900 dark:text-neutral-100
                       placeholder-neutral-400 focus:outline-none focus:ring-2 focus:ring-[#6A7E3F]/30 focus:border-[#6A7E3F] transition-colors"
                placeholder="Pencarian master data..."
            />
        </div>

        {{-- Grid --}}
        <div id="masterdata-grid" class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-3">
            @foreach($masterData as $item)
            <a href="{{ $item['route'] ?? '#' }}"
               @if(isset($item['route']) && $item['route'] !== '#') wire:navigate @endif
               data-title="{{ strtolower($item['title']) }}"
               class="masterdata-card group flex flex-col items-center justify-center gap-2 p-4
                      rounded-xl border border-neutral-200 dark:border-neutral-700
                      bg-white dark:bg-neutral-800
                      hover:border-[#6A7E3F] hover:bg-[#6A7E3F]/5
                      dark:hover:border-[#6A7E3F] dark:hover:bg-[#6A7E3F]/10
                      transition-all duration-150 cursor-pointer"
            >
                <div class="masterdata-icon-bg w-11 h-11 rounded-lg flex items-center justify-center
                            bg-neutral-100 dark:bg-neutral-700
                            transition-colors">
                    <flux:icon name="{{ $item['icon'] }}"
                               class="masterdata-icon w-5 h-5 text-neutral-500 dark:text-neutral-300 group-hover:text-[#4C5C2D] dark:group-hover:text-[#8CC7C4]"
                               variant="outline" />
                </div>
                <span class="masterdata-title text-xs font-medium text-center leading-tight text-neutral-600 dark:text-neutral-300 group-hover:text-[#4C5C2D] dark:group-hover:text-[#8CC7C4]">
                    {{ $item['title'] }}
                </span>
            </a>
            @endforeach
        </div>

        {{-- Empty state --}}
        <div id="masterdata-empty" class="hidden mt-8 text-center text-sm text-neutral-400">
            Master data tidak ditemukan.
        </div>
    </div>

    @push('scripts')
    <script>
        document.getElementById('masterdata-search').addEventListener('input', function () {
            const q = this.value.toLowerCase();
            const cards = document.querySelectorAll('.masterdata-card');
            let found = 0;
            cards.forEach(card => {
                const match = card.dataset.title.includes(q);
                card.classList.toggle('hidden', !match);
                if (match) found++;
            });
            document.getElementById('masterdata-empty').classList.toggle('hidden', found > 0);
        });
    </script>
    @endpush
</x-layouts::app>
