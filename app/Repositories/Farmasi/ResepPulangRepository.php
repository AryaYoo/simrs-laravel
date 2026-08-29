<?php

namespace App\Repositories\Farmasi;

use App\Models\ResepPulang;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ResepPulangRepository
{
    public function getList(
        string $search = '',
        ?string $tanggal1 = null,
        ?string $tanggal2 = null,
        int $perPage = 15
    ): LengthAwarePaginator {
        $query = ResepPulang::with([
            'barang.satuanKecil',
            'regPeriksa.pasien',
            'bangsal'
        ])
        ->when($search, function ($q) use ($search) {
            $q->where(function ($sub) use ($search) {
                $sub->where('no_rawat', 'like', "%{$search}%")
                    ->orWhere('kode_brng', 'like', "%{$search}%")
                    ->orWhere('dosis', 'like', "%{$search}%")
                    ->orWhere('no_batch', 'like', "%{$search}%")
                    ->orWhere('no_faktur', 'like', "%{$search}%")
                    ->orWhereHas('barang', function ($qB) use ($search) {
                        $qB->where('nama_brng', 'like', "%{$search}%");
                    })
                    ->orWhereHas('regPeriksa', function ($qR) use ($search) {
                        $qR->where('no_rkm_medis', 'like', "%{$search}%")
                           ->orWhereHas('pasien', function ($qP) use ($search) {
                               $qP->where('nm_pasien', 'like', "%{$search}%");
                           });
                    });
            });
        })
        ->when($tanggal1, function ($q) use ($tanggal1) {
            $q->whereDate('tanggal', '>=', $tanggal1);
        })
        ->when($tanggal2, function ($q) use ($tanggal2) {
            $q->whereDate('tanggal', '<=', $tanggal2);
        })
        ->orderBy('tanggal', 'desc')
        ->orderBy('jam', 'desc');

        return $query->paginate($perPage);
    }

    public function delete(
        string $no_rawat,
        string $kode_brng,
        string $tanggal,
        string $jam,
        string $no_batch,
        string $no_faktur
    ): int {
        return DB::table('resep_pulang')
            ->where('no_rawat', $no_rawat)
            ->where('kode_brng', $kode_brng)
            ->where('tanggal', $tanggal)
            ->where('jam', $jam)
            ->where('no_batch', $no_batch)
            ->where('no_faktur', $no_faktur)
            ->delete();
    }
}
