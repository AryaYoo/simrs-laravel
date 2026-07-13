<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuratKeteranganRawatInap extends Model
{
    protected $table = 'surat_keterangan_rawat_inap';
    protected $primaryKey = 'no_surat';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'no_surat',
        'no_rawat',
        'tanggalawal',
        'tanggalakhir',
    ];

    public function regPeriksa()
    {
        return $this->belongsTo(RegPeriksa::class, 'no_rawat', 'no_rawat');
    }
}
