<div class="flex flex-col gap-6 pb-24"
    x-data="{ isDirty: false, isSubmitting: false }"
    @input="isDirty = true"
    @change="isDirty = true"
    x-init="
        window.addEventListener('beforeunload', (e) => {
            if (isDirty && !isSubmitting) { e.preventDefault(); e.returnValue = ''; }
        });
        document.addEventListener('livewire:navigating', (e) => {
            if (isDirty && !isSubmitting) {
                if (!confirm('Data belum tersimpan. Yakin ingin meninggalkan halaman?')) { e.preventDefault(); }
            }
        });
        Livewire.hook('commit', ({ succeed, fail }) => {
            succeed(() => { setTimeout(() => { isSubmitting = false; }, 500); });
            fail(() => { isSubmitting = false; });
        });
    "
>
    {{-- Sticky Header --}}
    <div class="sticky top-0 z-40 bg-white/80 dark:bg-neutral-900/80 backdrop-blur-md border-b border-neutral-200 dark:border-neutral-700 -mx-4 px-4 py-3 mb-2 flex items-center justify-between shadow-sm">
        <div class="flex items-center gap-3">
            <a href="{{ route('modul.rawat-inap.kelahiran-bayi') }}" wire:navigate
               class="p-2 rounded-lg hover:bg-neutral-100 dark:hover:bg-neutral-800 transition-colors">
                <flux:icon name="chevron-left" class="w-5 h-5 text-neutral-500" />
            </a>
            <div>
                <h1 class="text-base font-bold text-neutral-800 dark:text-neutral-100">Edit Data Kelahiran Bayi</h1>
                <p class="text-[10px] text-neutral-500 font-medium uppercase tracking-wider">No. RM: {{ $no_rkm_medis }} &mdash; {{ $nm_pasien }}</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <flux:button href="{{ route('modul.rawat-inap.kelahiran-bayi') }}" wire:navigate variant="ghost" class="h-9 text-sm">
                Batal
            </flux:button>
            <flux:button wire:click="save" @click="isSubmitting = true" variant="primary" icon="check" class="!bg-[#4C5C2D] !border-[#4C5C2D] hover:!bg-[#3D4A24] h-9 px-6 text-sm">
                Update Data
            </flux:button>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-8 max-w-5xl mx-auto w-full px-4 lg:px-0">

        {{-- Section 1: Identitas --}}
        <div id="section-1" class="bg-white dark:bg-neutral-800 rounded-2xl border border-neutral-200 dark:border-neutral-700 shadow-sm overflow-hidden scroll-mt-24">
            <div class="px-6 py-4 bg-neutral-50 dark:bg-neutral-900/50 border-b border-neutral-200 dark:border-neutral-700 flex items-center gap-3">
                <div class="w-6 h-6 rounded-full bg-[#4C5C2D]/10 flex items-center justify-center">
                    <span class="text-[#4C5C2D] font-bold text-xs">1</span>
                </div>
                <h2 class="text-sm font-bold text-neutral-800 dark:text-neutral-100 capitalize">Identitas & Pendaftaran</h2>
            </div>
            <div class="p-6 space-y-6">
                {{-- RM & Nama (Readonly pada edit) --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="p-4 rounded-xl border-2 border-dashed border-[#4C5C2D]/30 bg-[#4C5C2D]/5">
                        <p class="text-[10px] font-bold text-[#4C5C2D] uppercase tracking-widest mb-2">Data Terkunci</p>
                        <div class="grid grid-cols-2 gap-4">
                            <flux:input label="No. RM Bayi" wire:model="no_rkm_medis" readonly class="bg-neutral-100 dark:bg-neutral-700 font-mono" />
                            <flux:input label="Nama Anak/Bayi" wire:model="nm_pasien" readonly class="bg-neutral-100 dark:bg-neutral-700" />
                        </div>
                    </div>
                    <div class="grid grid-cols-3 gap-4">
                        <flux:select label="J.K. Bayi" wire:model="jk">
                            <flux:select.option value="L">LAKI-LAKI</flux:select.option>
                            <flux:select.option value="P">PEREMPUAN</flux:select.option>
                        </flux:select>
                        <flux:input label="No. SKL" wire:model="no_skl" placeholder="0001/RM-SKL/05/2026" class="col-span-2" />
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4 bg-neutral-50/50 dark:bg-neutral-900/10 p-4 rounded-xl border border-neutral-100 dark:border-neutral-800">
                        <div class="flex items-center justify-between">
                            <h3 class="text-xs font-bold text-[#4C5C2D] uppercase tracking-wider mb-2">Informasi Ibu</h3>
                            <div x-data="{ openSearch: false }" class="relative">
                                <flux:button size="xs" variant="ghost" icon="link" class="h-6 text-[10px] px-2 text-[#4C5C2D] bg-[#4C5C2D]/10 hover:bg-[#4C5C2D]/20" @click="openSearch = !openSearch">
                                    Attach Pasien
                                </flux:button>
                                
                                <div x-show="openSearch" @click.away="openSearch = false" x-cloak
                                     class="absolute right-0 top-full mt-2 w-72 bg-white dark:bg-neutral-800 rounded-xl shadow-xl border border-neutral-200 dark:border-neutral-700 z-50 p-2">
                                    <flux:input wire:model.live.debounce.300ms="searchIbu" placeholder="Cari No. RM / Nama Ibu..." icon="magnifying-glass" class="mb-2" />
                                    
                                    @if(!empty($ibuList))
                                        <div class="max-h-48 overflow-y-auto rounded-lg border border-neutral-100 dark:border-neutral-700">
                                            @foreach($ibuList as $ibu)
                                                <button type="button" @click="openSearch = false; $wire.selectIbu('{{ $ibu['no_rkm_medis'] }}')"
                                                    class="w-full text-left px-3 py-2 hover:bg-neutral-100 dark:hover:bg-neutral-700 transition-colors border-b last:border-0 border-neutral-100">
                                                    <p class="text-xs font-bold text-neutral-800 dark:text-neutral-200">{{ $ibu['nm_pasien'] }}</p>
                                                    <p class="text-[10px] text-neutral-500">RM: {{ $ibu['no_rkm_medis'] }} | Usia: {{ $ibu['umur'] }}</p>
                                                </button>
                                            @endforeach
                                        </div>
                                    @elseif(strlen($searchIbu) >= 3)
                                        <div class="p-3 text-center text-xs text-neutral-500">Data pasien tidak ditemukan.</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="grid grid-cols-3 gap-4">
                            <div class="col-span-2">
                                <flux:input label="Ibu Bayi" wire:model="nm_ibu" placeholder="Nama Ibu Kandung" />
                            </div>
                            <div>
                                <flux:input label="Umur Ibu" wire:model="umur_ibu" placeholder="33" />
                                @error('umur_ibu') <span class="text-[10px] text-red-500 font-medium">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <flux:input label="Alamat Ibu" wire:model="alamat" placeholder="Alamat lengkap" />
                    </div>
                    <div class="space-y-4 bg-neutral-50/50 dark:bg-neutral-900/10 p-4 rounded-xl border border-neutral-100 dark:border-neutral-800">
                        <h3 class="text-xs font-bold text-[#4C5C2D] uppercase tracking-wider">Informasi Ayah</h3>
                        <div class="grid grid-cols-3 gap-4">
                            <div class="col-span-2">
                                <flux:input label="Nama Ayah" wire:model="nama_ayah" placeholder="Nama Lengkap Ayah" />
                                @error('nama_ayah') <span class="text-[10px] text-red-500 font-medium">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <flux:input label="Umur Ayah" wire:model="umur_ayah" placeholder="34" />
                                @error('umur_ayah') <span class="text-[10px] text-red-500 font-medium">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Section 2: Antropometri & Waktu --}}
        <div id="section-2" class="bg-white dark:bg-neutral-800 rounded-2xl border border-neutral-200 dark:border-neutral-700 shadow-sm overflow-hidden scroll-mt-24">
            <div class="px-6 py-4 bg-neutral-50 dark:bg-neutral-900/50 border-b border-neutral-200 dark:border-neutral-700 flex items-center gap-3">
                <div class="w-6 h-6 rounded-full bg-[#4C5C2D]/10 flex items-center justify-center">
                    <span class="text-[#4C5C2D] font-bold text-xs">2</span>
                </div>
                <h2 class="text-sm font-bold text-neutral-800 dark:text-neutral-100 capitalize">Antropometri & Waktu</h2>
            </div>
            <div class="p-6 space-y-6">
                <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                    <div>
                        <flux:input label="P.B. (cm)" wire:model="panjang_badan" placeholder="Panjang Badan" />
                        @error('panjang_badan') <span class="text-[10px] text-red-500 font-medium">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <flux:input label="B.B. (gram)" wire:model="berat_badan" placeholder="Berat Badan" />
                        @error('berat_badan') <span class="text-[10px] text-red-500 font-medium">{{ $message }}</span> @enderror
                    </div>
                    <flux:input label="Lingkar Dada (cm)" wire:model="lingkar_dada" />
                    <div>
                        <flux:input label="Lingkar Kepala (cm)" wire:model="lingkar_kepala" />
                        @error('lingkar_kepala') <span class="text-[10px] text-red-500 font-medium">{{ $message }}</span> @enderror
                    </div>
                    <flux:input label="Lingkar Perut (cm)" wire:model="lingkar_perut" />
                </div>
                <hr class="border-neutral-100 dark:border-neutral-700" />
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <flux:input type="date" label="Tgl. Lahir" wire:model="tgl_lahir" />
                    <flux:input type="time" label="Jam Lahir" wire:model="jam_lahir" step="1" />
                    <flux:input label="Umur Bayi saat ini" wire:model="umur" placeholder="Misal: 0 Bln 1 Hri" />
                </div>
            </div>
        </div>

        {{-- Section 3: Persalinan & Riwayat --}}
        <div id="section-3" class="bg-white dark:bg-neutral-800 rounded-2xl border border-neutral-200 dark:border-neutral-700 shadow-sm overflow-hidden scroll-mt-24">
            <div class="px-6 py-4 bg-neutral-50 dark:bg-neutral-900/50 border-b border-neutral-200 dark:border-neutral-700 flex items-center gap-3">
                <div class="w-6 h-6 rounded-full bg-[#4C5C2D]/10 flex items-center justify-center">
                    <span class="text-[#4C5C2D] font-bold text-xs">3</span>
                </div>
                <h2 class="text-sm font-bold text-neutral-800 dark:text-neutral-100 capitalize">Persalinan & Riwayat</h2>
            </div>
            <div class="p-6 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="md:col-span-2">
                        <flux:input label="Penyulit Kehamilan" wire:model="penyulit_kehamilan" placeholder="Penyulit/komplikasi kehamilan..." />
                    </div>
                    <div class="grid grid-cols-4 gap-2">
                        <flux:input label="Anak Ke" wire:model="anakke" placeholder="Ke" />
                        <flux:input label="G" wire:model="g" placeholder="G" />
                        <flux:input label="P" wire:model="p" placeholder="P" />
                        <flux:input label="A" wire:model="a" placeholder="A" />
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <flux:input label="Proses Kelahiran" wire:model="proses_lahir" placeholder="Normal, Sectio Caesarea, Vakum, dll..." />
                        @error('proses_lahir') <span class="text-xs text-red-500 font-medium">{{ $message }}</span> @enderror
                    </div>
                    <div class="space-y-2 relative">
                        <flux:input label="Cari Penolong Persalinan (NIK / Nama)" wire:model.live.debounce.300ms="searchPenolong" placeholder="Ketik minimal 3 karakter..." icon="magnifying-glass" />
                        @if(!empty($penolongList))
                            <div class="absolute z-50 w-full mt-1 bg-white dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-lg shadow-xl overflow-hidden max-h-60 overflow-y-auto">
                                @foreach($penolongList as $peg)
                                    <button type="button" wire:click="selectPenolong('{{ $peg['nik'] }}', '{{ $peg['nama'] }}')"
                                        class="w-full text-left px-4 py-2.5 hover:bg-neutral-100 dark:hover:bg-neutral-700 transition-colors border-b last:border-0 border-neutral-100">
                                        <p class="text-xs font-bold text-neutral-800">{{ $peg['nama'] }}</p>
                                        <p class="text-[10px] text-neutral-500">NIK: {{ $peg['nik'] }} | {{ $peg['jbtn'] }}</p>
                                    </button>
                                @endforeach
                            </div>
                        @endif
                        @error('penolong') <span class="text-xs text-red-500 font-medium">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <flux:input label="NIK Penolong" wire:model="penolong" readonly class="bg-neutral-50 font-mono" />
                    <flux:input label="Nama Penolong" wire:model="nm_penolong" readonly class="bg-neutral-50 col-span-2" />
                </div>
                <hr class="border-neutral-100 dark:border-neutral-700" />
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <flux:input label="Diagnosa" wire:model="diagnosa" placeholder="Diagnosa Kelahiran" />
                    <flux:input label="Ketuban" wire:model="ketuban" placeholder="Jernih, Keruh, Meconium-stained, dll" />
                </div>
                <flux:textarea label="Keterangan Tambahan" wire:model="keterangan" rows="3" placeholder="Keterangan tambahan proses persalinan..." />
            </div>
        </div>

        {{-- Section 4: Skor APGAR --}}
        <div id="section-4" class="bg-white dark:bg-neutral-800 rounded-2xl border border-neutral-200 dark:border-neutral-700 shadow-sm overflow-hidden scroll-mt-24">
            <div class="px-6 py-4 bg-neutral-50 dark:bg-neutral-900/50 border-b border-neutral-200 dark:border-neutral-700 flex items-center gap-3">
                <div class="w-6 h-6 rounded-full bg-[#4C5C2D]/10 flex items-center justify-center">
                    <span class="text-[#4C5C2D] font-bold text-xs">4</span>
                </div>
                <h2 class="text-sm font-bold text-neutral-800 dark:text-neutral-100 capitalize">Skor APGAR & Kondisi Klinis</h2>
            </div>
            <div class="p-6 space-y-6">
                <div>
                    <h3 class="text-xs font-bold text-neutral-500 uppercase tracking-widest mb-3">Tabel Penilaian Skor APGAR</h3>
                    <div class="overflow-x-auto rounded-xl border border-neutral-200 dark:border-neutral-700">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead>
                                <tr class="bg-neutral-50 dark:bg-neutral-950/20 text-neutral-500 font-bold border-b border-neutral-200 dark:border-neutral-700">
                                    <th class="px-4 py-3 border-r border-neutral-200 dark:border-neutral-700">Tanda</th>
                                    <th class="px-4 py-3 border-r border-neutral-200 dark:border-neutral-700">0</th>
                                    <th class="px-4 py-3 border-r border-neutral-200 dark:border-neutral-700">1</th>
                                    <th class="px-4 py-3 border-r border-neutral-200 dark:border-neutral-700">2</th>
                                    <th class="px-4 py-3 text-center w-20 border-r border-neutral-200 dark:border-neutral-700">N 1'</th>
                                    <th class="px-4 py-3 text-center w-20 border-r border-neutral-200 dark:border-neutral-700">N 5'</th>
                                    <th class="px-4 py-3 text-center w-20">N 10'</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                                @php
                                    $apgarRows = [
                                        ['label' => 'Frekuensi Jantung', 'zero' => 'Tidak Ada', 'one' => '< 100', 'two' => '> 100', 'fields' => ['f1','f5','f10']],
                                        ['label' => 'Usaha Nafas', 'zero' => 'Tidak Ada', 'one' => 'Lambat Tak Teratur', 'two' => 'Menangis Kuat', 'fields' => ['u1','u5','u10']],
                                        ['label' => 'Tonus Otot', 'zero' => 'Lumpuh', 'one' => 'Ext. Fleksi Sedikit', 'two' => 'Gerakan Aktif', 'fields' => ['t1','t5','t10']],
                                        ['label' => 'Refleks', 'zero' => 'Tidak Ada Respon', 'one' => 'Pergerakan Sedikit', 'two' => 'Menangis', 'fields' => ['r1','r5','r10']],
                                        ['label' => 'Warna Kulit', 'zero' => 'Biru Pucat', 'one' => 'Tubuh Kemerahan, Kaki Biru', 'two' => 'Kemerahan', 'fields' => ['w1','w5','w10']],
                                    ];
                                @endphp
                                @foreach($apgarRows as $row)
                                <tr>
                                    <td class="px-4 py-3 font-semibold border-r border-neutral-200 dark:border-neutral-700">{{ $row['label'] }}</td>
                                    <td class="px-4 py-3 border-r border-neutral-200 dark:border-neutral-700 text-neutral-600 dark:text-neutral-400">{{ $row['zero'] }}</td>
                                    <td class="px-4 py-3 border-r border-neutral-200 dark:border-neutral-700 text-neutral-600 dark:text-neutral-400">{{ $row['one'] }}</td>
                                    <td class="px-4 py-3 border-r border-neutral-200 dark:border-neutral-700 text-neutral-600 dark:text-neutral-400">{{ $row['two'] }}</td>
                                    @foreach($row['fields'] as $idx => $field)
                                    <td class="p-1 {{ $idx < 2 ? 'border-r border-neutral-200 dark:border-neutral-700' : '' }}">
                                        <select wire:model.live="{{ $field }}" class="w-full text-center bg-transparent border-0 focus:ring-0 text-sm font-bold text-[#4C5C2D]">
                                            <option value="0">0</option>
                                            <option value="1">1</option>
                                            <option value="2">2</option>
                                        </select>
                                    </td>
                                    @endforeach
                                </tr>
                                @endforeach
                                <tr class="bg-neutral-100 dark:bg-neutral-900/60 font-bold border-t-2 border-neutral-300 dark:border-neutral-700">
                                    <td colspan="4" class="px-4 py-3 text-right border-r border-neutral-200 dark:border-neutral-700">Jumlah Nilai :</td>
                                    <td class="px-4 py-3 text-center text-sm text-[#4C5C2D] border-r border-neutral-200 dark:border-neutral-700">
                                        {{ (int)$f1 + (int)$u1 + (int)$t1 + (int)$r1 + (int)$w1 }}
                                    </td>
                                    <td class="px-4 py-3 text-center text-sm text-[#4C5C2D] border-r border-neutral-200 dark:border-neutral-700">
                                        {{ (int)$f5 + (int)$u5 + (int)$t5 + (int)$r5 + (int)$w5 }}
                                    </td>
                                    <td class="px-4 py-3 text-center text-sm text-[#4C5C2D]">
                                        {{ (int)$f10 + (int)$u10 + (int)$t10 + (int)$r10 + (int)$w10 }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <hr class="border-neutral-100 dark:border-neutral-700" />
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <flux:input label="Resusitasi" wire:model="resusitas" placeholder="Tindakan resusitasi..." />
                    <flux:textarea label="Obat Yang Diberikan" wire:model="obat_diberikan" rows="3" placeholder="Vit K, Salep Mata, dll..." />
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <flux:input label="Mikasi (BAK Pertama)" wire:model="mikasi" placeholder="Jam / Status BAK Pertama" />
                    <flux:input label="Mikonium (BAB Pertama)" wire:model="mikonium" placeholder="Jam / Status BAB Pertama" />
                </div>
            </div>
        </div>

    </div>

    {{-- Floating Minimap --}}
    <div x-data="{
            activeSection: 1,
            init() {
                window.addEventListener('scroll', () => {
                    [1,2,3,4].forEach(id => {
                        const el = document.getElementById('section-' + id);
                        if (el) {
                            const rect = el.getBoundingClientRect();
                            if (rect.top <= 200 && rect.bottom >= 200) this.activeSection = id;
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
            <span :class="activeSection===1?'opacity-100 text-[#4C5C2D]':'opacity-0 group-hover:opacity-100 text-neutral-400'" class="text-[10px] font-black uppercase tracking-widest transition-all duration-300">Identitas</span>
            <button @click="scrollTo(1)" :class="activeSection===1?'bg-[#4C5C2D] text-white scale-110 shadow-lg':'bg-white dark:bg-neutral-800 text-neutral-400 border border-neutral-200 shadow-sm hover:scale-105'" class="w-10 h-10 rounded-xl flex items-center justify-center transition-all duration-300">
                <flux:icon name="user" class="w-5 h-5" />
            </button>
        </div>
        <div class="group flex items-center justify-end gap-3">
            <span :class="activeSection===2?'opacity-100 text-[#4C5C2D]':'opacity-0 group-hover:opacity-100 text-neutral-400'" class="text-[10px] font-black uppercase tracking-widest transition-all duration-300">Fisik & Waktu</span>
            <button @click="scrollTo(2)" :class="activeSection===2?'bg-[#4C5C2D] text-white scale-110 shadow-lg':'bg-white dark:bg-neutral-800 text-neutral-400 border border-neutral-200 shadow-sm hover:scale-105'" class="w-10 h-10 rounded-xl flex items-center justify-center transition-all duration-300">
                <flux:icon name="bars-3-bottom-left" class="w-5 h-5" />
            </button>
        </div>
        <div class="group flex items-center justify-end gap-3">
            <span :class="activeSection===3?'opacity-100 text-[#4C5C2D]':'opacity-0 group-hover:opacity-100 text-neutral-400'" class="text-[10px] font-black uppercase tracking-widest transition-all duration-300">Persalinan</span>
            <button @click="scrollTo(3)" :class="activeSection===3?'bg-[#4C5C2D] text-white scale-110 shadow-lg':'bg-white dark:bg-neutral-800 text-neutral-400 border border-neutral-200 shadow-sm hover:scale-105'" class="w-10 h-10 rounded-xl flex items-center justify-center transition-all duration-300">
                <flux:icon name="clipboard-document-list" class="w-5 h-5" />
            </button>
        </div>
        <div class="group flex items-center justify-end gap-3">
            <span :class="activeSection===4?'opacity-100 text-[#4C5C2D]':'opacity-0 group-hover:opacity-100 text-neutral-400'" class="text-[10px] font-black uppercase tracking-widest transition-all duration-300">APGAR & Obat</span>
            <button @click="scrollTo(4)" :class="activeSection===4?'bg-[#4C5C2D] text-white scale-110 shadow-lg':'bg-white dark:bg-neutral-800 text-neutral-400 border border-neutral-200 shadow-sm hover:scale-105'" class="w-10 h-10 rounded-xl flex items-center justify-center transition-all duration-300">
                <flux:icon name="heart" class="w-5 h-5" />
            </button>
        </div>
    </div>
</div>
