<?php

namespace App\Livewire\Modul\Farmasi;

use App\Models\ResepObat;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app', ['title' => 'Data Obat, Alkes dan BHP Medis'])]
class ObatAlkesBhp extends Component
{
    public ?string $no_resep = null;

    // Header Fields
    public string $no_rawat = '';
    public string $no_rkm_medis = '';
    public string $nm_pasien = '';

    public string $tgl_validasi = '';
    public string $jam_validasi = '';
    public string $menit_validasi = '';
    public string $detik_validasi = '';

    public string $tarif = 'Rawat Jalan';
    public bool $use_no_resep = true;

    public float $total = 0;
    public float $ppn = 0;
    public float $total_ppn = 0;

    public string $kd_depo = 'AP';
    public string $nm_depo = 'Apotek';

    public function mount(?string $no_resep = null): void
    {
        $this->no_resep = $no_resep;
        $now = now();
        $this->tgl_validasi = $now->format('Y-m-d');
        $this->jam_validasi = $now->format('H');
        $this->menit_validasi = $now->format('i');
        $this->detik_validasi = $now->format('s');

        if ($this->no_resep) {
            $resep = ResepObat::with(['regPeriksa.pasien'])->find($this->no_resep);
            if ($resep) {
                $this->no_rawat = $resep->no_rawat ?? '';
                $this->no_rkm_medis = $resep->regPeriksa->no_rkm_medis ?? '';
                $this->nm_pasien = $resep->regPeriksa->pasien->nm_pasien ?? '';

                if ($resep->status === 'ranap') {
                    $this->tarif = 'Rawat Inap';
                } else {
                    $this->tarif = 'Rawat Jalan';
                }
            }
        }
    }

    public function render()
    {
        return view('livewire.modul.farmasi.obat-alkes-bhp');
    }
}
