<?php

namespace App\Repositories\RawatInap;

use App\Models\SkriningNutrisiDewasa;
use Illuminate\Support\Facades\DB;

class SkriningNutrisiDewasaRepository
{
    /**
     * Get list of skrining nutrisi dewasa by no_rawat
     */
    public function getByNoRawat(string $noRawat)
    {
        return SkriningNutrisiDewasa::with(['petugas'])
            ->where('no_rawat', $noRawat)
            ->orderBy('tanggal', 'desc')
            ->get();
    }

    /**
     * Save (create or update) skrining nutrisi dewasa
     */
    public function save(array $data)
    {
        return DB::transaction(function () use ($data) {
            return SkriningNutrisiDewasa::updateOrCreate(
                ['no_rawat' => $data['no_rawat']],
                $data
            );
        });
    }

    /**
     * Delete skrining nutrisi dewasa by no_rawat
     */
    public function delete(string $noRawat)
    {
        return DB::transaction(function () use ($noRawat) {
            return SkriningNutrisiDewasa::where('no_rawat', $noRawat)->delete();
        });
    }
}
