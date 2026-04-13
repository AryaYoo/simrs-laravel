<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResepPulang extends Model
{
    protected $table = 'resep_pulang';
    public $timestamps = false;

    public function barang()
    {
        return $this->belongsTo(DataBarang::class, 'kode_brng', 'kode_brng');
    }

    public function bangsal()
    {
        return $this->belongsTo(Bangsal::class, 'kd_bangsal', 'kd_bangsal');
    }
}
