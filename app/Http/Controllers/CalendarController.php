<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\MagangAccessRight;
use App\Models\ApplicationMemberMagang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log; // Ditambahkan untuk mencatat error diam-diam

class CalendarController extends Controller
{
    /**
     * ===============================================================
     * HELPER: CEK SUPERADMIN
     * ===============================================================
     */
    private function authorizeSuperAdmin(): void
    {
        $hakAkses = request()->attributes->get('magang_access');

        if (!$hakAkses || !$hakAkses->isSuperAdmin()) {
            abort(403, 'Fitur Jadwal Kegiatan hanya dapat diakses oleh Super Admin.');
        }
    }

    /**
     * ===============================================================
     * CALENDAR PAGE (PESERTA / UMUM)
     * ===============================================================
     */
    public function index()
    {
        return view('partials.calendar');
    }

    /**
     * ===============================================================
     * FETCH EVENTS (API) - DIPERKUAT DENGAN HELM PENGAMAN & ANTI CACHE
     * ===============================================================
     */
    public function fetch(Request $request)
    {
        $events = [];

        /**
         * ===========================================================
         * 1. EVENT GLOBAL (Mading Sekolah)
         * ===========================================================
         */
        try {
            $globalEvents = Event::select(
                'id',
                'title',
                'start_date',
                'end_date',
                'color',
                'description'
            )->get();

            foreach ($globalEvents as $event) {
                // Gunakan try-catch kecil agar 1 tanggal rusak tidak merusak semua event
                try {
                    $events[] = [
                        'id'    => 'global-' . $event->id,
                        'title' => '📌 ' . $event->title,
                        'start' => $event->start_date
                            ? \Carbon\Carbon::parse($event->start_date)->format('Y-m-d')
                            : null,
                        'end'   => $event->end_date
                            ? \Carbon\Carbon::parse($event->end_date)->addDay()->format('Y-m-d')
                            : null,
                        'color' => $event->color ?: '#3b82f6',
                        'extendedProps' => [
                            'description' => $event->description,
                            'type'        => 'global',
                        ],
                    ];
                } catch (\Exception $e) {
                    Log::warning("Format tanggal rusak pada Event ID {$event->id}");
                    continue; // Lewati event yang rusak, lanjut ke event berikutnya
                }
            }
        } catch (\Exception $e) {
            Log::error('Gagal memuat Event Global: ' . $e->getMessage());
        }

        /**
         * ===========================================================
         * 2. EVENT MAGANG PESERTA LOGIN (Jadwal Pribadi)
         * ===========================================================
         */
        try {
            if (Auth::guard('magang')->check()) {
                $userId = Auth::guard('magang')->id();

                // Disederhanakan: Hapus pembatasan kolom agar relasi tidak mudah error
                $acceptedMember = ApplicationMemberMagang::with(['application.vacancy'])
                    ->where('user_id', $userId)
                    ->whereHas('application', function ($q) {
                        // Tambahkan status 'completed' agar anak arsip juga bisa lihat jadwal masa lalunya
                        $q->where('status', 'accepted');
                    })
                    ->latest('application_id')
                    ->first();

                if ($acceptedMember && $acceptedMember->application && $acceptedMember->application->vacancy) {
                    $vacancy = $acceptedMember->application->vacancy;

                    $events[] = [
                        'id'    => 'internship-' . $acceptedMember->application_id,
                        'title' => '🚀 MASA MAGANG: ' . $vacancy->title,
                        'start' => \Carbon\Carbon::parse($vacancy->start_date)->format('Y-m-d'),
                        'end'   => \Carbon\Carbon::parse($vacancy->end_date)->addDay()->format('Y-m-d'),
                        'color' => '#10b981',
                        'display' => 'block',
                        'extendedProps' => [
                            'description' => 'Masa magang Anda di ' . $vacancy->division_name,
                            'type'        => 'internship',
                            'status'      => $acceptedMember->application->status,
                        ],
                    ];
                }
            }
        } catch (\Exception $e) {
            // Jika jadwal pribadi error, sistem TIDAK crash. Global event tetap aman!
            Log::error('Gagal memuat Jadwal Pribadi Peserta: ' . $e->getMessage());
        }

        /**
         * ===========================================================
         * 3. KEMBALIKAN JSON DENGAN OBAT ANTI-PIKUN (NO-CACHE)
         * ===========================================================
         */
        return response()->json($events)
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');
    }

    /**
     * ===============================================================
     * ADMIN CALENDAR PAGE
     * ===============================================================
     */
    public function indexAdmin()
    {
        $this->authorizeSuperAdmin();
        return view('admin.calendar.index');
    }

    /**
     * ===============================================================
     * CREATE EVENT
     * ===============================================================
     */
    public function store(Request $request)
    {
        $this->authorizeSuperAdmin();

        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'start_date'  => 'required|date',
            'end_date'    => 'nullable|date|after_or_equal:start_date',
            'color'       => 'nullable|string|max:20',
            'description' => 'nullable|string|max:1000',
        ]);

        Event::create($validated);

        return response()->json(['success' => true, 'message' => 'Event berhasil ditambahkan.']);
    }

    /**
     * ===============================================================
     * UPDATE EVENT
     * ===============================================================
     */
    public function update(Request $request, Event $event)
    {
        $this->authorizeSuperAdmin();

        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'start_date'  => 'required|date',
            'end_date'    => 'nullable|date|after_or_equal:start_date',
            'color'       => 'nullable|string|max:20',
            'description' => 'nullable|string|max:1000',
        ]);

        $event->update($validated);

        return response()->json(['success' => true, 'message' => 'Event berhasil diperbarui.']);
    }

    /**
     * ===============================================================
     * DELETE EVENT
     * ===============================================================
     */
    public function destroy(Event $event)
    {
        $this->authorizeSuperAdmin();
        $event->delete();
        return response()->json(['success' => true, 'message' => 'Event berhasil dihapus.']);
    }
}
