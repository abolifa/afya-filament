<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\TransferInvoice;
use App\Models\TransferInvoiceItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TransferInvoiceItem>
 */
class TransferInvoiceItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'transfer_invoice_id' => TransferInvoice::factory(),
            'product_id' => Product::factory(),
            'quantity' => $this->faker->numberBetween(1, 15),
        ];
    }
}
