<?php

namespace Database\Factories;

use App\Models\Center;
use App\Models\Doctor;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<Doctor>
 */
class DoctorFactory extends Factory
{
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'center_id' => Center::factory(),
            'name' => $this->faker->name(),
            'specialization' => $this->faker->optional()->word(),
            'email' => Str::lower(Str::uuid()) . '@example.com',
            'phone' => $this->faker->optional()->numerify('09#########'),
            'password' => Hash::make('091091'),
            'remember_token' => Str::random(10),
        ];
    }
}
