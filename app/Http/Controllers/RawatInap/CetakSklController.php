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
        //
        // FIX: Prioritaskan lookup by no_rkm_medis_ibu (unique primary key) jika tersedia.
        // Ini menghindari ambiguitas ketika ada beberapa pasien dengan nama yang sama
        // (contoh: 3 pasien bernama "Irmawati" dengan No. RM berbeda).
        //
        // Fallback ke nama (+ filter jk='P') hanya untuk data LAMA yang belum
        // punya no_rkm_medis_ibu (diisi sebelum kolom ini ditambahkan).
        $ibunya = null;
        $noRkmIbu = $bayi->no_rkm_medis_ibu ?? null;

        if (!empty($noRkmIbu)) {
            // ✅ Data baru: lookup tepat by No. RM ibu — dijamin tidak salah orang
            $ibunya = DB::table('pasien')
                ->where('no_rkm_medis', $noRkmIbu)
                ->select('no_ktp', 'pekerjaan', 'no_rkm_medis')
                ->first();
        } elseif ($pasienRaw && !empty($pasienRaw->nm_ibu) && $pasienRaw->nm_ibu !== '-') {
            // ⚠️  Data lama: fallback ke nama, tambah filter jk='P' agar lebih aman
            $ibunya = DB::table('pasien')
                ->where('nm_pasien', $pasienRaw->nm_ibu)
                ->where('jk', 'P')
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
