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

        // Get baby's own pasien fields (tgl_lahir, jk, alamat, nm_ibu, umur, dll)
        $pasienRaw = DB::table('pasien')
            ->where('no_rkm_medis', $no_rkm_medis)
            ->select('no_ktp', 'pekerjaan', 'nm_ibu', 'alamat', 'tgl_lahir', 'jk', 'umur')
            ->first();

        // Get mother's pasien record to fetch correct no_ktp and pekerjaan.
        // The baby's pasien record stores nm_ibu (mother's name), which we match
        // against nm_pasien in the pasien table to find the mother's full record.
        $ibunya = null;
        if ($pasienRaw && !empty($pasienRaw->nm_ibu) && $pasienRaw->nm_ibu !== '-') {
            $ibunya = DB::table('pasien')
                ->where('nm_pasien', $pasienRaw->nm_ibu)
                ->select('no_ktp', 'pekerjaan', 'no_rkm_medis')
                ->first();
        }

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

        return view('modul.rawat-inap.kelahiran-bayi.cetak-skl', compact('bayi', 'pasienRaw', 'ibunya', 'setting'));
    }
}
