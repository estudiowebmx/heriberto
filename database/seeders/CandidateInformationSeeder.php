<?php

namespace Database\Seeders;

use App\Models\CandidateInformation;
use Illuminate\Database\Seeder;

class CandidateInformationSeeder extends Seeder
{
    public function run(): void
    {
        $info = [
            [
                'section' => 'biografia',
                'title' => '¿Quién es Heriberto Castañeda?',
                'content' => 'Soy Heriberto Castañeda, candidata a la gubernatura. Nací y crecí en este estado, conozco sus necesidades y su potencial. He dedicado mi vida al servicio público y al desarrollo comunitario, trabajando incansablemente por mejorar la calidad de vida de nuestras familias.',
                'order' => 1,
            ],
            [
                'section' => 'vision',
                'title' => 'Visión de Estado',
                'content' => 'Imagino un estado donde cada familia tenga acceso a oportunidades reales: empleo digno, educación de calidad, seguridad y servicios de salud eficientes. Mi visión es construir un gobierno transparente, cercano a la gente, que impulse el desarrollo económico sostenible y proteja a los más vulnerables.',
                'order' => 1,
            ],
            [
                'section' => 'propuestas',
                'title' => 'Seguridad',
                'content' => 'Implementaré un modelo de seguridad ciudadana con inteligencia, más policías capacitados y bien equipados, tecnología de vigilancia, y programas de prevención del delito enfocados en jóvenes. Coordinación total con los tres niveles de gobierno.',
                'order' => 1,
            ],
            [
                'section' => 'propuestas',
                'title' => 'Educación',
                'content' => 'Duplicaremos la inversión en infraestructura escolar, becas universales para estudiantes de bajos recursos, programas de inglés y tecnología desde primaria, y salarios dignos para nuestros maestros.',
                'order' => 2,
            ],
            [
                'section' => 'propuestas',
                'title' => 'Empleo y Economía',
                'content' => 'Crearemos un polo de desarrollo industrial con incentivos fiscales para empresas que generen empleos locales. Apoyo a emprendedores con créditos blandos y capacitación. Programa de empleo joven garantizado.',
                'order' => 3,
            ],
            [
                'section' => 'propuestas',
                'title' => 'Salud',
                'content' => 'Hospitales públicos con abasto total de medicamentos, telemedicina para comunidades rurales, y un programa de salud mental gratuito. Reducción de tiempos de espera en emergencias.',
                'order' => 4,
            ],
            [
                'section' => 'propuestas',
                'title' => 'Campo',
                'content' => 'Apoyo directo al productor local, tecnificación del campo, seguros de cosecha, y programas de comercialización sin intermediarios para que el campo mexicano sea rentable y digno.',
                'order' => 5,
            ],
            [
                'section' => 'mensaje',
                'title' => 'Mensaje a la ciudadanía',
                'content' => 'Este proyecto no es mío, es de todos. Los invito a sumarse, a ser parte del cambio que nuestro estado necesita. Juntos podemos construir el futuro que merecemos. ¡Únete a nuestro movimiento!',
                'order' => 1,
            ],
        ];

        foreach ($info as $item) {
            CandidateInformation::create($item);
        }
    }
}
