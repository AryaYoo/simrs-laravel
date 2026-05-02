<?php

namespace App\Repositories\RawatJalan;

use Illuminate\Support\Facades\DB;
use App\Models\CatatanPerawatan;

class CatatanDokterRepository
{
    /**
     * Get All Catatan Dokter for a specific registration
     */
    public static function getRiwayatCatatan(string $no_rawat)
    {
        return CatatanPerawatan::with(['regPeriksa.pasien:no_rkm_medis,nm_pasien', 'dokter:kd_dokter,nm_dokter'])
            ->where('no_rawat', $no_rawat)
            ->orderBy('tanggal', 'desc')
            ->orderBy('jam', 'desc')
            ->get()
            ->map(fn($item) => [
                'tanggal' => $item->tanggal,
                'jam' => $item->jam,
                'no_rawat' => $item->no_rawat,
                'no_r_m' => $item->regPeriksa->pasien->no_rkm_medis ?? '-',
                'nm_pasien' => $item->regPeriksa->pasien->nm_pasien ?? '-',
                'kd_dokter' => $item->kd_dokter,
                'nm_dokter' => $item->dokter->nm_dokter ?? '-',
                'catatan' => $item->catatan,
            ]);
    }

    /**
     * Save new Catatan Dokter
     */
    public static function saveCatatan(array $data)
    {
        return DB::transaction(function () use ($data) {
            return CatatanPerawatan::create([
                'no_rawat' => $data['no_rawat'],
                'tanggal' => $data['tanggal'],
                'jam' => $data['jam'],
                'kd_dokter' => $data['kd_dokter'],
                'catatan' => $data['catatan']
            ]);
        });
    }

    /**
     * Update existing Catatan Dokter
     * Because the table lacks a single PK, we delete the old record and insert the new one,
     * maintaining the same timestamp if necessary, or update using where clauses.
     */
    public static function updateCatatan(array $oldData, array $newData)
    {
        return DB::transaction(function () use ($oldData, $newData) {
            CatatanPerawatan::where('no_rawat', $oldData['no_rawat'])
                ->where('tanggal', $oldData['tanggal'])
                ->where('jam', $oldData['jam'])
                ->where('kd_dokter', $oldData['kd_dokter'])
                ->delete();

            return CatatanPerawatan::create([
                'no_rawat' => $newData['no_rawat'],
                'tanggal' => $newData['tanggal'],
                'jam' => $newData['jam'],
                'kd_dokter' => $newData['kd_dokter'],
                'catatan' => $newData['catatan']
            ]);
        });
    }

    /**
     * Delete Catatan Dokter
     */
    public static function deleteCatatan(string $no_rawat, string $tanggal, string $jam, string $kd_dokter)
    {
        return DB::transaction(function () use ($no_rawat, $tanggal, $jam, $kd_dokter) {
            return CatatanPerawatan::where('no_rawat', $no_rawat)
                ->where('tanggal', $tanggal)
                ->where('jam', $jam)
                ->where('kd_dokter', $kd_dokter)
                ->delete();
        });
    }
}
