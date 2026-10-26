<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DemostracionPersona;
use App\Services\BrevoSmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Throwable;

class DemostracionController extends Controller
{
    public function index(Request $request)
    {
        $query = DemostracionPersona::query()->orderBy('created_at', 'desc');

        if ($request->filled('nombre')) {
            $query->where('nombre', 'like', '%'.$request->nombre.'%');
        }

        if ($request->filled('telefono')) {
            $query->where('telefono', 'like', '%'.$request->telefono.'%');
        }

        return Inertia::render('Admin/Demostracion/Index', [
            'personas' => $query->paginate(50)->appends($request->query()),
            'filters' => $request->only(['nombre', 'telefono']),
        ]);
    }

    public function enviarSms(Request $request, BrevoSmsService $sms)
    {
        $validated = $request->validate([
            'mensaje' => 'required|string|max:480',
            'ids' => 'nullable|array',
            'ids.*' => 'integer|exists:demostracion_personas,id',
        ]);

        $query = DemostracionPersona::query();

        if (! empty($validated['ids'])) {
            $query->whereIn('id', $validated['ids']);
        }

        $personas = $query->get();

        $items = [];

        foreach ($personas as $persona) {
            $items[$persona->id] = [
                'telefono' => $persona->telefono,
                'contenido' => str_ireplace('{nombre}', $persona->nombre, $validated['mensaje']),
            ];
        }

        try {
            $respuestas = $sms->sendMany($items);
        } catch (Throwable $e) {
            Log::error('Error inesperado al enviar SMS de demostración', [
                'error' => $e->getMessage(),
            ]);

            $respuestas = [];
        }

        $enviados = 0;
        $errores = 0;
        $resultados = [];

        foreach ($personas as $persona) {
            $respuesta = $respuestas[$persona->id] ?? [
                'ok' => false,
                'http' => null,
                'messageId' => null,
                'code' => 'unknown_error',
                'error' => 'No se obtuvo respuesta del envío.',
            ];

            if ($respuesta['ok']) {
                $enviados++;
            } else {
                $errores++;
            }

            $resultados[] = [
                'id' => $persona->id,
                'nombre' => $persona->nombre,
                'telefono' => $persona->telefono,
                'ok' => $respuesta['ok'],
                'http' => $respuesta['http'],
                'messageId' => $respuesta['messageId'],
                'code' => $respuesta['code'],
                'error' => $respuesta['error'],
            ];
        }

        return response()->json([
            'ok' => true,
            'enviados' => $enviados,
            'total' => $personas->count(),
            'errores' => $errores,
            'resultados' => $resultados,
        ]);
    }

    public function destroy(DemostracionPersona $demostracion)
    {
        $demostracion->delete();

        return back()->with('success', 'Registro eliminado.');
    }
}
