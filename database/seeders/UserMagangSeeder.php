<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserMagangSeeder extends Seeder
{
    public function run(): void
    {

        for ($i = 1; $i <= 20; $i++) {

            $userId = DB::table('users_magang')->insertGetId([
                'username' => 'user' . $i,
                'email' => 'user' . $i . '@test.com',
                'password_hash' => Hash::make('password123'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('profiles_magang')->insert([
                'user_id' => $userId,
                'full_name' => 'Test User ' . $i,
                'education_level' => 'mahasiswa',
                'nim_nisn' => 'NIM' . $i,
                'institution_name' => 'Universitas Test',
                'major' => 'Informatika',
                'phone_number' => '08123456789',
                'address' => 'Alamat Testing',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
// Cara penggunaan 
// email: user1@test.com
// password: password123