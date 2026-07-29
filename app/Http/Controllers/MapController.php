<?php

namespace App\Http\Controllers;

use App\Models\Persona;
use App\Models\Seccion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class MapController extends Controller
{
    public function index()
    {
        return Inertia::render('Mapa/Index');
    }

    public function ranking(Request $request)
    {
        $tipo = $request->query('tipo', 'seccion');

        $query = Persona::query()->selectRaw(
            match ($tipo) {
                'distrito_local' => 'distrito_local as clave, COUNT(*) as total',
                'distrito_federal' => 'distrito_federal as clave, COUNT(*) as total',
                'municipio' => 'municipio as clave, COUNT(*) as total',
                'demarcacion' => "CONCAT(municipio, ' - D', demarcacion) as clave, COUNT(*) as total",
                default => 'seccion as clave, COUNT(*) as total',
            }
        )
            ->whereNotNull(match ($tipo) {
                'distrito_local' => 'distrito_local',
                'distrito_federal' => 'distrito_federal',
                'municipio' => 'municipio',
                'demarcacion' => 'demarcacion',
                default => 'seccion',
            })
            ->groupBy('clave')
            ->orderByDesc('total')
            ->limit(50)
            ->get();

        return response()->json($query);
    }

    public function metas(Request $request)
    {
        $tipo = $request->query('tipo', 'seccion');

        $query = Seccion::query()->selectRaw(
            match ($tipo) {
                'distrito_local' => 'distrito_local as clave, SUM(meta) as meta',
                'distrito_federal' => 'distrito_federal as clave, SUM(meta) as meta',
                'municipio' => 'municipio as clave, SUM(meta) as meta',
                'demarcacion' => "CONCAT(municipio, ' - D', demarcacion) as clave, SUM(meta) as meta",
                default => 'seccion as clave, SUM(meta) as meta',
            }
        )
            ->whereNotNull(match ($tipo) {
                'distrito_local' => 'distrito_local',
                'distrito_federal' => 'distrito_federal',
                'municipio' => 'municipio',
                'demarcacion' => 'demarcacion',
                default => 'seccion',
            })
            ->groupBy('clave')
            ->get();

        return response()->json($query);
    }

    public function dashboard()
    {
        $personas = Persona::selectRaw('municipio, COUNT(*) as total')
            ->whereNotNull('municipio')
            ->groupBy('municipio')
            ->pluck('total', 'municipio');

        $metas = Seccion::selectRaw('municipio, SUM(meta) as meta')
            ->groupBy('municipio')
            ->pluck('meta', 'municipio');

        // State-wide
        $totalPromovidos = $personas->sum();
        $totalMeta = $metas->sum();
        $estatal = ['total' => $totalPromovidos, 'meta' => $totalMeta, 'pct' => $totalMeta > 0 ? round(($totalPromovidos / $totalMeta) * 100) : 0];

        // Per municipio
        $municipios = [];
        foreach ($metas as $mun => $meta) {
            $prom = $personas[$mun] ?? 0;
            $municipios[] = [
                'municipio' => $mun,
                'total' => (int) $prom,
                'meta' => (int) $meta,
                'pct' => $meta > 0 ? round(($prom / $meta) * 100) : 0,
            ];
        }

        usort($municipios, fn($a, $b) => $b['pct'] - $a['pct']);

        return response()->json(['estatal' => $estatal, 'municipios' => $municipios]);
    }

    public function seccionesMunicipios()
    {
        $data = Seccion::pluck('municipio', 'seccion');
        return response()->json($data);
    }
}
