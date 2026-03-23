<?php

namespace App\Livewire\MasterData\Kecamatan;

use App\Models\Kecamatan;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app', ['title' => 'Master Data Kecamatan'])]
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

        $kecamatans = Kecamatan::query()
            ->where('kd_kec', 'like', $searchTerm)
            ->orWhere('nm_kec', 'like', $searchTerm)
            ->orderBy('kd_kec', 'asc')
            ->paginate($this->perPage);

        return view('livewire.master-data.kecamatan.index', [
            'kecamatans' => $kecamatans,
        ]);
    }
}
