<?php
namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CalendarController extends Controller
{
    public function index()
    {
        return view('calender');
    }

    public function fetch()
    {
        $events = Event::all()->map(function ($event) {
            return [
                'id'    => $event->id,
                'title' => $event->title,
                'start' => $event->start_date,
                'end'   => Carbon::parse($event->end_date)->addDay()->toDateString(),
                'color' => $event->color,
                'description' => $event->description
            ];
        });

        return response()->json($events);
    }

    public function store(Request $request)
    {
        Event::create($request->all());
        return response()->json(['success' => true]);
    }

    public function update(Request $request, Event $event)
    {
        $event->update($request->all());
        return response()->json(['success' => true]);
    }

    public function destroy(Event $event)
    {
        $event->delete();
        return response()->json(['success' => true]);
    }
}
?>