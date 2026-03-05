<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ApplicationMagang;

class StatusMagangController extends Controller
{
    /**
     * Menampilkan riwayat status lamaran peserta.
     */
    public function index()
    {
        // 1. Ambil data user yang sedang login dengan guard magang
        $user = Auth::guard('magang')->user();

        // 2. Ambil data lamaran milik user tersebut
        // Kita gunakan 'with' untuk me-load data lowongan (vacancy) agar tidak berat (Eager Loading)
        $applications = ApplicationMagang::where('leader_user_id', $user->id)
            ->with('vacancy') 
            ->orderBy('submission_date', 'desc')
            ->get();

        // 3. Kirim data ke view
        return view('magang.status.index', compact('applications'));
    }
}