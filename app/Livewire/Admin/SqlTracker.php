<?php

namespace App\Livewire\Admin;

use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app', ['title' => 'SQL Query Tracker Log'])]
class SqlTracker extends Component
{
    use WithPagination;

    public string $search = '';
    public string $action = '';
    public string $user = '';
    public string $date = '';
    
    public ?array $selectedLog = null;
    public bool $detailModalOpen = false;

    protected $queryString = [
        'search' => ['except' => ''],
        'action' => ['except' => ''],
        'user' => ['except' => ''],
        'date' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingAction()
    {
        $this->resetPage();
    }

    public function updatingUser()
    {
        $this->resetPage();
    }

    public function updatingDate()
    {
        $this->resetPage();
    }

    public function showDetail(string $tanggal, string $sqlan, string $usere)
    {
        $parsed = $this->parseSqlLog($sqlan);
        $this->selectedLog = [
            'tanggal' => $tanggal,
            'ip' => $parsed['ip'],
            'raw_sql' => $parsed['raw_sql'],
            'action' => $parsed['action'],
            'table' => $parsed['table'],
            'user' => $usere,
        ];
        $this->detailModalOpen = true;
    }

    public function closeDetail()
    {
        $this->detailModalOpen = false;
        $this->selectedLog = null;
    }

    private function parseSqlLog(string $sqlan): array
    {
        $parts = explode(' ', $sqlan, 2);
        $ip = $parts[0] ?? '-';
        $raw_sql = $parts[1] ?? '';

        $action = 'OTHER';
        $lowerSql = strtolower($raw_sql);
        if (str_starts_with($lowerSql, 'insert')) {
            $action = 'INSERT';
        } elseif (str_starts_with($lowerSql, 'update')) {
            $action = 'UPDATE';
        } elseif (str_starts_with($lowerSql, 'delete')) {
            $action = 'DELETE';
        }

        // Try to extract table name
        $table = '-';
        if ($action === 'INSERT') {
            if (preg_match('/insert\s+into\s+([a-zA-Z0-9_`]+)/i', $raw_sql, $matches)) {
                $table = str_replace('`', '', $matches[1]);
            }
        } elseif ($action === 'UPDATE') {
            if (preg_match('/update\s+([a-zA-Z0-9_`]+)/i', $raw_sql, $matches)) {
                $table = str_replace('`', '', $matches[1]);
            }
        } elseif ($action === 'DELETE') {
            if (preg_match('/delete\s+from\s+([a-zA-Z0-9_`]+)/i', $raw_sql, $matches)) {
                $table = str_replace('`', '', $matches[1]);
            }
        }

        return [
            'ip' => $ip,
            'raw_sql' => $raw_sql,
            'action' => $action,
            'table' => $table,
        ];
    }

    public function render()
    {
        $query = DB::table('trackersql');

        if ($this->date) {
            $query->whereDate('tanggal', $this->date);
        }

        if ($this->user) {
            $query->where('usere', $this->user);
        }

        if ($this->action) {
            if ($this->action === 'insert') {
                $query->where('sqlan', 'like', '% insert into%');
            } elseif ($this->action === 'update') {
                $query->where('sqlan', 'like', '% update %');
            } elseif ($this->action === 'delete') {
                $query->where('sqlan', 'like', '% delete %');
            }
        }

        if ($this->search) {
            $query->where('sqlan', 'like', '%' . $this->search . '%');
        }

        $logs = $query->orderBy('tanggal', 'desc')->paginate(20);

        // Fetch list of active users who generated logs for filter dropdown
        $usersList = DB::table('trackersql')
            ->select('usere')
            ->distinct()
            ->whereNotNull('usere')
            ->where('usere', '!=', '')
            ->orderBy('usere', 'asc')
            ->pluck('usere');

        // Map and parse log entries
        $mappedLogs = collect($logs->items())->map(function ($item) {
            $parsed = $this->parseSqlLog($item->sqlan);
            return [
                'tanggal' => $item->tanggal,
                'ip' => $parsed['ip'],
                'raw_sql' => $parsed['raw_sql'],
                'action' => $parsed['action'],
                'table' => $parsed['table'],
                'usere' => $item->usere,
            ];
        });

        return view('livewire.admin.sql-tracker', [
            'logs' => $logs,
            'mappedLogs' => $mappedLogs,
            'usersList' => $usersList,
        ]);
    }
}
