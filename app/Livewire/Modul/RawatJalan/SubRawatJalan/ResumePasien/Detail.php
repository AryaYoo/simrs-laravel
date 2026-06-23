<?php

namespace App\Livewire\Modul\RawatJalan\SubRawatJalan\ResumePasien;

use App\Models\RegPeriksa;
use App\Models\ResumePasien;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app', ['title' => 'Detail Resume Medis Rawat Jalan'])]
class Detail extends Component
{
    public string $no_rawat;
    public $resume;

    public function mount($no_rawat)
    {
        $this->no_rawat = str_replace('-', '/', $no_rawat);
        $this->resume = ResumePasien::with([
            'regPeriksa.pasien',
            'regPeriksa.dokter',
            'regPeriksa.poliklinik',
            'regPeriksa.penjab',
            'dokter'
        ])->findOrFail($this->no_rawat);
    }

    public function render()
    {
        return view('livewire.modul.rawat-jalan.sub-rawat-jalan.resume-pasien.detail');
    }
}
