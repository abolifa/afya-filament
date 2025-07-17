<?php

namespace App\Models;

use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

/**
 * @method static find(int|null $newProductId)
 * @method static pluck(string $string, string $string1)
 */
class Product extends Model
{
    /** @use HasFactory<ProductFactory> */
    use HasFactory;

    protected $guarded = ['id'];


    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the current stock for this product in the given center.
     *
     * @param int $centerId
     * @return int
     */
    public function stockInCenter(int $centerId): int
    {
        // Sum of confirmed invoices into this center
        $invoicesIn = DB::table('invoice_items')
            ->join('invoices', 'invoices.id', '=', 'invoice_items.invoice_id')
            ->where('invoice_items.product_id', $this->id)
            ->where('invoices.center_id', $centerId)
            ->where('invoices.status', 'confirmed')
            ->sum('invoice_items.quantity');

        // Sum of confirmed transfer‑in into this center
        $transfersIn = DB::table('transfer_invoice_items')
            ->join('transfer_invoices', 'transfer_invoices.id', '=', 'transfer_invoice_items.transfer_invoice_id')
            ->where('transfer_invoice_items.product_id', $this->id)
            ->where('transfer_invoices.to_center_id', $centerId)
            ->where('transfer_invoices.status', 'confirmed')
            ->sum('transfer_invoice_items.quantity');

        // Sum of confirmed orders (out) from this center
        $ordersOut = DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('order_items.product_id', $this->id)
            ->where('orders.center_id', $centerId)
            ->where('orders.status', 'confirmed')
            ->sum('order_items.quantity');

        // Sum of confirmed transfer‑out from this center
        $transfersOut = DB::table('transfer_invoice_items')
            ->join('transfer_invoices', 'transfer_invoices.id', '=', 'transfer_invoice_items.transfer_invoice_id')
            ->where('transfer_invoice_items.product_id', $this->id)
            ->where('transfer_invoices.from_center_id', $centerId)
            ->where('transfer_invoices.status', 'confirmed')
            ->sum('transfer_invoice_items.quantity');

        return $invoicesIn
            + $transfersIn
            - $ordersOut
            - $transfersOut;
    }
}
