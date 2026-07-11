<div class="flex flex-col gap-6 pb-24">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('modul.rawat-inap.kelahiran-bayi') }}" wire:navigate class="flex items-center justify-center w-10 h-8 rounded-md bg-[#4C5C2D] transition-colors hover:bg-[#3d4b24] shadow-sm">
                <flux:icon name="chevron-left" class="w-5 h-5 text-white" />
            </a>
            <div>
                <nav class="text-xs text-neutral-400 mb-0.5">
                    <a href="{{ route('modul.index') }}" wire:navigate class="hover:underline">Modul</a>
                    <span class="mx-1">/</span>
                    <a href="{{ route('modul.rawat-inap.index') }}" wire:navigate class="hover:underline">Rawat Inap</a>
                    <span class="mx-1">/</span>
                    <a href="{{ route('modul.rawat-inap.kelahiran-bayi') }}" wire:navigate class="hover:underline">Kelahiran Bayi</a>
                    <span class="mx-1">/</span>
                    <span>Pengaturan SKL</span>
                </nav>
                <h1 class="text-xl font-bold text-neutral-800 dark:text-neutral-100">Pengaturan SKL</h1>
            </div>
        </div>
    </div>

    {{-- Main Card --}}
    <div class="max-w-2xl bg-white dark:bg-neutral-800 rounded-xl border border-neutral-200 dark:border-neutral-700 p-6 shadow-sm">
        <h2 class="text-sm font-bold text-neutral-700 dark:text-neutral-300 uppercase tracking-wider mb-4">Nomor Terakhir SKL</h2>
        
        <form wire:submit.prevent="save" class="space-y-6">
            <div class="space-y-2">
                <flux:input label="Nomor Urut Terakhir" wire:model="last_skl_number" placeholder="Contoh: 1778" type="number" min="0" />
                <p class="text-xs text-neutral-400">Nomor ini adalah angka urutan terakhir yang berhasil digunakan. Pendaftaran bayi berikutnya akan menggunakan nomor ini ditambah 1.</p>
            </div>

            {{-- Live Preview --}}
            <div class="p-4 rounded-lg bg-neutral-50 dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-700">
                <p class="text-xs font-semibold text-neutral-500 uppercase tracking-wider mb-1">Pratinjau Nomor SKL Berikutnya:</p>
                <p class="text-lg font-bold text-[#4C5C2D] font-mono select-all">{{ $previewNoSkl }}</p>
            </div>

            <div class="flex items-center justify-end gap-2 pt-4 border-t border-neutral-100 dark:border-neutral-700">
                <flux:button href="{{ route('modul.rawat-inap.kelahiran-bayi') }}" wire:navigate variant="ghost" class="h-9 text-sm">
                    Batal
                </flux:button>
                <flux:button type="submit" variant="primary" class="!bg-[#4C5C2D] !border-[#4C5C2D] hover:!bg-[#3D4A24] h-9 px-6 text-sm">
                    Simpan Pengaturan
                </flux:button>
            </div>
        </form>
    </div>
</div>
