<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash; // WAJIB DITAMBAHKAN UNTUK PASSWORD

use App\Models\ProfileMagang;

class ProfileMagangController extends Controller
{
    /**
     * ===============================================================
     * EDIT PROFILE PAGE
     * ===============================================================
     */
    public function edit()
    {
        $user = Auth::guard('magang')->user();

        $profile = ProfileMagang::firstOrNew([
            'user_id' => $user->id
        ]);

        return view('magang.profile.edit', compact('profile', 'user'));
    }


    /**
     * ===============================================================
     * UPDATE PROFILE & PASSWORD
     * ===============================================================
     */
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
         */

        // CEK PINTAR: Apakah dia belum punya CV?
        $isCvRequired = empty($profile->cv_file_path);

        $request->validate([
            // Validasi Data Diri
            'full_name' => 'required|string|max:100',
            'nim_nisn' => 'required|string|max:50',
            'institution_name' => 'required|string|max:100',
            'education_level' => 'required|string|in:SMA,SMK,D3,S1',
            'major' => 'required|string|max:100',
            'phone_number' => ['required', 'regex:/^[0-9+\-\s]+$/', 'max:20'],
            'address' => 'required|string',

            // Validasi File PDF
            'cv_file' => [
                $isCvRequired ? 'required' : 'nullable', // Wajib jika belum punya
                'file',
                'mimes:pdf',
                'max:2048'
            ],
            'proposal_file' => 'nullable|file|mimes:pdf|max:5120',

            // Validasi Ganti Password (Jika diisi)
            'current_password' => 'nullable|required_with:password|string',
            'password' => 'nullable|min:8|confirmed',
        ], [
            // Custom Error Message biar lebih ramah
            'cv_file.required' => 'Anda WAJIB mengunggah Curriculum Vitae (CV) untuk pendaftaran pertama.',
            'cv_file.mimes' => 'File CV WAJIB berformat PDF.',
            'cv_file.max' => 'Ukuran file CV maksimal 2MB.',
            'proposal_file.mimes' => 'File Proposal WAJIB berformat PDF.',
            'proposal_file.max' => 'Ukuran file Proposal maksimal 5MB.',
            'current_password.required_with' => 'Masukkan password lama jika ingin mengganti password baru.',
            'password.confirmed' => 'Konfirmasi password baru tidak cocok.'
        ]);


        /**
         * ===========================================================
         * STEP 2 — CEK PASSWORD LAMA (JIKA INGIN GANTI)
         * ===========================================================
         */
        if ($request->filled('password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Password lama yang Anda masukkan salah.'])->withInput();
            }
        }


        /**
         * ===========================================================
         * STEP 3 — DATABASE TRANSACTION
         * ===========================================================
         */
        DB::transaction(function () use ($request, $profile, $user) {

            // --- 3A. UPDATE PASSWORD USER UTAMA ---
            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);

                // Opsional: Jika user mengubah nama di profil, ubah juga di tabel user
                if ($request->filled('full_name')) {
                    $user->name = $request->full_name;
                }

                $user->save();
            }

            // --- 3B. HANDLE CV UPLOAD ---
            if ($request->hasFile('cv_file')) {
                if ($profile->cv_file_path && Storage::disk('public')->exists($profile->cv_file_path)) {
                    Storage::disk('public')->delete($profile->cv_file_path);
                }
                $profile->cv_file_path = $request->file('cv_file')->store('cv_uploads/user_' . $user->id, 'public');
            }

            // --- 3C. HANDLE PROPOSAL UPLOAD ---
            if ($request->hasFile('proposal_file')) {
                if ($profile->proposal_file_path && Storage::disk('public')->exists($profile->proposal_file_path)) {
                    Storage::disk('public')->delete($profile->proposal_file_path);
                }
                $profile->proposal_file_path = $request->file('proposal_file')->store('proposal_uploads/user_' . $user->id, 'public');
            }

            // --- 3D. UPDATE DATA PROFILE (YANG SEBELUMNYA HILANG) ---
            $profile->full_name        = $request->full_name;
            $profile->nim_nisn         = $request->nim_nisn;
            $profile->institution_name = $request->institution_name;
            $profile->education_level  = $request->education_level;
            $profile->major            = $request->major;
            $profile->phone_number     = $request->phone_number;
            $profile->address          = $request->address;

            // SIMPAN DATA
            $profile->save();
        });


        /**
         * ===========================================================
         * REDIRECT SUCCESS
         * ===========================================================
         */
        return redirect()
            ->route('profile.edit')
            ->with('success', 'Profil dan Dokumen berhasil diperbarui! Sekarang Anda siap melamar.');
    }


    /**
     * ===============================================================
     * DELETE CV FILE
     * ===============================================================
     * Menghapus file CV saja tanpa menghapus data profil lainnya.
     */
    public function deleteCv()
    {
        /** @var \App\Models\UserMagang $user */
        $user = Auth::guard('magang')->user();

        $profile = ProfileMagang::where('user_id', $user->id)->first();

        if ($profile && $profile->cv_file_path) {
            // Hapus file fisik dari folder Storage
            if (Storage::disk('public')->exists($profile->cv_file_path)) {
                Storage::disk('public')->delete($profile->cv_file_path);
            }

            // Kosongkan path di Database
            $profile->cv_file_path = null;
            $profile->save();

            return back()->with('success', 'File CV berhasil dihapus. Silakan unggah file baru jika diperlukan.');
        }

        return back()->with('error', 'Gagal menghapus file atau file tidak ditemukan.');
    }
}
