<?php

namespace App\Livewire\Admin\AllWarehouseProducts;

use App\Models\WarehouseProduct;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';

    protected $queryString = ['search'];

    public function render()
    {
        $warehouseProducts = WarehouseProduct::with(['product', 'product.category', 'product.unit', 'warehouse'])
            ->where(function ($query) {
                $query->whereHas('product', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('sku', 'like', '%' . $this->search . '%');
                })
                ->orWhereHas('warehouse', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%');
                });
            })
            ->latest()
            ->paginate(10);

        return view('livewire.admin.all-warehouse-products.index', [
            'warehouseProducts' => $warehouseProducts,
        ]);
    }
}