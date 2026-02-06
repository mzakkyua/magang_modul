<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VacancyMagang;
use App\Models\ApplicationMagang;
use App\Models\ApplicationMemberMagang;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ApplicationMagangController extends Controller
{
    // Function Utama: Menyimpan Data Pendaftaran
    public function store(Request $request)
    {
        // 1. Validasi Input Dasar
        // Pastikan user ngirim data yang benar
        $request->validate([
            'vacancy_id' => 'required|exists:vacancies_magang,id',
            'research_title' => 'nullable|string|max:255',
            'research_abstract' => 'nullable|string', // Revisi Abstrak
            'members' => 'nullable|array', // List teman (bisa kosong kalau individu)
            'members.*' => 'exists:users_magang,id', // Cek apakah teman terdaftar di sistem
        ]);

        // Ambil User yang sedang Login (Si Ketua)
        $leaderId = Auth::guard('magang')->id();
        $vacancyId = $request->vacancy_id;
        
        // Gabungkan Ketua + Anggota untuk dihitung totalnya
        // (Ketua wajib dihitung sebagai 1 orang)
        $memberIds = $request->members ?? []; 
        array_unshift($memberIds, $leaderId); // Masukkan Ketua ke antrian pertama
        $totalOrang = count($memberIds);

        // =================================================================
        // MULAI TRANSAKSI DATABASE (Bungkus semua proses jadi satu paket)
        // Kalau ada satu error di tengah, semua batal disimpan (Rollback)
        // =================================================================
        return DB::transaction(function () use ($request, $leaderId, $vacancyId, $memberIds, $totalOrang) {
            
            // A. KUNCI DATA LOWONGAN (PENTING!)
            // lockForUpdate() ibarat kita pegang kunci toilet. 
            // Orang lain harus antri tunggu kita selesai proses cek kuota.
            $vacancy = VacancyMagang::where('id', $vacancyId)->lockForUpdate()->first();

            // ---------------------------------------------------------
            // LOGIC 1: Cek Status Lowongan (Buka/Tutup Admin)
            // ---------------------------------------------------------
            if ($vacancy->status !== 'open') {
                throw new \Exception('Maaf, pendaftaran untuk lowongan ini sedang ditutup oleh Admin.');
            }

            // ---------------------------------------------------------
            // LOGIC 2: Cek Aturan Minimal & Maksimal Anggota
            // ---------------------------------------------------------
            if ($totalOrang < $vacancy->min_members) {
                throw new \Exception("Gagal! Lowongan ini mewajibkan minimal {$vacancy->min_members} orang per kelompok.");
            }
            if ($totalOrang > $vacancy->max_members) {
                throw new \Exception("Gagal! Lowongan ini membatasi maksimal {$vacancy->max_members} orang per kelompok.");
            }

            // ---------------------------------------------------------
            // LOGIC 3: Cek Tipe Penelitian (Wajib Judul & Abstrak)
            // ---------------------------------------------------------
            if ($vacancy->type === 'penelitian') {
                if (empty($request->research_title) || empty($request->research_abstract)) {
                    throw new \Exception('Untuk jalur Penelitian, Judul dan Abstrak wajib diisi!');
                }
            }

            // ---------------------------------------------------------
            // LOGIC 4: Cek "SATU KAKI" (Apakah ada yang nyangkut?)
            // ---------------------------------------------------------
            foreach ($memberIds as $userId) {
                if ($this->isUserBusy($userId)) {
                    // Cari nama user biar pesannya enak dibaca
                    $nama = \App\Models\UserMagang::find($userId)->username;
                    throw new \Exception("Gagal! User atas nama '$nama' masih terdaftar di pengajuan lain yang sedang aktif.");
                }
            }

            // ---------------------------------------------------------
            // LOGIC 5: Hitung Sisa Kuota (Query Count SQL)
            // ---------------------------------------------------------
            // Hitung aplikasi yg statusnya BUKAN rejected
            $terpakai = ApplicationMagang::where('vacancy_id', $vacancyId)
                        ->whereIn('status', ['pending', 'verified', 'interview', 'accepted'])
                        ->count();

            if (($vacancy->quota_slots - $terpakai) <= 0) {
                throw new \Exception('Mohon maaf, Kuota pendaftaran baru saja penuh!');
            }

            // ---------------------------------------------------------
            // SAVE 1: Simpan Header Aplikasi (Map Berkas)
            // ---------------------------------------------------------
            $app = ApplicationMagang::create([
                'vacancy_id' => $vacancyId,
                'leader_user_id' => $leaderId,
                'research_title' => $request->research_title,
                'research_abstract' => $request->research_abstract, // Simpan Abstrak
                'status' => 'pending' // Default status
            ]);

            // ---------------------------------------------------------
            // SAVE 2: Simpan Detail Anggota (Looping)
            // ---------------------------------------------------------
            foreach ($memberIds as $userId) {
                ApplicationMemberMagang::create([
                    'application_id' => $app->id,
                    'user_id' => $userId,
                    'individual_status' => 'active'
                ]);
            }

            // Kalau sukses sampai sini, return pesan berhasil
            return redirect()->route('dashboard')->with('success', 'Pendaftaran Berhasil! Silakan tunggu verifikasi admin.');

        }); // Akhir Transaction
    }

    // ---------------------------------------------------------
    // HELPER: Fungsi Cek Apakah User Sedang Sibuk?
    // ---------------------------------------------------------
    private function isUserBusy($userId)
    {
        // Join tabel Member ke Header Aplikasi
        // Cari user ini di aplikasi yang statusnya Masih Aktif
        return DB::table('application_members_magang as m')
            ->join('applications_magang as a', 'm.application_id', '=', 'a.id')
            ->where('m.user_id', $userId)
            ->whereIn('a.status', ['pending', 'verified', 'interview', 'accepted'])
            ->exists(); // True jika ketemu, False jika aman
    }
}