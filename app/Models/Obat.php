<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Obat extends Model
{
    use HasFactory;

    protected $table = 'obat';

    protected $fillable = [
        'nama_obat',
        'jenis_obat',
        'dosis',
        'bentuk_obat',
        'stok',
        'harga_satuan',
        'tanggal_kadaluarsa',
        'nama_pabrikan',
        'keterangan',
    ];

    protected $dates = [
        'tanggal_kadaluarsa',
    ];
}
