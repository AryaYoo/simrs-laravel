<?php

namespace App\Http\Controllers\RawatInap;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CatatanSbar;
use App\Models\RegPeriksa;
use App\Models\SettingCetakWeb;
use App\Models\Pegawai;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CetakSbarController extends Controller
{
    public function cetak($no_rawat, $tanggal)
    {
        $no_rawat = str_replace('-', '/', $no_rawat);
        $tanggalDecoded = urldecode($tanggal);

        $catatan = CatatanSbar::with(['petugas', 'dokter'])
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

        // Ambil info kamar inap terkini
        $kamarInap = $regPeriksa->kamarInap->sortByDesc('tgl_masuk')->first() ?? $regPeriksa->kamarInap->last();

        // Ambil jabatan pelapor dari tabel pegawai
        $pegawai = Pegawai::find($catatan->nip);
        $jabatanPelapor = $pegawai->jbtn ?? 'Perawat / Petugas';
        $namaPelapor = $catatan->petugas->nama ?? ($pegawai->nama ?? '-');

        // Hitung umur detail: X Th Y Bl Z Hr
        $tglLahir = $regPeriksa->pasien->tgl_lahir ? Carbon::parse($regPeriksa->pasien->tgl_lahir) : null;
        $tglCatatan = Carbon::parse($catatan->tanggal);
        if ($tglLahir) {
            $diff = $tglLahir->diff($tglCatatan);
            $umurDetail = "{$diff->y} Th {$diff->m} Bl {$diff->d} Hr";
        } else {
            $umurDetail = ($regPeriksa->umurdaftar ?? '-') . ' ' . ($regPeriksa->sttsumur ?? '');
        }

        return view('modul.rawat-inap.catatan-sbar.cetak', [
            'catatan'        => $catatan,
            'regPeriksa'     => $regPeriksa,
            'setting'        => $setting,
            'kamarInap'      => $kamarInap,
            'jabatanPelapor' => $jabatanPelapor,
            'namaPelapor'    => $namaPelapor,
            'umurDetail'     => $umurDetail,
        ]);
    }
}
