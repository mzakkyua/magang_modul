<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\MagangAccessRight;
use Illuminate\Support\Facades\Hash;

class CreateSuperAdmin extends Command
{
    /**
     * Kata kunci untuk memanggil command ini di terminal
     */
    protected $signature = 'admin:create';

    /**
     * Deskripsi yang muncul saat kita ketik 'php artisan list'
     */
    protected $description = 'Membuat akun Superadmin baru secara interaktif (Aman untuk Production)';

    /**
     * Eksekusi logika command
     */
    public function handle()
    {
        $this->info('=== Setup Akun Superadmin ===');

        // 1. Minta input dari user (bisa ditekan Enter langsung untuk pakai nilai default)
        $name = $this->ask('Masukkan Nama Superadmin', 'Bapak Kepala Dinas');
        $email = $this->ask('Masukkan Email', 'bos@dinas.go.id');

        // 2. Cek apakah email sudah ada supaya tidak error di database
        if (User::where('email', $email)->exists()) {
            $this->error('Gagal: Email tersebut sudah terdaftar di sistem!');
            return Command::FAILURE; // Berhenti dengan status gagal
        }

        // 3. Minta password pakai metode secret() supaya teksnya disensor di layar (seperti ngetik password di Linux)
        $password = $this->secret('Masukkan Password rahasia');
        $passwordConfirm = $this->secret('Konfirmasi Password');

        if ($password !== $passwordConfirm) {
            $this->error('Gagal: Password dan Konfirmasi Password tidak cocok!');
            return Command::FAILURE;
        }

        // 4. Masukkan ke tabel users
        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
        ]);

        // 5. Masukkan hak aksesnya ke tabel magang_access_rights
        MagangAccessRight::create([
            'user_id' => $user->id,
            'role' => 'superadmin',
            'division_name' => null, // Superadmin memegang semua kendali, jadi tidak dikunci di 1 divisi
        ]);

        // 6. Selesai!
        $this->info("Berhasil! Akun Superadmin dengan email [{$email}] siap digunakan.");

        return Command::SUCCESS;
    }
}
