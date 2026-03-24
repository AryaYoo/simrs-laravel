<?php

namespace App\Livewire\Modul\RawatInap\SubRawatInap;

use App\Models\RegPeriksa;
use Livewire\Component;

class RiwayatPasien extends Component
{
    public $no_rawat;
    public $regPeriksa;

    public function mount($no_rawat)
    {
        $this->no_rawat = str_replace('-', '/', $no_rawat);
        $this->regPeriksa = RegPeriksa::with('pasien')->findOrFail($this->no_rawat);
    }

    public function render()
    {
        return view('livewire.modul.rawat-inap.sub-rawat-inap.riwayat-pasien');
    }
}
