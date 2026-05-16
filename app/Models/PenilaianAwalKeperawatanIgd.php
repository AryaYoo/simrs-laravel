<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PenilaianAwalKeperawatanIgd extends Model
{
    protected $table = 'penilaian_awal_keperawatan_igd';
    protected $primaryKey = 'no_rawat';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $guarded = [];

    public function regPeriksa()
    {
        return $this->belongsTo(RegPeriksa::class, 'no_rawat', 'no_rawat');
    }

    public function petugas()
    {
        return $this->belongsTo(Petugas::class, 'nip', 'nip');
    }

    public function masalah()
    {
        return $this->belongsToMany(
            MasterMasalahKeperawatanIgd::class,
            'penilaian_awal_keperawatan_igd_masalah',
            'no_rawat',
            'kode_masalah'
        );
    }

    public function detailRencana()
    {
        return $this->belongsToMany(
            MasterRencanaKeperawatanIgd::class,
            'penilaian_awal_keperawatan_ralan_rencana_igd',
            'no_rawat',
            'kode_rencana'
        );
    }
}
