<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\ProfileMagang;

class ProfileMagangController extends Controller
{
    // 1. Tampilkan Form Edit Profil
    public function edit()
    {
        // Ambil user yang sedang login
        $user = Auth::guard('magang')->user();
        
        // Ambil data profilnya
        $profile = $user->profile ?? new ProfileMagang(['user_id' => $user->id]);

        return view('magang.profile.edit', compact('profile', 'user'));
    }

    // 2. Simpan Perubahan (Termasuk Upload CV)
    public function update(Request $request)
    {
        $user = Auth::guard('magang')->user();
        $profile = $user->profile ?? new ProfileMagang(['user_id' => $user->id]);

        // A. Validasi Input
        $request->validate([
            'phone_number' => 'required|string|max:20',
            'institution_name' => 'required|string|max:100', // Wajib diisi sekarang
            'nim_nisn' => 'required|string|max:20',
            'major' => 'required|string|max:100',
            'address' => 'required|string',
            
            // Validasi File Upload
            // mimes:pdf = Wajib PDF
            // max:2048 = Maksimal 2MB
            'cv_file' => 'nullable|file|mimes:pdf|max:2048', 
            'proposal_file' => 'nullable|file|mimes:pdf|max:5120', // Proposal max 5MB
        ]);

        // B. Logic Upload File CV
        if ($request->hasFile('cv_file')) {
            // 1. Hapus file lama jika ada (biar server gak penuh sampah)
            if ($profile->cv_file_path && Storage::disk('public')->exists($profile->cv_file_path)) {
                Storage::disk('public')->delete($profile->cv_file_path);
            }

            // 2. Simpan file baru
            // Akan tersimpan di folder: storage/app/public/cv_uploads
            $cvPath = $request->file('cv_file')->store('cv_uploads', 'public');
            
            // 3. Update path di database
            $profile->cv_file_path = $cvPath;
        }

        // C. Logic Upload File Proposal (Sama seperti CV)
        if ($request->hasFile('proposal_file')) {
            if ($profile->proposal_file_path && Storage::disk('public')->exists($profile->proposal_file_path)) {
                Storage::disk('public')->delete($profile->proposal_file_path);
            }

            $proposalPath = $request->file('proposal_file')->store('proposal_uploads', 'public');
            $profile->proposal_file_path = $proposalPath;
        }

        // D. Simpan Data Teks Lainnya
        $profile->phone_number = $request->phone_number;
        $profile->nim_nisn = $request->nim_nisn;
        $profile->institution_name = $request->institution_name;
        $profile->major = $request->major;
        $profile->address = $request->address;
        
        $profile->save(); // Simpan ke database

        return redirect()->route('landing.index')->with('success', 'Profil berhasil diperbarui! Sekarang Anda bisa melamar.');
    }
}
