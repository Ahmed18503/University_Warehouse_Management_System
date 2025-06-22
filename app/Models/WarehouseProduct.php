<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class WarehouseProduct extends Pivot
{
    protected $table = 'warehouse_products';

    protected $fillable = [
        'product_id',
        'warehouse_id',
        'quantity_good',
        'quantity_obsolete',
        'min_stock_level',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

}
