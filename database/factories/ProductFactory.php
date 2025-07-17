<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'type' => $this->faker->randomElement(['medicine', 'equipment', 'service', 'other']),
            'name' => $this->faker->unique()->word(),
            'image' => null,
            'expiry_date' => $this->faker->optional()->date(),
            'description' => $this->faker->optional()->paragraph(),
            'usage' => $this->faker->optional()->sentence(),
            'alert_threshold' => 10,
        ];
    }
}
