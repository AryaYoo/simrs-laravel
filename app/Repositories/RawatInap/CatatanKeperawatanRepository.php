<?php

namespace App\Repositories\RawatInap;

use App\Models\CatatanKeperawatanRanap;

class CatatanKeperawatanRepository
{
    public function getByNoRawat($noRawat)
    {
        return CatatanKeperawatanRanap::with(['petugas', 'regPeriksa.pasien'])
            ->where('no_rawat', $noRawat)
            ->orderBy('tanggal', 'desc')
            ->orderBy('jam', 'desc')
            ->get();
    }

    public function store($data)
    {
        return \DB::transaction(function () use ($data) {
            return CatatanKeperawatanRanap::create($data);
        });
    }

    public function delete(string $noRawat, string $tanggal, string $jam): bool
    {
        return \DB::transaction(function () use ($noRawat, $tanggal, $jam) {
            return CatatanKeperawatanRanap::where('no_rawat', $noRawat)
                ->where('tanggal', $tanggal)
                ->where('jam', $jam)
                ->delete();
        });
    }

    public function update(string $noRawat, string $oldTanggal, string $oldJam, array $data): bool
    {
        return \DB::transaction(function () use ($noRawat, $oldTanggal, $oldJam, $data) {
            return CatatanKeperawatanRanap::where('no_rawat', $noRawat)
                ->where('tanggal', $oldTanggal)
                ->where('jam', $oldJam)
                ->update($data) > 0;
        });
    }
}
