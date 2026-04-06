<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppSetting extends Model
{
    // Mengarahkan ke tabel yang dibuat lewat Raw SQL tadi
    protected $table = 'pengaturan_aplikasi';
    public $timestamps = false; // Karena tidak ada created_at, updated_at di raw sqlnya

    protected $fillable = [
        'setting_key',
        'setting_value',
        'description',
    ];
}
