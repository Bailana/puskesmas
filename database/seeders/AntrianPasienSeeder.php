<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pasien;
use App\Models\Antrian;
use App\Models\Poli;

class AntrianPasienSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Delete antrian data related to pasien with no_rekam_medis RM001 to avoid foreign key constraint issues
        $pasien = Pasien::where('no_rekam_medis', 'RM001')->first();
        if ($pasien) {
            Antrian::where('pasien_id', $pasien->id)->delete();
        }

        // Create sample Poli if none exists
        $poli = Poli::first();
        if (!$poli) {
            $poli = Poli::create([
                'nama_poli' => 'Poli Umum',
                'deskripsi' => 'Poli umum untuk pemeriksaan umum',
            ]);
        }

        // Create sample Pasien if not exists
        $pasien = Pasien::firstOrCreate(
            ['no_rekam_medis' => 'RM001'],
            [
                'nik' => '1234567890123456',
                'nama_pasien' => 'Budi Santoso',
                'kepala_keluarga' => 'Santoso',
                'no_hp' => '081234567890',
                'tempat_lahir' => 'Jakarta',
                'tanggal_lahir' => '1990-01-01',
                'jenis_kelamin' => 'Laki-laki',
                'gol_darah' => 'O',
                'agama' => 'Islam',
                'pekerjaan' => 'Karyawan',
                'status_pernikahan' => 'Belum Menikah',
                'alamat_jalan' => 'Jl. Merdeka No. 1',
                'rt' => '001',
                'rw' => '002',
                'kelurahan' => 'Kelurahan A',
                'kecamatan' => 'Kecamatan B',
                'kabupaten' => 'Jakarta Selatan',
                'provinsi' => 'DKI Jakarta',
                'jaminan_kesehatan' => 'BPJS',
                'nomor_kepesertaan' => '9876543210',
            ]
        );

        // Create sample Antrian
        Antrian::create([
            'no_rekam_medis' => $pasien->no_rekam_medis,
            'pasien_id' => $pasien->id,
            'poli_id' => $poli->id,
            'tanggal_berobat' => now()->toDateString(),
            'status' => 'Menunggu',
        ]);
    }
}
