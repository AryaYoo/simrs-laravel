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

    /**
     * Override for composite primary keys.
     */
    protected function setKeysForSaveQuery($query)
    {
        $keys = ['no_rawat', 'kd_jenis_prw', 'kd_dokter', 'nip', 'tgl_perawatan', 'jam_rawat'];
        foreach ($keys as $keyName) {
            $query->where($keyName, '=', $this->getAttribute($keyName));
        }
        return $query;
    }

    protected $guarded = [];

    public function regPeriksa()
    {
        return $this->belongsTo(RegPeriksa::class, 'no_rawat', 'no_rawat');
    }

    public function jnsPerawatan()
    {
        return $this->belongsTo(JnsPerawatanInap::class, 'kd_jenis_prw', 'kd_jenis_prw');
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
