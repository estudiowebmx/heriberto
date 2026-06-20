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
        return Inertia::render('Admin/Denuncias/ComplainantIndex', [
            'complainants' => Complainant::orderBy('created_at', 'desc')->paginate(20),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Denuncias/ComplainantEdit', [
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

        return redirect()->route('admin.denuncias.complainants.index')->with('success', 'Denunciante registrado.');
    }

    public function edit(Complainant $complainant)
    {
        return Inertia::render('Admin/Denuncias/ComplainantEdit', [
            'complainant' => $complainant,
        ]);
    }

    public function update(Request $request, Complainant $complainant)
    {
        $validated = $request->validate([
            'nombre_completo' => 'required|string|max:255',
            'clave_elector' => 'required|string|max:30|unique:complainants,clave_elector,' . $complainant->id,
            'domicilio' => 'required|string|max:500',
            'email' => 'nullable|email|max:255',
            'sexo' => 'required|in:M,F',
        ]);

        $complainant->update($validated);

        return redirect()->route('admin.denuncias.complainants.index')->with('success', 'Denunciante actualizado.');
    }

    public function destroy(Complainant $complainant)
    {
        $complainant->delete();

        return redirect()->route('admin.denuncias.complainants.index')->with('success', 'Denunciante eliminado.');
    }
}
