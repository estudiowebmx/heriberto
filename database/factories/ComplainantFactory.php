<?php

namespace Database\Factories;

use App\Models\Complainant;
use Illuminate\Database\Eloquent\Factories\Factory;

class ComplainantFactory extends Factory
{
    protected $model = Complainant::class;

    public function definition(): array
    {
        return [
            'nombre_completo' => fake()->name(),
            'clave_elector' => fake()->unique()->regexify('[A-Z]{6}[0-9]{8}[A-Z]{2}[0-9]{2}'),
            'domicilio' => fake()->streetAddress() . ', ' . fake()->city(),
            'email' => fake()->optional()->safeEmail(),
            'sexo' => fake()->randomElement(['M', 'F']),
        ];
    }
}
