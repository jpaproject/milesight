<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
//         DB::table('users')->insert([
//             'name' => 'Admin',
//             'email' => 'admin@gmail.com',
//             'username' => 'admin',
//             'role' => 'admin',
//             'email_verified_at' => now(),
//             'password' => Hash::make('Together1!'),
//             'created_at' => now(),
//             'updated_at' => now(),
//         ]);

//         DB::table('users')->insert([
//             'name' => 'User Satu',
//             'email' => 'user@gmail.com',
//             'username' => 'user',
//             'role' => 'user',
//             'email_verified_at' => now(),
//             'password' => Hash::make('Together1!'),
//             'created_at' => now(),
//             'updated_at' => now(),
//         ]);
    
    	// Tema per bulan (ikon Indonesia)
        $temaBulan = [
            1 => 'Monas',
            2 => 'Borobudur',
            3 => 'Komodo',
            4 => 'RajaAmpat',
            5 => 'Prambanan',
            6 => 'Bali',
            7 => 'Bunaken',
            8 => 'MerahPutih',
            9 => 'Rinjani',
            10 => 'Toba',
            11 => 'Bromo',
            12 => 'Papua',
        ];

        $bulanSekarang = Carbon::now()->month;
        $tahunSekarang = Carbon::now()->year;

        // Password contoh: "MerahPutih@2025"
        $passwordPlain = $temaBulan[$bulanSekarang] . "@{$tahunSekarang}";

        DB::table('users')->insert([
            'name' => 'grootech',
            'email' => 'grootech@demo.com',
        	'username' => 'grootech',
            'password' => Hash::make($passwordPlain),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Tampilkan di terminal supaya tahu passwordnya
        $this->command->info("User 'groootech' dibuat.");
        $this->command->warn("Password demo: {$passwordPlain}");
    }
}
