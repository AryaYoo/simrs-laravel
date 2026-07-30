<?php

namespace App\Livewire\Modul\Farmasi;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app', ['title' => 'Daftar Resep Dokter'])]
class DaftarResepDokter extends Component
{
    public function render()
    {
        return view('livewire.modul.farmasi.daftar-resep-dokter');
    }
}
