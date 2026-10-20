<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataBarang extends Model
{
    protected $table = 'databarang';
    protected $primaryKey = 'kode_brng';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'kode_brng',
        'nama_brng',
        'kode_satbesar',
        'kode_sat',
        'letak_barang',
        'dasar',
        'h_beli',
        'ralan',
        'kelas1',
        'kelas2',
        'kelas3',
        'utama',
        'vip',
        'vvip',
        'beliluar',
        'jualbebas',
        'karyawan',
        'stokminimal',
        'kdjns',
        'isi',
        'kapasitas',
        'expire',
        'status',
        'kode_industri',
        'kode_kategori',
        'kode_golongan',
    ];

    public function satuanBesar()
    {
        return $this->belongsTo(KodeSatuan::class, 'kode_satbesar', 'kode_sat');
    }

    public function satuanKecil()
    {
        return $this->belongsTo(KodeSatuan::class, 'kode_sat', 'kode_sat');
    }

    public function jenis()
    {
        return $this->belongsTo(Jenis::class, 'kdjns', 'kdjns');
    }

    public function kategori()
    {
        return $this->belongsTo(KategoriBarang::class, 'kode_kategori', 'kode');
    }

    public function golongan()
    {
        return $this->belongsTo(GolonganBarang::class, 'kode_golongan', 'kode');
    }

    public function industri()
    {
        return $this->belongsTo(IndustriFarmasi::class, 'kode_industri', 'kode_industri');
    }

    public function gudangBarang()
    {
        return $this->hasMany(GudangBarang::class, 'kode_brng', 'kode_brng');
    }
}
