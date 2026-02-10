<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ApplicationMemberMagang; // Target yang dinilai (Perorangan)
use App\Models\AssessmentMagang;        // Model Nilai (Yang Bapak kirim)
use App\Models\MagangAccessRight;       // Model SK Penunjukan (Satpam)
use Illuminate\Support\Facades\Auth;
use App\Helpers\DashboardCache;

class AssessmentController extends Controller
{
    // =================================================================
    // 1. DAFTAR PESERTA YANG BISA DINILAI
    // =================================================================
    public function index(Request $request)
    {
        // 1. Ambil Data Admin yang Login
        $userId = Auth::id();
        $hakAkses = MagangAccessRight::where('user_id', $userId)->first();

        // Safety Check
        if (!$hakAkses) abort(403, 'Anda tidak memiliki hak akses penilaian.');

        // 2. Query Data: Ambil Anggota (Member) yang status lamarannya 'ACCEPTED'
        // Kita relasikan ke user->profile (buat ambil nama) dan application->vacancy (buat cek divisi)
        $query = ApplicationMemberMagang::with(['user.profile', 'application.vacancy', 'assessment'])
            ->whereHas('application', function ($q) {
                $q->where('status', 'accepted'); // Hanya yang sudah diterima magang
            });

        // 3. --- LOGIC FILTER DIVISI (Sesuai SK Penunjukan) ---
        // Kalau Admin IT, cuma boleh lihat anak magang di lowongan IT
        if ($hakAkses->role !== 'superadmin') {
            $query->whereHas('application.vacancy', function ($q) use ($hakAkses) {
                $q->where('division_name', $hakAkses->division_name);
            });
        }
        // ----------------------------------------------------

        // Fitur Pencarian Nama (Opsional tapi berguna)
        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('user.profile', function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%");
            });
        }

        $members = $query->paginate(10);

        return view('admin.assessments.index', compact('members'));
    }

    // =================================================================
    // 2. TAMPILKAN FORM PENILAIAN
    // =================================================================
    public function create($member_id)
    {
        // Cek Hak Akses Dulu (Fungsi ada di bawah)
        $this->checkAccess($member_id);

        // Ambil data member
        $member = ApplicationMemberMagang::with(['user.profile', 'application.vacancy'])->findOrFail($member_id);

        // Cek apakah sudah pernah dinilai? (Untuk menampilkan data lama jika diedit)
        $existingAssessment = AssessmentMagang::where('member_id', $member_id)->first();

        return view('admin.assessments.create', compact('member', 'existingAssessment'));
    }

    // =================================================================
    // 3. PROSES SIMPAN NILAI (STORE)
    // =================================================================
    public function store(Request $request, $member_id)
    {
        // 1. Cek Hak Akses Lagi (Penting biar tidak ditembak lewat Postman/API)
        $this->checkAccess($member_id);

        // 2. Validasi Input Sesuai Kolom Database Bapak
        $request->validate([
            'score_behavior'   => 'required|numeric|min:0|max:100',
            'score_discipline' => 'required|numeric|min:0|max:100',
            'score_performance' => 'required|numeric|min:0|max:100',
            'evaluation_notes' => 'nullable|string',
            'additional_notes' => 'nullable|string',
        ]);

        // 3. Hitung Final Score (Rata-rata)
        // Rumus: (Perilaku + Disiplin + Kinerja) / 3
        $finalScore = ($request->score_behavior + $request->score_discipline + $request->score_performance) / 3;

        // 4. Ambil Nama Penilai Otomatis
        // Mengambil nama dari tabel Users Pegawai yang sedang login
        $namaPenilai = Auth::user()->name ?? 'Admin Magang';

        // 5. Simpan ke Database (Pakai updateOrCreate)
        // Jika member_id ini sudah punya nilai, maka UPDATE. Jika belum, maka CREATE baru.
        AssessmentMagang::updateOrCreate(
            ['member_id' => $member_id], // Kunci pencarian
            [
                'assessor_name'     => $namaPenilai,
                'score_behavior'    => $request->score_behavior,
                'score_discipline'  => $request->score_discipline,
                'score_performance' => $request->score_performance,
                'final_score'       => $finalScore, // Hasil hitungan tadi
                'evaluation_notes'  => $request->evaluation_notes,
                'additional_notes'  => $request->additional_notes,
            ]
        );
        DashboardCache::clear();

        return redirect()->route('admin.assessments.index')
            ->with('success', 'Nilai berhasil disimpan. Skor Akhir: ' . number_format($finalScore, 2));
    }

    // =================================================================
    // FUNGSI BANTUAN: CEK PRIVASI DIVISI
    // =================================================================
    private function checkAccess($memberId)
    {
        $userId = Auth::id();
        $hakAkses = MagangAccessRight::where('user_id', $userId)->first();

        // Ambil data member yang mau dinilai
        $targetMember = ApplicationMemberMagang::with('application.vacancy')->findOrFail($memberId);

        // Kalau Superadmin -> Bebas akses kemana aja
        if ($hakAkses->role === 'superadmin') return;

        // Kalau Admin Bidang -> Cek apakah Divisinya SAMA?
        if ($hakAkses->division_name !== $targetMember->application->vacancy->division_name) {
            abort(403, 'AKSES DITOLAK: Anda tidak berhak menilai peserta dari divisi lain.');
        }
    }
}
