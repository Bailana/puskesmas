<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HasilperiksaAnak extends Model
{
    use HasFactory;

    protected $table = 'hasilperiksa_anak';

    protected $fillable = [
        'pasien_id',
        'berat_badan',
        'makanan_anak',
        'gejala',
        'nasehat',
        'pegobatan',
        'penanggung_jawab',
    ];

    public function pasien()
    {
        return $this->belongsTo(Pasien::class, 'pasien_id');
    }

    public function penanggungJawab()
    {
        return $this->belongsTo(User::class, 'penanggung_jawab');
    }
}
