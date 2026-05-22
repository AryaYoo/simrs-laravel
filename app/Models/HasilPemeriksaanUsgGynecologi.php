<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HasilPemeriksaanUsgGynecologi extends Model
{
    use HasFactory;

    protected $table = 'hasil_pemeriksaan_usg_gynecologi';
    
    protected $primaryKey = 'no_rawat';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'no_rawat',
        'tanggal',
        'kd_dokter',
        'diagnosa_klinis',
        'kiriman_dari',
        'uterus',
        'parametrium',
        'ovarium',
        'doppler',
        'kesimpulan',
    ];

    protected $casts = [
        'tanggal' => 'datetime',
    ];

    public function dokter()
    {
        return $this->belongsTo(Dokter::class, 'kd_dokter', 'kd_dokter');
    }

    public function regPeriksa()
    {
        return $this->belongsTo(RegPeriksa::class, 'no_rawat', 'no_rawat');
    }
}
