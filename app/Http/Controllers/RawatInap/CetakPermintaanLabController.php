<?php

namespace App\Http\Controllers\RawatInap;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PermintaanLab;
use App\Models\PermintaanLabPa;
use App\Models\PermintaanDetailPermintaanLab;
use App\Models\PermintaanPemeriksaanLab;
use App\Models\PermintaanPemeriksaanLabPa;
use App\Models\AppSetting;
use App\Models\SettingCetakWeb;
use Illuminate\Support\Facades\DB;

class CetakPermintaanLabController extends Controller
{
    public function cetak($no_rawat, $noorder)
    {
        $isPA = str_starts_with($noorder, 'PA');
        $pages = [];

        if ($isPA) {
            $permintaan = PermintaanLabPa::with([
                'dokter',
                'detailPemeriksaan.pemeriksaan'
            ])->where('noorder', $noorder)->firstOrFail();

            // Fetch patient info directly from reg_periksa and pasien since model relationship is incomplete
            $regPeriksa = DB::table('reg_periksa')
                ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
                ->leftJoin('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
                ->where('reg_periksa.no_rawat', $permintaan->no_rawat)
                ->first();

            $detailLab = []; // PA doesn't have TemplateLaboratorium details by default in this structure
            
            $flattenedPA = [];
            foreach ($permintaan->detailPemeriksaan as $detail) {
                if ($detail->pemeriksaan) {
                    $flattenedPA[] = ['type' => 'header', 'name' => $detail->pemeriksaan->nm_perawatan];
                }
            }
            $pages = array_chunk($flattenedPA, 25);
            
        } else {
            $permintaan = PermintaanLab::with([
                'dokter',
                'detailPemeriksaan.pemeriksaan'
            ])->where('noorder', $noorder)->firstOrFail();

            $regPeriksa = DB::table('reg_periksa')
                ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
                ->leftJoin('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
                ->where('reg_periksa.no_rawat', $permintaan->no_rawat)
                ->first();

            // Fetch details grouped by perawatan
            $detailPemeriksaan = PermintaanDetailPermintaanLab::with(['template', 'pemeriksaan'])
                ->where('noorder', $noorder)
                ->get();

            $detailLab = [];
            
            // Smart Chunking for Pagination
            $currentPage = [];
            $lineCount = 0;
            $pages = [];
            
            // First page has Kop Surat, so it holds less items
            $isFirstPage = true;

            foreach ($detailPemeriksaan as $detail) {
                if ($detail->pemeriksaan && $detail->template) {
                    $nm_perawatan = $detail->pemeriksaan->nm_perawatan;
                    
                    if (!isset($detailLab[$nm_perawatan])) {
                        $detailLab[$nm_perawatan] = [];
                        
                        $maxLines = $isFirstPage ? 16 : 25;
                        
                        // Break page before header if near the bottom to avoid orphans
                        if ($lineCount > $maxLines - 3) {
                            $pages[] = $currentPage;
                            $currentPage = [];
                            $lineCount = 0;
                            $isFirstPage = false;
                        }
                        
                        $currentPage[] = ['type' => 'header', 'name' => $nm_perawatan];
                        $lineCount++;
                    }
                    
                    $detailLab[$nm_perawatan][] = $detail->template;
                    $currentPage[] = ['type' => 'detail', 'data' => $detail->template];
                    $lineCount++;
                    
                    $maxLines = $isFirstPage ? 16 : 25;
                    if ($lineCount >= $maxLines) {
                        $pages[] = $currentPage;
                        $currentPage = [];
                        $lineCount = 0;
                        $isFirstPage = false;
                    }
                }
            }
            
            if (!empty($currentPage)) {
                $pages[] = $currentPage;
            }
        }

        // Fetch hospital settings from 'setting_cetak_web' (Independent web settings)
        $webSetting = SettingCetakWeb::first();
        
        if ($webSetting && !empty($webSetting->nama_instansi)) {
            $setting = $webSetting->toArray();
            // Decode back to binary so the view's base64_encode works uniformly
            if (!empty($setting['logo'])) {
                $setting['logo'] = base64_decode($setting['logo']);
            }
            if (!empty($setting['background'])) {
                $setting['wallpaper'] = base64_decode($setting['background']); // Map to 'wallpaper' to match Khanza's convention
            }
        } else {
            // Fallback to hospital settings from 'setting' table (Legacy Khanza)
            $legacySetting = DB::table('setting')->where('nama_instansi', 'Rumah Sakit Ibu dan Anak IBI Surabaya')->first();
            if (!$legacySetting) {
                $legacySetting = DB::table('setting')->first();
            }
            $setting = $legacySetting ? (array) $legacySetting : [];
        }

        return view('modul.rawat-inap.permintaan-laboratorium.cetak', compact('permintaan', 'regPeriksa', 'pages', 'setting', 'isPA'));
    }
}
