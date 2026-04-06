<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ApplicationMagang;
use App\Models\MagangAccessRight;
use Illuminate\Support\Facades\Auth;
use App\Helpers\DashboardCache;

/**
 * ======================================================================
 * CONTROLLER: ApplicationVerificationController
 * ======================================================================
 *
 * TUJUAN CONTROLLER
 * ----------------------------------------------------------------------
 * Controller ini menangani seluruh proses verifikasi pelamar magang
 * oleh Admin.
 *
 * Fitur utama controller ini:
 *
 * 1. Menampilkan daftar pelamar
 * 2. Menampilkan detail lamaran
 * 3. Memproses perubahan status pelamar
 *
 * ----------------------------------------------------------------------
 * SISTEM ROLE ADMIN
 * ----------------------------------------------------------------------
 * Sistem mendukung dua jenis admin:
 *
 * 1. Superadmin
 *    - Dapat melihat dan memverifikasi semua pelamar dari seluruh divisi
 *
 * 2. Admin Divisi
 *    - Hanya dapat melihat dan memverifikasi pelamar dari divisinya
 *
 * ----------------------------------------------------------------------
 * KEAMANAN SISTEM
 * ----------------------------------------------------------------------
 * Controller ini memiliki beberapa lapisan keamanan:
 *
 * - Validasi hak akses admin
 * - Filter divisi pada query
 * - Validasi divisi pada detail dan update
 *
 * Hal ini mencegah manipulasi URL seperti:
 *
 * /admin/applications/10
 *
 * yang mencoba membuka lamaran dari divisi lain.
 *
 * ----------------------------------------------------------------------
 * DAMPAK BISNIS
 * ----------------------------------------------------------------------
 * Perubahan status pelamar mempengaruhi:
 *
 * - Kuota lowongan
 * - Statistik dashboard
 *
 * Oleh karena itu setiap perubahan status akan
 * membersihkan cache dashboard.
 *
 * ======================================================================
 */
class ApplicationVerificationController extends Controller
{

    /**
     * ==================================================================
     * METHOD: index()
     * ==================================================================
     *
     * TUJUAN:
     * Menampilkan daftar pelamar magang.
     *
     * FITUR:
     * - Filter otomatis berdasarkan divisi admin
     * - Filter status pelamar (optional)
     * - Pagination (10 data per halaman)
     *
     * PERUBAHAN:
     * - Ditambahkan $statusCounts yang di-scope ke divisi admin.
     *   Sebelumnya dihitung di Blade (@php block) tanpa filter divisi,
     *   sehingga semua admin dari semua divisi melihat angka yang sama.
     *   Sekarang pola-nya sama dengan VacancyMagangController@index:
     *   gunakan base query → clone → hitung per status.
     *
     * ==================================================================
     */
    public function index(Request $request)
    {

        /*
        ==============================================================
        1. AMBIL USER LOGIN & HAK AKSES ADMIN
        ==============================================================
        */
        $userId = Auth::id();

        $hakAkses = MagangAccessRight::where('user_id', $userId)->first();

        if (!$hakAkses) {
            abort(403, 'Anda tidak memiliki akses ke halaman verifikasi ini.');
        }

        /*
        ==============================================================
        2. BASE QUERY DENGAN FILTER DIVISI
        ==============================================================
        Base query ini di-reuse untuk $statusCounts dan $data agar
        filter divisi selalu konsisten di keduanya.

        Clone dipakai agar setiap query berjalan independen
        tanpa kondisi sebelumnya ikut terbawa.
        */
        $base = ApplicationMagang::whereHas('vacancy', function ($q) use ($hakAkses) {
            // BUSINESS LOGIC: superadmin lihat semua divisi, admin divisi hanya divisinya
            if ($hakAkses->role !== 'superadmin') {
                $q->where('division_name', $hakAkses->division_name);
            }
        });

        /*
        ==============================================================
        3. HITUNG STATUS COUNTS (SCOPE SAMA DENGAN FILTER DIVISI)
        ==============================================================
        Variabel ini dikirim ke Blade untuk stats cards dan tab badges.
        Menggantikan @php block di Blade yang query tanpa filter divisi.
        */
        $statusCounts = [
            'pending'   => (clone $base)->where('status', 'pending')->count(),
            'verified'  => (clone $base)->where('status', 'verified')->count(),
            'interview' => (clone $base)->where('status', 'interview')->count(),
            'accepted'  => (clone $base)->where('status', 'accepted')->count(),
            'rejected'  => (clone $base)->where('status', 'rejected')->count(),
            'resigned'  => (clone $base)->where('status', 'resigned')->count(),
            'completed' => (clone $base)->where('status', 'completed')->count(),
        ];

        /*
        ==============================================================
        4. QUERY DATA UNTUK TABEL (DENGAN FILTER STATUS OPSIONAL)
        ==============================================================
        Eager loading digunakan untuk menghindari N+1 query.
        */
        $query = (clone $base)
            ->with(['vacancy', 'leader'])
            ->orderBy('submission_date', 'desc');

        // STEP: Filter status (opsional, dari query param ?status=...)
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $data = $query->paginate(10);

        return view('admin.applications.index', compact('data', 'statusCounts'));
    }



    /**
     * ==================================================================
     * METHOD: show()
     * ==================================================================
     *
     * TUJUAN:
     * Menampilkan detail lengkap lamaran.
     *
     * DATA YANG DIMUAT:
     * - anggota kelompok
     * - profil anggota
     * - data lowongan
     *
     * ==================================================================
     */
    public function show($id)
    {

        /*
        ==============================================================
        1. AMBIL DATA LAMARAN
        ==============================================================
        */
        $application = ApplicationMagang::with([
            'members.user.profile',
            'vacancy'
        ])->findOrFail($id);


        /*
        ==============================================================
        2. VALIDASI AKSES DIVISI
        ==============================================================
        */
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
     * ==================================================================
     * METHOD: updateStatus()
     * ==================================================================
     *
     * TUJUAN:
     * Memproses perubahan status lamaran oleh admin.
     *
     * STATUS YANG DIDUKUNG:
     *
     * - verified
     * - interview
     * - accepted
     * - rejected
     *
     * ==================================================================
     */
    public function updateStatus(Request $request, $id)
    {
        /*
    ==============================================================
    1. AMBIL DATA APLIKASI + RELASI VACANCY
    ==============================================================
    Eager loading digunakan agar data vacancy langsung dimuat
    bersama application sehingga menghindari lazy loading query
    tambahan saat mengakses:

    $app->vacancy->division_name
    $app->vacancy->quota_slots
    */
        $app = ApplicationMagang::with('vacancy')->findOrFail($id);

        /*

        /*
        ==============================================================
        MENCEGAH DOUBLE SUBMIT / STATUS GANDA
        ==============================================================
        */
        if ($app->status === $request->status) {
            return back()->with('error', 'Lamaran ini sudah berstatus ' . strtoupper($request->status) . '!');
        }
        /*
        
    ==============================================================
    2. VALIDASI HAK AKSES ADMIN
    ==============================================================
    Mencegah admin lintas divisi mengubah status pelamar
    */
        $userId = Auth::id();
        $hakAkses = MagangAccessRight::where('user_id', $userId)->first();

        if ($hakAkses->role !== 'superadmin') {
            if ($app->vacancy->division_name !== $hakAkses->division_name) {
                abort(403, 'Akses Ditolak: Anda tidak berhak memverifikasi pelamar ini.');
            }
        }

        /*
    ==============================================================
    3. VALIDASI INPUT STATUS
    ==============================================================
    */
        $request->validate([
            'status' => 'required|in:verified,interview,accepted,rejected,resigned,completed',
            'admin_feedback' => 'required_if:status,rejected,resigned|nullable|string|max:500',
        ], [
            'admin_feedback.required_if' =>
            'Mohon maaf, jika menolak atau menandai mundur, WAJIB menyertakan alasannya.',
        ]);

        /*
    ==============================================================
    4. SAFETY NET: CEK KUOTA LOWONGAN
    ==============================================================
    Digunakan saat lamaran yang sebelumnya rejected
    dipulihkan kembali menjadi accepted atau verified
    */
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

        /*
    ==============================================================
    5. SIMPAN PERUBAHAN STATUS & PEMBERSIHAN CATATAN
    ==============================================================
    */
        $app->status = $request->status;

        // Jika ditolak ATAU mengundurkan diri, simpan alasannya. Jika tidak, KOSONGKAN.
        if (in_array($request->status, ['rejected', 'resigned'])) {
            $app->admin_feedback = $request->admin_feedback;
        } else {
            $app->admin_feedback = null;
        }

        $app->save();

        /*
    ==============================================================
    5.5. AUTO UPDATE STATUS LOWONGAN BERDASARKAN KUOTA
    ==============================================================
    Memanggil fungsi pintar di model VacancyMagang untuk mengecek
    apakah lowongan harus ditutup (karena penuh) atau dibuka kembali
    (karena ada pelamar yang ditolak).
    */
        $app->vacancy->updateStatusBasedOnQuota();

        /*
    ==============================================================
    6. BERSIHKAN CACHE DASHBOARD
    ==============================================================
    Agar statistik dashboard langsung diperbarui
    */
        DashboardCache::clear();

        /*
    ==============================================================
    7. FEEDBACK PESAN KE ADMIN
    ==============================================================
    */
        $msg = "Status berhasil diubah menjadi " . ucfirst($request->status);

        if ($request->status === 'rejected') {
            $msg .= " (Kuota lowongan otomatis bertambah kembali).";
        } elseif ($request->status === 'accepted') {
            $msg .= " (Satu slot kuota permanen terpakai).";
        }

        return redirect()
            ->route('admin.applications.show', $id)
            ->with('success', $msg);
    }
}
