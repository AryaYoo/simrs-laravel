<?php

namespace App\Livewire\Modul\Laboratorium;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app', ['title' => 'Laboratorium'])]
class Index extends Component
{
    public function render()
    {
        return view('livewire.modul.laboratorium.index');
    }
}
