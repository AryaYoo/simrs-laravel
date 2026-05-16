<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterMasalahKeperawatanIgd extends Model
{
    protected $table = 'master_masalah_keperawatan_igd';
    protected $primaryKey = 'kode_masalah';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $guarded = [];

    public function rencana()
    {
        return $this->hasMany(MasterRencanaKeperawatanIgd::class, 'kode_masalah', 'kode_masalah');
    }
}
