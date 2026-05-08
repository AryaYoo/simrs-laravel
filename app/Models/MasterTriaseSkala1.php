<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterTriaseSkala1 extends Model
{
    protected $table = 'master_triase_skala1';
    protected $primaryKey = 'kode_skala1';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    protected $fillable = ['kode_pemeriksaan', 'kode_skala1', 'pengkajian_skala1'];
}
