<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ApplicationMagang;
use App\Models\VacancyMagang;

class ApplicationVerificationController extends Controller
{
    // =================================================================
    // 1. MELIHAT DAFTAR PELAMAR MASUK
    // =================================================================
    public function index(Request $request)
    {
        // Query Dasar: Ambil semua aplikasi pelamar
        // with('vacancy', 'leader') -> Eager Loading (Biar hemat query SQL)
        // Kita ambil data lowongannya & data ketua kelompoknya sekalian
        $applications = ApplicationMagang::with(['vacancy', 'leader'])
            ->orderBy('submission_date', 'desc'); // Yang baru daftar ada di atas

        // -------------------------------------------------------------
        // FITUR FILTER (Opsional tapi Penting)
        // Admin bisa filter berdasarkan Status atau Lowongan
        // -------------------------------------------------------------
        if ($request->has('status')) {
            $applications->where('status', $request->status);
        }
        
        if ($request->has('vacancy_id')) {
            $applications->where('vacancy_id', $request->vacancy_id);
        }

        // Tampilkan 10 data per halaman
        $data = $applications->paginate(10);

        return view('admin.applications.index', compact('data'));
    }

    // =================================================================
    // 2. MELIHAT DETAIL BERKAS (Satu Map Lamaran)
    // =================================================================
    public function show($id)
    {
        // Ambil data lamaran beserta semua anggotanya & profil mereka
        // Nested Eager Loading: members.user.profile (Ambil Anggota -> User -> Profilnya)
        $application = ApplicationMagang::with(['members.user.profile', 'vacancy'])
            ->findOrFail($id);

        return view('admin.applications.show', compact('application'));
    }

    // =================================================================
    // 3. PROSES VERIFIKASI (TERIMA / TOLAK) - JANTUNG LOGIC
    // =================================================================
    public function updateStatus(Request $request, $id)
    {
        // Cari data lamaran
        $app = ApplicationMagang::findOrFail($id);

        // 1. Validasi Input Admin
        $request->validate([
            // Status hanya boleh diganti ke yang valid
            'status' => 'required|in:verified,interview,accepted,rejected',
            
            // LOGIC KHUSUS: Jika DITOLAK, WAJIB ISI ALASAN!
            // required_if:status,rejected -> Kalau status == rejected, feedback wajib diisi
            'admin_feedback' => 'required_if:status,rejected|nullable|string|max:500',
        ], [
            // Pesan Error Custom biar Admin "Peka"
            'admin_feedback.required_if' => 'Mohon maaf Pak/Bu, jika menolak lamaran, WAJIB menyertakan alasannya agar mahasiswa bisa belajar.',
        ]);

        // 2. Logic Cek Kuota (Hanya Jaga-jaga)
        // Kalau Admin mau Mengubah dari REJECTED kembali ke ACCEPTED,
        // Kita harus cek dulu: "Jangan-jangan kuotanya udah diambil orang lain?"
        if ($app->status == 'rejected' && in_array($request->status, ['accepted', 'verified'])) {
            
            // Hitung sisa kuota lowongan ini sekarang
            $terpakai = ApplicationMagang::where('vacancy_id', $app->vacancy_id)
                        ->whereIn('status', ['pending', 'verified', 'interview', 'accepted'])
                        ->count();
            
            // Ambil data lowongan buat tau total slot
            $quota = $app->vacancy->quota_slots;

            if (($quota - $terpakai) <= 0) {
                return back()->with('error', 'Gagal memulihkan status! Kuota lowongan ini sudah penuh terisi pelamar lain.');
            }
        }

        // 3. Simpan Perubahan Status
        $app->status = $request->status;
        
        // Simpan feedback (Hanya jika ada inputan, kalau kosong biarkan null)
        if ($request->filled('admin_feedback')) {
            $app->admin_feedback = $request->admin_feedback;
        }

        $app->save(); // Update database

        // Feedback ke Admin
        $msg = "Status berhasil diubah menjadi " . ucfirst($request->status);
        if ($request->status == 'rejected') {
            $msg .= " (Kuota slot lowongan otomatis bertambah kembali).";
        } elseif ($request->status == 'accepted') {
            $msg .= " (Satu slot kuota permanen terpakai).";
        }

        return redirect()->route('admin.applications.show', $id)->with('success', $msg);
    }
}