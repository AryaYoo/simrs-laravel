<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingOperasi extends Model
{
    use HasFactory;

    protected $table = 'booking_operasi';
    public $timestamps = false;
    public $incrementing = false;

    // No primaryKey specified because it's a composite key, we handle queries manually via Repository

    protected $fillable = [
        'no_rawat',
        'kode_paket',
        'tanggal',
        'jam_mulai',
        'jam_selesai',
        'status',
        'kd_dokter',
        'kd_ruang_ok',
    ];

    public function regPeriksa()
    {
        return $this->belongsTo(RegPeriksa::class, 'no_rawat', 'no_rawat');
    }

    public function paketOperasi()
    {
        return $this->belongsTo(PaketOperasi::class, 'kode_paket', 'kode_paket');
    }

    public function dokter()
    {
        return $this->belongsTo(Dokter::class, 'kd_dokter', 'kd_dokter');
    }

    public function ruangOk()
    {
        return $this->belongsTo(RuangOk::class, 'kd_ruang_ok', 'kd_ruang_ok');
    }
}
