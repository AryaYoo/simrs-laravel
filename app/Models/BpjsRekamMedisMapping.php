<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BpjsRekamMedisMapping extends Model
{
    protected $table = 'bpjs_rekam_medis_mapping';
    public $timestamps = false;

    protected $fillable = [
        'kategori',
        'kode_rs',
        'nama_rs',
        'kode_standar',
        'nama_standar',
    ];
}
