<?php

namespace App\Http\Controllers\Laboratorium;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\AppSetting;
use App\Models\SettingCetakWeb;

class CetakHasilLabController extends Controller
{
    public function cetak($no_rawat, $tgl_periksa, $jam)
    {
        // 1. Fetch Patient Info & Reg Periksa
        $no_rawat_db = str_replace('-', '/', $no_rawat);
        
        $regPeriksa = DB::table('reg_periksa')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->leftJoin('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
            ->where('reg_periksa.no_rawat', $no_rawat_db)
            ->select('reg_periksa.*', 'pasien.nm_pasien', 'pasien.jk', 'pasien.tgl_lahir', 'pasien.umur', 'pasien.alamat', 'poliklinik.nm_poli')
            ->first();
            
        if (!$regPeriksa) {
            abort(404, 'Data pasien tidak ditemukan');
        }

        // 2. Fetch Master Periksa Lab
        $masterPeriksa = DB::table('periksa_lab as p')
            ->leftJoin('petugas as ptg', 'p.nip', '=', 'ptg.nip')
            ->leftJoin('dokter as dr_perujuk', 'p.dokter_perujuk', '=', 'dr_perujuk.kd_dokter')
            ->leftJoin('dokter as dr_pj', 'p.kd_dokter', '=', 'dr_pj.kd_dokter')
            ->where('p.no_rawat', $no_rawat_db)
            ->where('p.tgl_periksa', $tgl_periksa)
            ->where('p.jam', $jam)
            ->select(
                'p.no_rawat', 'p.tgl_periksa', 'p.jam',
                'ptg.nama as nm_petugas',
                'dr_perujuk.nm_dokter as dokter_perujuk',
                'dr_pj.nm_dokter as penanggung_jawab'
            )
            ->first();
            
        if (!$masterPeriksa) {
            abort(404, 'Data pemeriksaan lab tidak ditemukan');
        }

        // 3. Fetch Detail Periksa Lab
        $details = DB::table('detail_periksa_lab as d')
            ->join('template_laboratorium as t', 'd.id_template', '=', 't.id_template')
            ->where('d.no_rawat', $no_rawat_db)
            ->where('d.tgl_periksa', $tgl_periksa)
            ->where('d.jam', $jam)
            ->select(
                'd.kd_jenis_prw', 'd.tgl_periksa', 'd.jam',
                't.Pemeriksaan as nama_pemeriksaan', 'd.nilai', 't.satuan', 'd.nilai_rujukan', 'd.keterangan', 't.urut'
            )
            ->orderBy('t.urut')
            ->get();
            
        // Get category names (Jns Perawatan Lab) to create headers
        $jnsPerawatan = DB::table('jns_perawatan_lab')
            ->whereIn('kd_jenis_prw', $details->pluck('kd_jenis_prw')->unique())
            ->pluck('nm_perawatan', 'kd_jenis_prw');

        // Flat data for pagination
        $flattened = [];
        $currentGroup = null;
        
        foreach ($details as $detail) {
            if ($currentGroup !== $detail->kd_jenis_prw) {
                $flattened[] = [
                    'type' => 'header',
                    'name' => $jnsPerawatan[$detail->kd_jenis_prw] ?? 'Pemeriksaan',
                ];
                $currentGroup = $detail->kd_jenis_prw;
            }
            
            $flattened[] = [
                'type' => 'row',
                'pemeriksaan' => $detail->nama_pemeriksaan,
                'hasil' => $detail->nilai,
                'satuan' => $detail->satuan,
                'nilai_rujukan' => $detail->nilai_rujukan,
                'keterangan' => $detail->keterangan,
            ];
        }

        // 4. Chunking (SOP)
        $pages = [];
        if (count($flattened) > 22) {
            $pages[] = array_slice($flattened, 0, 22); // Page 1 (with Kop Surat)
            $remaining = array_slice($flattened, 22);
            $chunks = array_chunk($remaining, 32);     // Subsequent pages
            foreach ($chunks as $chunk) {
                $pages[] = $chunk;
            }
        } else {
            $pages[] = $flattened;
        }

        // 5. Kesan & Saran
        $saranKesan = DB::table('saran_kesan_lab')
            ->where('no_rawat', $no_rawat_db)
            ->where('tgl_periksa', $tgl_periksa)
            ->where('jam', $jam)
            ->first();

        // 6. Print Settings
        $webSetting = SettingCetakWeb::first();
        if ($webSetting && !empty($webSetting->nama_instansi)) {
            $settingCetak = $webSetting->toArray();
            if (!empty($settingCetak['logo'])) {
                $settingCetak['logo'] = base64_decode($settingCetak['logo']);
            }
            if (!empty($settingCetak['background'])) {
                $settingCetak['wallpaper'] = base64_decode($settingCetak['background']);
            }
        } else {
            $legacySetting = DB::table('setting')->where('nama_instansi', 'Rumah Sakit Ibu dan Anak IBI Surabaya')->first();
            if (!$legacySetting) {
                $legacySetting = DB::table('setting')->first();
            }
            $settingCetak = $legacySetting ? (array) $legacySetting : [];
        }

        return view('modul.laboratorium.cetak-hasil', compact(
            'regPeriksa',
            'masterPeriksa',
            'pages',
            'saranKesan',
            'settingCetak'
        ));
    }
}
