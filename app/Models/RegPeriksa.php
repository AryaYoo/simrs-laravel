<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegPeriksa extends Model
{
    protected $table = 'reg_periksa';
    protected $primaryKey = 'no_rawat';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'no_reg',
        'no_rawat',
        'tgl_registrasi',
        'jam_reg',
        'kd_dokter',
        'no_rkm_medis',
        'kd_poli',
        'kd_pj',
        'p_jawab',
        'almt_pj',
        'hubunganpj',
        'biaya_reg',
        'stts_daftar',
        'stts',
        'stts_poli',
        'status_bayar',
        'status_lanjut',
        'umur_daftar',
        'stts_umur',
    ];

    public function dokter()
    {
        return $this->belongsTo(Dokter::class, 'kd_dokter', 'kd_dokter');
    }

    public function pasien()
    {
        return $this->belongsTo(Pasien::class, 'no_rkm_medis', 'no_rkm_medis');
    }

    public function penjab()
    {
        return $this->belongsTo(Penjab::class, 'kd_pj', 'kd_pj');
    }

    public function kamarInap()
    {
        return $this->hasMany(KamarInap::class, 'no_rawat', 'no_rawat');
    }

    public function permintaanRanap()
    {
        return $this->hasOne(PermintaanRanap::class, 'no_rawat', 'no_rawat');
    }

    public function poliklinik()
    {
        return $this->belongsTo(Poliklinik::class, 'kd_poli', 'kd_poli');
    }

    public function diagnosaPasien()
    {
        return $this->hasMany(DiagnosaPasien::class, 'no_rawat', 'no_rawat');
    }

    public function prosedurPasien()
    {
        return $this->hasMany(ProsedurPasien::class, 'no_rawat', 'no_rawat');
    }

    public function rawatInapDrpr()
    {
        return $this->hasMany(RawatInapDrpr::class, 'no_rawat', 'no_rawat');
    }

    public function detailPeriksaLab()
    {
        return $this->hasMany(DetailPeriksaLab::class, 'no_rawat', 'no_rawat');
    }

    public function periksaRadiologi()
    {
        return $this->hasMany(PeriksaRadiologi::class, 'no_rawat', 'no_rawat');
    }

    public function detailPemberianObat()
    {
        return $this->hasMany(DetailPemberianObat::class, 'no_rawat', 'no_rawat');
    }

    public function pemeriksaanRanap()
    {
        return $this->hasMany(PemeriksaanRanap::class, 'no_rawat', 'no_rawat');
    }

    public function pemeriksaanRalan()
    {
        return $this->hasMany(PemeriksaanRalan::class, 'no_rawat', 'no_rawat');
    }

    public function rawatJlDrpr()
    {
        return $this->hasMany(RawatJlDrpr::class, 'no_rawat', 'no_rawat');
    }

    public function rawatJlDr()
    {
        return $this->hasMany(RawatJlDr::class, 'no_rawat', 'no_rawat');
    }

    public function rawatJlPr()
    {
        return $this->hasMany(RawatJlPr::class, 'no_rawat', 'no_rawat');
    }

    public function rawatInapDr()
    {
        return $this->hasMany(RawatInapDr::class, 'no_rawat', 'no_rawat');
    }

    public function rawatInapPr()
    {
        return $this->hasMany(RawatInapPr::class, 'no_rawat', 'no_rawat');
    }

    public function bridgingSep()
    {
        return $this->hasOne(BridgingSep::class, 'no_rawat', 'no_rawat');
    }

    public function resumePasien()
    {
        return $this->hasOne(ResumePasien::class, 'no_rawat', 'no_rawat');
    }

    public function resumePasienRanap()
    {
        return $this->hasOne(ResumePasienRanap::class, 'no_rawat', 'no_rawat');
    }

    public function hasilPemeriksaanUsg()
    {
        return $this->hasOne(HasilPemeriksaanUsg::class, 'no_rawat', 'no_rawat');
    }

    public function rujukMasuk()
    {
        return $this->hasOne(RujukMasuk::class, 'no_rawat', 'no_rawat');
    }
}
