<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CatatanPerawatan extends Model
{
    protected $table = 'catatan_perawatan';

    public $timestamps = false;
    
    // Tabel ini tidak memiliki single primary key
    // Kita biarkan Eloquent beroperasi tanpa primary key jika memungkinkan,
    // Atau set field yang unik (misalnya gabungan no_rawat, tanggal, jam) jika diperlukan.
    protected $primaryKey = null;
    public $incrementing = false;

    protected $fillable = [
        'tanggal',
        'jam',
        'no_rawat',
        'kd_dokter',
        'catatan',
    ];

    public function regPeriksa()
    {
        return $this->belongsTo(RegPeriksa::class, 'no_rawat', 'no_rawat');
    }

    public function dokter()
    {
        return $this->belongsTo(Dokter::class, 'kd_dokter', 'kd_dokter');
    }
}
