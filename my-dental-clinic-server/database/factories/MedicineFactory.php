<?php

namespace Database\Factories;

use App\Models\Medicine;
use Illuminate\Database\Eloquent\Factories\Factory;

class MedicineFactory extends Factory
{
    protected $model = Medicine::class;

    public function definition(): array
    {
        return [
            'medicine_name' => fake()->unique()->words(2, true),
            'unit' => fake()->randomElement(['tablet', 'capsule', 'ml', 'mg', 'g']),
            'instructs' => fake()->paragraph(),
        ];
    }
} 