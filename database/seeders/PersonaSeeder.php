<?php

namespace Database\Seeders;

use App\Models\Grupo;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class PersonaSeeder extends Seeder
{
    public function run(): void
    {
        $path = public_path('estructura-heriberto.xlsx');

        if (! file_exists($path)) {
            $this->command?->error('PersonaSeeder: no se encontró public/estructura-heriberto.xlsx');

            return;
        }

        $spreadsheet = IOFactory::load($path);
        $rows = $spreadsheet->getActiveSheet()->toArray(null, true, true, false);

        $grupo = Grupo::firstOrCreate(['nombre' => '2021']);

        DB::transaction(function () use ($rows, $grupo) {
            DB::table('grupo_persona')->delete();
            DB::table('personas')->delete();

            $now = now();
            $records = [];
            $sinCelularValido = 0;

            // Los encabezados están en la fila 5 (índice 4) y los datos inician en la fila 6.
            for ($i = 5; $i < count($rows); $i++) {
                $row = array_pad($rows[$i], 13, null);

                $nombre = $this->limpiar($row[0]);

                if ($nombre === null) {
                    continue;
                }

                $celular = $this->normalizarCelular($row[5]);

                if ($celular === null) {
                    $sinCelularValido++;
                }

                $records[] = [
                    'user_id' => null,
                    'nombre' => $nombre,
                    'apellido_paterno' => $this->limpiar($row[1]),
                    'apellido_materno' => $this->limpiar($row[2]),
                    'sexo' => $this->limpiar($row[3]),
                    'fecha_nacimiento' => $this->normalizarFecha($row[4]),
                    'celular' => $celular,
                    'curp' => null,
                    'clave_elector' => null,
                    'domicilio' => $this->limpiar($row[6]),
                    'colonia' => $this->limpiar($row[7]),
                    'localidad' => $this->limpiar($row[8]),
                    'seccion' => $this->normalizarSeccion($row[10]),
                    'promotor' => $this->limpiar($row[11]),
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            foreach (array_chunk($records, 500) as $chunk) {
                DB::table('personas')->insert($chunk);
            }

            $pivot = DB::table('personas')
                ->pluck('id')
                ->map(fn ($id) => [
                    'grupo_id' => $grupo->id,
                    'persona_id' => $id,
                ])
                ->all();

            foreach (array_chunk($pivot, 1000) as $chunk) {
                DB::table('grupo_persona')->insert($chunk);
            }

            $this->command?->info(
                'PersonaSeeder: '.count($records).' registros importados. '.
                $sinCelularValido.' registros quedaron sin celular válido.'
            );
        });
    }

    private function limpiar(mixed $valor): ?string
    {
        if ($valor === null) {
            return null;
        }

        $texto = trim((string) $valor);

        return $texto === '' ? null : $texto;
    }

    private function normalizarCelular(mixed $valor): ?string
    {
        if ($valor === null) {
            return null;
        }

        $digitos = preg_replace('/\D/', '', (string) $valor);

        return strlen($digitos) === 10 ? $digitos : null;
    }

    private function normalizarSeccion(mixed $valor): ?string
    {
        $seccion = $this->limpiar($valor);

        if ($seccion === null) {
            return null;
        }

        if (ctype_digit($seccion)) {
            return ltrim($seccion, '0') ?: '0';
        }

        return $seccion;
    }

    private function normalizarFecha(mixed $valor): ?string
    {
        if ($valor === null || trim((string) $valor) === '') {
            return null;
        }

        try {
            if ($valor instanceof \DateTimeInterface) {
                return Carbon::instance($valor)->format('Y-m-d');
            }

            if (is_numeric($valor)) {
                return Carbon::instance(ExcelDate::excelToDateTimeObject($valor))->format('Y-m-d');
            }

            $texto = trim((string) $valor);

            foreach (['n/j/Y', 'n-j-Y', 'Y-m-d', 'd/m/Y'] as $formato) {
                try {
                    return Carbon::createFromFormat($formato, $texto)->format('Y-m-d');
                } catch (\Throwable) {
                    // Se prueba el siguiente formato.
                }
            }

            return Carbon::parse($texto)->format('Y-m-d');
        } catch (\Throwable) {
            return null;
        }
    }
}
