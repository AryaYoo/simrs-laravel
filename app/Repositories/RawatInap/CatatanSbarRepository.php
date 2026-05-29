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
                    'assessment'        => $item->assessment,
                    'recommendation'    => $item->recommendation,
                    'advice'            => $item->advice,
                    'status_baca'       => $item->status_baca,
                    'status_konfirmasi' => $item->status_konfirmasi,
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
