<?php

namespace App\Repositories\Farmasi;

use App\Models\Opname;
use Illuminate\Support\Facades\DB;
use App\Livewire\Concerns\WithOptimisticLocking; // Wait, actually standard model operations for delete are fine without this here, but we can add it if needed later

class OpnameRepository
{
    public function getList(
        $search = '',
        $tanggal1 = null,
        $tanggal2 = null,
        $kd_bangsal = '',
        $kdjns = '',
        $kode_kategori = '',
        $kode_golongan = '',
        $perPage = 10
    ) {
        $query = Opname::with(['dataBarang.satuanKecil', 'bangsal'])
            ->select('opname.*', 'riwayat_barang_medis.jam as r_jam')
            ->leftJoin('riwayat_barang_medis', function($join) {
                $join->on('opname.kode_brng', '=', 'riwayat_barang_medis.kode_brng')
                     ->on('opname.tanggal', '=', 'riwayat_barang_medis.tanggal')
                     ->on('opname.kd_bangsal', '=', 'riwayat_barang_medis.kd_bangsal')
                     ->on('opname.no_batch', '=', 'riwayat_barang_medis.no_batch')
                     ->on('opname.no_faktur', '=', 'riwayat_barang_medis.no_faktur')
                     ->where('riwayat_barang_medis.posisi', '=', 'Opname');
            })
            ->when($search, function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('opname.kode_brng', 'like', "%{$search}%")
                        ->orWhereHas('dataBarang', function ($qBarang) use ($search) {
                            $qBarang->where('nama_brng', 'like', "%{$search}%");
                        })
                        ->orWhere('opname.no_batch', 'like', "%{$search}%")
                        ->orWhere('opname.no_faktur', 'like', "%{$search}%")
                        ->orWhere('opname.keterangan', 'like', "%{$search}%");
                });
            })
            ->when($tanggal1, function ($q) use ($tanggal1) {
                $q->whereDate('opname.tanggal', '>=', $tanggal1);
            })
            ->when($tanggal2, function ($q) use ($tanggal2) {
                $q->whereDate('opname.tanggal', '<=', $tanggal2);
            })
            ->when($kd_bangsal, function ($q) use ($kd_bangsal) {
                $q->where('opname.kd_bangsal', $kd_bangsal);
            })
            ->when($kdjns, function ($q) use ($kdjns) {
                $q->whereHas('dataBarang', function ($qBarang) use ($kdjns) {
                    $qBarang->where('kdjns', $kdjns);
                });
            })
            ->when($kode_kategori, function ($q) use ($kode_kategori) {
                $q->whereHas('dataBarang', function ($qBarang) use ($kode_kategori) {
                    $qBarang->where('kode_kategori', $kode_kategori);
                });
            })
            ->when($kode_golongan, function ($q) use ($kode_golongan) {
                $q->whereHas('dataBarang', function ($qBarang) use ($kode_golongan) {
                    $qBarang->where('kode_golongan', $kode_golongan);
                });
            })
            ->orderBy('opname.tanggal', 'desc')
            ->orderBy('r_jam', 'desc');

        return $query->paginate($perPage);
    }

    public function getSummary(
        $search = '',
        $tanggal1 = null,
        $tanggal2 = null,
        $kd_bangsal = '',
        $kdjns = '',
        $kode_kategori = '',
        $kode_golongan = ''
    ) {
        $query = Opname::query()
            ->when($search, function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('opname.kode_brng', 'like', "%{$search}%")
                        ->orWhereHas('dataBarang', function ($qBarang) use ($search) {
                            $qBarang->where('nama_brng', 'like', "%{$search}%");
                        })
                        ->orWhere('opname.no_batch', 'like', "%{$search}%")
                        ->orWhere('opname.no_faktur', 'like', "%{$search}%")
                        ->orWhere('opname.keterangan', 'like', "%{$search}%");
                });
            })
            ->when($tanggal1, function ($q) use ($tanggal1) {
                $q->whereDate('opname.tanggal', '>=', $tanggal1);
            })
            ->when($tanggal2, function ($q) use ($tanggal2) {
                $q->whereDate('opname.tanggal', '<=', $tanggal2);
            })
            ->when($kd_bangsal, function ($q) use ($kd_bangsal) {
                $q->where('opname.kd_bangsal', $kd_bangsal);
            })
            ->when($kdjns, function ($q) use ($kdjns) {
                $q->whereHas('dataBarang', function ($qBarang) use ($kdjns) {
                    $qBarang->where('kdjns', $kdjns);
                });
            })
            ->when($kode_kategori, function ($q) use ($kode_kategori) {
                $q->whereHas('dataBarang', function ($qBarang) use ($kode_kategori) {
                    $qBarang->where('kode_kategori', $kode_kategori);
                });
            })
            ->when($kode_golongan, function ($q) use ($kode_golongan) {
                $q->whereHas('dataBarang', function ($qBarang) use ($kode_golongan) {
                    $qBarang->where('kode_golongan', $kode_golongan);
                });
            });

        $res = $query->select(
            DB::raw('COUNT(*) as total_record'),
            DB::raw('COALESCE(SUM(opname.real * opname.h_beli), 0) as total_real'),
            DB::raw('COALESCE(SUM(opname.nomihilang), 0) as total_hilang'),
            DB::raw('COALESCE(SUM(opname.nomilebih), 0) as total_lebih')
        )->first();

        return [
            'record' => (int) ($res->total_record ?? 0),
            'real'   => (float) ($res->total_real ?? 0),
            'hilang' => (float) ($res->total_hilang ?? 0),
            'lebih'  => (float) ($res->total_lebih ?? 0),
        ];
    }

    public function getMasterBangsal()
    {
        return \App\Models\Bangsal::where('status', '1')->orWhereNull('status')->orderBy('nm_bangsal', 'asc')->get();
    }

    public function getMasterJenis()
    {
        return \App\Models\Jenis::orderBy('nama', 'asc')->get();
    }

    public function getMasterKategori()
    {
        return \App\Models\KategoriBarang::orderBy('nama', 'asc')->get();
    }

    public function getMasterGolongan()
    {
        return \App\Models\GolonganBarang::orderBy('nama', 'asc')->get();
    }

    public function delete($kode_brng, $tanggal, $kd_bangsal, $no_batch, $no_faktur)
    {
        return Opname::where('kode_brng', $kode_brng)
            ->where('tanggal', $tanggal)
            ->where('kd_bangsal', $kd_bangsal)
            ->where('no_batch', $no_batch)
            ->where('no_faktur', $no_faktur)
            ->delete();
    }
}
