<?php

namespace App\Http\Controllers\RawatJalan;

use App\Http\Controllers\Controller;
use App\Models\PenilaianAwalKeperawatanIgd;
use App\Models\RegPeriksa;
use App\Models\SettingCetakWeb;
use Illuminate\Support\Facades\DB;

class CetakPengkajianAwalIgdController extends Controller
{
    public function cetak($no_rawat)
    {
        $no_rawat = str_replace('-', '/', $no_rawat);

        $pengkajian = PenilaianAwalKeperawatanIgd::with([
                'petugas',
                'masalah',
                'detailRencana'
            ])
            ->where('no_rawat', $no_rawat)
            ->firstOrFail();

        $regPeriksa = RegPeriksa::with([
                'pasien.bahasa',
                'pasien.cacatFisik',
                'dokter',
                'poliklinik',
                'penjab'
            ])
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

        // Format masalah & rencana text
        $masalahArr = $pengkajian->masalah->pluck('nama_masalah')->toArray();
        $masalahText = !empty($masalahArr) ? implode(', ', $masalahArr) . ', ' : '-';

        $rencanaArr = $pengkajian->detailRencana->pluck('rencana_keperawatan')->toArray();
        $rencanaList = implode(', ', $rencanaArr);
        if (!empty($pengkajian->rencana)) {
            $rencanaList = $rencanaList ? $rencanaList . ', ' . $pengkajian->rencana : $pengkajian->rencana;
        }
        $rencanaText = !empty($rencanaList) ? $rencanaList . ', ' : '-';

        // QR verification data
        $qrData = "Dokumen Valid Penilaian Awal Keperawatan Gawat Darurat " . ($setting['nama_instansi'] ?? 'RSIA IBI Surabaya') . 
                  " | No. Rawat: " . $pengkajian->no_rawat . 
                  " | Petugas: " . ($pengkajian->petugas->nama ?? $pengkajian->nip) . 
                  " | Tgl: " . $pengkajian->tanggal;

        return view('modul.rawat-jalan.sub-rawat-jalan.pengkajian-awal-igd.cetak', [
            'pengkajian'  => $pengkajian,
            'regPeriksa'  => $regPeriksa,
            'setting'     => $setting,
            'masalahText' => $masalahText,
            'rencanaText' => $rencanaText,
            'qrData'      => $qrData,
        ]);
    }
}
