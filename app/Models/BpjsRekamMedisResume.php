<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BpjsRekamMedisResume extends Model
{
    protected $table = 'bpjs_rekam_medis_resume';
    protected $primaryKey = 'no_rawat';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'no_rawat',
        'no_sep',
        'keluhan_utama',
        'riwayat_penyakit',
        'diagnosis_masuk',
        'pemeriksaan_fisik',
        'plan_of_care',
        'instruksi_pulang',
        'alergi',
        'tgl_input',
        'petugas_id',
    ];
}
