<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\MagangAccessRight;
use App\Models\Division;
use Illuminate\Support\Facades\Hash;

class CreateAdminBidang extends Command
{
    /**
     * Kata kunci untuk memanggil command ini di terminal
     */
    protected $signature = 'admin:bidang';

    /**
     * Deskripsi command
     */
    protected $description = 'Membuat akun Admin Bidang (khusus 1 divisi) secara interaktif';

    public function handle()
    {
        $this->info('=== Setup Akun Admin Bidang ===');

        // 1. Ambil daftar divisi yang aktif dari database
        $divisions = Division::active()->pluck('name')->toArray();

        if (empty($divisions)) {
            $this->error('Gagal: Belum ada data Divisi yang aktif di database!');
            $this->line('Silakan tambahkan divisi terlebih dahulu (lewat seeder atau Superadmin).');
            return Command::FAILURE;
        }

        // 2. Minta input dari user
        $name = $this->ask('Masukkan Nama Admin Bidang', 'Admin TIK');
        $email = $this->ask('Masukkan Email', 'admin.tik@dinas.go.id');

        // 3. Cek apakah email sudah ada
        if (User::where('email', $email)->exists()) {
            $this->error('Gagal: Email tersebut sudah terdaftar di sistem!');
            return Command::FAILURE;
        }

        // 4. Minta password
        $password = $this->secret('Masukkan Password rahasia');
        $passwordConfirm = $this->secret('Konfirmasi Password');

        if ($password !== $passwordConfirm) {
            $this->error('Gagal: Password dan Konfirmasi Password tidak cocok!');
            return Command::FAILURE;
        }

        // 5. FITUR SPESIAL: Pilih Divisi dengan menu interaktif!
        $selectedDivision = $this->choice(
            'Admin ini akan memegang divisi mana?',
            $divisions
        );

        // 6. Masukkan ke tabel users
        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
        ]);

        // 7. Masukkan hak aksesnya (role = admin_bidang, division_name = pilihan)
        MagangAccessRight::create([
            'user_id' => $user->id,
            'role' => 'admin_bidang',
            'division_name' => $selectedDivision,
        ]);

        // 8. Selesai!
        $this->info("Berhasil! Akun Admin Bidang untuk divisi [{$selectedDivision}] siap digunakan.");

        return Command::SUCCESS;
    }
}
