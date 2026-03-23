<?php

namespace App\Livewire\MasterData\SukuBangsa;

use App\Models\SukuBangsa;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app', ['title' => 'Master Data Suku Bangsa'])]
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

        $sukuBangsas = SukuBangsa::query()
            ->where('id', 'like', $searchTerm)
            ->orWhere('nama_suku_bangsa', 'like', $searchTerm)
            ->orderBy('id', 'asc')
            ->paginate($this->perPage);

        return view('livewire.master-data.suku-bangsa.index', [
            'sukuBangsas' => $sukuBangsas,
        ]);
    }
}
