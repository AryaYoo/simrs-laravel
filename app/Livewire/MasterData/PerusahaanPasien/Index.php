<?php

namespace App\Livewire\MasterData\PerusahaanPasien;

use App\Models\PerusahaanPasien;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app', ['title' => 'Master Data Perusahaan Pasien'])]
class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public int $perPage = 20;

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function render()
    {
        $searchTerm = '%' . $this->search . '%';

        $perusahaans = PerusahaanPasien::query()
            ->where('kode_perusahaan', 'like', $searchTerm)
            ->orWhere('nama_perusahaan', 'like', $searchTerm)
            ->orWhere('kota', 'like', $searchTerm)
            ->orderBy('kode_perusahaan', 'asc')
            ->paginate($this->perPage);

        return view('livewire.master-data.perusahaan-pasien.index', [
            'perusahaans' => $perusahaans,
        ]);
    }
}
