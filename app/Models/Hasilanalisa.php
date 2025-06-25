<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hasilanalisa extends Model
{
    use HasFactory;

    protected $table = 'hasilanalisa';

    protected $fillable = [
        'pasien_id',
        'tekanan_darah',
        'frekuensi_nadi',
        'suhu',
        'frekuensi_nafas',
        'skor_nyeri',
        'skor_jatuh',
        'berat_badan',
        'tinggi_badan',
        'lingkar_kepala',
        'imt',
        'alat_bantu',
        'prosthesa',
        'cacat_tubuh',
        'adl_mandiri',
        'riwayat_jatuh',
        'status_psikologi',
        'hambatan_edukasi',
        'alergi',
        'catatan',
        'poli_tujuan',
        'penanggung_jawab',
        'tanggal_analisa',
    ];

    public function poli()
    {
        return $this->belongsTo(\App\Models\Poli::class, 'poli_tujuan', 'id');
    }

    public function penanggungJawab()
    {
        return $this->belongsTo(\App\Models\User::class, 'penanggung_jawab', 'id');
    }
}
