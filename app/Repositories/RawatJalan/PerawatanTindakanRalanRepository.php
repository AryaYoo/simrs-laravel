<?php

namespace App\Repositories\RawatJalan;

use Illuminate\Support\Facades\DB;
use App\Models\RawatJlDrpr;
use App\Models\RawatJlDr;
use App\Models\RawatJlPr;
use App\Models\Pegawai;
use App\Models\Dokter;
use App\Models\Petugas;
use App\Models\JnsPerawatan;

class PerawatanTindakanRalanRepository
{
    /**
     * Get Combined Actions History (Drpr, Dr, Pr)
     */
    public static function getAllTindakanHistory(string $no_rawat)
    {
        $rawatJlDrpr = RawatJlDrpr::with(['regPeriksa.pasien:no_rkm_medis,nm_pasien', 'jnsPerawatan:kd_jenis_prw,nm_perawatan', 'dokter:kd_dokter,nm_dokter', 'petugas:nip,nama'])
            ->select('no_rawat', 'kd_jenis_prw', 'kd_dokter', 'nip', 'tgl_perawatan', 'jam_rawat', 'material', 'bhp', 'tarif_tindakandr', 'tarif_tindakanpr', 'kso', 'menejemen', 'biaya_rawat')
            ->where('no_rawat', $no_rawat)
            ->get()->map(fn(RawatJlDrpr $i) => [ 
                ...$i->toArray(), 
                'type' => 'drpr', 
                'staff_dr' => $i->dokter->nm_dokter ?? '-', 
                'staff_pr' => $i->petugas->nama ?? '-', 
                'nm_perawatan' => $i->jnsPerawatan->nm_perawatan ?? '-',
                'no_r_m' => $i->regPeriksa->pasien->no_rkm_medis ?? '-',
                'nm_pasien' => $i->regPeriksa->pasien->nm_pasien ?? '-',
                'kd_staff_dr' => $i->kd_dokter,
                'kd_staff_pr' => $i->nip,
                'biaya_material' => $i->material,
                'biaya_bhp' => $i->bhp,
                'biaya_dr' => $i->tarif_tindakandr,
                'biaya_pr' => $i->tarif_tindakanpr,
                'biaya_kso' => $i->kso ?? 0,
                'biaya_menejemen' => $i->menejemen ?? 0,
            ]);

        $rawatJlDr = RawatJlDr::with(['regPeriksa.pasien:no_rkm_medis,nm_pasien', 'jnsPerawatan:kd_jenis_prw,nm_perawatan', 'dokter:kd_dokter,nm_dokter'])
            ->select('no_rawat', 'kd_jenis_prw', 'kd_dokter', 'tgl_perawatan', 'jam_rawat', 'material', 'bhp', 'tarif_tindakandr', 'kso', 'menejemen', 'biaya_rawat')
            ->where('no_rawat', $no_rawat)
            ->get()->map(fn(RawatJlDr $i) => [ 
                ...$i->toArray(), 
                'type' => 'dr', 
                'staff_dr' => $i->dokter->nm_dokter ?? '-', 
                'staff_pr' => '-', 
                'nm_perawatan' => $i->jnsPerawatan->nm_perawatan ?? '-',
                'no_r_m' => $i->regPeriksa->pasien->no_rkm_medis ?? '-',
                'nm_pasien' => $i->regPeriksa->pasien->nm_pasien ?? '-',
                'kd_staff_dr' => $i->kd_dokter,
                'kd_staff_pr' => '-',
                'biaya_material' => $i->material ?? 0,
                'biaya_bhp' => $i->bhp ?? 0,
                'biaya_dr' => $i->tarif_tindakandr ?? 0,
                'biaya_pr' => 0,
                'biaya_kso' => $i->kso ?? 0,
                'biaya_menejemen' => $i->menejemen ?? 0,
            ]);

        $rawatJlPr = RawatJlPr::with(['regPeriksa.pasien:no_rkm_medis,nm_pasien', 'jnsPerawatan:kd_jenis_prw,nm_perawatan', 'petugas:nip,nama'])
            ->select('no_rawat', 'kd_jenis_prw', 'nip', 'tgl_perawatan', 'jam_rawat', 'material', 'bhp', 'tarif_tindakanpr', 'kso', 'menejemen', 'biaya_rawat')
            ->where('no_rawat', $no_rawat)
            ->get()->map(fn(RawatJlPr $i) => [ 
                ...$i->toArray(), 
                'type' => 'pr', 
                'staff_dr' => '-', 
                'staff_pr' => $i->petugas->nama ?? '-', 
                'nm_perawatan' => $i->jnsPerawatan->nm_perawatan ?? '-',
                'no_r_m' => $i->regPeriksa->pasien->no_rkm_medis ?? '-',
                'nm_pasien' => $i->regPeriksa->pasien->nm_pasien ?? '-',
                'kd_staff_dr' => '-',
                'kd_staff_pr' => $i->nip,
                'biaya_material' => $i->material ?? 0,
                'biaya_bhp' => $i->bhp ?? 0,
                'biaya_dr' => 0,
                'biaya_pr' => $i->tarif_tindakanpr ?? 0,
                'biaya_kso' => $i->kso ?? 0,
                'biaya_menejemen' => $i->menejemen ?? 0,
            ]);

        return collect($rawatJlDrpr)->concat($rawatJlDr)->concat($rawatJlPr)->sortByDesc(fn($i) => $i['tgl_perawatan'] . $i['jam_rawat']);
    }

    /**
     * Search Functions
     */
    public static function searchDokter(string $search)
    {
        if (strlen($search) >= 3) {
            return Dokter::where('nm_dokter', 'like', '%' . $search . '%')
                ->orWhere('kd_dokter', 'like', '%' . $search . '%')
                ->limit(10)
                ->get();
        }
        return collect([]);
    }

    public static function searchPetugas(string $search)
    {
        if (strlen($search) >= 3) {
            return Petugas::where('nama', 'like', '%' . $search . '%')
                ->orWhere('nip', 'like', '%' . $search . '%')
                ->limit(10)
                ->get();
        }
        return collect([]);
    }

    public static function searchTarif(string $search, string $lookupType)
    {
        $tindakanFilter = JnsPerawatan::query()->where('status', '1');
        
        if (strlen($search) >= 3) {
            $tindakanFilter->where('nm_perawatan', 'like', '%' . $search . '%');
        }

        if ($lookupType == 'dr') {
            $tindakanFilter->where('total_byrdr', '>', 0);
        } else {
            $tindakanFilter->where('total_byrpr', '>', 0);
        }

        return $tindakanFilter->limit(50)->get();
    }

    /**
     * Core Data Modifying Action (Tindakan)
     */
    public static function saveTindakan(array $data)
    {
        DB::beginTransaction();
        try {
            $tarif = JnsPerawatan::find($data['kd_jenis_prw_selected']);
            if (!$tarif) throw new \Exception("Tarif tidak ditemukan.");

            if ($data['isEditTindakanMode']) {
                if ($data['original_tindakan_type'] == 'drpr') {
                    RawatJlDrpr::where(['no_rawat' => $data['no_rawat'], 'kd_jenis_prw' => $data['original_kd_jenis_prw'], 'tgl_perawatan' => $data['original_tgl_perawatan'], 'jam_rawat' => $data['original_jam_rawat']])->delete();
                } elseif ($data['original_tindakan_type'] == 'dr') {
                    RawatJlDr::where(['no_rawat' => $data['no_rawat'], 'kd_jenis_prw' => $data['original_kd_jenis_prw'], 'tgl_perawatan' => $data['original_tgl_perawatan'], 'jam_rawat' => $data['original_jam_rawat']])->delete();
                } else {
                    RawatJlPr::where(['no_rawat' => $data['no_rawat'], 'kd_jenis_prw' => $data['original_kd_jenis_prw'], 'tgl_perawatan' => $data['original_tgl_perawatan'], 'jam_rawat' => $data['original_jam_rawat']])->delete();
                }

                $tgl = $data['original_tgl_perawatan'];
                $jam = $data['original_jam_rawat'];
            } else {
                $tgl = now()->format('Y-m-d');
                $jam = now()->format('H:i:s');
            }
            
            // Mengambil nilai fallback 0 jika field tidak ada di model JnsPerawatan
            $material = $tarif->material ?? 0;
            $bhp = $tarif->bhp ?? 0;
            $tarif_dr = $tarif->tarif_tindakandr ?? 0;
            $tarif_pr = $tarif->tarif_tindakanpr ?? 0;
            $kso = $tarif->kso ?? 0;
            $menejemen = $tarif->menejemen ?? 0;

            if ($data['kd_dokter_tindakan'] && $data['nip_tindakan']) {
                $biaya_rawat = $material + $bhp + $tarif_dr + $tarif_pr + $kso + $menejemen;
                RawatJlDrpr::create([
                    'no_rawat' => $data['no_rawat'], 'kd_jenis_prw' => $data['kd_jenis_prw_selected'], 'kd_dokter' => $data['kd_dokter_tindakan'], 'nip' => $data['nip_tindakan'], 'tgl_perawatan' => $tgl, 'jam_rawat' => $jam,
                    'material' => $material, 'bhp' => $bhp, 'tarif_tindakandr' => $tarif_dr, 'tarif_tindakanpr' => $tarif_pr, 'kso' => $kso, 'menejemen' => $menejemen, 'biaya_rawat' => $biaya_rawat, 'stts_bayar' => 'Belum'
                ]);
            } elseif ($data['kd_dokter_tindakan']) {
                $biaya_rawat = $material + $bhp + $tarif_dr + $kso + $menejemen;
                RawatJlDr::create([
                    'no_rawat' => $data['no_rawat'], 'kd_jenis_prw' => $data['kd_jenis_prw_selected'], 'kd_dokter' => $data['kd_dokter_tindakan'], 'tgl_perawatan' => $tgl, 'jam_rawat' => $jam,
                    'material' => $material, 'bhp' => $bhp, 'tarif_tindakandr' => $tarif_dr, 'kso' => $kso, 'menejemen' => $menejemen, 'biaya_rawat' => $biaya_rawat, 'stts_bayar' => 'Belum'
                ]);
            } else {
                $biaya_rawat = $material + $bhp + $tarif_pr + $kso + $menejemen;
                RawatJlPr::create([
                    'no_rawat' => $data['no_rawat'], 'kd_jenis_prw' => $data['kd_jenis_prw_selected'], 'nip' => $data['nip_tindakan'], 'tgl_perawatan' => $tgl, 'jam_rawat' => $jam,
                    'material' => $material, 'bhp' => $bhp, 'tarif_tindakanpr' => $tarif_pr, 'kso' => $kso, 'menejemen' => $menejemen, 'biaya_rawat' => $biaya_rawat, 'stts_bayar' => 'Belum'
                ]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public static function deleteTindakan(string $type, string $no_rawat, string $kd_jenis_prw, string $tgl, string $jam)
    {
        DB::beginTransaction();
        try {
            if ($type == 'drpr') {
                RawatJlDrpr::where([
                    'no_rawat' => $no_rawat,
                    'kd_jenis_prw' => $kd_jenis_prw,
                    'tgl_perawatan' => $tgl,
                    'jam_rawat' => $jam,
                ])->delete();
            } elseif ($type == 'dr') {
                RawatJlDr::where([
                    'no_rawat' => $no_rawat,
                    'kd_jenis_prw' => $kd_jenis_prw,
                    'tgl_perawatan' => $tgl,
                    'jam_rawat' => $jam,
                ])->delete();
            } else {
                RawatJlPr::where([
                    'no_rawat' => $no_rawat,
                    'kd_jenis_prw' => $kd_jenis_prw,
                    'tgl_perawatan' => $tgl,
                    'jam_rawat' => $jam,
                ])->delete();
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
