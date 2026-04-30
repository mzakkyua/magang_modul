<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\MagangAccessRight;
use App\Models\ApplicationMemberMagang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CalendarController extends Controller
{
    /**
     * ===============================================================
     * HELPER: CEK SUPERADMIN
     * ===============================================================
     * Digunakan oleh halaman / fitur admin calendar.
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
     * FETCH EVENTS (API)
     * ===============================================================
     * Dipakai peserta untuk dashboard kalender.
     */
    public function fetch()
    {
        $events = [];

        /**
         * ===========================================================
         * 1. EVENT GLOBAL
         * ===========================================================
         */
        $globalEvents = Event::select(
            'id',
            'title',
            'start_date',
            'end_date',
            'color',
            'description'
        )->get();

        foreach ($globalEvents as $event) {
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
        }

        /**
         * ===========================================================
         * 2. EVENT MAGANG PESERTA LOGIN
         * ===========================================================
         */
        if (Auth::guard('magang')->check()) {
            $userId = Auth::guard('magang')->id();

            $acceptedMember = ApplicationMemberMagang::with([
                'application.vacancy:id,title,division_name,start_date,end_date'
            ])
                ->where('user_id', $userId)
                ->whereHas('application', function ($q) {
                    $q->where('status', 'accepted');
                })
                ->latest('application_id')
                ->first();

            if (
                $acceptedMember &&
                $acceptedMember->application &&
                $acceptedMember->application->vacancy
            ) {
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
                    ],
                ];
            }
        }

        return response()->json($events);
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

        return response()->json([
            'success' => true,
            'message' => 'Event berhasil ditambahkan.',
        ]);
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

        return response()->json([
            'success' => true,
            'message' => 'Event berhasil diperbarui.',
        ]);
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

        return response()->json([
            'success' => true,
            'message' => 'Event berhasil dihapus.',
        ]);
    }
}
