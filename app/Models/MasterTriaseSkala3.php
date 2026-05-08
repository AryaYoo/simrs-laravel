<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterTriaseSkala3 extends Model
{
    protected $table = 'master_triase_skala3';
    protected $primaryKey = 'kode_skala3';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    protected $fillable = ['kode_pemeriksaan', 'kode_skala3', 'pengkajian_skala3'];
}
