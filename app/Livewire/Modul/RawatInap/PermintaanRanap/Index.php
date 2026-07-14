<?php

namespace App\Livewire\Modul\RawatInap\PermintaanRanap;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app', ['title' => 'Permintaan Rawat Inap'])]
class Index extends Component
{
    public function render()
    {
        return view('livewire.modul.rawat-inap.permintaan-ranap.index');
    }
}
