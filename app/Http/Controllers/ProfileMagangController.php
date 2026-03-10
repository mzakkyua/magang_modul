<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

use App\Models\ProfileMagang;

class ProfileMagangController extends Controller
{

    /**
     * ===============================================================
     * EDIT PROFILE PAGE
     * ===============================================================
     *
     * Menampilkan halaman edit profil peserta magang.
     *
     */

    public function edit()
    {

        /**
         * ===========================================================
         * STEP 1 — AMBIL USER LOGIN
         * ===========================================================
         */

        $user = Auth::guard('magang')->user();



        /**
         * ===========================================================
         * STEP 2 — AMBIL ATAU BUAT PROFILE
         * ===========================================================
         *
         * firstOrNew digunakan agar:
         * - jika profile sudah ada → ambil
         * - jika belum ada → buat instance baru
         *
         */

        $profile = ProfileMagang::firstOrNew([
            'user_id' => $user->id
        ]);



        return view('magang.profile.edit', compact('profile', 'user'));
    }



    /**
     * ===============================================================
     * UPDATE PROFILE
     * ===============================================================
     *
     * Update data profil termasuk:
     * - biodata
     * - upload CV
     * - upload proposal
     *
     */

    public function update(Request $request)
    {

        /**
         * ===========================================================
         * STEP 1 — AMBIL USER LOGIN
         * ===========================================================
         */

        $user = Auth::guard('magang')->user();



        /**
         * ===========================================================
         * STEP 2 — AMBIL PROFILE
         * ===========================================================
         */

        $profile = ProfileMagang::firstOrNew([
            'user_id' => $user->id
        ]);



        /**
         * ===========================================================
         * STEP 3 — VALIDASI INPUT
         * ===========================================================
         */

        $request->validate([

            'phone_number' => [
                'required',
                'regex:/^[0-9+\-\s]+$/',
                'max:20'
            ],

            'institution_name' => 'required|string|max:100',

            'major' => 'required|string|max:100',

            'address' => 'required|string',

            /**
             * VALIDASI FILE
             */

            'cv_file' => 'nullable|file|mimes:pdf|max:2048',

            'proposal_file' => 'nullable|file|mimes:pdf|max:5120',

        ]);



        /**
         * ===========================================================
         * STEP 4 — DATABASE TRANSACTION
         * ===========================================================
         */

        DB::transaction(function () use ($request, $profile, $user) {

            /**
             * =======================================================
             * HANDLE CV UPLOAD
             * =======================================================
             */

            if ($request->hasFile('cv_file')) {

                /**
                 * Hapus file lama jika ada
                 */

                if (
                    $profile->cv_file_path &&
                    Storage::disk('public')->exists($profile->cv_file_path)
                ) {

                    Storage::disk('public')->delete($profile->cv_file_path);
                }

                /**
                 * Simpan file baru
                 */

                $cvPath = $request->file('cv_file')
                    ->store('cv_uploads/user_' . $user->id, 'public');

                $profile->cv_file_path = $cvPath;
            }



            /**
             * =======================================================
             * HANDLE PROPOSAL UPLOAD
             * =======================================================
             */

            if ($request->hasFile('proposal_file')) {

                if (
                    $profile->proposal_file_path &&
                    Storage::disk('public')->exists($profile->proposal_file_path)
                ) {

                    Storage::disk('public')->delete($profile->proposal_file_path);
                }

                $proposalPath = $request->file('proposal_file')
                    ->store('proposal_uploads/user_' . $user->id, 'public');

                $profile->proposal_file_path = $proposalPath;
            }



            /**
             * =======================================================
             * UPDATE DATA PROFILE
             * =======================================================
             */

            $profile->phone_number     = $request->phone_number;
            $profile->institution_name = $request->institution_name;
            $profile->major            = $request->major;
            $profile->address          = $request->address;



            /**
             * SIMPAN DATA
             */

            $profile->save();
        });



        /**
         * ===========================================================
         * REDIRECT SUCCESS
         * ===========================================================
         */

        return redirect()
            ->route('landing.index')
            ->with('success', 'Profil berhasil diperbarui! Sekarang Anda bisa melamar.');
    }
}
