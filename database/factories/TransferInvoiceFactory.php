<?php

namespace Database\Factories;

use App\Models\Center;
use App\Models\TransferInvoice;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TransferInvoice>
 */
class TransferInvoiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $from = Center::factory();
        $to = Center::factory();

        return [
            'from_center_id' => $from,
            'to_center_id' => $to,
            'status' => $this->faker->randomElement([
                'pending', 'confirmed', 'cancelled'
            ]),
        ];
    }
}
