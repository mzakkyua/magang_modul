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
        \Carbon\Carbon::setLocale('id');
        \Illuminate\Support\Facades\Date::setLocale('id');
    }
}
