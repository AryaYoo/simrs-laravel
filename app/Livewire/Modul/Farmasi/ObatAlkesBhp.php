<?php

namespace App\Livewire\Modul\Farmasi;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app', ['title' => 'Data Obat, Alkes dan BHP Medis'])]
class ObatAlkesBhp extends Component
{
    public function render()
    {
        return view('livewire.modul.farmasi.obat-alkes-bhp');
    }
}
