<?php

namespace App\Livewire\Modul\Casemix;

use App\Repositories\Casemix\LaporanOperasiRepository;
use Livewire\Component;

class KustomLaporanOperasiDetail extends Component
{
    public $no_rawat;
    public $tanggal;
    public $laporan;
    public $operasi;
    public $biayaObat = 0;
    public $staff = [];
    public $biayaPerawatan = 0;

    public function mount($no_rawat, $tanggal = null)
    {
        $this->no_rawat = str_replace('-', '/', $no_rawat);
        if ($tanggal) {
            $this->tanggal = str_replace('_', ' ', $tanggal);
        }

        $this->laporan = LaporanOperasiRepository::getDetail($this->no_rawat, $this->tanggal);

        if (!$this->laporan) {
            abort(404, 'Data Laporan Operasi tidak ditemukan.');
        }

        $this->operasi = LaporanOperasiRepository::getOperasiDetail($this->no_rawat);
        $this->biayaObat = LaporanOperasiRepository::getBiayaObat($this->no_rawat);

        if ($this->operasi) {
            // Resolve staff names
            $this->staff = [
                'asisten_operator1' => LaporanOperasiRepository::resolveStaffName($this->operasi->asisten_operator1),
                'asisten_operator2' => LaporanOperasiRepository::resolveStaffName($this->operasi->asisten_operator2),
                'asisten_operator3' => LaporanOperasiRepository::resolveStaffName($this->operasi->asisten_operator3),
                'instrumen'         => LaporanOperasiRepository::resolveStaffName($this->operasi->instrumen),
                'perawaat_resusitas'=> LaporanOperasiRepository::resolveStaffName($this->operasi->perawaat_resusitas),
                'asisten_anestesi'  => LaporanOperasiRepository::resolveStaffName($this->operasi->asisten_anestesi),
                'asisten_anestesi2' => LaporanOperasiRepository::resolveStaffName($this->operasi->asisten_anestesi2),
                'bidan'             => LaporanOperasiRepository::resolveStaffName($this->operasi->bidan),
                'bidan2'            => LaporanOperasiRepository::resolveStaffName($this->operasi->bidan2),
                'bidan3'            => LaporanOperasiRepository::resolveStaffName($this->operasi->bidan3),
                'perawat_luar'      => LaporanOperasiRepository::resolveStaffName($this->operasi->perawat_luar),
                'omloop'            => LaporanOperasiRepository::resolveStaffName($this->operasi->omloop),
                'omloop2'           => LaporanOperasiRepository::resolveStaffName($this->operasi->omloop2),
                'omloop3'           => LaporanOperasiRepository::resolveStaffName($this->operasi->omloop3),
                'omloop4'           => LaporanOperasiRepository::resolveStaffName($this->operasi->omloop4),
                'omloop5'           => LaporanOperasiRepository::resolveStaffName($this->operasi->omloop5),
            ];

            // Calculate total Biaya Perawatan (sum of all component costs in operasi table)
            $op = $this->operasi;
            $this->biayaPerawatan = (float) (
                ($op->biayaoperator1 ?? 0) +
                ($op->biayaoperator2 ?? 0) +
                ($op->biayaoperator3 ?? 0) +
                ($op->biayaasisten_operator1 ?? 0) +
                ($op->biayaasisten_operator2 ?? 0) +
                ($op->biayaasisten_operator3 ?? 0) +
                ($op->biayainstrumen ?? 0) +
                ($op->biayadokter_anak ?? 0) +
                ($op->biayaperawaat_resusitas ?? 0) +
                ($op->biayadokter_anestesi ?? 0) +
                ($op->biayaasisten_anestesi ?? 0) +
                ($op->biayaasisten_anestesi2 ?? 0) +
                ($op->biayabidan ?? 0) +
                ($op->biayabidan2 ?? 0) +
                ($op->biayabidan3 ?? 0) +
                ($op->biayaperawat_luar ?? 0) +
                ($op->biayaalat ?? 0) +
                ($op->biayasewaok ?? 0) +
                ($op->akomodasi ?? 0) +
                ($op->bagian_rs ?? 0) +
                ($op->biaya_omloop ?? 0) +
                ($op->biaya_omloop2 ?? 0) +
                ($op->biaya_omloop3 ?? 0) +
                ($op->biaya_omloop4 ?? 0) +
                ($op->biaya_omloop5 ?? 0) +
                ($op->biayasarpras ?? 0) +
                ($op->biaya_dokter_pjanak ?? 0) +
                ($op->biaya_dokter_umum ?? 0)
            );
        }
    }

    public function render()
    {
        return view('livewire.modul.casemix.kustom-laporan-operasi-detail')
            ->layout('layouts.app');
    }
}