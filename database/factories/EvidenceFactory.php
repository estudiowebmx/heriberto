<?php

namespace Database\Factories;

use App\Models\Complaint;
use App\Models\Evidence;
use Illuminate\Database\Eloquent\Factories\Factory;

class EvidenceFactory extends Factory
{
    protected $model = Evidence::class;

    public function definition(): array
    {
        return [
            'complaint_id' => Complaint::factory(),
            'address' => fake()->streetAddress(),
            'leyenda' => fake()->sentence(3),
            'is_inhabited' => fake()->boolean(),
            'is_cac_president' => false,
            'photo_path' => 'evidences/placeholder.jpg',
        ];
    }
}
