<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataTriaseIgdSekunder extends Model
{
    protected $table = 'data_triase_igdsekunder';
    protected $primaryKey = 'no_rawat';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    protected $fillable = [
        'no_rawat',
        'anamnesa_singkat',
        'catatan',
        'plan',
        'tanggaltriase',
        'nik'
    ];

    public function regPeriksa()
    {
        return $this->belongsTo(RegPeriksa::class, 'no_rawat', 'no_rawat');
    }

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'nik', 'nik');
    }
}
