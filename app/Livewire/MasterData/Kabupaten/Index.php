<?php

namespace App\Livewire\MasterData\Kabupaten;

use App\Models\Kabupaten;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app', ['title' => 'Master Data Kabupaten'])]
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

        $kabupatens = Kabupaten::query()
            ->where('kd_kab', 'like', $searchTerm)
            ->orWhere('nm_kab', 'like', $searchTerm)
            ->orderBy('kd_kab', 'asc')
            ->paginate($this->perPage);

        return view('livewire.master-data.kabupaten.index', [
            'kabupatens' => $kabupatens,
        ]);
    }
}
