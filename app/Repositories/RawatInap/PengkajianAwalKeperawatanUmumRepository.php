<?php

namespace App\Repositories\RawatInap;

use App\Models\PenilaianAwalKeperawatanRanap;
use App\Models\MasterMasalahKeperawatan;
use App\Models\MasterRencanaKeperawatan;
use Illuminate\Support\Facades\DB;

class PengkajianAwalKeperawatanUmumRepository
{
    /**
     * Get existing pengkajian data by no_rawat
     */
    public function getByNoRawat(string $noRawat): ?PenilaianAwalKeperawatanRanap
    {
        return PenilaianAwalKeperawatanRanap::with(['petugas1', 'petugas2', 'dokter', 'masalah', 'detailRencana'])
            ->where('no_rawat', $noRawat)
            ->first();
    }

    /**
     * Get all master masalah keperawatan (umum, bukan IGD)
     */
    public function getMasterMasalah()
    {
        return MasterMasalahKeperawatan::orderBy('kode_masalah')->get();
    }

    /**
     * Get rencana keperawatan by array of kode_masalah
     */
    public function getRencanaByMasalah(array $kodeMasalah)
    {
        return MasterRencanaKeperawatan::whereIn('kode_masalah', $kodeMasalah)
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
            $pengkajian = PenilaianAwalKeperawatanRanap::create($data);

            // Insert masalah pivot
            foreach ($masalahCodes as $kode) {
                DB::table('penilaian_awal_keperawatan_ranap_masalah')->insert([
                    'no_rawat' => $data['no_rawat'],
                    'kode_masalah' => $kode,
                ]);
            }

            // Insert rencana pivot
            foreach ($rencanaCodes as $kode) {
                DB::table('penilaian_awal_keperawatan_ranap_rencana')->insert([
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
            PenilaianAwalKeperawatanRanap::where('no_rawat', $noRawat)->update($data);

            // Sync masalah: delete all then re-insert
            DB::table('penilaian_awal_keperawatan_ranap_masalah')
                ->where('no_rawat', $noRawat)->delete();
            foreach ($masalahCodes as $kode) {
                DB::table('penilaian_awal_keperawatan_ranap_masalah')->insert([
                    'no_rawat' => $noRawat,
                    'kode_masalah' => $kode,
                ]);
            }

            // Sync rencana: delete all then re-insert
            DB::table('penilaian_awal_keperawatan_ranap_rencana')
                ->where('no_rawat', $noRawat)->delete();
            foreach ($rencanaCodes as $kode) {
                DB::table('penilaian_awal_keperawatan_ranap_rencana')->insert([
                    'no_rawat' => $noRawat,
                    'kode_rencana' => $kode,
                ]);
            }
        });
    }

    /**
     * Delete pengkajian and its pivots
     */
    public function delete(string $noRawat)
    {
        return DB::transaction(function () use ($noRawat) {
            DB::table('penilaian_awal_keperawatan_ranap_masalah')
                ->where('no_rawat', $noRawat)->delete();
            DB::table('penilaian_awal_keperawatan_ranap_rencana')
                ->where('no_rawat', $noRawat)->delete();
            PenilaianAwalKeperawatanRanap::where('no_rawat', $noRawat)->delete();
        });
    }
}
