<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ApplicationMagang;
use App\Models\MagangAccessRight;
use Illuminate\Support\Facades\Auth;
use App\Helpers\DashboardCache;

/**
 * =========================================================
 * CONTROLLER: ApplicationVerificationController
 * =========================================================
 * TANGGUNG JAWAB:
 * - Menampilkan daftar pelamar magang
 * - Menampilkan detail berkas pelamar
 * - Memproses verifikasi status (terima / tolak / interview)
 *
 * ROLE & AKSES:
 * - Superadmin  → Semua divisi
 * - Admin Divisi → HANYA divisinya sendiri
 *
 * PRINSIP KEAMANAN:
 * - Setiap method dilindungi oleh pengecekan hak akses
 * - URL manipulation dicegah (403)
 *
 * DAMPAK BISNIS:
 * - Perubahan status berpengaruh langsung ke kuota lowongan
 * - Cache dashboard harus dibersihkan setelah update
 *
 * POTENSI MIGRASI:
 * - Logic hak akses → Middleware / Policy
 * - Logic kuota → Service Layer
 * =========================================================
 */
class ApplicationVerificationController extends Controller
{
    /**
     * =====================================================
     * METHOD: index()
     * =====================================================
     * TUJUAN:
     * - Menampilkan daftar pelamar masuk
     * - Mendukung filter berdasarkan:
     *   - Divisi admin
     *   - Status pelamar (optional)
     *
     * OUTPUT:
     * - Data paginasi lamaran (10 per halaman)
     * =====================================================
     */
    public function index(Request $request)
    {
        /* =====================================================
         * 1. AMBIL DATA USER LOGIN & HAK AKSES
         * ===================================================== */
        $userId = Auth::id();

        $hakAkses = MagangAccessRight::where('user_id', $userId)->first();

        // Safety check: pastikan user memang admin magang
        if (!$hakAkses) {
            abort(403, 'Anda tidak memiliki akses ke halaman verifikasi ini.');
        }

        /* =====================================================
         * 2. QUERY DASAR: AMBIL LAMARAN
         * =====================================================
         * - Eager loading untuk menghindari N+1 query
         * - Urutkan dari pendaftaran terbaru
         * ===================================================== */
        $applications = ApplicationMagang::with(['vacancy', 'leader'])
            ->orderBy('submission_date', 'desc');

        /* =====================================================
         * 3. FILTER BERDASARKAN DIVISI (ADMIN BIDANG)
         * =====================================================
         * LOGIC:
         * - Superadmin → lihat semua
         * - Admin Divisi → hanya lowongan divisinya
         *
         * SECURITY NOTE:
         * - Filter ini mencegah admin lintas divisi
         * ===================================================== */
        if ($hakAkses->role !== 'superadmin') {
            $applications->whereHas('vacancy', function ($q) use ($hakAkses) {
                $q->where('division_name', $hakAkses->division_name);
            });
        }

        /* =====================================================
         * 4. FILTER TAMBAHAN DARI REQUEST (OPTIONAL)
         * =====================================================
         * Contoh:
         * - pending
         * - verified
         * - accepted
         * ===================================================== */
        if ($request->filled('status')) {
            $applications->where('status', $request->status);
        }

        $data = $applications->paginate(10);

        return view('admin.applications.index', compact('data'));
    }

    /**
     * =====================================================
     * METHOD: show()
     * =====================================================
     * TUJUAN:
     * - Menampilkan detail lengkap satu lamaran
     * - Digunakan untuk proses review & verifikasi
     *
     * SECURITY:
     * - Admin hanya boleh membuka lamaran divisinya
     * =====================================================
     */
    public function show($id)
    {
        /* =====================================================
         * 1. AMBIL DATA LAMARAN
         * =====================================================
         * - Memuat anggota kelompok + profil
         * - Memuat data lowongan
         * ===================================================== */
        $application = ApplicationMagang::with([
            'members.user.profile',
            'vacancy'
        ])->findOrFail($id);

        /* =====================================================
         * 2. SECURITY CHECK (ANTI URL MANIPULATION)
         * =====================================================
         * Contoh Kasus:
         * - Admin IT mencoba buka lamaran divisi Keuangan
         * ===================================================== */
        $userId = Auth::id();
        $hakAkses = MagangAccessRight::where('user_id', $userId)->first();

        if ($hakAkses->role !== 'superadmin') {
            if ($application->vacancy->division_name !== $hakAkses->division_name) {
                abort(403, 'Akses Ditolak: Pelamar ini bukan untuk divisi Anda.');
            }
        }

        return view('admin.applications.show', compact('application'));
    }

    /**
     * =====================================================
     * METHOD: updateStatus()
     * =====================================================
     * TUJUAN:
     * - Memproses perubahan status lamaran:
     *   verified | interview | accepted | rejected
     *
     * INI ADALAH JANTUNG LOGIC SISTEM
     * =====================================================
     */
    public function updateStatus(Request $request, $id)
    {
        $app = ApplicationMagang::findOrFail($id);

        /* =====================================================
         * 1. SECURITY CHECK (HAK VERIFIKASI)
         * =====================================================
         * Mencegah admin lintas divisi mengubah status
         * ===================================================== */
        $userId = Auth::id();
        $hakAkses = MagangAccessRight::where('user_id', $userId)->first();

        if ($hakAkses->role !== 'superadmin') {
            if ($app->vacancy->division_name !== $hakAkses->division_name) {
                abort(403, 'Akses Ditolak: Anda tidak berhak memverifikasi pelamar ini.');
            }
        }

        /* =====================================================
         * 2. VALIDASI INPUT ADMIN
         * =====================================================
         * ATURAN BISNIS:
         * - Status wajib valid
         * - Jika reject → alasan WAJIB diisi
         * ===================================================== */
        $request->validate([
            'status' => 'required|in:verified,interview,accepted,rejected',
            'admin_feedback' => 'required_if:status,rejected|nullable|string|max:500',
        ], [
            'admin_feedback.required_if' =>
            'Mohon maaf, jika menolak lamaran, WAJIB menyertakan alasannya.',
        ]);

        /* =====================================================
         * 3. SAFETY NET: CEK KUOTA LOWONGAN
         * =====================================================
         * KASUS KHUSUS:
         * - Lamaran yang sudah rejected dipulihkan ke accepted
         *
         * TUJUAN:
         * - Mencegah kuota minus
         * ===================================================== */
        if (
            $app->status === 'rejected' &&
            in_array($request->status, ['accepted', 'verified'])
        ) {
            $terpakai = ApplicationMagang::where('vacancy_id', $app->vacancy_id)
                ->whereIn('status', ['pending', 'verified', 'interview', 'accepted'])
                ->count();

            $quota = $app->vacancy->quota_slots;

            if (($quota - $terpakai) <= 0) {
                return back()->with(
                    'error',
                    'Gagal memulihkan status! Kuota lowongan ini sudah penuh.'
                );
            }
        }

        /* =====================================================
         * 4. SIMPAN PERUBAHAN STATUS
         * ===================================================== */
        $app->status = $request->status;

        if ($request->filled('admin_feedback')) {
            $app->admin_feedback = $request->admin_feedback;
        }

        $app->save();

        // Bersihkan cache dashboard agar data terbaru tampil
        DashboardCache::clear();

        /* =====================================================
         * 5. PESAN FEEDBACK KE ADMIN
         * ===================================================== */
        $msg = "Status berhasil diubah menjadi " . ucfirst($request->status);

        if ($request->status === 'rejected') {
            $msg .= " (Kuota slot lowongan otomatis bertambah kembali).";
        } elseif ($request->status === 'accepted') {
            $msg .= " (Satu slot kuota permanen terpakai).";
        }

        return redirect()
            ->route('admin.applications.show', $id)
            ->with('success', $msg);
    }
}
