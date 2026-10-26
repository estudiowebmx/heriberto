<?php

namespace App\Http\Controllers;

use App\Models\Persona;
use App\Models\Seccion;
use Illuminate\Http\Request;
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

        $column = match ($tipo) {
            'distrito_local' => 'secciones.distrito_local',
            'distrito_federal' => 'secciones.distrito_federal',
            'municipio' => 'secciones.municipio',
            'demarcacion' => "CONCAT(secciones.municipio, ' - D', secciones.demarcacion)",
            default => 'personas.seccion',
        };

        $query = Persona::query()
            ->join('secciones', 'personas.seccion', '=', 'secciones.seccion')
            ->selectRaw("{$column} as clave, COUNT(*) as total")
            ->groupBy('clave')
            ->orderByDesc('total')
            ->limit(50);

        if ($tipo === 'demarcacion') {
            $query->whereNotNull('secciones.demarcacion')
                ->where('secciones.demarcacion', '!=', '');
        } else {
            $query->whereNotNull($column)->where($column, '!=', '');
        }

        return response()->json($query->get());
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
            });

        if ($tipo === 'demarcacion') {
            $query->where('demarcacion', '!=', '');
        }

        $query = $query->groupBy('clave')->get();

        return response()->json($query);
    }

    public function dashboard()
    {
        $personas = Persona::query()
            ->join('secciones', 'personas.seccion', '=', 'secciones.seccion')
            ->selectRaw("CONCAT(secciones.municipio, ' - D', secciones.demarcacion) as clave, COUNT(*) as total")
            ->whereNotNull('secciones.demarcacion')
            ->where('secciones.demarcacion', '!=', '')
            ->where('secciones.municipio', 'XALISCO')
            ->groupBy('clave')
            ->pluck('total', 'clave');

        $metas = Seccion::selectRaw("CONCAT(municipio, ' - D', demarcacion) as clave, SUM(meta) as meta")
            ->whereNotNull('demarcacion')
            ->where('demarcacion', '!=', '')
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

        usort($municipios, fn ($a, $b) => $b['pct'] - $a['pct']);

        $seccionesXalisco = Persona::query()
            ->join('secciones', 'personas.seccion', '=', 'secciones.seccion')
            ->selectRaw('personas.seccion as seccion, COUNT(*) as total')
            ->where('secciones.municipio', 'XALISCO')
            ->whereNotNull('personas.seccion')
            ->where('personas.seccion', '!=', '')
            ->groupBy('personas.seccion')
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

        usort($secciones, fn ($a, $b) => $b['pct'] - $a['pct']);

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
