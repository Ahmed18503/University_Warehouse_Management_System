<?php

namespace App\Livewire\Admin\ProductDeletionRequests;

use App\Models\Product;
use App\Models\ProductDeletionRequest;
use App\Models\Warehouse;
use App\Models\WarehouseProduct;
use Livewire\Component;

class Create extends Component
{
    public ProductDeletionRequest $productDeletionRequest;
    public $warehouse_id;
    public $product_id;
    public $quantity_to_delete;
    public $reason;

    protected function rules(): array
    {
        return [
            'warehouse_id' => [
                'required',
                'exists:warehouse,id',
                function ($attribute, $value, $fail) {
                    if ($value != auth()->user()->warehouse_id) {
                        $fail('You can only submit deletion requests for your assigned warehouse.');
                    }
                },
            ],
            'product_id' => [
                'required',
                'exists:products,id',
                function ($attribute, $value, $fail) {
                    if (!empty($this->warehouse_id)) {
                        $warehouseProduct = WarehouseProduct::where('warehouse_id', $this->warehouse_id)
                            ->where('product_id', $value)
                            ->first();

                        if (!$warehouseProduct) {
                            $fail('This product does not exist in the selected warehouse.');
                        } elseif ($warehouseProduct->quantity_good < $this->quantity_to_delete) {
                            $fail('The quantity to delete exceeds the available stock in the warehouse. Available: ' . $warehouseProduct->quantity_good);
                        }
                    }
                },
            ],
            'quantity_to_delete' => 'required|integer|min:1',
            'reason' => 'required|string|min:10',
        ];
    }

    function mount()
    {
        $this->productDeletionRequest = new ProductDeletionRequest();
        $this->warehouse_id = auth()->user()->warehouse_id; // Pre-fill with user's warehouse
    }

    function save()
    {
        $this->validate();

        try {
            $this->productDeletionRequest->fill([
                'product_id' => $this->product_id,
                'warehouse_id' => $this->warehouse_id,
                'quantity_to_delete' => $this->quantity_to_delete,
                'reason' => $this->reason,
                'requested_by_user_id' => auth()->id(),
                'status' => 'pending',
            ]);
            $this->productDeletionRequest->save();

            $this->dispatch('done', success: 'Product Deletion Request submitted successfully!');
            return redirect()->route('admin.product-deletion-requests.index');
        } catch (\Throwable $th) {
            $this->dispatch('done', error: "Something Went Wrong: " . $th->getMessage());
        }
    }

    public function render()
    {
        $products = collect();
        if (!empty($this->warehouse_id)) {
            $products = Product::whereHas('warehouseProducts', function ($query) {
                $query->where('warehouse_id', $this->warehouse_id);
            })->get();
        }

        return view('livewire.admin.product-deletion-requests.create', [
            'warehouses' => Warehouse::all(),
            'products' => $products,
        ]);
    }
} 