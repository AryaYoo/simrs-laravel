<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
 
class HasilPemeriksaanUsg extends Model
{
    protected $table = 'hasil_pemeriksaan_usg';
    protected $primaryKey = 'no_rawat';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
 
    protected $fillable = [
        'no_rawat',
        'tanggal',
        'kd_dokter',
        'diagnosa_klinis',
        'kiriman_dari',
        'hta',
        'kantong_gestasi',
        'ukuran_bokongkepala',
        'jenis_prestasi',
        'diameter_biparietal',
        'panjang_femur',
        'lingkar_abdomen',
        'tafsiran_berat_janin',
        'usia_kehamilan',
        'plasenta_berimplatansi',
        'derajat_maturitas',
        'jumlah_air_ketuban',
        'indek_cairan_ketuban',
        'kelainan_kongenital',
        'peluang_sex',
        'kesimpulan',
    ];
 
    public function regPeriksa()
    {
        return $this->belongsTo(RegPeriksa::class, 'no_rawat', 'no_rawat');
    }
 
    public function dokter()
    {
        return $this->belongsTo(Dokter::class, 'kd_dokter', 'kd_dokter');
    }
 
    public function gambar()
    {
        return $this->hasOne(HasilPemeriksaanUsgGambar::class, 'no_rawat', 'no_rawat');
    }
}
