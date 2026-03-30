<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
<<<<<<< HEAD
=======
use Illuminate\Support\Facades\Storage; // Pastikan ini di-import
>>>>>>> main

class UserMagangSeeder extends Seeder
{
    public function run(): void
    {
<<<<<<< HEAD

=======
        /**
         * =========================================================
         * MEMBUAT FILE DUMMY FISIK
         * =========================================================
         * Agar saat Admin menekan tombol "Lihat CV" atau "Proposal",
         * file-nya benar-benar ada dan tidak Error 404.
         */
        if (!Storage::disk('public')->exists('cv_uploads/dummy.pdf')) {
            Storage::disk('public')->put('cv_uploads/dummy.pdf', 'Ini adalah file CV dummy untuk keperluan testing.');
        }

        if (!Storage::disk('public')->exists('proposal_uploads/dummy.pdf')) {
            Storage::disk('public')->put('proposal_uploads/dummy.pdf', 'Ini adalah file Proposal dummy untuk keperluan testing.');
        }

        /**
         * =========================================================
         * SEEDING DATA USER & PROFILE
         * =========================================================
         */
>>>>>>> main
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
<<<<<<< HEAD
                'education_level' => 'mahasiswa',
=======
                'education_level' => 'S1', // Disesuaikan dengan pilihan di form
>>>>>>> main
                'nim_nisn' => 'NIM' . $i,
                'institution_name' => 'Universitas Test',
                'major' => 'Informatika',
                'phone_number' => '08123456789',
                'address' => 'Alamat Testing',
<<<<<<< HEAD
=======

                // MENGISI DATA FILE DUMMY
                'cv_file_path' => 'cv_uploads/dummy.pdf',
                'proposal_file_path' => 'proposal_uploads/dummy.pdf',

>>>>>>> main
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
<<<<<<< HEAD
// Cara penggunaan 
// email: user1@test.com
// password: password123
=======
>>>>>>> main
