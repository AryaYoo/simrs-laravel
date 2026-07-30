<?php

namespace App\Repositories\Farmasi;

use App\Models\DetailJual;
use App\Models\Penjualan;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class PenjualanRepository
{
    // -----------------------------------------------------------------------
    // List & Filter
    // -----------------------------------------------------------------------

    /**
     * Ambil daftar penjualan dengan search, filter tanggal, filter petugas,
     * dan paginasi.
     */
    public static function getList(
        string $search  = '',
        string $dari    = '',
        string $sampai  = '',
        string $nip     = '',
        int    $perPage = 20
    ): LengthAwarePaginator {
        $query = Penjualan::with(['petugas', 'bangsal'])
            ->orderByDesc('tgl_jual')
            ->orderByDesc('nota_jual');

        if ($dari) {
            $query->whereDate('tgl_jual', '>=', $dari);
        }

        if ($sampai) {
            $query->whereDate('tgl_jual', '<=', $sampai);
        }

        if ($nip) {
            $query->where('nip', $nip);
        }

        if ($search) {
            $like = "%{$search}%";
            $query->where(function ($q) use ($like) {
                $q->where('nm_pasien',   'like', $like)
                  ->orWhere('no_rkm_medis', 'like', $like)
                  ->orWhere('nota_jual',    'like', $like);
            });
        }

        return $query->paginate($perPage);
    }

    // -----------------------------------------------------------------------
    // Detail
    // -----------------------------------------------------------------------

    /**
     * Ambil data lengkap satu nota: header penjualan + item detail + sum tiap kolom.
     * Mengembalikan null jika nota tidak ditemukan.
     *
     * @return array{
     *     penjualan: Penjualan,
     *     items: Collection,
     *     sumSubtotal: float,
     *     sumDiskon: float,
     *     sumTambahan: float,
     *     sumEmbalase: float,
     *     sumTuslah: float,
     *     sumTotal: float,
     *     grandTotal: float,
     * }|null
     */
    public static function getDetail(string $notaJual): ?array
    {
        $penjualan = Penjualan::with(['petugas', 'bangsal', 'pasien'])
            ->find($notaJual);

        if (! $penjualan) {
            return null;
        }

        $items = DetailJual::with(['barang', 'satuan'])
            ->where('nota_jual', $notaJual)
            ->get();

        $sumSubtotal = (float) $items->sum('subtotal');
        $sumDiskon   = (float) $items->sum('bsr_dis');
        $sumTambahan = (float) $items->sum('tambahan');
        $sumEmbalase = (float) $items->sum('embalase');
        $sumTuslah   = (float) $items->sum('tuslah');
        $sumTotal    = (float) $items->sum('total');

        // Grand total = jumlah total item + PPN + Ongkos Kirim
        $grandTotal = $sumTotal
            + (float) ($penjualan->ppn    ?? 0)
            + (float) ($penjualan->ongkir ?? 0);

        return [
            'penjualan'   => $penjualan,
            'items'       => $items,
            'sumSubtotal' => $sumSubtotal,
            'sumDiskon'   => $sumDiskon,
            'sumTambahan' => $sumTambahan,
            'sumEmbalase' => $sumEmbalase,
            'sumTuslah'   => $sumTuslah,
            'sumTotal'    => $sumTotal,
            'grandTotal'  => $grandTotal,
        ];
    }

    // -----------------------------------------------------------------------
    // Dropdowns / Lookups
    // -----------------------------------------------------------------------

    /**
     * Daftar petugas yang pernah melakukan penjualan, untuk dropdown filter.
     * Diurutkan berdasarkan nama.
     *
     * @return array<int, array{nip: string, nama: string}>
     */
    public static function getPetugasList(): array
    {
        return Penjualan::with('petugas')
            ->select('nip')
            ->distinct()
            ->whereNotNull('nip')
            ->get()
            ->map(fn ($p) => [
                'nip'  => $p->nip,
                'nama' => $p->petugas->nama ?? $p->nip,
            ])
            ->sortBy('nama')
            ->values()
            ->toArray();
    }
}
