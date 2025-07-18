<?php

namespace Database\Factories;

use App\Models\Center;
use App\Models\Invoice;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Invoice>
 */
class InvoiceFactory extends Factory
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
            'supplier_id' => $this->faker->boolean()
                ? Supplier::factory()
                : null,
            'status' => $this->faker->randomElement([
                'pending', 'confirmed', 'cancelled'
            ]),
        ];
    }
}
