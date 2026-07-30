<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KodeSatuan extends Model
{
    protected $table      = 'kodesatuan';
    protected $primaryKey = 'kode_sat';
    public    $incrementing = false;
    protected $keyType    = 'string';
    public    $timestamps = false;

    protected $fillable = [
        'kode_sat',
        'satuan',
    ];

    // -----------------------------------------------------------------------
    // Relations
    // -----------------------------------------------------------------------

    public function detailJual(): HasMany
    {
        return $this->hasMany(DetailJual::class, 'kode_sat', 'kode_sat');
    }
}
