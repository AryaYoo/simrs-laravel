<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailJual extends Model
{
    protected $table    = 'detailjual';
    public $incrementing = false;
    public $timestamps  = false;

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

    protected $casts = [
        'h_jual'   => 'float',
        'h_beli'   => 'float',
        'jumlah'   => 'float',
        'subtotal' => 'float',
        'dis'      => 'float',
        'bsr_dis'  => 'float',
        'tambahan' => 'float',
        'embalase' => 'float',
        'tuslah'   => 'float',
        'total'    => 'float',
    ];

    // -----------------------------------------------------------------------
    // Relations
    // -----------------------------------------------------------------------

    public function penjualan(): BelongsTo
    {
        return $this->belongsTo(Penjualan::class, 'nota_jual', 'nota_jual');
    }

    public function barang(): BelongsTo
    {
        return $this->belongsTo(DataBarang::class, 'kode_brng', 'kode_brng');
    }

    public function satuan(): BelongsTo
    {
        return $this->belongsTo(KodeSatuan::class, 'kode_sat', 'kode_sat');
    }

    // -----------------------------------------------------------------------
    // Helpers
    // -----------------------------------------------------------------------

    /** Nama barang lengkap, fallback ke kode jika belum di-eager-load. */
    public function namaBarang(): string
    {
        return $this->barang->nama_brng ?? $this->kode_brng ?? '-';
    }

    /** Nama satuan, fallback ke kode satuan. */
    public function namaSatuan(): string
    {
        return $this->satuan->satuan ?? $this->kode_sat ?? '-';
    }
}
