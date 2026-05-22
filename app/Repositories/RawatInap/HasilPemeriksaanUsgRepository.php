<?php

namespace App\Repositories\RawatInap;

use App\Models\HasilPemeriksaanUsg;
use Illuminate\Support\Facades\DB;

class HasilPemeriksaanUsgRepository
{
    /**
     * Mengambil data USG untuk satu kunjungan (no_rawat)
     */
    public function getByNoRawat($noRawat)
    {
        return HasilPemeriksaanUsg::with(['dokter'])
            ->where('no_rawat', $noRawat)
            ->orderBy('tanggal', 'desc')
            ->get();
    }

    /**
     * Menyimpan data baru
     */
    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {
            return HasilPemeriksaanUsg::create($data);
        });
    }

    /**
     * Mengubah data
     */
    public function update($noRawat, array $data)
    {
        return DB::transaction(function () use ($noRawat, $data) {
            $record = HasilPemeriksaanUsg::where('no_rawat', $noRawat)->first();
            
            if ($record) {
                $record->update($data);
                return $record;
            }
            
            return null;
        });
    }

    /**
     * Menghapus data
     */
    public function delete($noRawat)
    {
        return DB::transaction(function () use ($noRawat) {
            return HasilPemeriksaanUsg::where('no_rawat', $noRawat)->delete();
        });
    }

    /**
     * Cek apakah data sudah ada
     */
    public function exists($noRawat)
    {
        return HasilPemeriksaanUsg::where('no_rawat', $noRawat)->exists();
    }
}
