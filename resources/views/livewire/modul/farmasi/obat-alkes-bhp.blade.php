<div class="flex flex-col gap-6 pb-8">
    {{-- Header & Breadcrumb dengan Tombol Back (SOP #8: history.back()) --}}
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center gap-3">
            <button type="button" onclick="history.back()" class="flex items-center justify-center w-10 h-8 rounded-md bg-[#4C5C2D] transition-colors hover:bg-[#3d4b24] shadow-sm" title="Kembali">
                <flux:icon name="chevron-left" class="w-5 h-5 text-white" />
            </button>
            <div>
                <nav class="text-xs text-neutral-400 mb-0.5">
                    <a href="{{ route('modul.index') }}" wire:navigate class="hover:underline">Modul</a>
                    <span class="mx-1">/</span>
                    <span>Farmasi</span>
                    <span class="mx-1">/</span>
                    <a href="{{ route('modul.farmasi.daftar-resep-dokter') }}" wire:navigate class="hover:underline">Daftar Resep Dokter</a>
                    <span class="mx-1">/</span>
                    <span>Data Obat, Alkes dan BHP Medis</span>
                </nav>
                <h1 class="text-xl font-bold text-neutral-800 dark:text-neutral-100 flex items-center gap-2">
                    Data Obat, Alkes dan BHP Medis
                </h1>
            </div>
        </div>
    </div>

    {{-- Form Header Atas (Sesuai Rujukan Gambar Khanza - Desain Modern SIMRS Laralite) --}}
    <div class="bg-white dark:bg-neutral-800 rounded-2xl border border-neutral-200 dark:border-neutral-700 p-5 shadow-sm space-y-4">
        
        {{-- Baris 1: No.Rawat, No.RM, Nama Pasien --}}
        <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-center text-xs">
            {{-- No. Rawat --}}
            <div class="md:col-span-4 flex items-center gap-2">
                <label class="w-20 font-semibold text-neutral-600 dark:text-neutral-300 flex-shrink-0">No.Rawat :</label>
                <div class="relative flex-1">
                    <input type="text" wire:model="no_rawat" readonly placeholder="2026/04/09/000019"
                        class="w-full bg-neutral-50 dark:bg-neutral-900 border border-neutral-300 dark:border-neutral-700 text-neutral-800 dark:text-neutral-100 rounded-full text-xs px-4 py-1.5 font-mono font-bold shadow-inner focus:outline-none">
                </div>
            </div>

            {{-- No. RM --}}
            <div class="md:col-span-3 flex items-center gap-2">
                <label class="w-16 font-semibold text-neutral-600 dark:text-neutral-300 flex-shrink-0">No.RM :</label>
                <div class="relative flex-1">
                    <input type="text" wire:model="no_rkm_medis" readonly placeholder="007362"
                        class="w-full bg-neutral-50 dark:bg-neutral-900 border border-neutral-300 dark:border-neutral-700 text-neutral-800 dark:text-neutral-100 rounded-full text-xs px-4 py-1.5 font-mono font-bold shadow-inner focus:outline-none">
                </div>
            </div>

            {{-- Nama Pasien --}}
            <div class="md:col-span-5 flex items-center gap-2">
                <label class="w-24 font-semibold text-neutral-600 dark:text-neutral-300 flex-shrink-0">Nama Pasien :</label>
                <div class="relative flex-1">
                    <input type="text" wire:model="nm_pasien" readonly placeholder="EMI WIJAYANTI"
                        class="w-full bg-neutral-50 dark:bg-neutral-900 border border-neutral-300 dark:border-neutral-700 text-neutral-800 dark:text-neutral-100 rounded-full text-xs px-4 py-1.5 font-bold uppercase shadow-inner focus:outline-none">
                </div>
            </div>
        </div>

        {{-- Baris 2: Tanggal, Tarif, No.Resep --}}
        <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-center text-xs">
            {{-- Tanggal & Jam --}}
            <div class="md:col-span-6 flex items-center gap-2 flex-wrap sm:flex-nowrap">
                <label class="w-20 font-semibold text-neutral-600 dark:text-neutral-300 flex-shrink-0">Tanggal :</label>
                <div class="flex items-center gap-1.5 flex-1">
                    <input type="date" wire:model="tgl_validasi"
                        class="bg-white dark:bg-neutral-900 border border-neutral-300 dark:border-neutral-700 text-neutral-800 dark:text-neutral-100 rounded-lg text-xs px-2.5 py-1.5 shadow-sm focus:border-[#4C5C2D] focus:ring-[#4C5C2D]">
                    
                    <select wire:model="jam_validasi" class="bg-white dark:bg-neutral-900 border border-neutral-300 dark:border-neutral-700 text-neutral-800 dark:text-neutral-100 rounded-lg text-xs px-2 py-1.5 shadow-sm focus:border-[#4C5C2D] focus:ring-[#4C5C2D]">
                        @for($h=0; $h<24; $h++)
                            <option value="{{ sprintf('%02d', $h) }}">{{ sprintf('%02d', $h) }}</option>
                        @endfor
                    </select>
                    <span>:</span>
                    <select wire:model="menit_validasi" class="bg-white dark:bg-neutral-900 border border-neutral-300 dark:border-neutral-700 text-neutral-800 dark:text-neutral-100 rounded-lg text-xs px-2 py-1.5 shadow-sm focus:border-[#4C5C2D] focus:ring-[#4C5C2D]">
                        @for($m=0; $m<60; $m++)
                            <option value="{{ sprintf('%02d', $m) }}">{{ sprintf('%02d', $m) }}</option>
                        @endfor
                    </select>
                    <span>:</span>
                    <select wire:model="detik_validasi" class="bg-white dark:bg-neutral-900 border border-neutral-300 dark:border-neutral-700 text-neutral-800 dark:text-neutral-100 rounded-lg text-xs px-2 py-1.5 shadow-sm focus:border-[#4C5C2D] focus:ring-[#4C5C2D]">
                        @for($s=0; $s<60; $s++)
                            <option value="{{ sprintf('%02d', $s) }}">{{ sprintf('%02d', $s) }}</option>
                        @endfor
                    </select>

                    <label class="flex items-center gap-1 cursor-pointer ml-1">
                        <input type="checkbox" checked class="rounded border-neutral-300 text-[#4C5C2D] focus:ring-[#4C5C2D]">
                    </label>
                </div>
            </div>

            {{-- Tarif & No.Resep --}}
            <div class="md:col-span-6 flex items-center gap-4 flex-wrap sm:flex-nowrap">
                <div class="flex items-center gap-2 flex-1">
                    <label class="w-12 font-semibold text-neutral-600 dark:text-neutral-300 flex-shrink-0">Tarif :</label>
                    <select wire:model="tarif" class="w-full bg-white dark:bg-neutral-900 border border-neutral-300 dark:border-neutral-700 text-neutral-800 dark:text-neutral-100 rounded-lg text-xs px-3 py-1.5 font-medium shadow-sm focus:border-[#4C5C2D] focus:ring-[#4C5C2D]">
                        <option value="Rawat Jalan">Rawat Jalan</option>
                        <option value="Rawat Inap">Rawat Inap</option>
                        <option value="Bebas">Bebas</option>
                    </select>
                </div>

                <div class="flex items-center gap-2">
                    <label class="flex items-center gap-1.5 font-semibold text-neutral-700 dark:text-neutral-200 cursor-pointer">
                        <input type="checkbox" wire:model="use_no_resep" class="rounded border-neutral-300 text-[#4C5C2D] focus:ring-[#4C5C2D]">
                        <span>No.Resep</span>
                    </label>
                    @if($no_resep)
                        <span class="bg-[#4C5C2D]/10 text-[#4C5C2D] dark:bg-[#8CC7C4]/20 dark:text-[#8CC7C4] font-mono font-bold px-2.5 py-1 rounded-full text-xs border border-[#4C5C2D]/20">
                            {{ $no_resep }}
                        </span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Baris 3: Total, PPN, Total+PPN, Depo --}}
        <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-center text-xs pt-1 border-t border-neutral-100 dark:border-neutral-700/60">
            {{-- Total, PPN, Total+PPN --}}
            <div class="md:col-span-7 flex items-center gap-4 flex-wrap">
                <div class="flex items-center gap-1.5">
                    <span class="font-semibold text-neutral-600 dark:text-neutral-400">Total :</span>
                    <span class="font-mono font-bold text-neutral-800 dark:text-neutral-100">{{ number_format($total, 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="font-semibold text-neutral-600 dark:text-neutral-400">PPN :</span>
                    <span class="font-mono font-bold text-neutral-800 dark:text-neutral-100">{{ number_format($ppn, 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="font-semibold text-neutral-600 dark:text-neutral-400">Total+PPN :</span>
                    <span class="font-mono font-bold text-[#4C5C2D] dark:text-[#8CC7C4]">{{ number_format($total_ppn, 0, ',', '.') }}</span>
                </div>
            </div>

            {{-- Depo --}}
            <div class="md:col-span-5 flex items-center gap-2">
                <label class="w-14 font-semibold text-neutral-600 dark:text-neutral-300 flex-shrink-0">Depo :</label>
                <div class="flex items-center gap-2 flex-1">
                    <input type="text" wire:model="kd_depo" readonly
                        class="w-16 bg-neutral-50 dark:bg-neutral-900 border border-neutral-300 dark:border-neutral-700 text-neutral-800 dark:text-neutral-100 rounded-full text-xs px-3 py-1.5 font-bold text-center shadow-inner focus:outline-none">
                    <input type="text" wire:model="nm_depo" readonly
                        class="flex-1 bg-neutral-50 dark:bg-neutral-900 border border-neutral-300 dark:border-neutral-700 text-neutral-800 dark:text-neutral-100 rounded-full text-xs px-4 py-1.5 font-bold shadow-inner focus:outline-none">
                    <button type="button" class="p-1.5 rounded-lg bg-neutral-100 dark:bg-neutral-700 text-neutral-600 dark:text-neutral-300 hover:bg-neutral-200 dark:hover:bg-neutral-600 transition-colors">
                        <flux:icon name="paper-clip" class="w-4 h-4" />
                    </button>
                </div>
            </div>
        </div>

    </div>

    {{-- Body Container Placeholder untuk Rincian Item Obat --}}
    <div class="bg-white dark:bg-neutral-800 rounded-xl border border-neutral-200 dark:border-neutral-700 p-12 text-center flex flex-col items-center justify-center min-h-[300px] shadow-sm">
        <div class="w-16 h-16 rounded-2xl bg-[#4C5C2D]/10 text-[#4C5C2D] dark:bg-[#8CC7C4]/20 dark:text-[#8CC7C4] flex items-center justify-center mb-3 shadow-sm">
            <flux:icon name="check-badge" class="w-8 h-8" />
        </div>
        <h3 class="text-base font-bold text-neutral-800 dark:text-neutral-100">Validasi Data Obat, Alkes dan BHP Medis</h3>
        <p class="text-xs text-neutral-500 dark:text-neutral-400 max-w-md mt-1 leading-relaxed">
            Header form di atas telah disesuaikan dengan standar data peresepan & depo farmasi. Area ini akan berisi daftar item obat & alkes yang akan divalidasi.
        </p>
    </div>
</div>
