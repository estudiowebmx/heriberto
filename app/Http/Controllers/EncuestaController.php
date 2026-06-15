<?php

namespace App\Http\Controllers;

use App\Models\Encuesta;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EncuestaController extends Controller
{
    public function index()
    {
        return Inertia::render('Conoceme/Index');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre_completo' => 'required|string|max:255',
            'fecha_nacimiento' => 'nullable|date',
            'telefono' => 'required|string|size:10|regex:/^[0-9]{10}$/',
            'profesion_ocupacion' => 'required|string|max:255',
            'lugar_nacimiento' => 'required|string|max:255',
            'domicilio_actual' => 'nullable|string',
            'hijos_info' => 'nullable|string',
            'padres_viven' => 'nullable|string|in:si,no,solo_uno',
            'padres_origen' => 'nullable|string|max:255',
            'padres_ocupacion' => 'nullable|string|max:255',
            'preocupaciones' => 'nullable|array',
            'problema_justicia' => 'nullable|string',
            'enfoque_gobernador' => 'nullable|string',
            'errores_gobierno' => 'nullable|array',
            'deficiencias_servicios' => 'nullable|array',
        ]);

        Encuesta::create($validated);

        return response()->json([
            'ok' => true,
            'message' => '¡Gracias por compartir tu información! Tus respuestas nos ayudan a construir un mejor futuro para Nayarit.',
        ]);
    }
}
