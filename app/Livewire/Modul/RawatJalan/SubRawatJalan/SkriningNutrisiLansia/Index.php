<?php

namespace App\Livewire\Modul\RawatJalan\SubRawatJalan\SkriningNutrisiLansia;

use Livewire\Component;
use App\Models\RegPeriksa;
use App\Livewire\Concerns\WithOptimisticLocking;

class Index extends Component
{
    use WithOptimisticLocking;

    public $noRawat;
    public $regPeriksa;

    public function mount($no_rawat)
    {
        $this->noRawat = str_replace('-', '/', $no_rawat);
        $this->regPeriksa = RegPeriksa::with(['pasien', 'dokter', 'poliklinik'])
            ->where('no_rawat', $this->noRawat)
            ->firstOrFail();

        $this->initializeLock($this->regPeriksa);
    }

    public function render()
    {
        return view('livewire.modul.rawat-jalan.sub-rawat-jalan.skrining-nutrisi-lansia.index')
            ->layout('layouts.app', ['title' => 'Skrining Nutrisi Pasien Lansia - Rawat Jalan']);
    }
}
