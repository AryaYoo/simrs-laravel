<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TemplateLaboratorium extends Model
{
    protected $table = 'template_laboratorium';
    protected $primaryKey = 'id_template';
    public $timestamps = false;

    protected $fillable = [
        'id_template',
        'kd_jenis_prw',
        'Pemeriksaan',
        'satuan',
        'nilai_rujukan_ld',
        'nilai_rujukan_la',
        'nilai_rujukan_pd',
        'nilai_rujukan_pa',
        'bagian',
        'urut',
    ];

    public function detailPeriksa()
    {
        return $this->hasMany(DetailPeriksaLab::class, 'id_template', 'id_template');
    }

    public function pemeriksaanHeader()
    {
        return $this->belongsTo(JnsPerawatanLab::class, 'kd_jenis_prw', 'kd_jenis_prw');
    }
}

