<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\UserMagang;
use App\Models\Certificate;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Models\MagangAccessRight;

class CertificateController extends Controller
{
    // ================= ADMIN =================

    public function create()
    {
        // 1. Ambil data admin yang sedang login
        $adminId = Auth::id();
        $hakAkses = MagangAccessRight::where('user_id', $adminId)->first();

        // 2. Query dasar: Cari user yang lamarannya 'accepted'
        $usersQuery = UserMagang::whereHas('applicationMembers.application', function ($query) {
            $query->whereIn('status', ['accepted', 'completed']);
        });

        // 3. FILTER DIVISI: Jika bukan superadmin, filter berdasarkan divisinya
        if ($hakAkses && $hakAkses->role !== 'superadmin') {
            // Kita tembus relasi: User -> Member -> Application -> Vacancy -> division_name
            $usersQuery->whereHas('applicationMembers.application.vacancy', function ($q) use ($hakAkses) {
                $q->where('division_name', $hakAkses->division_name);
            });
        }

        // 4. Eksekusi query
        $users = $usersQuery->get();

        return view('admin.certificate.create', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users_magang,id', // Tambahan: Pastikan user valid
            'title' => 'required|string|max:255',
            'file' => 'required|mimes:pdf,jpg,jpeg,png|max:2048'
        ]);

        $filePath = $request->file('file')->store('certificates', 'public');

        Certificate::create([
            'user_id' => $request->user_id,
            'title' => $request->title,
            'file' => $filePath
        ]);

        return back()->with('success', 'Sertifikat berhasil diupload');
    }

    // ================= USER (PEMAGANG) =================

    public function index()
    {
        // Menampilkan daftar sertifikat milik user yang sedang login
        $certificates = Certificate::where('user_id', Auth::id())->latest()->get();
        return view('magang.sertifikat.index', compact('certificates'));
    }

    public function download($id)
    {
        $cert = Certificate::findOrFail($id);

        // Keamanan: Cek apakah user yang login berhak mendownload sertifikat ini
        // (Bisa dilewati jika yang mendownload adalah admin, disesuaikan dengan role sistemmu)
        if ($cert->user_id != Auth::id()) {
            abort(403, 'Akses ditolak: Ini bukan sertifikat Anda.');
        }

        // PERBAIKAN 2: Mengatasi Error Intelephense & Cek Ketersediaan File
        $filePath = storage_path('app/public/' . $cert->file);

        // Jika file fisiknya hilang/terhapus, jangan sampai sistem crash (Error 500)
        if (!file_exists($filePath)) {
            abort(404, 'Mohon maaf, file sertifikat fisik tidak ditemukan di server.');
        }

        // Ini cara yang ramah IDE (Intelephense tidak akan komplain)
        return response()->download($filePath);
    }

    public function view($id)
    {
        $cert = Certificate::findOrFail($id);

        // Keamanan: Cek apakah user yang login berhak melihat sertifikat ini
        if ($cert->user_id != Auth::id()) {
            abort(403, 'Akses ditolak: Ini bukan sertifikat Anda.');
        }

        $filePath = storage_path('app/public/' . $cert->file);

        if (!file_exists($filePath)) {
            abort(404, 'Mohon maaf, file sertifikat fisik tidak ditemukan di server.');
        }

        // PERBEDAANNYA DI SINI:
        // Gunakan response()->file() agar file dibuka di browser (untuk iframe preview)
        // Kalau response()->download(), file akan langsung terunduh.
        return response()->file($filePath);
    }
}
