<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pasien extends Model
{
    protected $table = 'pasien';
    protected $primaryKey = 'no_rkm_medis';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $guarded = [];

    public function kelurahan() { return $this->belongsTo(Kelurahan::class, 'kd_kel', 'kd_kel'); }
    public function kecamatan() { return $this->belongsTo(Kecamatan::class, 'kd_kec', 'kd_kec'); }
    public function kabupaten() { return $this->belongsTo(Kabupaten::class, 'kd_kab', 'kd_kab'); }
    
    public function kelurahanPj() { return $this->belongsTo(Kelurahan::class, 'kelurahanpj', 'kd_kel'); }
    public function kecamatanPj() { return $this->belongsTo(Kecamatan::class, 'kecamatanpj', 'kd_kec'); }
    public function kabupatenPj() { return $this->belongsTo(Kabupaten::class, 'kabupatenpj', 'kd_kab'); }

    public function penjab() { return $this->belongsTo(Penjab::class, 'kd_pj', 'kd_pj'); }
    public function sukuBangsa() { return $this->belongsTo(SukuBangsa::class, 'suku_bangsa', 'id'); }
    public function bahasa() { return $this->belongsTo(BahasaPasien::class, 'bahasa_pasien', 'id'); }
    public function perusahaan() { return $this->belongsTo(PerusahaanPasien::class, 'perusahaan_pasien', 'kode_perusahaan'); }
    public function cacatFisik() { return $this->belongsTo(CacatFisik::class, 'cacat_fisik', 'id'); }
}
