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
        $personas = Persona::selectRaw("CONCAT(municipio, ' - D', demarcacion) as clave, COUNT(*) as total")
            ->whereNotNull('demarcacion')
            ->where('municipio', 'XALISCO')
            ->groupBy('clave')
            ->pluck('total', 'clave');

        $metas = Seccion::selectRaw("CONCAT(municipio, ' - D', demarcacion) as clave, SUM(meta) as meta")
            ->whereNotNull('demarcacion')
            ->where('municipio', 'XALISCO')
            ->groupBy('clave')
            ->pluck('meta', 'clave');

        $totalPromovidos = $personas->sum();
        $totalMeta = $metas->sum();
        $estatal = ['total' => $totalPromovidos, 'meta' => $totalMeta, 'pct' => $totalMeta > 0 ? round(($totalPromovidos / $totalMeta) * 100) : 0];

        $municipios = [];
        foreach ($metas as $clave => $meta) {
            $prom = $personas[$clave] ?? 0;
            $municipios[] = [
                'municipio' => $clave,
                'total' => (int) $prom,
                'meta' => (int) $meta,
                'pct' => $meta > 0 ? round(($prom / $meta) * 100) : 0,
            ];
        }

        usort($municipios, fn($a, $b) => $b['pct'] - $a['pct']);

        $seccionesXalisco = Persona::selectRaw('seccion, COUNT(*) as total')
            ->where('municipio', 'XALISCO')
            ->whereNotNull('seccion')
            ->where('seccion', '!=', '')
            ->groupBy('seccion')
            ->pluck('total', 'seccion');

        $metasSeccion = Seccion::where('municipio', 'XALISCO')
            ->pluck('meta', 'seccion');

        $secciones = [];
        foreach ($metasSeccion as $sec => $meta) {
            $prom = $seccionesXalisco[$sec] ?? 0;
            $secciones[] = [
                'seccion' => $sec,
                'total' => (int) $prom,
                'meta' => (int) $meta,
                'pct' => $meta > 0 ? round(($prom / $meta) * 100) : 0,
            ];
        }

        usort($secciones, fn($a, $b) => $b['pct'] - $a['pct']);

        return response()->json([
            'estatal' => $estatal,
            'municipios' => $municipios,
            'secciones' => $secciones,
        ]);
    }

    public function seccionesMunicipios()
    {
        $data = Seccion::pluck('municipio', 'seccion');
        return response()->json($data);
    }
}
