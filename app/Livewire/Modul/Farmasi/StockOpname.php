<?php

namespace App\Livewire\Modul\Farmasi;

use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use App\Repositories\Farmasi\OpnameRepository;

#[Layout('layouts.app', ['title' => 'Stock Opname'])]
class StockOpname extends Component
{
    use WithPagination;

    #[Url(history: true)]
    public string $search = '';
    
    #[Url(history: true)]
    public string $tanggal1 = '';
    
    #[Url(history: true)]
    public string $tanggal2 = '';

    #[Url(history: true)]
    public string $kd_bangsal = '';

    #[Url(history: true)]
    public string $kdjns = '';

    #[Url(history: true)]
    public string $kode_kategori = '';

    #[Url(history: true)]
    public string $kode_golongan = '';

    #[Url(history: true)]
    public int $perPage = 10;

    protected $repository;

    public function boot(OpnameRepository $repository)
    {
        $this->repository = $repository;
    }

    public function mount()
    {
        if (! $this->tanggal1) {
            $this->tanggal1 = date('Y-m-d');
        }
        if (! $this->tanggal2) {
            $this->tanggal2 = date('Y-m-d');
        }
    }

    public function updatedSearch()       { $this->resetPage(); }
    public function updatedTanggal1()     { $this->resetPage(); }
    public function updatedTanggal2()     { $this->resetPage(); }
    public function updatedKdBangsal()    { $this->resetPage(); }
    public function updatedKdjns()        { $this->resetPage(); }
    public function updatedKodeKategori() { $this->resetPage(); }
    public function updatedKodeGolongan() { $this->resetPage(); }
    public function updatedPerPage()      { $this->resetPage(); }

    public function resetFilter()
    {
        $this->reset(['search', 'kd_bangsal', 'kdjns', 'kode_kategori', 'kode_golongan']);
        $this->tanggal1 = date('Y-m-d');
        $this->tanggal2 = date('Y-m-d');
        $this->perPage = 10;
        $this->resetPage();
    }

    public function deleteOpname($kode_brng, $tanggal, $kd_bangsal, $no_batch, $no_faktur)
    {
        $this->repository->delete($kode_brng, $tanggal, $kd_bangsal, $no_batch, $no_faktur);
    }

    public function render()
    {
        return view('livewire.modul.farmasi.stock-opname', [
            'opnames' => $this->repository->getList(
                $this->search,
                $this->tanggal1,
                $this->tanggal2,
                $this->kd_bangsal,
                $this->kdjns,
                $this->kode_kategori,
                $this->kode_golongan,
                $this->perPage
            ),
            'summary' => $this->repository->getSummary(
                $this->search,
                $this->tanggal1,
                $this->tanggal2,
                $this->kd_bangsal,
                $this->kdjns,
                $this->kode_kategori,
                $this->kode_golongan
            ),
            'listBangsal' => $this->repository->getMasterBangsal(),
            'listJenis' => $this->repository->getMasterJenis(),
            'listKategori' => $this->repository->getMasterKategori(),
            'listGolongan' => $this->repository->getMasterGolongan(),
        ]);
    }
}
