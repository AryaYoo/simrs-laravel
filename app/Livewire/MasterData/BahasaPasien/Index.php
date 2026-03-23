<?php

namespace App\Livewire\MasterData\BahasaPasien;

use App\Models\BahasaPasien;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app', ['title' => 'Master Data Bahasa Pasien'])]
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

        $bahasaPasiens = BahasaPasien::query()
            ->where('id', 'like', $searchTerm)
            ->orWhere('nama_bahasa', 'like', $searchTerm)
            ->orderBy('id', 'asc')
            ->paginate($this->perPage);

        return view('livewire.master-data.bahasa-pasien.index', [
            'bahasaPasiens' => $bahasaPasiens,
        ]);
    }
}
