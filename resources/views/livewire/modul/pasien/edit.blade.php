<div class="flex flex-col gap-6 pb-8">
    {{-- Header / Breadcrumb --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('modul.pasien.show', $no_rkm_medis) }}" wire:navigate
           class="flex items-center justify-center w-8 h-8 rounded-lg transition-colors hover:bg-neutral-100 dark:hover:bg-neutral-700">
            <flux:icon name="chevron-left" class="w-5 h-5 text-neutral-500" />
        </a>
        <div>
            <nav class="text-xs text-neutral-400 mb-0.5">
                <a href="{{ route('modul.index') }}" wire:navigate class="hover:underline">Modul</a>
                <span class="mx-1">/</span>
                <a href="{{ route('modul.pasien.index') }}" wire:navigate class="hover:underline">Master Pasien</a>
                <span class="mx-1">/</span>
                <a href="{{ route('modul.pasien.show', $no_rkm_medis) }}" wire:navigate class="hover:underline">Detail</a>
                <span class="mx-1">/</span>
                <span>Edit Pasien</span>
            </nav>
            <h1 class="text-xl font-bold text-neutral-800 dark:text-neutral-100">Edit Data Pasien</h1>
        </div>
    </div>

    <form wire:submit="save" class="bg-white dark:bg-neutral-800 rounded-xl border border-neutral-200 dark:border-neutral-700 overflow-hidden">
        
    {{-- =================== MODAL SCAN KTP =================== --}}
    <div
        x-data="{
            showModal: false,
            mode: null,
            stream: null,
            isProcessing: false,
            openModal() {
                this.showModal = true;
                this.mode = null;
            },
            closeModal() {
                this.stopWebcam();
                this.showModal = false;
            },
            async startWebcam() {
                this.mode = 'webcam';
                await $nextTick();
                try {
                    this.stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment', width: {ideal:1280}, height: {ideal:720} } });
                    this.$refs.videoEl.srcObject = this.stream;
                } catch(e) {
                    alert('Kamera tidak dapat diakses: ' + e.message);
                    this.mode = null;
                }
            },
            stopWebcam() {
                if (this.stream) {
                    this.stream.getTracks().forEach(t => t.stop());
                    this.stream = null;
                }
            },
            async captureWebcam() {
                const video = this.$refs.videoEl;
                const canvas = this.$refs.canvasEl;
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                canvas.getContext('2d').drawImage(video, 0, 0);
                const base64 = canvas.toDataURL('image/jpeg', 0.9);
                this.isProcessing = true;
                this.stopWebcam();
                this.showModal = false;
                await $wire.call('processKtpFromBase64', base64);
                this.isProcessing = false;
            }
        }"
        class="p-6 border-b border-neutral-100 dark:border-neutral-800 bg-gradient-to-r from-amber-50/30 to-rose-50/30 dark:from-amber-950/10 dark:to-rose-950/10"
    >
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-base font-semibold text-neutral-800 dark:text-neutral-200 flex items-center gap-2">
                    <flux:icon name="viewfinder-circle" class="w-5 h-5 text-amber-500" />
                    Koreksi Data via Scan KTP
                </h2>
                <p class="text-sm text-neutral-500 mt-1">Gunakan KTP untuk memperbarui data secara otomatis jika ada kesalahan.</p>
            </div>
            <button
                type="button"
                @click="openModal()"
                x-bind:disabled="isProcessing"
                class="flex-shrink-0 inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-white dark:bg-neutral-700 border border-neutral-200 dark:border-neutral-600 text-neutral-700 dark:text-neutral-200 text-sm font-semibold transition-all shadow-sm hover:bg-neutral-50 dark:hover:bg-neutral-600"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.069A1 1 0 0121 8.82v6.362a1 1 0 01-1.447.894L15 14M3 8a2 2 0 012-2h8a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V8z"/></svg>
                <span x-text="isProcessing ? 'Memproses...' : 'Mulai Scan'">Mulai Scan</span>
            </button>
        </div>

        {{-- Hidden Inputs & Canvas --}}
        <input type="file" x-ref="fileInput" wire:model.live="ktp_image" accept="image/*" class="hidden" />
        <canvas x-ref="canvasEl" class="hidden"></canvas>

        {{-- ===== MODAL POPUP (Reuse same as new.blade.php) ===== --}}
        <div x-show="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="background-color: rgba(0,0,0,0.6); backdrop-filter: blur(4px);" @click.self="closeModal()" x-cloak>
             <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
                <div class="flex items-center justify-between px-6 py-4 border-b border-neutral-100 dark:border-neutral-800">
                    <h3 class="font-semibold text-neutral-800 dark:text-neutral-200">Koreksi via KTP</h3>
                    <button @click="closeModal()" class="text-neutral-400 hover:text-neutral-600"><flux:icon name="x-mark" variant="mini" /></button>
                </div>
                <div x-show="mode === null" class="p-6 grid grid-cols-2 gap-4">
                    <button @click="closeModal(); $refs.fileInput.click();" class="flex flex-col items-center gap-3 p-6 rounded-xl border-2 border-dashed border-neutral-200 dark:border-neutral-700 hover:border-amber-400 transition-all">
                        <flux:icon name="folder-open" class="w-6 h-6 text-amber-500" />
                        <span class="text-sm font-medium">Unggah File</span>
                    </button>
                    <button @click="startWebcam()" class="flex flex-col items-center gap-3 p-6 rounded-xl border-2 border-dashed border-neutral-200 dark:border-neutral-700 hover:border-amber-400 transition-all">
                        <flux:icon name="video-camera" class="w-6 h-6 text-amber-500" />
                        <span class="text-sm font-medium">Webcam</span>
                    </button>
                </div>
                <div x-show="mode === 'webcam'" class="flex flex-col">
                    <video x-ref="videoEl" autoplay playsinline muted class="w-full bg-black"></video>
                    <div class="p-4 flex justify-between gap-3 bg-neutral-50 dark:bg-neutral-800">
                        <flux:button @click="mode = null; stopWebcam();" size="sm">Batal</flux:button>
                        <flux:button @click="captureWebcam()" variant="primary" size="sm">Tangkap Foto</flux:button>
                    </div>
                </div>
             </div>
        </div>
    </div>


        <div class="p-6">
            <div class="space-y-8">
                
                {{-- SECTION 1: IDENTITAS UTAMA --}}
                <section>
                    <div class="flex items-center gap-2 mb-4">
                        <div class="w-1 h-5 bg-indigo-500 rounded-full"></div>
                        <h2 class="text-sm font-bold text-neutral-800 dark:text-neutral-200 uppercase tracking-wider">Identitas Utama Pasien</h2>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4">
                        <flux:input wire:model="no_rkm_medis" label="No. Rekam Medis" read-only disabled />
                        <div class="md:col-span-2">
                            <flux:input wire:model="nm_pasien" label="Nama Lengkap Pasien" placeholder="Masukkan nama sesuai identitas" required />
                        </div>
                        <flux:input wire:model="no_ktp" label="No. KTP / SIM / Paspor" placeholder="16 digit NIK" />
                        
                        <flux:radio.group wire:model="jk" label="Jenis Kelamin" variant="segmented">
                            <flux:radio value="L" label="Laki-laki" />
                            <flux:radio value="P" label="Perempuan" />
                        </flux:radio.group>
                        
                        <flux:input wire:model="tmp_lahir" label="Tempat Lahir" placeholder="Kota kelahiran" required />
                        <flux:input type="date" wire:model.live="tgl_lahir" label="Tanggal Lahir" max="{{ date('Y-m-d') }}" required />
                        <flux:input wire:model="umur" label="Umur Saat Ini" read-only />
                    </div>
                </section>

                <hr class="border-neutral-100 dark:border-neutral-800">

                {{-- SECTION 2: DATA SOSIAL & PENDUKUNG --}}
                <section>
                    <div class="flex items-center gap-2 mb-4">
                        <div class="w-1 h-5 bg-emerald-500 rounded-full"></div>
                        <h2 class="text-sm font-bold text-neutral-800 dark:text-neutral-200 uppercase tracking-wider">Data Sosial & Pendukung</h2>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4">
                        <flux:select wire:model="agama" label="Agama" required>
                            <flux:select.option value="ISLAM">ISLAM</flux:select.option>
                            <flux:select.option value="KRISTEN">KRISTEN</flux:select.option>
                            <flux:select.option value="KATOLIK">KATOLIK</flux:select.option>
                            <flux:select.option value="HINDU">HINDU</flux:select.option>
                            <flux:select.option value="BUDHA">BUDHA</flux:select.option>
                            <flux:select.option value="KONGHUCU">KONGHUCU</flux:select.option>
                        </flux:select>
                        
                        <flux:select wire:model="pnd" label="Pendidikan Terakhir" required>
                            @foreach($pendidikans as $p)
                                <flux:select.option value="{{ $p }}">{{ $p }}</flux:select.option>
                            @endforeach
                        </flux:select>
                        
                        <flux:select wire:model="stts_nikah" label="Status Pernikahan" required>
                            <flux:select.option value="BELUM MENIKAH">BELUM MENIKAH</flux:select.option>
                            <flux:select.option value="MENIKAH">MENIKAH</flux:select.option>
                            <flux:select.option value="JANDA">JANDA</flux:select.option>
                            <flux:select.option value="DUDHA">DUDHA</flux:select.option>
                        </flux:select>
                        
                        <flux:select wire:model="gol_darah" label="Golongan Darah">
                            <flux:select.option value="-">-</flux:select.option>
                            <flux:select.option value="A">A</flux:select.option>
                            <flux:select.option value="B">B</flux:select.option>
                            <flux:select.option value="AB">AB</flux:select.option>
                            <flux:select.option value="O">O</flux:select.option>
                        </flux:select>

                        <flux:select wire:model="suku_bangsa" label="Suku / Bangsa">
                            @foreach($sukuBangsas as $s)
                                <flux:select.option value="{{ $s->id }}">{{ $s->nama_suku_bangsa }}</flux:select.option>
                            @endforeach
                        </flux:select>

                        <flux:select wire:model="bahasa_pasien" label="Bahasa yang Digunakan">
                            @foreach($bahasaPasiens as $b)
                                <flux:select.option value="{{ $b->id }}">{{ $b->nama_bahasa }}</flux:select.option>
                            @endforeach
                        </flux:select>

                        <flux:select wire:model="cacat_fisik" label="Cacat Fisik">
                            @foreach($cacatFisiks as $c)
                                <flux:select.option value="{{ $c->id }}">{{ $c->nama_cacat }}</flux:select.option>
                            @endforeach
                        </flux:select>

                        <flux:input wire:model="nm_ibu" label="Nama Ibu Kandung" placeholder="Wajib diisi" required />
                    </div>
                </section>

                <hr class="border-neutral-100 dark:border-neutral-800">

                {{-- SECTION 3: ALAMAT & KONTAK --}}
                <section>
                    <div class="flex items-center gap-2 mb-4">
                        <div class="w-1 h-5 bg-amber-500 rounded-full"></div>
                        <h2 class="text-sm font-bold text-neutral-800 dark:text-neutral-200 uppercase tracking-wider">Alamat & Kontak Pasien</h2>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="md:col-span-2">
                            <flux:textarea wire:model="alamat" label="Alamat Domisili" placeholder="Jl. Contoh No. 123..." rows="2" required />
                        </div>
                        <flux:input wire:model="no_tlp" label="No. Telepon / HP" placeholder="08xxxxxx" />
                        <flux:input type="email" wire:model="email" label="Alamat Email" placeholder="nama@email.com" />

                        <flux:select wire:model="kd_kel" label="Kelurahan" searchable>
                            @foreach($kelurahans as $kel)
                                <flux:select.option value="{{ $kel->kd_kel }}">{{ $kel->nm_kel }}</flux:select.option>
                            @endforeach
                        </flux:select>

                        <flux:select wire:model="kd_kec" label="Kecamatan" searchable>
                            @foreach($kecamatans as $kec)
                                <flux:select.option value="{{ $kec->kd_kec }}">{{ $kec->nm_kec }}</flux:select.option>
                            @endforeach
                        </flux:select>

                        <flux:select wire:model="kd_kab" label="Kabupaten/Kota" searchable>
                            @foreach($kabupatens as $kab)
                                <flux:select.option value="{{ $kab->kd_kab }}">{{ $kab->nm_kab }}</flux:select.option>
                            @endforeach
                        </flux:select>

                        <flux:select wire:model="kd_prop" label="Provinsi">
                            @foreach($propinsis as $prop)
                                <flux:select.option value="{{ $prop->kd_prop }}">{{ $prop->nm_prop }}</flux:select.option>
                            @endforeach
                        </flux:select>
                    </div>
                </section>

                <hr class="border-neutral-100 dark:border-neutral-800">

                {{-- SECTION 4: DATA PENANGGUNG JAWAB --}}
                <section>
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-2">
                            <div class="w-1 h-5 bg-rose-500 rounded-full"></div>
                            <h2 class="text-sm font-bold text-neutral-800 dark:text-neutral-200 uppercase tracking-wider">Data Keluarga / Penanggung Jawab</h2>
                        </div>
                        <flux:button size="xs" icon="document-duplicate" wire:click="copyAddressToPj" variant="ghost">Samakan Alamat degan Pasien</flux:button>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4">
                        <flux:select wire:model="keluarga" label="Hubungan Keluarga" required>
                            @foreach($keluargas as $k)
                                <flux:select.option value="{{ $k }}">{{ $k }}</flux:select.option>
                            @endforeach
                        </flux:select>
                        <div class="md:col-span-2">
                            <flux:input wire:model="namakeluarga" label="Nama Lengkap PJ" placeholder="Nama penanggung jawab" required />
                        </div>
                        <flux:input wire:model="pekerjaanpj" label="Pekerjaan PJ" placeholder="Pekerjaan penanggung jawab" />

                        <div class="md:col-span-2 lg:col-span-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                            <div class="lg:col-span-2">
                                <flux:input wire:model="alamatpj" label="Alamat PJ" placeholder="Alamat penanggung jawab" required />
                            </div>
                            <flux:input wire:model="kelurahanpj" label="Kelurahan PJ" />
                            <flux:input wire:model="kecamatanpj" label="Kecamatan PJ" />
                            <flux:input wire:model="kabupatenpj" label="Kabupaten PJ" />
                            <flux:input wire:model="propinsipj" label="Provinsi PJ" />
                        </div>
                    </div>
                </section>

                <hr class="border-neutral-100 dark:border-neutral-800">

                {{-- SECTION 5: ASURANSI & INSTANSI --}}
                <section>
                    <div class="flex items-center gap-2 mb-4">
                        <div class="w-1 h-5 bg-blue-500 rounded-full"></div>
                        <h2 class="text-sm font-bold text-neutral-800 dark:text-neutral-200 uppercase tracking-wider">Pekerjaan & Asuransi</h2>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4">
                        <flux:select wire:model="kd_pj" label="Jenis Bayar / Asuransi" required>
                            @foreach($penjabs as $pj)
                                <flux:select.option value="{{ $pj->kd_pj }}">{{ $pj->png_jawab }}</flux:select.option>
                            @endforeach
                        </flux:select>
                        <flux:input wire:model="no_peserta" label="No. Kartu Peserta" />
                        
                        <flux:input wire:model="pekerjaan" label="Pekerjaan Pasien" />
                        <flux:input wire:model="nip" label="NIP / NRP" />

                        <flux:select wire:model="perusahaan_pasien" label="Instansi / Perusahaan">
                            <flux:select.option value="-">TIDAK ADA / UMUM</flux:select.option>
                            @foreach($perusahaans as $p)
                                <flux:select.option value="{{ $p->kode_perusahaan }}">{{ $p->nama_perusahaan }}</flux:select.option>
                            @endforeach
                        </flux:select>

                        <flux:input type="date" wire:model="tgl_daftar" label="Tanggal Pertama Daftar" disabled />
                    </div>
                </section>

            </div>
        </div>
        
        <div class="px-6 py-4 bg-neutral-50 dark:bg-neutral-800/50 border-t border-neutral-200 dark:border-neutral-700 flex justify-end gap-3 rounded-b-xl">
            <flux:button href="{{ route('modul.pasien.show', $no_rkm_medis) }}" wire:navigate variant="ghost">Batal</flux:button>
            <flux:button type="submit" variant="primary" icon="check">Simpan Perubahan</flux:button>
        </div>
    </form>
</div>
