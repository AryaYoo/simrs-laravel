<?php

namespace App\Repositories\RawatInap;

use Illuminate\Support\Facades\DB;
use App\Models\Kamar;
use App\Models\KamarInap;

class PindahKamarRepository
{
    /**
     * Search Kamar matching by kode kamar or bangsal name.
     */
    public static function searchKamar(string $search, int $limit = 50)
    {
        $query = Kamar::with('bangsal');
        if (strlen($search) >= 2) {
            $query->where('kd_kamar', 'like', '%' . $search . '%')
                  ->orWhereHas('bangsal', function($q) use ($search) {
                      $q->where('nm_bangsal', 'like', '%' . $search . '%');
                  });
        }
        return $query->limit($limit)->get();
    }

    /**
     * Move Patient (Atomic Save)
     * $data requires:
     * - no_rawat, kd_kamar, currentKamarInapArray
     * - tgl_pindah, jam_pindah, pilihan, trf_kamar, lama, total
     */
    public static function savePindahKamar(array $data)
    {
        DB::beginTransaction();
        try {
            $activeModel = KamarInap::where([
                'no_rawat' => $data['currentKamarInapArray']['no_rawat'],
                'kd_kamar' => $data['currentKamarInapArray']['kd_kamar'],
                'tgl_masuk' => $data['currentKamarInapArray']['tgl_masuk'],
                'jam_masuk' => $data['currentKamarInapArray']['jam_masuk'],
            ])->lockForUpdate()->first();

            if (!$activeModel) {
                throw new \Exception("Model kamar_inap tidak ditemukan untuk sinkronisasi.");
            }

            switch ($data['pilihan']) {
                case 1:
                    $tgl_asal = $activeModel->tgl_masuk;
                    $jam_asal = $activeModel->jam_masuk;
                    
                    Kamar::where('kd_kamar', $activeModel->kd_kamar)->update(['status' => 'KOSONG']);
                    $activeModel->delete();

                    KamarInap::create([
                        'no_rawat' => $data['no_rawat'],
                        'kd_kamar' => $data['kd_kamar'],
                        'trf_kamar' => $data['trf_kamar'],
                        'diagnosa_awal' => $data['currentKamarInapArray']['diagnosa_awal'] ?? '-',
                        'diagnosa_akhir' => '-',
                        'tgl_masuk' => $tgl_asal,
                        'jam_masuk' => $jam_asal,
                        'tgl_keluar' => '0000-00-00',
                        'jam_keluar' => '00:00:00',
                        'lama' => 0,
                        'ttl_biaya' => 0,
                        'stts_pulang' => '-',
                    ]);
                    break;

                case 2:
                    Kamar::where('kd_kamar', $activeModel->kd_kamar)->update(['status' => 'KOSONG']);
                    $activeModel->update([
                        'kd_kamar' => $data['kd_kamar'],
                        'trf_kamar' => $data['trf_kamar'],
                    ]);
                    break;

                case 3:
                case 4:
                    $finalRate = $activeModel->trf_kamar;
                    if ($data['pilihan'] == 4 && $data['trf_kamar'] > $finalRate) {
                        $finalRate = $data['trf_kamar'];
                    }

                    $activeModel->update([
                        'tgl_keluar' => $data['tgl_pindah'],
                        'jam_keluar' => $data['jam_pindah'],
                        'lama' => $data['lama'],
                        'trf_kamar' => $finalRate,
                        'ttl_biaya' => $data['total'],
                        'stts_pulang' => 'Pindah',
                    ]);

                    Kamar::where('kd_kamar', $activeModel->kd_kamar)->update(['status' => 'KOSONG']);

                    KamarInap::create([
                        'no_rawat' => $data['no_rawat'],
                        'kd_kamar' => $data['kd_kamar'],
                        'trf_kamar' => $data['trf_kamar'],
                        'diagnosa_awal' => $data['currentKamarInapArray']['diagnosa_awal'] ?? '-',
                        'diagnosa_akhir' => '-',
                        'tgl_masuk' => $data['tgl_pindah'],
                        'jam_masuk' => $data['jam_pindah'],
                        'tgl_keluar' => '0000-00-00',
                        'jam_keluar' => '00:00:00',
                        'lama' => 0,
                        'ttl_biaya' => 0,
                        'stts_pulang' => '-',
                    ]);
                    break;
            }

            // Update new room status to ISI
            Kamar::where('kd_kamar', $data['kd_kamar'])->update(['status' => 'ISI']);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
