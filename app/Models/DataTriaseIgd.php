<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataTriaseIgd extends Model
{
    protected $table = 'data_triase_igd';
    protected $primaryKey = 'no_rawat';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'no_rawat',
        'tgl_kunjungan',
        'cara_masuk',
        'alat_transportasi',
        'alasan_kedatangan',
        'keterangan_kedatangan',
        'kode_kasus',
        'tekanan_darah',
        'nadi',
        'pernapasan',
        'suhu',
        'saturasi_o2',
        'nyeri',
    ];

    public function regPeriksa()
    {
        return $this->belongsTo(RegPeriksa::class, 'no_rawat', 'no_rawat');
    }

    public function macamKasus()
    {
        return $this->belongsTo(MasterTriaseMacamKasus::class, 'kode_kasus', 'kode_kasus');
    }
}
