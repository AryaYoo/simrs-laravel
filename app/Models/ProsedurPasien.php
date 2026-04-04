<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProsedurPasien extends Model
{
    protected $table = 'prosedur_pasien';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'no_rawat',
        'kode',
        'status',
        'prioritas',
        'jumlah',
    ];

    public function icd9()
    {
        // Many SIMRS Khanza/Sik use a 'icd9' table for procedure codes.
        // If not, we might need to create an ICD9 model too.
        return $this->belongsTo(Icd9::class, 'kode', 'kode');
    }
}
