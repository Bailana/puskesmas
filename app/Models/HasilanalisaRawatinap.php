<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HasilanalisaRawatinap extends Model
{
    use HasFactory;

    protected $table = 'hasilanalisa_rawatinap';

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
        'ruangan',
        'penanggung_jawab',
    ];

    protected $casts = [
        'status_psikologi' => 'array',
        'hambatan_edukasi' => 'array',
    ];

    // Relationship to User model for penanggung_jawab
    public function penanggungJawabUser()
    {
        return $this->belongsTo(\App\Models\User::class, 'penanggung_jawab');
    }
}
