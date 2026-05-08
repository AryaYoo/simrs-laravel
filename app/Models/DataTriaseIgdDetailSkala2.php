<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataTriaseIgdDetailSkala2 extends Model
{
    protected $table = 'data_triase_igddetail_skala2';
    public $incrementing = false;
    public $timestamps = false;
    protected $fillable = ['no_rawat', 'kode_skala2'];

    public function master()
    {
        return $this->belongsTo(MasterTriaseSkala2::class, 'kode_skala2', 'kode_skala2');
    }
}
