<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PerawatUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Hapus user perawat dengan email perawat@example.com jika ada
        DB::table('users')->where('email', 'perawat@example.com')->delete();

        // Masukkan user perawat baru
        DB::table('users')->insert([
            'name' => 'Perawat Contoh',
            'email' => 'perawat@example.com',
            'role' => 'perawat',
            'password' => Hash::make('password123'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Masukkan user perawat baru Bai
        DB::table('users')->insert([
            'name' => 'Bai',
            'email' => 'baihaqi@gmail.com',
            'role' => 'perawat',
            'password' => Hash::make('password123'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
