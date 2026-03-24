<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RawatInapDrpr extends Model
{
    protected $table = 'rawat_inap_drpr';
    public $timestamps = false;
    protected $primaryKey = 'no_rawat'; // Not strictly unique, but helps Eloquent
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'no_rawat',
        'kd_jns_prw',
        'kd_dokter',
        'nip',
        'tgl_perawatan',
        'jam_rawat',
        'material',
        'tarif_dr',
        'tarif_pr',
        'kSO',
        'menejemen',
        'biaya_rawat',
    ];

    public function regPeriksa()
    {
        return $this->belongsTo(RegPeriksa::class, 'no_rawat', 'no_rawat');
    }

    public function jnsPerawatan()
    {
        return $this->belongsTo(JnsPerawatan::class, 'kd_jns_prw', 'kd_jns_prw');
    }

    public function dokter()
    {
        return $this->belongsTo(Dokter::class, 'kd_dokter', 'kd_dokter');
    }

    public function petugas()
    {
        return $this->belongsTo(Petugas::class, 'nip', 'nip');
    }
}
