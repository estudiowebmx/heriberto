<?php

namespace Database\Seeders;

use App\Models\Seccion;
use Illuminate\Database\Seeder;

class SeccionesSeeder extends Seeder
{
    public function run(): void
    {
        $path = public_path('secciones_municipios_distritos.csv');
        $file = fopen($path, 'r');

        fgetcsv($file); // skip header

        $batch = [];
        while (($row = fgetcsv($file)) !== false) {
            $batch[] = [
                'seccion' => $row[0],
                'municipio' => $row[1],
                'distrito_local' => $row[2],
                'distrito_federal' => $row[3],
                'created_at' => now(),
                'updated_at' => now(),
            ];

            if (count($batch) >= 200) {
                Seccion::insert($batch);
                $batch = [];
            }
        }

        if (!empty($batch)) {
            Seccion::insert($batch);
        }

        fclose($file);
    }
}
