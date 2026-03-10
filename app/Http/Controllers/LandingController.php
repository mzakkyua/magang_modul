<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VacancyMagang;

class LandingController extends Controller
{
    public function index(Request $request)
    {
        $query = VacancyMagang::where('status', 'open');

        // fitur search
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        // ambil data lowongan terbaru
        $vacancies = $query->latest()->get();

        return view('landing.index', compact('vacancies'));
    }

    public function show($id)
    {
        $vacancy = VacancyMagang::findOrFail($id);

        return view('landing.show', compact('vacancy'));
    }
}
