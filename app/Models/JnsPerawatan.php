<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JnsPerawatan extends Model
{
    protected $table = 'jns_perawatan';
    protected $primaryKey = 'kd_jns_prw';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'kd_jns_prw',
        'nm_perawatan',
        'kd_kategori',
        'material',
        'tarif_tindakan_dokter',
        'tarif_tindakan_petugas',
        'tarif_karyawan',
        'total_byr',
        'status',
    ];
}
