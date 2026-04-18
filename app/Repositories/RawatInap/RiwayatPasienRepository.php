<?php

namespace App\Repositories\RawatInap;

use App\Models\RegPeriksa;
use App\Models\Penjualan;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class RiwayatPasienRepository
{
    /**
     * Get basic registration info for the patient.
     */
    public static function getRegPeriksa(string $no_rawat)
    {
        return RegPeriksa::with(['pasien.bahasa', 'pasien.cacatFisik'])
            ->find($no_rawat);
    }

    /**
     * Get all visitation history with its massive eager loads.
     */
    public static function getRiwayatKunjungan(string $no_rkm_medis)
    {
        return RegPeriksa::with([
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
            'resumePasienRanap.dokter',
            'hasilPemeriksaanUsg.dokter',
            'hasilPemeriksaanUsg.gambar'
        ])
            ->where('no_rkm_medis', $no_rkm_medis)
            ->orderByDesc('tgl_registrasi')
            ->orderByDesc('jam_reg')
            ->get();
    }

    /**
     * Parse and format the collection into visitation details (table rows).
     */
    public static function formatKunjunganDetail(Collection $riwayatKunjungan, string $current_no_rawat): Collection
    {
        $kunjunganDetail = collect();
        $riwayatKunjungan->each(function ($kunjungan) use ($kunjunganDetail, $current_no_rawat) {
            // Calculate Rounded Age
            $tglLahir = $kunjungan->pasien->tgl_lahir ?? null;
            $tglReg   = $kunjungan->tgl_registrasi;
            $umur     = $tglLahir && $tglReg
                ? Carbon::parse($tglLahir)->diffInYears(Carbon::parse($tglReg))
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
                'is_current'     => $kunjungan->no_rawat === $current_no_rawat,
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
                    'is_current'     => $kunjungan->no_rawat === $current_no_rawat,
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

        return $kunjunganDetail;
    }

    /**
     * Group SOAP records by visitation.
     */
    public static function formatRiwayatSoapie(Collection $riwayatKunjungan): Collection
    {
        return $riwayatKunjungan->map(function ($kunjungan) {
            $ranap = $kunjungan->pemeriksaanRanap->map(function ($item) use ($kunjungan) {
                $item->status_lanjut = $kunjungan->status_lanjut;
                return $item;
            });
            $ralan = $kunjungan->pemeriksaanRalan->map(function ($item) use ($kunjungan) {
                $item->status_lanjut = $kunjungan->status_lanjut;
                return $item;
            });
            return $ranap->concat($ralan);
        })->collapse()->sortByDesc(function ($item) {
            return $item->tgl_perawatan . ' ' . $item->jam_rawat;
        })->groupBy('no_rawat');
    }

    /**
     * Get obat sales history.
     */
    public static function getRiwayatPenjualan(string $no_rkm_medis)
    {
        return Penjualan::with(['detailJual.barang', 'petugas', 'bangsal'])
            ->where('no_rkm_medis', $no_rkm_medis)
            ->orderByDesc('tgl_jual')
            ->get();
    }
}
