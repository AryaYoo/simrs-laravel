<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PeriksaRadiologi extends Model
{
    protected $table = 'periksa_radiologi';
    public $timestamps = false;
    public $incrementing = false;
    protected $primaryKey = 'no_rawat';

    protected $fillable = [
        'no_rawat',
        'tgl_periksa',
        'jam',
        'kd_jenis_prw',
        'kd_dokter',
        'nip',
        'proyeksi',
        'hasil',
        'biaya',
    ];

    public function jnsPerawatan()
    {
        return $this->belongsTo(JnsPerawatanRadiologi::class, 'kd_jenis_prw', 'kd_jenis_prw');
    }

    public function dokter()
    {
        return $this->belongsTo(Dokter::class, 'kd_dokter', 'kd_dokter');
    }

    public function petugas()
    {
        return $this->belongsTo(Petugas::class, 'nip', 'nip');
    }
}
