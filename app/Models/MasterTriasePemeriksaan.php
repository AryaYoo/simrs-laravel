<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterTriasePemeriksaan extends Model
{
    protected $table = 'master_triase_pemeriksaan';
    protected $primaryKey = 'kode_pemeriksaan';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = ['kode_pemeriksaan', 'nama_pemeriksaan'];

    public function skala1() { return $this->hasMany(MasterTriaseSkala1::class, 'kode_pemeriksaan', 'kode_pemeriksaan'); }
    public function skala2() { return $this->hasMany(MasterTriaseSkala2::class, 'kode_pemeriksaan', 'kode_pemeriksaan'); }
    public function skala3() { return $this->hasMany(MasterTriaseSkala3::class, 'kode_pemeriksaan', 'kode_pemeriksaan'); }
    public function skala4() { return $this->hasMany(MasterTriaseSkala4::class, 'kode_pemeriksaan', 'kode_pemeriksaan'); }
    public function skala5() { return $this->hasMany(MasterTriaseSkala5::class, 'kode_pemeriksaan', 'kode_pemeriksaan'); }
}
