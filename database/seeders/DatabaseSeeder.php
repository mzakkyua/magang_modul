<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Satpam pengecekan: Hanya jalankan ini jika BUKAN di server Production (asli)
        if (!app()->isProduction()) {

            // Bikin user test hanya di lokal
            User::factory()->create([
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);

            // Panggil seeder yang rawan (dummy user & reset password)
            $this->call([
                UserMagangSeeder::class,
                MagangRoleSeeder::class,
            ]);

            $this->command->info('Development seeders berhasil dijalankan.');
        }

        // 2. Seeder yang aman dan WAJIB jalan di production taruh di luar blok if
        $this->call([
            DivisionSettingMagangSeeder::class,
        ]);
    }
}
