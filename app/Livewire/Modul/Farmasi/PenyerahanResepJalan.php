<?php

namespace App\Livewire\Modul\Farmasi;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app', ['title' => 'Penyerahan Resep Obat Rawat Jalan'])]
class PenyerahanResepJalan extends Component
{
    public ?string $no_resep = null;

    public function mount(?string $no_resep = null): void
    {
        $this->no_resep = $no_resep;
    }

    public function render()
    {
        return view('livewire.modul.farmasi.penyerahan-resep-jalan');
    }
}
