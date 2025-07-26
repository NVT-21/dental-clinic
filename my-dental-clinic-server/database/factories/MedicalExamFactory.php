<?php

namespace Database\Factories;

use App\Models\Employee;
use App\Models\MedicalExam;
use App\Models\Appointment;
use Illuminate\Database\Eloquent\Factories\Factory;

class MedicalExamFactory extends Factory
{
    protected $model = MedicalExam::class;

    public function definition(): array
    {
        return [
            'idEmployee' => Employee::factory(),
            'idAppointment' => Appointment::factory(),
            'symptoms' => fake()->optional()->sentence(),
            'status' => fake()->randomElement(['Pending', 'In Progress', 'Completed', 'Cancelled']),
            'statusPayment' => fake()->randomElement(['Unpaid', 'Paid']),
            'ExamDate' => fake()->dateTimeBetween('-1 month', 'now'),
            'diagnosis' => fake()->optional()->paragraph(),
            'advice' => fake()->optional()->paragraph(),
            'createdById' => Employee::factory(),
        ];
    }
} 