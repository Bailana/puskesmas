<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tagihan extends Model
{
    protected $table = 'tagihan';

    protected $fillable = [
        'pasien_id',
        'poli_tujuan',
        'resep_obat',
        'total_biaya',
        'status',
    ];

    public $timestamps = true;

    public function pasien()
    {
        return $this->belongsTo(Pasien::class, 'pasien_id');
    }
}
