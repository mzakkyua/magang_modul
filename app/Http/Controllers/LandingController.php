<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VacancyMagang;

class LandingController extends Controller
{

    /**
     * ===============================================================
     * LANDING PAGE
     * ===============================================================
     *
     * Menampilkan daftar lowongan magang yang sedang dibuka.
     *
     * Fitur:
     * - Search lowongan
     * - Urutan terbaru
     *
     */

    public function index(Request $request)
    {

        /**
         * ===========================================================
         * STEP 1 — BASE QUERY
         * ===========================================================
         *
         * Hanya ambil lowongan yang statusnya OPEN
         *
         */

        $query = VacancyMagang::query()
            ->where('status', 'open');



        /**
         * ===========================================================
         * STEP 2 — SEARCH FEATURE
         * ===========================================================
         */

        if ($request->filled('search')) {

            // bersihkan input search
            $search = trim($request->search);

            $query->where('title', 'like', '%' . $search . '%');
        }



        /**
         * ===========================================================
         * STEP 3 — AMBIL DATA LOWONGAN
         * ===========================================================
         *
         * latest() = urutkan berdasarkan created_at DESC
         */

        $vacancies = $query
            ->latest()
            ->limit(20) // mencegah query terlalu besar
            ->get();



        /**
         * ===========================================================
         * STEP 4 — RETURN VIEW
         * ===========================================================
         */

        return view('landing.index', compact('vacancies'));
    }



    /**
     * ===============================================================
     * DETAIL LOWONGAN
     * ===============================================================
     *
     * Menampilkan detail lowongan.
     *
     * Hanya lowongan dengan status OPEN yang boleh diakses.
     *
     */

    public function show($id)
    {

        /**
         * ===========================================================
         * AMBIL DATA LOWONGAN
         * ===========================================================
         */

        $vacancy = VacancyMagang::where('id', $id)
            ->where('status', 'open')
            ->firstOrFail();



        /**
         * ===========================================================
         * RETURN VIEW DETAIL
         * ===========================================================
         */

        return view('landing.show', compact('vacancy'));
    }
}
