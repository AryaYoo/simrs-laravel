<div class="flex flex-col gap-6 pb-8">
    {{-- Header / Breadcrumb --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('modul.pasien.index') }}" wire:navigate class="flex items-center justify-center w-10 h-8 rounded-md bg-[#4C5C2D] transition-colors hover:bg-[#3d4b24] shadow-sm">
            <flux:icon name="chevron-left" class="w-5 h-5 text-white" />
        </a>
        <div>
            <nav class="text-xs text-neutral-400 mb-0.5">
                <a href="{{ route('modul.index') }}" wire:navigate class="hover:underline">Modul</a>
                <span class="mx-1">/</span>
                <a href="{{ route('modul.pasien.index') }}" wire:navigate class="hover:underline">Master Pasien</a>
                <span class="mx-1">/</span>
                <span>Tambah Pasien</span>
            </nav>
            <h1 class="text-xl font-bold text-neutral-800 dark:text-neutral-100">Tambah Pasien Baru</h1>
        </div>
    </div>

    <style>
        .ktp-highlight-empty,
        [data-ktp-empty="true"] {
            border-color: #10b981 !important;
            border-width: 2px !important;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.25) !important;
            background-color: rgba(16, 185, 129, 0.04) !important;
            transition: border-color 0.2s ease, box-shadow 0.2s ease, background-color 0.2s ease;
        }
    </style>

    <form
        wire:submit="save"
        x-ref="patientForm"
        x-data="{
            hasKtpHighlight: false,
            emptyCount: 0,
            highlightEmptyFields() {
                const form = this.$refs.patientForm;
                if (!form) return;
                const fields = form.querySelectorAll('input:not([type=hidden]):not([type=file]):not([type=checkbox]):not([type=radio]):not([disabled]):not([readonly]), select:not([disabled]), textarea:not([disabled]):not([readonly])');
                let count = 0;
                fields.forEach(field => {
                    if (field.closest('.ktp-scan-container')) return;
                    const val = (field.value || '').trim();
                    if (val === '') {
                        field.classList.add('ktp-highlight-empty');
                        field.setAttribute('data-ktp-empty', 'true');
                        count++;
                        const clearHighlight = () => {
                            if ((field.value || '').trim() !== '') {
                                field.classList.remove('ktp-highlight-empty');
                                field.removeAttribute('data-ktp-empty');
                                field.removeEventListener('input', clearHighlight);
                                field.removeEventListener('change', clearHighlight);
                                this.updateCount();
                            }
                        };
                        field.addEventListener('input', clearHighlight);
                        field.addEventListener('change', clearHighlight);
                    } else {
                        field.classList.remove('ktp-highlight-empty');
                        field.removeAttribute('data-ktp-empty');
                    }
                });
                this.emptyCount = count;
                this.hasKtpHighlight = count > 0;
            },
            updateCount() {
                const remaining = this.$refs.patientForm ? this.$refs.patientForm.querySelectorAll('.ktp-highlight-empty').length : 0;
                this.emptyCount = remaining;
                if (remaining === 0) this.hasKtpHighlight = false;
            },
            dismissHighlight() {
                if (!this.$refs.patientForm) return;
                this.$refs.patientForm.querySelectorAll('.ktp-highlight-empty').forEach(el => {
                    el.classList.remove('ktp-highlight-empty');
                    el.removeAttribute('data-ktp-empty');
                });
                this.hasKtpHighlight = false;
                this.emptyCount = 0;
            }
        }"
        @ktp-autofilled.window="setTimeout(() => highlightEmptyFields(), 250)"
        class="bg-white dark:bg-neutral-800 rounded-xl border border-neutral-200 dark:border-neutral-700 overflow-hidden"
    >
        
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
        class="ktp-scan-container p-6 border-b border-neutral-100 dark:border-neutral-800 bg-gradient-to-r from-indigo-50/50 to-violet-50/50 dark:from-indigo-950/20 dark:to-violet-950/20"
    >
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-base font-semibold text-neutral-800 dark:text-neutral-200 flex items-center gap-2">
                    <flux:icon name="viewfinder-circle" class="w-5 h-5 text-indigo-500" />
                    Scan KTP (OCR)
                </h2>
                <p class="text-sm text-neutral-500 mt-1">
                    Scan KTP menggunakan kamera, berkas foto, atau paste clipboard (Ctrl+V) untuk mengisi data pasien secara otomatis.
                </p>
            </div>
            <div>
                <button
                    type="button"
                    @click="openModal()"
                    x-bind:disabled="isProcessing"
                    x-bind:class="isProcessing ? 'opacity-60 cursor-not-allowed' : 'hover:bg-indigo-700 active:scale-95'"
                    class="flex-shrink-0 inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-indigo-600 text-white text-sm font-semibold transition-all shadow-sm"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.069A1 1 0 0121 8.82v6.362a1 1 0 01-1.447.894L15 14M3 8a2 2 0 012-2h8a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V8z"/></svg>
                    <span x-text="isProcessing ? 'Memproses...' : 'Scan KTP'">Scan KTP</span>
                </button>
            </div>
        </div>

        {{-- Loading Bar saat Processing --}}
        <div x-show="isProcessing" class="mt-4 flex items-center gap-3 text-sm text-indigo-600 dark:text-indigo-400">
            <svg class="animate-spin h-5 w-5 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span>AI sedang membaca & menganalisis KTP Anda, mohon tunggu...</span>
        </div>

        {{-- Loading Livewire saat Upload File --}}
        <div wire:loading wire:target="ktp_image" class="mt-4 flex items-center gap-3 text-sm text-indigo-600 dark:text-indigo-400">
            <svg class="animate-spin h-5 w-5 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span>AI sedang membaca & menganalisis KTP Anda, mohon tunggu...</span>
        </div>

        {{-- Hidden File Input --}}
        <input type="file" x-ref="fileInput" wire:model.live="ktp_image" accept="image/*" class="hidden" />
        <input type="file" x-ref="cameraInput" wire:model.live="ktp_image" accept="image/*" capture="environment" class="hidden" />
        <canvas x-ref="canvasEl" class="hidden"></canvas>

        {{-- ===== MODAL POPUP ===== --}}
        <div
            x-show="showModal"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 flex items-center justify-center p-4"
            style="background-color: rgba(0,0,0,0.6); backdrop-filter: blur(4px);"
            @click.self="closeModal()"
        >
            <div
                x-show="showModal"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="bg-white dark:bg-neutral-900 rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden"
            >
                {{-- Modal Header --}}
                <div class="flex items-center justify-between px-6 py-4 border-b border-neutral-100 dark:border-neutral-800">
                    <div class="flex items-center gap-2">
                        <flux:icon name="viewfinder-circle" class="w-5 h-5 text-indigo-500" />
                        <h3 class="font-semibold text-neutral-800 dark:text-neutral-200">Scan & Analisis KTP Pasien</h3>
                    </div>
                    <button type="button" @click="closeModal()" class="p-1.5 rounded-lg hover:bg-neutral-100 dark:hover:bg-neutral-800 text-neutral-400 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                {{-- Modal Body: Pilih Mode --}}
                <div x-show="mode === null" class="p-6 flex flex-col gap-4">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        {{-- Opsi 1: Upload File --}}
                        <button
                            type="button"
                            @click="closeModal(); $refs.fileInput.click();"
                            class="flex flex-col items-center gap-2.5 p-4 rounded-xl border-2 border-dashed border-neutral-200 dark:border-neutral-700 hover:border-indigo-400 hover:bg-indigo-50/50 dark:hover:bg-indigo-950/20 transition-all group"
                        >
                            <div class="w-11 h-11 rounded-full bg-indigo-100 dark:bg-indigo-900/40 flex items-center justify-center group-hover:scale-110 transition-transform">
                                <flux:icon name="folder-open" class="w-5 h-5 text-indigo-600 dark:text-indigo-400" />
                            </div>
                            <div class="text-center">
                                <p class="font-semibold text-xs text-neutral-800 dark:text-neutral-200">Dari Folder</p>
                                <p class="text-[10px] text-neutral-400 mt-0.5">JPG, PNG, WEBP</p>
                            </div>
                        </button>

                        {{-- Opsi 2: Webcam --}}
                        <button
                            type="button"
                            @click="startWebcam()"
                            class="flex flex-col items-center gap-2.5 p-4 rounded-xl border-2 border-dashed border-neutral-200 dark:border-neutral-700 hover:border-violet-400 hover:bg-violet-50/50 dark:hover:bg-violet-950/20 transition-all group"
                        >
                            <div class="w-11 h-11 rounded-full bg-violet-100 dark:bg-violet-900/40 flex items-center justify-center group-hover:scale-110 transition-transform">
                                <flux:icon name="video-camera" class="w-5 h-5 text-violet-600 dark:text-violet-400" />
                            </div>
                            <div class="text-center">
                                <p class="font-semibold text-xs text-neutral-800 dark:text-neutral-200">Kamera/Webcam</p>
                                <p class="text-[10px] text-neutral-400 mt-0.5">Ambil foto langsung</p>
                            </div>
                        </button>

                        {{-- Opsi 3: Paste Clipboard (Ctrl + V) --}}
                        <button
                            type="button"
                            @click="pasteFromClipboard()"
                            class="flex flex-col items-center gap-2.5 p-4 rounded-xl border-2 border-dashed border-emerald-300 dark:border-emerald-700/60 bg-emerald-50/30 dark:bg-emerald-950/20 hover:border-emerald-500 hover:bg-emerald-50/70 transition-all group"
                        >
                            <div class="w-11 h-11 rounded-full bg-emerald-100 dark:bg-emerald-900/40 flex items-center justify-center group-hover:scale-110 transition-transform">
                                <flux:icon name="clipboard-document" class="w-5 h-5 text-emerald-600 dark:text-emerald-400" />
                            </div>
                            <div class="text-center">
                                <p class="font-semibold text-xs text-emerald-800 dark:text-emerald-300">Paste (Ctrl + V)</p>
                                <p class="text-[10px] text-neutral-400 mt-0.5">Dari clipboard</p>
                            </div>
                        </button>
                    </div>

                    {{-- Clipboard Helper Box --}}
                    <div class="p-3 bg-neutral-50 dark:bg-neutral-800/60 rounded-xl border border-neutral-200/80 dark:border-neutral-700 text-xs text-neutral-600 dark:text-neutral-400 flex items-center gap-2.5">
                        <span class="inline-flex items-center justify-center px-2 py-0.5 text-[10px] font-bold font-mono bg-white dark:bg-neutral-700 border border-neutral-300 dark:border-neutral-600 rounded shadow-xs text-neutral-700 dark:text-neutral-200">
                            Ctrl + V
                        </span>
                        <span class="text-[11px] leading-tight">
                            Bisa langsung tekan <strong>Ctrl + V</strong> saat jendela ini terbuka untuk langsung menganalisis gambar KTP dari clipboard.
                        </span>
                    </div>
                </div>

                {{-- Modal Body: Webcam View --}}
                <div x-show="mode === 'webcam'" class="flex flex-col">
                    <div class="relative bg-black" style="aspect-ratio: 16/9;">
                        <video x-ref="videoEl" autoplay playsinline muted class="w-full h-full object-cover"></video>
                        {{-- Overlay guide frame --}}
                        <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                            <div class="border-2 border-white/60 rounded-lg w-4/5 h-3/4"
                                style="box-shadow: 0 0 0 9999px rgba(0,0,0,0.3);">
                            </div>
                        </div>
                        <p class="absolute bottom-3 left-0 right-0 text-center text-white/70 text-xs">Arahkan KTP ke dalam bingkai</p>
                    </div>
                    <div class="p-4 flex items-center justify-between gap-3 bg-neutral-50 dark:bg-neutral-800">
                        <flux:button @click="mode = null; stopWebcam();" variant="ghost" icon="arrow-left" size="sm">Kembali</flux:button>
                        <button
                            type="button"
                            @click="captureWebcam()"
                            class="flex items-center gap-2 px-5 py-2.5 rounded-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm transition-colors shadow-md shadow-indigo-200 active:scale-95"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="3" fill="currentColor"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/></svg>
                            Ambil Foto KTP
                        </button>
                        <div class="w-20"></div>
                    </div>
                </div>
            </div>
        </div>
        {{-- Banner Notifikasi Form Kosong Pasca Auto-Fill KTP --}}
        <div
            x-show="hasKtpHighlight"
            x-cloak
            x-transition
            class="mx-6 mt-5 p-3.5 rounded-lg bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-300 dark:border-emerald-800/60 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-sm text-emerald-800 dark:text-emerald-200 shadow-xs"
        >
            <div class="flex items-center gap-2.5">
                <span class="flex h-2.5 w-2.5 relative flex-shrink-0">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                </span>
                <span>
                    <strong>Data KTP Berhasil Diterapkan!</strong> Form dengan tepian hijau (<strong x-text="emptyCount"></strong> kolom) menandakan data yang masih kosong. Tepian hijau akan otomatis kembali ke warna default begitu kolom diisi.
                </span>
            </div>
            <button
                type="button"
                @click="dismissHighlight()"
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
                        <div class="flex flex-col gap-1.5">
                            <div class="flex items-center justify-between">
                                <label class="text-sm font-medium text-neutral-800 dark:text-neutral-200">No. Rekam Medis</label>
                                <div class="flex items-center gap-1.5 grayscale" title="Gunakan penomoran otomatis">
                                    <input type="checkbox" wire:model.live="auto_rm" class="rounded border-neutral-300 text-indigo-600 focus:ring-indigo-500 w-3.5 h-3.5">
                                    <span class="text-[10px] font-bold text-neutral-500 uppercase">Otomatis</span>
                                </div>
                            </div>
                            <flux:input wire:model.blur="no_rkm_medis" placeholder="Masukkan No. RM" :disabled="$auto_rm" />
                        </div>
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
                        <flux:input wire:model="umur" label="Umur Saat Ini" placeholder="Otomatis" read-only />
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
                            @fluxOptions($keluargas, null, null) {{-- Menggunakan data keluarga atau status nikah --}}
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
                            {{-- Propinsi PJ sebagai input text agar fleksibel --}}
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
                        <flux:input wire:model="no_peserta" label="No. Kartu Peserta" placeholder="No. BPJS/Asuransi" />
                        
                        <flux:input wire:model="pekerjaan" label="Pekerjaan Pasien" placeholder="Pekerjaan saat ini" />
                        <flux:input wire:model="nip" label="NIP / NRP" placeholder="Nomor Induk Pegawai" />

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
            <flux:button href="{{ route('modul.pasien.index') }}" wire:navigate variant="ghost">Batal</flux:button>
            <flux:button type="submit" variant="primary" icon="check">Simpan Pasien Baru</flux:button>
        </div>
    </form>
</div>
