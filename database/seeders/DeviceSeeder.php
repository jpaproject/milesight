<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DeviceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('devices')->insert([
            'area_id' => 1,
            'name' => 'BoardingLounge-B1-T1A-EM300-TH',
            'topic' => 'topic/BoardingLounge/B1/T1A/EM300/TH',
            'is_active' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('devices')->insert([
            'area_id' => 1,
            'name' => 'BoardingLounge-B2-T1A-EM300-TH',
            'topic' => 'topic/BoardingLounge/B2/T1A/EM300/TH',
            'is_active' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('devices')->insert([
            'area_id' => 2,
            'name' => 'BoardingLounge-B1-T1B-AM102',
            'topic' => 'topic/BoardingLounge/B1/T1B/AM102',
            'is_active' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('devices')->insert([
            'area_id' => 2,
            'name' => 'BoardingLounge-B2-T1B-AM102',
            'topic' => 'topic/BoardingLounge/B2/T1B/AM102',
            'is_active' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
