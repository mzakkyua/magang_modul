<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\VacancyMagang;
use App\Models\MagangAccessRight;
use Illuminate\Support\Facades\Auth;
use App\Helpers\DashboardCache;

/**
 * =========================================================
 * CONTROLLER: VacancyMagangController
 * =========================================================
 * TANGGUNG JAWAB:
 * - CRUD Lowongan Magang
 * - Pengaturan kuota & mode pendaftaran
 * - Pembatasan akses berdasarkan divisi
 *
 * ATURAN BISNIS UTAMA:
 * - Admin divisi hanya boleh mengelola lowongan divisinya
 * - Lowongan yang sudah memiliki pendaftar:
 *   → Tidak boleh diubah mode & kuotanya
 * - Mode individu selalu memiliki 1 anggota
 *
 * DAMPAK SISTEM:
 * - Perubahan lowongan memengaruhi:
 *   - Dashboard
 *   - Proses pendaftaran
 *
 * POTENSI MIGRASI:
 * - Hak akses → Policy / Middleware
 * - Validasi bisnis → Service Layer
 * =========================================================
 */
class VacancyMagangController extends Controller
{
    /**
     * =====================================================
     * HELPER: getHakAkses()
     * =====================================================
     * TUJUAN:
     * - Mengambil SK penunjukan admin magang
     * - Digunakan di seluruh method controller
     *
     * SECURITY:
     * - Menghentikan request jika admin tidak sah
     * =====================================================
     */
    private function getHakAkses()
    {
        $hakAkses = MagangAccessRight::where('user_id', Auth::id())->first();

        if (!$hakAkses) {
            abort(403, 'Anda tidak memiliki akses ke Modul Magang.');
        }

        return $hakAkses;
    }

    /**
     * =====================================================
     * METHOD: index()
     * =====================================================
     * TUJUAN:
     * - Menampilkan daftar lowongan magang
     *
     * FITUR:
     * - Superadmin → Semua divisi
     * - Admin divisi → Hanya divisinya
     * - Menampilkan jumlah pendaftar per lowongan
     * =====================================================
     */
    public function index()
    {
        $hakAkses = $this->getHakAkses();

        $query = VacancyMagang::withCount('applications');

        if ($hakAkses->role !== 'superadmin') {
            $query->where('division_name', $hakAkses->division_name);
        }

        $vacancies = $query
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('admin.vacancies.index', compact('vacancies'));
    }

    /**
     * =====================================================
     * METHOD: create()
     * =====================================================
     * TUJUAN:
     * - Menampilkan form pembuatan lowongan baru
     * =====================================================
     */
    public function create()
    {
        return view('admin.vacancies.create');
    }

    /**
     * =====================================================
     * METHOD: store()
     * =====================================================
     * TUJUAN:
     * - Menyimpan lowongan magang baru
     *
     * ATURAN KHUSUS:
     * - Admin divisi tidak bisa memilih divisi lain
     * - Mode pendaftaran menentukan min/max anggota
     * =====================================================
     */
    public function store(Request $request)
    {
        $hakAkses = $this->getHakAkses();

        /* =====================================================
         * 1. VALIDASI INPUT
         * ===================================================== */
        $request->validate([
            'title'             => 'required|string|max:200',
            'type'              => 'required|in:magang,penelitian',
            'registration_mode' => 'required|in:individu,kelompok,hybrid',
            'quota_slots'       => 'required|integer|min:1',
            'start_date'        => 'required|date',
            'end_date'          => 'required|date|after_or_equal:start_date',
            'description'       => 'nullable|string',
            'min_members'       => 'nullable|integer|min:1',
            'max_members'       => 'nullable|integer|min:1',
        ]);

        /* =====================================================
         * 2. TENTUKAN DIVISI LOWONGAN
         * =====================================================
         * - Superadmin → bebas memilih divisi
         * - Admin divisi → otomatis sesuai SK
         * ===================================================== */
        $division = $hakAkses->role === 'superadmin'
            ? $request->division_name
            : $hakAkses->division_name;

        /* =====================================================
         * 3. ATUR MIN / MAX ANGGOTA
         * ===================================================== */
        [$min, $max] = $this->resolveMemberRange(
            $request->registration_mode,
            $request->min_members,
            $request->max_members
        );

        /* =====================================================
         * 4. SIMPAN LOWONGAN
         * ===================================================== */
        VacancyMagang::create([
            'title'             => $request->title,
            'division_name'     => $division,
            'type'              => $request->type,
            'registration_mode' => $request->registration_mode,
            'quota_slots'       => $request->quota_slots,
            'min_members'       => $min,
            'max_members'       => $max,
            'start_date'        => $request->start_date,
            'end_date'          => $request->end_date,
            'description'       => $request->description,
            'status'            => 'open',
        ]);

        // Bersihkan cache dashboard agar data konsisten
        DashboardCache::clear();

        return redirect()
            ->route('admin.vacancies.index')
            ->with('success', 'Lowongan berhasil dibuat.');
    }

    /**
     * =====================================================
     * METHOD: edit()
     * =====================================================
     * TUJUAN:
     * - Menampilkan form edit lowongan
     *
     * BATASAN:
     * - Admin hanya boleh edit lowongan divisinya
     * - Beberapa field dikunci jika sudah ada pendaftar
     * =====================================================
     */
    public function edit(VacancyMagang $vacancy)
    {
        $hakAkses = $this->getHakAkses();

        // Cegah admin lintas divisi
        if (
            $hakAkses->role !== 'superadmin' &&
            $vacancy->division_name !== $hakAkses->division_name
        ) {
            abort(403, 'Anda tidak boleh mengedit lowongan divisi lain.');
        }

        // Cek apakah sudah ada pendaftar
        $hasApplicant = $vacancy->applications()->exists();

        return view(
            'admin.vacancies.edit',
            compact('vacancy', 'hasApplicant', 'hakAkses')
        );
    }

    /**
     * =====================================================
     * METHOD: update()
     * =====================================================
     * TUJUAN:
     * - Memperbarui data lowongan
     *
     * ATURAN:
     * - Jika sudah ada pendaftar:
     *   → mode & kuota tidak bisa diubah
     * =====================================================
     */
    public function update(Request $request, VacancyMagang $vacancy)
    {
        $hakAkses = $this->getHakAkses();
        $hasApplicant = $vacancy->applications()->exists();

        /* =====================================================
         * 1. VALIDASI DASAR
         * ===================================================== */
        $request->validate([
            'title'       => 'required|string|max:200',
            'type'        => 'required|in:magang,penelitian',
            'start_date'  => 'required|date',
            'end_date'    => 'required|date|after_or_equal:start_date',
            'description' => 'nullable|string',
        ]);

        /* =====================================================
         * 2. DATA YANG SELALU BOLEH DIUBAH
         * ===================================================== */
        $data = [
            'title'         => $request->title,
            'type'          => $request->type,
            'description'   => $request->description,
            'start_date'    => $request->start_date,
            'end_date'      => $request->end_date,
            'division_name' => $hakAkses->role === 'superadmin'
                ? $request->division_name
                : $vacancy->division_name,
        ];

        /* =====================================================
         * 3. DATA YANG HANYA BOLEH DIUBAH JIKA BELUM ADA PENDAFTAR
         * ===================================================== */
        if (!$hasApplicant) {
            $request->validate([
                'registration_mode' => 'required|in:individu,kelompok,hybrid',
                'quota_slots'       => 'required|integer|min:1',
                'min_members'       => 'nullable|integer|min:1',
                'max_members'       => 'nullable|integer|min:1',
            ]);

            [$min, $max] = $this->resolveMemberRange(
                $request->registration_mode,
                $request->min_members,
                $request->max_members
            );

            $data = array_merge($data, [
                'registration_mode' => $request->registration_mode,
                'quota_slots'       => $request->quota_slots,
                'min_members'       => $min,
                'max_members'       => $max,
            ]);
        }

        $vacancy->update($data);

        // Bersihkan cache dashboard
        DashboardCache::clear();

        return redirect()
            ->route('admin.vacancies.index')
            ->with('success', 'Lowongan berhasil diperbarui.');
    }

    /**
     * =====================================================
     * METHOD: toggleStatus()
     * =====================================================
     * TUJUAN:
     * - Membuka / menutup lowongan
     * =====================================================
     */
    public function toggleStatus(VacancyMagang $vacancy)
    {
        $vacancy->update([
            'status' => $vacancy->status === 'open' ? 'closed' : 'open',
        ]);

        DashboardCache::clear();

        return back()->with('success', 'Status lowongan berhasil diubah.');
    }

    /**
     * =====================================================
     * METHOD: destroy()
     * =====================================================
     * TUJUAN:
     * - Menghapus lowongan
     *
     * BATASAN:
     * - Lowongan dengan pendaftar tidak boleh dihapus
     * =====================================================
     */
    public function destroy(VacancyMagang $vacancy)
    {
        if ($vacancy->applications()->exists()) {
            return back()->withErrors([
                'error' => 'Lowongan tidak dapat dihapus karena sudah memiliki pendaftar.',
            ]);
        }

        $vacancy->delete();

        DashboardCache::clear();

        return redirect()
            ->route('admin.vacancies.index')
            ->with('success', 'Lowongan berhasil dihapus.');
    }

    /**
     * =====================================================
     * HELPER: resolveMemberRange()
     * =====================================================
     * TUJUAN:
     * - Menentukan min & max anggota berdasarkan mode
     *
     * ATURAN:
     * - individu → min = max = 1
     * - kelompok/hybrid → max ≥ min
     * =====================================================
     */
    private function resolveMemberRange($mode, $min, $max)
    {
        if ($mode === 'individu') {
            return [1, 1];
        }

        if ($max < $min) {
            abort(422, 'Jumlah maksimal anggota harus ≥ minimal.');
        }

        return [$min, $max];
    }
}
