<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Penjualan extends Model
{
    protected $table      = 'penjualan';
    protected $primaryKey = 'nota_jual';
    public    $incrementing = false;
    protected $keyType    = 'string';
    public    $timestamps = false;

    protected $fillable = [
        'nota_jual',
        'tgl_jual',
        'nip',
        'no_rkm_medis',
        'nm_pasien',
        'keterangan',
        'jns_jual',
        'ongkir',
        'ppn',
        'status',
        'kd_bangsal',
        'kd_rek',
        'nama_bayar',
    ];

    protected $casts = [
        'tgl_jual' => 'date',
        'ppn'      => 'float',
        'ongkir'   => 'float',
    ];

    // -----------------------------------------------------------------------
    // Relations
    // -----------------------------------------------------------------------

    public function detailJual(): HasMany
    {
        return $this->hasMany(DetailJual::class, 'nota_jual', 'nota_jual');
    }

    public function petugas(): BelongsTo
    {
        return $this->belongsTo(Petugas::class, 'nip', 'nip');
    }

    public function bangsal(): BelongsTo
    {
        return $this->belongsTo(Bangsal::class, 'kd_bangsal', 'kd_bangsal');
    }

    public function pasien(): BelongsTo
    {
        return $this->belongsTo(Pasien::class, 'no_rkm_medis', 'no_rkm_medis');
    }

    public function rekening(): BelongsTo
    {
        return $this->belongsTo(Rekening::class, 'kd_rek', 'kd_rek');
    }

    // -----------------------------------------------------------------------
    // Helpers
    // -----------------------------------------------------------------------

    /** Label jenis jual untuk tampilan badge. */
    public function jnsJualLabel(): string
    {
        return $this->jns_jual ?? '-';
    }

    /** CSS class badge berdasarkan jenis jual. */
    public function jnsJualBadgeClass(): string
    {
        return match ($this->jns_jual) {
            'Jual Bebas'  => 'bg-blue-100 text-blue-700',
            'Karyawan'    => 'bg-purple-100 text-purple-700',
            'Rawat Jalan' => 'bg-green-100 text-green-700',
            'Utama/BPJS'  => 'bg-teal-100 text-teal-700',
            'VIP', 'VVIP' => 'bg-amber-100 text-amber-700',
            default       => 'bg-neutral-100 text-neutral-600',
        };
    }

    /** Apakah sudah dibayar. */
    public function isSudahDibayar(): bool
    {
        return $this->status === 'Sudah Dibayar';
    }
}
