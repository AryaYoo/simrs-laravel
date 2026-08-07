<?php

namespace App\Livewire\Modul\Casemix;

use App\Repositories\Casemix\LaporanOperasiRepository;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;

class KustomLaporanOperasi extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    #[Url(history: true)]
    public $search = '';

    #[Url(history: true)]
    public $dari = '';

    #[Url(history: true)]
    public $sampai = '';

    public function mount()
    {
        // Hanya set default jika belum ada nilai dari URL query string
        if (empty($this->dari)) {
            $this->dari = date('Y-m-01');
        }
        if (empty($this->sampai)) {
            $this->sampai = date('Y-m-t');
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingDari()
    {
        $this->resetPage();
    }

    public function updatingSampai()
    {
        $this->resetPage();
    }

    public function resetFilter()
    {
        $this->search = '';
        $this->dari = date('Y-m-01');
        $this->sampai = date('Y-m-t');
        $this->resetPage();
    }

    public function render()
    {
        $laporanList = LaporanOperasiRepository::getList(
            $this->search,
            $this->dari,
            $this->sampai,
            15
        );

        return view('livewire.modul.casemix.kustom-laporan-operasi', [
            'laporanList' => $laporanList,
        ])->layout('layouts.app');
    }
}