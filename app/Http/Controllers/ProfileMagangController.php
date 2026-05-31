<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Auth\AuthenticationException;

use App\Models\ProfileMagang;
use App\Models\UserMagang;

/**
 * ======================================================================
 * CONTROLLER: ProfileMagangController
 * ======================================================================
 */
class ProfileMagangController extends Controller
{

    // ======================================================================
    // KONFIGURASI FILE UPLOAD
    // ======================================================================

    private const CV_DISK         = 'local';
    private const CV_MAX_KB       = 2048;  // 2 MB

    private const PROPOSAL_DISK   = 'local';
    private const PROPOSAL_MAX_KB = 5120; // 5 MB


    // ======================================================================
    // PRIVATE HELPER — AMBIL USER TERAUTENTIKASI
    // ======================================================================

    /**
     * Ambil user yang sedang login via guard 'magang'.
     *
     * dalam method memaksa Intelephense menerima tipe UserMagang
     * sejak assignment — sebelum instanceof check dilakukan.
     *
     * @throws AuthenticationException
     */
    private function getAuthUser(): UserMagang
    {
        /** @var UserMagang|null $user */
        $user = Auth::guard('magang')->user();

        if ($user instanceof UserMagang) {
            return $user;
        }

        throw new AuthenticationException('Unauthenticated.');
    }


    // ======================================================================
    // EDIT PROFILE PAGE
    // ======================================================================

    public function edit()
    {
        /** @var UserMagang $user */
        $user = $this->getAuthUser();

        $profile = ProfileMagang::firstOrNew([
            'user_id' => $user->id,
        ]);

        return view('magang.profile.edit', compact('profile', 'user'));
    }


    // ======================================================================
    // UPDATE PROFILE & PASSWORD
    // ======================================================================

    public function update(Request $request)
    {
        /** @var UserMagang $user */
        $user = $this->getAuthUser();

        $profile = ProfileMagang::firstOrNew([
            'user_id' => $user->id,
        ]);

        /**
         * ===========================================================
         * STEP 1 — VALIDASI INPUT
         * ===========================================================
         */
        $isCvRequired = empty($profile->cv_file_path);

        $request->validate([
            // Data diri
            'full_name'        => 'required|string|max:100',
            'nim_nisn'         => 'required|string|max:50',
            'institution_name' => 'required|string|max:100',
            'education_level'  => 'required|string|in:SMA,SMK,D3,S1,S2',
            'major'            => 'required|string|max:100',
            'phone_number'     => ['required', 'regex:/^[0-9+\-\s]+$/', 'max:20'],
            'address'          => 'required|string|max:500',

            // File CV — wajib jika belum pernah upload
            'cv_file' => array_filter([
                $isCvRequired ? 'required' : 'nullable',
                'file',
                'mimes:pdf',
                'mimetypes:application/pdf',
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

            'current_password' => 'nullable|required_with:password|string',
            'password'         => [
                'nullable',
                'min:8',
                'confirmed',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
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
         *
         * $userId & $currentSessId diekstrak sebelum closure.
         * $user di-redeclare dengan @var di dalam closure karena
         * Intelephense kehilangan tipe saat variabel masuk via use().
         */
        $userId        = $user->id;
        $currentSessId = $request->session()->getId();

        DB::transaction(function () use ($request, $profile, $user, $userId, $currentSessId) {

            /** @var UserMagang $user */

            /*
            ----------------------------------------------------------
            3A. UPDATE PASSWORD
            ----------------------------------------------------------
            */
            if ($request->filled('password')) {
                $user->password_hash = Hash::make($request->password);
                $user->save();

                DB::table('sessions')
                    ->where('user_id', $userId)
                    ->where('auth_guard', 'magang')
                    ->where('id', '!=', $currentSessId)
                    ->delete();

                Log::info('Password peserta diubah', [
                    'user_id'   => $userId,
                    'timestamp' => now()->toDateTimeString(),
                ]);
            }

            /*
            ----------------------------------------------------------
            3B. HANDLE CV UPLOAD
            ----------------------------------------------------------
            */
            if ($request->hasFile('cv_file')) {
                $profile->cv_file_path = $this->handleFileUpload(
                    $request->file('cv_file'),
                    'cv_uploads/user_' . $userId,
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
                    'proposal_uploads/user_' . $userId,
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
            $profile->user_id          = $profile->user_id ?? $userId;

            $profile->save();

            Log::info('Profil peserta diperbarui', [
                'user_id'   => $userId,
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

    public function deleteCv()
    {
        /** @var UserMagang $user */
        $user    = $this->getAuthUser();
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
     * Menangani seluruh proses replace file:
     * 1. Hapus file lama dari storage (jika ada)
     * 2. Generate nama file baru dengan UUID
     * 3. Simpan file baru ke path yang ditentukan
     * 4. Kembalikan path file baru untuk disimpan ke DB
     */
    private function handleFileUpload(
        \Illuminate\Http\UploadedFile $file,
        string $directory,
        string $disk,
        ?string $oldPath = null
    ): string {
        $this->deleteFileIfExists($oldPath, $disk);

        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();

        return $file->storeAs($directory, $filename, $disk);
    }


    /**
     * Hapus file dari storage jika masih ada.
     * Tidak throw error jika file sudah tidak ada (graceful).
     */
    private function deleteFileIfExists(?string $filePath, string $disk): void
    {
        if ($filePath && Storage::disk($disk)->exists($filePath)) {
            Storage::disk($disk)->delete($filePath);
        }
    }
}
