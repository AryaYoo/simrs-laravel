<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterTriaseMacamKasus extends Model
{
    protected $table = 'master_triase_macam_kasus';
    protected $primaryKey = 'kode_kasus';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'kode_kasus',
        'macam_kasus',
    ];
}
