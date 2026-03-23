<?php

namespace App\Livewire\MasterData\Dokter;

use App\Models\Dokter;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app', ['title' => 'Master Data Dokter'])]
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

        $dokters = Dokter::query()
            ->where('kd_dokter', 'like', $searchTerm)
            ->orWhere('nm_dokter', 'like', $searchTerm)
            ->orWhere('alumni', 'like', $searchTerm)
            ->orderBy('kd_dokter', 'asc')
            ->paginate($this->perPage);

        return view('livewire.master-data.dokter.index', [
            'dokters' => $dokters,
        ]);
    }
}
