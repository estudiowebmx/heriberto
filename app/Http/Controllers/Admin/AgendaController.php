<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AgendaEvent;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AgendaController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Agenda/Index', [
            'events' => AgendaEvent::orderBy('date', 'desc')->paginate(20),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Agenda/Edit', [
            'event' => null,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'type' => 'required|string|max:50',
        ]);

        AgendaEvent::create($validated);

        return redirect()->route('admin.agenda.index')->with('success', 'Evento creado.');
    }

    public function edit(AgendaEvent $agenda)
    {
        return Inertia::render('Admin/Agenda/Edit', [
            'event' => $agenda,
        ]);
    }

    public function update(Request $request, AgendaEvent $agenda)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'type' => 'required|string|max:50',
        ]);

        $agenda->update($validated);

        return redirect()->route('admin.agenda.index')->with('success', 'Evento actualizado.');
    }

    public function destroy(AgendaEvent $agenda)
    {
        $agenda->delete();

        return redirect()->route('admin.agenda.index')->with('success', 'Evento eliminado.');
    }
}
