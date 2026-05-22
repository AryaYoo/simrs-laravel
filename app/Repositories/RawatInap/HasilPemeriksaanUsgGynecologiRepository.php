<?php

namespace App\Repositories\RawatInap;

use App\Models\HasilPemeriksaanUsgGynecologi;
use Illuminate\Support\Facades\DB;

class HasilPemeriksaanUsgGynecologiRepository
{
    public function getByNoRawat($noRawat)
    {
        return HasilPemeriksaanUsgGynecologi::with(['dokter'])
            ->where('no_rawat', $noRawat)
            ->orderBy('tanggal', 'desc')
            ->get();
    }

    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {
            return HasilPemeriksaanUsgGynecologi::create($data);
        });
    }

    public function update($noRawat, array $data)
    {
        return DB::transaction(function () use ($noRawat, $data) {
            $record = HasilPemeriksaanUsgGynecologi::where('no_rawat', $noRawat)->first();
            if ($record) {
                $record->update($data);
                return $record;
            }
            return null;
        });
    }

    public function delete($noRawat)
    {
        return DB::transaction(function () use ($noRawat) {
            return HasilPemeriksaanUsgGynecologi::where('no_rawat', $noRawat)->delete();
        });
    }

    public function exists($noRawat)
    {
        return HasilPemeriksaanUsgGynecologi::where('no_rawat', $noRawat)->exists();
    }
}
