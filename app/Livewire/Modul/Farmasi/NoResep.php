<?php

namespace App\Livewire\Modul\Farmasi;

use App\Models\ResepObat;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app', ['title' => 'No. Resep'])]
class NoResep extends Component
{
    use WithPagination;

    public string $search   = '';
    public string $dari     = '';
    public string $sampai   = '';
    public int    $perPage  = 20;

    // Detail modal state
    public bool  $detailModalOpen = false;
    public ?array $activeResep    = null;

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
            // Enrich each detail with validated pricing from detail_pemberian_obat
            $validated = \App\Models\DetailPemberianObat::where('no_rawat', $resep->no_rawat)
                ->whereDate('tgl_perawatan', $resep->tgl_perawatan ?? $resep->tgl_peresepan)
                ->get()
                ->keyBy('kode_brng');

            $detail = $resep->detail->map(function ($item) use ($validated) {
                $v = $validated->get($item->kode_brng);
                return [
                    'nama_brng'   => $item->barang->nama_brng ?? $item->kode_brng,
                    'jml'         => $item->jml,
                    'aturan_pakai'=> $item->aturan_pakai,
                    'h_beli'      => $v?->h_beli ?? 0,
                    'embalase'    => $v?->embalase ?? 0,
                    'tuslah'      => $v?->tuslah ?? 0,
                    'total'       => $v?->total ?? 0,
                ];
            })->values()->toArray();

            $this->activeResep = array_merge($resep->toArray(), [
                'enriched_detail' => $detail,
            ]);
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
        $query = ResepObat::with([
            'dokter',
            'regPeriksa.pasien',
            'regPeriksa.poliklinik',
            'regPeriksa.penjab',
        ]);

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
                  ->orWhereHas('dokter', fn($qd) => $qd->where('nm_dokter', 'like', $like))
                  ->orWhereHas('regPeriksa.pasien', fn($qp) =>
                      $qp->where('nm_pasien', 'like', $like)
                         ->orWhere('no_rkm_medis', 'like', $like)
                  );
            });
        }

        $reseps = $query->orderByDesc('tgl_peresepan')
            ->orderByDesc('jam_peresepan')
            ->paginate($this->perPage);

        return view('livewire.modul.farmasi.no-resep', [
            'reseps' => $reseps,
        ]);
    }
}
