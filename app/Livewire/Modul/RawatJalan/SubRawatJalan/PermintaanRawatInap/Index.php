<?php

namespace App\Livewire\Modul\RawatJalan\SubRawatJalan\PermintaanRawatInap;

use App\Models\RegPeriksa;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app', ['title' => 'Permintaan Rawat Inap'])]
class Index extends Component
{
    public $no_rawat;
    public $no_rawat_slug;
    public $regPeriksa;

    public function mount($no_rawat)
    {
        // Decode the slug to normal no_rawat format
        $this->no_rawat = str_replace('-', '/', $no_rawat);
        $this->no_rawat_slug = $no_rawat;
        
        $this->regPeriksa = RegPeriksa::with(['pasien', 'dokter', 'poliklinik'])
            ->where('no_rawat', $this->no_rawat)
            ->firstOrFail();
    }

    public function render()
    {
        return view('livewire.modul.rawat-jalan.sub-rawat-jalan.permintaan-rawat-inap.index');
    }
}
