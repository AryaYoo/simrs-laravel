<?php

namespace App\Repositories\RawatInap;

use Illuminate\Support\Facades\DB;
use App\Models\Kamar;
use App\Models\KamarInap;
use App\Models\Penyakit;

class CheckOutRepository
{
    /**
     * Search ICD-10 codes.
     */
    public static function searchIcd(string $searchIcd, int $limit = 20)
    {
        if (strlen($searchIcd) >= 3) {
            return Penyakit::where('kd_penyakit', 'like', '%' . $searchIcd . '%')
                ->orWhere('nm_penyakit', 'like', '%' . $searchIcd . '%')
                ->limit($limit)
                ->get();
        }
        return collect([]);
    }

    /**
     * Checkout Patient (Atomic Save)
     * $data requires:
     * - currentKamarInapArray
     * - tgl_keluar, jam_keluar, lama, total_biaya, stts_pulang, kd_penyakit_akhir
     */
    public static function saveCheckOut(array $data)
    {
        DB::beginTransaction();
        try {
            // Update KamarInap directly
            $updated = KamarInap::where([
                'no_rawat' => $data['currentKamarInapArray']['no_rawat'],
                'kd_kamar' => $data['currentKamarInapArray']['kd_kamar'],
                'tgl_masuk' => $data['currentKamarInapArray']['tgl_masuk'],
                'jam_masuk' => $data['currentKamarInapArray']['jam_masuk'],
            ])->update([
                'tgl_keluar' => $data['tgl_keluar'],
                'jam_keluar' => $data['jam_keluar'],
                'lama' => $data['lama'],
                'ttl_biaya' => $data['total_biaya'],
                'stts_pulang' => $data['stts_pulang'],
                'diagnosa_akhir' => $data['kd_penyakit_akhir'] ?: '-',
            ]);

            if (!$updated) {
                throw new \Exception("Gagal memperbarui data inap aktif. Data mungkin sudah berubah.");
            }

            // Update Kamar Status to KOSONG
            Kamar::where('kd_kamar', $data['currentKamarInapArray']['kd_kamar'])->update(['status' => 'KOSONG']);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
