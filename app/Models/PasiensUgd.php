<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon; // Tambahkan ini

class PasiensUgd extends Model
{
    use HasFactory;

    protected $table = 'pasiens_ugd';

    protected $fillable = [
        'pasien_id',
        'nama_pasien',
        'status',
        'tanggal_masuk',
        'ruangan',
    ];

    public function pasien()
    {
        return $this->belongsTo(Pasien::class, 'pasien_id');
    }
}
