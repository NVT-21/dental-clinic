<?php

namespace Database\Factories;

use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

class PatientFactory extends Factory
{
    protected $model = Patient::class;

    public function definition(): array
    {
        return [
            'fullname' => fake()->name(),
            'phoneNumber' => fake()->phoneNumber(),
            'email' => fake()->unique()->safeEmail(),
            'birthdate' => fake()->date(),
            'message' => fake()->optional()->sentence(),
        ];
    }
} 