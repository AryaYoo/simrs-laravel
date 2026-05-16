<?php

namespace App\Repositories\RawatJalan;

use App\Models\PenilaianAwalKeperawatanIgd;
use App\Models\MasterMasalahKeperawatanIgd;
use App\Models\MasterRencanaKeperawatanIgd;
use Illuminate\Support\Facades\DB;

class PengkajianAwalIgdRepository
{
    /**
     * Get existing pengkajian data by no_rawat
     */
    public function getByNoRawat(string $noRawat): ?PenilaianAwalKeperawatanIgd
    {
        return PenilaianAwalKeperawatanIgd::with(['petugas', 'masalah', 'rencana'])
            ->where('no_rawat', $noRawat)
            ->first();
    }

    /**
     * Get all master masalah keperawatan
     */
    public function getMasterMasalah()
    {
        return MasterMasalahKeperawatanIgd::orderBy('kode_masalah')->get();
    }

    /**
     * Get rencana keperawatan by array of kode_masalah
     */
    public function getRencanaByMasalah(array $kodeMasalah)
    {
        return MasterRencanaKeperawatanIgd::whereIn('kode_masalah', $kodeMasalah)
            ->orderBy('kode_masalah')
            ->orderBy('kode_rencana')
            ->get();
    }

    /**
     * Store new pengkajian (insert to 3 tables in transaction)
     */
    public function store(array $data, array $masalahCodes, array $rencanaCodes)
    {
        return DB::transaction(function () use ($data, $masalahCodes, $rencanaCodes) {
            $pengkajian = PenilaianAwalKeperawatanIgd::create($data);

            // Insert masalah pivot
            foreach ($masalahCodes as $kode) {
                DB::table('penilaian_awal_keperawatan_igd_masalah')->insert([
                    'no_rawat' => $data['no_rawat'],
                    'kode_masalah' => $kode,
                ]);
            }

            // Insert rencana pivot
            foreach ($rencanaCodes as $kode) {
                DB::table('penilaian_awal_keperawatan_ralan_rencana_igd')->insert([
                    'no_rawat' => $data['no_rawat'],
                    'kode_rencana' => $kode,
                ]);
            }

            return $pengkajian;
        });
    }

    /**
     * Update existing pengkajian (update main + sync pivots in transaction)
     */
    public function update(string $noRawat, array $data, array $masalahCodes, array $rencanaCodes)
    {
        return DB::transaction(function () use ($noRawat, $data, $masalahCodes, $rencanaCodes) {
            PenilaianAwalKeperawatanIgd::where('no_rawat', $noRawat)->update($data);

            // Sync masalah: delete all then re-insert
            DB::table('penilaian_awal_keperawatan_igd_masalah')
                ->where('no_rawat', $noRawat)->delete();
            foreach ($masalahCodes as $kode) {
                DB::table('penilaian_awal_keperawatan_igd_masalah')->insert([
                    'no_rawat' => $noRawat,
                    'kode_masalah' => $kode,
                ]);
            }

            // Sync rencana: delete all then re-insert
            DB::table('penilaian_awal_keperawatan_ralan_rencana_igd')
                ->where('no_rawat', $noRawat)->delete();
            foreach ($rencanaCodes as $kode) {
                DB::table('penilaian_awal_keperawatan_ralan_rencana_igd')->insert([
                    'no_rawat' => $noRawat,
                    'kode_rencana' => $kode,
                ]);
            }
        });
    }
}
