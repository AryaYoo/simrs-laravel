<?php

namespace App\Repositories\RawatInap;

use Illuminate\Support\Facades\DB;
use App\Models\ResepObat;
use App\Models\ResepDokter as ResepDokterModel;
use App\Models\Dokter;

class ResepDokterRepository
{
    /**
     * Get obat list for the cart selection.
     */
    public static function getObatList(string $searchObat = '', array $cartKodes = [], int $perPage = 10)
    {
        $query = DB::table('databarang')
            ->leftJoin('kodesatuan', 'databarang.kode_sat', '=', 'kodesatuan.kode_sat')
            ->leftJoin('kategori_barang', 'databarang.kode_kategori', '=', 'kategori_barang.kode')
            ->leftJoin('industrifarmasi', 'databarang.kode_industri', '=', 'industrifarmasi.kode_industri')
            ->leftJoin('gudangbarang', 'databarang.kode_brng', '=', 'gudangbarang.kode_brng')
            ->select(
                'databarang.kode_brng',
                'databarang.nama_brng',
                'databarang.kode_sat',
                'databarang.beliluar as harga',
                DB::raw("IFNULL(SUM(gudangbarang.stok), 0) as stok"),
                'kategori_barang.nama as jenis_obat',
                DB::raw("'-' as komposisi"),
                'industrifarmasi.nama_industri as industri_farmasi'
            )
            ->where('databarang.status', '1')
            ->groupBy(
                'databarang.kode_brng',
                'databarang.nama_brng',
                'databarang.kode_sat',
                'databarang.beliluar',
                'kategori_barang.nama',
                'industrifarmasi.nama_industri'
            );

        if (!empty($searchObat)) {
            $query->where(function ($q) use ($searchObat) {
                $q->where('databarang.nama_brng', 'like', '%' . $searchObat . '%')
                    ->orWhere('databarang.kode_brng', 'like', '%' . $searchObat . '%');
            });
        }

        // Exclude obat yang sudah ada di keranjang
        if (!empty($cartKodes)) {
            $query->whereNotIn('databarang.kode_brng', $cartKodes);
        }

        return $query->paginate($perPage);
    }

    /**
     * Get historically saved resep for the particular visit.
     */
    public static function getSavedReseps(string $no_rawat)
    {
        return ResepObat::with(['detail.barang'])
            ->where('no_rawat', $no_rawat)
            ->where('status', 'ranap')
            ->orderBy('tgl_peresepan', 'desc')
            ->orderBy('jam_peresepan', 'desc')
            ->get();
    }

    /**
     * Transact and save the cart.
     */
    public static function saveResep(array $data)
    {
        $no_rawat = $data['no_rawat'];
        $kd_dokter_peresep = $data['kd_dokter'];
        $tgl_peresepan = $data['tgl_peresepan'];
        $jamF = $data['jam'];
        $auto_nomor = $data['auto_nomor'];
        $no_resep_input = $data['no_resep_input'] ?? '';
        $cart = $data['cart'];

        return retry(5, function () use ($tgl_peresepan, $auto_nomor, $no_resep_input, $no_rawat, $kd_dokter_peresep, $jamF, $cart) {
            return DB::transaction(function () use ($tgl_peresepan, $auto_nomor, $no_resep_input, $no_rawat, $kd_dokter_peresep, $jamF, $cart) {
                $tglSekarang = \Carbon\Carbon::parse($tgl_peresepan)->format('Ymd');

                if ($auto_nomor || empty($no_resep_input)) {
                    // Generate NO RESEP (format YYYYMMDDXXXX)
                    // Gunakan lockForUpdate untuk mencegah proses lain membaca MAX yang sama secara bersamaan
                    $maxData = ResepObat::where('no_resep', 'like', $tglSekarang . '%')
                        ->lockForUpdate()
                        ->max('no_resep');

                    if ($maxData) {
                        $lastNumber = (int) substr($maxData, -4);
                        $newNumber = $lastNumber + 1;
                    } else {
                        $newNumber = 1;
                    }
                    $generatedNo = $tglSekarang . sprintf('%04d', $newNumber);
                } else {
                    $generatedNo = $no_resep_input;
                }

                // Create Header
                ResepObat::create([
                    'no_resep' => $generatedNo,
                    'tgl_perawatan' => $tgl_peresepan,
                    'jam' => $jamF,
                    'no_rawat' => $no_rawat,
                    'kd_dokter' => $kd_dokter_peresep,
                    'tgl_peresepan' => $tgl_peresepan,
                    'jam_peresepan' => $jamF,
                    'status' => 'ranap',
                    'tgl_penyerahan' => $tgl_peresepan,
                    'jam_penyerahan' => $jamF,
                ]);

                // Save details
                foreach ($cart as $item) {
                    ResepDokterModel::create([
                        'no_resep' => $generatedNo,
                        'kode_brng' => $item['kode_brng'],
                        'jml' => $item['jml'],
                        'aturan_pakai' => $item['aturan_pakai'],
                    ]);
                }

                return $generatedNo;
            });
        }, 100);
    }

    /**
     * Get dropdown/modal dokter list
     */
    public static function getListDokter(string $search = '', int $limit = 20)
    {
        $query = Dokter::where('status', '1');
        if (!empty($search)) {
            $query->where('nm_dokter', 'like', '%' . $search . '%');
        }
        return $query->limit($limit)->get()->toArray();
    }

    /**
     * Delete Resep completely
     */
    public static function deleteResep(string $no_resep)
    {
        DB::beginTransaction();
        try {
            ResepDokterModel::where('no_resep', $no_resep)->delete();
            ResepObat::where('no_resep', $no_resep)->delete();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
