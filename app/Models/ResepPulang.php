<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResepPulang extends Model
{
    protected $table = 'resep_pulang';
    public $timestamps = false;

    public $incrementing = false;

    protected $fillable = [
        'no_rawat',
        'kode_brng',
        'jml_barang',
        'harga',
        'total',
        'dosis',
        'tanggal',
        'jam',
        'kd_bangsal',
        'no_batch',
        'no_faktur',
    ];

    public function barang()
    {
        return $this->belongsTo(DataBarang::class, 'kode_brng', 'kode_brng');
    }

    public function bangsal()
    {
        return $this->belongsTo(Bangsal::class, 'kd_bangsal', 'kd_bangsal');
    }

    public function regPeriksa()
    {
        return $this->belongsTo(RegPeriksa::class, 'no_rawat', 'no_rawat');
    }
}
