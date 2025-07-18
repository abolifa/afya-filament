<?php

namespace Database\Factories;

use App\Models\Center;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Center>
 */
class CenterFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $this->faker->unique(true); // clear previous uniques

        return [
            'name' => 'مركز ' . $this->faker->unique()->city(),
            'phone' => $this->faker->unique()->numerify('091#######'),
            'alt_phone' => $this->faker->optional()->numerify('092#######'),
            'address' => $this->faker->optional()->address(),
            'street' => $this->faker->optional()->streetName(),
            'city' => $this->faker->optional()->city(),
            'latitude' => $this->faker->optional()->latitude(),
            'longitude' => $this->faker->optional()->longitude(),
        ];
    }
}
