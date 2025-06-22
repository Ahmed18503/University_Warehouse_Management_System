<?php

namespace App\Livewire\Admin\InventoryTransfers;

use App\Models\InventoryTransfer;
use App\Models\InventoryTransferItem;
use App\Models\WarehouseProduct;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class PendingConfirmations extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;
    public $rejectionReason = '';

    protected $queryString = ['search' => ['except' => '', 'as' => 's'], 'perPage'];

    public function render()
    {
        $userWarehouseId = auth()->user()->warehouse_id;

        $pendingConfirmations = InventoryTransfer::query()
            ->where('destination_warehouse_id', $userWarehouseId)
            ->where('status', InventoryTransfer::STATUS_SHIPPED)
            ->with(['sourceWarehouse', 'destinationWarehouse', 'creator', 'items'])
            ->when($this->search, function ($query) {
                $query->where(function ($query) {
                    $query->where('notes', 'like', '%' . $this->search . '%')
                          ->orWhereHas('sourceWarehouse', function ($q) {
                              $q->where('name', 'like', '%' . $this->search . '%');
                          })
                          ->orWhereHas('destinationWarehouse', function ($q) {
                              $q->where('name', 'like', '%' . $this->search . '%');
                          });
                });
            })
            ->orderByDesc('created_at')
            ->paginate($this->perPage);

        return view('livewire.admin.inventory-transfers.pending-confirmations', [
            'pendingConfirmations' => $pendingConfirmations,
        ]);
    }

    public function confirmReceipt(InventoryTransfer $inventoryTransfer)
    {
        if (!auth()->user()->hasPermission('manage inventory transfers')) {
            session()->flash('error', 'You do not have permission to confirm receipt.');
            return;
        }

        if ($inventoryTransfer->status !== InventoryTransfer::STATUS_SHIPPED) {
            session()->flash('error', 'Transfer cannot be received in its current status.');
            return;
        }

        DB::beginTransaction();
        try {
            foreach ($inventoryTransfer->items as $item) {
                $destinationWarehouseProduct = WarehouseProduct::firstOrCreate(
                    [
                        'warehouse_id' => $inventoryTransfer->destination_warehouse_id,
                        'product_id' => $item->product_id
                    ],
                    [
                        'quantity_good' => 0,
                        'quantity_obsolete' => 0,
                        'min_stock_level' => 0
                    ]
                );

                $columnToUpdate = $item->item_type === InventoryTransferItem::TYPE_GOOD ? 'quantity_good' : 'quantity_obsolete';
                $destinationWarehouseProduct->increment($columnToUpdate, $item->shipped_quantity);

                $item->update([
                    'received_quantity' => $item->shipped_quantity,
                    'status' => InventoryTransferItem::STATUS_RECEIVED
                ]);
            }

            $inventoryTransfer->status = InventoryTransfer::STATUS_RECEIVED;
            $inventoryTransfer->received_by_user_id = auth()->id();
            $inventoryTransfer->received_at = now();
            $inventoryTransfer->save();

            DB::commit();
            session()->flash('success', 'Transfer received successfully.');
        } catch (\Throwable $th) {
            DB::rollBack();
            session()->flash('error', 'Failed to receive transfer: ' . $th->getMessage());
        }
    }

    public function rejectReceipt(InventoryTransfer $inventoryTransfer)
    {
        $this->validate([
            'rejectionReason' => 'required|string|min:10'
        ]);

        if (!auth()->user()->hasPermission('manage inventory transfers')) {
            session()->flash('error', 'You do not have permission to reject receipt.');
            return;
        }

        if ($inventoryTransfer->status !== InventoryTransfer::STATUS_SHIPPED) {
            session()->flash('error', 'Transfer cannot be rejected in its current status.');
            return;
        }

        DB::beginTransaction();
        try {
            // Return quantities to source warehouse
            foreach ($inventoryTransfer->items as $item) {
                $sourceWarehouseProduct = WarehouseProduct::where([
                    'warehouse_id' => $inventoryTransfer->source_warehouse_id,
                    'product_id' => $item->product_id
                ])->first();

                if ($sourceWarehouseProduct) {
                    $columnToUpdate = $item->item_type === InventoryTransferItem::TYPE_GOOD ? 'quantity_good' : 'quantity_obsolete';
                    $sourceWarehouseProduct->increment($columnToUpdate, $item->shipped_quantity);
                }

                $item->update([
                    'status' => InventoryTransferItem::STATUS_REJECTED,
                    'rejection_reason' => $this->rejectionReason
                ]);
            }

            $inventoryTransfer->status = InventoryTransfer::STATUS_REJECTED;
            $inventoryTransfer->rejection_reason = $this->rejectionReason;
            $inventoryTransfer->rejected_at = now();
            $inventoryTransfer->rejected_by_user_id = auth()->id();
            $inventoryTransfer->save();

            DB::commit();
            session()->flash('success', 'Transfer rejected successfully.');
            $this->reset('rejectionReason');
        } catch (\Throwable $th) {
            DB::rollBack();
            session()->flash('error', 'Failed to reject transfer: ' . $th->getMessage());
        }
    }
} 