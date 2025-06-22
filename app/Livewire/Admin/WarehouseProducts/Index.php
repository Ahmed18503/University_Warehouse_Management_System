<?php

namespace App\Livewire\Admin\WarehouseProducts;

use App\Models\WarehouseProduct;
use Livewire\Component;
use App\Models\Warehouse;
use App\Models\Product;
use App\Models\User;

class Index extends Component
{
 

    public function render()
    {
        $warehouseId = auth()->user()->warehouse_id;

        $warehouseProducts = WarehouseProduct::with(['product','product.category', 'product.unit'])
        ->where('warehouse_id', $warehouseId)
        ->get();

    return view('livewire.admin.warehouse-products.index', [
        'warehouseProducts' => $warehouseProducts,
    ]);
    }
}
