<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Administrador',
            'email' => 'admin@heriberto.sii.mx',
            'password' => 'peluquera',
            'role' => 'admin',
        ]);

        $this->call([
            CandidateInformationSeeder::class,
            AgendaEventSeeder::class,
            SeccionesSeeder::class,
            ColoniasSeeder::class,
        ]);
    }
}
