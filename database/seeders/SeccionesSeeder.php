<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SeccionesSeeder extends Seeder
{
    public function run(): void
    {
        $sections = $this->leerCatalogo();
        $defaults = $this->valoresPorMunicipio($sections);

        $this->completarDemarcaciones($sections, $defaults);

        $insertados = 0;
        $actualizados = 0;

        foreach ($sections as $seccion => $datos) {
            $existente = DB::table('secciones')->where('seccion', (string) $seccion)->first();

            if ($existente) {
                DB::table('secciones')->where('id', $existente->id)->update([
                    'municipio' => $datos['municipio'],
                    'distrito_local' => $datos['distrito_local'],
                    'distrito_federal' => $datos['distrito_federal'],
                    'demarcacion' => $datos['demarcacion'],
                    'updated_at' => now(),
                ]);
                $actualizados++;

                continue;
            }

            DB::table('secciones')->insert([
                'seccion' => (string) $seccion,
                'municipio' => $datos['municipio'],
                'distrito_local' => $datos['distrito_local'],
                'distrito_federal' => $datos['distrito_federal'],
                'demarcacion' => $datos['demarcacion'],
                'meta' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $insertados++;
        }

        $this->command?->info(
            "SeccionesSeeder: {$actualizados} secciones actualizadas y {$insertados} insertadas."
        );
    }

    /**
     * @return array<string, array{municipio: string, distrito_local: string|null, distrito_federal: string|null, demarcacion: string|null}>
     */
    private function leerCatalogo(): array
    {
        $path = public_path('secciones_municipios_distritos.csv');
        $sections = [];

        if (! file_exists($path)) {
            return $sections;
        }

        $file = fopen($path, 'r');
        fgetcsv($file);

        while (($row = fgetcsv($file)) !== false) {
            $seccion = $this->normalizarSeccion($row[0] ?? null);

            if ($seccion === null) {
                continue;
            }

            $sections[$seccion] = [
                'municipio' => $this->normalizarMunicipio($row[1] ?? ''),
                'distrito_local' => trim((string) ($row[2] ?? '')) ?: null,
                'distrito_federal' => trim((string) ($row[3] ?? '')) ?: null,
                'demarcacion' => null,
            ];
        }

        fclose($file);

        return $sections;
    }

    /**
     * @param  array<string, array{municipio: string, distrito_local: string|null, distrito_federal: string|null, demarcacion: string|null}>  $sections
     * @return array<string, array{distrito_local: string|null, distrito_federal: string|null}>
     */
    private function valoresPorMunicipio(array $sections): array
    {
        $conteo = [];

        foreach ($sections as $datos) {
            $municipio = $datos['municipio'];

            if ($municipio === '') {
                continue;
            }

            $llave = $datos['distrito_local'].'|'.$datos['distrito_federal'];
            $conteo[$municipio][$llave] = ($conteo[$municipio][$llave] ?? 0) + 1;
        }

        $defaults = [];

        foreach ($conteo as $municipio => $valores) {
            arsort($valores);
            $llave = array_key_first($valores);
            [$distritoLocal, $distritoFederal] = explode('|', $llave, 2);

            $defaults[$municipio] = [
                'distrito_local' => $distritoLocal ?: null,
                'distrito_federal' => $distritoFederal ?: null,
            ];
        }

        return $defaults;
    }

    /**
     * @param  array<string, array{municipio: string, distrito_local: string|null, distrito_federal: string|null, demarcacion: string|null}>  $sections
     * @param  array<string, array{distrito_local: string|null, distrito_federal: string|null}>  $defaults
     */
    private function completarDemarcaciones(array &$sections, array $defaults): void
    {
        $path = public_path('seccion_demarcacion_nayarit.csv');

        if (! file_exists($path)) {
            return;
        }

        $file = fopen($path, 'r');
        fgetcsv($file);

        while (($row = fgetcsv($file)) !== false) {
            $seccion = $this->normalizarSeccion($row[0] ?? null);

            if ($seccion === null) {
                continue;
            }

            $municipio = $this->normalizarMunicipio($row[1] ?? '');
            $demarcacion = trim((string) ($row[2] ?? '')) ?: null;

            if (! isset($sections[$seccion])) {
                $default = $defaults[$municipio] ?? [
                    'distrito_local' => null,
                    'distrito_federal' => null,
                ];

                $sections[$seccion] = [
                    'municipio' => $municipio,
                    'distrito_local' => $default['distrito_local'] ?? '0',
                    'distrito_federal' => $default['distrito_federal'] ?? '0',
                    'demarcacion' => null,
                ];
            }

            $sections[$seccion]['demarcacion'] = $demarcacion;
        }

        fclose($file);
    }

    private function normalizarSeccion(mixed $valor): ?string
    {
        $seccion = trim((string) $valor);

        if ($seccion === '') {
            return null;
        }

        return ltrim($seccion, '0') ?: '0';
    }

    private function normalizarMunicipio(mixed $valor): string
    {
        $municipio = mb_strtoupper(trim((string) $valor), 'UTF-8');

        return strtr($municipio, [
            'Á' => 'A',
            'É' => 'E',
            'Í' => 'I',
            'Ó' => 'O',
            'Ú' => 'U',
            'Ü' => 'U',
            'À' => 'A',
            'È' => 'E',
            'Ì' => 'I',
            'Ò' => 'O',
            'Ù' => 'U',
        ]);
    }
}
