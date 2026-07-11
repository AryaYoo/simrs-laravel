<?php

namespace App\Repositories\RawatInap;

use App\Models\PasienBayi;

class KelahiranBayiRepository
{
    /**
     * Mendapatkan data bayi beserta relasi pasien dengan fitur paginasi dan pencarian.
     */
    public function getPaginatedData(string $search = '', string $dari = '', string $sampai = '', string $jk = '', int $perPage = 20, string $sortColumn = 'tgl_daftar', string $sortDirection = 'desc')
    {
        $sortMapping = [
            'no_rkm_medis' => 'pasien_bayi.no_rkm_medis',
            'nm_pasien' => 'pasien.nm_pasien',
            'tgl_lahir' => 'pasien.tgl_lahir',
            'jam_lahir' => 'pasien_bayi.jam_lahir',
            'umur' => 'pasien.umur',
            'tgl_daftar' => 'pasien.tgl_daftar',
            'nm_ibu' => 'pasien.nm_ibu',
        ];

        $orderByColumn = $sortMapping[$sortColumn] ?? 'pasien.tgl_daftar';
        $direction = in_array(strtolower($sortDirection), ['asc', 'desc']) ? $sortDirection : 'desc';

        $query = PasienBayi::query()
            ->with('pasien')
            ->join('pasien', 'pasien_bayi.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->select('pasien_bayi.*', 'pasien.tgl_daftar', 'pasien.tgl_lahir', 'pasien.jk')
            ->when($search, function ($q) use ($search) {
                $q->where(function($query) use ($search) {
                    $query->where('pasien_bayi.no_rkm_medis', 'like', "%{$search}%")
                          ->orWhere('pasien_bayi.nama_ayah', 'like', "%{$search}%")
                          ->orWhere('pasien.nm_pasien', 'like', "%{$search}%")
                          ->orWhere('pasien.nm_ibu', 'like', "%{$search}%");
                });
            })
            ->when($dari, function ($q) use ($dari) {
                $q->whereDate('pasien.tgl_lahir', '>=', $dari);
            })
            ->when($sampai, function ($q) use ($sampai) {
                $q->whereDate('pasien.tgl_lahir', '<=', $sampai);
            })
            ->when($jk, function ($q) use ($jk) {
                $q->where('pasien.jk', $jk);
            })
            ->orderBy($orderByColumn, $direction);

        return $query->paginate($perPage);
    }

    /**
     * Menghapus data pasien bayi
     */
    public function delete(string $no_rkm_medis): bool
    {
        // Berdasarkan skema, pasien_bayi berelasi CASCADE dengan pasien
        // namun mungkin kita hanya ingin menghapus bayi, atau hapus pasien utuh.
        // Asumsi standar: Hapus pasien_bayi saja jika diperlukan,
        // Tapi no_rkm_medis itu pasien. Menghapus pasien bayi tidak otomatis hapus pasien
        // kecuali kita hapus pasien-nya. Untuk amannya, kita hapus pasien_bayi.
        $bayi = PasienBayi::find($no_rkm_medis);
        if ($bayi) {
            return $bayi->delete();
        }
        return false;
    }
}
