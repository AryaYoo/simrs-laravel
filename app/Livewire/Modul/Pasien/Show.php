<?php

namespace App\Livewire\Modul\Pasien;

use App\Models\Pasien;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app', ['title' => 'Detail Master Pasien'])]
class Show extends Component
{
    public $no_rkm_medis;

    public function mount($no_rkm_medis)
    {
        $this->no_rkm_medis = $no_rkm_medis;
    }

    public function render()
    {
        $pasien = Pasien::with([
            'kelurahan', 'kecamatan', 'kabupaten',
            'kelurahanPj', 'kecamatanPj', 'kabupatenPj',
            'penjab', 'sukuBangsa', 'bahasa', 'perusahaan', 'cacatFisik'
        ])->where('no_rkm_medis', $this->no_rkm_medis)->firstOrFail();

        return view('livewire.modul.pasien.show', [
            'pasien' => $pasien,
        ]);
    }
}
