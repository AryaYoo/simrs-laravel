<?php

namespace App\Livewire\Modul\Farmasi;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app', ['title' => 'No. Resep'])]
class NoResep extends Component
{
    public function render()
    {
        return view('livewire.modul.farmasi.no-resep');
    }
}
