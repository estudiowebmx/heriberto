<?php

namespace Database\Seeders;

use App\Models\AgendaEvent;
use Illuminate\Database\Seeder;

class AgendaEventSeeder extends Seeder
{
    public function run(): void
    {
        $events = [
            [
                'date' => now()->addDays(3),
                'title' => 'Foro Ciudadano "El Futuro es Hoy"',
                'description' => 'Dialogaremos con ciudadanos sobre nuestras propuestas de desarrollo económico y empleo.',
                'location' => 'Auditorio Municipal',
                'type' => 'foro',
            ],
            [
                'date' => now()->addDays(7),
                'title' => 'Visita a Comunidad Rural',
                'description' => 'Recorrido por comunidades para escuchar de viva voz las necesidades del campo.',
                'location' => 'Comunidad San Miguel',
                'type' => 'visita',
            ],
            [
                'date' => now()->addDays(14),
                'title' => 'Entrevista en Radio Estatal',
                'description' => 'Participaré en el programa matutino para hablar de mis propuestas de seguridad.',
                'location' => 'Radio Estatal',
                'type' => 'entrevista',
            ],
        ];

        foreach ($events as $event) {
            AgendaEvent::create($event);
        }
    }
}
