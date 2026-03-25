<?php

namespace App\Livewire\Modul\RawatInap\SubRawatInap;

use App\Models\RegPeriksa;
use Livewire\Component;

class RiwayatPasien extends Component
{
    public $no_rawat;
    public $regPeriksa;
    public $activeTab = 'kunjungan';
    public $riwayatKunjungan;

    public function mount($no_rawat)
    {
        $this->no_rawat = str_replace('-', '/', $no_rawat);
        $this->regPeriksa = RegPeriksa::with(['pasien.bahasa', 'pasien.cacatFisik'])->findOrFail($this->no_rawat);

        $noRkmMedis = $this->regPeriksa->no_rkm_medis;

        $this->riwayatKunjungan = RegPeriksa::with([
            'dokter',
            'penjab',
            'kamarInap.kamar',
        ])
        ->where('no_rkm_medis', $noRkmMedis)
        ->orderByDesc('tgl_registrasi')
        ->orderByDesc('jam_reg')
        ->get();
    }

    public function render()
    {
        return view('livewire.modul.rawat-inap.sub-rawat-inap.riwayat-pasien');
    }
}
