<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Models\UserMagang; // Pastikan nama Model ini sesuai dengan milikmu
use Illuminate\Support\Facades\Log;

// =======================================================
// BAWAAN ASLI LARAVEL (Menampilkan kata motivasi)
// =======================================================
Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


// =======================================================
// PETUGAS KEBERSIHAN OTOMATIS (Hapus Akun Zombie)
// =======================================================
Schedule::call(function () {

    // 1. Cari target: User yang email_verified_at nya kosong (NULL) 
    // DAN tanggal daftarnya sudah lebih dari 7 hari yang lalu
    $zombies = UserMagang::whereNull('email_verified_at')
        ->where('created_at', '<', now()->subDays(7))
        ->get();

    $jumlahZombie = $zombies->count();

    // 2. Jika ada zombie, hapus mereka
    if ($jumlahZombie > 0) {
        foreach ($zombies as $zombie) {
            // Hapus relasi profile jika ada (opsional tapi disarankan agar bersih)
            $zombie->profile()->delete();

            // Hapus akun zombienya
            $zombie->delete();
        }

        // Catat di log Laravel agar admin punya bukti riwayat pembersihan
        Log::info("Petugas Kebersihan: Berhasil menghapus {$jumlahZombie} akun zombie yang tidak diverifikasi.");
    }
})->dailyAt('00:00'); // Jadwalkan tepat jam 12 malam setiap hari