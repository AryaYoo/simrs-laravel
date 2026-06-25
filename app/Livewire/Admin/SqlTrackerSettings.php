<?php

namespace App\Livewire\Admin;

use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.app', ['title' => 'Pengaturan SQL Tracker Log'])]
class SqlTrackerSettings extends Component
{
    use WithFileUploads;

    public int $totalLogs = 0;
    public string $dbSize = '0 B';
    public string $pruneDays = '30';
    
    // File upload
    public $importedFile;
    public string $importStatus = '';
    public string $importMessage = '';

    public function mount()
    {
        $this->loadStats();
    }

    public function loadStats()
    {
        try {
            $this->totalLogs = DB::table('trackersql')->count();

            // Calculate size of trackersql table in MySQL
            $sizeResult = DB::select("
                SELECT (data_length + index_length) AS size 
                FROM information_schema.TABLES 
                WHERE table_schema = ? AND table_name = 'trackersql'
            ", [DB::connection()->getDatabaseName()]);
            
            $sizeInBytes = $sizeResult[0]->size ?? 0;
            $this->dbSize = $this->formatBytes($sizeInBytes);
        } catch (\Exception $e) {
            logger()->error('Failed to load trackersql stats: ' . $e->getMessage());
        }
    }

    public function exportLogs()
    {
        $filename = 'trackersql_backup_' . date('Ymd_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');
            // Write CSV headers
            fputcsv($handle, ['tanggal', 'sqle', 'usere']);

            // Chunk the queries to save server memory
            DB::table('trackersql')->orderBy('tanggal', 'desc')->chunk(2000, function ($logs) use ($handle) {
                foreach ($logs as $log) {
                    fputcsv($handle, [
                        $log->tanggal,
                        $log->sqle,
                        $log->usere,
                    ]);
                }
            });

            fclose($handle);
        }, $filename, $headers);
    }

    public function exportSql()
    {
        $filename = 'trackersql_backup_' . date('Ymd_His') . '.sql';
        $headers = [
            'Content-Type' => 'application/sql',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        return response()->streamDownload(function () {
            // Write SQL file header
            echo "-- SQL Tracker Log Backup\n";
            echo "-- Generated at: " . date('Y-m-d H:i:s') . "\n";
            echo "-- Table: trackersql\n\n";

            // Chunk the queries to save server memory
            DB::table('trackersql')->orderBy('tanggal', 'asc')->chunk(2000, function ($logs) {
                foreach ($logs as $log) {
                    $tanggal = addslashes($log->tanggal ?? '');
                    $sqle    = addslashes($log->sqle ?? '');
                    $usere   = addslashes($log->usere ?? '');
                    echo "INSERT INTO `trackersql` (`tanggal`, `sqle`, `usere`) VALUES ('{$tanggal}', '{$sqle}', '{$usere}');\n";
                }
            });
        }, $filename, $headers);
    }

    public function pruneLogs()
    {
        try {
            if ($this->pruneDays === 'all') {
                DB::table('trackersql')->truncate();
                $this->dispatch('swal', [
                    'icon' => 'success',
                    'title' => 'Sukses!',
                    'text' => 'Semua data tracker log berhasil dibersihkan.',
                ]);
            } else {
                $days = intval($this->pruneDays);
                $cutoffDate = now()->subDays($days);
                
                $deleted = DB::table('trackersql')
                    ->where('tanggal', '<', $cutoffDate)
                    ->delete();

                $this->dispatch('swal', [
                    'icon' => 'success',
                    'title' => 'Sukses!',
                    'text' => "Log yang lebih lama dari {$days} hari ({$deleted} baris) berhasil dihapus.",
                ]);
            }

            $this->loadStats();
        } catch (\Exception $e) {
            $this->dispatch('swal', [
                'icon' => 'error',
                'title' => 'Gagal!',
                'text' => 'Terjadi kesalahan saat menghapus log: ' . $e->getMessage(),
            ]);
        }
    }

    public function injectLogs()
    {
        $this->validate([
            'importedFile' => 'required|file|mimes:csv,txt|max:51200', // max 50MB
        ]);

        $this->importStatus = 'loading';
        $this->importMessage = 'Memproses file...';

        try {
            $path = $this->importedFile->getRealPath();
            $file = fopen($path, 'r');
            
            // Read header
            $header = fgetcsv($file);
            
            if (!$header || !in_array('tanggal', $header) || !in_array('sqle', $header)) {
                fclose($file);
                $this->importStatus = 'error';
                $this->importMessage = 'Format file tidak valid. CSV harus memiliki kolom "tanggal" dan "sqle".';
                return;
            }

            // Map header indexes
            $indexTanggal = array_search('tanggal', $header);
            $indexSqle = array_search('sqle', $header);
            $indexUsere = array_search('usere', $header);

            $insertData = [];
            $count = 0;
            
            DB::beginTransaction();

            while (($row = fgetcsv($file)) !== false) {
                if (count($row) <= max($indexTanggal, $indexSqle)) {
                    continue; // Skip invalid row
                }

                $insertData[] = [
                    'tanggal' => $row[$indexTanggal] ?: now()->toDateTimeString(),
                    'sqle' => $row[$indexSqle] ?: '',
                    'usere' => $indexUsere !== false ? ($row[$indexUsere] ?: 'System') : 'System',
                ];

                $count++;

                // Chunk inserts to prevent memory limits
                if (count($insertData) >= 1000) {
                    DB::table('trackersql')->insert($insertData);
                    $insertData = [];
                }
            }

            // Insert remaining rows
            if (!empty($insertData)) {
                DB::table('trackersql')->insert($insertData);
            }

            DB::commit();
            fclose($file);

            $this->importedFile = null;
            $this->importStatus = 'success';
            $this->importMessage = "Berhasil mengimpor {$count} baris log tracker.";
            
            $this->dispatch('swal', [
                'icon' => 'success',
                'title' => 'Sukses Impor!',
                'text' => "Berhasil mengimpor {$count} data log tracker.",
            ]);

            $this->loadStats();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->importStatus = 'error';
            $this->importMessage = 'Gagal mengimpor file: ' . $e->getMessage();
            
            $this->dispatch('swal', [
                'icon' => 'error',
                'title' => 'Gagal Impor!',
                'text' => 'Gagal mengimpor file: ' . $e->getMessage(),
            ]);
        }
    }

    private function formatBytes($bytes, $precision = 2): string
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');
        
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= pow(1024, $pow);
        
        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    public function render()
    {
        return view('livewire.admin.sql-tracker-settings');
    }
}
