<?php

namespace Database\Factories;

use App\Models\Employee;
use App\Models\ImportHistory;
use Illuminate\Database\Eloquent\Factories\Factory;

class ImportHistoryFactory extends Factory
{
    protected $model = ImportHistory::class;

    public function definition(): array
    {
        return [
            'import_date' => fake()->dateTimeBetween('-1 year', 'now'),
            'importer' => Employee::factory(),
            'note' => fake()->optional()->sentence(),
        ];
    }
} 