<?php

namespace App\Livewire\Modul\RawatInap;

use App\Models\RegPeriksa;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app', ['title' => 'Perawatan/Tindakan Pasien Rawat Inap'])]
class PerawatanTindakan extends Component
{
    public string $no_rawat;
    public $regPeriksa;
    public string $activeTab = 'penanganan_dokter';

    public function mount(string $no_rawat)
    {
        $this->no_rawat = str_replace('-', '/', $no_rawat);
        $this->regPeriksa = RegPeriksa::with([
            'pasien', 
            'penjab', 
            'dokter',
            'kamarInap.kamar', 
            'permintaanRanap.kamar'
        ])
        ->where('no_rawat', $this->no_rawat)
        ->firstOrFail();
    }

    public function render()
    {
        $rawatInapDrpr = \App\Models\RawatInapDrpr::with(['regPeriksa.pasien', 'jnsPerawatan', 'dokter', 'petugas'])
            ->where('no_rawat', $this->no_rawat)
            ->get();

        return view('livewire.modul.rawat-inap.perawatan-tindakan', [
            'rawatInapDrpr' => $rawatInapDrpr,
        ]);
    }
}
