<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PermintaanPemeriksaanLab extends Model
{
    protected $table = 'permintaan_pemeriksaan_lab';
    public $timestamps = false;

    public function jnsPemeriksaan()
    {
        return $this->belongsTo(JnsPerawatanLab::class, 'kd_jenis_prw', 'kd_jenis_prw');
    }

    public function permintaan()
    {
        return $this->belongsTo(PermintaanLab::class, 'noorder', 'noorder');
    }
}
