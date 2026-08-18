<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataBarang extends Model
{
    protected $table = 'databarang';
    protected $primaryKey = 'kode_brng';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'kode_brng',
        'nama_brng',
        'kode_satbesar',
        'kode_sat',
        'letak_barang',
        'dasar',
        'h_beli',
        'ralan',
        'kelas1',
        'kelas2',
        'kelas3',
        'utama',
        'vip',
        'vvip',
        'beliluar',
        'jualbebas',
        'karyawan',
        'stokminimal',
        'kdjns',
        'isi',
        'kapasitas',
        'expire',
        'status',
        'kode_industri',
        'kode_kategori',
        'kode_golongan',
    ];
}
