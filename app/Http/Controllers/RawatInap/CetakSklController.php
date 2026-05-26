<?php

namespace App\Http\Controllers\RawatInap;

use App\Http\Controllers\Controller;
use App\Models\PasienBayi;
use App\Models\SettingCetakWeb;
use Illuminate\Support\Facades\DB;

class CetakSklController extends Controller
{
    public function cetak($no_rkm_medis)
    {
        // Load baby record with relations
        $bayi = PasienBayi::with(['pasien', 'pegawai'])
            ->where('no_rkm_medis', $no_rkm_medis)
            ->firstOrFail();

        // Get extra pasien fields (no_ktp, pekerjaan) not exposed via model relation cast
        $pasienRaw = DB::table('pasien')
            ->where('no_rkm_medis', $no_rkm_medis)
            ->select('no_ktp', 'pekerjaan', 'nm_ibu', 'alamat', 'tgl_lahir', 'jk', 'umur')
            ->first();

        // Fetch hospital settings - SOP #7: prioritize setting_cetak_web
        $webSetting = SettingCetakWeb::first();

        if ($webSetting && !empty($webSetting->nama_instansi)) {
            $setting = $webSetting->toArray();
            if (!empty($setting['logo'])) {
                $setting['logo'] = base64_decode($setting['logo']);
            }
            if (!empty($setting['background'])) {
                $setting['wallpaper'] = base64_decode($setting['background']);
            }
        } else {
            // Fallback to legacy 'setting' table (Khanza)
            $legacySetting = DB::table('setting')->first();
            $setting = $legacySetting ? (array) $legacySetting : [];
        }

        return view('modul.rawat-inap.kelahiran-bayi.cetak-skl', compact('bayi', 'pasienRaw', 'setting'));
    }
}
