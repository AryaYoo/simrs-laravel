<x-layouts::app :title="__('Modul')">
    @php
    $modules = [
        ['title' => 'Registrasi','icon' => 'users', 'route' => route('modul.registrasi-pasien.index')],
        ['title' => 'Pasien','icon' => 'identification', 'route' => route('modul.pasien.index')],
        ['title' => 'Rawat Inap','icon' => 'home', 'route' => route('modul.rawat-inap.index')],
        ['title' => 'Rawat Jalan','icon' => 'calendar-days', 'route' => route('modul.rawat-jalan.index')]
    ];
    @endphp

    <div class="flex flex-col w-full h-full pb-8">

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
        <div class="relative mb-4">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <flux:icon name="magnifying-glass" class="w-4 h-4 text-neutral-400" />
            </div>
            <input
                type="text"
                id="module-search"
                class="block w-full pl-9 pr-3 py-2.5 text-sm rounded-lg border border-neutral-200 dark:border-neutral-700
                       bg-white dark:bg-neutral-800 text-neutral-900 dark:text-neutral-100
                       placeholder-neutral-400 focus:outline-none focus:ring-2 focus:ring-[#6A7E3F]/30 focus:border-[#6A7E3F] transition-colors"
                placeholder="Pencarian modul..."
            />
        </div>

        {{-- Grid --}}
        <div id="module-grid" class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-3">
            @foreach($modules as $module)
            <a href="{{ $module['route'] ?? '#' }}"
               @if(isset($module['route']) && $module['route'] !== '#') wire:navigate @endif
               data-title="{{ strtolower($module['title']) }}"
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

        {{-- Empty state --}}
        <div id="module-empty" class="hidden mt-8 text-center text-sm text-neutral-400">
            Modul tidak ditemukan.
        </div>
    </div>

    @push('scripts')
    <script>
        document.getElementById('module-search').addEventListener('input', function () {
            const q = this.value.toLowerCase();
            const cards = document.querySelectorAll('.module-card');
            let found = 0;
            cards.forEach(card => {
                const match = card.dataset.title.includes(q);
                card.classList.toggle('hidden', !match);
                if (match) found++;
            });
            document.getElementById('module-empty').classList.toggle('hidden', found > 0);
        });
    </script>
    @endpush
</x-layouts::app>