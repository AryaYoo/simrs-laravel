<?php

namespace App\Livewire\Modul\Farmasi;

use App\Models\ResepObat;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app', ['title' => 'Daftar Resep Dokter'])]
class DaftarResepDokter extends Component
{
    use WithPagination;

    public string $activeTab = 'ralan'; // 'ralan' or 'ranap'

    #[Url(history: true)]
    public string $search = '';

    #[Url(history: true)]
    public string $dari = '';

    #[Url(history: true)]
    public string $sampai = '';

    #[Url(history: true)]
    public int $perPage = 20;

    // Detail Modal State
    public bool $detailModalOpen = false;
    public ?array $activeResep = null;

    public function mount(): void
    {
        if (! $this->dari) {
            $this->dari = now()->format('Y-m-d');
        }
        if (! $this->sampai) {
            $this->sampai = now()->format('Y-m-d');
        }
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function updatedSearch()  { $this->resetPage(); }
    public function updatedDari()    { $this->resetPage(); }
    public function updatedSampai()  { $this->resetPage(); }
    public function updatedPerPage() { $this->resetPage(); }

    public function showDetail(string $noResep): void
    {
        $resep = ResepObat::with([
            'dokter',
            'regPeriksa.pasien',
            'regPeriksa.poliklinik',
            'regPeriksa.penjab',
            'detail.barang',
        ])->find($noResep);

        if ($resep) {
            $this->activeResep = $resep->toArray();
            $this->detailModalOpen = true;
        }
    }

    public function closeDetailModal(): void
    {
        $this->detailModalOpen = false;
        $this->activeResep = null;
    }

    public function render()
    {
        $reseps = collect();
        $countRalan = 0;
        $countRanap = 0;

        if ($this->activeTab === 'ralan') {
            $query = ResepObat::with([
                'dokter',
                'regPeriksa.pasien',
                'regPeriksa.poliklinik',
                'regPeriksa.penjab',
            ])
            ->where('status', 'ralan');

            if ($this->dari) {
                $query->whereDate('tgl_peresepan', '>=', $this->dari);
            }
            if ($this->sampai) {
                $query->whereDate('tgl_peresepan', '<=', $this->sampai);
            }

            if ($this->search) {
                $like = "%{$this->search}%";
                $query->where(function ($q) use ($like) {
                    $q->where('no_resep', 'like', $like)
                      ->orWhere('no_rawat', 'like', $like)
                      ->orWhereHas('dokter', function ($qd) use ($like) {
                          $qd->where('nm_dokter', 'like', $like);
                      })
                      ->orWhereHas('regPeriksa.pasien', function ($qp) use ($like) {
                          $qp->where('nm_pasien', 'like', $like)
                            ->orWhere('no_rkm_medis', 'like', $like);
                      })
                      ->orWhereHas('regPeriksa.poliklinik', function ($qpol) use ($like) {
                          $qpol->where('nm_poli', 'like', $like);
                      });
                });
            }

            $reseps = $query->orderByDesc('tgl_peresepan')
                ->orderByDesc('jam_peresepan')
                ->paginate($this->perPage);
        }

        // Tab counts
        $countRalanQuery = ResepObat::where('status', 'ralan');
        if ($this->dari) {
            $countRalanQuery->whereDate('tgl_peresepan', '>=', $this->dari);
        }
        if ($this->sampai) {
            $countRalanQuery->whereDate('tgl_peresepan', '<=', $this->sampai);
        }
        $countRalan = $countRalanQuery->count();

        return view('livewire.modul.farmasi.daftar-resep-dokter', [
            'reseps'     => $reseps,
            'countRalan' => $countRalan,
            'countRanap' => $countRanap,
        ]);
    }
}

