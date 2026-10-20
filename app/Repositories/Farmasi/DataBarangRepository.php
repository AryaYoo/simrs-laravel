<?php

namespace App\Repositories\Farmasi;

use App\Models\DataBarang;
use App\Models\GolonganBarang;
use App\Models\IndustriFarmasi;
use App\Models\Jenis;
use App\Models\KategoriBarang;
use App\Models\KodeSatuan;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DataBarangRepository
{
    public function getList(
        string $search = '',
        ?string $kdjns = null,
        ?string $kode_kategori = null,
        ?string $kode_golongan = null,
        ?string $status = null,
        ?string $stokFilter = null,
        int $perPage = 15
    ): LengthAwarePaginator {
        $query = DataBarang::with([
            'satuanBesar',
            'satuanKecil',
            'jenis',
            'kategori',
            'golongan',
            'industri',
        ])
        ->withSum('gudangBarang as total_stok', 'stok')
        ->when($search, function ($q) use ($search) {
            $q->where(function ($sub) use ($search) {
                $sub->where('kode_brng', 'like', "%{$search}%")
                    ->orWhere('nama_brng', 'like', "%{$search}%")
                    ->orWhere('letak_barang', 'like', "%{$search}%")
                    ->orWhereHas('satuanBesar', function ($qS) use ($search) {
                        $qS->where('satuan', 'like', "%{$search}%");
                    })
                    ->orWhereHas('satuanKecil', function ($qS) use ($search) {
                        $qS->where('satuan', 'like', "%{$search}%");
                    });
            });
        })
        ->when($kdjns, function ($q) use ($kdjns) {
            $q->where('kdjns', $kdjns);
        })
        ->when($kode_kategori, function ($q) use ($kode_kategori) {
            $q->where('kode_kategori', $kode_kategori);
        })
        ->when($kode_golongan, function ($q) use ($kode_golongan) {
            $q->where('kode_golongan', $kode_golongan);
        })
        ->when($status !== null && $status !== '', function ($q) use ($status) {
            $q->where('status', $status);
        })
        ->when($stokFilter === 'kritis', function ($q) {
            $q->where('stokminimal', '>', 0)
              ->havingRaw('COALESCE(total_stok, 0) <= stokminimal');
        })
        ->when($stokFilter === 'habis', function ($q) {
            $q->havingRaw('COALESCE(total_stok, 0) <= 0');
        })
        ->when($stokFilter === 'aman', function ($q) {
            $q->havingRaw('COALESCE(total_stok, 0) > stokminimal');
        });

        $newItems = session('newly_created_barang', []);
        if (!empty($newItems)) {
            $escapedCodes = implode("','", array_map('addslashes', $newItems));
            $query->orderByRaw("CASE WHEN databarang.kode_brng IN ('{$escapedCodes}') THEN 0 ELSE 1 END");
        }

        $query->orderBy('nama_brng', 'asc');

        return $query->paginate($perPage);
    }

    public function getDetail(string $kode_brng): ?DataBarang
    {
        return DataBarang::with([
            'satuanBesar',
            'satuanKecil',
            'jenis',
            'kategori',
            'golongan',
            'industri',
        ])
        ->withSum('gudangBarang as total_stok', 'stok')
        ->find($kode_brng);
    }

    public function delete(string $kode_brng): bool
    {
        $barang = DataBarang::find($kode_brng);
        if ($barang) {
            return (bool) $barang->delete();
        }
        return false;
    }

    public function getMasterJenis(): Collection
    {
        return Jenis::orderBy('nama', 'asc')->get();
    }

    public function getMasterKategori(): Collection
    {
        return KategoriBarang::orderBy('nama', 'asc')->get();
    }

    public function getMasterGolongan(): Collection
    {
        return GolonganBarang::orderBy('nama', 'asc')->get();
    }

    public function getMasterSatuan(): Collection
    {
        return KodeSatuan::orderBy('satuan', 'asc')->get();
    }

    public function getMasterIndustri(): Collection
    {
        return IndustriFarmasi::orderBy('nama_industri', 'asc')->get();
    }

    public function generateNextKode(): string
    {
        $last = DataBarang::where('kode_brng', 'like', 'B%')
            ->orderBy('kode_brng', 'desc')
            ->value('kode_brng');

        if ($last && preg_match('/^B(\d+)$/', $last, $matches)) {
            $nextNumber = intval($matches[1]) + 1;
            return 'B' . str_pad((string)$nextNumber, strlen($matches[1]), '0', STR_PAD_LEFT);
        }

        return 'B000000001';
    }
}
