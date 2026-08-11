<div class="flex flex-col gap-6 pb-8">
    {{-- Header / Breadcrumb --}}
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <button type="button" onclick="history.length > 1 ? history.back() : window.location.href = '{{ route('modul.index') }}'" class="flex items-center justify-center w-10 h-8 rounded-md bg-[#4C5C2D] transition-colors hover:bg-[#3d4b24] shadow-sm">
                <flux:icon name="chevron-left" class="w-5 h-5 text-white" />
            </button>
            <div>
                <nav class="text-xs text-neutral-400 mb-0.5">
                    <a href="{{ route('modul.index') }}" wire:navigate class="hover:underline">Modul</a>
                    <span class="mx-1">/</span>
                    <a href="{{ route('modul.rawat-jalan.index') }}" wire:navigate class="hover:underline">Rawat
                        Jalan</a>
                    <span class="mx-1">/</span>
                    <a href="{{ route('modul.rawat-jalan.perawatan-tindakan', str_replace('/', '-', $no_rawat)) }}"
                        wire:navigate class="hover:underline">Perawatan/Tindakan</a>
                    <span class="mx-1">/</span>
                    <span>iCare BPJS</span>
                </nav>
                <h1 class="text-xl font-bold text-neutral-800 dark:text-neutral-100">Riwayat iCare BPJS</h1>
            </div>
        </div>

        {{-- Tombol Refresh di Header --}}
        <button
            wire:click="refresh"
            wire:loading.attr="disabled"
            wire:loading.class="opacity-50 cursor-not-allowed"
            title="Refresh URL iCare — URL bersifat one-time dan bisa kadaluarsa"
            class="flex items-center gap-2 px-4 py-2 bg-white dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl text-sm font-semibold text-neutral-700 dark:text-neutral-200 hover:bg-neutral-50 dark:hover:bg-neutral-700 transition-all shadow-sm">
            <flux:icon wire:loading.remove name="arrow-path" class="w-4 h-4 text-[#4C5C2D]" />
            <flux:icon wire:loading name="arrow-path" class="w-4 h-4 text-[#4C5C2D] animate-spin" />
            <span wire:loading.remove>Refresh</span>
            <span wire:loading>Memuat...</span>
        </button>
    </div>

    {{-- Patient Card Banner --}}
    <div
        class="bg-[#4C5C2D] rounded-xl p-5 text-white shadow-sm flex flex-col md:flex-row justify-between md:items-center gap-4 relative overflow-hidden">
        {{-- Decorative pattern --}}
        <div class="absolute right-0 top-0 opacity-10 pointer-events-none">
            <svg width="200" height="200" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                <circle cx="90" cy="10" r="40" fill="currentColor" />
                <circle cx="10" cy="90" r="30" fill="currentColor" />
                <circle cx="50" cy="50" r="20" fill="currentColor" />
            </svg>
        </div>

        <div class="flex items-start gap-4 z-10">
            <div
                class="w-14 h-14 bg-white/20 rounded-full flex items-center justify-center flex-shrink-0 border-2 border-white/30 shadow-inner">
                <flux:icon name="user" class="w-7 h-7 text-white" />
            </div>
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span
                        class="bg-white/20 text-white text-[10px] font-bold px-2 py-0.5 rounded shadow-sm border border-white/10 tracking-wide">{{ $regPeriksa->no_rawat }}</span>
                    <span
                        class="bg-white/20 text-white text-[10px] font-bold px-2 py-0.5 rounded shadow-sm border border-white/10 tracking-wide">{{ $regPeriksa->no_rkm_medis }}</span>
                </div>
                <h2 class="text-lg font-black tracking-wide text-white uppercase drop-shadow-sm">
                    {{ $regPeriksa->pasien->nm_pasien }}</h2>
                <div class="flex items-center gap-4 mt-1.5 text-xs text-white/90 font-medium">
                    <div class="flex items-center gap-1.5">
                        <flux:icon name="phone" class="w-3.5 h-3.5 opacity-80" />
                        <span>{{ $regPeriksa->pasien->no_tlp ?? '-' }}</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <flux:icon name="home" class="w-3.5 h-3.5 opacity-80" />
                        <span>{{ $regPeriksa->poliklinik->nm_poli ?? '-' }}</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <flux:icon name="credit-card" class="w-3.5 h-3.5 opacity-80" />
                        <span>{{ $regPeriksa->penjab->png_jawab ?? '-' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- iCare Content --}}
    <div
        class="bg-white dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl overflow-hidden shadow-sm">

        {{-- State: Loading (saat Livewire sedang proses) --}}
        <div wire:loading class="p-12 flex flex-col items-center justify-center text-center">
            <flux:icon name="arrow-path" class="w-10 h-10 text-[#4C5C2D] animate-spin mb-4" />
            <p class="text-neutral-600 dark:text-neutral-300 font-medium">Menghubungi server iCare BPJS...</p>
            <p class="text-xs text-neutral-400 dark:text-neutral-500 mt-1">Mohon tunggu sebentar</p>
        </div>

        <div wire:loading.remove>
            @if($errorMessage)
                {{-- State: Error --}}
                <div class="p-12 flex flex-col items-center justify-center text-center">
                    <div
                        class="w-20 h-20 bg-red-50 dark:bg-red-900/20 text-red-400 rounded-full flex items-center justify-center mb-5">
                        <flux:icon name="exclamation-triangle" class="w-10 h-10" />
                    </div>
                    <h3 class="text-lg font-bold text-neutral-800 dark:text-neutral-200 mb-2">Gagal Mengambil Data iCare</h3>
                    <p class="text-neutral-500 dark:text-neutral-400 max-w-md text-sm leading-relaxed">{{ $errorMessage }}</p>

                    <button
                        wire:click="refresh"
                        wire:loading.attr="disabled"
                        class="mt-6 flex items-center gap-2 px-5 py-2.5 bg-[#4C5C2D] hover:bg-[#3d4b24] text-white text-sm font-semibold rounded-xl transition-all shadow-sm">
                        <flux:icon wire:loading.remove name="arrow-path" class="w-4 h-4" />
                        <flux:icon wire:loading name="arrow-path" class="w-4 h-4 animate-spin" />
                        <span wire:loading.remove>Coba Lagi</span>
                        <span wire:loading>Memuat...</span>
                    </button>
                </div>

            @elseif($iCareUrl)
                {{-- State: URL Siap — Embed iCare BPJS iframe (seperti mLITE) --}}
                <div class="flex flex-col w-full">
                    {{-- Toolbar Iframe --}}
                    <div class="flex items-center justify-between px-4 py-2.5 bg-neutral-100 dark:bg-neutral-900 border-b border-neutral-200 dark:border-neutral-700 text-xs">
                        <div class="flex items-center gap-2 text-neutral-600 dark:text-neutral-400 font-medium">
                            <flux:icon name="shield-check" class="w-4 h-4 text-green-600" />
                            <span>Server BPJS Kesehatan Terhubung</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <a
                                href="{{ $iCareUrl }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white dark:bg-neutral-800 border border-neutral-300 dark:border-neutral-600 rounded-lg text-neutral-700 dark:text-neutral-200 hover:bg-neutral-50 dark:hover:bg-neutral-700 transition-all text-xs font-semibold shadow-xs">
                                <flux:icon name="arrow-top-right-on-square" class="w-3.5 h-3.5" />
                                Buka di Tab Baru
                            </a>
                        </div>
                    </div>

                    {{-- Embed iCare Frame --}}
                    <div class="w-full h-[750px] relative bg-neutral-50 dark:bg-neutral-900">
                        <iframe
                            src="{{ $iCareUrl }}"
                            class="w-full h-full border-0"
                            allowfullscreen>
                        </iframe>
                    </div>
                </div>

            @else
                {{-- State: Idle --}}
                <div class="p-12 flex flex-col items-center justify-center text-center">
                    <flux:icon name="arrow-path" class="w-10 h-10 text-[#4C5C2D] animate-spin mb-4" />
                    <p class="text-neutral-500 dark:text-neutral-400">Sedang memproses permintaan iCare...</p>
                </div>
            @endif
        </div>
    </div>
</div>
