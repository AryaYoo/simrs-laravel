<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KategoriPenyakit extends Model
{
    protected $table = 'kategori_penyakit';
    protected $primaryKey = 'kd_ktg';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'kd_ktg',
        'nm_kategori',
        'ciri_umum',
    ];
}
