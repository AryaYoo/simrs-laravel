<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Icd9 extends Model
{
    protected $table = 'icd9';
    protected $primaryKey = 'kode';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'kode',
        'deskripsi_panjang',
        'deskripsi_pendek',
    ];
}
