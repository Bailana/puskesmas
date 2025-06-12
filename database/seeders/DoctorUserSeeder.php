<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DoctorUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Hapus user dokter dengan email dokter@example.com jika ada
        DB::table('users')->where('email', 'dokter@example.com')->delete();

        // Masukkan user dokter baru
        DB::table('users')->insert([
            'name' => 'Dokter Contoh',
            'email' => 'dokter@example.com',
            'role' => 'dokter',
            'password' => Hash::make('password123'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Hapus user dokter gigi dengan email doktergigi@example.com jika ada
        DB::table('users')->where('email', 'doktergigi@example.com')->delete();

        // Masukkan user dokter gigi baru
        DB::table('users')->insert([
            'name' => 'Dokter Gigi Contoh',
            'email' => 'doktergigi@example.com',
            'role' => 'doktergigi',
            'password' => Hash::make('password123'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
