<?php

namespace App\Livewire\Modul\Farmasi;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app', ['title' => 'Data Penjualan Obat & BHP'])]
class DataPenjualan extends Component
{
    public function render()
    {
        return view('livewire.modul.farmasi.data-penjualan');
    }
}
