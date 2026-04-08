<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JnsPerawatanLab extends Model
{
    protected $table = 'jns_perawatan_lab';
    protected $primaryKey = 'kd_jenis_prw';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'kd_jenis_prw',
        'nm_perawatan',
        'kd_kategori',
        'material',
        'tarif_tindakan_petugas',
        'tarif_tindakan_dokter',
        'total_byr',
        'kd_pj',
        'status',
    ];
}
