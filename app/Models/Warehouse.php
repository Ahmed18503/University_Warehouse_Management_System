<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Warehouse extends Model
{
    protected $table = 'warehouse';

    protected $fillable = [
        'name',
    ];

    /**
     * The products stored in this warehouse, along with quantity and min stock level.
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'warehouse_products')
                    ->withPivot('quantity_good', 'min_stock_level')
                    ->withTimestamps();
    }

    /**
     * Attach or update stock for a product.
     *
     * @param  \App\Models\Product|int  $product
     * @param  int  $quantity
     * @param  int|null  $minLevel
     * @return void
     */
    public function setProductStock($product, int $quantity, int $minLevel = null): void
    {
        $this->products()->syncWithoutDetaching([
            $product instanceof Product ? $product->id : $product => [
                'quantity_good'  => $quantity,
                'min_stock_level'   => $minLevel ?? 0,
            ]
        ]);
    }

    /**
     * Get all products that are currently below their minimum stock level.
     *
     * @return \Illuminate\Support\Collection
     */
    public function lowStockProducts()
    {
        return $this->products()
                    ->whereColumn('quantity_good', '<', 'min_stock_level')
                    ->get();
    }
}
