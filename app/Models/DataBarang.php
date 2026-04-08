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
        'kode_sat',
        'letak_barang',
        'h_beli',
        'ralan',
        'beliluar',
        'jualbimms',
        'karyawan',
        'stok',
        'kategori',
        'status',
    ];
}
