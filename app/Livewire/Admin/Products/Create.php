<?php

namespace App\Livewire\Admin\Products;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Unit;
use Livewire\Component;

class Create extends Component
{
    public Product $product;

    protected function rules(): array
    {
        return [
            'product.name'                => 'required',
            'product.price'               => 'required|numeric|min:0',
            'product.description'         => 'required',
            'product.product_category_id' => 'required|exists:product_categories,id',
            'product.unit_id'             => 'required|exists:units,id',
        ];
    }

    function mount()
    {
        $this->product = new Product();
    }
    function updated()
    {
        $this->validate();
    }

    function save()
    {
        try {
            $this->validate();

            $this->product->save();

            return redirect()->route('admin.products.index');
        } catch (\Throwable $th) {
            $this->dispatch('done', error: "Something Went Wrong: " . $th->getMessage());
        }
    }
    public function render()
    {
        // Get the ID of the 'Obsolete' category
        $obsoleteCategory = ProductCategory::where('name', 'كهنة')->first();
        $obsoleteCategoryId = $obsoleteCategory ? $obsoleteCategory->id : null;

        return view('livewire.admin.products.create', [
            // Filter out the 'Obsolete' category if it exists
            'productCategories' => ProductCategory::where('id', '!=', $obsoleteCategoryId)->get(),
            'units' => Unit::all(),
        ]);
    }
}
