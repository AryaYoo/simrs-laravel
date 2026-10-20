<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Visite extends Model
{
    protected $table = 'visite';
    public $timestamps = false;

    protected $fillable = [
        'no_rawat',
        'tgl_visite',
        'jam_visite',
        'nip',
        'keluhan',
        'pemeriksaan',
        'suhu_tubuh',
        'tensi',
        'berat',
        'tinggi',
        'respirasi',
        'nadi',
        'spo2',
        'gcs',
        'kesadaran',
        'alergi',
        'penilaian',
        'rtl',
        'instruksi',
        'evaluasi',
        'riwayat_pengobatan',
        'interaksi_obat',
    ];

    public function regPeriksa()
    {
        return $this->belongsTo(RegPeriksa::class, 'no_rawat', 'no_rawat');
    }

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'nip', 'nik');
    }

    public function petugas()
    {
        return $this->belongsTo(Petugas::class, 'nip', 'nip');
    }
}
