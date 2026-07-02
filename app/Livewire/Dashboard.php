<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

#[Layout('layouts.app', ['title' => 'Dashboard'])]
class Dashboard extends Component
{
    use WithPagination;

    public $filterDate;
    public $filterSearch = '';

    protected $queryString = [
        'filterDate' => ['except' => ''],
        'filterSearch' => ['except' => ''],
    ];

    public function mount()
    {
        if (!$this->filterDate) {
            $this->filterDate = Carbon::today()->format('Y-m-d');
        }
    }

    public function updatingFilterDate()
    {
        $this->resetPage();
    }

    public function updatingFilterSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $today = Carbon::today()->format('Y-m-d');
        
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

        // ─── 3. Monitoring Kelengkapan (Tabel Registrasi) ─────────────────────
        $query = DB::table('reg_periksa as rp')
            ->join('pasien as p', 'rp.no_rkm_medis', '=', 'p.no_rkm_medis')
            ->join('penjab as pj', 'rp.kd_pj', '=', 'pj.kd_pj')
            ->leftJoin('dokter as d', 'rp.kd_dokter', '=', 'd.kd_dokter')
            ->select('rp.no_rawat', 'rp.no_rkm_medis', 'rp.tgl_registrasi', 'rp.jam_reg', 'rp.stts', 'rp.status_lanjut', 'p.nm_pasien', 'pj.png_jawab', 'd.nm_dokter');

        if ($this->filterDate) {
            $query->where('rp.tgl_registrasi', $this->filterDate);
        }

        if ($this->filterSearch) {
            $query->where(function($q) {
                $q->where('p.nm_pasien', 'like', '%' . $this->filterSearch . '%')
                  ->orWhere('rp.no_rkm_medis', 'like', '%' . $this->filterSearch . '%')
                  ->orWhere('rp.no_rawat', 'like', '%' . $this->filterSearch . '%');
            });
        }

        $registrations = $query->orderByDesc('rp.tgl_registrasi')
            ->orderByDesc('rp.jam_reg')
            ->paginate(15);

        // Fetch Checklist Data for the current page
        $noRawats = collect($registrations->items())->pluck('no_rawat')->toArray();
        $checklistData = [];

        if (!empty($noRawats)) {
            $rawatJlDr = DB::table('rawat_jl_dr')->whereIn('no_rawat', $noRawats)->pluck('no_rawat')->toArray();
            $rawatJlPr = DB::table('rawat_jl_pr')->whereIn('no_rawat', $noRawats)->pluck('no_rawat')->toArray();
            $rawatJlDrPr = DB::table('rawat_jl_drpr')->whereIn('no_rawat', $noRawats)->pluck('no_rawat')->toArray();
            
            $rawatInapDr = DB::table('rawat_inap_dr')->whereIn('no_rawat', $noRawats)->pluck('no_rawat')->toArray();
            $rawatInapPr = DB::table('rawat_inap_pr')->whereIn('no_rawat', $noRawats)->pluck('no_rawat')->toArray();
            $rawatInapDrPr = DB::table('rawat_inap_drpr')->whereIn('no_rawat', $noRawats)->pluck('no_rawat')->toArray();
            
            $diagnosa = DB::table('diagnosa_pasien')->whereIn('no_rawat', $noRawats)->pluck('no_rawat')->toArray();
            
            // Catatan Dokter
            $catatan = DB::table('catatan_perawatan')->whereIn('no_rawat', $noRawats)->pluck('no_rawat')->toArray();
            
            // Resume
            $resume = DB::table('resume_pasien')->whereIn('no_rawat', $noRawats)->pluck('no_rawat')->toArray();
            $resumeRanap = DB::table('resume_pasien_ranap')->whereIn('no_rawat', $noRawats)->pluck('no_rawat')->toArray();
            
            $pengkajianRanap = DB::table('penilaian_awal_keperawatan_ranap')->whereIn('no_rawat', $noRawats)->pluck('no_rawat')->toArray();

            $resepObat = DB::table('resep_obat')->whereIn('no_rawat', $noRawats)
                ->selectRaw("no_rawat, COUNT(*) as permintaan, SUM(CASE WHEN status = 'Sudah' THEN 1 ELSE 0 END) as selesai")
                ->groupBy('no_rawat')->get()->keyBy('no_rawat');
            $pemberianObat = DB::table('detail_pemberian_obat')->whereIn('no_rawat', $noRawats)
                ->selectRaw("no_rawat, COUNT(*) as pemberian")
                ->groupBy('no_rawat')->get()->keyBy('no_rawat');

            $permintaanLab = DB::table('permintaan_lab')->whereIn('no_rawat', $noRawats)
                ->selectRaw("no_rawat, COUNT(*) as permintaan, SUM(CASE WHEN tgl_hasil IS NOT NULL AND tgl_hasil != '' THEN 1 ELSE 0 END) as hasil")
                ->groupBy('no_rawat')->get()->keyBy('no_rawat');

            $permintaanRad = DB::table('permintaan_radiologi')->whereIn('no_rawat', $noRawats)
                ->selectRaw("no_rawat, COUNT(*) as permintaan, SUM(CASE WHEN tgl_hasil IS NOT NULL AND tgl_hasil != '' THEN 1 ELSE 0 END) as hasil, SUM(CASE WHEN status = 'Sudah' THEN 1 ELSE 0 END) as selesai")
                ->groupBy('no_rawat')->get()->keyBy('no_rawat');

            foreach ($noRawats as $nr) {
                $checklistData[$nr] = [
                    'ralan' => [
                        'dr' => in_array($nr, $rawatJlDr),
                        'pr' => in_array($nr, $rawatJlPr),
                        'drpr' => in_array($nr, $rawatJlDrPr),
                        'diagnosa' => in_array($nr, $diagnosa),
                        'catatan' => in_array($nr, $catatan),
                        'resume' => in_array($nr, $resume),
                    ],
                    'ranap' => [
                        'pengkajian' => in_array($nr, $pengkajianRanap),
                        'dr' => in_array($nr, $rawatInapDr),
                        'pr' => in_array($nr, $rawatInapPr),
                        'drpr' => in_array($nr, $rawatInapDrPr),
                        'diagnosa' => in_array($nr, $diagnosa),
                        'catatan' => in_array($nr, $catatan),
                        'resume' => in_array($nr, $resumeRanap) || in_array($nr, $resume),
                    ],
                    'farmasi' => [
                        'permintaan' => isset($resepObat[$nr]) && $resepObat[$nr]->permintaan > 0,
                        'pemberian' => isset($pemberianObat[$nr]) && $pemberianObat[$nr]->pemberian > 0,
                        'validasi' => isset($resepObat[$nr]) && $resepObat[$nr]->selesai > 0,
                    ],
                    'lab' => [
                        'permintaan' => isset($permintaanLab[$nr]) && $permintaanLab[$nr]->permintaan > 0,
                        'hasil' => isset($permintaanLab[$nr]) && $permintaanLab[$nr]->hasil > 0,
                    ],
                    'radiologi' => [
                        'permintaan' => isset($permintaanRad[$nr]) && $permintaanRad[$nr]->permintaan > 0,
                        'hasil' => isset($permintaanRad[$nr]) && $permintaanRad[$nr]->hasil > 0,
                        'selesai' => isset($permintaanRad[$nr]) && $permintaanRad[$nr]->selesai > 0,
                    ]
                ];
            }
        }


        // ─── 4. Top 10 Pasien Umum dengan Pengeluaran Terbanyak ──────────────────
        $topUmumPatients = DB::table('reg_periksa as rp')
            ->join('penjab as pj', 'rp.kd_pj', '=', 'pj.kd_pj')
            ->join('pasien as p', 'rp.no_rkm_medis', '=', 'p.no_rkm_medis')
            ->join('billing as b', 'rp.no_rawat', '=', 'b.no_rawat')
            ->where('pj.png_jawab', 'like', '%Umum%')
            ->whereNotIn('b.status', ['-', 'Dokter', 'Perawat', 'TtlObat', 'TtlRanap Dokter', 'TtlRanap Paramedis', 'TtlRalan Dokter', 'TtlRalan Paramedis', 'TtlKamar', 'TtlTambahan', 'TtlRetur Obat', 'TtlResep Pulang', 'TtlPotongan', 'TtlLaborat', 'TtlOperasi', 'TtlRadiologi', 'Tagihan'])
            ->selectRaw("
                p.no_rkm_medis,
                p.nm_pasien,
                COUNT(DISTINCT rp.no_rawat) as total_kunjungan,
                SUM(b.totalbiaya) as total_pengeluaran
            ")
            ->groupBy('p.no_rkm_medis', 'p.nm_pasien')
            ->having('total_pengeluaran', '>', 0)
            ->orderByDesc('total_pengeluaran')
            ->limit(10)
            ->get();

        return view('livewire.dashboard', [
            'stats'            => $stats,
            'trendData'        => $trendData,
            'registrations'    => $registrations,
            'checklistData'    => $checklistData,
            'topUmumPatients'  => $topUmumPatients,
        ]);
    }
}
