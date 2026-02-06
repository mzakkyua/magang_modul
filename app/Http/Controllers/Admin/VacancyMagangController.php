<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\VacancyMagang; // Panggil Model Lowongan
use Illuminate\Support\Facades\DB;

class VacancyMagangController extends Controller
{
    // =================================================================
    // 1. READ DATA (MENAMPILKAN DAFTAR LOWONGAN)
    // =================================================================
    // Fungsi ini dipanggil saat Admin membuka menu "Daftar Lowongan"
    public function index(Request $request)
    {
        // Ambil data dari database
        // orderBy('created_at', 'desc') -> Biar lowongan terbaru muncul paling atas
        // paginate(10) -> Biar gak berat, kita tampilkan 10 baris per halaman
        $vacancies = VacancyMagang::query()
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Kirim data ($vacancies) ke tampilan (View)
        return view('admin.vacancies.index', compact('vacancies'));
    }

    // =================================================================
    // 2. CREATE DATA (MEMBUAT LOWONGAN BARU)
    // =================================================================
    
    // A. Menampilkan Form Pembuatan
    public function create()
    {
        // Hanya return view kosong berisi form inputan
        return view('admin.vacancies.create');
    }

    // B. Proses Menyimpan Data ke Database (JANTUNG LOGIC DI SINI)
    public function store(Request $request)
    {
        // -------------------------------------------------------------
        // LANGKAH 1: VALIDASI INPUT DASAR
        // Pastikan Admin tidak mengosongkan kolom wajib
        // -------------------------------------------------------------
        $request->validate([
            'title'             => 'required|string|max:200',
            'division_name'     => 'required|string|max:100', // Admin Bidang ketik divisinya
            'type'              => 'required|in:magang,penelitian', // Harus pilih salah satu
            'registration_mode' => 'required|in:individu,kelompok,hybrid',
            'quota_slots'       => 'required|integer|min:1', // Kuota minimal 1
            
            // Validasi Tanggal: Tanggal Selesai TIDAK BOLEH sebelum Tanggal Mulai
            'start_date'        => 'required|date',
            'end_date'          => 'required|date|after_or_equal:start_date',
            
            'description'       => 'nullable|string',
            
            // Validasi Anggota (Nullable dulu, nanti dicek logic di bawah)
            'min_members'       => 'nullable|integer|min:1',
            'max_members'       => 'nullable|integer|min:1',
        ]);

        // -------------------------------------------------------------
        // LANGKAH 2: LOGIC "ANTI CURANG" (MIN & MAX ANGGOTA)
        // Kita atur aturan main berdasarkan Mode Pendaftaran yang dipilih Admin
        // -------------------------------------------------------------
        
        // Ambil inputan admin sementara
        $min = $request->min_members;
        $max = $request->max_members;
        $mode = $request->registration_mode;

        // KASUS A: Jika Mode INDIVIDU
        // Atur paksa jadi 1 orang. Gak peduli admin input berapa.
        if ($mode === 'individu') {
            $min = 1;
            $max = 1;
        }
        
        // KASUS B: Jika Mode KELOMPOK
        // Minimal WAJIB 2 orang. Kalau 1 orang itu namanya Individu dong.
        elseif ($mode === 'kelompok') {
            // Cek Error: Minimal kureng dari 2?
            if ($min < 2) {
                return back()->withInput()->withErrors(['min_members' => 'Mode Kelompok wajib minimal 2 orang!']);
            }
            // Cek Error: Maksimal lebih kecil dari Minimal? (Gak logis)
            if ($max < $min) {
                return back()->withInput()->withErrors(['max_members' => 'Maksimal anggota harus lebih besar dari minimal!']);
            }
        }
        
        // KASUS C: Jika Mode HYBRID
        // Bebas, tapi logika angka (Max >= Min) tetap harus jalan
        elseif ($mode === 'hybrid') {
            if ($max < $min) {
                return back()->withInput()->withErrors(['max_members' => 'Logic Error: Max < Min']);
            }
        }

        // -------------------------------------------------------------
        // LANGKAH 3: SIMPAN KE DATABASE
        // -------------------------------------------------------------
        VacancyMagang::create([
            'title'             => $request->title,
            'division_name'     => $request->division_name,
            'type'              => $request->type,
            'registration_mode' => $mode,
            'quota_slots'       => $request->quota_slots,
            
            // PENTING: Simpan nilai $min & $max yang sudah lolos Logic di atas
            'min_members'       => $min, 
            'max_members'       => $max,
            
            'start_date'        => $request->start_date,
            'end_date'          => $request->end_date,
            'description'       => $request->description,
            'status'            => 'open', // Default langsung Buka
        ]);

        // Kembali ke halaman index dengan pesan sukses
        return redirect()->route('admin.vacancies.index')->with('success', 'Lowongan berhasil dibuat!');
    }

    // =================================================================
    // 3. UPDATE DATA (EDIT LOWONGAN)
    // =================================================================

    // A. Menampilkan Form Edit (Isinya data lama)
    public function edit($id)
    {
        // Cari data berdasarkan ID. Kalau gak ketemu, tampilkan Error 404 (findOrFail)
        $vacancy = VacancyMagang::findOrFail($id);
        
        return view('admin.vacancies.edit', compact('vacancy'));
    }

    // B. Proses Simpan Perubahan
    public function update(Request $request, $id)
    {
        // Cari data lama dulu
        $vacancy = VacancyMagang::findOrFail($id);

        // 1. Validasi Input (Mirip store, tapi sesuaikan kebutuhan)
        $request->validate([
            'title'         => 'required|string|max:200',
            'quota_slots'   => 'required|integer|min:1',
            'start_date'    => 'required|date',
            'end_date'      => 'required|date|after_or_equal:start_date',
            // ... (Tambahkan validasi lain jika perlu)
        ]);
        
        // 2. Update Data
        // $request->except(...) artinya ambil semua input KECUALI token & method
        $vacancy->update($request->except(['_token', '_method']));
        
        // Note: Jika di form edit Admin boleh ubah mode (Individu/Kelompok),
        // Logic Min/Max di function store() wajib di-copy paste ke sini juga!

        return redirect()->route('admin.vacancies.index')->with('success', 'Lowongan berhasil diperbarui.');
    }

    // =================================================================
    // 4. FITUR SPESIAL: MANUAL CLOSE (SAKLAR ADMIN)
    // =================================================================
    // Fungsi ini buat menutup pendaftaran mendadak walau tanggal masih berlaku
    
    public function toggleStatus($id)
    {
        $vacancy = VacancyMagang::findOrFail($id);

        // Cek Status Sekarang, lalu balikkan kondisinya
        if ($vacancy->status === 'open') {
            // Kalau lagi Buka -> Tutup
            $vacancy->status = 'closed';
            $msg = 'Sukses! Lowongan ditutup. Tombol daftar disembunyikan.';
        } else {
            // Kalau lagi Tutup -> Buka
            $vacancy->status = 'open';
            $msg = 'Sukses! Lowongan dibuka kembali.';
        }

        $vacancy->save(); // Simpan perubahan status

        return back()->with('success', $msg); // Refresh halaman
    }
    
    // =================================================================
    // 5. DELETE DATA (HAPUS LOWONGAN)
    // =================================================================
    public function destroy($id)
    {
        $vacancy = VacancyMagang::findOrFail($id);
        
        // Hapus data
        $vacancy->delete();
        
        // Note: Karena kita pakai foreign key 'CASCADE' di migration,
        // Semua data pelamar di lowongan ini juga otomatis ikut terhapus.
        
        return redirect()->route('admin.vacancies.index')->with('success', 'Lowongan berhasil dihapus permanen.');
    }
}