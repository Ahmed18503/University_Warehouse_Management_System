<?php

namespace App\Livewire\Admin\WarehouseAudits;

use App\Models\Warehouse;
use App\Models\WarehouseAudit;
use App\Models\WarehouseProduct;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class Create extends Component
{
    public $warehouse_id;
    public $warehouse_name;
    public $notes;
    
    public $availableProducts = [];
    public $selectedProducts = [];

    protected function rules(): array
    {
        return [
            'notes' => 'nullable|string|max:1000',
            'selectedProducts' => 'required|array|min:1',
        ];
    }

    protected $messages = [
        'selectedProducts.required' => 'Please select at least one product to audit.',
    ];

    public function mount()
    {
        $user = auth()->user();

        if (is_null($user->warehouse_id)) {
            session()->flash('error', 'You must be assigned to a warehouse to create an audit.');
            return $this->redirect(route('admin.dashboard'), navigate: true);
        }
        
        $warehouse = Warehouse::find($user->warehouse_id);

        if (is_null($warehouse)) {
             session()->flash('error', 'The warehouse you are assigned to could not be found.');
             return $this->redirect(route('admin.dashboard'), navigate: true);
        }

        $this->warehouse_id = $warehouse->id;
        $this->warehouse_name = $warehouse->name;
        $this->availableProducts = DB::table('warehouse_products')
            ->join('products', 'warehouse_products.product_id', '=', 'products.id')
            ->where('warehouse_products.warehouse_id', $this->warehouse_id)
            ->select(
                'products.id as product_id',
                'products.name as product_name',
                'products.code as product_code',
                'warehouse_products.quantity_good',
                'warehouse_products.quantity_obsolete'
            )
            ->orderBy('products.name')
            ->get()
            ->all();
    }

    public function toggleSelectAll($checked)
    {
        if ($checked) {
            $this->selectedProducts = collect($this->availableProducts)->pluck('product_id')->toArray();
        } else {
            $this->selectedProducts = [];
        }
    }

    /**
     * Create a new WarehouseAudit and populate it with a snapshot
     * of the current warehouse inventory.
     */
    public function createAudit()
    {
        $this->validate();

        DB::beginTransaction();
        try {
            $warehouseAudit = WarehouseAudit::create([
                'warehouse_id' => $this->warehouse_id,
                'auditor_id' => auth()->id(),
                'audit_date' => now(),
                'status' => WarehouseAudit::STATUS_IN_PROGRESS,
                'notes' => $this->notes,
            ]);

            $productsToAudit = WarehouseProduct::where('warehouse_id', $this->warehouse_id)
                                                ->whereIn('product_id', $this->selectedProducts)
                                                ->with('product') // Eager load product for cost
                                                ->get();

            if (count($productsToAudit) === 0) {
                DB::rollBack();
                session()->flash('error', 'No products selected for audit.');
                return;
            }

            foreach ($productsToAudit as $wp) {
                $warehouseAudit->items()->create([
                    'product_id' => $wp->product_id,
                    'system_qty_good' => $wp->quantity_good,
                    'system_qty_obsolete' => $wp->quantity_obsolete,
                    'counted_qty_good' => 0, 
                    'counted_qty_obsolete' => 0,
                    'unit_cost' => $wp->product->cost ?? 0,
                ]);
            }

            DB::commit();

            session()->flash('success', 'Warehouse audit created successfully. You can now begin counting.');
            return $this->redirect(route('admin.warehouse-audits.show', $warehouseAudit->id), navigate: true);

        } catch (\Throwable $th) {
            DB::rollBack();
            session()->flash('error', "An error occurred: " . $th->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.admin.warehouse-audits.create');
    }
}


