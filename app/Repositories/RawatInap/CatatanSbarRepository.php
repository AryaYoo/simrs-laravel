<?php

namespace App\Repositories\RawatInap;

use App\Models\CatatanSbar;
use Illuminate\Support\Facades\DB;

class CatatanSbarRepository
{
    /**
     * Get list of catatan SBAR by no_rawat
     */
    public function getByNoRawat($noRawat)
    {
        return CatatanSbar::with(['petugas', 'dokter'])
            ->where('no_rawat', $noRawat)
            ->orderBy('tanggal', 'desc')
            ->get()
            ->map(function ($item) {
                return [
                    'no_rawat'          => $item->no_rawat,
                    'tanggal'           => $item->tanggal ? $item->tanggal->format('Y-m-d H:i:s') : null,
                    'nip'               => $item->nip,
                    'kd_dokter'         => $item->kd_dokter,
                    'situation'         => $item->situation,
                    'background'        => $item->background,
                    'bg_riwayat_penyakit_dahulu' => $item->bg_riwayat_penyakit_dahulu,
                    'bg_riwayat_penyakit'        => $item->bg_riwayat_penyakit,
                    'bg_informasi_klinis'        => $item->bg_informasi_klinis,
                    'bg_penunjang'               => $item->bg_penunjang,
                    'bg_alergi'                  => $item->bg_alergi,
                    'bg_kesadaran'               => $item->bg_kesadaran,
                    'bg_tensi'                   => $item->bg_tensi,
                    'bg_nadi'                    => $item->bg_nadi,
                    'bg_respirasi'               => $item->bg_respirasi,
                    'bg_suhu'                    => $item->bg_suhu,
                    'bg_spo2'                    => $item->bg_spo2,
                    'bg_terapi'                  => $item->bg_terapi,
                    'assessment'        => $item->assessment,
                    'recommendation'    => $item->recommendation,
                    'advice'            => $item->advice,
                    'status_baca'       => $item->status_baca,
                    'status_konfirmasi' => $item->status_konfirmasi,
                    'status_verifikasi' => $item->status_verifikasi,
                    'petugas'           => [
                        'nama' => $item->petugas->nama ?? '-',
                    ],
                    'dokter'            => [
                        'nama' => $item->dokter->nm_dokter ?? '-',
                    ],
                ];
            })->toArray();
    }

    /**
     * Store new catatan SBAR record
     */
    public function store(array $data)
    {
        return DB::transaction(function () use ($data) {
            return CatatanSbar::create($data);
        });
    }

    /**
     * Update existing catatan SBAR record
     */
    public function update(string $noRawat, string $oldTanggal, array $data)
    {
        return DB::transaction(function () use ($noRawat, $oldTanggal, $data) {
            CatatanSbar::where('no_rawat', $noRawat)
                ->where('tanggal', $oldTanggal)
                ->update($data);
        });
    }

    /**
     * Delete catatan SBAR record
     */
    public function delete(string $noRawat, string $tanggal)
    {
        return DB::transaction(function () use ($noRawat, $tanggal) {
            CatatanSbar::where('no_rawat', $noRawat)
                ->where('tanggal', $tanggal)
                ->delete();
        });
    }
}
