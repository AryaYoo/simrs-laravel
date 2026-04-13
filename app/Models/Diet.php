<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Diet extends Model
{
    protected $table = 'diet';
    protected $primaryKey = 'kd_diet';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = ['kd_diet', 'nama_diet'];
}
