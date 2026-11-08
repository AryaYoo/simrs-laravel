<?php

namespace App\Repositories\RawatJalan;

use App\Models\SkriningNutrisiAnak;
use Illuminate\Support\Facades\DB;

class SkriningNutrisiAnakRepository
{
    /**
     * Get list of skrining nutrisi anak by no_rawat
     */
    public function getByNoRawat(string $noRawat)
    {
        return SkriningNutrisiAnak::with(['petugas'])
            ->where('no_rawat', $noRawat)
            ->orderBy('tanggal', 'desc')
            ->get();
    }

    /**
     * Save (create or update) skrining nutrisi anak
     */
    public function save(array $data)
    {
        return DB::transaction(function () use ($data) {
            return SkriningNutrisiAnak::updateOrCreate(
                ['no_rawat' => $data['no_rawat']],
                $data
            );
        });
    }

    /**
     * Delete skrining nutrisi anak by no_rawat
     */
    public function delete(string $noRawat)
    {
        return DB::transaction(function () use ($noRawat) {
            return SkriningNutrisiAnak::where('no_rawat', $noRawat)->delete();
        });
    }
}

