<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BridgingSep extends Model
{
    protected $table = 'bridging_sep';
    protected $primaryKey = 'no_sep';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'no_sep',
        'no_rawat',
        'tglsep',
        'jnspelayanan',
        'nomr',
        'nama_pasien',
        'diagawal',
        'nmdiagnosaawal',
    ];

    public function regPeriksa()
    {
        return $this->belongsTo(RegPeriksa::class, 'no_rawat', 'no_rawat');
    }

    public function resume()
    {
        return $this->hasOne(BpjsRekamMedisResume::class, 'no_rawat', 'no_rawat');
    }

    public function logs()
    {
        return $this->hasMany(BpjsRekamMedisLog::class, 'no_sep', 'no_sep');
    }
}
