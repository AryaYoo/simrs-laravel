<?php

namespace App\Repositories\RawatInap;

use App\Models\CatatanAdimeGizi;
use Illuminate\Support\Facades\DB;

class CatatanAdimeGiziRepository
{
    /**
     * Get list of catatan ADIME Gizi by no_rawat
     */
    public function getByNoRawat($noRawat)
    {
        return CatatanAdimeGizi::with(['petugas'])
            ->where('no_rawat', $noRawat)
            ->orderBy('tanggal', 'desc')
            ->get()
            ->map(function ($item) {
                return [
                    'no_rawat'    => $item->no_rawat,
                    'tanggal'     => $item->tanggal ? $item->tanggal->format('Y-m-d H:i:s') : null,
                    'asesmen'     => $item->asesmen,
                    'diagnosis'   => $item->diagnosis,
                    'intervensi'  => $item->intervensi,
                    'monitoring'  => $item->monitoring,
                    'evaluasi'    => $item->evaluasi,
                    'instruksi'   => $item->instruksi,
                    'nip'         => $item->nip,
                    'petugas'     => [
                        'nama' => $item->petugas->nama ?? '-',
                    ],
                ];
            })->toArray();
    }

    /**
     * Store new catatan ADIME record
     */
    public function store(array $data)
    {
        return DB::transaction(function () use ($data) {
            return CatatanAdimeGizi::create($data);
        });
    }

    /**
     * Update existing catatan ADIME record
     */
    public function update(string $noRawat, string $oldTanggal, array $data)
    {
        return DB::transaction(function () use ($noRawat, $oldTanggal, $data) {
            CatatanAdimeGizi::where('no_rawat', $noRawat)
                ->where('tanggal', $oldTanggal)
                ->update($data);
        });
    }

    /**
     * Delete catatan ADIME record
     */
    public function delete(string $noRawat, string $tanggal)
    {
        return DB::transaction(function () use ($noRawat, $tanggal) {
            CatatanAdimeGizi::where('no_rawat', $noRawat)
                ->where('tanggal', $tanggal)
                ->delete();
        });
    }
}
