<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChecklistPreOperasi extends Model
{
    protected $table = 'checklist_pre_operasi';

    public $timestamps = false;
    protected $primaryKey = 'no_rawat';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'no_rawat',
        'tanggal',
        'sncn',
        'tindakan',
        'kd_dokter_bedah',
        'kd_dokter_anestesi',
        'identitas',
        'surat_ijin_bedah',
        'surat_ijin_anestesi',
        'surat_ijin_transfusi',
        'penandaan_area_operasi',
        'keadaan_umum',
        'pemeriksaan_penunjang_rontgen',
        'keterangan_pemeriksaan_penunjang_rontgen',
        'pemeriksaan_penunjang_ekg',
        'keterangan_pemeriksaan_penunjang_ekg',
        'pemeriksaan_penunjang_usg',
        'keterangan_pemeriksaan_penunjang_usg',
        'pemeriksaan_penunjang_ctscan',
        'keterangan_pemeriksaan_penunjang_ctscan',
        'pemeriksaan_penunjang_mri',
        'keterangan_pemeriksaan_penunjang_mri',
        'persiapan_darah',
        'keterangan_persiapan_darah',
        'perlengkapan_khusus',
        'nip_petugas_ruangan',
        'nip_perawat_ok',
    ];

    protected $casts = [
        'tanggal' => 'datetime',
    ];

    public function regPeriksa()
    {
        return $this->belongsTo(RegPeriksa::class, 'no_rawat', 'no_rawat');
    }

    public function dokterBedah()
    {
        return $this->belongsTo(Dokter::class, 'kd_dokter_bedah', 'kd_dokter');
    }

    public function dokterAnestesi()
    {
        return $this->belongsTo(Dokter::class, 'kd_dokter_anestesi', 'kd_dokter');
    }

    public function petugasRuangan()
    {
        return $this->belongsTo(Petugas::class, 'nip_petugas_ruangan', 'nip');
    }

    public function perawatOk()
    {
        return $this->belongsTo(Petugas::class, 'nip_perawat_ok', 'nip');
    }
}
