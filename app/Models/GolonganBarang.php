<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GolonganBarang extends Model
{
    protected $table = 'golongan_barang';
    protected $primaryKey = 'kode';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'kode',
        'nama',
    ];
}
