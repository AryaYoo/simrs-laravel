<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PasienBayi extends Model
{
    protected $table = 'pasien_bayi';
    protected $primaryKey = 'no_rkm_medis';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $guarded = [];

    public function pasien()
    {
        return $this->belongsTo(Pasien::class, 'no_rkm_medis', 'no_rkm_medis');
    }

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'penolong', 'nik');
    }
}
