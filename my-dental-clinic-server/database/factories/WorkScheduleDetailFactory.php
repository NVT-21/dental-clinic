<?php

namespace Database\Factories;

use App\Models\WorkScheduleDetail;
use Illuminate\Database\Eloquent\Factories\Factory;

class WorkScheduleDetailFactory extends Factory
{
    protected $model = WorkScheduleDetail::class;

    public function definition(): array
    {
        return [
            // Giá trị này nên được override trong seeder
            'workScheduleId' => null,
            'shiftId' => null,
            'status' => fake()->randomElement(['off', 'working']),
        ];
    }
}
