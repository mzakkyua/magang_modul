<?php

namespace App\Http\Controllers\Requests\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class ForgotPasswordMagangController extends Controller
{
    // ==============================
    // TAMPILKAN FORM REQUEST RESET
    // ==============================
    public function showLinkRequestForm()
    {
        return view('auth.passwords.email-magang');
    }

    // ==============================
    // KIRIM LINK RESET KE EMAIL
    // ==============================
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $status = Password::broker('magang')
            ->sendResetLink(
                $request->only('email')
            );

        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);
    }
}
