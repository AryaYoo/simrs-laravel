<?php

namespace App\Repositories\RawatJalan;

use App\Models\CatatanObservasiIgd;
use Illuminate\Support\Facades\DB;

class CatatanObservasiIgdRepository
{
    /**
     * Get list of observasi by no_rawat
     */
    public function getByNoRawat($noRawat)
    {
        return CatatanObservasiIgd::with(['petugas'])
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
            return CatatanObservasiIgd::create($data);
        });
    }

    /**
     * Update existing observasi record (composite key)
     */
    public function update(string $noRawat, string $oldTglPerawatan, string $oldJamRawat, array $data)
    {
        return DB::transaction(function () use ($noRawat, $oldTglPerawatan, $oldJamRawat, $data) {
            CatatanObservasiIgd::where('no_rawat', $noRawat)
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
            CatatanObservasiIgd::where('no_rawat', $noRawat)
                ->where('tgl_perawatan', $tglPerawatan)
                ->where('jam_rawat', $jamRawat)
                ->delete();
        });
    }
}
