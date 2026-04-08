<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResumePasien extends Model
{
    protected $table = 'resume_pasien';
    protected $primaryKey = 'no_rawat';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'no_rawat',
        'kd_dokter',
        'keluhan_utama',
        'jalannya_penyakit',
        'pemeriksaan_penunjang',
        'hasil_laborat',
        'diagnosa_utama',
        'kd_diagnosa_utama',
        'diagnosa_sekunder',
        'kd_diagnosa_sekunder',
        'diagnosa_sekunder2',
        'kd_diagnosa_sekunder2',
        'diagnosa_sekunder3',
        'kd_diagnosa_sekunder3',
        'diagnosa_sekunder4',
        'kd_diagnosa_sekunder4',
        'prosedur_utama',
        'kd_prosedur_utama',
        'prosedur_sekunder',
        'kd_prosedur_sekunder',
        'prosedur_sekunder2',
        'kd_prosedur_sekunder2',
        'prosedur_sekunder3',
        'kd_prosedur_sekunder3',
        'kondisi_pulang',
        'obat_pulang',
    ];

    public function dokter()
    {
        return $this->belongsTo(Dokter::class, 'kd_dokter', 'kd_dokter');
    }

    public function regPeriksa()
    {
        return $this->belongsTo(RegPeriksa::class, 'no_rawat', 'no_rawat');
    }
}
