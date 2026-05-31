<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VacancyMagang;
use App\Models\ApplicationMagang;
use App\Models\ApplicationMemberMagang;
use App\Models\ProfileMagang;
use App\Models\UserMagang;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ApplicationMagangController extends Controller
{

    /**
     * ===============================================================
     * STORE APPLICATION
     * ===============================================================
     */
    public function store(Request $request)
    {
        /** @var \App\Models\UserMagang $user */
        $user     = Auth::guard('magang')->user();
        $leaderId = $user->id;

        /**
         * ===============================================================
         * STEP 0 — FAIL FAST: CEK KELENGKAPAN PROFIL KETUA
         * ===============================================================
         * Taruh paling atas agar tidak membuka koneksi DB / transaksi
         * sia-sia kalau profil ketuanya saja belum lengkap.
         */
        $profile = ProfileMagang::where('user_id', $leaderId)->first();

        if (!$profile || !$profile->isComplete()) {
            return back()->with(
                'error',
                'Gagal melamar! Anda WAJIB melengkapi seluruh data Profil (termasuk unggah CV) terlebih dahulu.'
            );
        }

        /**
         * ===============================================================
         * STEP 1 — VALIDASI INPUT
         * ===============================================================
         *
         * BARU:
         * - `distinct`  → mencegah email anggota duplikat dalam satu form
         * - `max:3000`  → sanitasi panjang abstract
         * - `max:255`   → sudah ada, tetap dipertahankan
         *
         * CATATAN: validasi `not_in leader email` dilakukan manual di Step 3
         * karena Rule `different` tidak bisa cross-field array dengan mudah.
         */
        $request->validate([
            'vacancy_id'        => 'required|exists:vacancies_magang,id',
            'research_title'    => 'nullable|string|max:255',
            'research_abstract' => 'nullable|string|max:3000',

            // BARU: distinct → mencegah email duplikat dalam satu pengajuan
            'member_emails'     => 'nullable|array',
            'member_emails.*'   => 'email|exists:users_magang,email|distinct',
        ], [
            'member_emails.*.exists'    => 'Email anggota :input tidak ditemukan di sistem. Pastikan mereka sudah mendaftar akun.',
            'member_emails.*.distinct'  => 'Email anggota :input tercantum lebih dari sekali.',
            'research_abstract.max'     => 'Abstrak penelitian maksimal 3000 karakter.',
        ]);

        /**
         * ===============================================================
         * STEP 2 — AMBIL DATA DASAR & KONVERSI EMAIL KE ID
         * ===============================================================
         */
        $vacancyId   = $request->vacancy_id;
        $memberEmails = array_filter($request->member_emails ?? []);

        // Konversi email → ID (hanya sekali query)
        $memberIds = [];
        if (!empty($memberEmails)) {
            $memberIds = UserMagang::whereIn('email', $memberEmails)
                ->pluck('id')
                ->toArray();
        }

        /**
         * ===============================================================
         * STEP 3 — NORMALIZE & SUSUN FINAL MEMBER LIST
         * ===============================================================
         *
         * BARU: Buang duplikat termasuk jika ketua secara tidak sengaja
         * memasukkan email dirinya sendiri di form anggota.
         * Ketua selalu menjadi elemen pertama (index 0).
         */
        // Hapus leaderId dari array anggota (antisipasi input ganda dari form)
        $memberIds = array_values(array_unique(array_diff($memberIds, [$leaderId])));

        // Ketua selalu di posisi pertama
        array_unshift($memberIds, $leaderId);

        $totalOrang = count($memberIds);

        /**
         * ===============================================================
         * STEP 4 — BUNGKUS DALAM DB TRANSACTION
         * ===============================================================
         *
         * Semua pengecekan kuota, duplikat, dan penyimpanan data
         * dilakukan di dalam satu transaksi agar rollback otomatis
         * jika ada bagian yang gagal.
         */
        try {
            return DB::transaction(function () use ($request, $leaderId, $vacancyId, $memberIds, $totalOrang, $profile, $user) {

                /**
                 * ===========================================================
                 * STEP 5 — LOCK VACANCY (anti race condition kuota)
                 * ===========================================================
                 * lockForUpdate() mengunci baris vacancy di DB sampai
                 * transaksi ini selesai, mencegah dua orang lolos kuota
                 * di saat bersamaan.
                 */
                $vacancy = VacancyMagang::where('id', $vacancyId)
                    ->lockForUpdate()
                    ->firstOrFail();

                /**
                 * ===========================================================
                 * BUSINESS RULE 1 — STATUS LOWONGAN
                 * ===========================================================
                 */
                if ($vacancy->status !== 'open') {
                    throw new \Exception('Maaf, pendaftaran untuk lowongan ini sedang ditutup oleh admin.');
                }

                /**
                 * ===========================================================
                 * BUSINESS RULE 2 — MODE PENDAFTARAN
                 * ===========================================================
                 */
                if ($vacancy->registration_mode === 'individu' && $totalOrang > 1) {
                    throw new \Exception('Lowongan ini hanya menerima pendaftaran individu.');
                }

                if ($vacancy->registration_mode === 'kelompok' && $totalOrang == 1) {
                    throw new \Exception('Lowongan ini hanya menerima pendaftaran kelompok.');
                }

                /**
                 * ===========================================================
                 * BUSINESS RULE 3 — MIN & MAX MEMBER
                 * ===========================================================
                 */
                $isHybridIndividu = ($vacancy->registration_mode === 'hybrid' && $totalOrang === 1);

                if (!$isHybridIndividu) {
                    if ($totalOrang < $vacancy->min_members) {
                        throw new \Exception("Pendaftaran kelompok mewajibkan minimal {$vacancy->min_members} orang.");
                    }
                    if ($totalOrang > $vacancy->max_members) {
                        throw new \Exception("Lowongan ini membatasi maksimal {$vacancy->max_members} orang.");
                    }
                }

                /**
                 * ===========================================================
                 * BUSINESS RULE 4 — VALIDASI PENELITIAN
                 * ===========================================================
                 */
                $this->checkResearchRules($vacancy, $request, $profile);

                /**
                 * ===========================================================
                 * BUSINESS RULE 5 — DUPLICATE APPLY
                 * ===========================================================
                 * Menggunakan exists() karena hanya butuh cek ada/tidak,
                 * lebih efisien dari first() yang menarik seluruh kolom.
                 */
                $this->checkDuplicate($vacancyId, $leaderId);

                /**
                 * ===========================================================
                 * BUSINESS RULE 6 & 8 — VALIDASI SEMUA MEMBER
                 * ===========================================================
                 *
                 * IMPROVEMENT BESAR:
                 * Sebelumnya ada dua loop terpisah:
                 *   - Loop 1: cek isUserBusy (per user = N query)
                 *   - Loop 2: cek profile per anggota (N+1 query)
                 *
                 * Sekarang:
                 *   - Ambil semua user sekaligus dengan eager load profile: 1 query
                 *   - Iterasi collection di PHP (tidak buka koneksi DB lagi)
                 */
                $this->validateMembers($memberIds, $leaderId, $totalOrang);

                /**
                 * ===========================================================
                 * BUSINESS RULE 7 — CEK KUOTA SLOT
                 * ===========================================================
                 */
                $this->checkQuota($vacancyId, $vacancy);

                /**
                 * ===========================================================
                 * SAVE — BUAT APLIKASI & ANGGOTA
                 * ===========================================================
                 */
                $application = $this->saveApplication($request, $vacancyId, $leaderId, $memberIds);

                /**
                 * ===========================================================
                 * AUTO UPDATE STATUS LOWONGAN
                 * ===========================================================
                 */
                $vacancy->updateStatusBasedOnQuota();

                /**
                 * ===========================================================
                 * AUDIT LOG
                 * ===========================================================
                 * Penting untuk instansi: siapa mendaftar apa dan kapan.
                 */
                Log::info('Pendaftaran magang baru berhasil', [
                    'application_id' => $application->id,
                    'leader_id'      => $leaderId,
                    'leader_email'   => $user->email,
                    'vacancy_id'     => $vacancyId,
                    'vacancy_name'   => $vacancy->title ?? $vacancyId,
                    'total_anggota'  => $totalOrang,
                    'timestamp'      => now()->toDateTimeString(),
                ]);

                return redirect()
                    ->route('status')
                    ->with('success_apply', 'Lamaran berhasil dikirim.');
            });
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }


    // ======================================================================
    // PRIVATE METHODS — PEMISAHAN TANGGUNG JAWAB
    // ======================================================================


    /**
     * -----------------------------------------------------------------------
     * checkResearchRules()
     * -----------------------------------------------------------------------
     * Memvalidasi aturan khusus jalur penelitian:
     * - judul + abstrak wajib diisi
     * - proposal wajib sudah diunggah di profil
     * -----------------------------------------------------------------------
     */
    private function checkResearchRules(VacancyMagang $vacancy, Request $request, ProfileMagang $profile): void
    {
        if ($vacancy->type !== 'penelitian') {
            return;
        }

        if (empty($request->research_title) || empty($request->research_abstract)) {
            throw new \Exception('Untuk jalur penelitian, judul dan abstrak wajib diisi.');
        }

        if (empty($profile->proposal_file_path)) {
            throw new \Exception('Untuk jalur penelitian, Anda wajib mengunggah File Proposal di menu Profil Anda sebelum melamar.');
        }
    }


    /**
     * -----------------------------------------------------------------------
     * checkDuplicate()
     * -----------------------------------------------------------------------
     * Mencegah ketua melamar ke lowongan yang sama lebih dari sekali
     * (selama masih dalam status aktif).
     *
     * Menggunakan exists() — lebih efisien dari first() karena tidak
     * perlu menarik data kolom, hanya cek keberadaan baris.
     * -----------------------------------------------------------------------
     */
    private function checkDuplicate(int $vacancyId, int $leaderId): void
    {
        $alreadyApplied = ApplicationMagang::where('vacancy_id', $vacancyId)
            ->where('leader_user_id', $leaderId)
            ->whereIn('status', ['pending', 'verified', 'interview', 'accepted'])
            ->exists(); // ← perbaikan: exists() bukan first()

        if ($alreadyApplied) {
            throw new \Exception('Anda sudah mendaftar pada lowongan ini.');
        }
    }


    /**
     * -----------------------------------------------------------------------
     * validateMembers()
     * -----------------------------------------------------------------------
     * Memvalidasi seluruh anggota tim dalam satu batch:
     *   1. Apakah user busy (masih aktif di aplikasi lain)?
     *   2. Apakah profil anggota sudah lengkap (jika daftar kelompok)?
     */
    private function validateMembers(array $memberIds, int $leaderId, int $totalOrang): void
    {
        // STEP A: Ambil semua user + profile dalam 1 query (eager load)
        $users = UserMagang::whereIn('id', $memberIds)
            ->with('profile') // ← eager load, tidak N+1 lagi
            ->get()
            ->keyBy('id');

        // STEP B: Cek siapa saja yang busy — 1 query WhereIn + exists
        $busyUserIds = DB::table('application_members_magang as m')
            ->join('applications_magang as a', 'm.application_id', '=', 'a.id')
            ->whereIn('m.user_id', $memberIds)
            ->whereIn('a.status', ['pending', 'verified', 'interview', 'accepted'])
            ->pluck('m.user_id')
            ->unique()
            ->flip(); // flip agar lookup O(1) bukan O(n)

        // STEP C: Iterasi di PHP (tanpa koneksi DB tambahan)
        foreach ($memberIds as $userId) {
            $userModel = $users->get($userId);
            $nama      = $userModel?->username ?? 'User';

            // Cek busy
            if ($busyUserIds->has($userId)) {
                throw new \Exception("Pendaftaran Gagal: '{$nama}' masih terdaftar pada pengajuan lain yang aktif.");
            }

            // Cek kelengkapan profil anggota (skip ketua — sudah dicek di Step 0)
            if ($userId !== $leaderId && $totalOrang > 1) {
                $memberProfile = $userModel?->profile;

                if (!$memberProfile || !$memberProfile->isComplete()) {
                    throw new \Exception("Pendaftaran gagal! Anggota '{$nama}' belum melengkapi Profil atau belum mengunggah CV.");
                }
            }
        }
    }


    /**
     * -----------------------------------------------------------------------
     * checkQuota()
     * -----------------------------------------------------------------------
     * Memastikan masih ada slot tersedia di lowongan sebelum menyimpan.
     * Dipanggil di dalam transaction setelah lockForUpdate(), sehingga
     * hitungan slot yang dibaca sudah konsisten (tidak race condition).
     * -----------------------------------------------------------------------
     */
    private function checkQuota(int $vacancyId, VacancyMagang $vacancy): void
    {
        $terpakai = ApplicationMagang::where('vacancy_id', $vacancyId)
            ->whereIn('status', ['pending', 'verified', 'interview', 'accepted'])
            ->count();

        // Karena admin minimal set 1, maka jika sisa <= 0, pasti sudah penuh.
        if (($vacancy->quota_slots - $terpakai) <= 0) {
            throw new \Exception('Mohon maaf, kuota pendaftaran baru saja penuh atau sedang ditutup.');
        }
    }

    /**
     * -----------------------------------------------------------------------
     * saveApplication()
     * -----------------------------------------------------------------------
     * Menyimpan header aplikasi dan seluruh anggotanya.
     * Dipisah agar store() tidak terlalu panjang dan bagian ini
     * mudah di-test secara independen.
     * -----------------------------------------------------------------------
     */
    private function saveApplication(Request $request, int $vacancyId, int $leaderId, array $memberIds): ApplicationMagang
    {
        // SAVE 1: Header aplikasi
        $application = ApplicationMagang::create([
            'vacancy_id'        => $vacancyId,
            'leader_user_id'    => $leaderId,
            'research_title'    => $request->research_title,
            'research_abstract' => $request->research_abstract,
            'status'            => 'pending',
        ]);

        // SAVE 2: Semua anggota (insert batch lebih efisien dari loop create())
        $now     = now();
        $members = array_map(fn($userId) => [
            'application_id'    => $application->id,
            'user_id'           => $userId,
            'individual_status' => 'active',
            'created_at'        => $now,
            'updated_at'        => $now,
        ], $memberIds);

        ApplicationMemberMagang::insert($members); // ← 1 INSERT, bukan N INSERT

        return $application;
    }
}
