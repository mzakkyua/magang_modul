<?php

namespace App\Http\Controllers\Requests\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class ForgotPasswordAdminController extends Controller
{
  // Menampilkan halaman form ketik email admin
  public function showLinkRequestForm()
  {
    return view('auth.passwords.email-admin');
  }

  // Memproses pengiriman link reset password ke email admin
  public function sendResetLinkEmail(Request $request)
  {
    $request->validate([
      'email' => 'required|email',
    ]);

    // Menggunakan broker 'users' sesuai config/auth.php
    $status = Password::broker('users')->sendResetLink(
      $request->only('email')
    );

    return $status === Password::RESET_LINK_SENT
      ? back()->with('status', __($status))
      : back()->withErrors(['email' => __($status)]);
  }
}
