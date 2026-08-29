<div class="flex flex-col gap-6 pb-12 max-w-7xl mx-auto w-full"
x-on:swal:toast.window="
    Swal.fire({
        toast: true,
        position: 'top-end',
        icon: $event.detail[0].icon || 'success',
        title: $event.detail[0].title,
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true
    });
">
    <form wire:submit.prevent="save"
        x-data="{
            confirmSave() {
                Swal.fire({
                    title: 'Simpan Data Obat & BHP?',
                    text: 'Apakah data obat, alkes & BHP ini sudah benar dan siap disimpan?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#4C5C2D',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Ya, Simpan!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $wire.save();
                    }
                });
            },
            handleEnter(e) {
                if (e.key !== 'Enter' || e.shiftKey || e.target.tagName === 'TEXTAREA') return;
                const current = e.target;
                const currentStep = parseInt(current.getAttribute('data-step') || '0');
                if (currentStep > 0) {
                    e.preventDefault();
                    const nextEl = this.$el.querySelector(`[data-step='${currentStep + 1}']`);
                    if (nextEl) {
                        nextEl.focus();
                        if (typeof nextEl.select === 'function' && nextEl.tagName === 'INPUT' && nextEl.type !== 'date') {
                            nextEl.select();
                        }
                    } else if (currentStep >= 26) {
                        this.confirmSave();
                    }
                }
            }
        }"
        @keydown="handleEnter($event)">
        
        {{-- Header & Breadcrumb dengan Tombol Back (SOP #8: history.back()) dan Tombol Simpan --}}
        <div class="flex items-center justify-between flex-wrap gap-4 bg-white dark:bg-neutral-800 p-4 rounded-xl border border-neutral-200 dark:border-neutral-700 shadow-sm sticky top-2 z-20">
            <div class="flex items-center gap-3">
                <button type="button" onclick="history.length > 1 ? history.back() : window.location.href = '{{ route('modul.farmasi.data-barang') }}'"
                    class="flex items-center justify-center w-10 h-8 rounded-md bg-[#4C5C2D] transition-colors hover:bg-[#3d4b24] shadow-sm"
                    title="Kembali">
                    <flux:icon name="chevron-left" class="w-5 h-5 text-white" />
                </button>
                <div>
                    <nav class="text-xs text-neutral-400 mb-0.5">
                        <a href="{{ route('modul.index') }}" wire:navigate class="hover:underline">Modul</a>
                        <span class="mx-1">/</span>
                        <a href="{{ route('modul.farmasi.index') }}" wire:navigate class="hover:underline">Farmasi</a>
                        <span class="mx-1">/</span>
                        <a href="{{ route('modul.farmasi.data-barang') }}" wire:navigate class="hover:underline">Data Obat, Alkes & BHP</a>
                        <span class="mx-1">/</span>
                        <span>Tambah Barang</span>
                    </nav>
                    <h1 class="text-lg font-bold text-neutral-800 dark:text-neutral-100 flex items-center gap-2">
                        Tambah Data Obat, Alkes dan BHP Medis
                    </h1>
                </div>
            </div>

            <div class="flex items-center gap-2.5">
                <button type="button" onclick="history.length > 1 ? history.back() : window.location.href = '{{ route('modul.farmasi.data-barang') }}'"
                    class="px-4 py-2 text-xs font-semibold rounded-lg border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 text-neutral-700 dark:text-neutral-200 hover:bg-neutral-50 dark:hover:bg-neutral-700 transition-colors shadow-sm">
                    Batal
                </button>
                <button type="button" @click="confirmSave()" data-step="27" wire:loading.attr="disabled"
                    class="inline-flex items-center gap-2 px-5 py-2 text-xs font-semibold rounded-lg text-white shadow transition-colors disabled:opacity-50 cursor-pointer"
                    style="background-color: #4C5C2D;"
                    title="Simpan Master Barang">
                    <flux:icon name="check" class="w-4 h-4" />
                    Simpan Barang
                </button>
            </div>
        </div>

        {{-- Vertical Linear Flow (1 -> 2 -> 3 -> 4) --}}
        <div class="flex flex-col gap-6 mt-6">
            
            {{-- SEKSI 1: IDENTITAS & KLASIFIKASI BARANG --}}
            <div class="bg-white dark:bg-neutral-800 rounded-xl border border-neutral-200 dark:border-neutral-700 p-6 shadow-sm flex flex-col gap-5">
                <div class="flex items-center justify-between border-b border-neutral-200 dark:border-neutral-700 pb-3">
                    <div class="flex items-center gap-2.5">
                        <span class="w-6 h-6 rounded-full bg-[#4C5C2D] text-white text-xs font-bold flex items-center justify-center">1</span>
                        <h2 class="text-sm font-bold uppercase tracking-wider text-neutral-800 dark:text-neutral-100">
                            Identitas & Klasifikasi Barang
                        </h2>
                    </div>
                    <span class="text-xs text-neutral-400 font-mono">Step 1 - 7</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    {{-- 1. Kode Barang --}}
                    <div>
                        <label class="block text-xs font-semibold text-neutral-700 dark:text-neutral-300 mb-1.5">
                            Kode Barang <span class="text-red-500">*</span>
                        </label>
                        <input type="text" data-step="1" wire:model="kode_brng"
                            class="block w-full px-3.5 py-2.5 text-xs font-mono font-bold rounded-lg border border-neutral-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-900 text-neutral-900 dark:text-neutral-100 focus:bg-white focus:ring-2 focus:ring-[#4C5C2D]/30 focus:border-[#4C5C2D] transition-colors"
                            placeholder="B000000001" required />
                        @error('kode_brng') <span class="text-red-500 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    {{-- 2. Nama Barang --}}
                    <div class="md:col-span-2">
                        <label class="block text-xs font-semibold text-neutral-700 dark:text-neutral-300 mb-1.5">
                            Nama Lengkap Barang <span class="text-red-500">*</span>
                        </label>
                        <input type="text" data-step="2" wire:model="nama_brng"
                            class="block w-full px-3.5 py-2.5 text-xs font-bold rounded-lg border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-neutral-900 dark:text-neutral-100 focus:ring-2 focus:ring-[#4C5C2D]/30 focus:border-[#4C5C2D] transition-colors"
                            placeholder="Contoh: Paracetamol 500mg Tablet / Spuit 3cc" required />
                        @error('nama_brng') <span class="text-red-500 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    {{-- 3. Industri Farmasi --}}
                    <div>
                        <label class="block text-xs font-semibold text-neutral-700 dark:text-neutral-300 mb-1.5">
                            Industri Farmasi (Pabrikan)
                        </label>
                        <select data-step="3" wire:model="kode_industri"
                            class="block w-full px-3.5 py-2.5 text-xs rounded-lg border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-neutral-900 dark:text-neutral-100 focus:ring-2 focus:ring-[#4C5C2D]/30 focus:border-[#4C5C2D] transition-colors">
                            <option value="">-- Pilih Industri Farmasi --</option>
                            @foreach($listIndustri as $ind)
                                <option value="{{ $ind->kode_industri }}">{{ $ind->kode_industri }} - {{ $ind->nama_industri }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- 4. Kandungan / Letak --}}
                    <div class="md:col-span-2">
                        <label class="block text-xs font-semibold text-neutral-700 dark:text-neutral-300 mb-1.5">
                            Kandungan / Zat Aktif / Letak Rak
                        </label>
                        <input type="text" data-step="4" wire:model="letak_barang"
                            class="block w-full px-3.5 py-2.5 text-xs rounded-lg border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-neutral-900 dark:text-neutral-100 focus:ring-2 focus:ring-[#4C5C2D]/30 focus:border-[#4C5C2D] transition-colors"
                            placeholder="Contoh: Paracetamol / Rak A-02" />
                    </div>

                    {{-- 5. Jenis --}}
                    <div>
                        <label class="block text-xs font-semibold text-neutral-700 dark:text-neutral-300 mb-1.5">Jenis Barang</label>
                        <select data-step="5" wire:model="kdjns"
                            class="block w-full px-3.5 py-2.5 text-xs rounded-lg border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-neutral-900 dark:text-neutral-100 focus:ring-2 focus:ring-[#4C5C2D]/30 focus:border-[#4C5C2D] transition-colors">
                            <option value="">-- Pilih Jenis --</option>
                            @foreach($listJenis as $jns)
                                <option value="{{ $jns->kdjns }}">{{ $jns->nama }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- 6. Kategori --}}
                    <div>
                        <label class="block text-xs font-semibold text-neutral-700 dark:text-neutral-300 mb-1.5">Kategori Barang</label>
                        <select data-step="6" wire:model="kode_kategori"
                            class="block w-full px-3.5 py-2.5 text-xs rounded-lg border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-neutral-900 dark:text-neutral-100 focus:ring-2 focus:ring-[#4C5C2D]/30 focus:border-[#4C5C2D] transition-colors">
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($listKategori as $kat)
                                <option value="{{ $kat->kode }}">{{ $kat->nama }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- 7. Golongan --}}
                    <div>
                        <label class="block text-xs font-semibold text-neutral-700 dark:text-neutral-300 mb-1.5">Golongan Barang</label>
                        <select data-step="7" wire:model="kode_golongan"
                            class="block w-full px-3.5 py-2.5 text-xs rounded-lg border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-neutral-900 dark:text-neutral-100 focus:ring-2 focus:ring-[#4C5C2D]/30 focus:border-[#4C5C2D] transition-colors">
                            <option value="">-- Pilih Golongan --</option>
                            @foreach($listGolongan as $gol)
                                <option value="{{ $gol->kode }}">{{ $gol->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- SEKSI 2: KEMASAN, SATUAN & LOGISTIK STOK --}}
            <div class="bg-white dark:bg-neutral-800 rounded-xl border border-neutral-200 dark:border-neutral-700 p-6 shadow-sm flex flex-col gap-5">
                <div class="flex items-center justify-between border-b border-neutral-200 dark:border-neutral-700 pb-3">
                    <div class="flex items-center gap-2.5">
                        <span class="w-6 h-6 rounded-full bg-[#4C5C2D] text-white text-xs font-bold flex items-center justify-center">2</span>
                        <h2 class="text-sm font-bold uppercase tracking-wider text-neutral-800 dark:text-neutral-100">
                            Kemasan, Satuan & Logistik Stok
                        </h2>
                    </div>
                    <span class="text-xs text-neutral-400 font-mono">Step 8 - 14</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    {{-- 8. Satuan Besar --}}
                    <div class="lg:col-span-2">
                        <label class="block text-xs font-semibold text-neutral-700 dark:text-neutral-300 mb-1.5">
                            Satuan Besar (Kemasan Luar) <span class="text-red-500">*</span>
                        </label>
                        <select data-step="8" wire:model="kode_satbesar"
                            class="block w-full px-3.5 py-2.5 text-xs rounded-lg border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-neutral-900 dark:text-neutral-100 focus:ring-2 focus:ring-[#4C5C2D]/30 focus:border-[#4C5C2D] transition-colors" required>
                            <option value="">-- Pilih Satuan Besar --</option>
                            @foreach($listSatuan as $sat)
                                <option value="{{ $sat->kode_sat }}">{{ $sat->kode_sat }} - {{ $sat->satuan }}</option>
                            @endforeach
                        </select>
                        @error('kode_satbesar') <span class="text-red-500 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    {{-- 9. Isi Konversi --}}
                    <div class="lg:col-span-2">
                        <label class="block text-xs font-semibold text-neutral-700 dark:text-neutral-300 mb-1.5">
                            Isi Konversi (Jumlah satuan kecil per satuan besar) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" step="any" data-step="9" wire:model="isi"
                            class="block w-full px-3.5 py-2.5 text-xs font-bold rounded-lg border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-neutral-900 dark:text-neutral-100 focus:ring-2 focus:ring-[#4C5C2D]/30 focus:border-[#4C5C2D] transition-colors" required />
                        @error('isi') <span class="text-red-500 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    {{-- 10. Satuan Kecil --}}
                    <div class="lg:col-span-2">
                        <label class="block text-xs font-semibold text-neutral-700 dark:text-neutral-300 mb-1.5">
                            Satuan Terkecil (Satuan Resep/Pakai) <span class="text-red-500">*</span>
                        </label>
                        <select data-step="10" wire:model="kode_sat"
                            class="block w-full px-3.5 py-2.5 text-xs rounded-lg border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-neutral-900 dark:text-neutral-100 focus:ring-2 focus:ring-[#4C5C2D]/30 focus:border-[#4C5C2D] transition-colors" required>
                            <option value="">-- Pilih Satuan Kecil --</option>
                            @foreach($listSatuan as $sat)
                                <option value="{{ $sat->kode_sat }}">{{ $sat->kode_sat }} - {{ $sat->satuan }}</option>
                            @endforeach
                        </select>
                        @error('kode_sat') <span class="text-red-500 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    {{-- 11. Kapasitas / Dosis --}}
                    <div class="lg:col-span-2">
                        <label class="block text-xs font-semibold text-neutral-700 dark:text-neutral-300 mb-1.5">
                            Kapasitas / Dosis Potensi <span class="text-red-500">*</span>
                        </label>
                        <input type="number" step="any" data-step="11" wire:model="kapasitas"
                            class="block w-full px-3.5 py-2.5 text-xs font-bold rounded-lg border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-neutral-900 dark:text-neutral-100 focus:ring-2 focus:ring-[#4C5C2D]/30 focus:border-[#4C5C2D] transition-colors" required />
                        @error('kapasitas') <span class="text-red-500 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    {{-- 12. Stok Minimal --}}
                    <div>
                        <label class="block text-xs font-semibold text-neutral-700 dark:text-neutral-300 mb-1.5">
                            Stok Minimal
                        </label>
                        <input type="number" step="any" data-step="12" wire:model="stokminimal"
                            class="block w-full px-3.5 py-2.5 text-xs font-bold rounded-lg border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-neutral-900 dark:text-neutral-100 focus:ring-2 focus:ring-[#4C5C2D]/30 focus:border-[#4C5C2D] transition-colors" />
                    </div>

                    {{-- 13. Tanggal Kadaluwarsa --}}
                    <div class="lg:col-span-2">
                        <label class="block text-xs font-semibold text-neutral-700 dark:text-neutral-300 mb-1.5">
                            Tanggal Kadaluwarsa
                        </label>
                        <input type="date" data-step="13" wire:model="expire"
                            class="block w-full px-3.5 py-2.5 text-xs rounded-lg border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-neutral-900 dark:text-neutral-100 focus:ring-2 focus:ring-[#4C5C2D]/30 focus:border-[#4C5C2D] transition-colors" />
                    </div>

                    {{-- 14. Status --}}
                    <div>
                        <label class="block text-xs font-semibold text-neutral-700 dark:text-neutral-300 mb-1.5">
                            Status Keaktifan
                        </label>
                        <select data-step="14" wire:model="status"
                            class="block w-full px-3.5 py-2.5 text-xs font-semibold rounded-lg border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-neutral-900 dark:text-neutral-100 focus:ring-2 focus:ring-[#4C5C2D]/30 focus:border-[#4C5C2D] transition-colors">
                            <option value="1">Aktif</option>
                            <option value="0">Non-Aktif</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- SEKSI 3: HARGA POKOK & TARIF PELAYANAN UMUM --}}
            <div class="bg-white dark:bg-neutral-800 rounded-xl border border-neutral-200 dark:border-neutral-700 p-6 shadow-sm flex flex-col gap-5">
                <div class="flex items-center justify-between border-b border-neutral-200 dark:border-neutral-700 pb-3">
                    <div class="flex items-center gap-2.5">
                        <span class="w-6 h-6 rounded-full bg-[#4C5C2D] text-white text-xs font-bold flex items-center justify-center">3</span>
                        <h2 class="text-sm font-bold uppercase tracking-wider text-neutral-800 dark:text-neutral-100">
                            Harga Pokok & Tarif Pelayanan Umum
                        </h2>
                    </div>
                    <span class="text-xs text-neutral-400 font-mono">Step 15 - 21</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    {{-- 15. Harga Dasar --}}
                    <div>
                        <label class="block text-xs font-semibold text-neutral-700 dark:text-neutral-300 mb-1.5">
                            Harga Dasar / Cost (Rp)
                        </label>
                        <input type="number" step="any" data-step="15" wire:model="dasar"
                            class="block w-full px-3.5 py-2.5 text-xs font-bold rounded-lg border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-neutral-900 dark:text-neutral-100 focus:ring-2 focus:ring-[#4C5C2D]/30 focus:border-[#4C5C2D] transition-colors" />
                    </div>

                    {{-- 16. Harga Beli --}}
                    <div>
                        <label class="block text-xs font-semibold text-neutral-700 dark:text-neutral-300 mb-1.5">
                            Harga Beli / HPP (Rp)
                        </label>
                        <input type="number" step="any" data-step="16" wire:model="h_beli"
                            class="block w-full px-3.5 py-2.5 text-xs font-bold text-red-600 dark:text-red-400 rounded-lg border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 focus:ring-2 focus:ring-[#4C5C2D]/30 focus:border-[#4C5C2D] transition-colors" />
                    </div>

                    {{-- 17. Tarif Rawat Jalan --}}
                    <div>
                        <label class="block text-xs font-semibold text-neutral-700 dark:text-neutral-300 mb-1.5">
                            Tarif Rawat Jalan (Rp)
                        </label>
                        <input type="number" step="any" data-step="17" wire:model="ralan"
                            class="block w-full px-3.5 py-2.5 text-xs font-bold text-blue-600 dark:text-blue-400 rounded-lg border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 focus:ring-2 focus:ring-[#4C5C2D]/30 focus:border-[#4C5C2D] transition-colors" />
                    </div>

                    {{-- 18. Tarif Rnp Utama / BPJS --}}
                    <div>
                        <label class="block text-xs font-semibold text-neutral-700 dark:text-neutral-300 mb-1.5">
                            Tarif Rnp Utama / BPJS (Rp)
                        </label>
                        <input type="number" step="any" data-step="18" wire:model="utama"
                            class="block w-full px-3.5 py-2.5 text-xs font-bold text-emerald-600 dark:text-emerald-400 rounded-lg border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 focus:ring-2 focus:ring-[#4C5C2D]/30 focus:border-[#4C5C2D] transition-colors" />
                    </div>

                    {{-- 19. Apotek Luar --}}
                    <div>
                        <label class="block text-xs font-semibold text-neutral-700 dark:text-neutral-300 mb-1.5">
                            Tarif Apotek Luar (Rp)
                        </label>
                        <input type="number" step="any" data-step="19" wire:model="beliluar"
                            class="block w-full px-3.5 py-2.5 text-xs font-bold rounded-lg border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-neutral-900 dark:text-neutral-100 focus:ring-2 focus:ring-[#4C5C2D]/30 focus:border-[#4C5C2D] transition-colors" />
                    </div>

                    {{-- 20. Jual Bebas --}}
                    <div>
                        <label class="block text-xs font-semibold text-neutral-700 dark:text-neutral-300 mb-1.5">
                            Tarif Jual Bebas / OTC (Rp)
                        </label>
                        <input type="number" step="any" data-step="20" wire:model="jualbebas"
                            class="block w-full px-3.5 py-2.5 text-xs font-bold rounded-lg border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-neutral-900 dark:text-neutral-100 focus:ring-2 focus:ring-[#4C5C2D]/30 focus:border-[#4C5C2D] transition-colors" />
                    </div>

                    {{-- 21. Tarif Karyawan --}}
                    <div class="lg:col-span-2">
                        <label class="block text-xs font-semibold text-neutral-700 dark:text-neutral-300 mb-1.5">
                            Tarif Karyawan (Rp)
                        </label>
                        <input type="number" step="any" data-step="21" wire:model="karyawan"
                            class="block w-full px-3.5 py-2.5 text-xs font-bold rounded-lg border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-neutral-900 dark:text-neutral-100 focus:ring-2 focus:ring-[#4C5C2D]/30 focus:border-[#4C5C2D] transition-colors" />
                    </div>
                </div>
            </div>

            {{-- SEKSI 4: MATRIKS TARIF RAWAT INAP BERJENJANG --}}
            <div class="bg-white dark:bg-neutral-800 rounded-xl border border-neutral-200 dark:border-neutral-700 p-6 shadow-sm flex flex-col gap-5">
                <div class="flex items-center justify-between border-b border-neutral-200 dark:border-neutral-700 pb-3">
                    <div class="flex items-center gap-2.5">
                        <span class="w-6 h-6 rounded-full bg-[#4C5C2D] text-white text-xs font-bold flex items-center justify-center">4</span>
                        <h2 class="text-sm font-bold uppercase tracking-wider text-neutral-800 dark:text-neutral-100">
                            Matriks Tarif Rawat Inap (Kelas Kamar)
                        </h2>
                    </div>
                    <span class="text-xs text-neutral-400 font-mono">Step 22 - 26</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
                    {{-- 22. Kelas 1 --}}
                    <div>
                        <label class="block text-xs font-semibold text-neutral-700 dark:text-neutral-300 mb-1.5">
                            Tarif Kelas 1 (Rp)
                        </label>
                        <input type="number" step="any" data-step="22" wire:model="kelas1"
                            class="block w-full px-3.5 py-2.5 text-xs font-bold rounded-lg border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-neutral-900 dark:text-neutral-100 focus:ring-2 focus:ring-[#4C5C2D]/30 focus:border-[#4C5C2D] transition-colors" />
                    </div>

                    {{-- 23. Kelas 2 --}}
                    <div>
                        <label class="block text-xs font-semibold text-neutral-700 dark:text-neutral-300 mb-1.5">
                            Tarif Kelas 2 (Rp)
                        </label>
                        <input type="number" step="any" data-step="23" wire:model="kelas2"
                            class="block w-full px-3.5 py-2.5 text-xs font-bold rounded-lg border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-neutral-900 dark:text-neutral-100 focus:ring-2 focus:ring-[#4C5C2D]/30 focus:border-[#4C5C2D] transition-colors" />
                    </div>

                    {{-- 24. Kelas 3 --}}
                    <div>
                        <label class="block text-xs font-semibold text-neutral-700 dark:text-neutral-300 mb-1.5">
                            Tarif Kelas 3 (Rp)
                        </label>
                        <input type="number" step="any" data-step="24" wire:model="kelas3"
                            class="block w-full px-3.5 py-2.5 text-xs font-bold rounded-lg border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-neutral-900 dark:text-neutral-100 focus:ring-2 focus:ring-[#4C5C2D]/30 focus:border-[#4C5C2D] transition-colors" />
                    </div>

                    {{-- 25. Kelas VIP --}}
                    <div>
                        <label class="block text-xs font-semibold text-neutral-700 dark:text-neutral-300 mb-1.5">
                            Tarif Kelas VIP (Rp)
                        </label>
                        <input type="number" step="any" data-step="25" wire:model="vip"
                            class="block w-full px-3.5 py-2.5 text-xs font-bold rounded-lg border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-neutral-900 dark:text-neutral-100 focus:ring-2 focus:ring-[#4C5C2D]/30 focus:border-[#4C5C2D] transition-colors" />
                    </div>

                    {{-- 26. Kelas VVIP --}}
                    <div>
                        <label class="block text-xs font-semibold text-neutral-700 dark:text-neutral-300 mb-1.5">
                            Tarif Kelas VVIP (Rp)
                        </label>
                        <input type="number" step="any" data-step="26" wire:model="vvip"
                            class="block w-full px-3.5 py-2.5 text-xs font-bold rounded-lg border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-neutral-900 dark:text-neutral-100 focus:ring-2 focus:ring-[#4C5C2D]/30 focus:border-[#4C5C2D] transition-colors" />
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
