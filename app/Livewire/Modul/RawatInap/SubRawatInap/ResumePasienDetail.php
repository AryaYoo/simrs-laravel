<?php

namespace App\Livewire\Modul\RawatInap\SubRawatInap;

use App\Models\RegPeriksa;
use App\Models\ResumePasienRanap;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app', ['title' => 'Detail Resume Medis'])]
class ResumePasienDetail extends Component
{
    public string $no_rawat;
    public $resume;

    public function mount($no_rawat)
    {
        $this->no_rawat = str_replace('-', '/', $no_rawat);
        $this->resume = ResumePasienRanap::with([
            'regPeriksa.pasien',
            'regPeriksa.dokter',
            'regPeriksa.rujukMasuk',
            'regPeriksa.kamarInap.kamar.bangsal',
            'regPeriksa.penjab',
            'dokter'
        ])->findOrFail($this->no_rawat);
    }

    public function render()
    {
        return view('livewire.modul.rawat-inap.sub-rawat-inap.resume-pasien-detail');
    }
}
