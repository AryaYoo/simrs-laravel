<?php

namespace App\Livewire\Modul\RawatInap;

use App\Models\RegPeriksa;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app', ['title' => 'Daftar Rawat Inap'])]
class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public string $dari = '';
    public string $sampai = '';
    public string $filterType = '';
    public int $perPage = 20;

    public function mount()
    {
        $this->dari   = now()->format('Y-m-d');
        $this->sampai = now()->format('Y-m-d');
    }

    public function updatedSearch()  { $this->resetPage(); }
    public function updatedDari()    { $this->resetPage(); }
    public function updatedSampai()  { $this->resetPage(); }
    public function updatedPerPage() { $this->resetPage(); }

    public function setFilter(string $type)
    {
        $this->filterType = ($this->filterType === $type) ? '' : $type;
        $this->resetPage();
    }

    public function render()
    {
        $searchTerm = '%' . $this->search . '%';

        $baseQuery = RegPeriksa::query()
            ->where('status_lanjut', 'Ranap')
            ->when($this->dari,    fn($q) => $q->whereDate('tgl_registrasi', '>=', $this->dari))
            ->when($this->sampai,  fn($q) => $q->whereDate('tgl_registrasi', '<=', $this->sampai))
            ->where(function ($query) use ($searchTerm) {
                $query->where('no_rawat', 'like', $searchTerm)
                    ->orWhere('no_rkm_medis', 'like', $searchTerm)
                    ->orWhereHas('pasien', function ($q) use ($searchTerm) {
                        $q->where('nm_pasien', 'like', $searchTerm);
                    });
            });

        // Current count for summary
        $counts = (clone $baseQuery)
            ->join('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
            ->selectRaw("
                COUNT(*) as total,
                SUM(CASE WHEN penjab.png_jawab LIKE '%BPJS%' THEN 1 ELSE 0 END) as bpjs,
                SUM(CASE WHEN penjab.png_jawab LIKE '%UMUM%' OR penjab.png_jawab = '-' THEN 1 ELSE 0 END) as umum
            ")->first();

        // Apply visual filter to results only
        $resultsQuery = (clone $baseQuery)
            ->when($this->filterType === 'bpjs', function($q) {
                $q->whereHas('penjab', fn($sq) => $sq->where('png_jawab', 'like', '%BPJS%'));
            })
            ->when($this->filterType === 'umum', function($q) {
                $q->whereHas('penjab', fn($sq) => $sq->where('png_jawab', 'like', '%UMUM%')->orWhere('png_jawab', '-'));
            })
            ->when($this->filterType === 'lainnya', function($q) {
                $q->whereHas('penjab', fn($sq) => $sq->where('png_jawab', 'not like', '%BPJS%')
                    ->where('png_jawab', 'not like', '%UMUM%')
                    ->where('png_jawab', '!=', '-'));
            });

        $regPeriksas = $resultsQuery
            ->with(['pasien', 'penjab', 'permintaanRanap'])
            ->orderBy('tgl_registrasi', 'desc')
            ->orderBy('jam_reg', 'desc')
            ->paginate($this->perPage);

        return view('livewire.modul.rawat-inap.index', [
            'regPeriksas' => $regPeriksas,
            'summary' => [
                'total' => $counts->total ?? 0,
                'bpjs' => $counts->bpjs ?? 0,
                'umum' => $counts->umum ?? 0,
                'lainnya' => max(0, ($counts->total ?? 0) - ($counts->bpjs ?? 0) - ($counts->umum ?? 0)),
            ]
        ]);
    }
}
