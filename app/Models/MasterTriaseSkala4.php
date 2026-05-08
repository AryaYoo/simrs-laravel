<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterTriaseSkala4 extends Model
{
    protected $table = 'master_triase_skala4';
    protected $primaryKey = 'kode_skala4';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    protected $fillable = ['kode_pemeriksaan', 'kode_skala4', 'pengkajian_skala4'];
}
