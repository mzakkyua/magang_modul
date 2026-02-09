<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class AdminProfileController extends Controller
{
    // 1. TAMPILKAN HALAMAN PROFIL
    public function index()
    {
        $user = Auth::user();
        return view('admin.profile', compact('user'));
    }

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
        if ($request->hasFile('photo')) {
            // 1. Hapus foto lama jika ada (Biar server gak penuh sampah)
            if ($user->profile_photo_path && Storage::disk('public')->exists($user->profile_photo_path)) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }

            // 2. Simpan foto baru ke folder 'profile-photos' di storage public
            // Hasilnya misal: 'profile-photos/acak123.jpg'
            $path = $request->file('photo')->store('profile-photos', 'public');

            // 3. Update path di database user
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