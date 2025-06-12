<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PasienSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('pasiens')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        DB::table('pasiens')->insert([
            [
                'no_rekam_medis' => 'RM001',
                'nik' => '3201010101010001',
                'nama_pasien' => 'Budi Santoso',
                'kepala_keluarga' => 'Santoso',
                'no_hp' => '081234567890',
                'tempat_lahir' => 'Jakarta',
                'tanggal_lahir' => '1980-01-01',
                'jenis_kelamin' => 'Laki-laki',
                'gol_darah' => 'O',
                'agama' => 'Islam',
                'pekerjaan' => 'Karyawan Swasta',
                'status_pernikahan' => 'Menikah',
                'alamat_jalan' => 'Jl. Merdeka No.1',
                'rt' => '01',
                'rw' => '02',
                'kelurahan' => 'Kelurahan A',
                'kecamatan' => 'Kecamatan B',
                'kabupaten' => 'Jakarta Selatan',
                'provinsi' => 'DKI Jakarta',
                'jaminan_kesehatan' => 'BPJS',
                'nomor_kepesertaan' => '1234567890',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'no_rekam_medis' => 'RM002',
                'nik' => '3201010101010002',
                'nama_pasien' => 'Siti Aminah',
                'kepala_keluarga' => 'Aminah',
                'no_hp' => '082345678901',
                'tempat_lahir' => 'Bandung',
                'tanggal_lahir' => '1990-05-15',
                'jenis_kelamin' => 'Perempuan',
                'gol_darah' => 'A',
                'agama' => 'Kristen',
                'pekerjaan' => 'Guru',
                'status_pernikahan' => 'Belum Menikah',
                'alamat_jalan' => 'Jl. Sudirman No.10',
                'rt' => '03',
                'rw' => '04',
                'kelurahan' => 'Kelurahan C',
                'kecamatan' => 'Kecamatan D',
                'kabupaten' => 'Bandung',
                'provinsi' => 'Jawa Barat',
                'jaminan_kesehatan' => 'Mandiri',
                'nomor_kepesertaan' => '0987654321',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'no_rekam_medis' => 'RM003',
                'nik' => '3201010101010003',
                'nama_pasien' => 'Agus Wijaya',
                'kepala_keluarga' => 'Wijaya',
                'no_hp' => '083456789012',
                'tempat_lahir' => 'Surabaya',
                'tanggal_lahir' => '1975-12-20',
                'jenis_kelamin' => 'Laki-laki',
                'gol_darah' => 'B',
                'agama' => 'Hindu',
                'pekerjaan' => 'Dokter',
                'status_pernikahan' => 'Menikah',
                'alamat_jalan' => 'Jl. Diponegoro No.5',
                'rt' => '05',
                'rw' => '06',
                'kelurahan' => 'Kelurahan E',
                'kecamatan' => 'Kecamatan F',
                'kabupaten' => 'Surabaya',
                'provinsi' => 'Jawa Timur',
                'jaminan_kesehatan' => 'BPJS',
                'nomor_kepesertaan' => '1122334455',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
