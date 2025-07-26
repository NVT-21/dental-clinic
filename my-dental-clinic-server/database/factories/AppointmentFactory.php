<?php

namespace Database\Factories;

use App\Models\Appointment;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

class AppointmentFactory extends Factory
{
    protected $model = Appointment::class;

    public function definition(): array
    {
        return [
            'idPatient' => Patient::factory(),
            'bookingDate' => fake()->dateTimeBetween('now', '+1 month'),
            'appointmentTime' => fake()->time('H:i'),
            'status' => fake()->randomElement(['Pending', 'Confirmed', 'Completed', 'Cancelled']),
            'is_done' => false,
            'symptoms' => fake()->optional()->sentence(),
            'notes' => fake()->optional()->paragraph(),
            'appointment_type' => fake()->randomElement(['consultation', 'treatment']),
            'estimated_duration' => fake()->numberBetween(15, 120),
            'locked_by' => null,
            'locked_at' => null,
        ];
    }
} 