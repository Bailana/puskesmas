<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Antrian extends Model
{
    use HasFactory;

    protected $table = 'antrian'; // Correct table name as per migration

    protected $fillable = [
        'no_rekam_medis',
        'tanggal_berobat',
        'status',
        'poli_id',
        'pasien_id',
    ];

    // Define relationship to Pasien model
    public function pasien()
    {
        return $this->belongsTo(Pasien::class, 'pasien_id');
    }

    // Define relationship to Poli model
    public function poli()
    {
        return $this->belongsTo(Poli::class, 'poli_id');
    }
}
