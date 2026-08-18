<div class="flex flex-col gap-6 pb-8">
    {{-- Header & Breadcrumb dengan Tombol Back (SOP #8: history.back()) dan Tombol Simpan --}}
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
        <div>
            <flux:button wire:click="save" variant="primary" icon="check" class="!bg-[#4C5C2D] !border-[#4C5C2D] hover:!bg-[#3D4A24] h-9 px-5 text-xs font-bold shadow-sm">
                Simpan Data
            </flux:button>
        </div>
    </div>

    {{-- Form Header Atas --}}
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
            <div class="md:col-span-6 flex items-center gap-2 flex-wrap sm:flex-nowrap"
                x-data="{
                    isLive: true,
                    timer: null,
                    startTimer() {
                        this.stopTimer();
                        this.timer = setInterval(() => {
                            if (!this.isLive) return;
                            const now = new Date();
                            const yyyy = now.getFullYear();
                            const mm = String(now.getMonth() + 1).padStart(2, '0');
                            const dd = String(now.getDate()).padStart(2, '0');
                            $wire.tgl_validasi = `${yyyy}-${mm}-${dd}`;
                            $wire.jam_validasi = String(now.getHours()).padStart(2, '0');
                            $wire.menit_validasi = String(now.getMinutes()).padStart(2, '0');
                            $wire.detik_validasi = String(now.getSeconds()).padStart(2, '0');
                        }, 1000);
                    },
                    stopTimer() {
                        if (this.timer) {
                            clearInterval(this.timer);
                            this.timer = null;
                        }
                    },
                    toggleLive(checked) {
                        this.isLive = checked;
                        if (this.isLive) {
                            this.startTimer();
                        } else {
                            this.stopTimer();
                        }
                    }
                }" x-init="startTimer()">
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

                    <label class="flex items-center gap-1 cursor-pointer ml-1" title="Real-time Clock Timer">
                        <input type="checkbox" x-model="isLive" @change="toggleLive($event.target.checked)" class="rounded border-neutral-300 text-[#4C5C2D] focus:ring-[#4C5C2D]">
                    </label>
                </div>
            </div>

            {{-- Tarif (Rawat Jalan, Beli Luar, Karyawan, Utama/BPJS) & No.Resep --}}
            <div class="md:col-span-6 flex items-center gap-4 flex-wrap sm:flex-nowrap">
                <div class="flex items-center gap-2 flex-1">
                    <label class="w-12 font-semibold text-neutral-600 dark:text-neutral-300 flex-shrink-0">Tarif :</label>
                    <select wire:model="tarif" class="w-full bg-white dark:bg-neutral-900 border border-neutral-300 dark:border-neutral-700 text-neutral-800 dark:text-neutral-100 rounded-lg text-xs px-3 py-1.5 font-semibold shadow-sm focus:border-[#4C5C2D] focus:ring-[#4C5C2D]">
                        @foreach($optionsTarif as $opt)
                            <option value="{{ $opt }}">{{ $opt }}</option>
                        @endforeach
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

            {{-- Depo / Kamar --}}
            <div class="md:col-span-5 flex items-center gap-2">
                <label class="w-14 font-semibold text-neutral-600 dark:text-neutral-300 flex-shrink-0">Depo :</label>
                <div class="flex items-center gap-2 flex-1">
                    <input type="text" wire:model="kd_depo" readonly wire:click="openBangsalModal"
                        class="w-16 bg-neutral-50 dark:bg-neutral-900 border border-neutral-300 dark:border-neutral-700 text-neutral-800 dark:text-neutral-100 rounded-full text-xs px-3 py-1.5 font-bold text-center shadow-inner cursor-pointer focus:outline-none">
                    <input type="text" wire:model="nm_depo" readonly wire:click="openBangsalModal"
                        class="flex-1 bg-neutral-50 dark:bg-neutral-900 border border-neutral-300 dark:border-neutral-700 text-neutral-800 dark:text-neutral-100 rounded-full text-xs px-4 py-1.5 font-bold shadow-inner cursor-pointer focus:outline-none">
                    <button type="button" wire:click="openBangsalModal" class="p-1.5 rounded-lg bg-neutral-100 dark:bg-neutral-700 text-neutral-600 dark:text-neutral-300 hover:bg-neutral-200 dark:hover:bg-neutral-600 transition-colors" title="Pilih Kamar / Depo">
                        <flux:icon name="paper-clip" class="w-4 h-4" />
                    </button>
                </div>
            </div>
        </div>

    </div>

    {{-- Tabs Navigation: Umum & Racikan --}}
    <div class="border-b border-neutral-200 dark:border-neutral-700 flex items-center gap-2 mt-2">
        <button type="button"
            wire:click="setTab('umum')"
            class="px-4 py-2 text-xs font-bold transition-all border-b-2 flex items-center gap-1.5 {{ $activeTab === 'umum' ? 'border-[#4C5C2D] text-[#4C5C2D] dark:text-[#8CC7C4] dark:border-[#8CC7C4]' : 'border-transparent text-neutral-500 hover:text-neutral-700 dark:text-neutral-400 dark:hover:text-neutral-200' }}">
            <flux:icon name="beaker" class="w-4 h-4" />
            <span>Umum</span>
            <span class="px-1.5 py-0.5 rounded-full text-[10px] font-extrabold {{ $activeTab === 'umum' ? 'bg-[#4C5C2D]/10 text-[#4C5C2D] dark:bg-[#8CC7C4]/20 dark:text-[#8CC7C4]' : 'bg-neutral-100 text-neutral-600 dark:bg-neutral-800 dark:text-neutral-400' }}">
                {{ count($listObatUmum) }}
            </span>
        </button>

        <button type="button"
            wire:click="setTab('racikan')"
            class="px-4 py-2 text-xs font-bold transition-all border-b-2 flex items-center gap-1.5 {{ $activeTab === 'racikan' ? 'border-[#4C5C2D] text-[#4C5C2D] dark:text-[#8CC7C4] dark:border-[#8CC7C4]' : 'border-transparent text-neutral-500 hover:text-neutral-700 dark:text-neutral-400 dark:hover:text-neutral-200' }}">
            <flux:icon name="square-3-stack-3d" class="w-4 h-4" />
            <span>Racikan</span>
            <span class="px-1.5 py-0.5 rounded-full text-[10px] font-extrabold bg-neutral-100 text-neutral-500 dark:bg-neutral-800 dark:text-neutral-400">
                {{ count($listObatRacikan) }}
            </span>
        </button>
    </div>

    {{-- Content Table per Tab --}}
    @if($activeTab === 'umum')
        {{-- TAB UMUM COMPACT TABLE --}}
        <div class="bg-white dark:bg-neutral-800 rounded-xl border border-neutral-200 dark:border-neutral-700 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-[11px]">
                    <thead class="bg-neutral-50 dark:bg-neutral-800 text-neutral-600 dark:text-neutral-300 uppercase text-[9px] tracking-tight font-extrabold border-b border-neutral-200 dark:border-neutral-700 sticky top-0 z-10">
                        <tr>
                            <th class="px-1.5 py-2 text-center w-6">K</th>
                            <th class="px-1.5 py-2 text-center whitespace-nowrap">Jumlah</th>
                            <th class="px-2 py-2 whitespace-nowrap">Kode Barang</th>
                            <th class="px-2 py-2 whitespace-nowrap">Nama Barang</th>
                            <th class="px-1.5 py-2 whitespace-nowrap">Satuan</th>
                            <th class="px-2 py-2 text-right whitespace-nowrap">Harga (Rp)</th>
                            <th class="px-2 py-2 whitespace-nowrap">Jenis Obat</th>
                            <th class="px-1.5 py-2 text-center whitespace-nowrap">Emb</th>
                            <th class="px-1.5 py-2 text-center whitespace-nowrap">Tsl</th>
                            <th class="px-1.5 py-2 text-center whitespace-nowrap">Stok</th>
                            <th class="px-2 py-2 whitespace-nowrap">Aturan Pakai</th>
                            <th class="px-1.5 py-2 whitespace-nowrap">I.F.</th>
                            <th class="px-1.5 py-2 whitespace-nowrap">Kategori</th>
                            <th class="px-1.5 py-2 whitespace-nowrap">Golongan</th>
                            <th class="px-1.5 py-2 whitespace-nowrap">No.Batch</th>
                            <th class="px-1.5 py-2 whitespace-nowrap">No.Faktur</th>
                            <th class="px-1.5 py-2 whitespace-nowrap">Kadaluarsa</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-100 dark:divide-neutral-700/60 font-medium">
                        @forelse($listObatUmum as $index => $item)
                            <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-700/40 transition-colors">
                                {{-- K (Checkbox) --}}
                                <td class="px-1.5 py-1 text-center">
                                    <input type="checkbox" checked class="rounded border-neutral-300 text-[#4C5C2D] focus:ring-[#4C5C2D] w-3 h-3">
                                </td>

                                {{-- Jumlah (FORM EDITABLE) --}}
                                <td class="px-1 py-1 text-center whitespace-nowrap">
                                    <input type="number" step="any" wire:model.live="listObatUmum.{{ $index }}.jumlah"
                                        class="w-12 bg-white dark:bg-neutral-900 border border-neutral-300 dark:border-neutral-700 text-neutral-800 dark:text-neutral-100 rounded text-[11px] px-1 py-0.5 font-bold text-center focus:border-[#4C5C2D] focus:ring-[#4C5C2D]">
                                </td>

                                {{-- Kode Barang --}}
                                <td class="px-2 py-1 font-mono text-neutral-600 dark:text-neutral-400 whitespace-nowrap">
                                    {{ $item['kode_brng'] }}
                                </td>

                                {{-- Nama Barang --}}
                                <td class="px-2 py-1 font-bold text-neutral-800 dark:text-neutral-100 whitespace-nowrap">
                                    {{ $item['nama_brng'] }}
                                </td>

                                {{-- Satuan --}}
                                <td class="px-1.5 py-1 text-neutral-600 dark:text-neutral-400 whitespace-nowrap">
                                    {{ $item['satuan'] }}
                                </td>

                                {{-- Harga(Rp) --}}
                                <td class="px-2 py-1 text-right font-mono font-bold text-neutral-800 dark:text-neutral-200 whitespace-nowrap">
                                    {{ number_format($item['harga'], 0, ',', '.') }}
                                </td>

                                {{-- Jenis Obat --}}
                                <td class="px-2 py-1 text-neutral-600 dark:text-neutral-400 whitespace-nowrap text-[10px]">
                                    {{ $item['jenis_obat'] }}
                                </td>

                                {{-- Emb / Embalase (FORM EDITABLE) --}}
                                <td class="px-1 py-1 text-center whitespace-nowrap">
                                    <input type="number" step="any" wire:model.live="listObatUmum.{{ $index }}.embalase"
                                        class="w-12 bg-white dark:bg-neutral-900 border border-neutral-300 dark:border-neutral-700 text-neutral-800 dark:text-neutral-100 rounded text-[11px] px-1 py-0.5 text-center font-mono focus:border-[#4C5C2D] focus:ring-[#4C5C2D]">
                                </td>

                                {{-- Tsl / Tuslah (FORM EDITABLE) --}}
                                <td class="px-1 py-1 text-center whitespace-nowrap">
                                    <input type="number" step="any" wire:model.live="listObatUmum.{{ $index }}.tuslah"
                                        class="w-12 bg-white dark:bg-neutral-900 border border-neutral-300 dark:border-neutral-700 text-neutral-800 dark:text-neutral-100 rounded text-[11px] px-1 py-0.5 text-center font-mono focus:border-[#4C5C2D] focus:ring-[#4C5C2D]">
                                </td>

                                {{-- Stok --}}
                                <td class="px-1.5 py-1 text-center font-mono font-bold text-emerald-700 dark:text-emerald-400 whitespace-nowrap">
                                    {{ number_format($item['stok'], 0, ',', '.') }}
                                </td>

                                {{-- Aturan Pakai (FORM EDITABLE) --}}
                                <td class="px-1.5 py-1 whitespace-nowrap">
                                    <input type="text" wire:model.live="listObatUmum.{{ $index }}.aturan_pakai" placeholder="Aturan Pakai"
                                        class="w-32 bg-white dark:bg-neutral-900 border border-neutral-300 dark:border-neutral-700 text-neutral-800 dark:text-neutral-100 rounded text-[11px] px-1.5 py-0.5 font-medium focus:border-[#4C5C2D] focus:ring-[#4C5C2D]">
                                </td>

                                {{-- I.F. --}}
                                <td class="px-1.5 py-1 text-neutral-500 whitespace-nowrap text-[10px]">
                                    {{ $item['industri'] }}
                                </td>

                                {{-- Kategori --}}
                                <td class="px-1.5 py-1 text-neutral-600 dark:text-neutral-400 whitespace-nowrap text-[10px]">
                                    {{ $item['kategori'] }}
                                </td>

                                {{-- Golongan --}}
                                <td class="px-1.5 py-1 text-neutral-500 whitespace-nowrap text-[10px]">
                                    {{ $item['golongan'] }}
                                </td>

                                {{-- No.Batch (FORM EDITABLE) --}}
                                <td class="px-1 py-1 whitespace-nowrap">
                                    <input type="text" wire:model.live="listObatUmum.{{ $index }}.no_batch" placeholder="No. Batch"
                                        class="w-20 bg-white dark:bg-neutral-900 border border-neutral-300 dark:border-neutral-700 text-neutral-800 dark:text-neutral-100 rounded text-[11px] px-1 py-0.5 font-mono focus:border-[#4C5C2D] focus:ring-[#4C5C2D]">
                                </td>

                                {{-- No.Faktur (FORM EDITABLE) --}}
                                <td class="px-1 py-1 whitespace-nowrap">
                                    <input type="text" wire:model.live="listObatUmum.{{ $index }}.no_faktur" placeholder="No. Faktur"
                                        class="w-20 bg-white dark:bg-neutral-900 border border-neutral-300 dark:border-neutral-700 text-neutral-800 dark:text-neutral-100 rounded text-[11px] px-1 py-0.5 font-mono focus:border-[#4C5C2D] focus:ring-[#4C5C2D]">
                                </td>

                                {{-- Kadaluarsa --}}
                                <td class="px-1.5 py-1 whitespace-nowrap text-neutral-500 text-[10px]">
                                    {{ $item['kadaluarsa'] }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="17" class="px-4 py-10 text-center text-neutral-400 dark:text-neutral-500">
                                    <flux:icon name="beaker" class="w-8 h-8 mx-auto mb-2 opacity-50" />
                                    <p class="text-xs font-semibold">Tidak ada item obat umum pada resep ini.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @else
        {{-- TAB RACIKAN PLACEHOLDER --}}
        <div class="bg-white dark:bg-neutral-800 rounded-xl border border-neutral-200 dark:border-neutral-700 p-12 text-center flex flex-col items-center justify-center min-h-[250px] shadow-sm">
            <div class="w-16 h-16 rounded-2xl bg-neutral-100 dark:bg-neutral-700 text-neutral-400 flex items-center justify-center mb-3">
                <flux:icon name="square-3-stack-3d" class="w-8 h-8" />
            </div>
            <h3 class="text-base font-bold text-neutral-700 dark:text-neutral-200">Data Resep Racikan</h3>
            <p class="text-xs text-neutral-400 max-w-md mt-1">Belum ada item resep racikan terdaftar pada resep ini.</p>
        </div>
    @endif

    {{-- MODAL LOOKUP DEPO / KAMAR --}}
    @if($isBangsalModalOpen)
        <div class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 sm:p-6" style="background-color: rgba(0,0,0,0.6); backdrop-filter: blur(4px);">
            <div class="relative w-full max-w-lg bg-white dark:bg-neutral-900 rounded-2xl shadow-2xl border border-neutral-200 dark:border-neutral-700 overflow-hidden">
                
                {{-- Modal Header --}}
                <div class="flex items-center justify-between px-5 py-3.5 bg-[#4C5C2D] text-white">
                    <h3 class="font-mono font-bold text-white text-sm flex items-center gap-2">
                        <flux:icon name="building-office" class="w-4 h-4 text-white" />
                        :: [ Kamar / Depo ] ::
                    </h3>
                    <button type="button" wire:click="closeBangsalModal" class="p-1 rounded-lg hover:bg-white/10 text-white/80 hover:text-white transition-colors">
                        <flux:icon name="x-mark" class="w-5 h-5" />
                    </button>
                </div>

                {{-- Modal Body --}}
                <div class="p-4 space-y-3">
                    {{-- Search Bar --}}
                    <div class="relative">
                        <input type="text" wire:model.live.debounce.300ms="searchBangsalModal" placeholder="Cari Kode atau Nama Kamar..."
                            class="w-full bg-white dark:bg-neutral-800 border border-neutral-300 dark:border-neutral-700 text-neutral-800 dark:text-neutral-100 rounded-lg text-xs pl-8 pr-3 py-2 shadow-sm focus:border-[#4C5C2D] focus:ring-[#4C5C2D]">
                        <flux:icon name="magnifying-glass" class="w-4 h-4 absolute left-2.5 top-2.5 text-neutral-400" />
                    </div>

                    {{-- Tabel Kamar / Depo --}}
                    <div class="max-h-80 overflow-y-auto border border-neutral-200 dark:border-neutral-700 rounded-lg">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead class="bg-neutral-100 dark:bg-neutral-800 text-neutral-600 dark:text-neutral-300 font-bold uppercase text-[10px] sticky top-0 z-10">
                                <tr>
                                    <th class="px-3 py-2 border-b border-neutral-200 dark:border-neutral-700 w-32 font-mono">Kode Kamar</th>
                                    <th class="px-3 py-2 border-b border-neutral-200 dark:border-neutral-700">Nama Kamar</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-neutral-100 dark:divide-neutral-700/60">
                                @forelse($listBangsal as $b)
                                    <tr wire:click="selectBangsal('{{ $b['kd_bangsal'] }}', '{{ addslashes($b['nm_bangsal']) }}')"
                                        class="hover:bg-amber-50 dark:hover:bg-neutral-700/50 cursor-pointer transition-colors">
                                        <td class="px-3 py-2 font-mono font-bold text-neutral-800 dark:text-neutral-200">
                                            {{ $b['kd_bangsal'] }}
                                        </td>
                                        <td class="px-3 py-2 text-neutral-700 dark:text-neutral-300">
                                            {{ $b['nm_bangsal'] }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="p-4 text-center text-neutral-400 italic text-xs">
                                            Data Kamar / Depo tidak ditemukan.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Modal Footer --}}
                <div class="px-4 py-2.5 bg-neutral-50 dark:bg-neutral-800/50 border-t border-neutral-200 dark:border-neutral-700 flex justify-end">
                    <button type="button" wire:click="closeBangsalModal" class="px-4 py-1.5 rounded-lg bg-neutral-200 dark:bg-neutral-700 hover:bg-neutral-300 text-neutral-700 dark:text-neutral-200 font-semibold text-xs transition-colors">
                        Tutup
                    </button>
                </div>

            </div>
        </div>
    @endif
</div>
