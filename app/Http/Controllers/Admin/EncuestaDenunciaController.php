<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Complainant;
use App\Models\EncuestaDenuncia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class EncuestaDenunciaController extends Controller
{
    public function index()
    {
        $query = EncuestaDenuncia::with(['publicaciones', 'user', 'complainant'])
            ->orderBy('created_at', 'desc');

        if (auth()->user()->isColaborador()) {
            $query->where('user_id', auth()->id());
        }

        return Inertia::render('Admin/Denuncias/Encuestas/Index', [
            'encuestas' => $query->paginate(15),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Denuncias/Encuestas/Create', [
            'complainants' => Complainant::withCount(['complaints', 'lonas'])
                ->orderBy('nombre_completo')
                ->get(['id', 'nombre_completo', 'clave_elector', 'domicilio', 'email', 'sexo']),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'complainant_id' => 'required|exists:complainants,id',
            'publicaciones' => 'required|array|min:1',
            'publicaciones.*.red_social' => 'required|string|max:255',
            'publicaciones.*.pagina' => 'required|string|max:255',
            'publicaciones.*.fecha_publicacion' => 'required|date',
            'publicaciones.*.enlace' => 'required|string|max:2000',
        ]);

        $complainant = Complainant::findOrFail($validated['complainant_id']);

        $now = now();
        $diaNumero = $now->format('d');
        $meses = [
            1 => 'enero', 2 => 'febrero', 3 => 'marzo', 4 => 'abril',
            5 => 'mayo', 6 => 'junio', 7 => 'julio', 8 => 'agosto',
            9 => 'septiembre', 10 => 'octubre', 11 => 'noviembre', 12 => 'diciembre',
        ];
        $mes = $meses[(int) $now->format('m')];

        $encuesta = EncuestaDenuncia::create([
            'complainant_id' => $complainant->id,
            'registered_by' => auth()->user()->name,
            'user_id' => auth()->id(),
            'dia_numero' => $diaNumero,
            'mes' => $mes,
        ]);

        foreach ($validated['publicaciones'] as $i => $pub) {
            $encuesta->publicaciones()->create([
                'consecutivo' => $i + 1,
                'red_social' => $pub['red_social'],
                'pagina' => $pub['pagina'],
                'fecha_publicacion' => $pub['fecha_publicacion'],
                'enlace' => $pub['enlace'],
            ]);
        }

        return redirect()->route('admin.denuncias.encuestas.index')
            ->with('success', 'Denuncia de encuestas registrada correctamente.');
    }

    public function show(EncuestaDenuncia $encuesta)
    {
        $encuesta->load(['publicaciones', 'user', 'complainant']);

        return Inertia::render('Admin/Denuncias/Encuestas/Show', [
            'encuesta' => $encuesta,
        ]);
    }

    public function edit(EncuestaDenuncia $encuesta)
    {
        $encuesta->load('publicaciones');

        $selectedComplainant = $encuesta->complainant_id;

        return Inertia::render('Admin/Denuncias/Encuestas/Edit', [
            'encuesta' => $encuesta,
            'complainants' => Complainant::withCount(['complaints', 'lonas'])
                ->orderBy('nombre_completo')
                ->get(['id', 'nombre_completo', 'clave_elector', 'domicilio', 'email', 'sexo']),
        ]);
    }

    public function update(Request $request, EncuestaDenuncia $encuesta)
    {
        $validated = $request->validate([
            'complainant_id' => 'required|exists:complainants,id',
            'publicaciones' => 'required|array|min:1',
            'publicaciones.*.id' => 'nullable|integer|exists:encuesta_publicaciones,id',
            'publicaciones.*.red_social' => 'required|string|max:255',
            'publicaciones.*.pagina' => 'required|string|max:255',
            'publicaciones.*.fecha_publicacion' => 'required|date',
            'publicaciones.*.enlace' => 'required|string|max:2000',
        ]);

        $complainant = Complainant::findOrFail($validated['complainant_id']);

        $encuesta->update([
            'complainant_id' => $complainant->id,
        ]);

        $existingIds = collect($validated['publicaciones'])
            ->pluck('id')
            ->filter()
            ->toArray();

        $encuesta->publicaciones()->whereNotIn('id', $existingIds)->delete();

        foreach ($validated['publicaciones'] as $i => $pub) {
            $data = [
                'consecutivo' => $i + 1,
                'red_social' => $pub['red_social'],
                'pagina' => $pub['pagina'],
                'fecha_publicacion' => $pub['fecha_publicacion'],
                'enlace' => $pub['enlace'],
            ];

            if (!empty($pub['id'])) {
                $existing = $encuesta->publicaciones()->find($pub['id']);
                if ($existing) {
                    $existing->update($data);
                }
            } else {
                $encuesta->publicaciones()->create($data);
            }
        }

        return redirect()->route('admin.denuncias.encuestas.index')
            ->with('success', 'Denuncia actualizada.');
    }

    public function verify(EncuestaDenuncia $encuesta)
    {
        $encuesta->load(['publicaciones', 'complainant']);

        return Inertia::render('Admin/Denuncias/Encuestas/Verify', [
            'encuesta' => $encuesta,
        ]);
    }

    public function confirmVerify(EncuestaDenuncia $encuesta)
    {
        $encuesta->update(['verificada' => true]);

        return redirect()->route('admin.denuncias.encuestas.index')
            ->with('success', 'Denuncia verificada correctamente.');
    }

    public function destroy(EncuestaDenuncia $encuesta)
    {
        $encuesta->delete();

        return redirect()->route('admin.denuncias.encuestas.index')
            ->with('success', 'Denuncia eliminada.');
    }

    public function generateWord(EncuestaDenuncia $encuesta)
    {
        $encuesta->load(['publicaciones', 'complainant']);

        $templatePath = public_path('DENUNCIA-ENCUESTAS.docx');

        if (!file_exists($templatePath)) {
            abort(404, 'Plantilla de denuncia no encontrada.');
        }

        @mkdir(storage_path('app/public/tmp'), 0755, true);

        $processor = new \PhpOffice\PhpWord\TemplateProcessor($templatePath);

        $complainant = $encuesta->complainant;
        $publicaciones = $encuesta->publicaciones->sortBy('consecutivo')->values();

        $fecha = \Carbon\Carbon::parse($encuesta->created_at);
        $fechaStr = $fecha->format('d') . ' de ' . $this->meses()[(int) $fecha->format('m')] . ' de ' . $fecha->format('Y');

        $paginaNombre = $publicaciones->first()->pagina ?? '';
        $redSocial = $publicaciones->first()->red_social ?? '';

        $fechas = $publicaciones->pluck('fecha_publicacion')->map(function ($f) {
            $d = \Carbon\Carbon::parse($f);
            return $d->format('d') . ' de ' . $this->meses()[(int) $d->format('m')] . ' de ' . $d->format('Y');
        });

        $fechasReiteradas = $fechas->implode(', ');

        $enlaces = $publicaciones->pluck('enlace');

        $processor->setValues([
            'denuncia_fecha' => $fechaStr,
            'denunciante_nombre' => $complainant->nombre_completo,
            'denunciante_clave_elector' => $complainant->clave_elector,
            'denunciante_email' => $complainant->email ?? '',
            'pagina_nombre' => $paginaNombre,
            'pagina_red_social' => $redSocial,
            'fecha_publicacion' => $fechas->first() ?? '',
            'fechas_publicaciones_reiteradas' => $fechasReiteradas,
        ]);

        for ($i = 0; $i < 3; $i++) {
            $num = $i + 1;
            $processor->setValue("enlace{$num}", $enlaces->get($i, ''));
            $processor->setValue("fecha{$num}", $fechas->get($i, ''));
        }

        $restCount = max(0, $publicaciones->count() - 3);
        $processor->setValue('enlaceN.', $restCount > 0 ? "Y {$restCount} enlaces más que se detallan en las pruebas documentales anexas." : '');

        $outputPath = storage_path('app/public/tmp/encuesta_' . uniqid() . '.docx');
        $processor->saveAs($outputPath);

        $outputName = 'denuncia_encuestas_' . $encuesta->id . '.docx';

        return response()->download($outputPath, $outputName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ])->deleteFileAfterSend();
    }

    private function meses(): array
    {
        return [
            1 => 'enero', 2 => 'febrero', 3 => 'marzo', 4 => 'abril',
            5 => 'mayo', 6 => 'junio', 7 => 'julio', 8 => 'agosto',
            9 => 'septiembre', 10 => 'octubre', 11 => 'noviembre', 12 => 'diciembre',
        ];
    }
}
