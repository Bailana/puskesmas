<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HasilPeriksa extends Model
{
    use HasFactory;

    protected $table = 'hasilperiksa';

    protected $fillable = [
        'pasien_id',
        'penanggung_jawab',
        'tanggal_periksa',
        'anamnesis',
        'pemeriksaan_fisik',
        'rencana_dan_terapi',
        'diagnosis',
        'edukasi',
        'kode_icd',
        'kesan_status_gizi',
    ];

    public $timestamps = true;

    public function penanggungJawabUser()
    {
        return $this->belongsTo(\App\Models\User::class, 'penanggung_jawab');
    }

    public function obats()
    {
        return $this->belongsToMany(\App\Models\Obat::class, 'hasilperiksa_obat', 'hasilperiksa_id', 'obat_id')
            ->withPivot('jumlah', 'catatan_obat')
            ->withTimestamps();
    }
}
