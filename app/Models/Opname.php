<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Opname extends Model
{
    protected $table = 'opname';
    public $timestamps = false;
    public $incrementing = false; // Composite primary key
    
    protected $fillable = [
        'kode_brng',
        'h_beli',
        'tanggal',
        'stok',
        'real',
        'selisih',
        'nomihilang',
        'lebih',
        'nomilebih',
        'keterangan',
        'kd_bangsal',
        'no_batch',
        'no_faktur'
    ];

    public function dataBarang()
    {
        return $this->belongsTo(DataBarang::class, 'kode_brng', 'kode_brng');
    }

    public function bangsal()
    {
        return $this->belongsTo(Bangsal::class, 'kd_bangsal', 'kd_bangsal');
    }
}
