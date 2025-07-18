<?php

namespace Database\Factories;

use App\Models\Center;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<Patient>
 */
class PatientFactory extends Factory
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
            'file_number' => $this->faker->unique()->numerify('########'),
            'national_id' => $this->faker->unique()->numerify('1###########'),
            'family_issue_number' => $this->faker->optional()->numerify('##########'),
            'name' => $this->faker->name(),
            'phone' => $this->faker->unique()->numerify('09#########'),
            'password' => static::$password ??= Hash::make('091091'),
            'email' => $this->faker->boolean(70) ? Str::lower(Str::uuid()) . '@example.com' : null,
            'gender' => $this->faker->randomElement(['male', 'female']),
            'dob' => $this->faker->optional()->date(),
            'blood_group' => $this->faker->optional()->randomElement(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-']),
            'image' => null,
            'verified' => $this->faker->boolean(),
            'center_id' => Center::factory(),
            'remember_token' => Str::random(10),
        ];
    }
}
