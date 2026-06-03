<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailPermintaanResepPulang extends Model
{
    protected $table = 'detail_permintaan_resep_pulang';
    // Table doesn't have a specific primary key column, we will use composite keys or no primary key
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'no_permintaan',
        'kode_brng',
        'jml',
        'dosis'
    ];

    public function permintaan()
    {
        return $this->belongsTo(PermintaanResepPulang::class, 'no_permintaan', 'no_permintaan');
    }

    public function barang()
    {
        return $this->belongsTo(DataBarang::class, 'kode_brng', 'kode_brng');
    }
}
