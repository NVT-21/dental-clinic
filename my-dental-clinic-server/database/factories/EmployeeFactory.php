<?php

namespace Database\Factories;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmployeeFactory extends Factory
{
    protected $model = Employee::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'fullName' => fake()->name(),
            'birthday' => fake()->date(),
            'idRoom' => null, // Will be set when creating with room
            'role' => fake()->randomElement(['Doctor', 'Administrator', 'Receptionist']),
            'gender' => fake()->randomElement(['Male', 'Female']),
            'phoneNumber' => fake()->phoneNumber(),
            'status' => fake()->randomElement(['Active', 'Inactive']),
        ];
    }
} 