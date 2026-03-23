<?php

namespace App\Livewire\Modul\RawatJalan;

use App\Models\RegPeriksa;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app', ['title' => 'Detail Pasien Rawat Jalan'])]
class Show extends Component
{
    public string $no_rawat;
    public $regPeriksa;

    public function mount(string $no_rawat)
    {
        $this->no_rawat = str_replace('-', '/', $no_rawat);
        $this->regPeriksa = RegPeriksa::with([
            'pasien', 
            'penjab', 
            'dokter',
            'poliklinik'
        ])
        ->where('no_rawat', $this->no_rawat)
        ->firstOrFail();
    }

    public function render()
    {
        return view('livewire.modul.rawat-jalan.show');
    }
}
