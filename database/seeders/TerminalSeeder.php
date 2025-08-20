<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TerminalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('terminals')->insert([
            'name' => 'T1A',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('terminals')->insert([
            'name' => 'T1B',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
