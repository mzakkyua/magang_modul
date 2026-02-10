<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\VacancyMagang;
use App\Models\MagangAccessRight;
use App\Models\ApplicationMagang;
use Illuminate\Support\Facades\Auth;
use App\Helpers\DashboardCache;

class VacancyMagangController extends Controller
{
    // =================================================================
    // 1. READ DATA (MENAMPILKAN DAFTAR LOWONGAN)
    // =================================================================
    public function index(Request $request)
    {
        $userId = Auth::id();
        $hakAkses = MagangAccessRight::where('user_id', $userId)->first();

        if (!$hakAkses) {
            abort(403, 'Anda tidak memiliki hak akses ke Modul Magang.');
        }

        $query = VacancyMagang::query();

        if ($hakAkses->role !== 'superadmin') {
            $query->where('division_name', $hakAkses->division_name);
        }

        $vacancies = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('admin.vacancies.index', compact('vacancies'));
    }

    // =================================================================
    // 2. CREATE DATA
    // =================================================================
    public function create()
    {
        return view('admin.vacancies.create');
    }

    public function store(Request $request)
    {
        $userId = Auth::id();
        $hakAkses = MagangAccessRight::where('user_id', $userId)->first();

        if (!$hakAkses) {
            abort(403, 'Akses Ditolak');
        }

        $divisiInput = ($hakAkses->role === 'superadmin')
            ? $request->division_name
            : $hakAkses->division_name;

        $request->validate([
            'title'             => 'required|string|max:200',
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

        $min  = $request->min_members;
        $max  = $request->max_members;
        $mode = $request->registration_mode;

        if ($mode === 'individu') {
            $min = 1;
            $max = 1;
        } elseif ($mode === 'kelompok') {
            if ($min < 2) {
                return back()->withInput()->withErrors([
                    'min_members' => 'Mode Kelompok wajib minimal 2 orang!'
                ]);
            }
            if ($max < $min) {
                return back()->withInput()->withErrors([
                    'max_members' => 'Maksimal anggota harus lebih besar dari minimal!'
                ]);
            }
        } elseif ($mode === 'hybrid') {
            if ($max < $min) {
                return back()->withInput()->withErrors([
                    'max_members' => 'Logic Error: Max < Min'
                ]);
            }
        }

        VacancyMagang::create([
            'title'             => $request->title,
            'division_name'     => $divisiInput,
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
        DashboardCache::clear();

        return redirect()->route('admin.vacancies.index')
            ->with('success', 'Lowongan berhasil dibuat!');
    }

    // =================================================================
    // 3. UPDATE DATA (LOWONGAN TERKUNCI JIKA SUDAH ADA PENDAFTAR)
    // =================================================================
    public function edit($id)
    {
        $vacancy = VacancyMagang::findOrFail($id);

        $userId = Auth::id();
        $hakAkses = MagangAccessRight::where('user_id', $userId)->first();

        if (
            $hakAkses->role !== 'superadmin'
            && $vacancy->division_name !== $hakAkses->division_name
        ) {
            abort(403, 'Anda tidak boleh mengedit lowongan divisi lain!');
        }

        $hasApplicant = ApplicationMagang::where('vacancy_id', $id)->exists();

        return view('admin.vacancies.edit', compact('vacancy', 'hasApplicant'));
    }

    public function update(Request $request, $id)
    {
        $vacancy = VacancyMagang::findOrFail($id);

        $request->validate([
            'title'       => 'required|string|max:200',
            'start_date'  => 'required|date',
            'end_date'    => 'required|date|after_or_equal:start_date',
        ]);

        $hasApplicant = ApplicationMagang::where('vacancy_id', $id)->exists();

        // Field yang SELALU boleh diubah
        $data = $request->only([
            'title',
            'description',
            'start_date',
            'end_date',
        ]);

        // Field aturan inti HANYA boleh diubah jika BELUM ADA PENDAFTAR
        if (!$hasApplicant) {
            $data = array_merge($data, $request->only([
                'registration_mode',
                'min_members',
                'max_members',
                'quota_slots',
            ]));
        }

        $vacancy->update($data);
        DashboardCache::clear();

        return redirect()->route('admin.vacancies.index')
            ->with('success', 'Lowongan berhasil diperbarui.');
    }

    // =================================================================
    // 4. MANUAL OPEN / CLOSE
    // =================================================================
    public function toggleStatus($id)
    {
        $vacancy = VacancyMagang::findOrFail($id);

        if ($vacancy->status === 'open') {
            $vacancy->status = 'closed';
            $msg = 'Lowongan berhasil ditutup.';
        } else {
            $vacancy->status = 'open';
            $msg = 'Lowongan berhasil dibuka kembali.';
        }

        $vacancy->save();
        DashboardCache::clear();

        return back()->with('success', $msg);
    }

    // =================================================================
    // 5. DELETE DATA (DISARANKAN JANGAN DIPAKAI JIKA SUDAH ADA PENDAFTAR)
    // =================================================================
    public function destroy($id)
    {
        $vacancy = VacancyMagang::findOrFail($id);

        $hasApplicant = ApplicationMagang::where('vacancy_id', $id)->exists();

        if ($hasApplicant) {
            return back()->withErrors([
                'error' => 'Lowongan tidak dapat dihapus karena sudah memiliki pendaftar.'
            ]);
        }

        $vacancy->delete();
        DashboardCache::clear();

        return redirect()->route('admin.vacancies.index')
            ->with('success', 'Lowongan berhasil dihapus.');
    }
}
