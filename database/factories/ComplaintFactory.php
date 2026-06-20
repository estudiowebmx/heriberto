<?php

namespace Database\Factories;

use App\Models\Complainant;
use App\Models\Complaint;
use Illuminate\Database\Eloquent\Factories\Factory;

class ComplaintFactory extends Factory
{
    protected $model = Complaint::class;

    public function definition(): array
    {
        return [
            'complainant_id' => Complainant::factory(),
            'colonia' => fake()->word() . ' ' . fake()->randomElement(['Norte', 'Sur', 'Centro', 'Oriente', 'Poniente']),
            'cac_president_name' => fake()->firstName() . ' ' . fake()->lastName() . ' ' . fake()->lastName(),
        ];
    }
}
