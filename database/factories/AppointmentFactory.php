<?php

namespace Database\Factories;

use App\Models\Appointment;
use App\Models\Center;
use App\Models\Doctor;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Appointment>
 */
class AppointmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'center_id' => Center::factory(),
            'patient_id' => Patient::factory(),
            'doctor_id' => $this->faker->boolean(80) ? Doctor::factory() : null,
            'date' => $this->faker->date(),
            'time' => $this->faker->time(),
            'status' => $this->faker->randomElement(['pending', 'confirmed', 'cancelled', 'completed']),
            'intended' => $this->faker->boolean(),
            'notes' => $this->faker->optional()->text(),
        ];
    }
}
