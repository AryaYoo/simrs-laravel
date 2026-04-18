<?php

namespace App\Repositories\RawatInap;

use Illuminate\Support\Facades\DB;
use App\Models\PemeriksaanRanap;
use App\Models\RawatInapDrpr;
use App\Models\RawatInapDr;
use App\Models\RawatInapPr;
use App\Models\Pegawai;
use App\Models\Dokter;
use App\Models\Petugas;
use App\Models\JnsPerawatanInap;

class PerawatanTindakanRepository
{
    /**
     * Get Combined Actions History (Drpr, Dr, Pr)
     */
    public static function getAllTindakanHistory(string $no_rawat)
    {
        $rawatInapDrpr = RawatInapDrpr::with(['regPeriksa.pasien:no_rkm_medis,nm_pasien', 'jnsPerawatan:kd_jenis_prw,nm_perawatan', 'dokter:kd_dokter,nm_dokter', 'petugas:nip,nama'])
            ->select('no_rawat', 'kd_jenis_prw', 'kd_dokter', 'nip', 'tgl_perawatan', 'jam_rawat', 'material', 'bhp', 'tarif_tindakandr', 'tarif_tindakanpr', 'kSO', 'kso', 'menejemen', 'biaya_rawat')
            ->where('no_rawat', $no_rawat)
            ->get()->map(fn(RawatInapDrpr $i) => [ 
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
                'biaya_kso' => $i->kSO ?? $i->kso ?? 0,
                'biaya_menejemen' => $i->menejemen ?? 0,
            ]);

        $rawatInapDr = RawatInapDr::with(['regPeriksa.pasien:no_rkm_medis,nm_pasien', 'jnsPerawatan:kd_jenis_prw,nm_perawatan', 'dokter:kd_dokter,nm_dokter'])
            ->select('no_rawat', 'kd_jenis_prw', 'kd_dokter', 'tgl_perawatan', 'jam_rawat', 'material', 'bhp', 'tarif_tindakandr', 'kSO', 'kso', 'menejemen', 'biaya_rawat')
            ->where('no_rawat', $no_rawat)
            ->get()->map(fn(RawatInapDr $i) => [ 
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
                'biaya_kso' => $i->kSO ?? $i->kso ?? 0,
                'biaya_menejemen' => $i->menejemen ?? 0,
            ]);

        $rawatInapPr = RawatInapPr::with(['regPeriksa.pasien:no_rkm_medis,nm_pasien', 'jnsPerawatan:kd_jenis_prw,nm_perawatan', 'petugas:nip,nama'])
            ->select('no_rawat', 'kd_jenis_prw', 'nip', 'tgl_perawatan', 'jam_rawat', 'material', 'bhp', 'tarif_tindakanpr', 'kSO', 'kso', 'menejemen', 'biaya_rawat')
            ->where('no_rawat', $no_rawat)
            ->get()->map(fn(RawatInapPr $i) => [ 
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
                'biaya_kso' => $i->kSO ?? $i->kso ?? 0,
                'biaya_menejemen' => $i->menejemen ?? 0,
            ]);

        return collect($rawatInapDrpr)->concat($rawatInapDr)->concat($rawatInapPr)->sortByDesc(fn($i) => $i['tgl_perawatan'] . $i['jam_rawat']);
    }

    /**
     * Get Examination (SOAP) History
     */
    public static function getPemeriksaanHistory(string $no_rawat)
    {
        return PemeriksaanRanap::with(['regPeriksa.pasien', 'pegawai'])
            ->where('no_rawat', $no_rawat)
            ->orderBy('tgl_perawatan', 'desc')
            ->orderBy('jam_rawat', 'desc')
            ->get();
    }

    /**
     * Search Functions
     */
    public static function searchPegawai(string $searchPegawai)
    {
        if (strlen($searchPegawai) >= 3) {
            return Pegawai::where('nama', 'like', '%' . $searchPegawai . '%')
                ->orWhere('nik', 'like', '%' . $searchPegawai . '%')
                ->limit(10)
                ->get();
        }
        return collect([]);
    }

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

    public static function searchTarif(string $search, string $kelasStr, string $lookupType)
    {
        $tindakanFilter = JnsPerawatanInap::query();
        if (strlen($search) >= 3) {
            $tindakanFilter->where('nm_perawatan', 'like', '%' . $search . '%');
        }
        
        if ($kelasStr != '-') {
            $tindakanFilter->where(function($q) use ($kelasStr) {
                $q->where('kelas', $kelasStr)->orWhere('kelas', '-');
            });
        }

        if ($lookupType == 'dr') {
            $tindakanFilter->where('total_byrdr', '>', 0)->orWhere('total_byrdrpr', '>', 0);
        } else {
            $tindakanFilter->where('total_byrpr', '>', 0)->orWhere('total_byrdrpr', '>', 0);
        }

        return $tindakanFilter->limit(50)->get();
    }

    /**
     * Core Data Modifying Action (SOAP)
     */
    public static function insertPemeriksaan(array $data)
    {
        DB::beginTransaction();
        try {
            PemeriksaanRanap::create([
                'no_rawat'      => $data['no_rawat'],
                'tgl_perawatan' => $data['tgl_perawatan'],
                'jam_rawat'     => $data['jam_rawat'],
                'suhu_tubuh'    => $data['suhu_tubuh'] ?: '-',
                'tensi'         => $data['tensi'] ?: '-',
                'nadi'          => $data['nadi'] ?: '-',
                'respirasi'     => $data['respirasi'] ?: '-',
                'tinggi'        => $data['tinggi'] ?: '-',
                'berat'         => $data['berat'] ?: '-',
                'spo2'          => $data['spo2'] ?: '-',
                'gcs'           => $data['gcs'] ?: '-',
                'kesadaran'     => $data['kesadaran'] ?: 'Compos Mentis',
                'keluhan'       => $data['keluhan'] ?: '-',
                'pemeriksaan'   => $data['pemeriksaan'] ?: '-',
                'alergi'        => $data['alergi'] ?: '-',
                'penilaian'     => $data['penilaian'] ?: '-',
                'rtl'           => $data['rtl'] ?: '-',
                'instruksi'     => $data['instruksi'] ?: '-',
                'evaluasi'      => $data['evaluasi'] ?: '-',
                'nip'           => $data['nip'],
            ]);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public static function updatePemeriksaan(PemeriksaanRanap $model, array $data)
    {
        DB::beginTransaction();
        try {
            $model->update([
                'suhu_tubuh'    => $data['suhu_tubuh'] ?: '-',
                'tensi'         => $data['tensi'] ?: '-',
                'nadi'          => $data['nadi'] ?: '-',
                'respirasi'     => $data['respirasi'] ?: '-',
                'tinggi'        => $data['tinggi'] ?: '-',
                'berat'         => $data['berat'] ?: '-',
                'spo2'          => $data['spo2'] ?: '-',
                'gcs'           => $data['gcs'] ?: '-',
                'kesadaran'     => $data['kesadaran'] ?: 'Compos Mentis',
                'keluhan'       => $data['keluhan'] ?: '-',
                'pemeriksaan'   => $data['pemeriksaan'] ?: '-',
                'alergi'        => $data['alergi'] ?: '-',
                'penilaian'     => $data['penilaian'] ?: '-',
                'rtl'           => $data['rtl'] ?: '-',
                'instruksi'     => $data['instruksi'] ?: '-',
                'evaluasi'      => $data['evaluasi'] ?: '-',
                'nip'           => $data['nip'],
            ]);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public static function deletePemeriksaan(string $no_rawat, string $tgl, string $jam)
    {
        DB::beginTransaction();
        try {
            PemeriksaanRanap::where([
                'no_rawat'      => $no_rawat,
                'tgl_perawatan' => $tgl,
                'jam_rawat'     => $jam,
            ])->delete();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Core Data Modifying Action (Tindakan)
     */
    public static function saveTindakan(array $data)
    {
        DB::beginTransaction();
        try {
            $tarif = JnsPerawatanInap::find($data['kd_jenis_prw_selected']);
            if (!$tarif) throw new \Exception("Tarif tidak ditemukan.");

            if ($data['isEditTindakanMode']) {
                if ($data['original_tindakan_type'] == 'drpr') {
                    RawatInapDrpr::where(['no_rawat' => $data['no_rawat'], 'kd_jenis_prw' => $data['original_kd_jenis_prw'], 'tgl_perawatan' => $data['original_tgl_perawatan'], 'jam_rawat' => $data['original_jam_rawat']])->delete();
                } elseif ($data['original_tindakan_type'] == 'dr') {
                    RawatInapDr::where(['no_rawat' => $data['no_rawat'], 'kd_jenis_prw' => $data['original_kd_jenis_prw'], 'tgl_perawatan' => $data['original_tgl_perawatan'], 'jam_rawat' => $data['original_jam_rawat']])->delete();
                } else {
                    RawatInapPr::where(['no_rawat' => $data['no_rawat'], 'kd_jenis_prw' => $data['original_kd_jenis_prw'], 'tgl_perawatan' => $data['original_tgl_perawatan'], 'jam_rawat' => $data['original_jam_rawat']])->delete();
                }

                $tgl = $data['original_tgl_perawatan'];
                $jam = $data['original_jam_rawat'];
            } else {
                $tgl = now()->format('Y-m-d');
                $jam = now()->format('H:i:s');
            }

            if ($data['kd_dokter_tindakan'] && $data['nip_tindakan']) {
                RawatInapDrpr::create([
                    'no_rawat' => $data['no_rawat'], 'kd_jenis_prw' => $data['kd_jenis_prw_selected'], 'kd_dokter' => $data['kd_dokter_tindakan'], 'nip' => $data['nip_tindakan'], 'tgl_perawatan' => $tgl, 'jam_rawat' => $jam,
                    'material' => $tarif->material, 'bhp' => $tarif->bhp, 'tarif_tindakandr' => $tarif->tarif_tindakandr, 'tarif_tindakanpr' => $tarif->tarif_tindakanpr, 'kso' => $tarif->kso, 'menejemen' => $tarif->menejemen, 'biaya_rawat' => $tarif->total_byrdrpr
                ]);
            } elseif ($data['kd_dokter_tindakan']) {
                RawatInapDr::create([
                    'no_rawat' => $data['no_rawat'], 'kd_jenis_prw' => $data['kd_jenis_prw_selected'], 'kd_dokter' => $data['kd_dokter_tindakan'], 'tgl_perawatan' => $tgl, 'jam_rawat' => $jam,
                    'material' => $tarif->material, 'bhp' => $tarif->bhp, 'tarif_tindakandr' => $tarif->tarif_tindakandr, 'kso' => $tarif->kso, 'menejemen' => $tarif->menejemen, 'biaya_rawat' => $tarif->total_byrdr
                ]);
            } else {
                RawatInapPr::create([
                    'no_rawat' => $data['no_rawat'], 'kd_jenis_prw' => $data['kd_jenis_prw_selected'], 'nip' => $data['nip_tindakan'], 'tgl_perawatan' => $tgl, 'jam_rawat' => $jam,
                    'material' => $tarif->material, 'bhp' => $tarif->bhp, 'tarif_tindakanpr' => $tarif->tarif_tindakanpr, 'kso' => $tarif->kso, 'menejemen' => $tarif->menejemen, 'biaya_rawat' => $tarif->total_byrpr
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
                RawatInapDrpr::where([
                    'no_rawat' => $no_rawat,
                    'kd_jenis_prw' => $kd_jenis_prw,
                    'tgl_perawatan' => $tgl,
                    'jam_rawat' => $jam,
                ])->delete();
            } elseif ($type == 'dr') {
                RawatInapDr::where([
                    'no_rawat' => $no_rawat,
                    'kd_jenis_prw' => $kd_jenis_prw,
                    'tgl_perawatan' => $tgl,
                    'jam_rawat' => $jam,
                ])->delete();
            } else {
                RawatInapPr::where([
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
