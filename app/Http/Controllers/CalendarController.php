<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CalendarController extends Controller
{

    /**
     * ===============================================================
     * CALENDAR PAGE
     * ===============================================================
     *
     * Menampilkan halaman kalender.
     *
     */

    public function index()
    {
        return view('calender');
    }



    /**
     * ===============================================================
     * FETCH EVENTS (API)
     * ===============================================================
     *
     * Mengambil daftar event untuk ditampilkan di calendar frontend.
     *
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
                // Pastikan formatnya YYYY-MM-DD
                'start' => \Carbon\Carbon::parse($event->start_date)->format('Y-m-d'),
                'end'   => $event->end_date ? \Carbon\Carbon::parse($event->end_date)->addDay()->format('Y-m-d') : null,
                'color' => $event->color ?? '#3b82f6',
                'extendedProps' => [
                    'description' => $event->description,
                    'type' => 'global'
                ]
            ];
        }

        // 2. CEK EVENT MAGANG USER (Jika Login)
        if (auth()->guard('magang')->check()) {
            $userId = auth()->guard('magang')->id();

            // Cari lamaran yang statusnya 'accepted'
            $acceptedMember = \App\Models\ApplicationMemberMagang::where('user_id', $userId)
                ->whereHas('application', function ($q) {
                    $q->where('status', 'accepted');
                })
                ->with('application.vacancy')
                ->first();

            // Jika ketemu, masukkan ke dalam array events
            if ($acceptedMember && $acceptedMember->application->vacancy) {
                $vacancy = $acceptedMember->application->vacancy;

                $events[] = [
                    'id'    => 'internship-' . $acceptedMember->application_id,
                    'title' => '🚀 MASA MAGANG: ' . $vacancy->title,
                    // Pastikan formatnya YYYY-MM-DD
                    'start' => \Carbon\Carbon::parse($vacancy->start_date)->format('Y-m-d'),
                    'end'   => \Carbon\Carbon::parse($vacancy->end_date)->addDay()->format('Y-m-d'),
                    'color' => '#10b981', // Hijau Emerald
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
     * CREATE EVENT
     * ===============================================================
     */

    public function store(Request $request)
    {

        /**
         * VALIDASI INPUT
         */

        $validated = $request->validate([

            'title' => 'required|string|max:255',

            'start_date' => 'required|date',

            'end_date' => 'nullable|date|after_or_equal:start_date',

            'color' => 'nullable|string|max:20',

            'description' => 'nullable|string|max:1000',

        ]);



        /**
         * CREATE EVENT
         */

        Event::create($validated);



        return response()->json([
            'success' => true
        ]);
    }



    /**
     * ===============================================================
     * UPDATE EVENT
     * ===============================================================
     */

    public function update(Request $request, Event $event)
    {

        /**
         * VALIDASI INPUT
         */

        $validated = $request->validate([

            'title' => 'required|string|max:255',

            'start_date' => 'required|date',

            'end_date' => 'nullable|date|after_or_equal:start_date',

            'color' => 'nullable|string|max:20',

            'description' => 'nullable|string|max:1000',

        ]);



        /**
         * UPDATE EVENT
         */

        $event->update($validated);



        return response()->json([
            'success' => true
        ]);
    }



    /**
     * ===============================================================
     * DELETE EVENT
     * ===============================================================
     */

    public function destroy(Event $event)
    {

        $event->delete();

        return response()->json([
            'success' => true
        ]);
    }

    /**
     * ===============================================================
     * ADMIN CALENDAR PAGE
     * ===============================================================
     * Menampilkan halaman manajemen kalender khusus untuk admin
     */
    public function indexAdmin()
    {
        return view('admin.calendar.index');
    }
}
