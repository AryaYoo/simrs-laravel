<x-layouts::app :title="__('Semua Modul Farmasi')">
    @php
    $farmasiModules = [
        ['title' => 'Penjualan Obat & BHP', 'icon' => 'shopping-bag', 'route' => route('modul.farmasi.input-penjualan')],
        ['title' => 'Daftar Resep Dokter', 'icon' => 'clipboard-document-list', 'route' => route('modul.farmasi.daftar-resep-dokter')],
        ['title' => 'Permintaan Resep Pulang', 'icon' => 'document-arrow-down', 'route' => route('modul.farmasi.permintaan-resep-pulang')],
        ['title' => 'Resep Pulang', 'icon' => 'arrow-right-start-on-rectangle', 'route' => route('modul.farmasi.resep-pulang')],
        ['title' => 'Obat, Alkes & BHP', 'icon' => 'beaker', 'route' => route('modul.farmasi.data-barang')],
        ['title' => 'No. Resep', 'icon' => 'hashtag', 'route' => route('modul.farmasi.no-resep')],
        ['title' => 'Stock Opname', 'icon' => 'archive-box', 'route' => route('modul.farmasi.stock-opname')],
    ];

    if (auth()->user()?->role === 'admin') {
        $farmasiModules[] = ['title' => 'Data Penyerahan Darah', 'icon' => 'heart', 'route' => route('modul.farmasi.penyerahan-darah')];
    }
    @endphp

    <div class="flex flex-col w-full h-full pb-8" 
         x-data="{ 
             searchQuery: '',
             modules: @js(collect($farmasiModules)->pluck('title')->map(fn($t) => strtolower($t))),
             get hasResults() {
                 if (this.searchQuery === '') return true;
                 const q = this.searchQuery.toLowerCase();
                 return this.modules.some(m => m.includes(q));
             }
         }">

        {{-- Header / Breadcrumb --}}
        <div class="flex items-center gap-3 mb-6">
            <div>
                <nav class="text-xs text-neutral-400 mb-0.5">
                    <a href="{{ route('modul.index') }}" wire:navigate class="hover:text-neutral-600 dark:hover:text-neutral-200 transition-colors">Modul</a>
                    <span class="mx-1">/</span>
                    <span>Farmasi</span>
                </nav>
                <h1 class="text-xl font-bold text-neutral-800 dark:text-neutral-100">Semua Modul Farmasi</h1>
            </div>
        </div>

        {{-- Search bar --}}
        <div class="relative mb-6">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <flux:icon name="magnifying-glass" class="w-4 h-4 text-neutral-400" />
            </div>
            <input
                type="text"
                x-model="searchQuery"
                class="block w-full pl-9 pr-3 py-2.5 text-sm rounded-lg border border-neutral-200 dark:border-neutral-700
                       bg-white dark:bg-neutral-800 text-neutral-900 dark:text-neutral-100
                       placeholder-neutral-400 focus:outline-none focus:ring-2 focus:ring-[#6A7E3F]/30 focus:border-[#6A7E3F] transition-colors"
                placeholder="Pencarian modul farmasi..."
            />
        </div>

        {{-- Section: Modul Farmasi --}}
        <div class="mb-8 module-section">
            <h2 class="text-sm font-bold text-neutral-700 dark:text-neutral-300 mb-3 border-b border-neutral-200 dark:border-neutral-700 pb-2">Menu Farmasi</h2>
            <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-3">
                @foreach($farmasiModules as $module)
                <a href="{{ $module['route'] ?? '#' }}"
                   @if(isset($module['route']) && $module['route'] !== '#') wire:navigate @endif
                   x-show="searchQuery === '' || '{{ strtolower($module['title']) }}'.includes(searchQuery.toLowerCase())"
                   class="module-card group flex flex-col items-center justify-center gap-2 p-4
                          rounded-xl border border-neutral-200 dark:border-neutral-700
                          bg-white dark:bg-neutral-800
                          hover:border-[#6A7E3F] hover:bg-[#6A7E3F]/5
                          dark:hover:border-[#6A7E3F] dark:hover:bg-[#6A7E3F]/10
                          transition-all duration-150 cursor-pointer"
                >
                    <div class="module-icon-bg w-11 h-11 rounded-lg flex items-center justify-center
                                bg-neutral-100 dark:bg-neutral-700
                                group-hover:bg-[#6A7E3F]/10 dark:group-hover:bg-[#6A7E3F]/20
                                transition-colors">
                        <flux:icon name="{{ $module['icon'] }}"
                                   class="module-icon w-5 h-5 text-neutral-500 dark:text-neutral-300 group-hover:text-[#4C5C2D] dark:group-hover:text-[#8CC7C4]"
                                   variant="outline" />
                    </div>
                    <span class="module-title text-xs font-medium text-center leading-tight text-neutral-600 dark:text-neutral-300 group-hover:text-[#4C5C2D] dark:group-hover:text-[#8CC7C4]">
                        {{ $module['title'] }}
                    </span>
                </a>
                @endforeach
            </div>
        </div>

        {{-- Empty state --}}
        <div x-show="!hasResults" x-cloak class="mt-8 text-center text-sm text-neutral-400">
            Modul farmasi tidak ditemukan.
        </div>
    </div>

</x-layouts::app>
