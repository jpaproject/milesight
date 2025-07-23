<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'username' => 'admin',
            'role' => 'admin',
            'email_verified_at' => now(),
            'password' => Hash::make('Together1!'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('users')->insert([
            'name' => 'User Satu',
            'email' => 'user@gmail.com',
            'username' => 'user',
            'role' => 'user',
            'email_verified_at' => now(),
            'password' => Hash::make('Together1!'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
