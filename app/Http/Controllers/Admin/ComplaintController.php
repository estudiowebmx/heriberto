<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Complainant;
use App\Models\Complaint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ComplaintController extends Controller
{
    public function index()
    {
        $query = Complaint::with(['complainant', 'evidences'])
            ->orderBy('created_at', 'desc');

        if (auth()->user()->isColaborador()) {
            $query->where('user_id', auth()->id());
        }

        return Inertia::render('Admin/Denuncias/Index', [
            'complaints' => $query->paginate(20),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Denuncias/ComplaintCreate', [
            'complainants' => Complainant::orderBy('nombre_completo')->get(['id', 'nombre_completo']),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'complainant_id' => 'required|exists:complainants,id',
            'colonia' => 'required|string|max:255',
            'cac_president_name' => 'required|string|max:255',
            'evidences' => 'required|array|min:1',
            'evidences.*.address' => 'required|string|max:500',
            'evidences.*.leyenda' => 'required|string|max:255',
            'evidences.*.is_inhabited' => 'required|boolean',
            'evidences.*.is_cac_president' => 'boolean',
            'evidences.*.photo' => 'required|file|mimes:jpg,jpeg,png,webp|max:15360',
        ]);

        $complaint = Complaint::create([
            'user_id' => auth()->id(),
            'complainant_id' => $validated['complainant_id'],
            'colonia' => $validated['colonia'],
            'cac_president_name' => $validated['cac_president_name'],
        ]);

        foreach ($validated['evidences'] as $evidenceData) {
            $photoPath = $evidenceData['photo']->store('evidences', 'public');

            $complaint->evidences()->create([
                'address' => $evidenceData['address'],
                'leyenda' => $evidenceData['leyenda'],
                'is_inhabited' => $evidenceData['is_inhabited'],
                'is_cac_president' => $evidenceData['is_cac_president'] ?? false,
                'photo_path' => $photoPath,
            ]);
        }

        return redirect()->route('admin.denuncias.index')->with('success', 'Denuncia registrada.');
    }

    public function show(Complaint $complaint)
    {
        $complaint->load(['complainant', 'evidences']);

        return Inertia::render('Admin/Denuncias/ComplaintShow', [
            'complaint' => $complaint,
        ]);
    }

    public function edit(Complaint $complaint)
    {
        $complaint->load(['complainant', 'evidences']);

        return Inertia::render('Admin/Denuncias/ComplaintEdit', [
            'complaint' => $complaint,
            'complainants' => Complainant::orderBy('nombre_completo')->get(['id', 'nombre_completo']),
        ]);
    }

    public function update(Request $request, Complaint $complaint)
    {
        $validated = $request->validate([
            'complainant_id' => 'required|exists:complainants,id',
            'colonia' => 'required|string|max:255',
            'cac_president_name' => 'required|string|max:255',
            'evidences' => 'required|array|min:1',
            'evidences.*.id' => 'nullable|exists:evidences,id',
            'evidences.*.address' => 'required|string|max:500',
            'evidences.*.leyenda' => 'required|string|max:255',
            'evidences.*.is_inhabited' => 'required|boolean',
            'evidences.*.is_cac_president' => 'boolean',
            'evidences.*.photo' => 'nullable|file|mimes:jpg,jpeg,png,webp|max:15360',
        ]);

        $complaint->update([
            'complainant_id' => $validated['complainant_id'],
            'colonia' => $validated['colonia'],
            'cac_president_name' => $validated['cac_president_name'],
        ]);

        $existingIds = collect($validated['evidences'])
            ->pluck('id')
            ->filter()
            ->values()
            ->toArray();

        $complaint->evidences()->whereNotIn('id', $existingIds)->get()->each(function ($evidence) {
            Storage::disk('public')->delete($evidence->photo_path);
            $evidence->delete();
        });

        foreach ($validated['evidences'] as $evidenceData) {
            $data = [
                'address' => $evidenceData['address'],
                'leyenda' => $evidenceData['leyenda'],
                'is_inhabited' => $evidenceData['is_inhabited'],
                'is_cac_president' => $evidenceData['is_cac_president'] ?? false,
            ];

            if (!empty($evidenceData['photo'])) {
                $data['photo_path'] = $evidenceData['photo']->store('evidences', 'public');
            }

            if (!empty($evidenceData['id'])) {
                $existing = $complaint->evidences()->find($evidenceData['id']);
                if ($existing) {
                    if (!empty($evidenceData['photo'])) {
                        Storage::disk('public')->delete($existing->photo_path);
                    }
                    $existing->update($data);
                }
            } else {
                if (empty($data['photo_path'])) {
                    continue;
                }
                $complaint->evidences()->create($data);
            }
        }

        return redirect()->route('admin.denuncias.index')->with('success', 'Denuncia actualizada.');
    }

    public function destroy(Complaint $complaint)
    {
        foreach ($complaint->evidences as $evidence) {
            Storage::disk('public')->delete($evidence->photo_path);
        }

        $complaint->delete();

        return redirect()->route('admin.denuncias.index')->with('success', 'Denuncia eliminada.');
    }

    public function generateWord(Complaint $complaint)
    {
        $outputPath = $this->buildProcessedDocx($complaint);
        $outputName = 'denuncia_' . $complaint->id . '.docx';

        return response()->download($outputPath, $outputName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ])->deleteFileAfterSend();
    }

    public function generatePdf(Complaint $complaint)
    {
        $docxPath = $this->buildProcessedDocx($complaint);

        $phpWord = \PhpOffice\PhpWord\IOFactory::load($docxPath);

        $pdfPath = storage_path('app/public/tmp/denuncia_' . uniqid() . '.pdf');

        $renderer = new \PhpOffice\PhpWord\Writer\PDF\DomPDF($phpWord);
        $renderer->save($pdfPath);

        @unlink($docxPath);

        $outputName = 'denuncia_' . $complaint->id . '.pdf';

        return response()->download($pdfPath, $outputName, [
            'Content-Type' => 'application/pdf',
        ])->deleteFileAfterSend();
    }

    private function buildProcessedDocx(Complaint $complaint): string
    {
        $complaint->load(['complainant', 'evidences']);

        $templatePath = public_path('ejemplo_denuncia.docx');

        if (!file_exists($templatePath)) {
            throw new \RuntimeException('Plantilla de denuncia no encontrada.');
        }

        @mkdir(storage_path('app/public/tmp'), 0755, true);

        $processor = new \PhpOffice\PhpWord\TemplateProcessor($templatePath);

        $evidenceList = $this->getSortedEvidences($complaint);
        $uniqueLeyendas = $complaint->evidences->pluck('leyenda')->unique()->values();
        $sexoChar = ($complaint->complainant->sexo ?? 'M') === 'M' ? 'o' : 'a';
        $ciudadano = $sexoChar === 'o' ? 'ciudadano' : 'ciudadana';
        $leyendaPlural = $uniqueLeyendas->count() > 1 ? 's' : '';

        $processor->setValues([
            'denunciante_nombre' => $complaint->complainant->nombre_completo,
            'denunciate_nombre' => $complaint->complainant->nombre_completo,
            'denunciante_clave_elector' => $complaint->complainant->clave_elector,
            'denunciante_domicilio' => $complaint->complainant->domicilio,
            'denunciante_correo' => $complaint->complainant->email ?? '',
            'denunciante_sexo' => $sexoChar,
            'fecha' => '26 de junio de 2026',
            'ciudadano' => $ciudadano,
            'cac_nombre_presidente' => $complaint->cac_president_name,
            'cac_colonia' => $complaint->colonia,
            'texto_leyendas' => $complaint->formatted_leyendas_text,
            'lona_leyenda' => $uniqueLeyendas->first() ?? '',
            'leyenda_plural' => $leyendaPlural,
        ]);

        $tableData = [];
        foreach ($evidenceList as $i => $ev) {
            $tableData[] = [
                'lona_clave' => "Anexo fotogr\xC3\xA1fico " . ($i + 1),
                'cac_colonia' => $i === 0 ? $complaint->colonia : '',
                'cac_domicilio_presidente' => $ev->address,
                'inmueble_condicion' => $ev->is_inhabited ? 'Habitado' : 'Deshabitado',
                'lona_leyenda' => $ev->leyenda,
            ];
        }

        $processor->cloneRowAndSetValues('lona_clave', $tableData);

        $evidenceCount = count($evidenceList);

        if ($evidenceCount > 0) {
            $processor->cloneBlock('foto_block', $evidenceCount, true, true);

            foreach ($evidenceList as $i => $ev) {
                $num = $i + 1;
                $photoPath = Storage::disk('public')->path($ev->photo_path);

                if (file_exists($photoPath)) {
                    $processor->setImageValue('foto_imagen#' . $num, [
                        'path' => $photoPath,
                        'width' => 500,
                        'height' => 500,
                        'ratio' => true,
                    ]);
                }

                $processor->setValue('foto_titulo#' . $num, "Anexo Fotogr\xC3\xA1fico " . $num);
                $processor->setValue('foto_domicilio#' . $num, $ev->address);
                $processor->setValue('foto_colonia#' . $num, $complaint->colonia);
            }
        }

        $outputPath = storage_path('app/public/tmp/denuncia_' . uniqid() . '.docx');
        $processor->saveAs($outputPath);

        return $outputPath;
    }

    private function getSortedEvidences(Complaint $complaint): array
    {
        $list = $complaint->evidences->all();
        usort($list, function ($a, $b) {
            if ($a->is_cac_president && !$b->is_cac_president) return -1;
            if (!$a->is_cac_president && $b->is_cac_president) return 1;
            return $a->id <=> $b->id;
        });
        return $list;
    }
}
