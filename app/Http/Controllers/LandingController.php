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
        $search = $request->search;

    
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


         /**
         * ===========================================================
         * Magang
         * ===========================================================
         */

        $vacanciesMagang = VacancyMagang::query()
            ->where('status', 'open')
            ->where('type', 'magang')
            ->when($search, function ($query) use ($search) {

                $query->where(function ($q) use ($search) {

                    $q->where('title', 'LIKE', "%{$search}%")
                        ->orWhere('division_name', 'LIKE', "%{$search}%")
                        ->orWhere('description', 'LIKE', "%{$search}%");

                });

            })
            ->latest()
            ->limit(20)
            ->get();

             /**
         * ===========================================================
         * Penelitian
         * ===========================================================
         */

            $vacanciesPenelitian = VacancyMagang::query()
            ->where('status', 'open')
            ->where('type', 'penelitian')
            ->when($search, function ($query) use ($search) {

                $query->where(function ($q) use ($search) {

                    $q->where('title', 'LIKE', "%{$search}%")
                        ->orWhere('division_name', 'LIKE', "%{$search}%")
                        ->orWhere('description', 'LIKE', "%{$search}%");

                });

            })
            ->latest()
            ->get();


       


        /**
         * ===========================================================
         * STEP 4 — RETURN VIEW
         * ===========================================================
         */

         return view('landing.index', compact(
            'vacanciesMagang',
            'vacanciesPenelitian',
            'search'
        ));
    }}