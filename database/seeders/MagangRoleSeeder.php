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
        // 1. BUAT DUMMY PEGAWAI (Simulasi Data Pusat)
        // Anggap ini data yang sudah ada di server kantor
        $pakBos = User::create([
            'name' => 'Bapak Kepala Dinas',
            'email' => 'bos@dinas.go.id', // atau username/NIP
            'password' => Hash::make('password'),
        ]);

        $pakZakky = User::create([
            'name' => 'M. Zakky Pegawai',
            'email' => 'zakky@dinas.go.id',
            'password' => Hash::make('password'),
        ]);

        // 2. BERIKAN "SK JABATAN" MAGANG (Ini Data Modul Bapak)
        
        // Tunjuk Pak Bos jadi Super Admin
        MagangAccessRight::create([
            'user_id' => $pakBos->id,
            'role' => 'superadmin',
            'division_name' => null, // Bebas akses
        ]);

        // Tunjuk Pak Zakky jadi Admin Bidang IT
        MagangAccessRight::create([
            'user_id' => $pakZakky->id,
            'role' => 'admin_bidang',
            'division_name' => 'IT', // Terkunci di IT
        ]);
    }
}