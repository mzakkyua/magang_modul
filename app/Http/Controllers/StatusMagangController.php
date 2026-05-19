<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ApplicationMagang;

class StatusMagangController extends Controller
{

    /**
     * ===============================================================
     * RIWAYAT STATUS LAMARAN PESERTA
     * ===============================================================
     *
     * Halaman ini menampilkan:
     * - daftar lamaran yang pernah diajukan
     * - status masing-masing lamaran
     *
     * Status yang mungkin:
     * - pending
     * - verified
     * - interview
     * - accepted
     * - rejected
     *
     */

    public function index()
    {
        /**
         * ===========================================================
         * STEP 1 — AMBIL USER LOGIN
         * ===========================================================
         */
        $user = Auth::guard('magang')->user();

        /**
         * ===========================================================
         * STEP 2 — AMBIL DATA LAMARAN
         * ===========================================================
         *
         * User dapat melihat:
         *
         * 1. Lamaran sebagai ketua
         * 2. Lamaran sebagai anggota kelompok
         *
         * Tetapi:
         * - ownership utama tetap di ketua
         * - anggota hanya bersifat read-only visibility
         *
         * Tujuan:
         * - UX lebih jelas
         * - anggota tetap tahu statusnya
         * - tidak mengubah business flow existing
         * ===========================================================
         */
        $applications = ApplicationMagang::query()

            /**
             * -------------------------------------------------------
             * SEBAGAI KETUA
             * -------------------------------------------------------
             */
            ->where('leader_user_id', $user->id)

            /**
             * -------------------------------------------------------
             * ATAU SEBAGAI ANGGOTA
             * -------------------------------------------------------
             */
            ->orWhereHas('members', function ($query) use ($user) {

                $query->where(
                    'user_id',
                    $user->id
                );
            })

            /**
             * -------------------------------------------------------
             * EAGER LOAD
             * -------------------------------------------------------
             */
            ->with([
                'vacancy',
                'members.user.profile',
            ])

            /**
             * -------------------------------------------------------
             * SORTING
             * -------------------------------------------------------
             */
            ->orderByDesc('created_at')
            ->orderByDesc('id')

            /**
             * -------------------------------------------------------
             * PAGINATION
             * -------------------------------------------------------
             */
            ->paginate(10);

        /**
         * ===========================================================
         * STEP 3 — MAP EXTRA INFO
         * ===========================================================
         *
         * Tambahkan flag:
         * - apakah user adalah ketua
         */
        $applications->getCollection()->transform(function ($application) use ($user) {

            $application->is_leader =
                (int) $application->leader_user_id === (int) $user->id;

            return $application;
        });

        /**
         * ===========================================================
         * STEP 4 — RETURN VIEW
         * ===========================================================
         */
        return view(
            'magang.status.index',
            compact('applications')
        );
    }
}
