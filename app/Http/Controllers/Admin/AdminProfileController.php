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
 * - Menampilkan histori sesi login (device, IP, last active)
 * - Mengelola update data profil (nama, email, foto, password)
 *
 * KEAMANAN:
 * - Validasi ketat pada perubahan password
 * - Verifikasi password lama sebelum update
 * - Pembatasan ukuran & tipe file foto
 *
 * CATATAN DESAIN:
 * - Tidak menggunakan Model Session (langsung DB)
 * - Helper user-agent ditulis manual (tanpa library eksternal)
 *
 * POTENSI MIGRASI:
 * - Session bisa diganti ke Laravel Sanctum / Jetstream
 * - Parsing user-agent bisa pakai jenssegers/agent
 * =========================================================
 */
class AdminProfileController extends Controller
{
    /**
     * =====================================================
     * METHOD: index()
     * =====================================================
     * TUJUAN:
     * - Menampilkan halaman profil admin
     * - Menampilkan daftar sesi login aktif & histori
     *
     * DATA DIKIRIM KE VIEW:
     * - user     → data admin login
     * - sessions → daftar device & aktivitas login
     * =====================================================
     */
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        /* =====================================================
         * AMBIL DATA SESI LOGIN USER
         * =====================================================
         * - Mengambil data dari tabel sessions
         * - Diurutkan dari aktivitas terbaru
         * - Dipetakan agar lebih mudah dipakai di view
         *
         * CATATAN:
         * - last_activity disimpan dalam bentuk timestamp
         * - session()->getId() dipakai untuk tandai device aktif
         * ===================================================== */
        $sessions = DB::table('sessions')
            ->where('user_id', $user->id)
            ->orderBy('last_activity', 'desc')
            ->get()
            ->map(function ($session) {
                return (object) [
                    'agent' => $this->createAgent($session->user_agent),
                    'ip_address' => $session->ip_address,
                    'is_current_device' => $session->id === request()->session()->getId(),
                    'last_active' => Carbon::createFromTimestamp($session->last_activity)->diffForHumans(),
                ];
            });

        return view('admin.profile', compact('user', 'sessions'));
    }

    /**
     * =====================================================
     * HELPER: createAgent()
     * =====================================================
     * TUJUAN:
     * - Mengubah user_agent string menjadi informasi yang
     *   lebih manusiawi (platform & browser)
     *
     * PARAMETER:
     * - $userAgent → string user_agent dari tabel sessions
     *
     * OUTPUT:
     * - object { platform, browser }
     *
     * CATATAN:
     * - Regex sederhana, cukup untuk kebutuhan dashboard
     *
     * POTENSI MIGRASI:
     * - Bisa diganti ke library jenssegers/agent
     * =====================================================
     */
    private function createAgent($userAgent)
    {
        $agent = [
            'platform' => 'Tidak Diketahui',
            'browser'  => 'Tidak Diketahui',
        ];

        // Deteksi platform
        if (preg_match('/windows|win32/i', $userAgent)) $agent['platform'] = 'Windows';
        elseif (preg_match('/macintosh|mac os x/i', $userAgent)) $agent['platform'] = 'macOS';
        elseif (preg_match('/linux/i', $userAgent)) $agent['platform'] = 'Linux';
        elseif (preg_match('/android/i', $userAgent)) $agent['platform'] = 'Android';
        elseif (preg_match('/iphone/i', $userAgent)) $agent['platform'] = 'iPhone';

        // Deteksi browser
        if (preg_match('/MSIE/i', $userAgent) && !preg_match('/Opera/i', $userAgent)) $agent['browser'] = 'Internet Explorer';
        elseif (preg_match('/Firefox/i', $userAgent)) $agent['browser'] = 'Firefox';
        elseif (preg_match('/Chrome/i', $userAgent)) $agent['browser'] = 'Chrome';
        elseif (preg_match('/Safari/i', $userAgent)) $agent['browser'] = 'Safari';
        elseif (preg_match('/Opera/i', $userAgent)) $agent['browser'] = 'Opera';

        return (object) $agent;
    }

    /**
     * =====================================================
     * METHOD: update()
     * =====================================================
     * TUJUAN:
     * - Memperbarui data profil admin:
     *   - Nama
     *   - Email
     *   - Foto profil
     *   - Password (opsional)
     *
     * PRINSIP:
     * - Password hanya diubah jika user mengisi password baru
     * - Foto lama dibersihkan untuk mencegah file sampah
     * =====================================================
     */
    public function update(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        /* =====================================================
         * 1. VALIDASI DATA (KETAT & AMAN)
         * =====================================================
         * LOGIC KHUSUS PASSWORD:
         * - current_password & new_password saling bergantung
         * - new_password harus dikonfirmasi
         *
         * VALIDASI FOTO:
         * - Hanya file gambar
         * - Maksimal 2MB
         * ===================================================== */
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

        /* =====================================================
         * 2. LOGIC FOTO PROFIL
         * =====================================================
         * URUTAN:
         * 1. Hapus foto jika user klik tombol hapus
         * 2. Upload foto baru (menimpa foto lama)
         *
         * CATATAN:
         * - File lama selalu dihapus untuk mencegah storage penuh
         * ===================================================== */

        // 2.1 Hapus foto jika diminta
        if ($request->input('delete_photo') == '1') {
            if ($user->profile_photo_path && Storage::disk('public')->exists($user->profile_photo_path)) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }
            $user->profile_photo_path = null;
        }

        // 2.2 Upload foto baru
        if ($request->hasFile('photo')) {
            if ($user->profile_photo_path && Storage::disk('public')->exists($user->profile_photo_path)) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }

            $path = $request->file('photo')->store('profile-photos', 'public');
            $user->profile_photo_path = $path;
        }

        /* =====================================================
         * 3. LOGIC UPDATE PASSWORD
         * =====================================================
         * - Hanya dijalankan jika password baru diisi
         * - Password lama wajib diverifikasi
         * ===================================================== */
        if ($request->filled('new_password')) {

            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Password lama salah!']);
            }

            $user->password = Hash::make($request->new_password);
        }

        /* =====================================================
         * 4. UPDATE DATA PROFIL
         * ===================================================== */
        $user->name  = $request->name;
        $user->email = $request->email;
        $user->save();

        return back()->with('success', 'Profil berhasil diperbarui!');
    }
}
