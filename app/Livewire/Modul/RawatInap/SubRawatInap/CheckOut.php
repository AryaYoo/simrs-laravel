<?php

namespace App\Livewire\Modul\RawatInap\SubRawatInap;

use App\Models\RegPeriksa;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app', ['title' => 'Check Out Pasien'])]
class CheckOut extends Component
{
    public string $no_rawat;
    public $regPeriksa;

    public function mount(string $no_rawat)
    {
        $this->no_rawat = str_replace('-', '/', $no_rawat);
        $this->regPeriksa = RegPeriksa::with(['pasien', 'kamarInap.kamar'])
            ->where('no_rawat', $this->no_rawat)
            ->firstOrFail();
    }

    public function render()
    {
        return view('livewire.modul.rawat-inap.sub-rawat-inap.check-out');
    }
}
