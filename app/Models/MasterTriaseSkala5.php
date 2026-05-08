<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterTriaseSkala5 extends Model
{
    protected $table = 'master_triase_skala5';
    protected $primaryKey = 'kode_skala5';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    protected $fillable = ['kode_pemeriksaan', 'kode_skala5', 'pengkajian_skala5'];
}
