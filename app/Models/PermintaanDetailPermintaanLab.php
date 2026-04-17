<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PermintaanDetailPermintaanLab extends Model
{
    protected $table = 'permintaan_detail_permintaan_lab';
    public $timestamps = false;
    protected $primaryKey = ['noorder', 'kd_jenis_prw', 'id_template'];
    public $incrementing = false;

    protected $fillable = [
        'noorder',
        'kd_jenis_prw',
        'id_template',
        'stts_bayar',
    ];

    public function template()
    {
        return $this->belongsTo(TemplateLaboratorium::class, 'id_template', 'id_template');
    }

    public function pemeriksaan()
    {
        return $this->belongsTo(JnsPerawatanLab::class, 'kd_jenis_prw', 'kd_jenis_prw');
    }
}
