<?php

namespace App\Livewire\Modul\RawatInap\SubRawatInap\RiwayatPasien;

use App\Repositories\RawatInap\RiwayatPasienRepository;
use Livewire\Component;

class Index extends Component
{
    public string $no_rawat;
    public string $no_rkm_medis;
    public $activeTab = 'kunjungan';

    public function mount($no_rawat)
    {
        $this->no_rawat = str_replace('-', '/', $no_rawat);
        $regPeriksa = RiwayatPasienRepository::getRegPeriksa($this->no_rawat);
        if (!$regPeriksa) {
            abort(404, 'Data registrasi tidak ditemukan.');
        }
        $this->no_rkm_medis = $regPeriksa->no_rkm_medis;
    }

    public function render()
    {
        $regPeriksa = RiwayatPasienRepository::getRegPeriksa($this->no_rawat);
        $riwayatKunjungan = RiwayatPasienRepository::getRiwayatKunjungan($this->no_rkm_medis);
        $kunjunganDetail = RiwayatPasienRepository::formatKunjunganDetail($riwayatKunjungan, $this->no_rawat);
        $riwayatSoapie = RiwayatPasienRepository::formatRiwayatSoapie($riwayatKunjungan);
        $riwayatPenjualan = RiwayatPasienRepository::getRiwayatPenjualan($this->no_rkm_medis);

        return view('livewire.modul.rawat-inap.sub-rawat-inap.riwayat-pasien.index', [
            'regPeriksa'       => $regPeriksa,
            'riwayatKunjungan' => $riwayatKunjungan,
            'kunjunganDetail'  => $kunjunganDetail,
            'riwayatSoapie'    => $riwayatSoapie,
            'riwayatPenjualan' => $riwayatPenjualan,
        ]);
    }
}
