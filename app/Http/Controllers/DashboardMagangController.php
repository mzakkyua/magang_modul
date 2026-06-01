<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache; // Tambahan untuk fitur Cache
use App\Models\VacancyMagang;
use App\Models\ApplicationMemberMagang;
use App\Models\Division;

class DashboardMagangController extends Controller
{

    /**
     * ============================================================
     * DASHBOARD PESERTA MAGANG
     * ============================================================
     *
     * Controller ini bertanggung jawab untuk menampilkan
     * dashboard utama peserta magang setelah login.
     *
     * Data yang ditampilkan pada dashboard:
     *
     * 1. Daftar lowongan MAGANG yang sedang open
     * 2. Daftar lowongan PENELITIAN yang sedang open
     * 3. Status apakah user adalah siswa SMK
     * 4. Hasil pencarian lowongan berdasarkan keyword
     *
     * Sistem menggunakan guard authentication:
     *
     * guard : magang
     *
     * sehingga user yang diambil adalah peserta magang
     * (mahasiswa, siswa SMK, peneliti, dll)
     *
     * ============================================================
     */

    /**
     * ============================================================
     * MENU NILAI
     * ============================================================
     * Menampilkan halaman nilai magang peserta yang sedang login.
     *
     * Relasi yang dipakai:
     * UserMagang → applicationMembers → application (accepted) → assessment
     */
    public function nilai()
    {
        $userId = Auth::guard('magang')->id();

        /**
         * PERUBAHAN: dari ->first() menjadi ->get() + ->latest()
         * agar semua periode magang (bukan hanya yang pertama)
         * ditampilkan, termasuk magang ke-2, ke-3, dst.
         *
         * Relasi yang di-eager load:
         * - assessment      : nilai per periode
         * - application.vacancy : info lowongan (judul, divisi, periode)
         */
        $memberRecords = ApplicationMemberMagang::where('user_id', $userId)
            ->whereHas('application', function ($q) {
                $q->whereIn('status', ['accepted', 'completed']);
            })
            ->with([
                'assessment',
                'application.vacancy:id,title,division_name,type,start_date,end_date',
            ])
            ->latest()
            ->get();

        // Mengarah ke folder resources/views/nilai/index.blade.php
        return view('magang.nilai.index', compact('memberRecords'));
    }

    public function index(Request $request)
    {

        $user = Auth::guard('magang')->user();

        $isSMK = optional($user->profile)->education_level === 'SMK';

        $search = trim($request->search);

        $baseQuery = VacancyMagang::query()
            ->where('status', 'open')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'LIKE', "%{$search}%")
                        ->orWhere('division_name', 'LIKE', "%{$search}%")
                        ->orWhere('description', 'LIKE', "%{$search}%");
                });
            });

        $vacanciesMagang = (clone $baseQuery)
            ->where('type', 'magang')
            ->latest()
            ->get();

        $vacanciesPenelitian = (clone $baseQuery)
            ->where('type', 'penelitian')
            ->latest()
            ->get();

        /**
         * ============================================================
         * STATS BAR DIGITAL (Menghitung Peserta, Divisi, Alumni)
         * Disimpan di Cache 10 menit agar query database tidak jebol
         * ============================================================
         */
        $globalStats = Cache::remember('global_stats_magang', now()->addMinutes(10), function () {
            return [
                // 1. Hitung Divisi Aktif
                'jumlahDivisi' => Division::active()->count(),

                // 2. Hitung Peserta Aktif (Member active + Aplikasi accepted)
                'pesertaAktif' => ApplicationMemberMagang::where('individual_status', 'active')
                    ->whereHas('application', function ($q) {
                        $q->where('status', 'accepted');
                    })->count(),

                // 3. Hitung Alumni (Member finished + Aplikasi completed)
                'alumniMagang' => ApplicationMemberMagang::where('individual_status', 'finished')
                    ->whereHas('application', function ($q) {
                        $q->where('status', 'completed');
                    })->count(),
            ];
        });

        return view('magang.dashboard.index', compact(
            'vacanciesMagang',
            'vacanciesPenelitian',
            'isSMK',
            'search',
            'globalStats' // <- Data dikirim ke view Blade
        ));
    }

    public function show(int $id)
    {
        $vacancy = VacancyMagang::where('id', $id)
            ->where('status', 'open')
            ->firstOrFail();

        // Mengarah ke view gabungan yang ada di folder landing
        return view('landing.show', compact('vacancy'));
    }
}
