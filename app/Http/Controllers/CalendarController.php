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

        $events = Event::query()
            ->select([
                'id',
                'title',
                'start_date',
                'end_date',
                'color',
                'description'
            ])
            ->get()
            ->map(function ($event) {

                return [
                    'id'    => $event->id,
                    'title' => $event->title,

                    /**
                     * FullCalendar menggunakan format:
                     * YYYY-MM-DD
                     */

                    'start' => $event->start_date,

                    /**
                     * End date +1 hari agar FullCalendar
                     * menampilkan event sampai hari terakhir
                     */

                    'end'   => $event->end_date
                        ? Carbon::parse($event->end_date)->addDay()->toDateString()
                        : null,

                    'color' => $event->color,

                    'description' => $event->description
                ];
            });

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
}
