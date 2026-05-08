<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterTriaseSkala2 extends Model
{
    protected $table = 'master_triase_skala2';
    protected $primaryKey = 'kode_skala2';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    protected $fillable = ['kode_pemeriksaan', 'kode_skala2', 'pengkajian_skala2'];
}
