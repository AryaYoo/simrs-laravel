<?php

namespace App\Repositories\RawatInap;

use Illuminate\Support\Facades\DB;
use App\Models\PermintaanLab;
use App\Models\PermintaanLabPa;
use App\Models\JnsPerawatanLab;
use App\Models\TemplateLaboratorium;
use App\Models\Dokter;

class PermintaanLabRepository
{
    /**
     * Get Predicted Order Number (simulated safely without locks)
     */
    public static function getPredictedOrderNo(string $kategori)
    {
        $dateStr = date('Ymd');
        $prefix = $kategori; // PK, PA, or MB
        $mainTable = $kategori === 'PA' ? 'permintaan_labpa' : 'permintaan_lab';
        
        $lastOrder = DB::table($mainTable)
            ->where('noorder', 'like', $prefix . $dateStr . '%')
            ->orderBy('noorder', 'desc')
            ->first();

        // Fallback for PA since Khanza sometimes mix them
        if ($kategori === 'PA' && !$lastOrder) {
            $lastOrder = DB::table('permintaan_lab')
                ->where('noorder', 'like', 'PA' . $dateStr . '%')
                ->orderBy('noorder', 'desc')
                ->first();
        }

        if ($lastOrder) {
            $lastNum = (int) substr($lastOrder->noorder, -4);
            $nextNum = str_pad($lastNum + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $nextNum = '0001';
        }

        return $prefix . $dateStr . $nextNum;
    }

    /**
     * Get Master Laboratorium List
     */
    public static function getPemeriksaanList(string $kategori, string $search = '', int $perPage = 15)
    {
        $query = JnsPerawatanLab::where('status', '1')
            ->where('kategori', $kategori);

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nm_perawatan', 'like', '%' . $search . '%')
                  ->orWhere('kd_jenis_prw', 'like', '%' . $search . '%');
            });
        }

        return $query->paginate($perPage, ['*'], 'masterPage');
    }

    /**
     * Get Detailed Parameters for Selected Tests
     */
    public static function getDetailParameters(string $kategori, array $selectedTests, string $searchDetail = '')
    {
        if (empty($selectedTests)) return collect([]);

        $query = TemplateLaboratorium::whereIn('kd_jenis_prw', $selectedTests)
            ->whereHas('pemeriksaanHeader', function($q) use ($kategori) {
                $q->where('kategori', $kategori);
            });

        if ($searchDetail) {
            $query->where('Pemeriksaan', 'like', '%' . $searchDetail . '%');
        }

        return $query->orderBy('kd_jenis_prw')->orderBy('urut')->get();
    }

    /**
     * Get History for Both PK & PA
     */
    public static function getHistory(string $no_rawat)
    {
        $historyPK = PermintaanLab::with(['dokter', 'detailPemeriksaan.pemeriksaan'])
            ->where('no_rawat', $no_rawat)
            ->get()
            ->map(function($item) {
                $item->tipe = 'PK';
                return $item;
            });

        $historyPA = PermintaanLabPa::with(['dokter', 'detailPemeriksaan.pemeriksaan'])
            ->where('no_rawat', $no_rawat)
            ->get()
            ->map(function($item) {
                $item->tipe = 'PA';
                return $item;
            });

        return $historyPK->concat($historyPA)
            ->sortByDesc(function($item) {
                return $item->tgl_permintaan . ' ' . $item->jam_permintaan;
            })
            ->values();
    }

    /**
     * Get Doctor List
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
     * Save the transaction securely
     */
    public static function savePermintaan(array $data)
    {
        return DB::transaction(function () use ($data) {
            $kategori = $data['kategori'];
            $dateStr = date('Ymd');
            $prefix = $kategori;
            $mainTable = $kategori === 'PA' ? 'permintaan_labpa' : 'permintaan_lab';

            // Generate Fresh No Order with lockForUpdate to prevent race conditions
            $lastOrder = DB::table($mainTable)
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

            if ($kategori === 'PA') {
                // Simpan ke Header PA
                DB::table('permintaan_labpa')->insert([
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
                    'diagnosa_klinis' => $data['diagnosa_klinis'] ?: '-',
                    'pengambilan_bahan' => $data['pa']['pengambilan_bahan'],
                    'diperoleh_dengan' => $data['pa']['diperoleh_dengan'] ?: '-',
                    'lokasi_jaringan' => $data['pa']['lokasi_jaringan'] ?: '-',
                    'diawetkan_dengan' => $data['pa']['diawetkan_dengan'] ?: '-',
                    'pernah_dilakukan_di' => $data['pa']['pernah_dilakukan_di'] ?: '-',
                    'tanggal_pa_sebelumnya' => $data['pa']['tanggal_sebelumnya'],
                    'nomor_pa_sebelumnya' => $data['pa']['nomor_sebelumnya'] ?: '-',
                    'diagnosa_pa_sebelumnya' => $data['pa']['diagnosa_sebelumnya'] ?: '-'
                ]);

                // Simpan Item Pemeriksaan PA
                foreach ($data['selectedTests'] as $kd) {
                    DB::table('permintaan_pemeriksaan_labpa')->insert([
                        'noorder' => $noorder,
                        'kd_jenis_prw' => $kd,
                        'stts_bayar' => 'Belum'
                    ]);
                }
            } else {
                // Header PK / MB
                DB::table('permintaan_lab')->insert([
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

                // Tests
                $testIdsToSave = TemplateLaboratorium::whereIn('id_template', $data['selectedDetails'])
                    ->distinct()
                    ->pluck('kd_jenis_prw');

                foreach ($testIdsToSave as $kd) {
                    DB::table('permintaan_pemeriksaan_lab')->insert([
                        'noorder' => $noorder,
                        'kd_jenis_prw' => $kd,
                        'stts_bayar' => 'Belum'
                    ]);
                }

                // Parameters
                foreach ($data['selectedDetails'] as $id_template) {
                    $template = TemplateLaboratorium::find($id_template);
                    if ($template) {
                        DB::table('permintaan_detail_permintaan_lab')->insert([
                            'noorder' => $noorder,
                            'kd_jenis_prw' => $template->kd_jenis_prw,
                            'id_template' => $id_template,
                            'stts_bayar' => 'Belum'
                        ]);
                    }
                }
            }

            return $noorder;
        });
    }

    /**
     * Batal Permintaan securely
     */
    public static function batalPermintaan(string $noorder)
    {
        $isPA = str_starts_with($noorder, 'PA');
        $headerTable = $isPA ? 'permintaan_labpa' : 'permintaan_lab';
        $itemTable = $isPA ? 'permintaan_pemeriksaan_labpa' : 'permintaan_pemeriksaan_lab';
        $detailTable = 'permintaan_detail_permintaan_lab';

        DB::transaction(function () use ($noorder, $isPA, $headerTable, $itemTable, $detailTable) {
            $permintaan = DB::table($headerTable)
                ->where('noorder', $noorder)
                ->lockForUpdate()
                ->first();

            if (!$permintaan) {
                throw new \Exception("Data permintaan tidak ditemukan.");
            }

            if (isset($permintaan->tgl_sampel)) {
                if ($permintaan->tgl_sampel != '1000-01-01' && $permintaan->tgl_sampel != '0000-00-00') {
                    throw new \Exception("Permintaan tidak dapat dibatalkan karena sudah diproses.");
                }
            }

            // Hapus Items
            if (!$isPA) {
                DB::table($detailTable)->where('noorder', $noorder)->delete();
            }
            DB::table($itemTable)->where('noorder', $noorder)->delete();
            
            // Hapus Header
            DB::table($headerTable)->where('noorder', $noorder)->delete();
        });
    }

}
