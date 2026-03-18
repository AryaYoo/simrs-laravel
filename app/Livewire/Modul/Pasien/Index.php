<?php

namespace App\Livewire\Modul\Pasien;

use App\Models\Pasien;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app', ['title' => 'Master Data Pasien'])]
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

        $pasiens = Pasien::query()
            ->where('no_rkm_medis', 'like', $searchTerm)
            ->orWhere('nm_pasien', 'like', $searchTerm)
            ->orderBy('tgl_daftar', 'desc')
            ->orderBy('no_rkm_medis', 'desc')
            ->paginate($this->perPage);

        return view('livewire.modul.pasien.index', [
            'pasiens' => $pasiens,
        ]);
    }
}
