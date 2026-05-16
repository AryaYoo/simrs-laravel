<?php

namespace App\Repositories\RawatJalan;

use App\Models\CatatanKeperawatanRalan;

class CatatanKeperawatanRepository
{
    public function getByNoRawat($noRawat)
    {
        return CatatanKeperawatanRalan::with(['petugas', 'regPeriksa.pasien'])
            ->where('no_rawat', $noRawat)
            ->orderBy('tanggal', 'desc')
            ->orderBy('jam', 'desc')
            ->get();
    }

    public function store($data)
    {
        return \DB::transaction(function () use ($data) {
            return CatatanKeperawatanRalan::create($data);
        });
    }

    public function delete(string $noRawat, string $tanggal, string $jam): bool
    {
        return \DB::transaction(function () use ($noRawat, $tanggal, $jam) {
            return CatatanKeperawatanRalan::where('no_rawat', $noRawat)
                ->where('tanggal', $tanggal)
                ->where('jam', $jam)
                ->delete();
        });
    }

    public function update(string $noRawat, string $oldTanggal, string $oldJam, array $data): bool
    {
        return \DB::transaction(function () use ($noRawat, $oldTanggal, $oldJam, $data) {
            return CatatanKeperawatanRalan::where('no_rawat', $noRawat)
                ->where('tanggal', $oldTanggal)
                ->where('jam', $oldJam)
                ->update($data) > 0;
        });
    }
}
