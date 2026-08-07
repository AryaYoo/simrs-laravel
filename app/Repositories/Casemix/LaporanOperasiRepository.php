<?php

namespace App\Repositories\Casemix;

use App\Models\LaporanOperasi;
use App\Models\Operasi;
use App\Models\Petugas;
use App\Models\Pegawai;
use App\Models\Dokter;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class LaporanOperasiRepository
{
    /**
     * Get paginated list of Laporan Operasi with search and date filters
     */
    public static function getList(
        string $search = '',
        string $dari = '',
        string $sampai = '',
        int $perPage = 15
    ): LengthAwarePaginator {
        $query = LaporanOperasi::with([
            'regPeriksa.pasien',
            'regPeriksa.dokter',
            'regPeriksa.poliklinik',
            'regPeriksa.penjab',
        ])
        ->orderBy('tanggal', 'desc');

        if ($dari) {
            $query->whereDate('tanggal', '>=', $dari);
        }

        if ($sampai) {
            $query->whereDate('tanggal', '<=', $sampai);
        }

        if ($search) {
            $like = "%{$search}%";
            $query->where(function ($q) use ($like) {
                $q->where('no_rawat', 'like', $like)
                  ->orWhere('diagnosa_preop', 'like', $like)
                  ->orWhere('diagnosa_postop', 'like', $like)
                  ->orWhere('jaringan_dieksekusi', 'like', $like)
                  ->orWhereHas('regPeriksa.pasien', function ($p) use ($like) {
                      $p->where('nm_pasien', 'like', $like)
                        ->orWhere('no_rkm_medis', 'like', $like);
                  });
            });
        }

        return $query->paginate($perPage);
    }

    /**
     * Get detail single Laporan Operasi by no_rawat and tanggal
     */
    public static function getDetail(string $noRawat, string $tanggal = null): ?LaporanOperasi
    {
        $query = LaporanOperasi::with([
            'regPeriksa.pasien',
            'regPeriksa.dokter',
            'regPeriksa.poliklinik',
            'regPeriksa.penjab',
            'regPeriksa.kamarInap.kamar.bangsal',
        ])->where('no_rawat', $noRawat);

        if ($tanggal) {
            $query->where('tanggal', $tanggal);
        }

        return $query->first();
    }

    /**
     * Get related operation details (tim medis, kategori, biaya, dll)
     */
    public static function getOperasiDetail(string $noRawat): ?Operasi
    {
        return Operasi::with([
            'paketOperasi',
            'dokterOperator1',
            'dokterOperator2',
            'dokterOperator3',
            'dokterAnak',
            'dokterAnestesi',
            'dokterPjanak',
            'dokterUmum',
        ])
        ->where('no_rawat', $noRawat)
        ->first();
    }

    /**
     * Helper to resolve staff / doctor name by ID
     */
    public static function resolveStaffName(?string $id): string
    {
        if (empty($id) || $id === '-' || $id === '0') {
            return '-';
        }

        $petugas = Petugas::where('nip', $id)->first();
        if ($petugas && !empty($petugas->nama)) {
            return $petugas->nama;
        }

        $pegawai = Pegawai::where('nik', $id)->first();
        if ($pegawai && !empty($pegawai->nama)) {
            return $pegawai->nama;
        }

        $dokter = Dokter::where('kd_dokter', $id)->first();
        if ($dokter && !empty($dokter->nm_dokter)) {
            return $dokter->nm_dokter;
        }

        return $id;
    }

    /**
     * Calculate total biaya obat operasi for a given no_rawat
     */
    public static function getBiayaObat(string $noRawat): float
    {
        // 1. Check beri_obat_operasi
        $totalBeriObat = DB::table('beri_obat_operasi')
            ->where('no_rawat', $noRawat)
            ->sum(DB::raw('hargasatuan * jumlah'));

        // 2. Check detail_pemberian_obat
        $totalPemberianObat = DB::table('detail_pemberian_obat')
            ->where('no_rawat', $noRawat)
            ->sum('total');

        return (float) ($totalBeriObat + $totalPemberianObat);
    }
}