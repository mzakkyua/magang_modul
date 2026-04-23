<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\MagangAccessRight;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CalendarController extends Controller
{

    /**
     * ===============================================================
     * HELPER: CEK SUPERADMIN
     * ===============================================================
     * Digunakan oleh method admin-facing (indexAdmin, store, update, destroy).
     * Jika bukan superadmin → abort 403.
     *
     * Method index() dan fetch() TIDAK menggunakan helper ini karena
     * keduanya juga dipakai oleh peserta magang (dashboard kalender).
     */
    private function authorizeSuperAdmin(): void
    {
        $hakAkses = MagangAccessRight::where('user_id', Auth::id())->first();

        if (!$hakAkses || $hakAkses->role !== 'superadmin') {
            abort(403, 'Fitur Jadwal Kegiatan hanya dapat diakses oleh Super Admin.');
        }
    }



    /**
     * ===============================================================
     * CALENDAR PAGE (PESERTA)
     * ===============================================================
     * Menampilkan halaman kalender untuk peserta magang.
     * Tidak diproteksi superadmin karena dipakai di dashboard peserta.
     */
    public function index()
    {
        return view('calender');
    }



    /**
     * ===============================================================
     * FETCH EVENTS (API)
     * ===============================================================
     * Mengambil daftar event untuk ditampilkan di calendar frontend.
     * Tidak diproteksi superadmin karena dipakai peserta juga.
     */
    public function fetch()
    {
        $events = [];

        // 1. AMBIL EVENT GLOBAL (Yang diinput Admin)
        $globalEvents = \App\Models\Event::all();
        foreach ($globalEvents as $event) {
            $events[] = [
                'id'    => 'global-' . $event->id,
                'title' => '📌 ' . $event->title,
                'start' => \Carbon\Carbon::parse($event->start_date)->format('Y-m-d'),
                'end'   => $event->end_date ? \Carbon\Carbon::parse($event->end_date)->addDay()->format('Y-m-d') : null,
                'color' => $event->color ?? '#3b82f6',
                'extendedProps' => [
                    'description' => $event->description,
                    'type' => 'global'
                ]
            ];
        }

        // 2. CEK EVENT MAGANG USER (Jika Login sebagai peserta)
        if (auth()->guard('magang')->check()) {
            $userId = auth()->guard('magang')->id();

            $acceptedMember = \App\Models\ApplicationMemberMagang::where('user_id', $userId)
                ->whereHas('application', function ($q) {
                    $q->where('status', 'accepted');
                })
                ->with('application.vacancy')
                ->first();

            if ($acceptedMember && $acceptedMember->application->vacancy) {
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
                        'type' => 'internship'
                    ]
                ];
            }
        }

        return response()->json($events);
    }



    /**
     * ===============================================================
     * ADMIN CALENDAR PAGE
     * ===============================================================
     * Menampilkan halaman manajemen kalender.
     * HANYA superadmin — admin divisi biasa → 403.
     */
    public function indexAdmin()
    {
        // STEP: Blokir akses jika bukan superadmin
        $this->authorizeSuperAdmin();

        return view('admin.calendar.index');
    }



    /**
     * ===============================================================
     * CREATE EVENT
     * ===============================================================
     * HANYA superadmin — admin divisi biasa → 403.
     */
    public function store(Request $request)
    {
        // STEP: Blokir akses jika bukan superadmin
        $this->authorizeSuperAdmin();

        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'start_date'  => 'required|date',
            'end_date'    => 'nullable|date|after_or_equal:start_date',
            'color'       => 'nullable|string|max:20',
            'description' => 'nullable|string|max:1000',
        ]);

        Event::create($validated);

        return response()->json(['success' => true]);
    }



    /**
     * ===============================================================
     * UPDATE EVENT
     * ===============================================================
     * HANYA superadmin — admin divisi biasa → 403.
     */
    public function update(Request $request, Event $event)
    {
        // STEP: Blokir akses jika bukan superadmin
        $this->authorizeSuperAdmin();

        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'start_date'  => 'required|date',
            'end_date'    => 'nullable|date|after_or_equal:start_date',
            'color'       => 'nullable|string|max:20',
            'description' => 'nullable|string|max:1000',
        ]);

        $event->update($validated);

        return response()->json(['success' => true]);
    }



    /**
     * ===============================================================
     * DELETE EVENT
     * ===============================================================
     * HANYA superadmin — admin divisi biasa → 403.
     */
    public function destroy(Event $event)
    {
        // STEP: Blokir akses jika bukan superadmin
        $this->authorizeSuperAdmin();

        $event->delete();

        return response()->json(['success' => true]);
    }
}
