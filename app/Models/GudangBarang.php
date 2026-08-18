<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GudangBarang extends Model
{
    protected $table = 'gudangbarang';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'kode_brng',
        'kd_bangsal',
        'stok',
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
}
