<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VacancyMagang;

class LandingController extends Controller
{
    public function index(Request $request)
    {
        // Ambil lowongan yang statusnya 'open'
        $query = VacancyMagang::where('status', 'open');

        // Fitur Cari (Opsional)
        if ($request->has('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        // Urutkan terbaru & pagination
        $vacancies = $query->orderBy('created_at', 'desc')->paginate(9);

        // PENTING: return view 'landing' (sesuai nama file landing.blade.php)
        return view('landing', compact('vacancies'));
    }

    public function show($id)
    {
        $vacancy = VacancyMagang::findOrFail($id);
        return view('landing_detail', compact('vacancy')); // Nanti kita buat file ini juga
    }
}