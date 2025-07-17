<?php

namespace Database\Factories;

use App\Models\Center;
use App\Models\Schedule;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Schedule>
 */
class ScheduleFactory extends Factory
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
            'day' => $this->faker->randomElement(['saturday', 'sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday']),
            'start_time' => $this->faker->time('H:i'),
            'end_time' => $this->faker->time('H:i'),
            'is_active' => $this->faker->boolean(),
        ];
    }
}
