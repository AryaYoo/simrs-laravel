<?php

namespace App\Livewire\Modul\Farmasi;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app', ['title' => 'Penerimaan Obat, Alkes & BHP'])]
class PenerimaanObatBhp extends Component
{
    public function render()
    {
        return view('livewire.modul.farmasi.penerimaan-obat-bhp');
    }
}
