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
            'terminal_id' => 1,
            'name' => 'CC Atas',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('areas')->insert([
            'terminal_id' => 1,
            'name' => 'CC Bawah',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('areas')->insert([
            'terminal_id' => 1,
            'name' => 'Transit',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('areas')->insert([
            'terminal_id' => 2,
            'name' => 'CC Atas',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('areas')->insert([
            'terminal_id' => 2,
            'name' => 'CC Bawah',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('areas')->insert([
            'terminal_id' => 2,
            'name' => 'Transit',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
