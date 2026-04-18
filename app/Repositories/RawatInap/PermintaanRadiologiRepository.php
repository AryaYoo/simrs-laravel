<?php

namespace App\Repositories\RawatInap;

use Illuminate\Support\Facades\DB;
use App\Models\JnsPerawatanRadiologi;
use App\Models\PermintaanRadiologi as PermintaanRadiologiModel;
use App\Models\Dokter;

class PermintaanRadiologiRepository
{
    /**
     * Get Predicted Order Number 
     */
    public static function getPredictedOrderNo()
    {
        $dateStr = date('Ymd');
        $prefix = 'PR';

        $lastOrder = DB::table('permintaan_radiologi')
            ->where('noorder', 'like', $prefix . $dateStr . '%')
            ->orderBy('noorder', 'desc')
            ->first();

        if ($lastOrder) {
            $lastNum = (int) substr($lastOrder->noorder, -4);
            $nextNum = str_pad($lastNum + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $nextNum = '0001';
        }

        return $prefix . $dateStr . $nextNum;
    }

    /**
     * Get List Radiologi Tests
     */
    public static function getPemeriksaanList(string $search = '', int $perPage = 25)
    {
        $query = JnsPerawatanRadiologi::where('status', '1');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nm_perawatan', 'like', '%' . $search . '%')
                    ->orWhere('kd_jenis_prw', 'like', '%' . $search . '%');
            });
        }

        return $query->paginate($perPage, ['*'], 'pemeriksaanPage');
    }

    /**
     * Get History
     */
    public static function getHistory(string $no_rawat)
    {
        return PermintaanRadiologiModel::with(['dokter', 'detailPemeriksaan.pemeriksaan'])
            ->where('no_rawat', $no_rawat)
            ->orderBy('tgl_permintaan', 'desc')
            ->orderBy('jam_permintaan', 'desc')
            ->get();
    }

    /**
     * Get Dropdown / List Dokter
     */
    public static function getListDokter(string $search = '', int $limit = 20)
    {
        $query = Dokter::where('status', '1');
        if (!empty($search)) {
            $query->where('nm_dokter', 'like', '%' . $search . '%');
        }
        return $query->limit($limit)->get()->toArray();
    }

    /**
     * Save the Radiology order (Transaction + Lock)
     */
    public static function savePermintaan(array $data)
    {
        return DB::transaction(function () use ($data) {
            $dateStr = date('Ymd');
            $prefix = 'PR';

            // Atomic lock to guarantee unicity
            $lastOrder = DB::table('permintaan_radiologi')
                ->where('noorder', 'like', $prefix . $dateStr . '%')
                ->orderBy('noorder', 'desc')
                ->lockForUpdate()
                ->first();

            if ($lastOrder) {
                $lastNum = (int) substr($lastOrder->noorder, -4);
                $nextNum = str_pad($lastNum + 1, 4, '0', STR_PAD_LEFT);
            } else {
                $nextNum = '0001';
            }

            $noorder = $prefix . $dateStr . $nextNum;

            if ($data['auto_waktu']) {
                $jamF = date('H:i:s');
            } else {
                $jamF = sprintf('%02d:%02d:%02d', $data['jam_jam'], $data['jam_menit'], $data['jam_detik']);
            }

            // Insert Header
            DB::table('permintaan_radiologi')->insert([
                'noorder' => $noorder,
                'no_rawat' => $data['no_rawat'],
                'tgl_permintaan' => $data['tgl_permintaan'],
                'jam_permintaan' => $jamF,
                'tgl_sampel' => '1000-01-01',
                'jam_sampel' => '00:00:00',
                'tgl_hasil' => '1000-01-01',
                'jam_hasil' => '00:00:00',
                'dokter_perujuk' => $data['kd_dokter_perujuk'],
                'status' => 'ranap',
                'informasi_tambahan' => $data['informasi_tambahan'] ?: '-',
                'diagnosa_klinis' => $data['diagnosa_klinis'] ?: '-'
            ]);

            // Insert Details
            foreach ($data['selectedTests'] as $kd) {
                DB::table('permintaan_pemeriksaan_radiologi')->insert([
                    'noorder' => $noorder,
                    'kd_jenis_prw' => $kd,
                    'stts_bayar' => 'Belum'
                ]);
            }

            return $noorder;
        });
    }

    /**
     * Batal Permintaan
     */
    public static function batalPermintaan(string $noorder)
    {
        DB::transaction(function () use ($noorder) {
            $permintaan = DB::table('permintaan_radiologi')
                ->where('noorder', $noorder)
                ->lockForUpdate()
                ->first();

            if (!$permintaan) {
                throw new \Exception("Data permintaan tidak ditemukan.");
            }

            if ($permintaan->tgl_sampel != '1000-01-01' && $permintaan->tgl_sampel != '0000-00-00') {
                throw new \Exception("Permintaan tidak dapat dibatalkan karena sudah diproses oleh unit Radiologi.");
            }

            DB::table('permintaan_pemeriksaan_radiologi')->where('noorder', $noorder)->delete();
            DB::table('permintaan_radiologi')->where('noorder', $noorder)->delete();
        });
    }
}
