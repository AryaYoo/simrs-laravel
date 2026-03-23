<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PermintaanRanap extends Model
{
    protected $table = 'permintaan_ranap';
    public $timestamps = false;

    protected $fillable = [
        'no_rawat',
        'tanggal',
        'kd_kamar',
        'diagnosa',
        'catatan',
    ];

    public function regPeriksa()
    {
        return $this->belongsTo(RegPeriksa::class, 'no_rawat', 'no_rawat');
    }

    public function kamar()
    {
        return $this->belongsTo(Kamar::class, 'kd_kamar', 'kd_kamar');
    }
}
