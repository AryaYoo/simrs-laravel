<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Operasi extends Model
{
    protected $table = 'operasi';
    protected $primaryKey = 'no_rawat';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'no_rawat',
        'tgl_operasi',
        'jenis_anasthesi',
        'kategori',
        'operator1',
        'operator2',
        'operator3',
        'asisten_operator1',
        'asisten_operator2',
        'asisten_operator3',
        'instrumen',
        'dokter_anak',
        'perawaat_resusitas',
        'dokter_anestesi',
        'asisten_anestesi',
        'asisten_anestesi2',
        'bidan',
        'bidan2',
        'bidan3',
        'perawat_luar',
        'omloop',
        'omloop2',
        'omloop3',
        'omloop4',
        'omloop5',
        'dokter_pjanak',
        'dokter_umum',
        'kode_paket',
        'biayaoperator1',
        'biayaoperator2',
        'biayaoperator3',
        'biayaasisten_operator1',
        'biayaasisten_operator2',
        'biayaasisten_operator3',
        'biayainstrumen',
        'biayadokter_anak',
        'biayaperawaat_resusitas',
        'biayadokter_anestesi',
        'biayaasisten_anestesi',
        'biayaasisten_anestesi2',
        'biayabidan',
        'biayabidan2',
        'biayabidan3',
        'biayaperawat_luar',
        'biayaalat',
        'biayasewaok',
        'akomodasi',
        'bagian_rs',
        'biaya_omloop',
        'biaya_omloop2',
        'biaya_omloop3',
        'biaya_omloop4',
        'biaya_omloop5',
        'biayasarpras',
        'biaya_dokter_pjanak',
        'biaya_dokter_umum',
        'status',
    ];

    public function regPeriksa()
    {
        return $this->belongsTo(RegPeriksa::class, 'no_rawat', 'no_rawat');
    }

    public function paketOperasi()
    {
        return $this->belongsTo(PaketOperasi::class, 'kode_paket', 'kode_paket');
    }

    public function dokterOperator1()
    {
        return $this->belongsTo(Dokter::class, 'operator1', 'kd_dokter');
    }

    public function dokterOperator2()
    {
        return $this->belongsTo(Dokter::class, 'operator2', 'kd_dokter');
    }

    public function dokterOperator3()
    {
        return $this->belongsTo(Dokter::class, 'operator3', 'kd_dokter');
    }

    public function dokterAnak()
    {
        return $this->belongsTo(Dokter::class, 'dokter_anak', 'kd_dokter');
    }

    public function dokterAnestesi()
    {
        return $this->belongsTo(Dokter::class, 'dokter_anestesi', 'kd_dokter');
    }

    public function dokterPjanak()
    {
        return $this->belongsTo(Dokter::class, 'dokter_pjanak', 'kd_dokter');
    }

    public function dokterUmum()
    {
        return $this->belongsTo(Dokter::class, 'dokter_umum', 'kd_dokter');
    }
}