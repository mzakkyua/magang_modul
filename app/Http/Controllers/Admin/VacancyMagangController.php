<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\VacancyMagang;
use App\Models\MagangAccessRight; // Pastikan model ini sudah ada
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class VacancyMagangController extends Controller
{
    // =================================================================
    // 1. READ DATA (MENAMPILKAN DAFTAR LOWONGAN)
    // =================================================================
    public function index(Request $request)
    {
        // 1. Ambil ID User yang sedang login (Login Pegawai)
        $userId = Auth::id();

        // 2. Cek Jabatan dia di tabel SK Magang (Access Rights)
        $hakAkses = MagangAccessRight::where('user_id', $userId)->first();

        // 3. Safety Check: Kalau dia login tapi gak punya hak akses (Penyusup)
        if (!$hakAkses) {
            abort(403, 'Anda tidak memiliki hak akses ke Modul Magang.');
        }

        // 4. Mulai Query Lowongan
        $query = VacancyMagang::query();

        // 5. LOGIC FILTER (CLUSTER DIVISI)
        // Cek Role dari tabel $hakAkses, BUKAN dari tabel User
        if ($hakAkses->role !== 'superadmin') {
            // Filter: Hanya tampilkan lowongan milik divisi dia (misal: IT)
            $query->where('division_name', $hakAkses->division_name);
        }

        // 6. Urutkan dan Paginate (10 per halaman)
        $vacancies = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('admin.vacancies.index', compact('vacancies'));
    }

    // =================================================================
    // 2. CREATE DATA (MEMBUAT LOWONGAN BARU)
    // =================================================================
    
    // A. Menampilkan Form Pembuatan
    public function create()
    {
        return view('admin.vacancies.create');
    }

    // B. Proses Menyimpan Data ke Database
    public function store(Request $request)
    {
        // Ambil User & Hak Aksesnya
        $userId = Auth::id();
        $hakAkses = MagangAccessRight::where('user_id', $userId)->first();
        
        if (!$hakAkses) {
            abort(403, 'Akses Ditolak');
        }

        // Tentukan Divisi: 
        // Kalau Superadmin -> Pakai inputan form (Bebas pilih).
        // Kalau Admin Bidang -> Pakai data divisi dia sendiri (Otomatis).
        $divisiInput = ($hakAkses->role === 'superadmin') ? $request->division_name : $hakAkses->division_name;

        // -------------------------------------------------------------
        // LANGKAH 1: VALIDASI INPUT
        // -------------------------------------------------------------
        $request->validate([
            'title'             => 'required|string|max:200',
            // division_name wajib kalau superadmin, tapi kalau admin bidang boleh null (krn otomatis)
            'division_name'     => ($hakAkses->role === 'superadmin' ? 'required|string|max:100' : 'nullable'),
            'type'              => 'required|in:magang,penelitian',
            'registration_mode' => 'required|in:individu,kelompok,hybrid',
            'quota_slots'       => 'required|integer|min:1',
            
            'start_date'        => 'required|date',
            'end_date'          => 'required|date|after_or_equal:start_date',
            
            'description'       => 'nullable|string',
            
            'min_members'       => 'nullable|integer|min:1',
            'max_members'       => 'nullable|integer|min:1',
        ]);

        // -------------------------------------------------------------
        // LANGKAH 2: LOGIC "ANTI CURANG" (MIN & MAX ANGGOTA)
        // -------------------------------------------------------------
        $min = $request->min_members;
        $max = $request->max_members;
        $mode = $request->registration_mode;

        // KASUS A: INDIVIDU
        if ($mode === 'individu') {
            $min = 1;
            $max = 1;
        }
        // KASUS B: KELOMPOK
        elseif ($mode === 'kelompok') {
            if ($min < 2) {
                return back()->withInput()->withErrors(['min_members' => 'Mode Kelompok wajib minimal 2 orang!']);
            }
            if ($max < $min) {
                return back()->withInput()->withErrors(['max_members' => 'Maksimal anggota harus lebih besar dari minimal!']);
            }
        }
        // KASUS C: HYBRID
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
            'division_name'     => $divisiInput, // <--- Pakai Variabel Otomatis Tadi
            'type'              => $request->type,
            'registration_mode' => $mode,
            'quota_slots'       => $request->quota_slots,
            'min_members'       => $min, 
            'max_members'       => $max,
            'start_date'        => $request->start_date,
            'end_date'          => $request->end_date,
            'description'       => $request->description,
            'status'            => 'open',
        ]);

        return redirect()->route('admin.vacancies.index')->with('success', 'Lowongan berhasil dibuat!');
    }

    // =================================================================
    // 3. UPDATE DATA (EDIT LOWONGAN)
    // =================================================================
    public function edit($id)
    {
        $vacancy = VacancyMagang::findOrFail($id);
        
        // (Opsional) Cek Hak Akses Edit
        // Kalau Admin IT coba edit lowongan Keuangan -> Tolak
        $userId = Auth::id();
        $hakAkses = MagangAccessRight::where('user_id', $userId)->first();
        
        if ($hakAkses->role !== 'superadmin' && $vacancy->division_name !== $hakAkses->division_name) {
            abort(403, 'Anda tidak boleh mengedit lowongan divisi lain!');
        }

        return view('admin.vacancies.edit', compact('vacancy'));
    }

    public function update(Request $request, $id)
    {
        $vacancy = VacancyMagang::findOrFail($id);

        $request->validate([
            'title'         => 'required|string|max:200',
            'quota_slots'   => 'required|integer|min:1',
            'start_date'    => 'required|date',
            'end_date'      => 'required|date|after_or_equal:start_date',
        ]);
        
        $vacancy->update($request->except(['_token', '_method']));
        
        return redirect()->route('admin.vacancies.index')->with('success', 'Lowongan diperbarui.');
    }

    // =================================================================
    // 4. MANUAL CLOSE (SAKLAR ADMIN)
    // =================================================================
    public function toggleStatus($id)
    {
        $vacancy = VacancyMagang::findOrFail($id);

        if ($vacancy->status === 'open') {
            $vacancy->status = 'closed';
            $msg = 'Sukses! Lowongan ditutup.';
        } else {
            $vacancy->status = 'open';
            $msg = 'Sukses! Lowongan dibuka kembali.';
        }

        $vacancy->save();

        return back()->with('success', $msg);
    }
    
    // =================================================================
    // 5. DELETE DATA
    // =================================================================
    public function destroy($id)
    {
        $vacancy = VacancyMagang::findOrFail($id);
        $vacancy->delete();
        
        return redirect()->route('admin.vacancies.index')->with('success', 'Lowongan berhasil dihapus.');
    }
}