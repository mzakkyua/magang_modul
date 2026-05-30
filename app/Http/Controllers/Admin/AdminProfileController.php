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
     * Filter auth_guard = 'web' memastikan hanya sesi admin
     * yang ditampilkan — sesi peserta magang tidak ikut muncul.
     */
    $rawSessions = DB::table('sessions')
      ->where('user_id', $user->id)
      ->where('auth_guard', 'web')
      ->orderBy('last_activity', 'desc')
      ->get();


    /**
     * --------------------------------------------------------------
     * STEP 3
     * AMBIL SESSION ID AKTIF
     * --------------------------------------------------------------
     *
     * Diambil di luar closure map() agar Intelephense dapat
     * menyimpulkan tipe data dengan benar (string, bukan object).
     *
     * Manfaat tambahan:
     * - Lebih efisien: session()->getId() hanya dipanggil sekali,
     *   bukan setiap iterasi map.
     */
    $currentSessionId = session()->getId() ?? '';


    /**
     * --------------------------------------------------------------
     * STEP 4
     * TRANSFORM DATA SESSION
     * --------------------------------------------------------------
     *
     * Map digunakan untuk memformat data session agar lebih
     * mudah digunakan oleh view.
     *
     * Variabel $currentSessionId di-pass via `use` agar tersedia
     * di dalam closure tanpa perlu chaining request()->session().
     */
    $sessions = $rawSessions->map(function ($session) use ($currentSessionId) {

      $item = new \stdClass();

      /**
       * IP Address dari session login
       */
      $item->ip_address = $session->ip_address;

      /**
       * Menentukan apakah session ini adalah device
       * yang sedang digunakan saat ini.
       *
       * Perbandingan dilakukan antara dua string (session ID),
       * sehingga Intelephense dapat menyimpulkan tipe dengan benar.
       */
      $item->is_current_device = $session->id === $currentSessionId;

      /**
       * Mengubah timestamp last_activity menjadi
       * format waktu yang mudah dibaca manusia.
       *
       * Contoh output:
       * - 5 minutes ago
       * - 2 hours ago
       * - 3 days ago
       */
      $item->last_active = Carbon::createFromTimestamp($session->last_activity)
        ->diffForHumans();

      return $item;
    });


    /**
     * --------------------------------------------------------------
     * STEP 5
     * MENGIRIM DATA KE VIEW
     * --------------------------------------------------------------
     *
     * View yang digunakan:
     * resources/views/admin/profile/index.blade.php
     *
     * Data yang dikirim:
     * - user     : data admin yang sedang login
     * - sessions : histori sesi login (hanya guard web)
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
   * 2. Upload foto profil baru ATAU hapus foto — tidak keduanya.
   * 3. Update password jika user mengisi password baru.
   * 4. Update nama dan email.
   * 5. Simpan perubahan ke database.
   *
   * ==================================================================
   */
  public function update(Request $request)
  {
    /** @var \App\Models\User $user */
    $user = Auth::user();

    $request->validate([
      'name'             => 'required|string|max:255',
      'email'            => [
        'required',
        'email',
        Rule::unique('users')->ignore($user->id),
      ],
      'current_password' => 'nullable|required_with:new_password',
      'new_password'     => 'nullable|required_with:current_password|min:8|confirmed',
      'photo'            => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
    ]);

    $newPhotoPath     = null;
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
              'current_password' => 'Password lama salah!',
            ]);
          }

          $user->password = Hash::make($request->new_password);
        }

        /**
         * ----------------------------------------------------------
         * HANDLE FOTO — UPLOAD ATAU DELETE, TIDAK KEDUANYA
         * ----------------------------------------------------------
         *
         * Upload foto baru dan hapus foto adalah dua aksi yang
         * saling eksklusif. Menggunakan elseif memastikan jika
         * keduanya dikirim bersamaan (manipulasi form), hanya
         * upload yang diproses — delete diabaikan.
         *
         * Tanpa elseif (dua if terpisah):
         *   1. Blok upload jalan → $oldPhotoToDelete = foto lama
         *   2. Blok delete jalan → $oldPhotoToDelete di-overwrite
         *      dengan foto BARU yang baru saja di-set
         *   Hasil: foto baru diupload lalu langsung dihapus,
         *   user berakhir tanpa foto sama sekali.
         * ----------------------------------------------------------
         */
        if ($request->hasFile('photo')) {

          /**
           * Upload file baru ke storage.
           * File lama BELUM dihapus — tunggu transaction sukses.
           */
          $newPhotoPath = $request->file('photo')
            ->store('profile-photos', 'public');

          $oldPhotoToDelete         = $user->profile_photo_path;
          $user->profile_photo_path = $newPhotoPath;
        } elseif ($request->input('delete_photo') == '1') {

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
        $user->name  = trim($request->name);
        $user->email = strtolower(trim($request->email));

        /**
         * ----------------------------------------------------------
         * SAVE DATABASE
         * ----------------------------------------------------------
         */
        $user->save();

        /**
         * ----------------------------------------------------------
         * REVOKE SESSION LAIN JIKA PASSWORD DIGANTI
         * ----------------------------------------------------------
         *
         * Tujuan:
         * Mengeluarkan seluruh device lain setelah password berubah
         * untuk mencegah session lama tetap aktif.
         *
         * Filter auth_guard = 'web' memastikan hanya sesi admin
         * yang di-revoke — sesi peserta magang tidak terpengaruh.
         *
         * Session saat ini TIDAK dihapus.
         */
        if ($request->filled('new_password')) {

          DB::table('sessions')
            ->where('user_id', $user->id)
            ->where('auth_guard', 'web')
            ->where('id', '!=', $request->session()->getId())
            ->delete();
        }

        $request->session()->regenerate();
      });

      /**
       * --------------------------------------------------------------
       * HAPUS FILE LAMA
       * --------------------------------------------------------------
       *
       * Dilakukan SETELAH transaction sukses.
       * Jika dilakukan di dalam transaction dan DB rollback,
       * file sudah terhapus tapi DB tidak berubah → orphan data.
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
       * Mencegah orphan file di storage jika DB transaction gagal.
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
