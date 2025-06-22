<?php

namespace App\Livewire\Admin\WarehouseProducts;

use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Add extends Component
{
    public $product_id;
    public $quantity;
    public $min_stock_level = 0;
    public $new_price;
    public $use_new_price = false;

    protected function rules(): array
    {
        return [
            'product_id'     => 'required|exists:products,id',
            'quantity'       => 'required|integer|min:1',
            'min_stock_level'=> 'required|integer|min:0',
            'new_price'      => 'required_if:use_new_price,true|numeric|min:0',
        ];
    }

    public function updated($field)
    {
        $this->validateOnly($field);

        // When you pick a product, preload its price & existing min_stock_level
        if ($field === 'product_id') {
            $p = Product::find($this->product_id);
            $this->new_price       = $p ? $p->price : null;
            $record = DB::table('warehouse_products')
                        ->where([
                            'warehouse_id' => auth()->user()->warehouse_id,
                            'product_id'   => $this->product_id,
                        ])->first();
            $this->min_stock_level = $record->min_stock_level ?? 0;
        }
    }

    public function save()
    {
        $this->validate();

        $wid = auth()->user()->warehouse_id;
        $where = ['warehouse_id' => $wid, 'product_id' => $this->product_id];

        // 1) Update product price if needed
        if ($this->use_new_price) {
            Product::where('id', $this->product_id)
                ->update(['price' => $this->new_price]);
        }

        // 2) Increment or insert with min_stock_level
        $updated = DB::table('warehouse_products')
            ->where($where)
            ->increment('quantity_good', $this->quantity);

        if ($updated) {
            // also update the minimum if it changed
            DB::table('warehouse_products')
                ->where($where)
                ->update(['min_stock_level' => $this->min_stock_level, 'updated_at' => now()]);
        } else {
            DB::table('warehouse_products')->insert([
                'warehouse_id'     => $wid,
                'product_id'       => $this->product_id,
                'quantity_good' => $this->quantity,
                'min_stock_level'  => $this->min_stock_level,
                'created_at'       => now(),
                'updated_at'       => now(),
            ]);
        }

        // 3) Flash success + possible warning
        session()->flash('success', 'تمت الإضافة بنجاح.');
        $current = DB::table('warehouse_products')
                     ->where($where)
                     ->value('quantity_good');

        if ($current < $this->min_stock_level) {
            session()->flash('warning', "الكمية الحالية ($current) أقل من الحد الأدنى ({$this->min_stock_level}).");
        }

        $this->reset(['product_id','quantity','min_stock_level','use_new_price','new_price']);
    }

    public function render()
    {
        return view('livewire.admin.warehouse-products.add', [
            'products' => Product::all(),
        ]);
    }
}
