<div class="flex flex-col gap-6 pb-8"
    x-data="{
        showDetail: false,
        selectedLog: null,
        openDetail(log) {
            this.selectedLog = log;
            this.showDetail = true;
        }
    }">
    {{-- Header / Breadcrumb --}}
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('dashboard') }}" wire:navigate class="flex items-center justify-center w-10 h-8 rounded-md bg-[#4C5C2D] transition-colors hover:bg-[#3d4b24] shadow-sm">
                <flux:icon name="chevron-left" class="w-5 h-5 text-white" />
            </a>
            <div>
                <nav class="text-xs text-neutral-400 mb-0.5">
                    <a href="{{ route('dashboard') }}" wire:navigate class="hover:underline">Dashboard</a>
                    <span class="mx-1">/</span>
                    <span>SQL Tracker</span>
                </nav>
                <h1 class="text-xl font-bold text-neutral-800 dark:text-neutral-100">SQL Query Tracker Log</h1>
            </div>
        </div>
        <div>
            <flux:button href="{{ route('admin.sql-tracker.settings') }}" wire:navigate icon="cog-6-tooth" class="!bg-[#4C5C2D] hover:!bg-[#3d4b24] text-white">
                Pengaturan Log
            </flux:button>
        </div>
    </div>

    {{-- Filters Card --}}
    <div class="bg-white dark:bg-neutral-800 rounded-xl border border-neutral-200 dark:border-neutral-700 p-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
            {{-- Search SQL --}}
            <div>
                <flux:input wire:model.live.debounce.300ms="search" label="Cari SQL / Query" placeholder="Masukkan kata kunci query..." />
            </div>

            {{-- Filter User --}}
            <div>
                <flux:input wire:model.live.debounce.300ms="user" label="Filter User / Pegawai" placeholder="Ketik username..." />
            </div>

            {{-- Filter Action --}}
            <div>
                <flux:select wire:model.live="action" label="Filter Tipe Aksi">
                    <option value="">Semua Aksi</option>
                    <option value="insert">INSERT</option>
                    <option value="update">UPDATE</option>
                    <option value="delete">DELETE</option>
                </flux:select>
            </div>

            {{-- Filter Date --}}
            <div>
                <flux:input wire:model.live="date" type="date" label="Filter Tanggal" />
            </div>
        </div>
    </div>

    {{-- Table Card --}}
    <div class="bg-white dark:bg-neutral-800 rounded-xl border border-neutral-200 dark:border-neutral-700 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-neutral-50 dark:bg-neutral-900/50 text-xs font-semibold uppercase text-neutral-500 dark:text-neutral-400 border-b border-neutral-200 dark:border-neutral-700">
                    <tr>
                        <th class="p-4 w-44">Waktu</th>
                        <th class="p-4 w-36">User</th>
                        <th class="p-4 w-32">IP Address</th>
                        <th class="p-4 w-28">Tipe Aksi</th>
                        <th class="p-4 w-44">Nama Tabel</th>
                        <th class="p-4">Potongan Query SQL</th>
                        <th class="p-4 w-24 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700 text-neutral-700 dark:text-neutral-200">
                    @forelse($mappedLogs as $log)
                        @php
                            $logJson = json_encode([
                                'tanggal' => $log['tanggal'],
                                'usere'   => $log['usere'],
                                'ip'      => $log['ip'],
                                'action'  => $log['action'],
                                'table'   => $log['table'],
                                'raw_sql' => $log['raw_sql'],
                            ]);
                        @endphp
                        <tr class="hover:bg-neutral-50/50 dark:hover:bg-neutral-900/50 transition-colors">
                            <td class="p-4 whitespace-nowrap text-neutral-500 dark:text-neutral-400">
                                {{ \Carbon\Carbon::parse($log['tanggal'])->format('d-m-Y H:i:s') }}
                            </td>
                            <td class="p-4 font-medium whitespace-nowrap">
                                {{ $log['usere'] }}
                            </td>
                            <td class="p-4 text-xs font-mono text-neutral-500 dark:text-neutral-400">
                                {{ $log['ip'] }}
                            </td>
                            <td class="p-4 whitespace-nowrap">
                                @if($log['action'] === 'INSERT')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-50 text-green-700 border border-green-200 dark:bg-green-950/30 dark:text-green-400 dark:border-green-900/50">
                                        INSERT
                                    </span>
                                @elseif($log['action'] === 'UPDATE')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-50 text-amber-700 border border-amber-200 dark:bg-amber-950/30 dark:text-amber-400 dark:border-amber-900/50">
                                        UPDATE
                                    </span>
                                @elseif($log['action'] === 'DELETE')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-rose-50 text-rose-700 border border-rose-200 dark:bg-rose-950/30 dark:text-rose-400 dark:border-rose-900/50">
                                        DELETE
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-neutral-100 text-neutral-700 border border-neutral-200 dark:bg-neutral-800 dark:text-neutral-400 dark:border-neutral-700">
                                        {{ $log['action'] }}
                                    </span>
                                @endif
                            </td>
                            <td class="p-4 font-semibold text-xs font-mono text-[#4C5C2D] dark:text-[#8CC7C4] whitespace-nowrap">
                                {{ $log['table'] }}
                            </td>
                            <td class="p-4 font-mono text-xs max-w-md truncate text-neutral-500 dark:text-neutral-400" title="{{ $log['raw_sql'] }}">
                                {{ $log['raw_sql'] }}
                            </td>
                            <td class="p-4 text-center">
                                <button
                                    type="button"
                                    @click="openDetail({{ $logJson }})"
                                    class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-lg border border-neutral-200 dark:border-neutral-600 text-neutral-600 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-700 transition-colors"
                                >
                                    Detail
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-neutral-400 dark:text-neutral-500">
                                Tidak ada data log SQL yang terekam.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
            <div class="p-4 border-t border-neutral-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-900/50">
                {{ $logs->links() }}
            </div>
        @endif
    </div>

    {{-- Detail SQL Modal (Pure Alpine.js - SOP #6, no Livewire round-trip) --}}
    <div x-show="showDetail" x-cloak class="fixed inset-0 z-[99] flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-neutral-900/60 backdrop-blur-sm" @click="showDetail = false"></div>

        <div class="relative bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-200 dark:border-neutral-800 shadow-2xl max-w-3xl w-full p-6 overflow-hidden flex flex-col max-h-[85vh]" @click.stop>
            <div class="flex justify-between items-start border-b border-neutral-100 dark:border-neutral-800 pb-3 mb-4">
                <div>
                    <h3 class="text-lg font-bold text-neutral-800 dark:text-neutral-100">Detail Riwayat Query SQL</h3>
                    <p class="text-xs text-neutral-500">Log audit rekayasa database</p>
                </div>
                <button @click="showDetail = false" class="text-neutral-400 hover:text-neutral-600 dark:hover:text-neutral-200 transition-colors">
                    <flux:icon name="x-mark" class="w-6 h-6" />
                </button>
            </div>

            <template x-if="selectedLog">
                <div class="flex-1 overflow-y-auto space-y-4 pr-1 text-sm">
                    <div class="grid grid-cols-2 gap-4 bg-neutral-50 dark:bg-neutral-950 p-4 rounded-xl border border-neutral-100 dark:border-neutral-800/80">
                        <div>
                            <span class="text-xs text-neutral-400 block">Waktu Transaksi</span>
                            <span class="font-medium text-neutral-700 dark:text-neutral-300" x-text="selectedLog.tanggal"></span>
                        </div>
                        <div>
                            <span class="text-xs text-neutral-400 block">User / NIK Pegawai</span>
                            <span class="font-medium text-neutral-700 dark:text-neutral-300" x-text="selectedLog.usere"></span>
                        </div>
                        <div>
                            <span class="text-xs text-neutral-400 block">IP Client</span>
                            <span class="font-medium text-neutral-700 dark:text-neutral-300 font-mono text-xs" x-text="selectedLog.ip"></span>
                        </div>
                        <div>
                            <span class="text-xs text-neutral-400 block">Aksi / Target Tabel</span>
                            <div class="mt-1 flex items-center gap-2">
                                <span
                                    class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold border"
                                    :class="{
                                        'bg-green-50 text-green-700 border-green-200 dark:bg-green-950/20 dark:text-green-400 dark:border-green-900/30': selectedLog.action === 'INSERT',
                                        'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-950/20 dark:text-amber-400 dark:border-amber-900/30': selectedLog.action === 'UPDATE',
                                        'bg-rose-50 text-rose-700 border-rose-200 dark:bg-rose-950/20 dark:text-rose-400 dark:border-rose-900/30': selectedLog.action === 'DELETE',
                                        'bg-neutral-100 text-neutral-700 border-neutral-200': !['INSERT','UPDATE','DELETE'].includes(selectedLog.action)
                                    }"
                                    x-text="selectedLog.action"
                                ></span>
                                <span class="font-mono text-xs font-bold text-[#4C5C2D] dark:text-[#8CC7C4]" x-text="selectedLog.table"></span>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <span class="text-xs font-semibold text-neutral-500 block">Query SQL Lengkap:</span>
                        <div class="relative bg-neutral-950 text-neutral-200 font-mono text-xs p-4 rounded-xl border border-neutral-800 overflow-x-auto leading-relaxed whitespace-pre-wrap select-all" x-text="selectedLog.raw_sql"></div>
                    </div>
                </div>
            </template>

            <div class="border-t border-neutral-100 dark:border-neutral-800 pt-3 mt-4 flex justify-end">
                <flux:button variant="filled" class="!bg-[#4C5C2D] hover:!bg-[#3d4b24]" @click="showDetail = false">
                    Tutup
                </flux:button>
            </div>
        </div>
    </div>
</div>
