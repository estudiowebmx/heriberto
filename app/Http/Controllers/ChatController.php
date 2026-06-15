<?php

namespace App\Http\Controllers;

use App\Ai\Agents\CandidateBot;
use App\Models\Persona;
use App\Models\Seccion;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ChatController extends Controller
{
    public function index()
    {
        return Inertia::render('Chat/Index');
    }

    public function send(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:2000',
            'conversation_id' => 'nullable|string',
        ]);

        $agent = new CandidateBot;

        if ($request->conversation_id) {
            $agent = $agent->continue($request->conversation_id, as: $request->user());
        } else {
            $agent = $agent->forUser($request->user() ?? null);
        }

        $response = $agent->prompt($request->message);

        return response()->json([
            'message' => (string) $response,
            'conversation_id' => $response->conversationId,
        ]);
    }

    public function registrar(Request $request)
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

        $persona = Persona::create($validated);

        $mensaje = "¡Muchas gracias por sumarte a nuestro proyecto, {$persona->nombre}! Tu participación es fundamental para sacar adelante a nuestro estado. En breve nos pondremos en contacto contigo vía WhatsApp al número que nos proporcionaste. ¡Juntos haremos historia!";

        return response()->json([
            'message' => $mensaje,
            'ok' => true,
        ]);
    }

    public function buscarSeccion(Request $request)
    {
        $numero = $request->query('q');

        if (!$numero) {
            return response()->json(null, 404);
        }

        $seccion = Seccion::where('seccion', $numero)->first();

        if (!$seccion) {
            return response()->json(null, 404);
        }

        $dmunicipal = $seccion->municipio && $seccion->demarcacion
            ? $seccion->municipio . ' - D' . $seccion->demarcacion
            : null;

        return response()->json([
            'seccion' => $seccion->seccion,
            'municipio' => $seccion->municipio,
            'distrito_local' => $seccion->distrito_local,
            'distrito_federal' => $seccion->distrito_federal,
            'demarcacion' => $seccion->demarcacion,
            'dmunicipal' => $dmunicipal,
        ]);
    }
}
