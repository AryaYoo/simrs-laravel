<?php

namespace App\Repositories\RawatInap;

use App\Models\Kamar;
use App\Models\KamarInap;
use App\Models\PermintaanRanap;
use App\Models\Penyakit;
use Illuminate\Support\Facades\DB;
use Exception;

class PermintaanRanapRepository
{
    /**
     * Get pending antrian with pagination.
     */
    public function getAntrianPending($search, $perPage = 10)
    {
        $query = PermintaanRanap::with(['kamar', 'kamar.bangsal', 'regPeriksa', 'regPeriksa.pasien'])
            ->doesntHave('kamarInap');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('regPeriksa.pasien', function ($q2) use ($search) {
                    $q2->where('nm_pasien', 'like', '%' . $search . '%')
                        ->orWhere('no_rkm_medis', 'like', '%' . $search . '%');
                })->orWhere('no_rawat', 'like', '%' . $search . '%');
            });
        }

        return $query->paginate($perPage);
    }

    /**
     * Get count of pending antrian.
     */
    public function getPendingCount()
    {
        return PermintaanRanap::doesntHave('kamarInap')->count();
    }

    /**
     * Get riwayat permintaan ranap with pagination.
     */
    public function getRiwayat($tanggalMulai, $tanggalSelesai, $caraBayar, $search, $perPage = 10)
    {
        $query = PermintaanRanap::with([
                'kamar', 'kamar.bangsal',
                'regPeriksa', 'regPeriksa.pasien', 'regPeriksa.penjab',
                'kamarInap'
            ])
            ->whereBetween('tanggal', [$tanggalMulai, $tanggalSelesai]);

        if ($caraBayar) {
            $query->whereHas('regPeriksa', function ($q) use ($caraBayar) {
                $q->where('kd_pj', $caraBayar);
            });
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('regPeriksa.pasien', function ($q2) use ($search) {
                    $q2->where('nm_pasien', 'like', '%' . $search . '%')
                        ->orWhere('no_rkm_medis', 'like', '%' . $search . '%');
                })->orWhere('no_rawat', 'like', '%' . $search . '%');
            });
        }

        return $query->orderBy('tanggal', 'desc')->paginate($perPage);
    }

    /**
     * Get detail permintaan by no_rawat.
     */
    public function getDetail($noRawat)
    {
        return PermintaanRanap::with([
                'kamar', 'kamar.bangsal',
                'regPeriksa', 'regPeriksa.pasien',
                'regPeriksa.dokter', 'regPeriksa.poliklinik', 'regPeriksa.penjab',
                'kamarInap'
            ])
            ->where('no_rawat', $noRawat)
            ->first();
    }

    /**
     * Get data for Check In form.
     */
    public function getForCheckIn($noRawat)
    {
        return PermintaanRanap::with(['kamar', 'kamar.bangsal', 'regPeriksa', 'regPeriksa.pasien'])
            ->where('no_rawat', $noRawat)
            ->first();
    }

    /**
     * Search Kamar for lookup.
     */
    public function searchKamar($search, $limit = 50)
    {
        $query = Kamar::with('bangsal')->where('status', '!=', 'ISI');
        
        if (strlen($search) >= 2) {
            $query->where(function ($q) use ($search) {
                $q->where('kd_kamar', 'like', '%' . $search . '%')
                    ->orWhereHas('bangsal', function ($q2) use ($search) {
                        $q2->where('nm_bangsal', 'like', '%' . $search . '%');
                    });
            });
        }
        
        return $query->limit($limit)->get();
    }

    /**
     * Search Diagnosa for lookup.
     */
    public function searchDiagnosa($search, $limit = 50)
    {
        $query = Penyakit::query();
        
        if (strlen($search) >= 2) {
            $query->where('kd_penyakit', 'like', '%' . $search . '%')
                ->orWhere('nm_penyakit', 'like', '%' . $search . '%');
        }
        
        return $query->limit($limit)->get();
    }

    /**
     * Perform the Check In logic (transaction).
     */
    public function processCheckIn($data)
    {
        DB::beginTransaction();
        try {
            // Check Kamar availability again
            $kamar = Kamar::where('kd_kamar', $data['kd_kamar'])->first();
            if ($kamar && $kamar->status == 'ISI') {
                throw new Exception('Kamar sudah terisi, silakan pilih kamar lain.');
            }

            KamarInap::create([
                'no_rawat'      => $data['no_rawat'],
                'kd_kamar'      => $data['kd_kamar'],
                'trf_kamar'     => $data['tarif_kamar'],
                'diagnosa_awal' => $data['diagnosa_awal'] ?: '-',
                'diagnosa_akhir'=> '-',
                'tgl_masuk'     => $data['tanggal_masuk'],
                'jam_masuk'     => $data['jam_masuk'],
                'tgl_keluar'    => '0000-00-00',
                'jam_keluar'    => '00:00:00',
                'lama'          => $data['lama_inap'],
                'ttl_biaya'     => $data['tarif_kamar'] * $data['lama_inap'],
                'stts_pulang'   => '-',
            ]);

            Kamar::where('kd_kamar', $data['kd_kamar'])->update(['status' => 'ISI']);

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
