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
            'is_active' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('devices')->insert([
            'area_id' => 1,
            'name' => 'BoardingLounge-B2-T1A-EM300-TH',
            'is_active' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('devices')->insert([
            'area_id' => 1,
            'name' => 'Lingkingatas-B1-T1A-EM300-TH',
            'is_active' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('devices')->insert([
            'area_id' => 1,
            'name' => 'Lingkingatas-B2-T1A-EM300-TH',
            'is_active' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('devices')->insert([
            'area_id' => 1,
            'name' => 'ConnectingB1-B2-T1A-EM300-TH',
            'is_active' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('devices')->insert([
            'area_id' => 1,
            'name' => 'ConnectingB2-B3-T1A-EM300-TH',
            'is_active' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('devices')->insert([
            'area_id' => 1,
            'name' => 'CCatas-BoardingLounge-T1A-EM300-TH',
            'is_active' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('devices')->insert([
            'area_id' => 1,
            'name' => 'CCatas-setelahPSCP-T1A-EM300-TH',
            'is_active' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('devices')->insert([
            'area_id' => 2,
            'name' => 'Checkin-1-T1A-EM300-TH',
            'is_active' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('devices')->insert([
            'area_id' => 2,
            'name' => 'Checkin-2-T1A-EM300-TH',
            'is_active' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('devices')->insert([
            'area_id' => 2,
            'name' => 'Hallcheckin-T1A-EM300-TH',
            'is_active' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('devices')->insert([
            'area_id' => 2,
            'name' => 'SCP1-T1A-EM300-TH',
            'is_active' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('devices')->insert([
            'area_id' => 2,
            'name' => 'PerkantoranSayapKiriKeberangkatan-T1A-EM300-TH',
            'is_active' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('devices')->insert([
            'area_id' => 3,
            'name' => 'Lingkingbawah-B1-T1A-EM300-TH',
            'is_active' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('devices')->insert([
            'area_id' => 3,
            'name' => 'Lingkingbawah-B2-T1A-EM300-TH',
            'is_active' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('devices')->insert([
            'area_id' => 3,
            'name' => 'Lingkingbawah-B3-T1A-EM300-TH',
            'is_active' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('devices')->insert([
            'area_id' => 3,
            'name' => 'Lingkingbawah-B4-T1A-EM300-TH',
            'is_active' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('devices')->insert([
            'area_id' => 4,
            'name' => 'BoardingLounge-B1-T1B-AM102',
            'is_active' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('devices')->insert([
            'area_id' => 4,
            'name' => 'BoardingLounge-B2-T1B-AM102',
            'is_active' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
