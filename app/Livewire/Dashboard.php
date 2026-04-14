<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\RegPeriksa;
use App\Models\KamarInap;
use App\Models\Kamar;
use App\Models\Penjab;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

#[Layout('layouts.app', ['title' => 'Dashboard'])]
class Dashboard extends Component
{
    public $activeTab = 'overview';

    public function render()
    {
        $today = Carbon::today()->format('Y-m-d');
        
        // 1. KPI Cards Data
        $stats = [
            'inhouse' => KamarInap::where('stts_pulang', '-')->count(),
            'today_reg' => RegPeriksa::where('tgl_registrasi', $today)->count(),
            'today_bpjs' => RegPeriksa::where('tgl_registrasi', $today)
                ->whereHas('penjab', function($q) {
                    $q->where('png_jawab', 'like', '%BPJS%');
                })->count(),
            'beds' => [
                'total' => Kamar::where('statusdata', '1')->count(),
                'filled' => Kamar::where('status', 'ISI')->count(),
            ]
        ];

        // 2. Trend Data (Last 7 Days)
        $trendData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i)->format('Y-m-d');
            $count = RegPeriksa::where('tgl_registrasi', $date)->count();
            $trendData[] = [
                'date' => Carbon::parse($date)->format('d M'),
                'count' => $count
            ];
        }

        // 3. Insurance Distribution
        $insuranceDist = RegPeriksa::where('tgl_registrasi', '>=', Carbon::today()->subDays(30))
            ->with('penjab')
            ->get()
            ->groupBy('kd_pj')
            ->map(fn($group) => [
                'label' => $group->first()->penjab->png_jawab ?? 'Unknown',
                'count' => $group->count()
            ])
            ->sortByDesc('count')
            ->take(5);

        // 4. Recent Registrations
        $recentRegistrations = RegPeriksa::with(['pasien', 'penjab', 'dokter'])
            ->orderBy('tgl_registrasi', 'desc')
            ->orderBy('jam_reg', 'desc')
            ->limit(10)
            ->get();

        return view('livewire.dashboard', [
            'stats' => $stats,
            'trendData' => $trendData,
            'insuranceDist' => $insuranceDist,
            'recent' => $recentRegistrations
        ]);
    }
}
