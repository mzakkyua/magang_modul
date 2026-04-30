<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\UserMagang;
use App\Models\Certificate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\MagangAccessRight;

/**
 * ======================================================================
 * CONTROLLER: CertificateController
 * ======================================================================
 *
 * Menangani upload, download, dan view sertifikat peserta magang.
 *
 * IMPROVEMENT DARI VERSI SEBELUMNYA:
 *
 * 🔴 CRITICAL
 *   - [FIX] Filename di-randomize dengan Str::uuid()
 *   - [FIX] File lama otomatis dihapus saat admin upload ulang (replace)
 *   - [FIX] File disimpan di storage private (anti IDOR)
 *   - [FIX] download() dan view() pakai route model binding (Certificate $certificate)
 *           → Laravel otomatis 404 jika ID tidak ada, tidak perlu findOrFail() manual
 *
 * 🟠 PERFORMANCE
 *   - [FIX] Query eligible users di create() dengan eager load lengkap
 *
 * 🟡 SECURITY
 *   - [FIX] Validasi MIME ganda (extension + real MIME type)
 *   - [FIX] Audit log di semua operasi sensitif
 *
 * ======================================================================
 */
class CertificateController extends Controller
{

    // ======================================================================
    // KONFIGURASI
    // ======================================================================

    private const STORAGE_DISK      = 'local';
    private const STORAGE_PATH      = 'certificates';
    private const ALLOWED_MIMES     = 'pdf,jpg,jpeg,png';
    private const ALLOWED_MIMETYPES = 'application/pdf,image/jpeg,image/png';
    private const MAX_SIZE_KB       = 5120; // 5 MB


    // ======================================================================
    // ADMIN — CREATE FORM
    // ======================================================================

    public function create()
    {
        $adminId  = Auth::id();
        $hakAkses = request()->attributes->get('magang_access');

        $usersQuery = UserMagang::whereHas('applicationMembers.application', function ($q) {
            $q->whereIn('status', ['accepted', 'completed']);
        })
            ->whereHas('applicationMembers.assessment');

        if ($hakAkses && !$hakAkses->isSuperAdmin()) {
            $usersQuery->whereHas('applicationMembers.application.vacancy', function ($q) use ($hakAkses) {
                $q->where('division_name', $hakAkses->division_name);
            });
        }

        $users = $usersQuery->with([
            'profile',
            'applicationMembers.assessment',
            'applicationMembers.application.vacancy',
            'certificates',
        ])->get();

        return view('admin.certificate.create', compact('users'));
    }


    // ======================================================================
    // ADMIN — STORE (UPLOAD)
    // ======================================================================

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users_magang,id',
            'title'   => 'required|string|max:255',
            'file'    => [
                'required',
                'file',
                'mimes:'     . self::ALLOWED_MIMES,
                'mimetypes:' . self::ALLOWED_MIMETYPES,
                'max:'       . self::MAX_SIZE_KB,
            ],
        ]);

        $uploadedFile = $request->file('file');
        $filename     = Str::uuid() . '.' . $uploadedFile->getClientOriginalExtension();
        $filePath     = $uploadedFile->storeAs(self::STORAGE_PATH, $filename, self::STORAGE_DISK);

        $existing = Certificate::where('user_id', $request->user_id)->latest()->first();

        if ($existing) {
            $this->deleteFileIfExists($existing->file);
        }

        $cert = Certificate::updateOrCreate(
            ['user_id' => $request->user_id],
            [
                'title'                => $request->title,
                'file'                 => $filePath,
                'uploaded_by_admin_id' => Auth::id(),
                'uploaded_at'          => now(),
                'replaced_at'          => $existing ? now() : null,
            ]
        );

        Log::info('Sertifikat diupload oleh admin', [
            'admin_id'       => Auth::id(),
            'user_id'        => $request->user_id,
            'certificate_id' => $cert->id,
            'filename'       => $filename,
            'replaced'       => $existing ? true : false,
            'timestamp'      => now()->toDateTimeString(),
        ]);

        return back()->with('success', 'Sertifikat berhasil diupload.');
    }


    // ======================================================================
    // USER (PEMAGANG) — INDEX
    // ======================================================================

    public function index()
    {
        $userId       = Auth::guard('magang')->id();
        $certificates = Certificate::where('user_id', $userId)->latest()->get();

        return view('magang.sertifikat.index', compact('certificates'));
    }


    // ======================================================================
    // USER (PEMAGANG) — DOWNLOAD
    // ======================================================================

    /**
     * IMPROVEMENT: Pakai route model binding Certificate $certificate
     * → Laravel otomatis throw 404 jika sertifikat tidak ditemukan
     * → Tidak perlu findOrFail() manual
     * → Ownership check tetap ada di bawah
     */
    public function download(Certificate $certificate)
    {
        $cert   = $certificate; // alias agar kode di bawah tidak perlu diubah
        $userId = Auth::guard('magang')->id();

        if ((int) $cert->user_id !== (int) $userId) {
            Log::warning('Percobaan download sertifikat tidak sah', [
                'requester_id'   => $userId,
                'certificate_id' => $cert->id,
                'owner_id'       => $cert->user_id,
            ]);
            abort(403, 'Akses ditolak: Ini bukan sertifikat Anda.');
        }

        if (!Storage::disk(self::STORAGE_DISK)->exists($cert->file)) {
            abort(404, 'Mohon maaf, file sertifikat tidak ditemukan di server.');
        }

        Log::info('Sertifikat didownload', [
            'user_id'        => $userId,
            'certificate_id' => $cert->id,
            'timestamp'      => now()->toDateTimeString(),
        ]);

        $fullPath = Storage::disk(self::STORAGE_DISK)->path($cert->file);
        $filename = $cert->title . '.' . pathinfo($cert->file, PATHINFO_EXTENSION);

        return response()->download($fullPath, $filename);
    }


    // ======================================================================
    // USER (PEMAGANG) — VIEW (INLINE DI BROWSER)
    // ======================================================================

    /**
     * IMPROVEMENT: Pakai route model binding Certificate $certificate
     * → Konsisten dengan download()
     */
    public function view(Certificate $certificate)
    {
        $cert   = $certificate;
        $userId = Auth::guard('magang')->id();

        if ((int) $cert->user_id !== (int) $userId) {
            Log::warning('Percobaan view sertifikat tidak sah', [
                'requester_id'   => $userId,
                'certificate_id' => $cert->id,
                'owner_id'       => $cert->user_id,
            ]);
            abort(403, 'Akses ditolak: Ini bukan sertifikat Anda.');
        }

        if (!Storage::disk(self::STORAGE_DISK)->exists($cert->file)) {
            abort(404, 'Mohon maaf, file sertifikat tidak ditemukan di server.');
        }

        return response()->file(
            Storage::disk(self::STORAGE_DISK)->path($cert->file)
        );
    }


    // ======================================================================
    // PRIVATE HELPERS
    // ======================================================================

    private function deleteFileIfExists(?string $filePath): void
    {
        if ($filePath && Storage::disk(self::STORAGE_DISK)->exists($filePath)) {
            Storage::disk(self::STORAGE_DISK)->delete($filePath);
        }
    }
}
