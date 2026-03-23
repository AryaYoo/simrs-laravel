<?php

namespace App\Livewire\MasterData\Perujuk;

use App\Models\RujukMasuk;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app', ['title' => 'Master Data Perujuk'])]
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

        $perujuks = RujukMasuk::query()
            ->select('perujuk', 'alamat')
            ->where('perujuk', 'like', $searchTerm)
            ->orWhere('alamat', 'like', $searchTerm)
            ->distinct()
            ->orderBy('perujuk', 'asc')
            ->paginate($this->perPage);

        return view('livewire.master-data.perujuk.index', [
            'perujuks' => $perujuks,
        ]);
    }
}
