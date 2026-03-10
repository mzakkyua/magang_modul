<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VacancyMagang;
use Illuminate\Support\Facades\Auth;

class DashboardMagangController extends Controller
{
    public function index()
    {
        $user = Auth::guard('magang')->user();

        $isSMK = optional($user->profile)->education_level === 'siswa_smk';

        $vacanciesMagang = VacancyMagang::where('type', 'magang')
            ->where('status', 'open')
            ->latest()
            ->get();

        $vacanciesPenelitian = VacancyMagang::where('type', 'penelitian')
            ->where('status', 'open')
            ->latest()
            ->get();

        return view('magang.dashboard.index', compact(
            'vacanciesMagang',
            'vacanciesPenelitian',
            'isSMK'
        ));
    }
}
