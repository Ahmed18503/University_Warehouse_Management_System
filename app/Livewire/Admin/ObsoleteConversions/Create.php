<?php

namespace App\Livewire\Admin\ObsoleteConversions;

use App\Models\ObsoleteConversionRequest;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\WarehouseProduct;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class Create extends Component
{
    public $product_id;
    public $quantity;
    public $reason;
    public $obsoleteCategoryId;

    protected function rules(): array
    {
        return [
            'product_id' => ['required', 'exists:products,id',
                function ($attribute, $value, $fail) {
                    // Ensure the product is not already in the obsolete category
                    $product = Product::find($value);
                    if ($product && $product->product_category_id === $this->obsoleteCategoryId) {
                        $fail('The selected product is already in the obsolete category.');
                    }
                },
            ],
            'quantity' => ['required', 'integer', 'min:1',
                function ($attribute, $value, $fail) {
                    // Check if quantity is less than or equal to current stock in the user's warehouse
                    $userWarehouseId = auth()->user()->warehouse_id;
                    $warehouseProduct = WarehouseProduct::where('product_id', $this->product_id)
                                                        ->where('warehouse_id', $userWarehouseId)
                                                        ->first();

                    if (!$warehouseProduct || $warehouseProduct->quantity_good < $value) {
                        $fail('Insufficient stock in your warehouse for this product.');
                    }
                },
            ],
            'reason'   => 'required|string|min:10',
        ];
    }

    public function mount()
    {
        $obsoleteCategory = ProductCategory::where('name', 'كهنة')->first();
        $this->obsoleteCategoryId = $obsoleteCategory ? $obsoleteCategory->id : null;
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function save()
    {
        $this->validate();

        DB::beginTransaction();
        try {
            ObsoleteConversionRequest::create([
                'product_id'         => $this->product_id,
                'warehouse_id'       => auth()->user()->warehouse_id,
                'quantity'           => $this->quantity,
                'reason'             => $this->reason,
                'requested_by_user_id' => auth()->id(),
                'status'             => 'pending',
            ]);

            DB::commit();
            session()->flash('success', 'Obsolete conversion request submitted successfully!');
            return $this->redirect(route('admin.obsolete-conversions.create'), navigate: true);

        } catch (\Throwable $th) {
            DB::rollBack();
            session()->flash('error', "Something went wrong: " . $th->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.admin.obsolete-conversions.create', [
            'products' => Product::where('product_category_id', '!=', $this->obsoleteCategoryId)->get(),
        ]);
    }
}
