<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DemostracionPersona;
use App\Services\BrevoSmsService;
use Illuminate\Http\Request;
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

        $enviados = 0;
        $errores = [];

        foreach ($personas as $persona) {
            $contenido = str_ireplace('{nombre}', $persona->nombre, $validated['mensaje']);

            try {
                $resultado = $sms->send($persona->telefono, $contenido);

                if ($resultado['ok']) {
                    $enviados++;
                } else {
                    $errores[] = [
                        'nombre' => $persona->nombre,
                        'telefono' => $persona->telefono,
                        'error' => $resultado['error'] ?? 'Error desconocido',
                    ];
                }
            } catch (Throwable $e) {
                $errores[] = [
                    'nombre' => $persona->nombre,
                    'telefono' => $persona->telefono,
                    'error' => $e->getMessage(),
                ];
            }
        }

        return response()->json([
            'ok' => true,
            'enviados' => $enviados,
            'total' => $personas->count(),
            'errores' => $errores,
        ]);
    }

    public function destroy(DemostracionPersona $demostracion)
    {
        $demostracion->delete();

        return back()->with('success', 'Registro eliminado.');
    }
}
