<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Encuesta;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EncuestaController extends Controller
{
    public function index()
    {
        $encuestas = Encuesta::orderBy('created_at', 'desc')->paginate(20);
        $total = Encuesta::count();

        $stats = [
            'preocupaciones' => $this->countJsonField('preocupaciones'),
            'problema_justicia' => $this->countField('problema_justicia'),
            'errores_gobierno' => $this->countJsonField('errores_gobierno'),
            'deficiencias_servicios' => $this->countJsonField('deficiencias_servicios'),
            'total' => $total,
        ];

        return Inertia::render('Admin/Encuestas/Index', [
            'encuestas' => $encuestas,
            'stats' => $stats,
        ]);
    }

    private function countJsonField($field)
    {
        $rows = Encuesta::whereNotNull($field)->pluck($field);
        $counts = [];
        foreach ($rows as $row) {
            $items = is_string($row) ? json_decode($row, true) : $row;
            if (is_array($items)) {
                foreach ($items as $item) {
                    $counts[$item] = ($counts[$item] ?? 0) + 1;
                }
            }
        }
        arsort($counts);
        return $counts;
    }

    private function countField($field)
    {
        return Encuesta::whereNotNull($field)
            ->selectRaw("$field as label, COUNT(*) as total")
            ->groupBy($field)
            ->orderByDesc('total')
            ->pluck('total', 'label')
            ->toArray();
    }
}
