<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VacancyMagang;
use Illuminate\Support\Facades\Auth;

class DashboardMagangController extends Controller
{
    public function index()
    {
        // 1. AMBIL LIST LOWONGAN MAGANG (PKL)
        // Logic: Cari yang tipe 'magang' DAN statusnya 'open'
        $lowonganMagang = VacancyMagang::where('type', 'magang')
            ->where('status', 'open')
            ->orderBy('created_at', 'desc')
            ->get();

        // 2. AMBIL LIST LOWONGAN PENELITIAN
        // Logic: Cari yang tipe 'penelitian' DAN statusnya 'open'
        $lowonganPenelitian = VacancyMagang::where('type', 'penelitian')
            ->where('status', 'open')
            ->orderBy('created_at', 'desc')
            ->get();

        // 3. LOGIC TAMBAHAN: CEK USER SMK ATAU MAHASISWA?
        // (Opsional: Jika SMK tidak boleh lihat Penelitian, bisa difilter di sini)
        $user = Auth::guard('magang')->user();
        $isSMK = optional($user->profile)->education_level === 'siswa_smk';

        // Kirim 2 bungkusan data (Magang & Penelitian) ke View
        return view('magang.dashboard.index', [
            'vacancies' => $lowonganMagang,
            'vacancies' => $lowonganPenelitian,
             // Biar di frontend bisa diatur tampilannya
        ]);
    }
}
