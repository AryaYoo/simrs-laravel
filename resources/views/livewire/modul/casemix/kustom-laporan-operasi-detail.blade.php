<div class="flex flex-col gap-6 pb-12">
    {{-- Header / Navigation --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <button onclick="history.back()" class="flex items-center justify-center w-10 h-8 rounded-md bg-[#4C5C2D] transition-colors hover:bg-[#3d4b24] shadow-sm">
                <flux:icon name="chevron-left" class="w-5 h-5 text-white" />
            </button>
            <div>
                <nav class="text-xs text-neutral-400 mb-0.5">
                    <a href="{{ route('modul.index') }}" wire:navigate class="hover:underline">Modul</a>
                    <span class="mx-1">/</span>
                    <a href="{{ route('modul.casemix.kustom-laporan-operasi') }}" wire:navigate class="hover:underline">Casemix</a>
                    <span class="mx-1">/</span>
                    <span class="text-neutral-700 dark:text-neutral-300 font-medium">Detail Laporan Operasi</span>
                </nav>
                <h1 class="text-xl font-bold text-neutral-800 dark:text-neutral-100">Detail Laporan Operasi</h1>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <button onclick="history.back()" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-semibold bg-neutral-100 dark:bg-neutral-700 text-neutral-700 dark:text-neutral-200 hover:bg-neutral-200 dark:hover:bg-neutral-600 transition-all">
                <flux:icon name="arrow-left" class="w-4 h-4" />
                <span>Kembali</span>
            </button>

            <a href="{{ route('modul.casemix.kustom-laporan-operasi.kustom-cetak', [str_replace('/', '-', $laporan->no_rawat), str_replace(' ', '_', $laporan->tanggal)]) }}"
                wire:navigate
                class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold bg-[#4C5C2D] text-white hover:bg-[#3d4b24] transition-all shadow-sm">
                <flux:icon name="printer" class="w-4 h-4" />
                <span>Cetak Dokumen</span>
            </a>
        </div>
    </div>

    {{-- SECTION 1: INFORMASI PASIEN --}}
    <div class="bg-white dark:bg-neutral-800 rounded-xl border border-neutral-200 dark:border-neutral-700 overflow-hidden shadow-sm">
        <div class="bg-[#4C5C2D] text-white px-5 py-3.5 flex items-center gap-2">
            <flux:icon name="user-circle" class="w-5 h-5" />
            <h2 class="text-sm font-bold tracking-wide">1. INFORMASI PASIEN</h2>
        </div>
        <div class="p-5">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                {{-- Tanggal Operasi --}}
                <div class="p-3.5 rounded-xl bg-neutral-50 dark:bg-neutral-900/50 border border-neutral-200/80 dark:border-neutral-700">
                    <span class="text-[11px] font-semibold text-neutral-400 block mb-1 uppercase tracking-wider">Tanggal Operasi</span>
                    <span class="text-sm font-bold text-neutral-800 dark:text-neutral-100 flex items-center gap-1.5">
                        <flux:icon name="calendar-days" class="w-4 h-4 text-[#4C5C2D] dark:text-[#8CC7C4]" />
                        {{ date('d-m-Y H:i', strtotime($laporan->tanggal)) }}
                    </span>
                </div>

                {{-- No Rawat --}}
                <div class="p-3.5 rounded-xl bg-neutral-50 dark:bg-neutral-900/50 border border-neutral-200/80 dark:border-neutral-700">
                    <span class="text-[11px] font-semibold text-neutral-400 block mb-1 uppercase tracking-wider">No Rawat</span>
                    <span class="text-sm font-mono font-bold text-[#4C5C2D] dark:text-[#8CC7C4]">
                        {{ $laporan->no_rawat }}
                    </span>
                </div>

                {{-- Nama Pasien --}}
                <div class="p-3.5 rounded-xl bg-neutral-50 dark:bg-neutral-900/50 border border-neutral-200/80 dark:border-neutral-700">
                    <span class="text-[11px] font-semibold text-neutral-400 block mb-1 uppercase tracking-wider">Nama Pasien</span>
                    <span class="text-sm font-bold text-neutral-800 dark:text-neutral-100 block">
                        {{ $laporan->regPeriksa->pasien->nm_pasien ?? '-' }}
                    </span>
                    <span class="text-xs text-neutral-400 font-mono">
                        RM: {{ $laporan->regPeriksa->pasien->no_rkm_medis ?? '-' }}
                    </span>
                </div>

                {{-- Jenis Ans --}}
                <div class="p-3.5 rounded-xl bg-neutral-50 dark:bg-neutral-900/50 border border-neutral-200/80 dark:border-neutral-700">
                    <span class="text-[11px] font-semibold text-neutral-400 block mb-1 uppercase tracking-wider">Jenis Ans</span>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold bg-[#4C5C2D]/10 text-[#4C5C2D] dark:bg-[#8CC7C4]/10 dark:text-[#8CC7C4]">
                        {{ $operasi->jenis_anasthesi ?? '-' }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- SECTION 2: LAPORAN OPERASI --}}
    <div class="bg-white dark:bg-neutral-800 rounded-xl border border-neutral-200 dark:border-neutral-700 overflow-hidden shadow-sm">
        <div class="bg-[#4C5C2D] text-white px-5 py-3.5 flex items-center gap-2">
            <flux:icon name="document-text" class="w-5 h-5" />
            <h2 class="text-sm font-bold tracking-wide">2. LAPORAN OPERASI</h2>
        </div>
        <div class="p-5 space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                {{-- Diagnosa Pre Operatif --}}
                <div class="p-3.5 rounded-xl bg-amber-50/60 dark:bg-amber-950/20 border border-amber-200/70 dark:border-amber-900/40">
                    <span class="text-[11px] font-bold text-amber-800 dark:text-amber-400 block mb-1 uppercase tracking-wider">Diagnosa Pre Operatif</span>
                    <span class="text-sm font-semibold text-neutral-800 dark:text-neutral-100">
                        {{ $laporan->diagnosa_preop ?: '-' }}
                    </span>
                </div>

                {{-- Diagnosa Post Operatif --}}
                <div class="p-3.5 rounded-xl bg-emerald-50/60 dark:bg-emerald-950/20 border border-emerald-200/70 dark:border-emerald-900/40">
                    <span class="text-[11px] font-bold text-emerald-800 dark:text-emerald-400 block mb-1 uppercase tracking-wider">Diagnosa Post Operatif</span>
                    <span class="text-sm font-semibold text-neutral-800 dark:text-neutral-100">
                        {{ $laporan->diagnosa_postop ?: '-' }}
                    </span>
                </div>

                {{-- Jaringan yang di-Eksisi/-Insisi --}}
                <div class="p-3.5 rounded-xl bg-neutral-50 dark:bg-neutral-900/50 border border-neutral-200/80 dark:border-neutral-700">
                    <span class="text-[11px] font-semibold text-neutral-400 block mb-1 uppercase tracking-wider">Jaringan yang di-Eksisi / -Insisi</span>
                    <span class="text-sm font-semibold text-neutral-800 dark:text-neutral-100">
                        {{ $laporan->jaringan_dieksekusi ?: '-' }}
                    </span>
                </div>

                {{-- Kirim PA --}}
                <div class="p-3.5 rounded-xl bg-neutral-50 dark:bg-neutral-900/50 border border-neutral-200/80 dark:border-neutral-700">
                    <span class="text-[11px] font-semibold text-neutral-400 block mb-1 uppercase tracking-wider">Kirim PA</span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-bold {{ $laporan->permintaan_pa === 'Ya' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-200' : 'bg-neutral-200 text-neutral-700 dark:bg-neutral-700 dark:text-neutral-300' }}">
                        {{ $laporan->permintaan_pa }}
                    </span>
                </div>

                {{-- Selesai Operasi --}}
                <div class="p-3.5 rounded-xl bg-neutral-50 dark:bg-neutral-900/50 border border-neutral-200/80 dark:border-neutral-700">
                    <span class="text-[11px] font-semibold text-neutral-400 block mb-1 uppercase tracking-wider">Selesai Operasi</span>
                    <span class="text-sm font-bold text-neutral-800 dark:text-neutral-100 flex items-center gap-1.5">
                        <flux:icon name="clock" class="w-4 h-4 text-emerald-600 dark:text-emerald-400" />
                        {{ date('d-m-Y H:i', strtotime($laporan->selesaioperasi)) }}
                    </span>
                </div>

                {{-- Nomor Implan --}}
                <div class="p-3.5 rounded-xl bg-neutral-50 dark:bg-neutral-900/50 border border-neutral-200/80 dark:border-neutral-700">
                    <span class="text-[11px] font-semibold text-neutral-400 block mb-1 uppercase tracking-wider">Nomor Implan</span>
                    <span class="text-sm font-mono font-semibold text-neutral-800 dark:text-neutral-100">
                        {{ $laporan->nomor_implan ?: '-' }}
                    </span>
                </div>
            </div>

            {{-- Isi Catatan Laporan Operasi --}}
            <div>
                <span class="text-xs font-bold text-neutral-700 dark:text-neutral-300 block mb-2 uppercase tracking-wider">Catatan Laporan Operasi</span>
                <div class="p-4 rounded-xl bg-neutral-50 dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-700 font-mono text-xs leading-relaxed text-neutral-800 dark:text-neutral-200 whitespace-pre-line min-h-[120px]">
                    {{ $laporan->laporan_operasi ?: 'Tidak ada catatan rincian operasi.' }}
                </div>
            </div>
        </div>
    </div>

    {{-- SECTION 3: TAGIHAN PERAWATAN --}}
    <div class="bg-white dark:bg-neutral-800 rounded-xl border border-neutral-200 dark:border-neutral-700 overflow-hidden shadow-sm">
        <div class="bg-[#4C5C2D] text-white px-5 py-3.5 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <flux:icon name="banknotes" class="w-5 h-5" />
                <h2 class="text-sm font-bold tracking-wide">3. TAGIHAN PERAWATAN & TIM OPERASI</h2>
            </div>
            @if($operasi)
                <span class="text-xs bg-white/20 px-2.5 py-1 rounded-md font-mono">Status: {{ $operasi->status ?: 'Ranap' }}</span>
            @endif
        </div>

        <div class="p-5 space-y-6">
            @if($operasi)
                {{-- Perawatan Header Banner --}}
                <div class="p-4 rounded-xl bg-[#4C5C2D]/10 border border-[#4C5C2D]/20 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                    <div>
                        <span class="text-[11px] font-bold text-[#4C5C2D] dark:text-[#8CC7C4] block uppercase tracking-wider mb-0.5">Perawatan (Paket Operasi)</span>
                        <h3 class="text-base font-bold text-neutral-900 dark:text-neutral-100">
                            {{ $operasi->paketOperasi->nm_perawatan ?? ($operasi->kode_paket ?: '-') }}
                        </h3>
                    </div>
                    <div class="text-left sm:text-right">
                        <span class="text-[11px] font-semibold text-neutral-500 dark:text-neutral-400 block">Kategori</span>
                        <span class="text-xs font-bold text-neutral-800 dark:text-neutral-200">{{ $operasi->kategori ?: '-' }}</span>
                    </div>
                </div>

                {{-- Tim Medis & Petugas dengan Tarif Komponen --}}
                <div>
                    <h3 class="text-xs font-bold text-neutral-700 dark:text-neutral-300 uppercase tracking-wider mb-3 pb-1 border-b border-neutral-200 dark:border-neutral-700 flex items-center gap-1.5">
                        <flux:icon name="user-group" class="w-4 h-4 text-[#4C5C2D] dark:text-[#8CC7C4]" />
                        <span>Rincian Tim Medis & Tarif Perawatan</span>
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 text-xs">
                        {{-- Operator 1 --}}
                        @if (($operasi->biayaoperator1 ?? 0) > 0)
                        <div class="p-3 rounded-xl bg-neutral-50 dark:bg-neutral-900/50 border border-neutral-200/80 dark:border-neutral-700 flex flex-col justify-between">
                            <div>
                                <span class="text-[11px] font-bold text-neutral-400 block mb-0.5 uppercase tracking-wider">Operator 1</span>
                                <span class="font-bold text-neutral-800 dark:text-neutral-100 block">{{ $operasi->dokterOperator1->nm_dokter ?? '-' }}</span>
                            </div>
                            <span class="mt-2 font-mono font-bold text-[#4C5C2D] dark:text-[#8CC7C4] text-xs">
                                {{ number_format($operasi->biayaoperator1, 0, ',', '.') }}
                            </span>
                        </div>
                        @endif

                        {{-- Operator 2 --}}
                        @if (($operasi->biayaoperator2 ?? 0) > 0)
                        <div class="p-3 rounded-xl bg-neutral-50 dark:bg-neutral-900/50 border border-neutral-200/80 dark:border-neutral-700 flex flex-col justify-between">
                            <div>
                                <span class="text-[11px] font-bold text-neutral-400 block mb-0.5 uppercase tracking-wider">Operator 2</span>
                                <span class="font-bold text-neutral-800 dark:text-neutral-100 block">{{ $operasi->dokterOperator2->nm_dokter ?? '-' }}</span>
                            </div>
                            <span class="mt-2 font-mono font-bold text-[#4C5C2D] dark:text-[#8CC7C4] text-xs">
                                {{ number_format($operasi->biayaoperator2, 0, ',', '.') }}
                            </span>
                        </div>
                        @endif

                        {{-- Operator 3 --}}
                        @if (($operasi->biayaoperator3 ?? 0) > 0)
                        <div class="p-3 rounded-xl bg-neutral-50 dark:bg-neutral-900/50 border border-neutral-200/80 dark:border-neutral-700 flex flex-col justify-between">
                            <div>
                                <span class="text-[11px] font-bold text-neutral-400 block mb-0.5 uppercase tracking-wider">Operator 3</span>
                                <span class="font-bold text-neutral-800 dark:text-neutral-100 block">{{ $operasi->dokterOperator3->nm_dokter ?? '-' }}</span>
                            </div>
                            <span class="mt-2 font-mono font-bold text-[#4C5C2D] dark:text-[#8CC7C4] text-xs">
                                {{ number_format($operasi->biayaoperator3, 0, ',', '.') }}
                            </span>
                        </div>
                        @endif

                        {{-- Asisten Operator 1 --}}
                        @if (($operasi->biayaasisten_operator1 ?? 0) > 0)
                        <div class="p-3 rounded-xl bg-neutral-50 dark:bg-neutral-900/50 border border-neutral-200/80 dark:border-neutral-700 flex flex-col justify-between">
                            <div>
                                <span class="text-[11px] font-bold text-neutral-400 block mb-0.5 uppercase tracking-wider">Asisten Operator 1</span>
                                <span class="font-semibold text-neutral-800 dark:text-neutral-100 block">{{ $staff['asisten_operator1'] ?? '-' }}</span>
                            </div>
                            <span class="mt-2 font-mono font-bold text-[#4C5C2D] dark:text-[#8CC7C4] text-xs">
                                {{ number_format($operasi->biayaasisten_operator1, 0, ',', '.') }}
                            </span>
                        </div>
                        @endif

                        {{-- Asisten Operator 2 --}}
                        @if (($operasi->biayaasisten_operator2 ?? 0) > 0)
                        <div class="p-3 rounded-xl bg-neutral-50 dark:bg-neutral-900/50 border border-neutral-200/80 dark:border-neutral-700 flex flex-col justify-between">
                            <div>
                                <span class="text-[11px] font-bold text-neutral-400 block mb-0.5 uppercase tracking-wider">Asisten Operator 2</span>
                                <span class="font-semibold text-neutral-800 dark:text-neutral-100 block">{{ $staff['asisten_operator2'] ?? '-' }}</span>
                            </div>
                            <span class="mt-2 font-mono font-bold text-[#4C5C2D] dark:text-[#8CC7C4] text-xs">
                                {{ number_format($operasi->biayaasisten_operator2, 0, ',', '.') }}
                            </span>
                        </div>
                        @endif

                        {{-- Asisten Operator 3 --}}
                        @if (($operasi->biayaasisten_operator3 ?? 0) > 0)
                        <div class="p-3 rounded-xl bg-neutral-50 dark:bg-neutral-900/50 border border-neutral-200/80 dark:border-neutral-700 flex flex-col justify-between">
                            <div>
                                <span class="text-[11px] font-bold text-neutral-400 block mb-0.5 uppercase tracking-wider">Asisten Operator 3</span>
                                <span class="font-semibold text-neutral-800 dark:text-neutral-100 block">{{ $staff['asisten_operator3'] ?? '-' }}</span>
                            </div>
                            <span class="mt-2 font-mono font-bold text-[#4C5C2D] dark:text-[#8CC7C4] text-xs">
                                {{ number_format($operasi->biayaasisten_operator3, 0, ',', '.') }}
                            </span>
                        </div>
                        @endif

                        {{-- Instrumen --}}
                        @if (($operasi->biayainstrumen ?? 0) > 0)
                        <div class="p-3 rounded-xl bg-neutral-50 dark:bg-neutral-900/50 border border-neutral-200/80 dark:border-neutral-700 flex flex-col justify-between">
                            <div>
                                <span class="text-[11px] font-bold text-neutral-400 block mb-0.5 uppercase tracking-wider">Instrumen</span>
                                <span class="font-semibold text-neutral-800 dark:text-neutral-100 block">{{ $staff['instrumen'] ?? '-' }}</span>
                            </div>
                            <span class="mt-2 font-mono font-bold text-[#4C5C2D] dark:text-[#8CC7C4] text-xs">
                                {{ number_format($operasi->biayainstrumen, 0, ',', '.') }}
                            </span>
                        </div>
                        @endif

                        {{-- Dokter Anak --}}
                        @if (($operasi->biayadokter_anak ?? 0) > 0)
                        <div class="p-3 rounded-xl bg-neutral-50 dark:bg-neutral-900/50 border border-neutral-200/80 dark:border-neutral-700 flex flex-col justify-between">
                            <div>
                                <span class="text-[11px] font-bold text-neutral-400 block mb-0.5 uppercase tracking-wider">Dokter Anak</span>
                                <span class="font-bold text-neutral-800 dark:text-neutral-100 block">{{ $operasi->dokterAnak->nm_dokter ?? '-' }}</span>
                            </div>
                            <span class="mt-2 font-mono font-bold text-[#4C5C2D] dark:text-[#8CC7C4] text-xs">
                                {{ number_format($operasi->biayadokter_anak, 0, ',', '.') }}
                            </span>
                        </div>
                        @endif

                        {{-- Perawat Resusitas --}}
                        @if (($operasi->biayaperawaat_resusitas ?? 0) > 0)
                        <div class="p-3 rounded-xl bg-neutral-50 dark:bg-neutral-900/50 border border-neutral-200/80 dark:border-neutral-700 flex flex-col justify-between">
                            <div>
                                <span class="text-[11px] font-bold text-neutral-400 block mb-0.5 uppercase tracking-wider">Perawat Resusitas</span>
                                <span class="font-semibold text-neutral-800 dark:text-neutral-100 block">{{ $staff['perawaat_resusitas'] ?? '-' }}</span>
                            </div>
                            <span class="mt-2 font-mono font-bold text-[#4C5C2D] dark:text-[#8CC7C4] text-xs">
                                {{ number_format($operasi->biayaperawaat_resusitas, 0, ',', '.') }}
                            </span>
                        </div>
                        @endif

                        {{-- Dokter Anestesi --}}
                        @if (($operasi->biayadokter_anestesi ?? 0) > 0)
                        <div class="p-3 rounded-xl bg-neutral-50 dark:bg-neutral-900/50 border border-neutral-200/80 dark:border-neutral-700 flex flex-col justify-between">
                            <div>
                                <span class="text-[11px] font-bold text-neutral-400 block mb-0.5 uppercase tracking-wider">Dokter Anestesi</span>
                                <span class="font-bold text-neutral-800 dark:text-neutral-100 block">{{ $operasi->dokterAnestesi->nm_dokter ?? '-' }}</span>
                            </div>
                            <span class="mt-2 font-mono font-bold text-[#4C5C2D] dark:text-[#8CC7C4] text-xs">
                                {{ number_format($operasi->biayadokter_anestesi, 0, ',', '.') }}
                            </span>
                        </div>
                        @endif

                        {{-- Asisten Anestesi 1 --}}
                        @if (($operasi->biayaasisten_anestesi ?? 0) > 0)
                        <div class="p-3 rounded-xl bg-neutral-50 dark:bg-neutral-900/50 border border-neutral-200/80 dark:border-neutral-700 flex flex-col justify-between">
                            <div>
                                <span class="text-[11px] font-bold text-neutral-400 block mb-0.5 uppercase tracking-wider">Asisten Anestesi 1</span>
                                <span class="font-semibold text-neutral-800 dark:text-neutral-100 block">{{ $staff['asisten_anestesi'] ?? '-' }}</span>
                            </div>
                            <span class="mt-2 font-mono font-bold text-[#4C5C2D] dark:text-[#8CC7C4] text-xs">
                                {{ number_format($operasi->biayaasisten_anestesi, 0, ',', '.') }}
                            </span>
                        </div>
                        @endif

                        {{-- Asisten Anestesi 2 --}}
                        @if (($operasi->biayaasisten_anestesi2 ?? 0) > 0)
                        <div class="p-3 rounded-xl bg-neutral-50 dark:bg-neutral-900/50 border border-neutral-200/80 dark:border-neutral-700 flex flex-col justify-between">
                            <div>
                                <span class="text-[11px] font-bold text-neutral-400 block mb-0.5 uppercase tracking-wider">Asisten Anestesi 2</span>
                                <span class="font-semibold text-neutral-800 dark:text-neutral-100 block">{{ $staff['asisten_anestesi2'] ?? '-' }}</span>
                            </div>
                            <span class="mt-2 font-mono font-bold text-[#4C5C2D] dark:text-[#8CC7C4] text-xs">
                                {{ number_format($operasi->biayaasisten_anestesi2, 0, ',', '.') }}
                            </span>
                        </div>
                        @endif

                        {{-- Bidan 1 --}}
                        @if (($operasi->biayabidan ?? 0) > 0)
                        <div class="p-3 rounded-xl bg-neutral-50 dark:bg-neutral-900/50 border border-neutral-200/80 dark:border-neutral-700 flex flex-col justify-between">
                            <div>
                                <span class="text-[11px] font-bold text-neutral-400 block mb-0.5 uppercase tracking-wider">Bidan 1</span>
                                <span class="font-semibold text-neutral-800 dark:text-neutral-100 block">{{ $staff['bidan'] ?? '-' }}</span>
                            </div>
                            <span class="mt-2 font-mono font-bold text-[#4C5C2D] dark:text-[#8CC7C4] text-xs">
                                {{ number_format($operasi->biayabidan, 0, ',', '.') }}
                            </span>
                        </div>
                        @endif

                        {{-- Bidan 2 --}}
                        @if (($operasi->biayabidan2 ?? 0) > 0)
                        <div class="p-3 rounded-xl bg-neutral-50 dark:bg-neutral-900/50 border border-neutral-200/80 dark:border-neutral-700 flex flex-col justify-between">
                            <div>
                                <span class="text-[11px] font-bold text-neutral-400 block mb-0.5 uppercase tracking-wider">Bidan 2</span>
                                <span class="font-semibold text-neutral-800 dark:text-neutral-100 block">{{ $staff['bidan2'] ?? '-' }}</span>
                            </div>
                            <span class="mt-2 font-mono font-bold text-[#4C5C2D] dark:text-[#8CC7C4] text-xs">
                                {{ number_format($operasi->biayabidan2, 0, ',', '.') }}
                            </span>
                        </div>
                        @endif

                        {{-- Bidan 3 --}}
                        @if (($operasi->biayabidan3 ?? 0) > 0)
                        <div class="p-3 rounded-xl bg-neutral-50 dark:bg-neutral-900/50 border border-neutral-200/80 dark:border-neutral-700 flex flex-col justify-between">
                            <div>
                                <span class="text-[11px] font-bold text-neutral-400 block mb-0.5 uppercase tracking-wider">Bidan 3</span>
                                <span class="font-semibold text-neutral-800 dark:text-neutral-100 block">{{ $staff['bidan3'] ?? '-' }}</span>
                            </div>
                            <span class="mt-2 font-mono font-bold text-[#4C5C2D] dark:text-[#8CC7C4] text-xs">
                                {{ number_format($operasi->biayabidan3, 0, ',', '.') }}
                            </span>
                        </div>
                        @endif

                        {{-- Perawat Luar --}}
                        @if (($operasi->biayaperawat_luar ?? 0) > 0)
                        <div class="p-3 rounded-xl bg-neutral-50 dark:bg-neutral-900/50 border border-neutral-200/80 dark:border-neutral-700 flex flex-col justify-between">
                            <div>
                                <span class="text-[11px] font-bold text-neutral-400 block mb-0.5 uppercase tracking-wider">Perawat Luar</span>
                                <span class="font-semibold text-neutral-800 dark:text-neutral-100 block">{{ $staff['perawat_luar'] ?? '-' }}</span>
                            </div>
                            <span class="mt-2 font-mono font-bold text-[#4C5C2D] dark:text-[#8CC7C4] text-xs">
                                {{ number_format($operasi->biayaperawat_luar, 0, ',', '.') }}
                            </span>
                        </div>
                        @endif

                        {{-- Onloop 1 --}}
                        @if (($operasi->biaya_omloop ?? 0) > 0)
                        <div class="p-3 rounded-xl bg-neutral-50 dark:bg-neutral-900/50 border border-neutral-200/80 dark:border-neutral-700 flex flex-col justify-between">
                            <div>
                                <span class="text-[11px] font-bold text-neutral-400 block mb-0.5 uppercase tracking-wider">Onloop 1</span>
                                <span class="font-semibold text-neutral-800 dark:text-neutral-100 block">{{ $staff['omloop'] ?? '-' }}</span>
                            </div>
                            <span class="mt-2 font-mono font-bold text-[#4C5C2D] dark:text-[#8CC7C4] text-xs">
                                {{ number_format($operasi->biaya_omloop, 0, ',', '.') }}
                            </span>
                        </div>
                        @endif

                        {{-- Onloop 2 --}}
                        @if (($operasi->biaya_omloop2 ?? 0) > 0)
                        <div class="p-3 rounded-xl bg-neutral-50 dark:bg-neutral-900/50 border border-neutral-200/80 dark:border-neutral-700 flex flex-col justify-between">
                            <div>
                                <span class="text-[11px] font-bold text-neutral-400 block mb-0.5 uppercase tracking-wider">Onloop 2</span>
                                <span class="font-semibold text-neutral-800 dark:text-neutral-100 block">{{ $staff['omloop2'] ?? '-' }}</span>
                            </div>
                            <span class="mt-2 font-mono font-bold text-[#4C5C2D] dark:text-[#8CC7C4] text-xs">
                                {{ number_format($operasi->biaya_omloop2, 0, ',', '.') }}
                            </span>
                        </div>
                        @endif

                        {{-- Onloop 3 --}}
                        @if (($operasi->biaya_omloop3 ?? 0) > 0)
                        <div class="p-3 rounded-xl bg-neutral-50 dark:bg-neutral-900/50 border border-neutral-200/80 dark:border-neutral-700 flex flex-col justify-between">
                            <div>
                                <span class="text-[11px] font-bold text-neutral-400 block mb-0.5 uppercase tracking-wider">Onloop 3</span>
                                <span class="font-semibold text-neutral-800 dark:text-neutral-100 block">{{ $staff['omloop3'] ?? '-' }}</span>
                            </div>
                            <span class="mt-2 font-mono font-bold text-[#4C5C2D] dark:text-[#8CC7C4] text-xs">
                                {{ number_format($operasi->biaya_omloop3, 0, ',', '.') }}
                            </span>
                        </div>
                        @endif

                        {{-- Onloop 4 --}}
                        @if (($operasi->biaya_omloop4 ?? 0) > 0)
                        <div class="p-3 rounded-xl bg-neutral-50 dark:bg-neutral-900/50 border border-neutral-200/80 dark:border-neutral-700 flex flex-col justify-between">
                            <div>
                                <span class="text-[11px] font-bold text-neutral-400 block mb-0.5 uppercase tracking-wider">Onloop 4</span>
                                <span class="font-semibold text-neutral-800 dark:text-neutral-100 block">{{ $staff['omloop4'] ?? '-' }}</span>
                            </div>
                            <span class="mt-2 font-mono font-bold text-[#4C5C2D] dark:text-[#8CC7C4] text-xs">
                                {{ number_format($operasi->biaya_omloop4, 0, ',', '.') }}
                            </span>
                        </div>
                        @endif

                        {{-- Onloop 5 --}}
                        @if (($operasi->biaya_omloop5 ?? 0) > 0)
                        <div class="p-3 rounded-xl bg-neutral-50 dark:bg-neutral-900/50 border border-neutral-200/80 dark:border-neutral-700 flex flex-col justify-between">
                            <div>
                                <span class="text-[11px] font-bold text-neutral-400 block mb-0.5 uppercase tracking-wider">Onloop 5</span>
                                <span class="font-semibold text-neutral-800 dark:text-neutral-100 block">{{ $staff['omloop5'] ?? '-' }}</span>
                            </div>
                            <span class="mt-2 font-mono font-bold text-[#4C5C2D] dark:text-[#8CC7C4] text-xs">
                                {{ number_format($operasi->biaya_omloop5, 0, ',', '.') }}
                            </span>
                        </div>
                        @endif

                        {{-- Sewa OK / VK --}}
                        @if (($operasi->biayasewaok ?? 0) > 0)
                        <div class="p-3 rounded-xl bg-neutral-50 dark:bg-neutral-900/50 border border-neutral-200/80 dark:border-neutral-700 flex flex-col justify-between">
                            <div>
                                <span class="text-[11px] font-bold text-neutral-400 block mb-0.5 uppercase tracking-wider">Sewa OK / VK</span>
                                <span class="font-semibold text-neutral-800 dark:text-neutral-100 block">Biaya Sewa Ruang</span>
                            </div>
                            <span class="mt-2 font-mono font-bold text-[#4C5C2D] dark:text-[#8CC7C4] text-xs">
                                {{ number_format($operasi->biayasewaok, 0, ',', '.') }}
                            </span>
                        </div>
                        @endif

                        {{-- Alat --}}
                        @if (($operasi->biayaalat ?? 0) > 0)
                        <div class="p-3 rounded-xl bg-neutral-50 dark:bg-neutral-900/50 border border-neutral-200/80 dark:border-neutral-700 flex flex-col justify-between">
                            <div>
                                <span class="text-[11px] font-bold text-neutral-400 block mb-0.5 uppercase tracking-wider">Alat</span>
                                <span class="font-semibold text-neutral-800 dark:text-neutral-100 block">Sewa / Pemakaian Alat</span>
                            </div>
                            <span class="mt-2 font-mono font-bold text-[#4C5C2D] dark:text-[#8CC7C4] text-xs">
                                {{ number_format($operasi->biayaalat, 0, ',', '.') }}
                            </span>
                        </div>
                        @endif

                        {{-- Akomodasi --}}
                        @if (($operasi->akomodasi ?? 0) > 0)
                        <div class="p-3 rounded-xl bg-neutral-50 dark:bg-neutral-900/50 border border-neutral-200/80 dark:border-neutral-700 flex flex-col justify-between">
                            <div>
                                <span class="text-[11px] font-bold text-neutral-400 block mb-0.5 uppercase tracking-wider">Akomodasi</span>
                                <span class="font-semibold text-neutral-800 dark:text-neutral-100 block">Akomodasi Operasi</span>
                            </div>
                            <span class="mt-2 font-mono font-bold text-[#4C5C2D] dark:text-[#8CC7C4] text-xs">
                                {{ number_format($operasi->akomodasi, 0, ',', '.') }}
                            </span>
                        </div>
                        @endif

                        {{-- N.M.S. --}}
                        @if (($operasi->bagian_rs ?? 0) > 0)
                        <div class="p-3 rounded-xl bg-neutral-50 dark:bg-neutral-900/50 border border-neutral-200/80 dark:border-neutral-700 flex flex-col justify-between">
                            <div>
                                <span class="text-[11px] font-bold text-neutral-400 block mb-0.5 uppercase tracking-wider">N.M.S.</span>
                                <span class="font-semibold text-neutral-800 dark:text-neutral-100 block">Bagian RS / Omsk</span>
                            </div>
                            <span class="mt-2 font-mono font-bold text-[#4C5C2D] dark:text-[#8CC7C4] text-xs">
                                {{ number_format($operasi->bagian_rs, 0, ',', '.') }}
                            </span>
                        </div>
                        @endif

                        {{-- Sarpras --}}
                        @if (($operasi->biayasarpras ?? 0) > 0)
                        <div class="p-3 rounded-xl bg-neutral-50 dark:bg-neutral-900/50 border border-neutral-200/80 dark:border-neutral-700 flex flex-col justify-between">
                            <div>
                                <span class="text-[11px] font-bold text-neutral-400 block mb-0.5 uppercase tracking-wider">Sarpras</span>
                                <span class="font-semibold text-neutral-800 dark:text-neutral-100 block">Sarana & Prasarana</span>
                            </div>
                            <span class="mt-2 font-mono font-bold text-[#4C5C2D] dark:text-[#8CC7C4] text-xs">
                                {{ number_format($operasi->biayasarpras, 0, ',', '.') }}
                            </span>
                        </div>
                        @endif

                        {{-- Dokter PJ Anak --}}
                        @if (($operasi->biaya_dokter_pjanak ?? 0) > 0)
                        <div class="p-3 rounded-xl bg-neutral-50 dark:bg-neutral-900/50 border border-neutral-200/80 dark:border-neutral-700 flex flex-col justify-between">
                            <div>
                                <span class="text-[11px] font-bold text-neutral-400 block mb-0.5 uppercase tracking-wider">Dokter PJ Anak</span>
                                <span class="font-bold text-neutral-800 dark:text-neutral-100 block">{{ $operasi->dokterPjanak->nm_dokter ?? '-' }}</span>
                            </div>
                            <span class="mt-2 font-mono font-bold text-[#4C5C2D] dark:text-[#8CC7C4] text-xs">
                                {{ number_format($operasi->biaya_dokter_pjanak, 0, ',', '.') }}
                            </span>
                        </div>
                        @endif

                        {{-- Dokter Umum --}}
                        @if (($operasi->biaya_dokter_umum ?? 0) > 0)
                        <div class="p-3 rounded-xl bg-neutral-50 dark:bg-neutral-900/50 border border-neutral-200/80 dark:border-neutral-700 flex flex-col justify-between">
                            <div>
                                <span class="text-[11px] font-bold text-neutral-400 block mb-0.5 uppercase tracking-wider">Dokter Umum</span>
                                <span class="font-bold text-neutral-800 dark:text-neutral-100 block">{{ $operasi->dokterUmum->nm_dokter ?? '-' }}</span>
                            </div>
                            <span class="mt-2 font-mono font-bold text-[#4C5C2D] dark:text-[#8CC7C4] text-xs">
                                {{ number_format($operasi->biaya_dokter_umum, 0, ',', '.') }}
                            </span>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Summary Total Biaya Banner --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2 border-t border-neutral-200 dark:border-neutral-700">
                    <div class="p-4 rounded-xl bg-[#4C5C2D]/10 border border-[#4C5C2D]/30 flex items-center justify-between">
                        <div>
                            <span class="text-xs font-bold text-[#4C5C2D] dark:text-[#8CC7C4] block uppercase tracking-wider">Biaya Perawatan</span>
                            <span class="text-xs text-neutral-500">Total akumulasi tindakan & tim medis</span>
                        </div>
                        <span class="text-lg font-mono font-bold text-[#4C5C2D] dark:text-[#8CC7C4]">
                            Rp {{ number_format($biayaPerawatan, 0, ',', '.') }}
                        </span>
                    </div>

                    <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800/50 flex items-center justify-between">
                        <div>
                            <span class="text-xs font-bold text-emerald-800 dark:text-emerald-400 block uppercase tracking-wider">Biaya Obat</span>
                            <span class="text-xs text-neutral-500">Total penggunaan obat & BHP operasi</span>
                        </div>
                        <span class="text-lg font-mono font-bold text-emerald-700 dark:text-emerald-400">
                            Rp {{ number_format($biayaObat, 0, ',', '.') }}
                        </span>
                    </div>
                </div>
            @else
                <div class="py-8 text-center text-neutral-400">
                    <flux:icon name="exclamation-circle" class="w-8 h-8 mx-auto mb-2 opacity-50" />
                    <p class="text-xs">Data rincian paket tindakan `operasi` tidak ditemukan untuk No Rawat ini.</p>
                </div>
            @endif
        </div>
    </div>
</div>