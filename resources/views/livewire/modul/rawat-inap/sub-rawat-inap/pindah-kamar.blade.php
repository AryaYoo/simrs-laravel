<div class="flex flex-col gap-6 pb-8">
    {{-- Header & Navigation --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('modul.rawat-inap.show', str_replace('/', '-', $no_rawat)) }}" wire:navigate
               class="flex items-center justify-center w-8 h-8 rounded-lg transition-colors hover:bg-neutral-100 dark:hover:bg-neutral-700">
                <flux:icon name="chevron-left" class="w-5 h-5 text-neutral-500" />
            </a>
            <div>
                <nav class="text-xs text-neutral-400 mb-0.5 uppercase tracking-wider font-semibold">
                    <a href="{{ route('modul.index') }}" wire:navigate class="hover:text-brand-teal">Modul</a>
                    <span class="mx-1">/</span>
                    <a href="{{ route('modul.rawat-inap.index') }}" wire:navigate class="hover:text-brand-teal">Rawat Inap</a>
                    <span class="mx-1">/</span>
                    <a href="{{ route('modul.rawat-inap.show', str_replace('/', '-', $no_rawat)) }}" wire:navigate class="hover:text-brand-teal">Detail</a>
                    <span class="mx-1">/</span>
                    <span class="text-neutral-500">Pindah Kamar</span>
                </nav>
                <h1 class="text-xl font-bold text-neutral-800 dark:text-neutral-100 italic tracking-tight">Pindah Kamar Inap Pasien</h1>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <flux:button wire:click="save" variant="primary" icon="check-circle" 
                style="background-color: #4C5C2D; color: white; border: none; font-weight: 700; padding-left: 1.5rem; padding-right: 1.5rem;">
                Simpan Perpindahan
            </flux:button>
        </div>
    </div>

    {{-- Patient Information Card --}}
    <div class="bg-white dark:bg-neutral-800 rounded-xl border border-neutral-200 dark:border-neutral-700 p-4 shadow-sm overflow-hidden relative">
        <div class="absolute top-0 right-0 w-32 h-32 bg-brand-teal/5 rounded-full -mr-16 -mt-16"></div>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 relative">
            <div>
                <flux:label class="text-[0.65rem] opacity-60 uppercase font-black mb-1">No. Rawat</flux:label>
                <p class="font-mono font-bold text-neutral-700 dark:text-neutral-200">{{ $regPeriksa->no_rawat }}</p>
            </div>
            <div>
                <flux:label class="text-[0.65rem] opacity-60 uppercase font-black mb-1">No. RM</flux:label>
                <p class="font-bold text-neutral-700 dark:text-neutral-200">{{ $regPeriksa->no_rkm_medis }}</p>
            </div>
            <div class="md:col-span-2">
                <flux:label class="text-[0.65rem] opacity-60 uppercase font-black mb-1">Nama Pasien</flux:label>
                <p class="font-bold text-neutral-800 dark:text-neutral-100 uppercase italic">
                    {{ $regPeriksa->pasien->nm_pasien }} 
                    <span class="ml-2 text-xs font-normal text-neutral-500 normal-case italic">
                        ({{ $regPeriksa->umurdaftar }} {{ $regPeriksa->sttsumur }})
                    </span>
                </p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Left Column: Current & New Room --}}
        <div class="lg:col-span-2 flex flex-col gap-6">
            <div class="bg-white dark:bg-neutral-800 rounded-xl border border-neutral-200 dark:border-neutral-700 p-6 shadow-sm">
                <div class="flex items-center gap-2 mb-6 border-b border-neutral-100 dark:border-neutral-700 pb-4">
                    <div class="w-8 h-8 rounded-lg bg-yellow-50 flex items-center justify-center">
                        <flux:icon name="identification" class="w-4 h-4 text-yellow-600" />
                    </div>
                    <h3 class="font-bold text-neutral-800 dark:text-neutral-100 italic">Informasi Kamar & Waktu</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Selected Room Selection --}}
                    <div class="md:col-span-2 space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <flux:field>
                                <flux:label>Pilih Kamar Inap Baru</flux:label>
                                <div class="flex gap-2">
                                    <flux:input wire:model="kd_kamar" placeholder="Klik ikon pencarian..." readonly class="flex-1 bg-neutral-50" />
                                    <flux:button wire:click="openKamarModal" icon="magnifying-glass" square variant="ghost" class="bg-blue-50 text-blue-600 border-blue-100 hover:bg-blue-100" />
                                </div>
                            </flux:field>
                            <flux:field>
                                <flux:label>Nama Bangsal / Ruangan</flux:label>
                                <flux:input wire:model="nm_bangsal" readonly class="bg-neutral-50" />
                            </flux:field>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <flux:field>
                                <flux:label>Status Kamar</flux:label>
                                <flux:badge size="sm" color="{{ $status_kamar === 'KOSONG' ? 'green' : ($status_kamar === 'ISI' ? 'red' : 'neutral') }}" class="mt-2">
                                    {{ $status_kamar ?: 'BELUM DIPILIH' }}
                                </flux:badge>
                            </flux:field>
                            <flux:field>
                                <flux:label>Kelas</flux:label>
                                <flux:input wire:model="kelas_kamar" readonly class="bg-neutral-50 text-xs font-bold" />
                            </flux:field>
                            <flux:field>
                                <flux:label>Tarif / Hari</flux:label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-bold text-neutral-400">Rp.</span>
                                    <flux:input wire:model="trf_kamar" readonly class="pl-10 bg-neutral-50 font-mono font-bold" />
                                </div>
                            </flux:field>
                        </div>
                    </div>

                    {{-- Dates --}}
                    <flux:field>
                        <flux:label>Tanggal Pindah</flux:label>
                        <flux:input wire:model.live="tgl_pindah" type="date" />
                    </flux:field>
                    <flux:field>
                        <flux:label>Jam Pindah</flux:label>
                        <flux:input wire:model.live="jam_pindah" type="time" step="1" />
                    </flux:field>
                </div>
            </div>

            <div class="bg-white dark:bg-neutral-800 rounded-xl border border-neutral-200 dark:border-neutral-700 p-6 shadow-sm">
                <div class="flex items-center gap-2 mb-6 border-b border-neutral-100 dark:border-neutral-700 pb-4">
                    <div class="w-8 h-8 rounded-lg bg-green-50 flex items-center justify-center">
                        <flux:icon name="circle-stack" class="w-4 h-4 text-green-600" />
                    </div>
                    <h3 class="font-bold text-neutral-800 dark:text-neutral-100 italic">Kalkulasi Biaya Inap Lama</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 items-center">
                    <div class="text-center md:text-left bg-neutral-50 dark:bg-neutral-900/50 p-4 rounded-xl border border-neutral-100 dark:border-neutral-700/50">
                        <flux:label class="text-[0.65rem] opacity-60 uppercase font-black mb-1">Lama Inap</flux:label>
                        <p class="text-3xl font-black text-brand-teal">{{ $lama }} <span class="text-sm font-normal text-neutral-500 italic">Hari</span></p>
                    </div>
                    
                    <div class="flex items-center justify-center">
                        <flux:icon name="x-mark" class="w-6 h-6 text-neutral-300" />
                    </div>

                    <div class="text-center md:text-right">
                        <flux:label class="text-[0.65rem] opacity-60 uppercase font-black mb-1">Total Biaya Kamar Sebelum</flux:label>
                        <p class="text-2xl font-black text-neutral-800 dark:text-neutral-100">
                            <span class="text-sm font-normal text-neutral-500 mr-1 italic">Rp.</span>
                            {{ number_format($total, 0, ',', '.') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Column: Options --}}
        <div class="flex flex-col gap-6">
            <div class="bg-white dark:bg-neutral-800 rounded-xl border border-neutral-200 dark:border-neutral-700 p-6 shadow-sm flex-1">
                <div class="flex items-center gap-2 mb-6 border-b border-neutral-100 dark:border-neutral-700 pb-4">
                    <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center">
                        <flux:icon name="cog-8-tooth" class="w-4 h-4 text-indigo-600" />
                    </div>
                    <h3 class="font-bold text-neutral-800 dark:text-neutral-100 italic">Pilihan Logika Perpindahan</h3>
                </div>

                <flux:radio.group wire:model.live="pilihan" class="flex flex-col gap-4">
                    <div class="p-4 rounded-xl border border-neutral-100 dark:border-neutral-700 hover:bg-neutral-50 transition-colors cursor-pointer {{ $pilihan == 1 ? 'border-brand-teal/30 bg-brand-teal/5' : '' }}">
                        <flux:radio value="1" label="Hapus Kamar Lama" description="Kamar Inap sebelumnya dihapus dari billing. Pasien dihitung menginap mulai saat ini." />
                    </div>
                    <div class="p-4 rounded-xl border border-neutral-100 dark:border-neutral-700 hover:bg-neutral-50 transition-colors cursor-pointer {{ $pilihan == 2 ? 'border-brand-teal/30 bg-brand-teal/5' : '' }}">
                        <flux:radio value="2" label="Ganti Kamar (Merge)" description="Kamar Inap sebelumnya diganti kamarnya dengan yang baru dan harga menyesuaikan." />
                    </div>
                    <div class="p-4 rounded-xl border border-neutral-100 dark:border-neutral-700 hover:bg-neutral-50 transition-colors cursor-pointer {{ $pilihan == 3 ? 'border-brand-teal/30 bg-brand-teal/5' : '' }}">
                        <flux:radio value="3" label="Status Pindah (Standar)" description="Kamar lama ditutup & dihitung biayanya. Pasien masuk ke kamar baru sebagai record baru." />
                    </div>
                    <div class="p-4 rounded-xl border border-neutral-100 dark:border-neutral-700 hover:bg-neutral-50 transition-colors cursor-pointer {{ $pilihan == 4 ? 'border-brand-teal/30 bg-brand-teal/5' : '' }}">
                        <flux:radio value="4" label="Status Pindah (Harga Tertinggi)" description="Sama seperti No. 3, namun kamar lama menyesuaikan ke tarif yang lebih tinggi." />
                    </div>
                </flux:radio.group>
            </div>
        </div>
    </div>

    {{-- Modal Kamar Lookup --}}
    <flux:modal name="kamar-lookup" wire:model="isKamarModalOpen" variant="filled" class="md:min-w-[700px]">
        <div class="flex flex-col gap-4">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-bold italic">Cari Kamar Inap / Bed</h2>
                <flux:modal.close>
                    <flux:button variant="ghost" icon="x-mark" square size="sm" />
                </flux:modal.close>
            </div>

            <flux:input wire:model.live.debounce.300ms="searchKamar" icon="magnifying-glass" placeholder="Cari berdasarkan No. Bed atau Nama Bangsal..." autofocus />

            <div class="overflow-x-auto rounded-xl border border-neutral-200 dark:border-neutral-700 max-h-[400px]">
                <table class="w-full text-sm text-left">
                    <thead class="text-xs text-neutral-500 uppercase bg-neutral-50 dark:bg-neutral-900 border-b border-neutral-200 dark:border-neutral-700">
                        <tr>
                            <th class="px-4 py-3 font-black">Nomer Bed</th>
                            <th class="px-4 py-3 font-black">Kode Bangsal</th>
                            <th class="px-4 py-3 font-black">Nama Kamar / Bangsal</th>
                            <th class="px-4 py-3 font-black">Kelas</th>
                            <th class="px-4 py-3 font-black">Tarif</th>
                            <th class="px-4 py-3 font-black">Status</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-100 dark:divide-neutral-800">
                        @forelse($listKamar as $kamar)
                            <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-900/50 transition-colors cursor-pointer" 
                                wire:click="selectKamar('{{ $kamar->kd_kamar }}', '{{ $kamar->bangsal->nm_bangsal ?? '-' }}', {{ $kamar->trf_kamar }}, '{{ $kamar->status }}', '{{ $kamar->kelas }}')">
                                <td class="px-4 py-3 font-bold text-neutral-800 dark:text-neutral-100">{{ $kamar->kd_kamar }}</td>
                                <td class="px-4 py-3 font-mono text-xs opacity-60">{{ $kamar->kd_bangsal }}</td>
                                <td class="px-4 py-3 italic font-semibold">{{ $kamar->bangsal->nm_bangsal ?? '-' }}</td>
                                <td class="px-4 py-3 font-bold text-xs">{{ $kamar->kelas }}</td>
                                <td class="px-4 py-3 font-mono text-neutral-600 dark:text-neutral-400">{{ number_format($kamar->trf_kamar, 0, ',', '.') }}</td>
                                <td class="px-4 py-3">
                                    <flux:badge size="xs" color="{{ $kamar->status === 'KOSONG' ? 'green' : ($kamar->status === 'ISI' ? 'red' : 'neutral') }}">
                                        {{ $kamar->status }}
                                    </flux:badge>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <flux:button size="xs" variant="ghost" icon="chevron-right" square />
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-neutral-400 italic">
                                    {{ strlen($searchKamar) < 2 ? 'Ketik minimal 2 karakter untuk mencari...' : 'Kamar tidak ditemukan.' }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </flux:modal>
</div>
