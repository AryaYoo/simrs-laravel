<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TemplatePelaksanaanInformasiEdukasi extends Model
{
    protected $table = 'template_pelaksanaan_informasi_edukasi';
    public $timestamps = false;
    protected $primaryKey = 'no_template';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'no_template',
        'materi_edukasi',
        'lama_edukasi',
        'metode_edukasi',
    ];
}

