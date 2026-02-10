<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ApplicationMagang;
use App\Models\MagangAccessRight; // <--- PENTING: Panggil Model Hak Akses
use Illuminate\Support\Facades\Auth;
use App\Helpers\DashboardCache;

class ApplicationVerificationController extends Controller
{
    // =================================================================
    // 1. MELIHAT DAFTAR PELAMAR MASUK
    // =================================================================
    public function index(Request $request)
    {
        // 1. Ambil ID User Login
        $userId = Auth::id();

        // 2. Cek SK Hak Akses
        $hakAkses = MagangAccessRight::where('user_id', $userId)->first();

        // 3. Safety Check
        if (!$hakAkses) {
            abort(403, 'Anda tidak memiliki akses ke halaman verifikasi ini.');
        }

        // 4. Mulai Query: Ambil Pelamar + Data Lowongannya + Data Ketua
        $applications = ApplicationMagang::with(['vacancy', 'leader'])
            ->orderBy('submission_date', 'desc');

        // 5. --- LOGIC FILTER (ADMIN BIDANG) ---
        // Kalau BUKAN Superadmin, filter berdasarkan divisi di SK-nya
        if ($hakAkses->role !== 'superadmin') {

            // Logic: "Ambil lamaran yang Lowongannya memiliki nama divisi yang sama dengan saya"
            $applications->whereHas('vacancy', function ($q) use ($hakAkses) {
                $q->where('division_name', $hakAkses->division_name);
            });
        }
        // -----------------------------------

        // Filter tambahan dari Request (Misal Admin pilih dropdown Status)
        if ($request->has('status') && $request->status != '') {
            $applications->where('status', $request->status);
        }

        $data = $applications->paginate(10);

        return view('admin.applications.index', compact('data'));
    }

    // =================================================================
    // 2. MELIHAT DETAIL BERKAS (Satu Map Lamaran)
    // =================================================================
    public function show($id)
    {
        // Ambil Data
        $application = ApplicationMagang::with(['members.user.profile', 'vacancy'])
            ->findOrFail($id);

        // --- SECURITY CHECK (PENTING!) ---
        // Mencegah Admin IT mengintip URL detail pelamar Keuangan
        $userId = Auth::id();
        $hakAkses = MagangAccessRight::where('user_id', $userId)->first();

        if ($hakAkses->role !== 'superadmin') {
            // Cek apakah divisi pelamar SAMA dengan divisi Admin?
            if ($application->vacancy->division_name !== $hakAkses->division_name) {
                abort(403, 'Akses Ditolak: Pelamar ini bukan untuk divisi Anda.');
            }
        }
        // ----------------------------------

        return view('admin.applications.show', compact('application'));
    }

    // =================================================================
    // 3. PROSES VERIFIKASI (TERIMA / TOLAK) - JANTUNG LOGIC
    // =================================================================
    public function updateStatus(Request $request, $id)
    {
        $app = ApplicationMagang::findOrFail($id);

        // --- SECURITY CHECK (PENTING!) ---
        // Mencegah Admin IT menolak/menerima pelamar Keuangan
        $userId = Auth::id();
        $hakAkses = MagangAccessRight::where('user_id', $userId)->first();

        if ($hakAkses->role !== 'superadmin') {
            if ($app->vacancy->division_name !== $hakAkses->division_name) {
                abort(403, 'Akses Ditolak: Anda tidak berhak memverifikasi pelamar ini.');
            }
        }
        // ----------------------------------

        // 1. Validasi Input Admin
        $request->validate([
            'status' => 'required|in:verified,interview,accepted,rejected',
            // Wajib isi alasan kalau Reject
            'admin_feedback' => 'required_if:status,rejected|nullable|string|max:500',
        ], [
            'admin_feedback.required_if' => 'Mohon maaf, jika menolak lamaran, WAJIB menyertakan alasannya.',
        ]);

        // 2. Logic Cek Kuota (Safety Net)
        // Kalau Admin mengubah dari REJECTED -> ACCEPTED, cek kuota dulu
        if ($app->status == 'rejected' && in_array($request->status, ['accepted', 'verified'])) {

            $terpakai = ApplicationMagang::where('vacancy_id', $app->vacancy_id)
                ->whereIn('status', ['pending', 'verified', 'interview', 'accepted'])
                ->count();

            $quota = $app->vacancy->quota_slots;

            if (($quota - $terpakai) <= 0) {
                return back()->with('error', 'Gagal memulihkan status! Kuota lowongan ini sudah penuh.');
            }
        }

        // 3. Simpan Perubahan
        $app->status = $request->status;

        if ($request->filled('admin_feedback')) {
            $app->admin_feedback = $request->admin_feedback;
        }

        $app->save();
        DashboardCache::clear();

        // Pesan Sukses
        $msg = "Status berhasil diubah menjadi " . ucfirst($request->status);
        if ($request->status == 'rejected') {
            $msg .= " (Kuota slot lowongan otomatis bertambah kembali).";
        } elseif ($request->status == 'accepted') {
            $msg .= " (Satu slot kuota permanen terpakai).";
        }

        return redirect()->route('admin.applications.show', $id)->with('success', $msg);
    }
}
