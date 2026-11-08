<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PelaksanaanInformasiEdukasi extends Model
{
    protected $table = 'pelaksanaan_informasi_edukasi';
    public $timestamps = false;
    public $incrementing = false;

    protected $primaryKey = 'no_rawat';
    protected $keyType = 'string';

    protected $fillable = [
        'no_rawat',
        'tanggal',
        'nik',
        'materi_edukasi',
        'keterangan',
        'diberikan_pada',
        'keterangan_diberikan_pada',
        'lama_edukasi',
        'metode_edukasi',
        'hasil_verifikasi',
        'status',
    ];

    protected $casts = [
        'tanggal' => 'datetime',
    ];

    public function regPeriksa(): BelongsTo
    {
        return $this->belongsTo(RegPeriksa::class, 'no_rawat', 'no_rawat');
    }

    public function pegawai(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class, 'nik', 'nik');
    }

    public function petugas(): BelongsTo
    {
        return $this->belongsTo(Petugas::class, 'nik', 'nip');
    }
}

