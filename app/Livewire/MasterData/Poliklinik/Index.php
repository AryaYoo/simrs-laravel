<?php

namespace App\Livewire\MasterData\Poliklinik;

use App\Models\Poliklinik;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app', ['title' => 'Master Data Poliklinik'])]
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

        $units = Poliklinik::query()
            ->where('kd_poli', 'like', $searchTerm)
            ->orWhere('nm_poli', 'like', $searchTerm)
            ->orderBy('kd_poli', 'asc')
            ->paginate($this->perPage);

        return view('livewire.master-data.poliklinik.index', [
            'units' => $units,
        ]);
    }
}
