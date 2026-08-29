<?php

namespace App\Http\Controllers\Farmasi;

use App\Http\Controllers\Controller;
use App\Models\ResepPulang;
use App\Models\SettingCetakWeb;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CetakResepPulangController extends Controller
{
    public function cetakAturanPakai(Request $request)
    {
        $no_rawat = $request->query('no_rawat');
        $kode_brng = $request->query('kode_brng');
        $tanggal = $request->query('tanggal');
        $jam = $request->query('jam');
        $no_batch = $request->query('no_batch', '');
        $no_faktur = $request->query('no_faktur', '');

        $item = ResepPulang::with(['barang.satuanKecil', 'regPeriksa.pasien', 'bangsal'])
            ->where('no_rawat', $no_rawat)
            ->where('kode_brng', $kode_brng)
            ->where('tanggal', $tanggal)
            ->where('jam', $jam)
            ->where('no_batch', $no_batch)
            ->where('no_faktur', $no_faktur)
            ->first();

        if (!$item) {
            abort(404, 'Data Resep Pulang tidak ditemukan.');
        }

        // Fetch hospital settings - SOP: prioritize setting_cetak_web
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

        return view('modul.farmasi.cetak-aturan-pakai-resep-pulang', [
            'item' => $item,
            'setting' => $setting,
        ]);
    }
}
