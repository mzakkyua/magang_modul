<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VacancyMagang;
use App\Models\ApplicationMagang;
use App\Models\ApplicationMemberMagang;
use App\Models\UserMagang;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ApplicationMagangController extends Controller
{
    /**
     * ===============================================================
     * STORE APPLICATION
     * ===============================================================
     */
    public function store(Request $request)
    {
        /**
         * ===============================================================
         * STEP 1 — VALIDASI INPUT DASAR
         * ===============================================================
         */
        $request->validate([
            'vacancy_id'        => 'required|exists:vacancies_magang,id',
            'research_title'    => 'nullable|string|max:255',
            'research_abstract' => 'nullable|string',
            'member_emails'     => 'nullable|array',
            'member_emails.*'   => 'email|exists:users_magang,email', // Pastikan emailnya valid & terdaftar
        ], [
            'member_emails.*.exists' => 'Email anggota :input tidak ditemukan di sistem. Pastikan mereka sudah mendaftar akun.',
        ]);


        /**
         * ===============================================================
         * STEP 2 — AMBIL DATA DASAR & KONVERSI EMAIL KE ID
         * ===============================================================
         */
        $leaderId = Auth::guard('magang')->id();
        $vacancyId = $request->vacancy_id;

        $memberIds = [];
        if ($request->has('member_emails') && !empty($request->member_emails)) {
            $memberIds = UserMagang::whereIn('email', $request->member_emails)
                ->pluck('id')
                ->toArray();
        }


        /**
         * ===============================================================
         * STEP 3 — FILTER MEMBER
         * ===============================================================
         */
        $memberIds = array_diff($memberIds, [$leaderId]);
        array_unshift($memberIds, $leaderId);
        $memberIds = array_unique($memberIds);
        $totalOrang = count($memberIds);


        /**
         * ===============================================================
         * STEP 4 — DATABASE TRANSACTION DENGAN TRY CATCH
         * ===============================================================
         */
        try {
            return DB::transaction(function () use ($request, $leaderId, $vacancyId, $memberIds, $totalOrang) {

                /**
                 * ===========================================================
                 * STEP 5 — LOCK VACANCY
                 * ===========================================================
                 */
                $vacancy = VacancyMagang::where('id', $vacancyId)
                    ->lockForUpdate()
                    ->firstOrFail();


                /**
                 * ===========================================================
                 * BUSINESS RULE 1 — CEK STATUS LOWONGAN
                 * ===========================================================
                 */
                if ($vacancy->status !== 'open') {
                    throw new \Exception('Maaf, pendaftaran untuk lowongan ini sedang ditutup oleh admin.');
                }


                /**
                 * ===========================================================
                 * BUSINESS RULE 2 — CEK MODE PENDAFTARAN
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
                 * BUSINESS RULE 3 — CEK MIN & MAX MEMBER
                 * ===========================================================
                 */
                // Jika mode Hybrid dan dia daftar sendiri (Individu), loloskan pengecekan minimal anggota kelompok
                if ($vacancy->registration_mode === 'hybrid' && $totalOrang == 1) {
                    // Lolos (tidak melakukan pengecekan min/max di bawah)
                } else {
                    // Untuk mode Kelompok, atau Hybrid yang mengundang teman
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
                if ($vacancy->type === 'penelitian') {
                    if (empty($request->research_title) || empty($request->research_abstract)) {
                        throw new \Exception('Untuk jalur penelitian, judul dan abstrak wajib diisi.');
                    }
                }


                /**
                 * ===========================================================
                 * BUSINESS RULE 5 — CEK DUPLICATE APPLY
                 * ===========================================================
                 */
                $alreadyApplied = ApplicationMagang::where('vacancy_id', $vacancyId)
                    ->where('leader_user_id', $leaderId)
                    ->whereIn('status', ['pending', 'verified', 'interview', 'accepted'])
                    ->exists();

                if ($alreadyApplied) {
                    throw new \Exception('Anda sudah mendaftar pada lowongan ini.');
                }


                /**
                 * ===========================================================
                 * BUSINESS RULE 6 — CEK USER BUSY
                 * ===========================================================
                 */
                $users = UserMagang::whereIn('id', $memberIds)->pluck('username', 'id');

                foreach ($memberIds as $userId) {
                    if ($this->isUserBusy($userId)) {
                        $nama = $users[$userId] ?? 'User';
                        throw new \Exception("Anda '$nama' masih terdaftar pada pengajuan lain yang aktif.");
                    }
                }


                /**
                 * ===========================================================
                 * BUSINESS RULE 7 — CEK KUOTA SLOT
                 * ===========================================================
                 */
                $terpakai = ApplicationMagang::where('vacancy_id', $vacancyId)
                    ->whereIn('status', ['pending', 'verified', 'interview', 'accepted'])
                    ->count();

                if (($vacancy->quota_slots - $terpakai) <= 0) {
                    throw new \Exception('Mohon maaf, kuota pendaftaran baru saja penuh.');
                }


                /**
                 * ===========================================================
                 * BUSINESS RULE 8 — CEK KELENGKAPAN DOKUMEN (CV & PROPOSAL)
                 * ===========================================================
                 */
                // 1. Cek kelengkapan dokumen Ketua (Leader)
                $leaderProfile = \App\Models\ProfileMagang::where('user_id', $leaderId)->first();

                if (!$leaderProfile || empty($leaderProfile->cv_file_path)) {
                    throw new \Exception('Pendaftaran gagal! Anda belum mengunggah CV. Silakan lengkapi di menu Profil.');
                }

                // 2. Cek proposal khusus untuk jalur Penelitian
                if ($vacancy->type === 'penelitian' && empty($leaderProfile->proposal_file_path)) {
                    throw new \Exception('Untuk jalur penelitian, Anda wajib mengunggah File Proposal di menu Profil Anda.');
                }

                // 3. Cek CV Anggota (jika mendaftar secara kelompok)
                if ($totalOrang > 1) {
                    foreach ($memberIds as $userId) {
                        if ($userId === $leaderId) continue;

                        $memberProfile = \App\Models\ProfileMagang::where('user_id', $userId)->first();

                        if (!$memberProfile || empty($memberProfile->cv_file_path)) {
                            $memberName = $users[$userId] ?? 'Anggota';
                            throw new \Exception("Pendaftaran gagal! Anggota '$memberName' belum mengunggah CV di profilnya.");
                        }
                    }
                }


                /**
                 * ===========================================================
                 * SAVE STEP 1 — CREATE APPLICATION HEADER
                 * ===========================================================
                 */
                $application = ApplicationMagang::create([
                    'vacancy_id'        => $vacancyId,
                    'leader_user_id'    => $leaderId,
                    'research_title'    => $request->research_title,
                    'research_abstract' => $request->research_abstract,
                    'status'            => 'pending',
                ]);


                /**
                 * ===========================================================
                 * SAVE STEP 2 — CREATE APPLICATION MEMBERS
                 * ===========================================================
                 */
                foreach ($memberIds as $userId) {
                    ApplicationMemberMagang::create([
                        'application_id'    => $application->id,
                        'user_id'           => $userId,
                        'individual_status' => 'active',
                    ]);
                }

                /**
                 * ===========================================================
                 * AUTO UPDATE STATUS LOWONGAN
                 * ===========================================================
                 */
                $vacancy->updateStatusBasedOnQuota();


                /**
                 * ===========================================================
                 * SUCCESS RESPONSE
                 * ===========================================================
                 */
                return redirect()
                    ->route('dashboard.index')
                    ->with('success', 'Pendaftaran berhasil! Silakan menunggu verifikasi admin.');
            });
        } catch (\Exception $e) {
            /**
             * ===========================================================
             * ERROR HANDLING RESPONSE
             * ===========================================================
             */
            return back()->with('error', $e->getMessage());
        }
    }


    /**
     * ===============================================================
     * HELPER — CEK USER BUSY
     * ===============================================================
     */
    private function isUserBusy($userId)
    {
        return DB::table('application_members_magang as m')
            ->join('applications_magang as a', 'm.application_id', '=', 'a.id')
            ->where('m.user_id', $userId)
            ->whereIn('a.status', ['pending', 'verified', 'interview', 'accepted'])
            ->exists();
    }
}
