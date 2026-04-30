<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

use App\Models\ProfileMagang;

/**
 * ======================================================================
 * CONTROLLER: ProfileMagangController
 * ======================================================================
 *
 * Menangani update biodata, upload dokumen, dan ganti password peserta.
 *
 * IMPROVEMENT DARI VERSI SEBELUMNYA:
 *
 * 🔴 CRITICAL
 *   - [FIX] Filename CV & proposal di-randomize dengan Str::uuid()
 *           Nama file asli user tidak dipakai — mencegah tebak path,
 *           tabrakan nama, dan overwrite file orang lain
 *   - [FIX] Password policy diperkuat: wajib huruf besar + angka
 *           (konsisten dengan RegisterMagangController)
 *   - [FIX] Revoke session lain saat ganti password — jika ada sesi
 *           aktif di device lain, semua di-invalidate kecuali sesi ini
 *
 * 🟡 MAINTAINABILITY
 *   - [FIX] Logic upload file dipusatkan ke handleFileUpload()
 *           agar tidak ada duplikasi kode antara CV dan proposal
 *   - [FIX] Audit log ditambahkan untuk update profil dan ganti password
 *
 * ✅ SUDAH ADA SEBELUMNYA (DIPERTAHANKAN)
 *   - Validasi CV conditional (wajib jika belum punya)
 *   - Hash::check() untuk verifikasi password lama
 *   - DB::transaction untuk atomicity
 *   - File lama dihapus saat replace
 *   - deleteCv() untuk hapus CV saja
 *   - firstOrNew() untuk profile baru
 *
 * ======================================================================
 */
class ProfileMagangController extends Controller
{

    // ======================================================================
    // KONFIGURASI FILE UPLOAD
    // ======================================================================

    private const CV_DISK       = 'public';
    private const CV_MAX_KB     = 2048;  // 2 MB

    private const PROPOSAL_DISK   = 'public';
    private const PROPOSAL_MAX_KB = 5120; // 5 MB


    // ======================================================================
    // EDIT PROFILE PAGE
    // ======================================================================

    public function edit()
    {
        $user = Auth::guard('magang')->user();

        $profile = ProfileMagang::firstOrNew([
            'user_id' => $user->id
        ]);

        return view('magang.profile.edit', compact('profile', 'user'));
    }


    // ======================================================================
    // UPDATE PROFILE & PASSWORD
    // ======================================================================

    public function update(Request $request)
    {
        /** @var \App\Models\UserMagang $user */
        $user = Auth::guard('magang')->user();

        $profile = ProfileMagang::firstOrNew([
            'user_id' => $user->id
        ]);

        /**
         * ===========================================================
         * STEP 1 — VALIDASI INPUT
         * ===========================================================
         *
         * PASSWORD POLICY (diperkuat — konsisten dengan register):
         * - min:8           → minimal 8 karakter
         * - regex huruf besar → wajib ada minimal 1 huruf kapital
         * - regex angka     → wajib ada minimal 1 digit angka
         * - confirmed       → harus match dengan password_confirmation
         */
        $isCvRequired = empty($profile->cv_file_path);

        $request->validate([
            // Data diri
            'full_name'        => 'required|string|max:100',
            'nim_nisn'         => 'required|string|max:50',
            'institution_name' => 'required|string|max:100',
            'education_level'  => 'required|string|in:SMA,SMK,D3,S1',
            'major'            => 'required|string|max:100',
            'phone_number'     => ['required', 'regex:/^[0-9+\-\s]+$/', 'max:20'],
            'address'          => 'required|string|max:500',

            // File CV — wajib jika belum pernah upload, nullable jika sudah
            'cv_file' => array_filter([
                $isCvRequired ? 'required' : 'nullable',
                'file',
                'mimes:pdf',
                'mimetypes:application/pdf', // validasi konten real file
                'max:' . self::CV_MAX_KB,
            ]),

            // File proposal — selalu opsional
            'proposal_file' => [
                'nullable',
                'file',
                'mimes:pdf',
                'mimetypes:application/pdf',
                'max:' . self::PROPOSAL_MAX_KB,
            ],

            // Ganti password — semua nullable, tapi jika password diisi maka current_password wajib
            'current_password' => 'nullable|required_with:password|string',
            'password'         => [
                'nullable',
                'min:8',
                'confirmed',
                'regex:/[A-Z]/', // wajib huruf besar
                'regex:/[0-9]/', // wajib angka
            ],
        ], [
            'cv_file.required'               => 'Anda WAJIB mengunggah Curriculum Vitae (CV) untuk pendaftaran pertama.',
            'cv_file.mimes'                  => 'File CV harus berformat PDF.',
            'cv_file.mimetypes'              => 'File CV harus berformat PDF yang valid.',
            'cv_file.max'                    => 'Ukuran file CV maksimal 2MB.',
            'proposal_file.mimes'            => 'File Proposal harus berformat PDF.',
            'proposal_file.mimetypes'        => 'File Proposal harus berformat PDF yang valid.',
            'proposal_file.max'              => 'Ukuran file Proposal maksimal 5MB.',
            'current_password.required_with' => 'Masukkan password lama jika ingin mengganti password baru.',
            'password.confirmed'             => 'Konfirmasi password baru tidak cocok.',
            'password.regex'                 => 'Password baru harus mengandung minimal 1 huruf besar dan 1 angka.',
        ]);


        /**
         * ===========================================================
         * STEP 2 — VERIFIKASI PASSWORD LAMA
         * ===========================================================
         * Hash::check() memastikan current_password cocok dengan
         * password yang tersimpan di DB sebelum diizinkan mengganti.
         */
        if ($request->filled('password')) {
            if (!Hash::check($request->current_password, $user->password_hash)) {
                return back()
                    ->withErrors(['current_password' => 'Password lama yang Anda masukkan salah.'])
                    ->withInput();
            }
        }


        /**
         * ===========================================================
         * STEP 3 — DATABASE TRANSACTION
         * ===========================================================
         */
        DB::transaction(function () use ($request, $profile, $user) {

            /*
            ----------------------------------------------------------
            3A. UPDATE PASSWORD
            ----------------------------------------------------------
            IMPROVEMENT — Revoke session lain:
            Saat password diganti, semua session aktif di device lain
            di-invalidate. Hanya session saat ini yang tetap aktif.
            Ini mencegah session lama dipakai jika akun sempat diakses
            orang lain.
            ----------------------------------------------------------
            */
            if ($request->filled('password')) {
                $user->password_hash = Hash::make($request->password);
                $user->save();

                // Revoke semua session lain kecuali yang sekarang
                DB::table('sessions')
                    ->where('user_id', $user->id)
                    ->where('id', '!=', session()->getId())
                    ->delete();

                Log::info('Password peserta diubah', [
                    'user_id'   => $user->id,
                    'timestamp' => now()->toDateTimeString(),
                ]);
            }

            /*
            ----------------------------------------------------------
            3B. HANDLE CV UPLOAD
            ----------------------------------------------------------
            handleFileUpload() menangani:
            - Hapus file lama dari storage
            - Generate UUID sebagai nama file baru
            - Simpan file ke path yang ditentukan
            ----------------------------------------------------------
            */
            if ($request->hasFile('cv_file')) {
                $profile->cv_file_path = $this->handleFileUpload(
                    $request->file('cv_file'),
                    'cv_uploads/user_' . $user->id,
                    self::CV_DISK,
                    $profile->cv_file_path
                );
            }

            /*
            ----------------------------------------------------------
            3C. HANDLE PROPOSAL UPLOAD
            ----------------------------------------------------------
            */
            if ($request->hasFile('proposal_file')) {
                $profile->proposal_file_path = $this->handleFileUpload(
                    $request->file('proposal_file'),
                    'proposal_uploads/user_' . $user->id,
                    self::PROPOSAL_DISK,
                    $profile->proposal_file_path
                );
            }

            /*
            ----------------------------------------------------------
            3D. UPDATE DATA PROFIL
            ----------------------------------------------------------
            */
            $profile->full_name        = $request->full_name;
            $profile->nim_nisn         = $request->nim_nisn;
            $profile->institution_name = $request->institution_name;
            $profile->education_level  = $request->education_level;
            $profile->major            = $request->major;
            $profile->phone_number     = $request->phone_number;
            $profile->address          = $request->address;
            $profile->user_id          = $profile->user_id ?? $user->id;

            $profile->save();

            Log::info('Profil peserta diperbarui', [
                'user_id'   => $user->id,
                'timestamp' => now()->toDateTimeString(),
            ]);
        });

        return redirect()
            ->route('profile.edit')
            ->with('success', 'Profil dan Dokumen berhasil diperbarui! Sekarang Anda siap melamar.');
    }


    // ======================================================================
    // DELETE CV
    // ======================================================================

    /**
     * Hapus file CV saja tanpa menghapus data profil lainnya.
     */
    public function deleteCv()
    {
        /** @var \App\Models\UserMagang $user */
        $user    = Auth::guard('magang')->user();
        $profile = ProfileMagang::where('user_id', $user->id)->first();

        if ($profile && $profile->cv_file_path) {
            $this->deleteFileIfExists($profile->cv_file_path, self::CV_DISK);

            $profile->cv_file_path = null;
            $profile->save();

            Log::info('CV peserta dihapus', [
                'user_id'   => $user->id,
                'timestamp' => now()->toDateTimeString(),
            ]);

            return back()->with('success', 'File CV berhasil dihapus. Silakan unggah file baru jika diperlukan.');
        }

        return back()->with('error', 'Gagal menghapus file atau file tidak ditemukan.');
    }


    // ======================================================================
    // PRIVATE HELPERS
    // ======================================================================

    /**
     * -----------------------------------------------------------------------
     * handleFileUpload()
     * -----------------------------------------------------------------------
     *
     * Menangani seluruh proses replace file:
     * 1. Hapus file lama dari storage (jika ada)
     * 2. Generate nama file baru dengan UUID
     * 3. Simpan file baru ke path yang ditentukan
     * 4. Kembalikan path file baru untuk disimpan ke DB
     *
     * KENAPA UUID?
     * Nama file asli dari user ("cv andi ramdhani.pdf") tidak dipakai karena:
     * - Bisa bentrok dengan file user lain
     * - Bisa ditebak path-nya langsung
     * - Karakter spesial bisa menyebabkan error di beberapa OS
     *
     * -----------------------------------------------------------------------
     */
    private function handleFileUpload(
        \Illuminate\Http\UploadedFile $file,
        string $directory,
        string $disk,
        ?string $oldPath = null
    ): string {
        // Hapus file lama jika ada
        $this->deleteFileIfExists($oldPath, $disk);

        // UUID sebagai nama file — unik dan tidak bisa ditebak
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();

        return $file->storeAs($directory, $filename, $disk);
    }


    /**
     * -----------------------------------------------------------------------
     * deleteFileIfExists()
     * -----------------------------------------------------------------------
     * Hapus file dari storage jika masih ada.
     * Tidak throw error jika file sudah tidak ada (graceful).
     * -----------------------------------------------------------------------
     */
    private function deleteFileIfExists(?string $filePath, string $disk): void
    {
        if ($filePath && Storage::disk($disk)->exists($filePath)) {
            Storage::disk($disk)->delete($filePath);
        }
    }
}
