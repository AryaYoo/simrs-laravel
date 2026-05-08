<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataTriaseIgdDetailSkala3 extends Model
{
    protected $table = 'data_triase_igddetail_skala3';
    public $incrementing = false;
    public $timestamps = false;
    protected $fillable = ['no_rawat', 'kode_skala3'];

    public function master()
    {
        return $this->belongsTo(MasterTriaseSkala3::class, 'kode_skala3', 'kode_skala3');
    }
}
