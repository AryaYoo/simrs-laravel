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

        $riwayatKunjungan = RegPeriksa::with([
            'dokter',
            'penjab',
            'poliklinik',
            'kamarInap.kamar.bangsal',
            'diagnosaPasien.penyakit',
            'prosedurPasien.icd9',
            'rawatInapDrpr.jnsPerawatan',
            'rawatInapDrpr.dokter',
            'rawatInapDrpr.petugas',
            'rawatInapDr.jnsPerawatan',
            'rawatInapDr.dokter',
            'rawatInapPr.jnsPerawatan',
            'rawatInapPr.petugas',
            'rawatJlDrpr.jnsPerawatan',
            'rawatJlDrpr.dokter',
            'rawatJlDrpr.petugas',
            'rawatJlDr.jnsPerawatan',
            'rawatJlDr.dokter',
            'rawatJlPr.jnsPerawatan',
            'rawatJlPr.petugas',
            'detailPeriksaLab.template',
            'detailPeriksaLab.jnsPerawatan',
            'periksaRadiologi.jnsPerawatan',
            'detailPemberianObat.barang',
            'pemeriksaanRanap.pegawai',
            'pemeriksaanRalan.pegawai',
            'bridgingSep',
            'resumePasien.dokter',
            'resumePasienRanap.dokter'
        ])
            ->where('no_rkm_medis', $this->no_rkm_medis)
            ->orderByDesc('tgl_registrasi')
            ->orderByDesc('jam_reg')
            ->get();

        $kunjunganDetail = collect();
        $riwayatKunjungan->each(function ($kunjungan) use ($kunjunganDetail) {
            // Calculate Rounded Age
            $tglLahir = $kunjungan->pasien->tgl_lahir ?? null;
            $tglReg   = $kunjungan->tgl_registrasi;
            $umur     = $tglLahir && $tglReg
                ? \Carbon\Carbon::parse($tglLahir)->diffInYears(\Carbon\Carbon::parse($tglReg))
                : '-';
            $kunjungan->umur_daftar = is_numeric($umur) ? $umur . ' Th' : $umur;

            // 1. Initial Entry (Clinic/Poli)
            $kunjunganDetail->push([
                'no_rawat'       => $kunjungan->no_rawat,
                'tgl'            => $kunjungan->tgl_registrasi,
                'jam'            => $kunjungan->jam_reg,
                'kd_dokter'      => $kunjungan->kd_dokter,
                'nm_dokter'      => $kunjungan->dokter->nm_dokter ?? '-',
                'umur'           => $kunjungan->umur_daftar,
                'lokasi'         => $kunjungan->poliklinik->nm_poli ?? $kunjungan->kd_poli,
                'png_jawab'      => $kunjungan->penjab->png_jawab ?? $kunjungan->kd_pj,
                'is_first'       => true,
                'is_current'     => $kunjungan->no_rawat === $this->no_rawat,
                'kd_pj'          => $kunjungan->kd_pj,
            ]);

            // 2. Room Entries (Mutasi Kamar)
            foreach ($kunjungan->kamarInap->sortBy('tgl_masuk')->sortBy('jam_masuk') as $kamar) {
                $kunjunganDetail->push([
                    'no_rawat'       => $kunjungan->no_rawat,
                    'tgl'            => $kamar->tgl_masuk,
                    'jam'            => $kamar->jam_masuk,
                    'kd_dokter'      => $kunjungan->kd_dokter,
                    'nm_dokter'      => $kunjungan->dokter->nm_dokter ?? '-',
                    'umur'           => $kunjungan->umur_daftar,
                    'lokasi'         => ($kamar->kamar->kd_kamar ?? '') . ' ' . ($kamar->kamar->bangsal->nm_bangsal ?? ''),
                    'png_jawab'      => $kunjungan->penjab->png_jawab ?? $kunjungan->kd_pj,
                    'is_first'       => false,
                    'is_current'     => $kunjungan->no_rawat === $this->no_rawat,
                    'kd_pj'          => $kunjungan->kd_pj,
                ]);
            }

            // Also keep tindakan_semua logic for the other tab
            $kunjungan->tindakan_semua = collect([])
                ->concat($kunjungan->rawatInapDrpr)
                ->concat($kunjungan->rawatInapDr)
                ->concat($kunjungan->rawatInapPr)
                ->concat($kunjungan->rawatJlDrpr)
                ->concat($kunjungan->rawatJlDr)
                ->concat($kunjungan->rawatJlPr)
                ->sortByDesc(function ($item) {
                    return $item->tgl_perawatan . ' ' . $item->jam_rawat;
                });
        });

        $riwayatSoapie = $riwayatKunjungan->map(function ($kunjungan) {
            $ranap = $kunjungan->pemeriksaanRanap->map(function($item) use ($kunjungan) {
                $item->status_lanjut = $kunjungan->status_lanjut;
                return $item;
            });
            $ralan = $kunjungan->pemeriksaanRalan->map(function($item) use ($kunjungan) {
                $item->status_lanjut = $kunjungan->status_lanjut;
                return $item;
            });
            return $ranap->concat($ralan);
        })->collapse()->sortByDesc(function ($item) {
            return $item->tgl_perawatan . ' ' . $item->jam_rawat;
        })->groupBy('no_rawat');

        return view('livewire.modul.rawat-inap.sub-rawat-inap.riwayat-pasien', [
            'regPeriksa'       => $regPeriksa,
            'riwayatKunjungan' => $riwayatKunjungan,
            'kunjunganDetail'  => $kunjunganDetail,
            'riwayatSoapie'    => $riwayatSoapie,
        ]);
    }
}
