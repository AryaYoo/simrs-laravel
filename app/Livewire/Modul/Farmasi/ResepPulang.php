<?php

namespace App\Livewire\Modul\Farmasi;

use App\Repositories\Farmasi\ResepPulangRepository;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app', ['title' => 'Resep Pulang'])]
class ResepPulang extends Component
{
    use WithPagination;

    #[Url(history: true)]
    public string $search = '';

    #[Url(history: true)]
    public string $tanggal1 = '';

    #[Url(history: true)]
    public string $tanggal2 = '';

    #[Url(history: true)]
    public int $perPage = 15;

    protected ResepPulangRepository $repository;

    public function boot(ResepPulangRepository $repository): void
    {
        $this->repository = $repository;
    }

    public function mount(): void
    {
        if (!$this->tanggal1) {
            $this->tanggal1 = date('Y-m-d');
        }
        if (!$this->tanggal2) {
            $this->tanggal2 = date('Y-m-d');
        }
    }

    public function updatedSearch(): void   { $this->resetPage(); }
    public function updatedTanggal1(): void { $this->resetPage(); }
    public function updatedTanggal2(): void { $this->resetPage(); }
    public function updatedPerPage(): void  { $this->resetPage(); }

    public function resetFilter(): void
    {
        $this->reset(['search']);
        $this->tanggal1 = date('Y-m-d');
        $this->tanggal2 = date('Y-m-d');
        $this->perPage = 15;
        $this->resetPage();
    }

    public function deleteResepPulang(
        string $no_rawat,
        string $kode_brng,
        string $tanggal,
        string $jam,
        string $no_batch,
        string $no_faktur
    ): void {
        $deleted = $this->repository->delete(
            $no_rawat,
            $kode_brng,
            $tanggal,
            $jam,
            $no_batch,
            $no_faktur
        );

        if ($deleted) {
            $this->dispatch('swal:toast', [
                'icon' => 'success',
                'title' => 'Data resep pulang berhasil dihapus'
            ]);
        }
    }

    public function render()
    {
        return view('livewire.modul.farmasi.resep-pulang', [
            'resepPulangList' => $this->repository->getList(
                $this->search,
                $this->tanggal1,
                $this->tanggal2,
                $this->perPage
            ),
        ]);
    }
}
