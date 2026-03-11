<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\VacancyMagang;

class DashboardMagangController extends Controller
{

    public function index(Request $request)
    {

        /**
         * ===============================
         * USER LOGIN
         * ===============================
         */

        $user = Auth::guard('magang')->user();

        $isSMK = optional($user->profile)->education_level === 'siswa_smk';


        /**
         * ===============================
         * SEARCH INPUT
         * ===============================
         */

        $search = $request->search;


        /**
         * ===============================
         * QUERY MAGANG
         * ===============================
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
            ->get();



        /**
         * ===============================
         * QUERY PENELITIAN
         * ===============================
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
         * ===============================
         * RETURN VIEW
         * ===============================
         */

        return view('magang.dashboard.index', compact(
            'vacanciesMagang',
            'vacanciesPenelitian',
            'isSMK',
            'search'
        ));
    }
}