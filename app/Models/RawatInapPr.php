<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RawatInapPr extends Model
{
    protected $table = 'rawat_inap_pr';
    public $timestamps = false;
    protected $primaryKey = 'no_rawat';
    public $incrementing = false;
    protected $keyType = 'string';

    /**
     * Override for composite primary keys.
     */
    protected function setKeysForSaveQuery($query)
    {
        $keys = ['no_rawat', 'kd_jenis_prw', 'nip', 'tgl_perawatan', 'jam_rawat'];
        foreach ($keys as $keyName) {
            $query->where($keyName, '=', $this->getAttribute($keyName));
        }
        return $query;
    }

    protected $fillable = [
        'no_rawat',
        'kd_jenis_prw',
        'nip',
        'tgl_perawatan',
        'jam_rawat',
        'material',
        'bhp',
        'tarif_tindakanpr',
        'kso',
        'menejemen',
        'biaya_rawat',
    ];

    public function regPeriksa()
    {
        return $this->belongsTo(RegPeriksa::class, 'no_rawat', 'no_rawat');
    }

    public function jnsPerawatan()
    {
        return $this->belongsTo(JnsPerawatanInap::class, 'kd_jenis_prw', 'kd_jenis_prw');
    }

    public function petugas()
    {
        return $this->belongsTo(Petugas::class, 'nip', 'nip');
    }
}
