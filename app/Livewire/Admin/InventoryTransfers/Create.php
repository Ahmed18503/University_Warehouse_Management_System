<?php

namespace App\Livewire\Admin\InventoryTransfers;

use App\Models\InventoryTransfer;
use App\Models\InventoryTransferItem;
use App\Models\Product;
use App\Models\User;
use App\Models\Warehouse;
use App\Models\WarehouseProduct;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Create extends Component
{
    public InventoryTransfer $inventoryTransfer;
    public $items = [];
    public $selectedProduct = null;
    public $productQuantity = 1;
    public $transferType = InventoryTransferItem::TYPE_GOOD;
    public $availableGoodQuantity = 0;
    public $availableObsoleteQuantity = 0;
    public $notes = '';

    protected function rules(): array
    {
        return [
            'inventoryTransfer.source_warehouse_id' => [
                'required',
                'exists:warehouse,id',
                'different:inventoryTransfer.destination_warehouse_id'
            ],
            'inventoryTransfer.destination_warehouse_id' => 'required|exists:warehouse,id',
            'notes' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.requested_quantity' => 'required|integer|min:1',
            'items.*.item_type' => ['required', 'in:' . InventoryTransferItem::TYPE_GOOD . ',' . InventoryTransferItem::TYPE_OBSOLETE],
        ];
    }

    public function mount()
    {
        $user = auth()->user();
        $this->inventoryTransfer = new InventoryTransfer();
        $this->inventoryTransfer->created_by_user_id = $user->id;
        $this->inventoryTransfer->destination_warehouse_id = $user->warehouse_id;
        $this->inventoryTransfer->status = InventoryTransfer::STATUS_DRAFT;
    }

    public function addItem()
    {
        $this->validate([
            'selectedProduct' => 'required|exists:products,id',
            'productQuantity' => [
                'required',
                'integer',
                'min:1',
                function($attribute, $value, $fail) {
                    if ($this->transferType === InventoryTransferItem::TYPE_GOOD && $value > $this->availableGoodQuantity) {
                        $fail("Requested quantity exceeds available good quantity ({$this->availableGoodQuantity})");
                    } elseif ($this->transferType === InventoryTransferItem::TYPE_OBSOLETE && $value > $this->availableObsoleteQuantity) {
                        $fail("Requested quantity exceeds available obsolete quantity ({$this->availableObsoleteQuantity})");
                    }
                }
            ],
            'transferType' => ['required', 'in:' . InventoryTransferItem::TYPE_GOOD . ',' . InventoryTransferItem::TYPE_OBSOLETE],
        ]);

        $product = Product::with('unit')->find($this->selectedProduct);

        // Check if product already exists with same transfer type
        foreach ($this->items as $key => $item) {
            if ($item['product_id'] == $this->selectedProduct && $item['item_type'] == $this->transferType) {
                $newTotal = $item['requested_quantity'] + $this->productQuantity;
                $availableQty = $this->transferType === InventoryTransferItem::TYPE_GOOD 
                    ? $this->availableGoodQuantity + $item['requested_quantity'] 
                    : $this->availableObsoleteQuantity + $item['requested_quantity'];
                
                if ($newTotal > $availableQty) {
                    session()->flash('error', "Total requested quantity would exceed available {$this->transferType} quantity");
                    return;
                }
                
                $this->items[$key]['requested_quantity'] = $newTotal;
                $this->reset(['selectedProduct', 'productQuantity']);
                $this->updateAvailableQuantities();
                return;
            }
        }

        $this->items[] = [
            'product_id' => $product->id,
            'product_name' => $product->name,
            'product_code' => $product->code,
            'unit_name' => $product->unit->name,
            'requested_quantity' => $this->productQuantity,
            'item_type' => $this->transferType,
            'status' => InventoryTransferItem::STATUS_PENDING
        ];

        $this->reset(['selectedProduct', 'productQuantity']);
        $this->updateAvailableQuantities();
    }

    public function removeItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
        $this->updateAvailableQuantities();
    }

    public function updatedSelectedProduct()
    {
        $this->updateAvailableQuantities();
    }

    public function updatedTransferType()
    {
        $this->updateAvailableQuantities();
    }

    public function updatedInventoryTransferSourceWarehouseId()
    {
        $this->updateAvailableQuantities();
    }

    private function updateAvailableQuantities()
    {
        $this->availableGoodQuantity = 0;
        $this->availableObsoleteQuantity = 0;

        if ($this->inventoryTransfer->source_warehouse_id && $this->selectedProduct) {
            $warehouseProduct = WarehouseProduct::where('warehouse_id', $this->inventoryTransfer->source_warehouse_id)
                                              ->where('product_id', $this->selectedProduct)
                                              ->first();
            
            if ($warehouseProduct) {
                $this->availableGoodQuantity = $warehouseProduct->quantity_good;
                $this->availableObsoleteQuantity = $warehouseProduct->quantity_obsolete;

                // Deduct quantities already in items
                foreach ($this->items as $item) {
                    if ($item['product_id'] == $this->selectedProduct) {
                        if ($item['item_type'] == InventoryTransferItem::TYPE_GOOD) {
                            $this->availableGoodQuantity = max(0, $this->availableGoodQuantity - $item['requested_quantity']);
                        } elseif ($item['item_type'] == InventoryTransferItem::TYPE_OBSOLETE) {
                            $this->availableObsoleteQuantity = max(0, $this->availableObsoleteQuantity - $item['requested_quantity']);
                        }
                    }
                }
            }
        }
    }

    public function save()
    {
        $this->validate([
            'inventoryTransfer.source_warehouse_id' => 'required|exists:warehouse,id',
            'items' => 'required|array|min:1',
        ]);

        try {
            DB::beginTransaction();

            // Create the transfer
            $transfer = new InventoryTransfer();
            $transfer->source_warehouse_id = $this->inventoryTransfer->source_warehouse_id;
            $transfer->destination_warehouse_id = auth()->user()->warehouse_id;
            $transfer->created_by_user_id = auth()->id();
            $transfer->status = InventoryTransfer::STATUS_PENDING_SOURCE_APPROVAL;
            $transfer->notes = $this->notes;
            $transfer->submitted_at = now();
            $transfer->save();

            // Create transfer items
            foreach ($this->items as $item) {
                $transferItem = new InventoryTransferItem([
                    'product_id' => $item['product_id'],
                    'product_name' => $item['product_name'],
                    'product_code' => $item['product_code'],
                    'unit_name' => $item['unit_name'],
                    'requested_quantity' => $item['requested_quantity'],
                    'item_type' => $item['item_type'],
                    'status' => InventoryTransferItem::STATUS_PENDING
                ]);
                $transfer->items()->save($transferItem);
            }

            DB::commit();

            session()->flash('success', 'تم إنشاء طلب النقل بنجاح');
            return redirect()->route('admin.inventory-transfers.index');

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'فشل في إنشاء طلب النقل: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $sourceWarehouses = Warehouse::where('id', '!=', auth()->user()->warehouse_id)->get();
        
        return view('livewire.admin.inventory-transfers.create', [
            'warehouses' => $sourceWarehouses,
            'products' => Product::with('unit')->get(),
        ]);
    }
}
 