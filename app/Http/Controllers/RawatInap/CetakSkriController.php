<?php

namespace App\Http\Controllers\RawatInap;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SuratKeteranganRawatInap;
use App\Models\RegPeriksa;


class CetakSkriController extends Controller
{
    public function cetak($no_rawat, $no_surat)
    {
        $no_rawat = str_replace('-', '/', $no_rawat);
        
        $surat = SuratKeteranganRawatInap::where('no_surat', $no_surat)
            ->where('no_rawat', $no_rawat)
            ->firstOrFail();

        $regPeriksa = RegPeriksa::with(['pasien', 'dokter', 'poliklinik', 'penjab'])
            ->where('no_rawat', $no_rawat)
            ->firstOrFail();

        $webSetting = \App\Models\SettingCetakWeb::first();

        if ($webSetting && !empty($webSetting->nama_instansi)) {
            $setting = $webSetting->toArray();
            if (!empty($setting['logo'])) {
                $setting['logo'] = base64_decode($setting['logo']);
            }
        } else {
            $legacySetting = \Illuminate\Support\Facades\DB::table('setting')->first();
            $setting = $legacySetting ? (array) $legacySetting : [];
        }

        // Ambil data tambahan
        $diagnosa = \App\Models\DiagnosaPasien::with('penyakit')
            ->where('no_rawat', $no_rawat)
            ->orderBy('prioritas', 'asc')
            ->first();

        $sep = \App\Models\BridgingSep::where('no_rawat', $no_rawat)->first();

        // Get Spesialis from DB directly
        $nm_sps = 'Dokter Penanggung Jawab';
        if ($regPeriksa->dokter && $regPeriksa->dokter->kd_sps) {
            $spesialis = \Illuminate\Support\Facades\DB::table('spesialis')
                ->where('kd_sps', $regPeriksa->dokter->kd_sps)
                ->first();
            if ($spesialis) {
                $nm_sps = $spesialis->nm_sps;
            }
        }

        return view('modul.rawat-inap.surat-keterangan-rawat-inap.cetak', compact('surat', 'regPeriksa', 'setting', 'diagnosa', 'sep', 'nm_sps'));
    }
}
