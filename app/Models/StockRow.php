<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class StockRow extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'id', 'name', 'type', 'stock',
    ];

    // no table
    public function getTable()
    {
        return 'stock_rows';
    }

    public function newQuery(): Builder
    {
        return new class(StockRow::allStocks()) extends Builder {
            public function __construct(Collection $items)
            {
                parent::__construct((new StockRow())->newModelQuery()->getQuery());
                $this->models = $items;
            }

            public function get($columns = ['*'])
            {
                return collect($this->models);
            }
        };
    }

    // Override query to use collection

    public static function allStocks(): Collection
    {
        return Product::all()->map(function ($product) {
            $in = $product->orderItems()
                ->whereHas('order', fn($q) => $q->where('status', 'confirmed')->where('type', 'in'))
                ->sum('quantity');

            $out = $product->orderItems()
                ->whereHas('order', fn($q) => $q->where('status', 'confirmed')->where('type', 'out'))
                ->sum('quantity');

            $transferIn = $product->orderItems()
                ->whereHas('order', fn($q) => $q->where('status', 'confirmed')->where('type', 'transfer')
                    ->where('to_center_id', $product->center_id ?? 0)) // customize per center
                ->sum('quantity');

            $transferOut = $product->orderItems()
                ->whereHas('order', fn($q) => $q->where('status', 'confirmed')->where('type', 'transfer')
                    ->where('center_id', $product->center_id ?? 0)) // customize per center
                ->sum('quantity');

            return new static([
                'id' => $product->id,
                'name' => $product->name,
                'type' => $product->type,
                'stock' => $in + $transferIn - $out - $transferOut,
            ]);
        });
    }
}
