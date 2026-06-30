<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Colonia;
use App\Models\Complainant;
use App\Models\Lona;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class LonaController extends Controller
{
    public function index()
    {
        $query = Lona::with(['evidences', 'user'])
            ->orderBy('created_at', 'desc');

        if (auth()->user()->isColaborador()) {
            $query->where('user_id', auth()->id());
        }

        return Inertia::render('Admin/Denuncias/Lonas/Index', [
            'lonas' => $query->paginate(15),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Denuncias/Lonas/Create', [
            'complainants' => Complainant::orderBy('nombre_completo')
                ->get(['id', 'nombre_completo', 'clave_elector', 'domicilio', 'email', 'sexo']),
            'colonias' => Colonia::where('municipio', 'TEPIC')
                ->select('nombre')
                ->distinct()
                ->orderBy('nombre')
                ->pluck('nombre'),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'complainant_id' => 'required|exists:complainants,id',
            'evidences' => 'required|array|min:1',
            'evidences.*.calle_y_num' => 'required|string|max:500',
            'evidences.*.colonia' => 'required|string|max:255',
            'evidences.*.condicion_inmueble' => 'required|string|max:50',
            'evidences.*.photo' => 'required|file|mimes:jpg,jpeg,png,webp|max:15360',
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

        $lona = Lona::create([
            'denunciante_nombre' => $complainant->nombre_completo,
            'denunciante_clave_elector' => $complainant->clave_elector,
            'denunciante_domicilio' => $complainant->domicilio,
            'denunciante_email' => $complainant->email,
            'denunciante_sexo' => $complainant->sexo ?? 'M',
            'registered_by' => auth()->user()->name,
            'user_id' => auth()->id(),
            'dia_numero' => $diaNumero,
            'mes' => $mes,
        ]);

        foreach ($validated['evidences'] as $i => $evidenceData) {
            $photoPath = $evidenceData['photo']->store('lonas', 'public');

            $lona->evidences()->create([
                'consecutivo' => $i + 1,
                'calle_y_num' => $evidenceData['calle_y_num'],
                'colonia' => $evidenceData['colonia'],
                'condicion_inmueble' => $evidenceData['condicion_inmueble'],
                'photo_path' => $photoPath,
            ]);
        }

        return redirect()->route('admin.denuncias.lonas.index')
            ->with('success', 'Denuncia de lonas registrada correctamente.');
    }

    public function show(Lona $lona)
    {
        $lona->load(['evidences', 'user']);

        return Inertia::render('Admin/Denuncias/Lonas/Show', [
            'lona' => $lona,
        ]);
    }

    public function edit(Lona $lona)
    {
        $lona->load('evidences');

        return Inertia::render('Admin/Denuncias/Lonas/Edit', [
            'lona' => $lona,
            'complainants' => Complainant::orderBy('nombre_completo')
                ->get(['id', 'nombre_completo', 'clave_elector', 'domicilio', 'email', 'sexo']),
            'colonias' => Colonia::where('municipio', 'TEPIC')
                ->select('nombre')
                ->distinct()
                ->orderBy('nombre')
                ->pluck('nombre'),
        ]);
    }

    public function update(Request $request, Lona $lona)
    {
        $validated = $request->validate([
            'complainant_id' => 'required|exists:complainants,id',
            'evidences' => 'required|array|min:1',
            'evidences.*.id' => 'nullable|integer|exists:lona_evidences,id',
            'evidences.*.calle_y_num' => 'required|string|max:500',
            'evidences.*.colonia' => 'required|string|max:255',
            'evidences.*.condicion_inmueble' => 'required|string|max:50',
            'evidences.*.photo' => 'nullable|file|mimes:jpg,jpeg,png,webp|max:15360',
        ]);

        $complainant = Complainant::findOrFail($validated['complainant_id']);

        $lona->update([
            'denunciante_nombre' => $complainant->nombre_completo,
            'denunciante_clave_elector' => $complainant->clave_elector,
            'denunciante_domicilio' => $complainant->domicilio,
            'denunciante_email' => $complainant->email,
            'denunciante_sexo' => $complainant->sexo ?? 'M',
        ]);

        $existingIds = collect($validated['evidences'])
            ->pluck('id')
            ->filter()
            ->toArray();

        $lona->evidences()->whereNotIn('id', $existingIds)->get()->each(function ($ev) {
            Storage::disk('public')->delete($ev->photo_path);
            $ev->delete();
        });

        foreach ($validated['evidences'] as $i => $evidenceData) {
            $data = [
                'consecutivo' => $i + 1,
                'calle_y_num' => $evidenceData['calle_y_num'],
                'colonia' => $evidenceData['colonia'],
                'condicion_inmueble' => $evidenceData['condicion_inmueble'],
            ];

            if (!empty($evidenceData['photo'])) {
                $data['photo_path'] = $evidenceData['photo']->store('lonas', 'public');
            }

            if (!empty($evidenceData['id'])) {
                $existing = $lona->evidences()->find($evidenceData['id']);
                if ($existing) {
                    if (!empty($evidenceData['photo'])) {
                        Storage::disk('public')->delete($existing->photo_path);
                    }
                    $existing->update($data);
                }
            } else {
                $lona->evidences()->create($data);
            }
        }

        return redirect()->route('admin.denuncias.lonas.index')
            ->with('success', 'Denuncia actualizada.');
    }

    public function verify(Lona $lona)
    {
        $lona->load('evidences');

        return Inertia::render('Admin/Denuncias/Lonas/Verify', [
            'lona' => $lona,
        ]);
    }

    public function confirmVerify(Lona $lona)
    {
        $lona->update(['verificada' => true]);

        return redirect()->route('admin.denuncias.lonas.index')
            ->with('success', 'Denuncia verificada correctamente.');
    }

    public function destroy(Lona $lona)
    {
        foreach ($lona->evidences as $evidence) {
            Storage::disk('public')->delete($evidence->photo_path);
        }

        $lona->delete();

        return redirect()->route('admin.denuncias.lonas.index')
            ->with('success', 'Denuncia eliminada.');
    }

    public function generateWord(Lona $lona)
    {
        $lona->load('evidences');

        $templatePath = public_path('DENUNCIA-LONAS.docx');

        if (!file_exists($templatePath)) {
            abort(404, 'Plantilla de denuncia no encontrada.');
        }

        @mkdir(storage_path('app/public/tmp'), 0755, true);

        $processor = new \PhpOffice\PhpWord\TemplateProcessor($templatePath);

        $sexoPostfijo = ($lona->denunciante_sexo ?? 'M') === 'M' ? 'o' : 'a';

        $processor->setValues([
            'dia_numero' => $lona->dia_numero,
            'mes' => $lona->mes,
            'denunciante_nombre' => $lona->denunciante_nombre,
            'sexo_postfijo' => $sexoPostfijo,
            'denunciante_clave_elector' => $lona->denunciante_clave_elector,
            'denunciante_domicilio' => $lona->denunciante_domicilio,
            'denunciante_email' => $lona->denunciante_email ?? '',
        ]);

        $evidences = $lona->evidences->sortBy('consecutivo')->values();

        $tableData = [];
        foreach ($evidences as $ev) {
            $tableData[] = [
                'lona_consecutivo' => $ev->consecutivo,
                'lona_calle_y_num' => $ev->calle_y_num,
                'lona_colonia' => $ev->colonia,
                'lona_condicion_inmueble' => $ev->condicion_inmueble,
            ];
        }

        $processor->cloneRowAndSetValues('lona_consecutivo', $tableData);

        // Segunda tabla: después del primer cloneRowAndSetValues los placeholders
        // de la primera tabla ya son texto real. Los que quedan corresponden a la
        // segunda tabla. Se llama de nuevo para clonarlos y reemplazarlos ahí.
        $processor->cloneRowAndSetValues('lona_consecutivo', $tableData);

        $evidenceCount = $evidences->count();
        if ($evidenceCount > 0) {
            $processor->cloneBlock('foto_block', $evidenceCount, true, true);

            foreach ($evidences as $ev) {
                $num = $ev->consecutivo;
                $photoPath = Storage::disk('public')->path($ev->photo_path);

                $processor->setValue('lona_consecutivo#' . $num, $ev->consecutivo);
                $processor->setValue('lona_concecutivo#' . $num, $ev->consecutivo);
                $processor->setValue('lona_calle_y_num#' . $num, $ev->calle_y_num);
                $processor->setValue('lona_colonia#' . $num, $ev->colonia);
                $processor->setValue('lona_condicion_inmueble#' . $num, $ev->condicion_inmueble);

                if (file_exists($photoPath)) {
                    $processor->setImageValue('lona_imagen#' . $num, [
                        'path' => $photoPath,
                        'width' => 500,
                        'height' => 500,
                        'ratio' => true,
                    ]);
                }
            }
        }

        $outputPath = storage_path('app/public/tmp/lona_' . uniqid() . '.docx');
        $processor->saveAs($outputPath);

        $outputName = 'denuncia_lonas_' . $lona->id . '.docx';

        return response()->download($outputPath, $outputName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ])->deleteFileAfterSend();
    }
}
