<?php

namespace App\Repositories\RawatInap;

use App\Models\CatatanObservasiRanapKebidanan;
use Illuminate\Support\Facades\DB;

class CatatanObservasiRanapKebidananRepository
{
    /**
     * Get list of observasi by no_rawat
     */
    public function getByNoRawat($noRawat)
    {
        return CatatanObservasiRanapKebidanan::with(['petugas'])
            ->where('no_rawat', $noRawat)
            ->orderBy('tgl_perawatan', 'desc')
            ->orderBy('jam_rawat', 'desc')
            ->get()
            ->map(function ($item) {
                return [
                    'no_rawat'      => $item->no_rawat,
                    'tgl_perawatan' => $item->tgl_perawatan,
                    'jam_rawat'     => $item->jam_rawat,
                    'gcs'           => $item->gcs,
                    'td'            => $item->td,
                    'hr'            => $item->hr,
                    'rr'            => $item->rr,
                    'suhu'          => $item->suhu,
                    'spo2'          => $item->spo2,
                    'kontraksi'     => $item->kontraksi,
                    'bjj'           => $item->bjj,
                    'ppv'           => $item->ppv,
                    'vt'            => $item->vt,
                    'nip'           => $item->nip,
                    'petugas'       => [
                        'nama' => $item->petugas->nama ?? '-',
                    ],
                ];
            })->toArray();
    }

    /**
     * Store new observasi record
     */
    public function store(array $data)
    {
        return DB::transaction(function () use ($data) {
            return CatatanObservasiRanapKebidanan::create($data);
        });
    }

    /**
     * Update existing observasi record (composite key)
     */
    public function update(string $noRawat, string $oldTglPerawatan, string $oldJamRawat, array $data)
    {
        return DB::transaction(function () use ($noRawat, $oldTglPerawatan, $oldJamRawat, $data) {
            CatatanObservasiRanapKebidanan::where('no_rawat', $noRawat)
                ->where('tgl_perawatan', $oldTglPerawatan)
                ->where('jam_rawat', $oldJamRawat)
                ->update($data);
        });
    }

    /**
     * Delete observasi record (composite key)
     */
    public function delete(string $noRawat, string $tglPerawatan, string $jamRawat)
    {
        return DB::transaction(function () use ($noRawat, $tglPerawatan, $jamRawat) {
            CatatanObservasiRanapKebidanan::where('no_rawat', $noRawat)
                ->where('tgl_perawatan', $tglPerawatan)
                ->where('jam_rawat', $jamRawat)
                ->delete();
        });
    }
}
