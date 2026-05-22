<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterRencanaKeperawatan extends Model
{
    protected $table = 'master_rencana_keperawatan';
    protected $primaryKey = 'kode_rencana';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $guarded = [];

    public function masalah()
    {
        return $this->belongsTo(MasterMasalahKeperawatan::class, 'kode_masalah', 'kode_masalah');
    }
}
