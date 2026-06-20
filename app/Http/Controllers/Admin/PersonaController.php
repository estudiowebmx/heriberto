<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Persona;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PersonaController extends Controller
{
    public function index(Request $request)
    {
        $query = Persona::query()->orderBy('created_at', 'desc');

        if (auth()->user()->isColaborador()) {
            $query->where('user_id', auth()->id());
        }

        $likeFields = ['nombre', 'municipio'];
        $exactFields = ['telefono', 'seccion', 'distrito_local', 'distrito_federal', 'demarcacion'];

        foreach ($likeFields as $field) {
            if ($request->filled($field)) {
                $query->where('personas.' . $field, 'like', '%' . $request->$field . '%');
            }
        }

        foreach ($exactFields as $field) {
            if ($request->filled($field)) {
                $query->where('personas.' . $field, $request->$field);
            }
        }

        return Inertia::render('Admin/Personas/Index', [
            'personas' => $query->paginate(20)->appends($request->query()),
            'filters' => $request->only(array_merge($likeFields, $exactFields)),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Personas/Edit', [
            'persona' => null,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:100',
            'apellido_paterno' => 'required|string|max:100',
            'apellido_materno' => 'nullable|string|max:100',
            'telefono' => 'required|string|size:10|regex:/^[0-9]{10}$/',
            'seccion' => 'nullable|string|max:20',
            'municipio' => 'nullable|string|max:255',
            'distrito_local' => 'nullable|string|max:50',
            'distrito_federal' => 'nullable|string|max:50',
            'demarcacion' => 'nullable|string|max:50',
        ]);

        $validated['user_id'] = auth()->id();
        Persona::create($validated);

        return redirect()->route('admin.personas.index')->with('success', 'Persona registrada.');
    }

    public function edit(Persona $persona)
    {
        return Inertia::render('Admin/Personas/Edit', [
            'persona' => $persona,
        ]);
    }

    public function update(Request $request, Persona $persona)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:100',
            'apellido_paterno' => 'required|string|max:100',
            'apellido_materno' => 'nullable|string|max:100',
            'telefono' => 'required|string|size:10|regex:/^[0-9]{10}$/',
            'seccion' => 'nullable|string|max:20',
            'municipio' => 'nullable|string|max:255',
            'distrito_local' => 'nullable|string|max:50',
            'distrito_federal' => 'nullable|string|max:50',
            'demarcacion' => 'nullable|string|max:50',
        ]);

        $persona->update($validated);

        return redirect()->route('admin.personas.index')->with('success', 'Persona actualizada.');
    }

    public function destroy(Persona $persona)
    {
        $persona->delete();

        return redirect()->route('admin.personas.index')->with('success', 'Persona eliminada.');
    }
}
