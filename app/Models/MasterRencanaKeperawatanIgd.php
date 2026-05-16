<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterRencanaKeperawatanIgd extends Model
{
    protected $table = 'master_rencana_keperawatan_igd';
    protected $primaryKey = 'kode_rencana';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $guarded = [];

    public function masalah()
    {
        return $this->belongsTo(MasterMasalahKeperawatanIgd::class, 'kode_masalah', 'kode_masalah');
    }
}
