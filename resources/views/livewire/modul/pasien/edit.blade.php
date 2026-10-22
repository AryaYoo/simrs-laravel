<div class="flex flex-col gap-6 pb-8">
    {{-- Header / Breadcrumb --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('modul.pasien.show', $no_rkm_medis) }}" wire:navigate class="flex items-center justify-center w-10 h-8 rounded-md bg-[#4C5C2D] transition-colors hover:bg-[#3d4b24] shadow-sm">
            <flux:icon name="chevron-left" class="w-5 h-5 text-white" />
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

    <style>
        .ktp-highlight-empty,
        [data-ktp-empty="true"] {
            border-color: #10b981 !important;
            border-width: 2px !important;
            outline: 2px solid #10b981 !important;
            outline-offset: -1px !important;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.3) !important;
            background-color: rgba(16, 185, 129, 0.05) !important;
            transition: border-color 0.2s ease, outline 0.2s ease, box-shadow 0.2s ease, background-color 0.2s ease;
        }
    </style>

    <form wire:submit="save" id="patient-edit-form" class="bg-white dark:bg-neutral-800 rounded-xl border border-neutral-200 dark:border-neutral-700 overflow-hidden">
        
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
                if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                    if (confirm('Kamera browser langsung hanya diizinkan pada koneksi aman (HTTPS) atau localhost.\n\nKarena diakses melalui IP (' + window.location.hostname + '), browser membatasi WebRTC.\n\nKlik OK untuk membuka kamera / pemilih file perangkat, atau Batal.')) {
                        this.closeModal();
                        if (this.$refs.cameraInput) {
                            this.$refs.cameraInput.click();
                        } else {
                            this.$refs.fileInput.click();
                        }
                    }
                    return;
                }
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
            async processImageBase64(base64) {
                this.isProcessing = true;
                this.stopWebcam();
                this.showModal = false;
                await $wire.call('processKtpFromBase64', base64);
                this.isProcessing = false;
                if (typeof triggerKtpHighlight === 'function') {
                    setTimeout(triggerKtpHighlight, 150);
                    setTimeout(triggerKtpHighlight, 450);
                }
            },
            async captureWebcam() {
                const video = this.$refs.videoEl;
                const canvas = this.$refs.canvasEl;
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                canvas.getContext('2d').drawImage(video, 0, 0);
                const base64 = canvas.toDataURL('image/jpeg', 0.9);
                await this.processImageBase64(base64);
            },
            handlePaste(event) {
                if (this.isProcessing) return;
                const items = (event.clipboardData || event.originalEvent?.clipboardData)?.items;
                if (!items) return;
                for (let i = 0; i < items.length; i++) {
                    if (items[i].type.indexOf('image') !== -1) {
                        event.preventDefault();
                        const file = items[i].getAsFile();
                        const reader = new FileReader();
                        reader.onload = async (e) => {
                            await this.processImageBase64(e.target.result);
                        };
                        reader.readAsDataURL(file);
                        break;
                    }
                }
            },
            async pasteFromClipboard() {
                if (navigator.clipboard && navigator.clipboard.read) {
                    try {
                        const items = await navigator.clipboard.read();
                        for (const item of items) {
                            const imageType = item.types.find(t => t.startsWith('image/'));
                            if (imageType) {
                                const blob = await item.getType(imageType);
                                const reader = new FileReader();
                                reader.onload = async (e) => {
                                    await this.processImageBase64(e.target.result);
                                };
                                reader.readAsDataURL(blob);
                                return;
                            }
                        }
                        alert('Tidak ada gambar di clipboard. Silakan salin (copy) gambar KTP terlebih dahulu lalu tekan Ctrl + V.');
                    } catch (e) {
                        alert('Akses clipboard otomatis dibatasi oleh browser. Silakan langsung tekan Ctrl + V pada keyboard Anda.');
                    }
                } else {
                    alert('Silakan langsung tekan tombol Ctrl + V pada keyboard Anda untuk paste gambar KTP.');
                }
            }
        }"
        @paste.window="if (showModal) handlePaste($event)"
        class="ktp-scan-container p-6 border-b border-neutral-100 dark:border-neutral-800 bg-gradient-to-r from-amber-50/30 to-rose-50/30 dark:from-amber-950/10 dark:to-rose-950/10"
    >
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-base font-semibold text-neutral-800 dark:text-neutral-200 flex items-center gap-2">
                    <flux:icon name="viewfinder-circle" class="w-5 h-5 text-amber-500" />
                    Koreksi Data via Scan KTP
                </h2>
                <p class="text-sm text-neutral-500 mt-1">
                    Gunakan kamera, berkas foto KTP, atau paste clipboard (Ctrl+V) untuk memperbarui data pasien secara otomatis.
                </p>
            </div>
            <div>
                <button
                    type="button"
                    @click="openModal()"
                    x-bind:disabled="isProcessing"
                    class="flex-shrink-0 inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-white dark:bg-neutral-700 border border-neutral-200 dark:border-neutral-600 text-neutral-700 dark:text-neutral-200 text-sm font-semibold transition-all shadow-sm hover:bg-neutral-50 dark:hover:bg-neutral-600"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.069A1 1 0 0121 8.82v6.362a1 1 0 01-1.447.894L15 14M3 8a2 2 0 012-2h8a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V8z"/></svg>
                    <span x-text="isProcessing ? 'Memproses...' : 'Scan KTP'">Scan KTP</span>
                </button>
            </div>
        </div>

        {{-- Hidden Inputs & Canvas --}}
        <input type="file" x-ref="fileInput" wire:model.live="ktp_image" accept="image/*" class="hidden" />
        <input type="file" x-ref="cameraInput" wire:model.live="ktp_image" accept="image/*" capture="environment" class="hidden" />
        <canvas x-ref="canvasEl" class="hidden"></canvas>

        {{-- ===== MODAL POPUP (Reuse same as new.blade.php) ===== --}}
        <div x-show="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="background-color: rgba(0,0,0,0.6); backdrop-filter: blur(4px);" @click.self="closeModal()" x-cloak>
             <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden">
                <div class="flex items-center justify-between px-6 py-4 border-b border-neutral-100 dark:border-neutral-800">
                    <h3 class="font-semibold text-neutral-800 dark:text-neutral-200">Koreksi & Analisis via KTP</h3>
                    <button type="button" @click="closeModal()" class="text-neutral-400 hover:text-neutral-600"><flux:icon name="x-mark" variant="mini" /></button>
                </div>
                <div x-show="mode === null" class="p-6 flex flex-col gap-4">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <button type="button" @click="closeModal(); $refs.fileInput.click();" class="flex flex-col items-center gap-2.5 p-4 rounded-xl border-2 border-dashed border-neutral-200 dark:border-neutral-700 hover:border-amber-400 transition-all group">
                            <flux:icon name="folder-open" class="w-6 h-6 text-amber-500 group-hover:scale-110 transition-transform" />
                            <span class="text-xs font-medium">Unggah File</span>
                        </button>
                        <button type="button" @click="startWebcam()" class="flex flex-col items-center gap-2.5 p-4 rounded-xl border-2 border-dashed border-neutral-200 dark:border-neutral-700 hover:border-amber-400 transition-all group">
                            <flux:icon name="video-camera" class="w-6 h-6 text-amber-500 group-hover:scale-110 transition-transform" />
                            <span class="text-xs font-medium">Webcam</span>
                        </button>
                        <button type="button" @click="pasteFromClipboard()" class="flex flex-col items-center gap-2.5 p-4 rounded-xl border-2 border-dashed border-emerald-300 dark:border-emerald-700/60 bg-emerald-50/30 dark:bg-emerald-950/20 hover:border-emerald-500 transition-all group">
                            <flux:icon name="clipboard-document" class="w-6 h-6 text-emerald-600 dark:text-emerald-400 group-hover:scale-110 transition-transform" />
                            <span class="text-xs font-medium text-emerald-800 dark:text-emerald-300">Paste (Ctrl+V)</span>
                        </button>
                    </div>

                    <div class="p-3 bg-neutral-50 dark:bg-neutral-800/60 rounded-xl border border-neutral-200/80 dark:border-neutral-700 text-xs text-neutral-600 dark:text-neutral-400 flex items-center gap-2.5">
                        <span class="inline-flex items-center justify-center px-2 py-0.5 text-[10px] font-bold font-mono bg-white dark:bg-neutral-700 border border-neutral-300 dark:border-neutral-600 rounded shadow-xs text-neutral-700 dark:text-neutral-200">
                            Ctrl + V
                        </span>
                        <span class="text-[11px] leading-tight">
                            Bisa langsung tekan <strong>Ctrl + V</strong> saat jendela ini terbuka untuk langsung menganalisis gambar KTP dari clipboard.
                        </span>
                    </div>
                </div>
                <div x-show="mode === 'webcam'" class="flex flex-col">
                    <video x-ref="videoEl" autoplay playsinline muted class="w-full bg-black"></video>
                    <div class="p-4 flex justify-between gap-3 bg-neutral-50 dark:bg-neutral-800">
                        <flux:button @click="mode = null; stopWebcam();" size="sm">Batal</flux:button>
                        <flux:button @click="captureWebcam()" variant="primary" size="sm">Tangkap Foto</flux:button>
                    </div>
                </div>
             </div>
        {{-- Banner Notifikasi Form Kosong Pasca Auto-Fill KTP --}}
        <div
            id="ktp-highlight-banner"
            style="display: none;"
            class="mx-6 mt-5 p-3.5 rounded-lg bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-300 dark:border-emerald-800/60 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-sm text-emerald-800 dark:text-emerald-200 shadow-xs"
        >
            <div class="flex items-center gap-2.5">
                <span class="flex h-2.5 w-2.5 relative flex-shrink-0">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                </span>
                <span>
                    <strong>Data KTP Berhasil Diterapkan!</strong> Form dengan tepian hijau (<strong id="ktp-empty-count">0</strong> kolom) menandakan data yang masih kosong. Tepian hijau akan otomatis kembali normal begitu kolom diisi.
                </span>
            </div>
            <button
                type="button"
                onclick="dismissKtpHighlight()"
                class="self-end sm:self-auto flex-shrink-0 text-xs font-semibold px-2.5 py-1 rounded bg-white dark:bg-neutral-800 border border-emerald-300 dark:border-emerald-700 text-emerald-700 dark:text-emerald-300 hover:bg-emerald-100/60 transition-colors shadow-xs"
            >
                Tutup Sorotan
            </button>
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

    <script>
        function triggerKtpHighlight() {
            const form = document.getElementById('patient-edit-form') || document.querySelector('form');
            if (!form) return;

            const inputs = form.querySelectorAll(
                'input:not([type="hidden"]):not([type="file"]):not([type="checkbox"]):not([type="radio"]):not([disabled]):not([readonly]), ' +
                'select:not([disabled]), ' +
                'textarea:not([disabled]):not([readonly])'
            );

            let emptyCount = 0;

            inputs.forEach(el => {
                if (el.closest('.ktp-scan-container') || el.closest('[x-ref="videoEl"]')) return;

                const val = (el.value || '').trim();
                const isEmpty = (val === '' || val === '-');

                if (isEmpty) {
                    emptyCount++;
                    el.classList.add('ktp-highlight-empty');
                    el.setAttribute('data-ktp-empty', 'true');
                    el.style.setProperty('border-color', '#10b981', 'important');
                    el.style.setProperty('outline', '2px solid #10b981', 'important');
                    el.style.setProperty('outline-offset', '-1px', 'important');
                    el.style.setProperty('box-shadow', '0 0 0 3px rgba(16, 185, 129, 0.3)', 'important');
                    el.style.setProperty('background-color', 'rgba(16, 185, 129, 0.05)', 'important');

                    const removeHighlight = () => {
                        const currentVal = (el.value || '').trim();
                        if (currentVal !== '' && currentVal !== '-') {
                            el.classList.remove('ktp-highlight-empty');
                            el.removeAttribute('data-ktp-empty');
                            el.style.removeProperty('border-color');
                            el.style.removeProperty('outline');
                            el.style.removeProperty('outline-offset');
                            el.style.removeProperty('box-shadow');
                            el.style.removeProperty('background-color');
                            el.removeEventListener('input', removeHighlight);
                            el.removeEventListener('change', removeHighlight);

                            const remaining = form.querySelectorAll('.ktp-highlight-empty').length;
                            const countEl = document.getElementById('ktp-empty-count');
                            if (countEl) countEl.textContent = remaining;
                            if (remaining === 0) {
                                const banner = document.getElementById('ktp-highlight-banner');
                                if (banner) banner.style.display = 'none';
                            }
                        }
                    };

                    el.addEventListener('input', removeHighlight);
                    el.addEventListener('change', removeHighlight);
                } else {
                    el.classList.remove('ktp-highlight-empty');
                    el.removeAttribute('data-ktp-empty');
                    el.style.removeProperty('border-color');
                    el.style.removeProperty('outline');
                    el.style.removeProperty('outline-offset');
                    el.style.removeProperty('box-shadow');
                    el.style.removeProperty('background-color');
                }
            });

            const banner = document.getElementById('ktp-highlight-banner');
            const countEl = document.getElementById('ktp-empty-count');
            if (banner) {
                if (emptyCount > 0) {
                    if (countEl) countEl.textContent = emptyCount;
                    banner.style.display = 'flex';
                } else {
                    banner.style.display = 'none';
                }
            }
        }

        function dismissKtpHighlight() {
            const form = document.getElementById('patient-edit-form') || document.querySelector('form');
            if (!form) return;
            form.querySelectorAll('.ktp-highlight-empty').forEach(el => {
                el.classList.remove('ktp-highlight-empty');
                el.removeAttribute('data-ktp-empty');
                el.style.removeProperty('border-color');
                el.style.removeProperty('outline');
                el.style.removeProperty('outline-offset');
                el.style.removeProperty('box-shadow');
                el.style.removeProperty('background-color');
            });
            const banner = document.getElementById('ktp-highlight-banner');
            if (banner) banner.style.display = 'none';
        }

        window.addEventListener('ktp-autofilled', () => {
            setTimeout(triggerKtpHighlight, 100);
            setTimeout(triggerKtpHighlight, 400);
            setTimeout(triggerKtpHighlight, 800);
        });

        if (window.Livewire) {
            window.Livewire.on('ktp-autofilled', () => {
                setTimeout(triggerKtpHighlight, 100);
                setTimeout(triggerKtpHighlight, 400);
                setTimeout(triggerKtpHighlight, 800);
            });
        } else {
            document.addEventListener('livewire:init', () => {
                window.Livewire.on('ktp-autofilled', () => {
                    setTimeout(triggerKtpHighlight, 100);
                    setTimeout(triggerKtpHighlight, 400);
                    setTimeout(triggerKtpHighlight, 800);
                });
            });
        }
    </script>
</div>
