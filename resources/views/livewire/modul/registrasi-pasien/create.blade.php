<div class="flex flex-col gap-6 pb-8">
    <style>
        /* Custom styling untuk segmented radio status pasien */
        .status-radio {
            --color-accent: #84a98c !important;
            --color-accent-content: white !important;
        }
        .status-radio [data-flux-radio-segmented-indicator] {
            background-color: #84a98c !important;
            border-color: #84a98c !important;
        }
    </style>
    {{-- Header / Breadcrumb --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('modul.registrasi-pasien.index') }}" wire:navigate
           class="flex items-center justify-center w-8 h-8 rounded-lg transition-colors hover:bg-neutral-100 dark:hover:bg-neutral-700">
            <flux:icon name="chevron-left" class="w-5 h-5 text-neutral-500" />
        </a>
        <div>
            <nav class="text-xs text-neutral-400 mb-0.5">
                <a href="{{ route('modul.index') }}" wire:navigate class="hover:underline">Modul</a>
                <span class="mx-1">/</span>
                <a href="{{ route('modul.registrasi-pasien.index') }}" wire:navigate class="hover:underline">Registrasi</a>
                <span class="mx-1">/</span>
                <span>Baru</span>
            </nav>
            <h1 class="text-xl font-bold text-neutral-800 dark:text-neutral-100">Registrasi Pasien Baru</h1>
        </div>
    </div>

    <form wire:submit="save" class="bg-white dark:bg-neutral-800 rounded-2xl border border-neutral-200 dark:border-neutral-700 shadow-sm overflow-hidden">
        {{-- Custom Accent Bar --}}
        <div class="h-1.5 bg-[#4C5C2D]"></div>

        <div class="p-6 space-y-8">
            {{-- SEKSI 1: DATA REGISTRASI --}}
            <section>
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-1 h-5 bg-[#4C5C2D] rounded-full"></div>
                    <h2 class="text-sm font-bold text-neutral-800 dark:text-neutral-200 uppercase tracking-wider">Data Utama Registrasi</h2>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    {{-- No. Rawat --}}
                    <div class="flex flex-col gap-1.5">
                        <div class="flex items-center justify-between">
                            <label class="text-xs font-bold text-neutral-500 uppercase tracking-tight">No. Rawat</label>
                            <flux:checkbox wire:model.live="auto_rawat" size="xs" label="Auto" />
                        </div>
                        <flux:input wire:model="no_rawat" :disabled="$auto_rawat" placeholder="Otomatis" class="font-mono" />
                    </div>

                    {{-- No. Reg --}}
                    <div class="flex flex-col gap-1.5">
                        <div class="flex items-center justify-between">
                            <label class="text-xs font-bold text-neutral-500 uppercase tracking-tight">No. Reg</label>
                            <flux:checkbox wire:model.live="auto_reg" size="xs" label="Auto" />
                        </div>
                        <flux:input wire:model="no_reg" :disabled="$auto_reg" placeholder="O01" class="font-mono text-center" />
                    </div>

                    {{-- Tanggal Registrasi --}}
                    <div class="flex flex-col gap-1.5">
                        <label class="text-xs font-bold text-neutral-500 uppercase tracking-tight">Tanggal Registrasi</label>
                        <flux:input type="date" wire:model.live="tgl_registrasi" />
                    </div>

                    {{-- Jam Registrasi --}}
                    <div class="flex flex-col gap-1.5">
                        <div class="flex items-center justify-between">
                            <label class="text-xs font-bold text-neutral-500 uppercase tracking-tight">Jam Registrasi</label>
                            <flux:checkbox wire:model.live="auto_waktu" size="xs" label="Realtime" />
                        </div>
                        <flux:input type="time" wire:model="jam_reg" :disabled="$auto_waktu" />
                    </div>
                </div>
            </section>

            <hr class="border-neutral-100 dark:border-neutral-800">

            {{-- SEKSI 2: TUJUAN KUNJUNGAN & PASIEN --}}
            <section>
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-1 h-5 bg-sky-500 rounded-full"></div>
                    <h2 class="text-sm font-bold text-neutral-800 dark:text-neutral-200 uppercase tracking-wider">Tujuan & Identitas Pasien</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
                    {{-- Dokter --}}
                    <div class="md:col-span-6 flex flex-col gap-1.5">
                        <label class="text-xs font-bold text-neutral-500 uppercase tracking-tight">Dokter Dituju</label>
                        <div class="flex gap-2">
                            <flux:input wire:model="nm_dokter" class="flex-1" placeholder="Pilih Dokter..." readonly />
                            <button type="button" wire:click="openDokterModal" class="p-2 bg-neutral-100 dark:bg-neutral-700 rounded-lg hover:bg-neutral-200 transition-colors border border-neutral-200 dark:border-neutral-600">
                                <flux:icon name="paper-clip" class="w-4 h-4 text-neutral-500" />
                            </button>
                        </div>
                        <p class="text-[10px] text-neutral-400 italic">Kode: {{ $kd_dokter ?: '-' }}</p>
                    </div>

                    {{-- Unit/Poli --}}
                    <div class="md:col-span-6 flex flex-col gap-1.5">
                        <label class="text-xs font-bold text-neutral-500 uppercase tracking-tight">Unit / Poli</label>
                        <div class="flex gap-2">
                            <flux:input wire:model="nm_poli" class="flex-1" placeholder="Pilih Unit..." readonly />
                            <button type="button" wire:click="openPoliModal" class="p-2 bg-neutral-100 dark:bg-neutral-700 rounded-lg hover:bg-neutral-200 transition-colors border border-neutral-200 dark:border-neutral-600">
                                <flux:icon name="paper-clip" class="w-4 h-4 text-neutral-500" />
                            </button>
                        </div>
                        <p class="text-[10px] text-neutral-400 italic">Kode: {{ $kd_poli ?: '-' }} | Biaya: Rp {{ number_format($biaya_reg, 0, ',', '.') }}</p>
                    </div>

                    {{-- No. Rekam Medis --}}
                    <div class="md:col-span-4 flex flex-col gap-1.5">
                        <label class="text-xs font-bold text-neutral-500 uppercase tracking-tight">No. Rekam Medis</label>
                        <div class="flex gap-2">
                            <flux:input wire:model="no_rkm_medis" class="flex-1 font-mono" placeholder="Masukkan No. RM" />
                            <button type="button" wire:click="openPasienModal" class="p-2 bg-neutral-100 dark:bg-neutral-700 rounded-lg hover:bg-neutral-200 transition-colors border border-neutral-200 dark:border-neutral-600">
                                <flux:icon name="magnifying-glass" class="w-4 h-4 text-neutral-500" />
                            </button>
                        </div>
                    </div>

                    {{-- Nama Pasien --}}
                    <div class="md:col-span-5 flex flex-col gap-1.5">
                        <label class="text-xs font-bold text-neutral-500 uppercase tracking-tight">Nama Pasien</label>
                        <flux:input wire:model="nm_pasien" placeholder="Otomatis terisi" readonly />
                    </div>

                    {{-- Status Pasien --}}
                    <div class="md:col-span-3 flex flex-col gap-1.5">
                        <label class="text-xs font-bold text-neutral-500 uppercase tracking-tight">Status Pasien</label>
                        <flux:radio.group wire:model="status" variant="segmented" class="status-radio">
                            <flux:radio value="Baru" label="Baru" />
                            <flux:radio value="Lama" label="Lama" />
                        </flux:radio.group>
                    </div>
                </div>
            </section>

            <hr class="border-neutral-100 dark:border-neutral-800">

            {{-- SEKSI 3: DATA PENANGGUNG JAWAB & PEMBAYARAN --}}
            <section>
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-1 h-5 bg-rose-500 rounded-full"></div>
                    <h2 class="text-sm font-bold text-neutral-800 dark:text-neutral-200 uppercase tracking-wider">Penanggung Jawab & Cara Bayar</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <flux:input wire:model="p_jawab" label="Penanggung Jawab (PJ)" placeholder="Nama penanggung jawab" />
                    <flux:input wire:model="hubunganpj" label="Hubungan PJ" placeholder="mis: Orang Tua, Istri, Suami" />
                    <flux:input wire:model="almt_pj" label="Alamat PJ" placeholder="Alamat lengkap penanggung jawab" />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-12 gap-6 mt-6">
                    {{-- Jenis Bayar --}}
                    <div class="md:col-span-4 flex flex-col gap-1.5">
                        <label class="text-xs font-bold text-neutral-500 uppercase tracking-tight">Jenis Bayar / Asuransi</label>
                        <div class="flex gap-2">
                            <flux:input wire:model="png_jawab" class="flex-1" placeholder="Pilih Asuransi..." readonly />
                            <button type="button" wire:click="openPenjabModal" class="p-2 bg-neutral-100 dark:bg-neutral-700 rounded-lg hover:bg-neutral-200 transition-colors border border-neutral-200 dark:border-neutral-600">
                                <flux:icon name="paper-clip" class="w-4 h-4 text-neutral-500" />
                            </button>
                        </div>
                    </div>

                    {{-- No. KA --}}
                    <div class="md:col-span-4 flex flex-col gap-1.5">
                        <label class="text-xs font-bold text-neutral-500 uppercase tracking-tight">No. Kartu / BPJS</label>
                        <flux:input wire:model="no_ka" placeholder="Masukkan nomor kartu jika ada" />
                    </div>

                    {{-- Asal Rujukan --}}
                    <div class="md:col-span-4 flex flex-col gap-1.5">
                        <label class="text-xs font-bold text-neutral-500 uppercase tracking-tight">Asal Rujukan</label>
                        <div class="flex gap-2">
                            <flux:input wire:model="perujuk" class="flex-1" placeholder="Pilih Asal Rujukan..." />
                            <button type="button" wire:click="openPerujukModal" class="p-2 bg-neutral-100 dark:bg-neutral-700 rounded-lg hover:bg-neutral-200 transition-colors border border-neutral-200 dark:border-neutral-600">
                                <flux:icon name="paper-clip" class="w-4 h-4 text-neutral-500" />
                            </button>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <div class="px-6 py-4 bg-neutral-50 dark:bg-neutral-800/50 border-t border-neutral-100 dark:border-neutral-700 flex justify-end gap-3">
            <flux:button href="{{ route('modul.registrasi-pasien.index') }}" wire:navigate variant="ghost">Batal</flux:button>
            <flux:button type="submit" variant="primary" icon="check" class="bg-[#4C5C2D] hover:bg-[#3d4a24]">Simpan Registrasi</flux:button>
        </div>
    </form>

    {{-- ============================== MODALS LOOKUP ============================== --}}

    {{-- Modal Dokter --}}
    <flux:modal wire:model="isDokterModalOpen" variant="flyout" class="space-y-6">
        <div class="flex flex-col h-full">
            <div>
                <flux:heading size="lg">Pilih Dokter Dituju</flux:heading>
                <flux:subheading>Daftar dokter yang aktif saat ini.</flux:subheading>
            </div>
            
            <flux:input wire:model.live.debounce.300ms="searchDokter" icon="magnifying-glass" placeholder="Cari nama dokter..." class="my-4" />

            <div class="flex-1 overflow-y-auto border border-neutral-200 dark:border-neutral-700 rounded-xl">
                <table class="w-full text-sm text-left">
                    <thead class="bg-neutral-50 dark:bg-neutral-800 text-neutral-500 uppercase text-[10px] font-bold border-b border-neutral-100 dark:border-neutral-700">
                        <tr>
                            <th class="px-4 py-2">Nama Dokter</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-100 dark:divide-neutral-700">
                        @foreach($listDokter as $dok)
                            <tr wire:click="selectDokter('{{ $dok->kd_dokter }}', '{{ addslashes($dok->nm_dokter) }}')" class="hover:bg-neutral-50 dark:hover:bg-neutral-700/50 cursor-pointer transition-colors hover:text-[#4C5C2D]">
                                <td class="px-4 py-3 font-medium">{{ $dok->nm_dokter }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </flux:modal>

    {{-- Modal Poli --}}
    <flux:modal wire:model="isPoliModalOpen" variant="flyout" class="space-y-6">
        <div class="flex flex-col h-full">
            <div>
                <flux:heading size="lg">Pilih Unit / Poliklinik</flux:heading>
                <flux:subheading>Unit layanan rawat jalan atau IGD.</flux:subheading>
            </div>
            
            <flux:input wire:model.live.debounce.300ms="searchPoli" icon="magnifying-glass" placeholder="Cari nama poli..." class="my-4" />

            <div class="flex-1 overflow-y-auto border border-neutral-200 dark:border-neutral-700 rounded-xl">
                <table class="w-full text-sm text-left">
                    <thead class="bg-neutral-50 dark:bg-neutral-800 text-neutral-500 uppercase text-[10px] font-bold border-b border-neutral-100 dark:border-neutral-700">
                        <tr>
                            <th class="px-4 py-2">Nama Unit</th>
                            <th class="px-4 py-2 text-right">Biaya</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-100 dark:divide-neutral-700">
                        @foreach($listPoli as $poli)
                            <tr wire:click="selectPoli('{{ $poli->kd_poli }}', '{{ addslashes($poli->nm_poli) }}', {{ $poli->registrasi }})" class="hover:bg-neutral-50 dark:hover:bg-neutral-700/50 cursor-pointer transition-colors hover:text-[#4C5C2D]">
                                <td class="px-4 py-3 font-medium">{{ $poli->nm_poli }}</td>
                                <td class="px-4 py-3 text-right font-mono">{{ number_format($poli->registrasi, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </flux:modal>

    {{-- Modal Pasien --}}
    <flux:modal wire:model="isPasienModalOpen" variant="flyout" class="max-w-2xl">
        <div class="flex flex-col h-full">
            <div class="mb-4">
                <flux:heading size="lg">Cari Data Pasien</flux:heading>
                <flux:subheading>Cari berdasarkan Nama atau Nomor Rekam Medis.</flux:subheading>
            </div>
            
            <flux:input wire:model.live.debounce.300ms="searchPasien" icon="magnifying-glass" placeholder="Ketik nama atau No RM..." class="mb-6" />

            <div class="flex-1 overflow-y-auto border border-neutral-200 dark:border-neutral-700 rounded-xl">
                <table class="w-full text-xs text-left">
                    <thead class="bg-neutral-50 dark:bg-neutral-800 text-neutral-500 uppercase font-bold border-b border-neutral-100 dark:border-neutral-700 sticky top-0">
                        <tr>
                            <th class="px-4 py-2">No. RM</th>
                            <th class="px-4 py-2">Nama Pasien</th>
                            <th class="px-4 py-2">Tgl. Lahir</th>
                            <th class="px-4 py-2">Alamat</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-100 dark:divide-neutral-700">
                        @foreach($listPasien as $p)
                            <tr wire:click="selectPasien('{{ $p->no_rkm_medis }}', '{{ addslashes($p->nm_pasien) }}', '{{ addslashes($p->namakeluarga) }}', '{{ $p->keluarga }}', '{{ addslashes($p->alamatpj) }}', '{{ $p->kd_pj }}', '{{ addslashes($p->penjab->png_jawab ?? '-') }}')" 
                                class="hover:bg-[#4C5C2D]/10 cursor-pointer transition-all">
                                <td class="px-4 py-3 font-mono font-bold">{{ $p->no_rkm_medis }}</td>
                                <td class="px-4 py-3 font-bold text-neutral-800 dark:text-neutral-200 uppercase">{{ $p->nm_pasien }}</td>
                                <td class="px-4 py-3">{{ \Carbon\Carbon::parse($p->tgl_lahir)->format('d-m-Y') }}</td>
                                <td class="px-4 py-3 truncate max-w-[200px]">{{ $p->alamat }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="mt-4 p-4 bg-amber-50 dark:bg-amber-950/20 rounded-lg flex items-center justify-between">
                <p class="text-[10px] text-amber-700 dark:text-amber-400">Menampilkan 50 data pasien pertama. Persingkat pencarian jika tidak ditemukan.</p>
                <flux:button href="{{ route('modul.registrasi-pasien.new') }}" wire:navigate size="xs" variant="primary">Pasien Baru?</flux:button>
            </div>
        </div>
    </flux:modal>

    {{-- Modal Jenis Bayar --}}
    <flux:modal wire:model="isPenjabModalOpen" variant="flyout" class="space-y-6">
        <div class="flex flex-col h-full">
            <div>
                <flux:heading size="lg">Pilih Jenis Bayar</flux:heading>
                <flux:subheading>Asuransi, BPJS, atau Umum.</flux:subheading>
            </div>
            
            <flux:input wire:model.live.debounce.300ms="searchPenjab" icon="magnifying-glass" placeholder="Cari asuransi..." class="my-4" />

            <div class="flex-1 overflow-y-auto border border-neutral-200 dark:border-neutral-700 rounded-xl">
                <table class="w-full text-sm text-left">
                    <thead class="bg-neutral-50 dark:bg-neutral-800 text-neutral-500 uppercase text-[10px] font-bold border-b border-neutral-100 dark:border-neutral-700">
                        <tr>
                            <th class="px-4 py-2">Nama Asuransi / Penanggung Pasien</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-100 dark:divide-neutral-700">
                        @foreach($listPenjab as $pj)
                            <tr wire:click="selectPenjab('{{ $pj->kd_pj }}', '{{ addslashes($pj->png_jawab) }}')" class="hover:bg-neutral-50 dark:hover:bg-neutral-700/50 cursor-pointer transition-colors hover:text-[#4C5C2D]">
                                <td class="px-4 py-3 font-medium">{{ $pj->png_jawab }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </flux:modal>

    {{-- Modal Perujuk --}}
    <flux:modal wire:model="isPerujukModalOpen" variant="flyout" class="space-y-6">
        <div class="flex flex-col h-full">
            <div>
                <flux:heading size="lg">Daftar Perujuk</flux:heading>
                <flux:subheading>Instansi atau perujuk yang tersedia.</flux:subheading>
            </div>
            
            <flux:input wire:model.live.debounce.300ms="searchPerujuk" icon="magnifying-glass" placeholder="Cari perujuk..." class="my-4" />

            <div class="flex-1 overflow-y-auto border border-neutral-200 dark:border-neutral-700 rounded-xl">
                <table class="w-full text-sm text-left">
                    <thead class="bg-neutral-50 dark:bg-neutral-800 text-neutral-500 uppercase text-[10px] font-bold border-b border-neutral-100 dark:border-neutral-700">
                        <tr>
                            <th class="px-4 py-2">Asal Rujukan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-100 dark:divide-neutral-700">
                        @foreach($listPerujuk as $rujuk)
                            <tr wire:click="selectPerujuk('{{ addslashes($rujuk->perujuk) }}')" class="hover:bg-neutral-50 dark:hover:bg-neutral-700/50 cursor-pointer transition-colors hover:text-[#4C5C2D]">
                                <td class="px-4 py-3 font-medium">{{ $rujuk->perujuk }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </flux:modal>

</div>
