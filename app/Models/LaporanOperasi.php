<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LaporanOperasi extends Model
{
    protected $table = 'laporan_operasi';
    protected $primaryKey = 'no_rawat';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'no_rawat',
        'tanggal',
        'diagnosa_preop',
        'diagnosa_postop',
        'jaringan_dieksekusi',
        'selesaioperasi',
        'permintaan_pa',
        'nomor_implan',
        'laporan_operasi',
    ];

    public function regPeriksa()
    {
        return $this->belongsTo(RegPeriksa::class, 'no_rawat', 'no_rawat');
    }
}