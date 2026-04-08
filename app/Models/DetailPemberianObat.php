<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailPemberianObat extends Model
{
    protected $table = 'detail_pemberian_obat';
    public $timestamps = false;
    public $incrementing = false;
    protected $primaryKey = 'no_rawat';

    protected $fillable = [
        'no_rawat',
        'tgl_pemberian',
        'jam_pemberian',
        'kode_brng',
        'h_beli',
        'biaya_obat',
        'jml',
        'embalase',
        'tuslah',
        'total',
        'status',
        'kd_bangsal',
        'no_batch',
        'no_faktur',
    ];

    public function regPeriksa()
    {
        return $this->belongsTo(RegPeriksa::class, 'no_rawat', 'no_rawat');
    }

    public function barang()
    {
        return $this->belongsTo(DataBarang::class, 'kode_brng', 'kode_brng');
    }
}
