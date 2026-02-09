<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VacancyMagang;

class LandingController extends Controller
{
    public function index()
    {
        // Ambil SEMUA lowongan yang statusnya OPEN
        // Tidak perlu filter SMK/Mahasiswa dulu, tampilkan saja semua biar menarik
        $vacancies = VacancyMagang::where('status', 'open')
                    ->orderBy('created_at', 'desc')
                    ->paginate(9); // Tampilkan 9 lowongan per halaman (Grid 3x3)

        return view('landing', compact('vacancies'));
    }
    
    // Tampilkan Detail Lowongan untuk Publik
    public function show($id)
    {
        $vacancy = VacancyMagang::findOrFail($id);
        return view('landing-detail', compact('vacancy'));
    }
}