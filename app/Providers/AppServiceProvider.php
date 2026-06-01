<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

// 1. Tambahkan 2 alat surat menyurat ini di atas
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // 2. Cegat Pak Pos dan ganti isi teksnya
        VerifyEmail::toMailUsing(function (object $notifiable, string $url) {

            // Catatan: $notifiable adalah data user yang sedang mendaftar.
            // Kita bisa memanggil $notifiable->display_name yang sudah kamu buat di Model!

            return (new MailMessage)
                ->subject('Verifikasi Email Akun Sinakertrans') // Judul Email
                ->greeting('Halo, ' . $notifiable->display_name . '!') // Sapaan
                ->line('Terima kasih telah mendaftar di portal magang resmi Dinas Tenaga Kerja dan Transmigrasi Provinsi Jawa Timur.')
                ->line('Untuk menjaga keamanan akun dan melengkapi pendaftaran, silakan klik tombol di bawah ini untuk memverifikasi alamat email Anda.')
                ->action('Verifikasi Email Saya', $url) // Tombol biru
                ->line('Jika Anda tidak merasa mendaftar di portal kami, abaikan saja email ini.'); // Penutup
        });
    }
}
