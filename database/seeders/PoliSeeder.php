<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PoliSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $polis = [
            ['nama_poli' => 'umum'],
            ['nama_poli' => 'gigi'],
            ['nama_poli' => 'kia'],
            ['nama_poli' => 'lansia'],
        ];

        DB::table('poli')->insert($polis);
    }
}
