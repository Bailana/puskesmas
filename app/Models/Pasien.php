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

    protected $fillable = [
        'no_rekam_medis',
        'nik',
        'nama_pasien',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'gol_darah',
        'agama',
        'pekerjaan',
        'status_pernikahan',
        'alamat_jalan',
        'rt',
        'rw',
        'kelurahan',
        'kecamatan',
        'kabupaten',
        'provinsi',
        'jaminan_kesehatan',
        'nomor_kepesertaan',
        'kepala_keluarga',
        'no_hp',
    ];

    // Tambahkan relasi dan atribut lain sesuai kebutuhan

    public function riwayatBerobat()
    {
        return $this->hasMany(Antrian::class, 'pasien_id')->orderByDesc('tanggal_berobat');
    }
}
