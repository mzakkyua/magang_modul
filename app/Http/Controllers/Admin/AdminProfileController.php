<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Validation\Rule;

/**
 * =========================================================
 * CONTROLLER: AdminProfileController
 * =========================================================
 * TANGGUNG JAWAB:
 * - Menampilkan halaman profil admin
 * - Menampilkan histori sesi login
 * - Update data profil admin
 *
 * CATATAN:
 * - Parsing user-agent manual (tanpa library)
 * - Session diambil langsung dari DB
 * =========================================================
 */
class AdminProfileController extends Controller
{
  /**
   * =====================================================
   * METHOD: index()
   * =====================================================
   * Menampilkan halaman profil admin + histori sesi login
   * =====================================================
   */
  public function index()
  {
    /** @var \App\Models\User $user */
    $user = Auth::user();

    $sessions = DB::table('sessions')
      ->where('user_id', $user->id)
      ->orderBy('last_activity', 'desc')
      ->get()
      ->map(function ($session) {
        return (object) [
          'ip_address'       => $session->ip_address,
          'is_current_device' => $session->id === request()->session()->getId(),
          'last_active'      => Carbon::createFromTimestamp($session->last_activity)->diffForHumans(),
        ];
      });

    return view('admin.profile', compact('user', 'sessions'));
  }

  /**
   * =====================================================
   * HELPER: createAgent()
   * =====================================================
   * Mengubah user-agent string menjadi info platform & browser
   * =====================================================
   */
  private function createAgent($userAgent)
  {
    $agent = [
      'platform' => 'Tidak Diketahui',
      'browser'  => 'Tidak Diketahui',
    ];

    // Platform
    if (preg_match('/windows|win32/i', $userAgent)) {
      $agent['platform'] = 'Windows';
    } elseif (preg_match('/macintosh|mac os x/i', $userAgent)) {
      $agent['platform'] = 'macOS';
    } elseif (preg_match('/linux/i', $userAgent)) {
      $agent['platform'] = 'Linux';
    } elseif (preg_match('/android/i', $userAgent)) {
      $agent['platform'] = 'Android';
    } elseif (preg_match('/iphone/i', $userAgent)) {
      $agent['platform'] = 'iPhone';
    }

    // Browser
    if (preg_match('/MSIE/i', $userAgent) && !preg_match('/Opera/i', $userAgent)) {
      $agent['browser'] = 'Internet Explorer';
    } elseif (preg_match('/Firefox/i', $userAgent)) {
      $agent['browser'] = 'Firefox';
    } elseif (preg_match('/Chrome/i', $userAgent)) {
      $agent['browser'] = 'Chrome';
    } elseif (preg_match('/Safari/i', $userAgent)) {
      $agent['browser'] = 'Safari';
    } elseif (preg_match('/Opera/i', $userAgent)) {
      $agent['browser'] = 'Opera';
    }

    return (object) $agent;
  }

  /**
   * =====================================================
   * METHOD: update()
   * =====================================================
   * Update profil admin:
   * - Nama
   * - Email
   * - Foto
   * - Password (opsional)
   * =====================================================
   */
  public function update(Request $request)
  {
    /** @var \App\Models\User $user */
    $user = Auth::user();

    // 1. VALIDASI
    $request->validate([
      'name'  => 'required|string|max:255',
      'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],

      'current_password' => 'nullable|required_with:new_password',
      'new_password'     => 'nullable|required_with:current_password|min:8|confirmed',

      'photo' => ['nullable', 'image', 'max:2048'],
    ], [
      'current_password.required_with' => 'Mohon isi Password Lama jika ingin mengganti password.',
      'new_password.required_with'     => 'Mohon isi Password Baru jika Password Lama sudah diisi.',
    ]);

    // 2. FOTO PROFIL
    if ($request->input('delete_photo') == '1') {
      if ($user->profile_photo_path && Storage::disk('public')->exists($user->profile_photo_path)) {
        Storage::disk('public')->delete($user->profile_photo_path);
      }
      $user->profile_photo_path = null;
    }

    if ($request->hasFile('photo')) {
      if ($user->profile_photo_path && Storage::disk('public')->exists($user->profile_photo_path)) {
        Storage::disk('public')->delete($user->profile_photo_path);
      }

      $user->profile_photo_path = $request->file('photo')
        ->store('profile-photos', 'public');
    }

    // 3. UPDATE PASSWORD (OPSIONAL)
    if ($request->filled('new_password')) {
      if (!Hash::check($request->current_password, $user->password)) {
        return back()->withErrors(['current_password' => 'Password lama salah!']);
      }

      $user->password = Hash::make($request->new_password);
    }

    // 4. UPDATE DATA UTAMA
    $user->name  = $request->name;
    $user->email = $request->email;
    $user->save();

    return back()->with('success', 'Profil berhasil diperbarui!');
  }
}
