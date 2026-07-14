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

        // Ambil diagnosa awal dari kamar_inap (SOP Khanza)
        $kamarInap = \App\Models\KamarInap::where('no_rawat', $no_rawat)
            ->orderBy('tgl_masuk', 'asc')
            ->orderBy('jam_masuk', 'asc')
            ->first();
        
        $diagnosa_awal = '-';
        if ($kamarInap && !empty(trim($kamarInap->diagnosa_awal))) {
            $kodeOrNama = trim($kamarInap->diagnosa_awal);
            // Try to find in penyakit table (ICD-10 code lookup)
            $penyakit = \Illuminate\Support\Facades\DB::table('penyakit')
                ->where('kd_penyakit', $kodeOrNama)
                ->first();
            if ($penyakit) {
                $diagnosa_awal = $penyakit->nm_penyakit . ' (' . $kodeOrNama . ')';
            } else {
                $diagnosa_awal = $kodeOrNama;
            }
        } else {
            $diagnosa = \App\Models\DiagnosaPasien::with('penyakit')
                ->where('no_rawat', $no_rawat)
                ->orderBy('prioritas', 'asc')
                ->first();
            if ($diagnosa && $diagnosa->penyakit) {
                $diagnosa_awal = $diagnosa->penyakit->nm_penyakit . ' (' . $diagnosa->penyakit->kd_penyakit . ')';
            }
        }

        // Prioritas 1: DPJP yang di-override manual saat buat surat
        $dpjp = null;
        if (!empty($surat->dpjp_keterangan_inap)) {
            $dpjp = \App\Models\Dokter::find($surat->dpjp_keterangan_inap);
        }

        // Prioritas 2: DPJP dari rawat_inap_dr (dokter visite/perawatan terbanyak)
        if (!$dpjp) {
            $rawatInapDr = \Illuminate\Support\Facades\DB::table('rawat_inap_dr')
                ->select('kd_dokter', \Illuminate\Support\Facades\DB::raw('COUNT(*) as total'))
                ->where('no_rawat', $no_rawat)
                ->groupBy('kd_dokter')
                ->orderByDesc('total')
                ->first();

            if ($rawatInapDr) {
                $dpjp = \App\Models\Dokter::find($rawatInapDr->kd_dokter);
            }
        }

        // Prioritas 3: Fallback ke dokter pendaftar (reg_periksa)
        if (!$dpjp) {
            $dpjp = $regPeriksa->dokter;
        }

        // Ambil nama spesialis DPJP
        $nm_sps = 'Dokter Penanggung Jawab';
        if ($dpjp && $dpjp->kd_sps) {
            $spesialis = \Illuminate\Support\Facades\DB::table('spesialis')
                ->where('kd_sps', $dpjp->kd_sps)
                ->first();
            if ($spesialis) {
                $nm_sps = $spesialis->nm_sps;
            }
        }

        $sep = \App\Models\BridgingSep::where('no_rawat', $no_rawat)->first();

        return view('modul.rawat-inap.surat-keterangan-rawat-inap.cetak', compact('surat', 'regPeriksa', 'setting', 'diagnosa_awal', 'sep', 'nm_sps', 'dpjp'));
    }
}
