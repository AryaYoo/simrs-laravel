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
    public static function getPemeriksaanList(string $kategori, string $search = '', string $kd_pj = '', int $perPage = 15)
    {
        $query = JnsPerawatanLab::where('status', '1')
            ->where('kategori', $kategori);

        if ($kd_pj) {
            $query->where(function($q) use ($kd_pj) {
                $q->where('kd_pj', $kd_pj)
                  ->orWhere('kd_pj', '-');
            });
        }

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
     * Falls back to showing the jns_perawatan_lab entry itself when no template_laboratorium exists (DIRECT_ prefix)
     */
    public static function getDetailParameters(string $kategori, array $selectedTests, string $searchDetail = '')
    {
        if (empty($selectedTests)) return collect([]);

        $query = TemplateLaboratorium::whereIn('kd_jenis_prw', $selectedTests);

        if ($searchDetail) {
            $query->where('Pemeriksaan', 'like', '%' . $searchDetail . '%');
        }

        $templates = $query->orderBy('kd_jenis_prw')->orderBy('urut')->get();

        // Find tests that have NO template entries → show the test itself as a synthetic detail (legacy behavior)
        $testsWithTemplates = $templates->pluck('kd_jenis_prw')->unique()->toArray();
        $testsWithoutTemplates = array_diff($selectedTests, $testsWithTemplates);

        if (!empty($testsWithoutTemplates)) {
            $syntheticFilter = JnsPerawatanLab::whereIn('kd_jenis_prw', $testsWithoutTemplates);
            if ($searchDetail) {
                $syntheticFilter->where('nm_perawatan', 'like', '%' . $searchDetail . '%');
            }
            $synthetics = $syntheticFilter->get()->map(function ($item) {
                $t = new TemplateLaboratorium();
                $t->id_template    = 'DIRECT_' . $item->kd_jenis_prw;
                $t->kd_jenis_prw   = $item->kd_jenis_prw;
                $t->Pemeriksaan    = $item->nm_perawatan;
                $t->satuan         = '';
                $t->nilai_rujukan_ld = '';
                $t->nilai_rujukan_la = '';
                $t->nilai_rujukan_pd = '';
                $t->nilai_rujukan_pa = '';
                $t->urut           = 0;
                $t->setRelation('pemeriksaanHeader', $item);
                return $t;
            });
            return $templates->concat($synthetics)->sortBy('kd_jenis_prw')->values();
        }

        return $templates;
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
     * Get Hasil Laboratorium
     */
    public static function getHasilLaboratorium(string $no_rawat)
    {
        // 1. Ambil data master periksa_lab
        $masterPeriksa = DB::table('periksa_lab as p')
            ->join('jns_perawatan_lab as j', 'p.kd_jenis_prw', '=', 'j.kd_jenis_prw')
            ->leftJoin('petugas as ptg', 'p.nip', '=', 'ptg.nip')
            ->leftJoin('dokter as dr_perujuk', 'p.dokter_perujuk', '=', 'dr_perujuk.kd_dokter')
            ->leftJoin('dokter as dr_pj', 'p.kd_dokter', '=', 'dr_pj.kd_dokter')
            ->where('p.no_rawat', $no_rawat)
            ->select(
                'p.no_rawat', 'p.kd_jenis_prw', 'p.tgl_periksa', 'p.jam', 'p.biaya',
                'j.nm_perawatan',
                'ptg.nama as petugas',
                'dr_perujuk.nm_dokter as perujuk',
                'dr_pj.nm_dokter as penanggung_jawab'
            )
            ->orderBy('p.tgl_periksa', 'desc')
            ->orderBy('p.jam', 'desc')
            ->get();

        if ($masterPeriksa->isEmpty()) {
            return collect([]);
        }

        // 2. Ambil detail
        $details = DB::table('detail_periksa_lab as d')
            ->join('template_laboratorium as t', 'd.id_template', '=', 't.id_template')
            ->where('d.no_rawat', $no_rawat)
            ->select(
                'd.kd_jenis_prw', 'd.tgl_periksa', 'd.jam',
                't.Pemeriksaan as nama_pemeriksaan', 'd.nilai', 't.satuan', 'd.nilai_rujukan', 'd.keterangan', 't.urut'
            )
            ->orderBy('t.urut')
            ->get();

        // 3. Ambil saran kesan
        $saranKesan = DB::table('saran_kesan_lab')
            ->where('no_rawat', $no_rawat)
            ->get();

        // 4. Group data by tgl_periksa and jam
        $grouped = [];
        foreach ($masterPeriksa as $master) {
            $key = $master->tgl_periksa . '_' . $master->jam;
            
            if (!isset($grouped[$key])) {
                $sk = $saranKesan->where('tgl_periksa', $master->tgl_periksa)->where('jam', $master->jam)->first();
                $grouped[$key] = [
                    'tgl_periksa' => $master->tgl_periksa,
                    'jam' => $master->jam,
                    'petugas' => $master->petugas ?: '-',
                    'perujuk' => $master->perujuk ?: '-',
                    'penanggung_jawab' => $master->penanggung_jawab ?: '-',
                    'saran' => $sk ? $sk->saran : '-',
                    'kesan' => $sk ? $sk->kesan : '-',
                    'pemeriksaan' => []
                ];
            }

            $filteredDetails = $details->filter(function($d) use ($master) {
                return $d->kd_jenis_prw === $master->kd_jenis_prw && 
                       $d->tgl_periksa === $master->tgl_periksa && 
                       $d->jam === $master->jam;
            })->values()->all();

            $grouped[$key]['pemeriksaan'][] = [
                'kd_jenis_prw' => $master->kd_jenis_prw,
                'nm_perawatan' => $master->nm_perawatan,
                'biaya' => $master->biaya,
                'details' => $filteredDetails
            ];
        }

        return collect(array_values($grouped));
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
                    'tgl_sampel' => '0000-00-00',
                    'jam_sampel' => '00:00:00',
                    'tgl_hasil' => '0000-00-00',
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
                    'noorder'             => $noorder,
                    'no_rawat'            => $data['no_rawat'],
                    'tgl_permintaan'      => $data['tgl_permintaan'],
                    'jam_permintaan'      => $jamF,
                    'tgl_sampel'          => '0000-00-00',
                    'jam_sampel'          => '00:00:00',
                    'tgl_hasil'           => '0000-00-00',
                    'jam_hasil'           => '00:00:00',
                    'dokter_perujuk'      => $data['kd_dokter_perujuk'],
                    'status'              => 'ranap',
                    'informasi_tambahan'  => $data['informasi_tambahan'] ?: '-',
                    'diagnosa_klinis'     => $data['diagnosa_klinis'] ?: '-'
                ]);

                // Separate DIRECT_ IDs (no template) from real template IDs
                $directKds    = [];
                $realDetailIds = [];
                foreach ($data['selectedDetails'] as $id) {
                    if (str_starts_with((string)$id, 'DIRECT_')) {
                        $directKds[] = substr($id, 7); // strip 'DIRECT_' prefix
                    } else {
                        $realDetailIds[] = $id;
                    }
                }

                // kd_jenis_prw from real templates
                $testIdsFromTemplates = [];
                if (!empty($realDetailIds)) {
                    $testIdsFromTemplates = TemplateLaboratorium::whereIn('id_template', $realDetailIds)
                        ->distinct()
                        ->pluck('kd_jenis_prw')
                        ->toArray();
                }

                // Merge all kd_jenis_prw to save (real + direct) — deduplicated
                $allKds = array_unique(array_merge($testIdsFromTemplates, $directKds));

                foreach ($allKds as $kd) {
                    DB::table('permintaan_pemeriksaan_lab')->insert([
                        'noorder'      => $noorder,
                        'kd_jenis_prw' => $kd,
                        'stts_bayar'   => 'Belum'
                    ]);
                }

                // Parameters — only for real template entries
                foreach ($realDetailIds as $id_template) {
                    $template = TemplateLaboratorium::find($id_template);
                    if ($template) {
                        DB::table('permintaan_detail_permintaan_lab')->insert([
                            'noorder'      => $noorder,
                            'kd_jenis_prw' => $template->kd_jenis_prw,
                            'id_template'  => $id_template,
                            'stts_bayar'   => 'Belum'
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
