<?php

namespace Database\Seeders;

use App\Models\Colonia;
use Illuminate\Database\Seeder;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ColoniasSeeder extends Seeder
{
    public function run(): void
    {
        $path = public_path('Catalogo de Colonias.xls');

        if (!file_exists($path)) {
            $this->command?->warn('Archivo Catalogo de Colonias.xls no encontrado en public/.');
            return;
        }

        $spreadsheet = IOFactory::load($path);
        $sheet = $spreadsheet->getActiveSheet();
        $highestRow = $sheet->getHighestRow();

        $batch = [];
        $count = 0;

        for ($row = 7; $row <= $highestRow; $row++) {
            $nombre = trim((string) $sheet->getCell('J' . $row)->getValue());
            $municipio = trim((string) $sheet->getCell('F' . $row)->getValue());
            $seccion = trim((string) $sheet->getCell('G' . $row)->getValue());
            $tipoSeccion = trim((string) $sheet->getCell('H' . $row)->getValue());
            $tipoColonia = trim((string) $sheet->getCell('I' . $row)->getValue());
            $cp = trim((string) $sheet->getCell('K' . $row)->getValue());

            if ($nombre === '') {
                continue;
            }

            $batch[] = [
                'nombre' => $nombre,
                'municipio' => $municipio,
                'seccion' => $seccion,
                'tipo_seccion' => $tipoSeccion,
                'tipo_colonia' => $tipoColonia,
                'cp' => $cp,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            if (count($batch) >= 500) {
                Colonia::insert($batch);
                $count += count($batch);
                $batch = [];
            }
        }

        if (!empty($batch)) {
            Colonia::insert($batch);
            $count += count($batch);
        }

        if ($this->command) {
            $this->command->info("{$count} colonias importadas.");
        }
    }
}
