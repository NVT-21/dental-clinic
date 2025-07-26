<?php

namespace Database\Factories;

use App\Models\Medicine;
use App\Models\MedicineBatch;
use App\Models\ImportHistory;
use Illuminate\Database\Eloquent\Factories\Factory;

class MedicineBatchFactory extends Factory
{
    protected $model = MedicineBatch::class;

    public function definition(): array
    {
        $initial_quantity = fake()->numberBetween(100, 1000);
        return [
            'import_history_id' => ImportHistory::factory(),
            'medicine_id' => Medicine::factory(),
            'expiration_date' => fake()->dateTimeBetween('+1 year', '+3 years'),
            'cost_price' => fake()->numberBetween(10000, 100000),
            'selling_price' => fake()->numberBetween(15000, 150000),
            'initial_quantity' => $initial_quantity,
            'remaining_quantity' => fake()->numberBetween(0, $initial_quantity),
        ];
    }
} 