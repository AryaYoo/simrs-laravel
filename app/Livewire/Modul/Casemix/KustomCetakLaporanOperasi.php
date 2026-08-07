<?php

namespace App\Livewire\Modul\Casemix;

use App\Repositories\Casemix\LaporanOperasiRepository;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class KustomCetakLaporanOperasi extends Component
{
    public $no_rawat;
    public $tanggal;

    public $laporan;
    public $operasi;

    public $tab = 'ranap'; // 'ranap' atau 'ralan'

    public $selectedSource = 'ranap'; // 'ranap', 'ralan', atau 'none'
    public $selectedTgl = null;
    public $selectedJam = null;

    public $pemeriksaanRanapList = [];
    public $pemeriksaanRalanList = [];

    public function mount($no_rawat, $tanggal = null)
    {
        $this->no_rawat = str_replace('-', '/', $no_rawat);
        if ($tanggal) {
            $this->tanggal = str_replace('_', ' ', $tanggal);
        }

        $this->laporan = LaporanOperasiRepository::getDetail($this->no_rawat, $this->tanggal);

        if (!$this->laporan) {
            abort(404, 'Data Laporan Operasi tidak ditemukan');
        }

        $this->operasi = LaporanOperasiRepository::getOperasiDetail($this->no_rawat);

        $this->loadPemeriksaan();
    }

    public function loadPemeriksaan()
    {
        // 1. Rawat Inap
        $this->pemeriksaanRanapList = DB::table('pemeriksaan_ranap as pr')
            ->leftJoin('pegawai as p', 'p.nik', '=', 'pr.nip')
            ->leftJoin('dokter as d', 'd.kd_dokter', '=', 'pr.nip')
            ->where('pr.no_rawat', $this->no_rawat)
            ->orderBy('pr.tgl_perawatan', 'desc')
            ->orderBy('pr.jam_rawat', 'desc')
            ->select('pr.*', 'p.nama as nama_petugas', 'd.nm_dokter')
            ->get()
            ->toArray();

        // 2. Rawat Jalan (Untuk no_rawat ini atau no_rkm_medis pasien yang sama)
        $noRkmMedis = $this->laporan->regPeriksa->no_rkm_medis ?? null;

        $queryRalan = DB::table('pemeriksaan_ralan as pr')
            ->join('reg_periksa as rp', 'rp.no_rawat', '=', 'pr.no_rawat')
            ->leftJoin('pegawai as p', 'p.nik', '=', 'pr.nip')
            ->leftJoin('dokter as d', 'd.kd_dokter', '=', 'pr.nip');

        if ($noRkmMedis) {
            $queryRalan->where('rp.no_rkm_medis', $noRkmMedis);
        } else {
            $queryRalan->where('pr.no_rawat', $this->no_rawat);
        }

        $this->pemeriksaanRalanList = $queryRalan
            ->orderBy('pr.tgl_perawatan', 'desc')
            ->orderBy('pr.jam_rawat', 'desc')
            ->select('pr.*', 'rp.no_rawat as no_rawat_ralan', 'p.nama as nama_petugas', 'd.nm_dokter')
            ->get()
            ->toArray();

        // Default pilih pemeriksaan ranap pertama jika ada
        if (!empty($this->pemeriksaanRanapList)) {
            $first = $this->pemeriksaanRanapList[0];
            $this->selectedSource = 'ranap';
            $this->selectedTgl = $first->tgl_perawatan;
            $this->selectedJam = $first->jam_rawat;
            $this->tab = 'ranap';
        } elseif (!empty($this->pemeriksaanRalanList)) {
            $first = $this->pemeriksaanRalanList[0];
            $this->selectedSource = 'ralan';
            $this->selectedTgl = $first->tgl_perawatan;
            $this->selectedJam = $first->jam_rawat;
            $this->tab = 'ralan';
        } else {
            $this->selectedSource = 'none';
        }
    }

    public function selectPemeriksaan($source, $tgl, $jam)
    {
        $this->selectedSource = $source;
        $this->selectedTgl = $tgl;
        $this->selectedJam = $jam;
    }

    public function disablePreSurgical()
    {
        $this->selectedSource = 'none';
        $this->selectedTgl = null;
        $this->selectedJam = null;
    }

    public function render()
    {
        // Temukan detail item yang dipilih untuk preview
        $selectedItem = null;
        if ($this->selectedSource === 'ranap') {
            foreach ($this->pemeriksaanRanapList as $item) {
                if ($item->tgl_perawatan == $this->selectedTgl && $item->jam_rawat == $this->selectedJam) {
                    $selectedItem = $item;
                    break;
                }
            }
        } elseif ($this->selectedSource === 'ralan') {
            foreach ($this->pemeriksaanRalanList as $item) {
                if ($item->tgl_perawatan == $this->selectedTgl && $item->jam_rawat == $this->selectedJam) {
                    $selectedItem = $item;
                    break;
                }
            }
        }

        $printUrl = route('modul.casemix.kustom-laporan-operasi.cetak', [
            str_replace('/', '-', $this->no_rawat),
            str_replace(' ', '_', $this->tanggal),
        ]);

        $printKhanzaUrl = route('modul.casemix.kustom-laporan-operasi.cetak-khanza', [
            str_replace('/', '-', $this->no_rawat),
            str_replace(' ', '_', $this->tanggal),
        ]);

        if ($this->selectedSource !== 'none' && $this->selectedTgl && $this->selectedJam) {
            $queryParams = '?source=' . $this->selectedSource . '&tgl=' . urlencode($this->selectedTgl) . '&jam=' . urlencode($this->selectedJam);
            $printUrl .= $queryParams;
            $printKhanzaUrl .= $queryParams;
        } else {
            $printUrl .= '?source=none';
            $printKhanzaUrl .= '?source=none';
        }

        return view('livewire.modul.casemix.kustom-cetak-laporan-operasi', [
            'selectedItem' => $selectedItem,
            'printUrl' => $printUrl,
            'printKhanzaUrl' => $printKhanzaUrl,
        ])->layout('layouts.app');

    }
}
