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

    // -----------------------------------------------------------------------
    // Create Penjualan
    // -----------------------------------------------------------------------
    public static function getObatList(string $kd_bangsal, string $searchObat = '', array $cartKodes = [], int $perPage = 10)
    {
        $query = \Illuminate\Support\Facades\DB::table('databarang')
            ->leftJoin('kodesatuan', 'databarang.kode_sat', '=', 'kodesatuan.kode_sat')
            ->leftJoin('gudangbarang', function($join) use ($kd_bangsal) {
                $join->on('databarang.kode_brng', '=', 'gudangbarang.kode_brng')
                     ->where('gudangbarang.kd_bangsal', '=', $kd_bangsal);
            })
            ->select(
                'databarang.kode_brng',
                'databarang.nama_brng',
                'databarang.kode_sat',
                'databarang.beliluar as harga',
                \Illuminate\Support\Facades\DB::raw("IFNULL(SUM(gudangbarang.stok), 0) as stok")
            )
            ->where('databarang.status', '1')
            ->groupBy(
                'databarang.kode_brng',
                'databarang.nama_brng',
                'databarang.kode_sat',
                'databarang.beliluar'
            );

        if (!empty($searchObat)) {
            $query->where(function ($q) use ($searchObat) {
                $q->where('databarang.nama_brng', 'like', '%' . $searchObat . '%')
                    ->orWhere('databarang.kode_brng', 'like', '%' . $searchObat . '%');
            });
        }

        if (!empty($cartKodes)) {
            $query->whereNotIn('databarang.kode_brng', $cartKodes);
        }

        return $query->paginate($perPage);
    }

    public static function generateNoNota(string $tgl_jual): string
    {
        $tglSekarang = \Carbon\Carbon::parse($tgl_jual)->format('Ymd');
        $maxData = Penjualan::where('nota_jual', 'like', 'PJ' . $tglSekarang . '%')
            ->max('nota_jual');

        if ($maxData) {
            $lastNumber = (int) substr($maxData, -3);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        return 'PJ' . $tglSekarang . sprintf('%03d', $newNumber);
    }

    public static function savePenjualan(array $data)
    {
        return \Illuminate\Support\Facades\DB::transaction(function () use ($data) {
            // Generate Nota Jual: PJYYYYMMDDXXX
            $nota_jual = self::generateNoNota($data['tgl_jual']);

            $isPaid = (float)($data['jumlah_bayar'] ?? 0) >= (float)($data['tagihan'] ?? 0);

            // Insert Header
            Penjualan::create([
                'nota_jual'    => $nota_jual,
                'tgl_jual'     => $data['tgl_jual'],
                'nip'          => $data['nip'],
                'no_rkm_medis' => $data['no_rkm_medis'],
                'nm_pasien'    => $data['nm_pasien'],
                'keterangan'   => $data['keterangan'],
                'jns_jual'     => $data['jns_jual'],
                'ongkir'       => $data['ongkir'],
                'ppn'          => $data['ppn'],
                'status'       => $isPaid ? 'Sudah Dibayar' : 'Belum Dibayar',
                'kd_bangsal'   => $data['kd_bangsal'],
                'kd_rek'       => $data['kd_rek'],
                'nama_bayar'   => $data['nama_bayar'],
            ]);


            // Insert Details
            foreach ($data['cart'] as $item) {
                $jml = (float)($item['jml'] ?: 0);
                $harga = (float)($item['h_jual'] ?: 0);
                $subtotal = $jml * $harga;
                $dis_rp = (float)($item['dis_rp'] ?: 0);
                $tambahan = (float)($item['tambahan'] ?: 0);
                $embalase = (float)($item['embalase'] ?: 0);
                $tuslah = (float)($item['tuslah'] ?: 0);
                
                $total = ($subtotal - $dis_rp) + $tambahan + $embalase + $tuslah;

                DetailJual::create([
                    'nota_jual'    => $nota_jual,
                    'kode_brng'    => $item['kode_brng'],
                    'kode_sat'     => $item['satuan'],
                    'h_jual'       => $harga,
                    'h_beli'       => 0, // usually taken from databarang if needed
                    'jumlah'       => $jml,
                    'subtotal'     => $subtotal,
                    'dis'          => (float)($item['dis_persen'] ?: 0),
                    'bsr_dis'      => $dis_rp,
                    'tambahan'     => $tambahan,
                    'embalase'     => $embalase,
                    'tuslah'       => $tuslah,
                    'aturan_pakai' => $item['aturan_pakai'] ?? '',
                    'total'        => $total,
                    'no_batch'     => $item['no_batch'] ?? '',
                    'no_faktur'    => $item['no_faktur'] ?? '',
                ]);
            }

            return $nota_jual;
        });
    }

    /**
     * Hapus data transaksi penjualan (header dan detail).
     */
    public static function deletePenjualan(string $notaJual): bool
    {
        return \Illuminate\Support\Facades\DB::transaction(function () use ($notaJual) {
            DetailJual::where('nota_jual', $notaJual)->delete();
            return Penjualan::where('nota_jual', $notaJual)->delete() > 0;
        });
    }

    /**
     * Verifikasi status transaksi penjualan menjadi 'Sudah Dibayar' dan update Akun Bayar.
     */
    public static function verifikasiPenjualan(string $notaJual, string $namaBayar = ''): bool
    {
        $updateData = ['status' => 'Sudah Dibayar'];
        if (!empty($namaBayar)) {
            $updateData['nama_bayar'] = $namaBayar;
            $akun = \App\Models\AkunBayar::where('nama_bayar', $namaBayar)->first();
            if ($akun) {
                $updateData['kd_rek'] = $akun->kd_rek;
            }
        }
        return Penjualan::where('nota_jual', $notaJual)
            ->update($updateData) > 0;
    }

}

