<?php

namespace App\Http\Controllers\RawatInap;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ChecklistPreOperasi;
use App\Models\RegPeriksa;
use App\Models\SettingCetakWeb;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CetakChecklistPreOperasiController extends Controller
{
    public function cetak($no_rawat, $tanggal)
    {
        $no_rawat = str_replace('-', '/', $no_rawat);
        $tanggalDecoded = urldecode($tanggal);

        $checklist = ChecklistPreOperasi::with([
                'dokterBedah',
                'dokterAnestesi',
                'petugasRuangan',
                'perawatOk'
            ])
            ->where('no_rawat', $no_rawat)
            ->where('tanggal', $tanggalDecoded)
            ->firstOrFail();

        $regPeriksa = RegPeriksa::with(['pasien', 'dokter', 'poliklinik', 'penjab', 'kamarInap.kamar.bangsal'])
            ->where('no_rawat', $no_rawat)
            ->firstOrFail();

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

        $kamarInap = $regPeriksa->kamarInap->sortByDesc('tgl_masuk')->first() ?? $regPeriksa->kamarInap->last();

        return view('modul.rawat-inap.checklist-pre-operasi.cetak', [
            'checklist'  => $checklist,
            'regPeriksa' => $regPeriksa,
            'setting'    => $setting,
            'kamarInap'  => $kamarInap,
        ]);
    }
}
