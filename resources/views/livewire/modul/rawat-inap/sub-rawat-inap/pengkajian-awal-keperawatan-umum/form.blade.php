<div class="flex flex-col gap-6 pb-24" x-data="{
    activeSection: 1,
    showMinimap: false,
    init() {
        window.addEventListener('scroll', () => {
            this.showMinimap = window.scrollY > 200;
            const sections = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];
            sections.forEach(s => {
                const el = document.getElementById('section-' + s);
                if (el) {
                    const rect = el.getBoundingClientRect();
                    if (rect.top <= 150 && rect.bottom >= 150) this.activeSection = s;
                }
            });
        });
    },
    scrollTo(id) {
        const el = document.getElementById('section-' + id);
        if (el) window.scrollTo({ top: el.offsetTop - 100, behavior: 'smooth' });
    },
    morseScore: 0,
    sydneyScore: 0,
    calculateMorse() {
        this.morseScore = parseInt($wire.penilaian_jatuhmorse_nilai1 || 0) + parseInt($wire.penilaian_jatuhmorse_nilai2 || 0) + parseInt($wire.penilaian_jatuhmorse_nilai3 || 0) + parseInt($wire.penilaian_jatuhmorse_nilai4 || 0) + parseInt($wire.penilaian_jatuhmorse_nilai5 || 0) + parseInt($wire.penilaian_jatuhmorse_nilai6 || 0);
        $wire.penilaian_jatuhmorse_totalnilai = this.morseScore;
    },
    calculateSydney() {
        this.sydneyScore = parseInt($wire.penilaian_jatuhsydney_nilai1 || 0) + parseInt($wire.penilaian_jatuhsydney_nilai2 || 0) + parseInt($wire.penilaian_jatuhsydney_nilai3 || 0) + parseInt($wire.penilaian_jatuhsydney_nilai4 || 0) + parseInt($wire.penilaian_jatuhsydney_nilai5 || 0) + parseInt($wire.penilaian_jatuhsydney_nilai6 || 0) + parseInt($wire.penilaian_jatuhsydney_nilai7 || 0) + parseInt($wire.penilaian_jatuhsydney_nilai8 || 0) + parseInt($wire.penilaian_jatuhsydney_nilai9 || 0) + parseInt($wire.penilaian_jatuhsydney_nilai10 || 0) + parseInt($wire.penilaian_jatuhsydney_nilai11 || 0);
        $wire.penilaian_jatuhsydney_totalnilai = this.sydneyScore;
    },
    get morseRiskLevel() {
        if (this.morseScore <= 24) return 'Risiko Rendah (0-24), Tindakan: Intervensi pencegahan risiko jatuh standar';
        if (this.morseScore <= 44) return 'Risiko Sedang (25-44), Tindakan: Intervensi pencegahan risiko jatuh standar';
        return 'Risiko Tinggi (≥ 45), Tindakan: Intervensi pencegahan risiko jatuh standar dan risiko tinggi';
    },
    get sydneyRiskLevel() {
        if (this.sydneyScore <= 3) return 'Risiko Rendah (1-3), Tindakan: Intervensi pencegahan risiko jatuh standar';
        return 'Risiko Tinggi (≥ 4), Tindakan: Intervensi pencegahan risiko jatuh standar dan risiko tinggi';
    }
}" x-init="calculateMorse(); calculateSydney();">

    {{-- Sticky Header --}}
    <div class="sticky top-0 z-40 bg-white/80 dark:bg-neutral-900/80 backdrop-blur-md border-b border-neutral-200 dark:border-neutral-700 -mx-4 px-4 py-3 mb-2 flex items-center justify-between shadow-sm">
        <div class="flex items-center gap-3">
            <a href="{{ route('modul.rawat-inap.sub-rawat-inap.pengkajian-awal-keperawatan-umum', str_replace('/', '-', $regPeriksa->no_rawat)) }}" wire:navigate class="flex items-center justify-center w-8 h-8 rounded-lg bg-[#4C5C2D] hover:bg-[#3d4b24] transition-colors shadow-sm">
                <flux:icon name="chevron-left" class="w-5 h-5 text-white" />
            </a>
            <div>
                <h1 class="text-base font-bold text-neutral-800 dark:text-neutral-100">{{ $isEditMode ? 'Edit' : 'Tambah' }} Pengkajian Awal Keperawatan Umum</h1>
                <p class="text-[10px] text-neutral-500 font-medium uppercase tracking-wider">{{ $regPeriksa->pasien->nm_pasien }} ({{ $regPeriksa->no_rawat }})</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <flux:button href="{{ route('modul.rawat-inap.sub-rawat-inap.pengkajian-awal-keperawatan-umum', str_replace('/', '-', $regPeriksa->no_rawat)) }}" wire:navigate variant="ghost" class="h-9 text-sm">Batal</flux:button>
            <flux:button wire:click="save" variant="primary" icon="check" class="bg-[#4C5C2D] hover:bg-[#3D4A24] h-9 px-6 text-sm" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="save">Simpan</span>
                <span wire:loading wire:target="save">Menyimpan...</span>
            </flux:button>
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
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <flux:input wire:model="tanggal" type="datetime-local" label="Tanggal Pengkajian" required />
                    <div class="grid grid-cols-2 gap-4">
                        <flux:select wire:model="informasi" label="Informasi Didapat Dari">
                            <flux:select.option value="Autoanamnesis">Autoanamnesis</flux:select.option>
                            <flux:select.option value="Alloanamnesis">Alloanamnesis</flux:select.option>
                        </flux:select>
                        <flux:input wire:model="ket_informasi" label="Keterangan Informasi" />
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <flux:select wire:model="tiba_diruang_rawat" label="Tiba di Ruang Rawat">
                        <flux:select.option value="Jalan Tanpa Bantuan">Jalan Tanpa Bantuan</flux:select.option>
                        <flux:select.option value="Kursi Roda">Kursi Roda</flux:select.option>
                        <flux:select.option value="Brankar">Brankar</flux:select.option>
                    </flux:select>
                    <flux:select wire:model="kasus_trauma" label="Macam Kasus">
                        <flux:select.option value="Trauma">Trauma</flux:select.option>
                        <flux:select.option value="Non Trauma">Non Trauma</flux:select.option>
                    </flux:select>
                    <flux:select wire:model="cara_masuk" label="Cara Masuk">
                        <flux:select.option value="Poli">Poli</flux:select.option>
                        <flux:select.option value="IGD">IGD</flux:select.option>
                        <flux:select.option value="Lain-lain">Lain-lain</flux:select.option>
                    </flux:select>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- Petugas 1 Search --}}
                    <div class="relative z-[60]">
                        <flux:input wire:model.live.debounce.300ms="petugas1Search" label="Cari Petugas 1 (NIP/Nama)" placeholder="Ketik minimal 3 karakter..." icon="magnifying-glass" />
                        @if(!empty($petugas1List))
                            <div class="absolute left-0 right-0 mt-1 bg-white dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl shadow-xl max-h-48 overflow-y-auto">
                                @foreach($petugas1List as $p)
                                    <button type="button" wire:click="selectPetugas1('{{ $p->nip }}', '{{ addslashes($p->nama) }}')" class="w-full text-left px-4 py-3 hover:bg-neutral-50 dark:hover:bg-neutral-700 border-b last:border-0 transition-colors group">
                                        <div class="font-bold text-sm text-neutral-700 group-hover:text-[#4C5C2D]">{{ $p->nama }}</div>
                                        <div class="text-[10px] text-neutral-400 font-mono">{{ $p->nip }}</div>
                                    </button>
                                @endforeach
                            </div>
                        @endif
                        @if($nip1)
                            <div class="mt-2 p-3 bg-[#4C5C2D]/5 border border-[#4C5C2D]/20 rounded-xl flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <flux:icon name="user-circle" class="w-5 h-5 text-[#4C5C2D]" />
                                    <div>
                                        <div class="text-xs font-bold text-[#4C5C2D]">{{ $nmPetugas1 }}</div>
                                        <div class="text-[10px] text-neutral-500 font-mono">{{ $nip1 }}</div>
                                    </div>
                                </div>
                            </div>
                        @endif
                        @error('nip1') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Petugas 2 Search --}}
                    <div class="relative z-[50]">
                        <flux:input wire:model.live.debounce.300ms="petugas2Search" label="Cari Petugas 2 (NIP/Nama)" placeholder="Ketik minimal 3 karakter..." icon="magnifying-glass" />
                        @if(!empty($petugas2List))
                            <div class="absolute left-0 right-0 mt-1 bg-white dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl shadow-xl max-h-48 overflow-y-auto">
                                @foreach($petugas2List as $p)
                                    <button type="button" wire:click="selectPetugas2('{{ $p->nip }}', '{{ addslashes($p->nama) }}')" class="w-full text-left px-4 py-3 hover:bg-neutral-50 dark:hover:bg-neutral-700 border-b last:border-0 transition-colors group">
                                        <div class="font-bold text-sm text-neutral-700 group-hover:text-[#4C5C2D]">{{ $p->nama }}</div>
                                        <div class="text-[10px] text-neutral-400 font-mono">{{ $p->nip }}</div>
                                    </button>
                                @endforeach
                            </div>
                        @endif
                        @if($nip2)
                            <div class="mt-2 p-3 bg-[#4C5C2D]/5 border border-[#4C5C2D]/20 rounded-xl flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <flux:icon name="user-circle" class="w-5 h-5 text-[#4C5C2D]" />
                                    <div>
                                        <div class="text-xs font-bold text-[#4C5C2D]">{{ $nmPetugas2 }}</div>
                                        <div class="text-[10px] text-neutral-500 font-mono">{{ $nip2 }}</div>
                                    </div>
                                </div>
                            </div>
                        @endif
                        @error('nip2') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Dokter DPJP Search --}}
                <div class="relative z-[40]">
                    <flux:input wire:model.live.debounce.300ms="dokterSearch" label="Dokter DPJP" placeholder="Cari Dokter..." icon="magnifying-glass" />
                    @if(!empty($dokterList))
                        <div class="absolute left-0 right-0 mt-1 bg-white dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl shadow-xl max-h-48 overflow-y-auto">
                            @foreach($dokterList as $d)
                                <button type="button" wire:click="selectDokter('{{ $d->kd_dokter }}', '{{ addslashes($d->nm_dokter) }}')" class="w-full text-left px-4 py-3 hover:bg-neutral-50 dark:hover:bg-neutral-700 border-b last:border-0 transition-colors group">
                                    <div class="font-bold text-sm text-neutral-700 group-hover:text-[#4C5C2D]">{{ $d->nm_dokter }}</div>
                                    <div class="text-[10px] text-neutral-400 font-mono">{{ $d->kd_dokter }}</div>
                                </button>
                            @endforeach
                        </div>
                    @endif
                    @if($kd_dokter)
                        <div class="mt-2 p-3 bg-[#4C5C2D]/5 border border-[#4C5C2D]/20 rounded-xl flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <flux:icon name="user" class="w-5 h-5 text-[#4C5C2D]" />
                                <div>
                                    <div class="text-xs font-bold text-[#4C5C2D]">{{ $nmDokter }}</div>
                                    <div class="text-[10px] text-neutral-500 font-mono">{{ $kd_dokter }}</div>
                                </div>
                            </div>
                        </div>
                    @endif
                    @error('kd_dokter') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- Section 2: Riwayat Kesehatan --}}
        <div id="section-2" class="bg-white dark:bg-neutral-800 rounded-2xl border border-neutral-200 dark:border-neutral-700 shadow-sm overflow-hidden">
            <div class="px-6 py-4 bg-neutral-50 dark:bg-neutral-900/50 border-b border-neutral-200 dark:border-neutral-700 flex items-center gap-3">
                <div class="w-6 h-6 rounded-full bg-[#4C5C2D]/10 flex items-center justify-center"><span class="text-[#4C5C2D] font-bold text-xs">2</span></div>
                <h2 class="text-sm font-bold text-neutral-800 dark:text-neutral-100">I. Riwayat Kesehatan</h2>
            </div>
            <div class="p-6 space-y-6">
                <flux:textarea wire:model="rps" label="Riwayat Penyakit Sekarang (RPS)" rows="2" />
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <flux:input wire:model="rpd" label="Riwayat Penyakit Dahulu (RPD)" />
                    <flux:input wire:model="rpk" label="Riwayat Penyakit Keluarga (RPK)" />
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <flux:input wire:model="rpo" label="Riwayat Penggunaan Obat (RPO)" />
                    <flux:input wire:model="riwayat_pembedahan" label="Riwayat Pembedahan" />
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <flux:input wire:model="riwayat_dirawat_dirs" label="Dirawat Di RS" />
                    <flux:select wire:model="alat_bantu_dipakai" label="Alat Bantu">
                        @foreach(['Kacamata', 'Prothesa', 'Alat Bantu Dengar', 'Lain-lain'] as $v) <flux:select.option value="{{ $v }}">{{ $v }}</flux:select.option> @endforeach
                    </flux:select>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <flux:select wire:model="riwayat_kehamilan" label="Kehamilan">
                        <flux:select.option value="Tidak">Tidak</flux:select.option>
                        <flux:select.option value="Ya">Ya</flux:select.option>
                    </flux:select>
                    <flux:input wire:model="riwayat_kehamilan_perkiraan" label="Perkiraan Lahir" />
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <flux:input wire:model="riwayat_tranfusi" label="Transfusi" />
                    <flux:input wire:model="riwayat_alergi" label="Alergi" />
                </div>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <flux:select wire:model="riwayat_merokok" label="Merokok">
                        <flux:select.option value="Tidak">Tidak</flux:select.option>
                        <flux:select.option value="Ya">Ya</flux:select.option>
                    </flux:select>
                    <flux:input wire:model="riwayat_merokok_jumlah" label="Jumlah/Hari" />
                    <flux:select wire:model="riwayat_alkohol" label="Alkohol">
                        <flux:select.option value="Tidak">Tidak</flux:select.option>
                        <flux:select.option value="Ya">Ya</flux:select.option>
                    </flux:select>
                    <flux:input wire:model="riwayat_alkohol_jumlah" label="Jumlah/Hari" />
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <flux:select wire:model="riwayat_narkoba" label="Narkoba">
                        <flux:select.option value="Tidak">Tidak</flux:select.option>
                        <flux:select.option value="Ya">Ya</flux:select.option>
                    </flux:select>
                    <flux:select wire:model="riwayat_olahraga" label="Olahraga">
                        <flux:select.option value="Tidak">Tidak</flux:select.option>
                        <flux:select.option value="Ya">Ya</flux:select.option>
                    </flux:select>
                </div>
            </div>
        </div>

        {{-- Section 3: Pemeriksaan Fisik --}}
        <div id="section-3" class="bg-white dark:bg-neutral-800 rounded-2xl border border-neutral-200 dark:border-neutral-700 shadow-sm overflow-hidden">
            <div class="px-6 py-4 bg-neutral-50 dark:bg-neutral-900/50 border-b border-neutral-200 dark:border-neutral-700 flex items-center gap-3">
                <div class="w-6 h-6 rounded-full bg-[#4C5C2D]/10 flex items-center justify-center"><span class="text-[#4C5C2D] font-bold text-xs">3</span></div>
                <h2 class="text-sm font-bold text-neutral-800 dark:text-neutral-100">II. Pemeriksaan Fisik</h2>
            </div>
            <div class="p-6 space-y-6">
                <h3 class="text-xs font-black uppercase tracking-widest text-[#4C5C2D]">Tanda-tanda Vital</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-4">
                    <flux:input wire:model="pemeriksaan_mental" label="Mental" />
                    <flux:select wire:model="pemeriksaan_keadaan_umum" label="Keadaan Umum">
                        @foreach(['Baik', 'Sedang', 'Buruk'] as $v) <flux:select.option value="{{ $v }}">{{ $v }}</flux:select.option> @endforeach
                    </flux:select>
                    <flux:input wire:model="pemeriksaan_gcs" label="GCS (E,M,V)" />
                    <flux:input wire:model="pemeriksaan_td" label="TD (mmHg)" />
                    <flux:input wire:model="pemeriksaan_nadi" label="Nadi (x/mnt)" />
                    <flux:input wire:model="pemeriksaan_rr" label="RR (x/mnt)" />
                    <flux:input wire:model="pemeriksaan_suhu" label="Suhu (°C)" />
                    <flux:input wire:model="pemeriksaan_spo2" label="SpO2 (%)" />
                    <flux:input wire:model="pemeriksaan_bb" label="BB (Kg)" />
                    <flux:input wire:model="pemeriksaan_tb" label="TB (Cm)" />
                </div>

                <h3 class="text-xs font-black uppercase tracking-widest text-[#4C5C2D] pt-4">Sistem Saraf Pusat (SSP)</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="flex gap-2">
                        <div class="w-1/3"><flux:select wire:model="pemeriksaan_susunan_kepala" label="Kepala"><option value="TAK">TAK</option><option value="Hydrocephalus">Hydrocephalus</option><option value="Hematoma">Hematoma</option><option value="Lain-lain">Lain-lain</option></flux:select></div>
                        <div class="w-2/3"><flux:input wire:model="pemeriksaan_susunan_kepala_keterangan" label="Ket. Kepala" /></div>
                    </div>
                    <div class="flex gap-2">
                        <div class="w-1/3"><flux:select wire:model="pemeriksaan_susunan_wajah" label="Wajah"><option value="TAK">TAK</option><option value="Asimetris">Asimetris</option><option value="Kelainan Kongenital">Kelainan Kongenital</option></flux:select></div>
                        <div class="w-2/3"><flux:input wire:model="pemeriksaan_susunan_wajah_keterangan" label="Ket. Wajah" /></div>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <flux:select wire:model="pemeriksaan_susunan_leher" label="Leher"><option value="TAK">TAK</option><option value="Kaku Kuduk">Kaku Kuduk</option><option value="Pembesaran Thyroid">Pembesaran Thyroid</option><option value="Pembesaran KGB">Pembesaran KGB</option></flux:select>
                        <flux:select wire:model="pemeriksaan_susunan_sensorik" label="Sensorik"><option value="TAK">TAK</option><option value="Sakit Nyeri">Sakit Nyeri</option><option value="Rasa kebas">Rasa kebas</option></flux:select>
                    </div>
                    <div class="flex gap-2">
                        <div class="w-1/3"><flux:select wire:model="pemeriksaan_susunan_kejang" label="Kejang"><option value="TAK">TAK</option><option value="Kuat">Kuat</option><option value="Ada">Ada</option></flux:select></div>
                        <div class="w-2/3"><flux:input wire:model="pemeriksaan_susunan_kejang_keterangan" label="Ket. Kejang" /></div>
                    </div>
                </div>

                <h3 class="text-xs font-black uppercase tracking-widest text-[#4C5C2D] pt-4">Kardiovaskuler & Respirasi</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <flux:select wire:model="pemeriksaan_kardiovaskuler_denyut_nadi" label="Denyut Nadi"><option value="Teratur">Teratur</option><option value="Tidak Teratur">Tidak Teratur</option></flux:select>
                    <flux:select wire:model="pemeriksaan_kardiovaskuler_pulsasi" label="Pulsasi"><option value="Kuat">Kuat</option><option value="Lemah">Lemah</option><option value="Lain-lain">Lain-lain</option></flux:select>
                    <div class="flex gap-2">
                        <div class="w-1/2"><flux:select wire:model="pemeriksaan_kardiovaskuler_sirkulasi" label="Sirkulasi"><option value="Akral Hangat">Akral Hangat</option><option value="Akral Dingin">Akral Dingin</option><option value="Edema">Edema</option></flux:select></div>
                        <div class="w-1/2"><flux:input wire:model="pemeriksaan_kardiovaskuler_sirkulasi_keterangan" label="Ket" /></div>
                    </div>
                    
                    <flux:select wire:model="pemeriksaan_respirasi_pola_nafas" label="Pola Nafas"><option value="Normal">Normal</option><option value="Bradipnea">Bradipnea</option><option value="Tachipnea">Tachipnea</option></flux:select>
                    <flux:select wire:model="pemeriksaan_respirasi_retraksi" label="Retraksi"><option value="Tidak Ada">Tidak Ada</option><option value="Ringan">Ringan</option><option value="Berat">Berat</option></flux:select>
                    <flux:select wire:model="pemeriksaan_respirasi_suara_nafas" label="Suara Nafas"><option value="Vesikuler">Vesikuler</option><option value="Wheezing">Wheezing</option><option value="Rhonki">Rhonki</option></flux:select>
                    
                    <flux:select wire:model="pemeriksaan_respirasi_volume_pernafasan" label="Vol. Pernafasan"><option value="Normal">Normal</option><option value="Hiperventilasi">Hiperventilasi</option><option value="Hipoventilasi">Hipoventilasi</option></flux:select>
                    <div class="flex gap-2">
                        <div class="w-1/2"><flux:select wire:model="pemeriksaan_respirasi_jenis_pernafasan" label="Jns Pernafasan"><option value="Pernafasan Dada">Pernafasan Dada</option><option value="Alat Bantu Pernafasaan">Alat Bantu Pernafasaan</option></flux:select></div>
                        <div class="w-1/2"><flux:input wire:model="pemeriksaan_respirasi_jenis_pernafasan_keterangan" label="Ket" /></div>
                    </div>
                    <flux:select wire:model="pemeriksaan_respirasi_irama_nafas" label="Irama Nafas"><option value="Teratur">Teratur</option><option value="Tidak Teratur">Tidak Teratur</option></flux:select>
                    
                    <flux:select wire:model="pemeriksaan_respirasi_batuk" label="Batuk"><option value="Tidak">Tidak</option><option value="Ya : Produktif">Ya : Produktif</option><option value="Ya : Non Produktif">Ya : Non Produktif</option></flux:select>
                </div>
            </div>
        </div>

        {{-- Section 4: Pola Kehidupan Sehari-hari --}}
        <div id="section-4" class="bg-white dark:bg-neutral-800 rounded-2xl border border-neutral-200 dark:border-neutral-700 shadow-sm overflow-hidden">
            <div class="px-6 py-4 bg-neutral-50 dark:bg-neutral-900/50 border-b border-neutral-200 dark:border-neutral-700 flex items-center gap-3">
                <div class="w-6 h-6 rounded-full bg-[#4C5C2D]/10 flex items-center justify-center"><span class="text-[#4C5C2D] font-bold text-xs">4</span></div>
                <h2 class="text-sm font-bold text-neutral-800 dark:text-neutral-100">III. Pola Kehidupan Sehari-hari</h2>
            </div>
            <div class="p-6 space-y-6">
                <h3 class="text-xs font-black uppercase tracking-widest text-[#4C5C2D]">Pola Aktifitas (Mandiri/Bantuan)</h3>
                <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                    <flux:select wire:model="pola_aktifitas_makanminum" label="Makan/Minum"><option value="Mandiri">Mandiri</option><option value="Bantuan Orang Lain">Bantuan</option></flux:select>
                    <flux:select wire:model="pola_aktifitas_mandi" label="Mandi"><option value="Mandiri">Mandiri</option><option value="Bantuan Orang Lain">Bantuan</option></flux:select>
                    <flux:select wire:model="pola_aktifitas_eliminasi" label="Eliminasi"><option value="Mandiri">Mandiri</option><option value="Bantuan Orang Lain">Bantuan</option></flux:select>
                    <flux:select wire:model="pola_aktifitas_berpakaian" label="Berpakaian"><option value="Mandiri">Mandiri</option><option value="Bantuan Orang Lain">Bantuan</option></flux:select>
                    <flux:select wire:model="pola_aktifitas_berpindah" label="Berpindah"><option value="Mandiri">Mandiri</option><option value="Bantuan Orang Lain">Bantuan</option></flux:select>
                </div>
                <h3 class="text-xs font-black uppercase tracking-widest text-[#4C5C2D] pt-4">Pola Nutrisi & Tidur</h3>
                <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    <flux:input wire:model="pola_nutrisi_frekuesi_makan" label="Frekuensi Makan (x/Hari)" />
                    <flux:input wire:model="pola_nutrisi_jenis_makanan" label="Jenis Makanan" />
                    <flux:input wire:model="pola_nutrisi_porsi_makan" label="Porsi Makan" />
                    <flux:input wire:model="pola_tidur_lama_tidur" label="Lama Tidur (Jam)" />
                    <flux:select wire:model="pola_tidur_gangguan" label="Gangguan Tidur"><option value="Tidak Ada Gangguan">Tidak Ada</option><option value="Insomnia">Insomnia</option></flux:select>
                </div>
            </div>
        </div>

        {{-- Section 5: Pengkajian Fungsi --}}
        <div id="section-5" class="bg-white dark:bg-neutral-800 rounded-2xl border border-neutral-200 dark:border-neutral-700 shadow-sm overflow-hidden">
            <div class="px-6 py-4 bg-neutral-50 dark:bg-neutral-900/50 border-b border-neutral-200 dark:border-neutral-700 flex items-center gap-3">
                <div class="w-6 h-6 rounded-full bg-[#4C5C2D]/10 flex items-center justify-center"><span class="text-[#4C5C2D] font-bold text-xs">5</span></div>
                <h2 class="text-sm font-bold text-neutral-800 dark:text-neutral-100">IV. Pengkajian Fungsi</h2>
            </div>
            <div class="p-6 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <flux:select wire:model="pengkajian_fungsi_kemampuan_sehari" label="Kemampuan ADL"><option value="Mandiri">Mandiri</option><option value="Bantuan Minimal">Bantuan Minimal</option><option value="Bantuan Sebagian">Bantuan Sebagian</option><option value="Ketergantungan Total">Ketergantungan Total</option></flux:select>
                    <div class="flex gap-2">
                        <div class="w-1/2"><flux:select wire:model="pengkajian_fungsi_berjalan" label="Berjalan"><option value="TAK">TAK</option><option value="Penurunan Kekuatan/ROM">Penurunan Kekuatan/ROM</option><option value="Paralisis">Paralisis</option><option value="Sering Jatuh">Sering Jatuh</option><option value="Deformitas">Deformitas</option><option value="Hilang Keseimbangan">Hilang Keseimbangan</option><option value="Riwayat Patah Tulang">Riwayat Patah Tulang</option><option value="Lain-lain">Lain-lain</option></flux:select></div>
                        <div class="w-1/2"><flux:input wire:model="pengkajian_fungsi_berjalan_keterangan" label="Ket" /></div>
                    </div>
                    <flux:select wire:model="pengkajian_fungsi_aktifitas" label="Aktifitas"><option value="Tirah Baring">Tirah Baring</option><option value="Duduk">Duduk</option><option value="Berjalan">Berjalan</option></flux:select>
                    <flux:select wire:model="pengkajian_fungsi_ambulasi" label="Ambulasi"><option value="Walker">Walker</option><option value="Tongkat">Tongkat</option><option value="Kursi Roda">Kursi Roda</option><option value="Tidak Menggunakan">Tidak Menggunakan</option></flux:select>
                    <div class="flex gap-2">
                        <div class="w-1/2"><flux:select wire:model="pengkajian_fungsi_ekstrimitas_atas" label="Ekstremitas Atas"><option value="TAK">TAK</option><option value="Lemah">Lemah</option><option value="Oedema">Oedema</option><option value="Tidak Simetris">Tidak Simetris</option><option value="Lain-lain">Lain-lain</option></flux:select></div>
                        <div class="w-1/2"><flux:input wire:model="pengkajian_fungsi_ekstrimitas_atas_keterangan" label="Ket" /></div>
                    </div>
                    <div class="flex gap-2">
                        <div class="w-1/2"><flux:select wire:model="pengkajian_fungsi_ekstrimitas_bawah" label="Ekstremitas Bwh"><option value="TAK">TAK</option><option value="Varises">Varises</option><option value="Oedema">Oedema</option><option value="Tidak Simetris">Tidak Simetris</option><option value="Lain-lain">Lain-lain</option></flux:select></div>
                        <div class="w-1/2"><flux:input wire:model="pengkajian_fungsi_ekstrimitas_bawah_keterangan" label="Ket" /></div>
                    </div>
                    <div class="flex gap-2">
                        <div class="w-1/2"><flux:select wire:model="pengkajian_fungsi_menggenggam" label="Menggenggam"><option value="Tidak Ada Kesulitan">Tidak Ada Kesulitan</option><option value="Terakhir">Terakhir</option><option value="Lain-lain">Lain-lain</option></flux:select></div>
                        <div class="w-1/2"><flux:input wire:model="pengkajian_fungsi_menggenggam_keterangan" label="Ket" /></div>
                    </div>
                    <div class="flex gap-2">
                        <div class="w-1/2"><flux:select wire:model="pengkajian_fungsi_koordinasi" label="Koordinasi"><option value="Tidak Ada Kesulitan">Tidak Ada Kesulitan</option><option value="Ada Masalah">Ada Masalah</option></flux:select></div>
                        <div class="w-1/2"><flux:input wire:model="pengkajian_fungsi_koordinasi_keterangan" label="Ket" /></div>
                    </div>
                    <flux:select wire:model="pengkajian_fungsi_kesimpulan" label="Kesimpulan"><option value="Ya (Co DPJP)">Ya (Co DPJP)</option><option value="Tidak (Tidak Perlu Co DPJP)">Tidak (Tidak Perlu Co DPJP)</option></flux:select>
                </div>
            </div>
        </div>

        {{-- Section 6: Riwayat Psikososial --}}
        <div id="section-6" class="bg-white dark:bg-neutral-800 rounded-2xl border border-neutral-200 dark:border-neutral-700 shadow-sm overflow-hidden">
            <div class="px-6 py-4 bg-neutral-50 dark:bg-neutral-900/50 border-b border-neutral-200 dark:border-neutral-700 flex items-center gap-3">
                <div class="w-6 h-6 rounded-full bg-[#4C5C2D]/10 flex items-center justify-center"><span class="text-[#4C5C2D] font-bold text-xs">6</span></div>
                <h2 class="text-sm font-bold text-neutral-800 dark:text-neutral-100">V. Riwayat Psikologis & Sosial</h2>
            </div>
            <div class="p-6 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <flux:select wire:model="riwayat_psiko_kondisi_psiko" label="Kondisi Psiko"><option value="Tidak Ada Masalah">Tidak Ada Masalah</option><option value="Marah">Marah</option><option value="Takut">Takut</option><option value="Depresi">Depresi</option><option value="Cepat Lelah">Cepat Lelah</option><option value="Cemas">Cemas</option><option value="Gelisah">Gelisah</option><option value="Sulit Tidur">Sulit Tidur</option><option value="Lain-lain">Lain-lain</option></flux:select>
                    <flux:select wire:model="riwayat_psiko_gangguan_jiwa" label="Gangguan Jiwa"><option value="Tidak">Tidak</option><option value="Ya">Ya</option></flux:select>
                    <div class="flex gap-2">
                        <div class="w-1/2"><flux:select wire:model="riwayat_psiko_perilaku" label="Perilaku"><option value="Tidak Ada Masalah">Tidak Ada Masalah</option><option value="Perilaku Kekerasan">Perilaku Kekerasan</option><option value="Gangguan Efek">Gangguan Efek</option><option value="Gangguan Memori">Gangguan Memori</option><option value="Halusinasi">Halusinasi</option><option value="Kecenderungan Percobaan Bunuh Diri">Kecenderungan Percobaan Bunuh Diri</option><option value="Lain-lain">Lain-lain</option></flux:select></div>
                        <div class="w-1/2"><flux:input wire:model="riwayat_psiko_perilaku_keterangan" label="Ket" /></div>
                    </div>
                    <flux:select wire:model="riwayat_psiko_hubungan_keluarga" label="Hubungan Keluarga"><option value="Harmonis">Harmonis</option><option value="Tidak Harmonis">Tidak Harmonis</option></flux:select>
                    <flux:input value="{{ $regPeriksa->pasien->agama ?? '-' }}" label="Agama" readonly disabled />
                    <div class="flex gap-2">
                        <div class="w-1/2"><flux:select wire:model="riwayat_psiko_tinggal" label="Tinggal Dengan"><option value="Sendiri">Sendiri</option><option value="Orang Tua">Orang Tua</option><option value="Suami/Istri">Suami/Istri</option><option value="Keluarga">Keluarga</option><option value="Lain-lain">Lain-lain</option></flux:select></div>
                        <div class="w-1/2"><flux:input wire:model="riwayat_psiko_tinggal_keterangan" label="Ket" /></div>
                    </div>
                    <flux:input value="{{ $regPeriksa->pasien->pekerjaan ?? '-' }}" label="Pekerjaan" readonly disabled />
                    <div class="flex gap-2">
                        <div class="w-1/2"><flux:select wire:model="riwayat_psiko_nilai_kepercayaan" label="Nilai Kepercayaan"><option value="Tidak Ada">Tidak Ada</option><option value="Ada">Ada</option></flux:select></div>
                        <div class="w-1/2"><flux:input wire:model="riwayat_psiko_nilai_kepercayaan_keterangan" label="Ket" /></div>
                    </div>
                    <flux:input value="{{ $regPeriksa->pasien->bahasa_pasien ?? '-' }}" label="Bahasa" readonly disabled />
                    <flux:input value="{{ $regPeriksa->penjab->png_jawab ?? '-' }}" label="Pembayaran" readonly disabled />
                    <flux:input value="{{ $regPeriksa->pasien->pnd ?? '-' }}" label="Pendidikan Pasien" readonly disabled />
                    <flux:select wire:model="riwayat_psiko_pendidikan_pj" label="Pendidikan PJ"><option value="-">-</option><option value="TS">TS</option><option value="TK">TK</option><option value="SD">SD</option><option value="SMP">SMP</option><option value="SMA">SMA</option><option value="SLTA/SEDERAJAT">SLTA/SEDERAJAT</option><option value="D1">D1</option><option value="D2">D2</option><option value="D3">D3</option><option value="D4">D4</option><option value="S1">S1</option><option value="S2">S2</option><option value="S3">S3</option></flux:select>
                    <div class="flex gap-2">
                        <div class="w-1/2"><flux:select wire:model="riwayat_psiko_edukasi_diberikan" label="Edukasi Kepada"><option value="Pasien">Pasien</option><option value="Keluarga">Keluarga</option></flux:select></div>
                        <div class="w-1/2"><flux:input wire:model="riwayat_psiko_edukasi_diberikan_keterangan" label="Ket" /></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Section 7: Tingkat Nyeri --}}
        <div id="section-7" class="bg-white dark:bg-neutral-800 rounded-2xl border border-neutral-200 dark:border-neutral-700 shadow-sm overflow-hidden">
            <div class="px-6 py-4 bg-neutral-50 dark:bg-neutral-900/50 border-b border-neutral-200 dark:border-neutral-700 flex items-center gap-3">
                <div class="w-6 h-6 rounded-full bg-[#4C5C2D]/10 flex items-center justify-center"><span class="text-[#4C5C2D] font-bold text-xs">7</span></div>
                <h2 class="text-sm font-bold text-neutral-800 dark:text-neutral-100">VI. Pengkajian Tingkat Nyeri</h2>
            </div>
            <div class="p-6 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-neutral-50 dark:bg-neutral-900/50 p-4 rounded-xl border border-neutral-200 dark:border-neutral-700 flex justify-center">
                        <img src="{{ asset('img/pain_scale.png') }}" alt="Skala Nyeri" class="max-w-full h-auto object-contain">
                    </div>
                    <div class="grid grid-cols-1 gap-4">
                        <flux:select wire:model="penilaian_nyeri" label="Nyeri"><option value="Tidak Ada Nyeri">Tidak Ada Nyeri</option><option value="Nyeri Akut">Nyeri Akut</option><option value="Nyeri Kronis">Nyeri Kronis</option></flux:select>
                        <div class="flex gap-2">
                            <div class="w-1/2"><flux:select wire:model="penilaian_nyeri_penyebab" label="Penyebab"><option value="Proses Penyakit">Penyakit</option><option value="Benturan">Benturan</option><option value="Lain-lain">Lainnya</option></flux:select></div>
                            <div class="w-1/2"><flux:input wire:model="penilaian_nyeri_ket_penyebab" label="Ket Penyebab" /></div>
                        </div>
                        <div class="flex gap-2">
                            <div class="w-1/2"><flux:select wire:model="penilaian_nyeri_kualitas" label="Kualitas"><option value="Seperti Tertusuk">Tertusuk</option><option value="Berdenyut">Berdenyut</option><option value="Teriris">Teriris</option><option value="Tertindih">Tertindih</option><option value="Tertiban">Tertiban</option><option value="Lain-lain">Lainnya</option></flux:select></div>
                            <div class="w-1/2"><flux:input wire:model="penilaian_nyeri_ket_kualitas" label="Ket Kualitas" /></div>
                        </div>
                        <div class="flex gap-2">
                            <div class="w-1/2"><flux:input wire:model="penilaian_nyeri_lokasi" label="Lokasi" /></div>
                            <div class="w-1/2"><flux:select wire:model="penilaian_nyeri_menyebar" label="Menyebar"><option value="Tidak">Tdk Nular</option><option value="Ya">Menyebar</option></flux:select></div>
                        </div>
                        <div class="flex gap-2">
                            <div class="w-1/2"><flux:select wire:model="penilaian_nyeri_skala" label="Skala Nyeri">@for($i=0; $i<=10; $i++) <option value="{{$i}}">{{$i}}</option> @endfor</flux:select></div>
                            <div class="w-1/2"><flux:input wire:model="penilaian_nyeri_waktu" label="Durasi (Menit)" /></div>
                        </div>
                        <div class="flex gap-2">
                            <div class="w-1/2"><flux:select wire:model="penilaian_nyeri_hilang" label="Hilang Bila"><option value="Istirahat">Istirahat</option><option value="Medengar Musik">Musik</option><option value="Minum Obat">Obat</option></flux:select></div>
                            <div class="w-1/2"><flux:input wire:model="penilaian_nyeri_ket_hilang" label="Ket Hilang" /></div>
                        </div>
                        <div class="flex gap-2">
                            <div class="w-1/2"><flux:select wire:model="penilaian_nyeri_diberitahukan_dokter" label="Lapor Dokter"><option value="Tidak">Tidak</option><option value="Ya">Ya</option></flux:select></div>
                            <div class="w-1/2"><flux:input wire:model="penilaian_nyeri_jam_diberitahukan_dokter" type="time" label="Jam" /></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Section 8: Risiko Jatuh --}}
        <div id="section-8" class="bg-white dark:bg-neutral-800 rounded-2xl border border-neutral-200 dark:border-neutral-700 shadow-sm overflow-hidden">
            <div class="px-6 py-4 bg-neutral-50 dark:bg-neutral-900/50 border-b border-neutral-200 dark:border-neutral-700 flex items-center gap-3">
                <div class="w-6 h-6 rounded-full bg-[#4C5C2D]/10 flex items-center justify-center"><span class="text-[#4C5C2D] font-bold text-xs">8</span></div>
                <h2 class="text-sm font-bold text-neutral-800 dark:text-neutral-100">VII. Pengkajian Risiko Jatuh</h2>
            </div>
            <div class="p-6 grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div>
                    <h3 class="text-xs font-black uppercase tracking-widest text-[#4C5C2D] mb-4">Skala Morse</h3>
                    <div class="space-y-3">
                        <flux:select x-on:change="calculateMorse" wire:model="penilaian_jatuhmorse_nilai1" label="1. Riwayat Jatuh"><option value="0">Tidak (0)</option><option value="25">Ya (25)</option></flux:select>
                        <flux:select x-on:change="calculateMorse" wire:model="penilaian_jatuhmorse_nilai2" label="2. Diagnosis Sekunder"><option value="0">Tidak (0)</option><option value="15">Ya (15)</option></flux:select>
                        <flux:select x-on:change="calculateMorse" wire:model="penilaian_jatuhmorse_nilai3" label="3. Alat Bantu"><option value="0">Tdk Ada/Kursi Roda (0)</option><option value="15">Tongkat (15)</option><option value="30">Pegang Perabot (30)</option></flux:select>
                        <flux:select x-on:change="calculateMorse" wire:model="penilaian_jatuhmorse_nilai4" label="4. Terpasang Infuse"><option value="0">Tidak (0)</option><option value="20">Ya (20)</option></flux:select>
                        <flux:select x-on:change="calculateMorse" wire:model="penilaian_jatuhmorse_nilai5" label="5. Gaya Berjalan"><option value="0">Normal/Tirah Baring (0)</option><option value="10">Lemah (10)</option><option value="20">Terganggu (20)</option></flux:select>
                        <flux:select x-on:change="calculateMorse" wire:model="penilaian_jatuhmorse_nilai6" label="6. Status Mental"><option value="0">Sadar Kemampuan (0)</option><option value="15">Sering Lupa (15)</option></flux:select>
                        <div class="mt-4 p-4 bg-[#4C5C2D]/5 rounded-xl border border-[#4C5C2D]/20 flex justify-between items-center">
                            <span class="font-bold text-neutral-700 text-xs" x-text="morseRiskLevel"></span>
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-neutral-800">Total:</span>
                                <span class="text-2xl font-black text-[#4C5C2D]" x-text="morseScore"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div>
                    <h3 class="text-xs font-black uppercase tracking-widest text-[#4C5C2D] mb-4">Skala Sydney</h3>
                    <div class="space-y-3">
                        <flux:select x-on:change="calculateSydney" wire:model="penilaian_jatuhsydney_nilai1" label="1. Gangg Gaya Berjalan"><option value="0">Tidak (0)</option><option value="1">Ya (1)</option></flux:select>
                        <flux:select x-on:change="calculateSydney" wire:model="penilaian_jatuhsydney_nilai2" label="2. Pusing/Pingsan Posisi Tegak"><option value="0">Tidak (0)</option><option value="1">Ya (1)</option></flux:select>
                        <flux:select x-on:change="calculateSydney" wire:model="penilaian_jatuhsydney_nilai3" label="3. Kebingungan Setiap Saat"><option value="0">Tidak (0)</option><option value="1">Ya (1)</option></flux:select>
                        <flux:select x-on:change="calculateSydney" wire:model="penilaian_jatuhsydney_nilai4" label="4. Nokturia/Inkontinen"><option value="0">Tidak (0)</option><option value="1">Ya (1)</option></flux:select>
                        <flux:select x-on:change="calculateSydney" wire:model="penilaian_jatuhsydney_nilai5" label="5. Kebingungan Intermiten"><option value="0">Tidak (0)</option><option value="1">Ya (1)</option></flux:select>
                        <flux:select x-on:change="calculateSydney" wire:model="penilaian_jatuhsydney_nilai6" label="6. Kelemahan Umum"><option value="0">Tidak (0)</option><option value="1">Ya (1)</option></flux:select>
                        <flux:select x-on:change="calculateSydney" wire:model="penilaian_jatuhsydney_nilai7" label="7. Obat Berisiko Tinggi"><option value="0">Tidak (0)</option><option value="1">Ya (1)</option></flux:select>
                        <flux:select x-on:change="calculateSydney" wire:model="penilaian_jatuhsydney_nilai8" label="8. Riw Jatuh Dlm 12 Bulan"><option value="0">Tidak (0)</option><option value="1">Ya (1)</option></flux:select>
                        <flux:select x-on:change="calculateSydney" wire:model="penilaian_jatuhsydney_nilai9" label="9. Osteoporosis"><option value="0">Tidak (0)</option><option value="1">Ya (1)</option></flux:select>
                        <flux:select x-on:change="calculateSydney" wire:model="penilaian_jatuhsydney_nilai10" label="10. Gangg Dengar/Lihat"><option value="0">Tidak (0)</option><option value="1">Ya (1)</option></flux:select>
                        <flux:select x-on:change="calculateSydney" wire:model="penilaian_jatuhsydney_nilai11" label="11. Usia 70 Tahun Ke Atas"><option value="0">Tidak (0)</option><option value="1">Ya (1)</option></flux:select>
                        <div class="mt-4 p-4 bg-[#4C5C2D]/5 rounded-xl border border-[#4C5C2D]/20 flex justify-between items-center">
                            <span class="font-bold text-neutral-700 text-xs" x-text="sydneyRiskLevel"></span>
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-neutral-800">Total:</span>
                                <span class="text-2xl font-black text-[#4C5C2D]" x-text="sydneyScore"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Section 9: Skrining Gizi --}}
        <div id="section-9" class="bg-white dark:bg-neutral-800 rounded-2xl border border-neutral-200 dark:border-neutral-700 shadow-sm overflow-hidden">
            <div class="px-6 py-4 bg-neutral-50 dark:bg-neutral-900/50 border-b border-neutral-200 dark:border-neutral-700 flex items-center gap-3">
                <div class="w-6 h-6 rounded-full bg-[#4C5C2D]/10 flex items-center justify-center"><span class="text-[#4C5C2D] font-bold text-xs">9</span></div>
                <h2 class="text-sm font-bold text-neutral-800 dark:text-neutral-100">VIII. Skrining Gizi</h2>
            </div>
            <div class="p-6 space-y-6">
                <flux:select wire:model="skrining_gizi1" label="1. Apakah ada penurunan BB yang tidak diinginkan selama 6 bulan terakhir?">
                    <flux:select.option value="Tidak ada penurunan berat badan">Tidak ada (Skor 0)</flux:select.option>
                    <flux:select.option value="Tidak yakin/ tidak tahu/ terasa baju lebih longgar">Tidak Yakin (Skor 2)</flux:select.option>
                    <flux:select.option value="Ya 1-5 kg">Ya 1-5 kg (Skor 1)</flux:select.option>
                    <flux:select.option value="Ya 6-10 kg">Ya 6-10 kg (Skor 2)</flux:select.option>
                    <flux:select.option value="Ya 11-15 kg">Ya 11-15 kg (Skor 3)</flux:select.option>
                    <flux:select.option value="Ya > 15 kg">Ya > 15 kg (Skor 4)</flux:select.option>
                </flux:select>
                <flux:select wire:model="skrining_gizi2" label="2. Apakah asupan makan berkurang karena tidak nafsu makan?">
                    <flux:select.option value="Tidak">Tidak (Skor 0)</flux:select.option>
                    <flux:select.option value="Ya">Ya (Skor 1)</flux:select.option>
                </flux:select>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="flex gap-2">
                        <div class="w-1/2"><flux:select wire:model="skrining_gizi_diagnosa_khusus" label="Diagnosa Khusus"><option value="Tidak">Tidak</option><option value="Ya">Ya</option></flux:select></div>
                        <div class="w-1/2"><flux:input wire:model="skrining_gizi_ket_diagnosa_khusus" label="Ket" /></div>
                    </div>
                    <div class="flex gap-2">
                        <div class="w-1/2"><flux:select wire:model="skrining_gizi_diketahui_dietisen" label="Diketahui Dietisen"><option value="Tidak">Tidak</option><option value="Ya">Ya</option></flux:select></div>
                        <div class="w-1/2"><flux:input wire:model="skrining_gizi_jam_diketahui_dietisen" type="time" label="Jam" /></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Section 10: Masalah & Rencana Keperawatan --}}
        <div id="section-10" class="bg-white dark:bg-neutral-800 rounded-2xl border border-neutral-200 dark:border-neutral-700 shadow-sm overflow-hidden">
            <div class="px-6 py-4 bg-neutral-50 dark:bg-neutral-900/50 border-b border-neutral-200 dark:border-neutral-700 flex items-center gap-3">
                <div class="w-6 h-6 rounded-full bg-[#4C5C2D]/10 flex items-center justify-center"><span class="text-[#4C5C2D] font-bold text-xs">10</span></div>
                <h2 class="text-sm font-bold text-neutral-800 dark:text-neutral-100">Masalah & Rencana Keperawatan</h2>
            </div>
            <div class="p-6 space-y-6">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    {{-- Left: Checkbox Masalah --}}
                    <div class="border border-neutral-200 dark:border-neutral-700 rounded-xl overflow-hidden">
                        <div class="px-4 py-3 bg-neutral-50 dark:bg-neutral-900/50 border-b border-neutral-200 dark:border-neutral-700">
                            <p class="text-xs font-bold text-neutral-600 uppercase tracking-wider">Pilih Masalah Keperawatan</p>
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
                            <button type="button" @click="rencanaTab = 'master'" :class="rencanaTab === 'master' ? 'bg-white dark:bg-neutral-800 text-[#4C5C2D] border-b-2 border-[#4C5C2D]' : 'bg-neutral-50 dark:bg-neutral-900/50 text-neutral-500'" class="flex-1 px-4 py-3 text-xs font-bold uppercase tracking-wider transition-colors">Rencana</button>
                            <button type="button" @click="rencanaTab = 'lainnya'" :class="rencanaTab === 'lainnya' ? 'bg-white dark:bg-neutral-800 text-[#4C5C2D] border-b-2 border-[#4C5C2D]' : 'bg-neutral-50 dark:bg-neutral-900/50 text-neutral-500'" class="flex-1 px-4 py-3 text-xs font-bold uppercase tracking-wider transition-colors">Lainnya</button>
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
    <div class="fixed right-6 top-1/2 -translate-y-1/2 z-40 hidden xl:flex flex-col gap-3">
        @php
            $sections = [
                ['id' => 1, 'icon' => 'user', 'label' => 'Identitas'],
                ['id' => 2, 'icon' => 'document-text', 'label' => 'Kesehatan'],
                ['id' => 3, 'icon' => 'heart', 'label' => 'Fisik'],
                ['id' => 4, 'icon' => 'sun', 'label' => 'Aktivitas'],
                ['id' => 5, 'icon' => 'cog-8-tooth', 'label' => 'Fungsi'],
                ['id' => 6, 'icon' => 'user-group', 'label' => 'Psikososial'],
                ['id' => 7, 'icon' => 'exclamation-triangle', 'label' => 'Nyeri'],
                ['id' => 8, 'icon' => 'chart-bar', 'label' => 'Jatuh'],
                ['id' => 9, 'icon' => 'beaker', 'label' => 'Gizi'],
                ['id' => 10, 'icon' => 'clipboard-document-check', 'label' => 'Rencana']
            ];
        @endphp

        @foreach($sections as $sec)
            <div class="group flex items-center justify-end gap-3">
                <span :class="activeSection === {{ $sec['id'] }} ? 'opacity-100 text-[#4C5C2D] scale-100' : 'opacity-0 group-hover:opacity-100 text-neutral-400 scale-95 translate-x-2 group-hover:translate-x-0'"
                      class="text-[10px] font-black uppercase tracking-widest transition-all duration-300">{{ $sec['label'] }}</span>
                <button @click="scrollTo({{ $sec['id'] }})"
                        :class="activeSection === {{ $sec['id'] }} ? 'bg-[#4C5C2D] text-white scale-110 shadow-lg' : 'bg-white dark:bg-neutral-800 text-neutral-400 hover:text-[#4C5C2D] border border-neutral-200 dark:border-neutral-700 shadow-sm hover:scale-105'"
                        class="w-10 h-10 rounded-xl flex items-center justify-center transition-all duration-300">
                    <flux:icon name="{{ $sec['icon'] }}" class="w-5 h-5" />
                </button>
            </div>
        @endforeach
    </div>
</div>
