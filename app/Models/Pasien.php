<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pasien extends Model
{
    protected $table = 'pasiens'; // sesuaikan dengan nama tabel di database

    public $timestamps = true;

    protected $casts = [
        'tanggal_lahir' => 'date',
    ];

    // Tambahkan relasi dan atribut lain sesuai kebutuhan
}
