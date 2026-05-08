<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataTriaseIgdDetailSkala1 extends Model
{
    protected $table = 'data_triase_igddetail_skala1';
    public $incrementing = false;
    public $timestamps = false;
    protected $fillable = ['no_rawat', 'kode_skala1'];

    public function master()
    {
        return $this->belongsTo(MasterTriaseSkala1::class, 'kode_skala1', 'kode_skala1');
    }
}
