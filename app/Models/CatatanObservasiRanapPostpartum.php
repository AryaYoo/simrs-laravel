<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CatatanObservasiRanapPostpartum extends Model
{
    protected $table = 'catatan_observasi_ranap_postpartum';

    public $timestamps = false;
    protected $primaryKey = 'no_rawat';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'no_rawat',
        'tgl_perawatan',
        'jam_rawat',
        'gcs',
        'td',
        'hr',
        'rr',
        'suhu',
        'spo2',
        'tfu',
        'kontraksi',
        'perdarahan',
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
