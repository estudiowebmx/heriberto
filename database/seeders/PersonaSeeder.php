<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PersonaSeeder extends Seeder
{
    public function run(): void
    {
        $json = file_get_contents(__DIR__.'/data/personas.json');
        $personas = json_decode($json, true);

        DB::table('personas')->truncate();

        $now = now();

        $chunks = array_chunk($personas, 500);
        foreach ($chunks as $chunk) {
            $rows = array_map(function ($p) use ($now) {
                return [
                    'nombre'            => $p['nombre'] ?? '',
                    'apellido_paterno'  => $p['apellido_paterno'] ?? '',
                    'apellido_materno'  => $p['apellido_materno'] ?? '',
                    'telefono'          => $p['telefono'] ?? '0',
                    'seccion'           => $p['seccion'] ?? '',
                    'promotor'          => $p['promotor'] ?? '',
                    'municipio'         => $p['municipio'],
                    'distrito_local'    => $p['distrito_local'],
                    'distrito_federal'  => $p['distrito_federal'],
                    'demarcacion'       => $p['demarcacion'],
                    'created_at'        => $now,
                    'updated_at'        => $now,
                ];
            }, $chunk);

            DB::table('personas')->insert($rows);
        }

        $this->command?->info('PersonaSeeder: '.count($personas).' registros insertados.');
    }
}
