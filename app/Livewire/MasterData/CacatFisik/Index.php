<?php

namespace App\Livewire\MasterData\CacatFisik;

use App\Models\CacatFisik;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app', ['title' => 'Master Data Cacat Fisik'])]
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

        $cacatFisiks = CacatFisik::query()
            ->where('id', 'like', $searchTerm)
            ->orWhere('nama_cacat', 'like', $searchTerm)
            ->orderBy('id', 'asc')
            ->paginate($this->perPage);

        return view('livewire.master-data.cacat-fisik.index', [
            'cacatFisiks' => $cacatFisiks,
        ]);
    }
}
