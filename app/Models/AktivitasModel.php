<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AktivitasModel extends Model
{
    use HasFactory;
    protected $table = 'tb_aktivitas';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id_jadwal',
        'id_user',
        'aktivitas',
        'keterangan',
        'file',
        'status'
    ];

    public function jadwal()
    {
        return $this->belongsTo('App\Models\JadwalModel','id_jadwal');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User','id_user');
    }
}
