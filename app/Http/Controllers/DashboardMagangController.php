<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\VacancyMagang;

class DashboardMagangController extends Controller
{

    /**
     * ===============================================================
     * DASHBOARD PESERTA MAGANG
     * ===============================================================
     *
     * Menampilkan:
     *
     * - Lowongan Magang
     * - Lowongan Penelitian
     * - Status pendidikan user (SMK atau bukan)
     *
     * Data ini digunakan untuk:
     * - tab filtering
     * - conditional UI
     *
     */

    public function index()
    {

        /**
         * ===========================================================
         * STEP 1 — AMBIL USER YANG SEDANG LOGIN
         * ===========================================================
         */

        $user = Auth::guard('magang')->user();



        /**
         * ===========================================================
         * STEP 2 — CEK STATUS PENDIDIKAN USER
         * ===========================================================
         *
         * optional() digunakan untuk menghindari error
         * jika relasi profile belum tersedia.
         *
         */

        $isSMK = optional($user->profile)->education_level === 'siswa_smk';



        /**
         * ===========================================================
         * STEP 3 — AMBIL LOWONGAN MAGANG
         * ===========================================================
         *
         * Filter:
         * - type = magang
         * - status = open
         *
         */

        $vacanciesMagang = VacancyMagang::query()
            ->where('type', 'magang')
            ->where('status', 'open')
            ->latest()
            ->limit(20) // prevent heavy query jika data banyak
            ->get();



        /**
         * ===========================================================
         * STEP 4 — AMBIL LOWONGAN PENELITIAN
         * ===========================================================
         */

        $vacanciesPenelitian = VacancyMagang::query()
            ->where('type', 'penelitian')
            ->where('status', 'open')
            ->latest()
            ->limit(20)
            ->get();



        /**
         * ===========================================================
         * STEP 5 — RETURN VIEW DASHBOARD
         * ===========================================================
         */

        return view('magang.dashboard.index', compact(
            'vacanciesMagang',
            'vacanciesPenelitian',
            'isSMK'
        ));
    }
}
