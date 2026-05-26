<?php

namespace App\Livewire\Modul\RawatInap\KelahiranBayi;

use Livewire\Component;
use Livewire\Attributes\Title;

#[Title('Kelahiran Bayi - SIMRS LaraLite')]
class Index extends Component
{
    public function render()
    {
        return view('livewire.modul.rawat-inap.kelahiran-bayi.index');
    }
}
