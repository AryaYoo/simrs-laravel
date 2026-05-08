<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataTriaseIgdDetailSkala5 extends Model
{
    protected $table = 'data_triase_igddetail_skala5';
    public $incrementing = false;
    public $timestamps = false;
    protected $fillable = ['no_rawat', 'kode_skala5'];

    public function master()
    {
        return $this->belongsTo(MasterTriaseSkala5::class, 'kode_skala5', 'kode_skala5');
    }
}
