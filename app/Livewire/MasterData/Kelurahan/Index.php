<?php

namespace App\Livewire\MasterData\Kelurahan;

use App\Models\Kelurahan;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app', ['title' => 'Master Data Kelurahan'])]
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

        $kelurahans = Kelurahan::query()
            ->where('kd_kel', 'like', $searchTerm)
            ->orWhere('nm_kel', 'like', $searchTerm)
            ->orderBy('kd_kel', 'asc')
            ->paginate($this->perPage);

        return view('livewire.master-data.kelurahan.index', [
            'kelurahans' => $kelurahans,
        ]);
    }
}
