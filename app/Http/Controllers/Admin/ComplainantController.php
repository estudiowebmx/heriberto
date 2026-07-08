<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Complainant;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ComplainantController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/LonasOld/ComplainantIndex', [
            'complainants' => Complainant::withCount(['complaints', 'lonas'])
                ->orderBy('created_at', 'desc')
                ->paginate(20),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/LonasOld/ComplainantEdit', [
            'complainant' => null,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre_completo' => 'required|string|max:255',
            'clave_elector' => 'required|string|max:30|unique:complainants,clave_elector',
            'domicilio' => 'required|string|max:500',
            'email' => 'nullable|email|max:255',
            'sexo' => 'required|in:M,F',
        ]);

        Complainant::create($validated);

        return redirect()->route('admin.denunciantes.index')->with('success', 'Denunciante registrado.');
    }

    public function edit(Complainant $denunciante)
    {
        return Inertia::render('Admin/LonasOld/ComplainantEdit', [
            'complainant' => $denunciante,
        ]);
    }

    public function update(Request $request, Complainant $denunciante)
    {
        $validated = $request->validate([
            'nombre_completo' => 'required|string|max:255',
            'clave_elector' => 'required|string|max:30|unique:complainants,clave_elector,' . $denunciante->id,
            'domicilio' => 'required|string|max:500',
            'email' => 'nullable|email|max:255',
            'sexo' => 'required|in:M,F',
        ]);

        $denunciante->update($validated);

        return redirect()->route('admin.denunciantes.index')->with('success', 'Denunciante actualizado.');
    }

    public function destroy(Complainant $denunciante)
    {
        $denunciante->delete();

        return redirect()->route('admin.denunciantes.index')->with('success', 'Denunciante eliminado.');
    }
}
