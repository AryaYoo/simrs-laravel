<?php

namespace App\Repositories\RawatInap;

use App\Models\PelaksanaanInformasiEdukasi;
use Illuminate\Support\Facades\DB;

class PelaksanaanInformasiEdukasiRepository
{
    /**
     * Get list of pelaksanaan informasi dan edukasi by no_rawat
     */
    public function getByNoRawat(string $noRawat)
    {
        return PelaksanaanInformasiEdukasi::with(['pegawai', 'petugas'])
            ->where('no_rawat', $noRawat)
            ->orderBy('tanggal', 'desc')
            ->get();
    }

    /**
     * Find specific record by no_rawat and tanggal
     */
    public function find(string $noRawat, string $tanggal)
    {
        return PelaksanaanInformasiEdukasi::with(['pegawai', 'petugas'])
            ->where('no_rawat', $noRawat)
            ->where('tanggal', $tanggal)
            ->first();
    }

    /**
     * Store new record
     */
    public function store(array $data)
    {
        return DB::transaction(function () use ($data) {
            return PelaksanaanInformasiEdukasi::create($data);
        });
    }

    /**
     * Update existing record
     */
    public function update(string $noRawat, string $oldTanggal, array $data)
    {
        return DB::transaction(function () use ($noRawat, $oldTanggal, $data) {
            return PelaksanaanInformasiEdukasi::where('no_rawat', $noRawat)
                ->where('tanggal', $oldTanggal)
                ->update($data);
        });
    }

    /**
     * Delete record
     */
    public function delete(string $noRawat, string $tanggal)
    {
        return DB::transaction(function () use ($noRawat, $tanggal) {
            return PelaksanaanInformasiEdukasi::where('no_rawat', $noRawat)
                ->where('tanggal', $tanggal)
                ->delete();
        });
    }
}

