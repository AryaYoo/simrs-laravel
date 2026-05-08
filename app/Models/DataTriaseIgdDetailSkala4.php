<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataTriaseIgdDetailSkala4 extends Model
{
    protected $table = 'data_triase_igddetail_skala4';
    public $incrementing = false;
    public $timestamps = false;
    protected $fillable = ['no_rawat', 'kode_skala4'];

    public function master()
    {
        return $this->belongsTo(MasterTriaseSkala4::class, 'kode_skala4', 'kode_skala4');
    }
}
