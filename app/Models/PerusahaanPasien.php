<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PerusahaanPasien extends Model
{
    protected $table = 'perusahaan_pasien';
    protected $primaryKey = 'kode_perusahaan';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    protected $guarded = [];
}
