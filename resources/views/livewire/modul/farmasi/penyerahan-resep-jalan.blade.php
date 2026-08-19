<div class="flex flex-col gap-6 pb-8">

    {{-- ══════════════════════════════════════════════
         Header / Breadcrumb
         ══════════════════════════════════════════════ --}}
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center gap-3">
            <button type="button" onclick="history.back()"
                class="flex items-center justify-center w-10 h-8 rounded-md bg-[#4C5C2D] transition-colors hover:bg-[#3d4b24] shadow-sm"
                title="Kembali">
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
                    <span>Penyerahan Resep Obat Rawat Jalan</span>
                </nav>
                <h1 class="text-xl font-bold text-neutral-800 dark:text-neutral-100 flex items-center gap-2">
                    <flux:icon name="paper-airplane" class="w-5 h-5 text-[#4C5C2D]" />
                    Penyerahan Resep Obat Rawat Jalan
                </h1>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════
         Data Pasien
         ══════════════════════════════════════════════ --}}
    <div class="bg-white dark:bg-neutral-800 rounded-2xl border border-neutral-200 dark:border-neutral-700 shadow-sm overflow-hidden">
        {{-- Section header --}}
        <div class="flex items-center gap-2 px-5 py-3 border-b border-neutral-100 dark:border-neutral-700"
             style="background: linear-gradient(90deg, #4C5C2D10 0%, transparent 100%);">
            <flux:icon name="user" class="w-4 h-4 text-[#4C5C2D]" />
            <h2 class="text-sm font-bold text-[#4C5C2D] dark:text-[#8aad6a] tracking-wide uppercase">Data Pasien</h2>
        </div>
        <div class="p-5 grid grid-cols-2 md:grid-cols-4 gap-4">
            {{-- Nomor Rawat --}}
            <div>
                <p class="text-[10px] font-semibold text-neutral-400 dark:text-neutral-500 uppercase tracking-wider mb-0.5">Nomor Rawat</p>
                <p class="text-sm font-bold text-neutral-800 dark:text-neutral-100 font-mono">{{ $no_rawat ?: '-' }}</p>
            </div>
            {{-- No. R.M. --}}
            <div>
                <p class="text-[10px] font-semibold text-neutral-400 dark:text-neutral-500 uppercase tracking-wider mb-0.5">No. R.M.</p>
                <p class="text-sm font-bold text-neutral-800 dark:text-neutral-100 font-mono">{{ $no_rkm_medis ?: '-' }}</p>
            </div>
            {{-- Nama Pasien --}}
            <div class="col-span-2">
                <p class="text-[10px] font-semibold text-neutral-400 dark:text-neutral-500 uppercase tracking-wider mb-0.5">Nama Pasien</p>
                <p class="text-sm font-bold text-neutral-800 dark:text-neutral-100">{{ $nm_pasien ?: '-' }}</p>
            </div>
            {{-- Jenis Kelamin --}}
            <div>
                <p class="text-[10px] font-semibold text-neutral-400 dark:text-neutral-500 uppercase tracking-wider mb-0.5">Jenis Kelamin</p>
                <p class="text-sm font-medium text-neutral-700 dark:text-neutral-300">
                    @if(strtolower($jk) === 'l' || strtolower($jk) === 'laki-laki')
                        <span class="inline-flex items-center gap-1">
                            <flux:icon name="user" class="w-3.5 h-3.5 text-blue-500" /> Laki-laki
                        </span>
                    @elseif(strtolower($jk) === 'p' || strtolower($jk) === 'perempuan')
                        <span class="inline-flex items-center gap-1">
                            <flux:icon name="user" class="w-3.5 h-3.5 text-pink-500" /> Perempuan
                        </span>
                    @else
                        {{ $jk ?: '-' }}
                    @endif
                </p>
            </div>
            {{-- Umur --}}
            <div>
                <p class="text-[10px] font-semibold text-neutral-400 dark:text-neutral-500 uppercase tracking-wider mb-0.5">Umur Pasien</p>
                <p class="text-sm font-medium text-neutral-700 dark:text-neutral-300">{{ $umur ?: '-' }}</p>
            </div>
            {{-- Tgl Lahir --}}
            <div>
                <p class="text-[10px] font-semibold text-neutral-400 dark:text-neutral-500 uppercase tracking-wider mb-0.5">Tanggal Lahir</p>
                <p class="text-sm font-medium text-neutral-700 dark:text-neutral-300">
                    {{ $tgl_lahir ? \Carbon\Carbon::parse($tgl_lahir)->format('d/m/Y') : '-' }}
                </p>
            </div>
            {{-- No. HP --}}
            <div>
                <p class="text-[10px] font-semibold text-neutral-400 dark:text-neutral-500 uppercase tracking-wider mb-0.5">No. HP / Telp</p>
                <p class="text-sm font-medium text-neutral-700 dark:text-neutral-300">{{ $no_hp ?: '-' }}</p>
            </div>
            {{-- Alamat --}}
            <div class="col-span-2 md:col-span-4">
                <p class="text-[10px] font-semibold text-neutral-400 dark:text-neutral-500 uppercase tracking-wider mb-0.5">Alamat</p>
                <p class="text-sm font-medium text-neutral-700 dark:text-neutral-300">{{ $alamat ?: '-' }}</p>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════
         Daftar Obat yang Diserahkan (setelah validasi)
         ══════════════════════════════════════════════ --}}
    <div class="bg-white dark:bg-neutral-800 rounded-2xl border border-neutral-200 dark:border-neutral-700 shadow-sm overflow-hidden">
        <div class="flex items-center gap-2 px-5 py-3 border-b border-neutral-100 dark:border-neutral-700"
             style="background: linear-gradient(90deg, #4C5C2D10 0%, transparent 100%);">
            <flux:icon name="beaker" class="w-4 h-4 text-[#4C5C2D]" />
            <h2 class="text-sm font-bold text-[#4C5C2D] dark:text-[#8aad6a] tracking-wide uppercase">Obat yang Diserahkan</h2>
            <span class="ml-auto text-[10px] font-semibold bg-[#4C5C2D]/10 text-[#4C5C2D] px-2 py-0.5 rounded-full">
                {{ count($listObat) }} item
            </span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead>
                    <tr class="bg-neutral-50 dark:bg-neutral-900/50">
                        <th class="px-4 py-2.5 text-left font-semibold text-neutral-500 dark:text-neutral-400 w-10">No</th>
                        <th class="px-4 py-2.5 text-left font-semibold text-neutral-500 dark:text-neutral-400">Nama Obat</th>
                        <th class="px-4 py-2.5 text-center font-semibold text-neutral-500 dark:text-neutral-400 w-20">Jumlah</th>
                        <th class="px-4 py-2.5 text-left font-semibold text-neutral-500 dark:text-neutral-400">Aturan Pakai</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100 dark:divide-neutral-700">
                    @forelse($listObat as $i => $obat)
                        <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-700/40 transition-colors">
                            <td class="px-4 py-2.5 text-center text-neutral-500 dark:text-neutral-400 font-mono">{{ $i + 1 }}</td>
                            <td class="px-4 py-2.5 font-medium text-neutral-800 dark:text-neutral-200">{{ $obat['nama_brng'] }}</td>
                            <td class="px-4 py-2.5 text-center">
                                <span class="inline-flex items-center justify-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-[#4C5C2D]/10 text-[#4C5C2D] dark:bg-[#4C5C2D]/20 dark:text-[#8aad6a]">
                                    {{ $obat['jml'] }}
                                </span>
                            </td>
                            <td class="px-4 py-2.5 text-neutral-600 dark:text-neutral-400">{{ $obat['aturan_pakai'] }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-neutral-400 dark:text-neutral-500">
                                <div class="flex flex-col items-center gap-2">
                                    <flux:icon name="beaker" class="w-8 h-8 opacity-30" />
                                    <span>Belum ada obat yang divalidasi untuk resep ini.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════
         Kamera & Ambil Evidence
         ══════════════════════════════════════════════ --}}
    <div
        x-data="{
            stream: null,
            hasPermission: false,
            errorMsg: '',
            capturedSrc: '',

            async startCamera() {
                try {
                    this.stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' }, audio: false });
                    this.$refs.video.srcObject = this.stream;
                    this.hasPermission = true;
                    this.errorMsg = '';
                } catch (err) {
                    this.errorMsg = 'Tidak dapat mengakses kamera: ' + err.message;
                }
            },

            capturePhoto() {
                const video  = this.$refs.video;
                const canvas = this.$refs.canvas;
                canvas.width  = video.videoWidth  || 640;
                canvas.height = video.videoHeight || 480;
                canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);
                const dataUrl = canvas.toDataURL('image/jpeg', 0.75); // kompresi ~75%
                this.capturedSrc = dataUrl;
                // Kirim ke Livewire
                $wire.setCapturedImage(dataUrl);
            },
        }"
        x-init="startCamera()"
        class="bg-white dark:bg-neutral-800 rounded-2xl border border-neutral-200 dark:border-neutral-700 shadow-sm overflow-hidden"
    >
        <div class="flex items-center gap-2 px-5 py-3 border-b border-neutral-100 dark:border-neutral-700"
             style="background: linear-gradient(90deg, #4C5C2D10 0%, transparent 100%);">
            <flux:icon name="camera" class="w-4 h-4 text-[#4C5C2D]" />
            <h2 class="text-sm font-bold text-[#4C5C2D] dark:text-[#8aad6a] tracking-wide uppercase">Ambil Evidence Foto</h2>
        </div>

        <div class="p-5">
            {{-- Error kamera --}}
            <template x-if="errorMsg">
                <div class="mb-4 flex items-center gap-2 px-4 py-3 rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-600 dark:text-red-400 text-xs">
                    <flux:icon name="exclamation-triangle" class="w-4 h-4 flex-shrink-0" />
                    <span x-text="errorMsg"></span>
                </div>
            </template>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                {{-- Kolom Kiri: Live Preview Kamera --}}
                <div class="flex flex-col gap-3">
                    <p class="text-[10px] font-semibold text-neutral-400 dark:text-neutral-500 uppercase tracking-wider">Preview Kamera</p>
                    <div class="relative rounded-xl overflow-hidden bg-neutral-900 aspect-video flex items-center justify-center">
                        <video
                            x-ref="video"
                            autoplay
                            playsinline
                            muted
                            class="w-full h-full object-cover"
                        ></video>
                        {{-- Overlay jika belum ada permission --}}
                        <template x-if="!hasPermission && !errorMsg">
                            <div class="absolute inset-0 flex flex-col items-center justify-center gap-2 text-neutral-400">
                                <flux:icon name="camera" class="w-10 h-10 opacity-40 animate-pulse" />
                                <span class="text-xs">Memuat kamera...</span>
                            </div>
                        </template>
                        {{-- Viewfinder overlay --}}
                        <template x-if="hasPermission">
                            <div class="absolute inset-0 pointer-events-none">
                                <div class="absolute top-2 left-2 w-8 h-8 border-t-2 border-l-2 border-white/60 rounded-tl-lg"></div>
                                <div class="absolute top-2 right-2 w-8 h-8 border-t-2 border-r-2 border-white/60 rounded-tr-lg"></div>
                                <div class="absolute bottom-2 left-2 w-8 h-8 border-b-2 border-l-2 border-white/60 rounded-bl-lg"></div>
                                <div class="absolute bottom-2 right-2 w-8 h-8 border-b-2 border-r-2 border-white/60 rounded-br-lg"></div>
                            </div>
                        </template>
                    </div>
                    {{-- Canvas tersembunyi untuk capture --}}
                    <canvas x-ref="canvas" class="hidden"></canvas>
                </div>

                {{-- Kolom Kanan: Hasil Capture --}}
                <div class="flex flex-col gap-3">
                    <p class="text-[10px] font-semibold text-neutral-400 dark:text-neutral-500 uppercase tracking-wider">Hasil Evidence</p>
                    <div class="relative rounded-xl overflow-hidden bg-neutral-100 dark:bg-neutral-900 aspect-video flex items-center justify-center border-2 border-dashed border-neutral-200 dark:border-neutral-700"
                         :class="capturedSrc ? 'border-[#4C5C2D]' : ''">
                        {{-- Placeholder --}}
                        <template x-if="!capturedSrc">
                            <div class="flex flex-col items-center justify-center gap-2 text-neutral-400 dark:text-neutral-600">
                                <flux:icon name="photo" class="w-10 h-10 opacity-40" />
                                <span class="text-xs">Foto belum diambil</span>
                            </div>
                        </template>
                        {{-- Hasil Foto --}}
                        <template x-if="capturedSrc">
                            <img :src="capturedSrc" class="w-full h-full object-cover" alt="Evidence penyerahan obat" />
                        </template>
                        {{-- Badge "Terambil" --}}
                        <template x-if="capturedSrc">
                            <div class="absolute top-2 right-2">
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-[#4C5C2D] text-white shadow">
                                    <flux:icon name="check" class="w-3 h-3" /> Terambil
                                </span>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            {{-- Tombol Aksi --}}
            <div class="flex items-center justify-between gap-3 mt-5 pt-4 border-t border-neutral-100 dark:border-neutral-700 flex-wrap">
                {{-- Ambil Evidence --}}
                <button
                    type="button"
                    @click="capturePhoto()"
                    :disabled="!hasPermission"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg text-sm font-bold text-white transition-all shadow-md hover:shadow-lg active:scale-95 disabled:opacity-40 disabled:cursor-not-allowed"
                    style="background: linear-gradient(135deg, #4C5C2D 0%, #3D4A24 100%);"
                >
                    <flux:icon name="camera" class="w-4 h-4" />
                    Ambil Evidence
                </button>

                {{-- Simpan --}}
                <button
                    type="button"
                    wire:click="simpan"
                    wire:loading.attr="disabled"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg text-sm font-bold transition-all shadow-md hover:shadow-lg active:scale-95 disabled:opacity-40 disabled:cursor-not-allowed"
                    :class="capturedSrc
                        ? 'bg-emerald-600 hover:bg-emerald-700 text-white'
                        : 'bg-neutral-200 dark:bg-neutral-700 text-neutral-400 dark:text-neutral-500 cursor-not-allowed'"
                    :disabled="!capturedSrc"
                >
                    <span wire:loading wire:target="simpan" class="inline-flex items-center gap-2">
                        <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        Menyimpan...
                    </span>
                    <span wire:loading.remove wire:target="simpan" class="inline-flex items-center gap-2">
                        <flux:icon name="cloud-arrow-up" class="w-4 h-4" />
                        Simpan
                    </span>
                </button>
            </div>
        </div>
    </div>

</div>

