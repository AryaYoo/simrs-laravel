<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IndustriFarmasi extends Model
{
    protected $table = 'industrifarmasi';
    protected $primaryKey = 'kode_industri';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'kode_industri',
        'nama_industri',
        'alamat',
        'kota',
        'no_telp',
    ];
}
