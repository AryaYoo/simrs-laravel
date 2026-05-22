<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CatatanObservasiCHBP extends Model
{
    protected $table = 'catatan_observasi_chbp';

    public $timestamps = false;
    protected $primaryKey = 'no_rawat';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'no_rawat',
        'tgl_perawatan',
        'jam_rawat',
        'td',
        'hr',
        'suhu',
        'djj',
        'his',
        'ppv',
        'keterangan',
        'nip',
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
