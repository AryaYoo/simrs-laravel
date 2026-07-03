<div x-data="{ open: @entangle('isOpen') }"
     x-show="open"
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-[9999] flex items-center justify-center p-4 sm:p-6"
     style="display: none;"
     @keydown.escape.window="open = false">

    {{-- Backdrop --}}
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="open = false"></div>

    {{-- Panel --}}
    <div x-show="open"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95 translate-y-4"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="opacity-0 scale-95 translate-y-4"
         class="relative w-full max-w-xl bg-white dark:bg-neutral-800 rounded-2xl shadow-2xl z-10 overflow-hidden flex flex-col max-h-[80vh]">

        {{-- Header --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-neutral-100 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-900/50">
            <div class="flex items-center gap-2">
                <flux:icon name="magnifying-glass" class="w-5 h-5 text-[#4C5C2D] dark:text-[#8CC7C4]" />
                <h3 class="text-base font-bold text-neutral-800 dark:text-neutral-100">Cari Dokter DPJP</h3>
            </div>
            <button @click="open = false" class="w-8 h-8 rounded-lg flex items-center justify-center text-neutral-400 hover:text-neutral-700 dark:hover:text-neutral-200 hover:bg-neutral-100 dark:hover:bg-neutral-700 transition-colors">
                <flux:icon name="x-mark" class="w-5 h-5" />
            </button>
        </div>

        {{-- Body --}}
        <div class="p-5 flex flex-col flex-1 min-h-0">
            <div class="mb-4">
                <flux:input wire:model.live.debounce.300ms="search" placeholder="Cari nama atau kode dokter..." icon="magnifying-glass" autofocus />
            </div>

            <div class="overflow-y-auto flex-1 border border-neutral-100 dark:border-neutral-700 rounded-xl divide-y divide-neutral-100 dark:divide-neutral-700">
                @forelse ($doctors as $dr)
                    <button type="button" wire:click="select('{{ $dr->kd_dokter }}', '{{ addslashes($dr->nm_dokter) }}')"
                            class="w-full flex items-center justify-between px-4 py-3 text-left hover:bg-[#F1F5E9] dark:hover:bg-[#4C5C2D]/10 transition-colors group">
                        <div>
                            <div class="text-sm font-bold text-neutral-800 dark:text-neutral-100 group-hover:text-[#4C5C2D] dark:group-hover:text-[#8CC7C4] transition-colors">
                                {{ $dr->nm_dokter }}
                            </div>
                            <div class="text-[10px] text-neutral-400 dark:text-neutral-500 font-mono mt-0.5">
                                Kode: {{ $dr->kd_dokter }}
                            </div>
                        </div>
                        <flux:icon name="chevron-right" class="w-4 h-4 text-neutral-300 group-hover:text-[#4C5C2D] dark:group-hover:text-[#8CC7C4] transition-colors" />
                    </button>
                @empty
                    <div class="flex flex-col items-center justify-center py-12 text-neutral-400">
                        <flux:icon name="users" class="w-10 h-10 mb-2 opacity-30" />
                        <p class="text-xs">Dokter tidak ditemukan.</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Footer --}}
        <div class="px-6 py-3 border-t border-neutral-100 dark:border-neutral-700 flex justify-end bg-neutral-50 dark:bg-neutral-900/30">
            <flux:button variant="ghost" size="sm" @click="open = false">Batal</flux:button>
        </div>
    </div>
</div>
