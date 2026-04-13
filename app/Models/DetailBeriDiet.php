<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailBeriDiet extends Model
{
    protected $table = 'detail_beri_diet';
    public $timestamps = false;

    public function diet()
    {
        return $this->belongsTo(Diet::class, 'kd_diet', 'kd_diet');
    }
}
