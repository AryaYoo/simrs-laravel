<?php

namespace App\Http\Controllers\Casemix;

use App\Http\Controllers\Controller;
use App\Repositories\Casemix\LaporanOperasiRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CetakLaporanOperasiController extends Controller
{
    public function cetak(Request $request, $no_rawat, $tanggal = null)
    {
        $no_rawat = str_replace('-', '/', $no_rawat);
        if ($tanggal) {
            $tanggal = str_replace('_', ' ', $tanggal);
        }

        $laporan = LaporanOperasiRepository::getDetail($no_rawat, $tanggal);

        if (!$laporan) {
            abort(404, 'Data Laporan Operasi tidak ditemukan');
        }

        $operasi = LaporanOperasiRepository::getOperasiDetail($no_rawat);

        // Header Instansi / Kop Surat (SOP Cetak Web)
        $webSetting = \App\Models\SettingCetakWeb::first();
        if ($webSetting && !empty($webSetting->nama_instansi)) {
            $setting = $webSetting->toArray();
            if (!empty($setting['logo'])) {
                $setting['logo'] = base64_decode($setting['logo']);
            }
        } else {
            $legacySetting = DB::table('setting')->first();
            $setting = $legacySetting ? (array) $legacySetting : [];
        }

        $source = $request->query('source');
        $tgl = $request->query('tgl');
        $jam = $request->query('jam');

        $preSurgical = null;

        if ($source === 'none') {
            $preSurgical = null;
        } elseif ($source === 'ralan') {
            $queryRalan = DB::table('pemeriksaan_ralan as pr')
                ->leftJoin('pegawai as p', 'p.nik', '=', 'pr.nip')
                ->leftJoin('dokter as d', 'd.kd_dokter', '=', 'pr.nip')
                ->select('pr.*', 'p.nama as nama_petugas', 'd.nm_dokter');

            if ($tgl && $jam) {
                $queryRalan->where('pr.tgl_perawatan', $tgl)->where('pr.jam_rawat', $jam);
            } else {
                $queryRalan->where('pr.no_rawat', $no_rawat);
            }

            $preSurgical = $queryRalan->orderBy('pr.tgl_perawatan', 'desc')->orderBy('pr.jam_rawat', 'desc')->first();
        } else {
            // Default or source === 'ranap'
            $queryRanap = DB::table('pemeriksaan_ranap as pr')
                ->leftJoin('pegawai as p', 'p.nik', '=', 'pr.nip')
                ->leftJoin('dokter as d', 'd.kd_dokter', '=', 'pr.nip')
                ->select('pr.*', 'p.nama as nama_petugas', 'd.nm_dokter');

            if ($tgl && $jam) {
                $queryRanap->where('pr.tgl_perawatan', $tgl)->where('pr.jam_rawat', $jam);
            } else {
                $queryRanap->where('pr.no_rawat', $no_rawat);
            }

            $preSurgical = $queryRanap->orderBy('pr.tgl_perawatan', 'desc')->orderBy('pr.jam_rawat', 'desc')->first();

            // Fallback jika tidak ketemu di ranap dan tidak ada param spesifik
            if (!$preSurgical && !$source) {
                $preSurgical = DB::table('pemeriksaan_ralan as pr')
                    ->leftJoin('pegawai as p', 'p.nik', '=', 'pr.nip')
                    ->leftJoin('dokter as d', 'd.kd_dokter', '=', 'pr.nip')
                    ->where('pr.no_rawat', $no_rawat)
                    ->orderBy('pr.tgl_perawatan', 'desc')
                    ->orderBy('pr.jam_rawat', 'desc')
                    ->select('pr.*', 'p.nama as nama_petugas', 'd.nm_dokter')
                    ->first();
            }
        }

        return view('modul.casemix.kustom-laporan-operasi.cetak', compact('laporan', 'operasi', 'setting', 'preSurgical'));
    }
}