<?php

namespace App\Models;

use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
}
