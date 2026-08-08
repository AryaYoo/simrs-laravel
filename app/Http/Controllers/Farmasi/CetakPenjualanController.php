<?php

namespace App\Http\Controllers\Farmasi;

use App\Http\Controllers\Controller;
use App\Models\SettingCetakWeb;
use App\Repositories\Farmasi\PenjualanRepository;
use Illuminate\Support\Facades\DB;

class CetakPenjualanController extends Controller
{
    public function cetakNota($nota_jual)
    {
        $detail = PenjualanRepository::getDetail($nota_jual);

        if (!$detail) {
            abort(404, 'Data Penjualan tidak ditemukan.');
        }

        // Fetch hospital settings - SOP #7: prioritize setting_cetak_web
        $webSetting = SettingCetakWeb::first();

        if ($webSetting && !empty($webSetting->nama_instansi)) {
            $setting = $webSetting->toArray();
            if (!empty($setting['logo'])) {
                $setting['logo'] = base64_decode($setting['logo']);
            }
        } else {
            $legacySetting = DB::table('setting')->first();
            $setting = $legacySetting ? (array) $legacySetting : [];
        }

        return view('modul.farmasi.cetak-nota', array_merge($detail, ['setting' => $setting]));
    }

    public function cetakAturanPakai($nota_jual)
    {
        $detail = PenjualanRepository::getDetail($nota_jual);

        if (!$detail) {
            abort(404, 'Data Penjualan tidak ditemukan.');
        }

        $webSetting = SettingCetakWeb::first();

        if ($webSetting && !empty($webSetting->nama_instansi)) {
            $setting = $webSetting->toArray();
            if (!empty($setting['logo'])) {
                $setting['logo'] = base64_decode($setting['logo']);
            }
        } else {
            $legacySetting = DB::table('setting')->first();
            $setting = $legacySetting ? (array) $legacySetting : [];
        }

        return view('modul.farmasi.cetak-aturan-pakai', array_merge($detail, ['setting' => $setting]));
    }
}
