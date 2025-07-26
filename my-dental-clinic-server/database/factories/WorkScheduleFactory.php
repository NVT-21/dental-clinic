<?php

namespace Database\Factories;

use App\Models\Employee;
use App\Models\WorkSchedule;
use Illuminate\Database\Eloquent\Factories\Factory;

class WorkScheduleFactory extends Factory
{
    protected $model = WorkSchedule::class;

    public function definition(): array
    {
        return [
            'registerDate' => fake()->dateTimeBetween('-1 month', '+1 month'),
            'idEmployee' => Employee::factory(),
        ];
    }
} 