<?php

namespace App\Livewire\Modul\RegistrasiPasien;

use App\Models\RegPeriksa;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app', ['title' => 'Detail Pasien'])]
class Show extends Component
{
    public string $no_rawat;
    public $regPeriksa;

    public function mount(string $no_rawat)
    {
        $this->no_rawat = str_replace('-', '/', $no_rawat); // Handle potential slash encoding issues in URL
        $this->regPeriksa = RegPeriksa::with(['dokter', 'pasien', 'penjab'])
            ->where('no_rawat', $this->no_rawat)
            ->firstOrFail();
    }

    public function render()
    {
        return view('livewire.modul.registrasi-pasien.show');
    }
}
