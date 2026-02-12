<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Validation\Rule;

class AdminProfileController extends Controller
{
    // 1. TAMPILKAN HALAMAN PROFIL
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // --- AMBIL DATA SESI LOGIN ---
        $sessions = DB::table('sessions')
            ->where('user_id', $user->id)
            ->orderBy('last_activity', 'desc')
            ->get()
            ->map(function ($session) {
                return (object) [
                    // 'agent' => $this->createAgent($session->user_agent),
                    'ip_address' => $session->ip_address,
                    'is_current_device' => $session->id === request()->session()->getId(),
                    'last_active' => Carbon::createFromTimestamp($session->last_activity)->diffForHumans(),
                ];
            });

        return view('admin.profile', compact('user', 'sessions'));
    }

    // --- FUNGSI BANTUAN BIAR NAMA BROWSER RAPI (HELPER) ---
    // Taruh di paling bawah class controller (private function)
    // private function createAgent($userAgent)
    // {
    //     $agent = [
    //         'platform' => 'Tidak Diketahui',
    //         'browser'  => 'Tidak Diketahui',
    //     ];

    //     try {
    //         // Parse platform
    //         if (preg_match('/windows|win32/i', $userAgent)) {
    //             $agent['platform'] = 'Windows';
    //         } elseif (preg_match('/macintosh|mac os x/i', $userAgent)) {
    //             $agent['platform'] = 'macOS';
    //         } elseif (preg_match('/linux/i', $userAgent)) {
    //             $agent['platform'] = 'Linux';
    //         } elseif (preg_match('/android/i', $userAgent)) {
    //             $agent['platform'] = 'Android';
    //         } elseif (preg_match('/iphone/i', $userAgent)) {
    //             $agent['platform'] = 'iPhone';
    //         } elseif (preg_match('/ipad/i', $userAgent)) {
    //             $agent['platform'] = 'iPad';
    //         }

    //         // Parse browser
    //         if (preg_match('/MSIE|Trident/i', $userAgent) && !preg_match('/Opera/i', $userAgent)) {
    //             $agent['browser'] = 'Internet Explorer';
    //         } elseif (preg_match('/Firefox/i', $userAgent)) {
    //             $agent['browser'] = 'Firefox';
    //         } elseif (preg_match('/Chrome/i', $userAgent)) {
    //             $agent['browser'] = 'Chrome';
    //         } elseif (preg_match('/Safari/i', $userAgent)) {
    //             $agent['browser'] = 'Safari';
    //         } elseif (preg_match('/Opera/i', $userAgent)) {
    //             $agent['browser'] = 'Opera';
    //         } elseif (preg_match('/Edge/i', $userAgent)) {
    //             $agent['browser'] = 'Edge';
    //         }
    //     } catch (\Exception $e) {
    //         // Jika parsing error, fallback ke default
    //         Log::warning('User agent parsing error', ['user_agent' => $userAgent, 'error' => $e->getMessage()]);
    //     }

    //     return (object) $agent;
    // }

    public function update(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // 1. VALIDASI KETAT
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', \Illuminate\Validation\Rule::unique('users')->ignore($user->id)],

            // LOGIC BARU: Saling Membutuhkan
            // Jika new_password diisi, current_password WAJIB ada.
            'current_password' => 'nullable|required_with:new_password',

            // Jika current_password diisi, new_password WAJIB ada.
            'new_password' => 'nullable|required_with:current_password|min:8|confirmed',

            // VALIDASI FOTO: Maksimal 2MB, harus gambar (jpg, png, dll)
            'photo' => ['nullable', 'image', 'max:2048'],
        ], [
            // Custom Error Message biar Admin paham
            'current_password.required_with' => 'Mohon isi Password Lama jika ingin mengganti password.',
            'new_password.required_with' => 'Mohon isi Password Baru jika Password Lama sudah diisi.',
        ]);

        // --- LOGIC UPLOAD FOTO BARU ---
        // --- 1. LOGIC HAPUS FOTO (Eksekusi duluan) ---
        // Jika user klik tombol sampah, input hidden ini bernilai '1'
        if ($request->input('delete_photo') == '1') {
            if ($user->profile_photo_path && Storage::disk('public')->exists($user->profile_photo_path)) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }
            $user->profile_photo_path = null;
        }

        // --- 2. LOGIC UPLOAD FOTO BARU (Menimpa logic hapus jika ada) ---
        if ($request->hasFile('photo')) {
            if ($user->profile_photo_path && Storage::disk('public')->exists($user->profile_photo_path)) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }
            $path = $request->file('photo')->store('profile-photos', 'public');
            $user->profile_photo_path = $path;
        }
        // ------------------------------

        // 2. LOGIC UPDATE PASSWORD
        // Kita hanya proses JIKA user benar-benar mengisi password BARU
        if ($request->filled('new_password')) {

            // Cek Password Lama
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Password lama salah!']);
            }

            // Simpan Password Baru
            $user->password = Hash::make($request->new_password);
        }

        // 3. UPDATE PROFIL
        $user->name = $request->name;
        $user->email = $request->email;
        $user->save();

        return back()->with('success', 'Profil berhasil diperbarui!');
    }
}
