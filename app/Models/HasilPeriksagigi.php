<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HasilPeriksagigi extends Model
{
    use HasFactory;

    protected $table = 'hasilperiksagigi';

    protected $fillable = [
        'pasien_id',
        'penanggung_jawab',
        'tanggal_periksa',
        'odontogram',
        'pemeriksaan_subjektif',
        'pemeriksaan_objektif',
        'diagnosa',
        'terapi_anjuran',
        'catatan',
    ];

    public $timestamps = true;

    public function pasien()
    {
        return $this->belongsTo(\App\Models\Pasien::class, 'pasien_id');
    }

    public function penanggungJawabUser()
    {
        return $this->belongsTo(\App\Models\User::class, 'penanggung_jawab');
    }
}
