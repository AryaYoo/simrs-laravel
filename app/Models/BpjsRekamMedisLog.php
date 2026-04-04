<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BpjsRekamMedisLog extends Model
{
    protected $table = 'bpjs_rekam_medis_log';
    public $timestamps = false;

    protected $fillable = [
        'no_sep',
        'no_rawat',
        'tgl_kirim',
        'payload_request',
        'response_code',
        'response_message',
        'status_sukses',
        'user_id',
    ];
}
