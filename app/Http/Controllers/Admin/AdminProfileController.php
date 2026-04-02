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
 * ======================================================================
 * CONTROLLER: AdminProfileController
 * ======================================================================
 *
 * TUJUAN CONTROLLER
 * ----------------------------------------------------------------------
 * Controller ini bertanggung jawab untuk menangani seluruh fitur yang
 * berkaitan dengan profil Admin.
 *
 * Fitur yang ditangani:
 *
 * 1. Menampilkan halaman profil admin
 * 2. Menampilkan histori sesi login admin
 * 3. Memperbarui data profil admin
 *    - Nama
 *    - Email
 *    - Foto profil
 *    - Password
 *
 * ----------------------------------------------------------------------
 * SUMBER DATA
 * ----------------------------------------------------------------------
 * Data sesi login diambil dari tabel `sessions` yang digunakan oleh
 * Laravel ketika SESSION_DRIVER = database.
 *
 * Informasi yang ditampilkan:
 * - IP Address
 * - Device saat ini atau bukan
 * - Waktu terakhir aktif
 *
 * ----------------------------------------------------------------------
 * CATATAN UNTUK DEVELOPER SELANJUTNYA
 * ----------------------------------------------------------------------
 * Jika sistem berkembang, fitur berikut dapat ditambahkan:
 *
 * - Parsing user agent untuk menampilkan browser dan platform
 * - Riwayat login lengkap
 * - Logout device lain
 *
 * ======================================================================
 */
class AdminProfileController extends Controller
{

  /**
   * ==================================================================
   * METHOD: index()
   * ==================================================================
   *
   * TUJUAN:
   * Menampilkan halaman profil admin beserta histori sesi login.
   *
   * DATA YANG DIAMBIL:
   * - Data user admin yang sedang login
   * - Daftar session login yang tersimpan di database
   *
   * ALUR PROSES:
   *
   * 1. Mengambil user yang sedang login.
   * 2. Mengambil seluruh session login user dari tabel sessions.
   * 3. Mengurutkan session berdasarkan aktivitas terakhir.
   * 4. Mengubah timestamp menjadi format waktu yang mudah dibaca.
   * 5. Menentukan apakah session tersebut adalah device saat ini.
   * 6. Mengirim data ke view admin.profile.
   *
   * ==================================================================
   */
  public function index()
  {

    /**
     * --------------------------------------------------------------
     * STEP 1
     * MENGAMBIL USER YANG SEDANG LOGIN
     * --------------------------------------------------------------
     *
     * Auth::user() mengambil user yang sedang terautentikasi.
     * Pada sistem ini guard default digunakan oleh Admin.
     */
    /** @var \App\Models\User $user */
    $user = Auth::user();


    /**
     * --------------------------------------------------------------
     * STEP 2
     * MENGAMBIL HISTORI SESSION LOGIN
     * --------------------------------------------------------------
     *
     * Session login disimpan pada tabel `sessions` ketika
     * Laravel menggunakan database session driver.
     *
     * Query ini mengambil seluruh session yang dimiliki user
     * kemudian mengurutkannya dari aktivitas terbaru.
     */
    $sessions = DB::table('sessions')
      ->where('user_id', $user->id)
      ->orderBy('last_activity', 'desc')
      ->get()


      /**
       * ----------------------------------------------------------
       * STEP 3
       * TRANSFORM DATA SESSION
       * ----------------------------------------------------------
       *
       * Map digunakan untuk memformat data session agar lebih
       * mudah digunakan oleh view.
       */
      ->map(function ($session) {

        return (object) [

          /**
           * IP Address dari session login
           */
          'ip_address' => $session->ip_address,

          /**
           * Menentukan apakah session ini adalah device
           * yang sedang digunakan saat ini.
           *
           * request()->session()->getId()
           * mengambil session ID dari request yang aktif.
           */
          'is_current_device' => $session->id === request()->session()->getId(),

          /**
           * Mengubah timestamp last_activity menjadi
           * format waktu yang mudah dibaca manusia.
           *
           * Contoh output:
           * - 5 minutes ago
           * - 2 hours ago
           * - 3 days ago
           */
          'last_active' => Carbon::createFromTimestamp($session->last_activity)
            ->diffForHumans(),
        ];
      });


    /**
     * --------------------------------------------------------------
     * STEP 4
     * MENGIRIM DATA KE VIEW
     * --------------------------------------------------------------
     *
     * View yang digunakan:
     * resources/views/admin/profile.blade.php
     *
     * Data yang dikirim:
     * - user     : data admin yang sedang login
     * - sessions : histori sesi login
     */
    return view('admin.profile.index', compact('user', 'sessions'));
  }


  /**
   * ==================================================================
   * METHOD: update()
   * ==================================================================
   *
   * TUJUAN:
   * Memperbarui data profil admin.
   *
   * DATA YANG DAPAT DIPERBARUI:
   * - Nama
   * - Email
   * - Foto profil
   * - Password (opsional)
   *
   * ALUR PROSES:
   *
   * 1. Validasi input dari user.
   * 2. Menghapus foto profil jika user memilih delete.
   * 3. Upload foto profil baru jika ada.
   * 4. Update password jika user mengisi password baru.
   * 5. Update nama dan email.
   * 6. Simpan perubahan ke database.
   *
   * ==================================================================
   */
  public function update(Request $request)
  {

    /**
     * --------------------------------------------------------------
     * STEP 1
     * MENGAMBIL USER YANG SEDANG LOGIN
     * --------------------------------------------------------------
     */
    /** @var \App\Models\User $user */
    $user = Auth::user();


    /**
     * --------------------------------------------------------------
     * STEP 2
     * VALIDASI INPUT
     * --------------------------------------------------------------
     *
     * Validasi memastikan data yang dikirim user sesuai aturan.
     *
     * Aturan penting:
     *
     * - Email harus unik kecuali milik user sendiri.
     * - Password baru minimal 8 karakter.
     * - Password baru harus dikonfirmasi.
     * - Foto profil maksimal 2MB.
     */
    $request->validate([
      'name'  => 'required|string|max:255',

      'email' => [
        'required',
        'email',
        Rule::unique('users')->ignore($user->id)
      ],

      'current_password' => 'nullable|required_with:new_password',

      'new_password' => 'nullable|required_with:current_password|min:8|confirmed',

      'photo' => ['nullable', 'image', 'max:2048'],
    ], [
      'current_password.required_with' =>
      'Mohon isi Password Lama jika ingin mengganti password.',

      'new_password.required_with' =>
      'Mohon isi Password Baru jika Password Lama sudah diisi.',
    ]);


    /**
     * --------------------------------------------------------------
     * STEP 3
     * HAPUS FOTO PROFIL (JIKA DIMINTA USER)
     * --------------------------------------------------------------
     */
    if ($request->input('delete_photo') == '1') {

      if (
        $user->profile_photo_path &&
        Storage::disk('public')->exists($user->profile_photo_path)
      ) {
        Storage::disk('public')->delete($user->profile_photo_path);
      }

      $user->profile_photo_path = null;
    }


    /**
     * --------------------------------------------------------------
     * STEP 4
     * UPLOAD FOTO PROFIL BARU
     * --------------------------------------------------------------
     */
    if ($request->hasFile('photo')) {

      /**
       * Hapus foto lama jika ada
       */
      if (
        $user->profile_photo_path &&
        Storage::disk('public')->exists($user->profile_photo_path)
      ) {
        Storage::disk('public')->delete($user->profile_photo_path);
      }

      /**
       * Simpan foto baru
       */
      $user->profile_photo_path = $request->file('photo')
        ->store('profile-photos', 'public');
    }


    /**
     * --------------------------------------------------------------
     * STEP 5
     * UPDATE PASSWORD (OPSIONAL)
     * --------------------------------------------------------------
     *
     * Password hanya diupdate jika user mengisi field
     * new_password.
     */
    if ($request->filled('new_password')) {

      /**
       * Cek apakah password lama benar
       */
      if (!Hash::check($request->current_password, $user->password)) {

        return back()->withErrors([
          'current_password' => 'Password lama salah!'
        ]);
      }

      /**
       * Hash password baru sebelum disimpan
       */
      $user->password = Hash::make($request->new_password);
    }


    /**
     * --------------------------------------------------------------
     * STEP 6
     * UPDATE DATA UTAMA USER
     * --------------------------------------------------------------
     */
    $user->name  = $request->name;
    $user->email = $request->email;


    /**
     * Simpan perubahan ke database
     */
    $user->save();


    /**
     * --------------------------------------------------------------
     * STEP 7
     * REDIRECT KEMBALI KE HALAMAN PROFIL
     * --------------------------------------------------------------
     */
    return back()->with('success', 'Profil berhasil diperbarui!');
  }
}
