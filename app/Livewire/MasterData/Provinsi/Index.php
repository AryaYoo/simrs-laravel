<?php

namespace App\Livewire\MasterData\Provinsi;

use App\Models\Propinsi;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app', ['title' => 'Master Data Provinsi'])]
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

        $provinsis = Propinsi::query()
            ->where('kd_prop', 'like', $searchTerm)
            ->orWhere('nm_prop', 'like', $searchTerm)
            ->orderBy('kd_prop', 'asc')
            ->paginate($this->perPage);

        return view('livewire.master-data.provinsi.index', [
            'provinsis' => $provinsis,
        ]);
    }
}
