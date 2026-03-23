<?php

namespace App\Livewire\MasterData\Penjamin;

use App\Models\Penjab;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app', ['title' => 'Master Data Penjamin'])]
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

        $penjamins = Penjab::query()
            ->where('kd_pj', 'like', $searchTerm)
            ->orWhere('png_jawab', 'like', $searchTerm)
            ->orWhere('nama_perusahaan', 'like', $searchTerm)
            ->orderBy('kd_pj', 'asc')
            ->paginate($this->perPage);

        return view('livewire.master-data.penjamin.index', [
            'penjamins' => $penjamins,
        ]);
    }
}
