<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PenilaianAwalKeperawatanRanap extends Model
{
    protected $table = 'penilaian_awal_keperawatan_ranap';
    protected $primaryKey = 'no_rawat';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $guarded = [];

    public function regPeriksa()
    {
        return $this->belongsTo(RegPeriksa::class, 'no_rawat', 'no_rawat');
    }

    public function petugas1()
    {
        return $this->belongsTo(Petugas::class, 'nip1', 'nip');
    }

    public function petugas2()
    {
        return $this->belongsTo(Petugas::class, 'nip2', 'nip');
    }

    public function dokter()
    {
        return $this->belongsTo(Dokter::class, 'kd_dokter', 'kd_dokter');
    }

    public function masalah()
    {
        return $this->belongsToMany(
            MasterMasalahKeperawatan::class,
            'penilaian_awal_keperawatan_ranap_masalah',
            'no_rawat',
            'kode_masalah'
        );
    }

    public function detailRencana()
    {
        return $this->belongsToMany(
            MasterRencanaKeperawatan::class,
            'penilaian_awal_keperawatan_ranap_rencana',
            'no_rawat',
            'kode_rencana'
        );
    }
}
