<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PermintaanPemeriksaanLabPa extends Model
{
    protected $table = 'permintaan_pemeriksaan_labpa';
    public $timestamps = false;

    public function pemeriksaan()
    {
        return $this->belongsTo(JnsPerawatanLab::class, 'kd_jenis_prw', 'kd_jenis_prw');
    }

    public function permintaan()
    {
        return $this->belongsTo(PermintaanLabPa::class, 'noorder', 'noorder');
    }
}
