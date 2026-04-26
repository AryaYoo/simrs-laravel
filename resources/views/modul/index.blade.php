<x-layouts::app :title="__('Modul')">
    @php
    $mainModules = [
        ['title' => 'Registrasi','icon' => 'users', 'route' => route('modul.registrasi-pasien.index')],
        ['title' => 'Pasien','icon' => 'identification', 'route' => route('modul.pasien.index')],
        ['title' => 'Rawat Inap','icon' => 'home', 'route' => route('modul.rawat-inap.index')],
        ['title' => 'Rawat Jalan','icon' => 'calendar-days', 'route' => route('modul.rawat-jalan.index')],
    ];
    $casemixModules = [
        ['title' => 'Casemix Rawat Jalan', 'icon' => 'clipboard-document-check', 'route' => route('modul.casemix-rawat-jalan.index')],
        ['title' => 'Casemix Rawat Inap', 'icon' => 'clipboard-document-list', 'route' => route('modul.casemix-rawat-inap.index')]
    ];
    @endphp
    @endphp

    <div class="flex flex-col w-full h-full pb-8" 
         x-data="{ 
             searchQuery: '',
             modules: @js(collect(array_merge($mainModules, $casemixModules))->pluck('title')->map(fn($t) => strtolower($t))),
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
                    <span>Modul</span>
                </nav>
                <h1 class="text-xl font-bold text-neutral-800 dark:text-neutral-100">Daftar Modul</h1>
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
                placeholder="Pencarian modul..."
            />
        </div>

        {{-- Section: Modul Utama --}}
        <div class="mb-8 module-section">
            <h2 class="text-sm font-bold text-neutral-700 dark:text-neutral-300 mb-3 border-b border-neutral-200 dark:border-neutral-700 pb-2">Modul Utama</h2>
            <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-3">
                @foreach($mainModules as $module)
                <a href="{{ $module['route'] ?? '#' }}"
                   @if(isset($module['route']) && $module['route'] !== '#') wire:navigate @endif
                   x-show="searchQuery === '' || '{{ strtolower($module['title']) }}'.includes(searchQuery.toLowerCase())"
                   class="module-card group flex flex-col items-center justify-center gap-2 p-4
                          rounded-xl border border-neutral-200 dark:border-neutral-700
                          bg-white dark:bg-neutral-800
                          transition-all duration-150 cursor-pointer"
                >
                    <div class="module-icon-bg w-11 h-11 rounded-lg flex items-center justify-center
                                bg-neutral-100 dark:bg-neutral-700
                                transition-colors">
                        <flux:icon name="{{ $module['icon'] }}"
                                   class="module-icon w-5 h-5 text-neutral-500 dark:text-neutral-300"
                                   variant="outline" />
                    </div>
                    <span class="module-title text-xs font-medium text-center leading-tight text-neutral-600 dark:text-neutral-300">
                        {{ $module['title'] }}
                    </span>
                </a>
                @endforeach
            </div>
        </div>

        {{-- Section: Modul Casemix --}}
        <div class="mb-4 module-section">
            <h2 class="text-sm font-bold text-neutral-700 dark:text-neutral-300 mb-3 border-b border-neutral-200 dark:border-neutral-700 pb-2">Modul Casemix</h2>
            <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-3">
                @foreach($casemixModules as $module)
                <a href="{{ $module['route'] ?? '#' }}"
                   @if(isset($module['route']) && $module['route'] !== '#') wire:navigate @endif
                   x-show="searchQuery === '' || '{{ strtolower($module['title']) }}'.includes(searchQuery.toLowerCase())"
                   class="module-card group flex flex-col items-center justify-center gap-2 p-4
                          rounded-xl border border-neutral-200 dark:border-neutral-700
                          bg-white dark:bg-neutral-800
                          transition-all duration-150 cursor-pointer"
                >
                    <div class="module-icon-bg w-11 h-11 rounded-lg flex items-center justify-center
                                bg-neutral-100 dark:bg-neutral-700
                                transition-colors">
                        <flux:icon name="{{ $module['icon'] }}"
                                   class="module-icon w-5 h-5 text-neutral-500 dark:text-neutral-300"
                                   variant="outline" />
                    </div>
                    <span class="module-title text-xs font-medium text-center leading-tight text-neutral-600 dark:text-neutral-300">
                        {{ $module['title'] }}
                    </span>
                </a>
                @endforeach
            </div>
        </div>

        {{-- Empty state --}}
        <div x-show="!hasResults" x-cloak class="mt-8 text-center text-sm text-neutral-400">
            Modul tidak ditemukan.
        </div>
    </div>

</x-layouts::app>