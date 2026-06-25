<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

#[Layout('layouts.app', ['title' => 'Dashboard'])]
class Dashboard extends Component
{
    public function render()
    {
        $today = Carbon::today()->format('Y-m-d');
        $startOfMonth = Carbon::now()->startOfMonth()->format('Y-m-d');
        $endOfMonth   = Carbon::now()->endOfMonth()->format('Y-m-d');

        // ─── 1. KPI Cards ────────────────────────────────────────────────────
        $stats = [
            'inhouse'   => DB::table('kamar_inap')->where('stts_pulang', '-')->count(),
            'today_reg' => DB::table('reg_periksa')->where('tgl_registrasi', $today)->count(),
            'today_bpjs' => DB::table('reg_periksa')
                ->join('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
                ->where('reg_periksa.tgl_registrasi', $today)
                ->where('penjab.png_jawab', 'like', '%BPJS%')
                ->count(),
            'beds' => [
                'total'  => DB::table('kamar')->where('statusdata', '1')->count(),
                'filled' => DB::table('kamar')->where('status', 'ISI')->count(),
            ],
        ];

        // ─── 2. Tren Kunjungan Bulanan (6 bulan terakhir) ────────────────────
        $trendMonths = [];
        for ($i = 5; $i >= 0; $i--) {
            $m = Carbon::now()->startOfMonth()->subMonths($i);
            $trendMonths[] = [
                'label'  => $m->translatedFormat('M Y'),
                'year'   => $m->year,
                'month'  => $m->month,
                'start'  => $m->format('Y-m-d'),
                'end'    => $m->copy()->endOfMonth()->format('Y-m-d'),
            ];
        }

        // Aggregate per month with payer category
        $trendRaw = DB::table('reg_periksa')
            ->join('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
            ->whereBetween('reg_periksa.tgl_registrasi', [
                $trendMonths[0]['start'],
                $trendMonths[count($trendMonths) - 1]['end'],
            ])
            ->selectRaw("
                YEAR(reg_periksa.tgl_registrasi) as yr,
                MONTH(reg_periksa.tgl_registrasi) as mo,
                SUM(CASE WHEN penjab.png_jawab LIKE '%BPJS%' THEN 1 ELSE 0 END) as bpjs,
                SUM(CASE WHEN penjab.png_jawab LIKE '%Umum%' AND penjab.png_jawab NOT LIKE '%BPJS%' THEN 1 ELSE 0 END) as umum,
                SUM(CASE WHEN penjab.png_jawab NOT LIKE '%BPJS%' AND penjab.png_jawab NOT LIKE '%Umum%' THEN 1 ELSE 0 END) as lainnya,
                COUNT(*) as total
            ")
            ->groupByRaw('YEAR(reg_periksa.tgl_registrasi), MONTH(reg_periksa.tgl_registrasi)')
            ->get()
            ->keyBy(fn($row) => $row->yr . '-' . $row->mo);

        $trendData = [];
        foreach ($trendMonths as $m) {
            $key = $m['year'] . '-' . $m['month'];
            $row = $trendRaw->get($key);
            $trendData[] = [
                'label'   => $m['label'],
                'bpjs'    => $row ? (int)$row->bpjs    : 0,
                'umum'    => $row ? (int)$row->umum    : 0,
                'lainnya' => $row ? (int)$row->lainnya : 0,
                'total'   => $row ? (int)$row->total   : 0,
            ];
        }

        // ─── 3. Kelengkapan Rawat Jalan (bulan ini) ──────────────────────────
        $rjStats = DB::table('reg_periksa as rp')
            ->whereBetween('rp.tgl_registrasi', [$startOfMonth, $endOfMonth])
            ->where('rp.status_lanjut', 'Ralan')
            ->leftJoin(DB::raw('(SELECT DISTINCT no_rawat FROM rawat_jl_dr) jl_dr'), 'rp.no_rawat', '=', 'jl_dr.no_rawat')
            ->leftJoin(DB::raw('(SELECT DISTINCT no_rawat FROM rawat_jl_pr) jl_pr'), 'rp.no_rawat', '=', 'jl_pr.no_rawat')
            ->leftJoin(DB::raw('(SELECT DISTINCT no_rawat FROM rawat_jl_drpr) jl_drpr'), 'rp.no_rawat', '=', 'jl_drpr.no_rawat')
            ->leftJoin(DB::raw('(SELECT DISTINCT no_rawat FROM diagnosa_pasien) dp'), 'rp.no_rawat', '=', 'dp.no_rawat')
            ->leftJoin(DB::raw('(SELECT DISTINCT no_rawat FROM resume_pasien) rsp'), 'rp.no_rawat', '=', 'rsp.no_rawat')
            ->leftJoin(DB::raw('(SELECT DISTINCT no_rawat FROM resep_obat) ro'), 'rp.no_rawat', '=', 'ro.no_rawat')
            ->leftJoin(DB::raw('(SELECT DISTINCT no_rawat FROM permintaan_lab) plab'), 'rp.no_rawat', '=', 'plab.no_rawat')
            ->leftJoin(DB::raw('(SELECT DISTINCT no_rawat FROM permintaan_radiologi) prad'), 'rp.no_rawat', '=', 'prad.no_rawat')
            ->selectRaw("
                COUNT(rp.no_rawat)          as total,
                SUM(jl_dr.no_rawat IS NOT NULL)    as ada_dokter,
                SUM(jl_pr.no_rawat IS NOT NULL)    as ada_petugas,
                SUM(jl_drpr.no_rawat IS NOT NULL)  as ada_drpr,
                SUM(dp.no_rawat IS NOT NULL)       as ada_diagnosa,
                SUM(rsp.no_rawat IS NOT NULL)      as ada_resume,
                SUM(ro.no_rawat IS NOT NULL)       as ada_obat,
                SUM(plab.no_rawat IS NOT NULL)     as ada_lab,
                SUM(prad.no_rawat IS NOT NULL)     as ada_radiologi
            ")
            ->first();

        $rjTotal = max($rjStats->total ?? 1, 1);
        $ralanKelengkapan = [
            'total'     => $rjStats->total ?? 0,
            'items'     => [
                ['label' => 'Penanganan Dokter',         'value' => (int)($rjStats->ada_dokter   ?? 0), 'pct' => round(($rjStats->ada_dokter   ?? 0) / $rjTotal * 100)],
                ['label' => 'Penanganan Petugas',        'value' => (int)($rjStats->ada_petugas  ?? 0), 'pct' => round(($rjStats->ada_petugas  ?? 0) / $rjTotal * 100)],
                ['label' => 'Dokter + Petugas',          'value' => (int)($rjStats->ada_drpr     ?? 0), 'pct' => round(($rjStats->ada_drpr     ?? 0) / $rjTotal * 100)],
                ['label' => 'Diagnosa',                  'value' => (int)($rjStats->ada_diagnosa ?? 0), 'pct' => round(($rjStats->ada_diagnosa ?? 0) / $rjTotal * 100)],
                ['label' => 'Catatan Dokter (Resume)',   'value' => (int)($rjStats->ada_resume   ?? 0), 'pct' => round(($rjStats->ada_resume   ?? 0) / $rjTotal * 100)],
                ['label' => 'Permintaan Obat',           'value' => (int)($rjStats->ada_obat     ?? 0), 'pct' => round(($rjStats->ada_obat     ?? 0) / $rjTotal * 100)],
                ['label' => 'Permintaan Lab',            'value' => (int)($rjStats->ada_lab      ?? 0), 'pct' => round(($rjStats->ada_lab      ?? 0) / $rjTotal * 100)],
                ['label' => 'Permintaan Radiologi',      'value' => (int)($rjStats->ada_radiologi ?? 0), 'pct' => round(($rjStats->ada_radiologi ?? 0) / $rjTotal * 100)],
            ],
        ];

        // ─── 4. Kelengkapan Rawat Inap (bulan ini) ───────────────────────────
        $riStats = DB::table('reg_periksa as rp')
            ->whereBetween('rp.tgl_registrasi', [$startOfMonth, $endOfMonth])
            ->where('rp.status_lanjut', 'Ranap')
            ->leftJoin(DB::raw('(SELECT DISTINCT no_rawat FROM penilaian_awal_keperawatan_ranap) pakr'), 'rp.no_rawat', '=', 'pakr.no_rawat')
            ->leftJoin(DB::raw('(SELECT DISTINCT no_rawat FROM rawat_inap_dr) ri_dr'), 'rp.no_rawat', '=', 'ri_dr.no_rawat')
            ->leftJoin(DB::raw('(SELECT DISTINCT no_rawat FROM rawat_inap_pr) ri_pr'), 'rp.no_rawat', '=', 'ri_pr.no_rawat')
            ->leftJoin(DB::raw('(SELECT DISTINCT no_rawat FROM rawat_inap_drpr) ri_drpr'), 'rp.no_rawat', '=', 'ri_drpr.no_rawat')
            ->leftJoin(DB::raw('(SELECT DISTINCT no_rawat FROM diagnosa_pasien) dp2'), 'rp.no_rawat', '=', 'dp2.no_rawat')
            ->leftJoin(DB::raw('(SELECT DISTINCT no_rawat FROM resume_pasien_ranap) rspri'), 'rp.no_rawat', '=', 'rspri.no_rawat')
            ->leftJoin(DB::raw('(SELECT DISTINCT no_rawat FROM resep_obat) ro2'), 'rp.no_rawat', '=', 'ro2.no_rawat')
            ->leftJoin(DB::raw('(SELECT DISTINCT no_rawat FROM permintaan_lab) plab2'), 'rp.no_rawat', '=', 'plab2.no_rawat')
            ->leftJoin(DB::raw('(SELECT DISTINCT no_rawat FROM permintaan_radiologi) prad2'), 'rp.no_rawat', '=', 'prad2.no_rawat')
            ->selectRaw("
                COUNT(rp.no_rawat)              as total,
                SUM(pakr.no_rawat IS NOT NULL)  as ada_pengkajian,
                SUM(ri_dr.no_rawat IS NOT NULL)  as ada_dokter,
                SUM(ri_pr.no_rawat IS NOT NULL)  as ada_petugas,
                SUM(ri_drpr.no_rawat IS NOT NULL) as ada_drpr,
                SUM(dp2.no_rawat IS NOT NULL)    as ada_diagnosa,
                SUM(rspri.no_rawat IS NOT NULL)  as ada_resume,
                SUM(ro2.no_rawat IS NOT NULL)    as ada_obat,
                SUM(plab2.no_rawat IS NOT NULL)  as ada_lab,
                SUM(prad2.no_rawat IS NOT NULL)  as ada_radiologi
            ")
            ->first();

        $riTotal = max($riStats->total ?? 1, 1);
        $ranapKelengkapan = [
            'total' => $riStats->total ?? 0,
            'items' => [
                ['label' => 'Pengkajian Awal Keperawatan', 'value' => (int)($riStats->ada_pengkajian ?? 0), 'pct' => round(($riStats->ada_pengkajian ?? 0) / $riTotal * 100)],
                ['label' => 'Penanganan Dokter',           'value' => (int)($riStats->ada_dokter     ?? 0), 'pct' => round(($riStats->ada_dokter     ?? 0) / $riTotal * 100)],
                ['label' => 'Penanganan Petugas',          'value' => (int)($riStats->ada_petugas    ?? 0), 'pct' => round(($riStats->ada_petugas    ?? 0) / $riTotal * 100)],
                ['label' => 'Dokter + Petugas',            'value' => (int)($riStats->ada_drpr       ?? 0), 'pct' => round(($riStats->ada_drpr       ?? 0) / $riTotal * 100)],
                ['label' => 'Diagnosa',                    'value' => (int)($riStats->ada_diagnosa   ?? 0), 'pct' => round(($riStats->ada_diagnosa   ?? 0) / $riTotal * 100)],
                ['label' => 'Catatan Dokter (Resume)',     'value' => (int)($riStats->ada_resume     ?? 0), 'pct' => round(($riStats->ada_resume     ?? 0) / $riTotal * 100)],
                ['label' => 'Permintaan Obat',             'value' => (int)($riStats->ada_obat       ?? 0), 'pct' => round(($riStats->ada_obat       ?? 0) / $riTotal * 100)],
                ['label' => 'Permintaan Lab',              'value' => (int)($riStats->ada_lab        ?? 0), 'pct' => round(($riStats->ada_lab        ?? 0) / $riTotal * 100)],
                ['label' => 'Permintaan Radiologi',        'value' => (int)($riStats->ada_radiologi  ?? 0), 'pct' => round(($riStats->ada_radiologi  ?? 0) / $riTotal * 100)],
            ],
        ];

        // ─── 5. Kelengkapan Farmasi (bulan ini) ──────────────────────────────
        $farmasiPermintaan = DB::table('resep_obat')
            ->whereBetween('tgl_perawatan', [$startOfMonth, $endOfMonth])
            ->count();
        $farmasiPemberian = DB::table('detail_pemberian_obat')
            ->whereBetween('tgl_perawatan', [$startOfMonth, $endOfMonth])
            ->count();
        $farmasiValidasi = DB::table('resep_obat')
            ->whereBetween('tgl_perawatan', [$startOfMonth, $endOfMonth])
            ->where('status', 'Sudah')
            ->count();

        $farmasiTotal = max($farmasiPermintaan, 1);
        $farmasiKelengkapan = [
            'total' => $farmasiPermintaan,
            'items' => [
                ['label' => 'Permintaan Obat', 'value' => $farmasiPermintaan, 'pct' => 100],
                ['label' => 'Pemberian Obat',  'value' => $farmasiPemberian,  'pct' => round($farmasiPemberian  / $farmasiTotal * 100)],
                ['label' => 'Validasi/Selesai','value' => $farmasiValidasi,   'pct' => round($farmasiValidasi   / $farmasiTotal * 100)],
            ],
        ];

        // ─── 6. Kelengkapan Lab (bulan ini) ──────────────────────────────────
        $labPermintaan = DB::table('permintaan_lab')
            ->whereBetween('tgl_permintaan', [$startOfMonth, $endOfMonth])
            ->count();
        $labHasil = DB::table('permintaan_lab')
            ->whereBetween('tgl_permintaan', [$startOfMonth, $endOfMonth])
            ->whereNotNull('tgl_hasil')
            ->where('tgl_hasil', '!=', '')
            ->count();

        $labTotal = max($labPermintaan, 1);
        $labKelengkapan = [
            'total' => $labPermintaan,
            'items' => [
                ['label' => 'Permintaan Lab', 'value' => $labPermintaan, 'pct' => 100],
                ['label' => 'Hasil Lab Tersedia', 'value' => $labHasil, 'pct' => round($labHasil / $labTotal * 100)],
            ],
        ];

        // ─── 7. Kelengkapan Radiologi (bulan ini) ────────────────────────────
        $radPermintaan = DB::table('permintaan_radiologi')
            ->whereBetween('tgl_permintaan', [$startOfMonth, $endOfMonth])
            ->count();
        $radHasil = DB::table('permintaan_radiologi')
            ->whereBetween('tgl_permintaan', [$startOfMonth, $endOfMonth])
            ->where(function ($q) {
                $q->whereNotNull('tgl_hasil')->where('tgl_hasil', '!=', '');
            })
            ->count();
        $radSelesai = DB::table('permintaan_radiologi')
            ->whereBetween('tgl_permintaan', [$startOfMonth, $endOfMonth])
            ->where('status', 'Sudah')
            ->count();

        $radTotal = max($radPermintaan, 1);
        $radiologiKelengkapan = [
            'total' => $radPermintaan,
            'items' => [
                ['label' => 'Permintaan Radiologi', 'value' => $radPermintaan, 'pct' => 100],
                ['label' => 'Hasil Tersedia',        'value' => $radHasil,     'pct' => round($radHasil    / $radTotal * 100)],
                ['label' => 'Selesai / Sudah',       'value' => $radSelesai,   'pct' => round($radSelesai  / $radTotal * 100)],
            ],
        ];

        // ─── 8. Recent Registrations ─────────────────────────────────────────
        $recentRegistrations = DB::table('reg_periksa as rp')
            ->join('pasien as p', 'rp.no_rkm_medis', '=', 'p.no_rkm_medis')
            ->join('penjab as pj', 'rp.kd_pj', '=', 'pj.kd_pj')
            ->leftJoin('dokter as d', 'rp.kd_dokter', '=', 'd.kd_dokter')
            ->orderByDesc('rp.tgl_registrasi')
            ->orderByDesc('rp.jam_reg')
            ->limit(8)
            ->select('rp.no_rawat', 'rp.no_rkm_medis', 'rp.tgl_registrasi', 'rp.jam_reg', 'rp.stts', 'rp.status_lanjut', 'p.nm_pasien', 'pj.png_jawab', 'd.nm_dokter')
            ->get();

        return view('livewire.dashboard', [
            'stats'              => $stats,
            'trendData'          => $trendData,
            'ralanKelengkapan'   => $ralanKelengkapan,
            'ranapKelengkapan'   => $ranapKelengkapan,
            'farmasiKelengkapan' => $farmasiKelengkapan,
            'labKelengkapan'     => $labKelengkapan,
            'radiologiKelengkapan' => $radiologiKelengkapan,
            'recent'             => $recentRegistrations,
            'currentMonth'       => Carbon::now()->translatedFormat('F Y'),
        ]);
    }
}
