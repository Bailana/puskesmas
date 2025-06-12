<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Hapus user test@example.com jika ada
        User::where('email', 'test@example.com')->delete();

        // Buat user test baru
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $this->call([
            DoctorUserSeeder::class,
            \Database\Seeders\PerawatUserSeeder::class,
            \Database\Seeders\ResepsionisUserSeeder::class,
            \Database\Seeders\ApotekerUserSeeder::class,
            \Database\Seeders\PasienSeeder::class,
            ]);
    }
}
