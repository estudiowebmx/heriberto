<?php

namespace Database\Seeders;

use App\Models\Grupo;
use Illuminate\Database\Seeder;

class GrupoSeeder extends Seeder
{
    public function run(): void
    {
        Grupo::firstOrCreate(['nombre' => '2021']);

        $this->command?->info('GrupoSeeder: etiqueta 2021 lista.');
    }
}
