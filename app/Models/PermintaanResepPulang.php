<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PermintaanResepPulang extends Model
{
    protected $table = 'permintaan_resep_pulang';
    protected $primaryKey = 'no_permintaan';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'no_permintaan',
        'tgl_permintaan',
        'jam',
        'no_rawat',
        'kd_dokter',
        'status',
        'tgl_validasi',
        'jam_validasi'
    ];

    public function regPeriksa()
    {
        return $this->belongsTo(RegPeriksa::class, 'no_rawat', 'no_rawat');
    }

    public function dokter()
    {
        return $this->belongsTo(Dokter::class, 'kd_dokter', 'kd_dokter');
    }

    public function detailPermintaan()
    {
        return $this->hasMany(DetailPermintaanResepPulang::class, 'no_permintaan', 'no_permintaan');
    }
}
