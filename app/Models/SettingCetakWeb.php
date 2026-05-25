<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SettingCetakWeb extends Model
{
    protected $table = 'setting_cetak_web';

    protected $fillable = [
        'nama_instansi',
        'alamat_instansi',
        'kabupaten',
        'propinsi',
        'kontak',
        'email',
        'logo',
        'background',
    ];
}
