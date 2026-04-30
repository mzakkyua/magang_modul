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
         * STEP 2 — AMBIL DATA LAMARAN USER
         * ===========================================================
         *
         * Filtering berdasarkan:
         * leader_user_id
         *
         * Eager loading:
         * vacancy
         *
         * Tujuannya untuk menghindari N+1 query
         *
         */

        $applications = ApplicationMagang::query()
            ->where('leader_user_id', $user->id)
            ->with('vacancy')
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(10); // lebih scalable dibanding get()



        /**
         * ===========================================================
         * STEP 3 — RETURN VIEW
         * ===========================================================
         */

        return view('magang.status.index', compact('applications'));
    }
}
