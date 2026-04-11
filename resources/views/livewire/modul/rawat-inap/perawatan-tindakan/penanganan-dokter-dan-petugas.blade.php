<div class="animate-in fade-in duration-300">
    @php
        // Local script-based helpers can be moved to the parent or handled via Alpine events
    @endphp
    <script>
        window.confirmDeleteTindakan = function(type, kd, tgl, jam, staff) {
            Swal.fire({
                title: 'Hapus Tindakan?',
                text: 'Data yang dihapus tidak dapat dikembalikan!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#4C5C2D',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    @this.deleteTindakan(type, kd, tgl, jam, staff);
                }
            })
        }
    </script>
    {{-- Header Content --}}
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-2">
            <flux:icon name="identification" class="w-5 h-5 text-[#4C5C2D]" />
            <h3 class="text-sm font-semibold text-neutral-700 dark:text-neutral-200">Data Penanganan Dokter & Petugas</h3>
        </div>
        <div class="flex items-center gap-3">
            <span class="text-xs text-neutral-400 bg-neutral-100 dark:bg-neutral-700 px-2 py-1 rounded-full">{{ $allTindakan->count() }} catatan</span>
            <flux:button wire:click="openTindakanCreateModal" icon="plus" size="sm" variant="primary">
                Tambah Penanganan
            </flux:button>
        </div>
    </div>

    {{-- Treatment History Table (Consistent with SOAPIE) --}}
    <flux:table>
        <flux:table.columns>
            <flux:table.column>{{ __('Waktu Rawat') }}</flux:table.column>
            <flux:table.column>{{ __('Perawatan/Tindakan') }}</flux:table.column>
            <flux:table.column>{{ __('Dokter Pelaksana') }}</flux:table.column>
            <flux:table.column>{{ __('Petugas Pelaksana') }}</flux:table.column>
            <flux:table.column class="text-right pr-6">{{ __('Biaya') }}</flux:table.column>
            <flux:table.column>{{ __('Aksi') }}</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse ($allTindakan as $item)
                @php $itemJson = json_encode($item); @endphp
                <flux:table.row :key="$item['no_rawat'] . $item['kd_jenis_prw'] . $item['tgl_perawatan'] . $item['jam_rawat']">
                    <flux:table.cell class="whitespace-nowrap">
                        <span class="text-xs font-medium text-neutral-700 dark:text-neutral-300 block">{{ $item['tgl_perawatan'] }}</span>
                        <span class="text-[10px] font-mono text-neutral-400">{{ $item['jam_rawat'] }}</span>
                    </flux:table.cell>
                    <flux:table.cell>
                        <span class="text-xs font-bold text-neutral-800 dark:text-neutral-100 uppercase">{{ $item['nm_perawatan'] }}</span>
                    </flux:table.cell>
                    <flux:table.cell>
                        <span class="text-xs text-neutral-600 dark:text-neutral-400 truncate block max-w-[150px]">{{ $item['staff_dr'] }}</span>
                    </flux:table.cell>
                    <flux:table.cell>
                        <span class="text-xs text-neutral-600 dark:text-neutral-400 truncate block max-w-[150px]">{{ $item['staff_pr'] }}</span>
                    </flux:table.cell>
                    <flux:table.cell class="text-right pr-6">
                        <div class="inline-flex flex-col items-end">
                            <span class="text-[9px] text-neutral-400 font-bold uppercase leading-none mb-1">Total Biaya</span>
                            <span class="text-xs font-mono font-bold text-[#4C5C2D] dark:text-[#8CC7C4]">
                                Rp{{ number_format($item['biaya_rawat'], 0, ',', '.') }}
                            </span>
                        </div>
                    </flux:table.cell>
                    <flux:table.cell>
                        <div class="flex items-center gap-1.5">
                            <button type="button" @click="showDetailModal({{ $itemJson }})"
                                class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-medium bg-[#4C5C2D]/10 text-[#4C5C2D] hover:bg-[#4C5C2D]/20 transition-colors cursor-pointer border border-[#4C5C2D]/20">
                                <flux:icon name="eye" class="w-3.5 h-3.5" />
                                Detail
                            </button>
                            <button type="button" @click="editTindakan({{ $itemJson }})"
                                class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-medium bg-amber-50 text-amber-700 hover:bg-amber-100 transition-colors cursor-pointer border border-amber-200">
                                <flux:icon name="pencil-square" class="w-3.5 h-3.5" />
                                Edit
                            </button>
                        </div>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="6" class="text-center py-12">
                        <flux:icon name="identification" class="w-10 h-10 mx-auto mb-3 text-neutral-200 dark:text-neutral-700" />
                        <p class="text-xs text-neutral-400 uppercase tracking-widest font-medium italic">Belum ada penanganan yang tercatat</p>
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>

    {{-- Layer 1: CREATE FLYOUT MODAL --}}
    <flux:modal wire:model="tindakanCreateModalOpen" variant="flyout" class="w-full max-w-2xl">
        <div class="flex flex-col h-full bg-white dark:bg-neutral-900" x-data="{ showFormStaff: null }">
            {{-- Header --}}
            <div class="px-6 py-4 border-b border-neutral-200 dark:border-neutral-700 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="p-2 rounded-lg bg-[#4C5C2D]/10 text-[#4C5C2D]">
                        <flux:icon name="plus-circle" class="w-5 h-5" />
                    </div>
                    <h2 class="text-lg font-bold text-neutral-800 dark:text-neutral-100 uppercase">Input Penanganan</h2>
                </div>
                <flux:modal.close>
                    <flux:button variant="ghost" icon="x-mark" size="sm" />
                </flux:modal.close>
            </div>

            {{-- Form Content --}}
            <div class="flex-1 overflow-y-auto p-6 flex flex-col gap-8">
                {{-- Staff Selection Section --}}
                <div class="space-y-6">
                    {{-- Row: Dokter --}}
                    <div class="relative">
                        <flux:label class="text-xs font-bold uppercase text-neutral-400 mb-2 block tracking-wider">Dokter Pelaksana :</flux:label>
                        <div class="flex items-center gap-2">
                            <div class="flex-1 flex items-center gap-2">
                                <div class="w-20 h-10 bg-neutral-100/50 dark:bg-neutral-800 border border-neutral-100 dark:border-neutral-700 rounded-lg flex items-center justify-center text-[11px] font-mono text-[#4C5C2D]">
                                    {{ $kd_dokter_tindakan ?: '-' }}
                                </div>
                                <div class="flex-1 h-10 bg-white dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-lg flex items-center px-3 text-sm font-semibold text-neutral-700 dark:text-neutral-200 shadow-sm">
                                    {{ $nm_dokter_tindakan ?: 'Klik Cari Dokter...' }}
                                </div>
                            </div>
                            <flux:button @click="showFormStaff = (showFormStaff === 'dr' ? null : 'dr')" variant="primary" icon="magnifying-glass" square />
                        </div>
                        {{-- Dropdown --}}
                        <div x-show="showFormStaff === 'dr'" @click.away="showFormStaff = null"
                            class="absolute top-20 right-0 left-0 z-50 bg-white dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl shadow-xl p-4 animate-in fade-in slide-in-from-top-2">
                            <flux:input wire:model.live.debounce.300ms="dokterSearch" placeholder="Ketik nama untuk mencari..." icon="user" />
                            <div class="mt-3 space-y-1 max-h-48 overflow-y-auto">
                                @forelse($dokterList as $dr)
                                    <button type="button" wire:click="selectDokter('{{ $dr->kd_dokter }}', '{{ $dr->nm_dokter }}')" @click="showFormStaff = null"
                                        class="w-full flex items-center justify-between p-3 rounded-lg hover:bg-[#F1F5E9] text-left transition-colors group">
                                        <span class="text-sm font-semibold text-neutral-700 group-hover:text-[#4C5C2D]">{{ $dr->nm_dokter }}</span>
                                        <span class="text-[10px] font-mono text-neutral-400 bg-neutral-100 rounded px-1.5 py-0.5">{{ $dr->kd_dokter }}</span>
                                    </button>
                                @empty
                                    <div class="p-8 text-center text-xs text-neutral-400 italic">Cari dokter minimal 3 huruf...</div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    {{-- Row: Petugas --}}
                    <div class="relative">
                        <flux:label class="text-xs font-bold uppercase text-neutral-400 mb-2 block tracking-wider">Petugas Pelaksana :</flux:label>
                        <div class="flex items-center gap-2">
                            <div class="flex-1 flex items-center gap-2">
                                <div class="w-20 h-10 bg-neutral-100/50 dark:bg-neutral-800 border border-neutral-100 dark:border-neutral-700 rounded-lg flex items-center justify-center text-[11px] font-mono text-blue-600">
                                    {{ $nip_tindakan ?: '-' }}
                                </div>
                                <div class="flex-1 h-10 bg-white dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-lg flex items-center px-3 text-sm font-semibold text-neutral-700 dark:text-neutral-200 shadow-sm">
                                    {{ $nm_petugas_tindakan ?: 'Klik Cari Petugas...' }}
                                </div>
                            </div>
                            <flux:button @click="showFormStaff = (showFormStaff === 'pr' ? null : 'pr')" variant="primary" icon="magnifying-glass" square />
                        </div>
                        {{-- Dropdown --}}
                        <div x-show="showFormStaff === 'pr'" @click.away="showFormStaff = null"
                            class="absolute top-20 right-0 left-0 z-50 bg-white dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl shadow-xl p-4 animate-in fade-in slide-in-from-top-2">
                            <flux:input wire:model.live.debounce.300ms="petugasSearch" placeholder="Ketik nama untuk mencari..." icon="user" />
                            <div class="mt-3 space-y-1 max-h-48 overflow-y-auto">
                                @forelse($petugasList as $pr)
                                    <button type="button" wire:click="selectPetugas('{{ $pr->nip }}', '{{ $pr->nama }}')" @click="showFormStaff = null"
                                        class="w-full flex items-center justify-between p-3 rounded-lg hover:bg-blue-50 text-left transition-colors group">
                                        <span class="text-sm font-semibold text-neutral-700 group-hover:text-blue-600">{{ $pr->nama }}</span>
                                        <span class="text-[10px] font-mono text-neutral-400 bg-neutral-100 rounded px-1.5 py-0.5">{{ $pr->nip }}</span>
                                    </button>
                                @empty
                                    <div class="p-8 text-center text-xs text-neutral-400 italic">Cari petugas minimal 3 huruf...</div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="border-neutral-100 dark:border-neutral-800">

                {{-- Treatment Links --}}
                <div class="space-y-4">
                    <flux:label class="text-xs font-bold uppercase text-neutral-400 block mb-2 tracking-widest">Pilih Jenis Tindakan :</flux:label>
                    <div class="grid grid-cols-2 gap-4">
                        <button type="button" wire:click="openTindakanLookup('dr')"
                            class="flex flex-col items-center justify-center gap-3 p-8 rounded-2xl bg-white dark:bg-neutral-800 border-2 border-dashed border-[#4C5C2D]/20 hover:border-[#4C5C2D] hover:bg-[#F1F5E9]/30 transition-all group shadow-sm active:scale-95">
                            <div class="p-3 rounded-full bg-[#4C5C2D]/10 text-[#4C5C2D]">
                                <flux:icon name="paper-clip" class="w-6 h-6" />
                            </div>
                            <span class="text-xs font-bold text-[#4C5C2D] uppercase tracking-wider">Tindakan Medis (Dokter)</span>
                        </button>
                        <button type="button" wire:click="openTindakanLookup('pr')"
                            class="flex flex-col items-center justify-center gap-3 p-8 rounded-2xl bg-white dark:bg-neutral-800 border-2 border-dashed border-blue-600/20 hover:border-blue-600 hover:bg-blue-50/30 transition-all group shadow-sm active:scale-95">
                            <div class="p-3 rounded-full bg-blue-600/10 text-blue-600">
                                <flux:icon name="paper-clip" class="w-6 h-6" />
                            </div>
                            <span class="text-xs font-bold text-blue-600 uppercase tracking-wider">Tindakan Paramedis (Petugas)</span>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            <div class="p-6 border-t border-neutral-100 dark:border-neutral-800 bg-neutral-50/50 dark:bg-neutral-800 flex items-center gap-3">
                <flux:modal.close>
                    <flux:button variant="ghost" class="flex-1">Selesai / Tutup</flux:button>
                </flux:modal.close>
            </div>
        </div>
    </flux:modal>

    {{-- Layer 2: SINGLE SELECTION LOOKUP MODAL --}}
    <flux:modal wire:model="tindakanLookupOpen" class="w-full max-w-4xl z-[60]">
        <div class="flex flex-col h-full bg-white dark:bg-neutral-900 overflow-hidden">
            <div class="p-6 border-b border-neutral-100 dark:border-neutral-800 bg-neutral-50/50 dark:bg-neutral-800/50">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-lg font-bold text-neutral-800 dark:text-neutral-100 flex items-center gap-2 uppercase tracking-wide">
                        <flux:icon name="magnifying-glass" class="w-5 h-5 text-[#4C5C2D]" />
                        Cari {{ $lookupType == 'dr' ? 'Tindakan Medis' : 'Tindakan Paramedis' }}
                    </h2>
                    <flux:modal.close>
                        <flux:button variant="ghost" icon="x-mark" />
                    </flux:modal.close>
                </div>
                
                <flux:input wire:model.live.debounce.300ms="tindakanSearch" placeholder="Masukkan nama tindakan yang dicari..." icon="magnifying-glass" clearable />
            </div>

            <div class="flex-1 overflow-y-auto p-0 min-h-[50vh]">
                <table class="w-full text-left border-collapse">
                    <thead class="sticky top-0 z-10 bg-neutral-100 dark:bg-neutral-900 border-b border-neutral-200">
                        <tr>
                            <th class="p-4 text-[10px] font-bold text-neutral-400 uppercase tracking-widest pl-6">Kode</th>
                            <th class="p-4 text-[10px] font-bold text-neutral-400 uppercase tracking-widest">Nama Perawatan</th>
                            <th class="p-4 text-[10px] font-bold text-neutral-400 uppercase tracking-widest text-right pr-6">Tarif</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-100 dark:divide-neutral-800">
                        @forelse($tindakanList as $t)
                            <tr class="hover:bg-[#F1F5E9]/50 dark:hover:bg-neutral-800 transition-colors cursor-pointer group" wire:click="storeSingleTindakan('{{ $t->kd_jenis_prw }}')">
                                <td class="p-4 pl-6 text-xs font-mono text-neutral-500 group-hover:text-[#4C5C2D]">{{ $t->kd_jenis_prw }}</td>
                                <td class="p-4">
                                    <span class="text-sm font-bold text-neutral-700 dark:text-neutral-200 uppercase group-hover:text-[#4C5C2D]">{{ $t->nm_perawatan }}</span>
                                </td>
                                <td class="p-4 pr-6 text-right">
                                    <span class="text-sm font-black text-[#4C5C2D] dark:text-[#8CC7C4]">
                                        {{ number_format($lookupType == 'dr' ? ($t->total_byrdr ?: $t->total_byrdrpr) : ($t->total_byrpr ?: $t->total_byrdrpr), 0, ',', '.') }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="p-20 text-center">
                                    <flux:icon name="magnifying-glass" class="w-12 h-12 text-neutral-200 mx-auto mb-3" />
                                    <p class="text-sm text-neutral-400 italic">Cari nama tindakan minimal 3 huruf...</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="p-4 bg-neutral-50/50 dark:bg-neutral-800 border-t border-neutral-100 flex items-center justify-center">
                <p class="text-[10px] text-neutral-400 uppercase tracking-[.3em] font-medium">Klik pada baris untuk menyimpan tindakan secara otomatis</p>
            </div>
        </div>
    </flux:modal>

    {{-- Layer 3: DETAIL MODAL (Consistent with SOAPIE Detail) --}}
    <template x-teleport="body">
        <div x-show="detailModalOpen" @keydown.escape.window="closeDetailModal()" class="fixed inset-0 z-[70] flex items-center justify-center p-4">
            {{-- Backdrop --}}
            <div x-show="detailModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" 
                class="absolute inset-0 bg-neutral-900/60 backdrop-blur-sm" @click="closeDetailModal()"></div>
            
            {{-- Modal Panel --}}
            <div x-show="detailModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-90 translate-y-4" x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                class="relative w-full max-w-4xl max-h-[90vh] flex flex-col bg-white dark:bg-neutral-900 rounded-3xl shadow-2xl border border-neutral-200 dark:border-neutral-700 overflow-hidden">
                
                {{-- Header --}}
                <div class="flex items-center justify-between px-6 py-4 border-b border-neutral-100 dark:border-neutral-800/50 bg-gradient-to-r from-[#F1F5E9] to-white dark:from-[#4C5C2D]/10 dark:to-neutral-900 flex-shrink-0">
                    <div class="flex items-center gap-3">
                        <div class="p-2.5 rounded-2xl bg-[#4C5C2D]/10 dark:bg-[#4C5C2D]/30 border border-[#4C5C2D]/20">
                            <flux:icon name="identification" class="w-5 h-5 text-[#4C5C2D]" />
                        </div>
                        <div>
                            <h2 class="font-bold text-neutral-800 dark:text-neutral-100 text-base uppercase tracking-tight">Detail Penanganan Pasien</h2>
                            <p class="text-xs text-neutral-500">
                                <span x-text="detail.tgl_perawatan"></span> &bull;
                                <span x-text="detail.jam_rawat"></span> &bull;
                                <span class="font-mono" x-text="detail.no_rawat"></span>
                            </p>
                        </div>
                    </div>
                    <button @click="closeDetailModal()" class="p-1.5 rounded-xl hover:bg-white/60 dark:hover:bg-neutral-800 transition-colors text-neutral-400 hover:text-neutral-600">
                        <flux:icon name="x-mark" class="w-5 h-5" />
                    </button>
                </div>

                {{-- Scrollable Content --}}
                <div class="overflow-y-auto flex-1 p-6 flex flex-col gap-6 bg-neutral-50/50 dark:bg-neutral-900/30">
                    {{-- Identitas Pasien (Context Row) --}}
                    <section class="bg-white dark:bg-neutral-800/80 p-5 rounded-2xl border border-neutral-100 dark:border-neutral-700 shadow-sm">
                        <p class="text-[10px] font-bold uppercase tracking-widest text-[#6A7E3F] mb-4 flex items-center gap-1.5"><flux:icon name="user-circle" class="w-3.5 h-3.5" /> Konteks Pelayanan</p>
                        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                            <div class="bg-neutral-50/50 dark:bg-neutral-900 p-3 rounded-xl border border-neutral-100 dark:border-neutral-700">
                                <p class="text-[9px] text-neutral-400 font-bold uppercase mb-1">No. Rekam Medis</p>
                                <p class="text-sm font-bold text-neutral-700 dark:text-neutral-200 font-mono" x-text="detail.no_r_m"></p>
                            </div>
                            <div class="bg-[#4C5C2D]/5 dark:bg-[#4C5C2D]/20 p-3 rounded-xl border border-[#4C5C2D]/20 col-span-2">
                                <p class="text-[9px] text-[#4C5C2D] font-bold uppercase mb-1">Nama Pasien</p>
                                <p class="text-base font-black text-[#4C5C2D] dark:text-[#8CC7C4] uppercase" x-text="detail.nm_pasien"></p>
                            </div>
                            <div class="bg-neutral-50/50 dark:bg-neutral-900 p-3 rounded-xl border border-neutral-100 dark:border-neutral-700">
                                <p class="text-[9px] text-neutral-400 font-bold uppercase mb-1">Status Tagihan</p>
                                <span class="px-2 py-0.5 rounded-full text-[10px] bg-emerald-100 text-emerald-700 font-black uppercase">Tercatat</span>
                            </div>
                        </div>
                    </section>

                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-stretch">
                        {{-- Left: Pelaksana & Tindakan --}}
                        <div class="lg:col-span-12">
                            <section class="bg-white dark:bg-neutral-800/80 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-700 shadow-sm h-full flex flex-col">
                                <p class="text-[10px] font-bold uppercase tracking-widest text-[#6A7E3F] mb-6 flex items-center gap-1.5"><flux:icon name="document-text" class="w-3.5 h-3.5" /> Rincian Tindakan</p>
                                
                                <div class="bg-[#F1F5E9]/50 dark:bg-[#4C5C2D]/20 p-5 rounded-2xl border border-[#4C5C2D]/20 mb-6">
                                    <p class="text-[10px] font-bold text-[#4C5C2D] uppercase mb-1 tracking-widest">Penanganan / Tindakan :</p>
                                    <h3 class="text-xl font-black text-neutral-800 dark:text-neutral-100 uppercase" x-text="detail.nm_perawatan"></h3>
                                    <p class="text-xs font-mono text-neutral-400 mt-1" x-text="'Kode: ' + detail.kd_jenis_prw"></p>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    {{-- Dokter --}}
                                    <div class="bg-white dark:bg-neutral-900 p-4 rounded-2xl border border-neutral-100 dark:border-neutral-700 shadow-sm flex items-center gap-4">
                                        <div class="w-12 h-12 rounded-xl bg-[#4C5C2D]/10 flex items-center justify-center flex-shrink-0">
                                            <flux:icon name="user" class="w-6 h-6 text-[#4C5C2D]" />
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-[9px] font-bold text-neutral-400 uppercase tracking-widest">Dokter DPJP / Pelaksana</p>
                                            <p class="text-sm font-bold text-neutral-800 dark:text-neutral-100 truncate" x-text="detail.staff_dr"></p>
                                            <p class="text-[10px] font-mono text-neutral-400" x-text="detail.kd_staff_dr"></p>
                                        </div>
                                    </div>
                                    {{-- Petugas --}}
                                    <div class="bg-white dark:bg-neutral-900 p-4 rounded-2xl border border-neutral-100 dark:border-neutral-700 shadow-sm flex items-center gap-4">
                                        <div class="w-12 h-12 rounded-xl bg-blue-600/10 flex items-center justify-center flex-shrink-0">
                                            <flux:icon name="user-group" class="w-6 h-6 text-blue-600" />
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-[9px] font-bold text-neutral-400 uppercase tracking-widest">Petugas / Paramedis</p>
                                            <p class="text-sm font-bold text-neutral-800 dark:text-neutral-100 truncate" x-text="detail.staff_pr"></p>
                                            <p class="text-[10px] font-mono text-neutral-400" x-text="detail.kd_staff_pr"></p>
                                        </div>
                                    </div>
                                </div>
                            </section>
                        </div>

                        {{-- Section: Financial Breakdown --}}
                        <div class="lg:col-span-12">
                            <section class="bg-white dark:bg-neutral-800/80 p-6 rounded-2xl border border-neutral-100 dark:border-neutral-700 shadow-sm">
                                <p class="text-[10px] font-bold uppercase tracking-widest text-[#6A7E3F] mb-6 flex items-center gap-1.5"><flux:icon name="banknotes" class="w-3.5 h-3.5" /> Breakdown Komponen Biaya</p>
                                
                                <div class="grid grid-cols-2 md:grid-cols-3 gap-6">
                                    <div class="bg-neutral-50 dark:bg-neutral-900 p-4 rounded-2xl border border-neutral-100 dark:border-neutral-700">
                                        <p class="text-[9px] text-neutral-400 font-bold uppercase mb-2">Jasa Medis (Dr)</p>
                                        <p class="text-lg font-black text-neutral-700 dark:text-neutral-200" x-text="'Rp' + new Intl.NumberFormat('id-ID').format(detail.biaya_dr || 0)"></p>
                                    </div>
                                    <div class="bg-neutral-50 dark:bg-neutral-900 p-4 rounded-2xl border border-neutral-100 dark:border-neutral-700">
                                        <p class="text-[9px] text-neutral-400 font-bold uppercase mb-2">Jasa Paramedis</p>
                                        <p class="text-lg font-black text-neutral-700 dark:text-neutral-200" x-text="'Rp' + new Intl.NumberFormat('id-ID').format(detail.biaya_pr || 0)"></p>
                                    </div>
                                    <div class="bg-neutral-50 dark:bg-neutral-900 p-4 rounded-2xl border border-neutral-100 dark:border-neutral-700">
                                        <p class="text-[9px] text-neutral-400 font-bold uppercase mb-2">Material / Alat</p>
                                        <p class="text-lg font-black text-neutral-700 dark:text-neutral-200" x-text="'Rp' + new Intl.NumberFormat('id-ID').format(detail.biaya_material || 0)"></p>
                                    </div>
                                    <div class="bg-neutral-50 dark:bg-neutral-900 p-4 rounded-2xl border border-neutral-100 dark:border-neutral-700">
                                        <p class="text-[9px] text-neutral-400 font-bold uppercase mb-2">BHP</p>
                                        <p class="text-lg font-black text-neutral-700 dark:text-neutral-200" x-text="'Rp' + new Intl.NumberFormat('id-ID').format(detail.biaya_bhp || 0)"></p>
                                    </div>
                                    <div class="bg-neutral-50 dark:bg-neutral-900 p-4 rounded-2xl border border-neutral-100 dark:border-neutral-700">
                                        <p class="text-[9px] text-neutral-400 font-bold uppercase mb-2">KSO</p>
                                        <p class="text-lg font-black text-neutral-700 dark:text-neutral-200" x-text="'Rp' + new Intl.NumberFormat('id-ID').format(detail.biaya_kso || 0)"></p>
                                    </div>
                                    <div class="bg-[#4C5C2D]/10 dark:bg-[#4C5C2D]/30 p-4 rounded-2xl border border-[#4C5C2D]/30 flex flex-col justify-center">
                                        <p class="text-[9px] text-[#4C5C2D] font-bold uppercase mb-1">Total Tagihan</p>
                                        <p class="text-xl font-black text-[#4C5C2D] dark:text-[#8CC7C4]" x-text="'Rp' + new Intl.NumberFormat('id-ID').format(detail.biaya_rawat || 0)"></p>
                                    </div>
                                </div>
                            </section>
                        </div>
                    </div>
                </div>

                {{-- Detail Action Footer --}}
                <div class="px-6 py-5 border-t border-neutral-100 dark:border-neutral-800 bg-neutral-50/50 flex items-center justify-between flex-shrink-0">
                    <div class="flex items-center gap-3">
                        {{-- Edit Trigger --}}
                        <button type="button" @click="closeDetailModal(); $wire.editTindakan(detail)"
                            class="inline-flex items-center gap-2 px-6 py-2.5 rounded-2xl text-sm font-bold bg-amber-50 text-amber-700 border border-amber-200 hover:bg-amber-100 transition-all shadow-sm">
                            <flux:icon name="pencil-square" class="w-4 h-4" />
                            Edit Penanganan
                        </button>
                    </div>
                    
                    <div class="flex items-center gap-3">
                        <button type="button" @click="closeDetailModal(); confirmDeleteTindakan(detail.type, detail.kd_jenis_prw, detail.tgl_perawatan, detail.jam_rawat, detail.type == 'dr' ? detail.kd_staff_dr : (detail.type == 'pr' ? detail.kd_staff_pr : ''))"
                            class="px-6 py-2.5 rounded-2xl bg-white text-red-600 text-sm font-bold border border-red-100 hover:bg-red-50 transition-all shadow-sm">
                            Hapus Data
                        </button>
                        <button type="button" @click="closeDetailModal()"
                            class="px-8 py-2.5 rounded-2xl bg-neutral-900 text-white text-sm font-bold hover:bg-black transition-all shadow-lg active:scale-95">
                            Selesai
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </template>
</div>
