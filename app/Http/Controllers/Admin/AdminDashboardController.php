<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VacancyMagang;
use App\Models\ApplicationMagang;
use App\Models\UserMagang;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // KITA SIAPKAN DATA STATISTIK BIAR UI-NYA KEREN
        
        // 1. Total Lowongan Aktif
        $totalLowongan = VacancyMagang::where('status', 'open')->count();

        // 2. Total Pelamar Masuk (Belum diverifikasi)
        $perluVerifikasi = ApplicationMagang::where('status', 'pending')->count();

        // 3. Total Peserta Diterima (Sedang Magang)
        $sedangMagang = ApplicationMagang::where('status', 'accepted')->count();

        // 4. Statistik Peserta (Siswa SMK vs Mahasiswa)
        // Ini pake whereHas buat ngecek ke tabel profil
        $totalSiswa = UserMagang::whereHas('profile', function($q){
            $q->where('education_level', 'siswa_smk');
        })->count();
        
        $totalMahasiswa = UserMagang::whereHas('profile', function($q){
            $q->where('education_level', 'mahasiswa');
        })->count();

        return view('admin.dashboard.index', compact(
            'totalLowongan', 
            'perluVerifikasi', 
            'sedangMagang',
            'totalSiswa',
            'totalMahasiswa'
        ));
    }
}