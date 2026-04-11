<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PemeriksaanRanap extends Model
{
    protected $table = 'pemeriksaan_ranap';
    protected $primaryKey = 'no_rawat';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;


    protected $fillable = [
        'no_rawat',
        'tgl_perawatan',
        'jam_rawat',
        'suhu_tubuh',
        'tensi',
        'nadi',
        'respirasi',
        'tinggi',
        'berat',
        'spo2',
        'gcs',
        'kesadaran',
        'keluhan',
        'pemeriksaan',
        'alergi',
        'penilaian',
        'rtl',
        'instruksi',
        'evaluasi',
        'nip',
    ];

    public function regPeriksa()
    {
        return $this->belongsTo(RegPeriksa::class, 'no_rawat', 'no_rawat');
    }

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'nip', 'nik');
    }

    /**
     * Set the keys for a save update query.
     * Required for composite primary keys.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function setKeysForSaveQuery($query)
    {
        $query->where('no_rawat', $this->getAttribute('no_rawat'))
              ->where('tgl_perawatan', $this->getAttribute('tgl_perawatan'))
              ->where('jam_rawat', $this->getAttribute('jam_rawat'));

        return $query;
    }
}
