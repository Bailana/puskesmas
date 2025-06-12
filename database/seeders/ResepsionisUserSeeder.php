<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;

class ResepsionisUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Resepsionis User',
            'email' => 'resepsionis@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password123'), // default password
            'role' => 'resepsionis',
            'remember_token' => Str::random(10),
        ]);
    }
}
