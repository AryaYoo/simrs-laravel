<?php

namespace App\Livewire\Modul\RawatInap\SubRawatInap\Diagnosa;

use Livewire\Component;

class Index extends Component
{
    public $no_rawat;

    public function mount($no_rawat)
    {
        $this->no_rawat = str_replace('-', '/', $no_rawat);
    }

    public function render()
    {
        return view('livewire.modul.rawat-inap.sub-rawat-inap.diagnosa.index');
    }
}
