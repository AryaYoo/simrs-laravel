<div class="animate-in fade-in duration-300">
    {{-- Header Content --}}
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-2">
            <flux:icon name="document-text" class="w-5 h-5 text-[#4C5C2D]" />
            <h3 class="text-sm font-semibold text-neutral-700 dark:text-neutral-200">Data Catatan Dokter</h3>
        </div>
        <div class="flex items-center gap-3">
            <span class="text-xs text-neutral-400 bg-neutral-100 dark:bg-neutral-700 px-2 py-1 rounded-full">{{ count($this->allCatatan) }} catatan</span>
            <flux:button wire:click="openCatatanModal" icon="plus" size="sm" variant="primary">
                Tambah Catatan
            </flux:button>
        </div>
    </div>

    {{-- Catatan History Table --}}
    <flux:table>
        <flux:table.columns>
            <flux:table.column>{{ __('No. Rawat & Waktu') }}</flux:table.column>
            <flux:table.column>{{ __('Pasien') }}</flux:table.column>
            <flux:table.column>{{ __('Dokter') }}</flux:table.column>
            <flux:table.column>{{ __('Catatan') }}</flux:table.column>
            <flux:table.column><div class="w-full text-center">{{ __('Aksi') }}</div></flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse ($this->allCatatan as $item)
                @php $itemJson = json_encode($item); @endphp
                <flux:table.row :key="$item['no_rawat'] . $item['tanggal'] . $item['jam'] . $item['kd_dokter']">
                    <flux:table.cell class="whitespace-nowrap">
                        <span class="text-xs font-mono font-bold text-neutral-800 dark:text-neutral-100 block">{{ $item['no_rawat'] }}</span>
                        <span class="text-[10px] text-neutral-400 block mt-0.5">{{ $item['tanggal'] }} &bull; {{ $item['jam'] }}</span>
                    </flux:table.cell>
                    <flux:table.cell>
                        <span class="text-xs font-bold text-neutral-800 dark:text-neutral-100 uppercase">{{ $item['nm_pasien'] }}</span>
                        <span class="text-[10px] font-mono text-neutral-400 block mt-0.5">RM: {{ $item['no_r_m'] }}</span>
                    </flux:table.cell>
                    <flux:table.cell>
                        <span class="text-xs text-neutral-700 dark:text-neutral-300">{{ $item['nm_dokter'] }}</span>
                        <span class="text-[10px] font-mono text-neutral-400 block mt-0.5">Kode: {{ $item['kd_dokter'] }}</span>
                    </flux:table.cell>
                    <flux:table.cell>
                        <p class="text-xs text-neutral-600 dark:text-neutral-400 max-w-sm whitespace-normal">{{ $item['catatan'] }}</p>
                    </flux:table.cell>
                    <flux:table.cell class="text-center">
                        <div class="flex items-center justify-center gap-1.5">
                            <button type="button" @click="$wire.editCatatan({{ $itemJson }})"
                                class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-medium bg-amber-50 text-amber-700 hover:bg-amber-100 transition-colors cursor-pointer border border-amber-200">
                                <flux:icon name="pencil-square" class="w-3.5 h-3.5" />
                                Edit
                            </button>
                            <button type="button" @click="
                                Swal.fire({
                                    title: 'Hapus Catatan?',
                                    text: 'Data yang dihapus tidak dapat dikembalikan!',
                                    icon: 'warning',
                                    showCancelButton: true,
                                    confirmButtonColor: '#4C5C2D',
                                    cancelButtonColor: '#d33',
                                    confirmButtonText: 'Ya, Hapus!',
                                    cancelButtonText: 'Batal'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        $wire.deleteCatatan('{{ $item['tanggal'] }}', '{{ $item['jam'] }}', '{{ $item['kd_dokter'] }}');
                                    }
                                });
                            "
                                class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-medium bg-red-50 text-red-600 hover:bg-red-100 transition-colors cursor-pointer border border-red-200">
                                <flux:icon name="trash" class="w-3.5 h-3.5" />
                                Hapus
                            </button>
                        </div>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="5" class="text-center py-12">
                        <flux:icon name="document-text" class="w-10 h-10 mx-auto mb-3 text-neutral-200 dark:text-neutral-700" />
                        <p class="text-xs text-neutral-400 uppercase tracking-widest font-medium italic">Belum ada catatan dokter</p>
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>

    {{-- MODAL INPUT CATATAN --}}
    <flux:modal wire:model="catatanDokterModalOpen" class="w-full max-w-2xl">
        <div class="flex flex-col h-full bg-white dark:bg-neutral-900" x-data="{ showFormStaff: null }">
            {{-- Header --}}
            <div class="px-6 py-4 border-b border-neutral-200 dark:border-neutral-700 bg-neutral-50/50 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="p-2 rounded-lg bg-[#4C5C2D]/10 text-[#4C5C2D]">
                        <flux:icon name="pencil-square" class="w-5 h-5" />
                    </div>
                    <h2 class="text-lg font-bold text-neutral-800 dark:text-neutral-100 uppercase" x-text="$wire.isEditCatatanMode ? 'Edit Catatan Dokter' : 'Input Catatan Dokter'"></h2>
                </div>
                <flux:modal.close>
                    <flux:button variant="ghost" icon="x-mark" />
                </flux:modal.close>
            </div>

            {{-- Form Content --}}
            <div class="flex-1 overflow-y-auto p-6 flex flex-col gap-6">
                {{-- Dokter --}}
                <div class="relative z-50">
                    <flux:label class="text-xs font-bold uppercase text-neutral-400 mb-2 block tracking-wider">Dokter :</flux:label>
                    <div class="flex items-center gap-2">
                        <div class="flex-1 flex items-center gap-2">
                            <div class="w-24 h-10 bg-neutral-100/50 dark:bg-neutral-800 border border-neutral-100 dark:border-neutral-700 rounded-lg flex items-center justify-center text-[11px] font-mono text-[#4C5C2D]">
                                {{ $kd_dokter_catatan ?: '-' }}
                            </div>
                            <div class="flex-1 h-10 bg-white dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-lg flex items-center px-3 text-sm font-semibold text-neutral-700 dark:text-neutral-200 shadow-sm">
                                {{ $nm_dokter_catatan ?: 'Klik Cari Dokter...' }}
                            </div>
                        </div>
                        <flux:button @click="showFormStaff = (showFormStaff === 'dr' ? null : 'dr')" variant="primary" icon="magnifying-glass" square />
                    </div>
                    {{-- Dropdown --}}
                    <div x-show="showFormStaff === 'dr'" @click.away="showFormStaff = null"
                        class="absolute top-20 right-0 left-0 z-50 bg-white dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-xl shadow-xl p-4 animate-in fade-in slide-in-from-top-2">
                        <flux:input wire:model.live.debounce.300ms="dokterSearch" placeholder="Ketik nama untuk mencari..." icon="user" />
                        <div class="mt-3 space-y-1 max-h-48 overflow-y-auto">
                            @forelse($this->dokterList as $dr)
                                <button type="button" wire:click="selectDokterCatatan('{{ $dr->kd_dokter }}', '{{ $dr->nm_dokter }}')" @click="showFormStaff = null"
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

                {{-- Catatan --}}
                <div class="relative z-40">
                    <flux:label class="text-xs font-bold uppercase text-neutral-400 mb-2 block tracking-wider">Catatan :</flux:label>
                    <flux:textarea wire:model="isi_catatan" rows="6" placeholder="Masukkan catatan dokter..." />
                </div>
            </div>

            {{-- Footer --}}
            <div class="p-6 border-t border-neutral-100 dark:border-neutral-800 bg-neutral-50/50 flex items-center justify-end gap-3 flex-shrink-0">
                <flux:modal.close>
                    <flux:button variant="ghost">Batal</flux:button>
                </flux:modal.close>
                <flux:button wire:click="saveCatatan" variant="primary" class="w-48">
                    <div class="flex items-center justify-center gap-2">
                        <flux:icon name="check" class="w-4 h-4" />
                        Simpan Catatan
                    </div>
                </flux:button>
            </div>
        </div>
    </flux:modal>
</div>
