<?php

namespace Database\Factories;

use App\Models\Room;
use Illuminate\Database\Eloquent\Factories\Factory;

class RoomFactory extends Factory
{
    protected $model = Room::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(2, true),
            'quantity' => fake()->numberBetween(1, 5),
        ];
    }
} 