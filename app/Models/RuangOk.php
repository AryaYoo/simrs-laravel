<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RuangOk extends Model
{
    use HasFactory;

    protected $table = 'ruang_ok';
    protected $primaryKey = 'kd_ruang_ok';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'kd_ruang_ok',
        'nm_ruang_ok',
    ];
}
