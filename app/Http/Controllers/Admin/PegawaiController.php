<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\MagangAccessRight;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PegawaiController extends Controller
{
    // Menampilkan Tabel Seluruh Pegawai
    public function index()
    {
        // Keamanan Ekstra: Hanya Superadmin yang boleh buka halaman ini!
        $hakAkses = MagangAccessRight::where('user_id', Auth::id())->first();
        if (!$hakAkses || $hakAkses->role !== 'superadmin') {
            abort(403, 'Akses Ditolak: Hanya Superadmin yang dapat mengelola Data Pegawai.');
        }

        // Ambil data user, panggil sekalian relasi hak aksesnya, urutkan berdasarkan nama
        $pegawai = User::with('magangAccess')->orderBy('name', 'asc')->paginate(15);

        return view('admin.pegawai.index', compact('pegawai'));
    }

    // Memberikan ATAU Mengubah Hak Akses (Create & Update jadi satu)
    public function storeAccess(Request $request, $userId)
    {
        $request->validate([
            'role' => 'required|in:superadmin,admin',
            'division_name' => 'required_if:role,admin|nullable|string|max:100'
        ]);

        // FITUR PINTAR LARAVEL: updateOrCreate
        // Jika pegawai belum punya akses -> Dibuatkan (Create)
        // Jika sudah punya akses tapi divisinya ganti -> Diperbarui (Update)
        MagangAccessRight::updateOrCreate(
            ['user_id' => $userId],
            [
                'role' => $request->role,
                // Jika superadmin, divisi dikosongkan. Jika admin, isi dengan divisinya.
                'division_name' => $request->role === 'superadmin' ? null : $request->division_name
            ]
        );

        return back()->with('success', 'Hak akses pegawai berhasil diperbarui!');
    }

    // Mencabut Hak Akses (Delete)
    public function destroyAccess($userId)
    {
        // Mencegah Superadmin menghapus dirinya sendiri
        if ($userId == Auth::id()) {
            return back()->with('error', 'Anda tidak dapat mencabut hak akses Anda sendiri!');
        }

        MagangAccessRight::where('user_id', $userId)->delete();

        return back()->with('success', 'Hak akses pegawai berhasil dicabut!');
    }
}
