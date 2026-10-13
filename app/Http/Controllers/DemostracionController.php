<?php

namespace App\Http\Controllers;

use App\Models\DemostracionPersona;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DemostracionController extends Controller
{
    public function index()
    {
        return Inertia::render('Demostracion/Index');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:100',
            'telefono' => 'required|string|size:10|regex:/^[0-9]{10}$/',
        ]);

        DemostracionPersona::create($validated);

        return response()->json([
            'ok' => true,
            'message' => '¡Gracias! Tus datos se registraron correctamente.',
        ]);
    }
}
