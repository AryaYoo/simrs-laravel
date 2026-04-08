<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PemeriksaanRalan extends Model
{
    protected $table = 'pemeriksaan_ralan';
    public $timestamps = false;

    protected $fillable = [
        'no_rawat',
        'tgl_perawatan',
        'jam_rawat',
        'suhu_tubuh',
        'tensi',
        'nadi',
        'respirasi',
        'tinggi',
        'berat',
        'spo2',
        'gcs',
        'alergi',
        'lingkar_perut',
        'kesadaran',
        'keluhan',
        'pemeriksaan',
        'penilaian',
        'rtl',
        'instruksi',
        'evaluasi',
        'nip',
    ];

    public function regPeriksa()
    {
        return $this->belongsTo(RegPeriksa::class, 'no_rawat', 'no_rawat');
    }

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'nip', 'nik');
    }
}
