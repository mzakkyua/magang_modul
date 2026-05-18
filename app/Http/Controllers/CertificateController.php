<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ApplicationMemberMagang;
use App\Models\UserMagang;
use App\Models\Certificate;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

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
        $hakAkses = request()->attributes->get('magang_access');

        /**
         * Query ApplicationMemberMagang (bukan UserMagang lagi).
         * Satu baris member = satu slot untuk sertifikat.
         *
         * Kondisi yang HARUS terpenuhi sebelum sertifikat bisa diterbitkan:
         *   1. Status application = 'completed' (magang sudah diselesaikan)
         *   2. Sudah ada assessment (nilai sudah diinput)
         *
         * Status 'accepted' tidak masuk — peserta masih aktif magang,
         * belum layak menerima sertifikat.
         */
        $membersQuery = ApplicationMemberMagang::whereHas('application', function ($q) {
            $q->where('status', 'completed');   // ← hanya yang sudah selesai
        })
            ->whereHas('assessment');           // ← dan sudah dinilai

        // Admin divisi hanya lihat divisinya
        if ($hakAkses && !$hakAkses->isSuperAdmin()) {
            $membersQuery->whereHas('application.vacancy', function ($q) use ($hakAkses) {
                $q->where('division_name', $hakAkses->division_name);
            });
        }

        $members = $membersQuery->with([
            'user.profile',
            'application.vacancy',
            'assessment',
            'certificate',
        ])->get();

        return view('admin.certificate.create', compact('members'));
    }


    // ======================================================================
    // ADMIN — STORE (UPLOAD)
    // ======================================================================

    public function store(Request $request)
    {
        $request->validate([
            'application_member_id' => 'required|exists:application_members_magang,id',
            'title'                 => 'required|string|max:255',
            'file'                  => [
                'required',
                'file',
                'mimes:'     . self::ALLOWED_MIMES,
                'mimetypes:' . self::ALLOWED_MIMETYPES,
                'max:'       . self::MAX_SIZE_KB,
            ],
        ]);

        /**
         * ==============================================================
         * AMBIL DATA MEMBER
         * ==============================================================
         */
        $member = ApplicationMemberMagang::with('application.vacancy')
            ->findOrFail($request->application_member_id);

        /**
         * ==============================================================
         * GUARD: STATUS HARUS COMPLETED
         * ==============================================================
         */
        if ($member->application->status !== 'completed') {

            return back()
                ->withInput()
                ->with(
                    'error',
                    'Sertifikat hanya dapat diterbitkan setelah magang diselesaikan (status: Completed). Peserta ini masih berstatus aktif.'
                );
        }

        /**
         * ==============================================================
         * CEK SERTIFIKAT EXISTING
         * ==============================================================
         */
        $existing = Certificate::where(
            'application_member_id',
            $member->id
        )->first();

        /**
         * ==============================================================
         * VARIABLE FILE
         * ==============================================================
         *
         * $newFilePath
         * → file baru yang diupload
         *
         * $oldFileToDelete
         * → file lama yang akan dihapus
         *   setelah DB transaction sukses
         */
        $newFilePath = null;
        $oldFileToDelete = null;

        try {

            /**
             * ==========================================================
             * UPLOAD FILE BARU
             * ==========================================================
             *
             * Upload dilakukan sebelum transaction.
             * Jika DB gagal → file akan di-cleanup di catch.
             */
            $uploadedFile = $request->file('file');

            $filename = Str::uuid() . '.'
                . $uploadedFile->getClientOriginalExtension();

            $newFilePath = $uploadedFile->storeAs(
                self::STORAGE_PATH,
                $filename,
                self::STORAGE_DISK
            );

            /**
             * ==========================================================
             * DATABASE TRANSACTION
             * ==========================================================
             */
            $cert = DB::transaction(function () use (
                $request,
                $member,
                $existing,
                $newFilePath,
                &$oldFileToDelete
            ) {

                /**
                 * ------------------------------------------------------
                 * SIMPAN FILE LAMA UNTUK DIHAPUS NANTI
                 * ------------------------------------------------------
                 */
                if ($existing && $existing->file) {
                    $oldFileToDelete = $existing->file;
                }

                /**
                 * ------------------------------------------------------
                 * UPDATE ATAU CREATE
                 * ------------------------------------------------------
                 */
                return Certificate::updateOrCreate(
                    [
                        'application_member_id' => $member->id
                    ],
                    [
                        'user_id'              => $member->user_id,
                        'title'                => $request->title,
                        'file'                 => $newFilePath,
                        'uploaded_by_admin_id' => Auth::id(),
                        'uploaded_at'          => now(),
                        'replaced_at'          => $existing ? now() : null,
                    ]
                );
            });

            /**
             * ==========================================================
             * HAPUS FILE LAMA SETELAH TRANSACTION SUKSES
             * ==========================================================
             */
            if ($oldFileToDelete) {
                $this->deleteFileIfExists($oldFileToDelete);
            }

            /**
             * ==========================================================
             * LOG ACTIVITY
             * ==========================================================
             */
            Log::info('Sertifikat diupload oleh admin', [
                'admin_id'              => Auth::id(),
                'user_id'               => $member->user_id,
                'application_member_id' => $member->id,
                'certificate_id'        => $cert->id,
                'filename'              => $filename,
                'replaced'              => $existing ? true : false,
                'vacancy'               => $member->application->vacancy->title ?? '-',
                'timestamp'             => now()->toDateTimeString(),
            ]);

            return back()->with(
                'success',
                'Sertifikat berhasil diupload untuk periode magang ini.'
            );
        } catch (\Throwable $e) {

            /**
             * ==========================================================
             * CLEANUP FILE BARU JIKA DB GAGAL
             * ==========================================================
             *
             * Mencegah orphan file.
             */
            if ($newFilePath) {
                $this->deleteFileIfExists($newFilePath);
            }

            Log::error('Gagal upload sertifikat', [
                'admin_id' => Auth::id(),
                'member_id' => $member->id ?? null,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }


    // ======================================================================
    // USER (PEMAGANG) — INDEX
    // ======================================================================

    /**
     * Tampilkan semua riwayat magang user beserta assessment dan sertifikat
     * masing-masing periode. Tidak ada data yang hilang meski magang berkali-kali.
     */
    public function index()
    {
        $userId = Auth::guard('magang')->id();

        $memberRecords = ApplicationMemberMagang::where('user_id', $userId)
            ->whereHas('application', function ($q) {
                $q->whereIn('status', ['accepted', 'completed', 'resigned']);
            })
            ->with([
                'application.vacancy:id,title,division_name,type,start_date,end_date',
                'certificate',
            ])
            ->latest()
            ->get();

        return view('magang.sertifikat.index', compact('memberRecords'));
    }


    // ======================================================================
    // USER (PEMAGANG) — DOWNLOAD
    // ======================================================================

    public function download(Certificate $certificate)
    {
        $userId = Auth::guard('magang')->id();

        if ((int) $certificate->user_id !== (int) $userId) {
            Log::warning('Percobaan download sertifikat tidak sah', [
                'requester_id'   => $userId,
                'certificate_id' => $certificate->id,
                'owner_id'       => $certificate->user_id,
            ]);
            abort(403, 'Akses ditolak: Ini bukan sertifikat Anda.');
        }

        if (!Storage::disk(self::STORAGE_DISK)->exists($certificate->file)) {
            abort(404, 'File sertifikat tidak ditemukan di server.');
        }

        Log::info('Sertifikat didownload', [
            'user_id'        => $userId,
            'certificate_id' => $certificate->id,
            'timestamp'      => now()->toDateTimeString(),
        ]);

        $fullPath = Storage::disk(self::STORAGE_DISK)->path($certificate->file);
        $filename = $certificate->title . '.' . pathinfo($certificate->file, PATHINFO_EXTENSION);

        return response()->download($fullPath, $filename);
    }


    // ======================================================================
    // USER (PEMAGANG) — VIEW INLINE
    // ======================================================================

    public function view(Certificate $certificate)
    {
        $userId = Auth::guard('magang')->id();

        if ((int) $certificate->user_id !== (int) $userId) {
            Log::warning('Percobaan view sertifikat tidak sah', [
                'requester_id'   => $userId,
                'certificate_id' => $certificate->id,
                'owner_id'       => $certificate->user_id,
            ]);
            abort(403, 'Akses ditolak: Ini bukan sertifikat Anda.');
        }

        if (!Storage::disk(self::STORAGE_DISK)->exists($certificate->file)) {
            abort(404, 'File sertifikat tidak ditemukan di server.');
        }

        return response()->file(
            Storage::disk(self::STORAGE_DISK)->path($certificate->file)
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
