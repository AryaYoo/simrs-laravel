<div class="flex flex-col gap-6 pb-8">
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
                <span>Master Pasien</span>
            </nav>
            <h1 class="text-xl font-bold text-neutral-800 dark:text-neutral-100">Tambah Pasien Baru</h1>
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
        class="p-6 border-b border-neutral-100 dark:border-neutral-800 bg-gradient-to-r from-indigo-50/50 to-violet-50/50 dark:from-indigo-950/20 dark:to-violet-950/20"
    >
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-base font-semibold text-neutral-800 dark:text-neutral-200 flex items-center gap-2">
                    <flux:icon name="viewfinder-circle" class="w-5 h-5 text-indigo-500" />
                    Scan KTP (OCR)
                </h2>
                <p class="text-sm text-neutral-500 mt-1">Scan atau unggah foto KTP untuk mengisi formulir secara otomatis.</p>
            </div>
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

        {{-- Loading Bar saat Processing --}}
        <div x-show="isProcessing" class="mt-4 flex items-center gap-3 text-sm text-indigo-600 dark:text-indigo-400">
            <svg class="animate-spin h-5 w-5 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span>AI sedang membaca KTP Anda, mohon tunggu...</span>
        </div>

        {{-- Loading Livewire saat Upload File --}}
        <div wire:loading wire:target="ktp_image" class="mt-4 flex items-center gap-3 text-sm text-indigo-600 dark:text-indigo-400">
            <svg class="animate-spin h-5 w-5 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span>AI sedang membaca KTP Anda, mohon tunggu...</span>
        </div>

        {{-- Hidden File Input --}}
        <input type="file" x-ref="fileInput" wire:model.live="ktp_image" accept="image/*" class="hidden" />
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
                class="bg-white dark:bg-neutral-900 rounded-2xl shadow-2xl w-full max-w-md overflow-hidden"
            >
                {{-- Modal Header --}}
                <div class="flex items-center justify-between px-6 py-4 border-b border-neutral-100 dark:border-neutral-800">
                    <div class="flex items-center gap-2">
                        <flux:icon name="viewfinder-circle" class="w-5 h-5 text-indigo-500" />
                        <h3 class="font-semibold text-neutral-800 dark:text-neutral-200">Scan KTP Pasien</h3>
                    </div>
                    <button @click="closeModal()" class="p-1.5 rounded-lg hover:bg-neutral-100 dark:hover:bg-neutral-800 text-neutral-400 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                {{-- Modal Body: Pilih Mode --}}
                <div x-show="mode === null" class="p-6 grid grid-cols-2 gap-4">
                    {{-- Opsi 1: Upload File --}}
                    <button
                        @click="closeModal(); $refs.fileInput.click();"
                        class="flex flex-col items-center gap-3 p-6 rounded-xl border-2 border-dashed border-neutral-200 dark:border-neutral-700 hover:border-indigo-400 hover:bg-indigo-50/50 dark:hover:bg-indigo-950/20 transition-all group"
                    >
                        <div class="w-12 h-12 rounded-full bg-indigo-100 dark:bg-indigo-900/40 flex items-center justify-center group-hover:scale-110 transition-transform">
                            <flux:icon name="folder-open" class="w-6 h-6 text-indigo-600 dark:text-indigo-400" />
                        </div>
                        <div class="text-center">
                            <p class="font-semibold text-sm text-neutral-800 dark:text-neutral-200">Dari Folder</p>
                            <p class="text-xs text-neutral-400 mt-0.5">JPG, PNG, WEBP</p>
                        </div>
                    </button>

                    {{-- Opsi 2: Webcam --}}
                    <button
                        @click="startWebcam()"
                        class="flex flex-col items-center gap-3 p-6 rounded-xl border-2 border-dashed border-neutral-200 dark:border-neutral-700 hover:border-violet-400 hover:bg-violet-50/50 dark:hover:bg-violet-950/20 transition-all group"
                    >
                        <div class="w-12 h-12 rounded-full bg-violet-100 dark:bg-violet-900/40 flex items-center justify-center group-hover:scale-110 transition-transform">
                            <flux:icon name="video-camera" class="w-6 h-6 text-violet-600 dark:text-violet-400" />
                        </div>
                        <div class="text-center">
                            <p class="font-semibold text-sm text-neutral-800 dark:text-neutral-200">Kamera/Webcam</p>
                            <p class="text-xs text-neutral-400 mt-0.5">Ambil foto langsung</p>
                        </div>
                    </button>
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
    </div>


        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                {{-- Kiri: Informasi Utama --}}
                <div class="space-y-5">
                    <h2 class="text-sm font-semibold text-neutral-800 dark:text-neutral-200 border-b border-neutral-200 dark:border-neutral-700 pb-2">Informasi Utama</h2>
                    
                    <flux:input wire:model="no_rkm_medis" label="No. Rekam Medis" placeholder="Otomatis" disabled />
                    
                    <flux:input wire:model="nm_pasien" label="Nama Lengkap Pasien" placeholder="Masukkan nama pasien" required />
                    
                    <flux:input wire:model="no_ktp" label="Nomor KTP / NIK" placeholder="Masukkan NIK 16 digit" />
                    
                    <flux:radio.group wire:model="jk" label="Jenis Kelamin">
                        <flux:radio value="L" label="Laki-laki" />
                        <flux:radio value="P" label="Perempuan" />
                    </flux:radio.group>
                    
                    <flux:input wire:model="tmp_lahir" label="Tempat Lahir" placeholder="Kota kelahiran" required />
                    
                    <flux:input type="date" wire:model="tgl_lahir" label="Tanggal Lahir" max="{{ date('Y-m-d') }}" required />
                    
                    <flux:input wire:model="nm_ibu" label="Nama Ibu Kandung" placeholder="Masukkan nama ibu kandung" required />
                </div>

                {{-- Kanan: Kontak & Data Lainnya --}}
                <div class="space-y-5">
                    <h2 class="text-sm font-semibold text-neutral-800 dark:text-neutral-200 border-b border-neutral-200 dark:border-neutral-700 pb-2">Kontak & Keadaan Fisik</h2>
                    
                    <flux:input wire:model="no_tlp" label="Nomor Telepon/HP" placeholder="08..." />
                    
                    <flux:textarea wire:model="alamat" label="Alamat Lengkap" rows="3" placeholder="Masukkan alamat domisili" required />
                    
                    <flux:select wire:model="agama" label="Agama" required>
                        <flux:select.option value="ISLAM">ISLAM</flux:select.option>
                        <flux:select.option value="KRISTEN">KRISTEN</flux:select.option>
                        <flux:select.option value="KATOLIK">KATOLIK</flux:select.option>
                        <flux:select.option value="HINDU">HINDU</flux:select.option>
                        <flux:select.option value="BUDHA">BUDHA</flux:select.option>
                        <flux:select.option value="KONGHUCU">KONGHUCU</flux:select.option>
                        <flux:select.option value="KHYNTCHENG">KHYNTCHENG</flux:select.option>
                    </flux:select>
                    
                    <flux:select wire:model="stts_nikah" label="Status Pernikahan" required>
                        <flux:select.option value="BELUM MENIKAH">BELUM MENIKAH</flux:select.option>
                        <flux:select.option value="MENIKAH">MENIKAH</flux:select.option>
                        <flux:select.option value="JANDA">JANDA</flux:select.option>
                        <flux:select.option value="DUDHA">DUDHA</flux:select.option>
                        <flux:select.option value="JOMBLO">JOMBLO</flux:select.option>
                    </flux:select>
                    
                    <flux:select wire:model="gol_darah" label="Golongan Darah" required>
                        <flux:select.option value="-">-</flux:select.option>
                        <flux:select.option value="A">A</flux:select.option>
                        <flux:select.option value="B">B</flux:select.option>
                        <flux:select.option value="AB">AB</flux:select.option>
                        <flux:select.option value="O">O</flux:select.option>
                    </flux:select>
                </div>
                
            </div>
        </div>
        
        <div class="px-6 py-4 bg-neutral-50 dark:bg-neutral-800/50 border-t border-neutral-200 dark:border-neutral-700 flex justify-end gap-3 rounded-b-xl">
            <flux:button href="{{ route('modul.pasien.index') }}" wire:navigate variant="ghost">Batal</flux:button>
            <flux:button type="submit" variant="primary" icon="check">Simpan Pasien Baru</flux:button>
        </div>
    </form>
</div>
