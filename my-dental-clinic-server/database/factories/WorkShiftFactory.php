<?php

namespace Database\Factories;

use App\Models\WorkShift;
use Illuminate\Database\Eloquent\Factories\Factory;

class WorkShiftFactory extends Factory
{
    protected $model = WorkShift::class;

    public function definition(): array
    {
        return [
            'shiftName' => fake()->unique()->randomElement(['Morning', 'Afternoon', 'Evening', 'Night']),
            'startTime' => fake()->time('H:i'),
            'endTime' => fake()->time('H:i'),
        ];
    }
} 