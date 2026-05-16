<div class="flex flex-col gap-6 pb-24" x-data="{ isSubmitting: false }">
    {{-- Sticky Header --}}
    <div class="sticky top-0 z-40 bg-white/80 dark:bg-neutral-900/80 backdrop-blur-md border-b border-neutral-200 dark:border-neutral-700 -mx-4 px-4 py-3 mb-2 flex items-center justify-between shadow-sm">
        <div class="flex items-center gap-3">
            <a href="{{ route('modul.rawat-jalan.sub-rawat-jalan.pengkajian-awal-keperawatan-igd', str_replace('/', '-', $regPeriksa->no_rawat)) }}" wire:navigate class="p-2 rounded-lg hover:bg-neutral-100 dark:hover:bg-neutral-800 transition-colors">
                <flux:icon name="chevron-left" class="w-5 h-5 text-neutral-500" />
            </a>
            <div>
                <h1 class="text-base font-bold text-neutral-800 dark:text-neutral-100">Pengkajian Awal Keperawatan IGD</h1>
                <p class="text-[10px] text-neutral-500 font-medium uppercase tracking-wider">{{ $regPeriksa->pasien->nm_pasien }} ({{ $regPeriksa->no_rawat }})</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <flux:button href="{{ route('modul.rawat-jalan.sub-rawat-jalan.pengkajian-awal-keperawatan-igd', str_replace('/', '-', $regPeriksa->no_rawat)) }}" wire:navigate variant="ghost" class="h-9 text-sm">Batal</flux:button>
            <flux:button wire:click="save" @click="isSubmitting = true" variant="primary" icon="check" class="bg-[#4C5C2D] hover:bg-[#3D4A24] h-9 px-6 text-sm">Simpan</flux:button>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-8 max-w-5xl mx-auto w-full">

        {{-- Section 1: Identitas --}}
        <div id="section-1" class="bg-white dark:bg-neutral-800 rounded-2xl border border-neutral-200 dark:border-neutral-700 shadow-sm overflow-visible">
            <div class="px-6 py-4 bg-neutral-50 dark:bg-neutral-900/50 rounded-t-2xl border-b border-neutral-200 dark:border-neutral-700 flex items-center gap-3">
                <div class="w-6 h-6 rounded-full bg-[#4C5C2D]/10 flex items-center justify-center"><span class="text-[#4C5C2D] font-bold text-xs">1</span></div>
                <h2 class="text-sm font-bold text-neutral-800 dark:text-neutral-100">Identitas & Informasi</h2>
            </div>
            <div class="p-6 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <flux:input label="No. Rawat" value="{{ $regPeriksa->no_rawat }}" disabled />
                    <flux:input label="Pasien" value="{{ $regPeriksa->pasien->nm_pasien }}" disabled />
                    <flux:input label="Tgl Lahir / JK" value="{{ $regPeriksa->pasien->tgl_lahir }} / {{ $regPeriksa->pasien->jk === 'L' ? 'Laki-laki' : 'Perempuan' }}" disabled />
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <flux:input wire:model="tanggal" type="datetime-local" label="Tanggal Pengkajian" required />
                    <flux:select wire:model="informasi" label="Informasi Didapat Dari">
                        <flux:select.option value="Autoanamnesis">Autoanamnesis</flux:select.option>
                        <flux:select.option value="Alloanamnesis">Alloanamnesis</flux:select.option>
                    </flux:select>
                </div>
                {{-- Petugas Search --}}
                <div class="relative">
                    <flux:input wire:model.live.debounce.300ms="petugasSearch" label="Cari Petugas (NIP/Nama)" placeholder="Ketik minimal 3 karakter..." icon="magnifying-glass" />
                    @if(!empty($petugasList))
                        <div class="absolute z-[60] left-0 right-0 mt-1 bg-white dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl shadow-xl max-h-48 overflow-y-auto">
                            @foreach($petugasList as $p)
                                <button type="button" wire:click="selectPetugas('{{ $p->nip }}', '{{ $p->nama }}')" class="w-full text-left px-4 py-3 hover:bg-neutral-50 dark:hover:bg-neutral-700 border-b last:border-0 transition-colors group">
                                    <div class="font-bold text-sm text-neutral-700 group-hover:text-[#4C5C2D]">{{ $p->nama }}</div>
                                    <div class="text-[10px] text-neutral-400 font-mono">{{ $p->nip }}</div>
                                </button>
                            @endforeach
                        </div>
                    @endif
                    @if($nip)
                        <div class="mt-2 p-3 bg-[#4C5C2D]/5 border border-[#4C5C2D]/20 rounded-xl flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <flux:icon name="user-circle" class="w-5 h-5 text-[#4C5C2D]" />
                                <div>
                                    <div class="text-xs font-bold text-[#4C5C2D]">{{ $nmPetugas }}</div>
                                    <div class="text-[10px] text-neutral-500 font-mono">{{ $nip }}</div>
                                </div>
                            </div>
                            <button wire:click="$set('nip', null)" class="text-neutral-400 hover:text-red-500 transition-colors"><flux:icon name="trash" size="xs" /></button>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Section 2: Riwayat Kesehatan --}}
        <div id="section-2" class="bg-white dark:bg-neutral-800 rounded-2xl border border-neutral-200 dark:border-neutral-700 shadow-sm overflow-hidden">
            <div class="px-6 py-4 bg-neutral-50 dark:bg-neutral-900/50 border-b border-neutral-200 dark:border-neutral-700 flex items-center gap-3">
                <div class="w-6 h-6 rounded-full bg-[#4C5C2D]/10 flex items-center justify-center"><span class="text-[#4C5C2D] font-bold text-xs">2</span></div>
                <h2 class="text-sm font-bold text-neutral-800 dark:text-neutral-100">Riwayat Kesehatan Pasien</h2>
            </div>
            <div class="p-6 space-y-6">
                <flux:textarea wire:model="keluhan_utama" label="Riwayat Penyakit Sekarang (Keluhan Utama)" rows="3" placeholder="Keluhan utama pasien..." />
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <flux:textarea wire:model="rpd" label="Riwayat Penyakit Dahulu" rows="3" />
                    <flux:textarea wire:model="rpo" label="Riwayat Penggunaan Obat" rows="3" />
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <flux:select wire:model="status_kehamilan" label="Status Kehamilan">
                        <flux:select.option value="Tidak Hamil">Tidak Hamil</flux:select.option>
                        <flux:select.option value="Hamil">Hamil</flux:select.option>
                    </flux:select>
                    <flux:input wire:model="hpht" label="HPHT" />
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <flux:input wire:model="para" label="Para" />
                    <flux:input wire:model="abortus" label="Abortus" />
                    <flux:input wire:model="gravida" label="Gravida" />
                </div>
            </div>
        </div>

        {{-- Section 3: Pemeriksaan Fisik --}}
        <div id="section-3" class="bg-white dark:bg-neutral-800 rounded-2xl border border-neutral-200 dark:border-neutral-700 shadow-sm overflow-hidden">
            <div class="px-6 py-4 bg-neutral-50 dark:bg-neutral-900/50 border-b border-neutral-200 dark:border-neutral-700 flex items-center gap-3">
                <div class="w-6 h-6 rounded-full bg-[#4C5C2D]/10 flex items-center justify-center"><span class="text-[#4C5C2D] font-bold text-xs">3</span></div>
                <h2 class="text-sm font-bold text-neutral-800 dark:text-neutral-100">Pemeriksaan Fisik</h2>
            </div>
            <div class="p-6 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <flux:select wire:model="tekanan" label="Tekanan Intrakranial">
                        @foreach(['TAK','Sakit Kepala','Muntah','Pusing','Bingung'] as $v)<flux:select.option value="{{ $v }}">{{ $v }}</flux:select.option>@endforeach
                    </flux:select>
                    <flux:select wire:model="pupil" label="Pupil">
                        @foreach(['Normal','Miosis','Isokor','Anisokor'] as $v)<flux:select.option value="{{ $v }}">{{ $v }}</flux:select.option>@endforeach
                    </flux:select>
                    <flux:select wire:model="neurosensorik" label="Neurosensorik / Muskuloskeletal">
                        @foreach(['TAK','Spasme Otot','Perubahan Sensorik','Perubahan Motorik','Perubahan Bentuk Ekstremitas','Penurunan Tingkat Kesadaran','Fraktur/Dislokasi','Luksasio','Kerusakan Jaringan/Luka'] as $v)<flux:select.option value="{{ $v }}">{{ $v }}</flux:select.option>@endforeach
                    </flux:select>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <flux:select wire:model="integumen" label="Integumen">
                        @foreach(['TAK','Luka Bakar','Luka Robek','Lecet','Luka Decubitus','Luka Gangren'] as $v)<flux:select.option value="{{ $v }}">{{ $v }}</flux:select.option>@endforeach
                    </flux:select>
                    <flux:select wire:model="turgor" label="Turgor Kulit">
                        @foreach(['Baik','Menurun'] as $v)<flux:select.option value="{{ $v }}">{{ $v }}</flux:select.option>@endforeach
                    </flux:select>
                    <flux:select wire:model="edema" label="Edema">
                        @foreach(['Tidak Ada','Ekstremitas','Seluruh Tubuh','Asites','Palpebrae'] as $v)<flux:select.option value="{{ $v }}">{{ $v }}</flux:select.option>@endforeach
                    </flux:select>
                    <flux:select wire:model="mukosa" label="Mukosa Mulut">
                        @foreach(['Lembab','Kering'] as $v)<flux:select.option value="{{ $v }}">{{ $v }}</flux:select.option>@endforeach
                    </flux:select>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <flux:select wire:model="perdarahan" label="Perdarahan">
                        @foreach(['Tidak Ada','Ada'] as $v)<flux:select.option value="{{ $v }}">{{ $v }}</flux:select.option>@endforeach
                    </flux:select>
                    <flux:input wire:model="jumlah_perdarahan" label="Jumlah (cc)" />
                    <flux:input wire:model="warna_perdarahan" label="Warna" />
                </div>
                <flux:select wire:model="intoksikasi" label="Intoksikasi">
                    @foreach(['Tidak Ada','Ada','Gigitan Binatang','Zat Kimia','Gas','Obat'] as $v)<flux:select.option value="{{ $v }}">{{ $v }}</flux:select.option>@endforeach
                </flux:select>
                {{-- Eliminasi --}}
                <h3 class="text-xs font-black uppercase tracking-widest text-[#4C5C2D] pt-2">Eliminasi</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <flux:input wire:model="bab" label="BAB Frekuensi" />
                    <flux:input wire:model="xbab" label="BAB X/" />
                    <flux:input wire:model="kbab" label="Konsistensi" />
                    <flux:input wire:model="wbab" label="Warna BAB" />
                </div>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <flux:input wire:model="bak" label="BAK Frekuensi" />
                    <flux:input wire:model="xbak" label="BAK X/" />
                    <flux:input wire:model="wbak" label="Warna BAK" />
                    <flux:input wire:model="lbak" label="Lain-lain" />
                </div>
            </div>
        </div>

        {{-- Section 4: Psikososial & Fungsional --}}
        <div id="section-4" class="bg-white dark:bg-neutral-800 rounded-2xl border border-neutral-200 dark:border-neutral-700 shadow-sm overflow-hidden">
            <div class="px-6 py-4 bg-neutral-50 dark:bg-neutral-900/50 border-b border-neutral-200 dark:border-neutral-700 flex items-center gap-3">
                <div class="w-6 h-6 rounded-full bg-[#4C5C2D]/10 flex items-center justify-center"><span class="text-[#4C5C2D] font-bold text-xs">4</span></div>
                <h2 class="text-sm font-bold text-neutral-800 dark:text-neutral-100">Riwayat Psikososial & Pengkajian Fungsi</h2>
            </div>
            <div class="p-6 space-y-6">
                <h3 class="text-xs font-black uppercase tracking-widest text-[#4C5C2D]">Psikologis - Sosial - Budaya</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <flux:select wire:model="psikologis" label="Kondisi Psikologis">
                        @foreach(['Tidak Ada Masalah','Marah','Takut','Depresi','Cepat Lelah','Cemas','Gelisah','Lain-lain'] as $v)<flux:select.option value="{{ $v }}">{{ $v }}</flux:select.option>@endforeach
                    </flux:select>
                    <flux:select wire:model="jiwa" label="Gangguan Jiwa Di Masa Lalu">
                        <flux:select.option value="Tidak">Tidak</flux:select.option>
                        <flux:select.option value="Ya">Ya</flux:select.option>
                    </flux:select>
                    <flux:select wire:model="perilaku" label="Adakah Perilaku">
                        @foreach(['Perilaku Kekerasan','Gangguan Efek','Gangguan Memori','Halusinasi','Kecenderungan Percobaan Bunuh Diri','Lainnya','-'] as $v)<flux:select.option value="{{ $v }}">{{ $v }}</flux:select.option>@endforeach
                    </flux:select>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <flux:input wire:model="dilaporkan" label="Dilaporkan Ke" />
                    <flux:input wire:model="sebutkan" label="Sebutkan" />
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <flux:select wire:model="hubungan" label="Hubungan Dengan Keluarga">
                        @foreach(['Harmonis','Kurang Harmonis','Tidak Harmonis','Konflik Besar'] as $v)<flux:select.option value="{{ $v }}">{{ $v }}</flux:select.option>@endforeach
                    </flux:select>
                    <flux:select wire:model="tinggal_dengan" label="Tinggal Dengan">
                        @foreach(['Sendiri','Orang Tua','Suami / Istri','Lainnya'] as $v)<flux:select.option value="{{ $v }}">{{ $v }}</flux:select.option>@endforeach
                    </flux:select>
                    <flux:input wire:model="ket_tinggal" label="Keterangan" />
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <flux:select wire:model="budaya" label="Kepercayaan / Budaya Khusus">
                        <flux:select.option value="Tidak Ada">Tidak Ada</flux:select.option>
                        <flux:select.option value="Ada">Ada</flux:select.option>
                    </flux:select>
                    <flux:input wire:model="ket_budaya" label="Keterangan Budaya" />
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <flux:select wire:model="pendidikan_pj" label="Pendidikan P.J.">
                        @foreach(['-','TS','TK','SD','SMP','SMA','SLTA/SEDERAJAT','D1','D2','D3','D4','S1','S2','S3'] as $v)<flux:select.option value="{{ $v }}">{{ $v }}</flux:select.option>@endforeach
                    </flux:select>
                    <flux:input wire:model="ket_pendidikan_pj" label="Keterangan Pendidikan" />
                    <flux:select wire:model="edukasi" label="Edukasi Diberikan Kepada">
                        <flux:select.option value="Pasien">Pasien</flux:select.option>
                        <flux:select.option value="Keluarga">Keluarga</flux:select.option>
                    </flux:select>
                </div>
                <flux:input wire:model="ket_edukasi" label="Keterangan Edukasi" />

                <h3 class="text-xs font-black uppercase tracking-widest text-[#4C5C2D] pt-4">Pengkajian Fungsi</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <flux:select wire:model="kemampuan" label="Kemampuan Aktifitas">
                        @foreach(['Mandiri','Bantuan Minimal','Bantuan Sebagian','Ketergantungan Total'] as $v)<flux:select.option value="{{ $v }}">{{ $v }}</flux:select.option>@endforeach
                    </flux:select>
                    <flux:select wire:model="aktifitas" label="Aktifitas">
                        @foreach(['Tirah Baring','Duduk','Berjalan'] as $v)<flux:select.option value="{{ $v }}">{{ $v }}</flux:select.option>@endforeach
                    </flux:select>
                    <flux:select wire:model="alat_bantu" label="Alat Bantu">
                        <flux:select.option value="Tidak">Tidak</flux:select.option>
                        <flux:select.option value="Ya">Ya</flux:select.option>
                    </flux:select>
                </div>
                <flux:input wire:model="ket_bantu" label="Keterangan Alat Bantu" />
            </div>
        </div>

        {{-- Section 5: Skala Nyeri --}}
        <div id="section-5" class="bg-white dark:bg-neutral-800 rounded-2xl border border-neutral-200 dark:border-neutral-700 shadow-sm overflow-hidden">
            <div class="px-6 py-4 bg-neutral-50 dark:bg-neutral-900/50 border-b border-neutral-200 dark:border-neutral-700 flex items-center gap-3">
                <div class="w-6 h-6 rounded-full bg-[#4C5C2D]/10 flex items-center justify-center"><span class="text-[#4C5C2D] font-bold text-xs">5</span></div>
                <h2 class="text-sm font-bold text-neutral-800 dark:text-neutral-100">Skala Nyeri</h2>
            </div>
            <div class="p-6 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <flux:select wire:model="nyeri" label="Nyeri">
                        @foreach(['Tidak Ada Nyeri','Nyeri Akut','Nyeri Kronis'] as $v)<flux:select.option value="{{ $v }}">{{ $v }}</flux:select.option>@endforeach
                    </flux:select>
                    <flux:select wire:model="provokes" label="Penyebab (Provokes)">
                        @foreach(['Proses Penyakit','Benturan','Lain-lain'] as $v)<flux:select.option value="{{ $v }}">{{ $v }}</flux:select.option>@endforeach
                    </flux:select>
                    <flux:input wire:model="ket_provokes" label="Keterangan Penyebab" />
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <flux:select wire:model="quality" label="Kualitas">
                        @foreach(['Seperti Tertusuk','Berdenyut','Teriris','Tertindih','Tertiban','Lain-lain'] as $v)<flux:select.option value="{{ $v }}">{{ $v }}</flux:select.option>@endforeach
                    </flux:select>
                    <flux:input wire:model="ket_quality" label="Keterangan Kualitas" />
                    <flux:input wire:model="lokasi" label="Lokasi Nyeri" />
                </div>
                <div class="mb-4">
                    <h3 class="text-xs font-black uppercase tracking-widest text-[#4C5C2D] mb-3">Panduan Skala Nyeri</h3>
                    <div class="bg-neutral-50 dark:bg-neutral-900/50 p-4 rounded-xl border border-neutral-200 dark:border-neutral-700 flex justify-center">
                        <img src="{{ asset('img/skala-nyeri.png') }}" alt="Skala Nyeri" class="max-w-full h-auto rounded-lg" style="max-height: 250px;">
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <flux:select wire:model="menyebar" label="Menyebar">
                        <flux:select.option value="Tidak">Tidak</flux:select.option>
                        <flux:select.option value="Ya">Ya</flux:select.option>
                    </flux:select>
                    <flux:select wire:model="skala_nyeri" label="Skala Nyeri (0-10)">
                        @for($i = 0; $i <= 10; $i++)<flux:select.option value="{{ $i }}">{{ $i }}</flux:select.option>@endfor
                    </flux:select>
                    <flux:input wire:model="durasi" label="Durasi (Menit)" />
                    <flux:select wire:model="nyeri_hilang" label="Nyeri Hilang Bila">
                        @foreach(['Istirahat','Medengar Musik','Minum Obat'] as $v)<flux:select.option value="{{ $v }}">{{ $v }}</flux:select.option>@endforeach
                    </flux:select>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <flux:input wire:model="ket_nyeri" label="Keterangan Nyeri" />
                    <flux:select wire:model="pada_dokter" label="Diberitahukan Pada Dokter?">
                        <flux:select.option value="Tidak">Tidak</flux:select.option>
                        <flux:select.option value="Ya">Ya</flux:select.option>
                    </flux:select>
                    <flux:input wire:model="ket_dokter" label="Jam" />
                </div>
            </div>
        </div>

        {{-- Section 6: Risiko Jatuh & Masalah Keperawatan --}}
        <div id="section-6" class="bg-white dark:bg-neutral-800 rounded-2xl border border-neutral-200 dark:border-neutral-700 shadow-sm overflow-hidden">
            <div class="px-6 py-4 bg-neutral-50 dark:bg-neutral-900/50 border-b border-neutral-200 dark:border-neutral-700 flex items-center gap-3">
                <div class="w-6 h-6 rounded-full bg-[#4C5C2D]/10 flex items-center justify-center"><span class="text-[#4C5C2D] font-bold text-xs">6</span></div>
                <h2 class="text-sm font-bold text-neutral-800 dark:text-neutral-100">Risiko Jatuh & Masalah Keperawatan</h2>
            </div>
            <div class="p-6 space-y-6">
                <h3 class="text-xs font-black uppercase tracking-widest text-[#4C5C2D]">Pengkajian Risiko Jatuh (Get Up and Go)</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <flux:select wire:model="berjalan_a" label="a. Tidak seimbang / sempoyongan">
                        <flux:select.option value="Tidak">Tidak</flux:select.option>
                        <flux:select.option value="Ya">Ya</flux:select.option>
                    </flux:select>
                    <flux:select wire:model="berjalan_b" label="b. Jalan dengan alat bantu">
                        <flux:select.option value="Tidak">Tidak</flux:select.option>
                        <flux:select.option value="Ya">Ya</flux:select.option>
                    </flux:select>
                    <flux:select wire:model="berjalan_c" label="c. Menopang saat akan duduk">
                        <flux:select.option value="Tidak">Tidak</flux:select.option>
                        <flux:select.option value="Ya">Ya</flux:select.option>
                    </flux:select>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <flux:select wire:model="hasil" label="Hasil">
                        @foreach(['Tidak beresiko (tidak ditemukan a dan b)','Resiko rendah (ditemukan a/b)','Resiko tinggi (ditemukan a dan b)'] as $v)<flux:select.option value="{{ $v }}">{{ $v }}</flux:select.option>@endforeach
                    </flux:select>
                    <flux:select wire:model="lapor" label="Dilaporkan Kepada Dokter?">
                        <flux:select.option value="Tidak">Tidak</flux:select.option>
                        <flux:select.option value="Ya">Ya</flux:select.option>
                    </flux:select>
                    <flux:input wire:model="ket_lapor" label="Jam Dilaporkan" />
                </div>

                {{-- Masalah Keperawatan --}}
                <h3 class="text-xs font-black uppercase tracking-widest text-[#4C5C2D] pt-4">Masalah Keperawatan</h3>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    {{-- Left: Checkbox Masalah --}}
                    <div class="border border-neutral-200 dark:border-neutral-700 rounded-xl overflow-hidden">
                        <div class="px-4 py-3 bg-neutral-50 dark:bg-neutral-900/50 border-b border-neutral-200 dark:border-neutral-700">
                            <p class="text-xs font-bold text-neutral-600 uppercase tracking-wider">Pilih Masalah</p>
                        </div>
                        <div class="p-4 space-y-2 max-h-80 overflow-y-auto">
                            @foreach($masterMasalah as $m)
                                <label class="flex items-start gap-3 p-2 rounded-lg hover:bg-neutral-50 dark:hover:bg-neutral-700/50 cursor-pointer transition-colors group">
                                    <input type="checkbox" wire:model.live="selectedMasalah" value="{{ $m['kode_masalah'] }}"
                                           class="mt-0.5 rounded border-neutral-300 text-[#4C5C2D] focus:ring-[#4C5C2D]" />
                                    <span class="text-sm text-neutral-700 dark:text-neutral-300 group-hover:text-[#4C5C2D] transition-colors">{{ $m['nama_masalah'] }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Right: Rencana Keperawatan (Tabs) --}}
                    <div class="border border-neutral-200 dark:border-neutral-700 rounded-xl overflow-hidden" x-data="{ rencanaTab: 'master' }">
                        <div class="flex border-b border-neutral-200 dark:border-neutral-700">
                            <button @click="rencanaTab = 'master'" :class="rencanaTab === 'master' ? 'bg-white dark:bg-neutral-800 text-[#4C5C2D] border-b-2 border-[#4C5C2D]' : 'bg-neutral-50 dark:bg-neutral-900/50 text-neutral-500'" class="flex-1 px-4 py-3 text-xs font-bold uppercase tracking-wider transition-colors">Rencana Keperawatan</button>
                            <button @click="rencanaTab = 'lainnya'" :class="rencanaTab === 'lainnya' ? 'bg-white dark:bg-neutral-800 text-[#4C5C2D] border-b-2 border-[#4C5C2D]' : 'bg-neutral-50 dark:bg-neutral-900/50 text-neutral-500'" class="flex-1 px-4 py-3 text-xs font-bold uppercase tracking-wider transition-colors">Rencana Lainnya</button>
                        </div>
                        <div x-show="rencanaTab === 'master'" class="p-4 space-y-2 max-h-80 overflow-y-auto">
                            @if(!empty($availableRencana))
                                @foreach($availableRencana as $r)
                                    <label class="flex items-start gap-3 p-2 rounded-lg hover:bg-neutral-50 dark:hover:bg-neutral-700/50 cursor-pointer transition-colors group">
                                        <input type="checkbox" wire:model="selectedRencana" value="{{ $r['kode_rencana'] }}"
                                               class="mt-0.5 rounded border-neutral-300 text-[#4C5C2D] focus:ring-[#4C5C2D]" />
                                        <span class="text-sm text-neutral-700 dark:text-neutral-300">{{ $r['rencana_keperawatan'] }}</span>
                                    </label>
                                @endforeach
                            @else
                                <p class="text-center text-neutral-400 italic text-sm py-6">Centang masalah di sebelah kiri untuk menampilkan rencana.</p>
                            @endif
                        </div>
                        <div x-show="rencanaTab === 'lainnya'" class="p-4">
                            <flux:textarea wire:model="rencana" rows="6" placeholder="Tuliskan rencana keperawatan lainnya di sini..." />
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- Floating Minimap --}}
    <div x-data="{
            activeSection: 1,
            sections: [
                { id: 1, label: 'Identitas', icon: 'user' },
                { id: 2, label: 'Riwayat', icon: 'document-text' },
                { id: 3, label: 'Fisik', icon: 'heart' },
                { id: 4, label: 'Psikososial', icon: 'user-group' },
                { id: 5, label: 'Nyeri', icon: 'exclamation-triangle' },
                { id: 6, label: 'Risiko', icon: 'clipboard-document-check' }
            ],
            init() {
                window.addEventListener('scroll', () => {
                    this.sections.forEach(s => {
                        const el = document.getElementById('section-' + s.id);
                        if (el) {
                            const rect = el.getBoundingClientRect();
                            if (rect.top <= 150 && rect.bottom >= 150) this.activeSection = s.id;
                        }
                    });
                });
            },
            scrollTo(id) {
                const el = document.getElementById('section-' + id);
                if (el) window.scrollTo({ top: el.offsetTop - 100, behavior: 'smooth' });
            }
         }"
         class="fixed right-6 top-1/2 -translate-y-1/2 z-40 hidden xl:flex flex-col gap-3">
        <div class="group flex items-center justify-end gap-3">
            <span :class="activeSection === 1 ? 'opacity-100 text-[#4C5C2D] scale-100' : 'opacity-0 group-hover:opacity-100 text-neutral-400 scale-95 translate-x-2 group-hover:translate-x-0'"
                  class="text-[10px] font-black uppercase tracking-widest transition-all duration-300">Identitas</span>
            <button @click="scrollTo(1)"
                    :class="activeSection === 1 ? 'bg-[#4C5C2D] text-white scale-110 shadow-lg' : 'bg-white dark:bg-neutral-800 text-neutral-400 hover:text-[#4C5C2D] border border-neutral-200 dark:border-neutral-700 shadow-sm hover:scale-105'"
                    class="w-10 h-10 rounded-xl flex items-center justify-center transition-all duration-300">
                <flux:icon name="user" class="w-5 h-5" />
            </button>
        </div>

        <div class="group flex items-center justify-end gap-3">
            <span :class="activeSection === 2 ? 'opacity-100 text-[#4C5C2D] scale-100' : 'opacity-0 group-hover:opacity-100 text-neutral-400 scale-95 translate-x-2 group-hover:translate-x-0'"
                  class="text-[10px] font-black uppercase tracking-widest transition-all duration-300">Riwayat</span>
            <button @click="scrollTo(2)"
                    :class="activeSection === 2 ? 'bg-[#4C5C2D] text-white scale-110 shadow-lg' : 'bg-white dark:bg-neutral-800 text-neutral-400 hover:text-[#4C5C2D] border border-neutral-200 dark:border-neutral-700 shadow-sm hover:scale-105'"
                    class="w-10 h-10 rounded-xl flex items-center justify-center transition-all duration-300">
                <flux:icon name="document-text" class="w-5 h-5" />
            </button>
        </div>

        <div class="group flex items-center justify-end gap-3">
            <span :class="activeSection === 3 ? 'opacity-100 text-[#4C5C2D] scale-100' : 'opacity-0 group-hover:opacity-100 text-neutral-400 scale-95 translate-x-2 group-hover:translate-x-0'"
                  class="text-[10px] font-black uppercase tracking-widest transition-all duration-300">Fisik</span>
            <button @click="scrollTo(3)"
                    :class="activeSection === 3 ? 'bg-[#4C5C2D] text-white scale-110 shadow-lg' : 'bg-white dark:bg-neutral-800 text-neutral-400 hover:text-[#4C5C2D] border border-neutral-200 dark:border-neutral-700 shadow-sm hover:scale-105'"
                    class="w-10 h-10 rounded-xl flex items-center justify-center transition-all duration-300">
                <flux:icon name="heart" class="w-5 h-5" />
            </button>
        </div>

        <div class="group flex items-center justify-end gap-3">
            <span :class="activeSection === 4 ? 'opacity-100 text-[#4C5C2D] scale-100' : 'opacity-0 group-hover:opacity-100 text-neutral-400 scale-95 translate-x-2 group-hover:translate-x-0'"
                  class="text-[10px] font-black uppercase tracking-widest transition-all duration-300">Psikososial</span>
            <button @click="scrollTo(4)"
                    :class="activeSection === 4 ? 'bg-[#4C5C2D] text-white scale-110 shadow-lg' : 'bg-white dark:bg-neutral-800 text-neutral-400 hover:text-[#4C5C2D] border border-neutral-200 dark:border-neutral-700 shadow-sm hover:scale-105'"
                    class="w-10 h-10 rounded-xl flex items-center justify-center transition-all duration-300">
                <flux:icon name="user-group" class="w-5 h-5" />
            </button>
        </div>

        <div class="group flex items-center justify-end gap-3">
            <span :class="activeSection === 5 ? 'opacity-100 text-[#4C5C2D] scale-100' : 'opacity-0 group-hover:opacity-100 text-neutral-400 scale-95 translate-x-2 group-hover:translate-x-0'"
                  class="text-[10px] font-black uppercase tracking-widest transition-all duration-300">Nyeri</span>
            <button @click="scrollTo(5)"
                    :class="activeSection === 5 ? 'bg-[#4C5C2D] text-white scale-110 shadow-lg' : 'bg-white dark:bg-neutral-800 text-neutral-400 hover:text-[#4C5C2D] border border-neutral-200 dark:border-neutral-700 shadow-sm hover:scale-105'"
                    class="w-10 h-10 rounded-xl flex items-center justify-center transition-all duration-300">
                <flux:icon name="exclamation-triangle" class="w-5 h-5" />
            </button>
        </div>

        <div class="group flex items-center justify-end gap-3">
            <span :class="activeSection === 6 ? 'opacity-100 text-[#4C5C2D] scale-100' : 'opacity-0 group-hover:opacity-100 text-neutral-400 scale-95 translate-x-2 group-hover:translate-x-0'"
                  class="text-[10px] font-black uppercase tracking-widest transition-all duration-300">Risiko</span>
            <button @click="scrollTo(6)"
                    :class="activeSection === 6 ? 'bg-[#4C5C2D] text-white scale-110 shadow-lg' : 'bg-white dark:bg-neutral-800 text-neutral-400 hover:text-[#4C5C2D] border border-neutral-200 dark:border-neutral-700 shadow-sm hover:scale-105'"
                    class="w-10 h-10 rounded-xl flex items-center justify-center transition-all duration-300">
                <flux:icon name="clipboard-document-check" class="w-5 h-5" />
            </button>
        </div>
    </div>
</div>
