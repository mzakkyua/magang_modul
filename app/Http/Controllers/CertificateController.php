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
        $adminId  = Auth::id();
        $hakAkses = MagangAccessRight::where('user_id', $adminId)->first();

        // 2. Query dasar: Cari user yang lamarannya 'accepted' atau 'completed'
        $usersQuery = UserMagang::whereHas('applicationMembers.application', function ($query) {
            $query->whereIn('status', ['accepted', 'completed']);
        })
            /*
        ==============================================================
        PERUBAHAN: FILTER USER YANG SUDAH ADA PENILAIANNYA
        ==============================================================
        Sebelumnya semua peserta accepted/completed muncul di dropdown.
        Sekarang hanya peserta yang sudah diisi nilainya (ada record
        di tabel assessments_magang melalui relasi applicationMembers)
        yang akan tampil sebagai opsi penerima sertifikat.

        Alasannya: sertifikat sebaiknya baru diberikan setelah
        penilaian selesai diinput, bukan sebelumnya.
        ==============================================================
        */
            ->whereHas('applicationMembers.assessment');

        // 3. FILTER DIVISI: Jika bukan superadmin, filter berdasarkan divisinya
        if ($hakAkses && $hakAkses->role !== 'superadmin') {
            // Kita tembus relasi: User -> Member -> Application -> Vacancy -> division_name
            $usersQuery->whereHas('applicationMembers.application.vacancy', function ($q) use ($hakAkses) {
                $q->where('division_name', $hakAkses->division_name);
            });
        }

        // 4. Eksekusi query — sertakan profile agar full_name bisa diakses tanpa N+1
        $users = $usersQuery->with('profile')->get();

        return view('admin.certificate.create', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users_magang,id',
            'title'   => 'required|string|max:255',
            'file'    => 'required|mimes:pdf,jpg,jpeg,png|max:2048'
        ]);

        $filePath = $request->file('file')->store('certificates', 'public');

        Certificate::create([
            'user_id' => $request->user_id,
            'title'   => $request->title,
            'file'    => $filePath
        ]);

        return back()->with('success', 'Sertifikat berhasil diupload');
    }

    // ================= USER (PEMAGANG) =================

    public function index()
    {
        $certificates = Certificate::where('user_id', Auth::id())->latest()->get();
        return view('magang.sertifikat.index', compact('certificates'));
    }

    public function download($id)
    {
        $cert = Certificate::findOrFail($id);

        if ($cert->user_id != Auth::id()) {
            abort(403, 'Akses ditolak: Ini bukan sertifikat Anda.');
        }

        $filePath = storage_path('app/public/' . $cert->file);

        if (!file_exists($filePath)) {
            abort(404, 'Mohon maaf, file sertifikat fisik tidak ditemukan di server.');
        }

        return response()->download($filePath);
    }

    public function view($id)
    {
        $cert = Certificate::findOrFail($id);

        if ($cert->user_id != Auth::id()) {
            abort(403, 'Akses ditolak: Ini bukan sertifikat Anda.');
        }

        $filePath = storage_path('app/public/' . $cert->file);

        if (!file_exists($filePath)) {
            abort(404, 'Mohon maaf, file sertifikat fisik tidak ditemukan di server.');
        }

        return response()->file($filePath);
    }
}
