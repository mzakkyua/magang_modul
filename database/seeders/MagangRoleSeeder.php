<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\MagangAccessRight;
use Illuminate\Support\Facades\Hash;

class MagangRoleSeeder extends Seeder
{
    public function run(): void
    {
        // 1. BUAT AKUN SUPER ADMIN (Pak Bos)
        $super = User::create([
            'name' => 'Bapak Kepala Dinas',
            'email' => 'bos@dinas.go.id', 
            'password' => Hash::make('password'),
        ]);

        MagangAccessRight::create([
            'user_id' => $super->id,
            'role' => 'superadmin',
            'division_name' => null, // Bebas Akses
        ]);

        // 2. BUAT AKUN ADMIN BIDANG IT (Pak Zakky)
        $adminIT = User::create([
            'name' => 'M. Zakky (Admin IT)',
            'email' => 'zakky@dinas.go.id',
            'password' => Hash::make('password'),
        ]);

        MagangAccessRight::create([
            'user_id' => $adminIT->id,
            'role' => 'admin_bidang',
            'division_name' => 'IT', // Terkunci di IT
        ]);

        // 3. BUAT AKUN ADMIN BIDANG KEUANGAN (Buat Tes Filter)
        $adminKeu = User::create([
            'name' => 'Ibu Bendahara (Admin Keuangan)',
            'email' => 'keuangan@dinas.go.id',
            'password' => Hash::make('password'),
        ]);

        MagangAccessRight::create([
            'user_id' => $adminKeu->id,
            'role' => 'admin_bidang',
            'division_name' => 'Keuangan', // Terkunci di Keuangan
        ]);
    }
}