<?php

namespace App\Repositories\Farmasi;

use App\Models\Bangsal;
use App\Models\GudangBarang;
use App\Models\ResepObat;
use Illuminate\Support\Facades\DB;

class ObatAlkesBhpRepository
{
    /**
     * Ambil depo default berdasarkan kode bangsal.
     * Fallback ke array statis jika tidak ditemukan di DB.
     */
    public static function getDefaultDepo(string $kd_bangsal = 'AP'): array
    {
        $bangsal = Bangsal::where('kd_bangsal', $kd_bangsal)->first();

        if ($bangsal) {
            return [
                'kd_depo' => $bangsal->kd_bangsal,
                'nm_depo' => $bangsal->nm_bangsal,
            ];
        }

        return [
            'kd_depo' => 'AP',
            'nm_depo' => 'Apotek',
        ];
    }

    /**
     * Cari daftar bangsal aktif berdasarkan keyword pencarian.
     */
    public static function searchBangsal(string $search = '', int $limit = 50): array
    {
        $query = Bangsal::query()->where('status', '1');

        if (!empty($search)) {
            $s = "%{$search}%";
            $query->where(function ($q) use ($s) {
                $q->where('kd_bangsal', 'like', $s)
                  ->orWhere('nm_bangsal', 'like', $s);
            });
        }

        return $query->orderBy('nm_bangsal')->limit($limit)->get()->toArray();
    }

    /**
     * Ambil header resep beserta data pasien.
     * Mengembalikan array ['no_rawat', 'no_rkm_medis', 'nm_pasien', 'status']
     * atau null jika tidak ditemukan.
     */
    public static function getResepHeader(string $no_resep): ?array
    {
        $resep = ResepObat::with([
            'regPeriksa.pasien',
        ])->find($no_resep);

        if (!$resep) {
            return null;
        }

        return [
            'no_rawat'     => $resep->no_rawat ?? '',
            'no_rkm_medis' => $resep->regPeriksa->no_rkm_medis ?? '',
            'nm_pasien'    => $resep->regPeriksa->pasien->nm_pasien ?? '',
            'status'       => $resep->status ?? '',
        ];
    }

    /**
     * Ambil detail item obat dari resep beserta stok per depo.
     * Mengembalikan array of item obat yang sudah di-map ke format listObatUmum.
     */
    public static function getDetailObatResep(string $no_resep, string $kd_depo): array
    {
        $resep = ResepObat::with([
            'detail.barang',
        ])->find($no_resep);

        if (!$resep) {
            return [];
        }

        $result = [];

        foreach ($resep->detail as $item) {
            $barang = $item->barang;

            // Harga dari kolom ralan (rawat jalan), fallback ke h_beli
            $harga = floatval($barang->ralan ?? ($barang->h_beli ?? 0));
            $jml   = floatval($item->jml ?? 1);

            // Ambil detail tambahan via join (jenis, kategori, golongan, industri)
            $detail = self::getObatDetail($item->kode_brng, $kd_depo);

            $result[] = [
                'tercentang'   => true,
                'kode_brng'    => $item->kode_brng,
                'nama_brng'    => $barang->nama_brng ?? $item->kode_brng,
                'jumlah'       => $jml,
                'satuan'       => $barang->kode_sat ?? '-',
                'harga'        => $harga,
                'jenis_obat'   => $detail['jenis_obat'] ?? '-',
                'embalase'     => 0,
                'tuslah'       => 0,
                'stok'         => $detail['stok'] ?? 0,
                'aturan_pakai' => $item->aturan_pakai ?: '-',
                'industri'     => $detail['industri'] ?? '-',
                'kategori'     => $detail['kategori'] ?? '-',
                'golongan'     => $detail['golongan'] ?? '-',
                'no_batch'     => $detail['no_batch'] ?? '-',
                'no_faktur'    => $detail['no_faktur'] ?? '-',
                'kadaluarsa'   => '-',
            ];
        }

        return $result;
    }

    /**
     * Ambil satu baris data obat lengkap dengan semua relasi FK dan stok per depo.
     * Mengembalikan array atau null jika tidak ditemukan.
     */
    public static function getObatDetail(string $kode_brng, string $kd_depo): ?array
    {
        $brng = DB::table('databarang')
            ->leftJoin('kategori_barang', 'databarang.kode_kategori', '=', 'kategori_barang.kode')
            ->leftJoin('golongan_barang', 'databarang.kode_golongan', '=', 'golongan_barang.kode')
            ->leftJoin('industrifarmasi', 'databarang.kode_industri', '=', 'industrifarmasi.kode_industri')
            ->leftJoin('jenis', 'databarang.kdjns', '=', 'jenis.kdjns')
            ->where('databarang.kode_brng', $kode_brng)
            ->select(
                'databarang.kode_brng',
                'databarang.nama_brng',
                'databarang.kode_sat',
                'databarang.ralan',
                'databarang.h_beli',
                'kategori_barang.nama as nama_kategori',
                'golongan_barang.nama as nama_golongan',
                'industrifarmasi.nama_industri',
                'jenis.nama as nama_jenis',
            )
            ->first();

        if (!$brng) {
            return null;
        }

        // Stok per depo dari gudangbarang
        $gudang = GudangBarang::where('kode_brng', $kode_brng)
            ->where('kd_bangsal', $kd_depo)
            ->first();

        return [
            'kode_brng'  => $brng->kode_brng,
            'nama_brng'  => $brng->nama_brng,
            'satuan'     => $brng->kode_sat ?? '-',
            'harga'      => floatval($brng->ralan ?? ($brng->h_beli ?? 0)),
            'jenis_obat' => $brng->nama_jenis ?? '-',
            'kategori'   => $brng->nama_kategori ?? '-',
            'golongan'   => $brng->nama_golongan ?? '-',
            'industri'   => $brng->nama_industri ?? '-',
            'stok'       => $gudang ? floatval($gudang->stok) : 0,
            'no_batch'   => $gudang->no_batch ?? '-',
            'no_faktur'  => $gudang->no_faktur ?? '-',
        ];
    }

    /**
     * Cari daftar obat berdasarkan keyword dan depo aktif.
     * Menggunakan join lengkap sesuai FK tabel databarang (Khanza schema).
     * Mengembalikan array of item obat untuk ditampilkan di modal lookup.
     */
    public static function searchObat(string $search, string $kd_depo, int $limit = 50): array
    {
        $query = DB::table('databarang')
            ->leftJoin('kodesatuan', 'databarang.kode_sat', '=', 'kodesatuan.kode_sat')
            ->leftJoin('kategori_barang', 'databarang.kode_kategori', '=', 'kategori_barang.kode')
            ->leftJoin('golongan_barang', 'databarang.kode_golongan', '=', 'golongan_barang.kode')
            ->leftJoin('industrifarmasi', 'databarang.kode_industri', '=', 'industrifarmasi.kode_industri')
            ->leftJoin('jenis', 'databarang.kdjns', '=', 'jenis.kdjns')
            ->leftJoin('gudangbarang', function ($join) use ($kd_depo) {
                $join->on('databarang.kode_brng', '=', 'gudangbarang.kode_brng')
                     ->where('gudangbarang.kd_bangsal', '=', $kd_depo);
            })
            ->where('databarang.status', '1')
            ->select(
                'databarang.kode_brng',
                'databarang.nama_brng',
                'databarang.kode_sat',
                'databarang.ralan',
                'databarang.h_beli',
                'databarang.beliluar',
                'kategori_barang.nama as nama_kategori',
                'golongan_barang.nama as nama_golongan',
                'industrifarmasi.nama_industri',
                'jenis.nama as nama_jenis',
                'gudangbarang.stok as stok_depo',
                'gudangbarang.no_batch',
                'gudangbarang.no_faktur',
            )
            ->groupBy(
                'databarang.kode_brng',
                'databarang.nama_brng',
                'databarang.kode_sat',
                'databarang.ralan',
                'databarang.h_beli',
                'databarang.beliluar',
                'kategori_barang.nama',
                'golongan_barang.nama',
                'industrifarmasi.nama_industri',
                'jenis.nama',
                'gudangbarang.stok',
                'gudangbarang.no_batch',
                'gudangbarang.no_faktur',
            );

        if ($search !== '') {
            $s = "%{$search}%";
            $query->where(function ($q) use ($s) {
                $q->where('databarang.kode_brng', 'like', $s)
                  ->orWhere('databarang.nama_brng', 'like', $s)
                  ->orWhere('kategori_barang.nama', 'like', $s)
                  ->orWhere('jenis.nama', 'like', $s);
            });
        }

        $items = $query->orderBy('databarang.nama_brng')->limit($limit)->get();
        $result = [];

        foreach ($items as $brng) {
            $result[] = [
                'kode_brng'  => $brng->kode_brng,
                'nama_brng'  => $brng->nama_brng,
                'satuan'     => $brng->kode_sat ?? '-',
                'harga'      => floatval($brng->ralan ?? ($brng->h_beli ?? 0)),
                'stok'       => floatval($brng->stok_depo ?? 0),
                'jenis_obat' => $brng->nama_jenis ?? '-',
                'kategori'   => $brng->nama_kategori ?? '-',
                'golongan'   => $brng->nama_golongan ?? '-',
                'industri'   => $brng->nama_industri ?? '-',
                'no_batch'   => $brng->no_batch ?? '-',
                'no_faktur'  => $brng->no_faktur ?? '-',
            ];
        }

        return $result;
    }

    /**
     * Refresh stok semua item di listObatUmum sesuai depo baru.
     */
    public static function refreshStokDepoForList(array $listObatUmum, string $kd_depo): array
    {
        foreach ($listObatUmum as &$item) {
            $gudang = GudangBarang::where('kode_brng', $item['kode_brng'])
                ->where('kd_bangsal', $kd_depo)
                ->first();

            if ($gudang) {
                $item['stok'] = floatval($gudang->stok);
            }
        }

        return $listObatUmum;
    }

    /**
     * Simpan data validasi farmasi, kurangi stok gudang, dan update status resep ke "Sudah Dilayani".
     */
    public static function saveValidasi(array $payload): bool
    {
        return DB::transaction(function () use ($payload) {
            $noResep     = $payload['no_resep'] ?? null;
            $noRawat     = $payload['no_rawat'] ?? '';
            $tglValidasi = $payload['tgl_validasi'] ?? date('Y-m-d');
            $jamValidasi = sprintf(
                '%02d:%02d:%02d',
                intval($payload['jam_validasi'] ?? 0),
                intval($payload['menit_validasi'] ?? 0),
                intval($payload['detik_validasi'] ?? 0)
            );
            $kdDepo      = $payload['kd_depo'] ?? 'AP';
            $tarif       = $payload['tarif'] ?? 'Rawat Jalan';
            $status      = (str_contains(strtolower($tarif), 'bpjs') || str_contains(strtolower($tarif), 'ranap')) ? 'Ranap' : 'Ralan';
            $listObat    = $payload['list_obat'] ?? [];

            // 1. Update status resep_obat ke "Sudah Dilayani"
            if (!empty($noResep)) {
                DB::table('resep_obat')
                    ->where('no_resep', $noResep)
                    ->update([
                        'tgl_perawatan'  => $tglValidasi,
                        'jam'            => $jamValidasi,
                        'tgl_penyerahan' => $tglValidasi,
                        'jam_penyerahan' => $jamValidasi,
                    ]);
            }

            // 2. Simpan detail pemberian obat & kurangi stok gudang
            foreach ($listObat as $item) {
                if (isset($item['tercentang']) && !$item['tercentang']) {
                    continue; // Skip item yang uncheck
                }

                $kodeBrng = $item['kode_brng'];
                $jml      = floatval($item['jumlah'] ?? 0);
                if ($jml <= 0) {
                    continue;
                }

                $harga    = floatval($item['harga'] ?? 0);
                $embalase = floatval($item['embalase'] ?? 0);
                $tuslah   = floatval($item['tuslah'] ?? 0);
                $total    = ($harga * $jml) + $embalase + $tuslah;
                $noBatch  = !empty($item['no_batch']) && $item['no_batch'] !== '-' ? $item['no_batch'] : '-';
                $noFaktur = !empty($item['no_faktur']) && $item['no_faktur'] !== '-' ? $item['no_faktur'] : '-';

                // Insert / update ke detail_pemberian_obat
                DB::table('detail_pemberian_obat')->updateOrInsert(
                    [
                        'tgl_perawatan' => $tglValidasi,
                        'jam'           => $jamValidasi,
                        'no_rawat'      => $noRawat,
                        'kode_brng'     => $kodeBrng,
                        'no_batch'      => $noBatch,
                        'no_faktur'     => $noFaktur,
                    ],
                    [
                        'h_beli'     => $harga,
                        'biaya_obat' => $harga,
                        'jml'        => $jml,
                        'embalase'   => $embalase,
                        'tuslah'     => $tuslah,
                        'total'      => $total,
                        'status'     => $status,
                        'kd_bangsal' => $kdDepo,
                    ]
                );

                // Update stok di gudangbarang
                DB::table('gudangbarang')
                    ->where('kode_brng', $kodeBrng)
                    ->where('kd_bangsal', $kdDepo)
                    ->decrement('stok', $jml);
            }

            return true;
        });
    }
}
