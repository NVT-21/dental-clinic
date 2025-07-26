<?php

namespace Database\Factories;

use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServiceFactory extends Factory
{
    protected $model = Service::class;

    public function definition(): array
    {
        return [
            'serviceName' => fake()->unique()->words(3, true),
            'description' => fake()->paragraph(),
            'base_price' => fake()->numberBetween(100000, 5000000),
          
        ];
    }
} 