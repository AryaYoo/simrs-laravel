<?php

namespace App\Livewire\Modul\Farmasi;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app', ['title' => 'Data Penyerahan Darah'])]
class PenyerahanDarah extends Component
{
    public function render()
    {
        return view('livewire.modul.farmasi.penyerahan-darah');
    }
}
