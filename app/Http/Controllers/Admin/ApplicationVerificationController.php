<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ApplicationMagang;
use App\Models\MagangAccessRight;
use App\Models\AssessmentMagang;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Helpers\DashboardCache;

/**
 * ======================================================================
 * CONTROLLER: ApplicationVerificationController
 * ======================================================================
 */
class ApplicationVerificationController extends Controller
{

    // ======================================================================
    // KONFIGURASI STATE TRANSITION
    // ======================================================================
    //
    // Mendefinisikan status apa saja yang diizinkan dari status sebelumnya.
    // Ini mencegah admin salah klik (misal: dari rejected langsung ke completed)
    // atau manipulasi alur bisnis yang tidak masuk akal.
    //
    // ALUR NORMAL:
    //   pending → verified → interview → accepted → completed
    //                                              → resigned
    //             (semua bisa ditolak)  → rejected
    //   rejected → (boleh dipulihkan ke pending oleh superadmin)
    //
    // ======================================================================
    private const STATE_TRANSITIONS = [
        'pending'   => ['verified', 'rejected'],
        'verified'  => ['interview', 'rejected'],
        'interview' => ['accepted', 'rejected'],
        'accepted'  => ['completed', 'resigned'],
        'rejected'  => ['pending'],   // Pemulihan: hanya ke pending, bukan langsung accepted
        'resigned'  => [],            // Terminal state
        'completed' => [],            // Terminal state
    ];


    // ======================================================================
    // HELPER PRIVATE: resolveHakAkses()
    // ======================================================================
    //
    // TUJUAN:
    // Memusatkan logika validasi hak akses admin agar tidak duplikat
    // di setiap method (index, show, updateStatus).
    //
    // SEBELUMNYA:
    // Logika ini ditulis ulang secara manual di 3 method berbeda,
    // sehingga rawan inkonsistensi saat ada perubahan.
    //
    // SEKARANG:
    // Satu method, satu titik perubahan.
    //
    // ======================================================================
    private function resolveHakAkses(): MagangAccessRight
    {
        $hakAkses = request()->attributes->get('magang_access');

        if (!$hakAkses) {
            abort(403);
        }

        return $hakAkses;
    }


    // ======================================================================
    // HELPER PRIVATE: buildBaseQuery()
    // ======================================================================
    //
    // TUJUAN:
    // Membangun base query yang sudah di-scope ke divisi admin.
    // Di-reuse untuk statusCounts dan data tabel agar filter divisi konsisten.
    //
    // ======================================================================
    private function buildBaseQuery(MagangAccessRight $hakAkses)
    {
        return ApplicationMagang::whereHas('vacancy', function ($q) use ($hakAkses) {
            if (!$hakAkses->isSuperAdmin()) {  // ← Tambah tanda !
                $q->where('division_name', $hakAkses->division_name);
            }
            // Jika superadmin: tidak ada filter → tampilkan semua divisi
        });
    }


    // ======================================================================
    // HELPER PRIVATE: assertDivisiAccess()
    // ======================================================================
    //
    // TUJUAN:
    // Validasi bahwa admin tidak mengakses lamaran dari divisi lain.
    // Digunakan di show() dan updateStatus() untuk mencegah manipulasi URL.
    //
    // ======================================================================
    private function assertDivisiAccess(ApplicationMagang $application, MagangAccessRight $hakAkses): void
    {
        if (!$hakAkses->isSuperAdmin()) {  // ← Tambah tanda !
            if ($application->vacancy->division_name !== $hakAkses->division_name) {
                abort(403, 'Akses Ditolak: Pelamar ini bukan untuk divisi Anda.');
            }
        }
        // Superadmin: tidak ada pembatasan divisi
    }


    // ======================================================================
    // HELPER PRIVATE: isStateTransitionAllowed()
    // ======================================================================
    //
    // TUJUAN:
    // Memeriksa apakah perubahan dari status lama ke status baru diizinkan
    // berdasarkan STATE_TRANSITIONS yang sudah didefinisikan.
    //
    // ======================================================================
    private function isStateTransitionAllowed(string $fromStatus, string $toStatus): bool
    {
        return in_array($toStatus, self::STATE_TRANSITIONS[$fromStatus] ?? []);
    }


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
     * - Filter pencarian nama / email pelamar
     * - Pagination dengan withQueryString()
     *
     * IMPROVEMENT:
     * - statusCounts kini dihitung dengan 1 query grouped (bukan 7 query)
     * - Eager load leader.profile untuk menghindari N+1
     * - Auth dipusatkan ke resolveHakAkses()
     *
     * ==================================================================
     */
    public function index(Request $request)
    {

        /*
        ==============================================================
        1. AMBIL HAK AKSES ADMIN (dipusatkan)
        ==============================================================
        */
        $hakAkses = $this->resolveHakAkses();

        /*
        ==============================================================
        2. BASE QUERY DENGAN FILTER DIVISI
        ==============================================================
        Clone dipakai agar setiap turunan query berjalan independen.
        */
        $base = $this->buildBaseQuery($hakAkses);

        /*
        ==============================================================
        3. HITUNG STATUS COUNTS - EFISIEN: 1 QUERY SAJA
        ==============================================================
        SEBELUMNYA: 7 query terpisah per status → boros.

        SEKARANG: GROUP BY status → 1 query → di-map ke array.

        $statusCounts sengaja TIDAK ikut filter search agar
        angka kartu tidak berubah saat admin mengetik nama.
        */
        $rawCounts = (clone $base)
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $allStatuses = ['pending', 'verified', 'interview', 'accepted', 'rejected', 'resigned', 'completed'];

        $statusCounts = collect($allStatuses)
            ->mapWithKeys(fn($s) => [$s => $rawCounts->get($s, 0)])
            ->all();

        /*
        ==============================================================
        4. QUERY DATA UNTUK TABEL
        ==============================================================
        Eager loading dengan leader.profile untuk menghindari N+1
        saat blade mengakses leader->profile->full_name.
        */
        $query = (clone $base)
            ->with(['vacancy', 'leader.profile'])  // ← perbaikan: .profile ditambahkan
            ->orderBy('created_at', 'desc');

        // STEP: Filter status (opsional, dari query param ?status=...)
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // STEP: Filter pencarian nama / email pelamar (ketua lamaran)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('leader', function ($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // withQueryString() memastikan ?status=...&search=... ikut terbawa ke pagination
        $data = $query->paginate(10)->withQueryString();

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
     * IMPROVEMENT:
     * - Null safety pada hakAkses (dipusatkan ke resolveHakAkses)
     * - Validasi divisi dipindah ke assertDivisiAccess()
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
        resolveHakAkses() sudah handle null safety (abort 403 jika tidak ada).
        assertDivisiAccess() handle cross-division protection.
        */
        $hakAkses = $this->resolveHakAkses();
        $this->assertDivisiAccess($application, $hakAkses);

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
     * - verified, interview, accepted, rejected, resigned, completed
     *
     * IMPROVEMENT:
     * - DB::transaction + lockForUpdate mencegah race condition
     * - State transition validation mencegah status lompat sembarangan
     * - Null safety dipusatkan ke resolveHakAkses()
     * - Audit log ditambahkan untuk setiap perubahan status
     * - Members query dioptimalkan agar tidak 3 query terpisah
     *
     * ==================================================================
     */
    public function updateStatus(Request $request, $id)
    {
        /*
        ==============================================================
        1. VALIDASI INPUT AWAL (sebelum lock DB, hemat resource)
        ==============================================================
        */
        $request->validate([
            'status' => 'required|in:pending,verified,interview,accepted,rejected,resigned,completed',
            'admin_feedback' => 'required_if:status,rejected,resigned|nullable|string|max:500',
        ], [
            'admin_feedback.required_if' =>
            'Mohon maaf, jika menolak atau menandai mundur, WAJIB menyertakan alasannya.',
        ]);

        /*
        ==============================================================
        2. VALIDASI HAK AKSES ADMIN (dipusatkan)
        ==============================================================
        */
        $hakAkses = $this->resolveHakAkses();
        $newStatus = $request->status;

        /*
        ==============================================================
        3. BUNGKUS SEMUA LOGIC DALAM TRANSACTION + LOCK
        ==============================================================
        MASALAH SEBELUMNYA:
        Tanpa transaction, dua admin bisa klik "Accept" bersamaan,
        menyebabkan kuota terhitung dua kali atau status ganda.

        SOLUSI:
        lockForUpdate() mengunci baris di database sampai transaction
        selesai. Admin kedua akan menunggu hingga admin pertama selesai,
        lalu membaca data yang sudah terupdate → tidak bisa double accept.

        Ini kritis karena sistem menggunakan kuota slot yang terbatas.
        */
        $result = DB::transaction(function () use ($id, $request, $hakAkses, $newStatus) {

            /*
            ----------------------------------------------------------
            3a. AMBIL DATA APLIKASI DENGAN LOCK (anti race condition)
            ----------------------------------------------------------
            */
            $app = ApplicationMagang::with('vacancy')
                ->lockForUpdate()
                ->findOrFail($id);

            /*
            ----------------------------------------------------------
            3b. VALIDASI AKSES DIVISI
            ----------------------------------------------------------
            */
            $this->assertDivisiAccess($app, $hakAkses);

            /*
            ----------------------------------------------------------
            3c. MENCEGAH DOUBLE SUBMIT / STATUS SAMA
            ----------------------------------------------------------
            */
            if ($app->status === $newStatus) {
                return [
                    'error' => 'Lamaran ini sudah berstatus ' . strtoupper($newStatus) . '!'
                ];
            }

            /*
            ----------------------------------------------------------
            3d. VALIDASI STATE TRANSITION
            ----------------------------------------------------------
            BARU: Mencegah status lompat sembarangan.
            Contoh yang TIDAK diizinkan:
              pending   → completed
              rejected  → interview
              completed → pending

            STATE_TRANSITIONS mendefinisikan aturan bisnis secara eksplisit.
            ----------------------------------------------------------
            */
            if (!$this->isStateTransitionAllowed($app->status, $newStatus)) {
                $allowedList = implode(', ', self::STATE_TRANSITIONS[$app->status] ?? []);
                $allowedMsg  = $allowedList ?: 'tidak ada (status terminal)';

                return [
                    'error' => "Perubahan status dari '{$app->status}' ke '{$newStatus}' tidak diizinkan. "
                        . "Status yang diizinkan: {$allowedMsg}."
                ];
            }

            /*
            ----------------------------------------------------------
            3e. SAFETY NET: CEK KUOTA LOWONGAN
            ----------------------------------------------------------
            Digunakan saat lamaran yang sebelumnya rejected
            dipulihkan kembali menjadi pending.
            (Setelah state transition fix, rejected hanya bisa ke pending,
             bukan langsung accepted. Tapi pengecekan kuota tetap relevan
             bila pending kemudian diterima di step berikutnya.)
            ----------------------------------------------------------
            */
            if (
                $app->status === 'rejected' &&
                in_array($newStatus, ['accepted', 'verified', 'pending'])
            ) {
                $terpakai = ApplicationMagang::where('vacancy_id', $app->vacancy_id)
                    ->whereIn('status', ['pending', 'verified', 'interview', 'accepted'])
                    ->count();

                $quota = $app->vacancy->quota_slots;

                if (($quota - $terpakai) <= 0) {
                    return [
                        'error' => 'Gagal memulihkan status! Kuota lowongan ini sudah penuh.'
                    ];
                }
            }

            /*
            ----------------------------------------------------------
            3f. GUARD: TIDAK BOLEH COMPLETED SEBELUM SEMUA DINILAI
            ----------------------------------------------------------
            OPTIMASI: Daripada 3 query terpisah (count members, pluck id, count assessment),
            sekarang cukup 2 query yang lebih efisien.
            ----------------------------------------------------------
            */
            if ($newStatus === 'completed') {

                $memberIds = $app->members()->pluck('id');

                $totalMembers  = $memberIds->count();
                $dinilaiCount  = AssessmentMagang::whereIn('member_id', $memberIds)->count();

                if ($dinilaiCount < $totalMembers) {
                    $belumDinilai = $totalMembers - $dinilaiCount;

                    return [
                        'error' => "Tidak dapat menyelesaikan magang! Masih ada {$belumDinilai} peserta "
                            . "yang belum dinilai. Silakan lengkapi penilaian di menu Penilaian terlebih dahulu."
                    ];
                }
            }

            /*
            ----------------------------------------------------------
            3g. SIMPAN PERUBAHAN STATUS & CATATAN
            ----------------------------------------------------------
            */
            $oldStatus       = $app->status;
            $app->status     = $newStatus;

            // Jika ditolak ATAU mengundurkan diri, simpan alasannya. Jika tidak, KOSONGKAN.
            if (in_array($newStatus, ['rejected', 'resigned'])) {
                $app->admin_feedback = $request->admin_feedback;
            } else {
                $app->admin_feedback = null;
            }

            $app->save();

            /*
            ----------------------------------------------------------
            3h. AUTO UPDATE STATUS LOWONGAN BERDASARKAN KUOTA
            ----------------------------------------------------------
            */
            $app->vacancy->updateStatusBasedOnQuota();

            /*
            ----------------------------------------------------------
            3i. AUDIT LOG
            ----------------------------------------------------------
            Penting untuk instansi: siapa, kapan, ubah apa.
            Log ini tersimpan di storage/logs/laravel.log (atau channel lain).
            ----------------------------------------------------------
            */
            Log::info('Admin mengubah status lamaran magang', [
                'admin_id'       => Auth::id(),
                'application_id' => $app->id,
                'vacancy_id'     => $app->vacancy_id,
                'division'       => $app->vacancy->division_name,
                'old_status'     => $oldStatus,
                'new_status'     => $newStatus,
                'feedback'       => $request->admin_feedback ?? null,
                'timestamp'      => now()->toDateTimeString(),
            ]);

            return ['success' => true, 'app_id' => $app->id, 'new_status' => $newStatus];
        });

        /*
        ==============================================================
        4. PROSES HASIL TRANSACTION
        ==============================================================
        Jika ada error dari dalam transaction, kembalikan ke halaman
        sebelumnya dengan pesan error. Jika berhasil, bersihkan cache
        dan arahkan ke halaman detail.
        */
        if (isset($result['error'])) {
            return back()->with('error', $result['error']);
        }

        /*
        ==============================================================
        5. BERSIHKAN CACHE DASHBOARD
        ==============================================================
        Di luar transaction agar tidak memperpanjang waktu lock.
        */
        DashboardCache::clear();

        /*
        ==============================================================
        6. FEEDBACK PESAN KE ADMIN
        ==============================================================
        */
        $msg = "Status berhasil diubah menjadi " . ucfirst($newStatus);

        if ($newStatus === 'rejected') {
            $msg .= " (Kuota lowongan otomatis bertambah kembali).";
        } elseif ($newStatus === 'accepted') {
            $msg .= " (Satu slot kuota permanen terpakai).";
        } elseif ($newStatus === 'completed') {
            $msg .= " (Magang dinyatakan selesai. Sertifikat dapat diterbitkan).";
        }

        return redirect()
            ->route('admin.applications.show', $result['app_id'])
            ->with('success', $msg);
    }
}
