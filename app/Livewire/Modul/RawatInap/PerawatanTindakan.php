<?php

namespace App\Livewire\Modul\RawatInap;

use App\Models\PemeriksaanRanap;
use App\Models\RegPeriksa;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app', ['title' => 'Perawatan/Tindakan Pasien Rawat Inap', 'hideSidebar' => true])]
class PerawatanTindakan extends Component
{
    public string $no_rawat;
    public $regPeriksa;
    public string $activeTab = 'pemeriksaan';

    public function mount(string $no_rawat): void
    {
        $this->no_rawat = str_replace('-', '/', $no_rawat);
        $this->regPeriksa = RegPeriksa::with([
            'pasien',
            'penjab',
            'dokter',
            'kamarInap.kamar',
            'permintaanRanap.kamar',
        ])
        ->where('no_rawat', $this->no_rawat)
        ->firstOrFail();
    }

    public function setActiveTab(string $key): void
    {
        $this->activeTab = $key;
    }

    public function render()
    {
        $rawatInapDrpr = \App\Models\RawatInapDrpr::with(['regPeriksa.pasien', 'jnsPerawatan', 'dokter', 'petugas'])
            ->where('no_rawat', $this->no_rawat)
            ->get();

        $pemeriksaanRanap = PemeriksaanRanap::with(['regPeriksa.pasien', 'pegawai'])
            ->where('no_rawat', $this->no_rawat)
            ->orderBy('tgl_perawatan', 'desc')
            ->orderBy('jam_rawat', 'desc')
            ->get();

        return view('livewire.modul.rawat-inap.perawatan-tindakan', [
            'rawatInapDrpr'    => $rawatInapDrpr,
            'pemeriksaanRanap' => $pemeriksaanRanap,
        ]);
    }
}
