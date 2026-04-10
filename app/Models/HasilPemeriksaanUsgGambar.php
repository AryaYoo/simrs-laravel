<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
 
class HasilPemeriksaanUsgGambar extends Model
{
    protected $table = 'hasil_pemeriksaan_usg_gambar';
    protected $primaryKey = 'no_rawat';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
 
    protected $fillable = [
        'no_rawat',
        'photo',
    ];
 
    public function hasilUsg()
    {
        return $this->belongsTo(HasilPemeriksaanUsg::class, 'no_rawat', 'no_rawat');
    }
}
