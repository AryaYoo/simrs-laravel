<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaketOperasi extends Model
{
    use HasFactory;

    protected $table = 'paket_operasi';
    protected $primaryKey = 'kode_paket';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'kode_paket',
        'nm_perawatan',
    ];
}
