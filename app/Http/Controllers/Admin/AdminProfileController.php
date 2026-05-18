<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Carbon\Carbon;



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
    /** @var \App\Models\User $user */
    $user = Auth::user();

    $request->validate([
      'name'  => 'required|string|max:255',
      'email' => [
        'required',
        'email',
        Rule::unique('users')->ignore($user->id)
      ],
      'current_password' => 'nullable|required_with:new_password',
      'new_password' => 'nullable|required_with:current_password|min:8|confirmed',
      'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
    ]);

    $newPhotoPath = null;
    $oldPhotoToDelete = null;

    try {

      DB::transaction(function () use (
        $request,
        $user,
        &$newPhotoPath,
        &$oldPhotoToDelete
      ) {

        /**
         * ----------------------------------------------------------
         * UPDATE PASSWORD
         * ----------------------------------------------------------
         */
        if ($request->filled('new_password')) {

          if (!Hash::check($request->current_password, $user->password)) {
            throw \Illuminate\Validation\ValidationException::withMessages([
              'current_password' => 'Password lama salah!'
            ]);
          }

          $user->password = Hash::make($request->new_password);
        }

        /**
         * ----------------------------------------------------------
         * HANDLE UPLOAD FOTO BARU
         * ----------------------------------------------------------
         *
         * Upload file baru terlebih dahulu.
         * Tapi file lama BELUM dihapus.
         */
        if ($request->hasFile('photo')) {

          $newPhotoPath = $request->file('photo')
            ->store('profile-photos', 'public');

          /**
           * Simpan foto lama untuk dihapus nanti
           * setelah transaction sukses.
           */
          $oldPhotoToDelete = $user->profile_photo_path;

          /**
           * Update path baru ke model.
           */
          $user->profile_photo_path = $newPhotoPath;
        }

        /**
         * ----------------------------------------------------------
         * HANDLE DELETE FOTO
         * ----------------------------------------------------------
         */
        if ($request->input('delete_photo') == '1') {

          if ($user->profile_photo_path) {
            $oldPhotoToDelete = $user->profile_photo_path;
          }

          $user->profile_photo_path = null;
        }

        /**
         * ----------------------------------------------------------
         * UPDATE DATA PROFIL
         * ----------------------------------------------------------
         */
        $user->name = trim($request->name);

        $user->email = strtolower(
          trim($request->email)
        );

        /**
         * ----------------------------------------------------------
         * SAVE DATABASE
         * ----------------------------------------------------------
         */
        $user->save();
      });

      /**
       * --------------------------------------------------------------
       * HAPUS FILE LAMA
       * --------------------------------------------------------------
       *
       * Dilakukan SETELAH transaction sukses.
       */
      if (
        $oldPhotoToDelete &&
        Storage::disk('public')->exists($oldPhotoToDelete)
      ) {
        Storage::disk('public')->delete($oldPhotoToDelete);
      }

      return back()->with(
        'success',
        'Profil berhasil diperbarui!'
      );
    } catch (\Throwable $e) {

      /**
       * --------------------------------------------------------------
       * CLEANUP FILE BARU JIKA TRANSACTION GAGAL
       * --------------------------------------------------------------
       *
       * Mencegah orphan file.
       */
      if (
        $newPhotoPath &&
        Storage::disk('public')->exists($newPhotoPath)
      ) {
        Storage::disk('public')->delete($newPhotoPath);
      }

      throw $e;
    }
  }
}
