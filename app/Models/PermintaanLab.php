<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PermintaanLab extends Model
{
    protected $table = 'permintaan_lab';
    protected $primaryKey = 'noorder';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    public function detailPemeriksaan()
    {
        return $this->hasMany(PermintaanPemeriksaanLab::class, 'noorder', 'noorder');
    }

    public function dokter()
    {
        return $this->belongsTo(Dokter::class, 'dokter_perujuk', 'kd_dokter');
    }
}
