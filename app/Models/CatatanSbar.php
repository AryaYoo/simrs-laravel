<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CatatanSbar extends Model
{
    protected $table = 'catatan_sbar';

    public $timestamps = false;
    protected $primaryKey = 'no_rawat';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'no_rawat',
        'tanggal',
        'nip',
        'kd_dokter',
        'situation',
        'background',
        'assessment',
        'recommendation',
        'advice',
        'status_baca',
        'status_konfirmasi',
    ];

    protected $casts = [
        'tanggal' => 'datetime',
    ];

    public function regPeriksa()
    {
        return $this->belongsTo(RegPeriksa::class, 'no_rawat', 'no_rawat');
    }

    public function petugas()
    {
        return $this->belongsTo(Petugas::class, 'nip', 'nip');
    }

    public function dokter()
    {
        return $this->belongsTo(Dokter::class, 'kd_dokter', 'kd_dokter');
    }
}
