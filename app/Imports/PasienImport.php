<?php

namespace App\Imports;

use App\Models\Pasien;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PasienImport implements ToModel, WithHeadingRow
{
    use Importable;

    public function model(array $row)
    {
        return new Pasien([
            'no_rekam_medis' => $row['no_rekam_medis'],
            'nik' => $row['nik'],
            'nama_pasien' => $row['nama_pasien'],
            'tempat_lahir' => $row['tempat_lahir'],
            'tanggal_lahir' => $row['tanggal_lahir'],
            'jenis_kelamin' => $row['jenis_kelamin'],
            'gol_darah' => $row['gol_darah'] ?? null,
            'agama' => $row['agama'] ?? null,
            'pekerjaan' => $row['pekerjaan'] ?? null,
            'status_pernikahan' => $row['status_pernikahan'],
            'alamat_jalan' => $row['alamat_jalan'],
            'rt' => $row['rt'],
            'rw' => $row['rw'],
            'kelurahan' => $row['kelurahan'],
            'kecamatan' => $row['kecamatan'],
            'kabupaten' => $row['kabupaten'],
            'provinsi' => $row['provinsi'],
            'jaminan_kesehatan' => $row['jaminan_kesehatan'],
            'nomor_kepesertaan' => $row['nomor_kepesertaan'] ?? null,
        ]);
    }
}
