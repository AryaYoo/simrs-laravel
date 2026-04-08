<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailPeriksaLab extends Model
{
    protected $table = 'detail_periksa_lab';
    public $timestamps = false;
    public $incrementing = false;
    protected $primaryKey = 'no_rawat'; // Composite key handled manually if needed

    protected $fillable = [
        'no_rawat',
        'kd_jenis_prw',
        'id_template',
        'nilai',
        'keterangan',
        'tgl_periksa',
        'jam',
    ];

    public function template()
    {
        return $this->belongsTo(TemplateLaboratorium::class, 'id_template', 'id_template');
    }

    public function jnsPerawatan()
    {
        return $this->belongsTo(JnsPerawatanLab::class, 'kd_jenis_prw', 'kd_jenis_prw');
    }
}
