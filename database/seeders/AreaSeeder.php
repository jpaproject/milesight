<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AreaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('areas')->insert([
            'name' => 'T1A',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('areas')->insert([
            'name' => 'T1B',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
