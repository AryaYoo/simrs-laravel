<div class="flex flex-col gap-6 pb-8 max-w-5xl mx-auto">

    {{-- Header / Breadcrumb --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('modul.pasien.index') }}" wire:navigate
           class="flex items-center justify-center w-8 h-8 rounded-lg transition-colors hover:bg-neutral-100 dark:hover:bg-neutral-700">
            <flux:icon name="chevron-left" class="w-5 h-5 text-neutral-500" />
        </a>
        <div>
            <nav class="text-xs text-neutral-400 mb-0.5">
                <a href="{{ route('modul.index') }}" wire:navigate class="hover:underline">Modul</a>
                <span class="mx-1">/</span>
                <a href="{{ route('modul.pasien.index') }}" wire:navigate class="hover:underline">Pasien</a>
                <span class="mx-1">/</span>
                <span>Detail</span>
            </nav>
            <h1 class="text-lg font-bold text-neutral-800 dark:text-neutral-100">Detail Rekam Medis Pasien</h1>
        </div>
    </div>

    {{-- Content Layout --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
        
        {{-- Left Column: Identity & Contact (Wider) --}}
        <div class="lg:col-span-2 flex flex-col gap-6">
            {{-- Personal Identity Card --}}
            <div class="bg-white dark:bg-neutral-800 rounded-xl border border-neutral-200 dark:border-neutral-700 overflow-hidden">
                <div class="px-5 py-4 border-b border-neutral-100 dark:border-neutral-700 bg-neutral-50/50 dark:bg-neutral-800/50 flex items-center gap-2">
                    <flux:icon name="identification" class="w-5 h-5 text-[#4C5C2D]" />
                    <h2 class="font-semibold text-neutral-800 dark:text-neutral-100 text-[15px]">Informasi Identitas</h2>
                </div>
                <div class="p-5">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-y-4 gap-x-6">
                        @include('partials._field', ['label' => 'No. Rekam Medis', 'value' => $pasien->no_rkm_medis, 'bold' => true])
                        @include('partials._field', ['label' => 'Nama Pasien', 'value' => $pasien->nm_pasien, 'bold' => true])
                        @include('partials._field', ['label' => 'No. SIM/KTP', 'value' => $pasien->no_ktp])
                        @include('partials._field', ['label' => 'Jenis Kelamin', 'value' => $pasien->jk === 'L' ? 'Laki-Laki' : ($pasien->jk === 'P' ? 'Perempuan' : $pasien->jk)])
                        @include('partials._field', ['label' => 'Tempat, Tanggal Lahir', 'value' => $pasien->tmp_lahir . ', ' . $pasien->tgl_lahir])
                        @include('partials._field', ['label' => 'Umur', 'value' => $pasien->umur])
                        @include('partials._field', ['label' => 'Nama Ibu', 'value' => $pasien->nm_ibu])
                        @include('partials._field', ['label' => 'Golongan Darah', 'value' => $pasien->gol_darah])
                        @include('partials._field', ['label' => 'Status Nikah', 'value' => $pasien->stts_nikah])
                        @include('partials._field', ['label' => 'Agama', 'value' => $pasien->agama])
                        @include('partials._field', ['label' => 'Pekerjaan', 'value' => $pasien->pekerjaan])
                        @include('partials._field', ['label' => 'Pendidikan', 'value' => $pasien->pnd])
                    </div>
                </div>
            </div>

            {{-- Address and Contact Card --}}
            <div class="bg-white dark:bg-neutral-800 rounded-xl border border-neutral-200 dark:border-neutral-700 overflow-hidden">
                <div class="px-5 py-4 border-b border-neutral-100 dark:border-neutral-700 bg-neutral-50/50 dark:bg-neutral-800/50 flex items-center gap-2">
                    <flux:icon name="map-pin" class="w-5 h-5 text-[#4C5C2D]" />
                    <h2 class="font-semibold text-neutral-800 dark:text-neutral-100 text-[15px]">Alamat & Kontak</h2>
                </div>
                <div class="p-5">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-y-4 gap-x-6">
                        @include('partials._field', ['label' => 'Alamat', 'value' => $pasien->alamat, 'colspan' => 'col-span-full'])
                        @include('partials._field', ['label' => 'Kelurahan', 'value' => $pasien->kelurahan->nm_kel ?? '-'])
                        @include('partials._field', ['label' => 'Kecamatan', 'value' => $pasien->kecamatan->nm_kec ?? '-'])
                        @include('partials._field', ['label' => 'Kabupaten', 'value' => $pasien->kabupaten->nm_kab ?? '-'])
                        @include('partials._field', ['label' => 'No. Telepon / HP', 'value' => $pasien->no_telp])
                    </div>
                </div>
            </div>
            
            {{-- Responsible Person / Penanggung Jawab --}}
            <div class="bg-white dark:bg-neutral-800 rounded-xl border border-neutral-200 dark:border-neutral-700 overflow-hidden">
                <div class="px-5 py-4 border-b border-neutral-100 dark:border-neutral-700 bg-neutral-50/50 dark:bg-neutral-800/50 flex items-center gap-2">
                    <flux:icon name="users" class="w-5 h-5 text-[#4C5C2D]" />
                    <h2 class="font-semibold text-neutral-800 dark:text-neutral-100 text-[15px]">Data Penanggung Jawab</h2>
                </div>
                <div class="p-5">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-y-4 gap-x-6">
                        @include('partials._field', ['label' => 'Status Hubungan', 'value' => $pasien->keluarga])
                        @include('partials._field', ['label' => 'Nama Penanggung Jawab', 'value' => $pasien->namakeluarga])
                        @include('partials._field', ['label' => 'Pekerjaan', 'value' => $pasien->pekerjaanpj])
                        @include('partials._field', ['label' => 'Alamat', 'value' => $pasien->alamatpj, 'colspan' => 'col-span-full'])
                        @include('partials._field', ['label' => 'Kelurahan', 'value' => $pasien->kelurahanPj->nm_kel ?? '-'])
                        @include('partials._field', ['label' => 'Kecamatan', 'value' => $pasien->kecamatanPj->nm_kec ?? '-'])
                        @include('partials._field', ['label' => 'Kabupaten', 'value' => $pasien->kabupatenPj->nm_kab ?? '-'])
                    </div>
                </div>
            </div>
            
        </div>

        {{-- Right Column: Admin & Extra Info --}}
        <div class="flex flex-col gap-6">

            {{-- Insurance / Payment Status --}}
            <div class="bg-white dark:bg-neutral-800 rounded-xl border border-neutral-200 dark:border-neutral-700 overflow-hidden">
                <div class="px-5 py-4 border-b border-neutral-100 dark:border-neutral-700 bg-neutral-50/50 dark:bg-neutral-800/50 flex items-center gap-2">
                    <flux:icon name="credit-card" class="w-5 h-5 text-[#4C5C2D]" />
                    <h2 class="font-semibold text-neutral-800 dark:text-neutral-100 text-[15px]">Asuransi & Pelayanan</h2>
                </div>
                <div class="p-5">
                    <div class="flex flex-col gap-4">
                        @include('partials._field', ['label' => 'Asuransi/Askes', 'value' => $pasien->penjab->png_jawab ?? '-'])
                        @include('partials._field', ['label' => 'No. Peserta', 'value' => $pasien->no_peserta])
                        <div class="w-full h-px bg-neutral-100 dark:bg-neutral-700 my-1"></div>
                        @include('partials._field', ['label' => 'Tanggal Daftar Master', 'value' => $pasien->tgl_daftar])
                    </div>
                </div>
            </div>

            {{-- Additional Details --}}
            <div class="bg-white dark:bg-neutral-800 rounded-xl border border-neutral-200 dark:border-neutral-700 overflow-hidden">
                <div class="px-5 py-4 border-b border-neutral-100 dark:border-neutral-700 bg-neutral-50/50 dark:bg-neutral-800/50 flex items-center gap-2">
                    <flux:icon name="clipboard-document-list" class="w-5 h-5 text-[#4C5C2D]" />
                    <h2 class="font-semibold text-neutral-800 dark:text-neutral-100 text-[15px]">Informasi Tambahan</h2>
                </div>
                <div class="p-5">
                    <div class="flex flex-col gap-4">
                        @include('partials._field', ['label' => 'Suku/Bangsa', 'value' => $pasien->sukuBangsa->nama_suku_bangsa ?? '-'])
                        @include('partials._field', ['label' => 'Bahasa', 'value' => $pasien->bahasa->nama_bahasa ?? '-'])
                        @include('partials._field', ['label' => 'Instansi/Perusahaan', 'value' => $pasien->perusahaan->nama_perusahaan ?? '-'])
                        @include('partials._field', ['label' => 'NIP', 'value' => $pasien->nip])
                        @include('partials._field', ['label' => 'Cacat Fisik', 'value' => $pasien->cacatFisik->nama_cacat ?? '-'])
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
