<?php

namespace App\Livewire\Admin\InventoryTransfers;

use App\Models\InventoryTransfer;
use App\Models\InventoryTransferItem;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\WarehouseProduct;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Edit extends Component
{
    public InventoryTransfer $inventoryTransfer;
    public $items = [];
    public $selectedProduct = null;
    public $productQuantity = 1;
    public $transferType = InventoryTransferItem::TYPE_GOOD;
    public $rejectionReason = '';
    public $isSourceWarehouseUser = false;
    public $isDestinationWarehouseUser = false;

    protected function rules(): array
    {
        $rules = [
            'inventoryTransfer.source_warehouse_id' => 'required|exists:warehouse,id',
            'inventoryTransfer.destination_warehouse_id' => 'required|exists:warehouse,id|different:inventoryTransfer.source_warehouse_id',
            'inventoryTransfer.notes' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.requested_quantity' => 'required|integer|min:1',
            'items.*.item_type' => ['required', 'in:' . InventoryTransferItem::TYPE_GOOD . ',' . InventoryTransferItem::TYPE_OBSOLETE],
        ];

        // Add rules for approval quantities when in source approval state
        if ($this->inventoryTransfer->status === InventoryTransfer::STATUS_PENDING_SOURCE_APPROVAL) {
            $rules['items.*.approved_quantity'] = [
                'required', 
                'integer', 
                'min:0',
                function($attribute, $value, $fail) {
                    $index = explode('.', $attribute)[1];
                    $item = $this->items[$index];
                    
                    if ($value > $item['requested_quantity']) {
                        $fail('Approved quantity cannot exceed requested quantity.');
                    }

                    // Validate against available stock
                    $sourceWarehouseProduct = WarehouseProduct::where('warehouse_id', $this->inventoryTransfer->source_warehouse_id)
                        ->where('product_id', $item['product_id'])
                        ->first();

                    if (!$sourceWarehouseProduct) {
                        $fail("Product not found in source warehouse.");
                        return;
                    }

                    $columnToCheck = ($item['item_type'] === InventoryTransferItem::TYPE_GOOD) ? 'quantity_good' : 'quantity_obsolete';
                    if ($sourceWarehouseProduct->$columnToCheck < $value) {
                        $fail("Insufficient stock in source warehouse. Available: {$sourceWarehouseProduct->$columnToCheck}");
                    }
                }
            ];
        }

        return $rules;
    }

    public function mount($id)
    {
        $this->inventoryTransfer = InventoryTransfer::with(['items', 'sourceWarehouse', 'destinationWarehouse'])->findOrFail($id);
        
        // Set user warehouse flags
        $userWarehouseId = auth()->user()->warehouse_id;
        $this->isSourceWarehouseUser = $userWarehouseId === $this->inventoryTransfer->source_warehouse_id;
        $this->isDestinationWarehouseUser = $userWarehouseId === $this->inventoryTransfer->destination_warehouse_id;

        $this->items = $this->inventoryTransfer->items->map(function($item) {
            return [
                'id' => $item->id,
                'product_id' => $item->product_id,
                'product_name' => $item->product_name,
                'product_code' => $item->product_code,
                'unit_name' => $item->unit_name,
                'requested_quantity' => $item->requested_quantity,
                'approved_quantity' => $item->approved_quantity,
                'shipped_quantity' => $item->shipped_quantity,
                'received_quantity' => $item->received_quantity,
                'item_type' => $item->item_type,
                'status' => $item->status
            ];
        })->toArray();
    }

    public function approveTransfer()
    {
        if (!$this->isSourceWarehouseUser || !auth()->user()->hasPermission('manage inventory transfers')) {
            session()->flash('error', 'You do not have permission to approve transfers from this warehouse.');
            return;
        }

        if ($this->inventoryTransfer->status !== InventoryTransfer::STATUS_PENDING_SOURCE_APPROVAL) {
            session()->flash('error', 'Transfer cannot be approved in its current status.');
            return;
        }

        // Validate that all items have approved quantities set
        foreach ($this->items as $item) {
            if (!isset($item['approved_quantity']) || $item['approved_quantity'] === null) {
                session()->flash('error', 'Please set approved quantities for all items.');
                return;
            }
        }

        try {
            // Create an array of approved quantities for the transfer model
            $approvedQuantities = collect($this->items)->pluck('approved_quantity', 'id')->toArray();
            
            // Call the model's approve method
            $this->inventoryTransfer->approveItems($approvedQuantities, auth()->id());

            session()->flash('success', 'Transfer approved successfully.');
            return redirect()->route('admin.inventory-transfers.index');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to approve transfer: ' . $e->getMessage());
        }
    }

    public function rejectTransfer()
    {
        if (!$this->isSourceWarehouseUser || !auth()->user()->hasPermission('manage inventory transfers')) {
            session()->flash('error', 'You do not have permission to reject transfers from this warehouse.');
            return;
        }

        $this->validate([
            'rejectionReason' => 'required|string|min:10'
        ]);

        DB::beginTransaction();
        try {
            foreach ($this->items as $item) {
                $transferItem = $this->inventoryTransfer->items()->find($item['id']);
                $transferItem->update([
                    'status' => InventoryTransferItem::STATUS_REJECTED,
                    'rejection_reason' => $this->rejectionReason
                ]);
            }

            $this->inventoryTransfer->status = InventoryTransfer::STATUS_SOURCE_REJECTED;
            $this->inventoryTransfer->rejection_reason = $this->rejectionReason;
            $this->inventoryTransfer->source_approved_by_user_id = auth()->id();
            $this->inventoryTransfer->source_approved_at = now();
            $this->inventoryTransfer->save();

            DB::commit();
            session()->flash('success', 'Transfer rejected successfully.');
            return redirect()->route('admin.inventory-transfers.index');
        } catch (\Throwable $th) {
            DB::rollBack();
            session()->flash('error', 'Failed to reject transfer: ' . $th->getMessage());
        }
    }

    public function shipTransfer()
    {
        if (
            ! $this->isSourceWarehouseUser
            || ! auth()->user()->hasPermission('manage inventory transfers')
        ) {
            session()->flash('error', 'You do not have permission to ship transfers from this warehouse.');
            return;
        }

        if ($this->inventoryTransfer->status !== InventoryTransfer::STATUS_SOURCE_APPROVED) {
            session()->flash('error', 'Transfer cannot be shipped in its current status.');
            return;
        }

        DB::transaction(function() {
            $sourceId = $this->inventoryTransfer->source_warehouse_id;

            foreach ($this->inventoryTransfer->items as $item) {
                $qty = $item->approved_quantity;
                $col = $item->item_type === InventoryTransferItem::TYPE_GOOD
                    ? 'quantity_good'
                    : 'quantity_obsolete';

                // 1) Decrement the source stock
                $updated = DB::table('warehouse_products')
                    ->where('warehouse_id', $sourceId)
                    ->where('product_id', $item->product_id)
                    ->decrement($col, $qty);

                if (! $updated) {
                    throw new \Exception("Insufficient stock for {$item->product_name}.");
                }

                // 2) Mark this line-item as shipped
                DB::table('inventory_transfer_items')
                    ->where('id', $item->id)
                    ->update([
                        'shipped_quantity' => $qty,
                        'status'           => InventoryTransferItem::STATUS_SHIPPED,
                        'updated_at'       => now(),
                    ]);
            }

            // 3) Update the transfer header
            $this->inventoryTransfer->update([
                'status'             => InventoryTransfer::STATUS_SHIPPED,
                'shipped_by_user_id' => auth()->id(),
                'shipped_at'         => now(),
            ]);
        });

        session()->flash('success', 'Transfer shipped—and stock levels updated.');
        return redirect()->route('admin.inventory-transfers.index');
    }

    public function receiveTransfer()
    {
        if (!$this->isDestinationWarehouseUser || !auth()->user()->hasPermission('manage inventory transfers')) {
            session()->flash('error', 'You do not have permission to receive transfers at this warehouse.');
            return;
        }

        if ($this->inventoryTransfer->status !== InventoryTransfer::STATUS_SHIPPED) {
            session()->flash('error', 'Transfer cannot be received in its current status.');
            return;
        }

        DB::beginTransaction();
        try {
            foreach ($this->items as $item) {
                $transferItem = $this->inventoryTransfer->items()->find($item['id']);
                
                // Update destination warehouse quantities
                $destinationProduct = WarehouseProduct::firstOrCreate(
                    [
                        'warehouse_id' => $this->inventoryTransfer->destination_warehouse_id,
                        'product_id' => $item['product_id']
                    ],
                    [
                        'quantity_good' => 0,
                        'quantity_obsolete' => 0,
                        'min_stock_level' => 0
                    ]
                );

                $columnToUpdate = $item['item_type'] === InventoryTransferItem::TYPE_GOOD ? 'quantity_good' : 'quantity_obsolete';
                $destinationProduct->increment($columnToUpdate, $item['shipped_quantity']);

                // Update transfer item
                $transferItem->update([
                    'received_quantity' => $item['shipped_quantity'],
                    'status' => InventoryTransferItem::STATUS_RECEIVED
                ]);
            }

            $this->inventoryTransfer->status = InventoryTransfer::STATUS_RECEIVED;
            $this->inventoryTransfer->received_by_user_id = auth()->id();
            $this->inventoryTransfer->received_at = now();
            $this->inventoryTransfer->save();

            DB::commit();
            session()->flash('success', 'Transfer received successfully.');
            return redirect()->route('admin.inventory-transfers.index');
        } catch (\Throwable $th) {
            DB::rollBack();
            session()->flash('error', 'Failed to receive transfer: ' . $th->getMessage());
        }
    }

    public function completeTransfer()
    {
        if (
            ! $this->isDestinationWarehouseUser
            || ! auth()->user()->hasPermission('manage inventory transfers')
        ) {
            session()->flash('error', 'You do not have permission to complete transfers at this warehouse.');
            return;
        }

        if ($this->inventoryTransfer->status !== InventoryTransfer::STATUS_RECEIVED) {
            session()->flash('error', 'Transfer cannot be completed in its current status.');
            return;
        }

        DB::transaction(function() {
            $destId = $this->inventoryTransfer->destination_warehouse_id;

            foreach ($this->inventoryTransfer->items as $item) {
                $qty = $item->shipped_quantity;
                $col = $item->item_type === InventoryTransferItem::TYPE_GOOD
                    ? 'quantity_good'
                    : 'quantity_obsolete';

                // 1) Try to increment existing row…
                $incremented = DB::table('warehouse_products')
                    ->where('warehouse_id', $destId)
                    ->where('product_id', $item->product_id)
                    ->increment($col, $qty);

                // 2) …or insert if it didn’t exist
                if (! $incremented) {
                    DB::table('warehouse_products')->insert([
                        'warehouse_id'      => $destId,
                        'product_id'        => $item->product_id,
                        'quantity_good'     => $item->item_type === InventoryTransferItem::TYPE_GOOD ? $qty : 0,
                        'quantity_obsolete' => $item->item_type === InventoryTransferItem::TYPE_OBSOLETE ? $qty : 0,
                        'min_stock_level'   => 0,
                        'created_at'        => now(),
                        'updated_at'        => now(),
                    ]);
                }

                // 3) Mark this line-item as received
                DB::table('inventory_transfer_items')
                    ->where('id', $item->id)
                    ->update([
                        'received_quantity' => $qty,
                        'status'            => InventoryTransferItem::STATUS_RECEIVED,
                        'updated_at'        => now(),
                    ]);
            }

            // 4) Finally complete the transfer
            $this->inventoryTransfer->update([
                'status'       => InventoryTransfer::STATUS_COMPLETED,
                'completed_at' => now(),
            ]);
        });

        session()->flash('success', 'Transfer completed—and destination stock updated.');
        return redirect()->route('admin.inventory-transfers.index');
    }

    public function render()
    {
        return view('livewire.admin.inventory-transfers.edit', [
            'warehouses' => Warehouse::all(),
            'products' => Product::with('unit')->get(),
            'isSourceWarehouseUser' => $this->isSourceWarehouseUser,
            'isDestinationWarehouseUser' => $this->isDestinationWarehouseUser
        ]);
    }
} 