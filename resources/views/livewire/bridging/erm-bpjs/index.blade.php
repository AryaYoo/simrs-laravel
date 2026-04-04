<div class="flex flex-col gap-6 pb-8">
    {{-- Header --}}
    <div class="flex items-center justify-between flex-wrap gap-4">
        <div class="flex items-center gap-3">
            <div class="flex items-center justify-center w-10 h-10 rounded-xl bg-[#4C5C2D] text-white shadow">
                <flux:icon name="document-text" class="w-5 h-5" />
            </div>
            <div>
                <nav class="text-xs text-neutral-400 mb-0.5">
                    <span>Bridging</span>
                    <span class="mx-1">/</span>
                    <span class="font-medium text-[#4C5C2D]">ERM BPJS</span>
                </nav>
                <h1 class="text-2xl font-bold text-neutral-800">Rekam Medis Elektronik (E-Claim)</h1>
            </div>
        </div>
        
        {{-- Tombol Uji Koneksi --}}
        <div>
            <button wire:click="testConnection" wire:loading.attr="disabled"
                class="flex items-center gap-2 px-4 py-2 bg-white border border-slate-300 text-slate-700 text-sm font-medium rounded-lg hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#4C5C2D] transition-colors shadow-sm disabled:opacity-50">
                <flux:icon name="wifi" class="w-4 h-4 text-[#4C5C2D]" />
                <span wire:loading.remove wire:target="testConnection">Uji Koneksi API</span>
                <span wire:loading wire:target="testConnection">Menguji...</span>
            </button>
        </div>
    </div>

    {{-- Filter Bar --}}
    <div class="bg-white rounded-xl border border-neutral-200 shadow-sm p-4 flex flex-wrap gap-3 items-end">
        <div class="flex flex-col gap-1">
            <label class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Dari</label>
            <input type="date" wire:model.live="dari"
                   class="rounded-lg border-slate-300 text-sm focus:border-[#4C5C2D] focus:ring-[#4C5C2D]">
        </div>
        <div class="flex flex-col gap-1">
            <label class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Sampai</label>
            <input type="date" wire:model.live="sampai"
                   class="rounded-lg border-slate-300 text-sm focus:border-[#4C5C2D] focus:ring-[#4C5C2D]">
        </div>
        <div class="flex flex-col gap-1 flex-1 min-w-[200px]">
            <label class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Cari</label>
            <div class="relative">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </span>
                <input type="text" wire:model.live.debounce.300ms="search"
                       placeholder="No. SEP / No. Rawat / Nama pasien..."
                       class="w-full pl-9 rounded-lg border-slate-300 text-sm focus:border-[#4C5C2D] focus:ring-[#4C5C2D]">
            </div>
        </div>
        <div class="flex flex-col gap-1">
            <label class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Per halaman</label>
            <select wire:model.live="perPage" class="rounded-lg border-slate-300 text-sm focus:border-[#4C5C2D] focus:ring-[#4C5C2D]">
                <option value="10">10</option>
                <option value="15">15</option>
                <option value="25">25</option>
                <option value="50">50</option>
            </select>
        </div>
    </div>

    {{-- Data Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-neutral-200 overflow-hidden">
        <div wire:loading.delay class="w-full h-1 bg-[#4C5C2D]/20 overflow-hidden">
            <div class="h-full bg-[#4C5C2D] animate-pulse w-2/3"></div>
        </div>

        <table class="w-full text-left border-collapse text-sm">
            <thead>
                <tr class="bg-slate-50 text-slate-400 text-[10px] uppercase font-bold tracking-wider">
                    <th class="px-5 py-3 w-10">
                        <input type="checkbox" wire:model.live="selectAll" 
                               class="rounded border-slate-300 text-[#4C5C2D] focus:ring-[#4C5C2D]">
                    </th>
                    <th class="px-5 py-3">Tgl. SEP</th>
                    <th class="px-5 py-3">No. SEP</th>
                    <th class="px-5 py-3">Nama Pasien</th>
                    <th class="px-5 py-3">Jenis</th>
                    <th class="px-5 py-3">Status RME</th>
                    <th class="px-5 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-slate-700">
                @forelse($seps as $sep)
                @php
                    $hasResume  = (bool) $sep->resume;
                    $lastLog    = $sep->logs->sortByDesc('tgl_kirim')->first();
                    $isSent     = $lastLog?->status_sukses == 1;
                @endphp
                <tr class="hover:bg-slate-50 transition-colors {{ in_array($sep->no_rawat, $selected) ? 'bg-indigo-50/30' : '' }}">
                    <td class="px-5 py-3">
                        <input type="checkbox" wire:model.live="selected" value="{{ $sep->no_rawat }}"
                               class="rounded border-slate-300 text-[#4C5C2D] focus:ring-[#4C5C2D]">
                    </td>
                    <td class="px-5 py-3 whitespace-nowrap text-slate-500">{{ \Carbon\Carbon::parse($sep->tglsep)->format('d M Y') }}</td>
                    <td class="px-5 py-3 font-mono text-xs text-indigo-600">{{ $sep->no_sep }}</td>
                    <td class="px-5 py-3">
                        <div class="font-semibold text-slate-800">{{ $sep->nama_pasien }}</div>
                        <div class="text-[11px] text-slate-400">{{ $sep->no_rawat }} &bull; {{ $sep->nomr }}</div>
                    </td>
                    <td class="px-5 py-3">
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold
                            {{ $sep->jnspelayanan == '1' ? 'bg-blue-100 text-blue-700' : 'bg-amber-100 text-amber-700' }}">
                            {{ $sep->jnspelayanan == '1' ? 'RANAP' : 'RALAN' }}
                        </span>
                    </td>
                    <td class="px-5 py-3">
                        @if($isSent)
                            <span class="flex items-center gap-1 text-xs font-semibold text-green-600">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Terkirim
                            </span>
                        @elseif($hasResume)
                            <span class="flex items-center gap-1 text-xs font-semibold text-blue-600">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                Resume Siap
                            </span>
                        @else
                            <span class="text-xs text-slate-400 italic">Belum Diisi</span>
                        @endif
                    </td>
                    <td class="px-5 py-3">
                        <div class="flex items-center justify-end gap-2">
                            {{-- Edit Resume --}}
                            <button wire:click="editResume('{{ $sep->no_rawat }}')"
                                    class="p-1.5 rounded-lg text-[#4C5C2D] hover:bg-[#4C5C2D]/10 transition"
                                    title="Isi / Edit Resume Medis">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </button>

                            {{-- Sync / Update --}}
                            @if($hasResume)
                            <button wire:click="syncToBpjs('{{ $sep->no_rawat }}')"
                                    wire:confirm="{{ $isSent ? 'Kirim ULANG / UPDATE Rekam Medis ini ke BPJS?' : 'Kirim Rekam Medis ini ke BPJS?' }}"
                                    wire:loading.attr="disabled"
                                    wire:target="syncToBpjs('{{ $sep->no_rawat }}')"
                                    class="p-1.5 rounded-lg transition disabled:opacity-50 disabled:cursor-wait {{ $isSent ? 'text-amber-600 hover:bg-amber-50' : 'text-indigo-600 hover:bg-indigo-50' }}"
                                    title="{{ $isSent ? 'Kirim ULANG / UPDATE ke BPJS' : 'Kirim ke BPJS' }}">
                                <svg wire:loading.remove wire:target="syncToBpjs('{{ $sep->no_rawat }}')" 
                                     class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                     @if($isSent)
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                     @else
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                     @endif
                                </svg>
                                <svg wire:loading wire:target="syncToBpjs('{{ $sep->no_rawat }}')" 
                                     class="animate-spin w-4 h-4 {{ $isSent ? 'text-amber-600' : 'text-indigo-600' }}" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-5 py-16 text-center">
                        <div class="flex flex-col items-center text-slate-400">
                            <svg class="w-12 h-12 mb-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <span class="text-sm font-medium">Tidak ada data SEP ditemukan</span>
                            <span class="text-xs mt-1">Coba ubah filter tanggal atau kata kunci pencarian.</span>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div class="px-5 py-4 border-t border-slate-100">
            {{ $seps->links() }}
        </div>
    </div>

    {{-- Modal: Resume Medis --}}
    @if($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4"
         x-data x-on:keydown.escape.window="$wire.set('showModal', false)">
        {{-- Backdrop --}}
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm"
             wire:click="$set('showModal', false)"></div>

        {{-- Modal Card --}}
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] flex flex-col overflow-hidden">
            {{-- Modal Header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 bg-[#4C5C2D]">
                <div>
                    <h2 class="text-base font-bold text-white">Resume Medis (Discharge Summary)</h2>
                    <p class="text-xs text-white/70 mt-0.5">No. rawat: <span class="font-mono">{{ $activeNoRawat }}</span></p>
                </div>
                <button wire:click="$set('showModal', false)" class="text-white/60 hover:text-white p-1.5 rounded-lg transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- Modal Body --}}
            <div class="overflow-y-auto flex-1 p-6 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-slate-600 mb-1.5 uppercase tracking-wide">
                            Keluhan Utama <span class="text-red-500">*</span>
                        </label>
                        <textarea wire:model="keluhan_utama" rows="2"
                                  class="w-full rounded-lg border-slate-300 text-sm focus:border-[#4C5C2D] focus:ring-[#4C5C2D]"
                                  placeholder="Keluhan utama pasien..."></textarea>
                        @error('keluhan_utama')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-slate-600 mb-1.5 uppercase tracking-wide">
                            Riwayat Penyakit / Alasan Masuk <span class="text-red-500">*</span>
                        </label>
                        <textarea wire:model="riwayat_penyakit" rows="3"
                                  class="w-full rounded-lg border-slate-300 text-sm focus:border-[#4C5C2D] focus:ring-[#4C5C2D]"
                                  placeholder="Alasan masuk RS..."></textarea>
                        @error('riwayat_penyakit')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-slate-600 mb-1.5 uppercase tracking-wide">Diagnosis Masuk</label>
                        <textarea wire:model="diagnosis_masuk" rows="2"
                                  class="w-full rounded-lg @error('diagnosis_masuk') border-red-300 @else border-slate-300 @enderror text-sm focus:border-[#4C5C2D] focus:ring-[#4C5C2D]"
                                  placeholder="Diagnosis awal / masuk..."></textarea>
                        @error('diagnosis_masuk')<p class="text-red-500 text-[10px] mt-1 font-medium">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1.5 uppercase tracking-wide">Pemeriksaan Fisik</label>
                        <textarea wire:model="pemeriksaan_fisik" rows="3"
                                  class="w-full rounded-lg @error('pemeriksaan_fisik') border-red-300 @else border-slate-300 @enderror text-sm focus:border-[#4C5C2D] focus:ring-[#4C5C2D]"
                                  placeholder="Hasil pemeriksaan fisik..."></textarea>
                        @error('pemeriksaan_fisik')<p class="text-red-500 text-[10px] mt-1 font-medium">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1.5 uppercase tracking-wide">Plan of Care</label>
                        <textarea wire:model="plan_of_care" rows="3"
                                  class="w-full rounded-lg @error('plan_of_care') border-red-300 @else border-slate-300 @enderror text-sm focus:border-[#4C5C2D] focus:ring-[#4C5C2D]"
                                  placeholder="Rencana tindak lanjut perawatan..."></textarea>
                        @error('plan_of_care')<p class="text-red-500 text-[10px] mt-1 font-medium">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1.5 uppercase tracking-wide">Instruksi Pulang</label>
                        <textarea wire:model="instruksi_pulang" rows="2"
                                  class="w-full rounded-lg @error('instruksi_pulang') border-red-300 @else border-slate-300 @enderror text-sm focus:border-[#4C5C2D] focus:ring-[#4C5C2D]"
                                  placeholder="Instruksi setelah keluar RS..."></textarea>
                        @error('instruksi_pulang')<p class="text-red-500 text-[10px] mt-1 font-medium">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1.5 uppercase tracking-wide">Alergi</label>
                        <textarea wire:model="alergi" rows="2"
                                  class="w-full rounded-lg @error('alergi') border-red-300 @else border-slate-300 @enderror text-sm focus:border-[#4C5C2D] focus:ring-[#4C5C2D]"
                                  placeholder="Alergi obat / makanan yang diketahui..."></textarea>
                        @error('alergi')<p class="text-red-500 text-[10px] mt-1 font-medium">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            {{-- Modal Footer --}}
            <div class="px-6 py-4 border-t border-slate-100 flex justify-end gap-3 bg-slate-50">
                <button wire:click="$set('showModal', false)"
                        class="px-5 py-2 rounded-lg text-sm text-slate-600 hover:bg-slate-200 transition font-medium">
                    Batal
                </button>
                <button wire:click="saveResume" wire:loading.attr="disabled"
                        class="px-5 py-2 rounded-lg text-sm bg-[#4C5C2D] text-white hover:bg-[#6A7E3F] transition font-semibold flex items-center gap-2 disabled:opacity-60">
                    <span wire:loading wire:target="saveResume">
                        <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                    </span>
                    Simpan Resume
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- Floating Bulk Action Bar --}}
    @if(count($selected) > 0)
    <div class="fixed bottom-6 left-1/2 -translate-x-1/2 z-40 animate-in slide-in-from-bottom-4 duration-300">
        <div class="bg-slate-900 text-white px-6 py-3 rounded-2xl shadow-2xl flex items-center gap-6 border border-white/10 backdrop-blur-md bg-opacity-90">
            <div class="flex items-center gap-3">
                <div class="bg-[#4C5C2D] text-white w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold">
                    {{ count($selected) }}
                </div>
                <span class="text-xs font-medium text-slate-300 italic">item terpilih</span>
            </div>
            
            <div class="h-6 w-px bg-white/20"></div>

            <div class="flex items-center gap-2">
                <button wire:click="resetSelection" class="text-xs text-slate-400 hover:text-white transition font-medium">
                    Batal
                </button>
                <button wire:click="bulkSyncToBpjs" 
                        wire:confirm="Sinkronkan {{ count($selected) }} data terpilih ke BPJS?"
                        wire:loading.attr="disabled"
                        class="bg-[#4C5C2D] hover:bg-[#63753a] text-white px-4 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-2 shadow-lg active:scale-95 disabled:opacity-50">
                    <svg wire:loading.remove wire:target="bulkSyncToBpjs" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                    <svg wire:loading wire:target="bulkSyncToBpjs" class="animate-spin w-3.5 h-3.5 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                    <span>Sinkronkan Massal</span>
                </button>
            </div>
        </div>
    </div>
    @endif
</div>
