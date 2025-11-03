<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::latest()->get();

        $totalevents   = $events->count();
        

        return view('admin.events', compact('events','totalevents'));
    }

    public function show($event_id)
    {
        $event = Event::with(['user' , 'items' , 'items.product' , 'items.package'])->findOrFail($event_id);

        return view('admin.eventShow' , compact('event'));
    }
}
