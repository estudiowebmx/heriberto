<?php

namespace App\Http\Controllers\Admin;

use App\Ai\Agents\IneExtractorAgent;
use App\Http\Controllers\Controller;
use App\Models\Grupo;
use App\Models\Persona;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Laravel\Ai\Files\Image;
use Laravel\Ai\Responses\StructuredAgentResponse;

class PersonaController extends Controller
{
    public function index(Request $request)
    {
        $query = Persona::query()
            ->with([
                'grupos:id,nombre',
                'seccionInfo:id,seccion,municipio,distrito_local,distrito_federal,demarcacion',
            ])
            ->orderBy('created_at', 'desc');

        if (auth()->user()->isColaborador()) {
            $query->where('user_id', auth()->id());
        }

        $likeFields = [
            'nombre',
            'apellido_paterno',
            'apellido_materno',
            'celular',
            'curp',
            'clave_elector',
            'domicilio',
            'colonia',
            'localidad',
            'promotor',
        ];

        $exactFields = ['seccion', 'sexo'];

        foreach ($likeFields as $field) {
            if ($request->filled($field)) {
                $query->where('personas.'.$field, 'like', '%'.$request->$field.'%');
            }
        }

        foreach ($exactFields as $field) {
            if ($request->filled($field)) {
                $query->where('personas.'.$field, $request->$field);
            }
        }

        foreach (['municipio', 'distrito_local', 'distrito_federal', 'demarcacion'] as $field) {
            if ($request->filled($field)) {
                $query->whereHas('seccionInfo', fn ($seccion) => $seccion->where($field, $request->$field));
            }
        }

        if ($request->filled('grupo')) {
            $query->whereHas('grupos', fn ($grupo) => $grupo->where('nombre', $request->grupo));
        }

        $filters = $request->only(array_merge(
            $likeFields,
            $exactFields,
            ['municipio', 'distrito_local', 'distrito_federal', 'demarcacion', 'grupo']
        ));

        return Inertia::render('Admin/Personas/Index', [
            'personas' => $query->paginate(20)->appends($request->query()),
            'filters' => $filters,
            'gruposCatalogo' => Grupo::orderBy('nombre')->get(['id', 'nombre']),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Personas/Create', [
            'persona' => null,
            'gruposCatalogo' => Grupo::orderBy('nombre')->get(['id', 'nombre']),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validar($request);

        if (blank($validated['promotor'] ?? null)) {
            $validated['promotor'] = auth()->user()->name;
        }

        $persona = Persona::create([
            ...Arr::except($validated, ['grupos', 'nuevos_grupos']),
            'user_id' => auth()->id(),
        ]);

        $this->sincronizarGrupos($persona, $validated);

        return redirect()->route('admin.personas.index')->with('success', 'Persona registrada.');
    }

    public function extraerIne(Request $request)
    {
        $request->validate([
            'imagen' => 'required|image|mimes:jpeg,png,webp,gif|max:10240',
        ]);

        if (blank(config('ai.providers.deepseek.key'))) {
            return response()->json([
                'message' => 'Falta configurar DEEPSEEK_API_KEY en el archivo .env.',
            ], 503);
        }

        try {
            $agent = new IneExtractorAgent;

            $response = $agent->prompt(
                'Analiza la credencial INE de la imagen y extrae los datos solicitados.',
                [Image::fromUpload($request->file('imagen'))],
                provider: 'deepseek',
                model: config('ai.providers.deepseek.options.vision_model', 'deepseek-flash'),
                timeout: 90,
            );

            $data = $response instanceof StructuredAgentResponse
                ? $response->structured
                : $this->decodificarJson($response->text);

            if (empty($data)) {
                $data = $this->decodificarJson($response->text);
            }

            return response()->json([
                'data' => $this->normalizarDatosIne($data),
            ]);
        } catch (\Throwable $e) {
            Log::error('Error al extraer datos de la INE con DeepSeek', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'No se pudieron extraer los datos del INE. Intenta con una foto más clara.',
            ], 422);
        }
    }

    public function edit(Persona $persona)
    {
        $persona->load(['grupos:id,nombre', 'seccionInfo']);

        return Inertia::render('Admin/Personas/Edit', [
            'persona' => $persona,
            'gruposCatalogo' => Grupo::orderBy('nombre')->get(['id', 'nombre']),
        ]);
    }

    public function update(Request $request, Persona $persona)
    {
        $validated = $this->validar($request);

        $persona->update(Arr::except($validated, ['grupos', 'nuevos_grupos']));
        $this->sincronizarGrupos($persona, $validated);

        return redirect()->route('admin.personas.index')->with('success', 'Persona actualizada.');
    }

    public function destroy(Persona $persona)
    {
        $persona->delete();

        return redirect()->route('admin.personas.index')->with('success', 'Persona eliminada.');
    }

    private function decodificarJson(?string $texto): array
    {
        if (blank($texto)) {
            return [];
        }

        $texto = trim($texto);
        $texto = preg_replace('/^```(?:json)?\s*|\s*```$/mi', '', $texto) ?? $texto;
        $datos = json_decode($texto, true);

        return is_array($datos) ? $datos : [];
    }

    private function normalizarDatosIne(array $datos): array
    {
        $campos = [
            'nombre',
            'apellido_paterno',
            'apellido_materno',
            'sexo',
            'fecha_nacimiento',
            'curp',
            'clave_elector',
            'domicilio',
            'colonia',
            'localidad',
            'seccion',
        ];

        $resultado = [];

        foreach ($campos as $campo) {
            $valor = $datos[$campo] ?? null;

            if (is_string($valor)) {
                $valor = trim($valor);
            }

            $resultado[$campo] = $valor === '' ? null : $valor;
        }

        foreach (['curp', 'clave_elector'] as $campo) {
            if (filled($resultado[$campo])) {
                $resultado[$campo] = mb_strtoupper(
                    preg_replace('/\s+/', '', (string) $resultado[$campo]),
                    'UTF-8'
                );
            }
        }

        if (filled($resultado['curp']) && mb_strlen($resultado['curp']) >= 11) {
            $resultado['sexo'] = match (mb_substr($resultado['curp'], 10, 1)) {
                'H' => 'Masculino',
                'M' => 'Femenino',
                default => $resultado['sexo'],
            };
        }

        if (filled($resultado['sexo'])) {
            $sexo = mb_strtoupper(trim((string) $resultado['sexo']), 'UTF-8');

            $resultado['sexo'] = match (true) {
                in_array($sexo, ['H', 'HOMBRE', 'MASCULINO'], true) => 'Masculino',
                in_array($sexo, ['M', 'MUJER', 'FEMENINO'], true) => 'Femenino',
                default => $resultado['sexo'],
            };
        }

        if (filled($resultado['fecha_nacimiento'])) {
            $resultado['fecha_nacimiento'] = $this->normalizarFecha($resultado['fecha_nacimiento']);
        }

        if (filled($resultado['seccion'])) {
            $digitos = preg_replace('/\D/', '', (string) $resultado['seccion']);

            $resultado['seccion'] = $digitos !== ''
                ? (ltrim($digitos, '0') ?: '0')
                : null;
        }

        return $resultado;
    }

    private function normalizarFecha(mixed $fecha): ?string
    {
        $fecha = trim((string) $fecha);

        if ($fecha === '') {
            return null;
        }

        foreach (['Y-m-d', 'd/m/Y', 'd-m-Y', 'm/d/Y', 'd.m.Y', 'Y/m/d'] as $formato) {
            try {
                $fechaNormalizada = Carbon::createFromFormat($formato, $fecha);

                if ($fechaNormalizada !== false) {
                    return $fechaNormalizada->format('Y-m-d');
                }
            } catch (\Throwable) {
                // Se prueba el siguiente formato.
            }
        }

        try {
            return Carbon::parse($fecha)->format('Y-m-d');
        } catch (\Throwable) {
            return null;
        }
    }

    private function validar(Request $request): array
    {
        return $request->validate([
            'nombre' => 'required|string|max:100',
            'apellido_paterno' => 'nullable|string|max:100',
            'apellido_materno' => 'nullable|string|max:100',
            'sexo' => 'nullable|string|max:20',
            'fecha_nacimiento' => 'nullable|date',
            'celular' => 'nullable|digits:10',
            'curp' => 'nullable|string|max:18',
            'clave_elector' => 'nullable|string|max:20',
            'domicilio' => 'nullable|string|max:255',
            'colonia' => 'nullable|string|max:255',
            'localidad' => 'nullable|string|max:255',
            'seccion' => 'nullable|string|max:20',
            'promotor' => 'nullable|string|max:255',
            'grupos' => 'nullable|array',
            'grupos.*' => 'integer|exists:grupos,id',
            'nuevos_grupos' => 'nullable|string|max:1000',
        ]);
    }

    private function sincronizarGrupos(Persona $persona, array $validated): void
    {
        $ids = $validated['grupos'] ?? [];

        if (! empty($validated['nuevos_grupos'])) {
            $nombres = preg_split('/[,;\n]+/', $validated['nuevos_grupos']) ?: [];

            foreach ($nombres as $nombre) {
                $nombre = trim($nombre);

                if ($nombre === '') {
                    continue;
                }

                $ids[] = Grupo::firstOrCreate(['nombre' => $nombre])->id;
            }
        }

        $persona->grupos()->sync(array_values(array_unique($ids)));
    }
}
