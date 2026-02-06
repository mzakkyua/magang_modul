<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\VacancyMagang;
use App\Models\ApplicationMagang;
use App\Models\UserMagang;
use App\Models\MagangAccessRight; // <--- PENTING: Panggil Model SK
use Illuminate\Support\Facades\Auth;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // 1. Ambil ID User yang sedang Login (Pegawai)
        $userId = Auth::id();

        // 2. Cek Jabatan dia di tabel SK Magang
        $hakAkses = MagangAccessRight::where('user_id', $userId)->first();

        // 3. Safety Check: Kalau tidak punya hak akses, tendang keluar
        if (!$hakAkses) {
            abort(403, 'Akses Ditolak: Anda tidak terdaftar sebagai Admin Magang.');
        }

        // 4. Siapkan Query Dasar (Belum dieksekusi)
        $lowonganQuery = VacancyMagang::where('status', 'open');
        $pelamarQuery  = ApplicationMagang::where('status', 'pending');
        $magangQuery   = ApplicationMagang::where('status', 'accepted');

        // 5. --- LOGIC FILTER (KHUSUS ADMIN BIDANG) ---
        // Jika rolenya BUKAN 'superadmin', kita persempit datanya
        if ($hakAkses->role !== 'superadmin') {
            
            // A. Filter Lowongan: Cuma hitung lowongan divisi dia (misal: IT)
            $lowonganQuery->where('division_name', $hakAkses->division_name);
            
            // B. Filter Pelamar: Cuma hitung pelamar yang melamar ke lowongan IT
            $pelamarQuery->whereHas('vacancy', function($q) use ($hakAkses) {
                $q->where('division_name', $hakAkses->division_name);
            });

            // C. Filter Anak Magang: Cuma hitung yang diterima di lowongan IT
            $magangQuery->whereHas('vacancy', function($q) use ($hakAkses) {
                $q->where('division_name', $hakAkses->division_name);
            });
        }
        // ---------------------------------------------

        // 6. Eksekusi Hitung Data (COUNT)
        $totalLowongan   = $lowonganQuery->count();
        $perluVerifikasi = $pelamarQuery->count();
        $sedangMagang    = $magangQuery->count();

        // 7. Statistik Peserta (Siswa SMK vs Mahasiswa)
        // Note: Ini kita biarkan Global dulu agar terlihat ramai, 
        // karena User Magang itu akunnya global (belum tentu terikat divisi kalau belum daftar).
        $totalSiswa = UserMagang::whereHas('profile', function($q){
            $q->where('education_level', 'siswa_smk');
        })->count();
        
        $totalMahasiswa = UserMagang::whereHas('profile', function($q){
            $q->where('education_level', 'mahasiswa');
        })->count();

        // 8. Kirim ke View
        return view('admin.dashboard.index', compact(
            'totalLowongan', 
            'perluVerifikasi', 
            'sedangMagang',
            'totalSiswa',
            'totalMahasiswa'
        ));
    }
}