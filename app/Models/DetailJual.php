<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailJual extends Model
{
    protected $table = 'detailjual';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'nota_jual',
        'kode_brng',
        'kode_sat',
        'h_jual',
        'h_beli',
        'jumlah',
        'subtotal',
        'dis',
        'bsr_dis',
        'tambahan',
        'embalase',
        'tuslah',
        'aturan_pakai',
        'total',
        'no_batch',
    ];

    public function penjualan()
    {
        return $this->belongsTo(Penjualan::class, 'nota_jual', 'nota_jual');
    }

    public function barang()
    {
        return $this->belongsTo(DataBarang::class, 'kode_brng', 'kode_brng');
    }

    public function satuan()
    {
        return $this->belongsTo(Kodesatuan::class, 'kode_sat', 'kode_sat');
    }
}
