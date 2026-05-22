<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CatatanAdimeGizi extends Model
{
    protected $table = 'catatan_adime_gizi';

    public $timestamps = false;
    protected $primaryKey = 'no_rawat';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'no_rawat',
        'tanggal',
        'asesmen',
        'diagnosis',
        'intervensi',
        'monitoring',
        'evaluasi',
        'instruksi',
        'nip',
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
}
