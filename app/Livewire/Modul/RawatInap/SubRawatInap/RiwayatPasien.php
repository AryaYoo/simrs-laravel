<?php

namespace App\Livewire\Modul\RawatInap\SubRawatInap;

use App\Models\PemeriksaanRanap;
use App\Models\RegPeriksa;
use Livewire\Component;

class RiwayatPasien extends Component
{
    public string $no_rawat;
    public string $no_rkm_medis;
    public $activeTab = 'kunjungan';

    public function mount($no_rawat)
    {
        $this->no_rawat = str_replace('-', '/', $no_rawat);
        $regPeriksa = RegPeriksa::findOrFail($this->no_rawat);
        $this->no_rkm_medis = $regPeriksa->no_rkm_medis;
    }

    public function render()
    {
        $regPeriksa = RegPeriksa::with(['pasien.bahasa', 'pasien.cacatFisik'])
            ->find($this->no_rawat);

        $noRawatList = RegPeriksa::where('no_rkm_medis', $this->no_rkm_medis)
            ->pluck('no_rawat');

        $riwayatKunjungan = RegPeriksa::with(['dokter', 'penjab', 'kamarInap.kamar'])
            ->where('no_rkm_medis', $this->no_rkm_medis)
            ->orderByDesc('tgl_registrasi')
            ->orderByDesc('jam_reg')
            ->get();

        $riwayatSoapie = PemeriksaanRanap::with(['regPeriksa', 'pegawai'])
            ->whereIn('no_rawat', $noRawatList)
            ->orderByDesc('tgl_perawatan')
            ->orderByDesc('jam_rawat')
            ->get()
            ->groupBy('no_rawat');

        return view('livewire.modul.rawat-inap.sub-rawat-inap.riwayat-pasien', [
            'regPeriksa'       => $regPeriksa,
            'riwayatKunjungan' => $riwayatKunjungan,
            'riwayatSoapie'    => $riwayatSoapie,
        ]);
    }
}
